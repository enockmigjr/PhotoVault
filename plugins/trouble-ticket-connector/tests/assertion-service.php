<?php
/** Standalone fail-closed check when Identity Security Kit is absent. */

define( 'ABSPATH', __DIR__ );

class WP_Error {
	private $code;
	public function __construct( $code ) {
		$this->code = $code;
	}
	public function get_error_code() {
		return $this->code;
	}
}

function __( $message ) {
	return $message;
}

require dirname( __DIR__ ) . '/inc/assertion-service.php';

$result = trouble_ticket_connector_create_assertion( 42 );
if ( ! $result instanceof WP_Error || 'identity_kit_missing' !== $result->get_error_code() ) {
	fwrite( STDERR, "Identity Kit absence did not fail closed.\n" );
	exit( 1 );
}

fwrite( STDOUT, "Assertion service fail-closed check passed.\n" );
