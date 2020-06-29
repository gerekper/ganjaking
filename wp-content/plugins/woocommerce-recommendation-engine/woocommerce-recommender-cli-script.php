<?php

if ( php_sapi_name() !== 'cli' ) {
	echo 'Meant to be run from the command line';
	die( "Meant to be run from command line" );
}

ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

echo 'Begining Import Process';
echo PHP_EOL;

$base_path = wc_recommender_find_wordpress_base_path();
echo 'Found Base Path:' . $base_path;
echo PHP_EOL;

define( 'BASE_PATH', $base_path . "/" );
define( 'WP_USE_THEMES', false );
define( 'ABSPATH', $base_path . '/' );

echo 'Loading WordPress:' . $base_path;
echo PHP_EOL;
require_once( $base_path . '/wp-load.php' );


update_option( 'woocommerce_recommender_build_running', true );
update_option( 'woocommerce_recommender_cron_start', time() );
set_time_limit( 0 );

try {
	$builder = new WC_Recommender_Recorder();
	$builder->woocommerce_recommender_begin_build_simularity( false, 0 );
	update_option( 'woocommerce_recommender_cron_result', 'OK' );
} catch ( Exception $exc ) {
	update_option( 'woocommerce_recommender_cron_result', $exc->getTraceAsString() );
	echo $exc->getTraceAsString();
}

update_option( 'woocommerce_recommender_cron_end', time() );
update_option( 'woocommerce_recommender_build_running', false );

echo 'End Import Process';


function wc_recommender_find_wordpress_base_path() {
	$dir = dirname( __FILE__ );
	do {
		//it is possible to check for other files here
		if ( file_exists( $dir . "/wp-config.php" ) ) {
			return realpath( "$dir/.." );
		}
	} while ( $dir = realpath( "$dir/.." ) );
	return null;
}