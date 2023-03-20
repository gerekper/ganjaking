<?php
/**
 * WooCommerce Customer/Order/Coupon Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Admin;

defined( 'ABSPATH' ) or exit;

/**
 * Manual Exports admin page
 *
 * @since 5.0.0
 */
class Manual_Export {


	/**
	 * Show the Manual Export admin page.
	 *
	 * In 5.0.0 moved from \WC_Customer_Order_CSV_Export_Admin::render_export_page()
	 *
	 * @since 5.0.0
	 */
	public function output() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce_csv_exports' ) ) {
			return;
		}

		// show export form
		woocommerce_admin_fields( $this->get_settings() );

		wp_nonce_field( __FILE__ );

		submit_button( __( 'Export', 'woocommerce-customer-order-csv-export' ) );
	}


	/**
	 * Returns settings fields for the Manual Export admin page.
	 *
	 * In 5.0.0 moved from \WC_Customer_Order_CSV_Export_Admin::get_export_options()
	 *
	 * @since 5.0.0
	 */
	private function get_settings() {

		$output_types       = wc_customer_order_csv_export()->get_output_types();
		$export_types       = wc_customer_order_csv_export()->get_export_types();
		$order_statuses     = wc_get_order_statuses();
		$product_categories = [];

		foreach ( get_terms( 'product_cat' ) as $term ) {
			$product_categories[ $term->term_id ] = $term->name;
		}

		$options = [

			'export_section_title' => [
				'name' => __( 'Manual Export', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			],

			'type' => [
				'id'      => 'type',
				'name'    => __( 'Export type', 'woocommerce-customer-order-csv-export' ),
				'type'    => 'radio',
				'options' => $export_types,
				'default'  => \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
				'class'   => 'js-automation-export-type',
			],

			'output_type' => [
				'id'      => 'output_type',
				'name'    => __( 'Output type', 'woocommerce-customer-order-csv-export' ),
				'type'    => 'radio',
				'options' => wc_customer_order_csv_export()->get_output_types(),
				'default' => \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV,
				'class'   => 'js-automation-output-type',
			],

		];

		$use_legacy_formats = 'yes' === get_option( 'wc_customer_order_export_keep_legacy_formats' );

		$field_strings = [
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS    => [
				'filename'         => __( 'The filename for exported orders. Merge variables: %%timestamp%%, %%order_ids%%', 'woocommerce-customer-order-csv-export' ),
				'mark_as_exported' => __( 'If enabled, all exported orders will be marked as exported and excluded from future automated exports.', 'woocommerce-customer-order-csv-export' ),
			],
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS => [
				'filename'         => __( 'The filename for exported customers. Merge variables: %%timestamp%%', 'woocommerce-customer-order-csv-export' ),
				'mark_as_exported' => __( 'If enabled, all exported customers will be marked as exported and excluded from future automated exports.', 'woocommerce-customer-order-csv-export' ),
			],
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS   => [
				'filename' => __( 'The filename for exported coupons. Merge variables: %%timestamp%%', 'woocommerce-customer-order-csv-export' ),
			]
		];

		$user_id = get_current_user_id();

		// export format and filename fields for each combination of output type and export type
		foreach ( array_keys( $output_types ) as $output_type ) {

			foreach ( array_keys( $export_types ) as $export_type ) {

				// get saved export format for manual exports of this type
				$saved_export_format = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_{$output_type}_{$export_type}_manual_export_format", true ) : '';

				$options[ "{$output_type}_{$export_type}_export_format" ] = [
					'id'       => "{$output_type}_{$export_type}_export_format",
					'name'     => __( 'Format', 'woocommerce-customer-order-csv-export' ),
					'desc_tip' => __( 'Default is a new format for v3.0, Import matches the Customer/Order CSV Import plugin format.', 'woocommerce-customer-order-csv-export' ),
					'type'     => 'select_with_optgroup',
					'class'    => "wc-enhanced-select js-output-type-field show_if_${output_type} js-export-type-field show_if_${export_type}",
					'options'  => Export_Formats_Helper::get_export_formats( $output_type, $export_type, $use_legacy_formats ),
					'default'  => $saved_export_format ? $saved_export_format : 'default',
				];

				// get saved filename for manual exports of this type
				$custom_filename = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_{$output_type}_{$export_type}_manual_export_filename", true ) : '';

				$options[ "{$output_type}_{$export_type}_filename" ] = [
					'id'       => "{$output_type}_{$export_type}_filename",
					'name'     => __( 'Filename', 'woocommerce-customer-order-csv-export' ),
					'desc_tip' => $field_strings[ $export_type ]['filename'],
					'default'  => $custom_filename ?: "{$export_type}-export-%%timestamp%%.{$output_type}",
					'css'      => 'min-width: 300px;',
					'type'     => 'text',
					'class'    => "js-output-type-field show_if_${output_type} js-export-type-field show_if_${export_type}",
				];

			}

		}

		// mark as exported and include exported fields for orders and customers
		foreach ( [ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS ] as $export_type ) {

			$options[ "${export_type}_mark_as_exported" ] = [
				'id'       => "${export_type}_mark_as_exported",
				'name'     => __( 'Mark as exported', 'woocommerce-customer-order-csv-export' ),
				'desc'     => $field_strings[ $export_type ]['mark_as_exported'],
				'default'  => 'yes',
				'type'     => 'checkbox',
				'class'    => "js-export-type-field show_if_{$export_type}",
			];
		}

		// get saved general setting for manual exports
		$saved_batch_enabled   = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_manual_export_batch_enabled", true ) : '';
		$default_batch_enabled = ! empty( $saved_batch_enabled ) ? $saved_batch_enabled : false;

		$options = array_merge( $options, [

			'enable_batch_processing' => [
				'id'       => 'enable_batch_processing',
				'name'     => __( 'Batch processing', 'woocommerce-customer-order-csv-export' ),
				'desc'     => __( 'Use batch processing for manual exports.', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'Only enable this setting when notified that your site does not support background processing.', 'woocommerce-customer-order-csv-export' ),
				'default'  => $default_batch_enabled ? 'yes' : 'no',
				'type'     => 'checkbox',
			],

			'export_section_end' => [ 'type' => 'sectionend' ],

			'export_options_section_title' => [
				'name' => __( 'Export Options', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			],

			'statuses' => [
				'id'                => 'statuses',
				'name'              => __( 'Order statuses', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with these statuses will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'options'           => $order_statuses,
				'default'           => '',
				'class'             => 'wc-enhanced-select js-export-type-field show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export orders with any status.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			'refunds' => [
				'id'       => 'refunds',
				'name'     => __( 'Refunds', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'Determine whether to export all orders or those with at least 1 refund.', 'woocommerce-customer-order-csv-export' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select js-export-type-field show_if_orders',
				'default'  => 'all',
				'options'  => [
					'all'          => __( 'Include all orders', 'woocommerce-customer-order-csv-export' ),
					'only_refunds' => __( 'Only include orders with refunds', 'woocommerce-customer-order-csv-export' ),
				],
			],

			'product_categories' => [
				'id'                => 'product_categories',
				'name'              => __( 'Product categories', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with products in these categories will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'options'           => $product_categories,
				'default'           => '',
				'class'             => 'wc-enhanced-select js-export-type-field show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export orders with products in any category.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			'products' => [
				'id'                => 'products',
				'name'              => __( 'Products', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with these products will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'csv_product_search',
				'default'           => [],
				'value'             => [],
				'class'             => 'wc-product-search js-export-type-field show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Leave blank to export orders with any products.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			'coupon_product_categories' => [
				'id'                => 'coupon_product_categories',
				'name'              => __( 'Product categories', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Coupons that apply to these categories will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'options'           => $product_categories,
				'default'           => '',
				'class'             => 'wc-enhanced-select js-export-type-field show_if_coupons',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export coupons that apply to any category.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			'coupon_products' => [
				'id'                => 'coupon_products',
				'name'              => __( 'Products', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Coupons that apply to these products will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'csv_product_search',
				'default'           => [],
				'value'             => [],
				'class'             => 'wc-product-search js-export-type-field show_if_coupons',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Leave blank to export coupons that apply to any products.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			'start_date' => [
				'id'   => 'start_date',
				'name' => __( 'Start date', 'woocommerce-customer-order-csv-export' ),
				'class' => 'js-output-type-field show_if_csv show_if_xml js-export-type-field show_if_orders show_if_customers',
				'desc' => __( 'Start date of customers or orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', 'woocommerce-customer-order-csv-export' ),
				'type' => 'text',
			],

			'end_date' => [
				'id'   => 'end_date',
				'name' => __( 'End date', 'woocommerce-customer-order-csv-export' ),
				'class' => 'js-output-type-field show_if_csv show_if_xml js-export-type-field show_if_orders show_if_customers',
				'desc' => __( 'End date of customers or orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', 'woocommerce-customer-order-csv-export' ),
				'type' => 'text',
			],

		] );

		$add_notes_options = [];
		foreach ( array_keys( $output_types ) as $output_type ) {

			$saved_add_order_notes   = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_{$output_type}_manual_export_add_order_notes", true ) : '';
			$default_add_order_notes = '' !== $saved_add_order_notes ? $saved_add_order_notes : true;

			$add_notes_options["{$output_type}_add_notes"] = [
				'id'      => "{$output_type}_add_notes",
				'name'    => __( 'Add order notes', 'woocommerce-customer-order-csv-export' ),
				'desc'    => __( 'Enable to add a note to exported orders.', 'woocommerce-customer-order-csv-export' ),
				'type'    => 'checkbox',
				'default' => $default_add_order_notes ? 'yes' : 'no',
				'class'   => "js-output-type-field show_if_${output_type} js-export-type-field show_if_orders",
			];
		}

		$options = array_merge( $options, $add_notes_options );

		$options['export_options_section_end'] = [ 'type' => 'sectionend' ];

		if ( wc_customer_order_csv_export()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			$options['subscription_options_section_title'] = [
				'name' => __( 'Subscriptions options', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			];

			$options['subscription_orders'] = [
				'id'            => 'subscription_orders',
				'title'         => __( 'Export subscriptions orders only', 'woocommerce-customer-order-csv-export' ),
				'desc'          => __( 'Export subscription orders', 'woocommerce-customer-order-csv-export' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'class'         => 'js-export-type-field show_if_orders',
			];

			$options['subscription_renewals'] = [
				'id'            => 'subscription_renewals',
				'desc'          => __( 'Export renewal orders', 'woocommerce-customer-order-csv-export' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'class'         => 'js-export-type-field show_if_orders',
			];

			$options['subscription_options_section_end'] = [ 'type' => 'sectionend' ];

		}

		/**
		 * Allow actors to add or remove options from the CSV export page.
		 *
		 * @since 4.0.0
		 * @param array $options an array of options for the export tab
		 */
		return apply_filters( 'wc_customer_order_export_options', $options );
	}


}
