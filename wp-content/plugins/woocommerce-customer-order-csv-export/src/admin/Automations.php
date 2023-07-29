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

use SkyVerge\WooCommerce\CSV_Export\Admin\Automations\Edit;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;
use SkyVerge\WooCommerce\CSV_Export\Admin\Automations\List_Table;

defined( 'ABSPATH' ) or exit;

/**
 * The Automations/Automatic Exports page handler.
 *
 * @since 5.0.0
 */
class Automations {


	/** @var string add new format action */
	const ACTION_ADD = 'add';

	/** @var string edit format action */
	const ACTION_EDIT = 'edit';

	/** @var string delete format action */
	const ACTION_DELETE = 'delete';


	/** @var string the current action, like edit or delete */
	private $action;

	/** @var string the current automation ID, if any */
	private $automation_id;

	/** @var Edit edit screen handler instance */
	private $edit_screen_instance;


	/**
	 * Sets up the automations admin class.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {

		if ( ! empty( $_GET['automation_action'] ) ) {
			$this->action = sanitize_text_field( $_GET['automation_action'] );
		}

		if ( ! empty( $_GET['automation_id'] ) ) {
			$this->automation_id = sanitize_text_field( $_GET['automation_id'] );
		}
	}


	/**
	 * Outputs the HTML.
	 *
	 * @since 5.0.0
	 */
	public function output() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce_csv_exports' ) ) {
			return;
		}

		// maybe handle the delete action before displaying anything
		if ( self::ACTION_DELETE === $this->action && $this->automation_id ) {

			$this->handle_delete( $this->automation_id );

			$this->action = null;
		}

		switch ( $this->action ) {

			case self::ACTION_ADD:
			case self::ACTION_EDIT:

				$this->get_edit_screen_instance()->output( $this->automation_id );

			break;

			default:
				$this->output_list();
		}
	}


	/**
	 * Outputs the automations list table.
	 *
	 * @since 5.0.0
	 */
	protected function output_list() {

		?>
		<h1 class="wp-heading-inline"><?php echo esc_html_x( 'Automated Exports', 'page title', 'woocommerce-customer-order-csv-export' ); ?></h1>
		<a href="<?php echo esc_url( self::get_automation_add_url() ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add new', 'page title action', 'woocommerce-customer-order-csv-export' ); ?></a>

		<?php

		if ( ! class_exists( \WP_List_Table::class ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		require_once( wc_customer_order_csv_export()->get_plugin_path() . '/src/admin/Automations/List_Table.php' );

		$list_table = new List_Table();

		$list_table->prepare_items();
		$list_table->display();
	}


	/**
	 * Handles deleting an automation.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id automation ID to delete
	 */
	protected function handle_delete( $automation_id ) {

		try {

			if ( ! wp_verify_nonce( Framework\SV_WC_Helper::get_requested_value( 'nonce' ), 'wc_customer_order_export_admin_' . self::ACTION_DELETE . '_automation' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Please try again.', 'woocommerce-customer-order-csv-export' ) );
			}

			if ( $automation = Automation_Factory::get_automation( $automation_id ) ) {
				$automation->delete();
			}

			wc_customer_order_csv_export()->get_message_handler()->add_message( __( 'Automated export deleted', 'woocommerce-customer-order-csv-export' ) );

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$message = sprintf(
				/* translators: Placeholders: %s - error message */
				__( 'Could not delete automation. %s', 'woocommerce-customer-order-csv-export' ),
				$exception->getMessage()
			);

			wc_customer_order_csv_export()->get_message_handler()->add_error( trim( $message ) );
		}
	}


	/** Getter methods ************************************************************************************************/


	/**
	 * Gets the edit screen handler instance.
	 *
	 * @since 5.0.0
	 *
	 * @return Edit
	 */
	public function get_edit_screen_instance() {

		require_once( wc_customer_order_csv_export()->get_plugin_path() . '/src/admin/Automations/Edit.php' );

		if ( ! $this->edit_screen_instance instanceof Edit ) {
			$this->edit_screen_instance = new Edit();
		}

		return $this->edit_screen_instance;
	}


	/**
	 * Gets an automation's edit URL.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id automation ID
	 * @return string
	 */
	public static function get_automation_edit_url( $automation_id ) {

		return add_query_arg( [
			'automation_action' => self::ACTION_EDIT,
			'automation_id'     => $automation_id
		], self::get_screen_url() );
	}


	/**
	 * Gets an automation's delete URL.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id automation ID
	 * @return string
	 */
	public static function get_automation_delete_url( $automation_id ) {

		return add_query_arg( [
			'automation_action' => self::ACTION_DELETE,
			'automation_id'     => $automation_id,
			'nonce'             => wp_create_nonce( 'wc_customer_order_export_admin_' . self::ACTION_DELETE . '_automation' ),
		], self::get_screen_url() );
	}


	/**
	 * Gets the URL for adding an automation.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public static function get_automation_add_url() {

		return add_query_arg( [
			'automation_action' => self::ACTION_ADD,
		], self::get_screen_url() );
	}


	/**
	 * Gets this screen's URL.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public static function get_screen_url() {

		return add_query_arg( [
			'page' => 'wc_customer_order_csv_export',
			'tab'  => \WC_Customer_Order_CSV_Export_Admin::TAB_AUTOMATIONS,
		], admin_url( 'admin.php' ) );
	}


}
