<?php
/**
 * Template part: Carte Média de PhotoVault.
 * Inspired by Adobe Portfolio & Unsplash.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$media_id = get_the_ID();
$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';
$is_private = 'private' === get_post_status( $media_id );
$author_name = get_the_author();
$image_url = photovault_get_secure_image_url( $media_id, 'card' );
?>

<div class="glass-effect rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 hover:scale-[1.03] hover:shadow-indigo-950/20 border border-gray-800/80 group flex flex-col justify-between h-full bg-gray-900/30">
	<div class="relative aspect-[4/3] bg-gray-950 overflow-hidden rounded-t-3xl">
		<?php if ( $image_url ) : ?>
			<img class="w-full h-full object-cover transition-all duration-700 ease-out group-hover:scale-110" src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="400" height="400">
		<?php else : ?>
			<div class="w-full h-full flex items-center justify-center bg-gray-900/50 text-gray-700">
				<svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
			</div>
		<?php endif; ?>

		<!-- Overlay haut de gamme au survol -->
		<div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-between p-5 z-10">
			<!-- Badges en haut à droite -->
			<div class="flex justify-end gap-1.5">
				<?php if ( $is_protected ) : ?>
					<span class="bg-indigo-600/90 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full flex items-center shadow border border-indigo-400/20 backdrop-blur-md">🔒 PROTÉGÉ</span>
				<?php endif; ?>
				<?php if ( $is_private ) : ?>
					<span class="bg-gray-900/90 text-gray-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full flex items-center shadow border border-gray-700/30 backdrop-blur-md">👁️ PRIVÉ</span>
				<?php endif; ?>
			</div>

			<!-- Titre & Auteur en bas -->
			<div>
				<h3 class="text-white text-base font-extrabold truncate leading-tight"><?php the_title(); ?></h3>
				<p class="text-gray-400 text-xs mt-1 truncate flex items-center">
					<span class="opacity-60">Par</span>&nbsp;<span class="font-semibold text-gray-200"><?php echo esc_html( $author_name ); ?></span>
				</p>
			</div>
		</div>

		<!-- Badges visibles par défaut sans survol -->
		<div class="absolute top-3 right-3 flex gap-1 z-20 transition-opacity duration-300 group-hover:opacity-0">
			<?php if ( $is_protected ) : ?>
				<span class="bg-indigo-600/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full flex items-center shadow-lg border border-indigo-400/20 backdrop-blur-md">🔒</span>
			<?php endif; ?>
			<?php if ( $is_private ) : ?>
				<span class="bg-gray-800/95 text-gray-300 text-[10px] font-semibold px-2 py-0.5 rounded-full flex items-center shadow-lg border border-gray-700/35 backdrop-blur-md">👁️</span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Infos visibles au bas de la carte -->
	<div class="p-4 border-t border-gray-800/80 flex items-center justify-between rounded-b-3xl bg-gray-950/10">
		<span class="text-xs font-semibold text-gray-300 truncate max-w-[150px]"><?php the_title(); ?></span>
		<a href="<?php the_permalink(); ?>" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center group/btn">
			Découvrir
			<svg class="w-3.5 h-3.5 ml-1 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
		</a>
	</div>
</div>
