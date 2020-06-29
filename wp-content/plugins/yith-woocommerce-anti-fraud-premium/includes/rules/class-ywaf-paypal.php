<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWAF_PayPal' ) ) {

	/**
	 * PayPal rules class
	 *
	 * @class   YWAF_PayPal
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_PayPal extends YWAF_Rules {

		private $verified_addresses = array();

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$value                    = get_option( 'ywaf_paypal_verified' );
			$this->verified_addresses = ( $value != '' ) ? explode( ',', $value ) : array();

			$message = __( 'PayPal email address has not been verified!', 'yith-woocommerce-anti-fraud' );
			$points  = 100;

			parent::__construct( $message, $points );

		}

		/**
		 * Check if email is in blacklist.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk   = false;
			$parent_order = wp_get_post_parent_id( $order->get_id() );

			if ( $parent_order ) {
				$order = wc_get_order( $parent_order );
			}

			$paypal_email_fields = apply_filters( 'ywaf_paypal_email_fields', array(
				'Payer PayPal address',
				'paypal_email'
			) );

			$paypal_email = '';

			foreach ( $paypal_email_fields as $email_field ) {

				if ( $paypal_email == '' ) {
					$paypal_email = $order->get_meta( $email_field );
				}

			}

			if ( $paypal_email == '' ) {

				$order->update_meta_data( 'ywaf_paypal_check', 'waiting' );
				$order->add_order_note( __( 'Waiting for PayPal transaction data.', 'yith-woocommerce-anti-fraud' ) );
				$order->save();

				return true;
			}

			if ( ! in_array( $paypal_email, $this->verified_addresses ) ) {

				$order->update_meta_data( 'ywaf_paypal_check', 'process' );
				$order->add_order_note( __( 'Waiting for PayPal verification.', 'yith-woocommerce-anti-fraud' ) );
				$order->save();

				YITH_WAF()->paypal_mail_send( $order );

				$fraud_risk = true;

			}

			return $fraud_risk;

		}

		/**
		 * Add email to verified list.
		 *
		 * @since   1.0.0
		 *
		 * @param   $email
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_to_verified( $email ) {

			$this->verified_addresses[] = $email;
			update_option( 'ywaf_paypal_verified', implode( ',', $this->verified_addresses ) );

		}

	}

}