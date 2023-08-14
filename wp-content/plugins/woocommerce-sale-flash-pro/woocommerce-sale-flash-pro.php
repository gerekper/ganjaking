<?php
/**
 * Plugin Name: WooCommerce Sale Flash Pro
 * Plugin URI: https://woocommerce.com/products/sale-flash-pro/
 * Description: Gives you global and per-product control over sale flash display and lets you show % or the amount off.
 * Version: 1.3.1
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Text Domain: woocommerce-sale-flash-pro
 * Domain Path: /languages
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.3
 *
 * WC requires at least: 3.5
 * WC tested up to: 8.0
 * Woo: 18591:7761d62beb597bea4f6fab56fef6739c
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WooCommerce Sale Flash Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Sale_Flash_Pro\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_SALE_FLASH_PRO_FILE' ) ) {
	define( 'WC_SALE_FLASH_PRO_FILE', __FILE__ );
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.16
 * @return void
 */
function woocommerce_sale_flash_pro_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Sale Flash Pro requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-sale-flash-pro' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Sale_Flash_Pro' ) ) {
	/**
	 * WC_Sale_Flash_Pro class.
	 */
	class WC_Sale_Flash_Pro extends \Themesquad\WC_Sale_Flash_Pro\Plugin {
		/**
		 * How discounts are displayed.
		 *
		 * @var string
		 */
		public $discount_display_type;

		/**
		 * Extension settings.
		 *
		 * @var array
		 */
		public $settings;

		/**
		 * Constructor.
		 */
		protected function __construct() {
			parent::__construct();

			$this->discount_display_type = get_option( 'woocommerce_sale_flash_type' );

			// Init settings.
			$this->settings = array(
				array(
					'name'    => esc_html__( 'Sale flash type', 'woocommerce-sale-flash-pro' ),
					'desc'    => esc_html__( 'How sale flashes are displayed. Can be overridden by editing products.', 'woocommerce-sale-flash-pro' ),
					'id'      => 'woocommerce_sale_flash_type',
					'css'     => 'min-width:175px;',
					'type'    => 'select',
					'options' => array(
						'percent' => esc_html__( 'Percent e.g. 25% off!', 'woocommerce-sale-flash-pro' ),
						'amount'  => esc_html__( 'Amount e.g. &pound;25 off!', 'woocommerce-sale-flash-pro' ),
					),
				),
				array(
					'name' => esc_html__( 'Sale flash price', 'woocommerce-sale-flash-pro' ),
					'desc' => esc_html__( 'Show original price?', 'woocommerce-sale-flash-pro' ),
					'id'   => 'woocommerce_sale_flash_original_price',
					'type' => 'checkbox',
					'std'  => 'no',
				),
				array(
					'name' => esc_html__( 'Enable for variations', 'woocommerce-sale-flash-pro' ),
					'desc' => esc_html__( 'Show sale flashes for variable products e.g. Up to 40% off!', 'woocommerce-sale-flash-pro' ),
					'id'   => 'woocommerce_sale_flash_variations',
					'type' => 'checkbox',
					'std'  => 'no',
				),
			);

			// Default options.
			add_option( 'woocommerce_sale_flash_type', 'percent' );
			add_option( 'woocommerce_sale_flash_variations', 'no' );

			// Admin.
			add_action( 'woocommerce_settings_pricing_options_end', array( $this, 'admin_settings' ) );
			add_action( 'woocommerce_update_options_general', array( $this, 'save_admin_settings' ) );

			// Filters.
			if ( ! is_admin() ) {
				add_filter( 'woocommerce_sale_flash', array( $this, 'sale_flash' ), 1, 3 );
				add_filter( 'woocommerce_sale_price_html', array( $this, 'sale_price_html' ), 1, 2 );
			}

			add_action( 'woocommerce_product_options_pricing', array( $this, 'write_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'write_panel_save' ) );
		}

		/**
		 * Render the sale flash.
		 *
		 * @param string      $html HTML for the sale flash element.
		 * @param \WP_Post    $post Post object.
		 * @param \WC_Product $product Product Object.
		 * @return string New HTML for the sale flash element.
		 */
		public function sale_flash( $html, $post, $product ) {
			if ( 'yes' === get_post_meta( $post->ID, 'woocommerce_sale_flash_hide', true ) ) {
				return $html;
			}

			$sale_flash_type = get_post_meta( $product->get_id(), 'woocommerce_sale_flash_type', true );

			if ( ! $sale_flash_type ) {
				$sale_flash_type = $this->discount_display_type;
			}

			if ( $product->has_child() ) {
				if ( 'no' === get_option( 'woocommerce_sale_flash_variations' ) ) {
					return $html;
				}

				$discounts = array();

				foreach ( $product->get_children() as $child ) {
					$child_product = wc_get_product( $child );

					if ( ! $child_product->is_in_stock() || ! $child_product->get_sale_price() ) {
						continue;
					}

					if ( 'percent' === $sale_flash_type ) {
						$discount = round( ( ( $child_product->get_regular_price() - $child_product->get_sale_price() ) * 100 ) / $child_product->get_regular_price() );
					} else {
						$discount = $child_product->get_regular_price() - $child_product->get_sale_price();
					}

					$discounts[] = $discount;
				}

				if ( 0 === count( $discounts ) ) {
					return $html;
				}

				// Get Max discount.
				$discount = max( $discounts );

				if ( 'percent' === $sale_flash_type ) {
					/* translators: 1: discount price */
					$html = '<span class="onsale">' . sprintf( esc_html__( 'up to %s%% off!', 'woocommerce-sale-flash-pro' ), $discount ) . '</span>';

				} elseif ( 'amount' === $sale_flash_type && $discount > 0 ) {
					/* translators: 1: discount price */
					$html = '<span class="onsale">' . sprintf( esc_html__( 'up to %s off!', 'woocommerce-sale-flash-pro' ), wc_price( $discount ) ) . '</span>';
				}
			} elseif ( $product->get_regular_price() && $product->get_sale_price() ) {

				if ( 'percent' === $sale_flash_type ) {
					$discount = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) * 100 ) / $product->get_regular_price() );

					/* translators: 1: discount price */
					$html = '<span class="onsale">' . sprintf( esc_html__( '%s%% off!', 'woocommerce-sale-flash-pro' ), $discount ) . '</span>';

				} elseif ( 'amount' === $sale_flash_type ) {

					$discount = $product->get_regular_price() - $product->get_sale_price();

					if ( $discount > 0 ) {
						/* translators: 1: discount price */
						$html = '<span class="onsale">' . sprintf( esc_html__( '%s off!', 'woocommerce-sale-flash-pro' ), wc_price( $discount ) ) . '</span>';
					}
				}
			}

			return $html;
		}

		/**
		 * Render the sale price.
		 *
		 * @param string      $html HTML for the sale price element.
		 * @param \WC_Product $product Product Object.
		 * @return string New HTML for the sale price element.
		 */
		public function sale_price_html( $html, $product ) {
			if ( 'yes' === get_option( 'woocommerce_sale_flash_original_price' ) || is_single() ) {
				return $html;
			}

			return wc_price( $product->get_price() );
		}

		/**
		 * Output admin settings.
		 */
		public function admin_settings() {
			woocommerce_admin_fields( $this->settings );
		}

		/**
		 * Save admin settings.
		 */
		public function save_admin_settings() {
			woocommerce_update_options( $this->settings );
		}

		/**
		 * Output options in write panel.
		 */
		public function write_panel() {
			woocommerce_wp_select(
				array(
					'id'          => 'woocommerce_sale_flash_type',
					'label'       => esc_html__( 'Sale flash type', 'woocommerce-sale-flash-pro' ),
					'description' => esc_html__( 'Choose a sale flash style for this product.', 'woocommerce-sale-flash-pro' ),
					'options'     => array(
						''        => esc_html__( 'Default', 'woocommerce-sale-flash-pro' ),
						'percent' => esc_html__( 'Percent', 'woocommerce-sale-flash-pro' ),
						'amount'  => esc_html__( 'Amount', 'woocommerce-sale-flash-pro' ),
					),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => 'woocommerce_sale_flash_hide',
					'label'       => esc_html__( 'Hide sale flash', 'woocommerce-sale-flash-pro' ),
					'description' => esc_html__( 'Hide the sale flash for this product.', 'woocommerce-sale-flash-pro' ),
				)
			);
		}

		/**
		 * Save product write panel.
		 *
		 * @param integer $post_id Post ID.
		 */
		public function write_panel_save( $post_id ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$woocommerce_sale_flash_type = isset( $_POST['woocommerce_sale_flash_type'] ) ? wc_clean( wp_unslash( $_POST['woocommerce_sale_flash_type'] ) ) : '';

			update_post_meta( $post_id, 'woocommerce_sale_flash_type', in_array( $woocommerce_sale_flash_type, array( 'percent', 'amount' ), true ) ? $woocommerce_sale_flash_type : '' );

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			update_post_meta( $post_id, 'woocommerce_sale_flash_hide', isset( $_POST['woocommerce_sale_flash_hide'] ) ? 'yes' : 'no' );
		}
	}
}

add_action( 'plugins_loaded', 'woocommerce_sale_flash_pro_init' );

/**
 * Initalizes the extension.
 *
 * @since 1.2.16
 * @return void
 */
function woocommerce_sale_flash_pro_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_sale_flash_pro_missing_wc_notice' );
		return;
	}

	WC_Sale_Flash_Pro::instance();
}
