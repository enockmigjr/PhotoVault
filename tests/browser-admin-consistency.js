const { chromium } = require( 'playwright' );
const path = require( 'path' );

( async () => {
	const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
	const username = process.env.PHOTOVAULT_TEST_USERNAME;
	const password = process.env.PHOTOVAULT_TEST_PASSWORD;
	const outputDir = process.env.PHOTOVAULT_TEST_SCREENSHOT_DIR;
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
			page.locator( 'form' ).filter( { has: page.locator( '#username' ) } ).locator( 'button[type="submit"]' ).click(),
		] );

		await page.goto( `${ baseUrl }/wp-admin/edit.php?post_type=media_item&page=photovault-access-requests&request_status=pending`, { waitUntil: 'networkidle' } );
		await page.locator( '.pv-access-status-tabs' ).waitFor();
		const accessLayout = await page.evaluate( () => {
			const tabs = document.querySelector( '.pv-access-status-tabs' );
			const table = document.querySelector( '.pv-access-status-tabs + .pv-table-wrap' );
			const tabsRect = tabs.getBoundingClientRect();
			const tableRect = table.getBoundingClientRect();
			return {
				activeTabs: tabs.querySelectorAll( '[aria-current="page"]' ).length,
				documentOverflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
				leftDelta: Math.abs( tabsRect.left - tableRect.left ),
				tableStartsAfterTabs: tableRect.top >= tabsRect.bottom - 1,
				tabsWidth: Math.round( tabsRect.width ),
				tableWidth: Math.round( tableRect.width ),
			};
		} );
		if ( accessLayout.activeTabs !== 1 || accessLayout.documentOverflow > 2 || accessLayout.leftDelta > 2 || ! accessLayout.tableStartsAfterTabs || Math.abs( accessLayout.tabsWidth - accessLayout.tableWidth ) > 2 ) {
			throw new Error( `Access status tabs offset the table: ${ JSON.stringify( accessLayout ) }` );
		}
		if ( outputDir ) {
			await page.screenshot( { path: path.join( outputDir, 'photovault-access-status-tabs.png' ), fullPage: true } );
		}

		await page.goto( `${ baseUrl }/wp-admin/admin.php?page=identity-security-kit-audit`, { waitUntil: 'networkidle' } );
		await page.locator( '.isk-admin-pagination' ).waitFor();
		const identityPagination = await page.evaluate( () => {
			const pagination = document.querySelector( '.isk-admin-pagination' );
			const list = pagination.querySelector( '.page-numbers' );
			const current = pagination.querySelector( '.current' );
			const currentStyle = getComputedStyle( current );
			return {
				display: getComputedStyle( pagination ).display,
				justifyContent: getComputedStyle( pagination ).justifyContent,
				listDisplay: getComputedStyle( list ).display,
				currentHeight: Math.round( current.getBoundingClientRect().height ),
				currentBackground: currentStyle.backgroundColor,
				documentOverflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
			};
		} );
		if ( identityPagination.display !== 'flex' || identityPagination.justifyContent !== 'flex-end' || identityPagination.listDisplay !== 'flex' || identityPagination.currentHeight < 32 || identityPagination.currentBackground === 'rgba(0, 0, 0, 0)' || identityPagination.documentOverflow > 2 ) {
			throw new Error( `Identity pagination is inconsistent: ${ JSON.stringify( identityPagination ) }` );
		}
		if ( outputDir ) {
			await page.screenshot( { path: path.join( outputDir, 'identity-audit-pagination.png' ), fullPage: true } );
		}

		process.stdout.write( JSON.stringify( { accessLayout, identityPagination } ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
