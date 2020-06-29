<?php
/**
 * Porto WooCommerce Pre-Order Initialize
 *
 * @author     Porto Themes
 * @category   Library
 * @since      5.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Porto_Woocommerce_Pre_Order' ) ) :

	class Porto_Woocommerce_Pre_Order {

		public function __construct() {
			add_action( 'admin_init', array( $this, 'init_admin' ) );

			add_action( 'wp', array( $this, 'init_view' ) );

			add_action( 'init', array( $this, 'add_myaccount_pre_orders_endpoints' ), 1 );
		}

		public function init_admin() {
			require_once PORTO_LIB . '/lib/woocommerce-pre-order/classes/class-porto-pre-order-admin.php';
			new Porto_Pre_Order_Admin;
		}

		public function init_view() {
			if ( ! is_admin() || wp_doing_ajax() ) {
				require_once PORTO_LIB . '/lib/woocommerce-pre-order/classes/class-porto-pre-order-view.php';
				new Porto_Pre_Order_View;
			}

			if ( is_account_page() ) {
				require_once PORTO_LIB . '/lib/woocommerce-pre-order/classes/class-porto-pre-order-myaccount.php';
				new Porto_Pre_Order_Myaccount;
			}
		}

		public function add_myaccount_pre_orders_endpoints() {
			add_rewrite_endpoint( 'pre-orders', EP_ROOT | EP_PAGES );
		}
	}

	new Porto_Woocommerce_Pre_Order;
endif;
