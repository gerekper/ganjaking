<?php
/**
 * WC_CP_Product_Export class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.11.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Exporter support.
 *
 * @class    WC_CP_Product_Export
 * @version  3.14.0
 */
class WC_CP_Product_Export {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Add CSV columns for exporting composite data.
		add_filter( 'woocommerce_product_export_column_names', array( __CLASS__, 'add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( __CLASS__, 'add_columns' ) );

		// Custom column content.
		add_filter( 'woocommerce_product_export_product_column_wc_cp_components', array( __CLASS__, 'export_components' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_cp_scenarios', array( __CLASS__, 'export_scenarios' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_cp_layout', array( __CLASS__, 'export_layout' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_cp_editable_in_cart', array( __CLASS__, 'export_editable_in_cart' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_cp_sold_individually_context', array( __CLASS__, 'export_sold_individually_context' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_cp_shop_price_calc', array( __CLASS__, 'export_shop_price_calc' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wc_cp_add_to_cart_form_location', array( __CLASS__, 'export_add_to_cart_form_location' ), 10, 2 );
	}

	/**
	 * Add CSV columns for exporting composite data.
	 *
	 * @param  array  $columns
	 * @return array  $columns
	 */
	public static function add_columns( $columns ) {

		$columns[ 'wc_cp_components' ]                = __( 'Composite Components (JSON-encoded)', 'woocommerce-composite-products' );
		$columns[ 'wc_cp_scenarios' ]                 = __( 'Composite Scenarios (JSON-encoded)', 'woocommerce-composite-products' );
		$columns[ 'wc_cp_layout' ]                    = __( 'Composite Layout', 'woocommerce-composite-products' );
		$columns[ 'wc_cp_editable_in_cart' ]          = __( 'Composite Cart Editing', 'woocommerce-composite-products' );
		$columns[ 'wc_cp_sold_individually_context' ] = __( 'Composite Sold Individually', 'woocommerce-composite-products' );
		$columns[ 'wc_cp_shop_price_calc' ]           = __( 'Composite Catalog Price', 'woocommerce-composite-products' );
		$columns[ 'wc_cp_add_to_cart_form_location' ] = __( 'Composite Form Location', 'woocommerce-composite-products' );

		return $columns;
	}

	/**
	 * Components column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return string      $value
	 */
	public static function export_components( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {

			$term_ids_exporter    = false;
			$components_rest_data = $product->get_composite_data( 'rest' );

			if ( ! empty( $components_rest_data ) ) {

				$components_export_data = array();

				foreach ( $components_rest_data as $component_rest_data ) {

					$component_export_data = $component_rest_data;

					// Replace 'thumbnail_id' with 'thumbnail_src'.
					if ( ! empty( $component_rest_data[ 'thumbnail_src' ] ) ) {
						unset( $component_export_data[ 'thumbnail_id' ] );
					}

					// Refer to default option by SKU, if possible.
					if ( ! empty( $component_rest_data[ 'default_option_id' ] ) ) {

						$default_option = wc_get_product( $component_rest_data[ 'default_option_id' ] );

						if ( $default_option ) {
							$default_option_sku  = $default_option->get_sku( 'edit' );
							$component_export_data[ 'default_option_id' ] = $default_option_sku ? $default_option_sku : 'id:' . $component_rest_data[ 'default_option_id' ];
						}
					}

					// Refer to component option IDs by SKU, if possible.
					if ( ! empty( $component_rest_data[ 'query_ids' ] ) && is_array( $component_rest_data[ 'query_ids' ] ) ) {

						if ( 'product_ids' === $component_rest_data[ 'query_type' ] ) {

							$query_ids = array();

							foreach ( $component_rest_data[ 'query_ids' ] as $query_id ) {

								$option = wc_get_product( $query_id );

								if ( $option ) {
									$option_sku  = $option->get_sku( 'edit' );
									$query_ids[] = $option_sku ? $option_sku : 'id:' . $query_id;
								}
							}

							$component_export_data[ 'query_ids' ] = implode( ',', $query_ids );

						} elseif ( 'category_ids' === $component_rest_data[ 'query_type' ] ) {

							$term_ids_exporter                    = false === $term_ids_exporter ? new WC_Product_CSV_Exporter() : $term_ids_exporter;
							$component_export_data[ 'query_ids' ] = $term_ids_exporter->format_term_ids( $component_rest_data[ 'query_ids' ], 'product_cat' );
						}
					}

					// Export attribute filters by name, not ID.
					if ( isset( $component_rest_data[ 'attribute_filter_ids' ] ) ) {

						$atttribute_filter_labels = array();

						if ( ! empty( $component_rest_data[ 'attribute_filter_ids' ] ) && is_array( $component_rest_data[ 'attribute_filter_ids' ] ) ) {

							global $wc_product_attributes;

							foreach ( $wc_product_attributes as $attribute_taxonomy_name => $attribute_data ) {

								if ( in_array( $attribute_data->attribute_id, $component_rest_data[ 'attribute_filter_ids' ] ) && taxonomy_exists( $attribute_taxonomy_name ) ) {
									$atttribute_filter_labels[] = $attribute_data->attribute_label;
								}
							}
						}

						$component_export_data[ 'attribute_filters' ] = $atttribute_filter_labels;
						unset( $component_export_data[ 'attribute_filter_ids' ] );
					}

					$components_export_data[] = $component_export_data;
				}

				$value = json_encode( $components_export_data );
			}
		}

		return $value;
	}

	/**
	 * Scenarios column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return string      $value
	 */
	public static function export_scenarios( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {

			$scenarios_rest_data = $product->get_scenario_data( 'rest' );

			if ( ! empty( $scenarios_rest_data ) ) {

				$scenarios_export_data = array();

				foreach ( $scenarios_rest_data as $scenario_rest_data ) {

					$scenario_export_data = $scenario_rest_data;

					if ( ! empty( $scenario_rest_data[ 'configuration' ] ) ) {
						foreach ( $scenario_rest_data[ 'configuration' ] as $component_index => $component_configuration ) {
							if ( ! empty( $component_configuration[ 'component_options' ] ) && is_array( $component_configuration[ 'component_options' ] ) ) {

								$option_ids = array();

								foreach ( $component_configuration[ 'component_options' ] as $option_id ) {

									// Any flag.
									if ( 0 === intval( $option_id ) ) {
										$option_ids[] = 'selection:any';
									// None flag.
									} elseif ( -1 === intval( $option_id ) ) {
										$option_ids[] = 'selection:none';
									// Product IDs.
									} else {

										$option = wc_get_product( $option_id );

										if ( $option ) {
											$option_sku   = $option->get_sku( 'edit' );
											$option_ids[] = $option_sku ? $option_sku : 'id:' . $option_id;
										}
									}
								}

								$scenario_export_data[ 'configuration' ][ $component_index ][ 'component_options' ] = implode( ',', $option_ids );
							}
						}
					}

					$scenarios_export_data[] = $scenario_export_data;
				}

				$value = json_encode( $scenarios_export_data );
			}
		}

		return $value;
	}

	/**
	 * "Composite Layout" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_layout( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {
			$value = $product->get_layout( 'edit' );
		}

		return $value;
	}

	/**
	 * "Composite Cart Editing" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_editable_in_cart( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {
			$value = $product->get_editable_in_cart( 'edit' ) ? 1 : 0;
		}

		return $value;
	}

	/**
	 * "Composite Sold Individually" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_sold_individually_context( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {
			$value = $product->get_sold_individually_context( 'edit' );
		}

		return $value;
	}

	/**
	 * "Composite Catalog Proce" column content.
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_shop_price_calc( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {
			$value = $product->get_shop_price_calc( 'edit' );
		}

		return $value;
	}

	/**
	 * "Composite Form Location" column content.
	 *
	 * @since  3.14.0
	 *
	 * @param  mixed       $value
	 * @param  WC_Product  $product
	 * @return mixed       $value
	 */
	public static function export_add_to_cart_form_location( $value, $product ) {

		if ( $product->is_type( 'composite' ) ) {
			$value = $product->get_add_to_cart_form_location( 'edit' );
		}

		return $value;
	}
}

WC_CP_Product_Export::init();
