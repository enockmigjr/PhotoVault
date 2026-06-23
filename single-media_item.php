<?php
/**
 * Page de détail d'un média PhotoVault (single-media_item.php).
 *
 * @package PhotoVault
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$media_id = get_the_ID();
		$author_id = get_the_author_meta( 'ID' );
		$is_private = 'private' === get_post_status( $media_id );

		// Vérifier l'accès pour les posts privés.
		if ( $is_private ) {
			if ( ! is_user_logged_in() || ( get_current_user_id() !== $author_id && ! current_user_can( 'manage_options' ) ) ) {
				?>
				<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4 bg-[#0b0f19]">
					<svg class="h-16 w-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
					<h2 class="text-2xl font-extrabold text-white">Accès restreint</h2>
					<p class="text-gray-400 mt-2">Ce média est configuré en mode privé et n'est pas accessible au public.</p>
					<a href="<?php echo esc_url( home_url() ); ?>" class="mt-6 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl">Retour à l'accueil</a>
				</div>
				<?php
				get_footer();
				exit;
			}
		}

		$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';
		$folders = get_the_terms( $media_id, 'media_folder' );
		$categories = get_the_terms( $media_id, 'media_category' );
		$image_url = home_url( '/wp-json/photovault/v1/secure-image?id=' . $media_id );
		?>

		<div class="py-12 bg-[#0b0f19] min-h-screen">
			<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
					
					<!-- Image à gauche -->
					<div class="lg:col-span-2 space-y-6">
						<div class="glass-effect rounded-3xl overflow-hidden p-3 border border-gray-800 shadow-2xl relative">
							<div class="relative overflow-hidden rounded-2xl bg-black <?php echo $is_protected ? 'protected-media-container' : ''; ?>">
								<?php if ( $image_url ) : ?>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-auto max-h-[70vh] object-contain mx-auto select-none pointer-events-none">
									
									<?php if ( $is_protected ) : ?>
										<div class="watermark font-extrabold select-none">PHOTOVAULT</div>
									<?php endif; ?>
								<?php else : ?>
									<div class="w-full h-[50vh] flex items-center justify-center bg-gray-900 text-gray-700">
										<svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- Métadonnées à droite -->
					<div class="space-y-6">
						<div class="glass-effect p-8 rounded-3xl border border-gray-800 shadow-2xl space-y-6">
							<div>
								<div class="flex items-center gap-2 mb-2">
									<?php if ( $is_protected ) : ?>
										<span class="bg-indigo-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-indigo-400/30">🔒 PROTÉGÉ</span>
									<?php endif; ?>
									<?php if ( $is_private ) : ?>
										<span class="bg-gray-800 text-gray-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-gray-700">👁️ PRIVÉ</span>
									<?php endif; ?>
								</div>
								<h1 class="text-3xl font-extrabold text-white leading-tight"><?php the_title(); ?></h1>
								<p class="text-xs text-gray-500 mt-1">Publié le <?php echo get_the_date(); ?></p>
							</div>

							<?php if ( get_the_content() ) : ?>
								<div class="prose prose-invert text-sm text-gray-400 leading-relaxed border-t border-gray-800/80 pt-4">
									<?php the_content(); ?>
								</div>
							<?php endif; ?>

							<!-- Photographe -->
							<div class="border-t border-gray-800/80 pt-4 flex items-center">
								<div class="h-10 w-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm border border-indigo-500/20">
									<?php echo esc_html( strtoupper( substr( get_the_author(), 0, 2 ) ) ); ?>
								</div>
								<div class="ml-3">
									<p class="text-sm font-semibold text-white"><?php the_author(); ?></p>
									<p class="text-xs text-gray-500">Photographe Créateur</p>
								</div>
							</div>

							<!-- Catégories & Dossiers -->
							<div class="border-t border-gray-800/80 pt-4 space-y-3">
								<?php if ( ! empty( $folders ) && ! is_wp_error( $folders ) ) : ?>
									<div>
										<span class="block text-xs font-semibold text-gray-500 uppercase">Dossier</span>
										<div class="flex flex-wrap gap-2 mt-1">
											<?php foreach ( $folders as $folder ) : ?>
												<a href="<?php echo esc_url( get_term_link( $folder ) ); ?>" class="text-xs bg-gray-900 hover:bg-gray-800 border border-gray-800 text-indigo-400 px-3 py-1 rounded-lg transition-colors"><?php echo esc_html( $folder->name ); ?></a>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
									<div>
										<span class="block text-xs font-semibold text-gray-500 uppercase">Catégories</span>
										<div class="flex flex-wrap gap-2 mt-1">
											<?php foreach ( $categories as $cat ) : ?>
												<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="text-xs bg-gray-900 hover:bg-gray-800 border border-gray-800 text-gray-300 px-3 py-1 rounded-lg transition-colors"><?php echo esc_html( $cat->name ); ?></a>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<!-- Actions de téléchargement (uniquement si non protégé) -->
							<div class="border-t border-gray-800/80 pt-6">
								<?php if ( $is_protected ) : ?>
									<div class="bg-indigo-950/20 border border-indigo-500/20 text-indigo-400 p-4 rounded-xl text-xs flex items-center">
										<span class="mr-2">🔒</span> Ce média est sous protection. Le téléchargement est restreint.
									</div>
								<?php else : ?>
									<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>" download class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-center block transition-all shadow-lg cursor-pointer">
										Télécharger en haute définition
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php
	endwhile;
endif;

get_footer();
?>
