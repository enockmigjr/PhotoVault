<?php
/**
 * Custom Post Types du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistrement du Custom Post Type 'media_item'.
 */
function photovault_register_post_types() {
	$labels = array(
		'name'               => _x( 'Médias', 'post type general name', 'photovault' ),
		'singular_name'      => _x( 'Média', 'post type singular name', 'photovault' ),
		'menu_name'          => _x( 'PhotoVault', 'admin menu', 'photovault' ),
		'name_admin_bar'     => _x( 'Média', 'add new on admin bar', 'photovault' ),
		'add_new'            => _x( 'Ajouter Nouveau', 'media_item', 'photovault' ),
		'add_new_item'       => __( 'Ajouter un nouveau média', 'photovault' ),
		'new_item'           => __( 'Nouveau média', 'photovault' ),
		'edit_item'          => __( 'Modifier le média', 'photovault' ),
		'view_item'          => __( 'Voir le média', 'photovault' ),
		'all_items'          => __( 'Tous les médias', 'photovault' ),
		'search_items'       => __( 'Rechercher des médias', 'photovault' ),
		'parent_item_colon'  => __( 'Médias parents :', 'photovault' ),
		'not_found'          => __( 'Aucun média trouvé.', 'photovault' ),
		'not_found_in_trash' => __( 'Aucun média trouvé dans la corbeille.', 'photovault' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'gallery', 'with_front' => false ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-images-alt2',
		'show_in_rest'       => true, // Essentiel pour l'API REST
		'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
		'capability_type'     => array( 'media_item', 'media_items' ),
		'map_meta_cap'        => true, // Laisse WP mapper automatiquement les capacités
	);

	register_post_type( 'media_item', $args );
}
add_action( 'init', 'photovault_register_post_types' );

/**
 * Ajouter la Metabox "Protection du Média" dans l'éditeur de media_item.
 */
function photovault_add_protection_metabox() {
	add_meta_box(
		'photovault_protection_meta',
		__( 'Options de protection', 'photovault' ),
		'photovault_protection_metabox_callback',
		'media_item',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'photovault_add_protection_metabox' );

/**
 * Rendu de la Metabox de protection.
 */
function photovault_protection_metabox_callback( $post ) {
	wp_nonce_field( 'photovault_protection_save', 'photovault_protection_nonce' );
	$value = get_post_meta( $post->ID, 'is_protected', true );
	?>
	<p>
		<label for="photovault_is_protected">
			<input type="checkbox" name="photovault_is_protected" id="photovault_is_protected" value="1" <?php checked( $value, '1' ); ?>>
			<?php _e( '🔒 Activer la protection', 'photovault' ); ?>
		</label>
	</p>
	<p class="description">
		<?php _e( 'Empêche le clic droit, bloque le téléchargement direct pour les utilisateurs et ajoute un filigrane de sécurité.', 'photovault' ); ?>
	</p>
	<?php
}

/**
 * Sauvegarder la valeur de protection.
 */
function photovault_save_protection_metabox( $post_id ) {
	if ( ! isset( $_POST['photovault_protection_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['photovault_protection_nonce'], 'photovault_protection_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$is_protected = isset( $_POST['photovault_is_protected'] ) ? '1' : '0';
	update_post_meta( $post_id, 'is_protected', $is_protected );
}
add_action( 'save_post', 'photovault_save_protection_metabox' );

/**
 * Ajouter le sous-menu "Réglages" sous le menu PhotoVault dans wp-admin.
 */
function photovault_register_settings_menu() {
	add_submenu_page(
		'edit.php?post_type=media_item',
		__( 'Réglages PhotoVault', 'photovault' ),
		__( 'Réglages', 'photovault' ),
		'manage_options',
		'photovault-settings',
		'photovault_render_settings_page'
	);
}
add_action( 'admin_menu', 'photovault_register_settings_menu' );

/**
 * Enregistrer l'option de filigrane.
 */
function photovault_register_settings_fields() {
	register_setting( 'photovault-settings-group', 'photovault_watermark_text' );
}
add_action( 'admin_init', 'photovault_register_settings_fields' );

/**
 * Rendu de la page de réglages.
 */
function photovault_render_settings_page() {
	?>
	<div class="wrap" style="padding: 20px; max-width: 800px; border-radius: 12px; margin-top: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); background: #1e293b; color: #f8fafc; font-family: 'Inter', sans-serif;">
		<h1 style="color: #f8fafc; font-weight: 800; border-bottom: 1px solid #475569; padding-bottom: 15px; font-size: 24px;"><?php _e( 'Configuration du filigrane PhotoVault', 'photovault' ); ?></h1>
		
		<form method="post" action="options.php" style="margin-top: 20px;">
			<?php settings_fields( 'photovault-settings-group' ); ?>
			<?php do_settings_sections( 'photovault-settings-group' ); ?>
			
			<table class="form-table" style="width: 100%; border-collapse: collapse;">
				<tr style="border-bottom: 1px solid #334155;">
					<th scope="row" style="padding: 20px 10px; width: 250px; font-weight: 600; text-align: left; vertical-align: middle; color: #f8fafc;">
						<label for="photovault_watermark_text"><?php _e( 'Texte du filigrane', 'photovault' ); ?></label>
					</th>
					<td style="padding: 20px 10px;">
						<input type="text" id="photovault_watermark_text" name="photovault_watermark_text" value="<?php echo esc_attr( get_option( 'photovault_watermark_text', 'PHOTOVAULT' ) ); ?>" class="regular-text" style="background: #0f172a; color: #f8fafc; border: 1px solid #475569; border-radius: 6px; padding: 8px 12px; width: 100%; max-width: 400px; font-size: 14px;">
						<p class="description" style="color: #94a3b8; font-size: 12px; margin-top: 5px;"><?php _e( 'Ce texte s\'affichera de manière répétée en diagonale sur les aperçus d\'images protégées.', 'photovault' ); ?></p>
					</td>
				</tr>
			</table>
			
			<div style="margin-top: 30px;">
				<?php submit_button( __( 'Enregistrer les réglages', 'photovault' ), 'primary', 'submit', true, array( 'style' => 'background: #4f46e5; border: none; border-radius: 6px; padding: 10px 24px; font-weight: 600; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2); cursor: pointer; color: #ffffff;' ) ); ?>
			</div>
		</form>
	</div>
	<?php
}
