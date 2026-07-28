const { chromium } = require( 'playwright' );

( async () => {
	const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
	const username = process.env.PHOTOVAULT_TEST_USERNAME;
	const password = process.env.PHOTOVAULT_TEST_PASSWORD;
	const avatarPath = process.env.PHOTOVAULT_TEST_AVATAR;
	if ( ! username || ! password || ! avatarPath ) {
		throw new Error( 'Missing PHOTOVAULT_TEST_USERNAME, PHOTOVAULT_TEST_PASSWORD or PHOTOVAULT_TEST_AVATAR.' );
	}

	const browser = await chromium.launch( { headless: true, channel: process.env.PHOTOVAULT_TEST_BROWSER_CHANNEL || 'chrome' } );
	const page = await browser.newPage( { viewport: { width: 1440, height: 1000 } } );
	try {
		await page.goto( `${ baseUrl }/login/`, { waitUntil: 'domcontentloaded', timeout: 20000 } );
		await page.fill( '#username', username );
		await page.fill( '#password', password );
		await Promise.all( [
			page.waitForNavigation( { waitUntil: 'domcontentloaded', timeout: 20000 } ),
			page.locator( 'form' ).filter( { has: page.locator( '#username' ) } ).locator( 'button[type="submit"]' ).click(),
		] );

		await page.goto( `${ baseUrl }/profile/`, { waitUntil: 'domcontentloaded', timeout: 20000 } );
		await page.getByRole( 'heading', { name: 'Informations et securite' } ).waitFor();
		await page.locator( '[data-profile-open="profile-avatar-dialog"]' ).click();
		const dialog = page.locator( '#profile-avatar-dialog' );
		await dialog.waitFor( { state: 'visible' } );
		if ( await dialog.locator( 'input[name="phone"]' ).count() ) {
			throw new Error( 'The avatar flow still contains the phone field.' );
		}
		const avatar = page.locator( '[data-profile-avatar-image]' );
		const previousAvatar = await avatar.getAttribute( 'src' );
		let documentRequests = 0;
		page.on( 'request', ( request ) => {
			if ( request.resourceType() === 'document' ) documentRequests++;
		} );
		await dialog.locator( 'input[name="profile_avatar"]' ).setInputFiles( avatarPath );
		await Promise.all( [
			page.waitForResponse( ( response ) => response.request().method() === 'POST' && response.url().includes( '/account' ) && response.ok(), { timeout: 20000 } ),
			dialog.locator( 'button[type="submit"]' ).click(),
		] );

		const toast = page.locator( '[data-pv-runtime-toast]' );
		await toast.waitFor( { state: 'visible' } );
		await page.waitForFunction( ( initial ) => {
			const image = document.querySelector( '[data-profile-avatar-image]' );
			return image && image.getAttribute( 'src' ) !== initial;
		}, previousAvatar );
		if ( documentRequests !== 0 ) {
			throw new Error( 'The avatar flow triggered a document navigation.' );
		}
		await toast.locator( 'button' ).click();
		await toast.waitFor( { state: 'detached' } );
		if ( new URL( page.url() ).pathname !== '/profile/' ) {
			throw new Error( 'The asynchronous profile flow changed the public route.' );
		}

		process.stdout.write( JSON.stringify( { dashboard_layout: true, avatar_without_phone: true, async_upload: true, dialogs: true, dismissible_toast: true } ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
