<?php
/**
 * Template de gestion des commentaires (comments.php) de PhotoVault.
 *
 * @package PhotoVault
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area space-y-8 mt-10">

	<?php if ( have_comments() ) : ?>
		<h2 class="text-xl font-bold text-white mb-6">
			<?php
			$comments_number = get_comments_number();
			if ( '1' === $comments_number ) {
				printf( esc_html__( 'Un commentaire', 'photovault' ) );
			} else {
				printf( esc_html( $comments_number ) . ' commentaires' );
			}
			?>
		</h2>

		<ul class="comment-list space-y-6">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 42,
			) );
			?>
		</ul>

		<?php the_comments_navigation(); ?>

		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments text-gray-300 text-sm"><?php esc_html_e( 'Les commentaires sont fermés.', 'photovault' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php
	// Formulaire de commentaire stylisé.
	comment_form( array(
		'class_form'         => 'space-y-4',
		'title_reply_class'  => 'text-lg font-bold text-white mb-4',
		'comment_field'      => '<div><label for="comment" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Votre commentaire</label><textarea id="comment" name="comment" rows="4" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea></div>',
		'fields'             => array(
			'author' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label for="author" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Nom</label><input id="author" name="author" type="text" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required /></div>',
			'email'  => '<div><label for="email" class="block text-xs font-semibold text-gray-300 uppercase mb-1">E-mail</label><input id="email" name="email" type="email" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required /></div></div>',
		),
		'class_submit'       => 'px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all shadow-lg cursor-pointer',
		'label_submit'       => 'Publier le commentaire',
	) );
	?>

</div>
