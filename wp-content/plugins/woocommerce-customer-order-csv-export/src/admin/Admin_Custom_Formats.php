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

use SkyVerge\WooCommerce\PluginFramework\v5_11_6\SV_WC_Helper;

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order CSV Export Admin Custom Formats
 *
 * Parent class for custom formats tab in admin page
 *
 * @since 4.7.0
 */
class Admin_Custom_Formats {


	/** @var string add new format action */
	const ACTION_ADD = 'add';

	/** @var string edit format action */
	const ACTION_EDIT = 'edit';

	/** @var string delete format action */
	const ACTION_DELETE = 'delete';


	/** @var string the export type, like orders or customers */
	private $export_type;

	/** @var string the custom format key, if any */
	private $format_key;

	/** @var string the current action, `list`, `add`, `edit` or `delete` */
	private $action;

	/** @var Custom_Formats_List_Table instance */
	private $custom_formats_list_table;

	/** @var \WC_Customer_Order_CSV_Export_Admin_Custom_Format_Builder instance */
	private $custom_format_builder;


	/**
	 * Sets up the custom formats admin class.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		global $export_type;

		$this->export_type = $export_type;

		if ( ! empty( $_GET['format_action'] ) ) {
			$this->action = sanitize_text_field( $_GET['format_action'] );
		}

		if ( ! empty( $_GET['format_key'] ) ) {
			$this->format_key = sanitize_text_field( $_GET['format_key'] );
		}
	}


	/**
	 * Gets the custom formats list table class instance.
	 *
	 * @since 4.7.0
	 *
	 * @return Custom_Formats_List_Table instance
	 */
	public function get_custom_formats_list_table_instance() {

		if ( null === $this->custom_formats_list_table ) {
			$this->custom_formats_list_table = wc_customer_order_csv_export()->load_class( '/src/admin/Custom_Formats_List_Table.php', 'SkyVerge\WooCommerce\CSV_Export\Admin\Custom_Formats_List_Table' );
		}

		return $this->custom_formats_list_table;
	}


	/**
	 * Get the custom format builder class instance
	 *
	 * @since 4.7.0
	 *
	 * @return \WC_Customer_Order_CSV_Export_Admin_Custom_Format_Builder instance
	 */
	public function get_custom_format_builder_instance() {

		if ( null === $this->custom_format_builder ) {
			$this->custom_format_builder = wc_customer_order_csv_export()->load_class( '/src/admin/class-wc-customer-order-csv-export-admin-custom-format-builder.php', 'WC_Customer_Order_CSV_Export_Admin_Custom_Format_Builder' );
		}

		return $this->custom_format_builder;
	}


	/**
	 * Gets sections.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_sections() {

		/**
		 * Allows actors to change the sections for the custom formats admin page.
		 *
		 * @since 4.7.0
		 *
		 * @param array $sections
		 */
		return apply_filters( 'wc_customer_order_export_custom_formats_admin_sections', wc_customer_order_csv_export()->get_export_types() );
	}


	/**
	 * Outputs sections for the custom formats admin page.
	 *
	 * @since 4.7.0
	 * @deprecated 5.1.1
	 */
	public function output_sections() {

		wc_deprecated_function( __METHOD__, '5.1.1' );
	}


	/**
	 * Shows custom formats list table.
	 *
	 * @since 4.7.0
	 */
	private function render_custom_format_list_table() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce_csv_exports' ) ) {
			return;
		}

		echo '<div id="custom-formats">';

		$add_url = wp_nonce_url( add_query_arg( [
			'format_action' => self::ACTION_ADD,
		] ) );

		?>

		<h1 class="wp-heading-inline"><?php echo esc_html_x( 'Custom Formats', 'page title', 'woocommerce-customer-order-csv-export' ); ?></h1>
		<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action add-new-custom-format"><?php echo esc_html_x( 'Add new', 'page title action', 'woocommerce-customer-order-csv-export' ); ?></a>

		<?php

		// instantiate extended list table
		$custom_format_list_table = $this->get_custom_formats_list_table_instance();

		if ( ! empty( $_POST['export_type'] ) ) {
			$custom_format_list_table->set_export_type( wc_clean( $_POST['export_type'] ) );
		}

		// prepare and display the list table
		$custom_format_list_table->prepare_items();
		$custom_format_list_table->display();

		echo '</div>';
	}


	/**
	 * Shows custom formats admin page.
	 *
	 * @since 4.7.0
	 */
	public function output() {

		switch ( $this->action ) {

			case self::ACTION_ADD:
			case self::ACTION_EDIT:

				$this->get_custom_format_builder_instance()->output( $this->export_type, $this->format_key );

			break;

			case self::ACTION_DELETE:

				// verify this is a legitimate request
				if ( $this->format_key && wp_verify_nonce( SV_WC_Helper::get_requested_value( '_wpnonce' ), 'wc_customer_order_coupon_export_delete_custom_format' ) ) {
					wc_customer_order_csv_export()->get_formats_instance()->delete_custom_format( $this->export_type, $this->format_key );
				}

				$this->render_custom_format_list_table();

			break;

			default:
				$this->render_custom_format_list_table();
		}
	}


	/**
	 * Gets the custom formats list table URL.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_custom_formats_table_url() {

		return add_query_arg( [
			'page'    => 'wc_customer_order_csv_export',
			'tab'     => 'custom_formats',
		], admin_url( 'admin.php' ) );
	}


	/**
	 * Gets the delimiter options.
	 *
	 * @return array
	 * @since 4.7.0
	 *
	 */
	public function get_delimiter_options() {

		return [
			","  => __( 'Comma', 'woocommerce-customer-order-csv-export' ),
			";"  => __( 'Semicolon', 'woocommerce-customer-order-csv-export' ),
			"\t" => __( 'Tab', 'woocommerce-customer-order-csv-export' ),
		];
	}


	/**
	 * Gets the row type options.
	 *
	 * @return array
	 * @since 4.7.0
	 *
	 */
	public function get_row_type_options() {

		return [
			'order' => __( 'A single order', 'woocommerce-customer-order-csv-export' ),
			'item'  => __( 'A single line item', 'woocommerce-customer-order-csv-export' ),
		];
	}


	/**
	 * Gets the items format options.
	 *
	 * @return array
	 * @since 4.7.0
	 *
	 */
	public function get_items_format_options() {

		return [
			'pipe_delimited' => __( 'Pipe-delimited format', 'woocommerce-customer-order-csv-export' ),
			'json'           => __( 'JSON', 'woocommerce-customer-order-csv-export' ),
		];
	}


}
