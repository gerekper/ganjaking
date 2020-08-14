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
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

if ( ! class_exists( 'YITH_WC_SMS_Notifications' ) ) {

	class YITH_WC_SMS_Notifications {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WC_SMS_Notifications
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
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
		 * @return YITH_WC_SMS_Notifications
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
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );


			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_fields' ), 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_empty_array' ), 10, 2 );

			add_filter( 'http_request_args', array( $this, 'enable_unsafe_urls' ) );

			add_action( 'init', array( $this, 'includes' ), 5 );
			add_action( 'init', array( $this, 'init_order_statuses' ), 10 );
			add_action( 'init', array( $this, 'init_booking_statuses' ), 20 );
			add_action( 'init', array( $this, 'init_multivendor_integration' ), 20 );

			if ( 'YWSN_Void_Sender' === get_option( 'ywsn_sms_gateway' ) ) {
				add_action( 'admin_notices', array( $this, 'add_admin_notices' ) );
			}

			if ( 'requested' === get_option( 'ywsn_customer_notification' ) ) {
				add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'show_sms_request_option' ) );
				add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_sms_request_option' ) );
			}

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function includes() {

			include_once( 'includes/functions-ywsn.php' );
			include_once( 'includes/class-ywsn-messages.php' );
			include_once( 'includes/class-ywsn-sms-gateway.php' );
			include_once( 'includes/class-ywsn-metabox.php' );
			include_once( 'includes/class-ywsn-ajax.php' );

		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		 * @param   $path  string
		 * @param   $field array
		 *
		 * @return  string
		 * @since   1.2.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_custom_fields( $path, $field ) {

			if ( 'yith-wc-custom-checklist' === $field['type'] ) {
				$path = YWSN_TEMPLATE_PATH . '/admin/class-yith-wc-custom-checklist.php';
			}

			if ( 'yith-wc-check-matrix-table' === $field['type'] ) {
				$path = YWSN_TEMPLATE_PATH . '/admin/class-yith-wc-check-matrix-table.php';
			}

			if ( 'ywsn-sms-send' === $field['type'] ) {
				$path = YWSN_TEMPLATE_PATH . '/admin/class-ywsn-sms-send.php';
			}

			return $path;

		}

		/**
		 * Sanitize empty array
		 *
		 * @param   $value  array
		 * @param   $option array
		 *
		 * @return  array
		 * @since   1.3.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function sanitize_empty_array( $value, $option ) {

			if ( isset( $option['yith-type'] ) && ( 'yith-wc-check-matrix-table' === $option['yith-type'] || 'yith-wc-custom-checklist' === $option['yith-type'] ) ) {
				if ( empty( $value ) ) {
					$value = '';
				} else {
					$value = maybe_serialize( $value );
				}
			}

			return $value;
		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'ywsn-admin', YWSN_ASSETS_URL . '/css/ywsn-admin' . $suffix . '.css' );
			wp_enqueue_script( 'ywsn-admin', YWSN_ASSETS_URL . '/js/ywsn-admin' . $suffix . '.js', array( 'jquery' ) );

			$ext_charset = apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) );
			$sms_length  = empty( $ext_charset ) ? 160 : 70;

			if ( get_option( 'ywsn_enable_sms_length', 'no' ) === 'yes' ) {
				$sms_length = get_option( 'ywsn_sms_length', '160' );
			}

			$params = array(
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'sms_length'                => apply_filters( 'ywsn_sms_limit', $sms_length ),
				'sms_customer_notification' => get_option( 'ywsn_customer_notification' ),
				'sms_after_send'            => esc_html__( 'Message sent successfully!', 'yith-woocommerce-sms-notifications' ),
				'sms_no_message'            => esc_html__( 'Please select the type of message you want to send.', 'yith-woocommerce-sms-notifications' ),
				'sms_empty_message'         => esc_html__( 'Your message is blank!', 'yith-woocommerce-sms-notifications' ),
				'sms_wrong'                 => esc_html__( 'Please enter a valid phone number.', 'yith-woocommerce-sms-notifications' ),
				'sms_before_send'           => esc_html__( 'Sending...', 'yith-woocommerce-sms-notifications' ),
				'sms_manual_send_advice'    => esc_html__( 'The client did not requested sms notifications. Do you really want to send it?', 'yith-woocommerce-sms-notifications' ),
			);

			wp_localize_script( 'ywsn-admin', 'ywsn_admin', $params );

		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_admin_notices() {

			?>
			<div class="error">
				<p>
					<?php esc_html_e( 'To use this plugin, you should first select and configure an SMS service provider', 'yith-woocommerce-sms-notifications' ); ?>
				</p>
			</div>
			<?php

		}

		/**
		 * Init send SMS for each order status
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_order_statuses() {

			foreach ( array_keys( wc_get_order_statuses() ) as $status ) {

				$slug = ( 'wc-' === substr( $status, 0, 3 ) ) ? substr( $status, 3 ) : $status;

				add_action( 'woocommerce_order_status_' . $slug, array( $this, 'order_status_changed' ), 99, 2 );

			}

		}

		/**
		 * Init send SMS for each order status
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_booking_statuses() {

			if ( ywsn_is_booking_active() ) {
				foreach ( array_keys( yith_wcbk_get_booking_statuses( true ) ) as $status ) {
					add_action( 'yith_wcbk_booking_status_' . $status, array( $this, 'booking_status_changed' ), 99, 2 );
				}
			}

		}

		/**
		 * On change order status send SMS
		 *
		 * @param   $order_id integer
		 * @param   $order    WC_Order
		 *
		 * @return  void
		 * @throws  Exception
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function order_status_changed( $order_id, $order ) {

			$current_action    = str_replace( 'woocommerce_order_status_', '', current_action() );
			$order_status      = apply_filters( 'ywsn_order_status', $order->get_status(), $order_id );
			$order_status      = ( 'wc-' === substr( $order_status, 0, 3 ) ) ? $order_status : 'wc-' . $order_status;
			$order_status_slug = ( 'wc-' === substr( $order_status, 0, 3 ) ) ? substr( $order_status, 3 ) : $order_status;

			if ( false === $current_action || $current_action !== $order_status_slug || ! apply_filters( 'ywsn_allow_sms_sending', true, $order_id, $order ) ) {
				return;
			}

			$active_sms = $this->get_active_sms( $order );
			$args       = array(
				'object' => $order,
			);

			$sms          = new YWSN_Messages( $args );
			$shop_country = substr( get_option( 'woocommerce_default_country' ), 0, 2 );

			if ( isset( $active_sms[ $order_status ]['customer'] ) && 1 === (int) $active_sms[ $order_status ]['customer'] && $this->user_receives_sms( $order_id ) && wp_get_post_parent_id( $order_id ) === 0 && '' !== $order->get_billing_phone() ) {

				$message          = $this->get_status_customer_message( $order_status, $order );
				$message          = ywsn_replace_placeholders( $message, $order );
				$customer_country = $order->get_billing_country();
				$order_country    = '' !== $customer_country ? $customer_country : $shop_country;
				$sms->pre_send_sms( $order->get_billing_phone(), $message, 'customer', $order_country );

			}

			//APPLY_FILTER: ywsn_send_delay: sets a delay before sendind admin messages
			$delay = apply_filters( 'ywsn_send_delay', 0 );
			sleep( $delay );

			if ( isset( $active_sms[ $order_status ]['admin'] ) && 1 === (int) $active_sms[ $order_status ]['admin'] ) {

				$admin_phones = ywsn_get_admin_numbers( $order );
				$message      = get_option( 'ywsn_message_admin' );
				$message      = ywsn_replace_placeholders( $message, $order );

				foreach ( $admin_phones as $admin_phone ) {
					$admin_country = apply_filters( 'ywsn_admin_country_code', $shop_country, $admin_phone );
					$sms->pre_send_sms( $admin_phone, $message, 'admin', $admin_country );
				}
			}

		}

		/**
		 * On change booking status send SMS
		 *
		 * @param   $booking_id integer
		 *
		 * @return  void
		 * @throws  Exception
		 * @since   1.4.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function booking_status_changed( $booking_id ) {

			$current_action = str_replace( 'yith_wcbk_booking_status_', '', current_action() );
			$booking        = yith_get_booking( $booking_id );
			if ( ! $booking ) {
				return;
			}

			$order = $booking->get_order();
			if ( ! $order ) {
				return;
			}

			$booking_status = apply_filters( 'ywsn_order_status', $booking->get_status(), $booking_id );

			if ( false === $current_action || $current_action !== $booking_status || ! apply_filters( 'ywsn_allow_sms_sending_booking', true, $booking_id, $booking ) ) {
				return;
			}

			$active_sms = $this->get_active_sms( $order, $booking );
			$args       = array(
				'object' => $booking,
			);

			$sms          = new YWSN_Messages( $args );
			$shop_country = substr( get_option( 'woocommerce_default_country' ), 0, 2 );

			if ( isset( $active_sms[ $booking_status ]['customer'] ) && 1 === $active_sms[ $booking_status ]['customer'] && $this->user_receives_sms( $order->get_id() ) && wp_get_post_parent_id( $order->get_id() ) === 0 && '' !== $order->get_billing_phone() ) {

				$message          = $this->get_status_customer_message( $booking_status, $order, $booking );
				$message          = ywsn_replace_placeholders( $message, $booking );
				$customer_country = $order->get_billing_country();
				$order_country    = '' !== $customer_country ? $customer_country : $shop_country;
				$sms->pre_send_sms( $order->get_billing_phone(), $message, 'customer', $order_country );

			}

			//APPLY_FILTER: ywsn_send_delay: sets a delay before sendind admin messages
			$delay = apply_filters( 'ywsn_send_delay', 0 );
			sleep( $delay );

			if ( isset( $active_sms[ $booking_status ]['admin'] ) && 1 === (int) $active_sms[ $booking_status ]['admin'] ) {

				$admin_phones = ywsn_get_admin_numbers( $order );
				$message      = get_option( 'ywsn_message_booking_admin' );
				$message      = ywsn_replace_placeholders( $message, $booking );

				foreach ( $admin_phones as $admin_phone ) {
					$admin_country = apply_filters( 'ywsn_admin_country_code', $shop_country, $admin_phone );
					$sms->pre_send_sms( $admin_phone, $message, 'admin', $admin_country );
				}
			}

		}

		/**
		 * Get the customer message for current status
		 *
		 * @param   $status   string
		 * @param   $order    WC_Order
		 * @param   $booking  YITH_WCBK_Booking
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_status_customer_message( $status, $order, $booking = null ) {

			$lang        = $order->get_meta( 'wpml_language' );
			$booking_sms = $booking ? 'booking_' : '';

			$message = apply_filters( 'wpml_translate_single_string', get_option( 'ywsn_message_' . $booking_sms . $status ), 'admin_texts_ywsn_message_' . $booking_sms . $status, 'ywsn_message_' . $booking_sms . $status, $lang );

			if ( empty( $message ) ) {

				$message = get_option( 'ywsn_message_' . $booking_sms . 'generic' );

			}

			return $message;

		}

		/**
		 * Get active SMS list with special behavior for sub-orders
		 *
		 * @param   $order   WC_Order
		 * @param   $booking YITH_WCBK_Booking
		 *
		 * @return  array
		 * @since   1.0.3
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_active_sms( $order, $booking = null ) {

			$is_booking = $booking ? '_booking' : '';

			if ( wp_get_post_parent_id( $order->get_id() ) !== 0 ) {

				if ( get_option( 'ywsn_vendors_can_choose_status' ) === 'yes' ) {
					$active_sms = apply_filters( 'ywsn_active_sms' . $is_booking, array(), $order );
				} else {
					$active_sms = get_option( 'ywsn_vendor_active_sms' . $is_booking, array() );
				}
			} else {
				$active_sms = get_option( 'ywsn_sms_active_send' . $is_booking, array() );
			}

			return maybe_unserialize( $active_sms );

		}

		/**
		 * Check if customer wants to receive SMS
		 *
		 * @param   $order_id integer
		 *
		 * @return  boolean
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function user_receives_sms( $order_id ) {

			if ( 'requested' === get_option( 'ywsn_customer_notification' ) ) {

				$order       = wc_get_order( $order_id );
				$receive_sms = $order->get_meta( '_ywsn_receive_sms' );

				return ( 'yes' === $receive_sms );

			} else {

				return true;

			}

		}

		/**
		 * Show SMS request checkbox in checkout page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_sms_request_option() {

			if ( ! empty( $_POST['ywsn_receive_sms'] ) ) {

				$value = wc_clean( $_POST['ywsn_receive_sms'] );

			} else {

				$value = get_option( 'ywsn_checkout_checkbox_value' ) === 'yes';

			}

			//APPLY_FILTER: ywsn_checkout_option_label: sets a label for the checkout option
			$label = apply_filters( 'ywsn_checkout_option_label', get_option( 'ywsn_checkout_checkbox_text' ) );

			if ( ! empty( $label ) ) {

				woocommerce_form_field(
					'ywsn_receive_sms',
					array(
						'type'  => 'checkbox',
						'class' => array( 'form-row-wide' ),
						'label' => $label,
					),
					$value
				);

			}

		}

		/**
		 * Save SMS request checkbox in checkout page
		 *
		 * @param   $order_id integer
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		 * @param   $args array
		 *
		 * @return  array
		 * @since   1.0.8
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enable_unsafe_urls( $args ) {

			$active_gateway   = get_option( 'ywsn_sms_gateway' );
			$enabled_gateways = array( 'YWSN_Jazz' );

			if ( in_array( $active_gateway, $enabled_gateways, true ) ) {

				$args['reject_unsafe_urls'] = false;

			}

			return $args;

		}

		/**
		 * Add YITH WooCommerce Multi Vendor integration
		 *
		 * @return  void
		 * @since   1.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_multivendor_integration() {
			if ( ywsn_is_multivendor_active() ) {
				include_once( 'includes/class-ywsn-multivendor.php' );
			}
		}

		/**
		 * Register privacy text
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		 * @return  void
		 * @since   1.0.0
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
		 * @param   $links | links plugin array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWSN_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug']       = YWSN_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
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
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWSN_SLUG, YWSN_INIT );
		}

		/**
		 * Add Plugin Requirements
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_plugin_requirements() {

			$plugin_data  = get_plugin_data( plugin_dir_path( __FILE__ ) . '/init.php' );
			$plugin_name  = $plugin_data['Name'];
			$requirements = array(
				'min_wp_version' => '5.2.0',
				'min_wc_version' => '4.0.0',
			);
			yith_plugin_fw_add_requirements( $plugin_name, $requirements );
		}

	}

}
