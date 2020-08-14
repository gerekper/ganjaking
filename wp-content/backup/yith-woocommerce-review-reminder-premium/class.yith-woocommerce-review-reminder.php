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

if ( ! class_exists( 'YWRR_Review_Reminder' ) ) {

	/**
	 * Implements features of YWRR plugin
	 *
	 * @class   YWRR_Review_Reminder
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YWRR_Review_Reminder {

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-review-reminder/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-review-reminder/';

		/**
		 * @var string Yith WooCommerce Review Reminder panel page
		 */
		protected $_panel_page = 'yith_ywrr_panel';

		/**
		 * Single instance of the class
		 *
		 * @since 1.1.5
		 * @var YWRR_Review_Reminder
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWRR_Review_Reminder
		 * @since 1.1.5
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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_action( 'plugins_loaded', array( $this, 'include_privacy_text' ), 20 );
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YWRR_DIR . '/' . basename( YWRR_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_option( 'ywrr_mail_schedule_day', 7 );
			add_option( 'ywrr_mail_template', 'base' );
			delete_option( 'ywrr_enable_plugin' );

			add_action( 'init', array( $this, 'init_crons' ) );
			add_action( 'init', array( $this, 'includes' ), 15 );

			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'yith_review_reminder_premium', array( $this, 'premium_tab' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			add_filter( 'woocommerce_email_classes', array( $this, 'add_ywrr_email' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
			add_action( 'ywrr_email_header', array( $this, 'get_email_header' ), 10, 2 );
			add_action( 'ywrr_email_footer', array( $this, 'get_email_footer' ), 10, 3 );

			if ( get_option( 'ywrr_refuse_requests' ) == 'yes' ) {

				add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'show_request_option' ) );
				add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_request_option' ) );
				add_action( 'woocommerce_edit_account_form', array( $this, 'show_request_option_my_account' ) );
				add_action( 'woocommerce_save_account_details', array( $this, 'save_request_option_my_account' ) );

			}

		}

		/**
		 * Cron initialization
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_crons() {

			$ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';

			if ( ! wp_next_scheduled( 'ywrr_daily_send_mail_job' ) ) {
				wp_schedule_event( strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'ywrr_daily_send_mail_job' );
			}

			if ( ! wp_next_scheduled( 'ywrr_hourly_send_mail_job' ) ) {
				wp_schedule_event( strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'hourly', 'ywrr_hourly_send_mail_job' );
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

			include_once( 'includes/ywrr-functions.php' );
			include_once( 'includes/class-ywrr-unsubscribe.php' );
			include_once( 'includes/admin/class-ywrr-ajax.php' );

			if ( is_admin() ) {
				include_once( 'includes/admin/class-yith-custom-table.php' );
				include_once( 'templates/admin/ywrr-blocklist-table.php' );
			}

		}

		/**
		 * Initializes Javascript with localization
		 *
		 * @return  void
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts() {

			global $post;

			if ( ! ywrr_vendor_check() && ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_ywrr_panel' ) ) {

				wp_enqueue_style( 'ywrr-admin', yit_load_css_file( YWRR_ASSETS_URL . 'css/ywrr-admin.css' ), array(), YWRR_VERSION );
				wp_enqueue_script( 'ywrr-admin', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-admin.js' ), array(), YWRR_VERSION );

				$params = array(
					'ajax_url'               => admin_url( 'admin-ajax.php' ),
					'mail_wrong'             => esc_html__( 'Please insert a valid email address', 'yith-woocommerce-review-reminder' ),
					'before_send_test_email' => esc_html__( 'Sending test email...', 'yith-woocommerce-review-reminder' ),
					'please_wait'            => esc_html__( 'Please wait...', 'yith-woocommerce-review-reminder' ),
					'assets_url'             => YWRR_ASSETS_URL
				);

				wp_localize_script( 'ywrr-admin', 'ywrr_admin', $params );

			}

		}

		/**
		 * Get the email header.
		 *
		 * @param   $email_heading string
		 * @param   $template      mixed
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_email_header( $email_heading, $template = false ) {

			if ( ! $template ) {
				$template = get_option( 'ywrr_mail_template' );
			}

			$templates = ywrr_get_templates();

			if ( in_array( $template, $templates ) ) {

				wc_get_template( 'emails/' . $template . '/email-header.php', array( 'email_heading' => $email_heading ), false, YWRR_TEMPLATE_PATH );

			} else {

				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading, 'mail_type' => 'yith-review-reminder' ) );

			}

		}

		/**
		 * Get the email footer.
		 *
		 * @param   $unsubscribe_url  string
		 * @param   $template         mixed
		 * @param   $unsubscribe_text string
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_email_footer( $unsubscribe_url, $template = false, $unsubscribe_text ) {

			if ( ! $template ) {
				$template = get_option( 'ywrr_mail_template' );
			}

			$templates = ywrr_get_templates();

			if ( in_array( $template, $templates ) ) {

				wc_get_template( 'emails/' . $template . '/email-footer.php', array( 'unsubscribe_url' => $unsubscribe_url, 'unsubscribe_text' => $unsubscribe_text ), false, YWRR_TEMPLATE_PATH );

			} else {

				wc_get_template( 'emails/email-footer.php', array( 'mail_type' => 'yith-review-reminder' ) );

			}

		}

		/**
		 * ADMIN FUNCTIONS
		 */

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

			$admin_tabs = apply_filters( 'ywrr_panel_tabs', array(
				'mail'      => esc_html__( 'Mail Settings', 'yith-woocommerce-review-reminder' ),
				'blocklist' => esc_html__( 'Blocklist', 'yith-woocommerce-review-reminder' ),
				'premium'   => esc_html__( 'Premium Version', 'yith-woocommerce-review-reminder' )
			) );

			$args = array(
				'create_menu_page' => true,
				'plugin_slug'      => YWRR_SLUG,
				'parent_slug'      => '',
				'page_title'       => esc_html__( 'Review Reminder', 'yith-woocommerce-review-reminder' ),
				'menu_title'       => 'Review Reminder',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWRR_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Add the YWRR_Request_Mail class to WooCommerce mail classes
		 *
		 * @param   $email_classes array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_ywrr_email( $email_classes ) {

			$email_classes['YWRR_Request_Mail'] = include( 'includes/emails/class-ywrr-request-email.php' );

			return $email_classes;
		}

		/**
		 * Hook the mailer functions
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function load_wc_mailer() {

			add_filter( 'send_ywrr_mail', array( $this, 'send_transactional_email' ), 10, 1 );

		}

		/**
		 * Instantiate WC_Emails instance and send transactional emails
		 *
		 * @param   $args array
		 *
		 * @return  boolean
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send_transactional_email( $args = array() ) {

			try {

				WC_Emails::instance(); // Init self so emails exist.

				return apply_filters( 'send_ywrr_mail_notification', $args );

			} catch ( Exception $e ) {

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					trigger_error( 'Transactional email triggered fatal error for callback ' . current_filter(), E_USER_WARNING );
				}

				return false;
			}

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Show email request checkbox in checkout page
		 *
		 * @return  void
		 * @since   1.2.6
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_request_option() {

			if ( ! ywrr_check_blocklist( get_current_user_id(), '' ) ) {
				return;
			}

			//APPLY_FILTER: ywrr_checkout_option_label: checkout label text
			$label = apply_filters( 'ywrr_checkout_option_label', get_option( 'ywrr_refuse_requests_label' ) );

			if ( ! empty( $label ) ) {

				woocommerce_form_field( 'ywrr_receive_requests', array(
					'type'  => 'checkbox',
					'class' => array( 'form-row-wide' ),
					'label' => $label,
				), 0 );

			}

		}

		/**
		 * Save email request checkbox in checkout page
		 *
		 * @return  void
		 * @since   1.2.6
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save_request_option() {

			if ( empty( $_POST['ywrr_receive_requests'] ) && isset( $_POST['billing_email'] ) && $_POST['billing_email'] != '' ) {

				ywrr_add_to_blocklist( get_current_user_id(), $_POST['billing_email'] );

			}

		}

		/**
		 * Add customer request option to edit account page
		 *
		 * @return  void
		 * @since   1.2.6
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_request_option_my_account() {

			//APPLY_FILTER: ywrr_checkout_option_label: checkout label text
			$label = apply_filters( 'ywrr_checkout_option_label', get_option( 'ywrr_refuse_requests_label' ) );

			?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="ywrr_receive_requests">
					<input
						name="ywrr_receive_requests"
						type="checkbox"
						class=""
						value="1"
						<?php checked( ywrr_check_blocklist( get_current_user_id(), '' ) ); ?>
					/> <?php echo $label ?>
				</label>
			</p>
			<?php

		}

		/**
		 * Save customer request option from edit account page
		 *
		 * @param   $customer_id integer
		 *
		 * @return  void
		 * @since   1.2.6
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save_request_option_my_account( $customer_id ) {

			if ( isset( $_POST['billing_email'] ) && $_POST['billing_email'] != '' ) {

				if ( isset( $_POST['ywrr_receive_requests'] ) ) {

					ywrr_remove_from_blocklist( $customer_id );

				} else {

					$email = get_user_meta( $customer_id, 'billing_email' );

					ywrr_add_to_blocklist( $customer_id, $email );

				}

			}

		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
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
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YWRR_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, false );

			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $new_row_meta_args array
		 * @param   $plugin_meta       mixed
		 * @param   $plugin_file       string
		 * @param   $plugin_data       mixed
		 * @param   $status            mixed
		 * @param   $init_file         string
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWRR_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YWRR_SLUG;
			}

			return $new_row_meta_args;

		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}

		/**
		 * Register privacy text
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function include_privacy_text() {
			include_once( 'includes/class-ywrr-privacy.php' );
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
				'min_wp_version'  => '5.2.0',
				'min_wc_version'  => '4.0.0',
				'wp_cron_enabled' => true,
			);
			yith_plugin_fw_add_requirements( $plugin_name, $requirements );
		}

	}

}