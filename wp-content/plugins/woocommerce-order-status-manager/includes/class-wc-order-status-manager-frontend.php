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
 * Order Status Manager Frontend
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Frontend {


	/**
	* Add hooks
	*
	* @since 1.0.0
	*/
	public function __construct() {

		// Load frontend styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// render frontend embedded styles
		add_action( 'wp_print_styles', array( $this, 'render_embedded_styles' ), 1 );

		// alter available order actions based on order status
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'orders_actions' ), 10, 2 );

		// Add status description to view-order screen.
		add_action( 'woocommerce_view_order',                               array( $this, 'add_order_description_tooltip' ) );
		// Add status description to recent orders in my-account screen.
		add_action( 'woocommerce_my_account_my_orders_column_order-status', array( $this, 'add_recent_orders_description_tooltip' )  );
	}


	/**
	 * Loads frontend styles and scripts on the view-order page
	 *
	 * @since 1.0.0
	 */
	public function load_styles_scripts() {

		// use jQuery tipTip from WooCommerce if not enqueued already
		if ( $this->screen_has_order_status() && ! wp_script_is( 'jquery-tiptip', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		}
	}


	/**
	 * Renders the tipTip CSS on the view-order page
	 *
	 * @since 1.0.0
	 */
	public function render_embedded_styles() {

		if ( $this->screen_has_order_status() ) {

			echo '<style type="text/css">mark.order-status:hover{cursor:pointer;}#tiptip_holder{display:none;position:absolute;top:0;left:0;z-index:99999}#tiptip_holder.tip_top{padding-bottom:5px}#tiptip_holder.tip_top #tiptip_arrow_inner{margin-top:-7px;margin-left:-6px;border-top-color:#464646}#tiptip_holder.tip_bottom{padding-top:5px}#tiptip_holder.tip_bottom #tiptip_arrow_inner{margin-top:-5px;margin-left:-6px;border-bottom-color:#464646}#tiptip_holder.tip_right{padding-left:5px}#tiptip_holder.tip_right #tiptip_arrow_inner{margin-top:-6px;margin-left:-5px;border-right-color:#464646}#tiptip_holder.tip_left{padding-right:5px}#tiptip_holder.tip_left #tiptip_arrow_inner{margin-top:-6px;margin-left:-7px;border-left-color:#464646}#tiptip_content,.chart-tooltip{font-size:11px;color:#fff;padding:.5em .5em;background:#464646;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:1px 1px 3px rgba(0,0,0,0.1);-moz-box-shadow:1px 1px 3px rgba(0,0,0,0.1);box-shadow:1px 1px 3px rgba(0,0,0,0.1);text-align:center;max-width:150px}#tiptip_content code,.chart-tooltip code{background:#888;padding:1px}#tiptip_arrow,#tiptip_arrow_inner{position:absolute;border-color:transparent;border-style:solid;border-width:6px;height:0;width:0}</style>';
		}
	}


	/**
	 * Checks if the current screen has at least one order carrying an order status label
	 * Screens: view-order or my-account
	 *
	 * @since 1.3.0
	 * @return bool
	 */
	private function screen_has_order_status() {

		return is_account_page();
	}


	/**
	 * Alters available actions for a given order.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $actions
	 * @param \WC_Order $order
	 * @return array
	 */
	public function orders_actions( $actions, $order ) {

		// remove option to cancel an order if its status is 'pending-deposit'
		if ( 'pending-deposit' === $order->get_status() ) {
			unset( $actions['cancel'] );
		}

		return $actions;
	}


	/**
	 * Display status description as a tooltip on the view-order screen
	 *
	 * @since 1.0.0
	 * @param int $order_id The order ID
	 */
	public function add_order_description_tooltip( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( $order ) {

			$status      = new WC_Order_Status_Manager_Order_Status( $order->get_status() );
			$description = trim( $status->get_description() );

			if ( ! empty( $description ) ) {
				$this->render_order_status_description_tooltip( $status );
			}
		}
	}


	/**
	 * Display status description as a tooltip on the recent orders table in my-account screen
	 *
	 * @since 1.3.0
	 * @param \WC_Order $order
	 */
	public function add_recent_orders_description_tooltip( $order ) {

		$status      = new WC_Order_Status_Manager_Order_Status( $order->get_status() );
		$name        = $status->get_name();
		$description = trim( $status->get_description() );

		if ( ! empty( $description ) ) {
			echo '<mark class="order-status order-status-' . esc_attr( $status->get_slug() ) . '">' . $name . '</mark>';
			$this->render_order_status_description_tooltip( $status, 'order-status-' . $status->get_slug() );
		} else {
			echo $name;
		}
	}


	/**
	 * Enqueue inline js for initializing tooltips on order status labels
	 *
	 * @since 1.3.0
	 * @param \WC_Order_Status_Manager_Order_Status $order_status
	 * @param string $selector target class, optional, default 'order-status'
	 */
	private function render_order_status_description_tooltip( $order_status, $selector = 'order-status' ) {

		if ( $order_status instanceof WC_Order_Status_Manager_Order_Status && $description = $order_status->get_description() ) {

			wc_enqueue_js( "
				$( 'mark."  . esc_attr( $selector )  . "' ).tipTip( { content: '" . esc_js( $description ) . "' } );
			" );
		}
	}


}
