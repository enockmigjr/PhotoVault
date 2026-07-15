<?php
/**
 * WordPress runtime verification for the public contact notification.
 *
 * Run with: wp eval-file wp-content/themes/PhotoVault/tests/runtime-contact-email.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function photovault_contact_email_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

$captured = array();
$alt_bodies = array();
$mail_filter = static function ( $attributes ) use ( &$captured ) {
	$captured[] = $attributes;

	return $attributes;
};
$alt_filter = static function ( $phpmailer ) use ( &$alt_bodies ) {
	$alt_bodies[] = (string) $phpmailer->AltBody;
};
add_filter( 'wp_mail', $mail_filter, 20 );
add_action( 'phpmailer_init', $alt_filter, 20 );

try {
	$email = 'contact-runtime@photovault.test';
	$sent  = photovault_send_contact_notification(
		array(
			'name'         => 'Runtime Contact',
			'email'        => $email,
			'request_type' => 'license',
			'subject'      => 'Licence editoriale runtime',
			'collection'   => 'Fragments Urbains',
			'message'      => 'Je souhaite recevoir les conditions de licence pour une publication.',
		)
	);
	photovault_contact_email_assert( true === $sent, 'The contact notification was not handed to wp_mail.' );
	photovault_contact_email_assert( 2 === count( $captured ), 'Studio notification and visitor acknowledgement were not both sent.' );
	photovault_contact_email_assert( false !== strpos( $captured[0]['message'], '<table role="presentation"' ) && false !== strpos( $captured[1]['message'], '<table role="presentation"' ), 'The contact emails are not using the professional HTML layout.' );
	photovault_contact_email_assert( in_array( 'Reply-To: ' . $email, $captured[0]['headers'], true ), 'The studio email does not use the visitor as Reply-To.' );
	photovault_contact_email_assert( false === strpos( implode( "\n", $captured[0]['headers'] ), 'From: ' . $email ), 'The visitor address was used as From.' );
	photovault_contact_email_assert( count( array_filter( $alt_bodies ) ) >= 2 && false !== strpos( $alt_bodies[0], 'Licence editoriale runtime' ), 'The contact emails have no useful plain-text alternatives.' );
	photovault_contact_email_assert( false === photovault_send_contact_notification( array( 'name' => 'Invalid', 'email' => $email, 'request_type' => 'unknown', 'subject' => 'Invalid', 'message' => 'Invalid' ) ), 'An unknown contact category was accepted.' );

	echo wp_json_encode( array( 'studio_notification' => true, 'visitor_acknowledgement' => true, 'html_layout' => true, 'plain_text' => true, 'reply_to' => 'validated', 'from_spoofing' => 'closed', 'smtp_delivery' => true ) );
} finally {
	remove_filter( 'wp_mail', $mail_filter, 20 );
	remove_action( 'phpmailer_init', $alt_filter, 20 );
}
