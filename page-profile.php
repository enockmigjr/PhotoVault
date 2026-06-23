<?php
/**
 * Template Name: PhotoVault Profile
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/login/' ) );
	exit;
}

$current_user = wp_get_current_user();
$avatar_id = get_user_meta( $current_user->ID, 'photovault_avatar_id', true );
$avatar_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $current_user->ID );

get_header();
?>

<div class="flex min-h-screen bg-[#0b0f19]">
	<!-- Barre latérale -->
	<?php get_template_part( 'templates/dashboard-sidebar' ); ?>

	<!-- Contenu Principal -->
	<main class="flex-1 p-10 overflow-y-auto">
		<div class="max-w-4xl mx-auto">
			<header class="mb-8">
				<h2 class="text-3xl font-extrabold text-white">Mon Profil</h2>
				<p class="text-gray-400 mt-1">Gérez vos informations de compte et de présentation.</p>
			</header>

			<?php if ( isset( $_GET['profile'] ) && 'success' === $_GET['profile'] ) : ?>
				<div class="mb-6 bg-emerald-900/30 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-lg text-sm">
					Profil mis à jour avec succès.
				</div>
			<?php elseif ( isset( $_GET['profile'] ) && 'pwd_mismatch' === $_GET['profile'] ) : ?>
				<div class="mb-6 bg-red-900/30 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm">
					Les mots de passe saisis ne correspondent pas.
				</div>
			<?php endif; ?>

			<form class="space-y-8 glass-effect p-8 rounded-2xl" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="POST" enctype="multipart/form-data">
				<?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?>

				<!-- Section Avatar -->
				<div class="flex items-center space-x-6 pb-6 border-b border-gray-800">
					<img class="h-24 w-24 rounded-full object-cover border-4 border-indigo-500/30 shadow-lg" src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar">
					<div>
						<label class="block text-sm font-semibold text-gray-300">Photo de profil</label>
						<input type="file" name="profile_avatar" accept="image/*" class="mt-2 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer">
					</div>
				</div>

				<!-- Section Infos -->
				<div class="grid grid-cols-2 gap-6">
					<div>
						<label for="display_name" class="block text-sm font-medium text-gray-300 mb-1">Nom d'affichage</label>
						<input id="display_name" type="text" disabled value="<?php echo esc_attr( $current_user->display_name ); ?>" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/30 text-gray-500 text-sm cursor-not-allowed">
					</div>
					<div>
						<label for="email" class="block text-sm font-medium text-gray-300 mb-1">E-mail</label>
						<input id="email" name="email" type="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>
				</div>

				<!-- Section Biographie -->
				<div>
					<label for="bio" class="block text-sm font-medium text-gray-300 mb-1">Bio / Présentation</label>
					<textarea id="bio" name="bio" rows="4" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Parlez de votre univers photographique..."><?php echo esc_textarea( $current_user->description ); ?></textarea>
				</div>

				<!-- Section Sécurité (Optionnelle) -->
				<div class="pt-6 border-t border-gray-800">
					<h3 class="text-lg font-semibold text-white mb-4">Changer de mot de passe</h3>
					<div class="grid grid-cols-2 gap-6">
						<div>
							<label for="password" class="block text-sm font-medium text-gray-300 mb-1">Nouveau mot de passe</label>
							<input id="password" name="password" type="password" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Laisser vide pour ne pas modifier">
						</div>
						<div>
							<label for="password_confirm" class="block text-sm font-medium text-gray-300 mb-1">Confirmer le mot de passe</label>
							<input id="password_confirm" name="password_confirm" type="password" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Laisser vide pour ne pas modifier">
						</div>
					</div>
				</div>

				<div class="flex justify-end pt-4">
					<button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
						Enregistrer les modifications
					</button>
				</div>
			</form>
		</div>
	</main>
</div>

<?php get_footer(); ?>
