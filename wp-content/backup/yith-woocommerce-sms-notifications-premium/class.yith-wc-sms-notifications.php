<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_WC_SMS_Notifications
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! class_exists( 'YITH_WC_SMS_Notifications' ) ) {

	class YITH_WC_SMS_Notifications {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_SMS_Notifications
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel = null;

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-sms-notifications/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-sms-notifications/';

		/**
		 * @var string YITH WooCommerce SMS Notifications panel page
		 */
		protected $_panel_page = 'yith-wc-sms-notifications';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_SMS_Notifications
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			//Load plugin framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_action( 'plugins_loaded', array( $this, 'include_privacy_text' ), 20 );
			add_filter( 'plugin_action_links_' . plugin_basename( YWSN_DIR . '/' . basename( YWSN_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_fields' ), 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_empty_array' ), 10, 2 );

			add_filter( 'http_request_args', array( $this, 'enable_unsafe_urls' ) );

			$this->includes();

			add_action( 'init', array( $this, 'init_multivendor_integration' ), 20 );

			if ( 'YWSN_Void_Sender' == get_option( 'ywsn_sms_gateway' ) ) {

				add_action( 'admin_notices', array( $this, 'add_admin_notices' ) );

			}

			if ( 'requested' == get_option( 'ywsn_customer_notification' ) ) {

				add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'show_sms_request_option' ) );
				add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_sms_request_option' ) );

			}

			add_action( 'init', array( $this, 'init_order_statuses' ), 10 );

		}

		/**
		 * Files inclusion
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		private function includes() {

			include_once( 'includes/class-ywsn-messages.php' );
			include_once( 'includes/class-ywsn-sms-gateway.php' );
			include_once( 'includes/class-ywsn-url-shortener.php' );
			include_once( 'includes/class-ywsn-metabox.php' );
			include_once( 'includes/class-ywsn-ajax.php' );
			include_once( 'includes/functions-ywsn-gdpr.php' );

		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'  => _x( 'General Settings', 'general settings tab name', 'yith-woocommerce-sms-notifications' ),
				'messages' => _x( 'SMS Settings', 'sms settings tab name', 'yith-woocommerce-sms-notifications' ),
			);
			if ( apply_filters( 'ywsn_save_send_log', false ) ) {
				$admin_tabs['debug'] = _x( 'Debug', 'debug tab name', 'yith-woocommerce-sms-notifications' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'SMS Notifications', 'plugin name in admin page title', 'yith-woocommerce-sms-notifications' ),
				'menu_title'       => 'SMS Notifications',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWSN_DIR . 'plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Initialize custom fields
		 *
		 * @since   1.2.2
		 *
		 * @param   $path  string
		 * @param   $field array
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function add_custom_fields( $path, $field ) {

			if ( $field['type'] == 'yith-wc-custom-checklist' ) {
				$path = YWSN_TEMPLATE_PATH . '/admin/class-yith-wc-custom-checklist.php';
			}

			if ( $field['type'] == 'yith-wc-check-matrix-table' ) {
				$path = YWSN_TEMPLATE_PATH . '/admin/class-yith-wc-check-matrix-table.php';
			}

			if ( $field['type'] == 'ywsn-sms-send' ) {
				$path = YWSN_TEMPLATE_PATH . '/admin/class-ywsn-sms-send.php';
			}

			return $path;

		}

		/**
		 * Sanitize empty array
		 *
		 * @since   1.3.2
		 *
		 * @param   $value  array
		 * @param   $option array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function sanitize_empty_array( $value, $option ) {

			if ( isset( $option['yith-type'] ) && ( $option['yith-type'] == 'yith-wc-check-matrix-table' || $option['yith-type'] == 'yith-wc-custom-checklist' ) ) {
				if ( empty( $value ) ) {
					$value = '';
				} else {
					$value = maybe_serialize( $value );
				}
			}

			return $value;
		}

		/**
		 * Add scipts and styles
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			if ( is_admin() ) {
				global $post;

				$post_id = isset( $post ) ? $post->ID : '';
			} else {
				$post_id = isset( $_GET['id'] ) ? $_GET['id'] : '';
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'ywsn-admin', YWSN_ASSETS_URL . '/css/ywsn-admin' . $suffix . '.css' );

			wp_enqueue_script( 'ywsn-admin', YWSN_ASSETS_URL . '/js/ywsn-admin' . $suffix . '.js', array( 'jquery' ) );

			$ext_charset = apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) );
			$sms_length  = empty( $ext_charset ) ? 160 : 70;

			if ( get_option( 'ywsn_enable_sms_length', 'no' ) == 'yes' ) {
				$sms_length = get_option( 'ywsn_sms_length', '160' );
			}

			$params = array(
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'order_id'                  => $post_id,
				'sms_length'                => apply_filters( 'ywsn_sms_limit', $sms_length ),
				'sms_customer_notification' => get_option( 'ywsn_customer_notification' ),
				'sms_after_send'            => __( 'Message sent successfully!', 'yith-woocommerce-sms-notifications' ),
				'sms_no_message'            => __( 'Please select the type of message you want to send.', 'yith-woocommerce-sms-notifications' ),
				'sms_empty_message'         => __( 'Your message is blank!', 'yith-woocommerce-sms-notifications' ),
				'sms_wrong'                 => __( 'Please enter a valid phone number.', 'yith-woocommerce-sms-notifications' ),
				'sms_before_send'           => __( 'Sending...', 'yith-woocommerce-sms-notifications' ),
				'sms_manual_send_advice'    => __( 'The client did not requested sms notifications. Do you really want to send it?', 'yith-woocommerce-sms-notifications' ),
			);

			wp_localize_script( 'ywsn-admin', 'ywsn_admin', $params );

		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_admin_notices() {

			?>
            <div class="error">
                <p>
					<?php _e( 'To use this plugin, you should first select and configure an SMS service provider', 'yith-woocommerce-sms-notifications' ); ?>
                </p>
            </div>
			<?php

		}

		/**
		 * Init send SMS for each order status
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function init_order_statuses() {

			foreach ( array_keys( wc_get_order_statuses() ) as $status ) {

				$slug = ( 'wc-' === substr( $status, 0, 3 ) ) ? substr( $status, 3 ) : $status;

				add_action( 'woocommerce_order_status_' . $slug, array( $this, 'order_status_changed' ), 99, 2 );

			}

		}

		/**
		 * On change order status send SMS
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_id integer
		 * @param   $order    WC_Order
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function order_status_changed( $order_id, $order ) {

			$current_action    = str_replace( 'woocommerce_order_status_', '', current_action() );
			$order_status      = apply_filters( 'ywsn_order_status', $order->get_status(), $order_id );
			$order_status      = ( 'wc-' === substr( $order_status, 0, 3 ) ) ? $order_status : 'wc-' . $order_status;
			$order_status_slug = ( 'wc-' === substr( $order_status, 0, 3 ) ) ? substr( $order_status, 3 ) : $order_status;

			if ( 'none' == get_option( 'ywsn_sms_gateway' ) || $current_action == false || $current_action != $order_status_slug || ! apply_filters( 'ywsn_allow_sms_sending', true, $order_id, $order ) ) {
				return;
			}

			$active_sms = $this->get_active_sms( $order );

			if ( isset( $active_sms[ $order_status ]['customer'] ) && 1 == $active_sms[ $order_status ]['customer'] && $this->user_receives_sms( $order_id ) && wp_get_post_parent_id( $order_id ) == 0 ) {

				if ( '' != $order->get_billing_phone() ) {

					$customer_sms = new YWSN_Messages( $order, true );

					$customer_sms->single_sms();

				}

			}

			$delay = apply_filters( 'ywsn_send_delay', 0 );
			sleep( $delay );

			if ( isset( $active_sms[ $order_status ]['admin'] ) && 1 == $active_sms[ $order_status ]['admin'] ) {

				$admin_sms = new YWSN_Messages( $order, false );

				$admin_sms->admins_sms();

			}

		}

		/**
		 * Get active SMS list with special behavior for sub-orders
		 *
		 * @since   1.0.3
		 *
		 * @param   $order WC_Order
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_active_sms( $order ) {

			if ( wp_get_post_parent_id( $order->get_id() ) != 0 ) {

				if ( get_option( 'ywsn_vendors_can_choose_status' ) == 'yes' ) {
					$active_sms = apply_filters( 'ywsn_active_sms', array(), $order );
				} else {
					$active_sms = get_option( 'ywsn_vendor_active_sms', array() );
				}

			} else {

				$active_sms = get_option( 'ywsn_sms_active_send', array() );

			}

			return maybe_unserialize( $active_sms );

		}

		/**
		 * Check if customer wants to receive SMS
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_id integer
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function user_receives_sms( $order_id ) {

			if ( 'requested' == get_option( 'ywsn_customer_notification' ) ) {

				$order       = wc_get_order( $order_id );
				$receive_sms = $order->get_meta( '_ywsn_receive_sms' );

				return ( $receive_sms == 'yes' );

			} else {

				return true;

			}

		}

		/**
		 * Show SMS request checkbox in checkout page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function show_sms_request_option() {

			if ( ! empty( $_POST['ywsn_receive_sms'] ) ) {

				$value = wc_clean( $_POST['ywsn_receive_sms'] );

			} else {

				$value = get_option( 'ywsn_checkout_checkbox_value' ) == 'yes';

			}

			$label = apply_filters( 'ywsn_checkout_option_label', get_option( 'ywsn_checkout_checkbox_text' ) );

			if ( ! empty( $label ) ) {

				woocommerce_form_field( 'ywsn_receive_sms', array(
					'type'  => 'checkbox',
					'class' => array( 'form-row-wide' ),
					'label' => $label,
				), $value );

			}

		}

		/**
		 * Save SMS request checkbox in checkout page
		 *
		 * @since   1.0.0
		 *
		 * @param   $order_id integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function save_sms_request_option( $order_id ) {

			if ( ! empty( $_POST['ywsn_receive_sms'] ) ) {

				$order       = wc_get_order( $order_id );
				$receive_sms = isset( $_POST['ywsn_receive_sms'] ) ? 'yes' : 'no';
				$order->update_meta_data( '_ywsn_receive_sms', $receive_sms );
				$order->save();

			}

		}

		/**
		 * Enable unsafe URLs for some SMS operator
		 *
		 * @since   1.0.8
		 *
		 * @param   $args array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function enable_unsafe_urls( $args ) {

			$active_gateway   = get_option( 'ywsn_sms_gateway' );
			$enabled_gateways = array( 'YWSN_Jazz' );

			if ( in_array( $active_gateway, $enabled_gateways ) ) {

				$args['reject_unsafe_urls'] = false;

			}

			return $args;

		}

		/**
		 * Add YITH WooCommerce Multi Vendor integration
		 *
		 * @since   1.0.3
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function init_multivendor_integration() {

			if ( $this->is_multivendor_active() ) {

				include_once( 'includes/class-ywsn-multivendor.php' );

			}

		}

		/**
		 * Check if YITH WooCommerce Multi Vendor is active
		 *
		 * @since   1.0.3
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function is_multivendor_active() {

			return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;

		}

		/**
		 * Get Placeholders reference
		 *
		 * @since   1.0.8
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function placeholder_reference() {

			$placeholders = array(
				'{site_title}'       => __( 'Website name', 'yith-woocommerce-sms-notifications' ),
				'{order_id}'         => __( 'Order number', 'yith-woocommerce-sms-notifications' ),
				'{order_total}'      => __( 'Order total', 'yith-woocommerce-sms-notifications' ),
				'{order_status}'     => __( 'Order status', 'yith-woocommerce-sms-notifications' ),
				'{billing_name}'     => __( 'Billing name', 'yith-woocommerce-sms-notifications' ),
				'{shipping_name}'    => __( 'Shipping name', 'yith-woocommerce-sms-notifications' ),
				'{shipping_method}'  => __( 'Shipping method', 'yith-woocommerce-sms-notifications' ),
				'{additional_notes}' => __( 'Additional Notes', 'yith-woocommerce-sms-notifications' ),
				'{order_date}'       => __( 'Order Date', 'yith-woocommerce-sms-notifications' ),
			);

			if ( function_exists( 'YITH_YWOT' ) ) {

				$placeholders['{tracking_number}'] = __( 'Tracking Number', 'yith-woocommerce-sms-notifications' );
				$placeholders['{carrier_name}']    = __( 'Carrier name', 'yith-woocommerce-sms-notifications' );
				$placeholders['{shipping_date}']   = __( 'Shipping date', 'yith-woocommerce-sms-notifications' );
				$placeholders['{tracking_url}']    = __( 'Tracking url', 'yith-woocommerce-sms-notifications' );

			}

			return $placeholders;

		}

		/**
		 * Register privacy text
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function include_privacy_text() {
			include_once( 'includes/class-ywsn-privacy.php' );
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Andrea Grillo
		 * <andrea.grillo@yithemes.com>
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
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWSN_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YWSN_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since   2.0.0
		 * @return  void
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWSN_INIT, YWSN_SECRET_KEY, YWSN_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since   2.0.0
		 * @return  void
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWSN_SLUG, YWSN_INIT );
		}

	}

}