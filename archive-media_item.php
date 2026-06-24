<?php
/**
 * Archive publique des médias (archive-media_item.php) de PhotoVault.
 * Inspired by Adobe Portfolio, Flickr, Unsplash.
 *
 * @package PhotoVault
 */

get_header();

$folders = get_terms( array( 'taxonomy' => 'media_folder', 'hide_empty' => false ) );
$categories = get_terms( array( 'taxonomy' => 'media_category', 'hide_empty' => false ) );

// Requête initiale (médias publics). L'admin voit également ses images privées.
$post_statuses = array( 'publish' );
if ( current_user_can( 'manage_options' ) ) {
	$post_statuses[] = 'private';
}

$args = array(
	'post_type'      => 'media_item',
	'post_status'    => $post_statuses,
	'posts_per_page' => 12,
);
$query = new WP_Query( $args );
?>

<div class="py-16 bg-[#0b0f19] min-h-screen text-gray-100 font-sans">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- En-tête de section avec animation -->
		<header class="mb-14 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center gap-6 border-b border-gray-900 pb-10">
			<div>
				<h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight bg-gradient-to-r from-white via-gray-200 to-indigo-400 bg-clip-text text-transparent">Galerie d'Art Visuel</h1>
				<p class="text-gray-400 mt-2 text-sm sm:text-base font-medium max-w-xl">Explorez des photographies exclusives et des projets captivants protégés par PhotoVault.</p>
			</div>
			<div>
				<span class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold tracking-wider uppercase">
					🎯 Collection Active
				</span>
			</div>
		</header>

		<div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
			<!-- Panneau de Filtres à gauche -->
			<aside class="space-y-6 lg:col-span-1">
				<div class="glass-effect p-6 rounded-3xl border border-gray-800/80 space-y-6 sticky top-28 shadow-xl">
					<div class="flex items-center justify-between border-b border-gray-800/50 pb-4">
						<h3 class="text-base font-bold text-white tracking-wide uppercase flex items-center">
							<svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
							Filtres
						</h3>
						<button type="reset" id="reset-filters" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium cursor-pointer transition-colors">Réinitialiser</button>
					</div>
					
					<!-- Formulaire de filtres -->
					<form id="filters-form" class="space-y-5" onsubmit="return false;">
						<!-- Recherche -->
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Recherche</label>
							<div class="relative">
								<input type="text" id="filter-search" name="search" placeholder="Mots-clés..." class="w-full pl-9 pr-3 py-3 border border-gray-800 rounded-xl bg-gray-950/40 text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder-gray-600">
								<svg class="absolute left-3 top-3.5 h-3.5 w-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
							</div>
						</div>

						<!-- Dossier -->
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Dossier / Projet</label>
							<select id="filter-folder" name="folder" class="w-full px-3 py-3 border border-gray-800 rounded-xl bg-gray-950/40 text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
								<option value="" class="bg-[#0f172a]">Tous les dossiers</option>
								<?php foreach ( $folders as $fold ) : ?>
									<option value="<?php echo esc_attr( $fold->term_id ); ?>" class="bg-[#0f172a]"><?php echo esc_html( $fold->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Catégorie -->
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catégorie</label>
							<select id="filter-category" name="category" class="w-full px-3 py-3 border border-gray-800 rounded-xl bg-gray-950/40 text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
								<option value="" class="bg-[#0f172a]">Toutes les catégories</option>
								<?php foreach ( $categories as $cat ) : ?>
									<option value="<?php echo esc_attr( $cat->term_id ); ?>" class="bg-[#0f172a]"><?php echo esc_html( $cat->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Protection -->
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Protection</label>
							<select id="filter-protected" name="protected" class="w-full px-3 py-3 border border-gray-800 rounded-xl bg-gray-950/40 text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
								<option value="" class="bg-[#0f172a]">Tous les statuts</option>
								<option value="1" class="bg-[#0f172a]">🔒 Protégé uniquement</option>
								<option value="0" class="bg-[#0f172a]">Non protégé uniquement</option>
							</select>
						</div>

						<!-- Tri -->
						<div class="space-y-1.5">
							<label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Trier par</label>
							<select id="filter-orderby" name="orderby" class="w-full px-3 py-3 border border-gray-800 rounded-xl bg-gray-950/40 text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
								<option value="date_desc" class="bg-[#0f172a]">Date décroissante</option>
								<option value="date_asc" class="bg-[#0f172a]">Date croissante</option>
								<option value="alphabetical" class="bg-[#0f172a]">Ordre alphabétique</option>
							</select>
						</div>
					</form>
				</div>
			</aside>

			<!-- Grille d'affichage à droite -->
			<div class="lg:col-span-3 space-y-8">
				<div id="media-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 transition-all duration-300">
					<?php if ( $query->have_posts() ) : ?>
						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php get_template_part( 'templates/media-card' ); ?>
						<?php endwhile; wp_reset_postdata(); ?>
					<?php else : ?>
						<div class="col-span-full text-center py-24 glass-effect rounded-3xl border border-gray-800">
							<svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
							<p class="text-gray-400 font-semibold text-lg">Aucun média public disponible</p>
							<p class="text-gray-600 text-xs mt-1">Connectez-vous ou contactez l'administrateur pour ajouter vos créations.</p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('filters-form');
	const grid = document.getElementById('media-grid');
	const resetButton = document.getElementById('reset-filters');

	// Sécurisation de photovault_ajax en cas de non initialisation
	const pvAjax = window.photovault_ajax || {
		rest_url: '/wp-json/photovault/v1'
	};

	// Fonction de fetch et mise à jour de la grille
	function updateMediaGrid() {
		const search = document.getElementById('filter-search').value;
		const folder = document.getElementById('filter-folder').value;
		const category = document.getElementById('filter-category').value;
		const isProtected = document.getElementById('filter-protected').value;
		const orderby = document.getElementById('filter-orderby').value;

		// Construire l'URL de la requête API REST
		let url = `${pvAjax.rest_url}/media?`;
		if (search) url += `search=${encodeURIComponent(search)}&`;
		if (folder) url += `folder=${folder}&`;
		if (category) url += `category=${category}&`;
		if (isProtected) url += `protected=${isProtected}&`;
		if (orderby) url += `orderby=${orderby}&`;

		// Transition visuelle
		grid.style.opacity = '0.3';
		grid.style.filter = 'blur(4px)';

		fetch(url, {
			headers: {
				'X-WP-Nonce': pvAjax.nonce
			}
		})
			.then(response => response.json())
			.then(res => {
				grid.style.opacity = '1';
				grid.style.filter = 'none';
				if (res.success && res.data.length > 0) {
					grid.innerHTML = res.data.map(item => item.html).join('');
				} else {
					grid.innerHTML = `
						<div class="col-span-full text-center py-20 glass-effect rounded-3xl border border-gray-800/80">
							<svg class="mx-auto h-10 w-10 text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
							<p class="text-gray-400 font-medium">Aucun média ne correspond à vos filtres.</p>
						</div>
					`;
				}
			})
			.catch(err => {
				grid.style.opacity = '1';
				grid.style.filter = 'none';
				console.error(err);
			});
	}

	// Écouter les modifications des filtres
	form.querySelectorAll('select').forEach(select => {
		select.addEventListener('change', updateMediaGrid);
	});

	// Écouter les modifications clavier avec debounce
	let timeout = null;
	document.getElementById('filter-search').addEventListener('input', function() {
		clearTimeout(timeout);
		timeout = setTimeout(updateMediaGrid, 300);
	});

	// Réinitialisation des filtres
	resetButton.addEventListener('click', function() {
		form.reset();
		updateMediaGrid();
	});
});
</script>

<?php get_footer(); ?>
