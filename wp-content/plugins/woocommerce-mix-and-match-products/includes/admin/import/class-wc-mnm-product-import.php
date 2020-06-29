<?php
/**
 * Product Import Class
 *
 * @author   SomewhereWarm
 * @category Admin
 * @package  WooCommerce Mix and Match Products/Admin/Import
 * @since    1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Product_Import Class.
 *
 * Add support for MNM products to WooCommerce product import.
 */
class WC_MNM_Product_Import {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Map custom column titles.
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( __CLASS__, 'map_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( __CLASS__, 'add_columns_to_mapping_screen' ) );

		// Parse MnM items.
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'parse_mnm_items' ), 10, 2 );

		// Set MnM-type props.
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( __CLASS__, 'set_mnm_props' ), 10, 2 );
	}

	/**
	 * Register the 'Custom' columns in the importer.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function map_columns( $columns ) {

		$columns['mix-and-match'] = array(
				'name'    => __( 'Mix and Match Products', 'woocommerce-mix-and-match-products' ),
				'options' => array(
					'wc_mnm_contents' => __( 'MnM Contents (JSON-encoded)', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_min_container_size' => __( 'MnM Minimum Container Size', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_max_container_size' => __( 'MnM Maximum Container Size', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_priced_per_product' => __( 'MnM Per-Item Pricing', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_shipped_per_product' => __( 'MnM Per-Item Shipping', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_discount' => __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_layout' => __( 'MnM Layout', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_add_to_cart_form_location' => __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' ),
				)
			);

		return apply_filters( 'woocommerce_mnm_csv_product_import_mapping_options', $columns );

	}

	/**
	 * Add automatic mapping support for custom columns.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function add_columns_to_mapping_screen( $columns ) {

		$columns[ __( 'MnM Contents (JSON-encoded)', 'woocommerce-mix-and-match-products' ) ] 	= 'wc_mnm_contents';
		$columns[ __( 'MnM Minimum Container Size', 'woocommerce-mix-and-match-products' ) ]  	= 'wc_mnm_min_container_size';
		$columns[ __( 'MnM Maximum Container Size', 'woocommerce-mix-and-match-products' ) ]    = 'wc_mnm_max_container_size';
		$columns[ __( 'MnM Per-Item Pricing', 'woocommerce-mix-and-match-products' ) ]          = 'wc_mnm_priced_per_product';
		$columns[ __( 'MnM Per-Item Shipping', 'woocommerce-mix-and-match-products' ) ]    		= 'wc_mnm_shipped_per_product';
		$columns[ __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' ) ]			= 'wc_mnm_discount';
		$columns[ __( 'MnM Layout', 'woocommerce-mix-and-match-products' ) ]					= 'wc_mnm_layout';
		$columns[ __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' ) ]	= 'wc_mnm_add_to_cart_form_location';

		// Always add English mappings.
		$columns[ 'MnM Contents (JSON-encoded)' ]	= 'wc_mnm_contents';
		$columns[ 'MnM Minimum Container Size' ]    = 'wc_mnm_min_container_size';
		$columns[ 'MnM Maximum Container Size' ]    = 'wc_mnm_max_container_size';
		$columns[ 'MnM Per-Item Pricing' ]          = 'wc_mnm_priced_per_product';
		$columns[ 'MnM Per-Item Shipping' ]     	= 'wc_mnm_shipped_per_product';
		$columns[ 'MnM Per-Item Discount' ]         = 'wc_mnm_discount';
		$columns[ 'MnM Layout' ] 					= 'wc_mnm_layout';
		$columns[ 'MnM Add to Cart Form Location' ]	= 'wc_mnm_add_to_cart_form_location';

		return apply_filters( 'woocommerce_mnm_csv_product_import_mapping_default_columns', $columns );
	}

	/**
	 * Decode MNM data items and parse relative IDs.
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_mnm_items( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data[ 'wc_mnm_contents' ] ) ) {

			$mnm_data_items = json_decode( $parsed_data[ 'wc_mnm_contents' ], true );

			unset( $parsed_data[ 'wc_mnm_contents' ] );

			if ( is_array( $mnm_data_items ) ) {

				$parsed_data[ 'wc_mnm_contents' ] = array();

				foreach ( $mnm_data_items as $mnm_data_item_key => $mnm_data_item ) {

					$mnm_product_id = $mnm_data_items[ $mnm_data_item_key ][ 'product_id' ];
					$new_product_id = $importer->parse_relative_field( $mnm_product_id );

					$parsed_data[ 'wc_mnm_contents' ][ $new_product_id ]                 = $mnm_data_item;
					$parsed_data[ 'wc_mnm_contents' ][ $new_product_id ][ 'product_id' ] = $new_product_id;
				}
			}

		}

		return $parsed_data;
	}

	/**
	 * Set container-type props.
	 *
	 * @param  array  $parsed_data
	 * @return array
	 */
	public static function set_mnm_props( $product, $data ) {

		if ( $product instanceof WC_Product && $product->is_type( 'mix-and-match' ) ) {

			$props = apply_filters( 'woocommerce_mnm_import_set_props', array(
				'min_container_size'    	=> isset( $data[ 'wc_mnm_min_container_size' ] ) ? intval( $data[ 'wc_mnm_min_container_size' ] ) : 0,
				'max_container_size'    	=> isset( $data[ 'wc_mnm_max_container_size' ] ) && $data[ 'wc_mnm_max_container_size' ] != '' ? intval( $data[ 'wc_mnm_max_container_size' ] ) : '',
				'contents'      			=> isset( $data[ 'wc_mnm_contents' ] ) && ! empty( $data[ 'wc_mnm_contents' ] ) ? wp_list_pluck( $data[ 'wc_mnm_contents' ], 'product_id' ) : array(),
				'shipped_per_product'		=> isset( $data[ 'wc_mnm_shipped_per_product' ] ) && 1 === intval( $data[ 'wc_mnm_shipped_per_product' ] ) ? 'yes' : 'no',
				'priced_per_product'    	=> isset( $data[ 'wc_mnm_priced_per_product' ] ) && 1 === intval( $data[ 'wc_mnm_priced_per_product' ] ) ? 'yes' : 'no',
				'discount'  				=> isset( $data[ 'wc_mnm_discount' ] ) && $data[ 'wc_mnm_discount' ] !='' ? strval( $data[ 'wc_mnm_discount' ] ) : '',
				'layout'	    			=> isset( $data[ 'wc_mnm_layout' ] ) && $data[ 'wc_mnm_layout' ] != '' ? strval( $data[ 'wc_mnm_layout' ] ) : 'tabular',
				'add_to_cart_form_location' => isset( $data[ 'wc_mnm_add_to_cart_form_location' ] ) && $data[ 'wc_mnm_add_to_cart_form_location' ] != '' ? strval( $data[ 'wc_mnm_add_to_cart_form_location' ] ): 'default',
			), $product, $data );

			$product->set_props( $props );
		}

		return $product;
	}
}

WC_MNM_Product_Import::init();
