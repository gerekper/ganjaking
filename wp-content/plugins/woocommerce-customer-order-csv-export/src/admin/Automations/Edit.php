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

namespace SkyVerge\WooCommerce\CSV_Export\Admin\Automations;

use SkyVerge\WooCommerce\CSV_Export\Admin\Automations;
use SkyVerge\WooCommerce\CSV_Export\Admin\Export_Formats_Helper;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The automations edit screen handler.
 *
 * @since 5.0.0
 */
class Edit {


	/**
	 * Constructs the edit screen.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {

		add_action( 'woocommerce_admin_field_wc_customer_order_export_test_button', [ $this, 'render_test_button' ] );
	}


	/**
	 * Gets the automation settings.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return array
	 */
	public function get_settings( Automation $automation ) {

		$output_types = wc_customer_order_csv_export()->get_output_types();
		$export_types = wc_customer_order_csv_export()->get_export_types();

		$automation_output_type = $automation->get_output_type( 'edit' );
		$automation_export_type = $automation->get_export_type( 'edit' );

		// coupons don't currently support automations
		unset( $export_types[ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS ] );

		$settings = [

			[
				'name' => $automation->get_id() ? __( 'Edit automated export', 'woocommerce-customer-order-csv-export' ) : __( 'New automated export', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			],

			[
				'id'      => 'enabled',
				'type'    => 'checkbox',
				'name'    => __( 'Enabled', 'woocommerce-customer-order-csv-export' ),
				'default' => 'yes',
				'value'   => $automation->is_enabled( 'edit' ) ? 'yes' : 'no',
			],

			[
				'id'       => 'name',
				'type'     => 'text',
				'name'     => __( 'Export name', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'An internal name for this automated export.', 'woocommerce-customer-order-csv-export' ),
				'value'    => $automation->get_name( 'edit' ),
				'custom_attributes' => [
					'required'  => 'required',
					'maxlength' => 25,
				],
				'class' => 'js-automation-name',
			],

			[
				'id'       => 'export_type',
				'type'     => 'radio',
				'name'     => __( 'Export type', 'woocommerce-customer-order-csv-export' ),
				'options'  => $export_types,
				'default'  => current( array_keys( $export_types ) ),
				'value'    => $automation_export_type ?: null,
				'class'    => 'js-automation-export-type',
				'desc_tip' => $automation->get_id() ? __( 'The export type cannot be changed for existing automations.', 'woocommerce-customer-order-csv-export' ) : '',
			],

			[
				'id'       => 'output_type',
				'type'     => 'radio',
				'name'     => __( 'Output type', 'woocommerce-customer-order-csv-export' ),
				'options'  => $output_types,
				'default'  => current( array_keys( $output_types ) ),
				'value'    => $automation_output_type ?: null,
				'class'    => 'js-automation-output-type',
				'desc_tip' => $automation->get_id() ? __( 'The output cannot be changed for existing automations.', 'woocommerce-customer-order-csv-export' ) : '',
			],
		];

		// disable the output/export type inputs for existing automations
		foreach ( $settings as $key => $setting ) {

			if ( isset( $setting['id'] ) && in_array( $setting['id'], [ 'output_type', 'export_type' ] ) && $automation->get_id() ) {
				$settings[ $key ]['custom_attributes'] = [ 'disabled' => true ];
			}
		}

		$use_legacy_formats = 'yes' === get_option( 'wc_customer_order_export_keep_legacy_formats' );

		// display filename and format fields for each output & export type combo
		// their visibility will be toggled by JS
		foreach ( $output_types as $output_type => $output_type_label ) {

			foreach ( $export_types as $export_type => $export_type_label ) {

				// only define input values if this looped output/export type combo matches the automation being displayed
				$matches_automation = $output_type === $automation_output_type && $export_type === $automation_export_type;

				$settings[] = [
					'id'      => "{$output_type}_{$export_type}_filename",
					'type'    => 'text',
					'name'    => __( 'Filename', 'woocommerce-customer-order-csv-export' ),
					'default' => "{$export_type}-%%name%%-export-%%timestamp%%.{$output_type}",
					'value'   => $matches_automation ? $automation->get_filename( 'edit' ) : null,
					'css'     => 'min-width: 300px;',
					'class'   => "js-output-type-field js-export-type-field show_if_{$output_type} show_if_{$export_type}",
					'custom_attributes' => [
						'required' => 'required',
					],
				];

				$formats = Export_Formats_Helper::get_export_formats( $output_type, $export_type, $use_legacy_formats );

				$settings[] = [
					'id'      => "{$output_type}_{$export_type}_format",
					'type'    => 'select_with_optgroup',
					'name'    => __( 'Format', 'woocommerce-customer-order-csv-export' ),
					'options' => $formats,
					'default' => $matches_automation && $automation->get_format() ? sanitize_title( $automation->get_format()->get_key() ) : current( array_keys( $formats ) ),
					'class'   => "js-output-type-field js-export-type-field show_if_{$output_type} show_if_{$export_type}",
				];
			}
		}

		$settings[] = [ 'type' => 'sectionend' ];

		$settings[] = [
			'name' => __( 'Schedule options', 'woocommerce-customer-order-csv-export' ),
			'type' => 'title',
		];

		$settings[] = [
			'id'       => 'automation_action', // cannot be 'action', which conflicts with WP
			'name'     => __( 'Trigger automated export', 'woocommerce-customer-order-csv-export' ),
			'desc_tip' => __( "Choose whether to auto-export orders on a schedule or immediately when they're paid for.", 'woocommerce-customer-order-csv-export' ),
			'type'     => 'select',
			'options'  => [
				'interval'  => __( 'on scheduled intervals', 'woocommerce-customer-order-csv-export' ),
				'immediate' => __( 'immediately as orders are paid', 'woocommerce-customer-order-csv-export' ),
			],
			'value'   => $automation->get_action( 'edit' ),
			'default' => 'interval',
			'class'   => 'js-auto-export-trigger js-export-type-field show_if_orders',
		];

		$scheduled_descriptions = [
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS    => '',
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS => '',
		];

		foreach ( array_keys( $scheduled_descriptions ) as $export_type ) {

			// get the scheduled export time to display to user
			if ( $automation->get_next_run() && ( $scheduled_timestamp = $automation->get_next_run( 'edit' )->getTimestamp() ) ) {
				/* translators: Placeholders: %s - date */
				$scheduled_descriptions[ $export_type ] = sprintf( __( 'The next export is scheduled on %s', 'woocommerce-customer-order-csv-export' ), '<code>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $scheduled_timestamp ), wc_date_format() . ' ' . wc_time_format() ) . '</code>' );
			} else {
				$scheduled_descriptions[ $export_type ] = __( 'The export is not scheduled.', 'woocommerce-customer-order-csv-export' );
			}
		}

		$settings[] = [
			'id'       => 'start_time',
			'name'     => __( 'Start time', 'woocommerce-customer-order-csv-export' ),
			'desc_tip' => sprintf(
				/* translators: Placeholders: %s - export type, such as orders or customers */
				__( 'Any new %s will start exporting at this time.', 'woocommerce-customer-order-csv-export' ),
				$automation_export_type ?: __( 'items', 'woocommerce-customer-order-csv-export' )
			),
			/* translators: Placeholders: %s - time */
			'desc'     => sprintf(
				              __( 'Local time is %s.', 'woocommerce-customer-order-csv-export' ),
				              '<code>' . date_i18n( wc_time_format() ) . '</code>' ) . ' ' . ( ! empty( $scheduled_descriptions[ $automation_export_type ] ) ? $scheduled_descriptions[ $automation_export_type ] : '' ),
			'default'  => '',
			'value'    => $automation->get_start() ? $automation->get_start()->format( 'g:ia' ) : null,
			'type'     => 'text',
			'css'      => 'max-width: 100px;',
			'class'    => 'js-auto-export-timepicker js-auto-export-schedule-field',
		];

		$interval = (int) $automation->get_interval( 'edit' );

		$settings[] = [
			'id'       => 'interval',
			'name'     => __( 'Export interval (in minutes)*', 'woocommerce-customer-order-csv-export' ),
			'desc_tip' => sprintf(
			/* translators: Placeholders: %s - export type, such as orders or customers */
				__( 'Any new %s will be exported on this schedule.', 'woocommerce-customer-order-csv-export' ),
				$automation_export_type ?: __( 'items', 'woocommerce-customer-order-csv-export' )
			),
			'desc'     => __( 'Required in order to schedule the automatic export.', 'woocommerce-customer-order-csv-export' ),
			'default'  => '30',
			'value'    => $interval ? $interval / MINUTE_IN_SECONDS : null,
			'type'     => 'text',
			'css'      => 'max-width: 50px;',
			'class'    => 'js-auto-export-schedule-field',
		];

		$settings[] = [
			'id'       => 'method_type',
			'name'     => __( 'Method', 'woocommerce-customer-order-csv-export' ),
			'desc_tip' => __( 'Automatically export orders via the method selected.', 'woocommerce-customer-order-csv-export' ),
			'type'     => 'select',
			'options'  => wc_customer_order_csv_export()->get_methods_instance()->get_export_method_labels(),
			'value'    => $automation->get_method_type( 'edit' ),
			'default'  => 'disabled',
			'class'    => 'js-auto-export-method',
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			'desc'     => sprintf( __( 'Local exports are generated, then saved to the %1$sExport List%2$s for 14 days.', 'woocommerce-customer-order-csv-export' ), '<a href="' . admin_url( 'admin.php?page=wc_customer_order_csv_export&tab=export_list' ) . '">', '</a>' ),
		];

		$settings[] = [ 'type' => 'sectionend' ];

		// add the FTP settings
		$settings = array_merge( $settings, $this->get_ftp_settings( $automation ) );

		// add the HTTP POST settings
		$settings = array_merge( $settings, $this->get_http_post_settings( $automation ) );

		// add the email settings
		$settings = array_merge( $settings, $this->get_email_settings( $automation ) );

		$product_terms      = get_terms( 'product_cat' );
		$product_categories = [];

		// sanity check: get_terms() may return a WP_Error
		if ( is_array( $product_terms ) && ! empty( $product_terms ) ) {

			foreach ( $product_terms as $term ) {
				$product_categories[ $term->term_id ] = $term->name;
			}
		}

		$settings = array_merge( $settings, [

			[
				'name' => __( 'Export options', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			],

			[
				'id'                => 'statuses',
				'name'              => __( 'Order statuses', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with these statuses will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'options'           => wc_get_order_statuses(),
				'default'           => '',
				'value'             => $automation->get_statuses( 'edit' ),
				'class'             => 'wc-enhanced-select js-export-type-field show_if_orders js-action-field show_if_interval',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export orders with any status.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			[
				'id'                => 'product_category_ids',
				'name'              => __( 'Product categories', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with products in these categories will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'options'           => $product_categories,
				'default'           => [],
				'value'             => array_map( 'strval', $automation->get_product_category_ids( 'edit' ) ),
				'class'             => 'wc-enhanced-select js-export-type-field show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export orders with products in any category.', 'woocommerce-customer-order-csv-export' ),
				],
			],

			[
				'id'                => 'product_ids',
				'name'              => __( 'Products', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with these products will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'csv_product_search',
				'default'           => [],
				'value'             => $automation->get_product_ids( 'edit' ),
				'class'             => 'wc-product-search js-export-type-field show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => [
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Leave blank to export orders with any products.', 'woocommerce-customer-order-csv-export' ),
				],
			],

		] );

		$settings[] = [
			'id'      => 'add_notes',
			'name'    => __( 'Add order notes', 'woocommerce-customer-order-csv-export' ),
			'desc'    => __( 'Enable to add a note to exported orders.', 'woocommerce-customer-order-csv-export' ),
			'default' => 'yes',
			'value'   => $automation->is_note_enabled( 'edit' ) ? 'yes' : 'no',
			'type'    => 'checkbox',
			'class'   => 'js-export-type-field show_if_orders',
		];

		$settings[] = [ 'type' => 'sectionend' ];

		/**
		 * Filters the admin automation settings.
		 *
		 * @since 5.0.0
		 *
		 * @param array $settings settings definitions
		 * @param Automation $automation automation object
		 */
		return apply_filters( 'wc_customer_order_export_admin_automation_settings', $settings, $automation );
	}


	/**
	 * Gets the FTP settings fields.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 *
	 * @return array
	 */
	public function get_ftp_settings( Automation $automation ) {

		$values = wp_parse_args( $automation->get_method_settings( 'edit' ), [
			'ftp_server'       => null,
			'ftp_username'     => null,
			'ftp_password'     => null,
			'ftp_port'         => null,
			'ftp_path'         => null,
			'ftp_security'     => null,
			'ftp_passive_mode' => null,
		] );

		return [
			[
				'id'   => 'ftp_settings',
				'name' => __( 'FTP settings', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title'
			],

			[
				'id'       => 'ftp_server',
				'name'     => __( 'Server address', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'The address of the remote FTP server to upload to.', 'woocommerce-customer-order-csv-export' ),
				'value'    => $values['ftp_server'],
				'default'  => '',
				'type'     => 'text',
				'class'    => 'js-auto-export-ftp-field',
			],

			[
				'id'       => 'ftp_username',
				'name'     => __( 'Username', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'The username for the remote FTP server.', 'woocommerce-customer-order-csv-export' ),
				'value'    => $values['ftp_username'],
				'default'  => '',
				'type'     => 'text',
				'class'    => 'js-auto-export-ftp-field',
			],

			[
				'id'       => 'ftp_password',
				'name'     => __( 'Password', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'The password for the remote FTP server.', 'woocommerce-customer-order-csv-export' ),
				'value'    => $values['ftp_password'],
				'default'  => '',
				'type'     => 'password',
				'class'    => 'js-auto-export-ftp-field',
			],

			[
				'id'                => 'ftp_port',
				'name'              => __( 'Port', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'The port for the remote FTP server.', 'woocommerce-customer-order-csv-export' ),
				'value'             => $values['ftp_port'],
				'default'           => '21',
				'type'              => 'number',
				'class'             => 'js-auto-export-ftp-field js-auto-export-ftp-port',
				'style'             => 'max-width: 50px;',
				'custom_attributes' => [ 'min' => 0, 'step' => 1 ],
			],

			[
				'id'       => 'ftp_path',
				'name'     => __( 'Initial path', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'The initial path for the remote FTP server with trailing slash, but excluding leading slash.', 'woocommerce-customer-order-csv-export' ),
				'value'    => $values['ftp_path'],
				'default'  => '',
				'type'     => 'text',
				'class'    => 'js-auto-export-ftp-field',
			],

			[
				'id'       => 'ftp_security',
				'name'     => __( 'Security', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'Select the security type for the remote FTP server.', 'woocommerce-customer-order-csv-export' ),
				'value'    => $values['ftp_security'],
				'default'  => 'none',
				'options'  => [
					'none'    => __( 'None', 'woocommerce-customer-order-csv-export' ),
					'ftp_ssl' => __( 'FTP with Implicit SSL', 'woocommerce-customer-order-csv-export' ),
					'ftps'    => __( 'FTP with Explicit TLS/SSL', 'woocommerce-customer-order-csv-export' ),
					'sftp'    => __( 'SFTP (FTP over SSH)', 'woocommerce-customer-order-csv-export' )
				],
				'type'     => 'select',
				'class'    => 'js-auto-export-ftp-field js-auto-export-ftp-security',
			],

			[
				'id'      => 'ftp_passive_mode',
				'name'    => __( 'Passive mode', 'woocommerce-customer-order-csv-export' ),
				'desc'    => __( 'Enable passive mode if you are having issues connecting to FTP, especially if you see "PORT command successful" in the error log.', 'woocommerce-customer-order-csv-export' ),
				'value'   => $values['ftp_passive_mode'] ? 'yes' : 'no',
				'default' => 'no',
				'type'    => 'checkbox',
				'class'   => 'js-auto-export-ftp-field',
			],

			[
				'id'            => 'ftp_test_button',
				'name'          => __( 'Test FTP', 'woocommerce-customer-order-csv-export' ),
				'method'        => 'ftp',
				'is_configured' => ! empty( $values['ftp_server'] ) && ! empty( $values['ftp_username'] ) && ! empty( $values['ftp_password'] ),
				'type'          => 'wc_customer_order_export_test_button',
				'export_type'   => \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
				'class'         => 'js-auto-export-ftp-field js-auto-export-test-button',
			],

			[ 'type' => 'sectionend' ],

		];
	}


	/**
	 * Gets the HTTP POST settings fields.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 *
	 * @return array
	 */
	public function get_http_post_settings( Automation $automation ) {

		$values = wp_parse_args( $automation->get_method_settings( 'edit' ), [
			'http_post_url' => null,
		] );

		return [
			[
				'id'   => 'http_post_settings',
				'name' => __( 'HTTP POST settings', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title'
			],

			[
				'id'       => 'http_post_url',
				'name'     => __( 'HTTP POST URL', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'Enter the URL to POST the exported file to.', 'woocommerce-customer-order-csv-export' ),
				'default'  => '',
				'value'    => $values['http_post_url'],
				'type'     => 'text',
				'class'    => 'js-auto-export-http-post-field',
			],

			[
				'id'            => 'http_post_test_button',
				'name'          => __( 'Test HTTP POST', 'woocommerce-customer-order-csv-export' ),
				'method'        => 'http_post',
				'is_configured' => ! empty( $values['http_post_url'] ),
				'type'          => 'wc_customer_order_export_test_button',
				'class'         => 'js-auto-export-http-post-field js-auto-export-test-button',
			],

			[ 'type' => 'sectionend' ],

		];
	}


	/**
	 * Gets the email settings fields.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 *
	 * @return array
	 */
	public function get_email_settings( Automation $automation ) {

		$values = wp_parse_args( $automation->get_method_settings( 'edit' ), [
			'email_recipients' => null,
			'email_subject'    => null,
		] );

		return [
			[
				'id'   => 'email_settings',
				'name' => __( 'Email settings', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title'
			],

			[
				'id'       => 'email_recipients',
				'name'     => __( 'Recipient(s)', 'woocommerce-customer-order-csv-export' ),
				/* translators: Placeholders: %s - email address */
				'desc_tip' => sprintf( __( 'Enter recipients (comma separated) the exported file should be emailed to. Defaults to %s.', 'woocommerce-customer-order-csv-export' ), '<em>' . esc_html( get_option( 'admin_email' ) ) . '</em>' ),
				'default'  => '',
				'value'    => $values['email_recipients'],
				'type'     => 'text',
				'class'    => 'js-auto-export-email-field',
			],

			[
				'id'       => 'email_subject',
				'name'     => __( 'Email subject', 'woocommerce-customer-order-csv-export' ),
				'desc_tip' => __( 'Enter the email subject.', 'woocommerce-customer-order-csv-export' ),
				'default'  => sprintf(
					/* translators: Placeholders: %1$s - blog name */
					__( '[%1$s] Export', 'woocommerce-customer-order-csv-export' ),
					get_option( 'blogname' )
				),
				'value'    => $values['email_subject'],
				'type'     => 'text',
				'class'    => 'js-auto-export-email-field',
			],

			[
				'id'          => 'email_test_button',
				'name'        => __( 'Test Email', 'woocommerce-customer-order-csv-export' ),
				'method'      => 'email',
				'type'        => 'wc_customer_order_export_test_button',
				'class'       => 'js-auto-export-email-field js-auto-export-test-button',
			],

			[ 'type' => 'sectionend' ],

		];
	}


	/**
	 * Outputs the automation edit screen.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id existing automation ID, if any
	 */
	public function output( $automation_id ) {

		$automation = Automation_Factory::get_automation( $automation_id );

		if ( ! $automation ) {
			$automation = new Automation();
		}

		if ( ! empty( $_POST ) ) {

			// perform a test if that's what the submit was
			if ( $automation->get_id() && $method = Framework\SV_WC_Helper::get_posted_value( 'wc_customer_order_export_test_method' ) ) {

				// process test
				$result = wc_customer_order_csv_export()->get_export_handler_instance()->test_export_via( $method, $automation->get_export_type( 'edit' ), $automation->get_output_type( 'edit' ), $automation->get_method_settings( 'edit' ) );

				if ( 'error' === $result[1] ) {
					wc_customer_order_csv_export()->get_message_handler()->add_error( $result[0] );
				} else {
					wc_customer_order_csv_export()->get_message_handler()->add_message( $result[0] );
				}

				wp_safe_redirect( Automations::get_automation_edit_url( $automation->get_id() ) );
				exit;
			}

			$this->save( $automation );
		}

		echo '<div class="edit-automation-form">';

		woocommerce_admin_fields( $this->get_settings( $automation ) );

		wp_nonce_field( __FILE__ );

		submit_button( __( 'Save', 'woocommerce-customer-order-csv-export' ) );

		echo '</div>';
	}


	/**
	 * Saves the current automation edit screen.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation existing automation
	 */
	public function save( Automation $automation ) {

		try {

			// security check
			if ( ! wp_verify_nonce( Framework\SV_WC_Helper::get_posted_value( '_wpnonce' ), __FILE__ ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Please refresh the page and retry.', 'woocommerce-customer-order-csv-export' ) );
			}

			$name = stripslashes( sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'name' ) ) );

			if ( ! $name || ! is_string( $name ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'A name is required.', 'woocommerce-customer-order-csv-export' ) );
			}

			if ( strlen( $name ) > 25 ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'The name must be 25 characters or fewer.', 'woocommerce-customer-order-csv-export' ) );
			}

			if ( 'Manual' === $name ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'The name cannot be "Manual".', 'woocommerce-customer-order-csv-export' ) );
			}

			$automation->set_enabled( ! empty( $_POST['enabled'] ) );
			$automation->set_name( $name );

			$output_type = Framework\SV_WC_Helper::get_posted_value( 'output_type' ) ?: $automation->get_output_type( 'edit' );
			$export_type = Framework\SV_WC_Helper::get_posted_value( 'export_type' ) ?: $automation->get_export_type( 'edit' );

			// only set these for new automations
			if ( ! $automation->get_id() ) {
				$automation->set_output_type( sanitize_text_field( $output_type ) );
				$automation->set_export_type( sanitize_text_field( $export_type ) );
			}

			if ( $filename = Framework\SV_WC_Helper::get_posted_value( "{$output_type}_{$export_type}_filename" ) ) {
				$automation->set_filename( stripslashes( sanitize_text_field( $filename ) ) );
			}

			if ( $format = Framework\SV_WC_Helper::get_posted_value( "{$output_type}_{$export_type}_format" ) ) {
				$automation->set_format_key( sanitize_text_field( $format ) );
			}

			// save the transfer method settings
			$this->save_method_settings( $automation );

			// save the start and interval settings
			$this->save_schedule_settings( $automation );

			// orders-only props
			if ( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) {

				$automation->set_action( Framework\SV_WC_Helper::get_posted_value( 'automation_action' ) ? sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'automation_action' ) ): 'interval' );

				$statuses = isset( $_POST['statuses'] ) && is_array( $_POST['statuses'] ) ? wc_clean( $_POST['statuses'] ) : [];

				$automation->set_statuses( array_filter( $statuses ) );

				$product_ids = isset( $_POST['product_ids'] ) && is_array( $_POST['product_ids'] ) ? array_map( 'absint', $_POST['product_ids'] ) : [];

				$automation->set_product_ids( array_filter( $product_ids ) );

				$product_category_ids = isset( $_POST['product_category_ids'] ) && is_array( $_POST['product_category_ids'] ) ? array_map( 'absint', $_POST['product_category_ids'] ) : [];

				$automation->set_product_category_ids( array_filter( $product_category_ids ) );

				$automation->set_add_notes( ! empty( $_POST['add_notes'] ) );
			}

			// sets orders format properly
			$automation->set_format_key( stripslashes( Framework\SV_WC_Helper::get_posted_value( $output_type . '_' . $export_type . '_format' ) ) );

			$automation->save();

			wc_customer_order_csv_export()->get_message_handler()->add_message( __( 'Automated export saved successfully.', 'woocommerce-customer-order-csv-export' ) );

			/**
			 * Fires after an automated export has been saved.
			 *
			 * @since 5.1.0
			 *
			 * @param Automation $automation automation object
			 */
			do_action( 'wc_customer_order_export_automated_export_saved', $automation );

			wp_safe_redirect( Automations::get_automation_edit_url( $automation->get_id() ) );
			exit;

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			wc_customer_order_csv_export()->get_message_handler()->add_error( sprintf(
				/* translators: Placeholders: %s - error message */
				__( 'Automated export could not be saved. %s', 'woocommerce-customer-order-csv-export' ),
				$exception->getMessage()
			) );

			wp_safe_redirect( Automations::get_automation_add_url() );
			exit;
		}
	}


	/**
	 * Saves an automation's method settings from post data.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 */
	private function save_method_settings( Automation $automation ) {

		$method_type = sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'method_type' ) );

		$automation->set_method_type( $method_type );

		switch ( $method_type ) {

			case 'ftp':
				$this->save_ftp_settings( $automation );
				break;

			case 'http_post':
				$this->save_http_post_settings( $automation );
				break;

			case 'email':
				$this->save_email_settings( $automation );
				break;
		}
	}


	/**
	 * Saves an automation's FTP settings, if any.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 */
	private function save_ftp_settings( Automation $automation ) {

		$settings = [
			'ftp_server'       => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'ftp_server' ) ),
			'ftp_username'     => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'ftp_username' ) ),
			'ftp_password'     => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'ftp_password' ) ),
			'ftp_port'         => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'ftp_port' ) ),
			'ftp_path'         => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'ftp_path' ) ),
			'ftp_security'     => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'ftp_security' ) ),
			'ftp_passive_mode' => (bool) Framework\SV_WC_Helper::get_posted_value( 'ftp_passive_mode' ),
		];

		$automation->set_method_settings( $settings );
	}


	/**
	 * Saves an automation's HTTP POST settings, if any.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 */
	private function save_http_post_settings( Automation $automation ) {

		$settings = [
			'http_post_url' => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'http_post_url' ) ),
		];

		$automation->set_method_settings( $settings );
	}


	/**
	 * Saves an automation's email settings, if any.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 */
	private function save_email_settings( Automation $automation ) {

		$settings = [
			'email_recipients' => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'email_recipients' ) ),
			'email_subject'    => sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'email_subject' ) ),
		];

		$automation->set_method_settings( $settings );
	}


	/**
	 * Saves an automation's schedule settings from post data.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 */
	private function save_schedule_settings( Automation $automation ) {

		$saved_interval   = $automation->get_interval( 'edit' );
		$saved_start_time = $automation->get_start() ? $automation->get_start()->format( 'g:ia' ) : '';

		if ( $interval = (int) Framework\SV_WC_Helper::get_posted_value( 'interval' ) ) {
			$automation->set_interval( $interval * MINUTE_IN_SECONDS );
		} else {
			$automation->set_interval( '' );
		}

		$start_time        = Framework\SV_WC_Helper::get_posted_value( 'start_time' );
		$existing_schedule = $saved_start_time . $saved_interval;

		// only set the automation's start (and schedule) if a time is set and it has changed
		if ( $start_time ) {

			if ( $interval && $start_time . ( $interval * MINUTE_IN_SECONDS ) !== $existing_schedule ) {

				$current_time = current_time( 'timestamp' );
				$start        = strtotime( "today {$start_time}", $current_time );

				// if today's time has already passed, start tomorrow
				if ( $current_time > $start ) {
					$start = strtotime( "tomorrow {$start_time}", $current_time );
				}

				$automation->set_start( $start - wc_timezone_offset() );
			}

		} else {

			$automation->set_start( '' );
		}
	}


	/**
	 * Renders a test button.
	 *
	 * @since 5.0.0
	 *
	 * @param array $field field parameters
	 */
	public function render_test_button( $field ) {

		$name           = $field['name'];
		$atts           = [ 'data-method' => $field['method'] ];
		$classes        = array_merge( [ 'secondary' ], explode( ' ', $field['class'] ) );
		$button_type    = implode( ' ', $classes );

		// disable text button and change name if required
		if ( isset( $field['is_configured'] ) && ! $field['is_configured'] ) {
			$name = __( 'Please save your settings before testing', 'woocommerce-customer-order-csv-export' );
			$atts['disabled'] = 'disabled';
		}

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">Test</th>
			<td class="forminp">
				<?php submit_button( $name, $button_type, $field['id'], true, $atts ); ?>
			</td>
		</tr>
		<?php
	}


}
