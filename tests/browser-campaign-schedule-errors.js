const { chromium } = require( 'playwright' );

( async () => {
	const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
	const username = process.env.PHOTOVAULT_TEST_USERNAME;
	const password = process.env.PHOTOVAULT_TEST_PASSWORD;
	if ( ! username || ! password ) {
		throw new Error( 'Missing PHOTOVAULT_TEST_USERNAME or PHOTOVAULT_TEST_PASSWORD.' );
	}

	const browser = await chromium.launch( { headless: true } );
	const page = await browser.newPage( { viewport: { width: 1440, height: 1000 } } );
	try {
		await page.goto( `${ baseUrl }/login/`, { waitUntil: 'domcontentloaded' } );
		await page.fill( '#username', username );
		await page.fill( '#password', password );
		await Promise.all( [
			page.waitForNavigation( { waitUntil: 'domcontentloaded', timeout: 30000 } ),
			page.locator( 'form[data-pv-auth-form] button[type="submit"]' ).click(),
		] );

		await page.goto( `${ baseUrl }/wp-admin/admin.php?page=newsletter-campaign-kit-campaigns`, { waitUntil: 'domcontentloaded' } );
		await page.locator( '[data-nck-dialog-open="nck-campaign-editor"]' ).waitFor( { timeout: 30000 } );
		await page.locator( '[data-nck-dialog-open="nck-campaign-editor"]' ).click();
		await page.locator( '#nck-campaign-editor' ).waitFor( { state: 'visible' } );

		const title = `Schedule error ${ Date.now() }`;
		await page.fill( '#nck-campaign-title', title );
		await page.fill( '#nck-campaign-subject', 'Schedule error' );
		await page.locator( '#nck-campaign-editor .nck-recurrence-options summary' ).click();
		await page.locator( '#nck-campaign-editor [name="campaign_recurrence_enabled"]' ).check();

		const first = new Date( Date.now() + ( 60 * 60 * 1000 ) );
		const pad = ( value ) => String( value ).padStart( 2, '0' );
		const scheduledAt = `${ first.getFullYear() }-${ pad( first.getMonth() + 1 ) }-${ pad( first.getDate() ) }T${ pad( first.getHours() ) }:${ pad( first.getMinutes() ) }`;
		await page.fill( '#nck-campaign-editor [name="scheduled_at"]', scheduledAt );

		await page.locator( '#nck-campaign-editor input[type="submit"]' ).click();
		const error = page.locator( '.newsletter-campaign-kit-admin .notice-error' ).first();
		await error.waitFor( { timeout: 15000 } );
		const message = ( await error.textContent() ).trim();
		if ( ! /end date|recurrence/i.test( message ) ) {
			throw new Error( `Schedule error message is not explicit: ${ message }` );
		}

		const toast = page.locator( '.nck-runtime-toast.is-error' ).first();
		await toast.waitFor( { timeout: 5000 } );
		if ( ! /end date|recurrence/i.test( ( await toast.textContent() ) ) ) {
			throw new Error( 'The error toast did not surface the schedule validation message.' );
		}
		const tableText = await page.locator( '.nck-table-wrap' ).textContent();
		if ( tableText.includes( title ) ) {
			throw new Error( 'An invalid scheduled campaign was created.' );
		}

		process.stdout.write( JSON.stringify( { errorVisible: true, message } ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
