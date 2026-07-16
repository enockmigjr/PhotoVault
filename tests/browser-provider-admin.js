const { chromium } = require( 'playwright' );
const path = require( 'path' );

( async () => {
	const baseUrl = process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080';
	const username = process.env.PHOTOVAULT_TEST_USERNAME;
	const password = process.env.PHOTOVAULT_TEST_PASSWORD;
	const outputDir = process.env.PHOTOVAULT_TEST_SCREENSHOT_DIR;
	if ( ! username || ! password ) {
		throw new Error( 'Missing PHOTOVAULT_TEST_USERNAME or PHOTOVAULT_TEST_PASSWORD.' );
	}

	const browser = await chromium.launch( { headless: true } );
	const page = await browser.newPage( { viewport: { width: 1440, height: 1000 } } );
	try {
		await page.goto( `${ baseUrl }/wp-login.php`, { waitUntil: 'networkidle' } );
		await page.fill( '#user_login', username );
		await page.fill( '#user_pass', password );
		await Promise.all( [
			page.waitForLoadState( 'networkidle' ),
			page.click( '#wp-submit' ),
		] );

		const pages = [
			{
				name: 'identity-provider',
				url: `${ baseUrl }/wp-admin/admin.php?page=identity-security-kit`,
				selector: 'form input[name="action"][value="identity_security_kit_test_sms_provider"]',
			},
			{
				name: 'newsletter-provider',
				url: `${ baseUrl }/wp-admin/admin.php?page=newsletter-campaign-kit-settings`,
				selector: 'form input[name="action"][value="newsletter_campaign_kit_test_provider"]',
			},
		];
		const results = {};
		for ( const target of pages ) {
			await page.goto( target.url, { waitUntil: 'networkidle' } );
			await page.locator( target.selector ).waitFor( { state: 'attached' } );
			const dimensions = await page.evaluate( () => ( {
				documentWidth: document.documentElement.scrollWidth,
				viewportWidth: document.documentElement.clientWidth,
				hasFatal: document.body.innerText.includes( 'critical error' ),
			} ) );
			if ( dimensions.hasFatal || dimensions.documentWidth > dimensions.viewportWidth + 2 ) {
				throw new Error( `${ target.name } is invalid or overflows: ${ JSON.stringify( dimensions ) }` );
			}
			results[ target.name ] = dimensions;
			if ( outputDir ) {
				await page.screenshot( { path: path.join( outputDir, `${ target.name }.png` ), fullPage: true } );
			}
		}

		console.log( JSON.stringify( results ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exitCode = 1;
} );
