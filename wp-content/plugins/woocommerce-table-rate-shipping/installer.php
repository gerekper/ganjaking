<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$wpdb->hide_errors();

$collate = '';

if ( $wpdb->has_cap( 'collation' ) ) {
	$collate = $wpdb->get_charset_collate();
}

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

// Table for storing table rates themselves. shipping_method_id is an individual table of rates applied to a zone
$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_shipping_table_rates (
rate_id bigint(20) NOT NULL auto_increment,
rate_class varchar(200) NOT NULL,
rate_condition varchar(200) NOT NULL,
rate_min varchar(200) NOT NULL,
rate_max varchar(200) NOT NULL,
rate_cost varchar(200) NOT NULL,
rate_cost_per_item varchar(200) NOT NULL,
rate_cost_per_weight_unit varchar(200) NOT NULL,
rate_cost_percent varchar(200) NOT NULL,
rate_label longtext NULL,
rate_priority int(1) NOT NULL,
rate_order bigint(20) NOT NULL,
shipping_method_id bigint(20) NOT NULL,
rate_abort int(1) NOT NULL,
rate_abort_reason longtext NULL,
PRIMARY KEY  (rate_id)
) $collate;
";
dbDelta( $sql );

update_option( 'hide_table_rate_welcome_notice', '' );
