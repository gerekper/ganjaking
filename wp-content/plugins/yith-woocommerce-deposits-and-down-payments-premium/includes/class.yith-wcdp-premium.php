<?php
/**
 * Main premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
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

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Premium' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Premium extends YITH_WCDP {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// change default WooCommerce emails
			add_filter( 'woocommerce_email_order_meta_fields', array( $this, 'add_parent_order' ), 10, 3 );

			// emails init
			add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'register_email_actions' ) );
			add_filter( 'woocommerce_locate_core_template', array( $this, 'register_woocommerce_template' ), 10, 3 );

			// register resend notification email
			if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
				add_filter( 'woocommerce_order_actions', array( $this, 'enable_resend_notify_email' ) );
				add_action( 'woocommerce_order_action_expiring_deposit', array( $this, 'resend_notify_email' ), 10, 1 );
			} else {
				add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'enable_resend_notify_email' ) );
			}

			// register shortcodes
			add_action( 'init', array( 'YITH_WCDP_Shortcode', 'init' ), 5 );

			YITH_WCDP_Compatibility();
		}

		/**
		 * Install plugin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function install() {
			YITH_WCDP_Suborders_Premium();

			// init frontend class
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				YITH_WCDP_Frontend_Premium();
			}

			// init handlers
			YITH_WCDP_Deposits_Handler();
		}

		/* === HELPER METHODS === */

		/**
		 * Return true if deposit is default option; false otherwise
		 *
		 * @param $product_id int|bool Product id, if specified; false otherwise. If no product id is provided, global $product will be used
		 *
		 * @return bool Whether deposit is default option for product
		 * @since 1.0.0
		 */
		public function is_deposit_default( $product_id = false, $variation_id = false ) {
			global $product;

			$default_deposit = 'default';
			$product         = ! $product_id ? $product : ( is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id );

			// get variation specific option
			if ( $variation_id ) {
				$variation       = wc_get_product( $variation_id );
				$default_deposit = yit_get_prop( $variation, '_deposit_default', true );
				$default_deposit = ! empty( $default_deposit ) ? $default_deposit : 'default';
			}

			// get product specific option
			if ( $default_deposit == 'default' ) {
				$default_deposit = yit_get_prop( $product, '_deposit_default', true );
				$default_deposit = ! empty( $default_deposit ) ? $default_deposit : 'default';
			}

			$default_deposit = ( $default_deposit == 'default' ) ? get_option( 'yith_wcdp_general_deposit_default', 'no' ) : $default_deposit;

			return $default_deposit == 'yes';
		}

		/**
		 * Return true if deposit is enabled on product
		 *
		 * @param $product_id int|bool Product id, if specified; false otherwise. If no product id is provided, global $product will be used
		 *
		 * @return bool Whether deposit is enabled for product
		 * @since 1.0.0
		 */
		public function is_deposit_enabled_on_product( $product_id = false, $variation_id = false ) {
			global $product;

			$product                             = ! $product_id ? $product : ( is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id );
			$deposit_enabled                     = 'default';
			$deposit_expiration_product_fallback = 'default';

			// get global options
			$plugin_enabled = get_option( 'yith_wcdp_general_enable', 'yes' );

			// get variation specific metas
			if ( $variation_id ) {
				$variation       = wc_get_product( $variation_id );
				$deposit_enabled = yit_get_prop( $variation, '_enable_deposit', true );
				$deposit_enabled = ! empty( $deposit_enabled ) ? $deposit_enabled : 'default';

				$deposit_expiration_product_fallback = $variation->get_meta( '_deposit_expiration_product_fallback', true );
				$deposit_expiration_product_fallback = ! empty( $deposit_expiration_product_fallback ) ? $deposit_expiration_product_fallback : 'default';
			}

			// get product specific option
			if ( $deposit_enabled == 'default' ) {
				$deposit_enabled = yit_get_prop( $product, '_enable_deposit', true );
				$deposit_enabled = ! empty( $deposit_enabled ) ? $deposit_enabled : 'default';
			}

			$deposit_enabled = ( $deposit_enabled == 'default' ) ? get_option( 'yith_wcdp_general_deposit_enable', 'no' ) : $deposit_enabled;

			// get product specific expiration fallback
			if ( $deposit_expiration_product_fallback == 'default' ) {
				$deposit_expiration_product_fallback = $product->get_meta( '_deposit_expiration_product_fallback', true );
				$deposit_expiration_product_fallback = ! empty( $deposit_expiration_product_fallback ) ? $deposit_expiration_product_fallback : 'default';
			}

			$deposit_expiration_product_fallback = ( $deposit_expiration_product_fallback == 'default' ) ? get_option( 'yith_wcdp_deposits_expiration_product_fallback', 'disable_deposit' ) : $deposit_expiration_product_fallback;

			$deposit_expired = $deposit_expiration_product_fallback == 'disable_deposit' ? $this->is_deposit_expired_for_product( $product_id, $variation_id ) : false;

			return apply_filters( 'yith_wcdp_is_deposit_enabled_on_product', $plugin_enabled == 'yes' && $deposit_enabled == 'yes' && ! $deposit_expired, $product_id, $variation_id );
		}

		/**
		 * Return true in deposit is mandatory for a product
		 *
		 * @param $product_id int|bool Product id, if specified; false otherwise. If no product id is provided, global $product will be used
		 *
		 * @return bool Whether deposit is enabled for product
		 * @since 1.0.0
		 */
		public function is_deposit_mandatory( $product_id = false, $variation_id = false ) {
			global $product;

			$product           = ! $product_id ? $product : ( is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id );
			$deposit_mandatory = 'default';

			// get variation specific option
			if ( $variation_id ) {
				$variation         = wc_get_product( $variation_id );
				$deposit_mandatory = yit_get_prop( $variation, '_force_deposit', true );
				$deposit_mandatory = ! empty( $deposit_mandatory ) ? $deposit_mandatory : 'default';
			}

			// get product specific option
			if ( $deposit_mandatory == 'default' ) {
				$deposit_mandatory = yit_get_prop( $product, '_force_deposit', true );
				$deposit_mandatory = ! empty( $deposit_mandatory ) ? $deposit_mandatory : 'default';
			}

			$deposit_mandatory = ( $deposit_mandatory == 'default' ) ? get_option( 'yith_wcdp_general_deposit_force', 'no' ) : $deposit_mandatory;

			return apply_filters( 'yith_wcdp_is_deposit_mandatory', $deposit_mandatory == 'yes', $product_id, $variation_id );
		}

		/**
		 * Return tru if deposit has expired on product
		 *
		 * @param $product_id   int|bool Product id, if specified; false otherwise. If no product id is provided, global $product will be used
		 * @param $variation_id int|bool Variation id, if specified; false otherwise.
		 *
		 * @return bool Whether or not deposit has expired for the product
		 */
		public function is_deposit_expired_for_product( $product_id = false, $variation_id = false ) {
			global $product;

			$deposit_expires         = get_option( 'yith_wcdp_deposit_expiration_enable', 'no' );
			$deposit_expiration_type = get_option( 'yith_wcdp_deposits_expiration_type', 'num_of_days' );

			$deposit_expired = false;
			if ( $deposit_expires == 'yes' && $deposit_expiration_type == 'specific_date' ) {
				$expiration_date = false;

				if ( $variation_id ) {
					$variation                 = wc_get_product( $variation_id );
					$expiration_date_variation = $variation ? $variation->get_meta( '_deposit_expiration_date', true ) : false;

					if ( $expiration_date_variation ) {
						$expiration_date = $expiration_date_variation;
					}
				}

				if ( ! $expiration_date && $product_id ) {
					$product_obj             = ! $product_id ? $product : ( is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id );
					$expiration_date_product = $product_obj ? $product_obj->get_meta( '_deposit_expiration_date', true ) : false;

					if ( $expiration_date_product ) {
						$expiration_date = $expiration_date_product;
					}
				}

				if ( ! $expiration_date ) {
					$expiration_date_general = get_option( 'yith_wcdp_deposits_expiration_date', false );
					$expiration_date         = $expiration_date_general;
				}

				if ( ! $expiration_date ) {
					return false;
				}

				$deposit_expired = $expiration_date < date( 'Y-m-d 00:00:00' );
			}

			return apply_filters( 'yith_wcdp_is_deposit_expired_for_product', $deposit_expired, $product_id, $variation_id );
		}

		/**
		 * Retrieve deposit type
		 *
		 * @return string Deposit type (rate, amount)
		 * @since 1.0.0
		 */
		public function get_deposit_type( $product_id = false, $customer_id = false, $variation_id = false ) {
			global $product;

			$product_obj   = ( ! $product_id ) ? $product : wc_get_product( $product_id );
			$variation_obj = ( ! $variation_id ) ? false : wc_get_product( $variation_id );
			$customer_obj  = ( ! $customer_id ) ? wp_get_current_user() : get_user_by( 'id', $customer_id );

			// set default value
			$deposit_type = get_option( 'yith_wcdp_general_deposit_type', 'amount' );

			// if no product or customer, return default value
			if ( ! $product_obj || ! $customer_obj ) {
				return $deposit_type;
			}

			// retrieve first customer role ( multiple roles are allowed only via code, so we retrieve only first one )
			$customer_role = isset( $customer_obj->roles[0] ) ? $customer_obj->roles[0] : false;

			// retrieve product categories
			$product_categories_raw = get_the_terms( $product_obj->get_id(), 'product_cat' );
			$product_categories     = array();

			if ( ! empty( $product_categories_raw ) ) {
				foreach ( $product_categories_raw as $term ) {
					$product_categories[] = $term->term_id;
				}
			}

			// retrieve options for deposits
			$role_specific_deposit     = get_option( 'yith_wcdp_user_role_deposits' );
			$product_specific_deposit  = get_option( 'yith_wcdp_product_deposits' );
			$category_specific_deposit = get_option( 'yith_wcdp_category_deposits' );

			$product_terms_with_deposits = array();
			if ( ! empty( $category_specific_deposit ) ) {
				// retrieve product categories with specific deposit value set
				$product_terms_with_deposits = array_values( array_intersect( array_keys( $category_specific_deposit ), $product_categories ) );
			}

			// first, check user-role specific deposit type
			if ( isset( $role_specific_deposit[ $customer_role ] ) ) {
				$deposit_type = $role_specific_deposit[ $customer_role ]['type'];
			} // if no user-role specific deposit is set, check variation specific deposit type
			elseif ( $variation_obj && isset( $product_specific_deposit[ $variation_obj->get_id() ] ) ) {
				$deposit_type = $product_specific_deposit[ $variation_obj->get_id() ]['type'];
			} // if no variation specific deposit is set, check product specific deposit type
			elseif ( isset( $product_specific_deposit[ $product_obj->get_id() ] ) ) {
				$deposit_type = $product_specific_deposit[ $product_obj->get_id() ]['type'];
			} // if no product specific deposit type is set, check product-category specific deposit type
			elseif ( ! empty( $product_terms_with_deposits ) ) {
				$deposit_type = $category_specific_deposit[ $product_terms_with_deposits[0] ]['type'];
			}

			return apply_filters( 'yith_wcdp_deposit_type', $deposit_type, $product_id, $variation_id, $customer_id );
		}

		/**
		 * Retrieve deposit amount (needed on amount deposit type)
		 *
		 * @return string Amount
		 * @since 1.0.0
		 */
		public function get_deposit_amount( $product_id = false, $customer_id = false, $variation_id = false ) {
			global $product;

			$product_obj   = ( ! $product_id ) ? $product : wc_get_product( $product_id );
			$variation_obj = ( ! $variation_id ) ? false : wc_get_product( $variation_id );
			$customer_obj  = ( ! $customer_id ) ? wp_get_current_user() : get_user_by( 'id', $customer_id );

			// set default value
			$deposit_amount = get_option( 'yith_wcdp_general_deposit_amount', 0 );

			// if no product or customer, return default value
			if ( ! $product_obj || ! $customer_obj ) {
				return $deposit_amount;
			}

			// retrieve first customer role ( multiple roles are allowed only via code, so we retrieve only first one )
			$customer_role = isset( $customer_obj->roles[0] ) ? $customer_obj->roles[0] : false;

			// retrieve product categories
			$product_categories_raw = get_the_terms( $product_obj->get_id(), 'product_cat' );
			$product_categories     = array();

			if ( ! empty( $product_categories_raw ) ) {
				foreach ( $product_categories_raw as $term ) {
					$product_categories[] = $term->term_id;
				}
			}

			// retrieve options for deposits
			$role_specific_deposit     = get_option( 'yith_wcdp_user_role_deposits' );
			$product_specific_deposit  = get_option( 'yith_wcdp_product_deposits' );
			$category_specific_deposit = get_option( 'yith_wcdp_category_deposits' );

			$product_terms_with_deposits = array();
			if ( ! empty( $category_specific_deposit ) ) {
				// retrieve product categories with specific deposit value set
				$product_terms_with_deposits = array_values( array_intersect( array_keys( $category_specific_deposit ), $product_categories ) );
			}

			// first, check user-role specific deposit amount
			if ( isset( $role_specific_deposit[ $customer_role ] ) ) {
				$deposit_amount = $role_specific_deposit[ $customer_role ]['value'];
			} // if no user-role specific deposit is set, check variation specific deposit amount
			elseif ( $variation_obj && isset( $product_specific_deposit[ $variation_obj->get_id() ] ) ) {
				$deposit_amount = $product_specific_deposit[ $variation_obj->get_id() ]['value'];
			} // if no variation specific deposit is set, check product specific deposit amount
			elseif ( isset( $product_specific_deposit[ $product_obj->get_id() ] ) ) {
				$deposit_amount = $product_specific_deposit[ $product_obj->get_id() ]['value'];
			} // if no product specific deposit amount is set, check product-category specific deposit amount
			elseif ( ! empty( $product_terms_with_deposits ) ) {
				$deposit_amount = $category_specific_deposit[ $product_terms_with_deposits[0] ]['value'];
			}

			return apply_filters( 'yith_wcdp_deposit_amount', $deposit_amount, $product_id, $variation_id, $customer_id );
		}

		/**
		 * Retrieve deposit rate (needed on rate deposit type)
		 *
		 * @return string Amount
		 * @since 1.0.0
		 */
		public function get_deposit_rate( $product_id = false, $customer_id = false, $variation_id = false ) {
			global $product;

			$product_obj   = ( ! $product_id ) ? $product : wc_get_product( $product_id );
			$variation_obj = ( ! $variation_id ) ? false : wc_get_product( $variation_id );
			$customer_obj  = ( ! $customer_id ) ? wp_get_current_user() : get_user_by( 'id', $customer_id );

			// set default value
			$deposit_rate = get_option( 'yith_wcdp_general_deposit_rate', 0 );

			// if no product or customer, return default value
			if ( ! $product_obj || ! $customer_obj ) {
				return $deposit_rate;
			}

			// retrieve first customer role ( multiple roles are allowed only via code, so we retrieve only first one )
			$customer_role = isset( $customer_obj->roles[0] ) ? $customer_obj->roles[0] : false;

			// retrieve product categories
			$product_categories_raw = get_the_terms( $product_obj->get_id(), 'product_cat' );
			$product_categories     = array();

			if ( ! empty( $product_categories_raw ) ) {
				foreach ( $product_categories_raw as $term ) {
					$product_categories[] = $term->term_id;
				}
			}

			// retrieve options for deposits
			$role_specific_deposit     = get_option( 'yith_wcdp_user_role_deposits' );
			$product_specific_deposit  = get_option( 'yith_wcdp_product_deposits' );
			$category_specific_deposit = get_option( 'yith_wcdp_category_deposits' );

			$product_terms_with_deposits = array();
			if ( ! empty( $category_specific_deposit ) ) {
				// retrieve product categories with specific deposit value set
				$product_terms_with_deposits = array_values( array_intersect( array_keys( $category_specific_deposit ), $product_categories ) );
			}

			// first, check user-role specific deposit amount
			if ( isset( $role_specific_deposit[ $customer_role ] ) ) {
				$deposit_rate = $role_specific_deposit[ $customer_role ]['value'];
			} // if no user-role specific deposit is set, check variation specific deposit amount
			elseif ( $variation_obj && isset( $product_specific_deposit[ $variation_obj->get_id() ] ) ) {
				$deposit_rate = $product_specific_deposit[ $variation_obj->get_id() ]['value'];
			} // if no variation specific deposit is set, check product specific deposit amount
			elseif ( isset( $product_specific_deposit[ $product_obj->get_id() ] ) ) {
				$deposit_rate = $product_specific_deposit[ $product_obj->get_id() ]['value'];
			} // if no product specific deposit amount is set, check product-category specific deposit amount
			elseif ( ! empty( $product_terms_with_deposits ) ) {
				$deposit_rate = $category_specific_deposit[ $product_terms_with_deposits[0] ]['value'];
			}

			return apply_filters( 'yith_wcdp_deposit_rate', $deposit_rate, $product_id, $variation_id, $customer_id );
		}

		/**
		 * Calculate deposit for product and variation passed as param
		 *
		 * @param $product_id   int Product id
		 * @param $price        double|bool Current product price (often third party plugin changes cart item price); false to use price from product object
		 * @param $customer_id  int|bool Customer id; default to false, to consider current user
		 * @param $variation_id int|bool Variation id; default to false, to consider main product
		 *
		 * @return double Deposit amount for specified product and variation
		 * @since 1.0.4
		 */
		public function get_deposit( $product_id, $price = false, $context = 'edit', $customer_id = false, $variation_id = false ) {
			$product = wc_get_product( $variation_id ? $variation_id : $product_id );

			if ( ! $product ) {
				return 0;
			}

			$price          = 'view' == $context ? yith_wcdp_get_price_to_display( $product, array_merge( array( 'qty' => 1 ), $price ? array( 'price' => $price ) : array() ) ) : ( $price ? $price : $product->get_price() );
			$price          = floatval( apply_filters( 'yith_wcdp_product_price_for_deposit_operation', $price, $product ) );
			$deposit_type   = $this->get_deposit_type( $product_id, $customer_id, $variation_id );
			$deposit_amount = $this->get_deposit_amount( $product_id, $customer_id, $variation_id );
			$deposit_rate   = $this->get_deposit_rate( $product_id, $customer_id, $variation_id );
			$deposit_value  = 0;

			if ( $deposit_type == 'rate' ) {
				$deposit_value = $price * (double) $deposit_rate / 100;
			} elseif ( $deposit_type == 'amount' ) {
				$deposit_value = 'view' == $context ? yith_wcdp_get_price_to_display( $product, array(
					'qty'   => 1,
					'price' => $deposit_amount
				) ) : $deposit_amount;
			}

			$deposit_value = min( $deposit_value, $price );

			return $deposit_value;
		}

		/* === EMAIL METHODS === */

		/**
		 * Prints Deposit on email for the admin
		 *
		 * @param $fields        array Array of fields to filter
		 * @param $sent_to_admin bool Whether email is sent to admin
		 * @param $order         \WC_Order Current order
		 *
		 * @return array Filtered array of fields
		 */
		public function add_parent_order( $fields, $sent_to_admin, $order ) {
			if ( ! $sent_to_admin ) {
				return $fields;
			}

			$order_id         = yit_get_order_id( $order );
			$has_full_payment = yit_get_prop( $order, '_has_full_payment' );

			if ( ! $has_full_payment ) {
				return $fields;
			}

			$parent_order_id = YITH_WCDP_Suborders()->get_parent_order( $order_id );

			if ( $parent_order_id ) {
				$fields[] = array(
					'label' => apply_filters( 'yith_wcdp_deposit_label', __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ),
					'value' => sprintf( '<a href="%s">%s</a>', get_edit_post_link( $parent_order_id ), $parent_order_id )
				);
			}

			return $fields;
		}

		/**
		 * Register email classes for deposits
		 *
		 * @param $classes mixed Array of email class instances
		 *
		 * @return mixed Filtered array of email class instances
		 * @since 1.0.0
		 */
		public function register_email_classes( $classes ) {
			require_once( YITH_WCDP_INC . 'emails/class.yith-wcdp-emails.php' );

			$classes['YITH_WCDP_Customer_Deposit_Created_Email']  = include_once( YITH_WCDP_INC . 'emails/class.yith-wcdp-customer-deposit-created-email.php' );
			$classes['YITH_WCDP_Admin_Deposit_Created_Email']     = include_once( YITH_WCDP_INC . 'emails/class.yith-wcdp-admin-deposit-created-email.php' );
			$classes['YITH_WCDP_Customer_Deposit_Expiring_Email'] = include_once( YITH_WCDP_INC . 'emails/class.yith-wcdp-customer-deposit-expiring-email.php' );

			return $classes;
		}

		/**
		 * Register email action for deposists
		 *
		 * @param $emails mixed Array of registered actions
		 *
		 * @return mixed Filtered array of registered actions
		 * @since 1.0.0
		 */
		public function register_email_actions( $emails ) {
			$emails = array_merge(
				$emails,
				array(
					'yith_wcdp_deposits_created',
					'yith_wcdp_deposits_expiring'
				)
			);

			return $emails;
		}

		/**
		 * Adds notify email to re-send options
		 *
		 * @param $resend_emails mixed Enabled emails for resend
		 *
		 * @return mixed Filtered array of mails that can be sent again
		 * @since 1.0.0
		 */
		public function enable_resend_notify_email( $resend_emails ) {
			global $post;

			// check if global post define
			if ( ! $post ) {
				return $resend_emails;
			}

			// retrieve order object
			$order = wc_get_order( $post );

			// check if current global post is an order
			if ( ! $order ) {
				return $resend_emails;
			}

			// check if current order has a deposit
			if ( ! yit_get_prop( $order, '_has_deposit' ) ) {
				return $resend_emails;
			}

			// retrieve current order suborders
			$suborders = YITH_WCDP_Suborders_Premium()->get_suborder( yit_get_prop( $order, 'id' ) );

			// check if order have suborders
			if ( ! $suborders ) {
				return $resend_emails;
			}

			// enable "re-send notify email" only if at least one suborder is not expired, and not completed or cancelled
			$resend_available = false;
			foreach ( $suborders as $suborder_id ) {
				$suborder = wc_get_order( $suborder_id );

				if ( ! yit_get_prop( $suborder, 'has_expired' ) && ! in_array( $suborder->get_status(), array(
						'completed',
						'processing',
						'cancelled'
					) ) ) {
					$resend_available = true;
				}
			}

			// enable "re-send notify email"
			if ( $resend_available ) {
				if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
					$resend_emails['expiring_deposit'] = __( 'Expiring deposit notification email for customer', 'yith-woocommerce-deposits-and-down-payments' );
					$resend_emails['new_deposit']      = __( 'New deposit created notification email for customer', 'yith-woocommerce-deposits-and-down-payments' );
				} else {
					$resend_emails[] = 'expiring_deposit';
					$resend_emails[] = 'new_deposit';
				}
			}

			return $resend_emails;
		}

		/**
		 * Resend expiring sub-orders notification
		 *
		 * @param $order \WC_Order Current order
		 *
		 * @return void
		 * @since 1.1.2
		 */
		public function resend_notify_email( $order ) {
			/**
			 * @var $email \YITH_WCDP_Customer_Deposit_Expiring_Email class instance
			 */
			$email = WC()->mailer()->emails['YITH_WCDP_Customer_Deposit_Expiring_Email'];
			$email->trigger( yit_get_order_id( $order ) );
		}

		/**
		 * Locate default templates of woocommerce in plugin, if exists
		 *
		 * @param $core_file     string
		 * @param $template      string
		 * @param $template_base string
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function register_woocommerce_template( $core_file, $template, $template_base ) {
			$located = yith_wcdp_locate_template( $template );

			if ( $located && file_exists( $located ) ) {
				return $located;
			} else {
				return $core_file;
			}
		}

		/* === CHECKOUT PROCESS METHODS === */

		/**
		 * Update cart item when deposit is selected
		 *
		 * @param $cart_item mixed Current cart item
		 *
		 * @return mixed Filtered cart item
		 * @since 1.0.0
		 */
		public function update_cart_item( $cart_item ) {
			/**
			 * @var $product \WC_Product
			 */
			$product      = $cart_item['data'];
			$product_id   = $product->is_type( 'variation' ) ? yit_get_prop( $product, 'parent_id' ) : $product->get_id();
			$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : false;

			if ( $this->is_deposit_enabled_on_product( $product_id, $variation_id ) && ! yit_get_prop( $product, 'yith_wcdp_deposit', true ) && ! apply_filters( 'yith_wcdp_skip_cart_item_processing', false, $cart_item ) ) {
				$deposit_forced = $this->is_deposit_mandatory( $product_id, $variation_id );

				$deposit_value   = apply_filters( 'yith_wcdp_deposit_value', $this->get_deposit( $product_id, $product->get_price(), 'edit', false, $variation_id ), $product_id, $variation_id, $cart_item );
				$deposit_balance = apply_filters( 'yith_wcdp_deposit_balance', max( $product->get_price() - $deposit_value, 0 ), $product_id, $variation_id, $cart_item );

				if (
					apply_filters( 'yith_wcdp_process_cart_item_product_change', true, $cart_item ) &&
					isset( $_REQUEST['add-to-cart'] ) &&
					( ( $deposit_forced && ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) || ( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'deposit' ) )
				) {
					yit_set_prop( $cart_item['data'], 'price', $deposit_value );
					yit_set_prop( $cart_item['data'], 'yith_wcdp_deposit', true );

					if ( apply_filters( 'yith_wcdp_virtual_on_deposit', true, null ) ) {
						yit_set_prop( $cart_item['data'], 'virtual', 'yes' );
					}

					$cart_item['deposit_value']   = $deposit_value;
					$cart_item['deposit_balance'] = $deposit_balance;
				}
			}

			return $cart_item;
		}

		/**
		 * Add cart item data when deposit is selected, to store info to save with order
		 *
		 * @param $cart_item_data mixed Currently saved cart item data
		 * @param $product_id     int   Product id
		 *
		 * @return mixed Filtered cart item data
		 * @since 1.0.0
		 */
		public function update_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			$product      = wc_get_product( ! empty( $variation_id ) ? $variation_id : $product_id );
			$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : false;

			if ( $this->is_deposit_enabled_on_product( $product_id, $variation_id ) && ! apply_filters( 'yith_wcdp_skip_cart_item_data_processing', false, $cart_item_data, $product ) ) {
				$deposit_forced = $this->is_deposit_mandatory( $product_id, $variation_id );

				$deposit_type   = $this->get_deposit_type( $product_id, false, $variation_id );
				$deposit_amount = $this->get_deposit_amount( $product_id, false, $variation_id );
				$deposit_rate   = $this->get_deposit_rate( $product_id, false, $variation_id );

				$deposit_value   = $this->get_deposit( $product_id, $product->get_price(), 'edit', false, $variation_id );
				$deposit_balance = max( (double) $product->get_price() - (double) $deposit_value, 0 );

				$process_deposit = ( $deposit_forced && ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) || ( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'deposit' );

				if ( apply_filters( 'yith_wcdp_process_deposit', $process_deposit, $cart_item_data ) ) {
					$cart_item_data['deposit']                 = true;
					$cart_item_data['deposit_type']            = $deposit_type;
					$cart_item_data['deposit_amount']          = $deposit_amount;
					$cart_item_data['deposit_rate']            = $deposit_rate;
					$cart_item_data['deposit_value']           = $deposit_value;
					$cart_item_data['deposit_balance']         = $deposit_balance;
					$cart_item_data['deposit_shipping_method'] = isset( $_POST['shipping_method'] ) ? $_POST['shipping_method'] : false;
				}
			}

			return $cart_item_data;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCDP_Premium class
 *
 * @return \YITH_WCDP_Premium
 * @since 1.0.0
 */
function YITH_WCDP_Premium() {
	return YITH_WCDP_Premium::get_instance();
}