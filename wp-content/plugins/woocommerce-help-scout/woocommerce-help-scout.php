<?php
/**
 * Plugin Name: WooCommerce Help Scout
 * Plugin URI: https://woocommerce.com/products/woocommerce-help-scout/
 * Description: A Help Scout integration plugin for WooCommerce.
 * Version: 3.4
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-help-scout
 * Domain Path: /languages
 * Woo: 395318:1f5df97b2bc60cdb3951b72387ec2e28
 * WC tested up to: 6.3
 * WC requires at least: 2.6
 *
 * Copyright (c) 2018 WooCommerce.
 *
 * @package  WC_Help_Scout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '1f5df97b2bc60cdb3951b72387ec2e28', '395318' );

if ( ! class_exists( 'WC_Help_Scout' ) ) :

	define( 'WC_HELP_SCOUT_VERSION', '2.5' );
	define( 'WC_HELP_SCOUT_PLUGINURL', plugin_dir_url( __FILE__ ) );
	/**
	 * WooCommerce Help Scout main class.
	 */
	class WC_Help_Scout {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected $app_key;
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected $app_secret;

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Component instances.
		 *
		 * @var array
		 */
		protected $_components = array();

		/**
		 * Initialize the plugin.
		 */
		private function __construct() {
			$nonce = wp_create_nonce( 'woocommerce_help_scout_nonce' );

			// Define user set variables.
			$woocommerce_help_scout_settings = get_option( 'woocommerce_help-scout_settings' );
			$this->app_key = isset( $woocommerce_help_scout_settings['app_key'] ) ? $woocommerce_help_scout_settings['app_key'] : '';
			$this->app_secret = isset( $woocommerce_help_scout_settings['app_secret'] ) ? $woocommerce_help_scout_settings['app_secret'] : '';
			$this->mailbox_id = isset( $woocommerce_help_scout_settings['mailbox_id'] ) ? $woocommerce_help_scout_settings['mailbox_id'] : '';

			// Load plugin text domain.
			add_action( 'rest_api_init', array( $this, 'get_woo_data_endpoint' ) );

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {
				$this->includes();

				if ( is_admin() ) {
					require_once( dirname( __FILE__ ) . '/includes/class-wc-help-scout-privacy.php' );
				}

				// Register the integration.
				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

				// Instantiate components if API creds are defined.
				if ( $this->are_credentials_defined() ) {
					// Register API for Help Scout APP.
					add_action( 'woocommerce_api_loaded', array( $this, 'load_api' ) );
					add_filter( 'woocommerce_api_classes', array( $this, 'add_api' ) );
					add_action( 'wp_ajax_helpscot_test_Cron', array( $this, 'helpscot_test_Cron' ) );

					$this->_components['ajax']       = new WC_Help_Scout_Ajax();
					$this->_components['my_account'] = new WC_Help_Scout_My_Account();
					$this->_components['shortcodes'] = new WC_Help_Scout_Shortcodes();
				}

				if ( is_admin() ) {
					add_action( 'admin_notices', array( $this, 'admin_notices_helpscout' ) );
				}
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}



		/**
		 * Function get_woo_data_endpoint.
		 */
		public function get_woo_data_endpoint() {
			register_rest_route(
				'helpscout/v1',
				'/get-order-and-customer-data',
				array(
					'methods' => 'POST',
					'callback' => array( $this, 'get_woo_data_function' ),
					'permission_callback' => '__return_true',
				)
			);
		}

		/**
		 * Displays notices in admin.
		 *
		 * Error notices.
		 */
		public function admin_notices_helpscout() {
			if ( ! empty( $_POST ) ) {
				wp_verify_nonce( 'woocommerce_help-scout_nonce', 'woocommerce_help_scout_nonce' );
			}
			// Define user set variables.
			$woocommerce_help_scout_settings = get_option( 'woocommerce_help-scout_settings' );
			$app_key          = isset( $woocommerce_help_scout_settings['app_key'] ) ? $woocommerce_help_scout_settings['app_key'] : '';
			$app_secret       = isset( $woocommerce_help_scout_settings['app_secret'] ) ? $woocommerce_help_scout_settings['app_secret'] : '';
			$mailbox_id       = isset( $woocommerce_help_scout_settings['mailbox_id'] ) ? $woocommerce_help_scout_settings['mailbox_id'] : '';
			$settings_id = 'woocommerce_help-scout_';

			$post_api_key = sanitize_text_field( isset( $_POST[ $settings_id . 'api_key' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ $settings_id . 'api_key' ] ) ) : '';
			$post_mailbox_id = sanitize_text_field( isset( $_POST[ $settings_id . 'mailbox_id' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ $settings_id . 'mailbox_id' ] ) ) : '';

			if ( ( ( empty( $app_key ) || ( empty( $app_secret ) ) || empty( $mailbox_id ) ) && ! $_POST ) || ( isset( $_POST[ $settings_id . 'api_key' ] ) || isset( $_POST[ $settings_id . 'mailbox_id' ] ) && empty( $_POST[ $settings_id . 'mailbox_id' ] ) ) ) {
				$url = $this->get_settings_url_helpscout();
				/* translators: %2$s: search term */
				$spint_r = sprintf( __( '%1$sWooCommerce Help Scout is almost ready.%2$s To get started, %3$sconnect your Help Scout account%4$s and specify a Mailbox ID.', 'woocommerce-help-scout' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' );
				echo wp_kses_post(
					'<div class="updated fade"><p>' . $spint_r . '</p></div>' . "\n",
					array(
						'div' => array( 'class' => array() ),
						'a' => array( 'href' => array() ),
						'p' => array(),
						'strong' => array(),
					)
				);
			}
		}

		/**
		 * Check if client has defined Scout API Credentials.
		 *
		 * @return boolean
		 */
		public function are_credentials_defined() {
			if ( ! empty( $this->app_key ) && ! empty( $this->app_secret ) && ! empty( $this->mailbox_id ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Generate a URL to our specific settings screen.
		 *
		 * @since  1.3.4
		 * @return string Generated URL.
		 */
		public function get_settings_url_helpscout() {
			return add_query_arg(
				array(
					'page'    => 'wc-settings',
					'tab'     => 'integration',
					'section' => 'help-scout',
				),
				admin_url( 'admin.php' )
			);
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}
			load_plugin_textdomain( 'woocommerce-help-scout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			return self::$instance;
		}

		/**
		 * Get the plugin path.
		 *
		 * @since 1.3.0
		 *
		 * @return string Plugin path
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Includes.
		 */
		private function includes() {
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-integration.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-ajax.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-my-account.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-shortcodes.php';
			// include_once 'includes/deprecated.php';.
		}

		/**
		 * Return the WooCommerce logger API.
		 *
		 * @return WC_Logger
		 */
		public static function get_logger() {
			global $woocommerce;

			if ( class_exists( 'WC_Logger' ) ) {
				return new WC_Logger();
			} else {
				return $woocommerce->logger();
			}
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-help-scout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			/* translators: %s: search term */
			echo '<div class="error"><p>' . sprintf( esc_html_e( 'WooCommerce Help Scout depends on the last version of %s to work!', 'woocommerce-help-scout' ), '<a href="https://woocommerce.com/" target="_blank">' . esc_html_e( 'WooCommerce', 'woocommerce-help-scout' ) . '</a>' ) . '</p></div>';
		}

		/**
		 * Add a new integration to WooCommerce.
		 *
		 * @param  array $integrations WooCommerce integrations.
		 *
		 * @return array               Help Scout integration.
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_Help_Scout_Integration';

			return $integrations;
		}

		/**
		 * Get integration instance.
		 *
		 * @since 1.3.0
		 *
		 * @return null|WC_Help_Scout_Integration Help Scout integration instance
		 */
		public static function get_integration_instance() {
			$integrations = WC()->integrations;

			if ( is_a( $integrations, 'WC_Integrations' ) && ! empty( $integrations->integrations['help-scout'] ) ) {
				return $integrations->integrations['help-scout'];
			}

			return null;
		}

		/**
		 * Load API class.
		 *
		 * @return void
		 */
		public function load_api() {
			include_once 'includes/class-wc-help-scout-api.php';
		}

		/**
		 * Add a new API to WooCommerce.
		 *
		 * @param  array $apis WooCommerce APIs.
		 *
		 * @return array       Help Scout API.
		 */
		public function add_api( $apis ) {
			$apis[] = 'WC_Help_Scout_API';

			return $apis;
		}
		/**
		 * Uninstall plugin and delete settings.
		 */
		public function plugin_uninstall() {
			delete_option( 'woocommerce_help-scout_settings' );
			delete_option( 'helpscout_access_refresh_token' );
			delete_option( 'helpscout_expires_in' );
			wp_clear_scheduled_hook( 'my_task_hook' );
		}

		/**
		 * Function get_woo_data_function.
		 */
		public function get_woo_data_function() {
			global $wpdb;
			$data = file_get_contents( 'php://input' );
			$signature = ( ! empty( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) : '' );
			if ( $this->is_from_help_scout( $data, $signature ) ) {
				$helpscout_data = json_decode( $data );
				$customer_id = $helpscout_data->customer->id;
				$customer_email = $helpscout_data->customer->email;
				$customer_fname = $helpscout_data->customer->fname;
				$customer_lname = $helpscout_data->customer->lname;

				$data = $this->get_app_data( $customer_id, $customer_email, $orders = 10, $products = 0 );
				if ( is_wp_error( $data ) ) {
					$html = '<h4 class="sb-toggle__title">' . __( 'Sorry no data found', 'woocommerce-help-scout' ) . '</h4>';
				} else if ( isset( $data['customer'] ) && ! empty( $data['customer'] ) ) {
					$customer_since = gmdate( 'd M Y', strtotime( $data['customer']['sign_up']['date'] ) );
					$last_orders = $data['customer']['last_orders'];
					$profile_url = $data['customer']['profile_url'];
					$currency_symbol = $data['customer']['currency']['symbol'];
					$user_id = $data['customer']['id'];

					$last_year_date = gmdate( 'Y-m-d', strtotime( '-1 year' ) );

					$from_last_year = number_format( $this->get_order_sum( $user_id, $last_year_date, false ), 2 );
					$lifetime_value = number_format( $this->get_order_sum( $user_id, '', false ), 2 );
					$avg_value = number_format( $this->get_order_sum( $user_id, '', true ), 2 );

					$order_id = end( explode( ' ', trim( end( explode( '-', $helpscout_data->ticket->subject ) ) ) ) );
					$order_refund = wc_get_order( $order_id );

					$html = '<div id="sidebar_v2" class="" aria-hidden="false" bis_skin_checked="1">  
					<ul class="c-sb-list c-sb-list--two-line u-pad-b-2" style="border-bottom: 1px solid rgba(193,203,212,.2);">
					<li class="c-sb-list-item">
						<span class="c-sb-list-item__label t-tx-charcoal-300">
							' . __( 'Customer Since', 'woocommerce-help-scout' ) . '
							<span class="c-sb-list-item__text t-tx-charcoal-500">
								' . $customer_since . '
							</span>
						</span>
					</li>
					<li class="c-sb-list-item">
						<span title="418 Orders" class="c-sb-list-item__label t-tx-charcoal-300">
							' . __( 'Lifetime Value', 'woocommerce-help-scout' ) . '
							<span class="c-sb-list-item__text t-tx-charcoal-500">
								' . $currency_symbol . ' ' . $lifetime_value . '
							</span>
						</span>
					</li>
					<li class="c-sb-list-item">
							<span title="406 Orders" class="c-sb-list-item__label t-tx-charcoal-300">
								' . __( 'Past 12 Months', 'woocommerce-help-scout' ) . '
								<span class="c-sb-list-item__text t-tx-charcoal-500">
									' . $currency_symbol . ' ' . $from_last_year . '
								</span>
							</span>
						</li>
						<li class="c-sb-list-item">
							<span title="406 Orders" class="c-sb-list-item__label t-tx-charcoal-300">
								' . __( 'Average Order', 'woocommerce-help-scout' ) . '
								<span class="c-sb-list-item__text t-tx-charcoal-500">
									' . $currency_symbol . ' ' . $avg_value . '
								</span>
							</span>
						</li>
					</ul>';

					$html .= '<br/><h4 style="font-size: 18px;font-weight: bold;text-align: center;">This Order #' . $order_id . '</h4>';
					$html .= '<div style="text-align:center ">';
					if ( 0 < $order_refund->shipping_total ) {
						$html .= '<div><h4>&nbsp;&nbsp;Shipping:&nbsp;&nbsp;<b>' . wc_price( $order_refund->shipping_total ) . '</b></h4></div>';
					}
					if ( 0 < $order_refund->total_tax ) {
						$html .= '<div><h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GST:&nbsp;&nbsp;<b>' . wc_price( $order_refund->total_tax ) . '</b></h4></div>';
					}
					if ( 0 < $order_refund->total ) {
						$html .= '<div><h4>Order Total:&nbsp;&nbsp;<b>' . wc_price( $order_refund->total ) . '</b></h4></div>';
					}
					$html .= '<div><a href="' . admin_url() . 'post.php?post=' . $order_refund->id . '&action=edit" class="btn btn-primary" style="background:#005ca4;color:#fff;margin-bottom:10px;text-decoration:none !important">Refund Now</a></div>';
					$html .= '</div><br />';

					$order_count = 0;
					if ( ! empty( $last_orders ) ) {
						foreach ( $last_orders as $order ) {
							$order_count++;
						}
					}
					$order_count = ( $order_count > 10 ) ? 10 : $order_count;
					$html .= '<div class="toggleGroup sb-group open" bis_skin_checked="1">
						<a class="toggleBtn sb-toggle" href="#">
							<h4 class="sb-toggle__title"><i class="caret sb-caret"></i>' . __( 'Recent Orders', 'woocommerce-help-scout' ) . ' (' . $order_count . ')</h4>
						</a>
						<div class="toggle" bis_skin_checked="1">';
					if ( ! empty( $last_orders ) ) {
						foreach ( $last_orders as $order ) {

							$ordermeta = wc_get_order( $order['id'] );

							$project_id = '';
							$itemscount = 1;
							foreach ( $ordermeta->get_items() as $item_id => $item ) {
								if ( 1 == $itemscount ) {
									$project_id = wc_get_order_item_meta( $item_id, '_ProjectId', true );
								}
								$itemscount++;
							}

							$project_id = ! empty( $project_id ) ? $project_id : '#';

							$html .= '<table class="table table-condensed ecomm-app">
									<tbody><tr>
										<td class="num"><a href="' . $order['url'] . '" target="_blank" alt="Processing" title="Processing">' . $order['id'] . '</a></td>
										<td style="text-align:right;">' . $currency_symbol . ' ' . $order['total'] . '</td>
									</tr>
									<tr class="order-info">
										<td class="muted">' . gmdate( 'd M Y', strtotime( $order['date'] ) ) . '</td>
										<td class="open" style="text-align:right;">' . $order['status'] . '</td>
									</tr>
									</tbody>
								</table>';
						}
					}

						$html .= '</div>
					</div>
					<p class="cust-links u-mrg-b-2 u-pad-t-1"><a href="' . $profile_url . '" class="t-tx-blue-600" target="_blank">' . __( 'WooCommerce Profile', 'woocommerce-help-scout' ) . '</a></p>
			
					</div><br /><br /><br />';

					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_help_scout_customer_id' AND meta_value = %d", $customer_id ) );

					$subscriptions = '';
					$subscriptions = $this->has_active_subscription( $customer_id );
					$customer_data = '';
					if ( ! empty( $subscriptions ) ) {
						$customer_data = '<h4 style="font-size:18px;font-weight:bold;">Active Subscriptions</h4><br/>';
						$customer_data .= '<h4>' . $customer_fname . ' ' . $customer_lname . ' has following active subscriptions</h4>';
						$customer_data .= $subscriptions;
					}
					$html .= $customer_data;
				} else {
					$html = '<h4 class="sb-toggle__title">' . __( 'Sorry no data found', 'woocommerce-help-scout' ) . '</h4>';
				}
			} else {
				$html = '<h4 class="sb-toggle__title" style="color:#f00;">' . __( 'Authentication failed! Please check your Helpscout secret key', 'woocommerce-help-scout' ) . '</h4>';
			}

			echo json_encode( array( 'html' => $html ) );
			die();
		}

		/**
		 * Get Customer data for Help Scout APP.
		 *
		 * @param  int    $customer_id    Help Scout customer ID.
		 * @param  string $customer_email Customer email.
		 * @param  int    $orders         Total of last orders.
		 * @param  int    $products       Total of purchased products.
		 *
		 * @return array                  Customer data for the APP.
		 */
		public function get_app_data( $customer_id, $customer_email = '', $orders = 5, $products = 0 ) {
			$customer_id = $this->validate_request( $customer_id, 'customer', 'read' );

			if ( is_wp_error( $customer_id ) ) {
				return $customer_id;
			}

			// Get customer data.
			$customer_data = $this->get_customer_data( $customer_id, $customer_email, $orders, $products );

			if ( is_wp_error( $customer_data ) ) {
				return $customer_data;
			}

			return array( 'customer' => apply_filters( 'woocommerce_help_scout_api_response', $customer_data, $customer_id, $customer_email, $orders, '' ) );
		}

		/**
		 * Get customer data by Help Scout ID or email.
		 *
		 * @param  int    $id       Help Scout customer ID.
		 * @param  string $email    Customer email.
		 * @param  int    $orders   Total of last orders.
		 * @param  int    $products Total of purchased products.
		 *
		 * @return array            Customer data.
		 */
		public function get_customer_data( $id, $email, $orders, $products ) {
			global $wpdb;

			$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_help_scout_customer_id' AND meta_value = %d", $id ) );

			if ( $customer_id ) {
				$customer = new WP_User( $customer_id );

				return $this->get_registered_user_data( $customer, $orders, $products );
			} elseif ( ! empty( $email ) && is_email( $email ) ) {
				$customer = get_user_by( 'email', $email );

				if ( $customer ) {
					// Add Help Scout customer id.
					update_user_meta( $customer->ID, '_help_scout_customer_id', absint( $id ) );

					return $this->get_registered_user_data( $customer, $orders, $products );
				} else {
					// Try to get data from a non-registered user.
					$customer = $this->get_non_registered_user_data( $email, $orders, $products );

					if ( $customer ) {
						return $customer;
					}
				}
			}

			return new WP_Error( 'wc_help_scout_api_invalid_customer', __( 'Invalid customer', 'woocommerce-help-scout' ), array( 'status' => 404 ) );
		}

		/**
		 * Get data from a non-registered user.
		 *
		 * @param  string $email    Customer.
		 * @param  int    $orders   Total of last orders.
		 * @param  int    $products Total of purchased products.
		 *
		 * @return array            Customer data.
		 */
		public function get_non_registered_user_data( $email, $orders, $products ) {
			global $wpdb;

			$orders_limit  = ( 0 < $orders ) ? ' LIMIT ' . absint( $orders ) : '';
			$customer_data = array();
			$last_order    = null;

			// Get the customer orders.
			$order_ids = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_billing_email' AND meta_value = %s %s", $email, $orders_limit ) );

			if ( ! $order_ids ) {
				return array();
			}

			$orders_count = 0;
			$last_orders  = array();
			foreach ( $order_ids as $item ) {
				$order = wc_get_order( $item->post_id );

				if ( empty( $order ) ) {
					continue;
				}

				if ( 0 === $orders_count ) {
					$last_order = $order;
				}

				$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );

				$last_orders[] = array(
					'id'     => $order->get_order_number(),
					'url'    => add_query_arg(
						array(
							'post' => $item->post_id,
							'action' => 'edit',
						),
						admin_url( 'post.php' )
					),
					'date'   => $order_date,
					'total'  => $order->get_total(),
					'status' => $order->get_status(),
				);

				$orders_count++;
			}

			if ( ! $last_order ) {
				return array();
			}

			// Custom general data.
			$customer_data['id'] = 0;
			$customer_data['total_spent'] = '';
			$customer_data['sign_up'] = array(
				'date' => '',
				'diff' => '',
			);
			$customer_data['currency'] = array(
				'code'   => get_woocommerce_currency(),
				'symbol' => get_woocommerce_currency_symbol( get_woocommerce_currency() ),
			);
			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				$customer_data['billing_address'] = array(
					'first_name' => $last_order->billing_first_name,
					'last_name'  => $last_order->billing_last_name,
					'company'    => $last_order->billing_company,
					'address_1'  => $last_order->billing_address_1,
					'address_2'  => $last_order->billing_address_2,
					'city'       => $last_order->billing_city,
					'state'      => $last_order->billing_state,
					'postcode'   => $last_order->billing_postcode,
					'country'    => $last_order->billing_country,
					'email'      => $last_order->billing_email,
					'phone'      => $last_order->billing_phone,
				);
				$customer_data['shipping_address'] = array(
					'first_name' => $last_order->shipping_first_name,
					'last_name'  => $last_order->shipping_last_name,
					'company'    => $last_order->shipping_company,
					'address_1'  => $last_order->shipping_address_1,
					'address_2'  => $last_order->shipping_address_2,
					'city'       => $last_order->shipping_city,
					'state'      => $last_order->shipping_state,
					'postcode'   => $last_order->shipping_postcode,
					'country'    => $last_order->shipping_country,
					'phone'      => $last_order->billing_phone,
				);
			} else {
				$customer_data['billing_address'] = array(
					'first_name' => $last_order->get_billing_first_name(),
					'last_name'  => $last_order->get_billing_last_name(),
					'company'    => $last_order->get_billing_company(),
					'address_1'  => $last_order->get_billing_address_1(),
					'address_2'  => $last_order->get_billing_address_2(),
					'city'       => $last_order->get_billing_city(),
					'state'      => $last_order->get_billing_state(),
					'postcode'   => $last_order->get_billing_postcode(),
					'country'    => $last_order->get_billing_country(),
					'email'      => $last_order->get_billing_email(),
					'phone'      => $last_order->get_billing_phone(),
				);
				$customer_data['shipping_address'] = array(
					'first_name' => $last_order->get_shipping_first_name(),
					'last_name'  => $last_order->get_shipping_last_name(),
					'company'    => $last_order->get_shipping_company(),
					'address_1'  => $last_order->get_shipping_address_1(),
					'address_2'  => $last_order->get_shipping_address_2(),
					'city'       => $last_order->get_shipping_city(),
					'state'      => $last_order->get_shipping_state(),
					'postcode'   => $last_order->get_shipping_postcode(),
					'country'    => $last_order->get_shipping_country(),
					'phone'      => $last_order->get_billing_phone(),
				);
			}

			$customer_data['name'] = $customer_data['billing_address']['first_name'] . ' ' . $customer_data['billing_address']['last_name'];
			$customer_data['email'] = $customer_data['billing_address']['email'];
			$customer_data['avatar_url'] = $this->get_avatar_url_custom( $customer_data['billing_address']['email'] );

			$customer_data['profile_url'] = '';

			// Set the last orders.
			$customer_data['last_orders'] = $last_orders;

			// Get the purchased products.
			$purchased_products = array();
			$products_limit     = ( 0 < $products ) ? 'LIMIT ' . absint( $products ) : '';
			$products_query     = $wpdb->get_results(
				$wpdb->prepare(
					"
				SELECT DISTINCT order_items.order_item_name
				FROM   $wpdb->postmeta AS postmeta
					LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items
					ON order_items.order_id = postmeta.post_id
					AND order_items.order_item_type = 'line_item'
				WHERE  postmeta.meta_key = '_billing_email'
				AND    postmeta.meta_value = %s %s				
			 ",
					$email,
					$products_limit
				)
			);

			foreach ( $products_query as $item ) {
				$purchased_products[] = $item->order_item_name;
			}

			$customer_data['purchased_products'] = $purchased_products;

			return apply_filters( 'woocommerce_help_scout_customer_data', $customer_data );
		}


		/**
		 * Get data from a registered user.
		 *
		 * @param  WC_Order $customer Customer.
		 * @param  int      $orders   Total of last orders.
		 * @param  int      $products Total of purchased products.
		 *
		 * @return array              Customer data.
		 */
		public function get_registered_user_data( $customer, $orders, $products ) {
			$customer_data = $this->get_customer_details( $customer );
			$customer_data['last_orders'] = $this->get_last_orders( $customer, $orders );
			$customer_data['purchased_products'] = $this->get_purchased_products( $customer, $products );

			return $customer_data;
		}

		/**
		 * Function get_order_sum.
		 *
		 * @param string|int $customer_id Customer id.
		 * @param string     $from_date from date.
		 * @param bool       $avg set average.
		 */
		public function get_order_sum( $customer_id, $from_date = '', $avg = false ) {

			$query_string = array(
				'post_type' => 'shop_order',
				'meta_key'    => '_customer_user',
				'meta_value'  => $customer_id,
				'post_status' => array( 'wc-completed', 'wc-processing', 'wc-on-hold' ),
				'posts_per_page' => -1,
			);

			if ( ! empty( $from_date ) ) {
				$query_string['date_query'] = array( 'after' => $from_date );
			}

			$the_query = new WP_Query( $query_string );

			$total = 0;
			$order_count = 0;
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					$order = wc_get_order( get_the_ID() );
					$total += $order->get_total();
					$order_count++;
				}
			}

			if ( true === $avg ) {
				return round( ( $total / $order_count ), 2 );
			}

			return $total;
		}

		/**
		 * Get the customer purchased products.
		 *
		 * @param  WC_User $customer Customer data.
		 * @param  int     $products Total of products to list.
		 *
		 * @return array             Purchased products list.
		 */
		public function get_purchased_products( $customer, $products ) {
			global $wpdb;

			$purchased_products = array();
			$limit              = ( 0 < $products ) ? 'LIMIT ' . absint( $products ) : '';
			$query              = $wpdb->get_results(
				$wpdb->prepare(
					"
				SELECT DISTINCT order_items.order_item_name
				FROM   $wpdb->postmeta AS postmeta
					LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items
					ON order_items.order_id = postmeta.post_id
					AND order_items.order_item_type = 'line_item'
				WHERE  postmeta.meta_key = '_customer_user'
				AND    postmeta.meta_value = %s %s				
			 ",
					$customer->ID,
					$limit
				)
			);

			foreach ( $query as $item ) {
				$purchased_products[] = $item->order_item_name;
			}

			return $purchased_products;
		}

		/**
		 * Get customer last orders.
		 *
		 * @param  WC_User $customer Customer data.
		 * @param  int     $total    Total of orders to list.
		 *
		 * @return array             Last orders list.
		 */
		public function get_last_orders( $customer, $total ) {
			$orders = array();

			$args = array(
				'posts_per_page'      => intval( $total ),
				'post_type'           => 'shop_order',
				'meta_key'            => '_customer_user',
				'meta_value'          => $customer->ID,
				'ignore_sticky_posts' => 1,
			);

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
				$args['post_status'] = array_keys( wc_get_order_statuses() );
			}

			$query = get_posts( $args );

			$all_status = wc_get_order_statuses();

			$all_status_translate = array();
			foreach ( $all_status as $key => $slug ) {
				$all_status_translate[ $key ] = $slug;
			}

			foreach ( $query as $item ) {
				$order = new WC_Order( $item->ID );
				$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );
				$orders[] = array(
					'id'     => $order->get_order_number(),
					'url'    => add_query_arg(
						array(
							'post' => $item->ID,
							'action' => 'edit',
						),
						admin_url( 'post.php' )
					),
					'date'   => $order_date,
					'total'  => $order->get_total(),
					'status' => $all_status_translate[ 'wc-' . $order->get_status() ],
				);
			}

			return $orders;
		}

		/**
		 * Get customer details.
		 *
		 * @param  WC_User $customer Customer data.
		 *
		 * @return array             Customer details.
		 */
		public function get_customer_details( $customer ) {
			$sign_up_date   = $customer->data->user_registered;
			$lifetime_value = get_user_meta( $customer->ID, '_money_spent', true );
			$currency_code  = get_woocommerce_currency();

			$data = array(
				'id'              => $customer->ID,
				'name'            => $customer->first_name . ' ' . $customer->last_name,
				'email'           => $customer->user_email,
				'total_spent'     => $lifetime_value,
				'past_12_months'     => $lifetime_value,
				'sign_up'         => array(
					'date' => $sign_up_date,
					'diff' => human_time_diff( gmdate( 'U', strtotime( $sign_up_date ) ), current_time( 'timestamp' ) ),
				),
				'currency'        => array(
					'code'   => $currency_code,
					'symbol' => get_woocommerce_currency_symbol( $currency_code ),
				),
				'avatar_url'      => $this->get_avatar_url_custom( $customer->user_email ),
				'billing_address' => array(
					'first_name' => $customer->billing_first_name,
					'last_name'  => $customer->billing_last_name,
					'company'    => $customer->billing_company,
					'address_1'  => $customer->billing_address_1,
					'address_2'  => $customer->billing_address_2,
					'city'       => $customer->billing_city,
					'state'      => $customer->billing_state,
					'postcode'   => $customer->billing_postcode,
					'country'    => $customer->billing_country,
					'email'      => $customer->billing_email,
					'phone'      => $customer->billing_phone,
				),
				'shipping_address' => array(
					'first_name' => $customer->shipping_first_name,
					'last_name'  => $customer->shipping_last_name,
					'company'    => $customer->shipping_company,
					'address_1'  => $customer->shipping_address_1,
					'address_2'  => $customer->shipping_address_2,
					'city'       => $customer->shipping_city,
					'state'      => $customer->shipping_state,
					'postcode'   => $customer->shipping_postcode,
					'country'    => $customer->shipping_country,
				),
				'profile_url'     => add_query_arg( array( 'user_id' => $customer->ID ), admin_url( 'user-edit.php' ) ),
			);

			return $data;
		}

		/**
		 * Wrapper for @see get_avatar() which doesn't simply return the URL so we need to pluck it from the HTML img tag.
		 *
		 * @param  string $email The customer's email.
		 * @return string        The URL to the customer's avatar.
		 */
		public function get_avatar_url_custom( $email ) {
			$avatar_html = get_avatar( $email );

			// Get the URL of the avatar from the provided HTML.
			preg_match( '/src=["|\'](.+)[\&|"|\']/U', $avatar_html, $matches );

			if ( isset( $matches[1] ) && ! empty( $matches[1] ) ) {
				return esc_url_raw( $matches[1] );
			}

			return null;
		}

		/**
		 * Validate the request by checking:
		 *
		 * 1) the ID is a valid integer
		 * 2) the current user has the proper permissions
		 *
		 * @see    WC_API_Resource::validate_request().
		 * @param  string|int $id      The customer ID.
		 * @param  string     $type    The request type, unused because this method overrides the parent class.
		 * @param  string     $context The context of the request, either `read`, `edit` or `delete`.
		 *
		 * @return int|WP_Error          Valid user ID or WP_Error if any of the checks fails.
		 */
		public function validate_request( $id, $type, $context ) {

			$id = absint( $id );

			// Validate ID.
			if ( empty( $id ) ) {
				return new WP_Error( 'wc_help_scout_api_invalid_customer_id', __( 'Invalid customer ID', 'woocommerce-help-scout' ), array( 'status' => 404 ) );
			}

			if ( 'read' != $context ) {
				return new WP_Error( 'wc_help_scout_api_invalid_context', __( 'You have only read permission', 'woocommerce-help-scout' ), array( 'status' => 401 ) );
			}

			return $id;
		}


		/**
		 * Function is_from_help_scout.
		 *
		 * @param array  $data The Api request data.
		 * @param string $signature HTTP_X_HELPSCOUT_SIGNATURE key sent in api header.
		 */
		public function is_from_help_scout( $data, $signature ) {
			$helpscout_settings = get_option( 'woocommerce_help-scout_settings' );
			$helpscout_secret_key = $helpscout_settings['app_secret'];
			$calculated = base64_encode( hash_hmac( 'sha1', $data, $helpscout_secret_key, true ) );
			return $signature == $calculated;
		}

		/**
		 * Get customer subscription info
		 *
		 * @param int $user_id user_id.
		 */
		public function has_active_subscription( $user_id ) {
			$html = '';
			if ( class_exists( 'WC_Subscriptions' ) ) {
				$subscriptions = wcs_get_users_subscriptions( $user_id );
				// comparing every subscription.
				foreach ( $subscriptions as $key => $subscription ) {
					// for the following statuses we know the user was not added.
					// manually.
					$sub_order_id = $key;

					$status = $subscription->get_status();
					if ( in_array( $status, array( 'pending-canceled', 'active', 'on-hold', 'pending' ) ) ) {
						$current_subscription_start_date = $subscription->modified_date;
						$title = 'Order – ' . gmdate( 'M d, Y @ g:i A ', strtotime( $current_subscription_start_date ) );
						$html .= '<h4 class="c-sb-list-item__text t-tx-charcoal-500">' . $title . '</h4>';
						$html .= '<a href="' . admin_url() . 'post.php?post=' . $sub_order_id . '&action=edit" class="btn btn-primary" style="background:#005ca4;color:#fff;margin-bottom:10px;text-decoration:none !important;font-size: 12px;">Cancel Subscription</a>';
					}
				}
			}
			wp_reset_postdata();
			return $html;
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Help_Scout', 'get_instance' ) );
	register_uninstall_hook( __FILE__, array( 'WC_Help_Scout', 'plugin_uninstall' ) );
endif;
