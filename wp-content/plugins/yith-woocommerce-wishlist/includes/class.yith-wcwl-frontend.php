<?php
/**
 * Init class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Frontend' ) ) {
	/**
	 * Initiator class. Install the plugin database and load all needed stuffs.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Frontend
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = '3.0.14';

		/**
		 * Plugin database version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $db_version = '3.0.0';

		/**
		 * Store class yith_WCWL_Install.
		 *
		 * @var object
		 * @access private
		 * @since 1.0.0
		 */
		protected $_yith_wcwl_install;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Frontend
		 * @since 2.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			define( 'YITH_WCWL_VERSION', $this->version );
			define( 'YITH_WCWL_DB_VERSION', $this->db_version );

			$this->_yith_wcwl_install = YITH_WCWL_Install();

			// add filter for font-awesome compatibility
			add_filter( 'option_yith_wcwl_add_to_wishlist_icon', array( $this, 'update_font_awesome_classes' ) );
			add_filter( 'option_yith_wcwl_add_to_cart_icon', array( $this, 'update_font_awesome_classes' ) );

			// init class
			add_action( 'init', array( $this, 'init' ), 0 );

			// templates
			add_action( 'init', array( $this, 'add_button' ) );
			add_filter( 'body_class', array( $this, 'add_body_class' ) );
			add_action( 'template_redirect', array( $this, 'add_nocache_headers' ) );
			add_action( 'yith_wcwl_before_wishlist_title', array( $this, 'print_notices' ) );
			add_action( 'yith_wcwl_wishlist_before_wishlist_content', array( $this, 'wishlist_header' ), 10, 1 );
			add_action( 'yith_wcwl_wishlist_main_wishlist_content', array( $this, 'main_wishlist_content' ), 10, 1 );
			add_action( 'yith_wcwl_wishlist_after_wishlist_content', array( $this, 'wishlist_footer' ), 10, 1 );

			// template modifications
			add_filter( 'woocommerce_post_class', array( $this, 'add_products_class_on_loop' ) );

			// scripts
			add_action( 'wp_head', array( $this, 'detect_javascript' ), 0 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_stuffs' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// add YITH WooCommerce Frequently Bought Together Premium shortcode
			add_action( 'yith_wcwl_after_wishlist_form', array( $this, 'yith_wcfbt_shortcode' ), 10, 1 );
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'yith_wfbt_redirect_after_add_to_cart' ), 10, 1 );

			// YITH WCWL Loaded
			do_action( 'yith_wcwl_loaded' );
		}

		/**
		 * Initiator method.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init() {
			// update cookie from old version to new one
			$this->_update_cookies();
			$this->_destroy_serialized_cookies();
			$this->_convert_cookies_to_session();

			// register assets
			$this->register_styles();
			$this->register_scripts();
		}

		/* === ADD TO WISHLIST */

		/**
		 * Add the "Add to Wishlist" button. Needed to use in wp_head hook.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_button() {
			$positions = apply_filters( 'yith_wcwl_positions', array(
				'after_add_to_cart' => array( 'hook' => 'woocommerce_single_product_summary', 'priority' => 31 ),
				'add-to-cart' => array( 'hook' => 'woocommerce_single_product_summary', 'priority' => 31 ),
				'thumbnails'  => array( 'hook' => 'woocommerce_product_thumbnails', 'priority' => 21 ),
				'summary'     => array( 'hook' => 'woocommerce_after_single_product_summary', 'priority' => 11 )
			) );

			// Add the link "Add to wishlist"
			$position = get_option( 'yith_wcwl_button_position', 'add-to-cart' );

			if ( $position != 'shortcode' && isset( $positions[ $position ] ) ) {
				add_action( $positions[ $position ]['hook'], array( $this, 'print_button' ), $positions[ $position ]['priority'] );
			}

			// check if Add to wishlist button is enabled for loop
			$enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );

			if( ! $enabled_on_loop ){
				return;
			}

			$positions = apply_filters( 'yith_wcwl_loop_positions', array(
				'before_image' => array( 'hook' => 'woocommerce_before_shop_loop_item', 'priority' => 5 ),
				'before_add_to_cart' => array( 'hook' => 'woocommerce_after_shop_loop_item', 'priority' => 7 ),
				'after_add_to_cart' => array( 'hook' => 'woocommerce_after_shop_loop_item', 'priority' => 15 )
			) );

			// Add the link "Add to wishlist"
			$position = get_option( 'yith_wcwl_loop_position', 'after_add_to_cart' );

			if ( $position != 'shortcode' && isset( $positions[ $position ] ) ) {
				add_action( $positions[ $position ]['hook'], array( $this, 'print_button' ), $positions[ $position ]['priority'] );
			}
		}

		/**
		 * Print "Add to Wishlist" shortcode
		 *
		 * @return void
		 * @since 2.2.2
		 */
		public function print_button() {
			/**
			 * Developers can use this filter to remove ATW button selectively from specific pages or products
			 * You can use global $product or $post to execute checks
			 *
			 * @since 3.0.7
			 */
			if( ! apply_filters( 'yith_wcwl_show_add_to_wishlist', true ) ){
				return;
			}

			echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
		}

		/* === WISHLIST PAGE === */

		/**
		 * Prints wc notice for wishlist pages
		 *
		 * @return void
		 * @since 2.0.5
		 */
		public function print_notices() {
			if( function_exists( 'wc_print_notices' ) ) {
				wc_print_notices();
			}
		}

		/**
		 * Add specific body class when the Wishlist page is opened
		 *
		 * @param array $classes
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function add_body_class( $classes ) {
			$wishlist_page_id = YITH_WCWL()->get_wishlist_page_id();

			if ( ! empty( $wishlist_page_id ) && is_page( $wishlist_page_id ) ) {
				$classes[] = 'woocommerce-wishlist';
				$classes[] = 'woocommerce';
				$classes[] = 'woocommerce-page';
			}

			return $classes;
		}

		/**
		 * Send nocache headers on wishlist page
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function add_nocache_headers() {
			if( ! headers_sent() && yith_wcwl_is_wishlist_page() ){
				wc_nocache_headers();
			}
		}

		/* === SCRIPTS AND ASSETS === */

		/**
		 * Register styles required by the plugin
		 *
		 * @return void
		 */
		public function register_styles() {
			$woocommerce_base = WC()->template_path();
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			// register dependencies
			wp_register_style( 'jquery-selectBox', YITH_WCWL_URL . 'assets/css/jquery.selectBox.css', array(), '1.2.0' );
			wp_register_style( 'yith-wcwl-font-awesome', YITH_WCWL_URL . 'assets/css/font-awesome.css', array(), '4.7.0' );
			wp_register_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );

			// register main style
			$located = locate_template( array(
				$woocommerce_base . 'wishlist.css',
				'wishlist.css'
			) );

			if( ! $located ){
				wp_register_style( 'yith-wcwl-main', YITH_WCWL_URL . 'assets/css/style.css', array( 'jquery-selectBox', 'yith-wcwl-font-awesome' ), $this->version );
			}
			else{
				$stylesheet_directory = get_stylesheet_directory();
				$stylesheet_directory_uri = get_stylesheet_directory_uri();
				$template_directory = get_template_directory();
				$template_directory_uri = get_template_directory_uri();

				$style_url = ( strpos( $located, $stylesheet_directory ) !== false ) ? str_replace( $stylesheet_directory, $stylesheet_directory_uri, $located ) : str_replace( $template_directory, $template_directory_uri, $located );

				wp_register_style( 'yith-wcwl-user-main', $style_url, array( 'jquery-selectBox', 'yith-wcwl-font-awesome' ), $this->version );
			}

			// theme specific assets
			$current_theme = wp_get_theme();

			if( $current_theme->exists() ){
				$theme_slug = $current_theme->Template;

				if( file_exists( YITH_WCWL_DIR . 'assets/css/themes/' . $theme_slug . '.css' ) ){
					wp_register_style( 'yith-wcwl-theme', YITH_WCWL_URL . 'assets/css/themes/' . $theme_slug . '.css', array( $located ? 'yith-wcwl-user-main' : 'yith-wcwl-main' ), $this->version );
				}
			}
		}

		/**
		 * Register scripts required by the plugin
		 *
		 * @return void
		 */
		public function register_scripts() {
			$woocommerce_base = WC()->template_path();
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'unminified/' : '';

			$located = locate_template( array(
				$woocommerce_base . 'wishlist.js',
				'wishlist.js'
			) );

			wp_register_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.6', true );
			wp_register_script( 'jquery-selectBox', YITH_WCWL_URL . 'assets/js/jquery.selectBox.min.js', array( 'jquery' ), '1.2.0', true );

			$yith_wcwl_l10n = $this->get_localize();
			$main_script_deps = apply_filters( 'yith_wcwl_main_script_deps', array( 'jquery', 'jquery-selectBox' ) );

			if ( ! $located ) {
				wp_register_script( 'jquery-yith-wcwl', YITH_WCWL_URL . 'assets/js/' . $prefix . 'jquery.yith-wcwl.js', $main_script_deps, $this->version, true );
				wp_localize_script( 'jquery-yith-wcwl', 'yith_wcwl_l10n', $yith_wcwl_l10n );
			}
			else {
				wp_register_script( 'jquery-yith-wcwl-user', str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $located ), $main_script_deps, $this->version, true );
				wp_localize_script( 'jquery-yith-wcwl-user', 'yith_wcwl_l10n', $yith_wcwl_l10n );
			}
		}

		/**
		 * Enqueue styles, scripts and other stuffs needed in the <head>.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_and_stuffs() {
			// libraries
			wp_enqueue_style( 'woocommerce_prettyPhoto_css' );
			wp_enqueue_style( 'jquery-selectBox' );
			wp_enqueue_style( 'yith-wcwl-font-awesome' );

			// main plugin style
			if ( ! wp_style_is( 'yith-wcwl-user-main', 'registered' ) ) {
				wp_enqueue_style( 'yith-wcwl-main' );
			}
			else {
				wp_enqueue_style( 'yith-wcwl-user-main' );
			}

			// theme specific style
			if( wp_style_is( 'yith-wcwl-theme', 'registered' ) ){
				wp_enqueue_style( 'yith-wcwl-theme' );;
			}

			// custom style
			$this->enqueue_custom_style();
		}

		/**
		 * Enqueue style dynamically generated by the plugin
		 *
		 * @return void
		 */
		public function enqueue_custom_style() {
			$custom_css = $this->_build_custom_css();

			if( $custom_css ) {
				$handle = wp_script_is( 'yith-wcwl-user-main' ) ? 'yith-wcwl-user-main' : 'yith-wcwl-main';

				wp_add_inline_style( $handle, $custom_css );
			}
		}

		/**
		 * Enqueue plugin scripts.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'prettyPhoto' );
			wp_enqueue_script( 'jquery-selectBox' );

			if ( ! wp_script_is( 'jquery-yith-wcwl-user', 'registered' ) ) {
				wp_enqueue_script( 'jquery-yith-wcwl' );
			}
			else {
				wp_enqueue_script( 'jquery-yith-wcwl-user' );
			}
		}

		/**
		 * Return localize array
		 *
		 * @return array Array with variables to be localized inside js
		 * @since 2.2.3
		 */
		public function get_localize() {
			return apply_filters( 'yith_wcwl_localize_script', array(
				'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ),
				'redirect_to_cart' => get_option( 'yith_wcwl_redirect_cart' ),
				'multi_wishlist' => false,
				'hide_add_button' => apply_filters( 'yith_wcwl_hide_add_button', true ),
				'enable_ajax_loading' => 'yes' == get_option( 'yith_wcwl_ajax_enable', 'no' ),
				'ajax_loader_url' => YITH_WCWL_URL . 'assets/images/ajax-loader-alt.svg',
				'remove_from_wishlist_after_add_to_cart' => get_option( 'yith_wcwl_remove_after_add_to_cart' ) == 'yes',
				'is_wishlist_responsive' => apply_filters( 'yith_wcwl_is_wishlist_responsive', true ),
				'time_to_close_prettyphoto' => apply_filters( 'yith_wcwl_time_to_close_prettyphoto', 3000 ),
				'fragments_index_glue' => apply_filters( 'yith_wcwl_fragments_index_glue', '.' ),
				'labels' => array(
					'cookie_disabled' => __( 'We are sorry, but this feature is available only if cookies on your browser are enabled.', 'yith-woocommerce-wishlist' ),
					'added_to_cart_message' => sprintf( '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message" role="alert">%s</div></div>', apply_filters( 'yith_wcwl_added_to_cart_message', __( 'Product added to cart successfully', 'yith-woocommerce-wishlist' ) ) )
				),
				'actions' => array(
					'add_to_wishlist_action' => 'add_to_wishlist',
					'remove_from_wishlist_action' => 'remove_from_wishlist',
					'reload_wishlist_and_adding_elem_action'  => 'reload_wishlist_and_adding_elem',
					'load_mobile_action' => 'load_mobile',
					'delete_item_action' => 'delete_item',
					'save_title_action' => 'save_title',
					'save_privacy_action' => 'save_privacy',
					'load_fragments' => 'load_fragments'
				)
			) );
		}

		/**
		 * Remove the class no-js when javascript is activated
		 *
		 * We add the action at the start of head, to do this operation immediatly, without gap of all libraries loading
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function detect_javascript() {
			if( ! defined( 'YIT' ) ):
				?>
				<script>document.documentElement.className = document.documentElement.className + ' yes-js js_active js'</script>
			<?php
			endif;
		}

		/* === TEMPLATES === */

		/**
		 * Include main wishlist template
		 *
		 * @var $var array Array of parameters for current view
		 * @return void
		 */
		public function main_wishlist_content( $var ) {
			$template = isset( $var['template_part'] ) ? $var['template_part'] : 'view';
			$layout = ! empty( $var['layout'] ) ? $var['layout'] : '';

			yith_wcwl_get_template_part( $template, '', $layout, $var );
		}

		/**
		 * Include wishlist header template
		 *
		 * @var $var array Array of parameters for current view
		 * @return void
		 */
		public function wishlist_header( $var ) {
			$template = isset( $var['template_part'] ) ? $var['template_part'] : 'view';
			$layout = ! empty( $var['layout'] ) ? $var['layout'] : '';

			yith_wcwl_get_template_part( $template, 'header', $layout, $var );
		}

		/**
		 * Include wishlist footer template
		 *
		 * @var $var array Array of parameters for current view
		 * @return void
		 */
		public function wishlist_footer( $var ) {
			$template = isset( $var['template_part'] ) ? $var['template_part'] : 'view';
			$layout = ! empty( $var['layout'] ) ? $var['layout'] : '';

			yith_wcwl_get_template_part( $template, 'footer', $layout, $var );
		}

		/* === TEMPLATE MODIFICATIONS === */

		/**
		 * Add class to products when Add to Wishlist is shown on loop
		 *
		 * @param $classes array Array of available classes for the product
		 * @return array Array of filtered classes for the product
		 * @since 3.0.0
		 */
		public function add_products_class_on_loop( $classes ){
			if( yith_wcwl_is_single() ){
				return $classes;
			}

			$enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );

			if( ! $enabled_on_loop ){
				return $classes;
			}

			$position = get_option( 'yith_wcwl_loop_position', 'after_add_to_cart' );

			if( 'shortcode' == $position ){
				return $classes;
			}

			$classes[] = "add-to-wishlist-$position";

			return $classes;
		}

		/* === UTILS === */

		/**
		 * Format options that will sent through AJAX calls to refresh arguments
		 *
		 * @param $options array Array of options
		 * @param $context string Widget/Shortcode that will use the options
		 * @return array Array of formatted options
		 * @since 3.0.0
		 */
		public function format_fragment_options( $options, $context = '' ) {
			// removes unusable values, and changes options common for all fragments
			if( ! empty( $options ) ){
				foreach( $options as $id => $value ){
					if( is_object( $value ) || is_array( $value ) ){
						// remove item if type is not supported
						unset( $options[ $id ] );
					}
					elseif( 'ajax_loading' == $id ){
						$options['ajax_loading'] = false;
					}
				}
			}

			// applies context specific changes
			if( ! empty( $context ) ){
				$options['item'] = $context;

				switch( $context ) {
					case 'add_to_wishlist':
						unset( $options['template_part'] );
						unset( $options['label'] );
						unset( $options['exists'] );
						unset( $options['icon'] );
						unset( $options['link_classes'] );
						unset( $options['link_popup_classes'] );
						unset( $options['container_classes'] );
						unset( $options['found_in_list'] );
						unset( $options['found_item'] );
						unset( $options['popup_title'] );
						unset( $options['wishlist_url'] );
						break;
				}
			}

			return $options;
		}

		/**
		 * Decode options that comes from the fragment
		 *
		 * @param $options array Options for the fragments
		 * @return array Filtered options for the fragment
		 */
		public function decode_fragment_options( $options ) {
			if( ! empty( $options ) ){
				foreach( $options as $id => $value ){
					if( 'true' == $value ){
						$options[ $id ] = true;
					}
					elseif( 'false' == $value ){
						$options[ $id ] = false;
					}
				}
			}

			return $options;
		}

		/**
		 * Alter add to cart button when on wishlist page
		 *
		 * @return void
		 * @since 2.0.0
		 * @version 3.0.0
		 */
		public function alter_add_to_cart_button(){
			add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'alter_add_to_cart_args' ) );
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'alter_add_to_cart_text' ), 10, 2 );
			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'alter_add_to_cart_url' ), 10, 2 );
		}

		/**
		 * Restore default Add to Cart button, after wishlist handling
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function restore_add_to_cart_button() {
			remove_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'alter_add_to_cart_args' ) );
			remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'alter_add_to_cart_text' ) );
			remove_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'alter_add_to_cart_url' ) );
		}

		/**
		 * Changes arguments used to print Add to Cart button on wishlist (classes and attributes)
		 *
		 * @param $args array Array of arguments
		 * @return array Array of filtered arguments
		 * @since 3.0.0
		 */
		public function alter_add_to_cart_args( $args ) {
			$use_custom_button = get_option( 'yith_wcwl_add_to_cart_style' );
			$button_class = in_array( $use_custom_button, array( 'button_custom', 'button_default' ) );
			$icon = get_option( 'yith_wcwl_add_to_cart_icon' );
			$custom_icon = get_option( 'yith_wcwl_add_to_cart_custom_icon' );
			$classes = isset( $args['class'] ) ? explode( ' ', $args['class'] ) : array();

			if( ! $button_class &&  $pos = array_search( 'button', $classes ) !== false ){
				unset( $classes[ $pos ] );
			}
			elseif( $button_class ){
				$classes[] = 'button';
			}

			$classes[] = 'add_to_cart';
			$classes[] = 'alt';

			$args['class'] = implode( ' ', $classes );

			if( 'button_custom' == $use_custom_button && $icon != 'none' ) {
			   if( ! isset( $args['attributes'] ) ){
				   $args['attributes'] = array();
			   }

			   if( $icon != 'custom' ) {
				   $args['attributes']['data-icon'] = $icon;
			   }
			   elseif( $custom_icon ){
				   $args['attributes']['data-icon'] = $custom_icon;
			   }
			}

			return $args;
		}

		/**
		 * Filter Add to Cart button label on wishlist page
		 *
		 * @param $text string Button label
		 * @param \WC_Product Current product
		 * @return string Filtered label
		 */
		public function alter_add_to_cart_text( $text, $product ) {
			$label_option = get_option( 'yith_wcwl_add_to_cart_text', __( 'Add to cart', 'yith-woocommerce-wishlist' ) );
			$label = $product->is_type( 'variable' ) ? $text : apply_filters( 'yith_wcwl_add_to_cart_label', $label_option );

			return $label;
		}

		/**
		 * Filter Add to Cart button url on wishlist page
		 *
		 * @param $url string Url to the Add to Cart
		 * @param $product \WC_Product Current product
		 * @return string Filtered url
		 */
		public function alter_add_to_cart_url( $url, $product ) {
			global $yith_wcwl_wishlist_token;

			if( $yith_wcwl_wishlist_token ){
				$wishlist = yith_wcwl_get_wishlist( $yith_wcwl_wishlist_token );

				if( ! $wishlist ){
					return $url;
				}

				$wishlist_id = $wishlist->get_id();
				$item = $wishlist->get_product( $product->get_id() );

				if( wp_doing_ajax() ){
					$url = add_query_arg( 'add-to-cart', $product->get_id(), YITH_WCWL()->get_wishlist_url( 'view/' .  $yith_wcwl_wishlist_token ) );
				}

				if( $product->is_type( array( 'simple', 'variation' ) ) && get_option( 'yith_wcwl_redirect_cart' ) == 'yes' ){
					$url = add_query_arg( 'add-to-cart', $product->get_id(), wc_get_cart_url() );
				}

				if( ! $product->is_type( 'external' ) && get_option( 'yith_wcwl_remove_after_add_to_cart' ) == 'yes' ){
					$url = add_query_arg(
						array(
							'remove_from_wishlist_after_add_to_cart' => $product->get_id(),
							'wishlist_id' => $wishlist_id,
							'wishlist_token' => $yith_wcwl_wishlist_token
						),
						$url
					);
				}

				if( $item && 'yes' == get_option( 'yith_wcwl_quantity_show' ) ){
					$url = add_query_arg( 'quantity', $item->get_quantity(), $url );
				}
			}

			return apply_filters( 'yit_wcwl_add_to_cart_redirect_url', esc_url_raw( $url ), $url, $product );
		}

		/**
		 * Modernize font-awesome class, for old wishlist users
		 *
		 * @param $class string Original font-awesome class
		 * @return string Filtered font-awesome class
		 * @since 2.0.2
		 */
		public function update_font_awesome_classes( $class ) {
			$exceptions = array(
				'icon-envelope' => 'fa-envelope-o',
				'icon-star-empty' => 'fa-star-o',
				'icon-ok' => 'fa-check',
				'icon-zoom-in' => 'fa-search-plus',
				'icon-zoom-out' => 'fa-search-minus',
				'icon-off' => 'fa-power-off',
				'icon-trash' => 'fa-trash-o',
				'icon-share' => 'fa-share-square-o',
				'icon-check' => 'fa-check-square-o',
				'icon-move' => 'fa-arrows',
				'icon-file' => 'fa-file-o',
				'icon-time' => 'fa-clock-o',
				'icon-download-alt' => 'fa-download',
				'icon-download' => 'fa-arrow-circle-o-down',
				'icon-upload' => 'fa-arrow-circle-o-up',
				'icon-play-circle' => 'fa-play-circle-o',
				'icon-indent-left' => 'fa-dedent',
				'icon-indent-right' => 'fa-indent',
				'icon-facetime-video' => 'fa-video-camera',
				'icon-picture' => 'fa-picture-o',
				'icon-plus-sign' => 'fa-plus-circle',
				'icon-minus-sign' => 'fa-minus-circle',
				'icon-remove-sign' => 'fa-times-circle',
				'icon-ok-sign' => 'fa-check-circle',
				'icon-question-sign' => 'fa-question-circle',
				'icon-info-sign' => 'fa-info-circle',
				'icon-screenshot' => 'fa-crosshairs',
				'icon-remove-circle' => 'fa-times-circle-o',
				'icon-ok-circle' => 'fa-check-circle-o',
				'icon-ban-circle' => 'fa-ban',
				'icon-share-alt' => 'fa-share',
				'icon-resize-full' => 'fa-expand',
				'icon-resize-small' => 'fa-compress',
				'icon-exclamation-sign' => 'fa-exclamation-circle',
				'icon-eye-open' => 'fa-eye',
				'icon-eye-close' => 'fa-eye-slash',
				'icon-warning-sign' => 'fa-warning',
				'icon-folder-close' => 'fa-folder',
				'icon-resize-vertical' => 'fa-arrows-v',
				'icon-resize-horizontal' => 'fa-arrows-h',
				'icon-twitter-sign' => 'fa-twitter-square',
				'icon-facebook-sign' => 'fa-facebook-square',
				'icon-thumbs-up' => 'fa-thumbs-o-up',
				'icon-thumbs-down' => 'fa-thumbs-o-down',
				'icon-heart-empty' => 'fa-heart-o',
				'icon-signout' => 'fa-sign-out',
				'icon-linkedin-sign' => 'fa-linkedin-square',
				'icon-pushpin' => 'fa-thumb-tack',
				'icon-signin' => 'fa-sign-in',
				'icon-github-sign' => 'fa-github-square',
				'icon-upload-alt' => 'fa-upload',
				'icon-lemon' => 'fa-lemon-o',
				'icon-check-empty' => 'fa-square-o',
				'icon-bookmark-empty' => 'fa-bookmark-o',
				'icon-phone-sign' => 'fa-phone-square',
				'icon-hdd' => 'fa-hdd-o',
				'icon-hand-right' => 'fa-hand-o-right',
				'icon-hand-left' => 'fa-hand-o-left',
				'icon-hand-up' => 'fa-hand-o-up',
				'icon-hand-down' => 'fa-hand-o-down',
				'icon-circle-arrow-left' => 'fa-arrow-circle-left',
				'icon-circle-arrow-right' => 'fa-arrow-circle-right',
				'icon-circle-arrow-up' => 'fa-arrow-circle-up',
				'icon-circle-arrow-down' => 'fa-arrow-circle-down',
				'icon-fullscreen' => 'fa-arrows-alt',
				'icon-beaker' => 'fa-flask',
				'icon-paper-clip' => 'fa-paperclip',
				'icon-sign-blank' => 'fa-square',
				'icon-pinterest-sign' => 'fa-pinterest-square',
				'icon-google-plus-sign' => 'fa-google-plus-square',
				'icon-envelope-alt' => 'fa-envelope',
				'icon-comment-alt' => 'fa-comment-o',
				'icon-comments-alt' => 'fa-comments-o'
			);

			if( in_array( $class, array_keys( $exceptions ) ) ){
				$class = $exceptions[ $class ];
			}

			$class = str_replace( 'icon-', 'fa-', $class );

			return $class;
		}

		/**
		 * Add Frequently Bought Together shortcode to wishlist page
		 *
		 * @param mixed $meta
		 * @author Francesco Licandro
		 */
		public function yith_wcfbt_shortcode( $meta ){

			if( ! ( defined( 'YITH_WFBT' ) && YITH_WFBT ) || get_option( 'yith_wfbt_enable_integration' ) == 'no' ) {
				return;
			}

			$products = YITH_WCWL()->get_products(
				array(
					'wishlist_id' => is_user_logged_in() ? $meta['ID'] : ''
				));

			$ids   = array();
			// take id of products in wishlist
			foreach( $products as $product ) {
				$ids[] = $product['prod_id'];
			}

			if( empty( $ids ) ) {
				return;
			}

			do_shortcode( '[yith_wfbt products="' . implode( ',', $ids ) . '"]' );
		}

		/**
		 * Redirect after add to cart from YITH WooCommerce Frequently Bought Together Premium shortcode
		 *
		 * @since 2.0.0
		 */
		public function yith_wfbt_redirect_after_add_to_cart( $url ){
			if( ! isset( $_REQUEST['yith_wfbt_shortcode'] ) ) {
				return $url;
			}

			return get_option( 'yith_wcwl_redirect_cart' ) == 'yes' ? wc_get_cart_url() : YITH_WCWL()->get_wishlist_url();
		}

		/**
		 * Generate CSS code to append to each page, to apply custom style to wishlist elements
		 *
		 * @param $rules array Array of additional rules to add to default ones
		 * @return string Generated CSS code
		 */
		protected function _build_custom_css( $rules = array() ){
			$generated_code = '';
			$rules = apply_filters( 'yith_wcwl_custom_css_rules', array_merge( array(
				'color_add_to_wishlist' => array(
					'selector' => '.woocommerce a.add_to_wishlist.button.alt',
					'rules'    => array(
						'background' => array(
							'rule'    => 'background-color: %1$s; background: %1$s',
							'default' => '#333333'
						),
						'text' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff'
						),
						'border' => array(
							'rule'    => 'border-color: %s',
							'default' => '#333333'
						),
						'background_hover' => array(
							'rule'    => 'background-color: %1$s; background: %1$s',
							'default' => '#4F4F4F',
							'status' => ':hover'
						),
						'text_hover' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff',
							'status' => ':hover'
						),
						'border_hover' => array(
							'rule'    => 'border-color: %s',
							'default' => '#4F4F4F',
							'status' => ':hover'
						)
					),
					'deps' => array(
						'yith_wcwl_add_to_wishlist_style' => 'button_custom'
					)
				),
				'rounded_corners_radius' => array(
					'selector' => '.woocommerce a.add_to_wishlist.button.alt',
					'rules' => array(
						'rule' => 'border-radius: %dpx',
						'default' => 16
					),
					'deps' => array(
						'yith_wcwl_add_to_wishlist_style' => 'button_custom'
					)
				),
				'color_add_to_cart' => array(
					'selector' => '.woocommerce .wishlist_table a.add_to_cart.button.alt',
					'rules'    => array(
						'background' => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#333333'
						),
						'text' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff'
						),
						'border' => array(
							'rule'    => 'border-color: %s',
							'default' => '#333333'
						),
						'background_hover' => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#4F4F4F',
							'status'  => ':hover'
						),
						'text_hover' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff',
							'status'  => ':hover'
						),
						'border_hover' => array(
							'rule'    => 'border-color: %s',
							'default' => '#4F4F4F',
							'status'  => ':hover'
						)
					),
					'deps' => array(
						'yith_wcwl_add_to_cart_style' => 'button_custom'
					)
				),
				'add_to_cart_rounded_corners_radius' => array(
					'selector' => '.woocommerce .wishlist_table a.add_to_cart.button.alt',
					'rules' => array(
						'rule' => 'border-radius: %dpx',
						'default' => 16
					),
					'deps' => array(
						'yith_wcwl_add_to_cart_style' => 'button_custom',
					)
				),
				'color_button_style_1' => array(
					'selector' => '.woocommerce .hidden-title-form button,
								   .yith-wcwl-wishlist-new .create-wishlist-button,
								   .wishlist_manage_table tfoot button.submit-wishlist-changes,
								   .yith-wcwl-wishlist-search-form button.wishlist-search-button',
					'rules'    => array(
						'background' => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#333333'
						),
						'text' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff'
						),
						'border' => array(
							'rule'    => 'border-color: %s',
							'default' => '#333333'
						),
						'background_hover' => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#333333',
							'status'  => ':hover'
						),
						'text_hover' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff',
							'status'  => ':hover'
						),
						'border_hover' => array(
							'rule'    => 'border-color: %s',
							'default' => '#333333',
							'status'  => ':hover'
						)
					),
					'deps' => array(
						'yith_wcwl_add_to_cart_style' => 'button_custom'
					)
				),
				'color_button_style_2' => array(
					'selector' => '.woocommerce .wishlist-title a.show-title-form,
								   .woocommerce .hidden-title-form a.hide-title-form,
								   .wishlist_manage_table tfoot a.create-new-wishlist',
					'rules'    => array(
						'background' => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#333333'
						),
						'text' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff'
						),
						'border' => array(
							'rule'    => 'border-color: %s',
							'default' => '#333333'
						),
						'background_hover' => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#333333',
							'status'  => ':hover'
						),
						'text_hover' => array(
							'rule'    => 'color: %s',
							'default' => '#ffffff',
							'status'  => ':hover'
						),
						'border_hover' => array(
							'rule'    => 'border-color: %s',
							'default' => '#333333',
							'status'  => ':hover'
						)
					),
					'deps' => array(
						'yith_wcwl_add_to_cart_style' => 'button_custom'
					)
				),
				'color_wishlist_table' => array(
					'selector' => '.woocommerce table.shop_table.wishlist_table tr td',
					'rules'    => array(
						'background'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#FFFFFF'
						),
						'text'        => array(
							'rule'    => 'color: %s',
							'default' => '#6D6C6C'
						),
						'border'      => array(
							'rule'    => 'border-color: %s;',
							'default' => '#FFFFFF'
						)
					),
					'deps' => array(
						'yith_wcwl_add_to_cart_style' => 'button_custom'
					)
				),
				'color_headers_background' => array(
					'selector' => '.wishlist_table thead tr th,
								   .wishlist_table tfoot td td,
								   .widget_yith-wcwl-lists ul.dropdown li.current a,
								   .widget_yith-wcwl-lists ul.dropdown li a:hover,
								   .selectBox-dropdown-menu.selectBox-options li.selectBox-selected a,
								   .selectBox-dropdown-menu.selectBox-options li.selectBox-hover a',
					'rules' => array(
						'rule' => 'background: %1$s; background-color: %1$s;',
						'default' => '#F4F4F4'
					),
					'deps' => array(
						'yith_wcwl_add_to_cart_style' => 'button_custom'
					)
				),
				'color_share_button' => array(
					'selector' => '.yith-wcwl-share li a',
					'rules'    => array(
						'color'  => array(
							'rule'    => 'color: %s;',
							'default' => '#FFFFFF'
						),
						'color_hover'  => array(
							'rule'    => 'color: %s;',
							'status'  => ':hover',
							'default' => '#FFFFFF'
						)
					),
					'deps' => array(
						'yith_wcwl_enable_share' => 'yes'
					)
				),
				'color_fb_button' => array(
					'selector' => '.yith-wcwl-share a.facebook',
					'rules'    => array(
						'background'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#39599E'
						),
						'background_hover'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'status'  => ':hover',
							'default' => '#39599E'
						)
					),
					'deps' => array(
						'yith_wcwl_enable_share' => 'yes',
						'yith_wcwl_share_fb' => 'yes',
					)
				),
				'color_tw_button' => array(
					'selector' => '.yith-wcwl-share a.twitter',
					'rules'    => array(
						'background'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#45AFE2'
						),
						'background_hover'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'status'  => ':hover',
							'default' => '#39599E'
						)
					),
					'deps' => array(
						'yith_wcwl_enable_share' => 'yes',
						'yith_wcwl_share_twitter' => 'yes',
					)
				),
				'color_pr_button' => array(
					'selector' => '.yith-wcwl-share a.pinterest',
					'rules'    => array(
						'background'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#AB2E31'
						),
						'background_hover'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'status'  => ':hover',
							'default' => '#39599E'
						)
					),
					'deps' => array(
						'yith_wcwl_enable_share' => 'yes',
						'yith_wcwl_share_pinterest' => 'yes',
					)
				),
				'color_em_button' => array(
					'selector' => '.yith-wcwl-share a.email',
					'rules'    => array(
						'background'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#FBB102'
						),
						'background_hover'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'status'  => ':hover',
							'default' => '#39599E'
						)
					),
					'deps' => array(
						'yith_wcwl_enable_share' => 'yes',
						'yith_wcwl_share_email' => 'yes',
					)
				),
				'color_wa_button' => array(
					'selector' => '.yith-wcwl-share a.whatsapp',
					'rules'    => array(
						'background'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'default' => '#00A901'
						),
						'background_hover'  => array(
							'rule'    => 'background: %1$s; background-color: %1$s;',
							'status'  => ':hover',
							'default' => '#39599E'
						)
					),
					'deps' => array(
						'yith_wcwl_enable_share' => 'yes',
						'yith_wcwl_share_whatsapp' => 'yes',
					)
				),
			), $rules  ) );

			if( empty( $rules ) ){
				return $generated_code;
			}

			// retrieve dependencies
			$deps_list = wp_list_pluck( $rules, 'deps' );
			$dependencies = array();

			if( ! empty( $deps_list ) ){
				foreach( $deps_list as $rule => $deps ){
					foreach( $deps as $dep_rule => $dep_value ){
						if( ! isset( $dependencies[ $dep_rule ] ) ){
							$dependencies[ $dep_rule ] = get_option( $dep_rule );
						}
					}
				}
			}

			foreach( $rules as $id => $rule ){
				// check dependencies first
				if( ! empty( $rule['deps'] ) ){
					foreach( $rule['deps'] as $dep_rule => $dep_value ){
						if( ! isset( $dependencies[ $dep_rule ] ) || $dependencies[ $dep_rule ] != $dep_value ){
							continue 2;
						}
					}
				}

				// retrieve values from db
				$values = get_option( "yith_wcwl_{$id}" );
				$new_rules = array();
				$rules_code = '';

				// if we have a single-valued option, just search for the rule to apply
				if( isset( $rule['rules']['rule'] ) ){
					$status = isset( $rule['rules']['status'] ) ? $rule['rules']['status'] : '';

					if( ! isset( $new_rules[ $status ] ) ){
						$new_rules[ $status ] = array();
					}

					$new_rules[ $status ][] = $this->_build_css_rule( $rule['rules']['rule'], $values, $rule['rules']['default'] );
				}

				// otherwise cycle through rules, and generate CSS code
				else{
					foreach( $rule['rules'] as $property => $css ){
						$status = isset( $css['status'] ) ? $css['status'] : '';

						if( ! isset( $new_rules[ $status ] ) ){
							$new_rules[ $status ] = array();
						}

						$new_rules[ $status ][] = $this->_build_css_rule( $css['rule'], isset( $values[ $property ] ) ? $values[ $property ] : false, $css['default'] );
					}
				}

				// if code was generated, prepend selector
				if( ! empty( $new_rules ) ){
					foreach( $new_rules as $status => $rules ){
						$selector = $rule['selector'];

						if( ! empty( $status ) ){
							$updated_selector = array();
							$split_selectors = explode( ',', $rule['selector'] );

							foreach( $split_selectors as $split_selector ){
								$updated_selector[] = $split_selector . $status;
							}

							$selector = implode( ',', $updated_selector );
						}

						$rules_code .= $selector . '{' . implode( '', $rules ) . '}';
					}
				}

				// append new rule to generated CSS
				$generated_code .= $rules_code;
			}

			if( $custom_css = get_option( 'yith_wcwl_custom_css' ) ){
				$generated_code .= $custom_css;
			}

			return $generated_code;
		}

		/**
		 * Generate each single CSS rule that will be included in custom plugin CSS
		 *
		 * @param $rule string Rule to use; placeholders may be applied to be replaced with value {@see sprintf}
		 * @param $value string Value to inject inside rule, replacing placeholders
		 * @param $default string Default value, to be used instead of value when it is empty
		 *
		 * @return string Formatted CSS rule
		 */
		protected function _build_css_rule( $rule, $value, $default = '' ){
			$value = ( '0' === $value || ( ! empty( $value ) && ! is_array( $value ) ) ) ? $value : $default;

			return sprintf( rtrim( $rule, ';' ) . ';', $value );
		}

		/**
		 * Destroy serialize cookies, to prevent major vulnerability
		 *
		 * @return void
		 * @since 2.0.7
		 */
		protected function _destroy_serialized_cookies(){
			$name = 'yith_wcwl_products';

			if ( isset( $_COOKIE[$name] ) && is_serialized( stripslashes( $_COOKIE[ $name ] ) ) ) {
				$_COOKIE[ $name ] = json_encode( array() );
				yith_destroycookie( $name );
			}
		}

		/**
		 * Update old wishlist cookies
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function _update_cookies(){
			$cookie = yith_getcookie( 'yith_wcwl_products' );
			$new_cookie = array();

			if( ! empty( $cookie ) ) {
				foreach ( $cookie as $item ) {
					if ( ! isset( $item['add-to-wishlist'] ) ) {
						return;
					}

					$new_cookie[] = array(
						'prod_id'     => $item['add-to-wishlist'],
						'quantity'    => isset( $item['quantity'] ) ? $item['quantity'] : 1,
						'wishlist_id' => false
					);
				}

				yith_setcookie( 'yith_wcwl_products', $new_cookie );
			}
		}

		/**
	 	* Convert wishlist stored into cookies into
		 */
		protected function _convert_cookies_to_session(){
			$cookie = yith_getcookie( 'yith_wcwl_products' );

			if( ! empty( $cookie ) ){

				$default_list = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

				if( ! $default_list ){
					return false;
				}

				foreach ( $cookie as $item ){
					if( $default_list->has_product( $item['prod_id'] ) ){
						continue;
					}

					$new_item = new YITH_WCWL_Wishlist_Item();

					$new_item->set_product_id( $item['prod_id'] );
					$new_item->set_quantity( $item['quantity'] );

					if( isset( $item['dateadded'] ) ){
						$new_item->set_date_added( $item['dateadded'] );
					}

					$default_list->add_item( $new_item );
				}

				$default_list->save();

				yith_destroycookie( 'yith_wcwl_products' );
            }
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Frontend class
 *
 * @return \YITH_WCWL_Frontend|\YITH_WCWL_Frontend_Premium
 * @since 2.0.0
 */
function YITH_WCWL_Frontend(){
	return defined( 'YITH_WCWL_PREMIUM' ) ? YITH_WCWL_Frontend_Premium::get_instance() : YITH_WCWL_Frontend::get_instance();
}
