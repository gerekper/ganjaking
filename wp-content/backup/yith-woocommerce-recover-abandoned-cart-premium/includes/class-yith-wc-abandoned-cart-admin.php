<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH_WC_Recover_Abandoned_Cart_Admin
 *
 * @class   YITH_WC_Recover_Abandoned_Cart_Admin
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Recover_Abandoned_Cart_Admin' ) ) {

	class YITH_WC_Recover_Abandoned_Cart_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Dynamic_Pricing_Admin
		 */
		protected static $instance;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Panel page
		 */
		protected $_panel_page = 'yith_woocommerce_recover_abandoned_cart';



		/**
		 * @var WP_List_Table
		 */
		public $cpt_obj;

		/**
		 * @var WP_List_Table
		 */
		public $cpt_obj_pending_orders;

		/**
		 * @var WP_List_Table
		 */
		public $cpt_obj_emails;

		/**
		 * @var bool
		 */
		public $pending_orders = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Recover_Abandoned_Cart_Admin
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
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			$this->check_version();

			if ( get_option( 'ywrac_pending_orders_enabled' ) == 'yes' ) {
				$this->pending_orders = true;
				add_action(
					'update_option_ywrac_pending_orders_delete',
					array(
						$this,
						'update_woocommerce_hold_stock_minutes_option',
					)
				);
				add_action( 'woocommerce_order_status_completed', array( $this, 'register_order_pending_conversion' ) );
				add_action( 'woocommerce_order_status_pending', array( $this, 'pending_order_counter' ) );
			}

			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 15 );

			// custom styles and javascripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );

			$this->create_menu_items();

			// if the page is the editor of abandoned cart post type add the metabox
			if ( ywrac_check_valid_admin_page( YITH_WC_Recover_Abandoned_Cart()->post_type_name ) ) {
				YITH_WC_RAC_Metaboxes();
			}

			add_action( 'plugins_loaded', array( $this, 'load_privacy_dpa' ), 20 );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWRAC_DIR . '/' . basename( YITH_YWRAC_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// reset reports action
            add_action( 'wp_ajax_ywrac_reset_reports', array( $this, 'ajax_ywrac_reset_reports' ) );
            add_action( 'wp_ajax_nopriv_ywrac_reset_reports', array( $this, 'ajax_ywrac_reset_reports' ) );

		}

		/**
		 * Load the class
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function load_privacy_dpa() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once YITH_YWRAC_INC . 'class.yith-wc-abandoned-cart-privacy-dpa.php';
			}
		}


		/**
		 * Check the version of plugin
		 *
		 * @since  1.1.0
		 * @return void
		 * @author Emanuela Castorina
		 */
		private function check_version() {
			$current_option_version = get_option( 'yit_ywrac_version', '0' );
			$forced                 = isset( $_GET['update_ywrac_options'] ) && $_GET['update_ywrac_options'] == 'forced';

			if ( version_compare( $current_option_version, YITH_YWRAC_VERSION, '>=' ) && ! $forced ) {
				return;
			}

			// In the version 1.1.0 pending order statistics are added
			if ( version_compare( YITH_YWRAC_VERSION, '1.1.0', '>=' ) ) {
				// email sent
				if ( ! get_option( 'ywrac_email_sent_cart_counter' ) && get_option( 'ywrac_email_sent_counter' ) != '' ) {
					add_option( 'ywrac_email_sent_cart_counter', get_option( 'ywrac_email_sent_counter' ) );
				}
				// click on email
				if ( ! get_option( 'ywrac_email_cart_clicks_counter' ) && get_option( 'ywrac_email_clicks_counter' ) != '' ) {
					add_option( 'ywrac_email_cart_clicks_counter', get_option( 'ywrac_email_clicks_counter' ) );
				}
				// recovered carts
				if ( ! get_option( 'ywrac_total_recovered_carts' ) && get_option( 'ywrac_recovered_carts' ) != '' ) {
					add_option( 'ywrac_total_recovered_carts', get_option( 'ywrac_recovered_carts' ) );
				}
				// total amounts
				if ( ! get_option( 'ywrac_total_cart_amount' ) && get_option( 'ywrac_total_amount' ) != '' ) {
					add_option( 'ywrac_total_cart_amount', get_option( 'ywrac_total_amount' ) );
				}
			}

			update_option( 'yit_ywrac_version', YITH_YWRAC_VERSION );
		}


		/**
		 * Load YIT Plugin Framework
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Init function check if the plugin is enabled
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina
		 */
		public function init() {
			if ( get_option( 'ywrac_enabled' ) != 'yes' ) {
				return;
			}
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {
			wp_enqueue_style( 'yith_ywrac_backend', YITH_YWRAC_ASSETS_URL . '/css/backend.css', YITH_YWRAC_VERSION );
			wp_enqueue_script( 'yith_ywrac_admin', YITH_YWRAC_ASSETS_URL . '/js/ywrac-admin' . YITH_YWRAC_SUFFIX . '.js', array( 'jquery' ), YITH_YWRAC_VERSION, true );
			if ( ! wp_script_is( 'selectWoo' ) ) {
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );
			}
			wp_localize_script(
				'yith_ywrac_admin',
				'yith_ywrac_admin',
				array(
					'ajaxurl'               => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'send_email_nonce'      => wp_create_nonce( 'send-email' ),
					'sent_label'            => __( 'Email ', 'yith-woocommerce-recover-abandoned-cart' ),
					'sent_label_test'       => __( 'Email Sent!', 'yith-woocommerce-recover-abandoned-cart' ),
					'grab_email'            => get_option( 'ywrac_user_guest_enabled' ),
					'block_loader'          => YITH_YWRAC_ASSETS_URL . '/images/ajax-loader.gif',
                    'reset_confirmation'    => esc_html__( 'Are you sure you want to reset the reports?', 'yith-woocommerce-recover-abandoned-cart' ),
				)
			);

		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */
		private function create_menu_items() {

			// Add a panel under YITH Plugins tab
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_ywrac_carts', array( $this, 'carts_tab' ) );

			if ( $this->pending_orders ) {
				add_action( 'yith_ywrac_pending_orders', array( $this, 'pending_orders_tab' ) );
			}
			add_action( 'yith_ywrac_emails', array( $this, 'emails_tab' ) );
			add_action( 'yith_ywrac_recovered', array( $this, 'recovered_tab' ) );
			add_action( 'yith_ywrac_mailslog', array( $this, 'mailslog_tab' ) );
			add_action( 'yith_ywrac_reports', array( $this, 'reports_tab' ) );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'reports'        => __( 'Dashboard', 'yith-woocommerce-recover-abandoned-cart' ),
				'general'        => __( 'Settings', 'yith-woocommerce-recover-abandoned-cart' ),
				'carts'          => __( 'Carts', 'yith-woocommerce-recover-abandoned-cart' ),
				'pending_orders' => __( 'Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ),
				'email'          => __( 'Email Templates', 'yith-woocommerce-recover-abandoned-cart' ),
				'coupon'         => __( 'Coupons', 'yith-woocommerce-recover-abandoned-cart' ),
				'recovered'      => __( 'Recovered Carts', 'yith-woocommerce-recover-abandoned-cart' ),
				'mailslog'       => __( 'Email Logs', 'yith-woocommerce-recover-abandoned-cart' ),

			);

			if ( ! $this->pending_orders ) {
				unset( $admin_tabs['pending_orders'] );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Abandoned Cart',
				'menu_title'       => 'Abandoned Cart',
				'capability'       => 'manage_options',
				'parent'           => 'yith-woocommerce-recover-abandoned-cart',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWRAC_DIR . '/plugin-options',
			);

			// enable shop manager to set Dynamic Pricing Options
			if ( get_option( 'ywrac_enable_shop_manager' ) == 'yes' ) {
				add_filter( 'option_page_capability_yit_' . $args['parent'] . '_options', array( $this, 'change_capability' ) );
				add_filter( 'yit_plugin_panel_menu_page_capability', array( $this, 'change_capability' ) );
				$args['capability'] = 'manage_woocommerce';
			}

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_YWRAC_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			// Custom tinymce button
			add_action( 'admin_head', array( $this, 'tc_button' ) );

		}

		/**
		 * Modify the capability
		 *
		 * @param $capability
		 *
		 * @return string
		 */
		function change_capability( $capability ) {
			return 'manage_woocommerce';
		}

		/**
		 * Add a new button to tinymce
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function tc_button() {

			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			$post_type = '';

			if ( isset( $_GET['post'] ) ) {
				$post_type = get_post_type( $_GET['post'] );
			} elseif ( isset( $_GET['post_type'] ) ) {
				$post_type = $_GET['post_type'];
			}

			if ( $post_type != YITH_WC_Recover_Abandoned_Cart_Email()->post_type_name ) {
				return;
			}

			if ( get_user_option( 'rich_editing' ) == 'true' ) {
				add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
				add_filter( 'mce_buttons', array( $this, 'register_tc_button' ) );
				add_filter( 'mce_external_languages', array( $this, 'add_tc_button_lang' ) );
			}
		}

		/**
		 * Add plugin button to tinymce from filter mce_external_plugins
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function add_tinymce_plugin( $plugin_array ) {
			$plugin_array['tc_button'] = YITH_YWRAC_ASSETS_URL . '/js/tinymce/text-editor.js';
			return $plugin_array;
		}

		/**
		 * Register the custom button to tinymce from filter mce_buttons
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function register_tc_button( $buttons ) {
			array_push( $buttons, 'tc_button' );
			return $buttons;
		}

		/**
		 * Add multilingual to mce button from filter mce_external_languages
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function add_tc_button_lang( $locales ) {
			$locales ['tc_button'] = YITH_YWRAC_INC . 'admin/tinymce/tinymce-plugin-langs.php';
			return $locales;
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWRAC_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Carts Template
		 *
		 * Load the abandoned cart template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function carts_tab() {
			$this->cpt_obj = new YITH_YWRAC_Carts_List_Table();
			// YITH_WC_Recover_Abandoned_Cart()->update_carts();
			$carts_tab = YITH_YWRAC_TEMPLATE_PATH . '/admin/carts-tab.php';
			if ( file_exists( $carts_tab ) ) {
				include_once $carts_tab;
			}
		}

		/**
		 * Carts Template
		 *
		 * Load the abandoned cart template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function pending_orders_tab() {
			$this->cpt_obj_pending_orders = new YITH_YWRAC_Pending_Orders_List_Table();

			$pending_orders_tab = YITH_YWRAC_TEMPLATE_PATH . '/admin/pending_orders-tab.php';
			if ( file_exists( $pending_orders_tab ) ) {
				include_once $pending_orders_tab;
			}
		}

		/**
		 * Email Templates
		 *
		 * Load the email templates on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function emails_tab() {
			$this->cpt_obj_emails = new YITH_YWRAC_Emails_List_Table();

			$emails_tab = YITH_YWRAC_TEMPLATE_PATH . '/admin/emails-tab.php';
			if ( file_exists( $emails_tab ) ) {
				include_once $emails_tab;
			}
		}

		/**
		 * Email Log Templates
		 *
		 * Load the email logs templates on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function mailslog_tab() {
			$this->cpt_obj_mailslog = new YITH_YWRAC_Email_Log_List_Table();

			$mailslog_tab = YITH_YWRAC_TEMPLATE_PATH . '/admin/mailslog-tab.php';
			if ( file_exists( $mailslog_tab ) ) {
				include_once $mailslog_tab;
			}
		}

		/**
		 * Recovered Orders
		 *
		 * Load the order completed with recover cart email
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function recovered_tab() {
			$this->cpt_obj_orders = new YITH_YWRAC_Recovered_List_Table();

			$recovered_tab = YITH_YWRAC_TEMPLATE_PATH . '/admin/recovered-tab.php';
			if ( file_exists( $recovered_tab ) ) {
				include_once $recovered_tab;
			}

		}

		/**
		 * Reports Plugin Panel
		 *
		 * Load the stats of plugin
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function reports_tab() {

			// emails sent
			$email_sent_counter       = apply_filters( 'ywrac_email_sent_counter', get_option( 'ywrac_email_sent_counter', 0 ) );
			$email_sent_cart_counter  = apply_filters( 'ywrac_email_sent_cart_counter', get_option( 'ywrac_email_sent_cart_counter', 0 ) );
			$email_sent_order_counter = apply_filters( 'ywrac_email_sent_order_counter', get_option( 'ywrac_email_sent_order_counter', 0 ) );

			// email clicks
			$email_clicks_counter       = apply_filters( 'ywrac_email_clicks_counter', get_option( 'ywrac_email_clicks_counter', 0 ) );
			$email_cart_clicks_counter  = apply_filters( 'ywrac_email_cart_clicks_counter', get_option( 'ywrac_email_cart_clicks_counter', 0 ) );
			$email_order_clicks_counter = apply_filters( 'ywrac_email_order_clicks_counter', get_option( 'ywrac_email_order_clicks_counter', 0 ) );

			// abandoned carts and pending orders
			$abandoned_carts_counter = apply_filters( 'ywrac_abandoned_carts_counter', get_option( 'ywrac_abandoned_carts_counter', 0 ) );
			$total_pending_orders    = apply_filters( 'ywrac_total_pending_orders', get_option( 'ywrac_total_pending_orders', 0 ) );
			$total_pending_orders    = $total_pending_orders < 0 ? 0 : $total_pending_orders;
			$total_abandoned_carts   = apply_filters( 'ywrac_total_abandoned_carts', get_option( 'ywrac_total_abandoned_carts', 0 ) );

			// recovered carts
			$recovered_carts                = apply_filters( 'ywrac_recovered_carts', get_option( 'ywrac_recovered_carts', 0 ) );
			$total_recovered_pending_orders = apply_filters( 'ywrac_total_recovered_pending_orders', get_option( 'ywrac_total_recovered_pending_orders', 0 ) );
			$total_recovered_pending_orders = $total_recovered_pending_orders < 0 ? 0 : $total_recovered_pending_orders;
			$total_recovered_carts          = apply_filters( 'ywrac_total_recovered_carts', get_option( 'ywrac_total_recovered_carts', 0 ) );

			// rate conversion email sent/number of recovered items
			$rate_conversion       = $email_sent_counter ? apply_filters( 'ywrac_rate_conversion', number_format( 100 * $recovered_carts / $email_sent_counter, 2, '.', '' ) ) : 0;
			$rate_cart_conversion  = $email_sent_cart_counter ? apply_filters( 'ywrac_rate_cart_conversion', number_format( 100 * $total_recovered_carts / $email_sent_cart_counter, 2, '.', '' ) ) : 0;
			$rate_order_conversion = $email_sent_order_counter ? apply_filters( 'ywrac_rate_order_conversion', number_format( 100 * $total_recovered_pending_orders / $email_sent_order_counter, 2, '.', '' ) ) : 0;

			if ( $email_sent_counter != 0 ) {

			} else {
				$rate_conversion = apply_filters( 'ywrac_rate_conversion', 0 );
			}

			$total_amount       = apply_filters( 'ywrac_total_amount', get_option( 'ywrac_total_amount' ) );
			$total_cart_amount  = apply_filters( 'ywrac_total_cart_amount', get_option( 'ywrac_total_cart_amount' ) );
			$total_order_amount = apply_filters( 'ywrac_total_order_amount', get_option( 'ywrac_total_order_amount' ) );

			$reports_tab = YITH_YWRAC_TEMPLATE_PATH . '/admin/reports-tab.php';
			if ( file_exists( $reports_tab ) ) {
				include_once $reports_tab;

				echo '<button class="ywrac-reset-reports button-primary">' . esc_html__('Reset', 'yith-woocommerce-recover-abandoned-cart') . '</button>';
			    wp_nonce_field('ywrac_reset_reports_nonce', 'ywrac_reset_reports_nonce', false, true );
			}
		}

	    /**
		* Reset Reports
		*
		* reset all the reports
		*
		* @param $links | links plugin array
		*
		* @return   mixed Array
		* @since    1.4.3
		* @author   Armando Liccardo <armando.liccardo@yithemes.com>
		*/
		public function ajax_ywrac_reset_reports() {
		   check_ajax_referer( 'ywrac_reset_reports_nonce', 'nonce' );
		   $result = array( 'reset' => true );

		   update_option( 'ywrac_email_sent_counter', 0 );
		   update_option( 'ywrac_email_sent_cart_counter', 0 );
		   update_option( 'ywrac_email_sent_order_counter', 0 );
		   update_option( 'ywrac_email_clicks_counter', 0 );
		   update_option( 'ywrac_email_cart_clicks_counter', 0 );
		   update_option( 'ywrac_email_order_clicks_counter', 0 );
		   update_option( 'ywrac_abandoned_carts_counter', 0 );
		   update_option( 'ywrac_total_pending_orders', 0 );
		   update_option( 'ywrac_total_abandoned_carts', 0 );
		   update_option( 'ywrac_recovered_carts', 0 );
		   update_option( 'ywrac_total_recovered_pending_orders', 0 );
		   update_option( 'ywrac_total_recovered_carts', 0 );
		   update_option( 'ywrac_total_amount', 0 );
		   update_option( 'ywrac_total_cart_amount', 0 );
		   update_option( 'ywrac_total_order_amount', 0 );


           wp_send_json( $result );
        }

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
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
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWRAC_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_YWRAC_SLUG;
			}

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Add a textarea with editor as type of plugin panel
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function additional_textarea( $opt ) {
			$opt['default'] = ( get_option( $opt['id'] ) ) ? get_option( $opt['id'] ) : $opt['default'];
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo $opt['id']; ?>"><?php echo $opt['name']; ?></label>
				</th>
				<td class="forminp forminp-text">
				  <?php wc_get_template( 'admin/panel/textarea-editor.php', array( 'args' => $opt ) ); ?>
				</td>
			</tr>
			<?php
		}

		/**
		 * Update the value of textarea in the plugin panel
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function update_additional_textarea( $opt ) {

			if ( isset( $_POST[ $opt['id'] ] ) ) {
				update_option( $opt['id'], $_POST[ $opt['id'] ] );
			}

		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing;
		}

		/**
		 * Return the private panel_page value
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 * @return  string The panel page name
		 */
		public function get_panel_page() {
			return $this->_panel_page;
		}

		/**
		 * @param string $tab
		 *
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_panel_page_uri( $tab = '' ) {
			$panel_uri = add_query_arg( 'page', $this->_panel_page, admin_url( 'admin.php' ) );
			if ( $tab ) {
				$panel_uri = add_query_arg( 'tab', $tab, $panel_uri );
			}
			return $panel_uri;
		}

		/**
		 * Change delete Pending Orders after ... option
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function update_woocommerce_hold_stock_minutes_option() {

			$old_wc_value = get_option( '_ywrac_old_wc_value', '' );

			if ( $old_wc_value == '' ) {
				$old_wc_value = get_option( 'woocommerce_hold_stock_minutes' );
				update_option( '_ywrac_old_wc_value', $old_wc_value );
			}

			$value = get_option( 'ywrac_pending_orders_delete' );
			$value = ( '' !== $value ) ? 60 * $value : 60;

			$value = apply_filters( 'woocommerce_admin_settings_sanitize_option_woocommerce_hold_stock_minutes', '', '', $value );

			update_option( 'woocommerce_hold_stock_minutes', $value );
		}

		/**
		 * Cancel pending order
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function cancel_unpaid_orders() {
			global $wpdb;

			$duration = get_option( 'ywrac_pending_orders_delete' );

			if ( $duration < 1 ) {
				return;
			}

			$date = date( 'Y-m-d H:i:s', strtotime( '-' . absint( $duration * 60 ) . ' MINUTES', ywrac_get_timestamp() ) );

			$unpaid_orders = $wpdb->get_col(
				$wpdb->prepare(
					"
				SELECT posts.ID
				FROM {$wpdb->posts} AS posts
				WHERE 	posts.post_type   IN ('" . implode( "','", wc_get_order_types() ) . "')
				AND 	posts.post_status = 'wc-pending'
				AND 	posts.post_modified < %s
			",
					$date
				)
			);

			if ( $unpaid_orders ) {
				foreach ( $unpaid_orders as $unpaid_order ) {
					$order = wc_get_order( $unpaid_order );

					if ( apply_filters( 'woocommerce_cancel_unpaid_order', 'checkout' === get_post_meta( $unpaid_order, '_created_via', true ), $order ) ) {
						$order->update_status( 'cancelled', __( 'Unpaid order cancelled - time limit reached.', 'yith-woocommerce-recover-abandoned-cart' ) );
					}
				}
			}

			wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );
			wp_schedule_single_event( time() + ( absint( $duration ) * 60 ), 'woocommerce_cancel_unpaid_orders' );
		}


		/**
		 * Update the general counters when an order changes the status to pending
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function pending_order_counter() {
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'total_pending_orders' );
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'abandoned_carts_counter' );
		}


		/**
		 * Register an order pending conversion
		 *
		 * @param $order_id
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function register_order_pending_conversion( $order_id ) {

			$order       = wc_get_order( $order_id );
			$emails_sent = yit_get_prop( $order, '_emails_sent', true );

			if ( $emails_sent == '' ) {
				YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'total_pending_orders', false );
			}

			$is_recovered = yit_get_prop( $order, '_ywrac_recovered' );
			$email_id     = yit_get_prop( $order, '_ywrac_email_id_processed' );

			if ( empty( $email_id ) || ! empty( $is_recovered ) ) {
				return;
			}

			// add meta to order
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter_meta( $order_id, '_ywrac_recovered' );
			// update email template meta counter of recovered cart
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter_meta( $email_id, '_cart_recovered' );
			// update general counter of recovered cart
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'recovered_carts' ); // total of total_recovered_cart + total_pending_order
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'total_recovered_pending_orders' );
			// update total of recovered carts
			$order = wc_get_order( $order_id );

			YITH_WC_Recover_Abandoned_Cart_Helper()->update_amount_total( $order->order_total, 'order' );
			YITH_WC_Recover_Abandoned_Cart_Helper()->update_amount_total( $order->order_total );

			yit_save_prop(
				$order,
				array(
					'_cart_status'     => 'recovered',
					'_ywrac_recovered' => 'recovered',
				),
				false,
				true
			);

			$args = array(
				'order_id' => $order_id,
			);

			if ( get_option( 'ywrac_enable_email_admin' ) == 'yes' ) {
				do_action( 'send_recovered_cart_mail', $args );
			}

		}

	}
}

/**
 * Unique access to instance of YITH_WC_Recover_Abandoned_Cart_Admin class
 *
 * @return \YITH_WC_Recover_Abandoned_Cart_Admin
 */
function YITH_WC_Recover_Abandoned_Cart_Admin() {
	return YITH_WC_Recover_Abandoned_Cart_Admin::get_instance();
}
