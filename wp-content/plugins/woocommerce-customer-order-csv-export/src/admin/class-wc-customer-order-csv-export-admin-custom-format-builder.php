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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\CSV_Export\Export_Formats;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Admin Column Mapper Class
 *
 * Dedicated class for admin column mapping settings
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_Admin_Custom_Format_Builder {


	/**
	 * Setup admin custom format mapper class
	 *
	 * @since 4.0.0
	 */
	public function __construct() {

		// Render a custom field mapper form control when using woocommerce_admin_fields()
		add_action( 'woocommerce_admin_field_wc_customer_order_export_field_mapping', [ $this, 'render_field_mapping' ] );
	}


	/**
	 * Returns settings array for use by output/save functions
	 *
	 * @since 4.0.0
	 *
	 * @param Export_Formats\Export_Format_Definition $format_definition format definition
	 * @return array
	 */
	public function get_settings( Export_Formats\Export_Format_Definition $format_definition ) {

		$export_type = $format_definition->get_export_type();
		$output_type = $format_definition->get_output_type();

		// the base settings for all formats
		$settings = [
			[
				'name' => __( 'Format Options', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			],

			[
				'id'                => 'name',
				'name'              => __( 'Name', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'text',
				'default'           => $format_definition->get_name(),
				'class'             => 'js-name',
				'custom_attributes' => [
					'required' => '',
				],
			],
		];

		// add the export type's settings if they exist
		$settings_method = "get_{$export_type}_settings";

		if ( is_callable( [ __CLASS__, $settings_method ] ) ) {

			$type_settings = self::$settings_method( $format_definition );

		} else {

			$type_settings = [];
		}

		/**
		 * Filters the orders {type} custom export format settings.
		 *
		 * @since 5.0.0
		 *
		 * @param array $settings settings
		 * @param Export_Formats\Export_Format_Definition $format_definition format definition
		 */
		$type_settings = (array) apply_filters( "wc_customer_order_export_orders_{$output_type}_custom_format_settings", $type_settings, $format_definition );

		/**
		 * Filters the orders custom export format settings.
		 *
		 * @since 5.0.0
		 *
		 * @param array $settings settings
		 * @param string $output_type export output type, like csv
		 * @param Export_Formats\Export_Format_Definition $format_definition format definition
		 */
		$type_settings = (array) apply_filters( 'wc_customer_order_export_orders_custom_format_settings', $type_settings, $output_type, $format_definition );

		$settings = array_merge( $settings, $type_settings );

		if ( \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $output_type ) {

			$settings[] = [
				'id'      => 'delimiter',
				'name'    => __( 'CSV delimiter', 'woocommerce-customer-order-csv-export' ),
				'type'    => 'select',
				'options' => wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_delimiter_options(),
				'default' => $format_definition instanceof Export_Formats\CSV\CSV_Export_Format_Definition ? $format_definition->get_delimiter() : ',',
				'class'   => 'wc-enhanced-select js-delimiter',
			];

		} elseif ( \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML === $output_type ) {

			$settings[] = [
				'id'      => 'indent',
				'name'    => __( 'Indent output', 'woocommerce-customer-order-csv-export-suite' ),
				'desc'    => __( 'Enable to indent (pretty-print) XML output', 'woocommerce-customer-order-csv-export-suite' ),
				'default' => $format_definition instanceof Export_Formats\XML\XML_Export_Format_Definition && $format_definition->get_indent() ? 'yes' : 'no',
				'type'    => 'checkbox',
				'class'   => 'js-indent',
			];
		}

		// add the remaining global settings
		$settings = array_merge( $settings, [

			[
				'id'      => 'include_all_meta',
				'name'    => __( 'Include all meta', 'woocommerce-customer-order-csv-export' ),
				'desc'    => __( 'Enable to include all meta in the export', 'woocommerce-customer-order-csv-export' ),
				'default' => $format_definition->get_include_all_meta() ? 'yes' : 'no',
				'type'    => 'checkbox',
				'class'   => 'js-include-all-meta',
			],

			[ 'type' => 'sectionend' ],

			[
				'name' => \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $output_type ? __( 'Column Mapping', 'woocommerce-customer-order-csv-export' ) : __( 'Field Mapping', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			],

			[
				'id'           => 'mapping',
				'type'         => 'wc_customer_order_export_field_mapping',
				'export_type'  => $export_type,
				'output_type'  => $output_type,
				'default'      => $format_definition->get_mapping(),
				'data_sources' => $format_definition::get_data_sources(),
			],


			[ 'type' => 'sectionend' ],
		] );

		/**
		 * Allow actors to add or remove settings from the CSV export field mapping settings page.
		 *
		 * @since 4.0.0
		 *
		 * @param array $settings an array of settings for the given section
		 * @param string $export_type current export type
		 * @param Export_Formats\Export_Format_Definition $format_definition format definition
		 */
		return apply_filters( 'wc_customer_order_export_custom_format_settings', $settings, $export_type, $format_definition );
	}


	/**
	 * Gets the settings for Orders formats.
	 *
	 * @since 5.0.0
	 *
	 * @param Export_Formats\Export_Format_Definition $format_definition format definition
	 * @return array
	 */
	public function get_orders_settings( Export_Formats\Export_Format_Definition $format_definition ) {

		$settings = [];

		if ( \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $format_definition->get_output_type() ) {

			$settings = [

				[
					'id'       => 'row_type',
					'name'     => __( 'A row represents', 'woocommerce-customer-order-csv-export' ),
					'desc_tip' => __( 'Choose whether a single row in CSV should represent a full, single order or a single line item for an order.', 'woocommerce-customer-order-csv-export' ),
					'type'     => 'select',
					'options'  => wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_row_type_options(),
					'default'  => $format_definition instanceof Export_Formats\CSV\Orders_Export_Format_Definition ? $format_definition->get_row_type() : 'order',
					'class'    => 'wc-enhanced-select js-row-type',
				],

				[
					'id'       => 'items_format',
					'name'     => __( 'Items columns should use', 'woocommerce-customer-order-csv-export' ),
					'desc_tip' => __( 'Choose whether items columns (line items, shipping items, fee items, etc) should be pipe-delimited or JSON-formatted.', 'woocommerce-customer-order-csv-export' ),
					'type'     => 'select',
					'options'  => wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_items_format_options(),
					'default'  => $format_definition instanceof Export_Formats\CSV\Orders_Export_Format_Definition ? $format_definition->get_items_format() : 'pipe_delimited',
					'class'    => 'wc-enhanced-select js-show-if-single-order-format js-items-format',
				],
			];
		}

		return $settings;
	}


	/**
	 * Output field mapper
	 *
	 * @since 4.0.0
	 * @param array $options
	 */
	public function render_field_mapping( $options ) {

		$id           = $options['id'];
		$export_type  = $options['export_type'];
		$output_type  = $options['output_type'];
		$data_sources = $options['data_sources'];
		$mapping      = $options['default'];

		/**
		 * Filters the known data sources for the given format.
		 *
		 * @since 5.0.0
		 *
		 * @param array $sources data sources
		 * @param string $export_type such as orders or customers
		 */
		$data_sources = apply_filters( "wc_customer_order_export_{$output_type}_format_data_sources", $data_sources, $export_type );

		/**
		 * Filters the data sources available for an export format.
		 *
		 * @since 5.0.0
		 *
		 * @param array $data_sources the available data sources
		 */
		$data_options = apply_filters( 'wc_customer_order_export_format_data_sources', $data_sources, $export_type, $output_type );

		$headers = $this->get_mapping_headers( $options );

		$mapping = array_merge( $mapping, [
			'__INDEX__' => [
				'name'     => '',
				'source'   => '',
				'meta_key' => '',
			],
		] );

		include_once( wc_customer_order_csv_export()->get_plugin_path() . '/src/admin/views/html-custom-format-mapping.php' );
	}


	/**
	 * Gets the mapping table headers.
	 *
	 * @since 5.0.0
	 *
	 * @param array $options setting options
	 * @return array
	 */
	private function get_mapping_headers( $options ) {

		$name = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $options['output_type'] ? esc_html__( 'Column name', 'woocommerce-customer-order-csv-export' ) : esc_html__( 'Field name', 'woocommerce-customer-order-csv-export' );

		$headers = (array) apply_filters( 'wc_customer_order_export_field_mapping_fields', [
			'sort'            => '',
			'sv-check-column' => '<input type="checkbox" class="js-select-all" />', // this can be anything but `check-column` due to https://core.trac.wordpress.org/changeset/38703
			'name'            => $name,
			'source'          => esc_html__( 'Data source', 'woocommerce-customer-order-csv-export' ),
		], $options );

		return $headers;
	}


	/**
	 * Output the export format definitions as JSON for the given export type
	 *
	 * @since 4.0.0
	 *
	 * @param string $export_type export type, like orders or customers
	 * @param string $output_type output type, like csv or xml
	 */
	public function output_formats_json( $export_type, $output_type ) {

		$formats = wc_customer_order_csv_export()->get_formats_instance()->get_formats( $export_type, $output_type );

		if ( empty( $formats ) ) {
			return;
		}

		wc_enqueue_js( 'wc_customer_order_csv_export_admin.export_formats = ' . json_encode( $formats ) . ';' );
	}


	/**
	 * Show column mapping page
	 *
	 * @since 4.0.0
	 *
	 * @param string $export_type export type, like orders or customers
	 * @param string $format_key custom format key, if editing an existing format
	 */
	public function output( $export_type, $format_key = '' ) {
		global $output_type;

		// save custom format
		if ( ! empty( $_POST ) ) {
			$this->save( $export_type, $format_key );
		}

		$format_definition = null;

		if ( $format_key ) {
			$format_definition = wc_customer_order_csv_export()->get_formats_instance()->get_custom_format_definition( $export_type, $format_key );
		}

		if ( ! $format_definition ) {

			$output_types = wc_customer_order_csv_export()->get_output_types();
			$output_type  = ! empty( $_GET['output_type'] ) && isset( $output_types[ $_GET['output_type'] ] ) ? $_GET['output_type'] : current( array_keys( $output_types ) );

			$export_types = wc_customer_order_csv_export()->get_export_types();
			$export_type  = isset( $export_types[ $export_type ] ) ? $export_type : current( array_keys( $export_types ) );

			if ( $class = wc_customer_order_csv_export()->get_formats_instance()->get_format_definition_class( $output_type, $export_type ) ) {
				$format_definition = new $class( [] );
			} else {
				return;
			}

			// show load mapping modal automatically
			wc_enqueue_js( 'wc_customer_order_csv_export_admin.display_load_mapping_modal = true;' );
		}

		$output_type = $format_definition->get_output_type();

		// render settings fields
		woocommerce_admin_fields( $this->get_settings( $format_definition ) );

		echo '<input type="hidden" name="output_type" value="' . esc_attr( $format_definition->get_output_type() ) . '" />';

		// output JSON settings for formats (used for loading column mapping from existing formats)
		$this->output_formats_json( $format_definition->get_export_type(), $format_definition->get_output_type() );

		wp_nonce_field( __FILE__ );

		submit_button( __( 'Save', 'woocommerce-customer-order-csv-export' ) );
	}


	/**
	 * Save column mapping
	 *
	 * @since 4.0.0
	 *
	 * @param string $export_type export type, like orders or customers
	 * @param string $format_key existing custom format key, if any
	 */
	public function save( $export_type, $format_key = '' ) {

		try {

			// security check
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Please refresh the page and retry.', 'woocommerce-customer-order-csv-export' ) );
			}

			$output_types = wc_customer_order_csv_export()->get_output_types();

			if ( empty( $_POST['output_type'] ) || ! isset( $output_types[ $_POST['output_type'] ] ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid output type.' );
			}

			$class = wc_customer_order_csv_export()->get_formats_instance()->get_format_definition_class( $_POST['output_type'], $export_type );

			if ( ! $class ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid data.', 'woocommerce-customer-order-csv-export' ) );
			}

			$format_definition = new $class( [
				'key' => $format_key,
			] );

			$format_definition = $this->save_format( $format_definition );

			$message = __( 'Your custom format has been saved. ', 'woocommerce-customer-order-csv-export' );

			// add a link to the custom formats list to the message
			$url = wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_custom_formats_table_url();

			/* translators: %1$s - opening <a> tag, %2$s - closing </a> tag */
			$message .= sprintf( esc_html__( 'Return to the %1$scustom formats list%2$s.', 'woocommerce-customer-order-csv-export' ), '<a href="' . esc_url( $url ) . '">', '</a>' );

			wc_customer_order_csv_export()->get_message_handler()->add_message( $message );

			// if adding, redirect to the edit page for the newly created format
			if ( $format_definition->get_key() ) {

				$edit_url = add_query_arg( [
					'format_action'     => \SkyVerge\WooCommerce\CSV_Export\Admin\Admin_Custom_Formats::ACTION_EDIT,
					'format_key'        => $format_definition->get_key(),
				], remove_query_arg( [
						'format_action'
					] )
				);

				wp_safe_redirect( $edit_url );
				exit;

			} else {

				// show message if not redirecting
				wc_customer_order_csv_export()->get_message_handler()->show_messages( [
					'capabilities' => [
						'manage_woocommerce_csv_exports',
					],
				] );
			}

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$message = sprintf( __( 'Format could not be saved. %s', 'woocommerce-customer-order-csv-export' ), $exception->getMessage() );

			wc_customer_order_csv_export()->get_message_handler()->add_error( $message );

			$url = add_query_arg( [
				'page'    => 'wc_customer_order_csv_export',
				'tab'     => 'custom_formats',
				'section' => $export_type,
			], admin_url( 'admin.php' ) );

			wp_safe_redirect( $url );
			exit;
		}
	}


	/**
	 * Saves current custom format.
	 *
	 * @see WC_Admin_Settings::save_fields()
	 *
	 * @since 4.7.0
	 *
	 * @param Export_Formats\Export_Format_Definition $format_definition format definition object
	 * @return Export_Formats\Export_Format_Definition
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	private function save_format( Export_Formats\Export_Format_Definition $format_definition ) {

		$data = $_POST;

		if ( empty( $data ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'No data to save.', 'woocommerce-customer-order-csv-export' ) );
		}

		// parse settings into args
		$format_args = [];

		foreach ( $this->get_settings( $format_definition ) as $setting ) {

			if ( empty( $setting['id'] ) ) {
				continue;
			}

			$raw_value = isset( $data[ $setting['id'] ] ) ? $data[ $setting['id'] ] : null;

			// format the value based on type
			switch ( $setting['type'] ) {

				case 'checkbox':
					$value = ( '1' === $raw_value || 'yes' === $raw_value );
				break;

				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
				break;

				case 'multiselect':
				case 'multi_select_countries':
					$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
				break;

				case 'image_width':

					$value = [];

					if ( isset( $raw_value['width'] ) ) {
						$value['width']  = wc_clean( $raw_value['width'] );
						$value['height'] = wc_clean( $raw_value['height'] );
						$value['crop']   = isset( $raw_value['crop'] ) ? 1 : 0;
					} else {
						$value['width']  = $setting['default']['width'];
						$value['height'] = $setting['default']['height'];
						$value['crop']   = $setting['default']['crop'];
					}

				break;

				case 'select':

					$allowed_values = empty( $setting['options'] ) ? [] : array_map( 'strval', array_keys( $setting['options'] ) );

					if ( empty( $setting['default'] ) && empty( $allowed_values ) ) {
						$value = null;
						break;
					}
					$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
					$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;

				break;

				case 'relative_date_selector':
					$value = wc_parse_relative_date_option( $raw_value );
				break;

				default:
					$value = wc_clean( $raw_value );
				break;
			}

			if ( null === $value ) {
				continue;
			}

			$format_args[ $setting['id'] ] = $value;
		}

		if ( $format_definition->get_key() ) {
			$format_args['key'] = $format_definition->get_key();
		} else {
			$format_args['key'] = $format_definition::generate_unique_format_key( $format_definition->get_export_type(), $format_args['name'] );
		}

		$definition_class = get_class( $format_definition );

		/** @var Export_Formats\Export_Format_Definition $format_definition */
		$format_definition = new $definition_class( $format_args );

		wc_customer_order_csv_export()->get_formats_instance()->save_custom_format( $format_definition->get_export_type(), $format_definition );

		return $format_definition;
	}


}
