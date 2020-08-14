<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Subscription in My Account page
 *
 * @class   YWSBS_Subscription_My_Account
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_My_Account' ) ) {

	class YWSBS_Subscription_My_Account {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_My_Account
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Subscription_My_Account
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
		 */
		public function __construct() {

			add_action( 'woocommerce_before_my_account', array( $this, 'my_account_subscriptions' ) );
			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'subscriptions_related' ) );
			add_action( 'wp_loaded', array( $this, 'myaccount_actions' ), 90 );

			add_action( 'woocommerce_after_edit_address_form_billing', array( $this, 'my_account_edit_address' ), 10 );
			add_action( 'woocommerce_after_edit_address_form_shipping', array( $this, 'my_account_edit_address' ), 10 );
			add_action( 'woocommerce_after_save_address_validation', array( $this, 'check_my_account_save_address' ), 10, 2 );
			add_filter( 'woocommerce_address_to_edit', array( $this, 'fill_my_account_save_address' ), 10, 2 );

			add_shortcode( 'ywsbs_my_account_subscriptions', array( $this, 'my_account_subscriptions_shortcode' ) );

			// add endpoint view-quote
			add_action( 'init', array( $this, 'add_endpoint' ), 5 );
			add_action( 'woocommerce_account_view-subscription_endpoint', array( $this, 'load_subscription_detail_page' ), 1 );

		}

		public function check_my_account_save_address( $user_id, $load_address ) {
			if ( empty( $_POST['_ywsbs_edit'] ) || ! wp_verify_nonce( $_POST['_ywsbs_edit'], 'ywsbs_edit_address' ) || wc_notice_count( 'error' ) > 0 || ! in_array( $load_address, array( 'billing', 'shipping' ) ) ) {
				return;
			}

			$this->my_account_save_address( $user_id, $load_address );
		}

		public function fill_my_account_save_address( $address, $load_address ) {

			if ( ! isset( $_GET['subscription'] ) ) {
				return $address;
			}

			$subscription = ywsbs_get_subscription( $_GET['subscription'] );
			$sbs_fields   = $subscription->get_address_fields( $load_address );
			foreach ( $address as $key => $add ) {
					$address[ $key ]['value'] = $sbs_fields[ $key ];
			}

			return $address;
		}
		/**
		 * Add subscription section to my-account page
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function my_account_subscriptions() {
			wc_get_template( 'myaccount/my-subscriptions-view.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add subscription section to my-account page
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function my_account_subscriptions_shortcode() {
			ob_start();
			wc_get_template( 'myaccount/my-subscriptions-view.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
			return ob_get_clean();
		}

		/**
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function my_account_edit_address() {
			global $wp;

			if ( isset( $_GET['subscription'] ) ) {
				$subscription = ywsbs_get_subscription( $_GET['subscription'] );
				if ( get_current_user_id() == $subscription->user_id ) {
					echo '<p>' . esc_html__( 'Only the shipping address used for this subscription will be updated for future recurring payments', 'yith-woocommerce-subscription' );
					echo '<input type="hidden" name="ywsbs_edit_address_to_subscription" value="' . absint( $_GET['subscription'] ) . '" id="ywsbs_edit_address_to_subscription" />';
				}
			} elseif ( isset( $_GET['address'] ) || ( ( isset( $wp->query_vars['edit-address'] ) && ! empty( $wp->query_vars['edit-address'] ) ) ) ) {
				woocommerce_form_field(
					'change_subscriptions_addresses',
					array(
						'type'  => 'checkbox',
						'class' => array( 'form-row-wide' ),
						'label' => __( 'Update this address also for my active subscriptions', 'yith-woocommerce-subscription' ),
					)
				);
			}

			wp_nonce_field( 'ywsbs_edit_address', '_ywsbs_edit' );
		}

		/**
		 * @param $user_id
		 * @param $load_address
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function my_account_save_address( $user_id, $load_address ) {

			$fields = WC()->countries->get_address_fields( esc_attr( $_POST[ $load_address . '_country' ] ), $load_address . '_' );

			if ( isset( $_REQUEST['ywsbs_edit_address_to_subscription'] ) ) {
				// edit the address to single subscription
				$subscription_id = $_REQUEST['ywsbs_edit_address_to_subscription'];
				$subscription    = ywsbs_get_subscription( $subscription_id );
				$meta            = array();
				if ( $subscription->user_id == $user_id ) {
					foreach ( $fields as $key => $item ) {
						if ( isset( $_REQUEST[ $key ] ) ) {
							$meta[ '_' . $key ] = $_REQUEST[ $key ];
						}
					}
				}

				! empty( $meta ) && $subscription->update_subscription_meta( $meta );

				wp_safe_redirect( $subscription->get_view_subscription_url() );
				exit();

			} elseif ( isset( $_REQUEST['change_subscriptions_addresses'] ) ) {
				// edit the address to all subscriptions
				$subscriptions = YITH_WC_Subscription()->get_user_subscriptions( $user_id, 'active' );
				if ( $subscriptions ) {
					foreach ( $subscriptions as $sub_id ) {
						$sub  = ywsbs_get_subscription( $sub_id );
						$meta = array();

						foreach ( $fields as $key => $item ) {
							if ( isset( $_REQUEST[ $key ] ) ) {
								$meta[ '_' . $key ] = $_REQUEST[ $key ];
							}
						}

						! empty( $meta ) && $sub->update_subscription_meta( $meta );
					}
				}
			}
		}

		/**
		 * Add subscription section to my-account page
		 *
		 * @since   1.0.0
		 *
		 * @param $order
		 */
		public function subscriptions_related( $order ) {
			wc_get_template( 'myaccount/subscriptions-related.php', array( 'order' => $order ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the endpoint for the page in my account to manage the subscription view
		 *
		 * @since 1.0.0
		 */
		public function add_endpoint() {
			WC()->query->query_vars['view-subscription'] = get_option( 'woocommerce_myaccount_view_subscription_endpoint', 'view-subscription' );
		}

		/**
		 * Load the page of subscription
		 *
		 * @since 1.0.0
		 */
		public function load_subscription_detail_page() {
			global $wp, $post;

			if ( ! is_page( wc_get_page_id( 'myaccount' ) ) || ! isset( $wp->query_vars['view-subscription'] ) ) {
				return;
			}

			$this->view_subscription();
		}

		/**
		 * Show the quote detail
		 *
		 * @since 1.0.0
		 */
		public function view_subscription() {
			global $wp;
			if ( ! is_user_logged_in() ) {
				wc_get_template( 'myaccount/form-login.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
			} else {
				$subscription_id = $wp->query_vars['view-subscription'];
				$subscription    = new YWSBS_Subscription( $subscription_id );

				wc_get_template(
					'myaccount/view-subscription.php',
					array(
						'subscription' => $subscription,
						'user'         => get_user_by( 'id', get_current_user_id() ),
					),
					'',
					YITH_YWSBS_TEMPLATE_PATH . '/'
				);
			}
		}

		/**
		 * Change the status of subscription from myaccount page
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function myaccount_actions() {

			if ( isset( $_REQUEST['change_status'] ) && isset( $_REQUEST['subscription'] ) && isset( $_REQUEST['_wpnonce'] ) ) {

				$subscription = ywsbs_get_subscription( $_REQUEST['subscription'] );
				$new_status   = $_REQUEST['change_status'];

				if ( wp_verify_nonce( $_REQUEST['_wpnonce'], $subscription->id ) === false ) {
					wc_add_notice( __( 'This subscription cannot be updated. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );
				}

				if ( empty( $subscription ) || $subscription->user_id != get_current_user_id() ) {
					wc_add_notice( __( 'You seem to have already purchased this subscription ', 'yith-woocommerce-subscription' ), 'error' );
				}

				if ( $new_status == 'renew' ) {
					YITH_WC_Subscription()->renew_the_subscription( $subscription );
					$checkout_url = wc_get_checkout_url();
					wp_redirect( $checkout_url );
					exit;
				}

				YITH_WC_Subscription()->manual_change_status( $new_status, $subscription, 'customer' );
				wp_redirect( $subscription->get_view_subscription_url() );

				exit;

			} elseif ( isset( $_REQUEST['switch-variation'] ) && isset( $_REQUEST['subscription_id'] ) && isset( $_REQUEST['_wpnonce'] ) ) {

				$subscription = ywsbs_get_subscription( $_REQUEST['subscription_id'] );

				if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'switch-variation' ) === false ) {
					wc_add_notice( __( 'This subscription cannot be switched. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );
				}

				$variation_to_switch_id = $_REQUEST['switch-variation'];
				$variation_to_switch    = wc_get_product( $variation_to_switch_id );
				$variation              = wc_get_product( $subscription->variation_id );
				if ( $variation && $variation_to_switch ) {
					$variation_to_switch_priority    = yit_get_prop( $variation_to_switch, '_ywsbs_switchable_priority' );
					$variation_to_switch_gap_payment = yit_get_prop( $variation_to_switch, '_ywsbs_gap_payment' );
					$current_variation_priority      = yit_get_prop( $variation, '_ywsbs_switchable_priority' );
					$current_variation_priority      = ( $current_variation_priority ) ? $current_variation_priority : 0;

					update_post_meta( $subscription->id, 'ywsbs_switch_request', 'yes' );

					if ( $variation_to_switch_priority <= $current_variation_priority ) {

						YITH_WC_Subscription()->downgrade_process( $subscription->variation_id, $variation_to_switch_id, $subscription );
					} else {

						$pay_gap = ( isset( $_REQUEST['pay-gap'] ) && $variation_to_switch_gap_payment == 'yes' ) ? $_REQUEST['pay-gap-price'] : 0;
						YITH_WC_Subscription()->upgrade_process( $subscription->variation_id, $variation_to_switch_id, $subscription, $pay_gap );
					}
				}
			}

		}


	}
}

/**
 * Unique access to instance of YWSBS_Subscription_My_Account class
 *
 * @return \YWSBS_Subscription_My_Account
 */
function YWSBS_Subscription_My_Account() {
	return YWSBS_Subscription_My_Account::get_instance();
}
