<?php
/**
 * Frontend class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Frontend {

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WCMAP_VERSION;

		/**
		 * Security class instance
		 *
		 * @since 1.0.0
		 * @var \YITH_WCMAP_Security|null
		 */
		public $security = null;

		/**
		 * Page templates
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $_is_myaccount = false;

		/**
		 * Boolean to check if account have menu
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $_my_account_have_menu = false;

		/**
		 * Menu Shortcode
		 *
		 * @access protected
		 * @var string
		 */
		protected $_shortcode_name = 'yith-wcmap-menubar';

		/**
		 * My account endpoint
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $_menu_endpoints = array();

		/**
		 * Action print avatar form
		 *
		 * @since 2.2.0
		 * @var string
		 */
		public $action_print = 'ywcmap_print_avatar_form';

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			include( 'class.yith-wcmap-security.php' );
			$this->security = new YITH_WCMAP_Security();

			// plugin frontend init
			add_action( 'init', array( $this, 'init' ), 100 );

			// enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );

			// check if is shortcode my-account
			add_action( 'template_redirect', array( $this, 'check_myaccount' ), 1 );
			// redirect to the default endpoint
			add_action( 'template_redirect', array( $this, 'redirect_to_default' ), 150 );
			add_action( 'template_redirect', array( $this, 'is_wc_memberships_teams' ), 200 );
			// add new navigation
			add_action( 'woocommerce_account_navigation', array( $this, 'add_my_account_menu' ), 10 );
			// manage account content
			add_action( 'woocommerce_account_content', array( $this, 'manage_account_content' ), 1 );
			// change title
			add_action( 'template_redirect', array( $this, 'manage_account_title' ), 10 );

			// shortcode for print my account sidebar
			add_shortcode( $this->_shortcode_name, array( $this, 'my_account_menu' ) );

			// add avatar
			add_action( 'init', array( $this, 'add_avatar' ) );

			// shortcodes for my-downloads and view order content
			add_shortcode( 'my_downloads_content', array( $this, 'my_downloads_content' ) );
			add_shortcode( 'view_order_content', array( $this, 'view_order_content' ) );

			// shortcode to print default dashboard
			add_shortcode( 'default_dashboard_content', array( $this, 'print_default_dashboard_content' ) );

			// mem if is my account page
			add_action( 'shutdown', array( $this, 'save_is_my_account' ) );

			// reset default avatar
			add_action( 'init', array( $this, 'reset_default_avatar' ) );

			// AJAX Avatar
			add_action( 'wc_ajax_' . $this->action_print, array( $this, 'get_avatar_form_ajax' ) );

			// Prevent redirect to dashboard in Customize section using Smart Email plugin
			add_filter( 'yith_wcmap_no_redirect_to_default', array( $this, 'fix_issue_with_smartemail_plugin' ) );

            // YITH Proteo Style
            add_filter( 'yith_wcmap_get_custom_css', array( $this, 'add_proteo_style' ) );
		}

		/**
		 * Init plugins variable
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function init() {

			$this->_menu_endpoints = YITH_WCMAP()->items->get_items();

			// get current user and set user role
			$current_user = wp_get_current_user();
			$user_role    = (array) $current_user->roles;

			// first register string for translations then remove disable
			foreach ( $this->_menu_endpoints as $endpoint => &$options ) {

				// check if master is active
				if ( isset( $options['active'] ) && ! $options['active'] ) {
					unset( $this->_menu_endpoints[ $endpoint ] );
					continue;
				}

				// check master by user role and user membership
				if ( isset( $options['usr_roles'] ) && $this->_hide_by_usr_roles( $options['usr_roles'], $user_role ) &&
					isset( $options['membership_plans'] ) && $this->_hide_by_membership_plan( $options['membership_plans'] )
				) {
					unset( $this->_menu_endpoints[ $endpoint ] );
					continue;
				}  // check master by user roles
				elseif ( isset( $options['usr_roles'] ) && $this->_hide_by_usr_roles( $options['usr_roles'], $user_role ) ) {
					unset( $this->_menu_endpoints[ $endpoint ] );
					continue;
				}// check master by user membership
				elseif ( isset( $options['membership_plans'] ) && $this->_hide_by_membership_plan( $options['membership_plans'] ) ) {
					unset( $this->_menu_endpoints[ $endpoint ] );
					continue;
				}

				// check if child is active
				if ( isset( $options['children'] ) ) {
					foreach ( $options['children'] as $child_endpoint => $child_options ) {
						if ( ! $child_options['active'] ) {
							unset( $options['children'][ $child_endpoint ] );
							continue;
						}
						if ( isset( $child_options['usr_roles'] ) && $this->_hide_by_usr_roles( $child_options['usr_roles'], $user_role ) &&
							isset( $child_options['membership_plans'] ) && $this->_hide_by_membership_plan( $child_options['membership_plans'] )
						) {
							unset( $options['children'][ $child_endpoint ] );
							continue;
						}  // check master by user roles
						elseif ( isset( $child_options['usr_roles'] ) && $this->_hide_by_usr_roles( $child_options['usr_roles'], $user_role ) ) {
							unset( $options['children'][ $child_endpoint ] );
							continue;
						}// check master by user membership
						elseif ( isset( $child_options['membership_plans'] ) && $this->_hide_by_membership_plan( $child_options['membership_plans'] ) ) {
							unset( $options['children'][ $child_endpoint ] );
							continue;
						}


						// get translated label
						$options['children'][ $child_endpoint ]['label'] = $this->get_string_translated( $child_endpoint, $child_options['label'] );
						empty( $child_options['url'] ) || $options['children'][ $child_endpoint ]['url'] = $this->get_string_translated( $child_endpoint . '_url', $child_options['url'] );
						empty( $child_options['content'] ) || $options['children'][ $child_endpoint ]['content'] = $this->get_string_translated( $child_endpoint . '_content', $child_options['content'] );
					}
				}

				// get translated label
				$options['label'] = $this->get_string_translated( $endpoint, $options['label'] );
				empty( $options['url'] ) || $options['url'] = $this->get_string_translated( $endpoint . '_url', $options['url'] );
				empty( $options['content'] ) || $options['content'] = $this->get_string_translated( $endpoint . '_content', $options['content'] );
			}

			// remove theme sidebar
			if ( defined( 'YIT' ) && YIT ) {
				remove_action( 'yit_content_loop', 'yit_my_account_template', 5 );
				// also remove the my-account template
				$my_account_id = wc_get_page_id( 'myaccount' );
				if ( 'my-account.php' == get_post_meta( $my_account_id, '_wp_page_template', true ) ) {
					update_post_meta( $my_account_id, '_wp_page_template', 'default' );
				}
			}

			// remove standard woocommerce sidebar
			if ( ( $priority = has_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' ) ) !== false ) {
				remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation', $priority );
			}
		}

		/**
		 * Check if is a WooCommerce Memberships Teams Endpoint and it needs a different menu
		 *
		 * @since  2.5.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function is_wc_memberships_teams() {
			if ( ! class_exists( 'WC_Memberships_For_Teams_Loader' ) ) {
				return false;
			}

			$teams_area = wc_memberships_for_teams()->get_frontend_instance()->get_teams_area_instance();
			if ( $teams_area->is_teams_area_section() ) {
				remove_action( 'woocommerce_account_navigation', array( $this, 'add_my_account_menu' ), 10 );
				add_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation', 10 );
				return true;
			}

			return false;
		}

		/**
		 * Add plugin menu to My Account shortcode
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function add_my_account_menu() {

			if ( apply_filters( 'yith_wcmap_my_account_have_menu', $this->_my_account_have_menu ) ) {
				return;
			}

			$position = get_option( 'yith-wcmap-menu-position', 'left' );
			$tab      = get_option( 'yith-wcmap-menu-style', 'sidebar' ) === 'tab' ? '-tab' : '';

			ob_start();
			?>
			<div id="my-account-menu<?php echo esc_attr( $tab ); ?>" class="yith-wcmap position-<?php echo esc_attr( $position ); ?>">
				<?php echo do_shortcode( '[' . $this->_shortcode_name . ']' ); ?>
			</div>
			<?php

			echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// set my account menu variable. This prevent double menu
			$this->_my_account_have_menu = true;
		}

		/**
		 * Manage endpoint account content based on plugin option
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function manage_account_content() {

			// search for active endpoints
			$active = yith_wcmap_get_current_endpoint();
			// get active endpoint options by slug
			$endpoint = yith_wcmap_get_endpoint_by( $active, 'key', $this->_menu_endpoints );

			if ( empty( $endpoint ) || ! is_array( $endpoint ) ) {
				return;
			}
			// get key
			$key = key( $endpoint );

			// check in custom content
			if ( ! empty( $endpoint[ $key ]['content'] ) ) {

				if ( apply_filters( 'yith_wcmnap_hide_default_endpoint_content', true, $endpoint ) ) {
					remove_action( 'woocommerce_account_content', 'woocommerce_account_content' );
				}

				// add compatibility with WSDesk - WordPress Support Desk
				if ( has_shortcode( $endpoint[ $key ]['content'], 'wsdesk_support' ) ) {
					$this->enqueue_wsdesk_scripts();
				}

				echo do_shortcode( stripslashes( $endpoint[ $key ]['content'] ) );
			}
		}

		/**
		 * Enqueue scripts for WSDesk - WordPress Support Desk
		 *
		 * @since  2.5.1
		 * @author Francesco Licandro
		 * @return void
		 */
		public function enqueue_wsdesk_scripts() {

			if ( ! defined( 'EH_CRM_MAIN_JS' ) || ! function_exists( 'eh_crm_get_settingsmeta' ) ) {
				return;
			}

			wp_enqueue_script( 'jquery' );
			$handle  = 'bootstrap.min.js';
			$handle1 = 'bootstrap.js';
			$handle2 = 'bootstrap.css';
			$list    = 'enqueued';
			if ( ! wp_script_is( $handle, $list ) && ! wp_script_is( $handle1, $list ) && ! defined( 'WSDESK_UNLOAD_BOOT_JS' ) ) {
				wp_enqueue_script( 'wsdesk_bootstrap', EH_CRM_MAIN_JS . 'bootstrap.js' );
			}
			if ( ! wp_style_is( $handle2, $list ) && ! defined( 'WSDESK_UNLOAD_BOOT_CSS' ) ) {
				wp_enqueue_style( 'wsdesk_bootstrap', EH_CRM_MAIN_CSS . 'bootstrap.css' );
			}
			wp_enqueue_script( 'support_scripts', EH_CRM_MAIN_JS . "crm_support.js" );
			wp_enqueue_style( "slider", EH_CRM_MAIN_CSS . "slider.css" );
			wp_enqueue_style( "support_styles", EH_CRM_MAIN_CSS . "crm_support.css" );
			wp_enqueue_style( "new_styles", EH_CRM_MAIN_CSS . "new-style.css" );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui', EH_CRM_MAIN_CSS . "jquery-ui.css" );
			$selected = eh_crm_get_settingsmeta( 0, 'selected_fields' );
			if ( empty( $selected ) ) {
				$selected = array();
			}
			if ( in_array( "google_captcha", $selected ) ) {
				wp_enqueue_script( 'captcha_scripts', "https://www.google.com/recaptcha/api.js" );
			}
			wp_localize_script( 'support_scripts', 'support_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}

		/**
		 * Change my account page title based on endpoint
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 */
		public function manage_account_title() {

			global $wp, $post;

			// search for active endpoints
			$active = yith_wcmap_get_current_endpoint();
			// get active endpoint options by slug
			$endpoint = yith_wcmap_get_endpoint_by( $active, 'slug', $this->_menu_endpoints );

			if ( empty( $endpoint ) || ! is_array( $endpoint ) ) {
				return;
			}

			// get key
			$key = key( $endpoint );

			// set endpoint title
			if ( isset( $endpoint['view-quote'] ) && ! empty( $wp->query_vars[ $active ] ) ) {
				$order_id         = $wp->query_vars[ $active ];
				$post->post_title = sprintf( __( 'Quote #%s', 'yith-woocommerce-request-a-quote' ), $order_id );
			} elseif ( ! empty( $endpoint[ $key ]['label'] ) && $active != 'dashboard' ) {
				$post->post_title = stripslashes( $endpoint[ $key ]['label'] );
			}
		}

		/**
		 * Register a WPML string
		 *
		 * @access protected
		 * @since  2.0.0
		 * @author Francesco Licandro
		 * @param string $key
		 * @param string $value
		 * @deprecated
		 */
		protected function _register_string_wpml( $key, $value ) {
			do_action( 'wpml_register_single_string', 'yith-woocommerce-customize-myaccount-page', 'plugin_yit_wcmap_' . $key, $value );
		}

		/**
		 * Get a WPML translated string
		 *
		 * @access     protected
		 * @since      2.0.0
		 * @author     Francesco Licandro
		 * @param string $key
		 * @param string $value
		 * @return string
		 * @deprecated Use instead get_string_translated method
		 */
		protected function _get_string_wpml( $key, $value ) {
			$localized_label = apply_filters( 'wpml_translate_single_string', $value, 'yith-woocommerce-customize-myaccount-page', 'plugin_yit_wcmap_' . $key );
			return $localized_label;
		}

		/**
		 * Get a translated string
		 *
		 * @access protected
		 * @since  2.3.0
		 * @author Francesco Licandro
		 * @param string $key
		 * @param string $value
		 * @return string
		 */
		public function get_string_translated( $key, $value ) {
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$value = apply_filters( 'wpml_translate_single_string', $value, 'yith-woocommerce-customize-myaccount-page', 'plugin_yit_wcmap_' . $key );
			} elseif ( defined( 'POLYLANG_VERSION' ) && function_exists( 'pll__' ) ) {
				$value = pll__( $value );
			}

			return $value;
		}

		/**
		 * Hide field based on current user role
		 *
		 * @access protected
		 * @since  2.0.0
		 * @author Francesco Licandro
		 * @param array $roles
		 * @param array $current_user_role
		 * @return boolean
		 */
		protected function _hide_by_usr_roles( $roles, $current_user_role ) {
			// return if $roles is empty
			if ( empty( $roles ) || current_user_can( 'administrator' ) ) {
				return false;
			}

			// check if current user can
			$intersect = array_intersect( $roles, $current_user_role );
			if ( ! empty( $intersect ) ) {
				return false;
			}

			return true;
		}


		/**
		 * Integration with YITH WooCommerce Membership: show endpoint only if customer has purchased a specific membership plan
		 * @param $membership_plans
		 * @return bool
		 */
		protected function _hide_by_membership_plan( $membership_plans ) {
			// return if $roles is empty
			if ( ! defined( 'YITH_WCMBS_PREMIUM' ) || empty( $membership_plans ) || current_user_can( 'administrator' ) ) {
				return false;
			}

			$user_id    = get_current_user_id();
			$member     = YITH_WCMBS_Members()->get_member( $user_id );
			$user_plans = $member->get_membership_plans( array( 'return' => 'complete' ) );

			foreach ( $user_plans as $plan ) {

				if ( in_array( $plan->plan_id, $membership_plans ) ) {

					return false;

				}

			}

			return true;

		}


		/**
		 * Enqueue scripts and styles
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {

			if ( ! $this->_is_myaccount ) {
				return;
			};

			$paths       = apply_filters( 'yith_wcmap_stylesheet_paths', array( WC()->template_path() . 'yith-customize-myaccount.css', 'yith-customize-myaccount.css' ) );
			$located     = locate_template( $paths, false, false );
			$search      = array( get_stylesheet_directory(), get_template_directory() );
			$replace     = array( get_stylesheet_directory_uri(), get_template_directory_uri() );
			$stylesheet  = ! empty( $located ) ? str_replace( $search, $replace, $located ) : YITH_WCMAP_ASSETS_URL . '/css/ywcmap-frontend.css';
			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			wp_register_style( 'ywcmap-frontend', $stylesheet );
			wp_register_script( 'ywcmap-frontend', YITH_WCMAP_ASSETS_URL . '/js/ywcmap-frontend' . $suffix . '.js', array( 'jquery' ), false, true );
			// font awesome
			wp_register_style( 'font-awesome', YITH_WCMAP_ASSETS_URL . '/css/font-awesome.min.css' );

			// ENQUEUE STYLE
			wp_enqueue_style( 'ywcmap-frontend' );
			wp_enqueue_style( 'font-awesome' );

			$inline_css = yith_wcmap_get_custom_css();
			wp_add_inline_style( 'ywcmap-frontend', $inline_css );

			// ENQUEUE SCRIPTS
			wp_enqueue_script( 'ywcmap-frontend' );
			wp_localize_script( 'ywcmap-frontend', 'yith_wcmap', array(
				'ajaxurl'     => WC_AJAX::get_endpoint( "%%endpoint%%" ),
				'actionPrint' => $this->action_print,
			) );
		}

		/**
		 * Check if is page my-account and set class variable
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function check_myaccount() {
			global $post;
			// TODO usare wc_post_content_has_shortcode
			if ( ! is_null( $post ) && strpos( $post->post_content, 'woocommerce_my_account' ) !== false && is_user_logged_in() ) {
				$this->_is_myaccount = true;
			}

			$this->_is_myaccount = apply_filters( 'yith_wcmap_is_my_account_page', $this->_is_myaccount );
		}

		/**
		 * Redirect to default endpoint
		 *
		 * @access public
		 * @since  1.0.4
		 * @author Francesco Licandro
		 */
		public function redirect_to_default() {

			// exit if not my account
			if ( ! $this->_is_myaccount || ! is_array( $this->_menu_endpoints ) ) {
				return;
			}

			$current_endpoint = yith_wcmap_get_current_endpoint();
			// if a specific endpoint is required return
			if ( $current_endpoint != 'dashboard' || apply_filters( 'yith_wcmap_no_redirect_to_default', false ) ) {
				return;
			}

			$default_endpoint = get_option( 'yith-wcmap-default-endpoint', 'dashboard' );
			// let's third part filter default endpoint
			$default_endpoint = apply_filters( 'yith_wcmap_default_endpoint', $default_endpoint );
			$url              = wc_get_page_permalink( 'myaccount' );

			// otherwise if I'm not in my account yet redirect to default
			if ( ! get_option( 'yith_wcmap_is_my_account', true ) && ! isset( $_REQUEST['elementor-preview'] ) && $current_endpoint != $default_endpoint ) {
				$default_endpoint != 'dashboard' && $url = wc_get_endpoint_url( $default_endpoint, '', $url );
				wp_safe_redirect( $url );
				exit;
			}
		}

		/**
		 * Output my-account shortcode
		 *
		 * @since  1.0.0
		 * @author Frnacesco Licandro
		 */
		public function my_account_menu() {

			$args = apply_filters( 'yith-wcmap-myaccount-menu-template-args', array(
				'endpoints'      => $this->_menu_endpoints,
				'my_account_url' => get_permalink( wc_get_page_id( 'myaccount' ) ),
				'avatar'         => get_option( 'yith-wcmap-custom-avatar' ) == 'yes',
			) );

			ob_start();

			wc_get_template( 'ywcmap-myaccount-menu.php', $args, '', YITH_WCMAP_DIR . 'templates/' );

			return ob_get_clean();

		}

		/**
		 * Add user avatar
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function add_avatar() {

			if ( ! isset( $_FILES['ywcmap_user_avatar'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'wp_handle_upload' ) )
				return;

			// required file
			if ( ! function_exists( 'media_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}

			$media_id = media_handle_upload( 'ywcmap_user_avatar', 0 );

			if ( is_wp_error( $media_id ) ) {
				return;
			}

			// save media id for filter query in media library
			$medias   = get_option( 'yith-wcmap-users-avatar-ids', array() );
			$medias[] = $media_id;
			// then save
			update_option( 'yith-wcmap-users-avatar-ids', $medias );


			// save user meta
			$user = get_current_user_id();
			update_user_meta( $user, 'yith-wcmap-avatar', $media_id );

		}

		/**
		 * Print my-downloads endpoint content
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function my_downloads_content( $atts ) {

			$content       = '';
			$template_name = version_compare( WC()->version, '3.0', '>=' ) ? 'myaccount/downloads.php' : 'myaccount/my-downloads.php';
			$template      = apply_filters( 'yith_wcmap_downloads_shortcode_template', $template_name );

			ob_start();
			wc_get_template( $template );
			$content = ob_get_clean();

			// print message if no downloads
			if ( ! $content ) {
				$content = '<p>' . __( 'There are no available downloads yet.', 'yith-woocommerce-customize-myaccount-page' ) . '</p>';
			}

			return $content;
		}


		/**
		 * Print default dashboard content
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function print_default_dashboard_content( $atts ) {

			$content       = '';
			$template_name = 'myaccount/dashboard.php';
			$template      = apply_filters( 'yith_wcmap_dashboard_shortcode_template', $template_name );

			ob_start();
			wc_get_template( $template, array(
					'current_user' => get_user_by( 'id', get_current_user_id() ),
				)
			);
			$content = ob_get_clean();

			return $content;
		}

		/**
		 * Print view-order endpoint content, if view-order is not empty print order details
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function view_order_content( $atts ) {

			global $wp;

			$content  = '';
			$endpoint = yith_wcmap_get_endpoint_by( 'orders', 'key', $this->_menu_endpoints );

			if ( empty( $endpoint ) ) {
				return '';
			}

			$slug = $endpoint['orders']['slug'];

			if ( ! empty( $wp->query_vars[ $slug ] ) ) {

				$order_id = absint( $wp->query_vars[ $slug ] );
				$order    = wc_get_order( $order_id );

				if ( ! current_user_can( 'view_order', $order_id ) ) {
					$content = '<div class="woocommerce-error">' . __( 'Invalid order.', 'woocommerce' ) . ' <a href="' . wc_get_page_permalink( 'myaccount' ) . '" class="wc-forward">' . __( 'My Account', 'woocommerce' ) . '</a>' . '</div>';

				} else {
					// Backwards compatibility
					$status       = new stdClass();
					$status->name = wc_get_order_status_name( $order->get_status() );

					ob_start();
					wc_get_template( 'myaccount/view-order.php', array(
						'status'   => $status, // @deprecated 2.2
						'order'    => wc_get_order( $order_id ),
						'order_id' => $order_id,
					) );
					$content = ob_get_clean();
				}
			} else {

				if ( version_compare( WC()->version, '3.0', '>=' ) ) {
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
					ob_start();
					woocommerce_account_orders( $paged );
					$content = ob_get_clean();
				} else {
					// backward compatibility	
					extract( shortcode_atts( array(
						'order_count' => 15,
					), $atts ) );

					$order_count = $order_count == 'all' ? -1 : $order_count;

					ob_start();
					wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) );
					$content = ob_get_clean();

					// print message if no orders
					if ( ! $content ) {
						$content = '<p>' . __( 'There are no orders yet.', 'yith-woocommerce-customize-myaccount-page' ) . '</p>';
					}
				}
			}


			return $content;
		}

		/**
		 * Save an option to check if the page is myaccount
		 *
		 * @access public
		 * @since  1.0.4
		 * @author Francesco Licandro
		 */
		public function save_is_my_account() {
			update_option( 'yith_wcmap_is_my_account', $this->_is_myaccount );
		}

		/**
		 * Reset standard WordPress avatar for customer
		 *
		 * @since  1.1.2
		 * @author Francesco Licandro
		 */
		public function reset_default_avatar() {

			if ( ! isset( $_POST['action'] ) || $_POST['action'] != 'ywcmap_reset_avatar' ) {
				return;
			}

			// get user id
			$user     = get_current_user_id();
			$media_id = get_user_meta( $user, 'yith-wcmap-avatar', true );

			if ( ! $media_id ) {
				return;
			}

			// remove id from global list
			$medias = get_option( 'yith-wcmap-users-avatar-ids', array() );
			foreach ( $medias as $key => $media ) {
				if ( $media == $media_id ) {
					unset( $media[ $key ] );
					continue;
				}
			}

			// then save
			update_option( 'yith-wcmap-users-avatar-ids', $medias );

			// then delete user meta
			delete_user_meta( $user, 'yith-wcmap-avatar' );

			// then delete media attachment
			wp_delete_attachment( $media_id );

		}

		/**
		 * Get avatar upload form
		 *
		 * @since  2.2.0
		 * @author Francesco Licandro
		 * @access public
		 * @param boolean $print Print or return avatar form
		 * @param array   $args  Array of argument for the template
		 * @return string
		 */
		public function get_avatar_form( $print = false, $args = array() ) {
			ob_start();
			wc_get_template( 'ywcmap-myaccount-avatar-form.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
			$form = ob_get_clean();

			if ( $print ) {
				echo $form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return '';
			}

			return $form;
		}

		/**
		 * Get avatar upload form using Ajax
		 *
		 * @since  2.2.0
		 * @author Francesco Licandro
		 * @access public
		 * @return void
		 */
		public function get_avatar_form_ajax() {

			if ( ! is_ajax() ) {
				return;
			}

			echo $this->get_avatar_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			die();
		}


		/**
		 *  Prevent redirect to dashboard in Customize section using Smart Email plugin
		 * @param $value
		 * @return bool
		 */
		public function fix_issue_with_smartemail_plugin( $value ) {
			if ( isset( $_GET['sa_smart_emails'] ) )
				$value = true;
			return $value;
		}

		/**
		 * Retrieve the complete list of endpoints
		 * @return string
		 */
		public function get_menu_endpoints() {
			return $this->_menu_endpoints;
		}

		/**
		 * Check if is my account
		 * @return string
		 */
		public function is_my_account() {
			return $this->_is_myaccount;
		}


        /**
         * Add custom style for YITH Proteo theme
         * @param $style
         * @return mixed
         */
        public function add_proteo_style( $style ){
            if ( defined( 'YITH_PROTEO_VERSION' ) )
                $style = yith_wcmap_get_proteo_custom_style();

            return $style;
        }
	}
}