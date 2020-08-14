<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WooCommerce_Catalog_Mode' ) ) {

	/**
	 * Implements features of YITH WooCommerce Catalog Mode plugin
	 *
	 * @class   YITH_WooCommerce_Catalog_Mode
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YITH_WooCommerce_Catalog_Mode {

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
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-catalog-mode/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-catalog-mode/';

		/**
		 * @var string Yith WooCommerce Catalog Mode panel page
		 */
		protected $_panel_page = 'yith_wc_catalog_mode_panel';

		/**
		 * Single instance of the class
		 *
		 * @since 1.3.0
		 * @var YITH_WooCommerce_Catalog_Mode
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WooCommerce_Catalog_Mode
		 * @since 1.3.0
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

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YWCTM_DIR . '/' . basename( YWCTM_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );

			$this->include_files();

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_fields' ), 10, 2 );
			add_action( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_custom_field' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_admin' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'yith_catalog_mode_premium', array( $this, 'premium_tab' ) );

			if ( ! is_admin() || $this->is_quick_view() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

				add_action( 'init', array( $this, 'check_disable_shop' ), 11 );
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'hide_add_to_cart_loop' ), 5 );
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'avoid_add_to_cart' ), 10, 2 );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_frontend' ) );
				add_filter( 'ywctm_css_classes', array( $this, 'hide_atc_single_page' ) );
				add_filter( 'ywctm_css_classes', array( $this, 'hide_cart_widget' ) );

				if ( defined( 'YITH_WCWL' ) && YITH_WCWL ) {
					add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_wishlist' ), 10, 2 );
				}
			}

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function include_files() {

			//Check if options should be upgraded
			$update_path = YWCTM_DIR . 'includes/actions/update-2.0.0/ywctm-install.php';
			if ( ( '' === get_option( 'ywctm_update_version' ) || YWCTM_VERSION === get_transient( 'ywctm_prune_settings' ) ) && file_exists( $update_path ) ) {
				include_once( $update_path );
			}

			include_once( 'includes/ywctm-functions.php' );

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Initialize custom fields
		 *
		 * @param   $path  string
		 * @param   $field array
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_custom_fields( $path, $field ) {

			$custom_fields = array(
				'yith-multiple-field',
			);

			if ( in_array( $field['type'], $custom_fields, true ) ) {
				$path = YWCTM_DIR . '/includes/admin/fields/' . $field['type'] . '.php';
			}

			return $path;

		}

		/**
		 * Sanitize array fields
		 *
		 * @param   $value  mixed
		 * @param   $option array
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function sanitize_custom_field( $value, $option ) {

			$allowed_fields = array(
				'yith-multiple-field',
			);

			if ( isset( $option['yith-type'] ) && in_array( $option['yith-type'], $allowed_fields, true ) ) {
				if ( empty( $value ) ) {
					$value = '';
				} elseif ( is_array( $value ) ) {
					$value = maybe_serialize( $value );
				}
			}

			return $value;

		}

		/**
		 * Enqueue script file
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_scripts_admin() {

			wp_register_style( 'ywctm-admin', yit_load_css_file( YWCTM_ASSETS_URL . 'css/admin.css' ), array(), YWCTM_VERSION );

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] === $this->_panel_page || 'yith_vendor_ctm_settings' === $_GET['page'] ) ) {
				wp_enqueue_style( 'ywctm-admin' );
			}

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

			if ( defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM ) {

				$admin_tabs = array(
					'premium-settings' => esc_html_x( 'Settings', 'general settings tab name', 'yith-woocommerce-catalog-mode' ),
					'exclusions'       => esc_html_x( 'Exclusion List', 'exclusion settings tab name', 'yith-woocommerce-catalog-mode' ),
					'inquiry-form'     => esc_html_x( 'Inquiry Form', 'inquiry form settings tab name', 'yith-woocommerce-catalog-mode' ),
					'buttons-labels'   => esc_html_x( 'Buttons & Labels', 'buttons & labels settings tab name', 'yith-woocommerce-catalog-mode' ),
				);

			} else {

				$admin_tabs = array(
					'settings' => esc_html__( 'Settings', 'yith-woocommerce-catalog-mode' ),
					'premium'  => esc_html__( 'Premium Version', 'yith-woocommerce-catalog-mode' ),
				);

			}

			$args = array(
				'create_menu_page' => true,
				'plugin_slug'      => YWCTM_SLUG,
				'parent_slug'      => '',
				'page_title'       => 'Catalog Mode',
				'menu_title'       => 'Catalog Mode',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCTM_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Check if shop must be disabled
		 *
		 * @return  void
		 * @since   2.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_disable_shop() {
			if ( $this->disable_shop() ) {
				$priority = has_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ) );
				remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), $priority );
				add_filter( 'get_pages', array( $this, 'hide_cart_checkout_pages' ) );
				add_filter( 'wp_get_nav_menu_items', array( $this, 'hide_cart_checkout_pages' ) );
				add_filter( 'wp_nav_menu_objects', array( $this, 'hide_cart_checkout_pages' ) );
				add_action( 'wp', array( $this, 'check_pages_redirect' ) );
			}
		}

		/**
		 * Check if catalog mode is enabled for administrator
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_user_admin_enable() {

			$vendor_id = ( defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM ) ? ywctm_get_vendor_id() : '';

			return ( ( current_user_can( 'administrator' ) || current_user_can( 'manage_vendor_store' ) ) && is_user_logged_in() && ( 'no' === get_option( 'ywctm_admin_view' . $vendor_id ) ) );
		}

		/**
		 * Removes Cart and checkout pages from menu
		 *
		 * @param   $pages array
		 *
		 * @return  array
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_cart_checkout_pages( $pages ) {

			$excluded_pages = array(
				wc_get_page_id( 'cart' ),
				wc_get_page_id( 'checkout' ),
			);

			foreach ( $pages as $key => $page ) {

				$page_id = ( in_array( current_filter(), array( 'wp_get_nav_menu_items', 'wp_nav_menu_objects' ), true ) ? $page->object_id : $page->ID );

				if ( in_array( (int) $page_id, $excluded_pages, true ) ) {
					unset( $pages[ $key ] );

				}
			}

			return $pages;

		}

		/**
		 * Checks if "Cart & Checkout pages" needs to be hidden
		 *
		 * @return  boolean
		 * @since   1.0.2
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_hide_cart_checkout_pages() {

			return $this->check_user_admin_enable() && $this->disable_shop();

		}

		/**
		 * Avoid Cart and Checkout Pages to be visited
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_pages_redirect() {

			$cart     = is_page( wc_get_page_id( 'cart' ) );
			$checkout = is_page( wc_get_page_id( 'checkout' ) );

			wp_reset_query();

			if ( $cart || $checkout ) {
				wp_redirect( home_url() );
				exit;
			}

		}

		/**
		 * Disable Shop
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function disable_shop() {

			$disabled = false;

			if ( get_option( 'ywctm_disable_shop' ) === 'yes' ) {

				global $post;

				$post_id = isset( $post ) ? $post->ID : '';

				if ( ywctm_is_wpml_active() && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
					$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
				}

				$disabled = $this->apply_catalog_mode( $post_id );

			}

			return $disabled;

		}

		/**
		 * Check if Catalog mode must be applied to current user
		 *
		 * @param   $post_id integer
		 *
		 * @return  boolean
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function apply_catalog_mode( $post_id ) {

			$apply = false;

			if ( ! $this->check_user_admin_enable() ) {
				$target_users = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_apply_users', 'all' ), $post_id, 'ywctm_apply_users' );

				$apply = 'all' === $target_users || ! is_user_logged_in();

				if ( is_callable( array( $this, 'country_check' ) ) ) {
					$apply = $this->country_check( $apply, $post_id );
				}
			}

			return apply_filters( 'ywctm_applied_roles', $apply, $post_id );

		}

		/**
		 * Hides "Add to cart" button, if not excluded, from loop page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_add_to_cart_loop() {

			$ywctm_modify_woocommerce_after_shop_loop_item = apply_filters( 'ywctm_modify_woocommerce_after_shop_loop_item', true );

			if ( $this->check_hide_add_cart() ) {

				if ( $ywctm_modify_woocommerce_after_shop_loop_item ) {
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}
				add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

			} else {

				if ( $ywctm_modify_woocommerce_after_shop_loop_item ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}
				remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

			}

		}

		/**
		 * Checks if "Add to cart" needs to be hidden
		 *
		 * @param   $single            $boolean
		 * @param   $product_id        integer|boolean
		 * @param   $ignore_variations boolean
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_hide_add_cart( $single = false, $product_id = false, $ignore_variations = false ) {

			$hide = false;

			if ( apply_filters( 'ywctm_prices_only_on_cart', false ) ) {
				return $hide;
			}

			if ( $this->disable_shop() ) {
				$hide = true;
			} else {

				global $post;

				if ( ! $product_id && ! isset( $post ) ) {
					return false;
				}

				$product_id = ( $product_id ) ? $product_id : $post->ID;

				if ( ywctm_is_wpml_active() && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
				}

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					return false;
				}

				$atc_settings_general = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_settings' ), $product_id, 'ywctm_hide_add_to_cart_settings' );
				$behavior             = $atc_settings_general['action'];
				$where                = $atc_settings_general['where'];
				$items                = $atc_settings_general['items'];
				$can_hide             = true;
				$exclusion            = false;

				if ( ! $single ) {
					$hide_variations = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_variations' ), $product_id, 'ywctm_hide_variations' );
					//APPLY_FILTERS: ywctm_hide_variations_on_loop: hide variations only on loop
					$hide_variations = apply_filters( 'ywctm_hide_variations_on_loop', $hide_variations );
					$is_variable     = $product->is_type( 'variable' );
					$is_grouped      = $product->is_type( 'grouped' );
					$can_hide        = ( ( $is_variable || $is_grouped ) ? 'yes' === $hide_variations : true );
				}

				if ( $ignore_variations ) {
					$can_hide = true;
				}

				if ( 'all' !== $items ) {
					$exclusion = apply_filters( 'ywctm_get_exclusion', ( 'hide' === $behavior ? 'show' : 'hide' ), $product_id, 'atc', $behavior );
				}

				if ( ! $single ) {

					switch ( true ) {
						case 'hide' === $behavior && 'all' === $where && 'all' === $items:
						case 'hide' === $behavior && 'shop' === $where && 'all' === $items:
						case 'show' === $behavior && 'product' === $where && 'all' === $items:
						case 'hide' === $behavior && 'all' === $where && 'all' !== $items && 'hide' === $exclusion:
						case 'hide' === $behavior && 'shop' === $where && 'all' !== $items && 'hide' === $exclusion:
						case 'show' === $behavior && 'product' === $where && 'all' !== $items:
						case 'show' === $behavior && 'shop' === $where && 'all' !== $items && 'hide' === $exclusion:
						case 'show' === $behavior && 'all' === $where && 'all' !== $items && 'hide' === $exclusion:
							$hide_add_to_cart = true;
							break;
						default:
							$hide_add_to_cart = false;
					}
				} else {

					switch ( true ) {
						case 'hide' === $behavior && 'all' === $where && 'all' === $items:
						case 'hide' === $behavior && 'product' === $where && 'all' === $items:
						case 'show' === $behavior && 'shop' === $where && 'all' === $items:
						case 'hide' === $behavior && 'all' === $where && 'all' !== $items && 'hide' === $exclusion:
						case 'hide' === $behavior && 'product' === $where && 'all' !== $items && 'hide' === $exclusion:
						case 'show' === $behavior && 'shop' === $where && 'all' !== $items:
						case 'show' === $behavior && 'product' === $where && 'all' !== $items && 'hide' === $exclusion:
						case 'show' === $behavior && 'all' === $where && 'all' !== $items && 'hide' === $exclusion:
							$hide_add_to_cart = true;
							break;
						default:
							$hide_add_to_cart = false;
					}
				}

				//Set "Add to cart" button as hidden
				if ( $hide_add_to_cart && $this->apply_catalog_mode( $product_id ) && $can_hide ) {
					$hide = true;
				}

				//If "Add to cart" button is set as visible but price is hidden then hide it anyway
				if ( apply_filters( 'ywctm_check_price_hidden', false, $product_id ) && $can_hide ) {
					$hide = true;
				}

				if ( ! $single ) {
					$hide = apply_filters( 'ywctm_hide_on_loop_anyway', $hide, $product_id );
				} else {
					$hide = apply_filters( 'ywctm_hide_on_single_anyway', $hide, $product_id );
				}
			}

			return $hide;

		}

		/**
		 * Add plugin CSS rules if needed
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_styles_frontend() {

			//APPLY_FILTERS: ywctm_css_classes: CSS selector of elements that should be hidden
			$classes = apply_filters( 'ywctm_css_classes', array() );

			if ( ! empty( $classes ) ) {
				wp_enqueue_style( 'ywctm-frontend', yit_load_css_file( YWCTM_ASSETS_URL . 'css/frontend.css' ), array(), YWCTM_VERSION );
				$css = implode( ', ', $classes ) . '{display: none !important}';
				wp_add_inline_style( 'ywctm-frontend', $css );
			}

		}

		/**
		 * Hide cart widget if needed
		 *
		 * @param   $classes array
		 *
		 * @return  array
		 * @since   1.3.7
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_cart_widget( $classes ) {

			if ( $this->disable_shop() ) {

				$args = array(
					'.widget.woocommerce.widget_shopping_cart',
				);

				$theme_name = ywctm_get_theme_name();

				if ( 'storefront' === $theme_name ) {
					$args[] = '.site-header-cart.menu';
				}
				//APPLY_FILTERS: ywctm_cart_widget_classes: CSS selector of cart widgets
				$classes = array_merge( $classes, apply_filters( 'ywctm_cart_widget_classes', $args ) );

			}

			return $classes;

		}

		/**
		 * Hides "Add to cart" button from single product page
		 *
		 * @param   $classes array
		 *
		 * @return  array
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_atc_single_page( $classes ) {

			if ( $this->check_hide_add_cart( true ) && is_singular() ) {

				$hide_variations = get_option( 'ywctm_hide_variations' );

				$args = array(
					'form.cart button.single_add_to_cart_button',
				);

				if ( ! class_exists( 'YITH_YWRAQ_Frontend' ) || ( ( class_exists( 'YITH_Request_Quote_Premium' ) ) && ! YITH_Request_Quote_Premium()->check_user_type() ) ) {
					$args[] = 'form.cart .quantity';
				}

				if ( 'yes' === $hide_variations ) {
					$args[] = 'table.variations';
					$args[] = 'form.variations_form';
					$args[] = '.single_variation_wrap .variations_button';
				}

				//APPLY_FILTERS: ywctm_cart_widget_classes: CSS selector of add to cart buttons
				$classes = array_merge( $classes, apply_filters( 'ywctm_catalog_classes', $args ) );

			}

			return $classes;

		}

		/**
		 * Checks if "Add to cart" needs to be avoided
		 *
		 * @param   $passed     boolean
		 * @param   $product_id integer
		 *
		 * @return  boolean
		 * @since   1.0.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function avoid_add_to_cart( $passed, $product_id ) {

			if ( apply_filters( 'ywctm_prices_only_on_cart', false ) ) {
				return $passed;
			}

			if ( $this->disable_shop() ) {

				$passed = false;

			} else {

				if ( ywctm_is_wpml_active() && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
				}

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					return true;
				}

				$atc_settings_general = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_settings' ), $product_id, 'ywctm_hide_add_to_cart_settings' );
				$behavior             = $atc_settings_general['action'];
				$where                = $atc_settings_general['where'];

				if ( 'all' !== $atc_settings_general['items'] ) {
					$behavior = apply_filters( 'ywctm_get_exclusion', ( 'hide' === $behavior ? 'show' : 'hide' ), $product_id, 'atc', $behavior );
				}

				$hide_add_to_cart = ( 'hide' === $behavior && 'all' === $where );

				//Set "Add to cart" button as hidden
				if ( $hide_add_to_cart && $this->apply_catalog_mode( $product_id ) ) {
					$passed = false;
				}

				//If "Add to cart" button is set as visible but price is hidden then hide it anyway
				if ( apply_filters( 'ywctm_check_price_hidden', false, $product_id ) ) {
					$passed = false;
				}

				if ( apply_filters( 'ywctm_hide_on_single_anyway', false, $product_id ) && apply_filters( 'ywctm_hide_on_loop_anyway', false, $product_id ) ) {
					$passed = false;
				}
			}

			return $passed;

		}

		/**
		 * Checks if "Add to cart" needs to be hidden
		 *
		 * @param   $x          boolean @deprecated
		 * @param   $product_id integer|boolean
		 *
		 * @return  bool
		 * @since   1.0.2
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_add_to_cart_single( $x = true, $product_id = false ) {
			return $this->check_hide_add_cart( true, $product_id );
		}

		/**
		 * Checks if "Add to cart" needs to be hidden from loop page
		 *
		 * @return  boolean
		 * @since   1.0.6
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_hide_add_cart_loop() {
			return $this->check_hide_add_cart();
		}

		/**
		 * PLUGIN INTEGRATIONS
		 */

		/**
		 * Say if the code is execute by quick view
		 *
		 * @return  boolean
		 * @since   1.0.7
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function is_quick_view() {

			$actions = apply_filters( 'ywctm_quick_view_actions', array( 'yith_load_product_quick_view', 'yit_load_product_quick_view' ) );

			return defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions, true );
		}

		/**
		 * Hides add to cart on wishlist
		 *
		 * @param   $value   string
		 * @param   $product WC_Product
		 *
		 * @return  string
		 * @since   1.2.2
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_add_to_cart_wishlist( $value, $product ) {

			global $yith_wcwl_is_wishlist;

			if ( $this->check_hide_add_cart( true, $product->get_id() ) && $yith_wcwl_is_wishlist ) {

				$value = '';

			}

			return $value;

		}

		/**
		 * Hide add to cart button in quick view
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function hide_add_to_cart_quick_view() {

			if ( $this->check_hide_add_cart( true ) ) {

				$hide_variations = get_option( 'ywctm_hide_variations' );
				$args            = array(
					'form.cart button.single_add_to_cart_button',
				);

				$theme_name = ywctm_get_theme_name();

				if ( 'oceanwp' === $theme_name ) {
					$args[] = 'form.cart';
				}

				if ( ! class_exists( 'YITH_YWRAQ_Frontend' ) || ( ( class_exists( 'YITH_Request_Quote_Premium' ) ) && ! YITH_Request_Quote_Premium()->check_user_type() ) ) {
					$args[] = 'form.cart .quantity';
				}

				if ( 'yes' === $hide_variations ) {

					$args[] = 'table.variations';
					$args[] = 'form.variations_form';
					$args[] = '.single_variation_wrap .variations_button';

				}

				//APPLY_FILTERS: ywctm_cart_widget_classes: CSS selector of add to cart buttons
				$classes = implode( ', ', apply_filters( 'ywctm_catalog_classes', $args ) );

				ob_start();
				?>
				<style type="text/css">
					<?php echo $classes; ?>
					{
						display: none !important
					}
				</style>
				<?php
				echo ob_get_clean();
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
			$premium_tab_template = YWCTM_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
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
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCTM_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug'] = YWCTM_SLUG;
			}

			return $new_row_meta_args;

		}

		/**
		 * Add Plugin Requirements
		 *
		 * @return  void
		 * @since   2.0.0
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
