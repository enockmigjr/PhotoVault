<?php
/**
 * Page de détail d'un média PhotoVault (single-media_item.php).
 * Premium Portfolio Layout.
 *
 * @package PhotoVault
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$media_id = get_the_ID();
		$author_id = get_the_author_meta( 'ID' );
		$is_private = 'private' === get_post_status( $media_id );
		$is_admin = current_user_can( 'manage_options' );
		$is_owner = is_user_logged_in() && (get_current_user_id() === $author_id);

		// 1. Restriction d'accès stricte pour les posts privés.
		if ( $is_private && ! $is_admin && ! $is_owner ) {
			?>
			<div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4 bg-[#0b0f19]">
				<div class="p-4 rounded-full bg-red-950/20 border border-red-500/20 text-red-500 mb-6 animate-pulse">
					<svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
				</div>
				<h2 class="text-3xl font-black text-white">Contenu Privé</h2>
				<p class="text-gray-400 mt-2 max-w-md text-sm">Ce média est configuré en mode confidentiel et est uniquement accessible au propriétaire de la galerie.</p>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="mt-8 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20 cursor-pointer text-sm">Retourner à la galerie</a>
			</div>
			<?php
			get_footer();
			exit;
		}

		$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';
		$folders = get_the_terms( $media_id, 'media_folder' );
		$categories = get_the_terms( $media_id, 'media_category' );
		$image_url = home_url( '/wp-json/photovault/v1/secure-image?id=' . $media_id . '&_wpnonce=' . wp_create_nonce( 'wp_rest' ) );
		
		// Incrémenter les vues
		if ( ! $is_admin ) {
			$views = (int) get_post_meta( $media_id, 'photovault_views_count', true );
			update_post_meta( $media_id, 'photovault_views_count', $views + 1 );
		}
		?>

		<div class="py-16 bg-[#0b0f19] min-h-screen text-gray-200">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				
				<!-- Fil d'Ariane & Navigation entre médias -->
				<div class="flex justify-between items-center mb-10 text-xs text-gray-500">
					<div class="flex items-center gap-1.5">
						<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="hover:text-white transition-colors">Galerie</a>
						<span>/</span>
						<span class="text-gray-400 truncate max-w-[200px]"><?php the_title(); ?></span>
					</div>
					
					<div class="flex gap-4">
						<?php 
						$prev_post = get_previous_post();
						$next_post = get_next_post();
						if ( $prev_post ) :
						?>
							<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="hover:text-white transition-colors flex items-center gap-1 font-semibold">&larr; Précédent</a>
						<?php endif; ?>
						<?php if ( $prev_post && $next_post ) : ?><span>|</span><?php endif; ?>
						<?php if ( $next_post ) : ?>
							<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="hover:text-white transition-colors flex items-center gap-1 font-semibold">Suivant &rarr;</a>
						<?php endif; ?>
					</div>
				</div>

				<!-- Grille de présentation du média -->
				<div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
					
					<!-- Image principale à gauche -->
					<div class="lg:col-span-2 space-y-6">
						<div class="glass-effect rounded-3xl overflow-hidden p-3 border border-gray-800 shadow-2xl relative">
							<div class="relative overflow-hidden rounded-2xl bg-black/40 <?php echo $is_protected ? 'protected-media-container' : ''; ?>">
								<?php if ( $image_url ) : ?>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-auto max-h-[75vh] object-contain mx-auto select-none pointer-events-none rounded-xl">
									
									<?php if ( $is_protected ) : ?>
										<div class="watermark font-extrabold select-none"><?php echo esc_html( get_option( 'photovault_watermark_text', 'PHOTOVAULT' ) ); ?></div>
									<?php endif; ?>
								<?php else : ?>
									<div class="w-full h-[50vh] flex items-center justify-center bg-gray-900/50 text-gray-700">
										<svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- Métadonnées & Actions à droite -->
					<div class="space-y-6 lg:col-span-1">
						<div class="glass-effect p-8 rounded-3xl border border-gray-800 shadow-2xl space-y-6 bg-gray-950/20">
							<div>
								<div class="flex items-center gap-2 mb-3">
									<?php if ( $is_protected ) : ?>
										<span class="bg-indigo-600/90 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-indigo-400/20 backdrop-blur-md">🔒 PROTÉGÉ</span>
									<?php else : ?>
										<span class="bg-emerald-600/20 text-emerald-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/20">🔓 PUBLIC</span>
									<?php endif; ?>
									<?php if ( $is_private ) : ?>
										<span class="bg-gray-800 text-gray-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-gray-700">👁️ PRIVÉ</span>
									<?php endif; ?>
								</div>
								<h1 class="text-3xl font-black text-white leading-tight tracking-tight"><?php the_title(); ?></h1>
								<p class="text-xs text-gray-500 mt-2">Publié le <?php echo get_the_date(); ?></p>
							</div>

							<?php if ( get_the_content() ) : ?>
								<div class="prose prose-invert text-sm text-gray-400 leading-relaxed border-t border-gray-800/60 pt-5">
									<?php the_content(); ?>
								</div>
							<?php endif; ?>

							<!-- Détails Auteur -->
							<div class="border-t border-gray-800/60 pt-5 flex items-center">
								<div class="h-10 w-10 rounded-full bg-indigo-600/10 text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-500/20">
									<?php echo esc_html( strtoupper( substr( get_the_author(), 0, 2 ) ) ); ?>
								</div>
								<div class="ml-3">
									<p class="text-sm font-bold text-white"><?php the_author(); ?></p>
									<p class="text-xs text-gray-500">Propriétaire de l'œuvre</p>
								</div>
							</div>

							<!-- Catégories & Dossiers -->
							<div class="border-t border-gray-800/60 pt-5 space-y-4">
								<?php if ( ! empty( $folders ) && ! is_wp_error( $folders ) ) : ?>
									<div>
										<span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Dossier / Projet</span>
										<div class="flex flex-wrap gap-2 mt-2">
											<?php foreach ( $folders as $folder ) : ?>
												<a href="<?php echo esc_url( get_term_link( $folder ) ); ?>" class="text-[11px] font-semibold bg-gray-900 border border-gray-800 hover:border-indigo-500/40 text-indigo-400 px-3 py-1.5 rounded-xl transition-all"><?php echo esc_html( $folder->name ); ?></a>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
									<div>
										<span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Catégories</span>
										<div class="flex flex-wrap gap-2 mt-2">
											<?php foreach ( $categories as $cat ) : ?>
												<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="text-[11px] font-semibold bg-gray-900 border border-gray-800 hover:border-gray-600 text-gray-300 px-3 py-1.5 rounded-xl transition-all"><?php echo esc_html( $cat->name ); ?></a>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<!-- Actions de téléchargement sécurisées par proxy -->
							<div class="border-t border-gray-800/60 pt-6">
								<?php if ( $is_protected && ! $is_admin && ! $is_owner ) : ?>
									<div class="bg-indigo-950/20 border border-indigo-500/20 text-indigo-400/90 p-4 rounded-2xl text-xs flex items-center leading-relaxed">
										<span class="mr-2.5 text-base">🔒</span> Ce média est sous haute protection. Le téléchargement est désactivé.
									</div>
								<?php else : 
									$download_url = home_url( '/wp-json/photovault/v1/secure-image?id=' . $media_id . '&download=1&_wpnonce=' . wp_create_nonce( 'wp_rest' ) );
								?>
									<a href="<?php echo esc_url( $download_url ); ?>" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl text-center block transition-all shadow-lg hover:shadow-indigo-500/10 cursor-pointer text-sm">
										Télécharger en haute définition
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>

				</div>

				<!-- Section Médias Similaires -->
				<?php 
				$related_args = array(
					'post_type'      => 'media_item',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'post__not_in'   => array( $media_id ),
				);
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
					$related_args['tax_query'] = array(
						array(
							'taxonomy' => 'media_category',
							'field'    => 'term_id',
							'terms'    => wp_list_pluck( $categories, 'term_id' )
						)
					);
				}
				$related_query = new WP_Query( $related_args );
				if ( $related_query->have_posts() ) :
				?>
					<div class="border-t border-gray-900 pt-16 mt-16 space-y-8">
						<h2 class="text-2xl font-black text-white tracking-tight">Médias similaires</h2>
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
							<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
								<?php get_template_part( 'templates/media-card' ); ?>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php
	endwhile;
endif;

get_footer();
?>
