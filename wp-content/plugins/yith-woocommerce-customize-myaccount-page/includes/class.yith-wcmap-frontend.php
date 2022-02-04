<?php
/**
 * Frontend class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Frontend {

		/**
		 * Menu Shortcode
		 *
		 * @access protected
		 * @const string
		 */
		const SHORTCODE_NAME = 'yith-wcmap-menubar';

		/**
		 * Security class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Security|null
		 */
		public $security = null;

		/**
		 * Page templates
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $is_myaccount = false;

		/**
		 * Boolean to check if account have menu
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $my_account_have_menu = false;

		/**
		 * My account endpoint
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $menu_items = array();

		/**
		 * Current active endpoint
		 *
		 * @since 3.0.0
		 * @var array
		 */
		protected $current_endpoint = array();

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			include 'class.yith-wcmap-security.php';
			$this->security = new YITH_WCMAP_Security();

			// Plugin frontend init.
			add_action( 'init', array( $this, 'init' ), 100 );

			// Enqueue scripts and styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );

			// Check if is shortcode my-account.
			add_action( 'template_redirect', array( $this, 'check_myaccount' ), 1 );
			// Redirect to the default endpoint.
			add_action( 'template_redirect', array( $this, 'redirect_to_default' ), 150 );
			add_action( 'template_redirect', array( $this, 'is_wc_memberships_teams' ), 200 );
			// Add new navigation.
			add_action( 'woocommerce_account_navigation', array( $this, 'add_my_account_menu' ), 10 );
			// Manage account content.
			add_action( 'woocommerce_account_content', array( $this, 'manage_account_content' ), 1 );
			// Change title.
			add_action( 'template_redirect', array( $this, 'manage_account_title' ), 10 );

			// Shortcode for print my account menu.
			add_shortcode( self::SHORTCODE_NAME, array( $this, 'my_account_menu' ) );

			// Shortcodes for my-downloads and view order content.
			add_shortcode( 'my_downloads_content', array( $this, 'my_downloads_content' ) );
			add_shortcode( 'view_order_content', array( $this, 'view_order_content' ) );

			// Shortcode to print default dashboard.
			add_shortcode( 'default_dashboard_content', array( $this, 'print_default_dashboard_content' ) );

			// Memorize if is my account page.
			add_action( 'shutdown', array( $this, 'save_is_my_account' ) );
			// Prevent redirect to dashboard in Customize section using Smart Email plugin.
			add_filter( 'yith_wcmap_no_redirect_to_default', array( $this, 'fix_issue_with_smartemail_plugin' ) );

			add_action( 'yith_wcmap_print_single_endpoint', array( $this, 'print_single_item' ), 10, 2 );
			add_action( 'yith_wcmap_print_endpoints_group', array( $this, 'print_items_group' ), 10, 2 );
		}

		/**
		 * Init plugins variable
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function init() {

			$this->menu_items = YITH_WCMAP()->items->get_items();

			// First register string for translations then remove disable.
			foreach ( $this->menu_items as $item => &$options ) {

				// Check if master is active.
				if ( ! $this->is_item_visible( $item, $options ) ) {
					unset( $this->menu_items[ $item ] );
					continue;
				}

				// Check if child is active.
				if ( isset( $options['children'] ) ) {
					foreach ( $options['children'] as $child_item => $child_options ) {

						if ( ! $this->is_item_visible( $child_item, $child_options ) ) {
							unset( $options['children'][ $child_item ] );
							continue;
						}

						// Get translated label.
						$options['children'][ $child_item ]['label'] = $this->get_string_translated( $child_item, $child_options['label'] );
						if ( ! empty( $child_options['url'] ) ) {
							$options['children'][ $child_item ]['url'] = $this->get_string_translated( $child_item . '_url', $child_options['url'] );
						}
						if ( ! empty( $child_options['content'] ) ) {
							$options['children'][ $child_item ]['content'] = $this->get_string_translated( $child_item . '_content', $child_options['content'] );
						}
					}
				}

				// Get translated label.
				$options['label'] = $this->get_string_translated( $item, $options['label'] );
				if ( ! empty( $options['url'] ) ) {
					$options['url'] = $this->get_string_translated( $item . '_url', $options['url'] );
				}
				if ( ! empty( $options['content'] ) ) {
					$options['content'] = $this->get_string_translated( $item . '_content', $options['content'] );
				}
			}

			// Remove theme sidebar.
			if ( defined( 'YIT' ) && YIT ) {
				remove_action( 'yit_content_loop', 'yit_my_account_template', 5 );
				// Also remove the my-account template.
				$my_account_id = wc_get_page_id( 'myaccount' );
				if ( 'my-account.php' === get_post_meta( $my_account_id, '_wp_page_template', true ) ) {
					update_post_meta( $my_account_id, '_wp_page_template', 'default' );
				}
			}

			// Remove standard woocommerce sidebar.
			$priority = has_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );
			if ( false !== $priority ) {
				remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation', $priority );
			}
		}


		/**
		 * Is the given item visible on frontend?
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @param string $item The item to check.
		 * @param array  $options The item options.
		 * @return boolean
		 */
		public function is_item_visible( $item, $options ) {
			$visible = true;
			// Check if master is active.
			if ( isset( $options['active'] ) && ! $options['active'] ) {
				$visible = false;
			}

			// Get current user and set user role.
			$current_user = wp_get_current_user();
			$user_role    = (array) $current_user->roles;

			if ( isset( $options['visibility'] ) && 'roles' === $options['visibility'] && isset( $options['usr_roles'] ) && $this->hide_by_usr_roles( $options['usr_roles'], $user_role ) ) {
				$visible = false;
			}

			return apply_filters( 'yith_wcmap_is_menu_item_visible', $visible, $item, $options );
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

			if ( apply_filters( 'yith_wcmap_my_account_have_menu', $this->my_account_have_menu ) ) {
				return;
			}

			echo do_shortcode( '[' . self::SHORTCODE_NAME . ']' );
			// Set my account menu variable. This prevent double menu.
			$this->my_account_have_menu = true;
		}

		/**
		 * Get current active endpoint
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return array
		 */
		protected function get_current_endpoint() {
			if ( empty( $this->current_endpoint ) ) {
				// Search for active endpoints.
				$active = yith_wcmap_get_current_endpoint();
				// Get active endpoint options by slug.
				$endpoint = yith_wcmap_get_endpoint_by( $active, 'key', $this->menu_items );

				if ( ! empty( $endpoint ) && is_array( $endpoint ) ) {
					$this->current_endpoint = array_shift( $endpoint );
				}
			}

			return $this->current_endpoint;
		}

		/**
		 * Manage endpoint account content based on plugin/endpoint options
		 *
		 * @since  3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function manage_account_content() {

			// Get active endpoint.
			$endpoint = $this->get_current_endpoint();
			if ( empty( $endpoint ) ) {
				return;
			}

			// Check in custom content.
			if ( ! empty( $endpoint['content'] ) ) {

				switch ( $endpoint['content_position'] ) {
					case 'before':
						add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ), 5 );
						break;
					case 'after':
						add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ), 15 );
						break;
					case 'override':
						remove_action( 'woocommerce_account_content', 'woocommerce_account_content' );
						add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ) );
						break;
				}
			}
		}

		/**
		 * Print the custom endpoint content
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function print_endpoint_content() {
			global $wp_embed;

			// Get active endpoint and double check for content.
			$endpoint = $this->get_current_endpoint();
			if ( empty( $endpoint ) || empty( $endpoint['content'] ) ) {
				return;
			}

			// add compatibility with WSDesk - WordPress Support Desk.
			if ( has_shortcode( $endpoint['content'], 'wsdesk_support' ) ) {
				$this->enqueue_wsdesk_scripts();
			}

			$customer = wp_get_current_user();
			$content  = stripslashes( $endpoint['content'] );
			$content  = str_replace( '%%customer_name%%', $customer->display_name, $content );
			if ( apply_filters( 'yith_wcmap_wpautop_content', true, $endpoint ) ) {
				$content = wpautop( $content );
			}
			$content = apply_filters( 'yith_wcmap_endpoint_content', $content, $endpoint, $customer );

			echo do_shortcode( $wp_embed->autoembed( $content ) );
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
			wp_enqueue_script( 'support_scripts', EH_CRM_MAIN_JS . 'crm_support.js' );
			wp_enqueue_style( 'slider', EH_CRM_MAIN_CSS . 'slider.css' );
			wp_enqueue_style( 'support_styles', EH_CRM_MAIN_CSS . 'crm_support.css' );
			wp_enqueue_style( 'new_styles', EH_CRM_MAIN_CSS . 'new-style.css' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui', EH_CRM_MAIN_CSS . 'jquery-ui.css' );
			$selected = eh_crm_get_settingsmeta( 0, 'selected_fields' );
			if ( empty( $selected ) ) {
				$selected = array();
			}
			if ( in_array( 'google_captcha', $selected ) ) {
				$my_current_lang = apply_filters( 'wpml_current_language', null );
				wp_enqueue_script( 'captcha_scripts', 'https://www.google.com/recaptcha/api.js?hl=' . $my_current_lang );
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

			global $post;

			// Get active endpoint.
			$endpoint = $this->get_current_endpoint();
			if ( empty( $endpoint ) || empty( $post ) ) {
				return;
			}

			// Set endpoint title.
			if ( ! empty( $endpoint['label'] ) && 'dashboard' !== $endpoint['slug'] ) {
				$post->post_title = stripslashes( $endpoint['label'] );
			}

			$post->post_title = apply_filters( 'yith_wcmap_account_page_title', $post->post_title, $endpoint );
		}

		/**
		 * Get a translated string
		 *
		 * @access protected
		 * @since  2.3.0
		 * @author Francesco Licandro
		 * @param string $key The string key.
		 * @param string $value The string value.
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
		 * @param array $roles The roles valid.
		 * @param array $current_user_role The customer roles.
		 * @return boolean
		 */
		protected function hide_by_usr_roles( $roles, $current_user_role ) {
			// Return if $roles is empty.
			if ( apply_filters( 'yith_wcmap_skip_check_for_administrators', true ) && ( empty( $roles ) || current_user_can( 'administrator' ) ) ) {
				return false;
			}

			// Check if current user can.
			$intersect = array_intersect( $roles, $current_user_role );
			if ( ! empty( $intersect ) ) {
				return false;
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

			if ( ! $this->is_myaccount ) {
				return;
			};

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_style( 'ywcmap-frontend', YITH_WCMAP_ASSETS_URL . '/css/ywcmap-frontend.css', array(), YITH_WCMAP_VERSION );
			wp_register_script( 'ywcmap-frontend', YITH_WCMAP_ASSETS_URL . '/js/ywcmap-frontend' . $suffix . '.js', array( 'jquery', 'wp-util' ), YITH_WCMAP_VERSION, true );
			// Font awesome.
			wp_register_style( 'font-awesome', YITH_WCMAP_ASSETS_URL . '/css/font-awesome.min.css' );

			// ENQUEUE STYLE.
			wp_enqueue_style( 'ywcmap-frontend' );
			wp_enqueue_style( 'font-awesome' );

			$inline_css = yith_wcmap_get_custom_css();
			wp_add_inline_style( 'ywcmap-frontend', $inline_css );

			// Get AJAX Loader.
			$loader = YITH_WCMAP_ASSETS_URL . '/images/ajax-loader.gif';
			if ( 'custom' === get_option( 'yith_wcmap_ajax_loader_style', 'default' ) && get_option( 'yith_wcmap_ajax_loader_custom_icon', '' ) ) {
				$loader = esc_url( get_option( 'yith_wcmap_ajax_loader_custom_icon', '' ) );
			}

			// ENQUEUE SCRIPTS.
			wp_enqueue_script( 'ywcmap-frontend' );
			wp_localize_script(
				'ywcmap-frontend',
				'ywcmap',
				array(
					'ajaxNavigation'       => 'yes' === get_option( 'yith_wcmap_enable_ajax_navigation', 'no' ),
					'ajaxNavigationScroll' => apply_filters( 'yith_wcmap_enable_ajax_navigation_scroll', true ),
					'contentSelector'      => apply_filters( 'yith_wcmap_main_content_selector', '#content' ),
					'ajaxLoader'           => $loader,
				)
			);
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

			if ( ! is_null( $post ) && is_user_logged_in() && is_account_page() ) {
				$this->is_myaccount = true;
			}

			$this->is_myaccount = apply_filters( 'yith_wcmap_is_my_account_page', $this->is_myaccount );
		}

		/**
		 * Redirect to default endpoint
		 *
		 * @access public
		 * @since  1.0.4
		 * @author Francesco Licandro
		 */
		public function redirect_to_default() {

			// Exit if not my account.
			if ( ! $this->is_myaccount || ! is_array( $this->menu_items ) ) {
				return;
			}

			$current_endpoint = yith_wcmap_get_current_endpoint();
			// If a specific endpoint is required return.
			if ( $current_endpoint != 'dashboard' || apply_filters( 'yith_wcmap_no_redirect_to_default', false ) ) {
				return;
			}

			$default_endpoint = get_option( 'yith-wcmap-default-endpoint', 'dashboard' );
			// Let's third part filter default endpoint.
			$default_endpoint = apply_filters( 'yith_wcmap_default_endpoint', $default_endpoint );
			$url              = wc_get_page_permalink( 'myaccount' );

			// Otherwise if I'm not in my account yet redirect to default.
			if ( ! get_option( 'yith_wcmap_is_my_account', true ) && ! isset( $_REQUEST['elementor-preview'] ) && $current_endpoint !== $default_endpoint ) {
				$default_endpoint != 'dashboard' && $url = wc_get_endpoint_url( $default_endpoint, '', $url );
				wp_safe_redirect( $url );
				exit;
			}
		}

		/**
		 * Output my-account shortcode
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function my_account_menu() {

			$args = array(
				'current_user'   => wp_get_current_user(),
				'endpoints'      => $this->menu_items, // Leave endpoint key for backward compatibility.
				'my_account_url' => get_permalink( wc_get_page_id( 'myaccount' ) ),
				'logout_url'     => wc_logout_url(),
				'avatar_upload'  => YITH_WCMAP_Avatar::can_upload_avatar(),
				'avatar_size'    => YITH_WCMAP_Avatar::get_avatar_default_size(),
			);

			// Build wrap id and class
			$position = get_option( 'yith_wcmap_menu_position', 'vertical-left' );
			$layout   = get_option( 'yith_wcmap_menu_layout', 'simple' );
			$classes  = array(
				'position-' . $position,
				'layout-' . $layout,
				'position-' . ( 'vertical-left' === $position ? 'left' : 'right' ), // Backward compatibility.
			);

			$args['wrap_classes'] = implode( ' ', $classes );
			$args['wrap_id']      = 'horizontal' === $position ? 'my-account-menu-tab' : 'my-account-menu';
			// Let's filter the template args.
			$args = apply_filters( 'yith-wcmap-myaccount-menu-template-args', $args );

			ob_start();

			wc_get_template( 'ywcmap-myaccount-menu.php', $args, '', YITH_WCMAP_DIR . 'templates/' );

			return ob_get_clean();

		}

		/**
		 * Print my-downloads endpoint content
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param array $atts Shortcode attributes.
		 */
		public function my_downloads_content( $atts ) {

			$template = apply_filters( 'yith_wcmap_downloads_shortcode_template', 'myaccount/downloads.php' );

			ob_start();
			wc_get_template( $template );
			$content = ob_get_clean();

			// Print message if no downloads.
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
		 * @param array $atts Shortcode attributes.
		 */
		public function print_default_dashboard_content( $atts ) {

			$template_name = 'myaccount/dashboard.php';
			$template      = apply_filters( 'yith_wcmap_dashboard_shortcode_template', $template_name );

			ob_start();
			wc_get_template(
				$template,
				array(
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
		 * @param array $atts Shortcode attributes.
		 */
		public function view_order_content( $atts ) {

			global $wp;

			$endpoint = yith_wcmap_get_endpoint_by( 'orders', 'key', $this->menu_items );

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
					// Backwards compatibility.
					$status       = new stdClass();
					$status->name = wc_get_order_status_name( $order->get_status() );

					ob_start();
					wc_get_template(
						'myaccount/view-order.php',
						array(
							'status'   => $status, // @deprecated 2.2
							'order'    => wc_get_order( $order_id ),
							'order_id' => $order_id,
						)
					);
					$content = ob_get_clean();
				}
			} else {
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				ob_start();
				woocommerce_account_orders( $paged );
				$content = ob_get_clean();
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
			update_option( 'yith_wcmap_is_my_account', $this->is_myaccount );
		}

		/**
		 * Prevent redirect to dashboard in Customize section using Smart Email plugin
		 *
		 * @param boolean $value True for redirect, false otherwise.
		 * @return bool
		 */
		public function fix_issue_with_smartemail_plugin( $value ) {
			if ( isset( $_GET['sa_smart_emails'] ) ) {
				$value = true;
			}

			return $value;
		}

		/**
		 * Retrieve the complete list of endpoints
		 *
		 * @return string
		 * @deprecated Use instead get_menu_items
		 */
		public function get_menu_endpoints() {
			return $this->menu_items;
		}

		/**
		 * Retrieve the complete list of endpoints
		 *
		 * @return string
		 * @deprecated Use instead get_menu_items
		 */
		public function get_menu_items() {
			return $this->menu_items;
		}

		/**
		 * Check if is my account
		 *
		 * @return string
		 */
		public function is_my_account() {
			return $this->is_myaccount;
		}

		/**
		 * Add custom style for YITH Proteo theme
		 *
		 * @param string $style YITH Proteo style.
		 * @return mixed
		 * @deprecated
		 */
		public function add_proteo_style( $style ) {
			return $style;
		}

		/**
		 * Print single item on front menu
		 *
		 * @since  3.0.0
		 * @author Francesco Licandro
		 * @param string $item The item to print.
		 * @param array  $options The item options.
		 */
		public function print_single_item( $item, $options ) {

			global $wp_query;

			$classes = array();
			// Set item classes.
			if ( ! empty( $options['class'] ) ) {
				$classes[] = $options['class'];
			}

			// Get current endpoint.
			$current = yith_wcmap_get_current_endpoint();
			if ( $item === $current ) {
				$classes[] = 'active';
			}

			if ( ! isset( $options['url'] ) ) {
				$url = get_permalink( wc_get_page_id( 'myaccount' ) );
				if ( 'dashboard' !== $item ) {
					$url = wc_get_endpoint_url( $item, '', $url );
				}
				// Set AJAX class
				if ( 'yes' === get_option( 'yith_wcmap_enable_ajax_navigation', 'no' ) ) {
					$classes[] = 'has-ajax-navigation';
				}
			} else {
				$url = esc_url( $options['url'] );
			}

			if ( ! in_array( 'active', $classes, true ) ) {
				// Maybe set special active class for internal endpoints.
				if ( ( 'orders' === $item && $current === get_option( 'woocommerce_myaccount_view_order_endpoint', 'view-order' ) )
					 || ( 'refund-requests' === $item && class_exists( 'YITH_Advanced_Refund_System_My_Account' ) && isset( $wp_query->query_vars[ YITH_Advanced_Refund_System_My_Account::$view_request_endpoint ] ) )
					 || ( 'payment-methods' === $item && in_array( $current, array( 'add-payment-method', 'delete-payment-method', 'set-default-payment-method' ), true ) ) ) {

					$classes[] = 'active';
				}
			}

			// Let's filter only item classes.
			$classes = apply_filters( 'yith_wcmap_endpoint_menu_class', $classes, $item, $options );
			// Build args array.
			$args = apply_filters(
				'yith_wcmap_print_single_endpoint_args',
				array(
					'url'      => $url,
					'endpoint' => $item,
					'options'  => $options,
					'classes'  => $classes,
				)
			);

			wc_get_template( 'ywcmap-myaccount-menu-item.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
		}

		/**
		 * Print items group on front menu
		 *
		 * @since  3.0.0
		 * @author Francesco Licandro
		 * @param string $group The items group to print.
		 * @param array  $options The items group options.
		 */
		public function print_items_group( $group, $options ) {

			$classes = array( 'group-' . $group );
			$current = yith_wcmap_get_current_endpoint();

			if ( ! empty( $options['class'] ) ) {
				$classes[] = $options['class'];
			}

			// Options for style tab.
			if ( 'horizontal' === get_option( 'yith_wcmap_menu_position', 'vertical-left' ) ) {
				// Force option open to true.
				$options['open'] = false;
			} else {
				// Check in child and add class active.
				foreach ( $options['children'] as $child_key => $child ) {
					if ( isset( $child['slug'] ) && $child_key === $current && WC()->query->get_current_endpoint() ) {
						$options['open'] = true;
						break;
					}
				}
			}

			$class_icon = $options['open'] ? 'fa-chevron-up' : 'fa-chevron-down';
			$classes    = apply_filters( 'yith_wcmap_endpoints_group_class', $classes, $group, $options );

			// Build args array.
			$args = apply_filters(
				'yith_wcmap_print_endpoints_group_group',
				array(
					'options'    => $options,
					'classes'    => $classes,
					'class_icon' => $class_icon,
				)
			);

			wc_get_template( 'ywcmap-myaccount-menu-group.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
		}
	}
}
