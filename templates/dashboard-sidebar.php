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
    'wp-admin' => array(
        'label' => 'Administration',
        'url'   => admin_url(),
        'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'active_on' => array()
    ),
    // NOUVEAU : Lien vers l'accueil du site internet
    'home' => array(
        'label' => 'Voir le site',
        'url'   => home_url( '/' ),
        'icon'  => '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
        'active_on' => array()
    ),
);

$avatar_id = get_user_meta( $current_user->ID, 'photovault_avatar_id', true );
$avatar_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $current_user->ID );
?>

<div class="lg:hidden flex items-center justify-between bg-gray-950 p-4 border-b border-gray-800 sticky top-0 z-40 w-full">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-xl font-black text-white tracking-tight hover:opacity-80 transition-opacity">
        Photo<span class="text-indigo-500">Vault</span>
    </a>
    <button id="toggle-sidebar" class="text-gray-200 hover:text-white p-2 focus:outline-none" aria-label="Ouvrir le menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

<aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 h-screen -translate-x-full transition-transform duration-300 ease-in-out glass-effect border-r border-gray-800 flex flex-col justify-between bg-gray-950/95 shadow-2xl lg:sticky lg:top-0 lg:translate-x-0 lg:bg-gray-950/20">
    <div class="px-6 py-8">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hidden lg:inline-block text-2xl font-black text-white tracking-tight mb-8 hover:opacity-80 transition-opacity">
            Photo<span class="text-indigo-500">Vault</span>
        </a>
        
        <div class="flex lg:hidden justify-end mb-6">
            <button id="close-sidebar" class="text-gray-300 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="space-y-1">
            <?php foreach ( $menu_items as $key => $item ) : 
                $is_active = in_array( $current_page, $item['active_on'] );
                $active_class = $is_active ? 'bg-indigo-600 text-white font-bold' : 'text-gray-200 hover:bg-gray-800/50 hover:text-white font-medium';
            ?>
                <a href="<?php echo esc_url( $item['url'] ); ?>" class="flex items-center px-4 py-3.5 text-xs tracking-wider uppercase rounded-xl transition-all <?php echo esc_attr( $active_class ); ?>">
                    <?php echo $item['icon']; ?>
                    <?php echo esc_html( $item['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="p-4 border-t border-gray-800/60 bg-gray-950/40 lg:bg-gray-950/20">
        <div class="flex items-center mb-4 px-2">
            <img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-500/50" src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar">
            <div class="ml-3">
                <p class="text-sm font-bold text-white truncate max-w-[140px]"><?php echo esc_html( $current_user->display_name ); ?></p>
                <p class="text-xs text-gray-300 truncate max-w-[140px]">Administrateur</p>
            </div>
        </div>
        <a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="flex items-center px-4 py-3 text-xs tracking-wider uppercase font-semibold text-red-400 hover:bg-red-950/20 rounded-xl transition-all cursor-pointer">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Déconnexion
        </a>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('main-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        document.body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100');
        setTimeout(() => overlay.classList.add('hidden'), 300);
        document.body.classList.remove('overflow-hidden');
    }

    if(toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if(overlay) overlay.addEventListener('click', closeSidebar);
});
</script>