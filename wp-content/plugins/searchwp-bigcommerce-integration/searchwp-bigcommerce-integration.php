<?php
/*
Plugin Name: SearchWP BigCommerce Integration
Plugin URI: https://searchwp.com/extensions/bigcommerce-integration/
Description: Integrate SearchWP with BigCommerce
Version: 1.0
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2019 Jonathan Christopher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_BIGCOMMERCE_INTEGRATION_VERSION' ) ) {
	define( 'SEARCHWP_BIGCOMMERCE_INTEGRATION_VERSION', '1.0' );
}

/**
 * SearchWP BigCommerce Integration
 *
 * Class SearchWP_BigCommerce_Integration
 */
class SearchWP_BigCommerce_Integration {
	/**
	 * Constructor.
	 */
	function __construct() {
		add_filter( 'searchwp_extra_metadata', array( $this, 'extra_metadata' ), 10, 2 );
		add_filter( 'searchwp_custom_field_keys', array( $this, 'custom_field_keys' ), 10, 1 );
	}

	public function custom_field_keys( $keys ) {
		global $wpdb;

		$big_commerce_meta_values = $wpdb->get_results("
			SELECT $wpdb->postmeta.meta_value
			FROM $wpdb->postmeta
			WHERE $wpdb->postmeta.meta_key = 'bigcommerce_custom_fields'",
		ARRAY_A );

		if ( empty( $big_commerce_meta_values ) ) {
			return $keys;
		}

		$bigcommerce_keys = array();

		foreach ( $big_commerce_meta_values as $big_commerce_meta_value ) {
			$custom_field_record = maybe_unserialize( $big_commerce_meta_value['meta_value'] );

			if ( empty( $custom_field_record ) ) {
				continue;
			}

			$bigcommerce_keys = array_merge( $bigcommerce_keys, wp_list_pluck( $custom_field_record, 'name' ) );
		}

		$bigcommerce_keys = array_unique( $bigcommerce_keys );

		return array_merge( $keys, $bigcommerce_keys );
	}

	public function extra_metadata( $extra_meta, $post_being_indexed ) {
		// BigCommerce Custom Fields are stored in a single record
		$big_commerce_meta = get_post_meta( $post_being_indexed->ID, 'bigcommerce_custom_fields', true );

		if ( empty( $big_commerce_meta ) ) {
			return $extra_meta;
		}

		// Index BigCommerce Custom Fields as individual extra metadata in the SearchWP index
		foreach ( $big_commerce_meta as $bigcommerce_custom_field ) {
			// Sometimes the same key is used.
			if ( ! is_array( $extra_meta[ $bigcommerce_custom_field['name'] ] ) ) {
				$extra_meta[ $bigcommerce_custom_field['name'] ] = array();
			}

			$extra_meta[ $bigcommerce_custom_field['name'] ][] = $bigcommerce_custom_field['value'];
		}

		return $extra_meta;
	}
}

new SearchWP_BigCommerce_Integration();

/**
 * Instantiate the updater.
 */
if ( ! class_exists( 'SWP_BigCommerce_Integration_Updater' ) ) {
	include_once dirname( __FILE__ ) . '/vendor/updater.php';
}

function searchwp_bigcommerce_integration_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_VERSION' ) ) {
		return false;
	}

	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	$searchwp_bigcommerce_integration_updater = new SWP_BigCommerce_Integration_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 193520,
			'version'   => SEARCHWP_EDD_VERSION,
			'license'   => $license_key,
			'item_name' => 'BigCommerce Integration',
			'author'    => 'SearchWP, LLC',
			'url'       => site_url(),
		)
	);

	return $searchwp_bigcommerce_integration_updater;
}

add_action( 'admin_init', 'searchwp_bigcommerce_integration_update_check' );
