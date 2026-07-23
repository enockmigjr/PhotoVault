<?php
/**
 * Script de détection automatique des divergences entre les plugins actifs
 * (wp-content/plugins) et leurs miroirs dans le thème (wp-content/themes/PhotoVault/plugins).
 *
 * Usage: php check-plugin-sync.php
 */

$theme_dir = dirname( __DIR__ );
$source_base = dirname( dirname( $theme_dir ) ) . '/plugins';
$mirror_base = $theme_dir . '/plugins';

$plugins = array(
	'identity-security-kit',
	'newsletter-campaign-kit',
	'photovault-core',
);

$has_divergence = false;

echo "=== VÉRICATION DE LA SYNCHRONISATION DES PLUGINS ===\n\n";

foreach ( $plugins as $plugin ) {
	$src = $source_base . '/' . $plugin;
	$dst = $mirror_base . '/' . $plugin;

	echo "--- Plugin: {$plugin} ---\n";

	if ( ! is_dir( $src ) ) {
		echo " [ERREUR] Dossier source introuvable: {$src}\n";
		$has_divergence = true;
		continue;
	}

	if ( ! is_dir( $dst ) ) {
		echo " [ERREUR] Dossier miroir introuvable: {$dst}\n";
		$has_divergence = true;
		continue;
	}

	$src_files = get_all_files( $src );
	$dst_files = get_all_files( $dst );

	$src_keys = array_keys( $src_files );
	$dst_keys = array_keys( $dst_files );

	$only_in_src = array_diff( $src_keys, $dst_keys );
	$only_in_dst = array_diff( $dst_keys, $src_keys );
	$common      = array_intersect( $src_keys, $dst_keys );

	$plugin_diffs = 0;

	foreach ( $only_in_src as $rel_path ) {
		echo " [ABSENT MIROIR]  {$rel_path}\n";
		$plugin_diffs++;
	}

	foreach ( $only_in_dst as $rel_path ) {
		echo " [ABSENT SOURCE]  {$rel_path}\n";
		$plugin_diffs++;
	}

	foreach ( $common as $rel_path ) {
		$hash_src = md5_file( $src_files[ $rel_path ] );
		$hash_dst = md5_file( $dst_files[ $rel_path ] );

		if ( $hash_src !== $hash_dst ) {
			echo " [CONTENU DIFF]   {$rel_path}\n";
			$plugin_diffs++;
		}
	}

	if ( 0 === $plugin_diffs ) {
		echo " [OK] 100% synchronisé (" . count( $common ) . " fichiers vérifiés).\n\n";
	} else {
		echo " [FAIL] {$plugin_diffs} divergence(s) trouvée(s).\n\n";
		$has_divergence = true;
	}
}

if ( $has_divergence ) {
	echo "RÉSULTAT: Des divergences ont été détectées entre wp-content/plugins et PhotoVault/plugins.\n";
	exit( 1 );
} else {
	echo "RÉSULTAT: Les plugins et leurs miroirs sont parfaitement synchronisés.\n";
	exit( 0 );
}

function get_all_files( $dir, $base_dir = null ) {
	if ( null === $base_dir ) {
		$base_dir = $dir;
	}

	$files = array();
	$items = scandir( $dir );

	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item || '.git' === $item || 'node_modules' === $item || 'vendor' === $item ) {
			continue;
		}

		$full_path = $dir . '/' . $item;
		$rel_path  = ltrim( str_replace( $base_dir, '', $full_path ), '/' );

		if ( is_dir( $full_path ) ) {
			$files = array_merge( $files, get_all_files( $full_path, $base_dir ) );
		} else {
			$files[ $rel_path ] = $full_path;
		}
	}

	return $files;
}
