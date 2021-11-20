<?php
/**
 * WooCommerce Order Status Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Order Status Manager AJAX handler
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_AJAX {


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Render a status order icon
		add_action( 'wp_ajax_wc_order_status_manager_get_icon_image_url',  array( $this, 'render_icon_image_url' ) );

		// Save order status sort order
		add_action( 'wp_ajax_wc_order_status_manager_sort_order_statuses', array( $this, 'sort_order_statuses' ) );

		// Import custom order statuses
		add_action( 'wp_ajax_wc_order_status_manager_import_custom_order_statuses', array( $this, 'import_custom_order_statuses' ) );

		// Upon deleting an order status
		add_action( 'wp_ajax_wc_order_status_manager_can_safely_delete_order_status', array( $this, 'can_safely_delete_order_status' ) );
		add_action( 'wp_ajax_wc_order_status_manager_bulk_reassign_order_status',     array( $this, 'bulk_reassign_order_status' ) );

		// Enables or disables the plugin deactivation confirmation modal
		add_action( 'wp_ajax_wc_order_status_manager_set_deactivation_confirmation_state', [ $this, 'set_deactivation_confirmation_state' ] );

		add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'custom_order_preview_actions' ), 10, 2 );
	}


	/**
	 * Render the icon image attachment src
	 *
	 * @since 1.0.0
	 */
	public function render_icon_image_url() {

		if ( ! isset( $_REQUEST['attachment_id'] ) ) {
			return;
		}

		$icon_attachment_src = wp_get_attachment_image_src( $_REQUEST['attachment_id'], 'wc_order_status_icon' );

		if ( empty( $icon_attachment_src ) ) {
			return;
		}

		echo $icon_attachment_src[0];
		exit;
	}


	/**
	 * Sort the order statuses
	 *
	 * @since 1.3.0
	 */
	public function sort_order_statuses() {

		check_ajax_referer( 'sort-order-statuses', 'security' );

		if ( empty( $_POST['statuses'] ) || ! is_array( $_POST['statuses']) ) {
			die;
		}

		$statuses = array();

		foreach( $_POST['statuses'] as $index => $status ) {

			$order_status_post = get_page_by_path( $status, OBJECT, 'wc_order_status' );

			if ( $order_status_post ) {

				$statuses[ $status ] = (int) $index;

				wp_update_post( array(
					'ID'         => $order_status_post->ID,
					'menu_order' => (int) $index,
				) );
			}
		}

		wp_send_json_success( $statuses );
	}


	/**
	 * Import custom order statuses
	 *
	 * @since 1.3.0
	 */
	public function import_custom_order_statuses() {

		check_ajax_referer( 'import-custom-order-statuses', 'security' );

		wc_order_status_manager()->get_order_statuses_instance()->ensure_statuses_have_posts();

		wp_send_json_success( wc_get_order_statuses() );
	}


	/**
	 * Delete order status
	 *
	 * When deleting a custom order status check if there are orders
	 * using the status to be deleted to prompt for a reassignment
	 *
	 * @since 1.3.0
	 */
	public function can_safely_delete_order_status() {

		check_ajax_referer( 'delete-order-status', 'security' );

		if ( empty( $_POST['status'] ) && ! is_numeric( $_POST['status'] ) ) {
			die;
		}

		if ( $status = wc_order_status_manager()->is_order_status_cpt( $_POST['status'] ) ) {

			if ( $existing_orders = $status->has_orders( array( 'nopaging' => true, 'posts_per_page' => -1 ) ) ) {

				// Prompt for confirmation and status reassignment popup
				wp_send_json_error( array(
					'status_slug'  => $status->get_slug(),
					'status_name'  => $status->get_name(),
					'orders_count' => $existing_orders,
					'orders_link'  => add_query_arg( array(
						'post_status' => $status->get_slug( true ),
						'post_type'   => 'shop_order',
					), get_admin_url( null, 'edit.php' ) ),
				) );

			} else {

				// All clear
				wp_send_json_success();
			}
		}

		die;
	}


	/**
	 * Bulk reassign order status
	 *
	 * Change order status of many orders from one to another
	 *
	 * @since 1.3.0
	 */
	public function bulk_reassign_order_status() {

		check_ajax_referer( 'bulk-reassign-order-status', 'security' );

		if ( isset( $_POST['old_status'], $_POST['new_status'] ) ) {

			// sanity checks
			if ( ( $_POST['old_status'] !== $_POST['new_status'] )
			     && ( $old_status = wc_order_status_manager()->is_order_status_cpt( $_POST['old_status'] ) )
			     && ( $new_status = wc_order_status_manager()->is_order_status_cpt( $_POST['new_status'] ) ) ) {

				wc_order_status_manager()->get_order_statuses_instance()->handle_order_status_delete( $old_status->get_id(), $new_status->get_slug() );

				wp_send_json_success();

			} else {

				wp_send_json_error( "I'm sorry Dave, I can't do that" );
			}
		}

		die();
	}



	/**
	 * Adds custom order preview actions in order preview modal.
	 *
	 * @since 1.9.0
	 *
	 * @internal
	 *
	 * @param array $actions
	 * @param \WC_Order $order
	 * @return array
	 */
	public function custom_order_preview_actions( $actions, WC_Order $order ) {

		$custom_actions = wc_order_status_manager()->get_order_statuses_instance()->get_custom_order_actions( $order );

		if ( ! empty( $custom_actions ) ) {

			if ( ! isset( $actions['status'] ) ) {
				$actions['status'] = array(
					'group'   => __( 'Change status: ', 'woocommerce' ), // this textdomain is used here on purpose
					'actions' => $custom_actions,
				);
			} else {
				$actions['status']['actions'] = array_merge( $custom_actions, wc_order_status_manager()->get_order_statuses_instance()->trim_order_actions( $actions['status']['actions'] ) );
			}

		}

		return $actions;
	}


	/**
	 * Enables or disables the plugin deactivation confirmation modal.
	 *
	 * @internal
	 *
	 * @since 1.12.1-dev.1
	 */
	public function set_deactivation_confirmation_state() {

		check_ajax_referer( 'set-deactivation-confirmation-state', 'security' );

		if ( empty( $_POST['disabled'] ) ) {
			die;
		}

		update_user_meta( get_current_user_id(), WC_Order_Status_Manager::PLUGIN_DEACTIVATION_MODAL_OPTION, $_POST['disabled'] );

		wp_send_json_success();
	}


}
