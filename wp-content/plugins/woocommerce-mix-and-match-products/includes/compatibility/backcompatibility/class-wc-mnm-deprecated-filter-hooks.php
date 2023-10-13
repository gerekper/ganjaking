<?php
/**
 * Deprecated filter hooks
 *
 * @package WooCommerce Mix and Match/Compatibility
 * @since   2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles deprecation notices and triggering of legacy filter hooks
 */
class WC_MNM_Deprecated_Filter_Hooks extends WC_Deprecated_Filter_Hooks {

	/**
	 * Array of deprecated hooks we need to handle.
	 * Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
		'wc_mnm_grid_layout_columns'                  => 'woocommerce_mnm_grid_layout_columns',
		'wc_mnm_tabular_column_headers'               => 'woocommerce_mnm_tabular_column_headers',
		'wc_mnm_is_container_order_item_editable'     => 'woocommerce_is_mnm_container_order_item_editable',
		'wc_mnm_csv_product_import_mapping_default_columns' => 'woocommerce_mnm_csv_product_import_mapping_default_columns',
		'wc_mnm_csv_product_import_mapping_options'   => 'woocommerce_mnm_csv_product_import_mapping_options',
		'wc_mnm_import_set_props'                     => 'woocommerce_mnm_import_set_props',
		'wc_mnm_export_column_names'                  => 'woocommerce_mnm_export_column_names',
		'wc_mnm_system_status'                        => 'woocommerce_mnm_system_status',
		'wc_mnm_form_wrapper_classes'                 => 'woocommerce_mnm_form_wrapper_classes',
		'wc_mnm_container_min_size'                   => 'woocommerce_mnm_min_container_size',
		'wc_mnm_container_max_size'                   => 'woocommerce_mnm_max_container_size',
		'wc_mnm_container_is_priced_per_product'      => 'woocommerce_mnm_priced_per_product',
		'wc_mnm_container_has_discount'               => 'woocommerce_mnm_has_discount',
		// 'wc_mnm_shipped_per_product'                      => 'woocommerce_mnm_shipped_per_product', // Intentionally commented out. Replaced by it's inverse: wc_mnm_container_is_packed_together @see: WC_Product_Mix_and_Match::is_packed_together()
			'wc_mnm_container_is_on_sale'             => 'woocommerce_mnm_is_on_sale',
		'wc_mnm_container_empty_price_html'           => 'woocommerce_mnm_empty_price_html',
		'wc_mnm_container_show_free_string'           => 'woocommerce_mnm_show_free_string',
		'wc_mnm_container_free_price_html'            => 'woocommerce_mnm_free_price_html',
		'wc_mnm_container_sale_price_html'            => 'woocommerce_mnm_sale_price_html',
		'wc_mnm_container_get_price_html'             => 'woocommerce_get_mnm_price_html',
		'wc_mnm_container_prices_hash'                => 'woocommerce_mnm_prices_hash',
		'wc_mnm_container_price_data'                 => 'woocommerce_mnm_container_price_data',
		'wc_mnm_container_data_attributes'            => 'woocommerce_mix_and_match_data_attributes',
		'wc_mnm_child_item_discount_from_regular'     => 'woocommerce_mnm_item_discount_from_regular',
		'wc_mnm_child_cart_item_data'                 => 'woocommerce_mnm_child_cart_item_data',
		'wc_mnm_child_item_cart_item_identifier'      => 'woocommerce_mnm_child_item_cart_item_identifier',
		'wc_mnm_get_posted_container_configuration'   => 'woocommerce_mnm_get_posted_container_configuration',
		'wc_mnm_get_posted_container_form_data'       => 'woocommerce_mnm_get_posted_container_form_data',
		'wc_mnm_before_container_validation'          => 'woocommerce_mnm_before_container_validation',
		'wc_mnm_container_validation_context'         => 'woocommerce_mnm_container_validation_context',
		'wc_mnm_add_to_cart_container_validation'     => 'woocommerce_mnm_add_to_cart_container_validation',
		'wc_mnm_cart_container_validation'            => 'woocommerce_mnm_cart_container_validation',
		'wc_mnm_add_to_order_container_validation'    => 'woocommerce_mnm_add_to_order_container_validation',
		'wc_mnm_container_quantity_error_message'     => 'woocommerce_mnm_container_quantity_error_message',
		'wc_mnm_child_cart_item'                      => 'woocommerce_mnm_cart_item',
		'wc_mnm_container_cart_item'                  => 'woocommerce_mnm_container_cart_item',
		'wc_mnm_child_item_shipped_individually'      => 'woocommerce_mnm_item_shipped_individually',
		'wc_mnm_child_item_has_cumulative_weight'     => 'woocommerce_mnm_item_has_bundled_weight',
		'wc_mnm_add_to_cart_script_parameters'        => 'woocommerce_mnm_add_to_cart_parameters',
		'wc_mnm_order_item_legacy_part_of_meta'       => 'woocommerce_mnm_order_item_legacy_part_of_meta',
		'wc_mnm_supported_products'                   => 'woocommerce_mnm_supported_products',
		'wc_mnm_order_item_legacy_part_of_meta'       => 'woocommerce_mnm_order_item_legacy_part_of_meta',
		'wc_mnm_order_item_container_size_meta_value' => 'woocommerce_mnm_order_item_container_size_meta_value',
		'wc_mnm_order_item_meta_title'                => 'woocommerce_mnm_order_item_meta_title',
		'wc_mnm_order_item_meta_description'          => 'woocommerce_mnm_order_item_meta_description',
		'wc_mnm_sku_from_order_item'                  => 'woocommerce_mnm_sku_from_order_item',
		'wc_mnm_container_quantity_message'           => 'woocommerce_mnm_container_quantity_message',
		'wc_mnm_cocart_item_add_to_cart_validation'   => 'woocommerce_mnm_cocart_item_add_to_cart_validation',
		'wc_mnm_add_to_cart_form_location_options'    => 'woocommerce_mnm_add_to_cart_form_location_options',
		'wc_mnm_supported_layouts'                    => 'woocommerce_mnm_supported_layouts',
		'wc_mnm_quantity_name_prefix'                 => 'woocommerce_mnm_quantity_name_prefix',
		'wc_mnm_query_products_by_categories_args'    => 'wc_mnm_categories_get_cat_content_args',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
		'woocommerce_mnm_grid_layout_columns'              => '2.0.0',
		'woocommerce_mnm_tabular_column_headers'           => '2.0.0',
		'woocommerce_is_mnm_container_order_item_editable' => '2.0.0',
		'woocommerce_mnm_csv_product_import_mapping_default_columns' => '2.0.0',
		'woocommerce_mnm_csv_product_import_mapping_options' => '2.0.0',
		'woocommerce_mnm_import_set_props'                 => '2.0.0',
		'woocommerce_mnm_export_column_names'              => '2.0.0',
		'woocommerce_mnm_system_status'                    => '2.0.0',
		'woocommerce_mnm_form_wrapper_classes'             => '2.0.0',
		'woocommerce_mnm_min_container_size'               => '2.0.0',
		'woocommerce_mnm_max_container_size'               => '2.0.0',
		'woocommerce_mnm_priced_per_product'               => '2.0.0',
		'woocommerce_mnm_has_discount'                     => '2.0.0',
		'woocommerce_mnm_shipped_per_product'              => '2.0.0',
		'woocommerce_mnm_is_on_sale'                       => '2.0.0',
		'woocommerce_mnm_empty_price_html'                 => '2.0.0',
		'woocommerce_mnm_show_free_string'                 => '2.0.0',
		'woocommerce_mnm_free_price_html'                  => '2.0.0',
		'woocommerce_mnm_sale_price_html'                  => '2.0.0',
		'woocommerce_get_mnm_price_html'                   => '2.0.0',
		'woocommerce_mnm_prices_hash'                      => '2.0.0',
		'woocommerce_mnm_container_price_data'             => '2.0.0',
		'woocommerce_mix_and_match_data_attributes'        => '2.0.0',
		'woocommerce_mnm_item_discount_from_regular'       => '2.0.0',
		'woocommerce_mnm_child_item_cart_item_identifier'  => '2.0.0',
		'woocommerce_mnm_child_cart_item_data'             => '2.0.0',
		'woocommerce_mnm_get_posted_container_configuration' => '2.0.0',
		'woocommerce_mnm_get_posted_container_form_data'   => '2.0.0',
		'woocommerce_mnm_before_container_validation'      => '2.0.0',
		'woocommerce_mnm_container_validation_context'     => '2.0.0',
		'woocommerce_mnm_add_to_cart_container_validation' => '2.0.0',
		'woocommerce_mnm_cart_container_validation'        => '2.0.0',
		'woocommerce_mnm_add_to_order_container_validation' => '2.0.0',
		'woocommerce_mnm_container_quantity_error_message' => '2.0.0',
		'woocommerce_mnm_cart_item'                        => '2.0.0',
		'woocommerce_mnm_container_cart_item'              => '2.0.0',
		'woocommerce_mnm_item_shipped_individually'        => '2.0.0',
		'woocommerce_mnm_item_has_bundled_weight'          => '2.0.0',
		'woocommerce_mnm_add_to_cart_parameters'           => '2.0.0',
		'woocommerce_mnm_order_item_legacy_part_of_meta'   => '2.0.0',
		'woocommerce_mnm_supported_products'               => '2.0.0',
		'woocommerce_mnm_order_item_legacy_part_of_meta'   => '2.0.0',
		'woocommerce_mnm_order_item_container_size_meta_value' => '2.0.0',
		'woocommerce_mnm_order_item_meta_title'            => '2.0.0',
		'woocommerce_mnm_order_item_meta_description'      => '2.0.0',
		'woocommerce_mnm_sku_from_order_item'              => '2.0.0',
		'woocommerce_mnm_container_quantity_message'       => '2.0.0',
		'woocommerce_mnm_cocart_item_add_to_cart_validation' => '2.0.0',
		'woocommerce_mnm_add_to_cart_form_location_options' => '2.0.0',
		'woocommerce_mnm_supported_layouts'                => '2.0.0',
		'woocommerce_mnm_quantity_name_prefix'             => '2.0.0',
		'wc_mnm_categories_get_cat_content_args'           => '2.0.0',
	);
}
