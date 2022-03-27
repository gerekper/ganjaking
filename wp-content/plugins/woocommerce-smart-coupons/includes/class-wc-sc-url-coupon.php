<?php
/**
 * Coupons via URL
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.8.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_URL_Coupon' ) ) {

	/**
	 * Class for handling coupons applied via URL
	 */
	class WC_SC_URL_Coupon {

		/**
		 * Variable to hold instance of WC_SC_URL_Coupon
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Variable to hold coupon notices
		 *
		 * @var $coupon_notices
		 */
		private $coupon_notices = array();

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'wp_loaded', array( $this, 'apply_coupon_from_url' ), 19 );
			add_action( 'wp_loaded', array( $this, 'apply_coupon_from_session' ), 20 );
			add_action( 'wp_loaded', array( $this, 'move_applied_coupon_from_cookies_to_account' ) );
			add_action( 'wp_head', array( $this, 'convert_sc_coupon_notices_to_wc_notices' ) );
			add_filter( 'the_content', array( $this, 'show_coupon_notices' ) );

		}

		/**
		 * Get single instance of WC_SC_URL_Coupon
		 *
		 * @return WC_SC_URL_Coupon Singleton object of WC_SC_URL_Coupon
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
		 * Apply coupon code if passed in url
		 */
		public function apply_coupon_from_url() {

			if ( empty( $_SERVER['QUERY_STRING'] ) ) {
				return;
			}

			parse_str( wp_unslash( $_SERVER['QUERY_STRING'] ), $coupon_args ); // phpcs:ignore
			$coupon_args = wc_clean( $coupon_args );

			if ( isset( $coupon_args['coupon-code'] ) && ! empty( $coupon_args['coupon-code'] ) ) {

				$coupon_args['coupon-code'] = urldecode( $coupon_args['coupon-code'] );

				$coupon_codes = explode( ',', $coupon_args['coupon-code'] );

				$coupon_codes = array_filter( $coupon_codes ); // Remove empty coupon codes if any.

				$cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;

				$coupons_data = array();

				foreach ( $coupon_codes as $coupon_index => $coupon_code ) {
					// Process only first five coupons to avoid GET request parameter limit.
					if ( apply_filters( 'wc_sc_max_url_coupons_limit', 5 ) === $coupon_index ) {
						break;
					}

					if ( empty( $coupon_code ) ) {
						continue;
					}

					$coupons_data[] = array(
						'coupon-code' => $coupon_code,
					);
				}

				if ( empty( $cart ) || WC()->cart->is_empty() ) {
					$is_hold = apply_filters(
						'wc_sc_hold_applied_coupons',
						true,
						array(
							'coupons_data' => $coupons_data,
							'source'       => $this,
						)
					);
					if ( true === $is_hold ) {
						$this->hold_applied_coupon( $coupons_data );
					}
					// Set a session cookie to persist the coupon in case the cart is empty. This code will persist the coupon even if the param sc-page is not supplied.
					WC()->session->set_customer_session_cookie( true ); // Thanks to: Devon Godfrey.
				} else {
					foreach ( $coupons_data as $coupon_data ) {
						$coupon_code = $coupon_data['coupon-code'];
						if ( ! WC()->cart->has_discount( $coupon_code ) ) {
							WC()->cart->add_discount( trim( $coupon_code ) );
						}
					}
				}

				if ( empty( $coupon_args['sc-page'] ) ) {
					return;
				}

				$redirect_url = '';

				if ( in_array( $coupon_args['sc-page'], array( 'shop', 'cart', 'checkout', 'myaccount' ), true ) ) {
					if ( $this->is_wc_gte_30() ) {
						$page_id = wc_get_page_id( $coupon_args['sc-page'] );
					} else {
						$page_id = woocommerce_get_page_id( $coupon_args['sc-page'] );
					}
					$redirect_url = get_permalink( $page_id );
				} elseif ( is_string( $coupon_args['sc-page'] ) ) {
					if ( is_numeric( $coupon_args['sc-page'] ) && ! is_float( $coupon_args['sc-page'] ) ) {
						$page = $coupon_args['sc-page'];
					} else {
						$page = ( function_exists( 'wpcom_vip_get_page_by_path' ) ) ? wpcom_vip_get_page_by_path( $coupon_args['sc-page'], OBJECT, get_post_types() ) : get_page_by_path( $coupon_args['sc-page'], OBJECT, get_post_types() ); // phpcs:ignore
					}
					$redirect_url = get_permalink( $page );
				} elseif ( is_numeric( $coupon_args['sc-page'] ) && ! is_float( $coupon_args['sc-page'] ) ) {
					$redirect_url = get_permalink( $coupon_args['sc-page'] );
				}

				if ( empty( $redirect_url ) ) {
					$redirect_url = home_url();
				}

				$redirect_url = $this->get_redirect_url_after_smart_coupons_process( $redirect_url );

				wp_safe_redirect( $redirect_url );

				exit;

			}

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function apply_coupon_from_session() {

			$cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;

			if ( empty( $cart ) || WC()->cart->is_empty() ) {
				return;
			}

			$user_id = get_current_user_id();

			if ( 0 === $user_id ) {
				$unique_id               = ( ! empty( $_COOKIE['sc_applied_coupon_profile_id'] ) ) ? wc_clean( wp_unslash( $_COOKIE['sc_applied_coupon_profile_id'] ) ) : ''; // phpcs:ignore
				$applied_coupon_from_url = ( ! empty( $unique_id ) ) ? $this->get_applied_coupons_by_guest_user( $unique_id ) : array();
			} else {
				$applied_coupon_from_url = get_user_meta( $user_id, 'sc_applied_coupon_from_url', true );
			}

			if ( empty( $applied_coupon_from_url ) ) {
				return;
			}

			foreach ( $applied_coupon_from_url as $index => $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );
				if ( $coupon->is_valid() && ! WC()->cart->has_discount( $coupon_code ) ) {
					WC()->cart->add_discount( trim( $coupon_code ) );
					unset( $applied_coupon_from_url[ $index ] );
				}
			}

			if ( 0 === $user_id ) {
				$this->set_applied_coupon_for_guest_user( $unique_id, $applied_coupon_from_url );
			} else {
				update_user_meta( $user_id, 'sc_applied_coupon_from_url', $applied_coupon_from_url );
			}

		}

		/**
		 * Apply coupon code from session, if any
		 *
		 * @param array $coupons_args The coupon arguments.
		 */
		public function hold_applied_coupon( $coupons_args = array() ) {

			$user_id      = get_current_user_id();
			$saved_status = array();

			if ( 0 === $user_id ) {
				$saved_status = $this->save_applied_coupon_in_cookie( $coupons_args );
			} else {
				$saved_status = $this->save_applied_coupon_in_account( $coupons_args, $user_id );
			}

			if ( ! empty( $saved_status ) ) {
				foreach ( $coupons_args as $coupon_args ) {
					$coupon_code = $coupon_args['coupon-code'];
					$save_status = isset( $saved_status[ $coupon_code ] ) ? $saved_status[ $coupon_code ] : '';
					if ( 'saved' === $save_status ) {
						/* translators: %s: $coupon_code coupon code */
						$notice = sprintf( __( 'Coupon code "%s" applied successfully. Please add some products to the cart to see the discount.', 'woocommerce-smart-coupons' ), $coupon_code );
						$this->set_coupon_notices( $notice, 'success' );
					} elseif ( 'already_saved' === $save_status ) {
						/* translators: %s: $coupon_code coupon code */
						$notice = sprintf( __( 'Coupon code "%s" already applied! Please add some products to the cart to see the discount.', 'woocommerce-smart-coupons' ), $coupon_code );
						$this->set_coupon_notices( $notice, 'error' );
					}
				}
			}

		}

		/**
		 * Apply coupon code from session, if any
		 *
		 * @param array $coupons_args The coupon arguments.
		 * @return array $saved_status
		 */
		public function save_applied_coupon_in_cookie( $coupons_args = array() ) {

			$saved_status = array(); // Variable to store whether coupons saved/already saved in cookie.

			if ( ! empty( $coupons_args ) ) {

				if ( empty( $_COOKIE['sc_applied_coupon_profile_id'] ) ) {
					$unique_id = $this->generate_unique_id();
				} else {
					$unique_id = wc_clean( wp_unslash( $_COOKIE['sc_applied_coupon_profile_id'] ) ); // phpcs:ignore
				}

				$applied_coupons = $this->get_applied_coupons_by_guest_user( $unique_id );

				foreach ( $coupons_args as $coupon_args ) {
					$coupon_code = isset( $coupon_args['coupon-code'] ) ? $coupon_args['coupon-code'] : '';
					if ( is_array( $applied_coupons ) && in_array( $coupon_code, $applied_coupons, true ) ) {
						$saved_status[ $coupon_code ] = 'already_saved';
					} else {
						$applied_coupons[]            = $coupon_code;
						$saved_status[ $coupon_code ] = 'saved';
					}
				}

				$this->set_applied_coupon_for_guest_user( $unique_id, $applied_coupons );
				wc_setcookie( 'sc_applied_coupon_profile_id', $unique_id, $this->get_cookie_life() );
			}

			return $saved_status;

		}

		/**
		 * Apply coupon code from session, if any
		 *
		 * @param array $coupons_args The coupon arguments.
		 * @param int   $user_id The user id.
		 * @return array $saved_status
		 */
		public function save_applied_coupon_in_account( $coupons_args = array(), $user_id = 0 ) {

			$saved_status = array(); // Variable to store whether coupons saved/already saved in user meta.

			if ( ! empty( $coupons_args ) ) {

				$applied_coupons = get_user_meta( $user_id, 'sc_applied_coupon_from_url', true );

				if ( empty( $applied_coupons ) ) {
					$applied_coupons = array();
				}

				foreach ( $coupons_args as $coupon_args ) {
					$coupon_code = $coupon_args['coupon-code'];
					if ( ! in_array( $coupon_code, $applied_coupons, true ) ) {
						$applied_coupons[]            = $coupon_args['coupon-code'];
						$saved_status[ $coupon_code ] = 'saved';
					} else {
						$saved_status[ $coupon_code ] = 'already_saved';
					}
				}

				update_user_meta( $user_id, 'sc_applied_coupon_from_url', $applied_coupons );
			}

			return $saved_status;

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function move_applied_coupon_from_cookies_to_account() {

			$user_id = get_current_user_id();

			if ( $user_id > 0 && ! empty( $_COOKIE['sc_applied_coupon_profile_id'] ) ) {

				$unique_id = wc_clean( wp_unslash( $_COOKIE['sc_applied_coupon_profile_id'] ) ); // phpcs:ignore

				$applied_coupons = $this->get_applied_coupons_by_guest_user( $unique_id );

				if ( false !== $applied_coupons && is_array( $applied_coupons ) && ! empty( $applied_coupons ) ) {

					$saved_coupons = get_user_meta( $user_id, 'sc_applied_coupon_from_url', true );
					if ( empty( $saved_coupons ) || ! is_array( $saved_coupons ) ) {
						$saved_coupons = array();
					}
					$saved_coupons = array_merge( $saved_coupons, $applied_coupons );
					update_user_meta( $user_id, 'sc_applied_coupon_from_url', $saved_coupons );
					wc_setcookie( 'sc_applied_coupon_profile_id', '' );
					$this->delete_applied_coupons_of_guest_user( $unique_id );
					delete_option( 'sc_applied_coupon_profile_' . $unique_id );
				}
			}

		}

		/**
		 * Function to get redirect URL after processing Smart Coupons params
		 *
		 * @param string $url The URL.
		 * @return string $url
		 */
		public function get_redirect_url_after_smart_coupons_process( $url = '' ) {

			if ( empty( $url ) ) {
				return $url;
			}

			$query_string = ( ! empty( $_SERVER['QUERY_STRING'] ) ) ? wc_clean( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : array(); // phpcs:ignore

			parse_str( $query_string, $url_args );

			$sc_params  = array( 'coupon-code', 'sc-page' );
			$url_params = array_diff_key( $url_args, array_flip( $sc_params ) );

			$redirect_url = apply_filters( 'wc_sc_redirect_url_after_smart_coupons_process', add_query_arg( $url_params, $url ), array( 'source' => $this ) );

			return $redirect_url;
		}

		/**
		 * Function to convert sc coupon notices to wc notices
		 */
		public function convert_sc_coupon_notices_to_wc_notices() {
			$coupon_notices = $this->get_coupon_notices();
			// If we have coupon notices to be shown and we are on a woocommerce page then convert them to wc notices.
			if ( count( $coupon_notices ) > 0 && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
				foreach ( $coupon_notices as $notice_type => $notices ) {
					if ( count( $notices ) > 0 ) {
						foreach ( $notices as $notice ) {
							wc_add_notice( $notice, $notice_type );
						}
					}
				}
				$this->remove_coupon_notices();
			}
		}

		/**
		 * Function to get sc coupon notices
		 */
		public function get_coupon_notices() {
			return apply_filters( 'wc_sc_coupon_notices', $this->coupon_notices );
		}

		/**
		 * Function to set sc coupon notices
		 *
		 * @param string $notice notice.
		 * @param string $type notice type.
		 */
		public function set_coupon_notices( $notice = '', $type = '' ) {
			if ( empty( $notice ) || empty( $type ) ) {
				return;
			}
			if ( empty( $this->coupon_notices[ $type ] ) || ! is_array( $this->coupon_notices[ $type ] ) ) {
				$this->coupon_notices[ $type ] = array();
			}
			$this->coupon_notices[ $type ][] = $notice;
		}

		/**
		 * Function to remove sc coupon notices
		 */
		public function remove_coupon_notices() {
			$this->coupon_notices = array();
		}

		/**
		 * Function to add coupon notices to wp content
		 *
		 * @param string $content page content.
		 * @return string $content page content
		 */
		public function show_coupon_notices( $content = '' ) {

			$coupon_notices = $this->get_coupon_notices();

			if ( count( $coupon_notices ) > 0 ) {

				// Buffer output.
				ob_start();

				foreach ( $coupon_notices as $notice_type => $notices ) {
					if ( count( $coupon_notices[ $notice_type ] ) > 0 ) {
						wc_get_template(
							"notices/{$notice_type}.php",
							array(
								'messages' => $coupon_notices[ $notice_type ],
							)
						);
					}
				}

				$notices = wc_kses_notice( ob_get_clean() );
				$content = $notices . $content;
				$this->remove_coupon_notices(); // Empty out notice data.
			}

			return $content;

		}

		/**
		 * Function to get coupon codes by guest user's unique id.
		 *
		 * @param  string $unique_id Unique id for guest user.
		 *
		 * @return array.
		 */
		public function get_applied_coupons_by_guest_user( $unique_id = '' ) {
			$key = sprintf( 'sc_applied_coupon_profile_%s', $unique_id );

			// Get coupons from `transient`.
			$coupons = get_transient( $key );
			if ( ! empty( $coupons ) && is_array( $coupons ) ) {
				return $coupons;
			}
			// Get coupon from `wp_option`.
			return get_option( $key, array() );
		}

		/**
		 * Function to set applied coupons for guest user.
		 *
		 * @param  string $unique_id Unique id for guest user.
		 * @param  array  $coupons   Array of coupon codes.
		 *
		 * @return bool.
		 */
		public function set_applied_coupon_for_guest_user( $unique_id = '', $coupons = array() ) {

			if ( ! empty( $unique_id ) && is_array( $coupons ) ) {
				$key = sprintf( 'sc_applied_coupon_profile_%s', $unique_id );

				if ( empty( $coupons ) ) {
					return delete_transient( $key );
				} else {
					return set_transient(
						$key,
						$coupons,
						apply_filters( 'wc_sc_applied_coupon_by_url_expire_time', MONTH_IN_SECONDS )
					);
				}
			}

			return false;
		}

		/**
		 * Function to delete all applied coupons for a guest user.
		 *
		 * @param  string $unique_id Unique id for guest user.
		 *
		 * @return bool.
		 */
		public function delete_applied_coupons_of_guest_user( $unique_id = '' ) {

			if ( ! empty( $unique_id ) ) {
				$key = sprintf( 'sc_applied_coupon_profile_%s', $unique_id );
				return delete_transient( $key );
			}

			return false;
		}

	}

}

WC_SC_URL_Coupon::get_instance();
