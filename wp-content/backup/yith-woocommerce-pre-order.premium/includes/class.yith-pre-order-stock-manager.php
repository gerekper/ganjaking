<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Pre_Order_Stock_Manager' ) ) {
	/**
	 * Class YITH_Pre_Order_Stock_Manager
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.3.0
	 */
	class YITH_Pre_Order_Stock_Manager {

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.3.0
		 */
		public function __construct() {
			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				add_filter( 'woocommerce_product_get_stock_status', array( $this, 'check_stock_status' ), 10, 2 );
				// Filter woocommerce_product_get_stock_quantity must be used only in frontend
				if ( ! is_admin() ) {
					add_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'check_stock_quantity' ), 10, 2 );
				}
				add_filter( 'woocommerce_product_variation_get_stock_status', array( $this, 'check_stock_status' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_stock_quantity', array( $this, 'check_stock_quantity' ), 10, 2 );
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'remove_stock_qty_and_status_filters' ) );
				add_action( 'woocommerce_product_object_updated_props', array( $this, 'product_and_variation_set_stock' ) );
			} else {
				add_action( 'woocommerce_variation_set_stock_status', array( $this, 'variation_set_stock_status' ), 10, 2 );
				add_action( 'woocommerce_product_set_stock', array( $this, 'product_and_variation_set_stock' ) );
				add_action( 'woocommerce_variation_set_stock', array( $this, 'product_and_variation_set_stock' ) );
			}
		}

		public function check_stock_status( $status, $product ) {
			if ( 'yes' != get_option( 'yith_wcpo_allow_out_of_stock_selling', 'no' ) ) {
				return $status;
			}
			$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : '';
			if ( $current_screen && 'edit-product' == $current_screen->id ) {
				return $status;
			}
			if ( 'instock' == $status ) {
				return $status;
			}
			if ( ( isset( $_REQUEST['screen'] ) && 'edit-product' == $_REQUEST['screen'] ) ) {
				return $status;
			}
			if ( isset( $_REQUEST['action'] ) && 'woocommerce_save_variations' == $_REQUEST['action'] ) {
				return $status;
			}

			if ( 'simple' == $product->get_type() || 'variation' == $product->get_type() ) {
				$pre_order = new YITH_Pre_Order_Product( $product );
				$is_pre_order = $pre_order->get_pre_order_status();

				if ( 'yes' == $is_pre_order ) {
					$status = 'instock';
				}

			}

			return $status;
		}

		public function check_stock_quantity( $quantity, $product ) {
			if ( 'yes' != get_option( 'yith_wcpo_allow_out_of_stock_selling', 'no' ) ) {
				return $quantity;
			}
			$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : '';
			if ( $current_screen && 'edit-product' == $current_screen->id ) {
				return $quantity;
			}
			if ( isset( $_REQUEST['action'] ) && 'woocommerce_save_variations' == $_REQUEST['action'] ) {
				return $quantity;
			}
			if ( ( isset( $_REQUEST['screen'] ) && 'edit-product' == $_REQUEST['screen'] ) ) {
				return $quantity;
			}

			$pre_order = new YITH_Pre_Order_Product( $product );
			$is_pre_order = $pre_order->get_pre_order_status();

			if ( 'yes' == $is_pre_order ) {
				$quantity = apply_filters( 'yith_ywpo_stock_quantity', (int) 100, $quantity, $pre_order, $product );
			}

			return $quantity;
		}

		public function remove_stock_qty_and_status_filters() {
			remove_filter( 'woocommerce_product_get_stock_status', array( $this, 'check_stock_status' ) );
			remove_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'check_stock_quantity' ) );
			remove_filter( 'woocommerce_product_variation_get_stock_status', array( $this, 'check_stock_status' ) );
			remove_filter( 'woocommerce_product_variation_get_stock_quantity', array( $this, 'check_stock_quantity' ) );
		}

		/*
		 * WC 2.6 helper function
		 */
		public function variation_set_stock_status( $variation_id, $status ) {
			$product = wc_get_product( $variation_id );
			$this->product_and_variation_set_stock( $product, $status );
		}

		/**
		 * @type $product WC_Product
		 */
		public function product_and_variation_set_stock( $product, $status = null ) {
			if ( 'yes' != get_option( 'yith_wcpo_enable_pre_order_auto_outofstock_notification', 'no' )  ) {
				return;
			}

			if ( 'simple' == $product->get_type() || 'variation' == $product->get_type() ) {
				$pre_order = new YITH_Pre_Order_Product( $product->get_id() );
				$is_pre_order = $pre_order->get_pre_order_status();
				$is_in_stock = null;
				if ( $status ) {
					$is_in_stock = 'instock' == $status;
				} elseif ( version_compare( WC()->version, '3.0.0', '<' ) ) {
					$is_in_stock = $product->is_in_stock();
				} else {
					$is_in_stock = 'instock' == $product->get_stock_status( 'edit' );
				}

				if ( $is_in_stock ) {
					if ( 'yes' == $is_pre_order ) {
						$pre_order->set_pre_order_status( 'no' );
					}
				} else {
					if ( 'yes' != $is_pre_order ) {
						$pre_order->set_pre_order_status( 'yes' );
						WC()->mailer();
						do_action( 'yith_ywpo_out_of_stock', $pre_order->id );
					}
				}
			}
		}
	}
}