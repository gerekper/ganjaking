<?php
/**
 * Affiliate class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Affiliate' ) ) {
	/**
	 * WooCommerce Affiliate
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Affiliate {

		/**
		 * Single instance of the class for each token
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected static $instances = array();

		/**
		 * Referral token variable name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_ref_name = 'ref';

		/**
		 * Referral token variable name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_ref_cookie_name = 'yith_wcaf_referral_token';

		/**
		 * Referral token variable name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_ref_cookie_exp = WEEK_IN_SECONDS;

		/**
		 * Affiliate token
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_token;

		/**
		 * Token origin (query-string, cookie)
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_token_origin;

		/**
		 * Affiliate user
		 *
		 * @var array
		 * @since 1.0.0
		 */
		protected $_affiliate;

		/**
		 * Affiliate user
		 *
		 * @var \WP_User
		 * @since 1.0.0
		 */
		protected $_user;

		/**
		 * Affiliate rate
		 *
		 * @var float
		 * @since 1.0.0
		 */
		protected $_rate;

		/**
		 * Constructor method
		 *
		 * @since 1.0.0
		 */
		public function __construct( $token = null ) {
			// retrieve options.
			$this->_retrieve_options();

			// init affiliate object.
			$this->_retrieve_token( $token );
			$this->_retrieve_user();
			$this->_retrieve_affiliate();

			// register checkout handling.
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'process_checkout_handling' ), 10, 1 );

			// delete commissions for awaiting payment orders.
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'delete_commissions_for_order_awaiting_payment' ) );

			// PayPal hotfix.
			add_filter( 'woocommerce_paypal_args', array( $this, 'hotfix_paypal_return_url' ), 10, 1 );
		}

		/* === HELPER METHODS === */

		/**
		 * Return current ref variable name
		 *
		 * @return string Ref variable name
		 * @since 1.0.0
		 */
		public function get_ref_name() {
			if ( ! empty( $this->_ref_name ) ) {
				return $this->_ref_name;
			}

			return 'ref';
		}

		/**
		 * Return currently set token
		 *
		 * @return string|bool Current token; false if none set
		 * @since 1.0.0
		 */
		public function get_token() {
			if ( ! empty( $this->_token ) ) {
				return $this->_token;
			}

			return false;
		}

		/**
		 * Return token origin (cookie/query-string/constructor)
		 *
		 * @return string|bool Current token origin; false if none set
		 * @since 1.0.0
		 */
		public function get_token_origin() {
			if ( ! empty( $this->_token_origin ) ) {
				return $this->_token_origin;
			}

			return false;
		}

		/**
		 * Return current affiliate user
		 *
		 * @return WP_User|bool Current affiliate user; false if none set
		 * @since 1.0.0
		 */
		public function get_user() {
			if ( ! empty( $this->_user ) ) {
				return $this->_user;
			}

			return false;
		}

		/**
		 * Return current affiliate data
		 *
		 * @return mixed Current affiliate user; false if none set
		 * @since 1.0.0
		 */
		public function get_affiliate() {
			if ( ! empty( $this->_affiliate ) ) {
				return $this->_affiliate;
			}

			return false;
		}

		/**
		 * Executes again _retrieve_options()
		 * Used to let third party plugin customize options on per-affiliate basis
		 *
		 * @since 1.0.9
		 */
		public function reset_options() {
			$this->_retrieve_options();
		}

		/**
		 * Executes again _retrieve_token()
		 * Please, note that this method should be called *ALWAYS* before template_redirect, as this is the last safe hook
		 * to set a cookie (_retrieve_token() calls set cookie)
		 *
		 * @return bool/string New token retrieved; false if something went wrong
		 * @since 1.0.9
		 */
		public function reset_token() {
			if ( function_exists( 'did_action' ) && did_action( 'template_redirect' ) ) {
				return false;
			}

			$token = null;
			if ( 'constructor' == $this->_token_origin ) {
				$token = $this->_token;
			}

			$this->_retrieve_token( $token );

			return $this->_token;
		}

		/* === INIT METHODS === */

		/**
		 * Init class attributes for admin options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_options() {
			$make_cookie_expire = get_option( 'yith_wcaf_referral_make_cookie_expire', 'yes' );
			$cookie_expire      = get_option( 'yith_wcaf_referral_cookie_expire', $this->_ref_cookie_exp );

			$this->_ref_name        = get_option( 'yith_wcaf_referral_var_name', $this->_ref_name );
			$this->_ref_cookie_name = get_option( 'yith_wcaf_referral_cookie_name', $this->_ref_cookie_name );
			$this->_ref_cookie_exp  = ( 'yes' == $make_cookie_expire ) ? $cookie_expire : ( 15 * YEAR_IN_SECONDS );
		}

		/**
		 * Init class attribute for token
		 *
		 * @param string $token Token to be used, instead of retrieved one.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_token( $token ) {
			if ( is_null( $token ) ) {
				if ( isset( $_GET[ $this->_ref_name ] ) && '' != $_GET[ $this->_ref_name ] ) {
					$token = sanitize_text_field( wp_unslash( $_GET[ $this->_ref_name ] ) );

					// sets cookie for referrer id.
					setcookie( $this->_ref_cookie_name, $token, time() + intval( $this->_ref_cookie_exp ), COOKIEPATH, COOKIE_DOMAIN, false, true );

					// sets token origin as query-string,
					$this->_token_origin = 'query-string';
				} elseif ( isset( $_COOKIE[ $this->_ref_cookie_name ] ) ) {
					$token = sanitize_text_field( wp_unslash( $_COOKIE[ $this->_ref_cookie_name ] ) );

					// sets token origin as cookie
					$this->_token_origin = 'cookie';
				} else {
					$token               = false;
					$this->_token_origin = false;
				}
			} else {
				$this->_token_origin = 'constructor';
			}

			if ( ! YITH_WCAF_Affiliate_Handler()->is_valid_token( $token ) ) {
				$token = false;
			}

			$this->_token = $token;
		}

		/**
		 * Init class attribute for token-related user
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_user() {
			if ( empty( $this->_token ) ) {
				return;
			}

			$this->_user = YITH_WCAF_Affiliate_Handler()->get_user_by_token( $this->_token );
		}

		/**
		 * Init class attribute for token-related affiliate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_affiliate() {
			if ( empty( $this->_token ) ) {
				return;
			}

			$this->_affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $this->_token );
		}

		/* === AFFILIATE TOTAL METHODS === */

		/**
		 * Get affiliate total earnings
		 *
		 * @param bool $update Whether to update affiliate object before fetching earnings.
		 *
		 * @return float Total earnings
		 * @since 1.0.0
		 */
		public function get_total( $update = false ) {
			if ( $update ) {
				$this->_retrieve_affiliate();
			}

			if ( ! $this->_affiliate ) {
				return 0;
			}

			return (float) $this->_affiliate['earnings'];
		}

		/**
		 * Update affiliate total earning
		 *
		 * @param float $amount Value to sum to total affiliate earnings (a relative float value).
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_total( $amount ) {
			if ( ! $this->_affiliate ) {
				return;
			}

			$total_user_commissions = $this->_affiliate['earnings'];
			$total_user_commissions += (float) $amount;
			$total_user_commissions = $total_user_commissions > 0 ? $total_user_commissions : 0;

			YITH_WCAF_Affiliate_Handler()->update( $this->_affiliate['ID'], array( 'earnings' => $total_user_commissions ) );
			$this->_affiliate['earnings'] = $total_user_commissions;
		}

		/* === CHECKOUT HANDLING METHODS === */

		/**
		 * Process checkout handling, registering order meta data
		 *
		 * @param int $order_id Order id.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_checkout_handling( $order_id ) {
			$affiliate_token = $this->_affiliate['token'];
			$order           = wc_get_order( $order_id );

			if ( ! empty( $this->_token ) ) {
				do_action( 'yith_wcaf_process_checkout_with_affiliate', $order, $affiliate_token );
				// create order commissions.
				YITH_WCAF_Commission_Handler()->create_order_commissions( $order_id, $affiliate_token, $this->_token_origin );

				// register hit.
				yit_save_prop( $order, '_yith_wcaf_click_id', YITH_WCAF_Click_Handler()->get_last_hit() );

			}

			// delete token cookie.
			$this->delete_cookie_after_process();
		}

		/**
		 * Delete commissions and restore affiliate for orders awaiting payments
		 *
		 * @return void
		 * @since 1.0.5
		 */
		public function delete_commissions_for_order_awaiting_payment() {
			// Insert or update the post data.
			$order_id = absint( WC()->session->order_awaiting_payment );

			// Resume the unpaid order if its pending.
			if ( $order_id > 0 && ( $order = wc_get_order( $order_id ) ) && $order->has_status( array( 'pending', 'failed' ) ) ) {
				$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array( 'order_id' => $order_id ) );

				if ( ! empty( $commissions ) ) {
					foreach ( $commissions as $commission ) {
						YITH_WCAF_Commission_Handler()->delete( $commission['ID'] );
					}
				}

				// re-init affiliate class with session order store affiliate.
				$token = yit_get_prop( $order, '_yith_wcaf_referral', true );
				$this->_retrieve_token( $token );
				$this->_retrieve_user();
				$this->_retrieve_affiliate();
			}
		}

		/**
		 * Delete cookie after an order is processed with current token
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_cookie_after_process() {
			setcookie( $this->_ref_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false, true );
		}

		/* === MISC === */

		/**
		 * Changes return url, to make sure that user would return to store with referral code, if there was any when he left
		 *
		 * @param array $args Array of arguments for PayPal processing.
		 * @return array Array of filtered arguments for PayPal processing.
		 */
		public function hotfix_paypal_return_url( $args ) {
			$token = $this->get_token();

			if ( $token && isset( $args['cancel_return'] ) ) {
				$ref_name = YITH_WCAF_Affiliate_Handler()->get_ref_name();
				$args['cancel_return'] = add_query_arg( $ref_name, $token, $args['cancel_return'] );
			}

			return $args;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @param string $token Affiliate token.
		 *
		 * @return \YITH_WCAF_Affiliate
		 * @since 1.0.2
		 */
		public static function get_instance( $token = null ) {
			/*
			 * When creating class from token, an instance is correctly set to token index
			 * Otherwise, if class loads automatically token from REQUEST, instance will be stored under 0 index
			 */

			if ( class_exists( 'YITH_WCAF_Affiliate_Premium' ) ) {
				return YITH_WCAF_Affiliate_Premium::get_instance( $token );
			} else {
				if ( ! isset( self::$instances[ $token ] ) || is_null( self::$instances[ $token ] ) ) {
					self::$instances[ $token ] = new self();
				}

				return self::$instances[ $token ];
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Affiliate class
 *
 * @param string $token Unique affiliate token.
 *
 * @return \YITH_WCAF_Affiliate
 * @since 1.0.0
 */
function YITH_WCAF_Affiliate( $token = null ) {
	return YITH_WCAF_Affiliate::get_instance( $token );
}