<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YITH WooCommerce Subscription
 *
 * @class   YWSBS_Subscription_User
 * @since   2.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Subscription_User' ) ) {

	/**
	 * Class YWSBS_Subscription_User
	 */
	class YWSBS_Subscription_User {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_User
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_User
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Return the ids of user subscriptions
		 *
		 * @param int $user_id User ID.
		 *
		 * @return array|int
		 */
		public static function get_subscriptions_ids( $user_id ) {

			$ywsbs_cache_key  = 'ywsbs_subscription_ids';
			$subscription_ids = get_user_meta( $user_id, $ywsbs_cache_key, true );

			if ( empty( $subscription_ids ) ) {
				$args = array(
					'post_type'      => YITH_YWSBS_POST_TYPE,
					'posts_per_page' => - 1,
					'fields'         => 'ids',
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					                           array(
						                           'key'     => 'user_id',
						                           'value'   => $user_id,
						                           'compare' => '=',
					                           ),
					),
				);

				$subscription_ids = get_posts( $args );

				update_user_meta( $user_id, $ywsbs_cache_key, $subscription_ids );
			}

			return $subscription_ids ? $subscription_ids : array();
		}

		/**
		 * Return the list of subscriptions of a user.
		 *
		 * @param int $user_id User id.
		 *
		 * @return array
		 */
		public static function get_subscriptions( $user_id ) {

			$subscriptions = array();

			if ( empty( $user_id ) ) {
				return $subscriptions;
			}

			$subscription_ids = self::get_subscriptions_ids( $user_id );

			if ( $subscription_ids ) {
				foreach ( $subscription_ids as $subscription_id ) {
					$subscriptions[ $subscription_id ] = ywsbs_get_subscription( $subscription_id );
				}
			}

			return $subscriptions;

		}

		/**
		 * Check if the customer has a subscription with a specific product.
		 *
		 * @param int           $user_id    User id.
		 * @param int           $product_id Subscription Product ID.
		 * @param string| array $status     Subscription status.
		 *
		 * @return mixed|void
		 */
		public static function has_subscription( $user_id, $product_id, $status = '' ) {
			$subscriptions    = self::get_subscriptions( $user_id );
			$has_subscription = false;
			$status           = empty( $status ) ? false : (array) $status;
			$product_ids      = array( $product_id );
			global $sitepress;

			if ( ! empty( $sitepress ) ) {

				$type     = apply_filters( 'wpml_element_type', 'product' );
				$trid     = apply_filters( 'wpml_element_trid', null, $product_id, $type );
				$products = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );

				if ( ! empty( $products ) ) {
					foreach ( $products as $product ) {
						$product_ids[] = (int) $product->element_id;
					}
					$product_ids = array_unique( $product_ids );
				}
			}
			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					if ( ( in_array( $subscription->get_product_id(), $product_ids, true ) || in_array( $subscription->get_variation_id(), $product_ids, true ) ) && ( ! $status || ( $status && $subscription->has_status( $status ) ) ) ) {
						$has_subscription = true;
						break;
					}
				}
			}

			return apply_filters( 'ywsbs_user_has_subscription', $has_subscription, $user_id, $product_id, $status );
		}


		/**
		 * Check if the customer has a subscription with a specific product.
		 *
		 * @param int           $user_id    User id.
		 * @param int           $product_id Subscription Product ID.
		 * @param string| array $status     Subscription status.
		 *
		 * @return mixed|void
		 */
		public static function get_subscriptions_by_product( $user_id, $product_id, $status = '' ) {
			$subscriptions         = self::get_subscriptions( $user_id );
			$status                = empty( $status ) ? false : (array) $status;
			$product_subscriptions = array();
			$product_ids           = array( $product_id );

			global $sitepress;
			if ( ! empty( $sitepress ) ) {

				$type     = apply_filters( 'wpml_element_type', 'product' );
				$trid     = apply_filters( 'wpml_element_trid', null, $product_id, $type );
				$products = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );

				if ( ! empty( $products ) ) {
					foreach ( $products as $product ) {
						$product_ids[] = (int) $product->element_id;
					}
					$product_ids = array_unique( $product_ids );
				}
			}


			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					if ( ( in_array( $subscription->get_product_id(), $product_ids, true ) || in_array( $subscription->get_variation_id(), $product_ids, true ) ) && ( ! $status || ( $status && $subscription->has_status( $status ) ) ) ) {
						array_push( $product_subscriptions, $subscription );
					}
				}
			}

			return apply_filters( 'ywsbs_user_subscriptions_by_product', $product_subscriptions, $user_id, $product_id, $status );
		}


		/**
		 * Return the customer info of a subscription to show inside the subscription list table.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return string
		 *
		 * @since 2.0.4
		 */
		public static function get_user_info_for_subscription_list( $subscription ) {

			$user = get_user_by( 'id', $subscription->get_user_id() );

			$first_name = $subscription->get_billing_first_name();
			$last_name  = $subscription->get_billing_last_name();

			if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
				/* translators: 1: first name 2: last name */
				$buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'yith-woocommerce-subscription' ), $first_name, $last_name ) );
			} else {
				$buyer = $user ? ucwords( $user->display_name ) : __( 'Guest', 'yith-woocommerce-subscription' );
			}

			if ( $user ) {
				$buyer .= '<br><a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';
			}

			return apply_filters( 'ywsbs_user_info_for_subscription_list', $buyer, $subscription, $user );

		}

		/**
		 * Delete the user cache meta.
		 *
		 * @param int $user_id User id.
		 */
		public static function delete_user_cache( $user_id ) {
			delete_user_meta( $user_id, 'ywsbs_subscription_ids' );
		}

	}
}
