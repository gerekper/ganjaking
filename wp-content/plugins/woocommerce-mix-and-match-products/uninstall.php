<?php
/**
 * WooCommerce Mix and Match Products Uninstall
 *
 * Uninstalling Mix and Match deletes tables, post meta, and options.
 *
 * @package WooCommerce Mix and Match Prodicts\Uninstaller
 * @version 2.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/*
 * Only remove ALL product and page data if WC_MNM_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_MNM_REMOVE_ALL_DATA' ) && true === WC_MNM_REMOVE_ALL_DATA ) {

	global $wp, $wpdb;

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wc_mnm\_%';" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wc_mix_and_match\_%';" );

	// Delete any update process locks.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%_wc_mnm_updater_%'" );

	// Delete the entire child items table.
	$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}wc_mnm_child_items`" );

	// Delete post meta data.
	$meta_keys = array(
		'_min_raw_price',
		'_min_raw_regular_price',
		'_max_raw_price',
		'_max_raw_regular_price',
		'_layout_override',
		'_layout',
		'_add_to_cart_form_location',
		'_min_container_size',
		'_max_container_size',
		'_priced_per_product',
		'_discount',
		'_shipped_per_product',
		'_mnm_data',
		'_mnm_max_price',
		'_mnm_max_regular_price',
		'_mnm_base_price',
		'_mnm_base_regular_price',
		'_mnm_base_sale_price',
		'_mnm_layout_override',
		'_mnm_layout_style',
		'_mnm_add_to_cart_form_location',
		'_mnm_min_container_size',
		'_mnm_max_container_size',
		'_mnm_per_product_pricing',
		'_mnm_per_product_discount',
		'_mnm_per_product_shipping',
	);

	$key_placeholders = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );

	$wpdb->query(
		$wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ($key_placeholders)", $meta_keys )
	);

	// Delete product type.
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( $mnm_term ) {
		wp_delete_term( $mnm_term->term_id, 'product_type' );
	}

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
