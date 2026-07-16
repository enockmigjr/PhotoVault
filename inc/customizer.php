<?php
/**
 * Public header and footer customization controls.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register restrained presentation settings with current design defaults. */
function photovault_customize_register( $customizer ) {
	$customizer->add_section(
		'photovault_header',
		array(
			'title'       => __( 'PhotoVault header', 'photovault' ),
			'description' => __( 'The primary menu and its submenus are managed in Appearance > Menus.', 'photovault' ),
			'priority'    => 31,
		)
	);
	$customizer->add_setting( 'photovault_brand_name', array( 'default' => 'PhotoVault', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$customizer->add_control( 'photovault_brand_name', array( 'section' => 'photovault_header', 'label' => __( 'Text logo fallback', 'photovault' ), 'type' => 'text' ) );
	$customizer->add_setting( 'photovault_header_login_label', array( 'default' => __( 'Connexion', 'photovault' ), 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$customizer->add_control( 'photovault_header_login_label', array( 'section' => 'photovault_header', 'label' => __( 'Login label', 'photovault' ), 'type' => 'text' ) );
	$customizer->add_setting( 'photovault_header_join_label', array( 'default' => __( 'Rejoindre', 'photovault' ), 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$customizer->add_control( 'photovault_header_join_label', array( 'section' => 'photovault_header', 'label' => __( 'Registration CTA label', 'photovault' ), 'type' => 'text' ) );
	$customizer->add_setting( 'photovault_header_join_url', array( 'default' => home_url( '/register/' ), 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$customizer->add_control( 'photovault_header_join_url', array( 'section' => 'photovault_header', 'label' => __( 'Registration CTA URL', 'photovault' ), 'type' => 'url' ) );

	$customizer->add_section( 'photovault_footer', array( 'title' => __( 'PhotoVault footer', 'photovault' ), 'description' => __( 'Footer link columns are managed in Appearance > Menus.', 'photovault' ), 'priority' => 32 ) );
	$footer_fields = array(
		'photovault_footer_tagline' => array( __( 'Editorial tagline', 'photovault' ), __( 'Des archives visuelles pour ce qui mérite de rester.', 'photovault' ), 'text' ),
		'photovault_footer_description' => array( __( 'Studio description', 'photovault' ), __( 'Portfolio officiel, collections protégées et créations photographiques sur mesure entre Porto-Novo, Cotonou et les territoires documentés.', 'photovault' ), 'textarea' ),
		'photovault_footer_newsletter_title' => array( __( 'Newsletter heading', 'photovault' ), __( 'Lettre des archives', 'photovault' ), 'text' ),
		'photovault_footer_newsletter_description' => array( __( 'Newsletter description', 'photovault' ), __( 'Nouvelles séries, carnets d’atelier et invitations privées, avec votre consentement.', 'photovault' ), 'textarea' ),
		'photovault_footer_credit' => array( __( 'Footer credit', 'photovault' ), __( 'Direction artistique et développement par Enok Junior MIGNANWANDE.', 'photovault' ), 'text' ),
	);
	foreach ( $footer_fields as $setting => $field ) {
		$customizer->add_setting( $setting, array( 'default' => $field[1], 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ) );
		$customizer->add_control( $setting, array( 'section' => 'photovault_footer', 'label' => $field[0], 'type' => $field[2] ) );
	}
}
add_action( 'customize_register', 'photovault_customize_register' );

/** Return a customized text while preserving a non-empty design default. */
function photovault_theme_text( $setting, $default ) {
	$value = trim( (string) get_theme_mod( $setting, $default ) );

	return '' !== $value ? $value : $default;
}

/** Render a footer menu or its current default links. */
function photovault_render_footer_menu( $location, $fallback_links ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'mt-5 space-y-3 text-sm pv-footer-menu',
				'depth'          => 2,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	?>
	<ul class="mt-5 space-y-3 text-sm pv-footer-menu">
		<?php foreach ( $fallback_links as $link ) : if ( empty( $link['url'] ) ) { continue; } ?>
			<li><a class="pv-footer-link" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php
}
