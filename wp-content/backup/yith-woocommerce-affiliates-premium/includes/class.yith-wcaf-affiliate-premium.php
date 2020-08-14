<?php
/**
 * Affiliate Premium class
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

if ( ! class_exists( 'YITH_WCAF_Affiliate_Premium' ) ) {
	/**
	 * WooCommerce Affiliate Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Affiliate_Premium extends YITH_WCAF_Affiliate {

		/**
		 * Single instance of the class for each token
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected static $instances = array();

		/**
		 * Referral history enabled
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_history_cookie_enabled = 'no';

		/**
		 * Referral history cookie name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_history_cookie_name = 'yith_wcaf_referral_history';

		/**
		 * Referral history cookie expiration time
		 *
		 * @var int
		 * @since 1.0.0
		 */
		protected $_history_cookie_exp = WEEK_IN_SECONDS;

		/**
		 * Whether or not to delete referral cookie after checkout
		 *
		 * @var string
		 * @since 1.0.7
		 */
		protected $_delete_cookie_after_checkout = 'yes';

		/**
		 * Whether persistent commission calculation is enabled
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_persistent_calculation = 'no';

		/**
		 * Whether token ID should be retrieved by query_string or checkout field
		 *
		 * @var string
		 * @since 1.0.6
		 */
		protected $_general_referral_cod = 'query_string';

		/**
		 * Whether persistent token should be changed whenever a new affiliation link is visited or not
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_avoid_referral_change = 'no';

		/**
		 * Whether cookie should change once set or not
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_make_cookie_change = 'no';

		/**
		 * Affiliate changes hostory
		 *
		 * @var array
		 * @since 1.0.0
		 */
		protected $_history;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Affiliate_Premium
		 * @since 1.0.0
		 */
		public function __construct( $token = null ) {
			parent::__construct( $token );

			// register checkout handling
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'register_history' ), 10, 1 );

			// add affiliate form on the checkout
			add_action( 'woocommerce_before_checkout_form', array( $this, 'print_affiliate_form_on_checkout' ) );

			// filter plugin options
			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ) );

			// add ajax handling
			add_action( 'wp_ajax_yith_wcaf_set_referrer', array( $this, 'set_referrer_via_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_wcaf_set_referrer', array( $this, 'set_referrer_via_ajax' ) );

			// register order completed/processing handling
			add_action( 'woocommerce_order_status_completed', array( $this, 'register_persistent_affiliate' ), 10, 1 );
			add_action( 'woocommerce_order_status_processing', array( $this, 'register_persistent_affiliate' ) );
		}

		/**
		 * Filter general settings, to add history settings
		 *
		 * @param $settings mixed Original settings array
		 *
		 * @return mixed Filtered settings array
		 * @since 1.0.0
		 */
		public function filter_general_settings( $settings ) {
			$settings_options      = $settings['settings'];
			$before_index          = 'cookie-referral-expiration';
			$before_index_position = array_search( $before_index, array_keys( $settings_options ) );

			$settings_options_chunk_1 = array_slice( $settings_options, 0, $before_index_position + 1 );
			$settings_options_chunk_2 = array_slice( $settings_options, $before_index_position + 1, count( $settings_options ) );

			/**
			 * @since 1.0.9
			 */
			$make_cookie_change_setting = array(
				'cookie-make-cookie-change' => array(
					'title'   => __( 'Referral cookie changes?', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Make cookie change if another referral link is visited', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_make_cookie_change',
					'default' => 'yes'
				)
			);

			$history_settings = array(
				'cookie-history-enable' => array(
					'title'   => __( 'Enable history storage', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Enable storage of referral changes', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_history_cookie_enable',
					'default' => 'yes'
				),

				'cookie-history-name' => array(
					'title'    => __( 'Referral history name', 'yith-woocommerce-affiliates' ),
					'type'     => 'text',
					'desc'     => __( 'Select name for cookie that will store referral token history. This name should be as unique as possible, to avoid collision with other plugins: If you change this setting, all cookies created before will no longer be effective', 'yith-woocommerce-affiliates' ),
					'id'       => 'yith_wcaf_history_cookie_name',
					'css'      => 'min-width: 300px;',
					'default'  => 'yith_wcaf_referral_history',
					'desc_tip' => true
				),

				'cookie-history-expire-needed' => array(
					'title'   => __( 'Make history cookie expire', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Check this option if you want to make history cookie expire', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_history_make_cookie_expire',
					'default' => 'yes'
				),

				'cookie-history-expiration' => array(
					'title'             => __( 'Referral history exp.', 'yith-woocommerce-affiliates' ),
					'type'              => 'number',
					'desc'              => __( 'Number of seconds that have to pass before referral history cookie expires', 'yith-woocommerce-affiliates' ),
					'id'                => 'yith_wcaf_history_cookie_expire',
					'css'               => 'min-width: 100px;',
					'default'           => WEEK_IN_SECONDS,
					'custom_attributes' => array(
						'min'  => 1,
						'max'  => 9999999999999,
						'step' => 1
					),
					'desc_tip'          => true
				),

				'cookie-delete-after-checkout' => array(
					'title'   => __( 'Delete affiliation cookie after checkout', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Whether to delete affiliation cookie after first customer referral checkout or not', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_delete_cookie_after_checkout',
					'default' => 'yes'
				)
			);

			$settings['settings'] = array_merge(
				$settings_options_chunk_1,
				$make_cookie_change_setting,
				$history_settings,
				$settings_options_chunk_2
			);

			return $settings;
		}

		/* === HELPER METHODS === */

		/**
		 * Return current referrer history
		 *
		 * @return mixed Current token history; false if none set
		 * @since 1.0.0
		 */
		public function get_history() {
			if ( ! empty( $this->_history ) ) {
				return $this->_history;
			}

			return false;
		}

		/* === INIT METHODS === */

		/**
		 * Init class attributes for admin options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_options() {
			parent::_retrieve_options();

			$make_cookie_expire = get_option( 'yith_wcaf_history_make_cookie_expire', 'yes' );
			$cookie_expire      = get_option( 'yith_wcaf_history_cookie_expire', $this->_ref_cookie_exp );

			$this->_general_referral_cod         = get_option( 'yith_wcaf_general_referral_cod', $this->_general_referral_cod );
			$this->_history_cookie_enabled       = get_option( 'yith_wcaf_history_cookie_enable', $this->_history_cookie_enabled );
			$this->_history_cookie_name          = get_option( 'yith_wcaf_history_cookie_name', $this->_history_cookie_name );
			$this->_history_cookie_exp           = ( $make_cookie_expire == 'yes' ) ? $cookie_expire : ( 15 * YEAR_IN_SECONDS );
			$this->_make_cookie_change           = get_option( 'yith_wcaf_make_cookie_change', $this->_make_cookie_change );
			$this->_delete_cookie_after_checkout = get_option( 'yith_wcaf_delete_cookie_after_checkout', $this->_delete_cookie_after_checkout );
			$this->_persistent_calculation       = get_option( 'yith_wcaf_commission_persistent_calculation', $this->_persistent_calculation );
			$this->_avoid_referral_change        = get_option( 'yith_wcaf_avoid_referral_change', $this->_avoid_referral_change );
		}

		/**
		 * Init class attribute for token
		 *
		 * @param $token string Token to be used, instead of retrieved one
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_token( $token ) {
			if ( is_null( $token ) ) {
				$change_token = ! isset( $_COOKIE[ $this->_ref_cookie_name ] ) || ( $this->_make_cookie_change == 'yes' );

				if ( isset( $_GET[ $this->_ref_name ] ) && $_GET[ $this->_ref_name ] != '' && $change_token ) {
					$token = $_GET[ $this->_ref_name ];

					// sets cookie for referrer id
					setcookie( $this->_ref_cookie_name, $_GET[ $this->_ref_name ], time() + intval( $this->_ref_cookie_exp ), COOKIEPATH, COOKIE_DOMAIN, false, true );

					if ( $this->_history_cookie_enabled == 'yes' ) {
						// sets cookie for referrer history
						$history = isset( $_COOKIE[ $this->_history_cookie_name ] ) ? explode( ',', $_COOKIE[ $this->_history_cookie_name ] ) : array( $token );
						if ( end( $history ) != $token ) {
							array_push( $history, $token );
						}

						$this->_history = $history;

						$history = implode( ',', $history );

						setcookie( $this->_history_cookie_name, $history, time() + intval( $this->_history_cookie_exp ), COOKIEPATH, COOKIE_DOMAIN, false, true );
					}

					// sets token origin as query-string
					$this->_token_origin = 'query-string';

					do_action( 'yith_wcaf_after_set_cookie' );
				} elseif ( isset( $_COOKIE[ $this->_ref_cookie_name ] ) ) {
					$token = $_COOKIE[ $this->_ref_cookie_name ];

					if ( isset( $_COOKIE[ $this->_history_cookie_name ] ) ) {
						$this->_history = explode( ',', $_COOKIE[ $this->_history_cookie_name ] );
					}

					// sets token origin as cookie
					$this->_token_origin = 'cookie';
				} else {
					$token               = false;
					$this->_history      = false;
					$this->_token_origin = false;
				}

				if ( is_user_logged_in() ) {
					$current_user_id  = get_current_user_id();
					$persistent_token = get_user_meta( $current_user_id, '_yith_wcaf_persistent_token', true );

					if (
						$this->_persistent_calculation == 'yes' &&
						$persistent_token &&
						apply_filters( 'yith_wcaf_apply_persistent_token', true, $current_user_id, $persistent_token )
					) {
						if ( $this->_avoid_referral_change == 'yes' ) {
							$token = $persistent_token;
						} else {
							$token = ( ! $token ) ? $persistent_token : $token;
						}

						if ( $token == $persistent_token ) {
							$this->_token_origin = 'persistent';
						}
					}
				}
			} else {
				$this->_token_origin = 'constructor';
			}

			if ( ! YITH_WCAF_Affiliate_Handler()->is_valid_token( $token ) ) {
				$token = false;
			}

			$this->_token = $token;
		}

		/* ==== FRONTEND METHODS === */

		/**
		 * Print affiliate form on checkout page, if option is enabled
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_affiliate_form_on_checkout() {
			if ( 'checkout' != $this->_general_referral_cod ) {
				return;
			}

			echo do_shortcode( '[yith_wcaf_set_referrer]' );
		}

		/* === CHECKOUT HANDLING METHODS === */

		/**
		 * Process checkout handling, registering order meta data
		 *
		 * @param $order_id int Order id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_checkout_handling( $order_id ) {
			if ( 'yes' === get_option( 'yith_wcaf_coupon_enable', 'no' ) ) {
				$coupon_referrer = false;
				$order           = wc_get_order( $order_id );

				if ( ! $order ) {
					return;
				}

				$coupon_items = $order->get_items( 'coupon' );

				// check if order contains any coupon bound to an affiliate account
				if ( ! empty( $coupon_items ) ) {
					foreach ( $coupon_items as $item ) {
						/**
						 * @var $item \WC_Order_Item_Coupon
						 */
						$coupon          = new WC_Coupon( $item->get_code() );
						$coupon_referrer = $coupon->get_meta( 'coupon_referrer', true );

						if ( ! empty( $coupon_referrer ) ) {
							// stop at first occurrence, even if more coupons needs to be processed
							break;
						}
					}
				}

				// if an affiliate's coupon was found, reset the class to grant correct commissions
				if ( $coupon_referrer ) {
					$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $coupon_referrer );

					if ( $affiliate ) {
						$this->_token        = $affiliate['token'];
						$this->_token_origin = 'coupon';
						$this->_affiliate    = $affiliate;
						$this->_retrieve_user();
					}
				}
			}

			parent::process_checkout_handling( $order_id );
		}

		/**
		 * Register persistent affiliate, if option enabled
		 *
		 * @param $order_id int Order id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_persistent_affiliate( $order_id ) {
			if ( $this->_persistent_calculation != 'yes' ) {
				return;
			}

			$order    = wc_get_order( $order_id );
			$customer = $order->get_user_id();
			$referral = get_post_meta( $order_id, '_yith_wcaf_referral', true );

			if ( ! $customer || ! $referral ) {
				return;
			}

			/**
			 * Filter yith_wcaf_updated_persisten_token
			 *
			 * @param $user_id  int Current user id
			 * @param $referral string Current referral token
			 * @param $order_id int Current order id (if any; null otherwise)
			 *
			 * @since 1.1.1
			 */
			do_action( 'yith_wcaf_updated_persisten_token', $customer, $referral, $order_id );

			update_user_meta( $customer, '_yith_wcaf_persistent_token', $referral );
		}

		/**
		 * Register affiliates history within order metas
		 *
		 * @param $order_id int Order id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_history( $order_id ) {
			if ( $this->_history_cookie_enabled == 'yes' ) {
				yit_save_prop( wc_get_order( $order_id ), '_yith_wcaf_referral_history', $this->_history );
			}

			// delete token cookie
			$this->delete_history_cookie_after_process();
		}

		/**
		 * Delete cookie after an order is processed with current token
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_history_cookie_after_process() {
			if ( $this->_history_cookie_enabled == 'yes' ) {
				setcookie( $this->_history_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false, true );
			}
		}

		/**
		 * Delete cookie after an order is processed with current token
		 *
		 * @return void
		 * @since 1.0.7
		 */
		public function delete_cookie_after_process() {
			if ( 'yes' == $this->_delete_cookie_after_checkout ) {
				parent::delete_cookie_after_process();
			}
		}

		/* === AJAX HANDLING METHODS === */

		/**
		 * Set affiliate token via ajax call
		 *
		 * @return void
		 * @since 1.0.6
		 */
		public function set_referrer_via_ajax() {
			check_ajax_referer( 'set-referrer', 'security' );

			if ( ! empty( $_POST['referrer_token'] ) && $token = wc_clean( $_POST['referrer_token'] ) ) {

				if ( ! YITH_WCAF_Affiliate_Handler()->is_valid_token( $token ) ) {
					wc_add_notice( apply_filters( 'yith_wcaf_invalid_token_error_message', __( 'The affiliate code you provided is not valid; please, double check it!', 'yith-woocommerce-affiliates' ) ), 'error' );
				} else {
					wc_add_notice( apply_filters( 'yith_wcaf_valid_token_success_message', __( 'Thanks! We will give this user special thanks!', 'yith-woocommerce-affiliates' ) ), 'success' );

					setcookie( $this->_ref_cookie_name, $token, time() + intval( $this->_ref_cookie_exp ), COOKIEPATH, COOKIE_DOMAIN, false, true );

					if ( $this->_history_cookie_enabled == 'yes' ) {
						// sets cookie for referrer history
						$history = isset( $_COOKIE[ $this->_history_cookie_name ] ) ? explode( ',', $_COOKIE[ $this->_history_cookie_name ] ) : array( $token );
						if ( end( $history ) != $token ) {
							array_push( $history, $token );
						}

						$history = implode( ',', $history );
						setcookie( $this->_history_cookie_name, $history, time() + intval( $this->_history_cookie_exp ), COOKIEPATH, COOKIE_DOMAIN, false, true );
						do_action( 'yith_wcaf_referrer_set' );
					}
				}
			} else {
				wc_add_notice( apply_filters( 'yith_wcaf_missing_token_error_message', __( 'Please, enter the affiliate code', 'yith-woocommerce-affiliates' ) ), 'error' );
			}

			wc_print_notices();
			die();
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Affiliate_Premium
		 * @since 1.0.0
		 */
		public static function get_instance( $token = null ) {

			/*
			 * When creating class from token, an instance is correctly set to token index
			 * Otherwise, if class loads automatically token from REQUEST, instance will be stored under 0 index
			 */

			if ( ! isset( self::$instances[ $token ] ) || is_null( self::$instances[ $token ] ) ) {
				self::$instances[ $token ] = new self;
			}

			return self::$instances[ $token ];
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Affiliate class
 *
 * @param $token string Unique affiliate token
 *
 * @return \YITH_WCAF_Affiliate
 * @since 1.0.0
 */
function YITH_WCAF_Affiliate_Premium( $token = null ) {
	return YITH_WCAF_Affiliate_Premium::get_instance( $token );
}