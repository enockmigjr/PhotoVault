<?php
/**
 * Editorial comments template.
 *
 * @package PhotoVault
 */

if ( post_password_required() ) {
	return;
}

$commenter = wp_get_current_commenter();
$required  = get_option( 'require_name_email' );
$aria_req  = $required ? ' aria-required="true" required' : '';
?>
<div id="comments" class="pv-comments">
	<?php if ( have_comments() ) : ?>
		<header class="mb-8 flex flex-wrap items-end justify-between gap-4">
			<div>
				<p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( 'Conversation', 'photovault' ); ?></p>
				<h2 class="mt-2 font-serif text-3xl text-white">
					<?php echo esc_html( sprintf( _n( '%s commentaire', '%s commentaires', get_comments_number(), 'photovault' ), number_format_i18n( get_comments_number() ) ) ); ?>
				</h2>
			</div>
		</header>

		<ol class="comment-list space-y-6">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 44 ) ); ?>
		</ol>

		<?php the_comments_navigation( array( 'prev_text' => __( 'Commentaires précédents', 'photovault' ), 'next_text' => __( 'Commentaires suivants', 'photovault' ) ) ); ?>

		<?php if ( ! comments_open() ) : ?>
			<p class="mt-8 border-l-2 border-white/20 pl-4 text-sm text-gray-400"><?php esc_html_e( 'La conversation est désormais fermée.', 'photovault' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'           => 'pv-comment-form',
			'class_submit'         => 'pv-comment-submit',
			'title_reply'          => __( 'Laisser une trace', 'photovault' ),
			'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title font-serif text-3xl text-white">',
			'title_reply_after'    => '</h2>',
			'comment_notes_before' => '<p class="mt-3 text-sm leading-6 text-gray-500">' . esc_html__( 'Votre adresse e-mail ne sera pas publiée. Les champs obligatoires sont indiqués.', 'photovault' ) . '</p>',
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Commentaire', 'photovault' ) . '</label><textarea id="comment" name="comment" rows="6" required></textarea></p>',
			'fields'               => array(
				'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( 'Nom', 'photovault' ) . ( $required ? ' <span aria-hidden="true">*</span>' : '' ) . '</label><input id="author" name="author" type="text" autocomplete="name" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . '></p>',
				'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'E-mail', 'photovault' ) . ( $required ? ' <span aria-hidden="true">*</span>' : '' ) . '</label><input id="email" name="email" type="email" autocomplete="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $aria_req . '></p>',
				'url'    => '<p class="comment-form-url"><label for="url">' . esc_html__( 'Site web', 'photovault' ) . '</label><input id="url" name="url" type="url" autocomplete="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '"></p>',
			),
			'label_submit'         => __( 'Publier le commentaire', 'photovault' ),
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
		)
	);
	?>
</div>
