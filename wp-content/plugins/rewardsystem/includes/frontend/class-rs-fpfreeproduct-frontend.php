<?php
/**
 * Free Product Functionality.
 *
 * @package Rewardsystem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'FPRewardSystem_Free_Product' ) ) {

	/**
	 * Class Initialization.
	 */
	class FPRewardSystem_Free_Product {

		/**
		 * Rules.
		 *
		 * @var array
		 */
		private static $rules;

		/**
		 * Banning Type.
		 *
		 * @var string
		 */
		private static $banning_type;

		/**
		 * Points Data.
		 *
		 * @var object
		 */
		private static $points_data;

		/**
		 * Add Hooks.
		 */
		public static function init() {
			self::$rules = get_option( 'rewards_dynamic_rule' );

			add_action( 'wp_head', array( __CLASS__, 'add_free_product' ) );

			add_action( 'wp_head', array( __CLASS__, 'add_bonus_points_for_member_level' ) );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'save_data_to_order' ) );

			add_filter( 'woocommerce_order_item_name', array( __CLASS__, 'show_msg_in_order_details' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_name', array( __CLASS__, 'show_message_next_to_free_product' ), 10, 3 );

			add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'display_free_product_after_cart_table' ), 999 );

			add_action( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'alter_price_for_free_product' ) );

			add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'custom_price' ), 10, 3 );

			if ( '1' === get_option( 'rs_free_product_add_quantity' ) ) {

				add_filter( 'woocommerce_cart_item_quantity', array( __CLASS__, 'alter_quantity_for_free_product' ), 10, 2 );

				add_filter( 'woocommerce_is_sold_individually', array( __CLASS__, 'free_product_as_sold_individually' ), 10, 2 );
			}

			add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'add_to_cart_validation' ), 10, 5 );

			$failed_statuses = array( 'cancelled', 'failed', 'refunded' );
			foreach ( $failed_statuses as $failed_status ) {
				$success_statuses = array( 'pending ', 'processing', 'on-hold', 'completed' );
				foreach ( $success_statuses as $success_status ) {
					add_action( 'woocommerce_order_status_' . $success_status . '_to_' . $failed_status, array( __CLASS__, 'unset_free_product_from_order' ) );
				}
			}

			$success_statuses = array( 'pending ', 'processing', 'on-hold', 'completed' );
			foreach ( $success_statuses as $success_status ) {
				$failed_statuses = array( 'cancelled', 'failed', 'refunded' );
				foreach ( $failed_statuses as $failed_status ) {
					add_action( 'woocommerce_order_status_' . $failed_status . '_to_' . $success_status, array( __CLASS__, 'set_free_product_in_order' ) );
				}
			}

			// For newer version of Woocommerce (i.e) Version > 3.7.0.
			add_action( 'woocommerce_remove_cart_item', array( __CLASS__, 'unset_removed_products' ), 10, 1 );

			// For older version of Woocommerce (i.e) Version < 2.3.0.
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( __CLASS__, 'unset_removed_products' ), 10, 1 );

			// For newer version of Woocommerce (i.e) Version > 2.3.0.
			add_action( 'woocommerce_cart_item_removed', array( __CLASS__, 'unset_removed_products' ), 10, 1 );
		}

		/**
		 * Get banning type.
		 * */
		public static function get_banning_type() {
			if ( isset( self::$banning_type ) ) {
				return self::$banning_type;
			}

			self::$banning_type = check_banning_type( get_current_user_id() );

			return self::$banning_type;
		}

		/**
		 * Get Points data.
		 * */
		public static function get_points_data() {
			if ( isset( self::$points_data ) ) {
				return self::$points_data;
			}

			self::$points_data = new RS_Points_Data( get_current_user_id() );

			return self::$points_data;
		}

		/**
		 * Add Free Product To Cart
		 * */
		public static function add_free_product() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'earningonly' === self::get_banning_type() || 'both' === self::get_banning_type() ) {
				return;
			}

			if ( '1' === get_option( 'rs_free_product_add_by_user_or_admin' ) && ! is_cart() ) {
				return;
			}

			$user_id             = get_current_user_id();
			$total_earned_points = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			if ( empty( $total_earned_points ) ) {
				return;
			}

			$level_id          = rs_get_earning_and_redeeming_level_id( $total_earned_points, 'earning' );
			$free_product_type = self::free_product_type( $level_id );
			if ( '2' === $free_product_type ) {
				return;
			}

			$free_product_list = self::free_product_list( $level_id );
			if ( ! srp_check_is_array( $free_product_list ) ) {
				return;
			}

			$product_with_price    = array();
			$product_without_price = array();
			$main_variations       = array();
			$cart_item_key         = array();

			$purchase_product_list = get_user_meta( $user_id, 'product_id_for_free_product', true );
			$purchase_product_list = srp_check_is_array( $purchase_product_list ) ? $purchase_product_list : array();

			foreach ( $free_product_list as $product_id ) {
				if ( empty( $product_id ) ) {
					continue;
				}

				if ( ! check_if_user_already_purchase( $product_id, $level_id, $purchase_product_list ) ) {
					continue;
				}

				$removed_product_list = (array) get_user_meta( $user_id, 'listsetofids', true );
				$product_obj          = srp_product_object( $product_id );
				$variation_id         = ( is_object( $product_obj ) && 'simple' === srp_product_type( $product_id ) ) ? 0 : (int) $product_id;
				$parent_id            = get_post_parent( $product_obj );
				if ( $parent_id > 0 ) {
					$parent_productid  = $parent_id;
					$variation_obj     = new WC_Product_Variation( $product_id );
					$variations        = wc_get_formatted_variation( $variation_obj->get_variation_attributes(), true );
					$explode_variation = explode( ',', $variations );
					foreach ( $explode_variation as $each_variation ) {
						$explode_each_variation                                       = explode( ': ', $each_variation );
						$main_variations[ 'attribute_' . $explode_each_variation[0] ] = $explode_each_variation[1];
					}
					$get_current_cart_ids = WC()->cart->generate_cart_id( $parent_productid, $variation_id, $main_variations );
					$cart_item_key[]      = $get_current_cart_ids;
				} else {
					$parent_productid     = $product_id;
					$get_current_cart_ids = WC()->cart->generate_cart_id( $parent_productid, $variation_id, $main_variations );
					$cart_item_key[]      = $get_current_cart_ids;
				}
				$get_cart_count = WC()->cart->cart_contents_count;
				foreach ( WC()->cart->cart_contents as $key => $val ) {
					$product_price = $val['line_subtotal'];
					if ( $product_price > 0 ) {
						$product_with_price[] = $product_price;
					} else {
						$product_without_price[] = $product_price;
					}
				}
				$product_count_with_price    = array_sum( $product_with_price );
				$product_count_without_price = array_sum( $product_without_price );
				$found_or_not                = self::check_if_free_product_exist( $product_id );
				if ( ! in_array( $get_current_cart_ids, $removed_product_list ) ) {
					if ( ( $product_count_with_price > 0 ) ) {
						if ( ! $found_or_not ) {
							if ( ( $get_cart_count > 0 ) ) {
								if ( '1' === get_option( 'rs_free_product_add_by_user_or_admin' ) ) {
									WC()->cart->add_to_cart( $parent_productid, 1, $variation_id, $main_variations );
									self::set_price_for_free_product();
								}
							}
							WC()->session->set( 'setruleids', $level_id );
							WC()->session->set( 'excludedummyids', $level_id );
							WC()->session->set( 'dynamicruleproducts', $free_product_list );
						}
					} elseif ( '1' === $product_count_without_price ) {
							WC()->cart->remove_cart_item( $get_current_cart_ids );
					} else {
						foreach ( $cart_item_key as $list_of_cart_item_key ) {
							if ( ! in_array( $list_of_cart_item_key, $removed_product_list ) ) {
								WC()->cart->remove_cart_item( $list_of_cart_item_key );
							}
						}
					}
				}
				WC()->session->set( 'freeproductcartitemkeys', $cart_item_key );
			}
		}

		/**
		 * Set Price for Free Product.
		 * */
		public static function set_price_for_free_product() {
			$points       = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			$rule_id      = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
			$product_list = self::free_product_list( $rule_id );
			if ( ! srp_check_is_array( $product_list ) ) {
				return;
			}

			$purchased_product_list = get_user_meta( get_current_user_id(), 'product_id_for_free_product', true );
			$purchased_product_list = srp_check_is_array( $purchased_product_list ) ? $purchased_product_list : array();

			foreach ( WC()->cart->cart_contents as $item ) {
				$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				if ( ! in_array( $product_id, (array) $product_list ) ) {
					continue;
				}

				if ( ! check_if_user_already_purchase( $product_id, $rule_id, $purchased_product_list ) ) {
					continue;
				}

				$item['data']->set_price( 0 );
			}
		}

		/**
		 * Validate Cart.
		 *
		 * @param bool  $passed Bool Value.
		 * @param int   $product_id Product Id.
		 * @param int   $qty Quantity of the product.
		 * @param int   $variation_id Variation Id.
		 * @param array $variations Variations.
		 * */
		public static function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array() ) {
			$allowed_product = self::free_product_list_in_cart( get_current_user_id() );
			$product_id      = ! empty( $variation_id ) ? $variation_id : $product_id;
			if ( isset( $allowed_product[ $product_id ] ) ) {
				if ( '1' === get_option( 'rs_free_product_add_quantity' ) ) {
					wc_add_notice( __( 'This product already is in the cart.', 'rewardsystem' ), 'error' );
					return false;
				}
			} else {
				$removed_list = self::is_free_product( $product_id );
				if ( $removed_list ) {
					delete_user_meta( get_current_user_id(), 'listsetofids' );
				}
			}

			return $passed;
		}

		/**
		 * Check if free product already exists in cart.
		 *
		 * @param bool $user_id User ID.
		 * */
		public static function free_product_list_in_cart( $user_id ) {
			$free_product_list = array();
			foreach ( WC()->cart->cart_contents as $key => $values ) {
				$product_id       = ! empty( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'];
				$allowed_products = self::is_free_product( $product_id );
				if ( $allowed_products ) {
					$free_product_list[ $product_id ] = $allowed_products;
				}
			}

			return $free_product_list;
		}

		/**
		 * Check if free product exists in free product list.
		 *
		 * @param int $product_id Product Id.
		 * */
		public static function is_free_product( $product_id ) {
			$total_earned_points = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			$user_level          = rs_get_earning_and_redeeming_level_id( $total_earned_points, 'earning' );
			$free_product_list   = self::free_product_list( $user_level );
			if ( in_array( $product_id, $free_product_list ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Add Bonus Points for Member Level
		 * */
		public static function add_bonus_points_for_member_level() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'no' === get_option( 'rs_enable_earned_level_based_reward_points', 'no' ) ) {
				return;
			}

			$user_id = get_current_user_id();
			if ( 'earningonly' === self::get_banning_type() || 'both' === self::get_banning_type() ) {
				return;
			}

			$points = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			if ( ! $points ) {
				return;
			}

			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}

			$reached_rule_id = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
			if ( empty( $reached_rule_id ) ) {
				return;
			}

			$type = isset( self::$rules[ $reached_rule_id ]['type'] ) ? self::$rules[ $reached_rule_id ]['type'] : '1';
			if ( '1' === $type ) {
				return;
			}

			$bonus_points = isset( self::$rules[ $reached_rule_id ]['bounspoints'] ) ? self::$rules[ $reached_rule_id ]['bounspoints'] : '';
			if ( ! $bonus_points ) {
				return;
			}

			$rule_ids = get_user_meta( $user_id, 'rs_rule_ids_based_on_bouns_points', true );
			if ( srp_check_is_array( $rule_ids ) && in_array( $reached_rule_id, $rule_ids ) ) {
				return;
			}

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $bonus_points, 0, 'MLBP', $user_id, '', '', '', '', '' );
			} else {
				$values_to_insert = array(
					'pointstoinsert'    => $bonus_points,
					'event_slug'        => 'MLBP', /* Member Level Bonus Points */
					'user_id'           => $user_id,
					'totalearnedpoints' => $bonus_points,
				);
				$new_obj->total_points_management( $values_to_insert, false );
			}

			self::send_mail_for_admin_bonus_points( $reached_rule_id );
			self::send_mail_for_user_bonus_points( $reached_rule_id );

			if ( srp_check_is_array( $rule_ids ) ) {
				$data = array_merge( $rule_ids, array( $reached_rule_id ) );
				update_user_meta( $user_id, 'rs_rule_ids_based_on_bouns_points', array_unique( $data ) );
			} else {
				update_user_meta( $user_id, 'rs_rule_ids_based_on_bouns_points', array_filter( array( $reached_rule_id ) ) );
			}
		}

		/**
		 * Update purchased free product list for User.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function set_free_product_in_order( $order_id ) {
			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}

			$order   = wc_get_order( $order_id );
			$user_id = $order->get_user_id();

			$previous_ids = array();

			$free_product_id = get_user_meta( $user_id, 'product_id_for_free_product', true );
			$free_product_id = srp_check_is_array( $free_product_id ) ? $free_product_id : array();

			foreach ( self::$rules as $rule_id => $rule ) {
				foreach ( $order->get_items() as $item_id => $each_item ) {
					$product_id = ! empty( $each_item['variation_id'] ) ? $each_item['variation_id'] : $each_item['product_id'];
					if ( ! in_array( $product_id, (array) $rule['product_list'] ) ) {
						continue;
					}

					if ( srp_check_is_array( $free_product_id ) ) {
						if ( count( $free_product_id ) === count( $free_product_id, 1 ) ) {
							$previous_ids[ $rule_id ] = $free_product_id;
							$free_product_id          = $previous_ids;
						} else {
							$free_product_id[ $rule_id ][] = $product_id;
							$free_product_id[ $rule_id ]   = array_unique( $free_product_id[ $rule_id ] );
						}
					} else {
						$free_product_id[ $rule_id ][] = $product_id;
					}

					update_user_meta( $user_id, 'product_id_for_free_product', $free_product_id );
				}
			}
		}

		/**
		 * Update purchased free product list for User when order revised.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function unset_free_product_from_order( $order_id ) {
			$order     = new WC_Order( $order_id );
			$order_obj = srp_order_obj( $order );
			$user_id   = $order_obj['order_userid'];
			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}

			$product_id_in_order = get_user_meta( $user_id, 'product_id_for_free_product', true );
			$product_id_in_order = srp_check_is_array( $product_id_in_order ) ? $product_id_in_order : array();

			foreach ( self::$rules as $rule_id => $rule ) {
				foreach ( $order->get_items() as $item_id => $each_item ) {
					$product_id = ! empty( $each_item['variation_id'] ) ? $each_item['variation_id'] : $each_item['product_id'];
					if ( ! in_array( $product_id, (array) $rule['product_list'] ) ) {
						continue;
					}

					if ( ! isset( $product_id_in_order[ $rule_id ] ) ) {
						continue;
					}

					if ( ! srp_check_is_array( $product_id_in_order[ $rule_id ] ) ) {
						continue;
					}

					if ( ( array_search( $product_id, $product_id_in_order[ $rule_id ] ) === $key ) ) {
						unset( $product_id_in_order[ $rule_id ] );
						update_user_meta( $user_id, 'product_id_for_free_product', $product_id_in_order );
					}
				}
			}
		}

		/**
		 * Check whether free product is sold individually product.
		 *
		 * @param boolean $bool_val Boolean Value.
		 * @param WP_Post $product_obj Product object.
		 */
		public static function free_product_as_sold_individually( $bool_val, $product_obj ) {
			$total_points = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			if ( 0 === $total_points ) {
				return $bool_val;
			}

			$rule_id = rs_get_earning_and_redeeming_level_id( $total_points, 'earning' );
			if ( ! $rule_id ) {
				return $bool_val;
			}

			$product_list = self::free_product_list( $rule_id );
			if ( ! srp_check_is_array( $product_list ) ) {
				return $bool_val;
			}

			$product_id = product_id_from_obj( $product_obj );
			if ( in_array( $product_id, $product_list ) ) {
				return true;
			}

			return $bool_val;
		}

		/**
		 * Free product list.
		 *
		 * @param int $rule_id Rule ID.
		 */
		public static function free_product_list( $rule_id ) {
			$product_list = isset( self::$rules[ $rule_id ]['product_list'] ) ? self::$rules[ $rule_id ]['product_list'] : array();
			return $product_list;
		}

		/**
		 * Free product type.
		 *
		 * @param int $rule_id Rule ID.
		 */
		public static function free_product_type( $rule_id ) {
			$product_type = isset( self::$rules[ $rule_id ]['type'] ) ? self::$rules[ $rule_id ]['type'] : 1;
			return $product_type;
		}

		/**
		 * Check if free product exists.
		 *
		 * @param int $product_id Product ID.
		 */
		public static function check_if_free_product_exist( $product_id ) {
			$allowed_product = self::free_product_list_in_cart( get_current_user_id() );
			if ( isset( $allowed_product[ $product_id ] ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Send notification to admin for free product.
		 *
		 * @param float $points Points.
		 */
		public static function send_mail_for_admin( $points ) {

			if ( 'yes' !== get_option( 'rs_enable_admin_email_for_free_product' ) ) {
				return;
			}

			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}

			$rule_id          = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
			$level_name       = isset( self::$rules[ $rule_id ]['name'] ) ? self::$rules[ $rule_id ]['name'] : '';
			$free_product_ids = self::free_product_list( $rule_id );
			$product_ids      = implode( ',', $free_product_ids );

			$subject = get_option( 'rs_subject_for_free_product_mail_send_admin', 'Free Product - Notification' );
			$msg     = get_option( 'rs_content_for_free_product_mail_send_admin', 'Hi,<br/> Your user has got the product as free for reaching the configured level. Please check the below details,<br/> Username: [username]<br/>Product Name: [product_id]<br/>Level Name: [current_level_name].<br/>Thanks' );
			$message = str_replace( array( '[username] , [current_level_name]', '[product_id]' ), array( get_option( 'woocommerce_email_from_name' ), $level_name, $product_ids ), $msg );

			send_mail( get_option( 'woocommerce_email_from_address' ), $subject, $message );
		}

		/**
		 * Send notification to admin for bonus points.
		 *
		 * @param int $rule_id Rule ID.
		 */
		public static function send_mail_for_admin_bonus_points( $rule_id ) {
			if ( 'yes' !== get_option( 'rs_enable_admin_email_for_bonus_points', 'no' ) ) {
				return;
			}

			$user = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $user ) ) {
				return;
			}

			$user_name = is_object( $user ) ? $user->user_login : 'Guest';

			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}
			$level_name   = isset( self::$rules[ $rule_id ]['name'] ) ? self::$rules[ $rule_id ]['name'] : '';
			$bonus_points = isset( self::$rules[ $rule_id ]['bounspoints'] ) ? self::$rules[ $rule_id ]['bounspoints'] : '';

			$subject = get_option( 'rs_subject_for_bonus_points_admin_email', 'Bouns Points - Notification' );
			$message = get_option( 'rs_message_for_bonus_points_admin_email', 'Hi,<br/><br/> Your user <b>[username]</b> has received <b>[points_value]</b> bonus points for reaching the <b>[level_name]</b> level.<br/><br/>Thanks' );
			$message = str_replace( array( '[username]', '[level_name]', '[points_value]' ), array( $user_name, $level_name, $bonus_points ), $message );

			send_mail( get_option( 'woocommerce_email_from_address' ), $subject, $message );
		}

		/**
		 * Send notification to user for bonus points.
		 *
		 * @param int $rule_id Rule ID.
		 */
		public static function send_mail_for_user_bonus_points( $rule_id ) {

			if ( 'yes' !== get_option( 'rs_enable_user_email_for_bonus_points', 'no' ) ) {
				return;
			}

			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}

			$user_obj = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $user_obj ) ) {
				return;
			}

			$user_name    = is_object( $user_obj ) ? $user_obj->user_login : 'Guest';
			$level_name   = isset( self::$rules[ $rule_id ]['name'] ) ? self::$rules[ $rule_id ]['name'] : '';
			$bonus_points = isset( self::$rules[ $rule_id ]['bounspoints'] ) ? self::$rules[ $rule_id ]['bounspoints'] : '';

			$subject = get_option( 'rs_subject_for_bonus_points_customer_email', 'Bouns Points - Notification' );
			$message = get_option( 'rs_message_for_bonus_points_customer_email', 'Hi [username],<br/><br/> You have received <b>[points_value]</b> bonus point for reaching the [level_name] level.<br/><br/>Thanks' );
			$message = str_replace( array( '[username]', '[level_name]', '[points_value]' ), array( $user_name, $level_name, $bonus_points ), $message );

			send_mail( $user_obj->user_email, $subject, $message );
		}

		/**
		 * Save Free Product data in Order meta.
		 *
		 * @param int $order_id Order ID.
		 */
		public static function save_data_to_order( $order_id ) {
			if ( ! srp_check_is_array( self::$rules ) ) {
				return;
			}

			$order        = wc_get_order( $order_id );
			$order_obj    = srp_order_obj( $order );
			$user_id      = $order_obj['order_userid'];
			$points_data  = new RS_Points_Data( $user_id );
			$total_points = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? $points_data->total_earned_points() : $points_data->total_available_points();
			$rule_id      = rs_get_earning_and_redeeming_level_id( $total_points, 'earning' );
			if ( empty( $rule_id ) ) {
				return;
			}

			$free_product_type = self::free_product_type( $rule_id );
			if ( '2' === $free_product_type ) {
				return;
			}

			$free_product_msg = WC()->session->get( 'freeproductmsg' );

			$order->update_meta_data( 'listruleids', $rule_id );
			$order->update_meta_data( 'ruleidsdata', self::$rules[ $rule_id ] );
			$order->save();

			$product_in_order = get_user_meta( $user_id, 'product_id_for_free_product', true );
			$product_in_order = srp_check_is_array( $product_in_order ) ? $product_in_order : array();
			$previous_ids     = array();

			$send_mail = false;
			foreach ( $order->get_items() as $item_id => $each_item ) {
				$product_id        = ! empty( $each_item['variation_id'] ) ? $each_item['variation_id'] : $each_item['product_id'];
				$free_product_list = self::free_product_list( $rule_id );
				if ( ! in_array( $product_id, (array) $free_product_list ) ) {
					continue;
				}

				if ( srp_check_is_array( $product_in_order ) ) {
					if ( count( $product_in_order ) === count( $product_in_order, 1 ) ) {
						$previous_ids[ $rule_id ] = $product_in_order;
						$product_in_order         = $previous_ids;
					} else {
						$product_in_order[ $rule_id ][] = $product_id;
						$product_in_order[ $rule_id ]   = array_unique( $product_in_order[ $rule_id ] );
					}
				} else {
					$product_in_order[ $rule_id ][] = $product_id;
				}
				$send_mail = true;
				update_user_meta( $user_id, 'product_id_for_free_product', $product_in_order );
				wc_add_order_item_meta( $item_id, '_ruleidsdata', self::$rules[ $rule_id ] );
				wc_add_order_item_meta( $item_id, '_rsfreeproductmsg', $free_product_msg );
			}

			if ( $send_mail ) {
				// Admin Email.
				self::send_mail_for_admin( $total_points );
				// Send Email notification for Free Product to user.
				rs_free_product_notification_for_user( $user_id, $order_id );
			}

			WC()->session->__unset( 'setruleids' );
			WC()->session->__unset( 'freeproductcartitemkeys' );
			WC()->session->__unset( 'freeproductmsg' );
		}

		/**
		 * Alter price for free product.
		 *
		 * @param Object $cart_obj Cart Object.
		 */
		public static function alter_price_for_free_product( $cart_obj ) {
			$points  = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			$rule_id = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
			if ( empty( $rule_id ) ) {
				return;
			}

			$free_product_type = self::free_product_type( $rule_id );
			if ( '2' === $free_product_type ) {
				return;
			}

			$quantity = get_option( 'rs_free_product_quantity' );
			if ( empty( $quantity ) ) {
				return;
			}

			if ( '1' === $quantity ) {
				self::set_price_for_free_product();
			} else {
				$product_list = self::free_product_list( $rule_id );
				if ( ! srp_check_is_array( $product_list ) ) {
					return;
				}

				foreach ( $cart_obj->cart_contents as $key => $value ) {
					$product_id = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
					if ( ! in_array( $product_id, (array) $product_list ) ) {
						continue;
					}

					if ( $value['quantity'] > $quantity ) {
						$price      = $value['data']->get_price();
						$exceed_qty = $value['quantity'] - $quantity;
						$price      = ( $price * $exceed_qty ) / $value['quantity'];
						$value['data']->set_price( $price );
					} else {
						$value['data']->set_price( 0 );
					}
				}
			}
		}

		/**
		 * Custom Price.
		 *
		 * @param float  $price Product Price.
		 * @param Object $item Item.
		 * @param string $key Cart Item Key.
		 */
		public static function custom_price( $price, $item, $key ) {
			if ( '2' === get_option( 'rs_free_product_add_by_user_or_admin' ) ) {
				return $price;
			}

			if ( '1' === get_option( 'rs_free_product_add_quantity' ) ) {
				return $price;
			}

			if ( '' === get_option( 'rs_free_product_quantity' ) ) {
				return $price;
			}

			$product_id  = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$product_obj = wc_get_product( $product_id );

			return wc_price( $product_obj->get_price() );
		}

		/**
		 * Display Free Product after Cart Table.
		 */
		public static function display_free_product_after_cart_table() {
			if ( 'earningonly' === self::get_banning_type() || 'both' === self::get_banning_type() ) {
				return;
			}

			if ( ! allow_reward_points_for_user( get_current_user_id() ) ) {
				return;
			}

			$points = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			if ( empty( $points ) ) {
				return;
			}

			$rule_id = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
			if ( empty( $rule_id ) ) {
				return;
			}

			$free_product_type = self::free_product_type( $rule_id );
			if ( '2' === $free_product_type ) {
				return;
			}

			$product_lists = self::free_product_list( $rule_id );
			if ( ! srp_check_is_array( $product_lists ) ) {
				return;
			}

			$free_products = get_user_meta( get_current_user_id(), 'product_id_for_free_product', true );
			$free_products = srp_check_is_array( $free_products ) ? $free_products : array();
			?>
			<div class='fp_rs_display_free_product'>
				<h3><?php echo esc_html( get_option( 'rs_free_product_msg_caption' ) ); ?></h3>
			<?php
			foreach ( $product_lists as $product_id ) {
				if ( empty( $product_id ) ) {
					continue;
				}

				if ( ! check_if_user_already_purchase( $product_id, $rule_id, $free_products ) ) {
					continue;
				}

				$product_obj  = srp_product_object( $product_id );
				$variation_id = ( is_object( $product_obj ) && 'simple' === srp_product_type( $product_id ) ) ? 0 : (int) $product_id;
				$parent_id    = get_post_parent( $product_obj );
				if ( $parent_id > 0 ) {
					$var_object  = new WC_Product_Variation( $product_id );
					$variations  = wc_get_formatted_variation( $var_object->get_variation_attributes(), true );
					$explode_var = explode( ',', $variations );
					$main_var    = array();
					foreach ( $explode_var as $each_variation ) {
						$explode2 = explode( ': ', $each_variation );

						$main_var[ 'attribute_' . $explode2[0] ] = $explode2[1];
					}
					$cart_item_key = WC()->cart->generate_cart_id( $parent_id, $variation_id, $main_var );
				} else {
					$cart_item_key = WC()->cart->generate_cart_id( $product_id, $variation_id, array() );
				}

				$deleted_keys = get_user_meta( get_current_user_id(), 'listsetofids', true );
				if ( ! in_array( $cart_item_key, (array) $deleted_keys ) ) {
					continue;
				}

				$thumbnail_img = get_the_post_thumbnail_url( $product_id ) ? get_the_post_thumbnail_url( $product_id ) : wc_placeholder_img_src();
				?>
					<a href="javascript:void(0)" class="add_removed_free_product_to_cart" data-cartkey="<?php echo esc_attr( $cart_item_key ); ?>">
						<img src="<?php echo esc_url( $thumbnail_img ); ?>" width="30" height="30"/><?php echo esc_html( get_the_title( $product_id ) ); ?>
					</a><br/>
					<?php
			}
			?>
			</div>
			<?php
		}

		/**
		 * Display message for free product.
		 *
		 * @param string $product_name Product name.
		 * @param Object $cart_item Cart item object.
		 * @param string $cart_item_key Cart Item Key.
		 */
		public static function show_message_next_to_free_product( $product_name, $cart_item, $cart_item_key ) {
			if ( 'earningonly' === self::get_banning_type() || 'both' === self::get_banning_type() ) {
				return $product_name;
			}

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			$points     = ( '1' === get_option( 'rs_select_earn_points_based_on' ) ) ? self::get_points_data()->total_earned_points() : self::get_points_data()->total_available_points();
			$rule_id    = rs_get_earning_and_redeeming_level_id( $points, 'earning' );
			if ( empty( $rule_id ) ) {
				return $product_name;
			}

			$free_product_type = self::free_product_type( $rule_id );
			if ( '2' === $free_product_type ) {
				return $product_name;
			}

			$product_list = self::free_product_list( $rule_id );
			if ( ! srp_check_is_array( $product_list ) ) {
				return $product_name;
			}

			if ( ! in_array( $product_id, $product_list ) ) {
				return $product_name;
			}

			$purchased_products = get_user_meta( get_current_user_id(), 'product_id_for_free_product', true );
			$purchased_products = srp_check_is_array( $purchased_products ) ? $purchased_products : array();

			if ( ! check_if_user_already_purchase( $product_id, $rule_id, $purchased_products ) ) {
				return $product_name;
			}

			if ( 'yes' !== get_option( 'rs_remove_msg_from_cart_order' ) ) {
				return $product_name;
			}

			$msg_info     = ( '1' === get_option( 'rs_free_product_add_quantity' ) ) ? get_option( 'rs_free_product_message_info' ) : str_replace( '[free_product_quantity]', get_option( 'rs_free_product_quantity' ), get_option( 'rs_free_product_quantity_message_info' ) );
			$replaced_msg = str_replace( '[current_level_points]', $points, $msg_info );
			WC()->session->set( 'freeproductmsg', $replaced_msg );
			return $product_name . '<br>' . $replaced_msg;
		}

		/**
		 * Alter Qunatity for free product.
		 *
		 * @param int    $product_quantity Product quantity.
		 * @param string $values Cart item key.
		 */
		public static function alter_quantity_for_free_product( $product_quantity, $values ) {
			$cart_item_keys = ( null === WC()->session->get( 'freeproductcartitemkeys' ) ) ? array() : WC()->session->get( 'freeproductcartitemkeys' );
			if ( ! srp_check_is_array( $cart_item_keys ) ) {
				return $product_quantity;
			}

			if ( ! in_array( $values, $cart_item_keys ) ) {
				return $product_quantity;
			}

			echo wp_kses_post( sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $values ) );
			return $product_quantity;
		}

		/**
		 * Display message in Order details.
		 *
		 * @param string $item_name Product name.
		 * @param Object $item Product object.
		 */
		public static function show_msg_in_order_details( $item_name, $item ) {
			$free_product_msg = $item['rsfreeproductmsg'];
			if ( null === $free_product_msg || empty( $free_product_msg ) ) {
				return $item_name;
			}

			if ( 'yes' === get_option( 'rs_remove_msg_from_cart_order' ) ) {
				return $item_name . '<br>' . $free_product_msg;
			}

			return $item_name;
		}

		/**
		 * Remove product ids from user meta.
		 *
		 * @param string $cart_item_key Cart Item Key.
		 */
		public static function unset_removed_products( $cart_item_key ) {
			$cart_item_list = (array) get_user_meta( get_current_user_id(), 'listsetofids', true );
			$merged_data    = array_unique( array_filter( array_merge( $cart_item_list, (array) $cart_item_key ) ) );
			update_user_meta( get_current_user_id(), 'listsetofids', $merged_data );
		}
	}

	FPRewardSystem_Free_Product::init();
}
