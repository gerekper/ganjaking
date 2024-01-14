<?php
/**
 * Coupon Validator.
 *
 * @package SUMO Reward Points
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'SRP_Coupon_Validator' ) ) {

	/**
	 * Class.
	 */
	class SRP_Coupon_Validator {

		/**
		 * Redeemed Points.
		 *
		 * @var float
		 * */
		protected $redeemed_points;

		/**
		 * Available Points.
		 *
		 * @var float
		 * */
		protected $available_points;

		/**
		 * Object Type.
		 *
		 * @var object
		 * */
		protected $type = 'cart';

		/**
		 * Order.
		 * 
		 * @var object
		 */
		protected $order ;

		/**
		 * User ID.
		 *
		 * @var int
		 * */
		protected $user_id;

		/**
		 * Class Initialization.
		 *
		 * @param string $user_id User ID.
		 * @param string $redeemed_points Redeemed Points.
		 * @param string $order Order Object.
		 */
		public function __construct( $user_id, $redeemed_points, $order ) {
			if (is_object($order)) {
				$this->type = 'order';
				$this->order = $order;
			}
			$points_data = new RS_Points_Data( $user_id );

			$this->user_id     = $user_id;
			$this->redeemed_points  = $redeemed_points;
			$this->available_points = $points_data->total_available_points();
		}

		/**
		 * Is valid coupon?
		 *
		 * @param string $user_id User ID.
		 * @param string $redeemed_points Redeemed Points.
		 * @param string $order Order Object.
		 *
		 * @return bool
		 */
		public static function is_valid( $user_id, $redeemed_points, $order = false ) {
			$validation = new self( $user_id, $redeemed_points, $order );

			return $validation->validate_coupon();
		}

		/**
		 * Coupon Validation.
		 *
		 * @return bool
		 */
		public function validate_coupon() {
			$return = true;

			if ( ! $this->check_if_sale_price_product() ) {
				wc_add_notice( __( get_option( 'rs_redeeming_message_restrict_for_sale_price_product' ) ), 'error' );
				$return = false;
			} elseif ( ! $this->validate_minimum_points() ) {
				$return = false;
			} elseif ( ! $this->validate_maximum_points_per_day() ) {
				wc_add_notice( str_replace( '[max_points]', get_option( 'rs_maximum_redeeming_per_day_restriction' ), get_option( 'rs_maximum_redeeming_per_day_error', 'You are allowed to redeem a maximum of [max_points] points per day.' ) ), 'error' );
				$return = false;
			}

			return $return;
		}

		/**
		 * Coupon Validation.
		 *
		 * @return bool
		 */
		public function get_items() {
			$items = array();
			switch ($this->type) {
				case 'order':
					$items = $this->order->get_items();
					break;

				case 'cart':
					if (is_object(WC()->cart)) {
						$items = WC()->cart->get_cart();
					}
					break;
			}

			return $items;
		}

		/**
		 * Check if sale price product
		 *
		 * @return bool
		 */
		public function check_if_sale_price_product() {
			$return = true;

			if ('yes' != get_option( 'rs_restrict_sale_price_for_redeeming' )) {
				return $return;
			}

			if (!srp_check_is_array($this->get_items())) {
				return $return;
			}

			foreach ( $this->get_items() as $item ) {
				$product_id  = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$product_obj = srp_product_object( $product_id );
				$sale_price  = is_object( $product_obj ) ? $product_obj->get_sale_price() : '';
				if ( ! empty( $sale_price ) ) {
					return false;
				}
			}

			return $return;
		}

		/**
		 * Validate Minimum Available Points Restriction
		 *
		 * @return bool
		 */
		public function validate_minimum_points() {
			$return = true;

			if ( $this->get_minimum_available_points() && ( $this->available_points < $this->get_minimum_available_points() ) ) {
				$return = false;
			}

			return $return;
		}

		/**
		 * Get Minimum Available Points
		 *
		 * @return float
		 */
		public function get_minimum_available_points() {
			$minimum_available_points = 0;
			if ( 'yes' != get_option( 'rs_minimum_available_points_restriction_is_enabled', 'no' ) ) {
				return $minimum_available_points;
			}

			if ( '1' == get_option( 'rs_minimum_available_points_based_on', '1' ) ) {
				$minimum_available_points = get_option( 'rs_available_points_based_redeem', '0' );
			} else {
				$user_roles = get_user_by( 'id', $this->user_id )->roles;
				if ( ! srp_check_is_array( $user_roles ) ) {
					return $minimum_available_points;
				}

				$minimum_points_based_on_roles = array();
				foreach ( $user_roles as $role ) {
					$minimum_points_based_on_roles[] = get_option( 'rs_minimum_available_points_to_redeem_for_' . $role, '0' );
				}

				$minimum_available_points = srp_check_is_array( $minimum_points_based_on_roles ) ? max( $minimum_points_based_on_roles ) : 0;
			}

			return (float) $minimum_available_points;
		}

		/**
		 * Validate Maximum Points Restriction per day.
		 *
		 * @return bool
		 */
		public function validate_maximum_points_per_day() {
			$return = true;

			if ( ! rs_check_maximum_points_restriction_per_day( $this->user_id, $this->redeemed_points ) ) {
				$return = false;
			}

			return $return;
		}
	}
}
