<?php
/**
 * Product Import Class
 *
 * @package  WooCommerce Mix and Match Products/Admin/Import
 * @since    1.3.0
 * @version  2.3.0
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
	 * Variable: WC_Product_CSV_Importer Class.
	 *
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
		add_filter( 'woocommerce_product_importer_formatting_callbacks', array( __CLASS__, 'append_formatting_callbacks' ), 10, 2 );
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
				'wc_mnm_weight_cumulative'         => __( 'MnM Weight Cumulative', 'woocommerce-mix-and-match-products' ),
				'wc_mnm_discount'                  => __( 'MnM Per-Item Discount', 'woocommerce-mix-and-match-products' ),
				'wc_mnm_layout_override'           => __( 'MnM Layout Override', 'woocommerce-mix-and-match-products' ),
				'wc_mnm_layout'                    => __( 'MnM Layout', 'woocommerce-mix-and-match-products' ),
				'wc_mnm_add_to_cart_form_location' => __( 'MnM Add to Cart Form Location', 'woocommerce-mix-and-match-products' ),
			),
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
		$columns[ __( 'MnM Weight Cumulative', 'woocommerce-mix-and-match-products' ) ]          = 'wc_mnm_weight_cumulative';
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
		$columns['MnM Weight Cumulative']          = 'wc_mnm_weight_cumulative';
		$columns['MnM Per-Item Discount']          = 'wc_mnm_discount';
		$columns['MnM Layout Override']            = 'wc_mnm_layout_override';
		$columns['MnM Layout']                     = 'wc_mnm_layout';
		$columns['MnM Add to Cart Form Location']  = 'wc_mnm_add_to_cart_form_location';

		return apply_filters( 'wc_mnm_csv_product_import_mapping_default_columns', $columns );
	}

	/**
	 * Set formatting (decoding) callback for child item data.
	 *
	 * @since  2.3.0
	 *
	 * @param  array                    $callbacks
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function append_formatting_callbacks( $callbacks, $importer ) {

		$mnm_callbacks = array(
			'wc_mnm_child_category_ids' => array( $importer, 'parse_categories_field' ),
			'wc_mnm_child_items'        => array( __CLASS__, 'decode_child_items' ),
			'wc_mnm_min_container_size' => 'intval',
			'wc_mnm_max_container_size' => array( __CLASS__, 'maybe_parse_intval' ),
			'wc_mnm_priced_per_product' => array( $importer, 'parse_bool_field' ),
			'wc_mnm_weight_cumulative'  => array( $importer, 'parse_bool_field' ),
			'wc_mnm_discount'           => 'wc_format_decimal',
			'wc_mnm_layout_override'    => array( $importer, 'parse_bool_field' ),
		);

		$mapped_keys_reverse = array_flip( $importer->get_mapped_keys() );

		// Add all our callbacks by array index.
		foreach ( $mnm_callbacks as $mnm_key => $mnm_callback ) {
			if ( isset( $mapped_keys_reverse[ $mnm_key ] ) ) {
				$callbacks[ $mapped_keys_reverse[ $mnm_key ] ] = $mnm_callback;
			}
		}

		return $callbacks;
	}

	/**
	 * JSON Decode MNM child data.
	 *
	 * @since 2.3.0
	 *
	 * @param string $value Field value.
	 * @return array
	 */
	public static function decode_child_items( $value ) {
		return json_decode( $value, true );
	}

	/**
	 * If not null string, parse an integer.
	 *
	 * @since 2.3.0
	 *
	 * @param string $value Field value.
	 * @return mixed
	 */
	public static function maybe_parse_intval( $value ) {
		return ! empty( $value ) ? intval( $value ) : '';
	}

	/**
	 * Decode MNM child data items and parse relative IDs.
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_child_items( $parsed_data, $importer ) {

		if ( empty( $parsed_data['wc_mnm_child_items'] ) && ! empty( $parsed_data['wc_mnm_contents'] ) ) {
			wc_deprecated_argument( 'wc_mnm_contents', '2.0.0', 'New export column is wc_mnm_child_items. Generate a new product export using Mix and Match 2.0' );
			$parsed_data['wc_mnm_child_items'] = $parsed_data['wc_mnm_contents'];
		}

		if ( ! empty( $parsed_data['wc_mnm_child_items'] ) ) {

			// Already JSON-decoded by self::decode_child_items().
			$child_data_items = $parsed_data['wc_mnm_child_items'];

			// Clean out JSON.
			$parsed_data['wc_mnm_child_items'] = array();

			if ( is_array( $child_data_items ) ) {

				foreach ( $child_data_items as $child_item_key => $child_data ) {

					if ( ! empty( $child_data['product_id'] ) ) {
						$parsed_data['wc_mnm_child_items'][] = array(
							'product_id'   => $importer->parse_relative_field( $child_data['product_id'] ),
							'variation_id' => ! empty( $child_data['variation_id'] ) ? $importer->parse_relative_field( $child_data['variation_id'] ) : 0,
						);
					}
				}
			}
		}

		return $parsed_data;
	}

	/**
	 * Set container-type props.
	 * NB: We shouldn't need to parse anything further here. Parsed by core on import and rest handled by setters.
	 *
	 * @param WC_Product
	 * @param  array  $parsed_data
	 * @return WC_Product
	 */
	public static function set_mnm_props( $product, $data ) {

		if ( $product instanceof WC_Product && wc_mnm_is_product_container_type( $product ) ) {

			/**
			 * Filter container-type props.
			 *
			 * @param  array  $props - Container props.
			 * @param  WC_Product - The product object.
			 * @param  array $data - imported data.
			 * @return array
			 */

			$props = (array) apply_filters( 'wc_mnm_import_set_props', self::get_parsed_props( $data, $product ), $product, $data );

			if ( ! empty( $props ) ) {
				$product->set_props( $props );
			}
		}

		return $product;
	}


	/**
	 * Get container-type props from parsed data.
	 *
	 * @param  array  $data
	 * @param WC_Product
	 * @return array
	 */
	public static function get_parsed_props( $data, $product ) {

		$props = array();

		if ( isset( $data['wc_mnm_min_container_size'] ) ) {
			$props['min_container_size'] = $data['wc_mnm_min_container_size'];
		}

		if ( isset( $data['wc_mnm_max_container_size'] ) ) {
			$props['max_container_size'] = $data['wc_mnm_max_container_size'];
		}

		if ( isset( $data['wc_mnm_content_source'] ) ) {
			$props['content_source'] = $data['wc_mnm_content_source'];
		}

		if ( isset( $data['wc_mnm_child_category_ids'] ) ) {
			$props['child_category_ids'] = $data['wc_mnm_child_category_ids'];
		}

		// NB: Null cells are not parsed so we don't need to do anything special here to account for them.
		if ( isset( $data['wc_mnm_child_items'] ) ) {
			$props['child_items'] = $data['wc_mnm_child_items'];
		}

		if ( isset( $data['wc_mnm_packing_mode'] ) ) {
			$props['packing_mode'] = $data['wc_mnm_packing_mode'];
		}

		if ( isset( $data['wc_mnm_weight_cumulative'] ) ) {
			$props['weight_cumulative'] = $data['wc_mnm_weight_cumulative'];
		}

		if ( isset( $data['wc_mnm_priced_per_product'] ) ) {
			$props['priced_per_product'] = $data['wc_mnm_priced_per_product'];
		}

		if ( isset( $data['wc_mnm_discount'] ) ) {
			$props['discount'] = $data['wc_mnm_discount'];
		}

		if ( isset( $data['wc_mnm_layout_override'] ) ) {
			$props['layout_override'] = $data['wc_mnm_layout_override'];
		}

		if ( isset( $data['wc_mnm_layout'] ) ) {
			$props['layout'] = $data['wc_mnm_layout'];
		}

		if ( isset( $data['wc_mnm_add_to_cart_form_location'] ) ) {
			$props['add_to_cart_form_location'] = $data['wc_mnm_add_to_cart_form_location'];
		}

		return $props;
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

		if ( empty( $parsed_data['wc_mnm_child_items'] ) && ! empty( $parsed_data['wc_mnm_contents'] ) ) {
			$parsed_data['wc_mnm_child_items'] = $parsed_data['wc_mnm_contents'];
		}
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::parse_child_items()' );
		return parse_child_items( $parsed_data, $importer );
	}

	/**
	 * Decode MNM child category items and parse relative term IDs.
	 *
	 * @since 2.0.0
	 * @deprecated 2.3.0 - Register formatter directly in append_formatting_callbacks()
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array
	 */
	public static function parse_child_category_ids( $parsed_data, $importer ) {

		wc_deprecated_function( __METHOD__ . '()', '2.3.0', 'WC_Product_CSV_Importer::parse_categories_field()' );

		if ( ! empty( $parsed_data['wc_mnm_child_category_ids'] ) ) {
			$parsed_data['wc_mnm_child_category_ids'] = $importer->parse_categories_field( $parsed_data['wc_mnm_child_category_ids'] );
		}

		return $parsed_data;
	}
}
WC_MNM_Product_Import::init();
