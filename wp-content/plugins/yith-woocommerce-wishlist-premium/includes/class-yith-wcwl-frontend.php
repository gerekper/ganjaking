<?php
/**
 * Init class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
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
		public $version = '3.26.0';

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
		protected $yith_wcwl_install;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Frontend
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( is_null( static::$instance ) ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			define( 'YITH_WCWL_VERSION', $this->version );
			define( 'YITH_WCWL_DB_VERSION', $this->db_version );

			$this->yith_wcwl_install = YITH_WCWL_Install();

			// add filter for font-awesome compatibility.
			add_filter( 'option_yith_wcwl_add_to_wishlist_icon', array( $this, 'update_font_awesome_classes' ) );
			add_filter( 'option_yith_wcwl_add_to_cart_icon', array( $this, 'update_font_awesome_classes' ) );

			// init class.
			add_action( 'init', array( $this, 'init' ), 0 );

			// templates.
			add_action( 'init', array( $this, 'add_button' ) );
			add_filter( 'body_class', array( $this, 'add_body_class' ), 90 );
			add_action( 'template_redirect', array( $this, 'add_nocache_headers' ) );
			add_action( 'wp_head', array( $this, 'add_noindex_header' ) );
			add_filter( 'wp_robots', array( $this, 'add_noindex_robots' ) );
			add_action( 'yith_wcwl_before_wishlist_title', array( $this, 'print_notices' ) );
			add_action( 'yith_wcwl_wishlist_before_wishlist_content', array( $this, 'wishlist_header' ), 10, 1 );
			add_action( 'yith_wcwl_wishlist_main_wishlist_content', array( $this, 'main_wishlist_content' ), 10, 1 );
			add_action( 'yith_wcwl_wishlist_after_wishlist_content', array( $this, 'wishlist_footer' ), 10, 1 );

			// template modifications.
			add_filter( 'post_class', array( $this, 'add_products_class_on_loop' ), 20, 3 );

			// scripts.
			add_action( 'wp_head', array( $this, 'detect_javascript' ), 0 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_stuffs' ) );

			// add YITH WooCommerce Frequently Bought Together Premium shortcode.
			add_action( 'yith_wcwl_after_wishlist_form', array( $this, 'yith_wcfbt_shortcode' ), 10, 1 );
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'yith_wfbt_redirect_after_add_to_cart' ), 10, 1 );

			// YITH WCWL Loaded.
			/**
			 * DO_ACTION: yith_wcwl_loaded
			 *
			 * Allows to fire some action when the frontend class has loaded all the requirements.
			 */
			do_action( 'yith_wcwl_loaded' );
		}

		/**
		 * Initiator method.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init() {
			// update cookie from old version to new one.
			$this->update_cookies();
			$this->destroy_serialized_cookies();
			$this->convert_cookies_to_session();

			// register assets.
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
			$this->add_button_for_single();
			$this->add_button_for_loop();

			// Add the link "Add to wishlist" for Gutenberg blocks.
			add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'add_button_for_blocks_product_grid_item' ), 10, 3 );
		}

		/**
		 * Add ATW button to Single product page
		 * Accounts for both Legacy Templates and Blockified ones
		 *
		 * @return void
		 * @since 3.24.0
		 */
		public function add_button_for_single() {
			// Add the link "Add to wishlist".
			$position = get_option( 'yith_wcwl_button_position', 'add-to-cart' );

			/**
			 * APPLY_FILTERS: yith_wcwl_positions
			 *
			 * Filter the array of positions where to display the 'Add to wishlist' button in the product page.
			 *
			 * @param array $positions Array of positions
			 *
			 * @return array
			 */
			$positions = apply_filters(
				'yith_wcwl_positions',
				array(
					'after_add_to_cart' => array(
						'hook'     => 'woocommerce_single_product_summary',
						'priority' => 31,
					),
					'add-to-cart'       => array(
						'hook'     => 'woocommerce_single_product_summary',
						'priority' => 31,
					),
					'thumbnails'        => array(
						'hook'     => 'woocommerce_before_single_product_summary',
						'priority' => 21,
					),
					'summary'           => array(
						'hook'     => 'woocommerce_after_single_product_summary',
						'priority' => 11,
					),
				)
			);

			if ( 'shortcode' === $position || ! isset( $positions[ $position ] ) ) {
				return;
			}

			if ( yith_plugin_fw_wc_is_using_block_template_in_single_product() ) {
				$this->add_button_for_blockified_template( 'single-product', $position );
			} else {
				add_action( $positions[ $position ]['hook'], array( $this, 'print_button' ), $positions[ $position ]['priority'] );
			}
		}

		/**
		 * Add ATW button to Archive product page
		 * Accounts for both Legacy Templates and Blockified ones
		 *
		 * @return void
		 * @since 3.24.0
		 */
		public function add_button_for_loop() {
			// check if Add to wishlist button is enabled for loop.
			$enabled_on_loop = 'yes' === get_option( 'yith_wcwl_show_on_loop', 'no' );

			if ( ! $enabled_on_loop ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_loop_positions
			 *
			 * Filter the array of positions where to display the 'Add to wishlist' button in the loop page.
			 *
			 * @param array $positions Array of positions
			 *
			 * @return array
			 */
			$positions = apply_filters(
				'yith_wcwl_loop_positions',
				array(
					'before_image'       => array(
						'hook'     => 'woocommerce_before_shop_loop_item',
						'priority' => 5,
					),
					'before_add_to_cart' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 7,
					),
					'after_add_to_cart'  => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 15,
					),
				)
			);

			// Add the link "Add to wishlist" in the loop.
			$position = get_option( 'yith_wcwl_loop_position', 'after_add_to_cart' );

			if ( 'shortcode' === $position || ! isset( $positions[ $position ] ) ) {
				return;
			}

			if ( yith_plugin_fw_wc_is_using_block_template_in_product_catalogue() ) {
				$this->add_button_for_blockified_template( 'archive-product', $position );
			} else {
				add_action( $positions[ $position ]['hook'], array( $this, 'print_button' ), $positions[ $position ]['priority'] );
			}
		}

		/**
		 * Hooks action required to print ATW button in correct locations inside a Blockified template
		 *
		 * @param string $template Template to change.
		 * @param string $position Position where to add button.
		 * @return void
		 * @since 3.24.0
		 */
		public function add_button_for_blockified_template( $template, $position ) {
			switch ( $position ) {
				case 'add-to-cart':
				case 'after_add_to_cart':
					$block = 'single-product' === $template ? 'add-to-cart-form' : 'product-button';
					add_filter( "render_block_woocommerce/$block", array( $this, 'add_button_after_block' ), 10, 3 );
					break;
				case 'before_add_to_cart':
					$block = 'single-product' === $template ? 'add-to-cart-form' : 'product-button';
					add_filter( "render_block_woocommerce/$block", array( $this, 'add_button_before_block' ), 10, 3 );
					break;
				case 'thumbnails':
					add_filter( 'render_block_woocommerce/product-image-gallery', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
				case 'before_image':
					add_filter( 'render_block_woocommerce/product-image', array( $this, 'add_button_before_block' ), 10, 3 );
					break;
				case 'summary':
					add_filter( 'render_block_woocommerce/product-details', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
			}
		}

		/**
		 * Prepend ATW button to a block
		 * Uses block context to retrieve product id.
		 *
		 * @param string   $block_content The block content.
		 * @param string   $parsed_block  The full block, including name and attributes.
		 * @param WP_Block $block         The block instance.
		 *
		 * @return string Filtered block content.
		 */
		public function add_button_before_block( $block_content, $parsed_block, $block ) {
			$post_id = $block->context['postId'];
			$product = wc_get_product( $post_id );

			if ( ! $product ) {
				return $block_content;
			}

			$button = $this->get_button( $product->get_id() );

			return "$button $block_content";
		}

		/**
		 * Append ATW button to a block
		 * Uses block context to retrieve product id.
		 *
		 * @param string   $block_content The block content.
		 * @param string   $parsed_block  The full block, including name and attributes.
		 * @param WP_Block $block         The block instance.
		 *
		 * @return string Filtered block content.
		 */
		public function add_button_after_block( $block_content, $parsed_block, $block ) {
			global $post;

			$post_id = isset( $block->context['postId'] ) ? $block->context['postId'] : false;
			$post_id = $post_id ?? isset( $post->ID ) ? $post->ID : false;
			$product = wc_get_product( $post_id );

			if ( ! $product ) {
				return $block_content;
			}

			$button = $this->get_button( $product->get_id() );

			return "$block_content $button";
		}

		/**
		 * Add ATW button to Products block item
		 *
		 * @param string     $item_html HTML of the single block item.
		 * @param array      $data      Data used to render the item.
		 * @param WC_Product $product   Current product.
		 *
		 * @return string Filtered HTML.
		 */
		public function add_button_for_blocks_product_grid_item( $item_html, $data, $product ) {
			$enabled_on_loop = 'yes' === get_option( 'yith_wcwl_show_on_loop', 'no' );

			if ( ! $enabled_on_loop ) {
				return $item_html;
			}

			// Add the link "Add to wishlist" in the loop.
			$position = get_option( 'yith_wcwl_loop_position', 'after_add_to_cart' );
			$button   = $this->get_button( $product->get_id() );
			$parts    = array();

			preg_match( '/(<li class=".*?">)[\S|\s]*?(<a .*?>[\S|\s]*?<\/a>)([\S|\s]*?)(<\/li>)/', $item_html, $parts );

			if ( ! $parts || count( $parts ) < 5 ) {
				return $item_html;
			}

			// removes first match (entire match).
			array_shift( $parts );

			// removes empty parts.
			$parts = array_filter( $parts );
			$index = false;

			// searches for index to cut parts array.
			switch ( $position ) {
				case 'before_image':
					$index = 1;
					break;
				case 'before_add_to_cart':
					$index = 2;
					break;
				case 'after_add_to_cart':
					$index = 3;
					break;
			}

			// if index is found, stitch button in correct position.
			if ( $index ) {
				$first_set  = array_slice( $parts, 0, $index );
				$second_set = array_slice( $parts, $index );

				$parts = array_merge(
					$first_set,
					(array) $button,
					$second_set
				);

				// replace li classes.
				$parts[0] = preg_replace( '/class="(.*)"/', 'class="$1 add-to-wishlist-' . $position . '"', $parts[0] );
			}

			// join all parts together.
			$item_html = implode( '', $parts );

			// return item.
			return $item_html;
		}

		/**
		 * Returns HTML for ATW button
		 *
		 * @param int $product_id Optional product id (if empty global product will be used instead by the shortcode).
		 * @return string HTML for ATW button.
		 */
		public function get_button( $product_id = false ) {
			$shortcode_tag = 'yith_wcwl_add_to_wishlist';
			$options       = array();
			$text_options  = '';

			if ( $product_id ) {
				$options['product_id'] = $product_id;
			}

			if ( ! empty( $options ) ) {
				foreach ( $options as $option_key => $option_value ) {
					$text_options .= " $option_key=\"$option_value\"";
				}
			}

			$shortcode = "[$shortcode_tag $text_options]";

			return do_shortcode( $shortcode );
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
			/**
			 * APPLY_FILTERS: yith_wcwl_show_add_to_wishlist
			 *
			 * Filter whether to show the 'Add to wishlist' button.
			 *
			 * @param bool $show_button Whether to show the ATW button or not
			 *
			 * @return bool
			 */
			if ( ! apply_filters( 'yith_wcwl_show_add_to_wishlist', true ) ) {
				return;
			}

			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}

		/* === WISHLIST PAGE === */

		/**
		 * Prints wc notice for wishlist pages
		 *
		 * @return void
		 * @since 2.0.5
		 */
		public function print_notices() {
			if ( function_exists( 'wc_print_notices' ) ) {
				wc_print_notices();
			}
		}

		/**
		 * Add specific body class when the Wishlist page is opened
		 *
		 * @param array $classes Existing boy classes.
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
			if ( ! headers_sent() && yith_wcwl_is_wishlist_page() ) {
				wc_nocache_headers();
			}
		}

		/**
		 * Send noindex header on Add To Wishlist url (?add_to_wishlist=12345)
		 * Deprecated since version 5.7 of WordPress.
		 *
		 * @return void
		 * @since 3.0.20
		 */
		public function add_noindex_header() {
			/**
			 * APPLY_FILTERS: yith_wcwl_skip_noindex_headers
			 *
			 * Filter whether to disable the 'Add to wishlist' action from robots.
			 *
			 * @param bool $show_button Whether to disable the ATW action from robots or not
			 *
			 * @return bool
			 */
			if ( function_exists( 'wp_robots_no_robots' ) || ! isset( $_GET['add_to_wishlist'] ) || apply_filters( 'yith_wcwl_skip_noindex_headers', false ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			wp_robots_no_robots();
		}

		/**
		 * Disable search engines indexing for Add to Wishlist url.
		 * Uses "wp_robots" filter introduced in WP 5.7.
		 *
		 * @since 3.0.20
		 * @param array $robots Associative array of robots directives.
		 * @return array Filtered robots directives.
		 */
		public function add_noindex_robots( $robots ) {
			if ( ! isset( $_GET['add_to_wishlist'] ) || apply_filters( 'yith_wcwl_skip_noindex_headers', false ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $robots;
			}

			return wp_robots_no_robots( $robots );
		}

		/* === SCRIPTS AND ASSETS === */

		/**
		 * Register styles required by the plugin
		 *
		 * @return void
		 */
		public function register_styles() {
			$woocommerce_base = WC()->template_path();
			$assets_path      = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			// register dependencies.
			wp_register_style( 'jquery-selectBox', YITH_WCWL_URL . 'assets/css/jquery.selectBox.css', array(), '1.2.0' );
			wp_register_style( 'yith-wcwl-font-awesome', YITH_WCWL_URL . 'assets/css/font-awesome.css', array(), '4.7.0' );
			wp_register_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css', array(), '3.1.6' );

			/**
			 * APPLY_FILTERS: yith_wcwl_main_style_deps
			 *
			 * Filter the style dependencies to be used in the plugin.
			 *
			 * @param array $deps Array of style dependencies
			 *
			 * @return array
			 */
			$deps = apply_filters( 'yith_wcwl_main_style_deps', array( 'jquery-selectBox', 'yith-wcwl-font-awesome', 'woocommerce_prettyPhoto_css' ) );

			// register main style.
			$located = locate_template(
				array(
					$woocommerce_base . 'wishlist.css',
					'wishlist.css',
				)
			);

			if ( ! $located ) {
				wp_register_style( 'yith-wcwl-main', YITH_WCWL_URL . 'assets/css/style.css', $deps, $this->version );
			} else {
				$stylesheet_directory     = get_stylesheet_directory();
				$stylesheet_directory_uri = get_stylesheet_directory_uri();
				$template_directory       = get_template_directory();
				$template_directory_uri   = get_template_directory_uri();

				$style_url = ( strpos( $located, $stylesheet_directory ) !== false ) ? str_replace( $stylesheet_directory, $stylesheet_directory_uri, $located ) : str_replace( $template_directory, $template_directory_uri, $located );

				wp_register_style( 'yith-wcwl-user-main', $style_url, $deps, $this->version );
			}

			// theme specific assets.
			$current_theme = wp_get_theme();

			if ( $current_theme->exists() ) {
				$theme_slug = $current_theme->Template; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				if ( file_exists( YITH_WCWL_DIR . 'assets/css/themes/' . $theme_slug . '.css' ) ) {
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
			$assets_path      = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
			$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$prefix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'unminified/' : '';

			// register dependencies.
			wp_register_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.6', true );
			wp_register_script( 'jquery-selectBox', YITH_WCWL_URL . 'assets/js/jquery.selectBox.min.js', array( 'jquery' ), '1.2.0', true );

			/**
			 * APPLY_FILTERS: yith_wcwl_main_script_deps
			 *
			 * Filter the script dependencies to be used in the plugin.
			 *
			 * @param array $deps Array of script dependencies
			 *
			 * @return array
			 */
			$deps = apply_filters( 'yith_wcwl_main_script_deps', array( 'jquery', 'jquery-selectBox', 'prettyPhoto' ) );

			// get localized variables.
			$yith_wcwl_l10n = $this->get_localize();

			// register main script.
			$located = locate_template(
				array(
					$woocommerce_base . 'wishlist.js',
					'wishlist.js',
				)
			);

			if ( ! $located ) {
				wp_register_script( 'jquery-yith-wcwl', YITH_WCWL_URL . 'assets/js/' . $prefix . 'jquery.yith-wcwl' . $suffix . '.js', $deps, $this->version, true );
				wp_localize_script( 'jquery-yith-wcwl', 'yith_wcwl_l10n', $yith_wcwl_l10n );
			} else {
				wp_register_script( 'jquery-yith-wcwl-user', str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $located ), $deps, $this->version, true );
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
			// main plugin style.
			if ( ! wp_style_is( 'yith-wcwl-user-main', 'registered' ) ) {
				wp_enqueue_style( 'yith-wcwl-main' );
			} else {
				wp_enqueue_style( 'yith-wcwl-user-main' );
			}

			// theme specific style.
			if ( wp_style_is( 'yith-wcwl-theme', 'registered' ) ) {
				wp_enqueue_style( 'yith-wcwl-theme' );
			}

			// custom style.
			$this->enqueue_custom_style();
		}

		/**
		 * Enqueue style dynamically generated by the plugin
		 *
		 * @return void
		 */
		public function enqueue_custom_style() {
			$custom_css = $this->build_custom_css();

			if ( $custom_css ) {
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
			if ( ! wp_script_is( 'jquery-yith-wcwl-user', 'registered' ) ) {
				wp_enqueue_script( 'jquery-yith-wcwl' );
			} else {
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
			/**
			 * APPLY_FILTERS: yith_wcwl_localize_script
			 *
			 * Filter the array with the parameters sent to the plugin scripts trought the localize.
			 *
			 * @param array $localize Array of parameters
			 *
			 * @return array
			 */
			return apply_filters(
				'yith_wcwl_localize_script',
				array(
					'ajax_url'                    => admin_url( 'admin-ajax.php', 'relative' ),
					'redirect_to_cart'            => get_option( 'yith_wcwl_redirect_cart' ),
					'yith_wcwl_button_position'   => get_option( 'yith_wcwl_button_position' ),
					'multi_wishlist'              => false,
					/**
					 * APPLY_FILTERS: yith_wcwl_hide_add_button
					 *
					 * Filter whether to hide the 'Add to wishlist' button.
					 *
					 * @param bool $hide_button Whether to hide the ATW button or not
					 *
					 * @return bool
					 */
					'hide_add_button'             => apply_filters( 'yith_wcwl_hide_add_button', true ),
					'enable_ajax_loading'         => 'yes' === get_option( 'yith_wcwl_ajax_enable', 'no' ),
					'ajax_loader_url'             => YITH_WCWL_URL . 'assets/images/ajax-loader-alt.svg',
					'remove_from_wishlist_after_add_to_cart' => 'yes' === get_option( 'yith_wcwl_remove_after_add_to_cart' ),
					/**
					 * APPLY_FILTERS: yith_wcwl_is_wishlist_responsive
					 *
					 * Filter whether to use the responsive layout for the wishlist.
					 *
					 * @param bool $is_responsive Whether to use responsive layout or not
					 *
					 * @return bool
					 */
					'is_wishlist_responsive'      => apply_filters( 'yith_wcwl_is_wishlist_responsive', true ),
					/**
					 * APPLY_FILTERS: yith_wcwl_time_to_close_prettyphoto
					 *
					 * Filter the time (in miliseconds) to close the popup after the 'Ask for an estimate' request has been sent.
					 *
					 * @param int $time Time to close the popup
					 *
					 * @return int
					 */
					'time_to_close_prettyphoto'   => apply_filters( 'yith_wcwl_time_to_close_prettyphoto', 3000 ),
					/**
					 * APPLY_FILTERS: yith_wcwl_fragments_index_glue
					 *
					 * Filter the character used for the fragments index.
					 *
					 * @param string $char Character
					 *
					 * @return string
					 */
					'fragments_index_glue'        => apply_filters( 'yith_wcwl_fragments_index_glue', '.' ),
					/**
					 * APPLY_FILTERS: yith_wcwl_reload_on_found_variation
					 *
					 * Filter whether to reload fragments on new variations found.
					 *
					 * @param bool $reload_variations Whether to reload fragments
					 *
					 * @return bool
					 */
					'reload_on_found_variation'   => apply_filters( 'yith_wcwl_reload_on_found_variation', true ),
					/**
					 * APPLY_FILTERS: yith_wcwl_mobile_media_query
					 *
					 * Filter the breakpoint size for the mobile media queries.
					 *
					 * @param int $breakpoint Breakpoint size
					 *
					 * @return int
					 */
					'mobile_media_query'          => apply_filters( 'yith_wcwl_mobile_media_query', 768 ),
					'labels'                      => array(
						'cookie_disabled'       => __( 'We are sorry, but this feature is available only if cookies on your browser are enabled.', 'yith-woocommerce-wishlist' ),
						/**
						 * APPLY_FILTERS: yith_wcwl_added_to_cart_message
						 *
						 * Filter the message when a product has been added succesfully to the cart from the wishlist.
						 *
						 * @param string $message Message
						 *
						 * @return string
						 */
						'added_to_cart_message' => sprintf( '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message" role="alert">%s</div></div>', apply_filters( 'yith_wcwl_added_to_cart_message', __( 'Product added to cart successfully', 'yith-woocommerce-wishlist' ) ) ),
					),
					'actions'                     => array(
						'add_to_wishlist_action'      => 'add_to_wishlist',
						'remove_from_wishlist_action' => 'remove_from_wishlist',
						'reload_wishlist_and_adding_elem_action' => 'reload_wishlist_and_adding_elem',
						'load_mobile_action'          => 'load_mobile',
						'delete_item_action'          => 'delete_item',
						'save_title_action'           => 'save_title',
						'save_privacy_action'         => 'save_privacy',
						'load_fragments'              => 'load_fragments',
					),
					'nonce'                       => array(
						'add_to_wishlist_nonce'      => wp_create_nonce( 'add_to_wishlist' ),
						'remove_from_wishlist_nonce' => wp_create_nonce( 'remove_from_wishlist' ),
						'reload_wishlist_and_adding_elem_nonce' => wp_create_nonce( 'reload_wishlist_and_adding_elem' ),
						'load_mobile_nonce'          => wp_create_nonce( 'load_mobile' ),
						'delete_item_nonce'          => wp_create_nonce( 'delete_item' ),
						'save_title_nonce'           => wp_create_nonce( 'save_title' ),
						'save_privacy_nonce'         => wp_create_nonce( 'save_privacy' ),
						'load_fragments_nonce'       => wp_create_nonce( 'load_fragments' ),
					),
					/**
					 * APPLY_FILTERS: yith_wcwl_redirect_after_ask_an_estimate
					 *
					 * Filter whether to redirect after the 'Ask for an estimate' form has been submitted.
					 *
					 * @param bool $redirect Whether to redirect or not
					 *
					 * @return bool
					 */
					'redirect_after_ask_estimate' => apply_filters( 'yith_wcwl_redirect_after_ask_an_estimate', false ),
					/**
					 * APPLY_FILTERS: yith_wcwl_redirect_url_after_ask_an_estimate
					 *
					 * Filter the URL to redirect after the 'Ask for an estimate' form has been submitted.
					 *
					 * @param string $redirect_url Redirect URL
					 *
					 * @return string
					 */
					'ask_estimate_redirect_url'   => apply_filters( 'yith_wcwl_redirect_url_after_ask_an_estimate', get_home_url() ),
				)
			);
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
			if ( ! defined( 'YIT' ) ) :
				?>
				<script>document.documentElement.className = document.documentElement.className + ' yes-js js_active js'</script>
				<?php
			endif;
		}

		/* === TEMPLATES === */

		/**
		 * Include main wishlist template
		 *
		 * @param array $var Array of variables to pass to the template.
		 *
		 * @var $var array Array of parameters for current view
		 * @return void
		 */
		public function main_wishlist_content( $var ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.varFound
			$template = isset( $var['template_part'] ) ? $var['template_part'] : 'view';
			$layout   = ! empty( $var['layout'] ) ? $var['layout'] : '';

			yith_wcwl_get_template_part( $template, '', $layout, $var );
		}

		/**
		 * Include wishlist header template
		 *
		 * @param array $var Array of variables to pass to the template.
		 *
		 * @var $var array Array of parameters for current view
		 * @return void
		 */
		public function wishlist_header( $var ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.varFound
			$template = isset( $var['template_part'] ) ? $var['template_part'] : 'view';
			$layout   = ! empty( $var['layout'] ) ? $var['layout'] : '';

			yith_wcwl_get_template_part( $template, 'header', $layout, $var );
		}

		/**
		 * Include wishlist footer template
		 *
		 * @param array $var Array of variables to pass to the template.
		 *
		 * @var $var array Array of parameters for current view
		 * @return void
		 */
		public function wishlist_footer( $var ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.varFound
			$template = isset( $var['template_part'] ) ? $var['template_part'] : 'view';
			$layout   = ! empty( $var['layout'] ) ? $var['layout'] : '';

			yith_wcwl_get_template_part( $template, 'footer', $layout, $var );
		}

		/* === TEMPLATE MODIFICATIONS === */

		/**
		 * Add class to products when Add to Wishlist is shown on loop
		 *
		 * @param array        $classes Array of available classes for the product.
		 * @param string|array $class   Additional class.
		 * @param int          $post_id Post ID.
		 * @return array Array of filtered classes for the product
		 * @since 3.0.0
		 */
		public function add_products_class_on_loop( $classes, $class = '', $post_id = 0 ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
			if ( yith_wcwl_is_single() || doing_action( 'body_class' ) || ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
				return $classes;
			}

			$enabled_on_loop = 'yes' === get_option( 'yith_wcwl_show_on_loop', 'no' );

			if ( ! $enabled_on_loop ) {
				return $classes;
			}

			$position = get_option( 'yith_wcwl_loop_position', 'after_add_to_cart' );

			if ( 'shortcode' === $position ) {
				return $classes;
			}

			$classes[] = "add-to-wishlist-$position";

			return $classes;
		}

		/* === UTILS === */

		/**
		 * Format options that will sent through AJAX calls to refresh arguments
		 *
		 * @param array  $options Array of options.
		 * @param string $context Widget/Shortcode that will use the options.
		 * @return array Array of formatted options
		 * @since 3.0.0
		 */
		public function format_fragment_options( $options, $context = '' ) {
			// removes unusable values, and changes options common for all fragments.
			if ( ! empty( $options ) ) {
				foreach ( $options as $id => $value ) {
					if ( is_object( $value ) || is_array( $value ) ) {
						// remove item if type is not supported.
						unset( $options[ $id ] );
					} elseif ( 'ajax_loading' === $id ) {
						$options['ajax_loading'] = false;
					}
				}
			}

			// applies context specific changes.
			if ( ! empty( $context ) ) {
				$options['item'] = $context;

				switch ( $context ) {
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
		 * @param array $options Options for the fragments.
		 * @return array Filtered options for the fragment
		 */
		public function decode_fragment_options( $options ) {
			if ( ! empty( $options ) ) {
				foreach ( $options as $id => $value ) {
					if ( 'true' === $value ) {
						$options[ $id ] = true;
					} elseif ( 'false' === $value ) {
						$options[ $id ] = false;
					} else {
						$options[ $id ] = sanitize_text_field( wp_unslash( $value ) );
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
		public function alter_add_to_cart_button() {
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
		 * @param array $args Array of arguments.
		 * @return array Array of filtered arguments
		 * @since 3.0.0
		 */
		public function alter_add_to_cart_args( $args ) {
			$use_custom_button = get_option( 'yith_wcwl_add_to_cart_style' );
			$button_class      = in_array( $use_custom_button, array( 'button_custom', 'button_default' ), true );
			$icon              = get_option( 'yith_wcwl_add_to_cart_icon' );
			$custom_icon       = get_option( 'yith_wcwl_add_to_cart_custom_icon' );
			$classes           = isset( $args['class'] ) ? explode( ' ', $args['class'] ) : array();
			$pos               = array_search( 'button', $classes, true );

			if ( ! $button_class && false !== $pos ) {
				unset( $classes[ $pos ] );
			} elseif ( $button_class ) {
				$classes[] = 'button';
			}

			$classes[] = 'add_to_cart';
			$classes[] = 'alt';

			$args['class'] = implode( ' ', $classes );

			if ( 'button_custom' === $use_custom_button && 'none' !== $icon ) {
				if ( ! isset( $args['attributes'] ) ) {
					$args['attributes'] = array();
				}

				if ( 'custom' !== $icon ) {
					$args['attributes']['data-icon'] = $icon;
				} elseif ( $custom_icon ) {
					$args['attributes']['data-icon'] = $custom_icon;
				}
			}

			return $args;
		}

		/**
		 * Filter Add to Cart button label on wishlist page
		 *
		 * @param string      $text Button label.
		 * @param \WC_Product $product Current product.
		 * @return string Filtered label
		 */
		public function alter_add_to_cart_text( $text, $product ) {
			$label_option = get_option( 'yith_wcwl_add_to_cart_text', __( 'Add to cart', 'yith-woocommerce-wishlist' ) );

			/**
			 * APPLY_FILTERS: yith_wcwl_add_to_cart_label
			 *
			 * Filter the label of the 'Add to cart' button in the wishlist page.
			 *
			 * @param string $label Label
			 *
			 * @return string
			 */
			$label = $product->is_type( 'variable' ) ? $text : apply_filters( 'yith_wcwl_add_to_cart_label', $label_option );

			return $label;
		}

		/**
		 * Filter Add to Cart button url on wishlist page
		 *
		 * @param string      $url Url to the Add to Cart.
		 * @param \WC_Product $product Current product.
		 * @return string Filtered url
		 */
		public function alter_add_to_cart_url( $url, $product ) {
			global $yith_wcwl_wishlist_token;

			if ( $yith_wcwl_wishlist_token ) {
				$wishlist = yith_wcwl_get_wishlist( $yith_wcwl_wishlist_token );

				if ( ! $wishlist ) {
					return $url;
				}

				$wishlist_id = $wishlist->get_id();
				$item        = $wishlist->get_product( $product->get_id() );

				if ( wp_doing_ajax() ) {
					$url = add_query_arg( 'add-to-cart', $product->get_id(), YITH_WCWL()->get_wishlist_url( 'view/' . $yith_wcwl_wishlist_token ) );
				}

				if ( $product->is_type( array( 'simple', 'variation' ) ) && 'yes' === get_option( 'yith_wcwl_redirect_cart' ) ) {
					$url = add_query_arg( 'add-to-cart', $product->get_id(), wc_get_cart_url() );
				}

				if ( ! $product->is_type( 'external' ) && 'yes' === get_option( 'yith_wcwl_remove_after_add_to_cart' ) ) {
					$url = add_query_arg(
						array(
							'remove_from_wishlist_after_add_to_cart' => $product->get_id(),
							'wishlist_id'    => $wishlist_id,
							'wishlist_token' => $yith_wcwl_wishlist_token,
						),
						$url
					);
				}

				if ( $item && 'yes' === get_option( 'yith_wcwl_quantity_show' ) ) {
					$url = add_query_arg( 'quantity', $item->get_quantity(), $url );
				}
			}

			/**
			 * APPLY_FILTERS: yit_wcwl_add_to_cart_redirect_url
			 *
			 * Filter the URL to redirect after adding to the cart from the wishlist.
			 *
			 * @param string     $redirect_url Redirect URL
			 * @param string     $original_url Original URL
			 * @param WC_Product $product      Product object
			 *
			 * @return string
			 */
			return apply_filters( 'yit_wcwl_add_to_cart_redirect_url', esc_url_raw( $url ), $url, $product );
		}

		/**
		 * Modernize font-awesome class, for old wishlist users
		 *
		 * @param string $class Original font-awesome class.
		 * @return string Filtered font-awesome class
		 * @since 2.0.2
		 */
		public function update_font_awesome_classes( $class ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
			$exceptions = array(
				'icon-envelope'           => 'fa-envelope-o',
				'icon-star-empty'         => 'fa-star-o',
				'icon-ok'                 => 'fa-check',
				'icon-zoom-in'            => 'fa-search-plus',
				'icon-zoom-out'           => 'fa-search-minus',
				'icon-off'                => 'fa-power-off',
				'icon-trash'              => 'fa-trash-o',
				'icon-share'              => 'fa-share-square-o',
				'icon-check'              => 'fa-check-square-o',
				'icon-move'               => 'fa-arrows',
				'icon-file'               => 'fa-file-o',
				'icon-time'               => 'fa-clock-o',
				'icon-download-alt'       => 'fa-download',
				'icon-download'           => 'fa-arrow-circle-o-down',
				'icon-upload'             => 'fa-arrow-circle-o-up',
				'icon-play-circle'        => 'fa-play-circle-o',
				'icon-indent-left'        => 'fa-dedent',
				'icon-indent-right'       => 'fa-indent',
				'icon-facetime-video'     => 'fa-video-camera',
				'icon-picture'            => 'fa-picture-o',
				'icon-plus-sign'          => 'fa-plus-circle',
				'icon-minus-sign'         => 'fa-minus-circle',
				'icon-remove-sign'        => 'fa-times-circle',
				'icon-ok-sign'            => 'fa-check-circle',
				'icon-question-sign'      => 'fa-question-circle',
				'icon-info-sign'          => 'fa-info-circle',
				'icon-screenshot'         => 'fa-crosshairs',
				'icon-remove-circle'      => 'fa-times-circle-o',
				'icon-ok-circle'          => 'fa-check-circle-o',
				'icon-ban-circle'         => 'fa-ban',
				'icon-share-alt'          => 'fa-share',
				'icon-resize-full'        => 'fa-expand',
				'icon-resize-small'       => 'fa-compress',
				'icon-exclamation-sign'   => 'fa-exclamation-circle',
				'icon-eye-open'           => 'fa-eye',
				'icon-eye-close'          => 'fa-eye-slash',
				'icon-warning-sign'       => 'fa-warning',
				'icon-folder-close'       => 'fa-folder',
				'icon-resize-vertical'    => 'fa-arrows-v',
				'icon-resize-horizontal'  => 'fa-arrows-h',
				'icon-twitter-sign'       => 'fa-twitter-square',
				'icon-facebook-sign'      => 'fa-facebook-square',
				'icon-thumbs-up'          => 'fa-thumbs-o-up',
				'icon-thumbs-down'        => 'fa-thumbs-o-down',
				'icon-heart-empty'        => 'fa-heart-o',
				'icon-signout'            => 'fa-sign-out',
				'icon-linkedin-sign'      => 'fa-linkedin-square',
				'icon-pushpin'            => 'fa-thumb-tack',
				'icon-signin'             => 'fa-sign-in',
				'icon-github-sign'        => 'fa-github-square',
				'icon-upload-alt'         => 'fa-upload',
				'icon-lemon'              => 'fa-lemon-o',
				'icon-check-empty'        => 'fa-square-o',
				'icon-bookmark-empty'     => 'fa-bookmark-o',
				'icon-phone-sign'         => 'fa-phone-square',
				'icon-hdd'                => 'fa-hdd-o',
				'icon-hand-right'         => 'fa-hand-o-right',
				'icon-hand-left'          => 'fa-hand-o-left',
				'icon-hand-up'            => 'fa-hand-o-up',
				'icon-hand-down'          => 'fa-hand-o-down',
				'icon-circle-arrow-left'  => 'fa-arrow-circle-left',
				'icon-circle-arrow-right' => 'fa-arrow-circle-right',
				'icon-circle-arrow-up'    => 'fa-arrow-circle-up',
				'icon-circle-arrow-down'  => 'fa-arrow-circle-down',
				'icon-fullscreen'         => 'fa-arrows-alt',
				'icon-beaker'             => 'fa-flask',
				'icon-paper-clip'         => 'fa-paperclip',
				'icon-sign-blank'         => 'fa-square',
				'icon-pinterest-sign'     => 'fa-pinterest-square',
				'icon-google-plus-sign'   => 'fa-google-plus-square',
				'icon-envelope-alt'       => 'fa-envelope',
				'icon-comment-alt'        => 'fa-comment-o',
				'icon-comments-alt'       => 'fa-comments-o',
			);

			if ( in_array( $class, array_keys( $exceptions ), true ) ) {
				$class = $exceptions[ $class ];
			}

			$class = str_replace( 'icon-', 'fa-', $class );

			return $class;
		}

		/**
		 * Add Frequently Bought Together shortcode to wishlist page
		 *
		 * @param mixed $meta Meta.
		 */
		public function yith_wcfbt_shortcode( $meta ) {

			if ( ! ( defined( 'YITH_WFBT' ) && YITH_WFBT ) || 'no' === get_option( 'yith_wfbt_enable_integration' ) || empty( $meta ) ) {
				return;
			}

			$products = YITH_WCWL()->get_products(
				array(
					'wishlist_id' => is_user_logged_in() ? $meta['ID'] : '',
				)
			);

			$ids = array();
			// take id of products in wishlist.
			foreach ( $products as $product ) {
				$ids[] = $product['prod_id'];
			}

			if ( empty( $ids ) ) {
				return;
			}

			do_shortcode( '[yith_wfbt products="' . implode( ',', $ids ) . '"]' );
		}

		/**
		 * Redirect after add to cart from YITH WooCommerce Frequently Bought Together Premium shortcode
		 *
		 * @param string $url Redirect url.
		 *
		 * @since 2.0.0
		 */
		public function yith_wfbt_redirect_after_add_to_cart( $url ) {
			if ( ! isset( $_REQUEST['yith_wfbt_shortcode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $url;
			}

			return 'yes' === get_option( 'yith_wcwl_redirect_cart' ) ? wc_get_cart_url() : YITH_WCWL()->get_wishlist_url();
		}

		/**
		 * Generate CSS code to append to each page, to apply custom style to wishlist elements
		 *
		 * @param array $rules Array of additional rules to add to default ones.
		 * @return string Generated CSS code
		 */
		protected function build_custom_css( $rules = array() ) {
			$generated_code = '';

			/**
			 * APPLY_FILTERS: yith_wcwl_custom_css_rules
			 *
			 * Filter the array with the custom CSS rules to be used.
			 *
			 * @param array $css_rules CSS rules
			 *
			 * @return array
			 */
			$rules = apply_filters(
				'yith_wcwl_custom_css_rules',
				array_merge(
					array(
						'color_add_to_wishlist'    => array(
							'selector' => '.woocommerce a.add_to_wishlist.button.alt',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background-color: %1$s; background: %1$s',
									'default' => '#333333',
								),
								'text'             => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
								),
								'border'           => array(
									'rule'    => 'border-color: %s',
									'default' => '#333333',
								),
								'background_hover' => array(
									'rule'    => 'background-color: %1$s; background: %1$s',
									'default' => '#4F4F4F',
									'status'  => ':hover',
								),
								'text_hover'       => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
									'status'  => ':hover',
								),
								'border_hover'     => array(
									'rule'    => 'border-color: %s',
									'default' => '#4F4F4F',
									'status'  => ':hover',
								),
							),
							'deps'     => array(
								'yith_wcwl_add_to_wishlist_style' => 'button_custom',
							),
						),
						'rounded_corners_radius'   => array(
							'selector' => '.woocommerce a.add_to_wishlist.button.alt',
							'rules'    => array(
								'rule'    => 'border-radius: %dpx',
								'default' => 16,
							),
							'deps'     => array(
								'yith_wcwl_add_to_wishlist_style' => 'button_custom',
							),
						),
						'color_add_to_cart'        => array(
							'selector' => '.woocommerce .wishlist_table a.add_to_cart.button.alt',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#333333',
								),
								'text'             => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
								),
								'border'           => array(
									'rule'    => 'border-color: %s',
									'default' => '#333333',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#4F4F4F',
									'status'  => ':hover',
								),
								'text_hover'       => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
									'status'  => ':hover',
								),
								'border_hover'     => array(
									'rule'    => 'border-color: %s',
									'default' => '#4F4F4F',
									'status'  => ':hover',
								),
							),
							'deps'     => array(
								'yith_wcwl_add_to_cart_style' => 'button_custom',
							),
						),
						'add_to_cart_rounded_corners_radius' => array(
							'selector' => '.woocommerce .wishlist_table a.add_to_cart.button.alt',
							'rules'    => array(
								'rule'    => 'border-radius: %dpx',
								'default' => 16,
							),
							'deps'     => array(
								'yith_wcwl_add_to_cart_style' => 'button_custom',
							),
						),
						'color_button_style_1'     => array(
							'selector' => '.woocommerce .hidden-title-form button,
								   .yith-wcwl-wishlist-new .create-wishlist-button,
								   .wishlist_manage_table tfoot button.submit-wishlist-changes,
								   .yith-wcwl-wishlist-search-form button.wishlist-search-button',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#333333',
								),
								'text'             => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
								),
								'border'           => array(
									'rule'    => 'border-color: %s',
									'default' => '#333333',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#333333',
									'status'  => ':hover',
								),
								'text_hover'       => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
									'status'  => ':hover',
								),
								'border_hover'     => array(
									'rule'    => 'border-color: %s',
									'default' => '#333333',
									'status'  => ':hover',
								),
							),
							'deps'     => array(
								'yith_wcwl_add_to_cart_style' => 'button_custom',
							),
						),
						'color_button_style_2'     => array(
							'selector' => '.woocommerce .wishlist-title a.show-title-form,
								   .woocommerce .hidden-title-form a.hide-title-form,
								   .wishlist_manage_table tfoot a.create-new-wishlist',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#333333',
								),
								'text'             => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
								),
								'border'           => array(
									'rule'    => 'border-color: %s',
									'default' => '#333333',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#333333',
									'status'  => ':hover',
								),
								'text_hover'       => array(
									'rule'    => 'color: %s',
									'default' => '#ffffff',
									'status'  => ':hover',
								),
								'border_hover'     => array(
									'rule'    => 'border-color: %s',
									'default' => '#333333',
									'status'  => ':hover',
								),
							),
							'deps'     => array(
								'yith_wcwl_add_to_cart_style' => 'button_custom',
							),
						),
						'color_wishlist_table'     => array(
							'selector' => '.woocommerce table.shop_table.wishlist_table tr td',
							'rules'    => array(
								'background' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#FFFFFF',
								),
								'text'       => array(
									'rule'    => 'color: %s',
									'default' => '#6D6C6C',
								),
								'border'     => array(
									'rule'    => 'border-color: %s;',
									'default' => '#FFFFFF',
								),
							),
							'deps'     => array(
								'yith_wcwl_add_to_cart_style' => 'button_custom',
							),
						),
						'color_headers_background' => array(
							'selector' => '.wishlist_table thead tr th,
								   .wishlist_table tfoot td td,
								   .widget_yith-wcwl-lists ul.dropdown li.current a,
								   .widget_yith-wcwl-lists ul.dropdown li a:hover,
								   .selectBox-dropdown-menu.selectBox-options li.selectBox-selected a,
								   .selectBox-dropdown-menu.selectBox-options li.selectBox-hover a',
							'rules'    => array(
								'rule'    => 'background: %1$s; background-color: %1$s;',
								'default' => '#F4F4F4',
							),
							'deps'     => array(
								'yith_wcwl_add_to_cart_style' => 'button_custom',
							),
						),
						'color_share_button'       => array(
							'selector' => '.yith-wcwl-share li a',
							'rules'    => array(
								'color'       => array(
									'rule'    => 'color: %s;',
									'default' => '#FFFFFF',
								),
								'color_hover' => array(
									'rule'    => 'color: %s;',
									'status'  => ':hover',
									'default' => '#FFFFFF',
								),
							),
							'deps'     => array(
								'yith_wcwl_enable_share' => 'yes',
							),
						),
						'color_fb_button'          => array(
							'selector' => '.yith-wcwl-share a.facebook',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#39599E',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'status'  => ':hover',
									'default' => '#39599E',
								),
							),
							'deps'     => array(
								'yith_wcwl_enable_share' => 'yes',
								'yith_wcwl_share_fb'     => 'yes',
							),
						),
						'color_tw_button'          => array(
							'selector' => '.yith-wcwl-share a.twitter',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#45AFE2',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'status'  => ':hover',
									'default' => '#39599E',
								),
							),
							'deps'     => array(
								'yith_wcwl_enable_share'  => 'yes',
								'yith_wcwl_share_twitter' => 'yes',
							),
						),
						'color_pr_button'          => array(
							'selector' => '.yith-wcwl-share a.pinterest',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#AB2E31',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'status'  => ':hover',
									'default' => '#39599E',
								),
							),
							'deps'     => array(
								'yith_wcwl_enable_share' => 'yes',
								'yith_wcwl_share_pinterest' => 'yes',
							),
						),
						'color_em_button'          => array(
							'selector' => '.yith-wcwl-share a.email',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#FBB102',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'status'  => ':hover',
									'default' => '#39599E',
								),
							),
							'deps'     => array(
								'yith_wcwl_enable_share' => 'yes',
								'yith_wcwl_share_email'  => 'yes',
							),
						),
						'color_wa_button'          => array(
							'selector' => '.yith-wcwl-share a.whatsapp',
							'rules'    => array(
								'background'       => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'default' => '#00A901',
								),
								'background_hover' => array(
									'rule'    => 'background: %1$s; background-color: %1$s;',
									'status'  => ':hover',
									'default' => '#39599E',
								),
							),
							'deps'     => array(
								'yith_wcwl_enable_share'   => 'yes',
								'yith_wcwl_share_whatsapp' => 'yes',
							),
						),
					),
					$rules
				)
			);

			if ( empty( $rules ) ) {
				return $generated_code;
			}

			// retrieve dependencies.
			$deps_list    = wp_list_pluck( $rules, 'deps' );
			$dependencies = array();

			if ( ! empty( $deps_list ) ) {
				foreach ( $deps_list as $rule => $deps ) {
					foreach ( $deps as $dep_rule => $dep_value ) {
						if ( ! isset( $dependencies[ $dep_rule ] ) ) {
							$dependencies[ $dep_rule ] = get_option( $dep_rule );
						}
					}
				}
			}

			foreach ( $rules as $id => $rule ) {
				// check dependencies first.
				if ( ! empty( $rule['deps'] ) ) {
					foreach ( $rule['deps'] as $dep_rule => $dep_value ) {
						if ( ! isset( $dependencies[ $dep_rule ] ) || $dependencies[ $dep_rule ] !== $dep_value ) {
							continue 2;
						}
					}
				}

				// retrieve values from db.
				$values     = get_option( "yith_wcwl_{$id}" );
				$new_rules  = array();
				$rules_code = '';

				if ( isset( $rule['rules']['rule'] ) ) {
					// if we have a single-valued option, just search for the rule to apply.
					$status = isset( $rule['rules']['status'] ) ? $rule['rules']['status'] : '';

					if ( ! isset( $new_rules[ $status ] ) ) {
						$new_rules[ $status ] = array();
					}

					$new_rules[ $status ][] = $this->build_css_rule( $rule['rules']['rule'], $values, $rule['rules']['default'] );
				} else {
					// otherwise cycle through rules, and generate CSS code.
					foreach ( $rule['rules'] as $property => $css ) {
						$status = isset( $css['status'] ) ? $css['status'] : '';

						if ( ! isset( $new_rules[ $status ] ) ) {
							$new_rules[ $status ] = array();
						}

						$new_rules[ $status ][] = $this->build_css_rule( $css['rule'], isset( $values[ $property ] ) ? $values[ $property ] : false, $css['default'] );
					}
				}

				// if code was generated, prepend selector.
				if ( ! empty( $new_rules ) ) {
					foreach ( $new_rules as $status => $rules ) {
						$selector = $rule['selector'];

						if ( ! empty( $status ) ) {
							$updated_selector = array();
							$split_selectors  = explode( ',', $rule['selector'] );

							foreach ( $split_selectors as $split_selector ) {
								$updated_selector[] = $split_selector . $status;
							}

							$selector = implode( ',', $updated_selector );
						}

						$rules_code .= $selector . '{' . implode( '', $rules ) . '}';
					}
				}

				// append new rule to generated CSS.
				$generated_code .= $rules_code;
			}

			$custom_css = get_option( 'yith_wcwl_custom_css' );

			if ( $custom_css ) {
				$generated_code .= $custom_css;
			}

			return $generated_code;
		}

		/**
		 * Generate each single CSS rule that will be included in custom plugin CSS
		 *
		 * @param string $rule    Rule to use; placeholders may be applied to be replaced with value {@see sprintf}.
		 * @param string $value   Value to inject inside rule, replacing placeholders.
		 * @param string $default Default value, to be used instead of value when it is empty.
		 *
		 * @return string Formatted CSS rule
		 */
		protected function build_css_rule( $rule, $value, $default = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound
			$value = ( '0' === $value || ( ! empty( $value ) && ! is_array( $value ) ) ) ? $value : $default;

			return sprintf( rtrim( $rule, ';' ) . ';', $value );
		}

		/**
		 * Destroy serialize cookies, to prevent major vulnerability
		 *
		 * @return void
		 * @since 2.0.7
		 */
		protected function destroy_serialized_cookies() {
			$name = 'yith_wcwl_products';

			if ( isset( $_COOKIE[ $name ] ) && is_serialized( sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ) ) ) {
				$_COOKIE[ $name ] = wp_json_encode( array() );
				yith_destroycookie( $name );
			}
		}

		/**
		 * Update old wishlist cookies
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function update_cookies() {
			$cookie     = yith_getcookie( 'yith_wcwl_products' );
			$new_cookie = array();

			if ( ! empty( $cookie ) ) {
				foreach ( $cookie as $item ) {
					if ( ! isset( $item['add-to-wishlist'] ) ) {
						return;
					}

					$new_cookie[] = array(
						'prod_id'     => $item['add-to-wishlist'],
						'quantity'    => isset( $item['quantity'] ) ? $item['quantity'] : 1,
						'wishlist_id' => false,
					);
				}

				yith_setcookie( 'yith_wcwl_products', $new_cookie );
			}
		}

		/**
		 * Convert wishlist stored into cookies into
		 */
		protected function convert_cookies_to_session() {
			$cookie = yith_getcookie( 'yith_wcwl_products' );

			if ( ! empty( $cookie ) ) {

				$default_list = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

				if ( ! $default_list ) {
					return false;
				}

				foreach ( $cookie as $item ) {
					if ( $default_list->has_product( $item['prod_id'] ) ) {
						continue;
					}

					$new_item = new YITH_WCWL_Wishlist_Item();

					$new_item->set_product_id( $item['prod_id'] );
					$new_item->set_quantity( $item['quantity'] );

					if ( isset( $item['dateadded'] ) ) {
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
function YITH_WCWL_Frontend() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	if ( defined( 'YITH_WCWL_PREMIUM' ) ) {
		$instance = YITH_WCWL_Frontend_Premium::get_instance();
	} elseif ( defined( 'YITH_WCWL_EXTENDED' ) ) {
		$instance = YITH_WCWL_Frontend_Extended::get_instance();
	} else {
		$instance = YITH_WCWL_Frontend::get_instance();
	}

	return $instance;
}
