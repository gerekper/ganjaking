<?php
/**
 * Per Product Shipping Installer.
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$wpdb->hide_errors();

$collate = '';

if ( $wpdb->has_cap( 'collation' ) ) {
	if ( ! empty( $wpdb->charset ) ) {
		$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
	}
	if ( ! empty( $wpdb->collate ) ) {
		$collate .= " COLLATE $wpdb->collate";
	}
}

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

// Table for storing rules for products.
$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_per_product_shipping_rules (
rule_id bigint(20) NOT NULL auto_increment,
product_id bigint(20) NOT NULL,
rule_country varchar(10) NOT NULL,
rule_state varchar(10) NOT NULL,
rule_postcode varchar(200) NOT NULL,
rule_cost varchar(200) NOT NULL,
rule_item_cost varchar(200) NOT NULL,
rule_order bigint(20) NOT NULL,
PRIMARY KEY  (rule_id)
) $collate;
";
dbDelta( $sql );

// Upgrades.
$old_data = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'per_product_shipping'" );

foreach ( $old_data as $data ) {
	if ( $data->meta_value ) {
	    $wpdb->insert(
			"{$wpdb->prefix}woocommerce_per_product_shipping_rules",
			array(
				'rule_country' 		=> '',
				'rule_state' 		=> '',
				'rule_postcode' 	=> '',
				'rule_cost' 		=> '',
				'rule_item_cost' 	=> esc_attr( number_format( $data->meta_value, 2, '.', ',' ) ),
				'rule_order'		=> 0,
				'product_id'		=> $data->post_id,
			)
		);
		add_post_meta( $data->post_id, '_per_product_shipping', 'yes' );
	}
	add_post_meta( $data->post_id, 'old_per_product_shipping', $data->meta_value );
	delete_post_meta( $data->post_id, 'per_product_shipping' );
}

update_option( 'per_product_shipping_version', PER_PRODUCT_SHIPPING_VERSION );
