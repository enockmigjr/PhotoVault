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

		const modes = [
			'manual',
			'latest_posts',
			'recent_window',
			'category_posts',
			'selected_posts',
		];
		const results = [];

		for ( const mode of modes ) {
			let sentPost = null;
			await page.route( '**/wp-admin/admin-post.php', ( route ) => {
				const postData = route.request().postData() || '';
				if ( postData.includes( 'newsletter_campaign_kit_create_campaign' ) ) {
					sentPost = postData;
				}
				route.abort();
			} );

			await page.goto( `${ baseUrl }/wp-admin/admin.php?page=newsletter-campaign-kit-campaigns`, { waitUntil: 'domcontentloaded' } );
			await page.locator( '[data-nck-dialog-open="nck-campaign-editor"]' ).waitFor( { timeout: 30000 } );
			await page.locator( '[data-nck-dialog-open="nck-campaign-editor"]' ).click();
			await page.locator( '#nck-campaign-editor' ).waitFor( { state: 'visible' } );
			await page.fill( '#nck-campaign-title', `Regression ${ mode } ${ Date.now() }` );
			await page.fill( '#nck-campaign-subject', `Regression ${ mode }` );
			await page.selectOption( '#nck-content-source-type', mode );

			if ( mode === 'selected_posts' ) {
				await page.locator( '[data-nck-source-content-items] input[type="checkbox"]:not(:disabled)' ).first().check();
			}

			await page.locator( '#nck-campaign-editor input[type="submit"]' ).click();
			await page.waitForTimeout( 1200 );

			const sourceValue = sentPost ? ( sentPost.match( /name="content_source_type"\r\n\r\n([^\r\n]+)/ ) || [] )[1] : '';
			if ( ! sentPost || sourceValue !== mode ) {
				throw new Error( `Campaign source "${ mode }" did not submit: ${ sourceValue || 'no request' }` );
			}
			results.push( { mode, submitted: true } );
			await page.unroute( '**/wp-admin/admin-post.php' );
		}

		process.stdout.write( JSON.stringify( results ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
