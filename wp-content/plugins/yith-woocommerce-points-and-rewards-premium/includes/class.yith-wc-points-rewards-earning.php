<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards
 *
 * @class   YYITH_WC_Points_Rewards_Earning
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Earning' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Earning
	 */
	class YITH_WC_Points_Rewards_Earning {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Earning
		 */
		protected static $instance;

		/**
		 * Single instance of the class
		 *
		 * @var bool
		 */
		protected $points_applied = false;

		/**
		 * Check if the fix has been done during process
		 *
		 * @var bool
		 */
		protected $points_expired_check = false;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Earning
		 * @since 1.0.0
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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			if ( ! YITH_WC_Points_Rewards()->is_enabled() ) {
				return;
			}

			if ( apply_filters( 'ywpar_enable_points_upon_sales', YITH_WC_Points_Rewards()->get_option( 'enable_points_upon_sales', 'yes' ) == 'yes' ) ) {
				// add point when
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_points_earned_from_cart' ) );
				add_action( 'init', array( $this, 'init' ) );
			}
		}


		/**
		 *  Start the game for earn point with order and extra points.
		 *
		 *  Triggered by 'init' hook.
		 *
		 * @since  1.6.0
		 * @author Emanuela Castorina
		 */
		public function init() {

			$status_to_earn = YITH_WC_Points_Rewards()->get_option( 'order_status_to_earn_points' );

			if ( $status_to_earn ) {
				foreach ( $status_to_earn as $hook ) {
					add_action( $hook, array( $this, 'add_order_points' ), 5 );
				}
			}

			// remove point when the order is refunded or cancelled
			if ( YITH_WC_Points_Rewards()->get_option( 'remove_point_order_deleted' ) == 'yes' ) {
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'remove_order_points' ) );
				add_action( 'woocommerce_order_status_cancelled_to_completed', array( $this, 'add_order_points_after_order_status' ), 11 );
				add_action( 'woocommerce_order_status_cancelled_to_processing', array( $this, 'add_order_points_after_order_status' ), 11 );
			}

			if ( YITH_WC_Points_Rewards()->get_option( 'remove_point_refund_order' ) == 'yes' ) {
				add_action( 'woocommerce_order_partially_refunded', array( $this, 'remove_order_points_refund' ), 11, 2 );
				add_action( 'woocommerce_order_fully_refunded', array( $this, 'remove_order_points_refund' ), 11, 2 );
				add_action( 'wp_ajax_nopriv_woocommerce_delete_refund', array( $this, 'refund_delete' ), 9, 2 );
				add_action( 'wp_ajax_woocommerce_delete_refund', array( $this, 'refund_delete' ), 9, 2 );
			}

			if ( class_exists( 'YITH_WooCommerce_Advanced_Reviews' ) && defined( 'YITH_YWAR_PREMIUM' ) ) {
				add_action( 'ywar_review_approve_status_changed', array( $this, 'add_order_points_with_advanced_reviews' ), 10, 2 );
			} else {
				add_action( 'comment_post', array( $this, 'add_order_points_with_review' ), 10, 2 );
				add_action( 'wp_set_comment_status', array( $this, 'add_order_points_with_review' ), 10, 2 );
			}

			// extra-point to registration.
			add_action( 'user_register', array( $this, 'extrapoints_to_new_customer' ), 10 );

			// extrapoint birthday.
			if ( YITH_WC_Points_Rewards()->get_option( 'enable_points_on_birthday_exp' ) == 'yes' ) {
				add_action( 'ywpar_cron_birthday', array( $this, 'extra_points_birthdate' ), 10 );
			}

			// add points for previous orders to a new registered user.
			if ( 'yes' === YITH_WC_Points_Rewards()->get_option( 'assign_older_orders_points_to_new_registered_user' ) ) {
				add_action( 'user_register', array( $this, 'add_points_for_previous_orders_on_registration' ) );
			}
		}

		/**
		 * Assign Points to previous orders on user registration by user email = billing email.
		 *
		 * @since  1.7.3
		 * @param int $user_id user id.
		 * @author Armando Liccardo
		 */
		public function add_points_for_previous_orders_on_registration( $user_id ) {
			// getting the user.
			$u       = get_user_by( 'id', $user_id );
			$u_email = $u->user_email;

			$orders_query = array(
				'customer' => $u_email,
				'status'   => 'completed',
				'limit'    => -1,
				'orderby'  => 'date',
				'order'    => 'DESC',
			);

			if ( $this->ywpat_get_start_date() ) {
				$orders_query['date_completed'] = '>=' . $this->ywpat_get_start_date();
			}

			$eventual_orders = wc_get_orders( $orders_query );

			$total_points_to_add = 0;
			$points_to_add       = '';
			if ( $eventual_orders ) {
				foreach ( $eventual_orders as $o ) {
					// avoid to count the Vendor suborder.
					if ( $o->get_created_via() === 'yith_wcmv_vendor_suborder' ) {
						continue;
					}
					if ( ! empty( trim( $o->get_meta( 'ywpar_points_from_cart' ) ) ) ) {
						$total_points_to_add += $o->get_meta( 'ywpar_points_from_cart' );
					} else {
						$total         = $o->get_total();
						$points_to_add = YITH_WC_Points_Rewards_Earning()->get_point_earned_from_price( $total, false, '' );

						if ( $points_to_add ) {
							if ( get_option( 'ywpar_points_round_type', 'down' ) == 'down' || apply_filters( 'ywpar_floor_points', false ) ) {
								$points_to_add = floor( $points_to_add );

							} else {
								$points_to_add = ceil( $points_to_add );
							}
						}
						$total_points_to_add += $points_to_add;
					}
				}
			}

			if ( $total_points_to_add > 0 ) {

				YITH_WC_Points_Rewards()->add_point_to_customer( $user_id, $total_points_to_add, 'admin_action', __( 'Added Points from previous not registered orders', 'yith-woocommerce-points-and-rewards' ) );
			}

		}

		/**
		 * Get date of first log creation.
		 *
		 * @since  1.7.3
		 * @author Armando Liccardo
		 */
		function ywpat_get_start_date() {
			global $wpdb;

			$table_name = $wpdb->prefix . 'yith_ywpar_points_log';
			$query      = "SELECT date_earning FROM  $table_name ORDER BY date_earning ASC LIMIT 1";

			$db_v = $wpdb->get_var( $query );

			if ( $db_v ) {
				$db_v = explode( ' ', $db_v );
			}

			$s_date = isset( $db_v[0] ) ? $db_v[0] : '';

			// APPLY_FILTER : ywpar_get_start_date_filter: change or disable the logger start date
			return apply_filters( 'ywpar_get_start_date_filter', $s_date );

		}


		/**
		 * Assign pointson birthdate
		 *
		 * @since  1.6.0
		 * @author Emanuela Castorina
		 */
		public function extra_points_birthdate() {
			global $wpdb;

			$user_query = $wpdb->get_col( "SELECT user_id FROM {$wpdb->base_prefix}usermeta WHERE ( meta_key = 'ywces_birthday' OR  meta_key = 'yith_birthday' ) AND MONTH(meta_value) = MONTH(NOW()) AND DAY(meta_value) = DAY(NOW())" );

			if ( ! empty( $user_query ) ) {
				foreach ( $user_query as $user_id ) {
					$last_points = get_user_meta( $user_id, 'ywpar_last_birthday_points', true );
					if ( YITH_WC_Points_Rewards()->is_user_enabled( 'earn', $user_id ) && $last_points != date( 'Y' ) ) {
						$this->extra_points( array( 'birthday', 'points' ), $user_id, $order_id = 0 );
						update_user_meta( $user_id, 'ywpar_last_birthday_points', date( 'Y' ) );
					}
				}
			}

		}

		/**
		 * Calculate approx the points of product inside an order.
		 *
		 * @param integer               $product_id
		 * @param bool                  $integer
		 * @param WC_Order_Item_Product $order_item
		 * @param string                $currency
		 *
		 * @return int
		 */
		public function calculate_product_points_in_order( $product_id, $integer = true, $order_item, $currency ) {

			$product = wc_get_product( $product_id );

			$line_price        = $order_item->get_total() / $order_item->get_quantity();
			$points_from_price = $this->get_point_earned_from_price( $line_price, $integer, $currency );

			$points_from_product = false;
			if ( $product instanceof WC_Product ) {
				$points_from_product = $this->calculate_product_points( $product, $currency );
			}

			if ( $points_from_product !== false ) {
				$points = apply_filters( 'ywpar_get_calculate_product_points_in_order', min( $points_from_price, $points_from_product ), $points_from_price, $points_from_product, $product_id, $integer, $order_item );
			} else {
				$points = $points_from_price;
			}

			return $points;
		}

		/**
		 * Calculate the points of a product/variation for a single item
		 *
		 * @param WC_Product|int $product
		 * @param bool           $integer
		 *
		 * @param string         $currency
		 *
		 * @return int $points
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function calculate_product_points( $product, $integer = true, $currency = '' ) {

			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! is_object( $product ) ) {
				return 0;
			}

			if ( apply_filters( 'ywpar_exclude_product_on_sale', 'yes' == YITH_WC_Points_Rewards()->get_option( 'exclude_product_on_sale' ) && $product->is_on_sale(), $product ) ) {
				return 0;
			}

			if ( $product->is_type( 'variable' ) ) {
				/**
				 * @var $product WC_Product_Variable
				 */
				return $this->calculate_product_points_on_variable( $product, $integer );
			}

			if ( $product->is_type( 'grouped' ) ) {
				$grouped_points = 0;

				foreach ( $product->get_children() as $child_id ) {
					$child          = wc_get_product( $child_id );
					$grouped_points += $this->calculate_product_points( $child, $integer, $currency );
				}

				return $grouped_points;
			}

			$points_updated = false;
			$points         = 0;

			$product_id              = $product->get_id();
			$main_id                 = yit_get_base_product_id( $product );
			$point_earned            = yit_get_prop( $product, '_ywpar_point_earned', true );
			$point_earned_dates_from = yit_get_prop( $product, '_ywpar_point_earned_dates_from', true );
			$point_earned_dates_to   = yit_get_prop( $product, '_ywpar_point_earned_dates_to', true );

			if ( $point_earned != '' && $this->is_ondate( $point_earned_dates_from, $point_earned_dates_to ) ) {

				$is_percent = strpos( $point_earned, '%' );

				if ( $is_percent !== false ) {
					$point_earned = str_replace( '%', '', $point_earned );
					$points       = $this->get_point_earned( $product, 'product', false, $currency ) * $point_earned / 100;
				} else {
					$points = $point_earned;
				}

				$points_updated = true;
			}
			if ( ! $points_updated ) {
				if ( $product->is_type( 'variation' ) ) {
					$categories = get_the_terms( $main_id, 'product_cat' );
				} else {
					$categories = get_the_terms( $product_id, 'product_cat' );
				}

				if ( ! empty( $categories ) ) {
					$points = 0; // reset the global point

					foreach ( $categories as $term ) {
						$point_earned            = get_term_meta( $term->term_id, 'point_earned', true );
						$point_earned_dates_from = get_term_meta( $term->term_id, 'point_earned_dates_from', true );
						$point_earned_dates_to   = get_term_meta( $term->term_id, 'point_earned_dates_to', true );

						if ( $point_earned != '' && $this->is_ondate( $point_earned_dates_from, $point_earned_dates_to ) ) {

							$is_percent = strpos( $point_earned, '%' );
							if ( $is_percent !== false ) {
								$point_earned   = str_replace( '%', '', $point_earned );
								$current_points = $this->get_point_earned( $product, 'product', false ) * $point_earned / 100;
							} else {
								$current_points = $point_earned;
							}

							$points         = ( $current_points > $points ) ? $current_points : $points;
							$points_updated = true;
						}
					}

					list( $points, $points_updated ) = apply_filters( 'ywpar_points_earned_in_category', array( $points, $points_updated ), $product );
				}
			}
			if ( ! $points_updated ) {

				$points = $this->get_point_earned( $product, 'product', false );
			}

			$points = apply_filters( 'ywpar_get_product_point_round', $points );
			if ( $integer ) {
				if ( get_option( 'ywpar_points_round_type', 'down' ) == 'down' || apply_filters( 'ywpar_floor_points', false ) ) {
					$points = floor( $points );

				} else {
					$points = ceil( $points );
				}
			}

			// Let third party plugin to change the points earned for this product
			return apply_filters( 'ywpar_get_product_point_earned', $points, $product );
		}


		/**
		 * Calculate the points of a product variable for a single item
		 *
		 * @param      $product WC_Product_Variable|int
		 * @param bool $integer
		 *
		 * @return int $points
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function calculate_product_points_on_variable( $product, $integer = true ) {

			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! is_object( $product ) ) {
				return 0;
			}

			if ( ! $product->is_type( 'variable' ) ) {
				return 0;
			}

			$variations = $product->get_available_variations();
			$points     = array();
			if ( ! empty( $variations ) ) {
				foreach ( $variations as $variation ) {
					$points[] = $this->calculate_product_points( $variation['variation_id'] );
				}
			}

			$points = array_unique( $points );

			if ( count( $points ) == 0 ) {
				$return = 0;
			} elseif ( count( $points ) == 1 ) {
				$return = $points[0];
			} else {
				$return = min( $points ) . '-' . max( $points );
			}

			return apply_filters( 'ywpar_calculate_product_points_on_variable', $return, $product );
		}


		/**
		 * Calculate the total points in the carts
		 *
		 * @param bool $integer
		 *
		 * @return int $points
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function calculate_points_on_cart( $integer = true ) {

			$items = WC()->cart->get_cart();

			$tot_points = 0;
			foreach ( $items as $item => $values ) {

				$product_point = 0;
				if ( apply_filters( 'ywpar_calculate_points_for_product', true, $values, $item ) ) {
					$product_point = $this->calculate_product_points( $values['data'], false );
				}
				$total_product_points = floatval( $product_point * $values['quantity'] );
				if ( WC()->cart->applied_coupons && YITH_WC_Points_Rewards()->get_option( 'remove_points_coupon' ) == 'yes' && isset( WC()->cart->discount_cart ) && WC()->cart->discount_cart > 0 ) {
					if ( $values['line_subtotal'] ) {
						$total_product_points = ( $values['line_total'] / $values['line_subtotal'] ) * $total_product_points;
					}
				}

				$tot_points += $total_product_points;
			}

			$tot_points = ( $tot_points < 0 ) ? 0 : $tot_points;

			if ( $integer ) {
				if ( get_option( 'ywpar_points_round_type', 'down' ) == 'down' || apply_filters( 'ywpar_floor_points', false ) ) {
					$tot_points = floor( $tot_points );
				} else {
					$tot_points = round( $tot_points );
				}
			}

			return apply_filters( 'ywpar_calculate_points_on_cart', $tot_points );
		}


		/**
		 * Save the points that are in the cart in a post meta of the order
		 *
		 * @param int $order_id
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.5.0
		 */
		public function save_points_earned_from_cart( $order_id ) {
			$points_from_cart = $this->calculate_points_on_cart();
			$order            = wc_get_order( $order_id );
			yit_save_prop( $order, 'ywpar_points_from_cart', $points_from_cart );
		}

		/**
		 * Check the validate on an interval of date
		 *
		 * @param $datefrom
		 * @param $dateto
		 *
		 * @return int $points
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function is_ondate( $datefrom, $dateto ) {
			$now = time();

			if ( $datefrom == '' && $dateto == '' ) {
				return true;
			}

			// fix the $dateto
			$dateto += ( 24 * 60 * 60 ) - 1;

			if ( $datefrom == '' && $dateto != '' && $now <= $dateto ) {
				return true;
			}

			if ( $dateto == '' && $datefrom != '' && $now >= $datefrom ) {
				return true;
			}

			$ondate = ( ( $datefrom != '' && $now >= $datefrom ) && ( $dateto != '' && $now <= $dateto ) ) ? true : false;

			return $ondate;
		}

		/**
		 * Add points to the order from order_id
		 *
		 * @param $order_id
		 *
		 * @return void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function add_order_points( $order_id ) {

			$order = wc_get_order( $order_id );

			$customer_user = $order->get_customer_id();

			if ( $customer_user == 0 && YITH_WC_Points_Rewards()->get_option( 'assign_points_to_registered_guest', 'no' ) == 'yes' ) {
				$customer_user = get_user_by( 'email', $order->get_billing_email() );
				$customer_user = $customer_user ? $customer_user->ID : false;
			}

			$is_redeeming = false;
			if ( get_option( 'ywpar_disable_earning_while_reedeming', 'no' ) == 'yes' ) {
				$coupons = $order->get_coupon_codes();
				foreach ( $coupons as $i => $c ) {
					if ( strpos( $c, 'ywpar_' ) >= 0 ) {
						$is_redeeming = true;
					}
				}
			}

			if ( ! YITH_WC_Points_Rewards()->is_user_enabled( 'earn', $customer_user ) || $is_redeeming || apply_filters( 'ywpar_add_order_points', false, $order_id ) || ! $customer_user ) {
				return;
			}

			$is_set = yit_get_prop( $order, '_ywpar_points_earned', true );

			// return if the points are just calculated
			if ( is_array( $this->points_applied ) && in_array( $order_id, $this->points_applied ) || $is_set != '' ) {
				return;
			}

			$currency   = yit_get_prop( $order, 'currency' );
			$tot_points = yit_get_prop( $order, 'ywpar_points_from_cart', true );

			// this is necessary for old orders
			if ( $tot_points === '' ) {
				$tot_points  = 0;
				$order_items = $order->get_items();

				if ( ! empty( $order_items ) ) {
					foreach ( $order_items as $order_item ) {
						$product_id  = ( $order_item['variation_id'] != 0 && $order_item['variation_id'] != '' ) ? $order_item['variation_id'] : $order_item['product_id'];
						$item_points = $this->calculate_product_points_in_order( $product_id, false, $order_item, $currency );
						$tot_points  += $item_points * $order_item['qty'];
					}
				}

				$coupons = $order->get_coupon_codes();
				if ( sizeof( $coupons ) > 0 && YITH_WC_Points_Rewards()->get_option( 'remove_points_coupon' ) == 'yes' ) {
					$remove_points     = 0;
					$conversion_points = $this->get_conversion_option( $currency, $order );
					if ( $order->get_total_discount() ) {
						if ( $conversion_points['money'] * $conversion_points['points'] != 0 ) {
							$remove_points += $order->get_total_discount() / $conversion_points['money'] * $conversion_points['points'];
						}
					}

					$tot_points -= $remove_points;
				}

				$tot_points = ( $tot_points < 0 ) ? 0 : round( $tot_points );
			}

			// update order meta and add note to the order
			yit_save_prop(
				$order,
				array(
					'_ywpar_points_earned'     => $tot_points,
					'_ywpar_conversion_points' => $this->get_conversion_option( $currency, $order ),
				)
			);

			$this->points_applied[] = $order_id;
			$order->add_order_note( sprintf( _x( 'Customer earned %1$d %2$s for this purchase.', 'First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), $tot_points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ) ), 0 );

			if ( $customer_user > 0 ) {
				YITH_WC_Points_Rewards()->add_point_to_customer( $customer_user, $tot_points, 'order_completed', '', $order_id );
				$this->extra_points( array( 'num_of_orders', 'amount_spent', 'checkout_threshold' ), $customer_user, $order_id );
			}

		}

		/**
		 * Remove points to the order from order_id
		 *
		 * @param $order_id
		 *
		 * @return void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function remove_order_points( $order_id ) {

			$order        = wc_get_order( $order_id );
			$point_earned = yit_get_prop( $order, '_ywpar_points_earned', true );

			if ( $point_earned == '' ) {
				return;
			}

			$customer_user = $order->get_customer_id();
			$points        = $point_earned;
			$action        = 'order_' . $order->get_status();

			if ( $customer_user > 0 ) {
				yit_save_prop( $order, '_ywpar_points_earned', '' );
				YITH_WC_Points_Rewards()->add_point_to_customer( $customer_user, -$points, $action, '', $order_id );
				$order->add_order_note( sprintf( _x( 'Removed %1$d %2$s for order %3$s.', 'First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), -$points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ), YITH_WC_Points_Rewards()->get_action_label( $action ) ), 0 );
			}

		}


		/**
		 * Add point to the order if the refund is deleted
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function refund_delete() {
			check_ajax_referer( 'order-item', 'security' );

			$refund_id = absint( $_POST['refund_id'] );

			if ( $refund_id && 'shop_order_refund' === get_post_type( $refund_id ) ) {
				$order_id = wp_get_post_parent_id( $refund_id );
			}

			$point_earned = get_post_meta( $order_id, '_ywpar_points_earned', true );

			if ( $point_earned == '' ) {
				return;
			}

			$order                = wc_get_order( $order_id );
			$order_subtotal       = $order->get_subtotal();
			$user_id              = $order->get_user_id();
			$currency             = $order->get_currency();
			$refund_obj           = new WC_Order_Refund( $refund_id );
			$refund_amount        = $refund_obj->get_amount();
			$order_shipping_total = $refund_obj->get_shipping_total();

			if ( $refund_amount > 0 ) {

				if ( $refund_amount > $order_subtotal ) {
					// shipping must be removed from
					$refund_amount = $refund_amount - $order_shipping_total;
				}

				$conversion_points = yit_get_prop( $order, '_ywpar_conversion_points', true );
				if ( $conversion_points == '' ) {
					$conversion_points = $this->get_conversion_option( $currency, $order );
				}
				$points = round( $refund_amount / $conversion_points['money'] * $conversion_points['points'] );
				$action = 'refund_deleted';

				if ( $user_id > 0 ) {
					yit_save_prop( $order, '_ywpar_points_earned', $points + $point_earned );
					YITH_WC_Points_Rewards()->add_point_to_customer( $user_id, $points, $action, '', $order_id );
					$order->add_order_note( sprintf( _x( 'Added %1$d %2$s for cancelled refund.', 'First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ) ), 0 );
				}
			}

		}

		/**
		 * Remove points to the order if there's a partial refund
		 *
		 * @param $order_id
		 * @param $refund_id
		 *
		 * @return void
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function remove_order_points_refund( $order_id, $refund_id ) {

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$point_earned          = (float)yit_get_prop( $order, '_ywpar_points_earned', true );
			$total_points_refunded = (float)yit_get_prop( $order, '_ywpar_total_points_refunded', true );

			if ( $point_earned == '' ) {
				return;
			}

			$refund_obj    = new WC_Order_Refund( $refund_id );
			$refund_amount = $refund_obj->get_amount();

			$order_total    = $order->get_total();
			$order_subtotal = $order->get_subtotal();
			$user_id        = $order->get_user_id();
			$currency       = $order->get_currency();

			if ( $refund_amount > 0 ) {

				if ( $refund_amount > $order_subtotal ) {
					// shipping must be removed from
					$order_shipping_total = $order->get_shipping_total();
					$refund_amount        = $refund_amount - $order_shipping_total;
				}

				$conversion_points = get_post_meta( $order_id, '_ywpar_conversion_points', true );

				if ( $conversion_points == '' ) {
					$conversion_points = $this->get_conversion_option( $currency, $order );
				}

				if ( $refund_amount == abs( $order_total ) ) {
					$points = $point_earned;
				} else {
					$points = round( $refund_amount / $conversion_points['money'] * $conversion_points['points'] );
				}

				// fix the points to refund calculation if points are more of the gap
				$gap                   = $point_earned - $total_points_refunded;
				$points                = ( $points > $gap ) ? $gap : $points;
				$action                = 'order_refund';
				$total_points_refunded += $points;

				if ( $user_id > 0 ) {
					yit_save_prop( $order, '_ywpar_total_points_refunded', $total_points_refunded );
					// DO_ACTION : ywpar_customer_removed_points : action triggered before remove point to customer in a refund
					do_action( 'ywpar_customer_removed_points', $points, $order );
					YITH_WC_Points_Rewards()->add_point_to_customer( $user_id, -$points, $action, '', $order_id );
					$order->add_order_note( sprintf( _x( 'Removed %1$d %2$s for order refund.', 'First placeholder: number of point; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ) ), 0 );
				}
			}
		}


		/**
		 * Add point to the order if the status of order from cancelled become processing or completed
		 *
		 * @param $order_id
		 *
		 * @return void
		 * @since   1.1.3
		 * @author  Emanuela Castorina
		 *
		 */
		public function add_order_points_after_order_status( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$is_set = yit_get_prop( $order, '_ywpar_points_earned', true );

			// return if the points are just calculated
			if ( is_array( $this->points_applied ) && in_array( $order_id, $this->points_applied ) || $is_set != '' ) {
				return;
			}

			$user_id = $order->get_user_id();
			$points  = $is_set;
			$action  = 'order_' . $order->get_status();

			if ( $user_id > 0 ) {
				if ( ! YITH_WC_Points_Rewards()->is_user_enabled( 'earn', $user_id ) || apply_filters( 'ywpar_add_order_points', false, $order_id ) ) {
					return;
				}

				YITH_WC_Points_Rewards()->add_point_to_customer( $user_id, $points, $action, '', $order_id );
				$order->add_order_note( sprintf( _x( 'Added %1$d %2$s for order %3$s.', 'First placeholder: number of points; second placeholder: label of points; third placeholder: order number', 'yith-woocommerce-points-and-rewards' ), $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ), YITH_WC_Points_Rewards()->get_action_label( $action ) ), 0 );
			}
		}

		/**
		 * Return the global points of an object
		 *
		 * @param        $object
		 * @param string $type
		 * @param bool   $integer
		 *
		 * @param string $currency
		 *
		 * @return int
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function get_point_earned( $object, $type = 'order', $integer = false, $currency = '' ) {

			$conversion = $this->get_conversion_option( $currency );

			$price = 0;
			switch ( $type ) {
				case 'order':
					$price = $object->get_total();
					break;
				case 'product':
					$price = ( get_option( 'woocommerce_tax_display_cart' ) == 'excl' ) ? yit_get_price_excluding_tax( $object ) : yit_get_price_including_tax( $object );
					break;
				default:
			}

			$price  = apply_filters( 'ywpar_get_point_earned_price', $price, $currency, $object );
			$points = (float)$price / $conversion['money'] * $conversion['points'];

			return $integer ? round( $points ) : $points;
		}

		/**
		 * Return the global points of an object from price
		 *
		 * @param      $price
		 * @param bool $integer
		 *
		 * @param      $currency
		 *
		 * @return int
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function get_point_earned_from_price( $price, $integer = false, $currency ) {
			$conversion = $this->get_conversion_option( $currency );

			$points = ( $price / $conversion['money'] ) * $conversion['points'];

			return $integer ? round( $points ) : $points;
		}

		/**
		 * Return the global points of an object from price
		 *
		 * @param      $points
		 * @param bool $integer
		 *
		 * @return int
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function get_price_from_point_earned( $points, $integer = false ) {
			$conversion = $this->get_conversion_option();

			$price = $points * $conversion['money'] / $conversion['points'];

			return $price;
		}

		/**
		 * Return the global points of an object
		 *
		 * @param string $currency
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function get_conversion_option( $currency = '', $order = false ) {

			$default_currency = apply_filters( 'ywpar_multi_currency_current_currency', get_woocommerce_currency() );
			$currency         = empty( $currency ) ? $default_currency : $currency;

			$role_conversion_enabled = YITH_WC_Points_Rewards()->get_option( 'enable_conversion_rate_for_role' );
			$conversion_rate_level   = YITH_WC_Points_Rewards()->get_option( 'conversion_rate_level' );
			$conversions             = YITH_WC_Points_Rewards()->get_option( 'earn_points_conversion_rate' );
			$conversion              = isset( $conversions[ $currency ] ) ? $conversions[ $currency ] : array(
				'money'  => 0,
				'points' => 0,
			);

			if ( $role_conversion_enabled == 'yes' && is_user_logged_in() ) {
				$current_user    = ! empty( $order ) && $order instanceof WC_Order ? get_userdata( $order->get_customer_id() ) : wp_get_current_user();
				$conversion_rate = 0;
				if ( ! empty( $current_user->roles ) ) {
					$conversion_by_role = YITH_WC_Points_Rewards()->get_option( 'earn_points_role_conversion_rate' );
					if ( isset( $conversion_by_role['role_conversion'] ) ) {
						foreach ( $conversion_by_role['role_conversion'] as $conv_role ) {
							if ( isset( $conv_role['role'] ) ) {
								$current_role = $conv_role['role'];
								foreach ( $current_user->roles as $role ) {
									if ( $current_role == $role ) {
										$c = $conv_role;
										$c = isset( $c[ $currency ] ) ? $c[ $currency ] : array(
											'money'  => 0,
											'points' => 0,
										);
										if ( $c['points'] != '' && $c['money'] != '' && $c['money'] != 0 ) {
											$current_conversion_rate = abs( $c['points'] / $c['money'] );
											if ( ( $conversion_rate_level == 'high' && $current_conversion_rate >= $conversion_rate ) || ( $conversion_rate_level == 'low' && $current_conversion_rate < $conversion_rate ) ) {
												$conversion_rate = $current_conversion_rate;
												$conversion      = $c;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$conversion['money']  = ( empty( $conversion['money'] ) ) ? 1 : $conversion['money'];
			$conversion['points'] = ( empty( $conversion['points'] ) ) ? 1 : $conversion['points'];

			return apply_filters( 'ywpar_conversion_points_rate', $conversion );
		}

		/**
		 * Porting old extra-points method to the new
		 *
		 * @param $user_id
		 * @param $user_extra_point
		 *
		 * @return array
		 */
		private function populate_extra_points_counter( $user_id, $user_extra_point ) {
			$user_extra_point_counter = array();
			global $wpdb;

			$table_name = $wpdb->prefix . 'yith_ywpar_points_log';

			$review_counter = 0;
			$num_orders     = 0;
			$amount_used    = 0;

			if ( $user_extra_point ) {
				foreach ( $user_extra_point as $item ) {
					switch ( $item['option'] ) {
						case 'reviews':
							$review_counter += $item['value'];
							break;
						case 'num_of_orders':
							$num_orders += $item['value'];
							break;
						case 'amount_spent':
							$amount_used += $item['value'];
							break;
					}
				}
			}

			$user_extra_point_counter['reviews']['review_used']  = $review_counter;
			$user_extra_point_counter['reviews']['starter_date'] = date( 'Y-m-d' );
			$starter_date                                        = date( 'Y-m-d', time() - DAY_IN_SECONDS );
			if ( $num_orders ) {
				$query        = "SELECT date_earning FROM  $table_name where user_id = $user_id AND action='num_of_orders_exp' ORDER BY date_earning DESC LIMIT 1";
				$res          = $wpdb->get_row( $query );
				$starter_date = $res ? $res->date_earning : $starter_date;
			}

			$user_extra_point_counter['num_of_orders']['order_used']   = $num_orders;
			$user_extra_point_counter['num_of_orders']['starter_date'] = $starter_date;

			$starter_date = date( 'Y-m-d', time() - DAY_IN_SECONDS );
			if ( $amount_used ) {
				$query        = "SELECT date_earning FROM  $table_name where user_id = $user_id AND action='amount_spent_exp' ORDER BY date_earning DESC LIMIT 1";
				$res          = $wpdb->get_row( $query );
				$starter_date = $res ? $res->date_earning : $starter_date;
			}
			$user_extra_point_counter['amount_spent']['amount_used']  = $amount_used;
			$user_extra_point_counter['amount_spent']['starter_date'] = $starter_date;

			update_user_meta( $user_id, '_ywpar_extrapoint_counter', $user_extra_point_counter );

			return $user_extra_point_counter;

		}

		/**
		 * Add extra points to the user.
		 *
		 * @param array $types
		 * @param       $user_id
		 * @param int   $order_id
		 *
		 * @return void|bool
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function extra_points( $types, $user_id, $order_id = 0 ) {

			if ( empty( $types ) || empty( $user_id ) || ! YITH_WC_Points_Rewards()->is_user_enabled( 'earn', $user_id ) ) {
				return false;
			}

			$action                   = '';
			$user_extra_point         = get_user_meta( $user_id, '_ywpar_extrapoint', true );
			$user_extra_point_counter = get_user_meta( $user_id, '_ywpar_extrapoint_counter', true );
			$current_points           = get_user_meta( $user_id, '_ywpar_user_total_points', true );

			$user_extra_point = empty( $user_extra_point ) ? array() : (array)$user_extra_point;
			$current_points   = empty( $current_points ) ? 0 : (int)$current_points;

			// populate the counter if it empty
			if ( empty( $user_extra_point_counter ) ) {
				$user_extra_point_counter = $this->populate_extra_points_counter( $user_id, $user_extra_point );
			}

			foreach ( $types as $current_type ) {
				$extra_points = 0;
				switch ( $current_type ) {
					case 'registration':
						$point_value = YITH_WC_Points_Rewards()->get_option( 'points_on_registration' );

						if ( $point_value != '' && $point_value > 0 ) {
							$rule              = array(
								'option'    => 'registration',
								'value'     => 1,
								'points'    => $point_value,
								'date_from' => date( 'Y-m-d' ),
							);
							$extra_points      = $point_value;
							$user_extrapoint[] = $rule;
						}

						break;

					case 'reviews':
						$review_rules = YITH_WC_Points_Rewards()->get_option( 'review_exp' );

						if ( empty( $review_rules ) || 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_review_exp' ) ) {
							continue 2;
						}

						if ( isset( $user_extra_point_counter[ $current_type ] ) ) {
							$starter_date = $user_extra_point_counter[ $current_type ]['starter_date'];
							$review_used  = $user_extra_point_counter[ $current_type ]['review_used'];
						} else {
							$review_used                               = 0;
							$starter_date                              = date( 'Y-m-d' );
							$user_extra_point_counter[ $current_type ] = array(
								'starter_date' => $starter_date,
								'review_used'  => 0,
							);
						}

						$review_used     = is_nan( $review_used ) ? 0 : $review_used;
						$usable_comments = ywpar_get_usable_comments( $user_id, $starter_date );
						$review_num      = $usable_comments ? count( $usable_comments ) - $review_used : 0;
						$review_num      = is_nan( $review_num ) ? 0 : $review_num;

						if ( isset( $review_rules['list'] ) && ! empty( $review_rules['list'] ) ) {
							$review_num = apply_filters( 'ypar_extrapoints_renew_num', $review_num, $review_rules['list'] );

							foreach ( $review_rules['list'] as $review_rule ) {

								if ( $review_num > 0 ) {
									$repeat = isset( $review_rule['repeat'] ) ? $review_rule['repeat'] : 0;
									$rule   = array(
										'option' => $current_type,
										'value'  => $review_rule['number'],
										'points' => $review_rule['points'],
										'repeat' => $repeat,
									);

									// check if the rule is already applied
									if ( ! $this->check_extrapoint_rule( $rule, $user_extra_point ) ) {
										continue;
									}

									// if the customer has enogh reviews to use
									if ( $review_num >= $rule['value'] ) {
										$repeat_times = ( $repeat == 1 ) ? floor( $review_num / $review_rule['number'] ) : 1;
										$extra_points += $repeat_times * $rule['points'];
										$review_used  += $repeat_times * $review_rule['number'];
										$review_num   -= $repeat_times * $review_rule['number'];

										$user_extra_point[] = $rule;
									}

									$action = __( 'Reviews', 'yith-woocommerce-points-and-rewards' );
								}
							}

							$user_extra_point_counter[ $current_type ]['review_used'] = $review_used;

						}

						break;
					case 'num_of_orders':
						$num_order_rules = YITH_WC_Points_Rewards()->get_option( 'num_order_exp' );

						if ( empty( $num_order_rules ) || 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_num_order_exp' ) ) {
							continue 2;
						}

						if ( isset( $user_extra_point_counter[ $current_type ] ) ) {
							$starter_date = $user_extra_point_counter[ $current_type ]['starter_date'];
							$order_used   = $user_extra_point_counter[ $current_type ]['order_used'];
						} else {
							$order_used                                = 0;
							$starter_date                              = date( 'Y-m-d', time() - DAY_IN_SECONDS );
							$user_extra_point_counter[ $current_type ] = array(
								'starter_date' => $starter_date,
								'order_used'   => 0,
							);
						}

						$usable_num_of_order = ywpar_get_customer_order_count( $user_id, $starter_date );
						$order_num           = $usable_num_of_order - $order_used;

						if ( isset( $num_order_rules['list'] ) && ! empty( $num_order_rules['list'] ) ) {
							foreach ( $num_order_rules['list'] as $num_order_rule ) {
								if ( $order_num > 0 ) {
									$repeat = isset( $num_order_rule['repeat'] ) ? $num_order_rule['repeat'] : 0;
									$rule   = array(
										'option' => $current_type,
										'value'  => $num_order_rule['number'],
										'points' => $num_order_rule['points'],
										'repeat' => $repeat,
									);

									// check if the rule is already applied
									if ( ! $this->check_extrapoint_rule( $rule, $user_extra_point ) ) {
										continue;
									}

									// if the customer has enogh reviews to use
									if ( $order_num >= $rule['value'] ) {
										$repeat_times = ( $repeat == 1 ) ? floor( $order_num / $num_order_rule['number'] ) : 1;
										$extra_points += $repeat_times * $rule['points'];
										$order_used   += $repeat_times * $num_order_rule['number'];
										$order_num    -= $repeat_times * $num_order_rule['number'];

										$user_extra_point[] = $rule;
									}
								}
							}

							$user_extra_point_counter[ $current_type ]['order_used'] = $order_used;

						}

						break;
					case 'amount_spent':
						$amount_spent_rules = YITH_WC_Points_Rewards()->get_option( 'amount_spent_exp' );

						if ( empty( $amount_spent_rules ) || 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_amount_spent_exp' ) ) {
							continue 2;
						}

						if ( isset( $user_extra_point_counter[ $current_type ] ) ) {
							$starter_date = $user_extra_point_counter[ $current_type ]['starter_date'];
							$amount_used  = $user_extra_point_counter[ $current_type ]['amount_used'];
						} else {
							$amount_used                               = 0;
							$starter_date                              = date( 'Y-m-d', time() - DAY_IN_SECONDS );
							$user_extra_point_counter[ $current_type ] = array(
								'starter_date' => $starter_date,
								'amount_used'  => 0,
							);
						}

						$usable_amount = yith_ywpar_calculate_user_total_orders_amount( $user_id, $order_id, $starter_date );
						$amount        = $usable_amount - $amount_used;

						if ( isset( $amount_spent_rules['list'] ) && ! empty( $amount_spent_rules['list'] ) ) {
							foreach ( $amount_spent_rules['list'] as $amount_spent_rule ) {
								if ( $amount > 0 ) {
									$repeat = isset( $amount_spent_rule['repeat'] ) ? $amount_spent_rule['repeat'] : 0;
									$rule   = array(
										'option' => 'amount_spent',
										'value'  => $amount_spent_rule['number'],
										'points' => $amount_spent_rule['points'],
										'repeat' => $repeat,
									);

									// check if the rule is already applied
									if ( ! $this->check_extrapoint_rule( $rule, $user_extra_point ) ) {
										continue;
									}

									// if the customer has enough reviews to use
									if ( $amount >= $rule['value'] ) {
										$repeat_times = ( $repeat == 1 ) ? floor( $amount / $amount_spent_rule['number'] ) : 1;
										$extra_points += $repeat_times * $rule['points'];
										$amount_used  += $repeat_times * $amount_spent_rule['number'];
										$amount       -= $repeat_times * $amount_spent_rule['number'];

										$user_extra_point[] = $rule;
									}
								}
							}

							$user_extra_point_counter[ $current_type ]['amount_used'] = $amount_used;

						}

						break;

					case 'checkout_threshold':
						$checkout_thresolds = YITH_WC_Points_Rewards()->get_option( 'checkout_threshold_exp' );

						if ( empty( $checkout_thresolds ) || $order_id == 0 || 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_checkout_threshold_exp' ) ) {
							continue 2;
						}

						// get order
						$o     = wc_get_order( $order_id );
						$total = $o->get_total();

						// sort the thresolds array by number value
						$thresolds = $checkout_thresolds['list'];
						array_multisort( array_column( $thresolds, 'number' ), SORT_DESC, $thresolds );

						if ( isset( $thresolds ) && ! empty( $thresolds ) ) {
							foreach ( $thresolds as $checkout_thresold ) {
								$repeat = isset( $checkout_thresold['repeat'] ) ? $checkout_thresold['repeat'] : 0;
								$rule   = array(
									'option' => 'checkout_thresholds',
									'value'  => $checkout_thresold['number'],
									'points' => $checkout_thresold['points'],
									'repeat' => $repeat,
								);

								if ( $total >= $rule['value'] ) {
									$extra_points       += $rule['points'];
									$user_extra_point[] = $rule;
									if ( get_option( 'ywpar_checkout_threshold_not_cumulate' ) == 'yes' ) {
										break;
									}
								}
							}
						}

						break;

					case 'points':
						$points_rules = YITH_WC_Points_Rewards()->get_option( 'number_of_points_exp' );

						if ( empty( $points_rules ) || 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_number_of_points_exp' ) ) {
							continue 2;
						}

						$reusable_points = (int)get_user_meta( $user_id, '_ywpar_user_reusable_points', true );
						$usable_points   = $current_points - $reusable_points;

						if ( isset( $points_rules['list'] ) && ! empty( $points_rules['list'] ) ) {
							foreach ( $points_rules['list'] as $points_rule ) {
								if ( $usable_points > 0 ) {
									$repeat = isset( $points_rule['repeat'] ) ? $points_rule['repeat'] : 0;
									$rule   = array(
										'option' => $current_type,
										'value'  => $points_rule['number'],
										'points' => $points_rule['points'],
										'repeat' => $repeat,
									);

									// check if the rule is already applied
									if ( ! $this->check_extrapoint_rule( $rule, $user_extra_point ) ) {
										continue;
									}

									// if the customer has enogh reviews to use
									if ( $usable_points >= $rule['value'] ) {
										$repeat_times    = ( $repeat == 1 ) ? floor( $usable_points / $points_rule['number'] ) : 1;
										$extra_points    += $repeat_times * $rule['points'];
										$reusable_points += $repeat_times * $points_rule['number'];
										$usable_points   -= $repeat_times * $points_rule['number'];

										$user_extra_point[] = $rule;
									}
								}
							}

							update_user_meta( $user_id, '_ywpar_user_reusable_points', $reusable_points );

						}
						break;
					case 'birthday':
						$point_value = YITH_WC_Points_Rewards()->get_option( 'points_on_birthday' );

						if ( $point_value != '' && $point_value > 0 ) {
							$rule              = array(
								'option'    => 'birthday',
								'value'     => 1,
								'points'    => $point_value,
								'date_from' => date( 'Y-m-d' ),
							);
							$extra_points      = $point_value;
							$user_extrapoint[] = $rule;
						}
						break;
				}
				if ( $extra_points > 0 ) {
					YITH_WC_Points_Rewards()->add_point_to_customer( $user_id, $extra_points, $current_type . '_exp' );
					$current_points += $extra_points;
				}

				update_user_meta( $user_id, '_ywpar_extrapoint', $user_extra_point );
				update_user_meta( $user_id, '_ywpar_extrapoint_counter', $user_extra_point_counter );
			}
		}

		/**
		 * Check if an extra-points rule is used by customer.
		 *
		 * @param $rule
		 * @param $user_extrapoint
		 *
		 * @return bool
		 */
		private function check_extrapoint_rule( $rule, $user_extrapoint ) {
			$result = true;
			if ( $user_extrapoint ) {
				foreach ( $user_extrapoint as $ue_item ) {
					if ( $ue_item['option'] != $rule['option'] ) {
						continue;
					}

					if ( ! $rule['repeat'] ) {
						if ( $ue_item['value'] == $rule['value'] && $ue_item['points'] == $rule['points'] ) {
							$result = false;
						}
					}
				}
			}

			return $result;
		}

		/**
		 * Add Point to the user.
		 *
		 * @param      $user_id
		 * @param      $points
		 * @param      $action
		 * @param      $order_id
		 *
		 * @param bool $register_log
		 *
		 * @return void
		 * @deprecated
		 * @since      1.0.0
		 * @author     Emanuela Castorina
		 *
		 * @deprecated use YITH_WC_Points_Rewards()->add_point_to_customer
		 */
		public function add_points( $user_id, $points, $action, $order_id, $register_log = true ) {
			$current_point = get_user_meta( $user_id, '_ywpar_user_total_points', true );
			$current_point = empty( $current_point ) ? 0 : (int)$current_point;
			// add the new points to the total points of customer
			$p = $current_point + $points;

			// APPLY_FILTER : ywpar_disable_negative_point: disable or not negative points
			if ( apply_filters( 'ywpar_disable_negative_point', true, $user_id, $points, $action, $order_id ) ) {
				$p = $p > 0 ? $p : 0;
			}

			update_user_meta( $user_id, '_ywpar_user_total_points', $p );

			if ( $register_log ) {
				YITH_WC_Points_Rewards()->register_log( $user_id, $action, $order_id, $points );
			}
		}

		/**
		 * Triggered when a reviews status changes.
		 *
		 * If extra point to reviews is set, call the exrta_points method.
		 * Called by 'comment_post' 'wp_set_comment_status' hooks
		 *
		 * @param $comment_ID
		 * @param $status
		 *
		 * @author Emanuela Castorina
		 */
		public function add_order_points_with_review( $comment_ID, $status ) {
			// only if the review is approved assign the point to the user
			if ( 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_review_exp' ) || ( $status !== 'approve' && $status != 1 ) || ! is_user_logged_in() ) {
				return;
			}

			$comment = get_comment( $comment_ID );

			// only if is a review
			$post_type = get_post_type( $comment->comment_post_ID );

			if ( 'product' != $post_type ) {
				return;
			}

			// add point for review
			$this->extra_points( array( 'reviews' ), $comment->user_id );
		}

		/**
		 * This function get the user id to pass to extra_points method
		 * when a review status changed is triggered in YITH WooCommerce Advanced Review
		 *
		 * Triggered by 'ywar_review_approve_status_changed' YITH Advanced Review hook
		 *
		 * @param $review_id
		 * @param $status
		 */
		public function add_order_points_with_advanced_reviews( $review_id, $status ) {
			// only if the review is approved assign the point to the user
			if ( $status != 1 || ! is_user_logged_in() || 'yes' != YITH_WC_Points_Rewards()->get_option( 'enable_review_exp' ) ) {
				return;
			}

			$review_user = get_post_meta( $review_id, '_ywar_review_user_id', true );
			$this->extra_points( array( 'reviews' ), $review_user );
		}

		/**
		 * Registration extra-points.
		 *
		 * Assign extra-points to the user is the conditions setting about the registration are valid.
		 *
		 * @param $customer_user
		 */
		public function extrapoints_to_new_customer( $customer_user ) {

			// check if the review is set as extra-point rule
			if ( YITH_WC_Points_Rewards()->get_option( 'enable_points_on_registration_exp' ) != 'yes' ) {
				return;
			}

			$this->extra_points( array( 'registration' ), $customer_user );
		}


		/**
		 * Fix expiration point before version 1.3.0
		 *
		 * @return bool
		 */
		public function yith_ywpar_reset_expiration_points() {

			if ( $this->points_expired_check || 1 == get_option( 'ywpar_points_expired_check' ) || apply_filters( 'ywpar_expiration_old_mode', false ) ) {
				return false;
			}

			global $wpdb;

			$table_name = $wpdb->prefix . 'yith_ywpar_points_log';

			$query   = "Select id, amount, user_id, order_id from $table_name where action like 'expired_points'";
			$users   = array();
			$results = $wpdb->get_results( $query );
			$remove  = array();

			if ( $results ) {
				foreach ( $results as $result ) {

					if ( ! in_array( $result->user_id, $users ) ) {

						$current_point = get_user_meta( $result->user_id, '_ywpar_user_total_points', true );
						$old_points    = $wpdb->get_var( "select sum(amount) from $table_name where action NOT like 'expired_points' and user_id=$result->user_id" );

						if ( $current_point > 0 ) {
							$points_to_add = $old_points - $current_point;
						} else {
							$points_to_add = absint( $old_points ) + absint( $current_point );
						}
						YITH_WC_Points_Rewards()->add_point_to_customer( $result->user_id, $points_to_add, 'admin_action', '', $result->order_id, '', 0, 0 );

						$users[] = $result->user_id;
					}

					$remove[] = $result->id;
				}

				if ( ! empty( $remove ) ) {
					$query = "DELETE from $table_name WHERE id IN (" . implode( ',', $remove ) . ');';
					$wpdb->query( $query );
				}

				$query = "UPDATE $table_name SET `cancelled`=NULL WHERE 1=1;";
				$wpdb->query( $query );
			}

			$this->points_expired_check = true;

			update_option( 'ywpar_points_expired_check', 1 );
			update_option( 'yit_ywpar_expiration_mode', 'from_1.3.0' );

		}

		/**
		 * Return usable points
		 *
		 * @param $user_id
		 *
		 * @return int
		 * @author     Emanuela Castorina
		 *
		 * @deprecated since 1.6.0
		 * @since      1.0.0
		 */
		public function get_usable_points( $user_id ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'yith_ywpar_points_log';
			$from_id    = 1;
			$query      = "SELECT id FROM  $table_name where user_id = $user_id AND action='points_exp' ORDER BY date_earning DESC LIMIT 1";
			$res        = $wpdb->get_row( $query );

			if ( ! empty( $res ) ) {
				$from_id = $res->id;
			}

			$query = "SELECT SUM(ywpar_points.amount) as usable_points FROM $table_name as ywpar_points where user_id = $user_id AND id > $from_id";
			$res   = $wpdb->get_row( $query );

			if ( ! empty( $res ) ) {
				return $res->usable_points;
			}
		}


	}


}

/**
 * Unique access to instance of YITH_WC_Points_Rewards_Earning class
 *
 * @return \YITH_WC_Points_Rewards_Earning
 */
function YITH_WC_Points_Rewards_Earning() {
	return YITH_WC_Points_Rewards_Earning::get_instance();
}

