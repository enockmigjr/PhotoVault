<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[#0d0c0b] text-gray-100 min-h-screen flex flex-col' ); ?>>
<?php wp_body_open(); ?>

<?php
$is_dashboard_surface = function_exists( 'photovault_is_dashboard_surface' ) ? photovault_is_dashboard_surface() : is_page_template( 'page-dashboard.php' );
$archive_url          = get_post_type_archive_link( 'media_item' );
$dashboard_url        = home_url( '/dashboard/' );
$brand_name           = photovault_theme_text( 'photovault_brand_name', 'PhotoVault' );
$login_label          = photovault_theme_text( 'photovault_header_login_label', __( 'Connexion', 'photovault' ) );
$join_label           = photovault_theme_text( 'photovault_header_join_label', __( 'Rejoindre', 'photovault' ) );
$join_url             = get_theme_mod( 'photovault_header_join_url', home_url( '/register/' ) );
$public_navigation    = array(
	array( 'label' => __( 'Explorer', 'photovault' ), 'url' => $archive_url, 'active' => is_post_type_archive( 'media_item' ) || is_singular( 'media_item' ) || is_tax() ),
	array( 'label' => __( 'Services', 'photovault' ), 'url' => home_url( '/booking/' ), 'active' => is_page( array( 'booking', 'pricing', 'fonctionnalites' ) ), 'children' => array( array( 'label' => __( 'Réserver', 'photovault' ), 'url' => home_url( '/booking/' ) ), array( 'label' => __( 'Tarifs', 'photovault' ), 'url' => home_url( '/pricing/' ) ), array( 'label' => __( 'Fonctionnement', 'photovault' ), 'url' => home_url( '/fonctionnalites/' ) ) ) ),
	array( 'label' => __( 'Journal', 'photovault' ), 'url' => home_url( '/journal/' ), 'active' => ! is_front_page() && ( is_page( 'journal' ) || is_home() || is_singular( 'post' ) ) ),
	array( 'label' => __( 'À propos', 'photovault' ), 'url' => home_url( '/about/' ), 'active' => is_page( 'about' ) ),
	array( 'label' => __( 'Contact', 'photovault' ), 'url' => home_url( '/contact/' ), 'active' => is_page( 'contact' ) ),
);
?>

<?php if ( ! $is_dashboard_surface ) : ?>
	<header class="pv-site-header sticky top-0 z-50 border-b border-white/10 bg-[#0d0c0b]/95 backdrop-blur-xl">
		<div class="mx-auto flex h-20 max-w-[90rem] items-center justify-between px-5 sm:px-8 lg:px-12">
			<?php if ( has_custom_logo() ) : ?><div class="pv-header-logo"><?php the_custom_logo(); ?></div><?php else : ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center text-2xl font-extrabold text-white" aria-label="<?php echo esc_attr( sprintf( __( '%s, accueil', 'photovault' ), $brand_name ) ); ?>"><?php echo esc_html( $brand_name ); ?></a><?php endif; ?>

			<nav class="hidden items-center gap-8 lg:flex" aria-label="<?php esc_attr_e( 'Navigation principale', 'photovault' ); ?>">
				<?php if ( has_nav_menu( 'primary' ) ) : wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'pv-primary-menu', 'depth' => 2, 'fallback_cb' => false ) ); else : ?>
					<ul class="pv-primary-menu"><?php foreach ( $public_navigation as $item ) : ?><li class="<?php echo $item['active'] ? 'current-menu-item' : ''; ?><?php echo ! empty( $item['children'] ) ? ' menu-item-has-children' : ''; ?>"><a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $item['active'] ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a><?php if ( ! empty( $item['children'] ) ) : ?><ul class="sub-menu"><?php foreach ( $item['children'] as $child ) : ?><li><a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a></li><?php endforeach; ?></ul><?php endif; ?></li><?php endforeach; ?></ul>
				<?php endif; ?>
			</nav>

			<div class="hidden items-center gap-3 lg:flex">
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( $dashboard_url ); ?>" class="pv-header-account">
						<span class="pv-header-account__mark" aria-hidden="true"><?php echo esc_html( strtoupper( substr( wp_get_current_user()->display_name ?: wp_get_current_user()->user_login, 0, 1 ) ) ); ?></span>
						<span><?php esc_html_e( 'Mon espace', 'photovault' ); ?></span>
					</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="pv-header-icon" aria-label="<?php esc_attr_e( 'Se déconnecter', 'photovault' ); ?>" title="<?php esc_attr_e( 'Se déconnecter', 'photovault' ); ?>">
						<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5M15 12H3m9-9h6a3 3 0 013 3v12a3 3 0 01-3 3h-6" /></svg>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="pv-header-link"><?php echo esc_html( $login_label ); ?></a>
					<a href="<?php echo esc_url( $join_url ); ?>" class="pv-header-cta"><?php echo esc_html( $join_label ); ?></a>
				<?php endif; ?>
			</div>

			<button type="button" id="mobile-menu-button" class="pv-header-icon lg:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'photovault' ); ?>">
				<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="hamburger-icon" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
				<svg class="hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="close-icon" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" /></svg>
			</button>
		</div>
	</header>

	<div id="mobile-menu" class="fixed inset-x-0 bottom-0 top-20 z-[999] hidden origin-top scale-y-95 overflow-y-auto border-t border-white/10 bg-[#0d0c0b] px-5 pb-8 pt-6 opacity-0 transition duration-300 lg:hidden">
		<nav class="flex flex-col" aria-label="<?php esc_attr_e( 'Navigation mobile', 'photovault' ); ?>">
			<?php if ( has_nav_menu( 'primary' ) ) : wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'pv-mobile-menu', 'depth' => 2, 'fallback_cb' => false ) ); else : ?><ul class="pv-mobile-menu"><?php foreach ( $public_navigation as $item ) : ?><li class="<?php echo $item['active'] ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a><?php if ( ! empty( $item['children'] ) ) : ?><ul class="sub-menu"><?php foreach ( $item['children'] as $child ) : ?><li><a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a></li><?php endforeach; ?></ul><?php endif; ?></li><?php endforeach; ?></ul><?php endif; ?>
		</nav>

		<div class="mt-8 border-t border-white/10 pt-6">
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( $dashboard_url ); ?>" class="pv-header-cta flex w-full justify-center"><?php esc_html_e( 'Ouvrir mon espace', 'photovault' ); ?></a>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="mt-3 flex min-h-11 w-full items-center justify-center text-sm font-semibold text-gray-400 hover:text-white"><?php esc_html_e( 'Se déconnecter', 'photovault' ); ?></a>
			<?php else : ?>
				<div class="grid grid-cols-2 gap-3">
					<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="flex min-h-11 items-center justify-center border border-white/15 text-sm font-semibold text-gray-200"><?php echo esc_html( $login_label ); ?></a>
					<a href="<?php echo esc_url( $join_url ); ?>" class="pv-header-cta flex justify-center"><?php echo esc_html( $join_label ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
