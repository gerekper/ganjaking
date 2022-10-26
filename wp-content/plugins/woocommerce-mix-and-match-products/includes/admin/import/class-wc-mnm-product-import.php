<?php
/**
 * Product Import Class
 *
 * @package  WooCommerce Mix and Match Products/Admin/Import
 * @since    1.3.0
 * @version  2.2.0
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
	 * var WC_Product_CSV_Importer Class.
	 * @since 2.0.0
	 */
	private $importer = false;

	/**
	 * Hook in.
	 */
	public static function init() {

		// Map custom column titles.
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( __CLASS__, 'map_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( __CLASS__, 'add_columns_to_mapping_screen' ) );

		// Parse MnM items.
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'parse_child_category_ids' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'parse_child_items' ), 10, 2 );

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
					'wc_mnm_content_source'            => __( 'MnM Content Source', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_child_category_ids'        => __( 'MnM Child Category Ids', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_child_items'               => __( 'MnM Child Items (JSON-encoded)', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_min_container_size'        => __( 'MnM Minimum Container Size', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_max_container_size'        => __( 'MnM Maximum Container Size', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_priced_per_product'        => __( 'MnM Per-Item Pricing', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_packing_mode'              => __( 'MnM Packing Mode', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_weight_cumulative'         => __( 'MnM Weight Cumulative Weight', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_discount'                  => __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_layout_override'           => __( 'MnM Layout Override', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_layout'                    => __( 'MnM Layout', 'woocommerce-mix-and-match-products' ),
					'wc_mnm_add_to_cart_form_location' => __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' ),
				)
			);

		return apply_filters( 'wc_mnm_csv_product_import_mapping_options', $columns );

	}

	/**
	 * Add automatic mapping support for custom columns.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function add_columns_to_mapping_screen( $columns ) {

		$columns[ __( 'MnM Content Source', 'woocommerce-mix-and-match-products' ) ]             = 'wc_mnm_content_source';
		$columns[ __( 'MnM Child Category Ids', 'woocommerce-mix-and-match-products' ) ]         = 'wc_mnm_child_category_ids';
		$columns[ __( 'MnM Child Items (JSON-encoded)', 'woocommerce-mix-and-match-products' ) ] = 'wc_mnm_child_items';
		$columns[ __( 'MnM Minimum Container Size', 'woocommerce-mix-and-match-products' ) ]     = 'wc_mnm_min_container_size';
		$columns[ __( 'MnM Maximum Container Size', 'woocommerce-mix-and-match-products' ) ]     = 'wc_mnm_max_container_size';
		$columns[ __( 'MnM Per-Item Pricing', 'woocommerce-mix-and-match-products' ) ]           = 'wc_mnm_priced_per_product';
		$columns[ __( 'MnM Packing Mode', 'woocommerce-mix-and-match-products' ) ]               = 'wc_mnm_packing_mode';
		$columns[ __( 'MnM Weight Cumulative Weight', 'woocommerce-mix-and-match-products' ) ]   = 'wc_mnm_weight_cumulative';
		$columns[ __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' ) ]          = 'wc_mnm_discount';
		$columns[ __( 'MnM Layout Override', 'woocommerce-mix-and-match-products' ) ]            = 'wc_mnm_layout_override';
		$columns[ __( 'MnM Layout', 'woocommerce-mix-and-match-products' ) ]                     = 'wc_mnm_layout';
		$columns[ __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' ) ]  = 'wc_mnm_add_to_cart_form_location';

		// Always add English mappings.
		$columns['MnM Content Source']             = 'wc_mnm_content_source';
		$columns['MnM Child Category Ids']         = 'wc_mnm_child_category_ids';
		$columns['MnM Child Items (JSON-encoded)'] = 'wc_mnm_child_items';
		$columns['MnM Minimum Container Size']     = 'wc_mnm_min_container_size';
		$columns['MnM Maximum Container Size']     = 'wc_mnm_max_container_size';
		$columns['MnM Per-Item Pricing']           = 'wc_mnm_priced_per_product';
		$columns['MnM Packing Mode']               = 'wc_mnm_packing_mode';
		$columns['MnM Weight Cumulative Weight']   = 'wc_mnm_weight_cumulative';
		$columns['MnM Per-Item Discount']          = 'wc_mnm_discount';
		$columns['MnM Layout Override']            = 'wc_mnm_layout_override';
		$columns['MnM Layout']                     = 'wc_mnm_layout';
		$columns['MnM Add to Cart Form Location']  = 'wc_mnm_add_to_cart_form_location';

		return apply_filters( 'wc_mnm_csv_product_import_mapping_default_columns', $columns );
	}


	/**
	 * Decode MNM child category items and parse relative term IDs.
	 *
	 * @since 2.0.0
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_child_category_ids( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['wc_mnm_category_ids'] ) ) {
			$parsed_data['wc_mnm_category_ids'] = $importer->parse_categories_field( $parsed_data['wc_mnm_category_ids'] );
		}

		return $parsed_data;
	}


	/**
	 * Decode MNM child data items and parse relative IDs.
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_child_items( $parsed_data, $importer ) {

		if ( empty( $parsed_data[ 'wc_mnm_child_items' ] ) && ! empty( $parsed_data[ 'wc_mnm_contents'] ) ) {
			wc_deprecated_argument( 'wc_mnm_contents', '2.0.0', 'New export column is wc_mnm_child_items. Generate a new product export using Mix and Match 2.0' );
			$parsed_data[ 'wc_mnm_child_items' ] = $parsed_data[ 'wc_mnm_contents'];
		}

		if ( ! empty( $parsed_data['wc_mnm_child_items'] ) ) {

			$child_data_items = json_decode( $parsed_data['wc_mnm_child_items'], true );

			if ( is_array( $child_data_items ) ) {

				$parsed_data['wc_mnm_child_items'] = array();

				foreach ( $child_data_items as $child_item_key => $child_data ) {

					if ( ! empty( $child_data['product_id' ] ) ) {
						$parsed_data['wc_mnm_child_items'][] = array(
							'product_id'   => $importer->parse_relative_field( $child_data['product_id' ] ),
							'variation_id' => ! empty( $child_data['variation_id' ] ) ? $importer->parse_relative_field( $child_data['variation_id' ] ) : 0,
						);
					}

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

		if ( $product instanceof WC_Product && wc_mnm_is_product_container_type( $product ) ) {

			$props = apply_filters(
				'wc_mnm_import_set_props',
				array(
					'min_container_size'        => isset( $data['wc_mnm_min_container_size'] ) ? intval( $data['wc_mnm_min_container_size'] ) : 0,
					'max_container_size'        => isset( $data['wc_mnm_max_container_size'] ) && '' !== $data['wc_mnm_max_container_size'] ? intval( $data['wc_mnm_max_container_size'] ) : '',
					'content_source'            => isset( $data['wc_mnm_content_source'] ) && '' !== $data['wc_mnm_content_source'] ? strval( $data['wc_mnm_content_source'] ) : 'products',
					'child_category_ids'        => isset( $data['wc_mnm_child_category_ids'] ) && ! empty( $data['wc_mnm_child_category_ids'] ) ? $data['wc_mnm_child_category_ids'] : array(),
					'child_items'               => isset( $data['wc_mnm_child_items'] ) && ! empty( $data['wc_mnm_child_items'] ) ? $data['wc_mnm_child_items'] : array(),
					'packing_mode'              => isset( $data['wc_mnm_packing_mode'] ) && '' !== $data['wc_mnm_packing_mode'] ? strval( $data['wc_mnm_packing_mode'] ) : 'together',
					'weight_cumulative'         => isset( $data['wc_mnm_weight_cumulative'] ) && 1 === intval( $data['wc_mnm_weight_cumulative'] ) ? 'yes' : 'no',
					'priced_per_product'        => isset( $data['wc_mnm_priced_per_product'] ) && 1 === intval( $data['wc_mnm_priced_per_product'] ) ? 'yes' : 'no',
					'discount'                  => isset( $data['wc_mnm_discount'] ) && '' !== $data['wc_mnm_discount'] ? strval( $data['wc_mnm_discount'] ) : '',
					'layout_override'           => isset( $data['wc_mnm_layout_override'] ) && 1 === intval( $data['wc_mnm_layout_override'] ) ? 'yes' : 'no',
					'layout'                    => isset( $data['wc_mnm_layout'] ) && '' !== $data['wc_mnm_layout'] ? strval( $data['wc_mnm_layout'] ) : 'tabular',
					'add_to_cart_form_location' => isset( $data['wc_mnm_add_to_cart_form_location'] ) && '' !== $data['wc_mnm_add_to_cart_form_location'] ? strval( $data['wc_mnm_add_to_cart_form_location'] ) : 'default',
				),
				$product,
				$data
			);

			$product->set_props( $props );
		}

		return $product;
	}

	/**
	 * Decode MNM data items and parse relative IDs.
	 *
	 * @deprecated 2.0.0
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_mnm_items( $parsed_data, $importer ) {

		if ( empty( $parsed_data[ 'wc_mnm_child_items' ] ) && ! empty( $parsed_data[ 'wc_mnm_contents'] ) ) {
			$parsed_data[ 'wc_mnm_child_items' ] = $parsed_data[ 'wc_mnm_contents'];
		}
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::parse_child_items()' );
		return parse_child_items( $parsed_data, $importer );
	}

}
WC_MNM_Product_Import::init();
