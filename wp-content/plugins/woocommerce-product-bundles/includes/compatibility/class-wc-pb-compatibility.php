<?php
/**
 * WC_PB_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    4.6.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles compatibility with other WC extensions.
 *
 * @class    WC_PB_Compatibility
 * @version  6.3.4
 */
class WC_PB_Compatibility {

	/**
	 * Min required plugin versions to check.
	 * @var array
	 */
	private $required = array();

	/**
	 * Publicly accessible props for use by compat classes. Still not moved for back-compat.
	 * @var array
	 */
	public static $addons_prefix          = '';
	public static $bundle_prefix          = '';
	public static $compat_product         = '';
	public static $compat_bundled_product = '';
	public static $stock_data;

	/**
	 * The single instance of the class.
	 * @var WC_PB_Compatibility
	 *
	 * @since 5.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_PB_Compatibility instance. Ensures only one instance of WC_PB_Compatibility is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_PB_Compatibility
	 * @since  5.0.0
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 5.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '5.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 5.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '5.0.0' );
	}

	/**
	 * Setup compatibility class.
	 */
	protected function __construct() {

		// Define dependencies.
		$this->required = array(
			'cp'     => '6.2.0',
			'pao'    => '3.0.14',
			'minmax' => '1.4.1',
			'topatc' => '1.0.3',
			'bd'     => '1.3.1'
		);

		// Initialize.
		$this->load_modules();
	}

	/**
	 * Initialize.
	 *
	 * @since  5.4.0
	 *
	 * @return void
	 */
	protected function load_modules() {

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( $this, 'add_compatibility_notices' ) );
		}

		// Load modules.
		add_action( 'plugins_loaded', array( $this, 'module_includes' ), 100 );

		// Prevent initialization of deprecated mini-extensions.
		$this->unload_modules();
	}

	/**
	 * Core compatibility functions.
	 *
	 * @return void
	 */
	public static function core_includes() {
		require_once( 'core/class-wc-pb-core-compatibility.php' );
	}

	/**
	 * Prevent deprecated mini-extensions from initializing.
	 *
	 * @since  5.0.0
	 *
	 * @return void
	 */
	protected function unload_modules() {

		// Tabular Layout mini-extension was merged into PB.
		if ( class_exists( 'WC_PB_Tabular_Layout' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_PB_Tabular_Layout', 'load_plugin' ), 10 );
		}

		// Bundle-Sells mini-extension was merged into PB.
		if ( class_exists( 'WC_PB_Bundle_Sells' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_PB_Bundle_Sells', 'load_plugin' ), 10 );
		}
	}

	/**
	 * Load compatibility classes.
	 *
	 * @return void
	 */
	public function module_includes() {

		$module_paths = array();

		// Addons support.
		if ( class_exists( 'WC_Product_Addons' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, $this->required[ 'pao' ] ) >= 0 ) {
			$module_paths[ 'product_addons' ] = 'modules/class-wc-pb-addons-compatibility.php';
		}

		// NYP support.
		if ( function_exists( 'WC_Name_Your_Price' ) ) {
			$module_paths[ 'name_your_price' ] = 'modules/class-wc-pb-nyp-compatibility.php';
		}

		// Points and Rewards support.
		if ( class_exists( 'WC_Points_Rewards_Product' ) ) {
			$module_paths[ 'points_rewards_products' ] = 'modules/class-wc-pb-pnr-compatibility.php';
		}

		// Pre-orders support.
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			$module_paths[ 'pre_orders' ] = 'modules/class-wc-pb-po-compatibility.php';
		}

		// Composite Products support.
		if ( class_exists( 'WC_Composite_Products' ) && function_exists( 'WC_CP' ) && version_compare( WC_PB()->plugin_version( true, WC_CP()->version ), $this->required[ 'cp' ] ) >= 0 ) {
			$module_paths[ 'composite_products' ] = 'modules/class-wc-pb-cp-compatibility.php';
		}

		// One Page Checkout support.
		if ( function_exists( 'is_wcopc_checkout' ) ) {
			$module_paths[ 'one_page_checkout' ] = 'modules/class-wc-pb-opc-compatibility.php';
		}

		// Cost of Goods support.
		if ( class_exists( 'WC_COG' ) ) {
			$module_paths[ 'cost_of_goods' ] = 'modules/class-wc-pb-cog-compatibility.php';
		}

		// QuickView support.
		if ( class_exists( 'WC_Quick_View' ) ) {
			$module_paths[ 'quickview' ] = 'modules/class-wc-pb-qv-compatibility.php';
		}

		// PIP support.
		if ( class_exists( 'WC_PIP' ) ) {
			$module_paths[ 'pip' ] = 'modules/class-wc-pb-pip-compatibility.php';
		}

		// Subscriptions fixes.
		if ( class_exists( 'WC_Subscriptions' ) ) {
			$module_paths[ 'subscriptions' ] = 'modules/class-wc-pb-subscriptions-compatibility.php';
		}

		// Subscriptions fixes.
		if ( class_exists( 'WC_Memberships' ) ) {
			$module_paths[ 'memberships' ] = 'modules/class-wc-pb-members-compatibility.php';
		}

		// Min Max Quantities integration.
		if ( class_exists( 'WC_Min_Max_Quantities' ) ) {
			$module_paths[ 'min_max_quantities' ] = 'modules/class-wc-pb-min-max-compatibility.php';
		}

		// WP Import/Export support -- based on a hack that does not when exporting using WP-CLI.
		if ( ! defined( 'WP_CLI' )  ) {
			$module_paths[ 'wp_import_export' ] = 'modules/class-wc-pb-wp-ie-compatibility.php';
		}

		// WooCommerce Give Products support.
		if ( class_exists( 'WC_Give_Products' ) ) {
			$module_paths[ 'give_products' ] = 'modules/class-wc-pb-give-products-compatibility.php';
		}

		// Shipwire integration.
		if ( class_exists( 'WC_Shipwire' ) ) {
			$module_paths[ 'shipwire' ] = 'modules/class-wc-pb-shipwire-compatibility.php';
		}

		// Wishlists compatibility.
		if ( class_exists( 'WC_Wishlists_Plugin' ) ) {
			$module_paths[ 'wishlists' ] = 'modules/class-wc-pb-wl-compatibility.php';
		}

		// WooCommerce Services compatibility.
		if ( class_exists( 'WC_Connect_Loader' ) ) {
			$module_paths[ 'wc_services' ] = 'modules/class-wc-pb-wc-services-compatibility.php';
		}

		// Shipstation integration.
		$module_paths[ 'shipstation' ] = 'modules/class-wc-pb-shipstation-compatibility.php';

		// Storefront compatibility.
		$module_paths[ 'storefront' ] = 'modules/class-wc-pb-sf-compatibility.php';

		// ThemeAlien Variation Swatches for WooCommerce compatibility.
		$module_paths[ 'taws_variation_swatches' ] = 'modules/class-wc-pb-taws-variation-swatches-compatibility.php';

		/**
		 * 'woocommerce_bundles_compatibility_modules' filter.
		 *
		 * Use this to filter the required compatibility modules.
		 *
		 * @since  5.7.6
		 * @param  array $module_paths
		 */
		$module_paths = apply_filters( 'woocommerce_bundles_compatibility_modules', $module_paths );

		foreach ( $module_paths as $name => $path ) {
			require_once( $path );
		}
	}

	/**
	 * Get min module version.
	 *
	 * @since  6.0.0
	 * @return bool
	 */
	public function get_required_module_version( $module ) {
		return isset( $this->required[ $module ] ) ? $this->required[ $module ] : null;
	}

	/**
	 * Checks versions of compatible/integrated/deprecated extensions.
	 *
	 * @return void
	 */
	public function add_compatibility_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// CP version check.
		if ( class_exists( 'WC_Composite_Products' ) && function_exists( 'WC_CP' ) ) {
			$required_version = $this->required[ 'cp' ];
			if ( version_compare( WC_PB()->plugin_version( true, WC_CP()->version ), $required_version ) < 0 ) {

				$extension      = __( 'Composite Products', 'woocommerce-product-bundles' );
				$extension_full = __( 'WooCommerce Composite Products', 'woocommerce-product-bundles' );
				$extension_url  = 'https://woocommerce.com/products/composite-products/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Bundles</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-product-bundles' ), $extension, $extension_url, $extension_full, $required_version );

				WC_PB_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'cp_lt_' . $required_version, 'type' => 'warning' ) );
			}
		}

		// Addons version check.
		if ( class_exists( 'WC_Product_Addons' ) ) {

			$required_version = $this->required[ 'pao' ];

			if ( ! defined( 'WC_PRODUCT_ADDONS_VERSION' ) || version_compare( WC_PRODUCT_ADDONS_VERSION, $required_version ) < 0 ) {

				$extension      = __( 'Product Add-Ons', 'woocommerce-product-bundles' );
				$extension_full = __( 'WooCommerce Product Add-Ons', 'woocommerce-product-bundles' );
				$extension_url  = 'https://woocommerce.com/products/product-add-ons/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Bundles</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-product-bundles' ), $extension, $extension_url, $extension_full, $required_version );

				WC_PB_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'addons_lt_' . $required_version, 'type' => 'warning' ) );
			}
		}

		// Tabular layout mini-extension check.
		if ( class_exists( 'WC_PB_Tabular_Layout' ) ) {
			$notice = sprintf( __( 'The <strong>Tabular Layout</strong> mini-extension has been rolled into <strong>Product Bundles</strong>. Please deactivate and remove the <strong>Product Bundles - Tabular Layout</strong> feature plugin.', 'woocommerce-product-bundles' ) );
			WC_PB_Admin_Notices::add_notice( $notice, 'warning' );
		}

		// Bundle-Sells mini-extension version check.
		if ( class_exists( 'WC_PB_Bundle_Sells' ) ) {
			$notice = sprintf( __( 'The <strong>Bundle-Sells</strong> mini-extension has been rolled into <strong>Product Bundles</strong>. Please deactivate and remove the <strong>Product Bundles - Bundle-Sells</strong> feature plugin.', 'woocommerce-product-bundles' ) );
			WC_PB_Admin_Notices::add_notice( $notice, 'warning' );
		}

		// Min/Max Items mini-extension version check.
		if ( class_exists( 'WC_PB_Min_Max_Items' ) ) {
			$required_version = $this->required[ 'minmax' ];
			if ( version_compare( WC_PB()->plugin_version( true, WC_PB_Min_Max_Items::$version ), $required_version ) < 0 ) {

				$extension      = $extension_full = __( 'Product Bundles - Min/Max Items', 'woocommerce-product-bundles' );
				$extension_url  = 'https://wordpress.org/plugins/product-bundles-minmax-items-for-woocommerce/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Bundles</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-product-bundles' ), $extension, $extension_url, $extension_full, $required_version );

				WC_PB_Admin_Notices::add_notice( $notice, 'warning' );
			}
		}

		// Top Add-to-Cart mini-extension version check.
		if ( class_exists( 'WC_PB_Top_Add_To_Cart' ) ) {
			$required_version = $this->required[ 'topatc' ];
			if ( version_compare( WC_PB()->plugin_version( true, WC_PB_Top_Add_To_Cart::$version ), $required_version ) < 0 ) {

				$extension = __( 'Product Bundles - Top Add to Cart Button', 'woocommerce-product-bundles' );
				$notice    = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Bundles</strong>. Please update <strong>%1$s</strong> to version <strong>%2$s</strong> or higher.', 'woocommerce-product-bundles' ), $extension, $required_version );

				WC_PB_Admin_Notices::add_notice( $notice, 'warning' );
			}
		}

		// Bulk Discounts mini-extension version check.
		if ( class_exists( 'WC_PB_Bulk_Discounts' ) ) {
			$required_version = $this->required[ 'bd' ];
			if ( version_compare( WC_PB()->plugin_version( true, WC_PB_Bulk_Discounts::$version ), $required_version ) < 0 ) {

				$extension      = $extension_full = __( 'Product Bundles - Bulk Discounts', 'woocommerce-product-bundles' );
				$extension_url  = 'https://wordpress.org/plugins/product-bundles-bulk-discounts-for-woocommerce/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Bundles</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-product-bundles' ), $extension, $extension_url, $extension_full, $required_version );

				WC_PB_Admin_Notices::add_notice( $notice, 'warning' );
			}
		}
	}

	/**
	 * Rendering a PIP document?
	 *
	 * @since  5.5.0
	 *
	 * @param  string  $type
	 * @return boolean
	 */
	public function is_pip( $type = '' ) {
		return class_exists( 'WC_PB_PIP_Compatibility' ) && WC_PB_PIP_Compatibility::rendering_document( $type );
	}

	/**
	 * Tells if a product is a Name Your Price product, provided that the extension is installed.
	 *
	 * @param  mixed  $product_id
	 * @return boolean
	 */
	public function is_nyp( $product_id ) {

		if ( ! class_exists( 'WC_Name_Your_Price_Helpers' ) ) {
			return false;
		}

		if ( WC_Name_Your_Price_Helpers::is_nyp( $product_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Tells if a product is a subscription, provided that Subs is installed.
	 *
	 * @param  mixed  $product
	 * @return boolean
	 */
	public function is_subscription( $product ) {

		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return false;
		}

		return WC_Subscriptions_Product::is_subscription( $product );
	}

	/**
	 * Tells if an order item is a subscription, provided that Subs is installed.
	 *
	 * @param  mixed     $order
	 * @param  WC_Prder  $order
	 * @return boolean
	 */
	public function is_item_subscription( $order, $item ) {

		if ( ! class_exists( 'WC_Subscriptions_Order' ) ) {
			return false;
		}

		return WC_Subscriptions_Order::is_item_subscription( $order, $item );
	}

	/**
	 * Checks if a product has any required addons.
	 *
	 * @since  5.9.2
	 *
	 * @param  mixed    $product
	 * @param  boolean  $required
	 * @return boolean
	 */
	public function has_addons( $product, $required = false ) {

		if ( ! class_exists( 'WC_PB_Addons_Compatibility' ) ) {
			return false;
		}

		return WC_PB_Addons_Compatibility::has_addons( $product, $required );
	}

	/**
	 * Alias to 'wc_cp_is_composited_cart_item'.
	 *
	 * @since  5.0.0
	 *
	 * @param  array  $item
	 * @return boolean
	 */
	public function is_composited_cart_item( $item ) {

		$is = false;

		if ( function_exists( 'wc_cp_is_composited_cart_item' ) ) {
			$is = wc_cp_is_composited_cart_item( $item );
		}

		return $is;
	}

	/**
	 * Alias to 'wc_cp_is_composited_order_item'.
	 *
	 * @since  5.0.0
	 *
	 * @param  array     $item
	 * @param  WC_Order  $order
	 * @return boolean
	 */
	public function is_composited_order_item( $item, $order ) {

		$is = false;

		if ( function_exists( 'wc_cp_is_composited_order_item' ) ) {
			$is = wc_cp_is_composited_order_item( $item, $order );
		}

		return $is;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Checks if a product has required addons.
	 *
	 * @param  mixed  $product
	 * @return boolean
	 */
	public function has_required_addons( $product ) {
		_deprecated_function( __METHOD__ . '()', '5.9.2', __CLASS__ . '::has_addons()' );
		return $this->has_addons( $product, true );
	}
}

WC_PB_Compatibility::core_includes();
