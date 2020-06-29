<?php
/**
 * Uninstall plugin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Save for Later
 * @version 1.0.0
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;


$table_name = $wpdb->prefix.'ywsfl_list';
//remove any additional options and custom table
$sql = "DROP TABLE $table_name";

$wpdb->query( $sql );
?>