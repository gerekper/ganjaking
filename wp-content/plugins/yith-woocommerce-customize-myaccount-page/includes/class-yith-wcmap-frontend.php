<?php
/**
 * Frontend class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Frontend', false ) ) {
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
		 * @const  string
		 */
		const SHORTCODE_NAME = 'yith-wcmap-menubar';

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
			// Plugin frontend init.
			// Polylang require the pll_language_defined action to be sure that the languages are defined before showing the content
				add_action( 'pll_language_defined', array( $this, 'init' ) );
				add_action( 'init', array( $this, 'init' ), 100 );


			// Enqueue scripts and styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );
			// Check if is shortcode my-account.
			add_action( 'template_redirect', array( $this, 'check_myaccount' ), 1 );
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
			add_action( 'yith_wcmap_print_single_endpoint', array( $this, 'print_single_item' ), 10, 2 );
		}

		/**
		 * Init plugins variable
		 *
		 * @access public
		 * @since  1.0.0
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

				// Get translated label.
				$options['label'] = $this->get_string_translated( $item, $options['label'] );

				if ( ! empty( $options['content'] ) ) {
					$options['content'] = $this->get_string_translated( $item . '_content', $options['content'] );
				}
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_menu_items_initialized
			 *
			 * Filters the menu items.
			 *
			 * @param array $menu_items Menu items.
			 *
			 * @return array
			 */
			$this->menu_items = apply_filters( 'yith_wcmap_menu_items_initialized', $this->menu_items );

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
		 * @since  3.0.0
		 * @param string $item    The item to check.
		 * @param array  $options The item options.
		 * @return boolean
		 */
		public function is_item_visible( $item, $options ) {
			$visible = true;
			// Check if master is active.
			if ( isset( $options['active'] ) && ! $options['active'] ) {
				$visible = false;
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_is_menu_item_visible
			 *
			 * Filters whether the menu item is visible.
			 *
			 * @param bool   $visible Whether the menu item is visible or not.
			 * @param string $item    Item key.
			 * @param array  $options Item options.
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcmap_is_menu_item_visible', $visible, $item, $options );
		}

		/**
		 * Add plugin menu to My Account shortcode
		 *
		 * @since  2.4.0
		 * @return void
		 */
		public function add_my_account_menu() {
			/**
			 * APPLY_FILTERS: yith_wcmap_my_account_have_menu
			 *
			 * Filters whether the My Account page has menu.
			 *
			 * @param bool $my_account_have_menu Whether the My Account page has menu or not.
			 *
			 * @return bool
			 */
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
		 * @since  3.0.0
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
				$endpoint_key = yith_wcmap_get_current_endpoint();
				$is_default   = yith_wcmap_is_plugin_item( $endpoint_key ) || yith_wcmap_is_default_item( $endpoint_key );

				if ( ! $is_default ) {
					add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ), 20 );
				} else {
					remove_action( 'woocommerce_account_content', 'woocommerce_account_content' );
					add_action( 'woocommerce_account_content', array( $this, 'print_endpoint_content' ) );
				}
			}
		}

		/**
		 * Print the custom endpoint content
		 *
		 * @since  3.0.0
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

			// add compatibility with Notes for LearnDash plugin.
			if ( has_shortcode( $endpoint['content'], 'learndash_my_notes' ) || has_shortcode( $endpoint['content'], 'my_notes' ) ) {
				$this->register_learndash_notes_hooks();
			}

			$customer = wp_get_current_user();
			$content  = stripslashes( $endpoint['content'] );
			$content  = str_replace( '%%customer_name%%', apply_filters( 'yith_wcmap_user_name_in_menu', $customer->display_name, $customer ), $content );

			/**
			 * APPLY_FILTERS: yith_wcmap_wpautop_content
			 *
			 * Filters whether to replace double line breaks with paragraph elements in the endpoint content.
			 *
			 * @param bool  $wpautop_content Whether to replace double line breaks with paragraph elements or not.
			 * @param array $endpoint        Endpoint.
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_wcmap_wpautop_content', true, $endpoint ) ) {
				$content = wpautop( $content );
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_endpoint_content
			 *
			 * Filters the endpoint content.
			 *
			 * @param string $content   Endpoint content.
			 * @param array  $endpoint  Endpoint.
			 * @param WP_User $customer User object.
			 *
			 * @return string
			 */
			$content = apply_filters( 'yith_wcmap_endpoint_content', $content, $endpoint, $customer );

			echo do_shortcode( $wp_embed->autoembed( $content ) );
		}

		/**
		 * Register Notes for LearnDash hooks and filters for compatibility.
		 *
		 * @since  3.9.0
		 * @return void
		 */
		protected function register_learndash_notes_hooks() {
			add_filter( 'ldnt_my_notes_shortcode_args', array( $this, 'custom_ldnt_my_notes_shortcode_args' ), 10, 1 );
			// Pagination.
			add_filter( 'get_pagenum_link', array( $this, 'filter_ldnt_my_notes_shortcode_pagination' ), 10, 2 );
		}

		/**
		 * Customize shortcode [learndash_my_notes] by Notes for LearnDash query arguments.
		 *
		 * @since  3.9.0
		 * @param array $args The current shortcode query arguments.
		 * @return array
		 */
		public function custom_ldnt_my_notes_shortcode_args( $args ) {
			global $paged, $wp;
			$endpoint      = $this->get_current_endpoint();
			$endpoint_slug = $endpoint['slug'];
			// Set global variable to use get_next_posts_link and get_previous_posts_link WP functions.
			$paged         = ! empty( $wp->query_vars[ $endpoint_slug ] ) ? absint( $wp->query_vars[ $endpoint_slug ] ) : 1;
			$args['paged'] = $paged;
			return $args;
		}

		/**
		 * Customize shortcode [learndash_my_notes] by Notes for LearnDash pagination.
		 *
		 * @since  3.9.0
		 * @param string $url     The current pagination url.
		 * @param string $pagenum The pagenum to get pagination for.
		 * @return string
		 */
		public function filter_ldnt_my_notes_shortcode_pagination( $url, $pagenum ) {
			global $post;
			if ( $post && 'coursenote' === $post->post_type ) {
				$endpoint      = $this->get_current_endpoint();
				$endpoint_slug = $endpoint['slug'];

				$url = wc_get_endpoint_url( $endpoint_slug, $pagenum, wc_get_page_permalink( 'myaccount' ) );
			}

			return $url;
		}

		/**
		 * Enqueue scripts for WSDesk - WordPress Support Desk
		 *
		 * @since  2.5.1
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

			/**
			 * APPLY_FILTERS: yith_wcmap_account_page_title
			 *
			 * Filters the title of the My Account page.
			 *
			 * @param string $page_title Page title.
			 * @param array  $endpoint   Endpoint.
			 *
			 * @return string
			 */
			$post->post_title = apply_filters( 'yith_wcmap_account_page_title', $post->post_title, $endpoint );
		}

		/**
		 * Get a translated string
		 *
		 * @access protected
		 * @since  2.3.0
		 * @param string $key   The string key.
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
		 * Enqueue scripts and styles
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			if ( ! $this->is_myaccount ) {
				return;
			};

			$min = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? '.min' : '';
			wp_enqueue_style( 'ywcmap-frontend', YITH_WCMAP_ASSETS_URL . '/css/ywcmap-frontend' . $min . '.css', array(), YITH_WCMAP_VERSION );

			$inline_css = $this->get_custom_css();
			if ( ! empty( $inline_css ) ) {
				wp_add_inline_style( 'ywcmap-frontend', $inline_css );
			}
		}

		/**
		 * Get custom frontend CSS
		 *
		 * @since  3.12.0
		 * @return string
		 */
		public function get_custom_css() {
			// Init variables.
			$variables = array();

			// Logout button colors.
			$variables['logout-text-color']             = '#ffffff';
			$variables['logout-text-color-hover']       = '#ffffff';
			$variables['logout-background-color']       = '#c0c0c0';
			$variables['logout-background-color-hover'] = '#333333';
			// Menu items colors.
			$variables['items-text-color']        = '#777777';
			$variables['items-text-color-hover']  = '#000000';
			$variables['items-text-color-active'] = '#000000';
			// Menu items background.
			$variables['items-background-color']        = '#ffffff';
			$variables['items-background-color-hover']  = '#ffffff';
			$variables['items-background-color-active'] = '#ffffff';
			// Menu font size.
			$variables['font-size'] = '16px';
			// Menu background.
			$variables['menu-background'] = '#f4f4f4';
			// Menu border color.
			$variables['menu-border-color'] = '#e0e0e0';
			// Modern menu border color yith_wcmap_menu_item_shadow_color.
			$variables['items-border-color']        = '#eaeaea';
			$variables['items-border-color-hover']  = '#cceae9';
			$variables['items-border-color-active'] = '#cceae9';
			// Modern menu shadow color yith_wcmap_menu_item_shadow_color.
			$variables['items-shadow-color']        = 'rgba(114, 114, 114, 0.16)';
			$variables['items-shadow-color-hover']  = 'rgba(3,163,151,0.16)';
			$variables['items-shadow-color-active'] = 'rgba(3,163,151,0.16)';
			// Avatar style.
			$variables['avatar-border-radius'] = '0';
			// Items padding.
			$variables['menu-items-padding'] = '12px 5px';

			/**
			 * APPLY_FILTERS: yith_wcmap_custom_css_variables
			 *
			 * Filters the custom CSS variables.
			 *
			 * @param array $variables Custom CSS variables.
			 *
			 * @return array
			 */
			$variables = apply_filters( 'yith_wcmap_custom_css_variables', array_filter( $variables ) );
			if ( empty( $variables ) ) {
				return '';
			}

			$inline_css = ':root {';
			foreach ( $variables as $key => $value ) {
				$inline_css .= '--ywcmap-' . $key . ': ' . $value . ';';
			}
			$inline_css .= '}';
			// Remove whitespaces and line breaks.
			$inline_css = trim( preg_replace( '/\s\s+/', ' ', $inline_css ) );

			/**
			 * APPLY_FILTERS: yith_wcmap_get_custom_css
			 *
			 * Filters the custom CSS rules.
			 *
			 * @param string $inline_css Custom CSS rules.
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcmap_get_custom_css', $inline_css );
		}

		/**
		 * Check if is page my-account and set class variable
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function check_myaccount() {
			global $post;

			if ( ! is_null( $post ) && is_user_logged_in() && is_account_page() ) {
				$this->is_myaccount = true;
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_is_my_account_page
			 *
			 * Filters whether the current page is the My Account page.
			 *
			 * @param bool $is_myaccount Whether is the My Account page or not.
			 *
			 * @return bool
			 */
			$this->is_myaccount = apply_filters( 'yith_wcmap_is_my_account_page', $this->is_myaccount );
		}

		/**
		 * Output my-account shortcode
		 *
		 * @since  1.0.0
		 */
		public function my_account_menu() {

			$args = array(
				'current_user'   => wp_get_current_user(),
				'endpoints'      => $this->menu_items, // Leave endpoint key for backward compatibility.
				'my_account_url' => get_permalink( wc_get_page_id( 'myaccount' ) ),
				'logout_url'     => wc_logout_url(),
				'wrap_classes'   => 'position-vertical-left layout-simple position-left',
				'wrap_id'        => 'my-account-men',
				'avatar_size'    => 120,
			);

			// Let's filter the template args.
			if ( has_filter( 'yith-wcmap-myaccount-menu-template-args' ) ) {
				$args = apply_filters_deprecated( 'yith-wcmap-myaccount-menu-template-args', array( $args ), '3.12.0', 'yith_wcmap_myaccount_menu_template_args' );
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_myaccount_menu_template_args
			 *
			 * Filters the array with the arguments needed for the menu template.
			 *
			 * @param array $args Array with arguments.
			 *
			 * @return array
			 */
			$args = apply_filters( 'yith_wcmap_myaccount_menu_template_args', $args );

			ob_start();
			wc_get_template( 'ywcmap-myaccount-menu.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
			return ob_get_clean();

		}

		/**
		 * Print my-downloads endpoint content
		 *
		 * @access public
		 * @since  1.0.0
		 * @param array $atts Shortcode attributes.
		 */
		public function my_downloads_content( $atts ) {
			/**
			 * APPLY_FILTERS: yith_wcmap_downloads_shortcode_template
			 *
			 * Filters the template name for the Downloads endpoint.
			 *
			 * @param string $template_name Template name.
			 *
			 * @return string
			 */
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
		 * @param array $atts Shortcode attributes.
		 */
		public function print_default_dashboard_content( $atts ) {

			$template_name = 'myaccount/dashboard.php';

			/**
			 * APPLY_FILTERS: yith_wcmap_dashboard_shortcode_template
			 *
			 * Filters the template name for the Dashboard endpoint.
			 *
			 * @param string $template_name Template name.
			 *
			 * @return string
			 */
			$template = apply_filters( 'yith_wcmap_dashboard_shortcode_template', $template_name );

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
					$content = '<div class="woocommerce-error">' . __( 'Invalid order.', 'woocommerce' ) . ' <a href="' . wc_get_page_permalink( 'myaccount' ) . '" class="wc-forward">' . __( 'My Account', 'woocommerce' ) . '</a></div>';

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
		 */
		public function save_is_my_account() {
			update_option( 'yith_wcmap_is_my_account', $this->is_myaccount );
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
		 * @param string $item    The item to print.
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

			$url = get_permalink( wc_get_page_id( 'myaccount' ) );
			if ( 'dashboard' !== $item ) {
				$url = wc_get_endpoint_url( $item, '', $url );
			}

			if ( ! in_array( 'active', $classes, true ) ) {
				// Maybe set special active class for internal endpoints.
				if ( ( 'orders' === $item && get_option( 'woocommerce_myaccount_view_order_endpoint', 'view-order' ) === $current )
					|| ( 'refund-requests' === $item && class_exists( 'YITH_Advanced_Refund_System_My_Account' ) && isset( $wp_query->query_vars[ YITH_Advanced_Refund_System_My_Account::$view_request_endpoint ] ) )
					|| ( 'payment-methods' === $item && in_array( $current, array( 'add-payment-method', 'delete-payment-method', 'set-default-payment-method' ), true ) ) ) {

					$classes[] = 'active';
				}
			}

			// Let's filter only item classes.
			/**
			 * APPLY_FILTERS: yith_wcmap_endpoint_menu_class
			 *
			 * Filters the CSS classes for the endpoint in the menu.
			 *
			 * @param array  $classes  CSS classes.
			 * @param string $item    Item key.
			 * @param array  $options Item options.
			 *
			 * @return array
			 */
			$classes = apply_filters( 'yith_wcmap_endpoint_menu_class', $classes, $item, $options );

			// Build args array.
			/**
			 * APPLY_FILTERS: yith_wcmap_print_single_endpoint_args
			 *
			 * Filters the array of arguments needed to print the endpoint.
			 *
			 * @param array  $args    Array of arguments.
			 * @param string $item    Item key.
			 * @param array  $options Item options.
			 *
			 * @return array
			 */
			$args = apply_filters(
				'yith_wcmap_print_single_endpoint_args',
				array(
					'url'      => $url,
					'endpoint' => $item,
					'options'  => $options,
					'classes'  => $classes,
				),
				$item,
				$options
			);

			wc_get_template( 'ywcmap-myaccount-menu-item.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
		}
	}
}
