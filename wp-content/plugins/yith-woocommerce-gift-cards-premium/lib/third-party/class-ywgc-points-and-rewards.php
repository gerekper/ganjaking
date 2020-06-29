<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_Points_And_Rewards' ) ) {

	/**
	 *
	 * @class   YWGC_Points_And_Rewards
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YWGC_Points_And_Rewards {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			/**
			 * YITH WooCommerce Points and Rewards Premium compatibility.
			 * Set the points earned for a gift card product
			 */
			add_filter( 'ywpar_get_product_point_earned', array(
				$this,
				'set_points_rewards_earning'
			), 10, 2 );
		}

		/**
		 * Set the points earned while used within YITH Points and Rewards plugin.
		 *
		 * @param float      $points
		 * @param WC_Product $product
		 *
		 * @return float
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function set_points_rewards_earning( $points, $product ) {

			//  Gift card products are not eligible for earning points!
			$product_type = $product->get_type();
			
			if ( YWGC_GIFT_CARD_PRODUCT_TYPE == $product_type ) {
				return 0.00;
			}

			return $points;
		}
	}
}

YWGC_Points_And_Rewards::get_instance();