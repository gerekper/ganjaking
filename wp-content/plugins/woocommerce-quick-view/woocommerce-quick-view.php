<?php
/**
 * Plugin Name: WooCommerce Quick View
 * Plugin URI: https://woocommerce.com/products/woocommerce-quick-view/
 * Description: Let customers quick view products and add them to their cart from a lightbox. Supports variations.
 * Version: 1.2.11
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: wc_quick_view
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 * Tested up to: 5.5
 *
 * Copyright: © 2020 WooCommerce
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
	define( 'WC_QUICK_VIEW_VERSION', '1.2.11' ); // WRCS: DEFINED_VERSION.

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
		 * Scripts function.
		 */
		public function scripts() {
			global $post;
			if ( ! is_tax( 'product_cat' ) &&
				! is_tax( 'product_tag' ) &&
				! is_post_type_archive( 'product' ) &&
				! is_page_template( 'template-homepage.php' ) &&
				! ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product' ) ) ) {
				return;
			}

			do_action( 'wc_quick_view_enqueue_scripts' );

			$plugin_url = untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
			$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'woocommerce-quick-view', $plugin_url . '/assets/js/frontend' . $suffix . '.js', array( 'jquery', 'prettyPhoto', 'wc-add-to-cart-variation' ), WC_QUICK_VIEW_VERSION, true );

			// Load gallery scripts on product pages only if supported.
			if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
				wp_enqueue_script( 'zoom' );
			}
			if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
				wp_enqueue_script( 'flexslider' );
			}
			if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
				wp_enqueue_script( 'photoswipe-ui-default' );
				wp_enqueue_style( 'photoswipe-default-skin' );
				add_action( 'wp_footer', 'woocommerce_photoswipe' );
			}
			wp_enqueue_script( 'wc-single-product' );

			wp_enqueue_style( 'woocommerce_prettyPhoto_css' );
			wp_enqueue_style( 'wc_quick_view', $plugin_url . '/assets/css/style.css', array(), WC_QUICK_VIEW_VERSION );

			switch ( $this->quick_view_trigger ) {
				case 'non_ajax':
					$ajax_cart_en = get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes' ? true : false;

					if ( $ajax_cart_en ) {
						// Read more buttons and add-to-cart buttons of products that do not declare ajax-add-to-cart support.
						$selector = '.product a.button:not(.add_to_cart_button):not(.quick-view-detail-button), .product a.button:not(.ajax_add_to_cart):not(.quick-view-detail-button)';
					} else {
						$selector = '.product a.button:not(.quick-view-detail-button)';
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
								'width'   => '90%',
								'height'  => '90%',
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
		 * quick_view_button function.
		 */
		public function quick_view_button() {
			wc_get_template(
				'loop/quick-view-button.php',
				array(),
				'woocommerce-quick-view',
				untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/'
			);
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

