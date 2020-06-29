<?php
/**
 * Plugin Name: WooCommerce One Page Checkout
 * Description: Super fast sales with WooCommerce. Add to cart, checkout & pay all on the one page!
 * Author:      Automattic
 * Author URI:  https://woocommerce.com/
 * Text Domain: wcopc
 * Domain Path: languages
 * Plugin URI:  https://woocommerce.com/products/woocommerce-one-page-checkout/
 * Version: 1.7.6
 *
 * WC requires at least: 2.5
 * WC tested up to: 4.1
 * Woo: 527886:c9ba8f8352cd71b5508af5161268619a
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package One Page Checkout
 * @since 1.0
 * @author Automattic
 * @copyright Copyright (c) 2019 Automattic
 * @link https://woocommerce.com/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) || ! function_exists( 'is_woocommerce_active' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'c9ba8f8352cd71b5508af5161268619a', '527886' );

/**
 * Check if WooCommerce is active, and if it isn't, disable the plugin.
 *
 * @since 1.0
 */
if ( ! is_woocommerce_active() || version_compare( get_option( 'woocommerce_db_version' ), '2.5', '<' ) ) {
	add_action( 'admin_notices', 'PP_One_Page_Checkout::woocommerce_inactive_notice' );
	return;
}

define( 'WC_ONE_PAGE_CHECKOUT_VERSION', '1.7.6' ); // WRCS: DEFINED_VERSION.

/**
 * Load the text domain to make the plugin's strings available for localisation.
 *
 * @since 1.0.1
 */
function wcopc_load_plugin_textdomain() {

	$locale = apply_filters( 'plugin_locale', get_locale(), 'wcopc' );

	// Allow upgrade safe, site specific language files in /wp-content/languages/woocommerce/
	load_textdomain( 'wcopc', WP_LANG_DIR . '/woocommerce/wcopc-' . $locale . '.mo' );

	// Then check for a language file in /wp-content/plugins/woocommerce-one-page-checkout/languages/ (this will be overriden by any file already loaded)
	load_plugin_textdomain( 'wcopc', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'wcopc_load_plugin_textdomain' );

/**
 * Function that devs can use to check if a page includes the OPC shortcode
 *
 * @since 1.1
 */
function is_wcopc_checkout( $post_id = null ) {

	// If no post_id specified try getting the post_id
	if ( empty( $post_id ) ) {
		global $post;

		if ( is_object( $post ) ) {
			$post_id = $post->ID;
		} else {
			// Try to get the post ID from the URL in case this function is called before init
			$schema = is_ssl() ? 'https://' : 'http://';
			$url = explode('?', $schema . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
			$post_id = url_to_postid( $url[0] );
		}
	}

	// If still no post_id return straight away
	if ( empty( $post_id ) || is_admin() ) {

		$is_opc = false;

	} else {

		if ( 0 == PP_One_Page_Checkout::$shortcode_page_id ) {
			$post_to_check = ! empty( $post ) ? $post : get_post( $post_id );
			PP_One_Page_Checkout::check_for_shortcode( $post_to_check );
		}

		// Compare IDs
		if ( $post_id == PP_One_Page_Checkout::$shortcode_page_id || ( 'yes' == get_post_meta( $post_id, '_wcopc', true ) ) ) {
			$is_opc = true;
		} else {
			$is_opc = false;
		}

	}

	return apply_filters( 'is_wcopc_checkout', $is_opc );
}

/**
 * So that themes and other plugins can customise the text domain, the PP_One_Page_Checkout
 * should not be initialized until after the plugins_loaded and after_setup_theme hooks.
 * However, it also needs to run early on the init hook.
 *
 * @since 1.0
 */
function initialize_one_page_checkout(){
	PP_One_Page_Checkout::init();
}

add_action( 'init', 'initialize_one_page_checkout', -1 );


class PP_One_Page_Checkout {
	private static $nonce_action = 'pp_one_page_checkout';

	static $active_plugins;

	static $add_scripts = false;

	static $raw_shortcode_atts;

	static $shortcode_page_id = 0;

	static $products_to_display =  null;

	static $categories_to_display = null;

	static $template = 'checkout/product-table.php';

	static $templates;

	static $shop_variations;

	static $plugin_url;

	static $plugin_path;

	static $template_path;

	static $evaluated_shortcode;

	static $needs_payment_changed = false;

	static $guest_checkout_option_changed = false;

	public static function init() {

		self::$active_plugins = get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		self::$plugin_url     = untrailingslashit( plugins_url( '/', __FILE__ ) );
		self::$plugin_path    = untrailingslashit( plugin_dir_path( __FILE__ ) );
		self::$template_path  = self::$plugin_path . '/templates/';

		require_once self::$plugin_path . '/functions.php';
		require_once self::$plugin_path . '/classes/class-wcopc-admin-editor.php';
		require_once self::$plugin_path . '/classes/abstract-class-wcopc-template.php';
		require_once self::$plugin_path . '/classes/class-wcopc-easy-pricing-tables-template.php';
		require_once self::$plugin_path . '/classes/class-wcopc-compat-bookings.php';
		require_once self::$plugin_path . '/classes/class-wcopc-compat-subscriptions.php';
		require_once self::$plugin_path . '/classes/class-wcopc-compat-name-your-price.php';
		require_once self::$plugin_path . '/classes/class-wcopc-settings.php';

		self::$templates   = apply_filters( 'wcopc_templates', array(
			'product-table' => array(
				'label'               => __( 'Product Table', 'wcopc' ),
				'description'         => __( 'Display a row for each product containing its thumbnail, title and price. Best for a few simple products where the thumbnails are helpful, e.g. a set of halloween masks.', 'wcopc' ),
				'supports_containers' => false,
			),
			'product-list' => array(
				'label'               => __( 'Product List', 'wcopc' ),
				'description'         => __( 'Display a list of products with a radio button for selection. Useful when the customer does not need a description or photograph to choose, e.g. versions of an eBook.', 'wcopc' ),
				'supports_containers' => false,
			),
			'product-single'  => array(
				'label'               => __( 'Single Product', 'wcopc' ),
				'description'         => __( "Display the single product template for each product. Useful when the description, images, gallery and other meta data will help the customer choose, e.g. evening gowns.", 'wcopc' ),
				'supports_containers' => false,
			),
			'pricing-table'  => array(
				'label'               => __( 'Pricing Table', 'wcopc' ),
				'description'         => __( "Display a simple pricing table with each product's attributes, weight and dimensions. Useful to allow customers to compare different, but related products, e.g. membership subscriptions.", 'wcopc' ),
				'supports_containers' => false,
			),
		) );

		add_filter( 'woocommerce_ajax_get_endpoint', array( __CLASS__, 'make_sure_ajax_url_is_relative' ) );
		add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'add_product_selection_fields' ), 9 );

		// Change add to cart messages on OPC pages to say "Add to Order" and do not include the "View Cart ->" button
		add_filter( 'woocommerce_add_error', array( __CLASS__, 'maybe_filter_error_message'), 10, 1 );
		if ( self::is_woocommerce_pre( '3.0' ) ) {
			add_filter( 'wc_add_to_cart_message', array( __CLASS__, 'maybe_filter_add_to_cart_message' ), 10, 2 );
		} else {
			add_filter( 'wc_add_to_cart_message_html', array( __CLASS__, 'maybe_filter_add_to_cart_message' ), 10, 2 );
		}

		// Update products from the checkout page
		add_action( 'wp_ajax_pp_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_pp_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_pp_remove_from_cart', array( __CLASS__, 'ajax_remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_pp_remove_from_cart', array( __CLASS__, 'ajax_remove_from_cart' ) );
		add_action( 'wp_ajax_pp_update_add_in_cart', array( __CLASS__, 'ajax_update_add_cart' ) );
		add_action( 'wp_ajax_nopriv_pp_update_add_in_cart', array( __CLASS__, 'ajax_update_add_cart' ) );

		// Add a shortcode to circumvent WooCommerce non-empty cart requirement for displaying the checkout
		add_shortcode( apply_filters( 'woocommerce_one_page_checkout_shortcode_tag', 'woocommerce_one_page_checkout' ), array( __CLASS__, 'get_one_page_checkout' ) );

		// Add JavaScript
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue_single_product_styles_scripts' ) );

		// Add WooCommerce body class
		add_filter( 'body_class', array( __CLASS__, 'opc_woocommerce_body_class' ) );

		// Filter is_checkout() on OPC posts/pages
		add_filter( 'woocommerce_is_checkout', array( __CLASS__, 'is_checkout_filter' ) );

		// Because there is no reliable way to filter is_checkout(), we need to do a page ID hack
		add_filter( 'woocommerce_get_checkout_page_id', array( __CLASS__, 'is_checkout_hack' ) );

		// Checks if a queried page contains the one page checkout shortcode, needs to happen after the "template_redirect"
		add_action( 'the_posts', array( __CLASS__, 'ensure_shortcode_page_id_is_set' ), 10, 2 );

		// Allow empty cart when we're doing a request from a OPC page.
		add_action( 'wp_ajax_woocommerce_update_order_review', array( __CLASS__, 'maybe_allow_expired_session' ), 9 );
		add_action( 'wp_ajax_nopriv_woocommerce_update_order_review', array( __CLASS__, 'maybe_allow_expired_session' ), 9 );
		add_action( 'wc_ajax_update_order_review', array( __CLASS__, 'maybe_allow_expired_session' ), 9 );

		// Display order review template even when cart is empty in WC 2.3+
		add_action( 'woocommerce_update_order_review_fragments', array( __CLASS__, 'update_order_review_fragments' ), 9 );

		// Insert an OPC specific div for messages/notices
		add_action( 'wcopc_product_selection_fields_before', array( __CLASS__, 'opc_messages' ), 10, 2 );

		// Modify OPC empty cart error
		add_filter( 'woocommerce_add_error', array( __CLASS__, 'improve_empty_cart_error' ) );

		// Load single-product OPC type-specific templates
		add_action( 'wcopc_single_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart' ) );
		add_action( 'wcopc_simple_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart_core_types' ) );
		add_action( 'wcopc_variable_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart_core_types' ) );
		add_action( 'wcopc_deposit_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart_core_types' ) );

		// Unhook 'WC_Form_Handler::add_to_cart_action' from 'init' in OPC pages, to prevent products from being added to the cart when submitting an order
		if ( isset( $_POST['is_opc'] ) && ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_checkout' ) || ( isset( $_REQUEST['wc-ajax'] ) && 'checkout' == $_REQUEST['wc-ajax'] ) ) ) {
			remove_action( 'wp_loaded', 'WC_Form_Handler::add_to_cart_action', 20 );
		}

		// Tiny MCE button icon
		add_action( 'admin_head', array( __CLASS__, 'set_tinymce_button_icon' ) );

		// Prepend WC notices to the content if the page is an OPC page
		add_filter( 'the_content', array( __CLASS__, 'maybe_display_notices' ), 10, 2 );

		// If a link to an OPC page included the 'add-to-cart' param to automatically add a product to the cart, redirect to the OPC page without that param (to avoid page refreshes adding the product to the cart, again)
		add_filter( 'woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirect' ) );

		// Add option for enabling one page checkout on core single product page
		add_filter( 'product_type_options', array( __CLASS__, 'product_type_options' ) );
		add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'save_product_meta' ), 20, 2 );
		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'single_product_wcopc' ), 90 );
		add_action( 'template_redirect', array( __CLASS__, 'filter_single_product_wcopc' ), 30 );

		// Override the checkout template on OPC pages and Ajax requests to update checkout on OPC pages
		add_filter( 'wc_get_template', array( __CLASS__, 'override_checkout_template' ), 10, 5 );

		// Ensure we have a session when loading OPC pages
		add_action( 'template_redirect', array( __CLASS__, 'maybe_set_session' ), 1 );

		// Enable robots on OPC pages.
		add_action( 'wp_head', array( __CLASS__, 'maybe_enable_robots' ), 5 );

		// Hook in just before (and after) 'woocommerce_checkout_payment' to adjust needs_payment() to ensure the payment methods are displayed on page load
		add_action( 'woocommerce_checkout_order_review', array( __CLASS__, 'maybe_toggle_cart_needs_payment'), 19 );
		add_action( 'woocommerce_checkout_order_review', array( __CLASS__, 'maybe_toggle_cart_needs_payment'), 21 );

		// Add a variable for opc to check what the default/original guest checkout is before extensions like subscriptions override
		if ( self::is_woocommerce_pre( '3.3' ) ) {
			add_filter( 'woocommerce_params', array( __CLASS__, 'filter_woocommerce_script_paramaters' ), 10, 1 );
			add_filter( 'wc_checkout_params', array( __CLASS__, 'filter_woocommerce_script_paramaters' ), 10, 1 );

			// Make sure the wc_checkout_params.is_checkout JS value is true on custom OPC pages
			add_filter( 'wc_checkout_params', array( __CLASS__, 'checkout_params' ) );
		} else {
			add_filter( 'woocommerce_get_script_data', array( __CLASS__, 'filter_woocommerce_script_paramaters' ), 10, 2 );
			add_filter( 'woocommerce_get_script_data', array( __CLASS__, 'checkout_params' ), 10, 2 );
		}

		add_filter( 'woocommerce_default_address_fields', array( __CLASS__, 'filter_default_address_fields' ), 10, 1 );

		add_action( 'wcopc_before_display_checkout', array( __CLASS__, 'maybe_add_products_to_cart' ) );

		// Initialize our settings.
		self::get_settings_instance()->init();

		do_action( 'wcopc_loaded' );
	}

	/**
	 * Get the instance of our settings class.
	 *
	 * @return WCOPC_Settings
	 */
	public static function get_settings_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new WCOPC_Settings();
		}

		return $instance;
	}

	/**
	 * The master check for an OPC request. Checks everything from page ID to $_POST data for
	 * some indication that the current request relates to an Ajax request.
	 *
	 * @return bool
	 */
	public static function is_any_form_of_opc_page() {

		$is_opc = false;

		// Modify template if the page being loaded (non-ajax) is an OPC page
		if ( is_wcopc_checkout() ) {

			$is_opc = true;

		// Modify template when doing a 'woocommerce_update_order_review' ajax request
		} elseif ( isset( $_POST['post_data'] ) ) {

			parse_str( $_POST['post_data'], $checkout_post_data );

			if ( isset( $checkout_post_data['is_opc'] ) ) {
				$is_opc = true;
			}

		// Modify template when doing ajax and sending an OPC request
		} elseif ( check_ajax_referer( self::$nonce_action, 'nonce', false ) ) {

			$is_opc = true;
		}

		return $is_opc;
	}

	/**
	 * Load custom OPC order review template to include Remove/Quantity columns.
	 *
	 * @param  boolean $is_ajax
	 * @return void
	 */
	public static function load_opc_order_review_template( $deprecated = false ) {
		wc_get_template( 'checkout/deprecated/review-order.php', array( 'checkout' => WC()->checkout(), 'is_ajax' => $deprecated ), '', PP_One_Page_Checkout::$template_path );
	}

	/**
	 * If the cart is empty, and the request is OPC, disable the `expired session` warning.
	 *
	 * @since 1.7.1
	 */
	public static function maybe_allow_expired_session() {
		if ( WC()->cart->is_empty() && self::is_any_form_of_opc_page() ) {
			add_filter( 'woocommerce_checkout_update_order_review_expired', '__return_false' );
		}
	}

	/**
	 * Set empty order review and payment fields when updating the order table via Ajax and the cart is empty.
	 *
	 * @param  array
	 * @return array
	 * @since 1.1.1
	 */
	public static function update_order_review_fragments( $fragments ) {

		// If the cart is empty
		if ( self::is_any_form_of_opc_page() && 0 == sizeof( WC()->cart->get_cart() ) ) {

			// Remove the "session has expired" notice
			if ( isset( $fragments['form.woocommerce-checkout'] ) ) {
				unset( $fragments['form.woocommerce-checkout'] );
			}

			$checkout = WC()->checkout();

			// To have control over when the create account fields are displayed - we'll display them all the time and hide/show with js
			if ( ! is_user_logged_in() ) {
				if ( false === $checkout->enable_guest_checkout ) {
					$checkout->enable_guest_checkout = true;
					self::$guest_checkout_option_changed = true;
				}
			}

			// Add non-blocked order review fragment
			ob_start();
			woocommerce_order_review();
			$fragments['.woocommerce-checkout-review-order-table'] = ob_get_clean();

			// Reset guest checkout option
			if ( true === self::$guest_checkout_option_changed ) {
				$checkout->enable_guest_checkout = false;
				self::$guest_checkout_option_changed = false;
			}

			// Add non-blocked checkout payment fragement
			ob_start();
			woocommerce_checkout_payment();
			$fragments['.woocommerce-checkout-payment'] = ob_get_clean();
		}

		return $fragments;
	}

	/**
	 * Hook to wc_get_template() and override the checkout template used on OPC pages and when updating the order review fields
	 * via WC_Ajax::update_order_review()
	 *
	 * @return string
	 */
	public static function override_checkout_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( 'checkout/review-order.php' == $template_name && $default_path !== PP_One_Page_Checkout::$template_path && self::is_any_form_of_opc_page() ) {
			$located = wc_locate_template( 'checkout/review-order-opc.php', '', PP_One_Page_Checkout::$template_path );
		}

		return $located;
	}

	/**
	 * OPC single-product template action for custom product types - templates not included with OPC (must be loaded externally by hooking at this point).
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public static function opc_single_add_to_cart( $post_id ) {
		global $product;

		$product_type = wcopc_get_product_type( $product );

		// Change 'Add to cart' to 'Add to order' for known product types
		// Let custom types handle this themselves
		if ( in_array( $product_type, array( 'simple', 'variable', 'composite', 'bundle' ) ) ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'modify_single_add_to_cart_text' ) );
		}

		do_action( 'wcopc_' . $product_type . '_add_to_cart', $post_id );

		remove_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'modify_single_add_to_cart_text' ) );
	}

	/**
	 * OPC single-product template action for core product types - templates are included with OPC and loaded directly.
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public static function opc_single_add_to_cart_core_types( $post_id ) {
		global $product;
		wc_get_template( 'checkout/add-to-cart/' . wcopc_get_product_type( $product ) . '.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path );
	}

	/**
	 * If we're on a OPC page, filter the default WC add to cart message to say "added to order".
	 *
	 * @since 1.1
	 */
	public static function maybe_filter_add_to_cart_message( $message, $product_id ) {

		if ( is_wcopc_checkout() ) {

			if ( is_array( $product_id ) ) {
				$product_ids    = $product_id;
				$product_titles = array();

				foreach ( $product_ids as $product_id => $quantity ) {
					$product_titles[] = get_the_title( $product_id );
				}

				$message = self::get_add_to_cart_message( $quantity, wc_format_list_of_items( $product_titles ) );

			} else {

				$message = self::get_add_to_cart_message( 1, get_the_title( $product_id ) );

			}
		}

		return $message;
	}

	/**
	 * If we're on a OPC page, filter the default WC error messages to remove the view cart button.
	 *
	 * @since 1.1.1
	 */
	public static function maybe_filter_error_message( $message ) {

		if ( is_wcopc_checkout() ) {

			$message = preg_replace('/<a[^>]*>(' . __( 'View Cart', 'wcopc' ) .')<\/a>/iU','',$message);

		}

		return $message;
	}

	/**
	 * Helper function for displaying the added to order message for a certain product.
	 *
	 * @since 1.1
	 */
	protected static function get_add_to_cart_message( $quantity, $product_title ) {

		$product_title = '&quot;' . $product_title;

		if ( $quantity > 1 ) {
			$product_title = $quantity . ' &times; ' . $product_title;
		}

		return sprintf( __( '%s&quot; added to your order. Complete your order below.', 'wcopc' ), $product_title );
	}

	/**
	 * Change button 'Add to cart' text to 'Add to order' in OPC pages
	 * @param  WC_Product $product
	 * @return string
	 */
	public static function modify_single_add_to_cart_text( $product ) {
		return __( 'Add to order', 'wcopc' );
	}

	/**
	 * Display product selection fields on checkout page.
	 *
	 * @since 1.0
	 */
	public static function add_product_selection_fields() {

		if ( 0 == self::$shortcode_page_id ) {
			return;
		}

		do_action( 'wcopc_product_selection_fields_before', self::$template, self::$raw_shortcode_atts );

		if ( false === apply_filters( 'wcopc_show_product_selection_fields', true, self::$template ) ) {
			return;
		}

		$products = array();

		if ( ! empty( self::$products_to_display ) || ! empty( self::$categories_to_display ) ) {
			$args = array(
				'post_type'      => array( 'product', 'product_variation' ),
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
			);

			// Alter query if product ids or categories specified in shortcode
			if ( self::$products_to_display ) {

				$args['post__in'] = explode( ',', self::$products_to_display );
				$args['orderby']  = 'post__in';

			} else {

				if ( self::$categories_to_display ) {
					$args['tax_query'] = array(
						array(
							'taxonomy'  => 'product_cat',
							'terms'     => explode( ',', self::$categories_to_display )
						)
					);
				}

				// If we're not filtering by product ID, we don't want to display hidden products
				if ( self::is_woocommerce_pre( '3.0' ) ) {
					$args['meta_query'] = array(
						array(
							'key'     => '_visibility',
							'value'   => array( 'catalog', 'visible' ),
							'compare' => 'IN'
						)
					);
				} else {
					$args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'slug',
						'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
						'operator' => 'NOT IN',
					);
				}
			}

			$args = apply_filters( 'wcopc_products_query_args', $args );

			$product_posts = get_posts( $args );

			foreach ( $product_posts as $product_post ) {

				$product = wc_get_product( $product_post->ID );

				if ( ! is_object( $product ) ) {
					continue;
				}

				if ( ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) && ( self::$template != 'checkout/product-single.php' ) ) {

					$visible_children = wcopc_get_visible_children( $product );

					foreach ( $visible_children as $child_id ) {

						$child = wc_get_product( $child_id );

						if ( ( $product->is_type( 'variable' ) && self::all_variation_attributes_set( $child ) ) || ( $product->is_type( 'grouped' ) && ! $child->has_child() ) ) {
							$products = self::build_products_array( $child, $products );
						}
					}

				} else {
					$products = self::build_products_array( $product, $products );
				}
			}
		}

		$products = apply_filters( 'wcopc_products_for_selection_fields', $products, self::$template, self::$raw_shortcode_atts );

		?>
		<div id="opc-product-selection" data-opc_id="<?php echo self::$shortcode_page_id; ?>" class="wcopc">
			<?php if ( ! empty( $products ) ) : ?>
				<?php wc_get_template( self::$template, array( 'products' => $products ), '', self::$template_path ); ?>
			<?php endif; ?>
		</div><!-- .opc-product-selection --><?php

		self::maybe_show_shipping( $products );

		do_action( 'wcopc_product_selection_fields_after', self::$template, self::$raw_shortcode_atts );
	}

	/**
	 * Used to generate data that maps OPC list/table-template products to cart items.
	 *
	 * @param  WC_Product $product
	 * @param  array      $products
	 * @return array
	 */
	private static function build_products_array( $product, $products = array() ) {

		if ( ! is_object( $product ) || ! $product->exists() ) {
			return $products;
		}

		$products_in_cart = self::get_products_in_cart( self::$shortcode_page_id );

		if ( array_key_exists( $product->get_id(), $products_in_cart ) ) {
			wcopc_set_products_prop( $product, 'in_cart', true );
			wcopc_set_products_prop( $product, 'cart_item', $products_in_cart[ $product->get_id() ] );
		} else {
			wcopc_set_products_prop( $product, 'in_cart', false );
			wcopc_set_products_prop( $product, 'cart_item', array() );
		}

		// For the single product template we need to check if a product variation exists in the cart
		if ( $product->has_child() ) {

			$visible_children = wcopc_get_visible_children( $product );

			foreach( $visible_children as $product_id ) {
				if ( array_key_exists( $product_id , $products_in_cart ) ) {
					wcopc_set_products_prop( $product, 'in_cart', true );
					wcopc_set_products_prop( $product, 'cart_item', $products_in_cart[ $product_id ] );
				}
			}
		}

		$products[ $product->get_id() ] = $product;

		return $products;
	}

	/**
	 * Check if all variation's attributes are set
	 *
	 * @param  WC_Product_Variation  $variation
	 * @return boolean
	 */
	private static function all_variation_attributes_set( $variation ) {

		$set = true;

		// undefined attributes have null strings as array values
		foreach( $variation->get_variation_attributes() as $att ){
			if( ! $att ){
				$set = false;
				break;
			}
		}

		return $set;

	}

	/**
	 * Get a products variation data formatted in the same form that is used in
	 * the WooCommerce cart
	 *
	 * Based on the WC_Cart::get_item_data() method
	 *
	 * @since 1.0
	 */
	public static function get_formatted_variation_data( $variation_attributes, $product_attributes, $flat = false ) {

		$item_data = array();

		// Variation data
		if ( ! empty( $variation_attributes ) && ! empty( $product_attributes ) ) {

			$variation_list = array();

			foreach ( $variation_attributes as $name => $value ) {

				if ( '' === $value )
					continue;

				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $taxonomy ) ) {
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );

				// If this is a custom option slug, get the options name
				} else {

					if ( isset( $product_attributes[ str_replace( 'attribute_', '', $name ) ] ) ) {
						$label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $name ) ]['name'] );
					} else {
						$label = $name;
					}

					$options = array_map( 'trim', explode( WC_DELIMITER, $product_attributes[ str_replace( 'attribute_', '', $name ) ]['value'] ) );

					foreach ( $options as $option ) {
						if ( sanitize_title( $option ) == $value ) {
							$value = $option;
							break;
						}
					}
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => apply_filters( 'woocommerce_variation_option_name', $value )
				);
			}
		}

		// Output flat or in list format
		if ( sizeof( $item_data ) > 0 ) {

			if ( $flat ) {

				$string = '';

				foreach ( $item_data as $data ) {
					$string .= esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . ', ';
				}

				return rtrim( $string, ', ' );

			} else {
				ob_start();
				woocommerce_get_template( 'cart/cart-item-data.php', array( 'item_data' => $item_data ) );
				return ob_get_clean();
			}

		}

		return '';
	}

	/**
	 * Return a formatted single variation/attribute value.
	 *
	 * Useful for when we are already looping through attributes and need consistent formatting
	 *
	 * Attribute labels(titles?) are already handled by wc_attribute_label()
	 *
	 * @param  string 	$attribute_title
	 * @param  string 	$attribute_value
	 * @param  array 	$product_attributes (optional)
	 * @return void
	 */
	public static function get_formatted_attribute_value( $attribute_title = '', $attribute_value = '', $product_attributes = null ) {

		if ( empty( $attribute_title ) || empty( $attribute_value ) ) {
			return;
		}

		// clean up the title so it can be reused for our purposes below
		$attribute_title = esc_attr( str_replace( 'attribute_', '', $attribute_title ) );

		// If this is a term slug, get the term's nice name
		if ( taxonomy_exists( $attribute_title ) ) {
			$term = get_term_by( 'slug', $attribute_value, $attribute_title );
			if ( ! is_wp_error( $term ) && $term->name ) {
				$attribute_value = $term->name;
			}
		} else {
			// If the original product attributes ($product_attributes) are provided we can do some extra work compare values with the delimted list of custom product attributes to get the original formatting of that attribute otherwise just use the default ucwords version
			if ( ! $product_attributes ) {
				$attribute_value = ucwords( str_replace( '-', ' ', $attribute_value ) );
			} else {

				if ( isset( $product_attributes[ $attribute_title ] ) ) {

					$options = array_map( 'trim', explode( WC_DELIMITER, $product_attributes[ $attribute_title ]['value'] ) );

					foreach ( $options as $option ) {
						if ( sanitize_title( $option ) == $attribute_value ) {
							$attribute_value = $option;
							break;
						}
					}
				}
			}
		}

		return $attribute_value;

	}

	/**
	 * A custom ajax remove from cart function.
	 *
	 * @since 1.0
	 */
	public static function ajax_remove_from_cart() {

		do_action( 'wcopc_ajax_remove_from_cart_response_before' );

		check_ajax_referer( self::$nonce_action, 'nonce' );

		$remove        = false;
		$response_data = array();
		$item_removed  = false;

		// Get cart item id from cart
		$cart = WC()->cart->get_cart();

		foreach ( $cart as $cart_item_id => $value ) {

			// Requests coming from the OPC order-review template reference a specific cart item by its key.
			if ( isset( $_POST['update_key'] ) ) {

				if ( $cart_item_id == $_POST['update_key'] ) {
					$remove = true;
				}

			// Requests coming from OPC items reference their own product_id and OPC id.
			} elseif ( isset( $_POST['add_to_cart'] ) && ( $value['product_id'] == $_POST['add_to_cart'] || $value['variation_id'] == $_POST['add_to_cart'] ) ) {
				$remove = true;
			}

			if ( ! $remove ) {
				continue;
			}

			WC()->cart->set_quantity( $cart_item_id, 0 );
			wc_add_notice( sprintf( __( '&quot;%s&quot; was successfully removed from your order.', 'wcopc' ), get_the_title( $value['product_id'] ) ), 'success' );
			$response_data['result'] = 'success';
			$item_removed = true;
			break;
		}

		if ( ! $item_removed ) {
			wc_add_notice( sprintf( __( '&quot;%s&quot; could not be removed from your order.', 'wcopc' ), get_the_title( $value['product_id'] ) ), 'error' );
			$response_data['result'] = 'failure';
		}

		// Check cart items are valid, this is usually done when the cart is loaded or customer checks out, but we need to do it here to ensure coupons and items are checked
		do_action( 'woocommerce_check_cart_items' );

		$response_data['products_in_cart'] = self::get_products_in_cart();

		ob_start();
		wc_print_notices();
		$response_data['messages'] = ob_get_clean();

		$response_data = self::refresh_fragments( $response_data );

		$response_data = apply_filters( 'wcopc_ajax_remove_from_cart_response_data', $response_data );

		WC()->cart->maybe_set_cart_cookies();

		echo json_encode( $response_data );

		do_action( 'wcopc_ajax_remove_from_cart_response_after' );

		die();
	}

	/**
	 * A custom ajax add to cart function.
	 *
	 * The @see woocommerce_ajax_add_to_cart() function does not work for variable
	 * products, and the @see woocommerce_add_to_cart_action() function is too agressive
	 * in it's attribute_x field validation, so we need to use our own function.
	 *
	 * @since 1.0
	 */
	public static function ajax_add_to_cart( $bypass = false ) {

		check_ajax_referer( self::$nonce_action, 'nonce' );

		// Clear cart each time a new radio button is pressed
		if ( isset( $_REQUEST['empty_cart'] ) && ! apply_filters( 'wcopc_not_empty_cart', false ) ) {
			WC()->cart->empty_cart();
		}

		// Populate $_POST with 3rd party input data to allow 3rd party code to validate
		if ( isset( $_POST['input_data'] ) ) {

			parse_str( $_POST['input_data'], $input_data );

			if ( $input_data ) {

				foreach ( $input_data as $input_name => $input_value ) {

					// Write to $_POST only if key does not exist
					if ( ! isset( $_POST[ $input_name ] ) ) {
						$_REQUEST[ $input_name ] = $input_value;
						$_POST[ $input_name ]    = $input_value;
					}
				}
			}
		}

		$response_data       = array();
		$product_id          = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add_to_cart'] ) );
		$was_added_to_cart   = false;
		$product             = wc_get_product( $product_id );
		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $product->get_type(), $product );

		if ( ! $bypass ) {

			// Variation handling
			if ( 'variation' === $add_to_cart_handler ) {

				$variation_id       = $product_id;
				$product_id         = wcopc_get_variation_parent_id( $product );
				$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
				$all_variations_set = true;
				$variations         = array();

				$parent     = wc_get_product( $product_id );
				$attributes = $parent->get_attributes();
				$variations = $product->get_variation_attributes();
				$variation  = $product;

				// Verify all attributes
				foreach ( $variations as $name => $value ) {

					if ( $value ) {
						// Custom product attribute, get a formatted versin of the name so it displays nicely on the Review Order table
						if ( ! taxonomy_exists( esc_attr( str_replace( 'attribute_', '', $name ) ) ) ) {
							$variations[ $name ] = PP_One_Page_Checkout::get_formatted_attribute_value( $name, $value, $attributes );
						}
						continue;
					}

					$all_variations_set = false;
				}

				if ( $all_variations_set ) {
					// Add to cart validation
					$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

					if ( $passed_validation ) {
						if ( WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
							wc_add_notice( self::get_add_to_cart_message( $quantity, $product->get_title() . ' (' . self::get_formatted_variation_data( $variations, $attributes, true ) . ')' ), 'success' );
							$was_added_to_cart = true;
						}
					}
				} else {
					wc_add_notice( __( 'Please choose product options&hellip;', 'wcopc' ), 'error' );
				}

			// Variable product handling
			} elseif ( 'variable' === $add_to_cart_handler ) {

				$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
				$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
				$all_variations_set = true;
				$variations         = array();
				$passed_validation  = false;

				$attributes = $product->get_attributes();
				$variation  = wc_get_product( $variation_id );

				// Verify all attributes
				foreach ( $attributes as $attribute ) {
					if ( ! $attribute['is_variation'] ) {
						continue;
					}

					$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

					if ( isset( $_REQUEST[ $taxonomy ] ) ) {
						if ( $attribute['is_taxonomy'] ) {
							// Don't use wc_clean as it destroys sanitized characters
							$value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
						} else {
							$value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
						}

						// Get valid value from variation
						$variation_data = wc_get_product_variation_attributes( $variation_id );
						$valid_value    = $variation_data[ $taxonomy ];

						// Allow if valid
						if ( '' === $valid_value || $valid_value === $value ) {
							$variations[ $taxonomy ] = $value;
							continue;
						}
					}

					$all_variations_set = false;
				}

				if ( $all_variations_set ) {
					// Add to cart validation
					$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

					if ( $passed_validation ) {
						if ( WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
							wc_add_notice( self::get_add_to_cart_message( $quantity, $product->get_title() . ' (' . self::get_formatted_variation_data( $variations, $attributes, true ) . ')' ), 'success' );
							$was_added_to_cart = true;
						}
					}
				}

				// If variation_id is empty or variations weren't set, add a notice.
				if ( empty( $variation_id ) || ! $all_variations_set ) {
					wc_add_notice( __( 'Please choose product options&hellip;', 'wcopc' ), 'error' );
				}

			// Custom Handler
			} elseif ( has_action( 'wcopc_add_to_cart_handler_' . $add_to_cart_handler ) ){

				do_action( 'wcopc_add_to_cart_handler_' . $add_to_cart_handler, $url );

			// Simple Products
			} else {

				$quantity = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );

				// Add to cart validation
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

				if ( $passed_validation ) {
					// Add the product to the cart
					if ( WC()->cart->add_to_cart( $product_id, $quantity ) ) {
						wc_add_notice( self::get_add_to_cart_message( $quantity, wcopc_get_products_name( $product ) ), 'success' );
						$was_added_to_cart = true;
					}
				}

			}

		} else {

			$was_added_to_cart = true;
			$passed_validation = true;
		}

		// Check cart items are valid, this is usually done when the cart is loaded or customer checks out, but we need to do it here to ensure coupons and items are checked
		do_action( 'woocommerce_check_cart_items' );

		do_action( 'wcopc_ajax_add_to_cart_response_before' );

		WC()->cart->maybe_set_cart_cookies();

		ob_start();

		wc_print_notices();

		$response_data['messages']         = ob_get_clean();
		$response_data['products_in_cart'] = self::get_products_in_cart();

		if ( $passed_validation && $was_added_to_cart ) {

			$response_data           = self::refresh_fragments( $response_data );
			$response_data['result'] = 'success';
			if ( $product->is_type( 'variation' ) ) {
				$product_id = wcopc_get_variation_parent_id( $product );
			}
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		} else {
			$response_data['result'] = 'failure';
		}

		$response_data = apply_filters( 'wcopc_ajax_add_to_cart_response_data', $response_data );

		echo json_encode( $response_data );

		do_action( 'wcopc_ajax_add_to_cart_response_after' );

		die();
	}

	/**
	 * Checks if the product already exists in the cart. If it does, set the quantity to 0 (remove it) then call
	 * ajax_add_to_cart() function to add it back into the cart with the correct quantity amount.
	 *
	 * @since 1.0
	 */
	public static function ajax_update_add_cart() {

		check_ajax_referer( self::$nonce_action, 'nonce' );
		$cart_contents = WC()->cart->get_cart();

		$bypass = false;
		$update = false;

		foreach ( $cart_contents as $cart_item_id => $value ) {

			// Requests coming from the OPC order-review template reference a specific cart item by its key.
			if ( isset( $_POST['update_key'] ) ) {

				if ( $cart_item_id == $_POST['update_key'] ) {
					$update = true;
				}

			// Requests coming from OPC items reference their own product_id and OPC ID.
			} elseif ( isset( $_POST['add_to_cart'] ) && ( $value['product_id'] == $_POST['add_to_cart'] || $value['variation_id'] == $_POST['add_to_cart'] ) ) {
				$update = true;
			}

			if ( ! $update ) {
				continue;
			}

			// When a request comes from the modified order-review template, we need to modify cart items WITHOUT removing them to preserve sensitive cart item data added by other extensions.
			if ( isset( $_POST['update_key'] ) && isset( $_POST['quantity'] ) ) {

				$quantity = absint( $_POST['quantity'] );

				WC()->cart->set_quantity( $cart_item_id, $quantity );
				$bypass = true;
				wc_add_notice( self::get_add_to_cart_message( $quantity, $value['data']->get_title() ), 'success' );

			} else {

				WC()->cart->set_quantity( $cart_item_id, 0 );
			}

			break;
		}

		self::ajax_add_to_cart( $bypass );
	}

	/**
	 * Add updated mini cart fragments to response
	 *
	 * @since 1.2.5
	 */
	public static function refresh_fragments( $response_data ) {

		// Get mini cart
		ob_start();
		woocommerce_mini_cart();
		$mini_cart = ob_get_clean();

		// Setup initial fragments array including the mini cart
		$fragments = array(
			'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
		);

		// Allow plugins and themes to add their own fragments to be updated e.g. storefront header cart
		$response_data['fragments'] = apply_filters( 'woocommerce_add_to_cart_fragments', $fragments );

		// Send cart hash with  response data, same as core, for devs to use
		$response_data['cart_hash'] = apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() );

		return $response_data;
	}

	/**
	 * Registers our JavaScript for one page checkout with WordPress.
	 *
	 * @since 1.0
	 */
	public static function enqueue_scripts() {

		if ( ! self::$add_scripts ) {
			return;
		}

		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

		wp_enqueue_script( 'woocommerce-one-page-checkout', self::$plugin_url . '/js/one-page-checkout.js', array( 'jquery', 'wc-add-to-cart-variation' ), '1.0', true );

		$params = apply_filters( 'wcopc_script_data', array(
			'wcopc_nonce'                 => wp_create_nonce( self::$nonce_action ),
			'wcopc_complete_order_prompt' => '<a class="wc-south opc-complete-order" href="#customer_details">' . __( 'Modify &amp; complete order below', 'wcopc' ) . '</a>',
			'ajax_error_notice'           => '<div class="woocommerce-error">' . __( 'Error processing your request. Please try refreshing the page. Contact us if you continue to have issues.', 'wcopc' ) . '</div>',
			'ajax_url'                    => WC()->ajax_url(),
			'autoscroll'                  => self::get_settings_instance()->get_setting( 'autoscroll' ),
		) );

		wp_localize_script( 'woocommerce-one-page-checkout', 'wcopc', $params );

		if ( 'yes' === get_option( 'woocommerce_enable_lightbox' ) ) {
			wp_enqueue_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			wp_enqueue_script( 'prettyPhoto-init', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
			wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );
		}

		wp_enqueue_script( 'wc-checkout', $assets_path . 'js/frontend/checkout' . $suffix . '.js', array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n' ), WC_VERSION, true );

		wp_enqueue_script( 'wc-credit-card-form' );

		wp_enqueue_style( 'woocommerce-one-page-checkout', self::$plugin_url . '/css/one-page-checkout.css' );

		do_action( 'wcopc_enqueue_scripts' );
	}

	/**
	 * Enqueue the WooCommerce single product styles & scripts if the page is using an OPC
	 * shortcode and the product-single template.
	 *
	 * @since 1.4.1
	 */
	public static function maybe_enqueue_single_product_styles_scripts() {
		global $post;

		if ( ! empty( $post->post_content ) && false !== stripos( $post->post_content, '[woocommerce_one_page_checkout' ) && false !== stripos( $post->post_content, 'product-single' ) ) {

			$handles = array(
				array(
					'theme_feature' => 'wc-product-gallery-zoom',
					'script_handle' => 'zoom',
				),
				array(
					'theme_feature' => 'wc-product-gallery-slider',
					'script_handle' => 'flexslider',
				),
				array(
					'theme_feature' => 'wc-product-gallery-slider',
					'script_handle' => 'photoswipe-ui-default',
					'style_handle'  => 'photoswipe-default-skin',
				),
			);

			foreach( $handles as $handle_details ) {
				if ( current_theme_supports( $handle_details['theme_feature'] ) ) {

					wp_enqueue_script( $handle_details['script_handle'], '', array( 'jquery' ), WC_VERSION );

					if ( isset( $handle_details['style_handle'] ) ) {

						wp_enqueue_style( $handle_details['style_handle'], '', array(), WC_VERSION );

						add_action( 'wp_footer', 'woocommerce_photoswipe' );
					}
				}
			}

			wp_enqueue_script( 'wc-single-product', '', array( 'jquery' ), WC_VERSION );
		}
	}

	/**
	 * If we get to the 'the_posts' filter and self::$shortcode_page_id still hasn't been set,
	 * let's check the current post/posts to see if any contain the OPC shortcode.
	 *
	 * @since 1.0
	 */
	public static function ensure_shortcode_page_id_is_set( $posts, $query ) {

		// Return straight away if there are no posts or if its a secondary query
		if ( empty( $posts ) || ! $query->is_main_query() ) {
			return $posts;
		}

		if ( 0 == self::$shortcode_page_id ) {
			foreach ( $posts as $post ) {
				if ( ( false !== stripos( $post->post_content, '[woocommerce_one_page_checkout' ) ) || ( 'yes' == get_post_meta( $post->ID, '_wcopc', true ) ) ) {
					self::$add_scripts = true;
					self::$shortcode_page_id = $post->ID;
					break;
				}
			}
		}

		return $posts;
	}

	/**
	 * Because there is no reliable way to overload is_checkout(), we need to operate on a filter
	 * further up the line, and that is the 'woocommerce_get_checkout_page_id' filter.
	 *
	 * This function checks if we found a page containing the one page checkout shortcode earlier,
	 * and if we did, we let that act as the checkout page.
	 *
	 * @since 1.0
	 */
	public static function is_checkout_hack( $page_id ) {
		global $wp;

		if ( 0 != self::$shortcode_page_id ) {

			$backtrace = debug_backtrace( false ); // Warned you it was a hack

			$functions_to_ignore = apply_filters( 'wcopc_is_checkout_override_function_names', array( 'wc_template_redirect', 'get_checkout_url', 'get_checkout_payment_url', 'get_checkout_order_received_url', 'get_cancel_order_url', 'get_cancel_order_url_raw', 'is_checkout' ) );

			// An array of backtrace indexes to look for functions to ignore
			$backtrace_indexes = apply_filters( 'wcopc_is_checkout_override_function_backtrace_indexes', array( 3, 4, 5, 6 ) );

			// making sure we have arrays
			if ( is_array( $functions_to_ignore ) && is_array( $backtrace_indexes ) ) {
				// An array of function names in the backtrace at the ignored indexes
				$backtrace_functions = array_intersect_key( wp_list_pluck( $backtrace, 'function' ), array_flip( $backtrace_indexes ) );

				// If we don't find any functions which we ignore
				if ( 0 == count( array_intersect( $backtrace_functions, $functions_to_ignore ) ) ) {
					$page_id = self::$shortcode_page_id;
				}
			}
		}

		return $page_id;

	}

	/**
	 * Filter the result of `is_checkout()` for OPC posts/pages
	 *
	 * @param  boolean  $return
	 * @return boolean
	 */
	public static function is_checkout_filter( $return = false ) {

		if ( is_wcopc_checkout() ) {
			$return = true;
		}

		return $return;
	}

	/**
	 * Make sure the wc_checkout_params.is_checkout JS value is true on custom OPC pages
	 *
	 * @access public
	 * @param array $params
	 * @return array
	 */
	public static function checkout_params( $params, $handle = '' ) {
		global $post;
		// WC 3.3+ deprecates handle-specific filters in favour of 'woocommerce_get_script_data'.
		if ( 'woocommerce_get_script_data' === current_filter() && 'wc-checkout' !== $handle ) {
			return $params;
		} elseif ( $post->ID == self::$shortcode_page_id ) {
			$params['is_checkout'] = true;
		}

		return $params;
	}

	/**
	 * Checks if any post about to be displayed contains the one page checkout shortcode.
	 *
	 * We need to set @see self::$add_scripts here rather than in the shortcode so we can conditionally
	 * add the locale to the WooCommerce core script done in @see self::localize_script() hooked to
	 * 'woocommerce_params' which is run on 'wp_enqueue_script' (i.e. before the shortcode is evaluated).
	 *
	 * @since 1.0
	 */
	public static function check_for_shortcode( $post_to_check ) {

		if ( false !== stripos( $post_to_check->post_content, '[woocommerce_one_page_checkout' ) ) {
			self::$add_scripts = true;
			self::$shortcode_page_id = $post_to_check->ID;
			$contains_shortcode = true;
		} else {
			$contains_shortcode = false;
		}

		return $contains_shortcode;
	}

	/**
	 * Evaluate the OPC shortcode
	 *
	 * @since 1.0
	 */
	public static function get_one_page_checkout( $atts ) {

		// don't evaluate shortcode more than once on the same page
		if ( true === self::$evaluated_shortcode || is_admin() ) {
			return '';
		}

		self::$evaluated_shortcode = true;

		return WC_Shortcodes::shortcode_wrapper( __CLASS__ . '::one_page_checkout_shortcode', $atts, array( 'class'  => 'wcopc', 'before' => null, 'after'  => null ) );
	}

	/**
	 * Similar to the @see woocommerce_checkout() function except this function does not require
	 * any items to already be in the cart before displaying the checkout.
	 *
	 * @since 1.0
	 */
	public static function one_page_checkout_shortcode( $atts ){

		if ( ! wcopc_is_frontend_request() ) {
			return;
		}

		self::$raw_shortcode_atts = $atts;

		if ( isset( $atts['product_ids'] ) ) {
			self::$products_to_display = $atts['product_ids'];
		} else if ( isset( $atts['category_ids'] ) ) {
			self::$categories_to_display = $atts['category_ids'];
		}

		if ( isset( $atts['template'] ) && ! empty( $atts['template'] ) ) {

			// Template param can accept either a full file name and path or just the file name without path/extension
			if ( file_exists( wc_locate_template( $atts['template'], '', self::$template_path ) ) ) {

				self::$template = $atts['template'];

			} elseif ( file_exists( wc_locate_template( 'checkout/' . $atts['template'] . '.php', '', self::$template_path ) ) ) {

				// But if the template doens't exist, check
				self::$template = 'checkout/' . $atts['template'] . '.php';

			}

			// Allow plugins to override the template
			self::$template = apply_filters( 'wcopc_template', self::$template, $atts );
		}

		do_action( 'wcopc_before_display_checkout' );

		// Show non-cart errors
		wc_print_notices();

		WC()->cart->calculate_totals();

		// Get checkout object for WC 2.0+
		$checkout = WC()->checkout();

		wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout )  );

	}

	/**
	 * Runs just before @see woocommerce_ajax_update_order_review() and terminates the current request if
	 * the cart is empty to prevent WooCommerce printing an error that doesn't not apply on one page checkout purchases.
	 *
	 * @since 1.0
	 */
	public static function improve_empty_cart_error( $error ) {

		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) && $error == sprintf( __( 'Sorry, your session has expired. <a href="%s">Return to homepage &rarr;</a>', 'wcopc' ), home_url() ) ) {
			$error = __( 'You must select a product.', 'wcopc' );
		}

		return $error;
	}

	/**
	 * Returns the product or variation ID of all products in the cart.
	 * To allow table/list templates to recognize & manage their own cart items, pass the id of the current OPC container to retrieve cart items added by this OPC container only.
	 *
	 * @param  int   $opc_id  Only return cart items managed by a specific OPC page.
	 * @return array          Associated array of with product or variation IDs as the keys and quantity as the values.
	 * @since 1.0
	 */
	private static function get_products_in_cart( $opc_id = false ) {

		if ( ! isset( WC()->cart ) ) {
			return array();
		}

		$products_in_cart = array();

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			if ( false === apply_filters( 'wcopc_allow_cart_item_modification', true, $cart_item, $cart_item_key, $opc_id ) ) {
				continue;
			}

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			$products_in_cart[ $product_id ] = $cart_item;
		}

		return $products_in_cart;
	}

	/**
	 * Makes sure the ajax url is relative
	 *
	 * This filter changes the ajax URL and makes it relative. By being relative it fixes a bug when
	 * the main site is `https` but the current page is `http`.
	 *
	 * This issue is already fixed in WooCommerce but we implement this solution as well in our end to
	 * fix customer with older WooCommerce.
	 *
	 * @link https://github.com/woocommerce/woocommerce/issues/19116
	 * @link https://github.com/woocommerce/woocommerce/pull/19139
	 *
	 * @param string $url	Ajax URL
	 *
	 * @return string The new URL
	 */
	public static function make_sure_ajax_url_is_relative( $url ) {
		return preg_replace( '@^https?:@i', '', $url );
	}

	/*
	 * Plugin House Keeping
	 */

	/**
	 * Called when WooCommerce is inactive to display an inactive notice.
	 *
	 * @since 1.0
	 */
	public static function woocommerce_inactive_notice() {
		if ( current_user_can( 'activate_plugins' ) ) :
			if ( ! is_woocommerce_active() ) : ?>
				<div id="message" class="error">
					<p><?php printf( __( '%sWooCommerce One Page Checkout is inactive.%s The %sWooCommerce plugin%s must be active for WooCommerce One Page Checkout to work. Please %sinstall & activate WooCommerce%s', 'wcopc' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
				</div>
						<?php elseif ( version_compare( get_option( 'woocommerce_db_version' ), '2.5', '<' ) ) : ?>
				<div id="message" class="error">
					<p><?php printf( __( '%sWooCommerce One Page Checkout is inactive.%s This plugin requires WooCommerce 2.5 or newer. Please %supdate WooCommerce to version 2.5 or newer%s', 'wcopc' ), '<strong>', '</strong>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
				</div>
		<?php endif; ?>
	<?php endif;
	}

	/**
	 * Adds admin styles for setting the tinymce button icon
	 */
	public static function set_tinymce_button_icon() {
		?>
		<style>
		i.mce-i-wcopc {
			font: 400 20px/1 dashicons;
			padding: 0;
			vertical-align: top;
			speak: none;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			margin-left: -2px;
			padding-right: 2px
		}
		</style>
		<?php
	}

	/**
	 * Add 'wooommerce' body class. Helps with consistency of WooCommerce styles
	 */
	public static function opc_woocommerce_body_class($classes) {
		global $post;

		if ( empty( $post ) ) {
			return $classes;
		}

		if ( $post->ID == self::$shortcode_page_id ) {
			array_push($classes, 'woocommerce', 'woocommerce-page');
		}

		if ( 'yes' == get_post_meta( $post->ID, '_wcopc', true ) ) {
			array_push($classes, 'wcopc-product-single' );
		}

		return $classes;

	}

	/**
	 * If we're on an OPC page, display any WC notices at the top of the page - useful in case the store manager
	 * used a customer add-to-cart link and we want to display the success/error message at the top of the OPC page
	 * after the refresh.
	 *
	 * @since 1.1
	 */
	public static function maybe_display_notices( $content ) {

		if ( wcopc_is_frontend_request() && is_wcopc_checkout() ) {
			ob_start();
			wc_print_notices();
			$notices = ob_get_clean();

			$content = $notices . $content;
		}

		return $content;
	}

	/**
	 * Insert an OPC specific div for messages/notices. Helps with determining whether messages are displayed within
	 * the viewport or not and allows better targeting of OPC specific messages/notices.
	 *
	 * @since 1.1
	 */
	public static function opc_messages( $template, $raw_shortcode_atts ) {

		echo '<div id="opc-messages"></div>';

	}

	/**
	 * If the store manager has manually added an add-to-cart param to the OPC page ID, after adding the product
	 * to the cart, redirect to the OPC page without the add-to-cart param, to avoid adding the product again if
	 * the customer refreshes the page.
	 *
	 * @since 1.1
	 */
	public static function add_to_cart_redirect( $url ) {

		if ( ! is_ajax() && is_wcopc_checkout() ) {
			$schema = is_ssl() ? 'https://' : 'http://';
			$url = explode('?', $schema . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
			$url = remove_query_arg( array( 'add-to-cart', 'variation_id', 'quantity' ), $url[0] );
		}

		return $url;
	}

	/*
	 * Add checkbox to product data metabox title
	 */
	public static function product_type_options( $options ){

		$options['wcopc'] = array(
			'id'            => '_wcopc',
			'wrapper_class' => '',
			'label'         => __( 'One Page Checkout', 'wcopc'),
			'description'   => __( 'Add checkout to product page.', 'wcopc'),
			'default'       => 'no'
		);

		return $options;

	}

	/*
	 * Save extra meta info
	 */
	public static function save_product_meta( $post_id, $post ) {

		$product_type 	= empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		if ( isset( $_POST['_wcopc'] ) ) {
			update_post_meta( $post_id, '_wcopc', 'yes' );
		} else {
			update_post_meta( $post_id, '_wcopc', 'no' );
		}

	}

	/**
	 * Append opc checkout form template to core single product template if enabled
	 */
	public static function single_product_wcopc() {
		if ( is_wcopc_checkout() && is_product() ) {

			do_action( 'wcopc_before_display_checkout' );

			// Show non-cart errors
			wc_print_notices();

			WC()->cart->calculate_totals();

			// Get checkout object for WC 2.0+
			$checkout = WC()->checkout();

			wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout )  );

		}

	}

	/**
	 * Modifications to the core single product section when opc is enabled
	 */
	public static function filter_single_product_wcopc() {
		if ( is_wcopc_checkout() ) {
			// modify add to cart text
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'modify_single_add_to_cart_text' ) );

			// remove upsells & related products
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

			if ( is_product() ) {
				$product = wc_get_product();

				// show the shipping fields if needed
				if ( ! empty( $product ) && 'yes' == wcopc_get_products_prop( $product, 'wcopc', '_' ) ) {
					if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
						$products = array_filter( array_map( 'wc_get_product',
							wcopc_get_all_child_products( $product ) ) );
					} else {
						$products = array( $product );
					}

					self::maybe_show_shipping( $products );
				}
			}

			if ( is_page() ) {
				// Removes tabs when we're using a page as OPC.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			}
		}
	}

	/**
	 * Make sure a session is set whenever loading an OPC page.
	 *
	 * WC 2.3.9 started using the customer_id in the session on the logged out user nonce (introduced with
	 * https://github.com/woothemes/woocommerce/commit/242d7e76c5839c0461f583fd976522d0867aec07).
	 * This meant that if the customer:
	 * 1. loading an OPC page when they were not logged in an had no ideas in the cart, there would be no session
	 *    so the nonce set on the page at the time of load would not use the session's customer_id
	 * 2. once the customer added an item to the cart from the OPC page, the session would be set, but then the
	 *    verificaiton of the nonce would fail, as the first nonce used no user ID and the verification is checking
	 *    for a nonce with the session's customer_id
	 *
	 * @since 1.2.1
	 */
	public static function maybe_set_session() {
		if ( is_wcopc_checkout() && ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
	}

	/**
	 * Re-enables robots on OPC pages.
	 *
	 * WC 3.2 started adding no-index tags to checkout pages, this was introduced on
	 * https://github.com/woocommerce/woocommerce/blob/master/includes/wc-template-functions.php#L3233-L3243
	 *
	 * @since 1.5.6
	 */
	public static function maybe_enable_robots() {
		if ( is_wcopc_checkout() ) {
			remove_action( 'wp_head', 'wc_page_noindex' );
		}
	}

	/**
	 * Make sure shipping address fields are displayed if any of the available products require shipping
	 *
	 * @param array $products
	 *
	 * @since 1.2.2
	 */
	public static function maybe_show_shipping( $products ) {
		if ( empty( $products ) || ! isset( WC()->cart ) ) {
			return;
		}

		if ( 'no' === get_option( 'woocommerce_calc_shipping' ) || WC()->cart->needs_shipping_address() || wc_ship_to_billing_address_only() ) {
			return;
		}

		foreach ( $products as $product ) {
			if ( $product->has_child() ) {
				$visible_children = wcopc_get_visible_children( $product );

				foreach ( $visible_children as $child_id ) {
					$child = wc_get_product( $child_id );

					if ( $child->needs_shipping() ) {
						add_filter( 'woocommerce_cart_needs_shipping', '__return_true' );

						return;
					}
				}
			} else {
				if ( $product->needs_shipping() ) {
					add_filter( 'woocommerce_cart_needs_shipping', '__return_true' );

					return;
				}
			}
		}
	}

	/**
	 * Check if the installed version of WooCommerce is older than 2.3.
	 *
	 * @since 1.2.4
	 */
	public static function is_woocommerce_pre( $version ) {
		if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, $version, '<' ) ) {
			$woocommerce_is_pre = true;
		} else {
			$woocommerce_is_pre = false;
		}

		return $woocommerce_is_pre;
	}

	/**
	 * Add and then remove our filter on woocommerce_cart_needs_payment to ensure payment methods are output on page load
	 *
	 * @since 1.2.5
	 */
	public static function maybe_toggle_cart_needs_payment() {

		if ( false === WC()->cart->needs_payment() && false === self::$needs_payment_changed ) {
			add_filter( 'woocommerce_cart_needs_payment', '__return_true' );
			self::$needs_payment_changed = true;
		} elseif ( true === self::$needs_payment_changed ) {
			remove_filter( 'woocommerce_cart_needs_payment', '__return_true' );
			self::$needs_payment_changed = false;
		}
	}

	/**
	 * Make the un modified guest checkout option value available to opc and ensure the woocommerce js attaches to the create account checkbox (we'll hide it using js)
	 */
	public static function filter_woocommerce_script_paramaters( $woocommerce_params, $handle = '' ) {

		// WC 3.3+ deprecates handle-specific filters in favour of 'woocommerce_get_script_data'.
		if ( 'woocommerce_get_script_data' === current_filter() && ! in_array( $handle, array( 'woocommerce', 'wc-checkout' ) ) ) {
			return $woocommerce_params;
		}

		if ( self::is_any_form_of_opc_page() ) {
			$woocommerce_params['wcopc_option_guest_checkout'] = get_option( 'woocommerce_enable_guest_checkout' );
			$woocommerce_params['option_guest_checkout'] = 'yes';
		}
		return $woocommerce_params;
	}

	/**
	 * Do not autofocus the First Name address field on OPC pages in WooCommerce 3.0 and newer to avoid the
	 * browser scrolling to that field (and potentially past OPC product selection fields).
	 */
	public static function filter_default_address_fields( $fields ) {

		if ( self::is_any_form_of_opc_page() && isset( $fields['first_name']['autofocus'] ) && true === $fields['first_name']['autofocus'] ) {
			$fields['first_name']['autofocus'] = false;
		}

		return $fields;
	}

	/**
	 * Maybe adds products to cart if:
	 * - $_GET contains add-to-cart and is true, or..
	 * - shortcode_atts contain add_to_cart and is true
	 *
	 * @since 1.7.0
	 */
	public static function maybe_add_products_to_cart() {
		if ( ( empty( $_GET['add-to-cart'] ) || 'true' !== trim( $_GET['add-to-cart'] ) ) && ( ! isset( self::$raw_shortcode_atts['add_to_cart'] ) || 'true' !== self::$raw_shortcode_atts['add_to_cart'] ) ) {
			return;
		}

		$product_ids = self::get_product_ids_to_display();

		if ( empty( $product_ids ) ) {
			$product_ids = self::get_product_ids_in_categories_to_display();
		}

		foreach ( $product_ids as $product_id ) {
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				$found = false;
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->get_id() == $product_id ) {
						$found = true;
					}
				}
				// if product not found, add it
				if ( ! $found ) {
					WC()->cart->add_to_cart( $product_id );
				}
			} else {
				// if no products in cart, add it
				WC()->cart->add_to_cart( $product_id );
			}
		}
	}

	/**
	 * Returns the products ids to display in an array, returns empty array if no products.
	 *
	 * @return array
	 * @since 1.7.0
	 */
	private static function get_product_ids_to_display() {
		$product_ids = array();

		foreach ( explode( ',', self::$products_to_display ) as $product_id ) {
			if ( absint( $product_id ) ) {
				$product_ids[] = absint( $product_id );
			}
		}

		return $product_ids;
	}

	/**
	 * Returns the category ids to display in an array, returns empty array if no categories.
	 *
	 * @return array
	 * @since 1.7.0
	 */
	private static function get_category_ids_to_display() {
		$category_ids = array();

		foreach ( explode( ',', self::$categories_to_display ) as $category_id ) {
			if ( absint( $category_id ) ) {
				$category_ids[] = absint( $category_id );
			}
		}

		return $category_ids;
	}

	/**
	 * Based on the category ids selected to display, will return all the product ids that contain (one of the )the categories, returns empty array if there are no products.
	 *
	 * @return array
	 * @since 1.7.0
	 */
	private static function get_product_ids_in_categories_to_display() {
		$category_ids = self::get_category_ids_to_display();
		$product_ids  = get_objects_in_term( $category_ids, 'product_cat' );

		if ( empty( $category_ids ) || empty( $product_ids ) ) {
			return array();
		}

		return wc_get_products( array(
			'return'  => 'ids',
			'include' => $product_ids,
		) );
	}

	/**
	 * Deprecated Functions
	 */

	/**
	 * This was a helper function to get the URL of a given file but it did not reliably work on Windows so has been removed.
	 *
	 * As this plugin may be used as both a stand-alone plugin and as a submodule of
	 * a theme, the standard WP API functions, like plugins_url() can not be used.
	 *
	 * @since 1.0
	 * @return string URL to this file
	 */
	public static function get_url( $file ) {
		_deprecated_function( __METHOD__, '1.1.2' );

		$post_content_path = substr( dirname( __FILE__ ), strpos( __FILE__, basename( WP_CONTENT_DIR ) ) + strlen( basename( WP_CONTENT_DIR ) ) );

		// Return a content URL for this path & the specified file
		return content_url( $post_content_path . $file );
	}

	/**
	 * Runs just before @see woocommerce_ajax_update_order_review() and terminates the current request if
	 * the cart is empty to prevent WooCommerce printing an error that doesn't apply on one page checkout purchases.
	 *
	 * @since      1.0
	 * @deprecated 1.7.0
	 */
	public static function short_circuit_ajax_update_order_review() {
		wc_deprecated_function( __METHOD__, '1.7.0' );
	}

	/**
	 * Conditionally load custom OPC order review template to include Remove/Quantity columns.
	 * Must be done only when viewing an OPC page.
	 *
	 * @return void
	 * @deprecated 1.7.0
	 */
	public static function opc_order_review_template_actions() {
		wc_deprecated_function( __METHOD__, '1.7.0' );
	}
}
