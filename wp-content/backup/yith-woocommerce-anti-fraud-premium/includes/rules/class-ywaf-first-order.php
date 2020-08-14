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

if ( ! class_exists( 'YWAF_First_Order' ) ) {

	/**
	 * First order rules class
	 *
	 * @class   YWAF_First_Order
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_First_Order extends YWAF_Rules {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$message = __( 'This is the first order by this user.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_first_order_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if this is user's first order.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk = false;
			$args       = array(
				'post_type'      => 'shop_order',
				'post_status'    => 'wc-completed',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'     => '_billing_email',
						'value'   => $order->get_billing_email(),
						'compare' => '='
					)
				)
			);
			$query      = new WP_Query( $args );

			if ( $query->post_count < 1 ) {
				$fraud_risk = true;
			}

			wp_reset_query();
			wp_reset_postdata();

			return $fraud_risk;

		}

	}

}