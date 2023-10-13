<?php
/**
 * Init extended admin features of the plugin
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Admin_Extended' ) ) {
	/**
	 * WooCommerce Wishlist admin Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Admin_Extended extends YITH_WCWL_Admin {

		/**
		 * Various links
		 *
		 * @var string
		 * @access public
		 * @since 1.0.0
		 */
		public $showcase_images = array();

		/**
		 * Constructor of the class
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			parent::__construct();

			// add premium settings.
			add_filter( 'yith_wcwl_wishlist_page_options', array( $this, 'add_wishlist_options' ) );

			// email settings.
			add_action( 'yith_wcwl_email_settings', array( $this, 'email_settings' ) );
			add_action( 'yith_wcwl_print_email_settings', array( $this, 'print_email_settings' ) );

			add_action( 'wp_ajax_yith_wcwl_save_email_settings', array( $this, 'save_email_settings' ) );
			add_action( 'wp_ajax_nopriv_yith_wcwl_save_email_settings', array( $this, 'save_email_settings' ) );

			add_action( 'wp_ajax_yith_wcwl_save_mail_status', array( $this, 'save_mail_status' ) );
			add_action( 'wp_ajax_nopriv_yith_wcwl_save_mail_status', array( $this, 'save_mail_status' ) );
		}

		/* === INITIALIZATION SECTION === */

		/**
		 * Initiator method. Initiate properties.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		public function init() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
			parent::init();
		}

		/**
		 * Retrieve the admin panel tabs.
		 *
		 * @return array
		 */
		protected function get_admin_panel_tabs(): array {
			return apply_filters(
				'yith_wcwl_admin_panel_tabs',
				array(
					'dashboard' => array(
						'title'       => _x( 'Popular', 'Settings tab name', 'yith-woocommerce-wishlist' ),
						'description' => _x( 'Check the most popular products in your site.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
						'icon'        => 'dashboard',
					),
					'settings'  => array(
						'title' => _x( 'Settings', 'Settings tab name', 'yith-woocommerce-wishlist' ),
						'icon'  => 'settings',
					),
					'email'     => array(
						'title' => __( 'Email Settings', 'yith-woocommerce-wishlist' ),
						'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>',
					),
				)
			);
		}

		/**
		 * Add new options to wishlist settings tab
		 *
		 * @param array $options Array of available options.
		 * @return array Filtered array of options
		 */
		public function add_wishlist_options( $options ) {
			$settings = $options['settings-wishlist_page'];

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'show_quantity' => array(
						'name'          => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Product quantity (so users can manage the quantity of each product from the wishlist)', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_quantity_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'wishlist_info',
					),
				),
				'show_unit_price'
			);

			$options['settings-wishlist_page'] = $settings;

			return $options;
		}

		/**
		 * Handle email settings tab
		 * This method based on query string load single email options or the general table
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function email_settings() {

			$emails = YITH_WCWL()->emails;
			// is a single email view?
			$active = '';
			if ( isset( $_GET['section'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				foreach ( $emails as $email ) {
					if ( strtolower( $email ) === sanitize_text_field( wp_unslash( $_GET['section'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$active = $email;
						break;
					}
				}
			}

			// load mailer.
			$mailer = WC()->mailer();

			$emails_table = array();
			foreach ( $emails as $email ) {

				$email_class            = $mailer->emails[ $email ];
				$emails_table[ $email ] = array(
					'title'       => $email_class->get_title(),
					'description' => $email_class->get_description(),
					'recipient'   => $email_class->is_customer_email() ? __( 'Customer', 'yith-woocommerce-gift-cards' ) : $email_class->get_recipient(),
					'enable'      => $email_class->is_enabled(),
					'content'     => $email_class->get_content_type(),
				);
			}

			include_once YITH_WCWL_DIR . '/templates/admin/email-settings-tab.php';
		}

		/**
		 * Outout emal settings section
		 *
		 * @param string $email_key Email ID.
		 *
		 * @return void
		 */
		public function print_email_settings( $email_key ) {
			global $current_section;
			$current_section = strtolower( $email_key );
			$mailer          = WC()->mailer();
			$class           = $mailer->emails[ $email_key ];
			WC_Admin_Settings::get_settings_pages();

			if ( ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$class->process_admin_options();
			}

			include YITH_WCWL_DIR . '/templates/admin/email-settings-single.php';

			$current_section = null;
		}

		/**
		 * Save email settings in ajax.
		 *
		 * @return void
		 */
		public function save_email_settings() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['params'] ) ) {
				parse_str( $_POST['params'], $params ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				unset( $_POST['params'] );

				foreach ( $params as $key => $value ) {
					$_POST[ $key ] = $value;
				}

				global $current_section;

				$email_key       = isset( $_POST['email_key'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['email_key'] ) ) ) : '';
				$current_section = $email_key;

				$mailer = WC()->mailer();
				$class  = $mailer->emails[ $email_key ];
				$class->process_admin_options();

				$current_section = null;

				wp_send_json_success( array( 'msg' => 'Email updated' ) );
				die();
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Save email status in ajax.
		 *
		 * @return void
		 */
		public function save_mail_status() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['email_key'], $_POST['enabled'] ) ) {
				$email_key      = sanitize_text_field( wp_unslash( $_POST['email_key'] ) );
				$email_settings = get_option( 'woocommerce_' . $email_key . '_settings' );
				if ( is_array( $email_settings ) && ! empty( $email_key ) ) {
					$email_settings['enabled'] = sanitize_text_field( wp_unslash( $_POST['enabled'] ) );
					update_option( 'woocommerce_' . $email_key . '_settings', $email_settings );
				}
			}
			die();
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/* === PANEL HANDLING === */

		/**
		 * Adds params to use in admin template files
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function print_popular_table() {
			if ( isset( $_GET['action'] ) && 'send_promotional_email' === $_GET['action'] && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'send_promotional_email' ) ) {
				$emails          = WC_Emails::instance()->get_emails();
				$promotion_email = $emails['YITH_WCWL_Promotion_Email'];

				$additional_info['current_tab'] = 'dashboard-popular';
				$additional_info['product_id']  = isset( $_REQUEST['product_id'] ) ? intval( $_REQUEST['product_id'] ) : false;

				$additional_info['promotional_email_html_content'] = $promotion_email->get_option( 'content_html' );
				$additional_info['promotional_email_text_content'] = $promotion_email->get_option( 'content_text' );

				$additional_info['coupons'] = get_posts(
					array(
						'post_type'      => 'shop_coupon',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
					)
				);

				yith_wcwl_get_template( 'admin/wishlist-panel-send-promotional-email.php', $additional_info );
			}
		}
		/**
		 * Build single email settings page
		 *
		 * @param string $email_key The email key.
		 *
		 * @return string
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function build_single_email_settings_url( $email_key ) {
			return admin_url( 'admin.php?page=yith_wcwl_panel&tab=email&section=' . strtolower( $email_key ) );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Admin_Extended class
 *
 * @return \YITH_WCWL_Admin
 * @since 2.0.0
 */
function YITH_WCWL_Admin_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Admin_Extended::get_instance();
}
