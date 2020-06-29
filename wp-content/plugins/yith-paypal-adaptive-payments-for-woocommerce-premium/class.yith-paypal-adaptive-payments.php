<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Paypal_Adaptive_Payments' ) ) {

	class YITH_Paypal_Adaptive_Payments {

		protected static $instance;
		/**
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $_panel;
		/**
		 * @var string official documentation
		 */
		protected $_official_documentation = '//yithemes.com/docs-plugins/yith-paypal-adaptive-payments-for-woocommerce/';
		/**
		 * @var string landing page
		 */
		protected $_plugin_landing_url = '//yithemes.com/themes/plugins/yith-woocommerce-paypal-adaptive-payments/';

		/**
		 * @var string plugin official live demo
		 */
		protected $_premium_live_demo = '//plugins.yithemes.com/yith-paypal-adaptive-payments-for-woocommerce/';
		/**
		 * @var string panel page
		 */
		protected $_panel_page = 'yith_paypal_adaptive_payments_panel';


		public function __construct() {

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_PAYPAL_ADAPTIVE_DIR . '/' . basename( YITH_PAYPAL_ADAPTIVE_FILE ) ), array(
				$this,
				'action_links'
			) );
			//Add row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			//Add action for register and update plugin
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			//Add YITH PayPal Adaptive Payment menu
			add_action( 'admin_menu', array( $this, 'add_menu' ), 5 );

			add_action( 'yith_paypal_adaptive_payments_gateway_settings_tab', array(
				$this,
				'print_paypal_adaptive_payments_panel'
			) );

			add_action( 'woocommerce_admin_field_receivers-list', array( $this, 'show_receivers_list' ) );

			add_action( 'wp_ajax_paypal_adptive_payments_json_search_customers', array(
				$this,
				'paypal_adptive_payments_json_search_customers'
			) );
			add_action( 'wp_ajax_paypal_adptive_payments_search_paypal_email', array(
				$this,
				'paypal_adptive_payments_search_paypal_email'
			) );

			//Add endpoint
			add_action( 'init', array( $this, 'add_query_var' ), 5 );
			add_action( 'init', array( $this, 'rewrite_rules' ), 20 );
			add_action( 'update_option_ywpadp_receiver_endpoint', array( $this, 'rewrite_rules_after_update' ) );
			//Load gateways
			add_action( 'init', array( $this, 'load_gateways' ), 5 );
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_paypal_adaptive_gateway_class' ) );

			//Set a cron  for check incomplete payments
			add_action( 'init', array( $this, 'set_cron' ), 15 );
			add_filter( 'cron_schedules', array( $this, 'cron_schedule' ), 50 );
			add_action( 'update_option_ywpadp_cron_check_day', array( $this, 'destroy_schedule' ) );
			add_action( 'update_option_ywpadp_cron_check_type', array( $this, 'destroy_schedule' ) );

			add_action( 'wp_ajax_yith_paypal_adaptive_payments_complete_payment', array(
				$this,
				'pay_secondary_receiver'
			) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 10 );

			add_action( 'admin_init', array( $this, 'reset_receiver_options' ) );

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'remove_other_payment_gateways' ), 15 );

			if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				YITH_Paypal_Adaptive_Payments_Admin();
			}

			add_action( 'plugins_loaded', array( $this, 'load_privacy_class' ), 20 );

			YITH_PADP_Receivers();

			YITH_PayPal_Adaptive_Payments_Integrations();
		}

		/**
		 * @return YITH_Paypal_Adaptive_Payments
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/* load plugin fw
	  * @author YITHEMES
	  * @since 1.0.0
	  */
		public function plugin_fw_loader() {

			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * add custom action links
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $links
		 *
		 * @return array
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 * @param  $init_file
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_PAYPAL_ADAPTIVE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_PAYPAL_ADAPTIVE_SLUG;
				$new_row_meta_args['is_premium'] = true;

			}

			return $new_row_meta_args;
		}

		/** Register plugins for activation tab
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {

				require_once YITH_PAYPAL_ADAPTIVE_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_PAYPAL_ADAPTIVE_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_PAYPAL_ADAPTIVE_INIT, YITH_PAYPAL_ADAPTIVE_SECRET_KEY, YITH_PAYPAL_ADAPTIVE_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {

				require_once( YITH_PAYPAL_ADAPTIVE_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YITH_PAYPAL_ADAPTIVE_SLUG, YITH_PAYPAL_ADAPTIVE_INIT );
		}

		/**
		 * add YITH Paypal Adaptive Payments menu under YITH_Plugins
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_menu() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters( 'yith_padp_add_tab', array(
				'gateway-settings'           => __( 'Gateway Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),
				'general-settings'           => __( 'Receiver Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),
				'privacy-settings'           => __( 'Account & Privacy', 'yith-paypal-adaptive-payments-for-woocommerce' ),
				'cron-settings'              => __( 'Cron Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),
				'receiver-endpoint-settings' => __( 'Endpoint Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),

			) );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'PayPal Adaptive Payments', 'Plugin name in admin page title', 'yith-paypal-adaptive-payments-for-woocommerce' ),
				'menu_title'       => 'PayPal Adaptive Payments',
				'capability'       => apply_filters( 'yith_padp_manage_options', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_PAYPAL_ADAPTIVE_DIR . '/plugin-options'
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_PAYPAL_ADAPTIVE_DIR . 'plugin-fw/lib/yith-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		public function load_gateways() {

			$load = ! isset( $_GET['post'] ) || ( isset( $_GET['post'] ) && 'product' !== get_post_type( $_GET['post'] ) );
			if ( function_exists( 'WC' ) && $load ) {
				WC()->payment_gateways();
			}
		}

		/**
		 * search the custom by paypal email
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function paypal_adptive_payments_json_search_customers() {

			ob_start();

			//  check_ajax_referer( 'search-paypal-email-customers', 'security' );

			$term    = wc_clean( stripslashes( $_GET['term'] ) );
			$exclude = array();

			if ( empty( $term ) ) {
				die();
			}

			if ( ! empty( $_GET['exclude'] ) ) {
				$exclude = array_map( 'intval', explode( ',', $_GET['exclude'] ) );
			}

			$found_customers = array();

			add_action( 'pre_user_query', 'WC_AJAX::json_search_customer_name' );

			$customers_query = new WP_User_Query( array(
				'fields'         => 'all',
				'orderby'        => 'display_name',
				'search'         => '*' . $term . '*',
				'search_columns' => array( 'ID', 'user_login', 'yith_paypal_email', 'user_nicename' )
			) );

			remove_action( 'pre_user_query', 'WC_AJAX::json_search_customer_name' );

			$customers = $customers_query->get_results();

			if ( ! empty( $customers ) ) {
				foreach ( $customers as $customer ) {
					if ( ! in_array( $customer->ID, $exclude ) ) {


						$url                              = admin_url( 'user-edit.php' );
						$params                           = array( 'user_id' => $customer->ID );
						$edit_user_url                    = esc_url( add_query_arg( $params, $url ) );
						$found_customers[ $customer->ID ] = '#' . $customer->ID . '-' . $customer->display_name;

					}
				}
			}

			$found_customers = apply_filters( 'yith_paypal_adaptive_payments_json_search_found_customers', $found_customers );

			wp_send_json( $found_customers );
		}

		public function paypal_adptive_payments_search_paypal_email() {

			ob_start();
			check_ajax_referer( 'search-paypal-email-customers', 'security' );

			$user_id = $_REQUEST['user_id'];
			$user    = get_userdata( $user_id );
			$result  = '';
			if ( $user ) {

				$result = $user->yith_paypal_email;
			}

			wp_send_json( array( 'result' => $result ) );
		}

		public function show_receivers_list( $option ) {

			$option['option'] = $option;

			wc_get_template( 'receivers-list.php', $option, YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH . 'admin/types/', YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH . 'admin/types/' );
		}


		public function add_paypal_adaptive_gateway_class( $methods ) {

			$methods[] = YITH_Paypal_Adaptive_Payments_Gateway();

			return $methods;
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function set_cron() {

			if ( ! wp_next_scheduled( 'yith_paypal_adaptive_payments_cron' ) ) {
				wp_schedule_event( current_time( 'timestamp', 1 ), 'yith_padp_gap', 'yith_paypal_adaptive_payments_cron' );
			}

		}

		/**
		 * Cron Schedule
		 *
		 * Add new schedules to wordpress
		 *
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function cron_schedule( $schedules ) {

			$cron_time = get_option( 'ywpadp_cron_check_day', 1 );
			$cron_type = get_option( 'ywpadp_cron_check_type', 'hours' );

			switch ( $cron_type ) {

				case 'days':
					$interval = $cron_time * DAY_IN_SECONDS;
					break;
				case 'minutes':
					$interval = $cron_time * MINUTE_IN_SECONDS;
					break;
				default:
					$interval = $cron_time * HOUR_IN_SECONDS;
					break;
			}

			$schedules['yith_padp_gap'] = array(
				'interval' => $interval,
				'display'  => __( 'YITH PayPal Adaptive Payments for WooCommerce Cron', 'yith-paypal-adaptive-payments-for-woocomerce' )
			);

			return $schedules;
		}

		/**
		 * Destroy the schedule
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function destroy_schedule() {
			wp_clear_scheduled_hook( 'yith_paypal_adaptive_payments_cron' );
			$this->set_cron();
		}

		/**
		 *
		 */
		public function pay_secondary_receiver() {

			if ( check_admin_referer( 'yith-padp-complete-payment' ) ) {

				$order_id = absint( $_GET['order_id'] );

				do_action( 'yith_paypal_adaptive_payments_pay_secondary_receivers', $order_id );
			}

			wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
			die();
		}

		/**
		 * add query var
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_query_var() {

			$endpoint                            = yith_paypal_adaptive_payments_receivers_get_endpoint();
			WC()->query->query_vars[ $endpoint ] = $endpoint;

		}

		/**
		 * first installation or successive activation rewrite rules
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function rewrite_rules() {

			$rewrite = get_option( 'ywpadp_rewrite', true );

			if ( $rewrite ) {

				flush_rewrite_rules();
				update_option( 'ywpadp_rewrite', false );
			}
		}

		/**
		 * rewrite rules after update endpoint option
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function rewrite_rules_after_update() {

			flush_rewrite_rules();
		}

		/**
		 * print gateway settings in YITH Panel
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function print_paypal_adaptive_payments_panel() {

			if ( file_exists( YITH_PAYPAL_ADAPTIVE_DIR . '/templates/admin/settings-tab.php' ) ) {

				global $current_section;
				$current_section = 'yith_paypal_adaptive_payments';

				WC_Admin_Settings::get_settings_pages();

				if ( ! empty( $_POST ) ) {
					YITH_Paypal_Adaptive_Payments_Gateway()->process_admin_options();
					$admin_url = admin_url( 'admin.php' );
					$admin_url = esc_url( add_query_arg( array( 'page' => 'yith_paypal_adaptive_payments_panel' ) ), $admin_url );
					wp_safe_redirect( $admin_url );
					exit;

				}

				include_once( YITH_PAYPAL_ADAPTIVE_DIR . '/templates/admin/settings-tab.php' );
			}
		}

		public function enqueue_style() {

			if ( is_checkout() ) {
				wp_enqueue_style( 'yith_padp_style', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'css/yith_paypal_adp.css', array(), YITH_PAYPAL_ADAPTIVE_VERSION );
			}
		}

		/**
		 * reset the receivers option
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function reset_receiver_options() {

			if ( ( ! empty( $_GET['page'] ) && 'yith_paypal_adaptive_payments_panel' == $_GET['page'] ) && ( ! empty( $_GET['tab'] ) && 'general-settings' == $_GET['tab'] ) ) {

				if ( ( isset( $_REQUEST['ywcpadp_hidden_field'] ) && 'check_empty' == $_REQUEST['ywcpadp_hidden_field'] ) && ! isset( $_REQUEST['yith_receiver'] ) ) {

					update_option( 'yith_receiver', array() );
				}
			}
		}

		public function remove_other_payment_gateways( $available_gateways ) {

			$remove_other_payments = apply_filters( 'yith_paypal_adaptive_payments_remove_other_gateways', true );

			if ( YITH_Paypal_Adaptive_Payments_Gateway()->is_available() && $remove_other_payments ) {

				foreach ( $available_gateways as $gateway_id => $gateway ) {

					if ( 'yith_paypal_adaptive_payments' != $gateway_id ) {
						unset( $available_gateways[ $gateway_id ] );
					}
				}
			}

			return $available_gateways;
		}


		public function load_privacy_class() {

			require_once( YITH_PAYPAL_ADAPTIVE_INC . 'class.yith-padp-privacy.php' );

			new YITH_Adaptive_Payments_Privacy();
		}
	}
}