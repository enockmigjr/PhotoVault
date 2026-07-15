<?php
/**
 * Dashboard navigation for clients and platform administrators.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$section      = isset( $args['section'] ) ? sanitize_key( $args['section'] ) : 'overview';
$is_manager   = function_exists( 'photovault_current_user_can' ) ? photovault_current_user_can( 'photovault_manage_platform' ) : current_user_can( 'manage_options' );
$dashboard_url = home_url( '/dashboard/' );
$menu_items    = array(
	'overview' => array( 'label' => __( 'Apercu', 'photovault' ), 'url' => $dashboard_url, 'icon' => 'M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6v-9h-6v9zm0-16v5h6V4h-6z' ),
	'favorites' => array( 'label' => __( 'Favoris', 'photovault' ), 'url' => add_query_arg( 'section', 'favorites', $dashboard_url ), 'icon' => 'M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z' ),
	'downloads' => array( 'label' => __( 'Telechargements', 'photovault' ), 'url' => add_query_arg( 'section', 'downloads', $dashboard_url ), 'icon' => 'M12 3v12m0 0l-4-4m4 4l4-4M5 21h14' ),
	'access' => array( 'label' => __( 'Collections', 'photovault' ), 'url' => add_query_arg( 'section', 'access', $dashboard_url ), 'icon' => 'M7 10V7a5 5 0 0110 0v3m-11 0h12v10H6V10z' ),
	'newsletter' => array( 'label' => __( 'Newsletter', 'photovault' ), 'url' => add_query_arg( 'section', 'newsletter', $dashboard_url ), 'icon' => 'M3 6h18v12H3V6zm0 1l9 6 9-6' ),
	'security' => array( 'label' => __( 'Securite', 'photovault' ), 'url' => add_query_arg( 'section', 'security', $dashboard_url ), 'icon' => 'M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z' ),
);
if ( $is_manager ) {
	$menu_items['analytics'] = array( 'label' => __( 'Analytique', 'photovault' ), 'url' => add_query_arg( 'section', 'analytics', $dashboard_url ), 'icon' => 'M4 19V9m6 10V5m6 14v-7m4 7H2' );
}

$avatar_id  = get_user_meta( $current_user->ID, 'photovault_avatar_id', true );
$avatar_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $current_user->ID );
$role_label = $is_manager ? __( 'Administrateur', 'photovault' ) : __( 'Client', 'photovault' );
?>

<div class="sticky top-0 z-40 flex w-full items-center justify-between border-b border-white/10 bg-[#0d0c0b] p-4 lg:hidden">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-xl font-black text-white">Photo<span class="text-amber-300">Vault</span></a>
	<button id="toggle-sidebar" class="inline-flex h-10 w-10 items-center justify-center text-gray-200" type="button" aria-controls="main-sidebar" aria-expanded="false" aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'photovault' ); ?>">
		<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
	</button>
</div>

<div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-black/70 opacity-0 transition-opacity lg:hidden"></div>

<aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 flex h-screen w-72 -translate-x-full flex-col justify-between border-r border-white/10 bg-[#11100f] shadow-2xl transition-transform duration-200 lg:sticky lg:top-0 lg:translate-x-0">
	<div class="px-5 py-7">
		<div class="mb-7 flex items-center justify-between">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-2xl font-black text-white">Photo<span class="text-amber-300">Vault</span></a>
			<button id="close-sidebar" class="inline-flex h-9 w-9 items-center justify-center text-gray-300 lg:hidden" type="button" aria-label="<?php esc_attr_e( 'Fermer le menu', 'photovault' ); ?>">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
			</button>
		</div>

		<nav class="space-y-1" aria-label="<?php esc_attr_e( 'Espace personnel', 'photovault' ); ?>">
			<?php foreach ( $menu_items as $key => $item ) : ?>
				<?php $active = $section === $key; ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="flex items-center rounded-md px-4 py-3 text-sm font-semibold transition <?php echo esc_attr( $active ? 'bg-amber-300 text-black' : 'text-gray-300 hover:bg-white/[0.06] hover:text-white' ); ?>" <?php echo $active ? 'aria-current="page"' : ''; ?>>
					<svg class="mr-3 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo esc_attr( $item['icon'] ); ?>"></path></svg>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="mt-6 border-t border-white/10 pt-5">
			<a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="flex items-center rounded-md px-4 py-3 text-sm font-semibold text-gray-300 transition hover:bg-white/[0.06] hover:text-white">
				<svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 00-16 0m12-13a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
				<?php esc_html_e( 'Profil', 'photovault' ); ?>
			</a>
			<?php if ( $is_manager ) : ?>
				<a href="<?php echo esc_url( admin_url() ); ?>" class="flex items-center rounded-md px-4 py-3 text-sm font-semibold text-gray-300 transition hover:bg-white/[0.06] hover:text-white">
					<svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15a3 3 0 100-6 3 3 0 000 6zm7-3a7 7 0 00-.1-1l2-1.5-2-3.4-2.4 1a8 8 0 00-1.7-1L14.5 3h-5l-.4 3.1a8 8 0 00-1.7 1l-2.4-1-2 3.4L5.1 11a7 7 0 000 2L3 14.5l2 3.4 2.4-1a8 8 0 001.7 1l.4 3.1h5l.4-3.1a8 8 0 001.7-1l2.4 1 2-3.4-2.1-1.5a7 7 0 00.1-1z"></path></svg>
					<?php esc_html_e( 'Administration', 'photovault' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="border-t border-white/10 p-5">
		<div class="flex items-center gap-3">
			<img class="h-11 w-11 rounded-full object-cover ring-1 ring-white/20" src="<?php echo esc_url( $avatar_url ); ?>" alt="">
			<div class="min-w-0 flex-1"><p class="truncate text-sm font-bold text-white"><?php echo esc_html( $current_user->display_name ?: $current_user->user_login ); ?></p><p class="text-xs text-gray-500"><?php echo esc_html( $role_label ); ?></p></div>
			<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="inline-flex h-9 w-9 items-center justify-center text-gray-400 transition hover:text-red-300" title="<?php esc_attr_e( 'Deconnexion', 'photovault' ); ?>" aria-label="<?php esc_attr_e( 'Deconnexion', 'photovault' ); ?>">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5m5 5H3m12-8h4a2 2 0 012 2v12a2 2 0 01-2 2h-4"></path></svg>
			</a>
		</div>
	</div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const toggle = document.getElementById('toggle-sidebar');
	const close = document.getElementById('close-sidebar');
	const sidebar = document.getElementById('main-sidebar');
	const overlay = document.getElementById('sidebar-overlay');
	if (!toggle || !sidebar || !overlay) return;
	function setOpen(open) {
		sidebar.classList.toggle('-translate-x-full', !open);
		overlay.classList.toggle('hidden', !open);
		overlay.classList.toggle('opacity-100', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		document.body.classList.toggle('overflow-hidden', open);
	}
	toggle.addEventListener('click', function () { setOpen(true); });
	if (close) close.addEventListener('click', function () { setOpen(false); });
	overlay.addEventListener('click', function () { setOpen(false); });
	document.addEventListener('keydown', function (event) { if (event.key === 'Escape') setOpen(false); });
});
</script>
