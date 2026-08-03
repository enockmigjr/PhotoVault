<?php
/**
 * Connector settings screen.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the connector settings page. */
function trouble_ticket_connector_admin_menu() {
	add_options_page(
		__( 'Trouble Ticket Connector', 'trouble-ticket-connector' ),
		__( 'Support client', 'trouble-ticket-connector' ),
		'manage_trouble_ticket_connector',
		'trouble-ticket-connector',
		'trouble_ticket_connector_render_admin_page'
	);
}
add_action( 'admin_menu', 'trouble_ticket_connector_admin_menu' );

/**
 * Load styles only on the connector screen.
 *
 * @param string $hook Current admin hook.
 */
function trouble_ticket_connector_admin_assets( $hook ) {
	if ( 'settings_page_trouble-ticket-connector' === $hook ) {
		wp_enqueue_style( 'trouble-ticket-connector-admin', TROUBLE_TICKET_CONNECTOR_URL . 'assets/admin.css', array(), TROUBLE_TICKET_CONNECTOR_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'trouble_ticket_connector_admin_assets' );

/** Render the connector settings screen. */
function trouble_ticket_connector_render_admin_page() {
	if ( ! current_user_can( 'manage_trouble_ticket_connector' ) && ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$settings = trouble_ticket_connector_get_settings();
	?>
	<div class="wrap ttc-admin">
		<h1><?php esc_html_e( 'Connecteur de support client', 'trouble-ticket-connector' ); ?></h1>
		<p><?php esc_html_e( 'WordPress fournit uniquement une preuve d’identité courte. Les tickets restent dans la plateforme de support.', 'trouble-ticket-connector' ); ?></p>
		<?php trouble_ticket_connector_admin_notice(); ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="trouble_ticket_connector_save">
			<?php wp_nonce_field( 'trouble_ticket_connector_save' ); ?>
			<table class="form-table" role="presentation">
				<?php trouble_ticket_connector_text_row( 'support_url', __( 'URL du portail support', 'trouble-ticket-connector' ), $settings['support_url'], 'url' ); ?>
				<?php trouble_ticket_connector_text_row( 'integration_key', __( 'Clé publique / issuer', 'trouble-ticket-connector' ), $settings['integration_key'] ); ?>
				<?php trouble_ticket_connector_text_row( 'integration_id', __( 'ID de l’intégration', 'trouble-ticket-connector' ), $settings['integration_id'] ); ?>
				<?php trouble_ticket_connector_text_row( 'audience', __( 'Audience', 'trouble-ticket-connector' ), $settings['audience'] ); ?>
				<?php trouble_ticket_connector_text_row( 'origin', __( 'Origine exacte du site', 'trouble-ticket-connector' ), $settings['origin'], 'url' ); ?>
				<?php trouble_ticket_connector_text_row( 'label', __( 'Libellé du bouton', 'trouble-ticket-connector' ), $settings['label'] ); ?>
				<tr><th scope="row"><?php esc_html_e( 'Affichage', 'trouble-ticket-connector' ); ?></th><td><select name="settings[display_mode]"><option value="shortcode" <?php selected( $settings['display_mode'], 'shortcode' ); ?>><?php esc_html_e( 'Shortcode uniquement', 'trouble-ticket-connector' ); ?></option><option value="auto" <?php selected( $settings['display_mode'], 'auto' ); ?>><?php esc_html_e( 'Automatique', 'trouble-ticket-connector' ); ?></option></select></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Pages automatiques', 'trouble-ticket-connector' ); ?></th><td><input class="regular-text" name="settings[page_ids]" value="<?php echo esc_attr( implode( ',', $settings['page_ids'] ) ); ?>"><p class="description"><?php esc_html_e( 'IDs séparés par des virgules. Vide signifie toutes les pages.', 'trouble-ticket-connector' ); ?></p></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Position', 'trouble-ticket-connector' ); ?></th><td><select name="settings[position]"><option value="right" <?php selected( $settings['position'], 'right' ); ?>><?php esc_html_e( 'Droite', 'trouble-ticket-connector' ); ?></option><option value="left" <?php selected( $settings['position'], 'left' ); ?>><?php esc_html_e( 'Gauche', 'trouble-ticket-connector' ); ?></option></select></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Intégrité du chargeur', 'trouble-ticket-connector' ); ?></th><td><input class="large-text code" name="settings[loader_integrity]" value="<?php echo esc_attr( $settings['loader_integrity'] ); ?>"></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Secret d’intégration', 'trouble-ticket-connector' ); ?></th><td><input class="regular-text" type="password" name="integration_secret" autocomplete="new-password" value=""><p class="description"><?php /* translators: %s is the non-sensitive secret source name. */ echo esc_html( sprintf( __( 'Source actuelle : %s. Laisser vide pour conserver la valeur.', 'trouble-ticket-connector' ), trouble_ticket_connector_secret_source() ) ); ?></p></td></tr>
			</table>
			<?php submit_button( __( 'Enregistrer / faire une rotation locale', 'trouble-ticket-connector' ) ); ?>
		</form>
		<div class="ttc-actions"><?php trouble_ticket_connector_admin_action_forms(); ?></div>
		<p><code>[trouble_ticket_support]</code></p>
	</div>
	<?php
}

/**
 * Render one escaped text setting row.
 *
 * @param string $name  Setting name.
 * @param string $label Human-readable label.
 * @param string $value Current value.
 * @param string $type  HTML input type.
 */
function trouble_ticket_connector_text_row( $name, $label, $value, $type = 'text' ) {
	printf( '<tr><th scope="row"><label for="ttc-%1$s">%2$s</label></th><td><input class="regular-text" id="ttc-%1$s" type="%3$s" name="settings[%1$s]" value="%4$s"></td></tr>', esc_attr( $name ), esc_html( $label ), esc_attr( $type ), esc_attr( $value ) );
}

/** Render connection-test and local-revocation forms. */
function trouble_ticket_connector_admin_action_forms() {
	foreach ( array(
		'test'   => __( 'Tester la connexion publique', 'trouble-ticket-connector' ),
		'revoke' => __( 'Révoquer le secret local', 'trouble-ticket-connector' ),
	) as $action => $label ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="trouble_ticket_connector_' . esc_attr( $action ) . '">';
		wp_nonce_field( 'trouble_ticket_connector_' . $action );
		submit_button( $label, 'secondary', 'submit', false );
		echo '</form>';
	}
}

/** Render a safe notice selected from an allowlist. */
function trouble_ticket_connector_admin_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only notice selected from a strict map.
	$notice = isset( $_GET['ttc_notice'] ) ? sanitize_key( wp_unslash( $_GET['ttc_notice'] ) ) : '';
	$map    = array(
		'saved'           => array( 'success', __( 'Configuration enregistrée.', 'trouble-ticket-connector' ) ),
		'test_ok'         => array( 'success', __( 'Le portail reconnaît cette intégration et cette origine.', 'trouble-ticket-connector' ) ),
		'revoked'         => array( 'warning', __( 'Le secret local a été supprimé. Révoquez aussi le credential côté plateforme.', 'trouble-ticket-connector' ) ),
		'constant_secret' => array( 'error', __( 'Le secret provient de la configuration serveur et doit y être retiré.', 'trouble-ticket-connector' ) ),
		'secret_error'    => array( 'error', __( 'Le secret n’a pas pu être chiffré.', 'trouble-ticket-connector' ) ),
		'test_failed'     => array( 'error', __( 'La connexion publique a échoué sans exposer le secret.', 'trouble-ticket-connector' ) ),
	);
	if ( isset( $map[ $notice ] ) ) {
		printf( '<div class="notice notice-%s"><p>%s</p></div>', esc_attr( $map[ $notice ][0] ), esc_html( $map[ $notice ][1] ) );
	}
}
