<?php
/**
 * WC_PB_Product_Import class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Importer support.
 *
 * @class    WC_PB_Product_Import
 * @version  6.17.4
 */
class WC_PB_Product_Import {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Map custom column titles.
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( __CLASS__, 'map_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( __CLASS__, 'add_columns_to_mapping_screen' ) );

		// Parse bundled items.
		add_filter( 'woocommerce_product_importer_formatting_callbacks', array( __CLASS__, 'append_formatting_callbacks' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'parse_bundled_items' ), 10, 2 );

		// Parse Bundle Sells IDs.
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'parse_bundle_sells' ), 10, 2 );

		// Set bundle-type props.
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( __CLASS__, 'set_bundle_props' ), 10, 2 );
	}

	/**
	 * Register the 'Custom Column' column in the importer.
	 *
	 * @param  array  $options
	 * @return array  $options
	 */
	public static function map_columns( $options ) {

		$options[ 'wc_pb_bundled_items' ]             = __( 'Bundled Items (JSON-encoded)', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_min_bundle_size' ]           = __( 'Min Bundle Size', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_max_bundle_size' ]           = __( 'Max Bundle Size', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_virtual_bundle' ]            = __( 'Bundle Contents Virtual', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_aggregate_weight' ]          = __( 'Bundle Aggregate Weight', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_layout' ]                    = __( 'Bundle Layout', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_group_mode' ]                = __( 'Bundle Group Mode', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_editable_in_cart' ]          = __( 'Bundle Cart Editing', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_sold_individually_context' ] = __( 'Bundle Sold Individually', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_add_to_cart_form_location' ] = __( 'Bundle Form Location', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_bundle_sells' ]              = __( 'Bundle Sells', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_bundle_sells_title' ]        = __( 'Bundle Sells Title', 'woocommerce-product-bundles' );
		$options[ 'wc_pb_bundle_sells_discount' ]     = __( 'Bundle Sells Discount', 'woocommerce-product-bundles' );

		return $options;
	}

	/**
	 * Add automatic mapping support for custom columns.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function add_columns_to_mapping_screen( $columns ) {

		$columns[ __( 'Bundled Items (JSON-encoded)', 'woocommerce-product-bundles' ) ] = 'wc_pb_bundled_items';
		$columns[ __( 'Min Bundle Size', 'woocommerce-product-bundles' ) ]              = 'wc_pb_min_bundle_size';
		$columns[ __( 'Max Bundle Size', 'woocommerce-product-bundles' ) ]              = 'wc_pb_max_bundle_size';
		$columns[ __( 'Bundle Contents Virtual', 'woocommerce-product-bundles' ) ]      = 'wc_pb_virtual_bundle';
		$columns[ __( 'Bundle Aggregate Weight', 'woocommerce-product-bundles' ) ]      = 'wc_pb_aggregate_weight';
		$columns[ __( 'Bundle Layout', 'woocommerce-product-bundles' ) ]                = 'wc_pb_layout';
		$columns[ __( 'Bundle Group Mode', 'woocommerce-product-bundles' ) ]            = 'wc_pb_group_mode';
		$columns[ __( 'Bundle Cart Editing', 'woocommerce-product-bundles' ) ]          = 'wc_pb_editable_in_cart';
		$columns[ __( 'Bundle Sold Individually', 'woocommerce-product-bundles' ) ]     = 'wc_pb_sold_individually_context';
		$columns[ __( 'Bundle Form Location', 'woocommerce-product-bundles' ) ]         = 'wc_pb_add_to_cart_form_location';
		$columns[ __( 'Bundle Sells', 'woocommerce-product-bundles' ) ]                 = 'wc_pb_bundle_sells';
		$columns[ __( 'Bundle Sells Title', 'woocommerce-product-bundles' ) ]           = 'wc_pb_bundle_sells_title';
		$columns[ __( 'Bundle Sells Discount', 'woocommerce-product-bundles' ) ]        = 'wc_pb_bundle_sells_discount';

		// Always add English mappings.
		$columns[ 'Bundled Items (JSON-encoded)' ] = 'wc_pb_bundled_items';
		$columns[ 'Min Bundle Size' ]              = 'wc_pb_min_bundle_size';
		$columns[ 'Max Bundle Size' ]              = 'wc_pb_max_bundle_size';
		$columns[ 'Bundle Contents Virtual' ]      = 'wc_pb_virtual_bundle';
		$columns[ 'Bundle Aggregate Weight' ]      = 'wc_pb_aggregate_weight';
		$columns[ 'Bundle Layout' ]                = 'wc_pb_layout';
		$columns[ 'Bundle Group Mode' ]            = 'wc_pb_group_mode';
		$columns[ 'Bundle Cart Editing' ]          = 'wc_pb_editable_in_cart';
		$columns[ 'Bundle Sold Individually' ]     = 'wc_pb_sold_individually_context';
		$columns[ 'Bundle Form Location' ]         = 'wc_pb_add_to_cart_form_location';
		$columns[ 'Bundle Sells' ]                 = 'wc_pb_bundle_sells';
		$columns[ 'Bundle Sells Title' ]           = 'wc_pb_bundle_sells_title';
		$columns[ 'Bundle Sells Discount' ]        = 'wc_pb_bundle_sells_discount';

		return $columns;
	}

	/**
	 * Set formatting (decoding) callback for bundled item data.
	 *
	 * @since  6.9.0
	 *
	 * @param  array                    $callbacks
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function append_formatting_callbacks( $callbacks, $importer ) {
		
		$mapped_keys_reverse = array_flip( $importer->get_mapped_keys() );

		if ( isset( $mapped_keys_reverse[ 'wc_pb_bundled_items' ] ) ) {
			$callbacks[ $mapped_keys_reverse[ 'wc_pb_bundled_items' ] ] = array( __CLASS__, 'decode_bundled_items' );
		}

		return $callbacks;
	}

	/**
	 * Decodes bundled item data.
	 *
	 * @since  6.9.0
	 *
	 * @param  string  $data
	 * @return array
	 */
	public static function decode_bundled_items( $data ) {
		return json_decode( $data, true );
	}

	/**
	 * Decode bundled data items and parse relative IDs.
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_bundled_items( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data[ 'wc_pb_bundled_items' ] ) ) {

			$bundled_data_items = $parsed_data[ 'wc_pb_bundled_items' ];

			unset( $parsed_data[ 'wc_pb_bundled_items' ] );

			if ( is_array( $bundled_data_items ) ) {

				$parsed_data[ 'wc_pb_bundled_items' ] = array();

				foreach ( $bundled_data_items as $bundled_data_item_key => $bundled_data_item ) {

					$bundled_product_id = $bundled_data_items[ $bundled_data_item_key ][ 'product_id' ];

					$parsed_data[ 'wc_pb_bundled_items' ][ $bundled_data_item_key ]                 = $bundled_data_item;
					$parsed_data[ 'wc_pb_bundled_items' ][ $bundled_data_item_key ][ 'product_id' ] = $importer->parse_relative_field( $bundled_product_id );
				}
			}
		}

		return $parsed_data;
	}

	/**
	 * Decode Bundle Sells and parse relative IDs.
	 *
	 * @since  6.1.0
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_bundle_sells( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data[ 'wc_pb_bundle_sells' ] ) ) {

			$parsed_data[ 'meta_data' ][] = array(
				'key'   => '_wc_pb_bundle_sell_ids',
				'value' => $importer->parse_relative_comma_field( $parsed_data[ 'wc_pb_bundle_sells' ] )
			);
		}

		if ( ! empty( $parsed_data[ 'wc_pb_bundle_sells_title' ] ) ) {

			$parsed_data[ 'meta_data' ][] = array(
				'key'   => '_wc_pb_bundle_sells_title',
				'value' => wp_kses_post( $parsed_data[ 'wc_pb_bundle_sells_title' ] )
			);
		}

		if ( ! empty( $parsed_data[ 'wc_pb_bundle_sells_discount' ] ) ) {

			$parsed_data[ 'meta_data' ][] = array(
				'key'   => '_wc_pb_bundle_sells_discount',
				'value' => wc_format_decimal( $parsed_data[ 'wc_pb_bundle_sells_discount' ] )
			);
		}

		return $parsed_data;
	}


	/**
	 * Set bundle-type props.
	 *
	 * @param  array  $parsed_data
	 * @return array
	 */
	public static function set_bundle_props( $product, $data ) {

		if ( ( $product instanceof WC_Product ) && $product->is_type( 'bundle' ) ) {

			$props = array();

			if ( isset( $data[ 'wc_pb_bundled_items' ] ) ) {
				$props[ 'bundled_data_items' ] = ! empty( $data[ 'wc_pb_bundled_items' ] ) ? $data[ 'wc_pb_bundled_items' ] : array();
			}

			if ( isset( $data[ 'wc_pb_min_bundle_size' ] ) ) {
				$props[ 'min_bundle_size' ] = strval( $data[ 'wc_pb_min_bundle_size' ] );
			}

			if ( isset( $data[ 'wc_pb_max_bundle_size' ] ) ) {
				$props[ 'max_bundle_size' ] = strval( $data[ 'wc_pb_max_bundle_size' ] );
			}

			if ( isset( $data[ 'wc_pb_editable_in_cart' ] ) ) {
				$props[ 'editable_in_cart' ] = 1 === intval( $data[ 'wc_pb_editable_in_cart' ] ) ? 'yes' : 'no';
			}

			if ( isset( $data[ 'wc_pb_virtual_bundle' ] ) ) {
				$props[ 'virtual_bundle' ] = 1 === intval( $data[ 'wc_pb_virtual_bundle' ] ) ? 'yes' : 'no';
			}

			if ( isset( $data[ 'wc_pb_aggregate_weight' ] ) ) {
				$props[ 'aggregate_weight' ] = 1 === intval( $data[ 'wc_pb_aggregate_weight' ] ) ? 'yes' : 'no';
			}

			if ( isset( $data[ 'wc_pb_layout' ] ) ) {
				$props[ 'layout' ] = strval( $data[ 'wc_pb_layout' ] );
			}

			if ( isset( $data[ 'wc_pb_group_mode' ] ) ) {
				$props[ 'group_mode' ] = strval( $data[ 'wc_pb_group_mode' ] );
			}

			if ( isset( $data[ 'wc_pb_sold_individually_context' ] ) ) {
				$props[ 'sold_individually_context' ] = strval( $data[ 'wc_pb_sold_individually_context' ] );
			}

			if ( isset( $data[ 'wc_pb_add_to_cart_form_location' ] ) ) {
				$props[ 'add_to_cart_form_location' ] = strval( $data[ 'wc_pb_add_to_cart_form_location' ] );
			}

			if ( ! empty( $props ) ) {
				$product->set_props( $props );
			}
		}

		return $product;
	}
}

WC_PB_Product_Import::init();
