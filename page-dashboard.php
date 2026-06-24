<?php
/**
 * Template Name: PhotoVault Dashboard
 *
 * @package PhotoVault
 */

// Seul l'administrateur a accès au dashboard de statistiques
if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
    wp_redirect( home_url() );
    exit;
}

$current_user = wp_get_current_user();
$stats = photovault_get_photographer_stats( 0 ); // 0 pour statistiques globales du système

// Récupérer les 4 derniers médias importés globalement
$recent_media_query = new WP_Query( array(
    'post_type'      => 'media_item',
    'post_status'    => array( 'publish', 'private' ),
    'posts_per_page' => 4,
) );

get_header();
?>

<div class="flex flex-col lg:flex-row min-h-screen bg-[#0d0c0b] font-sans">
    
    <?php get_template_part( 'templates/dashboard-sidebar' ); ?>

    <main class="flex-1 p-6 sm:p-12 overflow-y-auto">
        <div class="max-w-6xl mx-auto space-y-12">
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">Panneau Analytique</h2>
                    <p class="text-gray-400 mt-1 text-sm font-medium">Aperçu général de l'activité, de la visibilité et de la sécurité des galeries.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs font-bold tracking-wider text-emerald-400 uppercase">Système En Ligne</span>
                </div>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total Médias</p>
                        <h3 class="text-4xl font-black text-white mt-2"><?php echo esc_html( $stats['total'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Publics</p>
                        <h3 class="text-4xl font-black text-emerald-400 mt-2"><?php echo esc_html( $stats['public'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-600/10 text-emerald-400 border border-emerald-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Privés</p>
                        <h3 class="text-4xl font-black text-gray-300 mt-2"><?php echo esc_html( $stats['private'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-800/50 text-gray-400 border border-gray-700/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Protégés</p>
                        <h3 class="text-4xl font-black text-indigo-400 mt-2"><?php echo esc_html( $stats['protected'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Dossiers</p>
                        <h3 class="text-4xl font-black text-white mt-2"><?php echo esc_html( $stats['folders'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-purple-600/10 text-purple-400 border border-purple-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Catégories</p>
                        <h3 class="text-4xl font-black text-white mt-2"><?php echo esc_html( $stats['categories'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-pink-600/10 text-pink-400 border border-pink-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Vues totales</p>
                        <h3 class="text-4xl font-black text-white mt-2"><?php echo esc_html( $stats['views'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-blue-600/10 text-blue-400 border border-blue-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                </div>

                <div class="glass-effect p-6 rounded-3xl flex items-center justify-between shadow-xl border border-gray-800/80">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Téléchargements</p>
                        <h3 class="text-4xl font-black text-white mt-2"><?php echo esc_html( $stats['downloads'] ); ?></h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-amber-600/10 text-amber-400 border border-amber-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect p-6 sm:p-8 rounded-3xl shadow-xl border border-gray-800/80">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white tracking-tight">Derniers médias importés</h3>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=media_item' ) ); ?>" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center">
                        Gérer dans WordPress Admin &rarr;
                    </a>
                </div>

                <?php if ( $recent_media_query->have_posts() ) : ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        <?php while ( $recent_media_query->have_posts() ) : $recent_media_query->the_post(); ?>
                            <?php get_template_part( 'templates/media-card' ); ?>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <div class="text-center py-12 text-gray-600">
                        <p>Aucun média disponible dans le système.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>