<?php
/**
 * Admin interface for Identity Security Kit.
 *
 * @package IdentitySecurityKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Identity Kit admin screens.
 */
function identity_security_kit_register_admin_menu() {
	add_menu_page(
		__( 'Identity Kit', 'identity-security-kit' ),
		__( 'Identity Kit', 'identity-security-kit' ),
		'identity_manage_settings',
		'identity-security-kit',
		'identity_security_kit_render_admin_page',
		'dashicons-shield-alt',
		57
	);

	add_submenu_page(
		'identity-security-kit',
		__( 'Overview', 'identity-security-kit' ),
		__( 'Overview', 'identity-security-kit' ),
		'identity_manage_settings',
		'identity-security-kit',
		'identity_security_kit_render_admin_page'
	);
}
add_action( 'admin_menu', 'identity_security_kit_register_admin_menu' );

/**
 * Count users with a PhotoVault-compatible avatar meta key.
 *
 * @return int
 */
function identity_security_kit_count_profile_avatars() {
	global $wpdb;

	$meta_key = sanitize_key( apply_filters( 'identity_security_kit_avatar_meta_key', 'photovault_avatar_id' ) );

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value <> ''",
			$meta_key
		)
	);
}

/**
 * Render a small metric card.
 *
 * @param string $label Metric label.
 * @param string $value Metric value.
 * @param string $note  Supporting note.
 */
function identity_security_kit_render_metric( $label, $value, $note ) {
	?>
	<div class="isk-card">
		<span><?php echo esc_html( $label ); ?></span>
		<strong><?php echo esc_html( $value ); ?></strong>
		<em><?php echo esc_html( $note ); ?></em>
	</div>
	<?php
}

/**
 * Render the admin page.
 */
function identity_security_kit_render_admin_page() {
	if ( ! current_user_can( 'identity_manage_settings' ) ) {
		wp_die( esc_html__( 'You are not allowed to manage Identity Kit settings.', 'identity-security-kit' ) );
	}

	$settings       = identity_security_kit_get_settings();
	$users_count    = count_users();
	$total_users    = isset( $users_count['total_users'] ) ? (int) $users_count['total_users'] : 0;
	$avatar_count   = identity_security_kit_count_profile_avatars();
	$capabilities   = identity_security_kit_get_capabilities();
	$settings_saved = isset( $_GET['settings-updated'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) );
	?>
	<div class="wrap identity-security-kit-admin">
		<h1><?php esc_html_e( 'Identity Security Kit', 'identity-security-kit' ); ?></h1>
		<p><?php esc_html_e( 'Front-office identity controls, server-side validation, and the foundation for stronger authentication policies.', 'identity-security-kit' ); ?></p>

		<?php if ( $settings_saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Identity settings saved.', 'identity-security-kit' ); ?></p></div>
		<?php endif; ?>

		<div class="isk-grid">
			<?php identity_security_kit_render_metric( __( 'Users', 'identity-security-kit' ), number_format_i18n( $total_users ), __( 'WordPress accounts', 'identity-security-kit' ) ); ?>
			<?php identity_security_kit_render_metric( __( 'Avatars', 'identity-security-kit' ), number_format_i18n( $avatar_count ), __( 'Profiles with custom avatar', 'identity-security-kit' ) ); ?>
			<?php identity_security_kit_render_metric( __( 'Password min', 'identity-security-kit' ), (string) $settings['min_password_length'], __( 'Characters required', 'identity-security-kit' ) ); ?>
			<?php identity_security_kit_render_metric( __( 'Capabilities', 'identity-security-kit' ), (string) count( $capabilities ), __( 'Granted to administrators', 'identity-security-kit' ) ); ?>
		</div>

		<div class="isk-layout">
			<section class="isk-panel">
				<h2><?php esc_html_e( 'Security settings', 'identity-security-kit' ); ?></h2>
				<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="identity_security_kit_save_settings">
					<?php wp_nonce_field( 'identity_security_kit_save_settings' ); ?>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="isk_min_password_length"><?php esc_html_e( 'Minimum password length', 'identity-security-kit' ); ?></label></th>
							<td>
								<input id="isk_min_password_length" class="small-text" name="min_password_length" type="number" min="8" max="128" value="<?php echo esc_attr( $settings['min_password_length'] ); ?>">
								<p class="description"><?php esc_html_e( 'Bounded between 8 and 128 characters. Server-side only; frontend fields cannot bypass it.', 'identity-security-kit' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="isk_max_avatar_size_mb"><?php esc_html_e( 'Max avatar size', 'identity-security-kit' ); ?></label></th>
							<td>
								<input id="isk_max_avatar_size_mb" class="small-text" name="max_avatar_size_mb" type="number" min="1" max="12" value="<?php echo esc_attr( $settings['max_avatar_size_mb'] ); ?>"> <?php esc_html_e( 'MB', 'identity-security-kit' ); ?>
								<p class="description"><?php esc_html_e( 'Bounded between 1 MB and 12 MB to avoid oversized profile uploads.', 'identity-security-kit' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="isk_max_avatar_dimension"><?php esc_html_e( 'Max avatar dimension', 'identity-security-kit' ); ?></label></th>
							<td>
								<input id="isk_max_avatar_dimension" class="small-text" name="max_avatar_dimension" type="number" min="512" max="6000" value="<?php echo esc_attr( $settings['max_avatar_dimension'] ); ?>"> px
								<p class="description"><?php esc_html_e( 'Bounded between 512px and 6000px per side.', 'identity-security-kit' ); ?></p>
							</td>
						</tr>
					</table>

					<?php submit_button( __( 'Save settings', 'identity-security-kit' ) ); ?>
				</form>
			</section>

			<section class="isk-panel">
				<h2><?php esc_html_e( 'Capabilities', 'identity-security-kit' ); ?></h2>
				<ul class="isk-list">
					<?php foreach ( $capabilities as $capability ) : ?>
						<li><code><?php echo esc_html( $capability ); ?></code></li>
					<?php endforeach; ?>
				</ul>
				<h2><?php esc_html_e( 'Next security modules', 'identity-security-kit' ); ?></h2>
				<ul class="isk-list">
					<li><?php esc_html_e( 'Email verification challenges', 'identity-security-kit' ); ?></li>
					<li><?php esc_html_e( 'OTP attempt limits and resend throttling', 'identity-security-kit' ); ?></li>
					<li><?php esc_html_e( 'TOTP/MFA enrollment and grace policy', 'identity-security-kit' ); ?></li>
					<li><?php esc_html_e( 'Security audit events', 'identity-security-kit' ); ?></li>
				</ul>
			</section>
		</div>
	</div>
	<style>
		.identity-security-kit-admin .isk-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0}.identity-security-kit-admin .isk-card,.identity-security-kit-admin .isk-panel{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px}.identity-security-kit-admin .isk-card span{display:block;color:#646970;font-size:12px;text-transform:uppercase;letter-spacing:.08em}.identity-security-kit-admin .isk-card strong{display:block;margin-top:8px;font-size:28px}.identity-security-kit-admin .isk-card em{display:block;margin-top:4px;color:#646970;font-style:normal}.identity-security-kit-admin .isk-layout{display:grid;grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:16px}.identity-security-kit-admin .isk-list{margin-left:0}.identity-security-kit-admin .isk-list li{border-bottom:1px solid #f0f0f1;margin:0;padding:8px 0}@media(max-width:960px){.identity-security-kit-admin .isk-grid,.identity-security-kit-admin .isk-layout{grid-template-columns:1fr}}
	</style>
	<?php
}

/**
 * Save admin settings.
 */
function identity_security_kit_handle_save_settings() {
	if ( ! current_user_can( 'identity_manage_settings' ) ) {
		wp_die( esc_html__( 'You are not allowed to update Identity Kit settings.', 'identity-security-kit' ) );
	}

	check_admin_referer( 'identity_security_kit_save_settings' );

	$settings = array(
		'min_password_length'  => isset( $_POST['min_password_length'] ) ? max( 8, min( 128, absint( $_POST['min_password_length'] ) ) ) : 8,
		'max_avatar_size_mb'   => isset( $_POST['max_avatar_size_mb'] ) ? max( 1, min( 12, absint( $_POST['max_avatar_size_mb'] ) ) ) : 6,
		'max_avatar_dimension' => isset( $_POST['max_avatar_dimension'] ) ? max( 512, min( 6000, absint( $_POST['max_avatar_dimension'] ) ) ) : 6000,
	);

	update_option( 'identity_security_kit_settings', $settings, false );
	wp_safe_redirect( admin_url( 'admin.php?page=identity-security-kit&settings-updated=true' ) );
	exit;
}
add_action( 'admin_post_identity_security_kit_save_settings', 'identity_security_kit_handle_save_settings' );