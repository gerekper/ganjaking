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

if ( ! class_exists( 'YWAF_Many_Attempts' ) ) {

	/**
	 * Many attempts rules class
	 *
	 * @class   YWAF_Many_Attempts
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Many_Attempts extends YWAF_Rules {

		private $order_date = null;
		private $order_limit = null;
		private $time_limit = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->order_limit = get_option( 'ywaf_rules_many_attempts_orders', 2 );
			$this->time_limit  = get_option( 'ywaf_rules_many_attempts_hours', 1 );

			$message = sprintf( __( 'More than %s orders have been placed in %s hours from this IP address.', 'yith-woocommerce-anti-fraud' ), $this->order_limit, $this->time_limit );
			$points  = get_option( 'ywaf_rules_many_attempts_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if there are many attempts from same IP in a minimum time interval.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk       = false;
			$this->order_date = $order->get_date_created();
			$ip_address       = $order->get_customer_ip_address();
			$args             = array(
				'post_type'      => 'shop_order',
				'post__not_in'   => array( $order->get_id() ),
				'post_status'    => array( 'wc-completed', 'wc-processing', 'wc-on-hold' ),
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'     => '_customer_ip_address',
						'value'   => $ip_address,
						'compare' => '='
					)
				)
			);

			add_filter( 'posts_where', array( $this, 'filter_where' ) );
			$query = new WP_Query( $args );
			remove_filter( 'posts_where', array( $this, 'filter_where' ) );

			if ( $query->post_count > $this->order_limit ) {
				$fraud_risk = true;
			}

			wp_reset_query();
			wp_reset_postdata();

			return $fraud_risk;

		}

		/**
		 * Set custom where condition
		 *
		 * @since   1.0.0
		 *
		 * @param   $where
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function filter_where( $where = '' ) {

			$start_date = date( 'Y-m-d H:i:s', strtotime( '-' . $this->time_limit . ' hours', strtotime( $this->order_date ) ) );

			$where .= " AND ( post_date >= '" . $start_date . "' AND post_date <= '" . $this->order_date . "' )";

			return $where;

		}

	}
}