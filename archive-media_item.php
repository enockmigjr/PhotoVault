<?php
/**
 * Public visual archive with asynchronous filters and progressive loading.
 *
 * @package PhotoVault
 */

get_header();

$folders    = get_terms( array( 'taxonomy' => 'media_folder', 'hide_empty' => false ) );
$categories = get_terms( array( 'taxonomy' => 'media_category', 'hide_empty' => false ) );
$statuses   = is_user_logged_in() ? array( 'publish', 'private' ) : array( 'publish' );
$query_args = array(
	'post_type'      => 'media_item',
	'post_status'    => $statuses,
	'posts_per_page' => 12,
);

$visibility_filter = null;
if ( function_exists( 'photovault_restrict_media_query_where' ) && ! current_user_can( 'photovault_manage_media' ) ) {
	$user_id          = get_current_user_id();
	$visibility_filter = static function ( $where, $query ) use ( $user_id ) {
		if ( 'media_item' !== $query->get( 'post_type' ) ) {
			return $where;
		}

		return photovault_restrict_media_query_where( $where, $user_id );
	};
	add_filter( 'posts_where', $visibility_filter, 10, 2 );
}

try {
	$query = new WP_Query( $query_args );
} finally {
	if ( $visibility_filter ) {
		remove_filter( 'posts_where', $visibility_filter, 10 );
	}
}
?>

<main class="pv-gallery-page min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 pb-12 pt-20 sm:pb-16 sm:pt-28">
		<div class="mx-auto max-w-[90rem] px-5 sm:px-8 lg:px-12">
			<p class="text-xs font-bold uppercase text-amber-200">PhotoVault / Archives visuelles</p>
			<div class="mt-5 grid gap-8 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-end">
				<h1 class="max-w-5xl font-serif text-4xl leading-[1.02] text-white sm:text-5xl">Des fragments de temps, sans ordre impose.</h1>
				<p class="max-w-xl text-sm leading-7 text-gray-400 sm:text-base">Parcourez les series publiques et les collections auxquelles vous avez acces. Les apercus sont optimises pour l'ecran; l'original n'est remis que lorsqu'une oeuvre autorise son telechargement.</p>
			</div>
		</div>
	</header>

	<section class="sticky top-0 z-40 border-b border-white/10 bg-[#0d0c0b]/95 backdrop-blur-xl" aria-label="Filtres de la galerie">
		<div class="mx-auto max-w-[90rem] px-5 py-4 sm:px-8 lg:px-12">
			<form id="filters-form" class="pv-gallery-filters" onsubmit="return false;">
				<label class="pv-gallery-search">
					<span class="pv-gallery-filter-label"><?php esc_html_e( 'Recherche', 'photovault' ); ?></span>
					<svg width="18" height="18" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
					<input id="filter-search" name="search" type="search" placeholder="Rechercher une oeuvre">
				</label>
				<label><span class="pv-gallery-filter-label"><?php esc_html_e( 'Collection', 'photovault' ); ?></span><select id="filter-folder" name="folder"><option value=""><?php esc_html_e( 'Toutes les collections', 'photovault' ); ?></option><?php foreach ( $folders as $folder ) : ?><option value="<?php echo esc_attr( $folder->term_id ); ?>"><?php echo esc_html( $folder->name ); ?></option><?php endforeach; ?></select></label>
				<label><span class="pv-gallery-filter-label"><?php esc_html_e( 'Categorie', 'photovault' ); ?></span><select id="filter-category" name="category"><option value=""><?php esc_html_e( 'Toutes les categories', 'photovault' ); ?></option><?php foreach ( $categories as $category ) : ?><option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option><?php endforeach; ?></select></label>
				<label><span class="pv-gallery-filter-label"><?php esc_html_e( 'Acces', 'photovault' ); ?></span><select id="filter-protected" name="protected"><option value=""><?php esc_html_e( 'Tous les acces', 'photovault' ); ?></option><option value="1"><?php esc_html_e( 'Oeuvres protegees', 'photovault' ); ?></option><option value="0"><?php esc_html_e( 'Oeuvres ouvertes', 'photovault' ); ?></option></select></label>
				<label><span class="pv-gallery-filter-label"><?php esc_html_e( 'Ordre', 'photovault' ); ?></span><select id="filter-orderby" name="orderby"><option value="date_desc"><?php esc_html_e( 'Plus recentes', 'photovault' ); ?></option><option value="date_asc"><?php esc_html_e( 'Plus anciennes', 'photovault' ); ?></option><option value="alphabetical"><?php esc_html_e( 'Titre A-Z', 'photovault' ); ?></option></select></label>
				<button id="reset-filters" class="pv-gallery-reset" type="reset" title="<?php esc_attr_e( 'Reinitialiser les filtres', 'photovault' ); ?>" aria-label="<?php esc_attr_e( 'Reinitialiser les filtres', 'photovault' ); ?>"><svg width="18" height="18" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg></button>
			</form>
		</div>
	</section>

	<section class="mx-auto max-w-[90rem] px-5 py-12 sm:px-8 sm:py-16 lg:px-12" aria-live="polite" aria-busy="false" id="gallery-results">
		<div id="media-grid" class="pv-gallery-masonry<?php echo $query->have_posts() ? '' : ' is-empty'; ?>">
			<?php if ( $query->have_posts() ) : ?>
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php get_template_part( 'templates/media-card' ); ?>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="pv-gallery-empty"><strong><?php esc_html_e( 'Les archives attendent leur premiere oeuvre.', 'photovault' ); ?></strong><span><?php esc_html_e( 'Revenez bientot ou modifiez vos criteres.', 'photovault' ); ?></span></div>
			<?php endif; ?>
		</div>
		<div class="mt-12 flex justify-center">
			<button id="load-more-media" class="pv-gallery-more<?php echo $query->max_num_pages > 1 ? '' : ' hidden'; ?>" type="button"><?php esc_html_e( 'Voir la suite', 'photovault' ); ?><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6"/></svg></button>
		</div>
	</section>
</main>

<?php get_template_part( 'templates/gallery-lightbox' ); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('filters-form');
	const grid = document.getElementById('media-grid');
	const results = document.getElementById('gallery-results');
	const reset = document.getElementById('reset-filters');
	const more = document.getElementById('load-more-media');
	const config = window.photovault_ajax || <?php echo wp_json_encode( array( 'rest_url' => esc_url_raw( rest_url( 'photovault/v1' ) ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) ); ?>;
	let page = 1;
	let pages = <?php echo absint( $query->max_num_pages ); ?>;
	let debounce;
	let requestController;

	function emptyState() {
		return '<div class="pv-gallery-empty"><strong>Aucune oeuvre ne correspond a cette recherche.</strong><span>Essayez une autre collection ou reinitialisez les filtres.</span></div>';
	}

	async function updateGallery(append) {
		if (requestController) requestController.abort();
		requestController = new AbortController();
		const controller = requestController;
		page = append ? page + 1 : 1;
		const params = new URLSearchParams(new FormData(form));
		params.set('page', String(page));
		results.setAttribute('aria-busy', 'true');
		more.disabled = true;
		more.setAttribute('aria-busy', 'true');
		try {
			const response = await fetch(config.rest_url.replace(/\/$/, '') + '/media?' + params.toString(), { credentials: 'same-origin', headers: config.nonce ? { 'X-WP-Nonce': config.nonce } : {}, signal: controller.signal });
			if (!response.ok) throw new Error('gallery_request_failed');
			const payload = await response.json();
			pages = Number(payload.pages || 0);
			const hasItems = Boolean(payload.success && payload.data.length);
			const html = hasItems ? payload.data.map(item => item.html).join('') : emptyState();
			if (append && payload.data.length) grid.insertAdjacentHTML('beforeend', html); else grid.innerHTML = html;
			grid.classList.toggle('is-empty', !hasItems && !append);
			more.classList.toggle('hidden', !pages || page >= pages);
			document.dispatchEvent(new CustomEvent('photovault:gallery-updated'));
		} catch (error) {
			if (error.name === 'AbortError') return;
			page = append ? Math.max(1, page - 1) : page;
			if (window.PhotoVaultProtectionNotice) window.PhotoVaultProtectionNotice('La galerie ne peut pas etre actualisee pour le moment.');
		} finally {
			if (requestController !== controller) return;
			results.setAttribute('aria-busy', 'false');
			more.disabled = false;
			more.removeAttribute('aria-busy');
		}
	}

	form.querySelectorAll('select').forEach(field => field.addEventListener('change', () => updateGallery(false)));
	document.getElementById('filter-search').addEventListener('input', function() { window.clearTimeout(debounce); debounce = window.setTimeout(() => updateGallery(false), 320); });
	reset.addEventListener('click', function() { window.setTimeout(() => updateGallery(false), 0); });
	more.addEventListener('click', function() { updateGallery(true); });
});
</script>

<?php get_footer(); ?>
