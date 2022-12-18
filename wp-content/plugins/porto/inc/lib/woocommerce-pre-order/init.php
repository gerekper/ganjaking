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

			// add pro_order products for pre_order elementor wc shortcode.
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'add_pre_order_items_wc_query' ), 10, 3 );
			add_filter(
				'woocommerce_products_widget_query_args',
				function( $query_args ) {
					if ( isset( $query_args['order'] ) && 0 === strpos( $query_args['order'], 'pre_order' ) ) {
						$query_args['order'] = str_replace( 'pre_order', '', $query_args['order'] );
						$query_args          = $this->add_pre_order_items_wc_query( $query_args, array( 'visibility' => 'pre_order' ), null );
					}
					return $query_args;
				}
			);
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

		public function add_pre_order_items_wc_query( $query_args, $attribute, $type ) {
			if ( 'pre_order' == $attribute['visibility'] ) {
				if ( ! isset( $query_args['meta_query'] ) ) {
					$query_args['meta_query'] = array();
				}
				$query_args['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key'   => '_porto_pre_order',
						'value' => 'yes',
					),
					array(
						'key'   => '_porto_variation_pre_order',
						'value' => 'yes',
					),
				);
			}
			return $query_args;
		}
	}

	new Porto_Woocommerce_Pre_Order;
endif;
