<?php
/**
 * Compare a PHPCS JSON report with the committed per-file/per-sniff baseline.
 *
 * Usage:
 * php tools/check-phpcs-baseline.php report.json phpcs-baseline.json
 * php tools/check-phpcs-baseline.php --write-baseline report.json phpcs-baseline.json
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

$write_mode = isset( $argv[1] ) && '--write-baseline' === $argv[1];
$offset     = $write_mode ? 1 : 0;
$report_path = isset( $argv[1 + $offset] ) ? $argv[1 + $offset] : '';
$baseline_path = isset( $argv[2 + $offset] ) ? $argv[2 + $offset] : '';

if ( '' === $report_path || '' === $baseline_path ) {
	fwrite( STDERR, "Missing PHPCS report or baseline path.\n" );
	exit( 2 );
}

/** Decode one required JSON file as an associative array. */
function photovault_read_json_file( $path ) {
	if ( ! is_readable( $path ) ) {
		throw new RuntimeException( 'Unreadable JSON file: ' . $path );
	}
	$data = json_decode( (string) file_get_contents( $path ), true );
	if ( ! is_array( $data ) ) {
		throw new RuntimeException( 'Invalid JSON file: ' . $path );
	}

	return $data;
}

/** Normalize an absolute PHPCS filename to a stable project-relative path. */
function photovault_normalize_report_path( $path ) {
	$path = str_replace( '\\', '/', (string) $path );
	$cwd  = str_replace( '\\', '/', (string) getcwd() );
	if ( 0 === strpos( strtolower( $path ), strtolower( $cwd . '/' ) ) ) {
		$path = substr( $path, strlen( $cwd ) + 1 );
	}

	return ltrim( $path, '/' );
}

/** Aggregate current violations by stable file and PHPCS source. */
function photovault_build_phpcs_snapshot( $report ) {
	$snapshot = array();
	$files    = isset( $report['files'] ) && is_array( $report['files'] ) ? $report['files'] : array();
	foreach ( $files as $filename => $details ) {
		$path     = photovault_normalize_report_path( $filename );
		$messages = isset( $details['messages'] ) && is_array( $details['messages'] ) ? $details['messages'] : array();
		foreach ( $messages as $message ) {
			$source = isset( $message['source'] ) ? (string) $message['source'] : '';
			if ( '' === $source ) {
				continue;
			}
			if ( ! isset( $snapshot[ $path ][ $source ] ) ) {
				$snapshot[ $path ][ $source ] = 0;
			}
			++$snapshot[ $path ][ $source ];
		}
	}
	ksort( $snapshot );
	foreach ( $snapshot as &$sources ) {
		ksort( $sources );
	}
	unset( $sources );

	return $snapshot;
}

try {
	$report   = photovault_read_json_file( $report_path );
	$snapshot = photovault_build_phpcs_snapshot( $report );
	if ( $write_mode ) {
		$payload = array(
			'version'      => 1,
			'generated_at' => gmdate( 'Y-m-d' ),
			'files'        => $snapshot,
		);
		$json = json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		if ( false === $json || false === file_put_contents( $baseline_path, $json . PHP_EOL ) ) {
			throw new RuntimeException( 'Unable to write baseline: ' . $baseline_path );
		}
		fwrite( STDOUT, sprintf( "PHPCS baseline written for %d files.\n", count( $snapshot ) ) );
		exit( 0 );
	}

	$baseline = photovault_read_json_file( $baseline_path );
	$allowed  = isset( $baseline['files'] ) && is_array( $baseline['files'] ) ? $baseline['files'] : array();
	$new      = array();
	foreach ( $snapshot as $path => $sources ) {
		foreach ( $sources as $source => $count ) {
			$limit = isset( $allowed[ $path ][ $source ] ) ? (int) $allowed[ $path ][ $source ] : 0;
			if ( $count > $limit ) {
				$new[] = sprintf( '%s: %s increased from %d to %d', $path, $source, $limit, $count );
			}
		}
	}
	if ( $new ) {
		fwrite( STDERR, "New PHPCS violations detected:\n- " . implode( "\n- ", $new ) . "\n" );
		exit( 1 );
	}

	fwrite( STDOUT, sprintf( "PHPCS baseline respected across %d files.\n", count( $snapshot ) ) );
} catch ( Throwable $exception ) {
	fwrite( STDERR, $exception->getMessage() . "\n" );
	exit( 2 );
}
