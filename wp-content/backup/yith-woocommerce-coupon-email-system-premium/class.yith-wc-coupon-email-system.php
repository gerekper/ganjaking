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
 * @class   YITH_WC_Coupon_Email_System
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

if ( ! class_exists( 'YITH_WC_Coupon_Email_System' ) ) {

	class YITH_WC_Coupon_Email_System {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WC_Coupon_Email_System
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
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-coupon-email-system/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-coupon-email-system/';

		/**
		 * @var string Yith WooCommerce Coupon Email System panel page
		 */
		protected $_panel_page = 'yith-wc-coupon-email-system';

		/**
		 * @var array
		 */
		protected $_email_types = array();

		/**
		 * @var array
		 */
		var $_available_coupons = array();

		/**
		 * @var array
		 */
		var $_email_templates = array();

		/**
		 * @var array
		 */
		var $_date_placeholders = array();

		/**
		 * @var array
		 */
		var $_date_formats = array();

		/**
		 * @var array
		 */
		var $_date_patterns = array();

		/**
		 * @var null
		 */
		var $_logger = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Coupon_Email_System
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
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			//Load plugin framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_action( 'plugins_loaded', array( $this, 'include_privacy_text' ), 20 );
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );

			add_filter( 'plugin_action_links_' . plugin_basename( YWCES_DIR . '/' . basename( YWCES_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			$this->includes();

			$this->_email_templates   = array(
				'ywces-1' => array(
					'folder' => '/emails/template-1',
					'path'   => YWCES_TEMPLATE_PATH
				),
				'ywces-2' => array(
					'folder' => '/emails/template-2',
					'path'   => YWCES_TEMPLATE_PATH
				),
				'ywces-3' => array(
					'folder' => '/emails/template-3',
					'path'   => YWCES_TEMPLATE_PATH
				),
			);
			$this->_date_placeholders = $this->get_date_placeholders();
			$this->_date_formats      = $this->get_date_formats();
			$this->_date_patterns     = $this->get_date_patterns();
			$this->_logger            = new WC_Logger();

			add_action( 'init', array( $this, 'init_available_coupons' ), 20 );
			add_action( 'init', array( $this, 'init_multivendor_integration' ), 20 );

			add_filter( 'woocommerce_email_classes', array( $this, 'add_ywces_custom_email' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );


			add_action( 'ywces_email_header', array( $this, 'get_email_header' ), 10, 2 );
			add_action( 'ywces_email_footer', array( $this, 'get_email_footer' ), 10, 1 );
			add_filter( 'yith_wcet_email_template_types', array( $this, 'add_yith_wcet_template' ) );

			if ( is_admin() ) {
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
				add_action( 'ywces_howto', array( $this, 'get_howto_content' ) );
				add_action( 'ywces_acceptance', array( YWCES_Acceptance_Table(), 'output' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
				add_action( 'show_user_profile', array( $this, 'add_birthday_field_admin' ) );
				add_action( 'edit_user_profile', array( $this, 'add_birthday_field_admin' ) );
				add_action( 'personal_options_update', array( $this, 'save_birthday_field_admin' ) );
				add_action( 'edit_user_profile_update', array( $this, 'save_birthday_field_admin' ) );
			}

			add_action( 'woocommerce_order_status_completed', array( $this, 'ywces_user_purchase' ) );
			add_action( 'ywces_daily_send_mail_job', array( $this, 'ywces_daily_send_mail_job' ) );

			if ( get_option( 'ywces_enable_birthday' ) == 'yes' ) {
				add_action( 'woocommerce_edit_account_form', array( $this, 'add_birthday_field' ) );
				add_action( 'woocommerce_register_form', array( $this, 'add_birthday_field' ) );
				add_action( 'woocommerce_save_account_details', array( $this, 'save_birthday_field' ) );
				add_action( 'woocommerce_created_customer', array( $this, 'save_birthday_field' ), 10, 1 );
				add_filter( 'woocommerce_checkout_fields', array( $this, 'add_birthday_field_checkout' ) );
				add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'save_birthday_field_checkout' ), 10 );
			}

			if ( get_option( 'ywces_refuse_coupon' ) == 'yes' ) {
				add_filter( 'woocommerce_checkout_fields', array( $this, 'add_accept_coupon_field_checkout' ), 20 );
				add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'save_accept_coupon_field_checkout' ) );
				add_action( 'woocommerce_edit_account_form', array( $this, 'add_accept_coupon_field_my_account' ) );
				add_action( 'woocommerce_save_account_details', array( $this, 'save_accept_coupon_field_my_account' ) );
			}

			if ( get_option( 'ywces_coupon_purge' ) == 'yes' ) {
				add_action( 'ywces_trash_coupon_cron', array( $this, 'trash_expired_coupons' ) );
			}

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );


		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		private function includes() {

			include_once( 'includes/class-ywces-emails.php' );
			include_once( 'includes/class-ywces-mandrill.php' );
			include_once( 'includes/functions.ywces.php' );

			if ( is_admin() ) {

				include_once( 'includes/class-yith-custom-table.php' );
				include_once( 'includes/class-ywces-ajax.php' );
				include_once( 'templates/admin/class-ywces-custom-send.php' );
				include_once( 'templates/admin/class-yith-wc-custom-textarea.php' );
				include_once( 'templates/admin/class-ywces-custom-table.php' );
				include_once( 'templates/admin/class-ywces-custom-collapse.php' );
				include_once( 'templates/admin/class-ywces-custom-coupon.php' );
				include_once( 'templates/admin/class-ywces-custom-mailskin.php' );
				include_once( 'templates/admin/class-ywces-custom-coupon-purge.php' );
				include_once( 'templates/admin/class-yith-wc-custom-product-select.php' );
				include_once( 'templates/admin/acceptance-table.php' );

			}

		}

		/**
		 * Register privacy text
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function include_privacy_text() {
			include_once( 'includes/class-ywces-privacy.php' );
		}


		/**
		 * Get available coupons
		 *
		 * @return  void
		 * @since   1.1.4
		 * @author  Alberto Ruggiero
		 */
		public function init_available_coupons() {

			$posts = get_posts(
				array(
					'post_type'   => 'shop_coupon',
					'post_status' => 'publish',
					'numberposts' => - 1,
					'meta_query'  => array(
						'relation' => 'OR',
						array(
							'key'     => 'generated_by',
							'value'   => 'ywces',
							'compare' => '!=',
						),
						array(
							'key'     => 'generated_by',
							'compare' => 'NOT EXISTS',
							'value'   => 'ywces',
						),
					),
				)
			);

			$coupons = array();

			foreach ( $posts as $post ) {

				$coupons[ $post->post_title ] = $post->post_title;

			}

			$this->_available_coupons = $coupons;

		}

		/**
		 * Add YITH WooCommerce Multi Vendor integration
		 *
		 * @return  void
		 * @since   1.0.5
		 * @author  Alberto Ruggiero
		 */
		public function init_multivendor_integration() {

			if ( $this->is_multivendor_active() ) {

				include_once( 'includes/class-ywces-multivendor.php' );

				$this->_available_coupons = YWCES_MultiVendor()->get_vendor_coupons();

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
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array();

			$admin_tabs['general'] = esc_html__( 'General Settings', 'yith-woocommerce-coupon-email-system' );

			if ( $this->is_multivendor_active() ) {
				$admin_tabs['admin-vendor'] = esc_html__( 'Vendors Settings', 'yith-woocommerce-coupon-email-system' );
			}

			if ( get_option( 'ywces_refuse_coupon' ) == 'yes' ) {
				$admin_tabs['acceptance'] = esc_html__( 'Coupons Acceptance', 'yith-woocommerce-coupon-email-system' );
			}

			$admin_tabs['mandrill'] = esc_html__( 'Mandrill Settings', 'yith-woocommerce-coupon-email-system' );
			$admin_tabs['howto']    = esc_html__( 'How To', 'yith-woocommerce-coupon-email-system' );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => esc_html__( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' ),
				'menu_title'       => 'Coupon Email System',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCES_DIR . 'plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Initializes CSS and javascript
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			$vendor_id = '0';
			$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'ywces-admin', YWCES_ASSETS_URL . '/css/ywces-admin.css', array(), YWCES_VERSION );
			wp_enqueue_script( 'ywces-admin', YWCES_ASSETS_URL . '/js/ywces-admin' . $suffix . '.js', array( 'jquery' ), YWCES_VERSION );

			if ( $this->is_multivendor_active() ) {

				if ( YWCES_MultiVendor()->vendors_coupon_active() ) {

					$vendor    = yith_get_vendor( 'current', 'user' );
					$vendor_id = $vendor->id;

				}

			}

			$params = apply_filters( 'ywces_admin_scripts_filter', array(
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'vendor_id'              => $vendor_id,
				'before_send_test_email' => esc_html__( 'Sending test email...', 'yith-woocommerce-coupon-email-system' ),
				'after_send_test_email'  => esc_html__( 'Test email has been sent successfully!', 'yith-woocommerce-coupon-email-system' ),
				'test_mail_wrong'        => esc_html__( 'Please insert a valid email address', 'yith-woocommerce-coupon-email-system' ),
				'test_mail_no_threshold' => esc_html__( 'You need to set at least a threshold to send a test email', 'yith-woocommerce-coupon-email-system' ),
				'test_mail_no_product'   => esc_html__( 'Please select at least a product', 'yith-woocommerce-coupon-email-system' ),
				'test_mail_no_amount'    => esc_html__( 'You need to select at least the amount/percentage of a coupon to send it in a test email', 'yith-woocommerce-coupon-email-system' ),
				'test_mail_days_elapsed' => esc_html__( 'Please specify the number of days', 'yith-woocommerce-coupon-email-system' ),
			) );

			wp_localize_script( 'ywces-admin', 'ywces_admin', $params );

		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_notices() {

			$active   = apply_filters( 'ywces_multivendor_coupon_active_notice', true );
			$messages = array();

			if ( count( $this->_available_coupons ) == 0 && $active ) {
				$messages[] = esc_html__( 'In order to use some of the features of YITH WooCommerce Coupon Email System you need to create at least one coupon', 'yith-woocommerce-coupon-email-system' );
			}

			if ( get_option( 'ywces_mandrill_enable' ) == 'yes' && get_option( 'ywces_mandrill_apikey' ) == '' ) {
				$messages[] = esc_html__( 'Please enter Mandrill API Key for YITH WooCommerce Coupon Email System', 'yith-woocommerce-coupon-email-system' );
			}

			if ( isset( $_POST['ywces_enable_register'] ) && '1' == $_POST['ywces_enable_register'] ) {

				if ( $_POST['ywces_coupon_register'] == '' ) {

					$messages[] = esc_html__( 'You need to select a coupon to send one for a new user registration.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST['ywces_enable_first_purchase'] ) && '1' == $_POST['ywces_enable_first_purchase'] ) {

				if ( $_POST['ywces_coupon_first_purchase'] == '' ) {

					$messages[] = esc_html__( 'You need to select a coupon to send one for a new first purchase.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST['ywces_enable_purchases'] ) && '1' == $_POST['ywces_enable_purchases'] ) {

				if ( ! isset( $_POST['ywces_thresholds_purchases'] ) ) {

					$messages[] = esc_html__( 'You need to set a threshold to send a coupon once a user reaches a specific number of purchases.', 'yith-woocommerce-coupon-email-system' );
					update_option( 'ywces_thresholds_purchases', '' );

				} else {

					$count = 0;

					foreach ( maybe_unserialize( $_POST['ywces_thresholds_purchases'] ) as $threshold ) {

						if ( $threshold['coupon'] == '' ) {

							$count ++;

						}

					}

					if ( $count > 0 ) {
						$messages[] = esc_html__( 'You need to set a coupon for each threshold to send one when users reach a specific number of purchases.', 'yith-woocommerce-coupon-email-system' );
					}

				}

			}

			if ( isset( $_POST['ywces_enable_spending'] ) && '1' == $_POST['ywces_enable_spending'] ) {

				if ( ! isset( $_POST['ywces_thresholds_spending'] ) ) {

					$messages[] = esc_html__( 'You need to set a threshold to send a coupon once a user reaches a specific spent amount.', 'yith-woocommerce-coupon-email-system' );
					update_option( 'ywces_thresholds_spending', '' );

				} else {

					$count = 0;

					foreach ( maybe_unserialize( $_POST['ywces_thresholds_spending'] ) as $threshold ) {

						if ( $threshold['coupon'] == '' ) {

							$count ++;

						}

					}

					if ( $count > 0 ) {

						$messages[] = esc_html__( 'You need to set a coupon for each threshold to send one when users reach a specific spent amount.', 'yith-woocommerce-coupon-email-system' );
					}

				}

			}

			if ( isset( $_POST['ywces_enable_product_purchasing'] ) && '1' == $_POST['ywces_enable_product_purchasing'] ) {

				if ( ! isset( $_POST['ywces_targets_product_purchasing'] ) || $_POST['ywces_targets_product_purchasing'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least one product to send a coupon once purchased.', 'yith-woocommerce-coupon-email-system' );

				}

				$coupon = maybe_unserialize( $_POST['ywces_coupon_product_purchasing'] );

				if ( $coupon['coupon_amount'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least the amount/percentage of a coupon to send it for the purchase of a specific product.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST['ywces_enable_birthday'] ) && '1' == $_POST['ywces_enable_birthday'] ) {

				$coupon = maybe_unserialize( $_POST['ywces_coupon_birthday'] );

				if ( $coupon['coupon_amount'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least the amount/percentage of a coupon to send it for the birthday of a user.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST['ywces_enable_last_purchase'] ) && '1' == $_POST['ywces_enable_last_purchase'] ) {

				$coupon = maybe_unserialize( $_POST['ywces_coupon_last_purchase'] );

				if ( $coupon['coupon_amount'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least the amount/percentage of a coupon to send it after a specific number of days following the last order.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( ! empty( $messages ) ) :

				?>
				<div class="error">
					<ul>
						<?php foreach ( $messages as $message ): ?>

							<li><?php echo $message ?></li>

						<?php endforeach; ?>
					</ul>
				</div>
			<?php

			endif;

		}

		/**
		 * Add the YWCES_Coupon_Mail class to WooCommerce mail classes
		 *
		 * @param   $email_classes
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_ywces_custom_email( $email_classes ) {

			$email_classes['YWCES_Coupon_Mail'] = include( 'includes/class-ywces-coupon-email.php' );

			return $email_classes;
		}

		/**
		 * Hook the mailer functions
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function load_wc_mailer() {

			add_filter( 'send_ywces_mail', array( $this, 'send_transactional_email' ), 10, 1 );

		}

		/**
		 * Instantiate WC_Emails instance and send transactional emails
		 *
		 * @param   $args
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function send_transactional_email( $args = array() ) {

			try {

				WC_Emails::instance(); // Init self so emails exist.

				return apply_filters( 'send_ywces_mail_notification', $args );

			} catch ( Exception $e ) {

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					trigger_error( 'Transactional email triggered fatal error for callback ' . current_filter(), E_USER_WARNING );
				}

				return false;
			}

		}

		/**
		 * Get the email header.
		 *
		 * @param   $email_heading
		 * @param   $template
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_email_header( $email_heading, $template = false ) {

			if ( ! $template ) {
				$template = get_option( 'ywces_mail_template', 'base' );
			}

			if ( array_key_exists( $template, $this->_email_templates ) ) {
				$path   = $this->_email_templates[ $template ]['path'];
				$folder = $this->_email_templates[ $template ]['folder'];

				wc_get_template( $folder . '/email-header.php', array( 'email_heading' => $email_heading ), '', $path . '/' );

			} else {
				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );

			}

		}

		/**
		 * Get the email footer.
		 *
		 * @param   $template
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_email_footer( $template = false ) {

			$site_name = get_option( 'blogname' );
			$site_url  = get_option( 'siteurl' );

			if ( ! $template ) {
				$template = get_option( 'ywces_mail_template', 'base' );
			}

			if ( array_key_exists( $template, $this->_email_templates ) ) {
				$path   = $this->_email_templates[ $template ]['path'];
				$folder = $this->_email_templates[ $template ]['folder'];

				wc_get_template( $folder . '/email-footer.php', array(
					'site_name' => $site_name,
					'site_url'  => $site_url
				), $path, $path );

			} else {
				echo apply_filters( 'ywces_footer_link', '<p></p><p><a href="' . $site_url . '">' . $site_name . '</a></p>' );
				wc_get_template( 'emails/email-footer.php' );
			}

		}

		/**
		 * If is active YITH WooCommerce Email Templates, add YWCES to list
		 *
		 * @param   $templates
		 *
		 * @return  array
		 * @since   1.0.1
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_yith_wcet_template( $templates ) {

			$templates[] = array(
				'id'   => 'yith-coupon-email-system',
				'name' => 'YITH WooCommerce Coupon Email System',
			);

			return $templates;

		}

		/**
		 * Check if YITH WooCommerce Multi Vendor is active
		 *
		 * @return  bool
		 * @since   1.0.5
		 * @author  Alberto Ruggiero
		 */
		public function is_multivendor_active() {

			return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;

		}

		/**
		 * Check if YITH WooCommerce Email Templates is active
		 *
		 * @return  bool
		 * @since   1.0.5
		 * @author  Alberto Ruggiero
		 */
		public function is_email_templates_active() {

			return defined( 'YITH_WCET_PREMIUM' ) && YITH_WCET_PREMIUM;

		}

		/**
		 * Get placeholder reference content.
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_howto_content() {

			?>
			<div id="plugin-fw-wc">
				<h3>
					<?php esc_html_e( 'Placeholder reference', 'yith-woocommerce-coupon-email-system' ); ?>
				</h3>
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{coupon_description}</b>
						</th>
						<td class="forminp">
							<?php esc_html_e( 'Replaced with the description of the given coupon. This placeholder must be included.', 'yith-woocommerce-coupon-email-system' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{site_title}</b>
						</th>
						<td class="forminp">
							<?php esc_html_e( 'Replaced with the site title', 'yith-woocommerce-coupon-email-system' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{customer_name}</b>
						</th>
						<td class="forminp">
							<?php esc_html_e( 'Replaced with the customer\'s name', 'yith-woocommerce-coupon-email-system' ) ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{customer_last_name}</b>
						</th>
						<td class="forminp">
							<?php esc_html_e( 'Replaced with the customer\'s last name', 'yith-woocommerce-coupon-email-system' ) ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{customeresc_html_email}</b>
						</th>
						<td class="forminp">
							<?php esc_html_e( 'Replaced with the customer\'s email', 'yith-woocommerce-coupon-email-system' ) ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{order_date}</b>
						</th>
						<td class="forminp">
							<?php esc_html_e( 'Replaced with the date of the order', 'yith-woocommerce-coupon-email-system' ) ?>
						</td>
					</tr>

					<?php if ( defined( 'YWCES_PREMIUM' ) ) : ?>

						<tr valign="top">
							<th scope="row" class="titledesc">
								<b>{purchases_threshold}</b>
							</th>
							<td class="forminp">
								<?php esc_html_e( 'Replaced with the number of purchases', 'yith-woocommerce-coupon-email-system' ) ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<b>{customer_money_spent}</b>
							</th>
							<td class="forminp">
								<?php esc_html_e( 'Replaced with the amount of money spent by the customer', 'yith-woocommerce-coupon-email-system' ) ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<b>{spending_threshold}</b>
							</th>
							<td class="forminp">
								<?php esc_html_e( 'Replaced with the spent amount of money', 'yith-woocommerce-coupon-email-system' ) ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<b>{days_ago}</b>
							</th>
							<td class="forminp">
								<?php esc_html_e( 'Replaced with the number of days since last purchase', 'yith-woocommerce-coupon-email-system' ) ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<b>{purchased_product}</b>
							</th>
							<td class="forminp">
								<?php esc_html_e( 'Replaced with the name of a purchased product', 'yith-woocommerce-coupon-email-system' ) ?>
							</td>
						</tr>

						<?php if ( $this->is_multivendor_active() ): ?>
							<tr valign="top">
								<th scope="row" class="titledesc">
									<b>{vendor_name}</b>
								</th>
								<td class="forminp">
									<?php esc_html_e( 'Replaced with the name of the vendor', 'yith-woocommerce-coupon-email-system' ) ?>
								</td>
							</tr>
						<?php endif; ?>

					<?php endif; ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * Add customer birthday field
		 *
		 * @param   $user
		 *
		 * @return  void
		 * @since   1.1.3
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_birthday_field_admin( $user ) {

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$date_format = get_option( 'ywces_date_format' );
			$birth_date  = '';

			if ( ! empty ( $user ) && get_user_meta( $user->ID, 'yith_birthday', true ) ) {

				$date       = DateTime::createFromFormat( 'Y-m-d', esc_attr( $user->yith_birthday ) );
				$birth_date = $date->format( $this->_date_formats[ $date_format ] );

			}
			?>
			<h3><?php esc_html_e( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' ); ?></h3>
			<table class="form-table">

				<tr>
					<th><label for="yith_birthday"><?php esc_html_e( 'Birth date', 'yith-woocommerce-coupon-email-system' ); ?></label></th>
					<td>
						<input
							type="text"
							class="ywces_date"
							name="yith_birthday"
							id="yith_birthday"
							value="<?php echo esc_attr( $birth_date ); ?>"
							placeholder="<?php echo $this->_date_placeholders[ $date_format ]; ?>"
							maxlength="10"
							pattern="<?php echo $this->_date_patterns[ $date_format ] ?>"
						/>

					</td>
				</tr>

			</table>

			<?php

		}

		/**
		 * Save customer birth date from admin page
		 *
		 * @param   $customer_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_birthday_field_admin( $customer_id ) {

			if ( isset( $_POST['yith_birthday'] ) && $_POST['yith_birthday'] != '' ) {

				$date_format = get_option( 'ywces_date_format' );

				if ( preg_match( "/{$this->_date_patterns[$date_format]}/", $_POST['yith_birthday'] ) ) {
					$this->save_birthdate( $customer_id );
				}

			}

		}

		/**
		 * Check if active options have a coupon assigned
		 *
		 * @param   $coupon_code
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function check_if_coupon_exists( $coupon_code ) {

			$result = false;

			$coupon    = new WC_Coupon( $coupon_code );
			$coupon_id = yit_get_prop( $coupon, 'id' );

			if ( $coupon_id ) {

				if ( $post = get_post( $coupon_id ) ) {

					$result = true;

				}

			}

			return $result;

		}

		/**
		 * Daily cron job
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywces_daily_send_mail_job() {

			if ( get_option( 'ywces_enable_last_purchase' ) == 'yes' ) {

				$users = $this->get_customers_id_by_last_purchase();

				if ( ! empty( $users ) ) {

					foreach ( $users as $customer_id ) {

						if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
							continue;
						}

						$coupon_code = $this->create_coupon( $customer_id, 'last_purchase' );

						$args = array(
							'days_ago' => get_option( 'ywces_days_last_purchase' )
						);

						$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'last_purchase', $coupon_code, $args );

						if ( ! $email_result ) {
							$this->write_log( array(
								                  'coupon_code' => $coupon_code,
								                  'type'        => 'last_purchase'
							                  ) );
						} else {
							//Set the user to not receive another coupon until he does a new purchase
							update_user_meta( $customer_id, '_last_purchase_coupon_sent', 'yes' );
						}

					}

				}

			}

			if ( get_option( 'ywces_enable_birthday' ) == 'yes' ) {

				$users = $this->get_customers_id_by_birthdate();

				if ( ! empty( $users ) ) {

					foreach ( $users as $customer_id ) {

						if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
							continue;
						}

						$last_coupon  = get_user_meta( $customer_id, '_birthday_coupon_sent', true );
						$coupon_date  = new DateTime( $last_coupon );
						$current_date = new DateTime();

						if ( $last_coupon != '' && ( $coupon_date->format( 'Y' ) >= $current_date->format( 'Y' ) ) ) {
							continue;
						}

						$coupon_code  = $this->create_coupon( $customer_id, 'birthday' );
						$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'birthday', $coupon_code );

						if ( ! $email_result ) {
							$this->write_log( array(
								                  'coupon_code' => $coupon_code,
								                  'type'        => 'birthday'
							                  ) );
						} else {
							//Set the user to not receive another coupon until he does a new purchase
							update_user_meta( $customer_id, '_birthday_coupon_sent', date( 'Y-m-d' ) );
						}
					}

				}

			}

		}

		/**
		 * Get a list of id of customers by last purchase who need to receive the coupon
		 *
		 * @param   $vendor_id
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_customers_id_by_last_purchase( $vendor_id = '' ) {

			$days = get_option( 'ywces_days_last_purchase' . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id ) ) );

			$date = date( 'Y-m-d', strtotime( '-' . $days . ' days' ) );

			$statuses = apply_filters( 'ywces_days_last_purchase_statuses', array( 'wc-completed' ) );

			$before_ids = array();
			$args       = array(
				'post_type'      => 'shop_order',
				'post_status'    => $statuses,
				'post_parent'    => 0,
				'posts_per_page' => - 1,
				'date_query'     => array(
					array(
						'before' => $date
					)
				)
			);
			$query      = new WP_Query( $args );

			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {

					$query->the_post();

					if ( in_array( $query->post->post_status, $statuses ) ) {

						$before_ids[] = yit_get_prop( $query->post, '_customer_user' );

					}

				}

			}

			wp_reset_query();
			wp_reset_postdata();
			$before_ids = array_unique( $before_ids );

			$after_ids = array();
			$args      = array(
				'post_type'      => 'shop_order',
				'post_status'    => 'any',
				'post_parent'    => 0,
				'posts_per_page' => - 1,
				'date_query'     => array(
					array(
						'after' => $date
					)
				)
			);
			$query     = new WP_Query( $args );

			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {

					$query->the_post();

					if ( in_array( $query->post->post_status, $statuses ) ) {

						$after_ids[] = yit_get_prop( $query->post, '_customer_user' );

					}

				}

			}

			wp_reset_query();
			wp_reset_postdata();

			$after_ids     = array_unique( $after_ids );
			$filtered_ids  = array_diff( $before_ids, $after_ids );
			$customers_ids = array();

			if ( ! empty( $filtered_ids ) ) {

				$user_args = array(
					'include'    => $filtered_ids,
					'meta_query' => array(
						'relation' => 'OR',
						array(
							'key'     => '_last_purchase_coupon_sent' . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id, true ) ),
							'value'   => 'no',
							'compare' => '='
						),
						array(
							'key'     => '_last_purchase_coupon_sent' . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id, true ) ),
							'compare' => 'NOT EXISTS'
						)
					)
				);

				$user_query = new WP_User_Query( $user_args );

				if ( ! empty( $user_query->get_results() ) ) {

					foreach ( $user_query->get_results() as $user ) {

						$customers_ids[] = $user->ID;

					}

				}

			}

			return $customers_ids;


		}

		/**
		 * Get a list of id of customers by birthdate who need to receive the coupon
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_customers_id_by_birthdate() {
			global $wpdb;

			$customers_ids = array();
			$days_before   = apply_filters( 'ywces_days_before_birthday', 0 );
			$user_query    = $wpdb->get_results( "SELECT user_id FROM {$wpdb->base_prefix}usermeta WHERE meta_key = 'yith_birthday' AND MONTH(meta_value) = MONTH(NOW()) AND DAY(meta_value) = DAY(NOW()) + {$days_before}" );

			if ( ! empty( $user_query ) ) {

				foreach ( $user_query as $user ) {

					$customers_ids[] = $user->user_id;

				}

			}

			return $customers_ids;

		}

		/**
		 * Write send log
		 *
		 * @param   $args
		 *
		 * @return  void
		 * @since   1.3.1
		 *
		 * @author  Alberto Ruggiero
		 */
		public function write_log( $args ) {

			$log = 'ERROR - Coupon not sent. Code: ' . $args['coupon_code'] . ' - Type: ' . $args['type'];

			$this->_logger->add( 'ywces-' . current_time( 'Y-m' ), $log );

		}

		/**
		 * Trigger coupons on user purchase
		 *
		 * @param   $order_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywces_user_purchase( $order_id ) {

			if ( wp_get_post_parent_id( $order_id ) ) {
				return;
			}

			$order       = wc_get_order( $order_id );
			$customer_id = yit_get_prop( $order, '_customer_user' );

			if ( $customer_id == 0 ) {
				return;
			}

			if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
				return;
			}

			//Set the user to receive again a coupon after XX days from his last purchase
			update_user_meta( $customer_id, '_last_purchase_coupon_sent', 'no' );

			$order_count   = ywces_order_count( $customer_id );
			$order_date    = date( 'Y-m-d', yit_datetime_to_timestamp( yit_get_prop( $order, 'date_created' ) ) );
			$billing_email = yit_get_prop( $order, 'billing_email' );

			if ( count( $this->_available_coupons ) > 0 ) {

				//Check if is user first purchase
				if ( get_option( 'ywces_enable_first_purchase' ) == 'yes' ) {

					if ( $order_count == 1 ) {

						$coupon_code = get_option( 'ywces_coupon_first_purchase' );

						if ( $this->check_if_coupon_exists( $coupon_code ) ) {

							$args = array(
								'order_date' => $order_date,
							);

							$this->bind_coupon( $coupon_code, $billing_email );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'first_purchase', $coupon_code, $args );

							if ( ! $email_result ) {
								$this->write_log( array(
									                  'coupon_code' => $coupon_code,
									                  'type'        => 'first_purchase'
								                  ) );
							}

						}

						return;

					}

				}

				//check if uses has reached an order threshold
				if ( get_option( 'ywces_enable_purchases' ) == 'yes' ) {

					$purchase_threshold = $this->check_threshold( $order_count, 'purchases', $customer_id );

					if ( ! empty( $purchase_threshold ) ) {

						$coupon_code = $purchase_threshold['coupon_id'];

						if ( $this->check_if_coupon_exists( $coupon_code ) ) {

							$args = array(
								'order_date' => $order_date,
								'threshold'  => $purchase_threshold['threshold'],
							);

							$this->bind_coupon( $coupon_code, $billing_email );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'purchases', $coupon_code, $args );

							if ( ! $email_result ) {
								$this->write_log( array(
									                  'coupon_code' => $coupon_code,
									                  'type'        => 'purchases'
								                  ) );
							}

						}

						return;

					}

				}

				//$money_spent = get_user_meta( $customer_id, '_money_spent', true );
				$money_spent = ywces_total_spent( $customer_id );

				//check if uses has reached a spending threshold
				if ( get_option( 'ywces_enable_spending' ) == 'yes' ) {

					$spending_threshold = $this->check_threshold( $money_spent, 'spending', $customer_id );

					if ( ! empty( $spending_threshold ) ) {

						$coupon_code = $spending_threshold['coupon_id'];

						if ( $this->check_if_coupon_exists( $coupon_code ) ) {

							$args = array(
								'order_date' => $order_date,
								'threshold'  => $spending_threshold['threshold'],
								'expense'    => $money_spent,
							);

							$this->bind_coupon( $coupon_code, $billing_email );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'spending', $coupon_code, $args );

							if ( ! $email_result ) {
								$this->write_log( array(
									                  'coupon_code' => $coupon_code,
									                  'type'        => 'spending'
								                  ) );
							}
						}

						return;

					}

				}

			}

			if ( get_option( 'ywces_enable_product_purchasing' ) == 'yes' && get_option( 'ywces_targets_product_purchasing' ) != '' ) {

				$is_deposits = yit_get_prop( $order, '_created_via' ) == 'yith_wcdp_balance_order';

				if ( ! $is_deposits ) {

					$target_products = get_option( 'ywces_targets_product_purchasing' );
					$target_products = is_array( $target_products ) ? $target_products : explode( ',', $target_products );
					$order_items     = $order->get_items();
					$found_product   = '';
					foreach ( $order_items as $item ) {

						$product_id = ( $item['variation_id'] != '0' ? $item['variation_id'] : $item['product_id'] );

						if ( in_array( $product_id, $target_products ) && $found_product == '' ) {

							$found_product = $product_id;
						}

					}

					if ( $found_product != '' ) {

						$coupon_code = $this->create_coupon( $customer_id, 'product_purchasing' );
						$args        = array(
							'order_date' => $order_date,
							'product'    => $found_product,
						);

						$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'product_purchasing', $coupon_code, $args );

						if ( ! $email_result ) {
							$this->write_log( array(
								                  'coupon_code' => $coupon_code,
								                  'type'        => 'product_purchasing'
							                  ) );
						}

					}

				}

			}

		}

		/**
		 * Add user email to coupon allowed emails
		 *
		 * @param   $coupon_code
		 * @param   $email
		 *
		 * @return  void
		 * @since   1.0.4
		 *
		 * @author  Alberto ruggiero
		 */
		public function bind_coupon( $coupon_code, $email ) {

			$coupon         = new WC_Coupon( $coupon_code );
			$valid_emails   = yit_get_prop( $coupon, 'customer_email' );
			$valid_emails[] = $email;

			$args = array(
				'customer_email'       => $valid_emails,
				'usage_limit_per_user' => '1'
			);

			yit_save_prop( $coupon, $args, false, true );

		}

		/**
		 * Check if a threshold is reached and returns the coupon code and the threshold
		 *
		 * @param   $amount
		 * @param   $type
		 * @param   $customer_id
		 * @param   $vendor_id
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function check_threshold( $amount, $type, $customer_id = false, $vendor_id = '' ) {

			$thresholds        = maybe_unserialize( get_option( 'ywces_thresholds_' . $type . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id ) ) ) );
			$closest_threshold = 0;
			$result            = array();

			if ( $thresholds != '' ) {

				foreach ( $thresholds as $key => $threshold ) {

					if ( $amount >= $threshold['amount'] && $closest_threshold < $threshold['amount'] ) {

						$customers = isset( $threshold['customers'] ) ? explode( ',', $threshold['customers'] ) : array();

						if ( ! empty( $customers ) && in_array( $customer_id, $customers ) ) {
							continue;
						}

						$closest_threshold = $threshold['amount'];
						$result            = array( 'coupon_id' => $threshold['coupon'], 'threshold' => $threshold['amount'] );

						if ( $customer_id ) {

							$customers[]                     = $customer_id;
							$thresholds[ $key ]['customers'] = implode( ',', $customers );
							update_option( 'ywces_thresholds_' . $type . ( apply_filters( 'ywces_set_vendor_id', '', $vendor_id ) ), $thresholds );

						}

					}

				}

			}

			return $result;

		}

		/**
		 * Creates a coupon with specific settings
		 *
		 * @param   $user_id
		 * @param   $type
		 * @param   $coupon_args
		 * @param   $vendor_id
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function create_coupon( $user_id, $type, $coupon_args = array(), $vendor_id = '' ) {

			$user_nickname      = get_user_meta( $user_id, 'nickname', true );
			$user_email         = get_user_meta( $user_id, 'billing_email', true );
			$coupon_first_part  = apply_filters( 'ywces_coupon_code_first_part', $user_nickname );
			$coupon_separator   = apply_filters( 'ywces_coupon_code_separator', '-' );
			$coupon_second_part = apply_filters( 'ywces_coupon_code_second_part', current_time( 'YmdHis' ) );

			$coupon_code = $coupon_first_part . $coupon_separator . $coupon_second_part . $vendor_id;

			$coupon_desc = '';

			switch ( $type ) {

				case 'last_purchase':
					$coupon_desc = esc_html__( 'On a specific number of days from the last purchase', 'yith-woocommerce-coupon-email-system' );
					break;

				case 'birthday':
					$coupon_desc = esc_html__( 'On customer birthday', 'yith-woocommerce-coupon-email-system' );
					break;

				case 'product_purchasing':
					$coupon_desc = esc_html__( 'On specific product purchase', 'yith-woocommerce-coupon-email-system' );
					break;

			}

			$coupon_data = array(
				'post_title'   => $coupon_code,
				'post_content' => '',
				'post_excerpt' => $coupon_desc,
				'post_status'  => 'publish',
				'post_author'  => apply_filters( 'ywces_set_coupon_author', 0, $vendor_id ),
				'post_type'    => 'shop_coupon'
			);

			$coupon_id = wp_insert_post( $coupon_data );

			if ( empty( $coupon_args ) ) {

				$option_suffix = '';

				if ( $vendor_id != '' ) {

					$option_suffix = '_' . $vendor_id;

				}

				$coupon_option = get_option( 'ywces_coupon_' . $type . $option_suffix );

			} else {

				$coupon_option = $coupon_args;

			}

			$ve          = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
			$expiry_date = ( $coupon_option['expiry_days'] != '' ) ? date( 'Y-m-d', strtotime( '+' . $coupon_option['expiry_days'] . ' days' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ) ) : '';

			update_post_meta( $coupon_id, 'discount_type', $coupon_option['discount_type'] );
			update_post_meta( $coupon_id, 'coupon_amount', $coupon_option['coupon_amount'] );
			update_post_meta( $coupon_id, 'individual_use', ( isset( $coupon_option['individual_use'] ) && $coupon_option['individual_use'] != '' ? 'yes' : 'no' ) );
			update_post_meta( $coupon_id, 'usage_limit', '1' );
			update_post_meta( $coupon_id, 'date_expires', $expiry_date );
			update_post_meta( $coupon_id, 'customer_email', $user_email );
			update_post_meta( $coupon_id, 'minimum_amount', $coupon_option['minimum_amount'] );
			update_post_meta( $coupon_id, 'maximum_amount', $coupon_option['maximum_amount'] );
			update_post_meta( $coupon_id, 'free_shipping', ( isset( $coupon_option['free_shipping'] ) && $coupon_option['free_shipping'] != '' ? 'yes' : 'no' ) );
			update_post_meta( $coupon_id, 'exclude_sale_items', ( isset( $coupon_option['exclude_sale_items'] ) && $coupon_option['exclude_sale_items'] != '' ? 'yes' : 'no' ) );

			do_action( 'ywces_additional_coupon_features', $coupon_id, $type, $coupon_option );


			if ( $vendor_id != '' ) {
				$vendor      = yith_get_vendor( $vendor_id, 'vendor' );
				$product_ids = implode( ',', $vendor->get_products() );

				update_post_meta( $coupon_id, 'vendor_id', $vendor_id );
				update_post_meta( $coupon_id, 'product_ids', $product_ids );
			}

			update_post_meta( $coupon_id, 'generated_by', 'ywces' );

			return $coupon_code;

		}

		/**
		 * Set custom where condition
		 *
		 * @param   $where
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywces_filter_where_orders( $where = '' ) {

			$days = get_option( 'ywces_days_last_purchase' );

			$where .= " AND post_date <= '" . date( 'Y-m-d', strtotime( '-' . $days . ' days' ) ) . "'";

			return $where;

		}

		/**
		 * Trash expired coupons
		 *
		 * @param   $return
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function trash_expired_coupons( $return = false ) {

			$args = array(
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'   => 'generated_by',
						'value' => 'ywces',
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => 'date_expires',
							'value'   => date( 'Y-m-d', strtotime( "today" ) ),
							'compare' => '<',
							'type'    => 'DATE'
						),
						array(
							'key'     => 'usage_count',
							'value'   => 1,
							'compare' => '>='
						)
					)
				)
			);

			$query = new WP_Query( $args );
			$count = $query->post_count;

			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {

					$query->the_post();

					wp_trash_post( $query->post->ID );

				}

			}

			wp_reset_query();
			wp_reset_postdata();

			if ( $return ) {

				return $count;

			}

			return null;

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Get Date placeholders
		 *
		 * @return  array
		 * @since   1.0.6
		 * @author  Alberto Ruggiero
		 */
		public function get_date_placeholders() {

			return apply_filters( 'ywces_date_placeholders', array(
				'yy-mm-dd' => esc_html__( 'YYYY-MM-DD', 'yith-woocommerce-coupon-email-system' ),
				'yy/mm/dd' => esc_html__( 'YYYY/MM/DD', 'yith-woocommerce-coupon-email-system' ),
				'mm-dd-yy' => esc_html__( 'MM-DD-YYYY', 'yith-woocommerce-coupon-email-system' ),
				'mm/dd/yy' => esc_html__( 'MM/DD/YYYY', 'yith-woocommerce-coupon-email-system' ),
				'dd-mm-yy' => esc_html__( 'DD-MM-YYYY', 'yith-woocommerce-coupon-email-system' ),
				'dd/mm/yy' => esc_html__( 'DD/MM/YYYY', 'yith-woocommerce-coupon-email-system' ),
			) );

		}

		/**
		 * Get Date formats
		 *
		 * @return  array
		 * @since   1.0.6
		 * @author  Alberto Ruggiero
		 */
		public function get_date_formats() {

			return apply_filters( 'ywces_date_formats', array(
				'yy-mm-dd' => 'Y-m-d',
				'yy/mm/dd' => 'Y/m/d',
				'mm-dd-yy' => 'm-d-Y',
				'mm/dd/yy' => 'm/d/Y',
				'dd-mm-yy' => 'd-m-Y',
				'dd/mm/yy' => 'd/m/Y',
			) );

		}

		/**
		 * Get Date patterns
		 *
		 * @return  array
		 * @since   1.0.6
		 * @author  Alberto Ruggiero
		 */
		public function get_date_patterns() {

			return apply_filters( 'ywces_date_patterns', array(
				'yy-mm-dd' => '([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
				'yy/mm/dd' => '([0-9]{4})\/(0[1-9]|1[012])\/(0[1-9]|1[0-9]|2[0-9]|3[01])',
				'mm-dd-yy' => '(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])-([0-9]{4})',
				'mm/dd/yy' => '(0[1-9]|1[012])\/(0[1-9]|1[0-9]|2[0-9]|3[01])\/([0-9]{4})',
				'dd-mm-yy' => '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})',
				'dd/mm/yy' => '(0[1-9]|1[0-9]|2[0-9]|3[01])\/(0[1-9]|1[012])\/([0-9]{4})',
			) );

		}

		/**
		 * Add customer birth date field to checkout process
		 *
		 * @param   $fields
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_birthday_field_checkout( $fields ) {

			$date_format = get_option( 'ywces_date_format' );

			if ( is_user_logged_in() ) {

				$user = get_user_by( 'id', get_current_user_id() );

				if ( $user->yith_birthday ) {
					$section = '';

				} else {

					$section = 'billing';

				}


			} else {

				$section = 'account';

			}

			if ( $section != '' ) {


				$required = apply_filters( 'ywces_required_birthday', '' );

				$fields[ $section ]['yith_birthday'] = array(
					'label'             => apply_filters( 'ywces_birthday_label', esc_html__( 'Birth date', 'yith-woocommerce-coupon-email-system' ), $this ),
					'custom_attributes' => array(
						'pattern'   => $this->_date_patterns[ $date_format ],
						'maxlength' => 10,

					),
					'required'          => ( $required === 'required' || $required === true ) ? true : false,
					'placeholder'       => $this->_date_placeholders[ $date_format ],
					'input_class'       => array( 'ywces-birthday' ),
					'class'             => array( 'form-row-wide' ),
					'priority'          => 999

				);

			}

			return $fields;

		}

		/**
		 * Add customer birth date field to edit account page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_birthday_field() {

			$user        = get_user_by( 'id', get_current_user_id() );
			$date_format = get_option( 'ywces_date_format' );
			$birth_date  = '';

			if ( ! empty ( $user ) && $user->yith_birthday ) {

				$date       = DateTime::createFromFormat( 'Y-m-d', esc_attr( $user->yith_birthday ) );
				$birth_date = $date->format( $this->_date_formats[ $date_format ] );

			}

			$enabled = ( $birth_date == '' ) ? '' : 'disabled';

			?>

			<p class="form-row form-row-wide">
				<label for="yith_birthday">
					<?php echo apply_filters( 'ywces_birthday_label', esc_html__( 'Birth date', 'yith-woocommerce-coupon-email-system' ), $this ); ?><?php echo ( apply_filters( 'ywces_required_birthday', '' ) == 'required' ) ? ' <abbr class="required" title="required">*</abbr>' : '' ?>
				</label>
				<input
					type="text"
					class="input-text"
					name="yith_birthday"
					maxlength="10"
					placeholder="<?php echo $this->_date_placeholders[ $date_format ]; ?>"
					pattern="<?php echo $this->_date_patterns[ $date_format ] ?>"
					value="<?php echo $birth_date; ?>"
					<?php echo apply_filters( 'ywces_required_birthday', '' ); ?>
					<?php echo $enabled; ?>
				/>

			</p>

			<?php

		}

		/**
		 * Save customer birth date from edit account page
		 *
		 * @param   $customer_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_birthday_field( $customer_id ) {

			$this->save_birthdate( $customer_id );

		}

		/**
		 * Save customer birth date from checkout process
		 *
		 * @param   $customer_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_birthday_field_checkout( $customer_id ) {

			$this->save_birthdate( $customer_id );

		}

		/**
		 * Save customer birth date
		 *
		 * @param   $customer_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_birthdate( $customer_id ) {

			if ( isset( $_POST['yith_birthday'] ) && $_POST['yith_birthday'] != '' ) {

				$date_format = get_option( 'ywces_date_format' );

				if ( preg_match( "/{$this->_date_patterns[$date_format]}/", $_POST['yith_birthday'] ) ) {

					$date       = DateTime::createFromFormat( $this->_date_formats[ $date_format ], sanitize_text_field( $_POST['yith_birthday'] ) );
					$birth_date = $date->format( 'Y-m-d' );

					update_user_meta( $customer_id, 'yith_birthday', $birth_date );

				}

			}

		}

		/**
		 * Show coupon checkbox in checkout page
		 *
		 * @param   $fields
		 *
		 * @return  array
		 * @since   1.3.1
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_accept_coupon_field_checkout( $fields ) {

			if ( is_user_logged_in() ) {
				$section = 'billing';
			} else {
				$section = 'account';
			}

			$fields[ $section ]['ywces_receive_coupons'] = array(
				'label'    => apply_filters( 'ywces_checkout_option_label', esc_html__( 'I accept to receive coupons via email', 'yith-woocommerce-coupon-email-system' ) ),
				'type'     => 'checkbox',
				'class'    => array( 'form-row-wide' ),
				'priority' => 998
			);

			return $fields;
		}

		/**
		 * Save coupon checkbox in checkout page
		 *
		 * @param   $customer_id
		 *
		 * @return  void
		 * @since   1.3.1
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_accept_coupon_field_checkout( $customer_id ) {

			if ( isset( $_POST['ywces_receive_coupons'] ) ) {

				update_user_meta( $customer_id, 'ywces_receive_coupons', 'yes' );

			}

		}

		/**
		 * Add customer request option to edit account page
		 *
		 * @return  void
		 * @since   1.2.6
		 * @author  Alberto Ruggiero
		 */
		public function add_accept_coupon_field_my_account() {

			$label   = apply_filters( 'ywces_checkout_option_label', esc_html__( 'I accept to receive coupons via email', 'yith-woocommerce-coupon-email-system' ) );
			$accepts = get_user_meta( get_current_user_id(), 'ywces_receive_coupons', true ) == 'yes';
			?>

			<p class="form-row form-row-wide">

				<label for="ywces_receive_coupons">
					<input
						name="ywces_receive_coupons"
						type="checkbox"
						class=""
						value="1"
						<?php checked( $accepts ); ?>
					/> <?php echo $label ?>
				</label>

			</p>

			<?php

		}

		/**
		 * Save customer request option from edit account page
		 *
		 * @param   $customer_id
		 *
		 * @return  void
		 * @since   1.2.6
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_accept_coupon_field_my_account( $customer_id ) {

			if ( isset( $_POST['ywces_receive_coupons'] ) ) {
				update_user_meta( $customer_id, 'ywces_receive_coupons', 'yes' );
			} else {
				update_user_meta( $customer_id, 'ywces_receive_coupons', 'no' );
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
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
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
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCES_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YWCES_SLUG;
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
			YIT_Plugin_Licence()->register( YWCES_INIT, YWCES_SECRET_KEY, YWCES_SLUG );
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
			YIT_Upgrade()->register( YWCES_SLUG, YWCES_INIT );
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


