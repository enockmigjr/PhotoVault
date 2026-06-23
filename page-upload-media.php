<?php
/**
 * Template Name: PhotoVault Upload
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/login/' ) );
	exit;
}

$folders = get_terms( array( 'taxonomy' => 'media_folder', 'hide_empty' => false ) );
$categories = get_terms( array( 'taxonomy' => 'media_category', 'hide_empty' => false ) );

get_header();
?>

<div class="flex min-h-screen bg-[#0b0f19]">
	<!-- Barre latérale -->
	<?php get_template_part( 'templates/dashboard-sidebar' ); ?>

	<!-- Contenu Principal -->
	<main class="flex-1 p-10 overflow-y-auto">
		<div class="max-w-4xl mx-auto">
			<header class="mb-8">
				<h2 class="text-3xl font-extrabold text-white">Ajouter un nouveau média</h2>
				<p class="text-gray-400 mt-1">Téléversez vos photos et organisez votre bibliothèque.</p>
			</header>

			<form class="space-y-8 glass-effect p-8 rounded-2xl" action="<?php echo esc_url( home_url( '/upload-media/' ) ); ?>" method="POST" enctype="multipart/form-data">
				<?php wp_nonce_field( 'photovault_upload_action', 'photovault_upload_nonce' ); ?>

				<!-- Zone Drag & Drop -->
				<div class="border-2 border-dashed border-gray-700 hover:border-indigo-500 rounded-2xl p-10 text-center transition-all-300 cursor-pointer relative bg-gray-900/20 group">
					<input type="file" name="media_files[]" multiple required accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
					<div class="space-y-3 pointer-events-none">
						<svg class="mx-auto h-12 w-12 text-gray-500 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
						<div class="text-sm text-gray-300">
							<span class="font-semibold text-indigo-400">Cliquez pour téléverser</span> ou glissez-déposez vos fichiers ici
						</div>
						<p class="text-xs text-gray-500">Formats acceptés : JPEG, PNG, WEBP (Max. 10 Mo par fichier)</p>
					</div>
				</div>

				<!-- Détails et Organisation -->
				<div class="grid grid-cols-2 gap-6">
					<div>
						<label for="title" class="block text-sm font-medium text-gray-300 mb-1">Titre par défaut (Optionnel)</label>
						<input id="title" name="title" type="text" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Ex: Coucher de soleil">
					</div>
					<div>
						<label for="category" class="block text-sm font-medium text-gray-300 mb-1">Catégorie Média</label>
						<select id="category" name="category" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-[#0f172a] text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
							<option value="">Sélectionner une catégorie</option>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="grid grid-cols-2 gap-6">
					<div>
						<label for="folder" class="block text-sm font-medium text-gray-300 mb-1">Dossier de rangement</label>
						<select id="folder" name="folder" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-[#0f172a] text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
							<option value="">Sélectionner un dossier</option>
							<?php foreach ( $folders as $fold ) : ?>
								<option value="<?php echo esc_attr( $fold->term_id ); ?>"><?php echo esc_html( $fold->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label for="visibility" class="block text-sm font-medium text-gray-300 mb-1">Visibilité</label>
						<select id="visibility" name="visibility" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-[#0f172a] text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
							<option value="publish">Public (visible par tous)</option>
							<option value="private">Privé (visible uniquement par moi et les admins)</option>
						</select>
					</div>
				</div>

				<div>
					<label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description (Optionnel)</label>
					<textarea id="description" name="description" rows="3" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Décrivez votre image..."></textarea>
				</div>

				<!-- Protection -->
				<div class="flex items-start p-4 bg-indigo-950/20 border border-indigo-500/20 rounded-xl">
					<div class="flex items-center h-5">
						<input id="is_protected" name="is_protected" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-800 rounded bg-gray-900/50">
					</div>
					<div class="ml-3 text-sm">
						<label for="is_protected" class="font-semibold text-white flex items-center">
							🔒 Protéger ce média
						</label>
						<p class="text-gray-400 mt-1">Empêche le clic droit, la sauvegarde de l'image d'origine et superpose un filigrane de protection.</p>
					</div>
				</div>

				<div class="flex justify-end pt-4">
					<button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
						Démarrer le téléversement
					</button>
				</div>
			</form>
		</div>
	</main>
</div>

<?php get_footer(); ?>
