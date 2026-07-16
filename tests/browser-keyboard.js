const { chromium } = require( 'playwright' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

async function assertReflow( page, name, url ) {
	await page.goto( url, { waitUntil: 'domcontentloaded', timeout: 30000 } );
	await page.waitForTimeout( 250 );
	const dimensions = await page.evaluate( () => ( {
		clientWidth: document.documentElement.clientWidth,
		scrollWidth: document.documentElement.scrollWidth,
	} ) );
	if ( dimensions.scrollWidth > dimensions.clientWidth + 2 ) {
		throw new Error( `${ name } has document-level horizontal overflow: ${ JSON.stringify( dimensions ) }` );
	}
}

async function assertOpenDialogAccessibility( page, selector ) {
	const dialog = page.locator( selector );
	await dialog.waitFor( { state: 'visible' } );
	const report = await new AxeBuilder( { page } )
		.include( selector )
		.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
		.analyze();
	const violations = report.violations.filter( ( violation ) => [ 'critical', 'serious' ].includes( violation.impact ) );
	if ( violations.length ) {
		throw new Error( `Open dialog accessibility failure: ${ JSON.stringify( violations, null, 2 ) }` );
	}
	return dialog;
}

( async () => {
	const baseUrl = ( process.env.PHOTOVAULT_TEST_BASE_URL || 'http://localhost:8080' ).replace( /\/$/, '' );
	const username = process.env.PHOTOVAULT_TEST_USERNAME;
	const password = process.env.PHOTOVAULT_TEST_PASSWORD;
	if ( ! username || ! password ) {
		throw new Error( 'Missing PHOTOVAULT_TEST_USERNAME or PHOTOVAULT_TEST_PASSWORD.' );
	}

	const browser = await chromium.launch( { headless: true } );
	const context = await browser.newContext( { viewport: { width: 720, height: 900 } } );
	const page = await context.newPage();
	try {
		await assertReflow( page, 'home', `${ baseUrl }/` );
		await assertReflow( page, 'gallery', `${ baseUrl }/gallery/` );

		const galleryOpener = page.locator( '[data-pv-lightbox-open]' ).first();
		await galleryOpener.focus();
		await page.keyboard.press( 'Enter' );
		const lightbox = await assertOpenDialogAccessibility( page, '#pv-gallery-lightbox' );
		const firstCount = await lightbox.locator( '[data-pv-lightbox-count]' ).textContent();
		await page.keyboard.press( 'ArrowRight' );
		const nextCount = await lightbox.locator( '[data-pv-lightbox-count]' ).textContent();
		if ( firstCount === nextCount ) {
			throw new Error( 'Gallery keyboard navigation did not advance to the next work.' );
		}
		await page.keyboard.press( 'Escape' );
		await lightbox.waitFor( { state: 'hidden' } );
		if ( ! await galleryOpener.evaluate( ( element ) => document.activeElement === element ) ) {
			throw new Error( 'Gallery focus was not restored to its opener.' );
		}

		await assertReflow( page, 'login', `${ baseUrl }/login/` );
		await page.fill( '#username', username );
		await page.fill( '#password', password );
		await Promise.all( [
			page.waitForNavigation( { waitUntil: 'domcontentloaded', timeout: 30000 } ),
			page.locator( 'form' ).filter( { has: page.locator( '#username' ) } ).locator( 'button[type="submit"]' ).click(),
		] );
		await assertReflow( page, 'dashboard', `${ baseUrl }/dashboard/` );
		await assertReflow( page, 'profile', `${ baseUrl }/profile/` );

		const profileOpener = page.locator( '[data-profile-open="profile-identity-dialog"]' );
		await profileOpener.focus();
		await page.keyboard.press( 'Enter' );
		const profileDialog = await assertOpenDialogAccessibility( page, '#profile-identity-dialog' );
		for ( let index = 0; index < 8; index++ ) {
			await page.keyboard.press( 'Tab' );
			const focusInside = await profileDialog.evaluate( ( dialog ) => dialog.contains( document.activeElement ) );
			if ( ! focusInside ) {
				throw new Error( 'Profile dialog released keyboard focus into the page.' );
			}
		}
		await page.keyboard.press( 'Escape' );
		await profileDialog.waitFor( { state: 'hidden' } );
		if ( ! await profileOpener.evaluate( ( element ) => document.activeElement === element ) ) {
			throw new Error( 'Profile focus was not restored to its opener.' );
		}

		process.stdout.write( JSON.stringify( {
			reflow_equivalent_200_percent: [ 'home', 'gallery', 'login', 'dashboard', 'profile' ],
			gallery_keyboard_navigation: true,
			open_dialog_wcag: true,
			focus_trap_and_restore: true,
		} ) );
	} finally {
		await browser.close();
	}
} )().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
