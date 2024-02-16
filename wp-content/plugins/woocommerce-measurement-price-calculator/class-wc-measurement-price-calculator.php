<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * Main WooCommerce Measurement Price Calculator class.
 *
 * @since 3.0
 */
class WC_Measurement_Price_Calculator extends Framework\SV_WC_Plugin {

	const VERSION = '3.22.2';

	/** @var WC_Measurement_Price_Calculator single instance of this plugin */
	protected static $instance;

	/** the plugin id */
	const PLUGIN_ID = 'measurement_price_calculator';

	/** @var \WC_Price_Calculator_Inventory the pricing calculator inventory handling class */
	private $pricing_calculator_inventory;

	/** @var \WC_Price_Calculator_Cart the pricing calculator cart class */
	private $cart;

	/** @var \WC_Price_Calculator_Product_Loop the pricing calculator frontend product loop class */
	private $product_loop;

	/** @var \WC_Price_Calculator_Product_Page the pricing calculator frontend product page class */
	private $product_page;

	/** @var \SkyVerge\WooCommerce\Measurement_Price_Calculator\Shortcodes instance */
	private $shortcodes;

	/** @var \WC_Price_Calculator_Compatibility the compatibility class */
	private $compatibility;


	/**
	 * Constructs and initializes the main plugin class.
	 *
	 * @since 3.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain'        => 'woocommerce-measurement-price-calculator',
				'supported_features' => [
					'hpos'   => true,
					'blocks' => [
						'cart'     => false,
						'checkout' => false,
					],
				],
			]
		);

		// include required files
		$this->includes();

		add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );

		// stock amounts are *not* integers by default
		remove_filter( 'woocommerce_stock_amount', 'intval' );
		// so let them be
		add_filter( 'woocommerce_stock_amount', 'floatval' );
	}


	/**
	 * Includes required files.
	 *
	 * @since 3.0
	 */
	private function includes() {

		$plugin_path = $this->get_plugin_path();

		require_once( $plugin_path . '/src/class-wc-price-calculator-cart.php' );
		require_once( $plugin_path . '/src/class-wc-price-calculator-measurement.php' );
		require_once( $plugin_path . '/src/class-wc-price-calculator-product-loop.php' );
		require_once( $plugin_path . '/src/class-wc-price-calculator-product-page.php' );
		require_once( $plugin_path . '/src/class-wc-price-calculator-product.php' );
		require_once( $plugin_path . '/src/class-wc-price-calculator-settings.php' );

		if ( is_admin() ) {

			require_once( $plugin_path . '/src/admin/woocommerce-measurement-price-calculator-admin-init.php' );
		}
	}


	/**
	 * Initializes Measurement Price Calculator when WooCommerce is ready.
	 *
	 * @internal
	 *
	 * @since 3.0
	 */
	public function woocommerce_init() {

		$this->pricing_calculator_inventory = $this->load_class( '/src/class-wc-price-calculator-inventory.php', 'WC_Price_Calculator_Inventory' );

		$this->product_loop = new \WC_Price_Calculator_Product_Loop();

		$this->product_page = new \WC_Price_Calculator_Product_Page();

		$this->cart = new \WC_Price_Calculator_Cart();

		$this->shortcodes = $this->load_class( '/src/Shortcodes.php', '\\SkyVerge\\WooCommerce\\Measurement_Price_Calculator\\Shortcodes' );

		$this->compatibility = $this->load_class( '/src/class-wc-price-calculator-compatibility.php', 'WC_Price_Calculator_Compatibility' );
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 3.14.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Measurement_Price_Calculator\Lifecycle( $this );
	}


	/**
	 * Gets the pricing calculator inventory handler instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \WC_Price_Calculator_Inventory
	 */
	public function get_pricing_calculator_inventory_instance() {

		return $this->pricing_calculator_inventory;
	}


	/**
	 * Gets the cart handler instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \WC_Price_Calculator_Cart
	 */
	public function get_cart_instance() {

		return $this->cart;
	}


	/**
	 * Gets the product loop handler instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \WC_Price_Calculator_Product_Loop
	 */
	public function get_product_loop_instance() {

		return $this->product_loop;
	}


	/**
	 * Gets the product page handler instance.
	 *
	 * @since 3.7.0
	 *
	 * @return \WC_Price_Calculator_Product_Page
	 */
	public function get_product_page_instance() {

		return $this->product_page;
	}


	/**
	 * Gets the shortcodes handler instance.
	 *
	 * @since 3.14.0
	 *
	 * @return \SkyVerge\WooCommerce\Measurement_Price_Calculator\Shortcodes
	 */
	public function get_shortcodes_instance() {

		return $this->shortcodes;
	}


	/**
	 * Gets the compatibility handler instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \WC_Price_Calculator_Compatibility
	 */
	public function get_compatibility_instance() {

		return $this->compatibility;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 3.3
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Measurement Price Calculator', 'woocommerce-measurement-price-calculator' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 3.3
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/measurement-price-calculator/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 3.14.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/measurement-price-calculator/';
	}


	/**
	 * Gets the plugin settings URL.
	 *
	 * @since 3.10.1
	 *
	 * @param string|null $_ unused
	 * @return string
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Returns the main Measurement Price Calculator Instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @see wc_measurement_price_calculator()
	 *
	 * @since 3.6.0
	 *
	 * @return \WC_Measurement_Price_Calculator
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}


/**
 * Returns the One True Instance of Measurement Price Calculator.
 *
 * @since 3.6.0
 *
 * @return \WC_Measurement_Price_Calculator
 */
function wc_measurement_price_calculator() {

	return \WC_Measurement_Price_Calculator::instance();
}
