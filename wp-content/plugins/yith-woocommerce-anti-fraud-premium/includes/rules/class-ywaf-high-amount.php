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

if ( ! class_exists( 'YWAF_High_Amount' ) ) {

	/**
	 * High amount rules class
	 *
	 * @class   YWAF_High_Amount
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_High_Amount extends YWAF_Rules {

		private $multiplier = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->multiplier = get_option( 'ywaf_rules_high_amount_multiplier', 2 );

			$message = sprintf( __( 'This order amount is %s times bigger than the total average for orders in your site.', 'yith-woocommerce-anti-fraud' ), $this->multiplier );
			$points  = get_option( 'ywaf_rules_high_amount_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if order is higher than the average.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			global $wpdb;

			$fraud_risk = false;
			$average    = round( $wpdb->get_var( "
                SELECT  AVG(a.meta_value)
                FROM    {$wpdb->prefix}postmeta a INNER JOIN {$wpdb->prefix}posts b ON b.ID = a.post_id
                WHERE   a.meta_key = '_order_total'
                AND     a.meta_value > 0
                AND     b.post_type = 'shop_order'
                AND     b.post_status IN ( 'wc-completed', 'wc-processing', 'wc-on-hold' )
                " ) );

			if ( ( $average > 0 ) && $order->get_total() > ( $average * $this->multiplier ) ) {
				$fraud_risk = true;
			}

			return $fraud_risk;

		}

	}

}