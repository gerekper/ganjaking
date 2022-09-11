<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_My_Account Class.
 *
 * @class   YWSBS_Subscription_My_Account
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_My_Account' ) ) {

	/**
	 * Class YWSBS_Subscription_My_Account
	 */
	class YWSBS_Subscription_My_Account {
		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_My_Account
		 */
		protected static $instance;

		/**
		 * Subscription actions arguments
		 *
		 * @var array
		 */
		protected $subscription_actions_args = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_My_Account
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'myaccount_actions' ), 90 );

			add_action( 'woocommerce_after_edit_address_form_billing', array( $this, 'my_account_edit_address' ), 10 );
			add_action( 'woocommerce_after_edit_address_form_shipping', array( $this, 'my_account_edit_address' ), 10 );

			add_action( 'woocommerce_after_save_address_validation', array( $this, 'check_my_account_save_address' ), 10, 2 );
			add_filter( 'woocommerce_address_to_edit', array( $this, 'fill_my_account_save_address' ), 10, 2 );

			// Endpoints.
			add_action( 'woocommerce_account_subscriptions_endpoint', array( $this, 'subscriptions_page' ), 1 );
			add_action( 'woocommerce_account_view-subscription_endpoint', array( $this, 'load_subscription_detail_page' ), 1 );

			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_subscription_menu_item' ), 20 );
			add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'set_subscription_menu_active_on_view_subscription' ), 10, 2 );

			add_filter( 'woocommerce_endpoint_subscriptions_title', array( $this, 'load_subscriptions_title' ) );
			add_filter( 'woocommerce_endpoint_view-subscription_title', array( $this, 'load_subscriptions_title' ) );

			// View Subscription Actions.
			add_action( 'ywsbs_after_subscription_status', array( $this, 'show_subscription_action' ) );
			add_action( 'ywsbs_after_view_subscription', array( $this, 'add_modal_to_action' ) );

			// View Subscription Switch.
			add_action( 'ywsbs_after_subscription_plan_info', array( $this, 'show_subscription_switch' ) );

			add_action( 'woocommerce_order_needs_payment', array( $this, 'check_order_needs_payment' ), 10, 3 );

			add_action( 'init', array( $this, 'init' ), 30 );
		}

		/**
		 * Check if the order can be paid on my account page
		 *
		 * @param bool     $needs_payment The order needs payment.
		 * @param WC_Order $order Order.
		 * @param array    $valid_order_statuses Status valid to pay an order.
		 *
		 * @return bool
		 */
		public function check_order_needs_payment( $needs_payment, $order, $valid_order_statuses ) {
			if ( $needs_payment ) {
				return true;
			}

			$subscriptions = $order->get_meta( 'subscriptions' );

			return ! empty( $subscriptions ) && in_array( $order->get_status(), $valid_order_statuses, true );
		}

		/**
		 * Init method to set proteo icon.
		 */
		public function init() {
			if ( defined( 'YITH_PROTEO_VERSION' ) ) {
				add_filter( 'yith_proteo_myaccount_custom_icon', array( $this, 'customize_my_account_proteo_icon' ), 10, 2 );
			}
		}

		/**
		 * Active the subscription menu inside the view subscription page.
		 *
		 * @param array  $classes Class list.
		 * @param string $endpoint Current item menu.
		 *
		 * @return array
		 */
		public function set_subscription_menu_active_on_view_subscription( $classes, $endpoint ) {
			global $wp;

			if ( YITH_WC_Subscription::$endpoint === $endpoint && isset( $wp->query_vars['view-subscription'] ) ) {
				array_push( $classes, 'is-active' );
			}

			return $classes;
		}

		/**
		 * Change the title of the endpoint.
		 *
		 * @param string $title .
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function load_subscriptions_title( $title ) {
			return esc_html__( 'Your Subscriptions', 'yith-woocommerce-subscription' );
		}

		/**
		 * My Account edit address.
		 */
		public function my_account_edit_address() {

			global $wp;

			if ( isset( $_GET['subscription'] ) ) { // phpcs:ignore
				$subscription_id = $_GET['subscription']; // phpcs:ignore
				$subscription    = ywsbs_get_subscription( $subscription_id );
				if ( get_current_user_id() === $subscription->get_user_id() ) {
					echo '<p>' . esc_html__( 'Only the shipping address used for this subscription will be updated for future recurring payments.', 'yith-woocommerce-subscription' );
					echo '<input type="hidden" name="ywsbs_edit_address_to_subscription" value="' . esc_attr( absint( $subscription_id ) ) . '" id="ywsbs_edit_address_to_subscription" />';
				}
			} elseif ( isset( $_GET['address'] ) || ( ( isset( $wp->query_vars['edit-address'] ) && ! empty( $wp->query_vars['edit-address'] ) ) ) ) { //phpcs:ignore
				woocommerce_form_field(
					'change_subscriptions_addresses',
					array(
						'type'  => 'checkbox',
						'class' => array( 'form-row-wide' ),
						'label' => esc_html__( 'Update this address also for my active subscriptions.', 'yith-woocommerce-subscription' ),
					)
				);
			}

			wp_nonce_field( 'ywsbs_edit_address', '_ywsbs_edit' );
		}

		/**
		 * Check My Account Save Address.
		 *
		 * @param int    $user_id User ID being saved.
		 * @param string $load_address Type of address e.g. billing or shipping.
		 */
		public function check_my_account_save_address( $user_id, $load_address ) {
			if ( ! isset( $_POST['_ywsbs_edit'] ) || empty( $_POST['_ywsbs_edit'] ) || ! wp_verify_nonce( $_POST['_ywsbs_edit'], 'ywsbs_edit_address' ) || wc_notice_count( 'error' ) > 0 || ! in_array( $load_address, array( 'billing', 'shipping' ) ) ) { // phpcs:ignore
				return;
			}

			$this->my_account_save_address( $user_id, $load_address );
		}

		/**
		 * Save My Account Address.
		 *
		 * @param int    $user_id User ID being saved.
		 * @param string $load_address Type of address e.g. billing or shipping.
		 */
		public function my_account_save_address( $user_id, $load_address ) {

			$posted = $_REQUEST; // phpcs:ignore

			$fields = WC()->countries->get_address_fields( esc_attr( $posted[ $load_address . '_country' ] ), $load_address . '_' );

			if ( isset( $posted['ywsbs_edit_address_to_subscription'] ) ) {
				// edit the address to single subscription.
				$subscription_id = $posted['ywsbs_edit_address_to_subscription'];
				$subscription    = ywsbs_get_subscription( $subscription_id );
				$meta            = array();
				if ( $subscription->get_user_id() === $user_id ) {

					foreach ( $fields as $key => $item ) {
						if ( isset( $posted[ $key ] ) ) {

							$meta[ '_' . $key ] = $posted[ $key ];
						}
					}
				}

				! empty( $meta ) && $subscription->update_subscription_meta( $meta );

				wp_safe_redirect( ywsbs_get_view_subscription_url( $subscription_id ) );
				exit();

			} elseif ( isset( $posted['change_subscriptions_addresses'] ) ) {
				// edit the address to all subscriptions.
				$subscriptions = YITH_WC_Subscription()->get_user_subscriptions( $user_id, 'active' );
				if ( $subscriptions ) {
					foreach ( $subscriptions as $sub_id ) {
						$sub  = ywsbs_get_subscription( $sub_id );
						$meta = array();

						foreach ( $fields as $key => $item ) {
							if ( isset( $posted[ $key ] ) ) {
								$meta[ '_' . $key ] = $posted[ $key ];
							}
						}

						! empty( $meta ) && $sub->update_subscription_meta( $meta );
					}
				}
			}
		}

		/**
		 * Fill My Account Address.
		 *
		 * @param array  $address Address.
		 * @param string $load_address Type of address e.g. billing or shipping.
		 *
		 * @return array
		 * @throws Exception Return Error.
		 */
		public function fill_my_account_save_address( $address, $load_address ) {

			if ( ! isset( $_GET['subscription'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $address;
			}

			$subscription = ywsbs_get_subscription( $_GET['subscription'] );  // phpcs:ignore
			$sbs_fields   = $subscription->get_address_fields( $load_address );
			foreach ( $address as $key => $add ) {
				$address[ $key ]['value'] = $sbs_fields[ $key ];
			}

			return $address;
		}

		/**
		 * Change the status of subscription from myaccount page
		 *
		 * @throws Exception Resubscribe the subscription error, downgrade or upgrade subscription.
		 * @since  1.0.0
		 */
		public function myaccount_actions() {

			$posted = $_REQUEST; // phpcs:ignore

			if ( isset( $posted['change_status'], $posted['subscription'], $posted['_wpnonce'] ) ) {

				$subscription = ywsbs_get_subscription( $posted['subscription'] );
				$new_status   = $posted['change_status'];

				if ( wp_verify_nonce( $posted['_wpnonce'], $subscription->id ) === false ) {
					wc_add_notice( esc_html__( 'This subscription cannot be updated. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );
				}

				if ( empty( $subscription ) || get_current_user_id() !== $subscription->get_user_id() ) {
					wc_add_notice( esc_html__( 'You seem to have already purchased this subscription ', 'yith-woocommerce-subscription' ), 'error' );
				}

				if ( 'renew' === $new_status ) {
					YITH_WC_Subscription()->renew_the_subscription( $subscription );
					$checkout_url = wc_get_checkout_url();
					wp_safe_redirect( $checkout_url );
					exit;
				}

				YITH_WC_Subscription()->manual_change_status( $new_status, $subscription, 'customer' );
				wp_safe_redirect( ywsbs_get_view_subscription_url( $subscription->get_id() ) );

				exit;

			} elseif ( isset( $posted['switch-variation'] ) && isset( $posted['subscription_id'] ) && isset( $posted['_wpnonce'] ) ) {

				if ( wp_verify_nonce( $posted['_wpnonce'], 'switch-variation' ) === false ) {
					wc_add_notice( __( 'This subscription cannot be switched. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );
				}

				$subscription = ywsbs_get_subscription( $posted['subscription_id'] );

				$variation_to_switch_id = $posted['switch-variation'];
				$variation_to_switch    = wc_get_product( $variation_to_switch_id );
				$variation              = wc_get_product( $subscription->get_variation_id() );

				if ( $variation && $variation_to_switch ) {
					$variation_to_switch_priority    = $variation_to_switch->get_meta( '_ywsbs_switchable_priority' );
					$variation_to_switch_gap_payment = $variation_to_switch->get_meta( '_ywsbs_gap_payment' );
					$current_variation_priority      = $variation->get_meta( '_ywsbs_switchable_priority' );
					$current_variation_priority      = ( $current_variation_priority ) ? $current_variation_priority : 0;

					$subscription->set( 'ywsbs_switch_request', 'yes' );

					if ( $variation_to_switch_priority <= $current_variation_priority ) {
						YITH_WC_Subscription()->downgrade_process( $subscription->get_variation_id(), $variation_to_switch_id, $subscription );
					} else {
						$pay_gap = ( isset( $posted['pay-gap'] ) && 'yes' === $variation_to_switch_gap_payment ) ? $posted['pay-gap-price'] : 0;

						YITH_WC_Subscription()->upgrade_process( $subscription->get_variation_id(), $variation_to_switch_id, $subscription, $pay_gap );
					}
				}
			}
		}


		/**
		 * Load the page of subscription
		 *
		 * @since 1.0.0
		 */
		public function load_subscription_detail_page() {
			global $wp;

			if ( ! is_page( wc_get_page_id( 'myaccount' ) ) || ! isset( $wp->query_vars['view-subscription'] ) ) {
				return;
			}

			$this->view_subscription();
		}

		/**
		 * Load the page with subscriptions
		 *
		 * @param int $current_page Current page.
		 *
		 * @since 1.0.0
		 */
		public function subscriptions_page( $current_page ) {
			global $wp;

			$current_page = empty( $current_page ) ? 1 : absint( $current_page );

			if ( ! is_page( wc_get_page_id( 'myaccount' ) ) || ! isset( $wp->query_vars['subscriptions'] ) ) {
				return;
			}

			echo YWSBS_Subscription_Shortcodes::my_account_subscriptions_shortcode( array( 'page' => $current_page ) ); // phpcs:ignore
		}

		/**
		 * Show the subscription detail
		 *
		 * @since 1.0.0
		 */
		public function view_subscription() {
			global $wp;
			if ( ! is_user_logged_in() ) {
				wc_get_template( 'myaccount/form-login.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
			} else {
				$subscription_id = $wp->query_vars['view-subscription'];
				$subscription    = ywsbs_get_subscription( $subscription_id );

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
		 * Return all it is necessary to set the actions inside the subscription detail page.
		 *
		 * @param YWSBS_Subscription $subscription Current Subscription.
		 *
		 * @return array|mixed|void
		 */
		public function get_subscription_action_args( $subscription ) {

			if ( isset( $this->subscription_actions_args[ $subscription->get_id() ] ) ) {
				return $this->subscription_actions_args[ $subscription->get_id() ];
			}

			$style  = get_option( 'ywsbs_subscription_action_style', 'buttons' );
			$pause  = false;
			$cancel = false;
			$resume = false;

			if ( $subscription->can_be_paused() ) {
				$text_for_modal    = '';
				$text_for_dropdown = '';
				$pause_options     = YWSBS_Subscription_Helper()->get_subscription_product_pause_options( $subscription );

				if ( 'dropdown' === $style ) {
					$dropdown_text = get_option( 'ywsbs_text_pause_subscription_dropdown' );
					if ( ! empty( $dropdown_text ) ) {
						$dropdown_text     = str_replace( '{{max_pause_period}}', $pause_options['max_pause_duration'] . ' ' . __( 'days', 'yith-woocommerce-subscription' ), $dropdown_text );
						$text_for_dropdown = str_replace( '{{max_pause_number}}', $pause_options['max_pause'], $dropdown_text );
					}
				}

				$modal_pause_text   = get_option( 'ywsbs_text_pause_subscription_modal' );
				$modal_button_label = get_option( 'ywsbs_text_pause_subscription_button' );
				if ( ! empty( $modal_pause_text ) ) {
					$modal_pause_text = str_replace( '{{max_pause_period}}', $pause_options['max_pause_duration'] . ' ' . __( 'days', 'yith-woocommerce-subscription' ), $modal_pause_text );
					$text_for_modal   = str_replace( '{{max_pause_number}}', $pause_options['max_pause'], $modal_pause_text );
				}

				$pause = array(
					'pause_info'         => $pause_options,
					'button_label'       => esc_html__( 'Pause', 'yith-woocommerce-subscription' ),
					'dropdown_text'      => $text_for_dropdown,
					'modal_text'         => $text_for_modal,
					'modal_button_label' => $modal_button_label,
					'close_modal_button' => get_option( 'ywsbs_text_close_modal' ),
					'nonce'              => wp_create_nonce( 'ywsbs_pause_subscription' ),
				);
			}

			if ( $subscription->can_be_cancelled() ) {

				$dropdown_text = '';

				if ( 'dropdown' === $style ) {
					$dropdown_text = get_option( 'ywsbs_text_cancel_subscription_dropdown' );
				}

				$modal_cancel_text = get_option( 'ywsbs_text_cancel_subscription_modal' );

				$cancel = array(
					'dropdown_text'      => $dropdown_text,
					'modal_text'         => $modal_cancel_text,
					'button_label'       => esc_html__( 'Cancel', 'yith-woocommerce-subscription' ),
					'modal_button_label' => get_option( 'ywsbs_text_cancel_subscription_button' ),
					'close_modal_button' => get_option( 'ywsbs_text_close_modal' ),
					'nonce'              => wp_create_nonce( 'ywsbs_cancel_subscription' ),
				);
			}

			if ( $subscription->can_be_resumed() ) {

				$dropdown_text = '';

				if ( 'dropdown' === $style ) {
					$dropdown_text = get_option( 'ywsbs_text_resume_subscription_dropdown' );
				}

				$modal_resume_text = get_option( 'ywsbs_text_resume_subscription_modal' );

				$resume = array(
					'dropdown_text'      => $dropdown_text,
					'modal_text'         => $modal_resume_text,
					'button_label'       => esc_html__( 'Resume', 'yith-woocommerce-subscription' ),
					'modal_button_label' => get_option( 'ywsbs_text_resume_subscription_button' ),
					'close_modal_button' => get_option( 'ywsbs_text_resume_subscription_close_button' ),
					'nonce'              => wp_create_nonce( 'ywsbs_resume_subscription' ),
				);
			}

			if ( ! $cancel && ! $pause && ! $resume ) {
				return false;
			}

			$args = array(
				'style'        => get_option( 'ywsbs_subscription_action_style', 'buttons' ),
				'subscription' => $subscription,
				'pause'        => $pause,
				'cancel'       => $cancel,
				'resume'       => $resume,
			);

			$this->subscription_actions_args[ $subscription->get_id() ] = $args;

			return $args;
		}

		/**
		 * Add Subscription Switch options inside the Subscription View page
		 *
		 * @param YWSBS_Subscription $subscription Current Subscription.
		 */
		public function show_subscription_switch( $subscription ) {

			if ( $subscription->can_be_switchable() ) {

				$switchable_variations = YWSBS_Subscription_Switch::get_switchable_variations( $subscription );

				if ( ! empty( $switchable_variations ) ) {
					$args = array(
						'subscription'          => $subscription,
						'switchable_variations' => $switchable_variations,
					);

					wc_get_template( 'myaccount/ywsbs-subscription-switch.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );

				}
			}

		}

		/**
		 * Add Subscription Actions inside the Subscription View page
		 *
		 * @param YWSBS_Subscription $subscription Current Subscription.
		 */
		public function show_subscription_action( $subscription ) {

			$args = $this->get_subscription_action_args( $subscription );

			$args && wc_get_template( 'myaccount/ywsbs-subscription-actions.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add Subscription Actions Modal inside the Subscription View page
		 *
		 * @param YWSBS_Subscription $subscription Current Subscription.
		 */
		public function add_modal_to_action( $subscription ) {

			$args = $this->get_subscription_action_args( $subscription );

			$args && wc_get_template( 'myaccount/ywsbs-subscription-action-modals.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}


		/**
		 * Add the menu item on WooCommerce My account Menu
		 * before the Logout item menu.
		 *
		 * @param array $wc_menu WooCommerce menu list.
		 *
		 * @return mixed
		 */
		public function add_subscription_menu_item( $wc_menu ) {

			if ( isset( $wc_menu['customer-logout'] ) ) {
				$logout = $wc_menu['customer-logout'];
				unset( $wc_menu['customer-logout'] );
			}

			$wc_menu['subscriptions'] = esc_html__( 'Subscriptions', 'yith-woocommerce-subscription' );

			if ( isset( $logout ) ) {
				$wc_menu['customer-logout'] = $logout;
			}

			return $wc_menu;
		}


		/**
		 * Change the icon inside my account on Proteo Theme.
		 *
		 * @param string $icon Icon.
		 * @param string $endpoint Endpoint.
		 *
		 * @return string
		 */
		public function customize_my_account_proteo_icon( $icon, $endpoint ) {

			if ( 'subscriptions' === $endpoint ) {
				return '<span class="yith-proteo-myaccount-icons ywsbs-icon ywsbs-icon-dollar lnr"></span>';
			}

			return $icon;
		}


		/**
		 * Add subscription section on my-account page
		 *
		 * @return  string
		 * @deprecated 2.0.0
		 * @since   1.0.0
		 */
		public function my_account_subscriptions_shortcode() {
			_deprecated_function( 'YWSBS_Subscription_My_Account::my_account_subscriptions_shortcode', '2.0.0', 'YWSBS_Subscription_Shortcodes::my_account_subscriptions_shortcode' );

			return YWSBS_Subscription_Shortcodes::my_account_subscriptions_shortcode();
		}
	}
}

/**
 * Unique access to instance of YWSBS_Subscription_My_Account class
 *
 * @return YWSBS_Subscription_My_Account
 */
function YWSBS_Subscription_My_Account() { // phpcs:ignore
	return YWSBS_Subscription_My_Account::get_instance();
}
