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

namespace SkyVerge\WooCommerce\CSV_Export;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Hook deprecator.
 *
 * @since 5.0.0
 */
class Hook_Deprecator extends Framework\SV_WC_Hook_Deprecator {


	/**
	 * Hook_Deprecator constructor.
	 *
	 * @since 5.0.0
	 * s
	 * @param \WC_Customer_Order_CSV_Export $plugin plugin instance
	 */
	public function __construct( \WC_Customer_Order_CSV_Export $plugin ) {

		parent::__construct( $plugin->get_plugin_name(), array_merge( $this->get_mapped_hooks(), $this->get_deprecated_hooks() ) );
	}


	/**
	 * Map a deprecated/renamed hook to a new one.
	 *
	 * This method copies the parent implementation to allow multiple old hooks
	 * to be replaced by a single new one.
	 *
	 * @see Framework\SV_WC_Hook_Deprecator
	 *
	 * @since 5.0.0
	 *
	 * @return mixed
	 */
	public function map_deprecated_hook() {

		$args     = func_get_args();
		$data     = $args[0];
		$new_hook = current_filter();

		$new_hooks = wp_list_pluck( $this->hooks, 'replacement' );

		// check if there are matching old hooks for the current hook
		foreach ( array_keys( $new_hooks, $new_hook ) as $old_hook ) {

			// check if there are any hooks added to the old hook
			if ( has_filter( $old_hook ) ) {

				// prepend old hook name to the args
				array_unshift( $args, $old_hook );

				// apply the hooks attached to the old hook to $data
				$data = call_user_func_array( 'apply_filters', $args );
			}
		}

		return $data;
	}


	/**
	 * Gets the deprecated hooks.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_deprecated_hooks() {

		return [

			'wc_customer_order_csv_export_admin_query_args' => [
				'version'     => '4.0.0',
				'replacement' => 'wc_customer_order_export_query_args',
				'removed'     => true,
				'map'         => true,
			],

			'wc_customer_order_csv_export_admin_user_query_args' => [
				'version'     => '4.0.0',
				'replacement' => 'wc_customer_order_export_user_query_args',
				'removed'     => true,
				'map'         => true,
			],

			'wc_customer_order_csv_export_settings' => [
				'version'     => '5.0.0',
				'removed'     => true,
				'map'         => false,
			],

			'wc_customer_order_export_format_column_data_options' => [
				'version'     => '5.0.0',
				'removed'     => true,
				'map'         => false,
			],
		];
	}


	/**
	 * Gets the hooks that should be mapped, but aren't deprecated.
	 *
	 * Many of the new hooks have two forms:
	 * + Hook name with the output type included, e.g. `wc_customer_order_export_start_csv_export_args`
	 * + Generic hook name that applies to all output types, e.g. `wc_customer_order_export_start_export_args`
	 *
	 * This allows us to map the deprecated CSV filters to their new versions, without accidentally also applying them
	 * to exports of other output types. For instance, people filtering the export filename in < v5 of the plugin
	 * shouldn't have their filtered .csv filename applied to XML exports after upgrade.
	 *
	 * Some exceptions include:
	 * + Filters that we can reasonably assume users would want to apply to XML exports as well, should they start using
	 *   them, like `wc_customer_order_export_auto_export_new_orders_only`
	 * + Filters that aren't aware of the output type and probably don't need to be, like `wc_customer_order_export_start_export_max_age`
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	protected function get_mapped_hooks() {

		$hooks = [];

		$mapped_hooks = [

			// deprecated in v5.0.0
			'5.0.0' => [

				// generator filters
				'wc_customer_order_csv_export_format'                     => 'wc_customer_order_export_csv_format',
				'wc_customer_order_csv_export_delimiter'                  => 'wc_customer_order_export_csv_delimiter',
				'wc_customer_order_csv_export_enclosure'                  => 'wc_customer_order_export_csv_enclosure',
				'wc_customer_order_csv_export_order_headers'              => 'wc_customer_order_export_csv_order_headers',
				'wc_customer_order_csv_export_get_orders_csv'             => 'wc_customer_order_export_get_orders_csv_output',
				'wc_customer_order_csv_export_order_line_item'            => 'wc_customer_order_export_csv_order_line_item',
				'wc_customer_order_csv_export_order_shipping_item'        => 'wc_customer_order_export_csv_order_shipping_item',
				'wc_customer_order_csv_export_order_fee_item'             => 'wc_customer_order_export_csv_order_fee_item',
				'wc_customer_order_csv_export_order_tax_item'             => 'wc_customer_order_export_csv_order_tax_item',
				'wc_customer_order_csv_export_order_coupon_item'          => 'wc_customer_order_export_csv_order_coupon_item',
				'wc_customer_order_csv_export_order_refund_data'          => 'wc_customer_order_export_csv_order_refund_data',
				'wc_customer_order_csv_export_order_row_one_row_per_item' => 'wc_customer_order_export_csv_order_row_one_row_per_item',
				'wc_customer_order_csv_export_order_row'                  => 'wc_customer_order_export_csv_order_row',
				'wc_customer_order_csv_export_get_customers_csv'          => 'wc_customer_order_export_get_customers_csv_output',
				'wc_customer_order_csv_export_customer_headers'           => 'wc_customer_order_export_csv_customer_headers',
				'wc_customer_order_csv_export_customer_row'               => 'wc_customer_order_export_csv_customer_row',
				'wc_customer_order_csv_export_get_coupons_csv'            => 'wc_customer_order_export_get_coupons_csv_output',
				'wc_customer_order_csv_export_coupon_headers'             => 'wc_customer_order_export_csv_coupon_headers',
				'wc_customer_order_csv_export_coupon_row'                 => 'wc_customer_order_export_csv_coupon_row',
				'wc_customer_order_csv_export_format_date'                => 'wc_customer_order_export_format_date',
				'wc_customer_order_csv_export_generated_csv_row'          => 'wc_customer_order_export_generated_csv_row',
				'wc_customer_order_csv_export_enable_bom'                 => 'wc_customer_order_export_csv_enable_bom',

				// cron filters
				'wc_customer_order_csv_export_auto_export_new_orders_only'    => 'wc_customer_order_export_auto_export_new_orders_only',
				'wc_customer_order_csv_auto_export_ids'                       => 'wc_customer_order_export_auto_export_ids',
				'wc_customer_order_csv_export_auto_export_new_customers_only' => 'wc_customer_order_export_auto_export_new_customers_only',
				'wc_customer_order_csv_auto_export_customers'                 => 'wc_customer_order_export_auto_export_customers',

				// download handler filters
				'wc_customer_order_csv_export_file_download_filename' => 'wc_customer_order_export_csv_file_download_filename',

				// export formats filters
				'wc_customer_order_csv_export_format_definition_object'   => 'wc_customer_order_export_format_definition_object',
				'wc_customer_order_csv_export_format_definition'          => 'wc_customer_order_export_csv_format_definition',
				'wc_customer_order_csv_export_meta_has_dedicated_column'  => 'wc_customer_order_export_meta_has_dedicated_source',
				'wc_customer_order_csv_export_all_meta_keys'              => 'wc_customer_order_export_all_meta_keys',
				'wc_customer_order_csv_export_format_column_data_options' => 'wc_customer_order_export_csv_format_data_sources',

				// export handler filters
				'wc_customer_order_csv_export_add_order_note'         => 'wc_customer_order_export_add_order_note',
				'wc_customer_order_csv_export_mark_order_exported'    => 'wc_customer_order_export_mark_order_exported',
				'wc_customer_order_csv_export_mark_customer_exported' => 'wc_customer_order_export_mark_customer_exported',
				'wc_customer_order_csv_export_filename_variables'     => 'wc_customer_order_export_filename_variables',
				'wc_customer_order_csv_export_filename'               => 'wc_customer_order_export_csv_filename',
				'wc_customer_order_csv_export_ids'                    => 'wc_customer_order_export_ids',
				'wc_customer_order_csv_export_start_export_args'      => 'wc_customer_order_export_start_csv_export_args',
				'wc_customer_order_csv_export_start_export_max_age'   => 'wc_customer_order_export_start_export_max_age',
				'wc_customer_order_csv_export_order_exported'         => 'wc_customer_order_export_csv_order_exported',
				'wc_customer_order_csv_export_customer_exported'      => 'wc_customer_order_export_csv_customer_exported',
				'wc_customer_order_csv_export_orders_exported'        => 'wc_customer_order_export_csv_orders_exported',
				'wc_customer_order_csv_export_customers_exported'     => 'wc_customer_order_export_csv_customers_exported',
				'wc_customer_order_csv_export_coupons_exported'       => 'wc_customer_order_export_csv_coupons_exported',

				// export methods filters
				'wc_customer_order_csv_export_email_subject'     => 'wc_customer_order_export_email_subject',
				'wc_customer_order_csv_export_get_export_method' => 'wc_customer_order_export_get_export_method',
				'wc_customer_order_csv_export_methods'           => 'wc_customer_order_export_methods',

				// query parser filters
				'wc_customer_order_csv_export_parsed_query_results' => 'wc_customer_order_export_parsed_query_results',
				'wc_customer_order_csv_export_query_args'           => 'wc_customer_order_export_query_args',
				'wc_customer_order_csv_export_user_query_args'      => 'wc_customer_order_export_user_query_args',
				'wc_customer_order_csv_export_coupon_query_args'    => 'wc_customer_order_export_coupon_query_args',
				'wc_customer_order_csv_export_before_orders_query'  => 'wc_customer_order_export_before_orders_query',
				'wc_customer_order_csv_export_after_orders_query'   => 'wc_customer_order_export_after_orders_query',
				'wc_customer_order_csv_export_before_users_query'   => 'wc_customer_order_export_before_users_query',
				'wc_customer_order_csv_export_after_users_query'    => 'wc_customer_order_export_after_users_query',

				// data store filters
				'wc_customer_order_csv_export_custom_data_store' => 'wc_customer_order_export_csv_custom_data_store',

				// export methods
				'wc_customer_order_csv_export_ftp_timeout'                    => 'wc_customer_order_export_ftp_timeout',
				'wc_customer_order_csv_export_ftp_over_implicit_curl_options' => 'wc_customer_order_export_ftp_over_implicit_curl_options',
				'wc_customer_order_csv_export_http_post_args'                 => 'wc_customer_order_export_http_post_args',
				'wc_customer_order_csv_export_http_post_success'              => 'wc_customer_order_export_http_post_success',

				// admin
				'wc_customer_order_csv_export_options'                                  => 'wc_customer_order_export_options',
				'wc_customer_order_csv_export_custom_formats_admin_sections'            => 'wc_customer_order_export_custom_formats_admin_sections',
				'wc_customer_order_csv_export_custom_format_settings'                   => 'wc_customer_order_export_custom_format_settings',
				'wc_customer_order_csv_export_admin_export_custom_formats_list_columns' => 'wc_customer_order_export_admin_export_custom_formats_list_columns',
				'wc_customer_order_csv_export_admin_custom_formats_list_custom_column'  => 'wc_customer_order_export_admin_custom_formats_list_custom_column',
				'wc_customer_order_csv_export_admin_custom_format_actions'              => 'wc_customer_order_export_admin_custom_format_actions',
				'wc_customer_order_csv_export_field_mapping_fields'                     => 'wc_customer_order_export_field_mapping_fields',
				'wc_customer_order_csv_export_admin_export_list_columns'                => 'wc_customer_order_export_admin_export_list_columns',
				'wc_customer_order_csv_export_admin_export_list_custom_column'          => 'wc_customer_order_export_admin_export_list_custom_column',
				'wc_customer_order_csv_export_admin_export_actions'                     => 'wc_customer_order_export_admin_export_actions',
				'wc_customer_order_csv_export_load_mapping_options'                     => 'wc_customer_order_export_load_mapping_options',

				// background handlers
				'wc_customer_order_csv_export_background_export_batch_handler_js_args'         => 'wc_customer_order_export_background_export_batch_handler_js_args',
				'wc_customer_order_csv_export_background_export_batch_handler_items_per_batch' => 'wc_customer_order_export_background_export_batch_handler_items_per_batch',
				'wc_customer_order_csv_export_background_export_queue_lock_time'               => 'wc_customer_order_export_background_export_queue_lock_time',
				'wc_customer_order_csv_export_background_export_memory_exceeded'               => 'wc_customer_order_export_background_export_memory_exceeded',
				'wc_customer_order_csv_export_background_export_default_time_limit'            => 'wc_customer_order_export_background_export_default_time_limit',
				'wc_customer_order_csv_export_background_export_time_exceeded'                 => 'wc_customer_order_export_background_export_time_exceeded',
				'wc_customer_order_csv_export_background_export_new_job_attrs'                 => 'wc_customer_order_export_background_export_new_job_attrs',
				'wc_customer_order_csv_export_background_export_returned_job'                  => 'wc_customer_order_export_background_export_returned_job',
				'wc_customer_order_csv_export_background_export_cron_interval'                 => 'wc_customer_order_export_background_export_cron_interval',
				'wc_customer_order_csv_export_background_export_job_created'                   => 'wc_customer_order_export_background_export_job_created',
				'wc_customer_order_csv_export_background_export_job_updated'                   => 'wc_customer_order_export_background_export_job_updated',
				'wc_customer_order_csv_export_background_export_job_complete'                  => 'wc_customer_order_export_background_export_job_complete',
				'wc_customer_order_csv_export_background_export_job_failed'                    => 'wc_customer_order_export_background_export_job_failed',
				'wc_customer_order_csv_export_background_export_job_deleted'                   => 'wc_customer_order_export_background_export_job_deleted',
			],
		];

		foreach ( $mapped_hooks as $version => $hooks ) {

			foreach ( $hooks as $old_hook => $new_hook ) {

				$hooks[ $old_hook ] = [
					'version'        => $version,
					'replacement'    => $new_hook,
					'removed'        => true,
					'map'            => true,
					'trigger_notice' => false,
				];
			}
		}

		return array_merge( $hooks, $this->get_xml_hooks() );
	}


	/**
	 * Gets the deprecated hooks from XML Export.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	protected function get_xml_hooks() {

		$hooks = [];

		$mapped_hooks = [

			// generator hooks
			'wc_customer_order_xml_export_suite_format'               => 'wc_customer_order_export_xml_format',
			'wc_customer_order_xml_export_suite_xml_indent'           => 'wc_customer_order_export_xml_indent',
			'wc_customer_order_xml_export_suite_xml_version'          => 'wc_customer_order_export_xml_version',
			'wc_customer_order_xml_export_suite_xml_encoding'         => 'wc_customer_order_export_xml_encoding',
			'wc_customer_order_xml_export_suite_xml_standalone'       => 'wc_customer_order_export_xml_standalone',
			'wc_customer_order_xml_export_suite_xml_root_element'     => 'wc_customer_order_export_xml_root_element',
			'wc_customer_order_xml_export_suite_orders_xml_data'      => 'wc_customer_order_export_xml_get_orders_xml_data',
			'wc_customer_order_xml_export_suite_orders_xml'           => 'wc_customer_order_export_xml_get_orders_output',
			'wc_customer_order_xml_export_suite_order_line_item'      => 'wc_customer_order_export_xml_order_line_item',
			'wc_customer_order_xml_export_suite_order_shipping_item'  => 'wc_customer_order_export_xml_order_shipping_item',
			'wc_customer_order_xml_export_suite_order_fee_item'       => 'wc_customer_order_export_xml_order_fee_item',
			'wc_customer_order_xml_export_suite_order_tax_item'       => 'wc_customer_order_export_xml_order_tax_item',
			'wc_customer_order_xml_export_suite_order_coupon_item'    => 'wc_customer_order_export_xml_order_coupon_item',
			'wc_customer_order_xml_export_suite_order_refund'         => 'wc_customer_order_export_xml_order_refund',
			'wc_customer_order_xml_export_suite_order_item_tax_data'  => 'wc_customer_order_export_xml_order_item_tax_data',
			'wc_customer_order_xml_export_suite_order_note'           => 'wc_customer_order_export_xml_order_note',
			'wc_customer_order_xml_export_suite_customers_xml_data'   => 'wc_customer_order_export_xml_customers_xml_data',
			'wc_customer_order_xml_export_suite_customers_xml'        => 'wc_customer_order_export_xml_customers_xml',
			'wc_customer_order_xml_export_suite_customer_export_data' => 'wc_customer_order_export_xml_customer_export_data',
			'wc_customer_order_xml_export_suite_coupons_xml_data'     => 'wc_customer_order_export_xml_coupons_xml_data',
			'wc_customer_order_xml_export_suite_coupons_xml'          => 'wc_customer_order_export_xml_coupons_xml',
			'wc_customer_order_xml_export_suite_coupon_export_data'   => 'wc_customer_order_export_xml_coupon_export_data',
			'wc_customer_order_xml_export_suite_format_date'          => 'wc_customer_order_export_xml_format_date',
			'wc_customer_order_xml_export_suite_orders_header'        => 'wc_customer_order_export_xml_orders_header',
			'wc_customer_order_xml_export_suite_customers_header'     => 'wc_customer_order_export_xml_customers_header',
			'wc_customer_order_xml_export_suite_coupons_header'       => 'wc_customer_order_export_xml_coupons_header',
			'wc_customer_order_xml_export_suite_orders_footer'        => 'wc_customer_order_export_xml_orders_footer',
			'wc_customer_order_xml_export_suite_customers_footer'     => 'wc_customer_order_export_xml_customers_footer',
			'wc_customer_order_xml_export_suite_coupons_footer'       => 'wc_customer_order_export_xml_coupons_footer',
			'wc_customer_order_xml_export_suite_get_orders_xml'       => 'wc_customer_order_export_get_orders_xml',
			'wc_customer_order_xml_export_suite_get_customers_xml'    => 'wc_customer_order_export_get_customers_xml',
			'wc_customer_order_xml_export_suite_get_coupons_xml'      => 'wc_customer_order_export_get_coupons_xml',

			// cron hooks
			'wc_customer_order_xml_export_suite_auto_export_new_orders_only'    => 'wc_customer_order_export_xml_auto_export_new_orders_only',
			'wc_customer_order_xml_export_suite_auto_export_ids'                => 'wc_customer_order_export_xml_auto_export_ids',
			'wc_customer_order_xml_export_suite_auto_export_new_customers_only' => 'wc_customer_order_export_xml_auto_export_new_customers_only',
			'wc_customer_order_xml_export_suite_auto_export_customers'          => 'wc_customer_order_export_xml_auto_export_customers',

			// download handler
			'wc_customer_order_xml_export_suite_file_download_filename' => 'wc_customer_order_export_xml_file_download_filename',

			// formats
			'wc_customer_order_xml_export_suite_format_field_data_options' => 'wc_customer_order_export_xml_format_data_sources',
			'wc_customer_order_xml_export_suite_format_definition'         => 'wc_customer_order_export_xml_format_definition',
			'wc_customer_order_xml_export_suite_all_meta_keys'             => 'wc_customer_order_export_all_meta_keys',
			'wc_customer_order_xml_export_suite_meta_has_dedicated_field'  => 'wc_customer_order_export_meta_has_dedicated_source',

			// export handler
			'wc_customer_order_xml_export_suite_add_order_note'         => 'wc_customer_order_export_xml_add_order_note',
			'wc_customer_order_xml_export_suite_mark_order_exported'    => 'wc_customer_order_export_xml_mark_order_exported',
			'wc_customer_order_xml_export_suite_mark_customer_exported' => 'wc_customer_order_export_xml_mark_customer_exported',
			'wc_customer_order_xml_export_suite_filename_variables'     => 'wc_customer_order_export_xml_filename_variables',
			'wc_customer_order_xml_export_suite_filename'               => 'wc_customer_order_export_xml_filename',
			'wc_customer_order_xml_export_suite_ids'                    => 'wc_customer_order_export_xml_ids',
			'wc_customer_order_xml_export_suite_start_export_args'      => 'wc_customer_order_export_start_xml_export_args',
			'wc_customer_order_xml_export_suite_export_max_age'         => 'wc_customer_order_export_start_export_max_age',
			'wc_customer_order_xml_export_suite_order_exported'         => 'wc_customer_order_export_xml_order_exported',
			'wc_customer_order_xml_export_suite_customer_exported'      => 'wc_customer_order_export_xml_customer_exported',
			'wc_customer_order_xml_export_suite_orders_exported'        => 'wc_customer_order_export_xml_orders_exported',
			'wc_customer_order_xml_export_suite_customers_exported'     => 'wc_customer_order_export_xml_customers_exported',
			'wc_customer_order_xml_export_suite_coupons_exported'       => 'wc_customer_order_export_xml_coupons_exported',

			// export methods
			'wc_customer_order_xml_export_suite_email_subject'     => 'wc_customer_order_export_xml_email_subject',
			'wc_customer_order_xml_export_suite_methods'           => 'wc_customer_order_export_methods',
			'wc_customer_order_xml_export_suite_get_export_method' => 'wc_customer_order_export_get_xml_export_method',

			// query parser
			'wc_customer_order_xml_export_suite_parsed_query_results' => 'wc_customer_order_export_parsed_xml_query_results',
			'wc_customer_order_xml_export_suite_query_args'           => 'wc_customer_order_export_xml_query_args',
			'wc_customer_order_xml_export_suite_user_query_args'      => 'wc_customer_order_export_xml_user_query_args',
			'wc_customer_order_xml_export_coupon_query_args'          => 'wc_customer_order_export_xml_coupon_query_args',
			'wc_customer_order_xml_export_suite_before_orders_query'  => 'wc_customer_order_export_before_xml_orders_query',
			'wc_customer_order_xml_export_suite_after_orders_query'   => 'wc_customer_order_export_after_xml_orders_query',
			'wc_customer_order_xml_export_suite_before_users_query'   => 'wc_customer_order_export_before_xml_users_query',
			'wc_customer_order_xml_export_suite_after_users_query'    => 'wc_customer_order_export_after_xml_users_query',

			// data stores
			'wc_customer_order_xml_export_suite_custom_data_store' => 'wc_customer_order_export_xml_custom_data_store',

			// export methods
			'wc_customer_order_xml_export_suite_ftp_timeout'                    => 'wc_customer_order_export_ftp_timeout',
			'wc_customer_order_xml_export_suite_ftp_over_implicit_curl_options' => 'wc_customer_order_export_ftp_over_implicit_curl_options',
			'wc_customer_order_xml_export_suite_http_post_args'                 => 'wc_customer_order_export_http_post_args',
			'wc_customer_order_xml_export_suite_http_post_success'              => 'wc_customer_order_export_http_post_success',

			// admin
			'wc_customer_order_xml_export_suite_options'                              => 'wc_customer_order_export_options',
			'wc_customer_order_xml_export_suite_custom_format_builder_sections'       => 'wc_customer_order_export_custom_formats_admin_sections',
			'wc_customer_order_xml_export_suite_custom_format_settings'               => 'wc_customer_order_export_custom_format_settings',
			'wc_customer_order_xml_export_suite_field_mapping_columns'                => 'wc_customer_order_export_format_data_sources',
			'wc_customer_order_xml_export_suite_sections'                             => 'wc_customer_order_export_xml_sections',
			'wc_customer_order_xml_export_suite_settings'                             => 'wc_customer_order_export_xml_settings',
			'wc_customer_order_xml_export_suite_admin_export_actions'                 => 'wc_customer_order_export_admin_export_actions',

			// background handlers
			'wc_customer_order_xml_export_suite_background_export_batch_handler_js_args'         => 'wc_customer_order_export_background_export_batch_handler_js_args',
			'wc_customer_order_xml_export_suite_background_export_batch_handler_items_per_batch' => 'wc_customer_order_export_background_export_batch_handler_items_per_batch',
			'wc_customer_order_xml_export_suite_background_export_queue_lock_time'               => 'wc_customer_order_export_background_export_queue_lock_time',
			'wc_customer_order_xml_export_suite_background_export_memory_exceeded'               => 'wc_customer_order_export_background_export_memory_exceeded',
			'wc_customer_order_xml_export_suite_background_export_default_time_limit'            => 'wc_customer_order_export_background_export_default_time_limit',
			'wc_customer_order_xml_export_suite_background_export_time_exceeded'                 => 'wc_customer_order_export_background_export_time_exceeded',
			'wc_customer_order_xml_export_suite_background_export_new_job_attrs'                 => 'wc_customer_order_export_background_export_new_job_attrs',
			'wc_customer_order_xml_export_suite_background_export_returned_job'                  => 'wc_customer_order_export_background_export_returned_job',
			'wc_customer_order_xml_export_suite_background_export_cron_interval'                 => 'wc_customer_order_export_background_export_cron_interval',
			'wc_customer_order_xml_export_suite_background_export_job_created'                   => 'wc_customer_order_export_background_export_job_created',
			'wc_customer_order_xml_export_suite_background_export_job_updated'                   => 'wc_customer_order_export_background_export_job_updated',
			'wc_customer_order_xml_export_suite_background_export_job_complete'                  => 'wc_customer_order_export_background_export_job_complete',
			'wc_customer_order_xml_export_suite_background_export_job_failed'                    => 'wc_customer_order_export_background_export_job_failed',
			'wc_customer_order_xml_export_suite_background_export_job_deleted'                   => 'wc_customer_order_export_background_export_job_deleted',

		];

		foreach ( $mapped_hooks as $old_hook => $new_hook ) {

			$hooks[ $old_hook ] = [
				'version'        => '5.0.0',
				'replacement'    => $new_hook,
				'removed'        => true,
				'map'            => true,
				'trigger_notice' => false,
			];
		}

		return $hooks;
	}


	/**
	 * Trigger errors for deprecated hooks.
	 *
	 * Overrides the framework handling to allow mapping a hook without throwing a notice.
	 *
	 * @since 5.0.0
	 */
	public function trigger_deprecated_errors() {
		global $wp_filter;

		// follow WP core behavior for showing deprecated notices and only do so when WP_DEBUG is on
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && apply_filters( 'sv_wc_plugin_framework_show_deprecated_hook_notices', true ) ) {

			// sanity check
			if ( ! is_array( $wp_filter ) || empty( $wp_filter ) ) {
				return;
			}

			foreach ( $this->hooks as $old_hook_tag => $hook ) {

				// if other actors have attached a callback to the deprecated/removed hook...
				if ( isset( $wp_filter[ $old_hook_tag ] ) && ( ! isset( $hook['trigger_notice'] ) || $hook['trigger_notice'] ) ) {
					$this->trigger_error( $old_hook_tag, $hook );
				}
			}
		}
	}


}
