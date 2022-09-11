<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * WooCommerce Product Documents main plugin class.
 *
 * @since 1.0
 */
class WC_Product_Documents extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.13.1';

	/** @var WC_Product_Documents single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'product_documents';

	/** @var \WC_Product_Documents_Admin the plugin admin class instance */
	protected $admin;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-product-documents',
			)
		);

		$this->includes();

		// display any product documents on the product pages
		add_action( 'woocommerce_single_product_summary', array( $this, 'render_product_documents' ), 25 );

		// register widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 1.9.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\Product_Documents\Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function init_plugin() {

		// add accordion shortcode
		add_shortcode( 'woocommerce_product_documents',      array( $this, 'product_documents_shortcode' ) );
		// add products list shortcode
		add_shortcode( 'woocommerce_product_documents_list', array( $this, 'product_documents_shortcode_list' ) );
	}


	/**
	 * Registers product documents widgets.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function register_widgets() {

		// load widget
		require_once( $this->get_plugin_path() . '/src/widgets/class-wc-product-documents-widget-documents.php' );

		// register widget
		register_widget( 'WC_Product_Documents_Widget_Documents' );
	}


	/**
	 * Handles the Product Documents shortcode.
	 *
	 * Renders the product documents UI element.
	 *
	 * @since 1.0
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content (may include HTML)
	 */
	public function product_documents_shortcode( $atts ) {

		require_once( $this->get_plugin_path() . '/src/shortcodes/class-wc-product-documents-shortcode.php' );

		return \WC_Shortcodes::shortcode_wrapper( array( 'WC_Product_Documents_Shortcode', 'output' ), $atts );
	}


	/**
	 * Handles the products list shortcode.
	 *
	 * Renders a list of products with their documents.
	 *
	 * @internal
	 *
	 * @since 1.2.0
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content (may include HTML)
	 */
	public function product_documents_shortcode_list( $atts ) {

		require_once( $this->get_plugin_path() . '/src/shortcodes/class-wc-product-documents-shortcode-list.php' );

		return \WC_Shortcodes::shortcode_wrapper( array( 'WC_Product_Documents_List_Shortcode', 'output' ), $atts );
	}


	/**
	 * Includes required files.
	 *
	 * @since 1.0
	 */
	private function includes() {

		// include template & helper functions
		require_once( $this->get_plugin_path() . '/src/wc-product-documents-template.php' );
		// include main objects
		require_once( $this->get_plugin_path() . '/src/class-wc-product-documents-collection.php' );

		if ( is_admin()  && ! wp_doing_ajax() ) {
			// include admin classes
			$this->admin_includes();
		}
	}


	/**
	 * Includes required admin files.
	 *
	 * @since 1.0
	 */
	private function admin_includes() {

		$this->admin = $this->load_class( '/src/admin/class-wc-product-documents-admin.php', 'WC_Product_Documents_Admin' );
	}


	/**
	 * Renders any product documents.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function render_product_documents() {
		global $post;

		if ( $post && $this->render_documents_on_product_page( $post->ID ) ) {

			woocommerce_product_documents_template( $post->ID, $this->get_documents_title_text( $post->ID ) );
		}
	}


	/**
	 * Determines whether the product documents element should be rendered on the product page.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id product identifier
	 * @return bool
	 */
	public function render_documents_on_product_page( $product_id ) {

		$product = $product_id > 0 ? wc_get_product( $product_id ) : null;

		return $product && 'yes' === $product->get_meta( '_wc_product_documents_display' );
	}


	/**
	 * Gets the admin handler.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Documents_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the main Product Documents instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.3.0
	 *
	 * @return \WC_Product_Documents
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the plugin configuration URL.
	 *
	 * @since 1.0
	 *
	 * @param string|null $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/product-documents/';
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 1.1
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Product Documents', 'woocommerce-product-documents' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.1
	 *
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation url.
	 *
	 * @since 1.4.0
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-product-documents/';
	}

	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the documents title text for the identified product, if any.
	 *
	 * @since 1.1
	 *
	 * @param int $product_id product identifier
	 * @return string documents title text
	 */
	public function get_documents_title_text( $product_id ) {

		// title configured for product?
		$product = $product_id > 0 ? wc_get_product( $product_id ) : null;
		$title   = $product ? $product->get_meta( '_wc_product_documents_title' ) : null;

		// use global default if not found
		return stripslashes( (string) ! $title ? get_option( 'wc_product_documents_title', '' ) : $title );
	}


}


/**
 * Returns the One True Instance of Product Documents.
 *
 * @since 1.3.0
 *
 * @return \WC_Product_Documents
 */
function wc_product_documents() {

	return \WC_Product_Documents::instance();
}
