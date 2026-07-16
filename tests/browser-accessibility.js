const { chromium } = require( 'playwright' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const wcagTags = [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ];

async function scanPage( page, name, url ) {
	await page.goto( url, { waitUntil: 'domcontentloaded', timeout: 30000 } );
	await page.waitForTimeout( 350 );
	const report = await new AxeBuilder( { page } ).withTags( wcagTags ).analyze();
	const violations = report.violations
		.filter( ( violation ) => [ 'critical', 'serious' ].includes( violation.impact ) )
		.map( ( violation ) => ( {
			page: name,
			id: violation.id,
			impact: violation.impact,
			help: violation.help,
			targets: violation.nodes.slice( 0, 5 ).map( ( node ) => node.target.join( ' ' ) ),
		} ) );

	return { name, url: page.url(), violations };
}

( async () => {
	const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
	const username = process.env.PHOTOVAULT_TEST_USERNAME;
	const password = process.env.PHOTOVAULT_TEST_PASSWORD;
	if ( ! username || ! password ) {
		throw new Error( 'Missing PHOTOVAULT_TEST_USERNAME or PHOTOVAULT_TEST_PASSWORD.' );
	}

	const browser = await chromium.launch( { headless: true } );
	const context = await browser.newContext( { viewport: { width: 1440, height: 1000 } } );
	const page = await context.newPage();
	try {
		const results = [];
		results.push( await scanPage( page, 'home', `${ baseUrl }/` ) );
		results.push( await scanPage( page, 'gallery', `${ baseUrl }/gallery/` ) );
		results.push( await scanPage( page, 'login', `${ baseUrl }/login/` ) );

		await page.fill( '#username', username );
		await page.fill( '#password', password );
		await Promise.all( [
			page.waitForNavigation( { waitUntil: 'domcontentloaded', timeout: 30000 } ),
			page.locator( 'form' ).filter( { has: page.locator( '#username' ) } ).locator( 'button[type="submit"]' ).click(),
		] );
		results.push( await scanPage( page, 'dashboard', `${ baseUrl }/dashboard/` ) );
		results.push( await scanPage( page, 'profile', `${ baseUrl }/profile/` ) );

		const violations = results.flatMap( ( result ) => result.violations );
		if ( violations.length ) {
			throw new Error( `Serious WCAG violations:\n${ JSON.stringify( violations, null, 2 ) }` );
		}

		process.stdout.write( JSON.stringify( {
			pages: results.map( ( result ) => result.name ),
			standards: wcagTags,
			serious_or_critical_violations: 0,
		} ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
