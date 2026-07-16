const { chromium } = require( 'playwright' );

const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
const mailpitUrl = ( process.env.PHOTOVAULT_TEST_MAILPIT_URL || 'http://localhost:8025' ).replace( /\/$/, '' );
const username = process.env.PHOTOVAULT_TEST_USERNAME;
const password = process.env.PHOTOVAULT_TEST_PASSWORD;
const email = process.env.PHOTOVAULT_TEST_EMAIL;
const flow = process.env.PHOTOVAULT_IDENTITY_FLOW || 'verify';

if ( ! username || ! password || ! email ) {
	throw new Error( 'Missing PhotoVault identity-flow credentials.' );
}

async function listMessages() {
	const response = await fetch( `${ mailpitUrl }/api/v1/messages` );
	if ( ! response.ok ) throw new Error( 'Mailpit messages could not be listed.' );
	return ( await response.json() ).messages || [];
}

async function waitForMessage( subjectPart, excludedIds = new Set() ) {
	for ( let attempt = 0; attempt < 20; attempt++ ) {
		const message = ( await listMessages() ).find( ( candidate ) => {
			const recipients = ( candidate.To || [] ).map( ( recipient ) => recipient.Address.toLowerCase() );
			return ! excludedIds.has( candidate.ID ) && recipients.includes( email.toLowerCase() ) && candidate.Subject.includes( subjectPart );
		} );
		if ( message ) {
			const response = await fetch( `${ mailpitUrl }/api/v1/message/${ encodeURIComponent( message.ID ) }` );
			if ( response.ok ) return response.json();
		}
		await new Promise( ( resolve ) => setTimeout( resolve, 500 ) );
	}
	throw new Error( `Mailpit did not receive ${ subjectPart } for ${ email }.` );
}

function extractActionUrl( message, path ) {
	const html = String( message.HTML || '' );
	const links = [ ...html.matchAll( /href="([^"]+)"/g ) ].map( ( match ) => match[ 1 ].replaceAll( '&amp;', '&' ).replaceAll( '&#038;', '&' ).replaceAll( '&#38;', '&' ) );
	const url = links.find( ( candidate ) => candidate.includes( path ) );
	if ( ! url ) throw new Error( `No ${ path } action URL was found in the email.` );
	return url;
}

async function login( page ) {
	await page.goto( `${ baseUrl }/login/`, { waitUntil: 'domcontentloaded' } );
	await page.fill( '#username', username );
	await page.fill( '#password', password );
	await Promise.all( [
		page.waitForNavigation( { waitUntil: 'domcontentloaded' } ),
		page.locator( 'form' ).filter( { has: page.locator( '#username' ) } ).locator( 'button[type="submit"]' ).click(),
	] );
}

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const context = await browser.newContext( { viewport: { width: 1280, height: 900 } } );
	const page = await context.newPage();
	try {
		if ( flow === 'verify' ) {
			const before = new Set( ( await listMessages() ).map( ( message ) => message.ID ) );
			await login( page );
			await page.goto( `${ baseUrl }/profile/`, { waitUntil: 'domcontentloaded' } );
			const resend = page.locator( 'form input[value="identity_security_kit_resend_email_verification"]' ).locator( '..' ).locator( 'button[type="submit"]' );
			await Promise.all( [ page.waitForNavigation( { waitUntil: 'domcontentloaded' } ), resend.click() ] );
			if ( ! page.url().includes( 'verify=resent' ) ) throw new Error( `Unexpected resend redirect: ${ page.url() }` );
			const message = await waitForMessage( 'Verify your email address', before );
			await page.goto( extractActionUrl( message, 'identity_security_kit_verify_email' ), { waitUntil: 'domcontentloaded' } );
			if ( ! page.url().includes( '/profile/' ) || ! page.url().includes( 'verify=success' ) ) throw new Error( `Unexpected verification redirect: ${ page.url() }` );
			process.stdout.write( JSON.stringify( { resend_delivery: true, verification_redirect: 'profile', verified: true } ) );
		} else if ( flow === 'reset' ) {
			const message = await waitForMessage( 'Password reset' );
			const resetUrl = extractActionUrl( message, '/reset-password/' );
			await page.goto( resetUrl, { waitUntil: 'domcontentloaded' } );
			if ( ! page.url().includes( '/reset-password/' ) ) throw new Error( `Reset link left the frontend: ${ page.url() }` );
			const nextPassword = process.env.PHOTOVAULT_TEST_NEW_PASSWORD || 'PV!Flow-2026-New-Strong';
			await page.fill( '#reset-password', nextPassword );
			await page.fill( '#reset-password-confirm', nextPassword );
			const resetForm = page.locator( 'form' ).filter( { has: page.locator( '#reset-password' ) } );
			await Promise.all( [ page.waitForNavigation( { waitUntil: 'domcontentloaded' } ), resetForm.locator( 'button[type="submit"]' ).click() ] );
			if ( ! page.url().includes( '/login/' ) || ! page.url().includes( 'password=reset' ) ) throw new Error( `Unexpected reset completion redirect: ${ page.url() }` );
			const confirmation = await waitForMessage( 'Password changed' );
			if ( ! String( confirmation.HTML || '' ).includes( '<table role="presentation"' ) ) throw new Error( 'Password-change confirmation is not using the professional HTML template.' );
			process.stdout.write( JSON.stringify( { native_reset_email: 'professional', reset_route: 'frontend', completion_redirect: 'login', confirmation_email: 'professional' } ) );
		} else if ( flow === 'preferences' ) {
			await login( page );
			await page.goto( `${ baseUrl }/profile/`, { waitUntil: 'domcontentloaded' } );
			await page.locator( '[data-profile-open="profile-preferences-dialog"]' ).click();
			const dialog = page.locator( '#profile-preferences-dialog' );
			await dialog.locator( 'select[name="gallery_density"]' ).selectOption( 'compact' );
			await dialog.locator( 'input[name="reduce_motion"]' ).check();
			await dialog.locator( 'select[name="dashboard_landing"]' ).selectOption( 'favorites' );
			await Promise.all( [ page.waitForNavigation( { waitUntil: 'domcontentloaded' } ), dialog.locator( 'button[type="submit"]' ).click() ] );
			if ( ! page.url().includes( 'profile=preferences_updated' ) ) throw new Error( `Unexpected preferences redirect: ${ page.url() }` );
			await page.goto( `${ baseUrl }/gallery/`, { waitUntil: 'domcontentloaded' } );
			const classes = await page.locator( 'body' ).getAttribute( 'class' );
			if ( ! classes.includes( 'pv-gallery-compact' ) || ! classes.includes( 'pv-reduce-motion' ) ) throw new Error( `Saved preference classes are missing: ${ classes }` );
			await page.goto( `${ baseUrl }/dashboard/`, { waitUntil: 'domcontentloaded' } );
			const activeHref = await page.locator( '#main-sidebar a[aria-current="page"]' ).first().getAttribute( 'href' );
			if ( ! activeHref || ! activeHref.includes( 'section=favorites' ) ) throw new Error( `Dashboard landing preference was not applied: ${ activeHref }` );
			process.stdout.write( JSON.stringify( { gallery_density: 'compact', reduced_motion: true, dashboard_landing: 'favorites' } ) );
		} else if ( flow === 'mfa-anchor' ) {
			await login( page );
			await page.goto( `${ baseUrl }/profile/`, { waitUntil: 'domcontentloaded' } );
			const totp = page.locator( '[data-mfa-method="totp"]' );
			await totp.locator( 'details' ).first().locator( 'summary' ).click();
			await totp.locator( 'input[name="current_password"]' ).fill( password );
			await Promise.all( [ page.waitForNavigation( { waitUntil: 'domcontentloaded' } ), totp.locator( 'button[type="submit"]' ).click() ] );
			if ( new URL( page.url() ).hash !== '#identity-security-mfa' ) throw new Error( `MFA action did not restore the security section: ${ page.url() }` );
			await page.locator( '.identity-totp-qr' ).waitFor( { state: 'visible' } );
			const top = await page.locator( '#identity-security-mfa' ).evaluate( ( element ) => element.getBoundingClientRect().top );
			if ( top < -2 || top > 160 ) throw new Error( `MFA section is outside the useful viewport after redirect: ${ top }` );
			const cancel = totp.locator( 'form.identity-security-mfa-secondary-action button[type="submit"]' );
			await Promise.all( [ page.waitForNavigation( { waitUntil: 'domcontentloaded' } ), cancel.click() ] );
			process.stdout.write( JSON.stringify( { mfa_redirect_anchor: true, qr_visible_after_reload: true, enrollment_cleanup: true } ) );
		} else {
			throw new Error( `Unknown identity flow: ${ flow }` );
		}
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
