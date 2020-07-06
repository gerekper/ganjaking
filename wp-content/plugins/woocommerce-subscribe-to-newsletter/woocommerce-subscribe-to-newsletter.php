<?php
/**
 * Plugin Name: WooCommerce Subscribe to Newsletter
 * Plugin URI: https://woocommerce.com/products/newsletter-subscription/
 * Description: Allow users to subscribe to your newsletter via the checkout page and via a sidebar widget. Supports MailChimp and Campaign Monitor and also MailChimp ecommerce360 tracking. Go to WooCommerce > Settings > Newsletter to configure the plugin.
 * Version: 2.9.0
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Requires at least: 4.4
 * Tested up to: 5.4
 * WC requires at least: 2.6
 * WC tested up to: 4.3
 * Woo: 18605:9b4ddf6c5bcc84c116ede70d840805fe
 *
 * Text Domain: woocommerce-subscribe-to-newsletter
 * Domain Path: /languages/
 *
 * Copyright: © 2020 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '9b4ddf6c5bcc84c116ede70d840805fe', '18605' );

if ( is_woocommerce_active() ) {

	/**
	 * Localisation
	 **/
	load_plugin_textdomain( 'woocommerce-subscribe-to-newsletter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * woocommerce_subscribe_to_newsletter class
	 **/
	if ( ! class_exists( 'woocommerce-subscribe-to-newsletter' ) ) {

		/**
		 * WC_Subscribe_To_Newsletter class.
		 */
		class WC_Subscribe_To_Newsletter {

			/**
			 * Service provider.
			 *
			 * @var mixed
			 */
			protected $service = null;

			/**
			 * Constructor
			 */
			public function __construct() {
				$this->define_constants();
				$this->includes();

				// Widget
				add_action( 'widgets_init', array( $this, 'init_widget' ) );

				// Dashboard stats
				add_action( 'wp_dashboard_setup', array( $this, 'init_dashboard' ) );

				// Frontend
				add_action( 'woocommerce_loaded', array( $this, 'load_post_wc_class' ) );
			}

			/**
			 * Auto-load in-accessible properties on demand.
			 *
			 * @since 2.8.0
			 *
			 * @param mixed $key Key name.
			 * @return mixed
			 */
			public function __get( $key ) {
				if ( 'service' === $key ) {
					_doing_it_wrong( 'WC_Subscribe_To_Newsletter->service', 'This property is no longer available. Use the method WC_Subscribe_To_Newsletter->provider() instead.', '2.8.0' );

					return $this->provider();
				}
			}

			/**
			 * Define constants.
			 *
			 * @since 2.5.0
			 */
			public function define_constants() {
				$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_VERSION', '2.9.0' );
				$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_PATH', plugin_dir_path( __FILE__ ) );
				$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_URL', plugin_dir_url( __FILE__ ) );
				$this->define( 'WC_NEWSLETTER_SUBSCRIPTION_BASENAME', plugin_basename( __FILE__ ) );
			}

			/**
			 * Define constant if not already set.
			 *
			 * @since 2.5.0
			 *
			 * @param string      $name  The constant name.
			 * @param string|bool $value The constant value.
			 */
			private function define( $name, $value ) {
				if ( ! defined( $name ) ) {
					define( $name, $value );
				}
			}

			/**
			 * Includes the necessary files.
			 *
			 * @since 2.5.0
			 */
			public function includes() {
				include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/wc-newsletter-subscription-functions.php';

				if ( wc_newsletter_subscription_is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {
					include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-points-rewards.php';
				}

				if ( wc_newsletter_subscription_is_request( 'admin' ) ) {
					include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/admin/class-wc-newsletter-subscription-admin.php';
				}

				if ( wc_newsletter_subscription_is_request( 'frontend' ) ) {
					include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-frontend-scripts.php';
					include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-checkout.php';
					include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-register.php';
				}
			}

			/**
			 * Loads any class that needs to check for WC loaded.
			 *
			 * @since 2.3.12
			 */
			public function load_post_wc_class() {
				if ( is_admin() ) {
					require_once( dirname( __FILE__ ) . '/includes/class-wc-subscribe-to-newsletter-privacy.php' );
				}
			}

			/**
			 * Gets service provider class.
			 *
			 * @since 2.8.0
			 *
			 * @return mixed
			 */
			public function provider() {
				if ( is_null( $this->service ) ) {
					$service = get_option( 'woocommerce_newsletter_service' );

					if ( 'mailchimp' === $service ) {
						$api_key = get_option( 'woocommerce_mailchimp_api_key' );

						if ( $api_key ) {
							include_once 'includes/class-wc-mailchimp-newsletter-integration.php';

							$list          = get_option( 'woocommerce_mailchimp_list' );
							$this->service = new WC_Mailchimp_Newsletter_Integration( $api_key, $list );
						}
					} elseif ( 'mailpoet' === $service ) {
						include_once 'includes/class-wc-mailpoet-integration.php';

						$list          = get_option( 'woocommerce_mailpoet_list' );
						$this->service = new WC_Mailpoet_Integration( $list );
					} else {
						$api_key = get_option( 'woocommerce_cmonitor_api_key' );

						if ( $api_key ) {
							include_once 'includes/class-wc-cm-integration.php';

							$list          = get_option( 'woocommerce_cmonitor_list' );
							$this->service = new WC_CM_Integration( $api_key, $list );
						}
					}
				}

				return $this->service;
			}

			/**
			 * init_dashboard function.
			 *
			 * @access public
			 * @return void
			 */
			public function init_dashboard() {
				$provider = $this->provider();

				if ( current_user_can( 'manage_woocommerce' ) && $provider && $provider->has_list() ) {
					wp_add_dashboard_widget( 'woocommmerce_dashboard_subscribers', esc_html__( 'Newsletter subscribers', 'woocommerce-subscribe-to-newsletter' ), array( $provider, 'show_stats' ) );
				}
			}

			/**
			 * Registers custom widgets.
			 */
			public function init_widget() {
				include_once dirname( __FILE__ ) . '/includes/class-wc-widget-subscribe-to-newsletter.php';

				register_widget( 'WC_Widget_Subscribe_To_Newsletter' );
			}

			/**
			 * newsletter_field function.
			 *
			 * @deprecated 2.9.0
			 *
			 * @param mixed $woocommerce_checkout
			 * @return void
			 */
			public function newsletter_field( $woocommerce_checkout ) {
				_deprecated_function( __FUNCTION__, '2.9.0', 'Moved to WC_Newsletter_Subscription_Checkout->checkout_content()' );

				if ( is_user_logged_in() && get_user_meta( get_current_user_id(), '_wc_subscribed_to_newsletter', true ) ) {
					return;
				}

				$provider = $this->provider();

				if ( ! $provider || ! $provider->has_list() ) {
					return;
				}

				$value = ( 'checked' === get_option( 'woocommerce_newsletter_checkbox_status' ) ? 1 : 0 );
				$label = get_option( 'woocommerce_newsletter_label' );

				if ( ! $label ) {
					$label = _x( 'Subscribe to our newsletter', 'subscription checkbox label', 'woocommerce-subscribe-to-newsletter' );
				}

				woocommerce_form_field(
					'subscribe_to_newsletter',
					array(
						'type'  => 'checkbox',
						'class' => array( 'form-row-wide' ),
						'label' => $label,
					),
					$value
				);
			}

			/**
			 * process_newsletter_field function.
			 *
			 * @deprecated 2.9.0
			 *
			 * @param mixed $order_id
			 * @param mixed $posted
			 */
			public function process_newsletter_field( $order_id, $posted ) {
				_deprecated_function( __FUNCTION__, '2.9.0', 'WC_Newsletter_Subscription_Checkout->process_checkout_order()' );

				$provider = $this->provider();

				if ( ! $provider || ! $provider->has_list() ) {
					return;
				}

				if ( ! isset( $_POST['subscribe_to_newsletter'] ) ) {
					return; // They don't want to subscribe
				}

				wc_newsletter_subscription_subscribe(
					$posted['billing_email'],
					array(
						'first_name' => $posted['billing_first_name'],
						'last_name'  => $posted['billing_last_name'],
					)
				);

				if ( is_user_logged_in() ) {
					update_user_meta( get_current_user_id(), '_wc_subscribed_to_newsletter', 1 );
				}
			}

			/**
			 * process_ppe_newsletter_field function.
			 *
			 * @deprecated 2.9.0
			 *
			 * @param WC_Order $order Order object.
			 */
			public function process_ppe_newsletter_field( $order ) {
				_deprecated_function( __FUNCTION__, '2.9.0' );

				$provider = $this->provider();

				if ( ! $provider || ! $provider->has_list() ) {
					return;
				}

				if ( ! isset( $_REQUEST['subscribe_to_newsletter'] ) ) {
					return; // They don't want to subscribe
				}

				$billing_email = version_compare( WC_VERSION, '2.7', '<' ) ? $order->billing_email : $order->get_billing_email();
				wc_newsletter_subscription_subscribe( $billing_email );

				$order->add_order_note( esc_html__( 'User subscribed to newsletter via PayPal Express return page.', 'woocommerce-subscribe-to-newsletter' ) );
			}

			/**
			 * Processes register form.
			 *
			 * @since 2.6
			 * @deprecated 2.9.0
			 *
			 * @param int   $customer_id       Customer ID.
			 * @param array $new_customer_data Customer data.
			 */
			public function process_register( $customer_id, $new_customer_data ) {
				_deprecated_function( __FUNCTION__, '2.9.0', 'WC_Newsletter_Subscription_Register->process_register()' );

				$provider = $this->provider();

				if ( ! $provider || ! $provider->has_list() ) {
					return;
				}

				if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || ! isset( $_REQUEST['subscribe_to_newsletter'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					return;
				}

				$subscriber = array(
					'first_name' => '',
					'last_name'  => '',
					'email'      => $new_customer_data['user_email'],
				);

				if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
					try {
						$customer   = new WC_Customer( $customer_id );
						$subscriber = wp_parse_args(
							array(
								'first_name' => $customer->get_first_name(),
								'last_name'  => $customer->get_last_name(),
							),
							$subscriber
						);
					} catch ( Exception $e ) {
						return;
					}
				}

				wc_newsletter_subscription_subscribe(
					$subscriber['email'],
					array(
						'first_name' => $subscriber['first_name'],
						'last_name'  => $subscriber['last_name'],
					)
				);
			}

			/**
			 * Points and rewards
			 *
			 * @deprecated 2.9.0
			 *
			 * @return array
			 */
			public function pw_action_settings( $settings ) {
				_deprecated_function( __FUNCTION__, '2.9', 'WC_Newsletter_Subscription_Points_Rewards->add_settings()' );

				return $settings;
			}

			/**
			 * Points and rewards description
			 *
			 * @deprecated 2.9.0
			 *
			 * @param  [type] $event_description
			 * @param  [type] $event_type
			 * @param  [type] $event
			 * @return [type]
			 */
			public function pw_action_event_description( $event_description, $event_type, $event ) {
				_deprecated_function( __FUNCTION__, '2.9', 'WC_Newsletter_Subscription_Points_Rewards->event_description()' );

				return $event_description;
			}

			/**
			 * The signup action for points and rewards
			 *
			 * @deprecated 2.9.0
			 *
			 * @param  string $email
			 * @param  int|null $user_id
			 */
			public function pw_action( $email, $user_id = null ) {
				_deprecated_function( __FUNCTION__, '2.9', 'WC_Newsletter_Subscription_Points_Rewards->reward_newsletter_signup()' );

				// can't give points to a user who isn't logged in
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
				}

				if ( is_null( $user_id ) ) {
					return;
				}

				// get the points configured for this custom action
				$points = get_option( 'wc_points_rewards_wc_newsletter_signup' );

				if ( ! empty( $points ) && class_exists( 'WC_Points_Rewards_Manager' ) ) {
					$entries = WC_Points_Rewards_Points_Log::get_points_log_entries( array(
						'user'       => $user_id,
						'event_type' => 'wc-newsletter-signup',
					) );

					// Check if user was already awarded points for signup.
					if ( 0 < count( $entries ) ) {
						return;
					}

					// arbitrary data can be passed in with the points change, this will be persisted to the points event log
					$data = array( 'email' => $email );

					WC_Points_Rewards_Manager::increase_points( $user_id, $points, 'wc-newsletter-signup', $data );
				}
			}

			/**
			 * Add points to customer for creating an account
			 *
			 * @since 2.3.8
			 * @deprecated 2.9.0
			 *
			 * @param  int|null $user_id
			 */
			public function create_account_action( $user_id ) {
				_deprecated_function( __FUNCTION__, '2.9' );

				if ( ! empty( $_POST['subscribe_to_newsletter'] ) ) {
					$this->pw_action( null, $user_id );
				}
			}

			/**
			 * process_register_form function.
			 *
			 * @deprecated 2.6
			 *
			 * @param mixed $sanitized_user_login
			 * @param mixed $user_email
			 * @param mixed $reg_errors
			 */
			public function process_register_form( $sanitized_user_login, $user_email, $reg_errors ) {
				_deprecated_function( __FUNCTION__, '2.6', 'WC_Subscribe_To_Newsletter->process_register()' );

				$provider = $this->provider();

				if ( ! $provider || ! $provider->has_list() ) {
					return;
				}

				if ( defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
					return; // Ship checkout
				}

				if ( ! isset( $_REQUEST['subscribe_to_newsletter'] ) ) {
					return; // They don't want to subscribe
				}

				wc_newsletter_subscription_subscribe( $user_email );
			}

			/**
			 * Adds plugin action links.
			 *
			 * @since 2.3.5
			 * @deprecated 2.6.0
			 *
			 * @param array $links Plugin action links.
			 * @return array
			 */
			public function plugin_action_links( $links ) {
				_deprecated_function( __METHOD__, '2.6.0', 'Moved to WC_Newsletter_Subscription_Admin->action_links()' );

				return $links;
			}

			/**
			 * Add_tab function.
			 *
			 * @deprecated 2.8.0
			 *
			 * @param array $settings_tabs Current setting tabs.
			 * @return array
			 */
			public function add_tab( $settings_tabs ) {
				_deprecated_function( __FUNCTION__, '2.8' );
				return $settings_tabs;
			}

			/**
			 * Settings_tab_action function.
			 *
			 * @deprecated 2.8.0
			 */
			public function settings_tab_action() {
				_deprecated_function( __FUNCTION__, '2.8' );
			}

			/**
			 * Add settings fields for each tab.
			 *
			 * @deprecated 2.8.0
			 */
			public function add_settings_fields() {
				_deprecated_function( __FUNCTION__, '2.8' );
			}

			/**
			 * Get the tab current in view/processing.
			 *
			 * @deprecated 2.8.0
			 */
			public function get_tab_in_view( $current_filter, $filter_base ) {
				_deprecated_function( __FUNCTION__, '2.8' );
				return str_replace( $filter_base, '', $current_filter );
			}

			/**
			 * Prepare form fields to be used in the various tabs.
			 *
			 * @deprecated 2.8.0
			 */
			public function init_form_fields() {
				_deprecated_function( __FUNCTION__, '2.8' );
			}

			/**
			 * Save settings in a single field in the database for each tab's fields (one field per tab).
			 *
			 * @deprecated 2.8.0
			 */
			public function save_settings() {
				_deprecated_function( __FUNCTION__, '2.8' );
			}
		}

		$GLOBALS['WC_Subscribe_To_Newsletter'] = new WC_Subscribe_To_Newsletter();
	} // End if().
} // End if().
