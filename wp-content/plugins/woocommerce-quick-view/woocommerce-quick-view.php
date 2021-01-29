<?php
/**
 * Plugin Name: WooCommerce Quick View
 * Plugin URI: https://woocommerce.com/products/woocommerce-quick-view/
 * Description: Let customers quick view products and add them to their cart from a lightbox. Supports variations.
 * Version: 1.4.0
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: wc_quick_view
 * WC tested up to: 5.0
 * WC requires at least: 2.6
 * Tested up to: 5.6
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 187509:619c6e57ce72c49c4b57e15b06eddb65
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.8
 */
function woocommerce_quick_view_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Quick View requires WooCommerce to be installed and active. You can download %s here.', 'wc_quick_view' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * WC_Quick_View class
 */
if ( ! class_exists( 'WC_Quick_View' ) ) :
	define( 'WC_QUICK_VIEW_VERSION', '1.4.0' ); // WRCS: DEFINED_VERSION.

	class WC_Quick_View {
		private $quick_view_trigger;

		/**
		 * __construct function.
		 */
		public function __construct() {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );

			// Default option.
			add_option( 'quick_view_trigger', 'button' );

			// Load options.
			$this->quick_view_trigger = get_option( 'quick_view_trigger' );

			// Scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 11 );

			// Show a product via API.
			add_action( 'woocommerce_api_wc_quick_view', array( $this, 'quick_view' ) );

			// Settings.
			add_filter( 'woocommerce_general_settings', array( $this, 'settings' ) );

			// Filter product blocks to add a quickview button.
			add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'product_block' ), 10, 3 );
		}

		/**
		 * Plugin page links
		 */
		public function plugin_links( $links ) {
			$plugin_links = array(
				'<a href="https://docs.woocommerce.com/">' . esc_html__( 'Support', 'wc_quick_view' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/woocommerce-quick-view/">' . esc_html__( 'Docs', 'wc_quick_view' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * settings function.
		 *
		 * @param array $settings
		 */
		public function settings( $settings ) {

			$settings[] = array(
				'name' => __( 'Quick View', 'wc_quick_view' ),
				'type' => 'title',
				'desc' => 'The following options are used to configure the Quick View extension.',
				'id'   => 'wc_quick_view',
			);

			$settings[] = array(
				'id'      => 'quick_view_trigger',
				'name'    => __( 'Quick View Trigger', 'wc_quick_view' ),
				'desc'    => __( 'Choose what event should trigger quick view', 'wc_quick_view' ),
				'type'    => 'select',
				'options' => array(
					'button'   => __( 'Quick View Button', 'wc_quick_view' ),
					// 'thumbnail'     => __( 'Product Thumbnail', 'wc_quick_view' ),
					'non_ajax' => __( 'Any non-ajax add to cart button', 'wc_quick_view' ),
				),
			);

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'wc_quick_view',
			);

			return $settings;
		}

		/**
		 * Register scripts and enqueue the styles.
		 */
		public function scripts() {
			do_action( 'wc_quick_view_enqueue_scripts' );

			$script_dependencies = array( 'jquery', 'prettyPhoto', 'wc-single-product', 'wc-add-to-cart-variation' );
			$style_dependencies  = array( 'woocommerce_prettyPhoto_css' );

			// Load gallery scripts on product pages only if supported.
			if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
				$script_dependencies[] = 'zoom';
			}
			if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
				$script_dependencies[] = 'flexslider';
			}
			if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
				$script_dependencies[] = 'photoswipe-ui-default';
				$style_dependencies[]  = 'photoswipe-default-skin';
				add_action( 'wp_footer', 'woocommerce_photoswipe' );
			}

			$plugin_url = untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
			$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'woocommerce-quick-view', $plugin_url . '/assets/js/frontend' . $suffix . '.js', $script_dependencies, WC_QUICK_VIEW_VERSION, true );

			switch ( $this->quick_view_trigger ) {
				case 'non_ajax':
					$ajax_cart_en = get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes' ? true : false;

					if ( $ajax_cart_en ) {
						// Read more buttons and add-to-cart buttons of products that do not declare ajax-add-to-cart support.
						$selector = '.product a.button:not(.add_to_cart_button):not(.quick-view-detail-button), .product a.button:not(.ajax_add_to_cart):not(.quick-view-detail-button), .wc-block-grid__product .wp-block-button > a:not(.add_to_cart_button):not(.quick-view-detail-button), .wc-block-grid__product .wp-block-button > a:not(.ajax_add_to_cart):not(.quick-view-detail-button)';
					} else {
						$selector = '.product a.button:not(.quick-view-detail-button), .wc-block-grid__product .wp-block-button > a:not(.quick-view-detail-button)';
					}
					break;
				default:
					$selector = 'a.quick-view-button';

					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'quick_view_button' ), 5 );
					break;
			}

			$selector = apply_filters( 'quick_view_selector', $selector );
			$selector = trim( $selector, "'" );

			$link = esc_url_raw(
				add_query_arg(
					apply_filters(
						'woocommerce_loop_quick_view_link_args',
						urlencode_deep(
							array(
								'wc-api'  => 'WC_Quick_View',
								'product' => 'product_id_placeholder',
								'ajax'    => 'true',
							)
						)
					),
					home_url( '/' )
				)
			);

			wp_localize_script(
				'woocommerce-quick-view',
				'quickview_options',
				array(
					'selector' => $selector,
					'link'     => $link,
				)
			);

			add_action( 'woocommerce_before_shop_loop', array( $this, 'enqueue_scripts' ) );
			add_action( 'woocommerce_shortcode_before_products_loop', array( $this, 'enqueue_scripts' ) );

			wp_enqueue_style( 'wc_quick_view', $plugin_url . '/assets/css/style.css', $style_dependencies, WC_QUICK_VIEW_VERSION );
		}

		/**
		 * Enqueue the quick view script and it's dependencies.
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'woocommerce-quick-view' );
		}

		/**
		 * Display ajax content.
		 */
		public function quick_view() {
			global $post;

			$product_id = absint( $_GET['product'] );

			if ( $product_id ) {

				// Get product ready.
				$post = get_post( $product_id );

				setup_postdata( $post );

				wc_get_template(
					'quick-view.php',
					array(),
					'woocommerce-quick-view',
					untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/'
				);

			}

			exit;
		}

		/**
		 * Output a quick view button.
		 *
		 * @param WC_Product $product Optional product object.
		 * @param bool       $return  Return the output.
		 *
		 * @return string|void
		 */
		public function quick_view_button( $product = null, $return = false ) {
			if ( ! $product ) {
				global $product;
			}

			if ( $return ) {
				ob_start();
			}

			wc_get_template(
				'loop/quick-view-button.php',
				array( 'product' => $product ),
				'woocommerce-quick-view',
				untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/'
			);

			if ( $return ) {
				return ob_get_clean();
			}
		}

		/**
		 * Filter output for a product block.
		 *
		 * @param string     $html    Product block HTML.
		 * @param object     $data    Block data.
		 * @param WC_Product $product Product object.
		 * @return string
		 */
		public function product_block( $html, $data, $product ) {
			$this->enqueue_scripts();

			if ( 'non_ajax' !== $this->quick_view_trigger && ! empty( $data->button ) ) {
				$data->quick_view_button = $this->quick_view_button( $product, true );

				$html = str_replace( $data->button, $data->quick_view_button . $data->button, $html );
			}

			return $html;
		}

	}
endif;

add_action( 'plugins_loaded', 'woocommerce_quick_view_init' );

/**
 * Initialize extension.
 *
 * @since 1.2.8
 * @return void
 */
function woocommerce_quick_view_init() {
	load_plugin_textdomain( 'wc_quick_view', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_quick_view_missing_wc_notice' );
		return;
	}

	$GLOBALS['WC_Quick_View'] = new WC_Quick_View();
}

