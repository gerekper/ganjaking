<?php
/**
 * YITH WooCommerce Points and Rewards Premium Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Points_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Points_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Points_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			add_filter( 'yith_wcmap_get_items', array( $this, 'add_endpoint_icon' ) );
			// Banner options.
			add_filter( 'yith_wcmap_banner_counter_type_options', array( $this, 'add_counter_type' ), 10 );
			add_filter( 'yith_wcmap_banner_points_counter_value', array( $this, 'count_customer_points' ), 10, 2 );
		}

		/**
		 * Add a default endpoint icon if not set
		 *
		 * @since 1.0.0
		 * @param array $items The items array.
		 * @return array
		 */
		public function add_endpoint_icon( $items ) {
			$key = 'my-points';
			if ( function_exists( 'yith_points' ) ) {
				$key = yith_points()->endpoint;
			} elseif ( function_exists( 'YITH_WC_Points_Rewards_Frontend' ) ) {
				$key = YITH_WC_Points_Rewards_Frontend()->endpoint;
			}

			if ( isset( $items[ $key ] ) && empty( $items[ $key ]['icon'] ) ) {
				$items[ $key ]['icon'] = 'diamond';
			}

			return $items;
		}

		/**
		 * Add gift card count option to available counter types
		 *
		 * @since 3.0.0
		 * @param array $options Banner counter options.
		 * @return array
		 */
		public function add_counter_type( $options ) {
			$options['points'] = _x( 'Customer points', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' );

			return $options;
		}

		/**
		 * Return the number of customer gift cards
		 *
		 * @since 3.0.0
		 * @param integer $value The counter value.
		 * @param integer $customer_id The customer ID.
		 * @return integer
		 */
		public function count_customer_points( $value, $customer_id = 0 ) {
			if ( ! $customer_id ) {
				$customer_id = get_current_user_id();
			}

			if ( function_exists( 'ywpar_get_current_customer' ) ) {
				$customer = ywpar_get_current_customer();
				$points   = $customer->get_total_points();
			} else {
				$points = get_user_meta( $customer_id, '_ywpar_user_total_points', true );
			}

			return intval( $points );
		}
	}
}
