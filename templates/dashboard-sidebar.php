<?php
/**
 * Template part: Sidebar du Dashboard PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$current_page = basename( get_page_template() );

// Définir les liens et icônes (SVGs).
$menu_items = array(
	'dashboard' => array(
		'label' => 'Dashboard',
		'url'   => home_url( '/dashboard/' ),
		'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>',
		'active_on' => array( 'page-dashboard.php' )
	),
	'my-media' => array(
		'label' => 'Mes médias',
		'url'   => home_url( '/my-media/' ),
		'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
		'active_on' => array( 'page-my-media.php' )
	),
	'upload-media' => array(
		'label' => 'Ajouter média',
		'url'   => home_url( '/upload-media/' ),
		'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
		'active_on' => array( 'page-upload-media.php' )
	),
	'profile' => array(
		'label' => 'Mon Profil',
		'url'   => home_url( '/profile/' ),
		'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
		'active_on' => array( 'page-profile.php' )
	),
);

// Obtenir l'avatar personnalisé du photographe.
$avatar_id = get_user_meta( $current_user->ID, 'photovault_avatar_id', true );
$avatar_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $current_user->ID );
?>

<aside class="w-64 glass-effect border-r border-gray-800 flex flex-col justify-between h-screen sticky top-0">
	<div class="px-6 py-8">
		<h1 class="text-2xl font-extrabold text-white tracking-tight mb-8">
			Photo<span class="text-indigo-500">Vault</span>
		</h1>
		
		<nav class="space-y-1">
			<?php foreach ( $menu_items as $key => $item ) : 
				$is_active = in_array( $current_page, $item['active_on'] );
				$active_class = $is_active ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800/50 hover:text-white';
			?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all <?php echo esc_attr( $active_class ); ?>">
					<?php echo $item['icon']; ?>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>

	<!-- Profil & Déconnexion en bas de la sidebar -->
	<div class="p-4 border-t border-gray-800">
		<div class="flex items-center mb-4 px-2">
			<img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-500/50" src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar">
			<div class="ml-3">
				<p class="text-sm font-semibold text-white truncate max-w-[140px]"><?php echo esc_html( $current_user->display_name ); ?></p>
				<p class="text-xs text-gray-500 truncate max-w-[140px]">Photographe</p>
			</div>
		</div>
		<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="flex items-center px-4 py-3 text-sm font-medium text-red-400 hover:bg-red-950/20 rounded-xl transition-all cursor-pointer">
			<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
			Déconnexion
		</a>
	</div>
</aside>
