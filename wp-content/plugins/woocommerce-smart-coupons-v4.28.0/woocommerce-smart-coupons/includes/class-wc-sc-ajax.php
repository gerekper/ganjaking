<?php
/**
 * Smart Coupons Ajax Actions
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.2.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Ajax' ) ) {

	/**
	 * Class for handling ajax actions for Smart Coupons
	 */
	class WC_SC_Ajax {

		/**
		 * Variable to hold instance of WC_SC_Ajax
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'wp_ajax_sc_json_search_coupons', array( $this, 'sc_json_search_coupons' ) );
			add_action( 'wp_ajax_sc_json_search_storewide_coupons', array( $this, 'sc_json_search_storewide_coupons' ) );
			add_action( 'wp_ajax_smart_coupons_json_search', array( $this, 'smart_coupons_json_search' ) );
			add_action( 'wp_ajax_hide_notice_delete_after_usage', array( $this, 'hide_notice_delete_after_usage' ) );

		}

		/**
		 * Get single instance of WC_SC_Ajax
		 *
		 * @return WC_SC_Ajax Singleton object of WC_SC_Ajax
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Function to search coupons
		 *
		 * @param string $x Search term.
		 * @param array  $post_types Post types.
		 */
		public function sc_json_search_coupons( $x = '', $post_types = array( 'shop_coupon' ) ) {
			global $wpdb;

			check_ajax_referer( 'search-coupons', 'security' );

			$term = (string) wc_clean( wp_unslash( $_GET['term'] ) ); // phpcs:ignore

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $term,
				'fields'         => 'all',
			);

			$posts = wp_cache_get( 'wc_sc_search_coupon_by_code_' . sanitize_title( $term ), 'woocommerce_smart_coupons' );

			if ( false === $posts ) {
				$posts = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						"SELECT * FROM {$wpdb->prefix}posts
							WHERE post_type = %s
								AND post_title LIKE %s
								AND post_status = %s",
						'shop_coupon',
						$wpdb->esc_like( $term ) . '%',
						'publish'
					)
				);
				wp_cache_set( 'wc_sc_search_coupon_by_code_' . sanitize_title( $term ), $posts, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_search_coupon_by_code_' . sanitize_title( $term ) );
			}

			$found_products = array();

			$all_discount_types = wc_get_coupon_types();

			if ( $posts ) {
				foreach ( $posts as $post ) {

					$discount_type = get_post_meta( $post->ID, 'discount_type', true );

					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {
						$discount_type                       = ' (' . __( 'Type', 'woocommerce-smart-coupons' ) . ': ' . $all_discount_types[ $discount_type ] . ')';
						$found_products[ $post->post_title ] = $post->post_title . $discount_type;
					}
				}
			}

			wp_send_json( $found_products );

		}

		/**
		 * Function to search storewide coupons
		 *
		 * @param string $x Search term.
		 * @param array  $post_types Post types.
		 */
		public function sc_json_search_storewide_coupons( $x = '', $post_types = array( 'shop_coupon' ) ) {
			global $wpdb;

			check_ajax_referer( 'search-coupons', 'security' );

			$term = (string) wc_clean( wp_unslash( $_GET['term'] ) ); // phpcs:ignore

			if ( empty( $term ) ) {
				die();
			}

			$found_coupons = array();
			$coupon_posts  = array();

			$posts = wp_cache_get( 'wc_sc_search_storewide_coupon_by_code_' . sanitize_title( $term ), 'woocommerce_smart_coupons' );

			if ( false === $posts ) {
				$posts = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						"SELECT p.ID,
								p.post_title,
								pm.meta_key,
								pm.meta_value
							FROM {$wpdb->posts} AS p
								JOIN {$wpdb->postmeta} AS pm
									ON (p.ID = pm.post_id AND pm.meta_key IN (%s,%s,%s,%s,%s,%s,%s))
							WHERE p.post_type = %s
								AND p.post_title LIKE %s
								AND p.post_status = %s",
						'discount_type',
						'coupon_amount',
						'date_expires',
						'auto_generate_coupon',
						'customer_email',
						'sc_disable_email_restriction',
						'wc_sc_expiry_time',
						'shop_coupon',
						$wpdb->esc_like( $term ) . '%',
						'publish'
					),
					ARRAY_A
				);
				wp_cache_set( 'wc_sc_search_storewide_coupon_by_code_' . sanitize_title( $term ), $posts, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_search_storewide_coupon_by_code_' . sanitize_title( $term ) );
			}

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post ) {
					$post_id    = ( ! empty( $post['ID'] ) ) ? absint( $post['ID'] ) : 0;
					$post_title = ( ! empty( $post['post_title'] ) ) ? $post['post_title'] : '';
					if ( empty( $post_id ) || empty( $post_title ) ) {
						continue;
					}
					if ( empty( $coupon_posts[ $post_id ] ) || ! is_array( $coupon_posts[ $post_id ] ) ) {
						$coupon_posts[ $post_id ] = array();
					}
					$coupon_posts[ $post_id ]['post_id']    = $post_id;
					$coupon_posts[ $post_id ]['post_title'] = $post_title;
					switch ( $post['meta_key'] ) {
						case 'discount_type':
						case 'coupon_amount':
						case 'date_expires':
						case 'auto_generate_coupon':
						case 'sc_disable_email_restriction':
						case 'wc_sc_expiry_time':
							$coupon_posts[ $post_id ][ $post['meta_key'] ] = $post['meta_value']; // phpcs:ignore
							break;
						case 'customer_email':
							$coupon_posts[ $post_id ][ $post['meta_key'] ] = maybe_unserialize( $post['meta_value'] ); // phpcs:ignore
							break;
					}
				}
			}

			$all_discount_types = wc_get_coupon_types();

			if ( ! empty( $coupon_posts ) ) {
				foreach ( $coupon_posts as $post_id => $coupon_post ) {
					$discount_type                = ( ! empty( $coupon_post['discount_type'] ) ) ? $coupon_post['discount_type'] : '';
					$coupon_amount                = ( ! empty( $coupon_post['coupon_amount'] ) ) ? $coupon_post['coupon_amount'] : 0;
					$date_expires                 = ( ! empty( $coupon_post['date_expires'] ) ) ? absint( $coupon_post['date_expires'] ) : 0;
					$wc_sc_expiry_time            = ( ! empty( $coupon_post['wc_sc_expiry_time'] ) ) ? absint( $coupon_post['wc_sc_expiry_time'] ) : 0;
					$auto_generate_coupon         = ( ! empty( $coupon_post['auto_generate_coupon'] ) ) ? $coupon_post['auto_generate_coupon'] : '';
					$sc_disable_email_restriction = ( ! empty( $coupon_post['sc_disable_email_restriction'] ) ) ? $coupon_post['sc_disable_email_restriction'] : '';
					$customer_email               = ( ! empty( $coupon_post['customer_email'] ) ) ? $coupon_post['customer_email'] : array();

					if ( empty( $discount_type ) || 'smart_coupon' === $discount_type ) {
						continue;
					}
					if ( empty( $coupon_amount ) ) {
						continue;
					}
					if ( ! empty( $date_expires ) ) {
						$date_expires += $wc_sc_expiry_time;
						if ( time() >= $date_expires ) {
							continue;
						}
					}
					if ( 'yes' === $auto_generate_coupon ) {
						continue;
					}
					if ( 'yes' === $sc_disable_email_restriction ) {
						continue;
					}
					if ( ! empty( $customer_email ) ) {
						continue;
					}

					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {
						/* translators: 1. The coupon code, 2. The discount type */
						$found_coupons[ $coupon_post['post_title'] ] = sprintf( __( '%1$s (Type: %2$s)', 'woocommerce-smart-coupons' ), $coupon_post['post_title'], $all_discount_types[ $discount_type ] );
					}
				}

				$found_coupons = apply_filters(
					'wc_sc_json_search_storewide_coupons',
					$found_coupons,
					array(
						'source'       => $this,
						'search_text'  => $term,
						'posts'        => $posts,
						'coupon_posts' => $coupon_posts,
					)
				);
			}

			wp_send_json( $found_coupons );

		}

		/**
		 * JSON Search coupon via ajax
		 *
		 * @param string $x Search text.
		 * @param array  $post_types Post types.
		 */
		public function smart_coupons_json_search( $x = '', $post_types = array( 'shop_coupon' ) ) {
			global $wpdb, $store_credit_label;

			check_ajax_referer( 'search-coupons', 'security' );

			$term = (string) wc_clean( wp_unslash( $_GET['term'] ) ); // phpcs:ignore

			if ( empty( $term ) ) {
				die();
			}

			$posts = wp_cache_get( 'wc_sc_shortcode_search_coupon_by_code_' . sanitize_title( $term ), 'woocommerce_smart_coupons' );

			if ( false === $posts ) {
				$posts = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						"SELECT *
							FROM {$wpdb->prefix}posts
							WHERE post_type = %s
								AND post_title LIKE %s
								AND post_status = %s",
						'shop_coupon',
						$wpdb->esc_like( $term ) . '%',
						'publish'
					)
				);
				wp_cache_set( 'wc_sc_shortcode_search_coupon_by_code_' . sanitize_title( $term ), $posts, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_shortcode_search_coupon_by_code_' . sanitize_title( $term ) );
			}

			$found_products = array();

			$all_discount_types = wc_get_coupon_types();

			if ( $posts ) {

				foreach ( $posts as $post ) {

					$discount_type = get_post_meta( $post->ID, 'discount_type', true );
					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {

						$coupon = new WC_Coupon( $post->post_title );

						if ( $this->is_wc_gte_30() ) {
							$discount_type = $coupon->get_discount_type();
							$coupon_amount = $coupon->get_amount();
						} else {
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						}

						switch ( $discount_type ) {

							case 'smart_coupon':
								$coupon_type   = ! empty( $store_credit_label['singular'] ) ? ucwords( $store_credit_label['singular'] ) : __( 'Store Credit', 'woocommerce-smart-coupons' );
								$coupon_amount = wc_price( $coupon_amount );
								break;

							case 'fixed_cart':
								$coupon_type   = __( 'Cart Discount', 'woocommerce-smart-coupons' );
								$coupon_amount = wc_price( $coupon_amount );
								break;

							case 'fixed_product':
								$coupon_type   = __( 'Product Discount', 'woocommerce-smart-coupons' );
								$coupon_amount = wc_price( $coupon_amount );
								break;

							case 'percent_product':
								$coupon_type   = __( 'Product Discount', 'woocommerce-smart-coupons' );
								$coupon_amount = $coupon_amount . '%';
								break;

							case 'percent':
								$coupon_type   = ( $this->is_wc_gte_30() ) ? __( 'Discount', 'woocommerce-smart-coupons' ) : __( 'Cart Discount', 'woocommerce-smart-coupons' );
								$coupon_amount = $coupon_amount . '%';
								$max_discount  = get_post_meta( $post->ID, 'wc_sc_max_discount', true );
								if ( ! empty( $max_discount ) && is_numeric( $max_discount ) ) {
									/* translators: %s: Maximum coupon discount amount */
									$coupon_type .= ' ' . sprintf( __( 'upto %s', 'woocommerce-smart-coupons' ), wc_price( $max_discount ) );
								}
								break;

							default:
								$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
								$coupon_type         = apply_filters( 'wc_sc_coupon_type', $default_coupon_type, $coupon, $all_discount_types );
								$coupon_amount       = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
								break;

						}

						$discount_type = ' ( ' . $coupon_amount . ' ' . $coupon_type . ' )';
						$discount_type = wp_strip_all_tags( $discount_type );

						$found_products[ $post->post_title ] = $post->post_title . ' ' . $discount_type;
					}
				}
			}

			if ( ! empty( $found_products ) ) {
				echo wp_json_encode( $found_products );
			}

			die();
		}

		/**
		 * Function to Hide Notice Delete After Usage
		 */
		public function hide_notice_delete_after_usage() {

			check_ajax_referer( 'hide-smart-coupons-notice', 'security' );

			$current_user_id = get_current_user_id();
			update_user_meta( $current_user_id, 'hide_delete_credit_after_usage_notice', 'yes' );

			wp_send_json( array( 'message' => 'success' ) );

		}

	}

}

WC_SC_Ajax::get_instance();
