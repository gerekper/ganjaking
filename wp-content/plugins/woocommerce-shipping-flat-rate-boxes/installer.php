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

$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_shipping_flat_rate_boxes (
  box_id bigint(20) NOT NULL auto_increment,
  box_length varchar(200) NOT NULL,
  box_width varchar(200) NOT NULL,
  box_height varchar(200) NOT NULL,
  box_max_weight varchar(200) NOT NULL,
  box_cost varchar(200) NOT NULL,
  box_cost_per_weight_unit varchar(200) NOT NULL,
  box_cost_percent varchar(200) NOT NULL,
  shipping_method_id bigint(20) NOT NULL,
  PRIMARY KEY  (box_id)
) $collate;
";

dbDelta($sql);

if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
	// Table for storing shipping zones
	$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_shipping_zones (
  zone_id bigint(20) NOT NULL auto_increment,
  zone_name varchar(255) NOT NULL,
  zone_enabled int(1) NOT NULL DEFAULT 1,
  zone_type varchar(40) NOT NULL DEFAULT '',
  zone_order bigint(20) NOT NULL,
  PRIMARY KEY  (zone_id)
) $collate;
";

    dbDelta($sql);

	// Table for storing a shipping zones locations which it applies to. Type can be postcode, state, or country.
	$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_shipping_zone_locations (
  location_id bigint(20) NOT NULL auto_increment,
  location_code varchar(255) NOT NULL,
  zone_id bigint(20) NOT NULL,
  location_type varchar(40) NOT NULL,
  PRIMARY KEY  (location_id)
) $collate;
";
	dbDelta($sql);

	// Table for storing shipping zones individial shipping methods and their options
	$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_shipping_zone_shipping_methods (
  shipping_method_id bigint(20) NOT NULL auto_increment,
  shipping_method_type varchar(255) NOT NULL,
  zone_id bigint(20) NOT NULL,
  shipping_method_order bigint(20) NOT NULL default 0,
  PRIMARY KEY  (shipping_method_id)
) $collate;
";
	dbDelta( $sql );
}

update_option( 'hide_box_shipping_welcome_notice', '' );
