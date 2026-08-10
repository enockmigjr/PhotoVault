const { chromium } = require( 'playwright' );

const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
const username = process.env.PHOTOVAULT_TEST_USERNAME;
const password = process.env.PHOTOVAULT_TEST_PASSWORD;

if ( ! username || ! password ) {
	throw new Error( 'Missing PHOTOVAULT_TEST_USERNAME or PHOTOVAULT_TEST_PASSWORD.' );
}

( async () => {
	const browser = await chromium.launch( { headless: true } );
	try {
		const anonymous = await browser.newPage();
		await anonymous.goto( `${ baseUrl }/`, { waitUntil: 'domcontentloaded', timeout: 30000 } );
		await anonymous.getByRole( 'button', { name: 'Assistance' } ).click();
		const anonymousFrame = anonymous.frameLocator( 'iframe[title="Assistance et suivi des demandes"]' );
		await anonymousFrame.getByLabel( 'Adresse email' ).waitFor( { timeout: 30000 } );

		const member = await browser.newPage();
		await member.goto( `${ baseUrl }/login/`, { waitUntil: 'domcontentloaded', timeout: 30000 } );
		await member.fill( '#username', username );
		await member.fill( '#password', password );
		await Promise.all( [
			member.waitForNavigation( { waitUntil: 'domcontentloaded', timeout: 30000 } ),
			member.locator( 'form' ).filter( { has: member.locator( '#username' ) } ).locator( 'button[type="submit"]' ).click(),
		] );
		await member.goto( `${ baseUrl }/`, { waitUntil: 'domcontentloaded', timeout: 30000 } );
		await member.getByRole( 'button', { name: 'Assistance' } ).click();
		const memberFrame = member.frameLocator( 'iframe[title="Assistance et suivi des demandes"]' );
		await memberFrame.getByRole( 'button', { name: 'Nouvelle demande' } ).waitFor( { timeout: 30000 } );

		process.stdout.write( JSON.stringify( { anonymous_verification: true, verified_member_without_otp: true } ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
