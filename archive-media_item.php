<?php
/**
 * Archive publique des médias (archive-media_item.php) de PhotoVault.
 *
 * @package PhotoVault
 */

get_header();

$folders = get_terms( array( 'taxonomy' => 'media_folder', 'hide_empty' => false ) );
$categories = get_terms( array( 'taxonomy' => 'media_category', 'hide_empty' => false ) );

// Requête initiale (médias publics).
$args = array(
	'post_type'      => 'media_item',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
);
$query = new WP_Query( $args );
?>

<div class="py-12 bg-[#0b0f19] min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<header class="mb-10 flex justify-between items-end">
			<div>
				<h1 class="text-4xl font-extrabold text-white">Galerie Publique</h1>
				<p class="text-gray-400 mt-1">Explorez les photographies exceptionnelles partagées par notre communauté.</p>
			</div>
		</header>

		<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
			<!-- Panneau de Filtres à gauche -->
			<aside class="space-y-6">
				<div class="glass-effect p-6 rounded-2xl border border-gray-800 space-y-6 sticky top-28">
					<h3 class="text-lg font-bold text-white">Filtres de recherche</h3>
					
					<!-- Formulaire de filtres -->
					<form id="filters-form" class="space-y-4">
						<!-- Recherche -->
						<div>
							<label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Recherche</label>
							<input type="text" id="filter-search" name="search" placeholder="Rechercher..." class="w-full px-3 py-2.5 border border-gray-800 rounded-lg bg-gray-900/50 text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
						</div>

						<!-- Dossier -->
						<div>
							<label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Dossier</label>
							<select id="filter-folder" name="folder" class="w-full px-3 py-2.5 border border-gray-800 rounded-lg bg-[#0f172a] text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
								<option value="">Tous les dossiers</option>
								<?php foreach ( $folders as $fold ) : ?>
									<option value="<?php echo esc_attr( $fold->term_id ); ?>"><?php echo esc_html( $fold->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Catégorie -->
						<div>
							<label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Catégorie</label>
							<select id="filter-category" name="category" class="w-full px-3 py-2.5 border border-gray-800 rounded-lg bg-[#0f172a] text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
								<option value="">Toutes les catégories</option>
								<?php foreach ( $categories as $cat ) : ?>
									<option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Protection -->
						<div>
							<label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Protection</label>
							<select id="filter-protected" name="protected" class="w-full px-3 py-2.5 border border-gray-800 rounded-lg bg-[#0f172a] text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
								<option value="">Tous les statuts</option>
								<option value="1">🔒 Protégé uniquement</option>
								<option value="0">Non protégé uniquement</option>
							</select>
						</div>

						<!-- Tri -->
						<div>
							<label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Trier par</label>
							<select id="filter-orderby" name="orderby" class="w-full px-3 py-2.5 border border-gray-800 rounded-lg bg-[#0f172a] text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
								<option value="date_desc">Date décroissante</option>
								<option value="date_asc">Date croissante</option>
								<option value="alphabetical">Ordre alphabétique</option>
							</select>
						</div>
					</form>
				</div>
			</aside>

			<!-- Grille d'affichage à droite -->
			<div class="lg:col-span-3 space-y-8">
				<div id="media-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
					<?php if ( $query->have_posts() ) : ?>
						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php get_template_part( 'templates/media-card' ); ?>
						<?php endwhile; wp_reset_postdata(); ?>
					<?php else : ?>
						<div class="col-span-full text-center py-16 glass-effect rounded-2xl">
							<p class="text-gray-500">Aucun média public trouvé.</p>
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

	// Fonction de fetch et mise à jour de la grille
	function updateMediaGrid() {
		const search = document.getElementById('filter-search').value;
		const folder = document.getElementById('filter-folder').value;
		const category = document.getElementById('filter-category').value;
		const isProtected = document.getElementById('filter-protected').value;
		const orderby = document.getElementById('filter-orderby').value;

		// Construire l'URL de la requête API REST
		let url = `${photovault_ajax.rest_url}/media?`;
		if (search) url += `search=${encodeURIComponent(search)}&`;
		if (folder) url += `folder=${folder}&`;
		if (category) url += `category=${category}&`;
		if (isProtected) url += `protected=${isProtected}&`;
		if (orderby) url += `orderby=${orderby}&`;

		// Animation de chargement
		grid.style.opacity = '0.5';

		fetch(url)
			.then(response => response.json())
			.then(res => {
				grid.style.opacity = '1';
				if (res.success && res.data.length > 0) {
					grid.innerHTML = res.data.map(item => item.html).join('');
				} else {
					grid.innerHTML = `
						<div class="col-span-full text-center py-16 glass-effect rounded-2xl">
							<p class="text-gray-500">Aucun média ne correspond à vos filtres.</p>
						</div>
					`;
				}
			})
			.catch(err => {
				grid.style.opacity = '1';
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
});
</script>

<?php get_footer(); ?>
