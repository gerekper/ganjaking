<?php
/**
 * WooCommerce Product Retailers
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Product Retailers main plugin class.
 *
 * @since 1.0
 */
class WC_Product_Retailers extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.14.0';

	/** @var \WC_Product_Retailers single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'product_retailers';

	/** plugin text domain, DEPRECATED as of 1.7.0 */
	const TEXT_DOMAIN = 'woocommerce-product-retailers';

	/** @var \WC_Product_Retailers_Admin instance */
	protected $admin;

	/** @var \WC_Product_Retailers_List the admin retailers list screen */
	private $admin_retailers_list;

	/** @var \WC_Product_Retailers_Edit the admin retailers edit screen */
	private $admin_retailers_edit;

	/** @var boolean set to try after the retailer dropdown is rendered on the product page */
	private $retailer_dropdown_rendered = false;


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-product-retailers',
			)
		);

		// include required files
		$this->includes();

		// initialize custom taxonomy
		add_action( 'init', array( $this, 'init_taxonomies' ), 25 );

		// render frontend embedded styles
		add_action( 'wp_print_styles', array( $this, 'render_embedded_styles' ), 1 );

		// control the loop add to cart buttons for the product retailer products
		add_filter( 'woocommerce_is_purchasable',           array( $this, 'product_is_purchasable' ), 10, 2 );
		add_filter( 'woocommerce_product_is_visible',       array( $this, 'product_variation_is_visible' ), 1, 2 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 2 );

		// register widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// add the product retailers dropdown on the single product page (next to the 'add to cart' button if available)
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_retailer_dropdown' ) );
		add_action( 'woocommerce_single_product_summary',   array( $this, 'add_retailer_dropdown' ), 35 );
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 1.11.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\Product_Retailers\Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function init_plugin() {

		if ( ! is_admin() || ! is_ajax() ) {

			// add accordion shortcode
			add_shortcode( 'woocommerce_product_retailers', array( $this, 'product_retailers_shortcode' ) );
		}
	}


	/**
	 * Initializes custom taxonomies.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function init_taxonomies() {

		\WC_Product_Retailers_Taxonomy::initialize();
	}


	/**
	 * Registers product retailers widgets.
	 *
	 * @internal
	 *
	 * @since 1.4.0
	 */
	public function register_widgets() {

		// load widget
		require_once( $this->get_plugin_path() . '/includes/widgets/class-wc-product-retailers-widget.php' );

		// register widget
		register_widget( 'WC_Product_Retailers_Widget' );
	}


	/**
	 * Handles the Product Retailers shortcode.
	 *
	 * Renders the product retailers UI element.
	 *
	 * @internal
	 *
	 * @since 1.4.0
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content (may contain HTML)
	 */
	public function product_retailers_shortcode( $atts ) {

		require_once( $this->get_plugin_path() . '/includes/shortcodes/class-wc-product-retailers-shortcode.php' );

		return \WC_Shortcodes::shortcode_wrapper( array( 'WC_Product_Retailers_Shortcode', 'output' ), $atts );
	}


	/**
	 * Includes required files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-product-retailers-product.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-product-retailers-taxonomy.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-product-retailers-retailer.php' );

		if ( is_admin() ) {

			$this->admin_includes();
		}

		require_once( $this->get_plugin_path() . '/includes/wc-product-retailers-template-functions.php' );
	}


	/**
	 * Include required admin files.
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		$this->admin                = $this->load_class( '/includes/admin/class-wc-product-retailers-admin.php', 'WC_Product_Retailers_Admin' );
		$this->admin_retailers_list = $this->load_class( '/includes/admin/class-wc-product-retailers-list.php',  'WC_Product_Retailers_List' );
		$this->admin_retailers_edit = $this->load_class( '/includes/admin/class-wc-product-retailers-edit.php',  'WC_Product_Retailers_Edit' );
	}


	/**
	 * Renders the product retailers frontend button/select box styles.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function render_embedded_styles() {
		global $post;

		if ( is_product() ) {

			$product = wc_get_product( $post->ID );

			if ( \WC_Product_Retailers_Product::has_retailers( $product ) ) :

				?>
				<style type="text/css">
					.wc-product-retailers-wrap {
						clear:both;
						padding: 1em 0;
					}
					.wc-product-retailers-wrap ul {
						list-style: none;
						margin-left: 0;
					}
					.wc-product-retailers-wrap ul.wc-product-retailers li {
						margin-bottom: 5px;
						margin-right: 5px;
						overflow: auto;
						zoom: 1;
					}
				</style>
				<?php

			endif;
		}
	}


	/**
	 * Makes product variations visible.
	 *
	 * Marks variations visible even if they don't have a price, as long as they are sold only through retailers.
	 * This is one of the few times where we are altering this filter in a positive manner, and so we try to hook into it first.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param bool $visible whether the product is visible
	 * @param int $product_id the product id
	 * @return bool
	 */
	public function product_variation_is_visible( $visible, $product_id ) {

		$product = wc_get_product( $product_id );

		if ( $product->is_type( 'variable' ) && \WC_Product_Retailers_Product::is_retailer_only_purchase( $product ) ) {

			$visible = true;
		}

		return $visible;
	}


	/**
	 * Marks "retailer only" products as not purchasable.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $purchasable whether the product is purchasable
	 * @param \WC_Product $product the product
	 * @return bool
	 */
	public function product_is_purchasable( $purchasable, $product ) {

		if ( \WC_Product_Retailers_Product::is_retailer_only_purchase( $product ) ) {

			$purchasable = false;
		}

		return $purchasable;
	}


	/**
	 * Modifies the 'add to cart' text.
	 *
	 * Runs for simple product retailer products which are sold only through retailers to display the catalog button text.
	 * This is because the customer must select a retailer to purchase.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $label the 'add to cart' label
	 * @param \WC_Product $product product object
	 * @return string the 'add to cart' label
	 */
	public function add_to_cart_text( $label, $product ) {

		if ( $product->is_type( array( 'simple', 'subscription' ) ) && \WC_Product_Retailers_Product::is_retailer_only_purchase( $product ) && WC_Product_Retailers_Product::has_retailers( $product ) ) {

			$label = __( \WC_Product_Retailers_Product::get_catalog_button_text( $product ), 'woocommerce-product-retailers' );
		}

		return $label;
	}


	/**
	 * Displays the product retailers drop down box.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_retailer_dropdown() {
		global $product;

		// get any product retailers
		$retailers = \WC_Product_Retailers_Product::get_product_retailers( $product );

		// only add dropdown if retailers have been assigned and it hasn't already been displayed
		if (    $this->retailer_dropdown_rendered
			 || empty( $retailers )
			 || \WC_Product_Retailers_Product::product_retailers_hidden( $product )
			 || \WC_Product_Retailers_Product::product_retailers_hidden_if_in_stock( $product ) ) {

			return;
		}

		$this->retailer_dropdown_rendered = true;

		woocommerce_single_product_product_retailers( $product, $retailers );
	}


	/**
	 * Returns the main Product Retailers instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.5.0
	 *
	 * @return \WC_Product_Retailers
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the admin handler.
	 *
	 * @since 1.8.0
	 *
	 * @return \WC_Product_Retailers_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 1.2.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Product Retailers', 'woocommerce-product-retailers' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.2.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the global default Product Button text default.
	 *
	 * @since 1.0.0
	 *
	 * @return string the default product button text
	 */
	public function get_product_button_text() {

		return get_option( 'wc_product_retailers_product_button_text', '' );
	}


	/**
	 * Gets the global default Catalog Button text default.
	 *
	 * @since 1.0.0
	 *
	 * @return string the default product button text
	 */
	public function get_catalog_button_text() {

		return get_option( 'wc_product_retailers_catalog_button_text', '' );
	}


	/**
	 * Gets the plugin configuration URL
	 *
	 * @since 1.1.0
	 *
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products&section=display' );
	}


	/**
	 * Gets the plugin documentation url.
	 *
	 * @since 1.6.0
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-product-retailers/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/product-retailers/';
	}


}


/**
 * Returns the One True Instance of Product Retailers.
 *
 * @since 1.5.0
 *
 * @return \WC_Product_Retailers
 */
function wc_product_retailers() {

	return \WC_Product_Retailers::instance();
}
