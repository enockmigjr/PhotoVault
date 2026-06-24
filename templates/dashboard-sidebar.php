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
// Définir les liens et icônes (SVGs).
$menu_items = array(
	'dashboard' => array(
		'label' => 'Dashboard',
		'url'   => home_url( '/dashboard/' ),
		'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>',
		'active_on' => array( 'page-dashboard.php' )
	),
	'wp-admin' => array(
		'label' => 'Administration',
		'url'   => admin_url(),
		'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
		'active_on' => array()
	),
);

// Obtenir l'avatar personnalisé de l'administrateur.
$avatar_id = get_user_meta( $current_user->ID, 'photovault_avatar_id', true );
$avatar_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $current_user->ID );
?>

<aside class="w-64 glass-effect border-r border-gray-800 flex flex-col justify-between h-screen sticky top-0 bg-gray-950/20 shadow-2xl">
	<div class="px-6 py-8">
		<h1 class="text-2xl font-black text-white tracking-tight mb-8">
			Photo<span class="text-indigo-500">Vault</span>
		</h1>
		
		<nav class="space-y-1">
			<?php foreach ( $menu_items as $key => $item ) : 
				$is_active = in_array( $current_page, $item['active_on'] );
				$active_class = $is_active ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300 hover:bg-gray-800/50 hover:text-white font-medium';
			?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="flex items-center px-4 py-3.5 text-xs tracking-wider uppercase rounded-xl transition-all <?php echo esc_attr( $active_class ); ?>">
					<?php echo $item['icon']; ?>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>

	<!-- Profil & Déconnexion en bas de la sidebar -->
	<div class="p-4 border-t border-gray-800/60 bg-gray-950/20">
		<div class="flex items-center mb-4 px-2">
			<img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-500/50" src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar">
			<div class="ml-3">
				<p class="text-sm font-bold text-white truncate max-w-[140px]"><?php echo esc_html( $current_user->display_name ); ?></p>
				<p class="text-xs text-gray-500 truncate max-w-[140px]">Administrateur</p>
			</div>
		</div>
		<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="flex items-center px-4 py-3 text-xs tracking-wider uppercase font-semibold text-red-400 hover:bg-red-950/20 rounded-xl transition-all cursor-pointer">
			<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
			Déconnexion
		</a>
	</div>
</aside>
