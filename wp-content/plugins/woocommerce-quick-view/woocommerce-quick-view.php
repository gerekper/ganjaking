<?php
/**
 * Plugin Name: WooCommerce Quick View
 * Plugin URI: https://woocommerce.com/products/woocommerce-quick-view/
 * Description: Let customers quick view products and add them to their cart from a lightbox. Supports variations.
 * Version: 1.7.0
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires at least: 4.7
 * Requires PHP: 5.4
 * Tested up to: 6.1
 * Text Domain: woocommerce-quick-view
 * Domain Path: /languages
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.4
 * Woo: 187509:619c6e57ce72c49c4b57e15b06eddb65
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Quick_View\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_QUICK_VIEW_FILE' ) ) {
	define( 'WC_QUICK_VIEW_FILE', __FILE__ );
}

/**
 * WC_Quick_View class
 */
if ( ! class_exists( 'WC_Quick_View' ) ) :
	class WC_Quick_View extends \Themesquad\WC_Quick_View\Plugin {
		private $quick_view_trigger;

		/**
		 * Constructor.
		 */
		protected function __construct() {
			parent::__construct();

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

			// Enqueue scripts. We use the `woocommerce_before_shop_loop_item`
			// action because it's a shared one between PHP templates and
			// shortcodes.
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'enqueue_scripts' ) );

			if ( 'non_ajax' !== $this->quick_view_trigger ) {
				// Add a quickview button to PHP templates and shortcodes.
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'quick_view_button' ), 5 );
			}

			// Add a quickview button to WC Blocks.
			add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'product_block' ), 10, 3 );
		}

		/**
		 * Plugin page links
		 *
		 * @deprecated 1.7.0
		 */
		public function plugin_links( $links ) {
			wc_deprecated_function( __FUNCTION__, '1.7.0', 'Themesquad\WC_Quick_View\Admin\Admin::plugin_row_meta()' );

			return $links;
		}

		/**
		 * settings function.
		 *
		 * @param array $settings
		 */
		public function settings( $settings ) {

			$settings[] = array(
				'name' => __( 'Quick View', 'woocommerce-quick-view' ),
				'type' => 'title',
				'desc' => 'The following options are used to configure the Quick View extension.',
				'id'   => 'wc_quick_view',
			);

			$settings[] = array(
				'id'      => 'quick_view_trigger',
				'name'    => __( 'Quick View Trigger', 'woocommerce-quick-view' ),
				'desc'    => __( 'Choose what event should trigger quick view', 'woocommerce-quick-view' ),
				'type'    => 'select',
				'options' => array(
					'button'   => __( 'Quick View Button', 'woocommerce-quick-view' ),
					// 'thumbnail'     => __( 'Product Thumbnail', 'woocommerce-quick-view' ),
					'non_ajax' => __( 'Any non-ajax add to cart button', 'woocommerce-quick-view' ),
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

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'woocommerce-quick-view', WC_QUICK_VIEW_URL . 'assets/js/frontend' . $suffix . '.js', $script_dependencies, WC_QUICK_VIEW_VERSION, true );

			if ( 'non_ajax' === $this->quick_view_trigger ) {
				$ajax_cart_en = get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes' ? true : false;

				if ( $ajax_cart_en ) {
					// Read more buttons and add-to-cart buttons of products that do not declare ajax-add-to-cart support.
					$selector = '.product a.button:not(.add_to_cart_button):not(.quick-view-detail-button), .product a.button:not(.ajax_add_to_cart):not(.quick-view-detail-button), .wc-block-grid__product .wp-block-button > a:not(.add_to_cart_button):not(.quick-view-detail-button), .wc-block-grid__product .wp-block-button > a:not(.ajax_add_to_cart):not(.quick-view-detail-button)';
				} else {
					$selector = '.product a.button:not(.quick-view-detail-button), .wc-block-grid__product .wp-block-button > a:not(.quick-view-detail-button)';
				}
			} else {
				$selector = 'a.quick-view-button';
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

			wp_enqueue_style( 'wc_quick_view', WC_QUICK_VIEW_URL . 'assets/css/style.css', $style_dependencies, WC_QUICK_VIEW_VERSION );
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
					WC_QUICK_VIEW_PATH . 'templates/'
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
				WC_QUICK_VIEW_PATH . 'templates/'
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

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.8
 */
function woocommerce_quick_view_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Quick View requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-quick-view' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Initialize extension.
 *
 * @since 1.2.8
 */
function woocommerce_quick_view_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_quick_view_missing_wc_notice' );
		return;
	}

	$GLOBALS['WC_Quick_View'] = WC_Quick_View::instance();
}
add_action( 'plugins_loaded', 'woocommerce_quick_view_init' );
