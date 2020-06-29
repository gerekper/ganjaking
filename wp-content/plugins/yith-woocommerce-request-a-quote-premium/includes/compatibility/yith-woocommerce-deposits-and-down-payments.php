<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Implements the YWRAQ_Deposits class.
 *
 * @class   YWRAQ_Deposits
 * @package YITH
 * @since   2.1.0
 * @author  YITH
 */
if ( ! class_exists( 'YWRAQ_Deposits' ) ) {
	/**
	 * Class YWRAQ_Deposits
	 */
	class YWRAQ_Deposits {


		/**
		 * Single instance of the class
		 *
		 * @var \YWRAQ_Deposits
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWRAQ_Deposits
		 * @since 2.1.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  2.1.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			//admin order metabox
			add_filter( 'ywraq_order_metabox', array( $this, 'metabox_deposit_options' ) );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'update_cart_item_data' ), 40, 3 );
			add_filter( 'woocommerce_get_order_item_totals', array( $this, 'add_deposit_amount'), 10, 4);
		}

		/**
		 * @param $total_rows
		 * @param $order  WC_Order
		 * @param $tax_display
		 *
		 * @return mixed
		 */
		public function add_deposit_amount( $total_rows, $order, $tax_display ) {
			$is_quote = YITH_YWRAQ_Order_Request()->is_quote( $order->get_id() );

			$deposit_enabled = $order->get_meta( '_ywraq_deposit_enable' );
			$pay_now = $order->get_meta( '_ywraq_pay_quote_now' );

			$has_deposit = $order->get_meta('_has_deposit');
			//check if the deposit is enabled on quote
			if ( ! $is_quote || $has_deposit || ! ywraq_is_true( $deposit_enabled ) || ywraq_is_true( $pay_now ) ) {
				return $total_rows;
			}


			$deposit_rate  = (int) $order->get_meta( '_ywraq_deposit_rate' );
			$deposit_value = ( $order->get_total() - $order->get_total_fees() ) * (double) $deposit_rate / 100 + $order->get_total_fees();
			if ( $deposit_value > 0 ) {
				$deposit_amount        = sprintf( __( '%s (of %s)', 'yith-woocommerce-request-a-quote' ), wc_price( $deposit_value, array( 'currency' => $order->get_currency() )), $order->get_formatted_order_total( $tax_display ) );
				$total_rows['deposit'] = array(
					'label' => __( 'Pay now:', 'yith-woocommerce-request-a-quote' ),
					'value' => $deposit_amount,
				);
			}

			return $total_rows;
		}

		/**
		 * @param $cart_item_data
		 * @param $product_id
		 * @param $variation_id
		 *
		 * @return mixed
		 */
		public function update_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			$order_id = WC()->session->get( 'order_awaiting_payment' );

			if ( $order_id ) {
				$order = wc_get_order( $order_id );
				if ( ! $order || ! YITH_YWRAQ_Order_Request()->is_quote( $order_id ) ) {
					return $cart_item_data;
				}
			}else{
				return $cart_item_data;
			}

			$deposit_enabled  = $order->get_meta('_ywraq_deposit_enable');
			$pay_now = $order->get_meta( '_ywraq_pay_quote_now' );
			//check if the deposit is enabled on quote
			if ( ! ywraq_is_true( $deposit_enabled ) ||  ywraq_is_true( $pay_now ) ) {
				return $cart_item_data;
			}
			$product_id = ! empty( $variation_id ) ? $variation_id : $product_id;
			$product    = wc_get_product( $product_id );
			$price                             = isset( $cart_item_data['ywraq_price'] ) ? $cart_item_data['ywraq_price'] : $product->get_price();
			$deposit_rate                      = (int) $order->get_meta('_ywraq_deposit_rate');
			$deposit_value                     = $price * (double) $deposit_rate / 100;

			$deposit_balance                   = $price - $deposit_value;
			$cart_item_data['deposit']         = true;
			$cart_item_data['deposit_type']    = 'amount';
			$cart_item_data['deposit_amount']  = 0;
			$cart_item_data['deposit_rate']    = $deposit_rate;
			$cart_item_data['deposit_value']   = $deposit_value;
			$cart_item_data['deposit_balance'] = $deposit_balance;

			return $cart_item_data;
		}


		/**
		 * @param $options
		 *
		 * @return array
		 */
		public function metabox_deposit_options( $options ) {
			$new_options = array();
			foreach ($options as $key => $item ) {
				$new_options[ $key ] = $item;
				if( 'ywraq_customer_sep2' == $key ){
					$new_options['ywraq_deposit_enable'] = array(
						'label' => __( 'Enable Deposit', 'yith-woocommerce-request-a-quote' ),
						'type'  => 'onoff',
						'desc'  => '',
						'std'   => 'no',
					);

					$new_options['ywraq_deposit_rate'] = array(
						'label' => __( 'Deposit Rate', 'yith-woocommerce-request-a-quote' ),
						'type' => 'number',
						'desc' => __( 'Percentage of product total price required as deposit', 'yith-woocommerce-request-a-quote' ),
						'css' => 'min-width: 100px;',
						'default' => 10,
						'step' => 'any',
						'min' => 0,
						'max' => 100,
					);

					$new_options['ywraq_deposit_sep'] = array(
							'type' => 'sep'
					);
				}
			}

			return $new_options;
		}

	}

	/**
	 * Unique access to instance of YWRAQ_Deposits class
	 *
	 * @return \YWRAQ_Deposits
	 */
	function YWRAQ_Deposits() {
		return YWRAQ_Deposits::get_instance();
	}

	YWRAQ_Deposits();

}