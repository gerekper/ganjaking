<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF' ) ) {
	/**
	 * WooCommerce Affiliates
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF {
		/**
		 * Plugin version
		 *
		 * @const string
		 * @since 1.0.0
		 */
		const YITH_WCAF_VERSION = '1.7.2';

		/**
		 * Plugin DB version
		 *
		 * @const string
		 * @since 1.0.0
		 */
		const YITH_WCAF_DB_VERSION = '1.0.1';

		/**
		 * Available plugin endpoints
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_available_endpoints = array();

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAF
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCAF
		 * @since 1.0.0
		 */
		public function __construct() {
			do_action( 'yith_wcaf_startup' );

			// load plugin-fw.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			// enqueue frontend scripts.
			add_action( 'init', array( $this, 'register_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// install plugin.
			add_action( 'init', array( $this, 'install' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );

			// init frontend dashboard.
			add_filter( 'the_title', array( $this, 'filter_dashboard_title' ) );
			add_action( 'template_redirect', array( $this, 'send_frame_options_header' ) );

			// handle ajax requests.
			add_action( 'wp_ajax_yith_wcaf_json_search_products_and_variations', array(
				$this,
				'ajax_json_search_products',
			) );
			add_action( 'wp_ajax_nopriv_yith_wcaf_json_search_products_and_variations', array(
				$this,
				'ajax_json_search_products',
			) );

			// register shortcodes.
			add_action( 'init', array( 'YITH_WCAF_Shortcode', 'init' ), 5 );
		}

		/**
		 * Register frontend scripts
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_scripts() {
			$path           = ( defined( 'SCRIPT_DEBUG' ) && WP_DEBUG ) ? 'unminified/' : '';
			$suffix         = ( defined( 'SCRIPT_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
			$wc_assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';


			// register fontello fonts (social share icons).
			wp_register_style( 'yith-wcaf-fontello', YITH_WCAF_URL . 'assets/css/fontello/fontello.css' );

			// register plugin style.
			wp_register_style( 'yith-wcaf', YITH_WCAF_URL . 'assets/css/yith-wcaf.css', array( 'select2' ) );

			// register selectWoo if missing.
			if ( ! wp_script_is( 'selectWoo', 'registered' ) ) {
				wp_register_script( 'selectWoo', plugins_url( 'assets/js/selectWoo/selectWoo.full' . $suffix . '.js', WC_PLUGIN_FILE ), array( 'jquery' ), WC_VERSION );
			}

			// register country select, if missing.
			if ( ! wp_script_is( 'wc-country-select', 'registered' ) ) {
				wp_register_script( 'wc-country-select', plugins_url( 'assets/js/frontend/country-select' . $suffix . '.js', WC_PLUGIN_FILE ), array( 'jquery' ), WC_VERSION );
			}

			// register plugin script.
			wp_register_script( 'yith-wcaf', YITH_WCAF_URL . 'assets/js/' . $path . 'yith-wcaf' . $suffix . '.js', array(
				'jquery',
				'jquery-ui-datepicker',
				'selectWoo',
				'wc-country-select',
			), false, true );

			wp_localize_script(
				'yith-wcaf',
				'yith_wcaf',
				array(
					'labels'                => array(
						'select2_i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
						'select2_i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
						'select2_i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
						'select2_i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
						'select2_i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
						'select2_i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
						'select2_i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
						'select2_i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
						'select2_i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
						'select2_i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
						'select2_i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
						'select2_i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
						'link_copied_message'               => _X( 'Url copied', 'alert message', 'yith-woocommerce-affiliates' ),
					),
					'ajax_url'              => admin_url( 'admin-ajax.php' ),
					'set_cookie_via_ajax'   => 'yes' === get_option( 'yith_wcaf_referral_cookie_ajax_set', 'no' ),
					'referral_var'          => get_option( 'yith_wcaf_referral_var_name' ),
					'search_products_nonce' => wp_create_nonce( 'search-products' ),
					'set_referrer_nonce'    => wp_create_nonce( 'set-referrer' ),
					'get_withdraw_amount'   => wp_create_nonce( 'get-withdraw-amount' ),
				)
			);

			do_action( 'yith_wcaf_scripts_registered' );
		}

		/**
		 * Enqueue frontend scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			global $wp, $post;

			do_action( 'yith_wcaf_before_style_enqueue' );
			wp_enqueue_style( 'yith-wcaf' );

			/**
			 * Only enqueues Fontello stylesheet when on generate-link endpoint, or when page contains link_generator shortcode
			 *
			 * @since 1.6.5
			 */
			if ( isset( $wp->query_vars['generate-link'] ) || ( $post instanceof WP_Post && strpos( $post->post_content, '[yith_wcaf_link_generator' ) !== false ) ) {
				wp_enqueue_style( 'yith-wcaf-fontello' );
			}

			do_action( 'yith_wcaf_before_script_enqueue' );
			wp_enqueue_script( 'yith-wcaf' );
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
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

		/* === PRIVACY LOADER === */

		/**
		 * Loads privacy class
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function privacy_loader() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once YITH_WCAF_INC . 'class.yith-wcaf-privacy.php';
				new YITH_WCAF_Privacy();
			}
		}

		/* === INSTALL METHODS === */

		/**
		 * Adds schedules to wp cron default system
		 *
		 * @param array $schedules Schedules to add to defaults.
		 *
		 * @return array Filtered array of "to add" schedules
		 * @since 1.0.0
		 */
		public function add_schedules( $schedules ) {
			$schedules = array_merge(
				$schedules,
				array(
					'weekly'  => array(
						'interval' => WEEK_IN_SECONDS,
						'display'  => __( 'Once a week', 'yith-woocommerce-affiliates' ),
					),
					'monthly' => array(
						'interval' => DAY_IN_SECONDS * 30,
						'display'  => __( 'Once a month', 'yith-woocommerce-affiliates' ),
					),
				)
			);

			return $schedules;
		}

		/**
		 * Execute plugin installation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function install() {
			$this->_install_tables();

			if ( is_admin() ) {
				$this->_install_pages();
			}

			// init affiliate.
			YITH_WCAF_Affiliate();

			// init handlers.
			YITH_WCAF_Affiliate_Handler();
			YITH_WCAF_Click_Handler();
			YITH_WCAF_Rate_Handler();
			YITH_WCAF_Commission_Handler();
			YITH_WCAF_Payment_Handler();

			/**
			 * @since 1.2.0
			 */
			$this->_install_role();

			/**
			 * @since 1.3.0 Moved before standby, to let other part of the application filter endpoints list before init
			 */
			$this->_install_endpoints();

			do_action( 'yith_wcaf_standby' );
		}

		/**
		 * Create plugin tables
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _install_tables() {
			global $wpdb;

			// adds tables name in global $wpdb.
			$wpdb->yith_affiliates         = $wpdb->prefix . 'yith_wcaf_affiliates';
			$wpdb->yith_commissions        = $wpdb->prefix . 'yith_wcaf_commissions';
			$wpdb->yith_commission_notes   = $wpdb->prefix . 'yith_wcaf_commission_notes';
			$wpdb->yith_clicks             = $wpdb->prefix . 'yith_wcaf_clicks';
			$wpdb->yith_payments           = $wpdb->prefix . 'yith_wcaf_payments';
			$wpdb->yith_payment_commission = $wpdb->prefix . 'yith_wcaf_payment_commission';
			$wpdb->yith_payment_notes      = $wpdb->prefix . 'yith_wcaf_payment_notes';

			// skip if current db version is equal to plugin db version.
			$current_db_version = get_option( 'yith_wcaf_db_version' );
			if ( $current_db_version === self::YITH_WCAF_DB_VERSION ) {
				return;
			}

			// assure dbDelta function is defined.
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// retrieve table charset.
			$charset_collate = $wpdb->get_charset_collate();

			// adds affiliates table.
			$sql = "CREATE TABLE $wpdb->yith_affiliates (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    token varchar(255) NOT NULL,
                    user_id bigint(20) NOT NULL,
                    rate decimal(4,2) DEFAULT NULL,
                    earnings double(15,3) NOT NULL DEFAULT 0,
                    refunds double(15,3) NOT NULL DEFAULT 0,
                    paid double(15,3) NOT NULL DEFAULT 0,
                    click int(9) NOT NULL DEFAULT 0,
                    conversion int(9) NOT NULL DEFAULT 0,
                    enabled tinyint(1) NOT NULL DEFAULT 0,
                    banned tinyint(1) NOT NULL DEFAULT 0,
                    payment_email varchar(100) NOT NULL DEFAULT '',
                    PRIMARY KEY ID (ID)
				) $charset_collate;";

			dbDelta( $sql );

			// adds commissions table.
			$sql = "CREATE TABLE $wpdb->yith_commissions (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    order_id bigint(20) NOT NULL,
                    line_item_id bigint(20) NOT NULL,
                    affiliate_id bigint(20) NOT NULL,
                    rate decimal(4,2) NOT NULL,
                    amount double(9,3) NOT NULL DEFAULT 0,
                    refunds double(9,3) NOT NULL DEFAULT 0,
                    status varchar(255) NOT NULL,
                    created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    last_edit datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY (ID)
				) $charset_collate;";

			dbDelta( $sql );

			// adds commission notes table.
			$sql = "CREATE TABLE $wpdb->yith_commission_notes (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    commission_id bigint(20) NOT NULL,
                    note_content text NOT NULL,
                    note_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY (ID)
				) $charset_collate;";

			dbDelta( $sql );

			// adds clicks table.
			$sql = "CREATE TABLE $wpdb->yith_clicks (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    affiliate_id bigint(20) NOT NULL,
                    link varchar(255) NOT NULL,
                    origin varchar(255) DEFAULT NULL,
                    origin_base varchar(255) DEFAULT NULL,
                    IP varchar(15) NOT NULL,
                    click_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					order_id bigint(20) DEFAULT NULL,
					conv_date datetime DEFAULT NULL,
					conv_time bigint(10) DEFAULT NULL,
                    PRIMARY KEY (ID)
				) $charset_collate;";

			dbDelta( $sql );

			// adds payments table.
			$sql = "CREATE TABLE $wpdb->yith_payments(
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    affiliate_id bigint(20) NOT NULL,
                    payment_email varchar(100) NOT NULL DEFAULT '',
                    gateway varchar(255) NOT NULL DEFAULT '',
                    status varchar(255) NOT NULL,
                    amount double(15,4) NOT NULL DEFAULT 0,
                    created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    completed_at datetime DEFAULT NULL,
                    transaction_key varchar(255) DEFAULT NULL,
                    PRIMARY KEY (ID)
				) $charset_collate;";

			dbDelta( $sql );

			// adds payment_commission table.
			$sql = "CREATE TABLE $wpdb->yith_payment_commission(
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    payment_id bigint(20) NOT NULL,
                    commission_id bigint(20) NOT NULL,
                    PRIMARY KEY (ID)
				) $charset_collate;";

			dbDelta( $sql );

			// adds commission notes table.
			$sql = "CREATE TABLE $wpdb->yith_payment_notes (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    payment_id bigint(20) NOT NULL,
                    note_content text NOT NULL,
                    note_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY (ID)
				) $charset_collate;";

			dbDelta( $sql );

			update_option( 'yith_wcaf_db_version', self::YITH_WCAF_DB_VERSION );
		}

		/**
		 * Install affiliate pages, if it doesn't exists; return created page ID
		 *
		 * @return int Created page id
		 * @since 1.0.0
		 */
		protected function _install_pages() {
			$page = array(
				'name'    => 'affiliate-dashboard',
				'title'   => __( 'Affiliate Dashboard', 'yith-woocommerce-affiliates' ),
				'content' => '<!-- wp:shortcode -->[' . apply_filters( 'yith_wcaf_affiliate_dashboard_shortcode_tag', 'yith_wcaf_affiliate_dashboard' ) . ']<!-- /wp:shortcode -->',
			);

			return wc_create_page( esc_sql( $page['name'] ), 'yith_wcaf_dashboard_page_id', $page['title'], $page['content'] );
		}

		/**
		 * Register plugin endpoints
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _install_endpoints() {
			$current_db_version = get_option( 'yith_wcaf_db_version' );

			// init endpoints.
			$this->_available_endpoints = apply_filters( 'yith_wcaf_available_endpoints', array_merge(
				array(
					'commissions' => __( 'Commissions', 'yith-woocommerce-affiliates' ),
				),
				YITH_WCAF_Click_Handler()->are_hits_registered() ? array(
					'clicks' => __( 'Clicks', 'yith-woocommerce-affiliates' ),
				) : array(),
				array(
					'payments'      => __( 'Payments', 'yith-woocommerce-affiliates' ),
					'generate-link' => __( 'Generate Link', 'yith-woocommerce-affiliates' ),
					'settings'      => __( 'Settings', 'yith-woocommerce-affiliates' ),
				)
			) );

			foreach ( $this->get_dashboard_endpoints() as $endpoint => $title ) {
				add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
			}

			/**
			 * Flush rewrite rule only on db changes (flush can be very expensive operation, and it is really unlikely
			 * to change endpoints without changing db)
			 *
			 * @since 1.3.0 Added option _yith_wcaf_flush_rewrite_rules; developers can set it to true, to force rewrite
			 * rules flush on next page load
			 */
			if ( self::YITH_WCAF_DB_VERSION === $current_db_version && ! get_option( '_yith_wcaf_flush_rewrite_rules', false ) ) {
				return;
			}

			update_option( '_yith_wcaf_flush_rewrite_rules', false );
			flush_rewrite_rules();
		}

		/**
		 * Add role to affiliates if updating from an older version
		 *
		 * @return void
		 * @since 1.2.0
		 */
		protected function _install_role() {
			$already_processed = get_option( 'yith_wcaf_add_role_to_affiliates', false );

			if ( $already_processed ) {
				return;
			}

			add_role( YITH_WCAF_Affiliate_Handler()->get_role_name(), __( 'Affiliate', 'yith-wcaf-affiliates' ), array(
				'read' => true,
			) );

			$affiliates = YITH_WCAF_Affiliate_Handler()->get_affiliates();

			if ( ! empty( $affiliates ) ) {
				foreach ( $affiliates as $affiliate ) {
					$user = get_user_by( 'id', $affiliate['user_id'] );

					if ( $user && ! is_wp_error( $user ) ) {
						$user->add_role( YITH_WCAF_Affiliate_Handler()->get_role_name() );
					}
				}
			}

			update_option( 'yith_wcaf_add_role_to_affiliates', true );
		}

		/* === INIT FRONTEND DASHBOARD === */

		/**
		 * Register plugins query vars
		 *
		 * @param mixed $vars Available query vars.
		 *
		 * @return mixed Filtered query vars
		 * @since 1.0.0
		 */
		public function add_query_vars( $vars ) {
			foreach ( $this->get_dashboard_endpoints() as $endpoint => $title ) {
				$vars[] = $endpoint;
			}

			return $vars;
		}

		/**
		 * Filter dashboard pages title
		 *
		 * @param string $title Current page title.
		 *
		 * @return string Filtered page title
		 * @since 1.0.0
		 */
		public function filter_dashboard_title( $title ) {
			global $wp_query;

			if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && $this->is_dashboard_url() ) {
				$current_endpoint = $this->get_dashboard_endpoint();

				if ( $current_endpoint ) {
					$endpoints = $this->get_dashboard_endpoints();
					$title     = $endpoints[ $current_endpoint ];
				}

				remove_filter( 'the_title', array( $this, 'filter_dashboard_title' ) );
			}

			return $title;
		}

		/**
		 * Sends same origin header for Affiliate Dashboard page, to avoid it from being retrieved via i-frame
		 *
		 * @returns void
		 * @since 1.2.2
		 */
		public function send_frame_options_header() {
			if ( yith_wcaf_is_affiliate_dashboard_page() ) {
				send_frame_options_header();
			}
		}

		/* === HELPER METHODS === */

		/**
		 * Retrieve products matching search params and print retrieved data json-encoded
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_json_search_products() {
			WC_AJAX::json_search_products_and_variations();
		}

		/**
		 * Returns available endpoints
		 *
		 * @return array Array of available endpoints
		 * @since 1.3.0
		 */
		public function get_dashboard_endpoints() {
			/**
			 * Doing it wrong added to prevent \YITH_WCAF::get_dashboard_endpoints from being called before \YITH_WCAF::install
			 * @since 1.6.4
			 */
			if ( ! did_action( 'init' ) ) {
				_doing_it_wrong( 'get_dashboard_endpoints', esc_html_x( 'You must call \YITH_WCAF::get_dashboard_endpoints after init hook, otherwise endpoint array will be empty', 'Doing it wrong message', 'yith-woocommerce-affiliates' ), '1.6.4' );
			}

			return apply_filters( 'yith_wcaf_get_dashboard_endpoints', $this->_available_endpoints );
		}

		/**
		 * Return current dashboard endpoint
		 *
		 * @return string|bool Current request endpoint, or false if none set
		 * @since 1.0.0
		 */
		public function get_dashboard_endpoint() {
			global $wp;

			foreach ( $this->get_dashboard_endpoints() as $endpoint => $title ) {
				if ( isset( $wp->query_vars[ $endpoint ] ) ) {
					return $endpoint;
				}
			}

			return false;
		}

		/**
		 * Get referral url
		 *
		 * @param string|bool $token Token to use for url, or false, if current user token should be used
		 * @param string|bool $base_url Base url to use, or false, if home url should be used
		 *
		 * @return string|bool Generated affiliate url, or false on failure
		 * @since 1.0.0
		 */
		public function get_referral_url( $token = false, $base_url = false ) {
			$ref_name = YITH_WCAF_Affiliate_Handler()->get_ref_name();

			if ( ! $token && is_user_logged_in() ) {
				$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( get_current_user_id() );
				$token     = $affiliate['token'];
			}

			if ( ! $base_url ) {
				$base_url = home_url();
			}

			if ( ! $token || ! $base_url ) {
				return false;
			}

			return apply_filters( 'yith_wcaf_get_referral_url', esc_url( add_query_arg( $ref_name, $token, $base_url ) ), $ref_name, $token, $base_url );

		}

		/**
		 * Check if current request if for a dashboard page
		 *
		 * @param string $endpoint Endpoint to check.
		 *
		 * @return bool Whether current request if for a dashboard page or not
		 * @since 1.0.0
		 */
		public function is_dashboard_url( $endpoint = '' ) {
			global $wp;

			if ( $endpoint ) {
				return in_array( $endpoint, array_keys( $this->get_dashboard_endpoints() ) );
			} else {
				foreach ( $this->get_dashboard_endpoints() as $endpoint => $title ) {
					if ( isset( $wp->query_vars[ $endpoint ] ) ) {
						return true;
					}
				}

				return false;
			}
		}

		/**
		 * Return affiliate dashboard url
		 *
		 * @param string $endpoint Optional endpoint of the page.
		 * @param string $value    Optional value to pass to the endpoint.
		 *
		 * @return string Dashboard url, or home url if no dashboard page is set
		 * @since 1.0.0
		 */
		public function get_affiliate_dashboard_url( $endpoint = '', $value = '' ) {
			$dashboard_page_id = get_option( 'yith_wcaf_dashboard_page_id' );

			if ( ! $dashboard_page_id ) {
				return home_url();
			}

			if ( function_exists( 'wpml_object_id_filter' ) ) {
				$dashboard_page_id = wpml_object_id_filter( $dashboard_page_id, 'page', true );
			} elseif ( function_exists( 'icl_object_id' ) ) {
				$dashboard_page_id = icl_object_id( $dashboard_page_id, 'page', true );
			}
			$permalink = get_permalink( $dashboard_page_id );

			if ( get_option( 'permalink_structure' ) && ! defined( 'ICL_PLUGIN_PATH' ) ) {
				if ( strstr( $permalink, '?' ) ) {
					$query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
					$permalink    = current( explode( '?', $permalink ) );
				} else {
					$query_string = '';
				}
				$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
			} else {
				$url = add_query_arg( $endpoint, $value, $permalink );
			}

			return apply_filters( 'yith_wcaf_get_endpoint_url', $url, $endpoint, $value, $permalink );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCAF_Premium' ) ) {
				return YITH_WCAF_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCAF::$instance ) ) {
					YITH_WCAF::$instance = new YITH_WCAF();
				}

				return YITH_WCAF::$instance;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF class
 *
 * @return \YITH_WCAF
 * @since 1.0.0
 */
function YITH_WCAF() {
	return YITH_WCAF::get_instance();
}
