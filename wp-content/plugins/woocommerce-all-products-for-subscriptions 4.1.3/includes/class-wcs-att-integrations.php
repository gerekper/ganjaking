<?php
/**
 * WCS_ATT_Integrations class
 *
 * @package  WooCommerce All Products For Subscriptions
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility with other extensions.
 *
 * @class    WCS_ATT_Integrations
 * @version  4.0.6
 */
class WCS_ATT_Integrations {

	/**
	 * Min required plugin versions to check.
	 *
	 * @var array
	 */
	private static $required = array();

	/**
	 * Cache block based cart detection result.
	 *
	 * @since  3.3.0
	 * @var    array
	 */
	private static $is_block_based_cart = null;

	/**
	 * Initialize.
	 */
	public static function init() {

		self::$required = array(
			'cp'     => '6.2.0',
			'pb'     => '6.2.0',
			'addons' => '3.0.14',
			'blocks' => '7.2.0'
		);

		// Cart/Checkout Block support.
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), self::$required[ 'blocks' ] ) >= 0 ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-blocks.php' );
		}

		// Product Bundles and Composite Products support.
		if ( class_exists( 'WC_Bundles' ) || class_exists( 'WC_Composite_Products' ) || class_exists( 'WC_Mix_and_Match' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
			WCS_ATT_Integration_PB_CP::init();
		}

		// Product Add-Ons support.
		if ( class_exists( 'WC_Product_Addons' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, self::$required[ 'addons' ] ) >= 0 ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pao.php' );
			WCS_ATT_Integration_PAO::init();
		}

		// Name Your Price support.
		if ( class_exists( 'WC_Name_Your_Price' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-nyp.php' );
			WCS_ATT_Integration_NYP::init();
		}

		// Flatsome compatibility.
		if ( function_exists( 'wc_is_active_theme' ) && wc_is_active_theme( 'flatsome' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-fs.php' );
		}

		// Square compatibility.
		if ( class_exists( 'WooCommerce\Square\Plugin' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-square.php' );
		}

		// AfterPay compatibility.
		if ( class_exists( 'WC_Gateway_Afterpay' ) && is_callable( array( 'WC_Gateway_Afterpay', 'getInstance' ) ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-afterpay.php' );
			WCS_ATT_Integration_AfterPay::init();
		}

		// Stripe compatibility.
		if ( class_exists( 'WC_Gateway_Stripe' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-stripe.php' );
		}

		// WooCommerce Payments compatibility.
		if ( class_exists( 'WC_Payments' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-wc-payments.php' );
		}

		// Declare HPOS compatibility.
		add_action( 'before_woocommerce_init', array( __CLASS__, 'declare_hpos_compatibility' ) );

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( __CLASS__, 'display_notices' ) );
		}
	}

	/**
	 * Declare HPOS( Custom Order tables) compatibility.
	 *
	 * @since 4.0.3
	 */
	public static function declare_hpos_compatibility() {

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		$compatibility = WCS_ATT_Core_Compatibility::is_wc_version_gte( '7.6.0' );

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCS_ATT()->plugin_basename(), $compatibility );
	}

	/**
	 * Checks versions of compatible/integrated/deprecated extensions.
	 *
	 * @since  2.4.0
	 *
	 * @return void
	 */
	public static function display_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// PB version check.
		if ( class_exists( 'WC_Bundles' ) && function_exists( 'WC_PB' ) ) {
			$required_version = self::$required[ 'pb' ];
			if ( version_compare( WCS_ATT()->plugin_version( true, WC_PB()->version ), $required_version ) < 0 ) {

				$extension      = __( 'Product Bundles', 'woocommerce-all-products-for-subscriptions' );
				$extension_full = __( 'WooCommerce Product Bundles', 'woocommerce-all-products-for-subscriptions' );
				$extension_url  = 'https://woocommerce.com/products/product-bundles/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>All Products for WooCommerce Subscriptions</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-all-products-for-subscriptions' ), $extension, $extension_url, $extension_full, $required_version );

				WCS_ATT_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'pb_lt_' . $required_version, 'type' => 'native' ) );
			}
		}

		// CP version check.
		if ( class_exists( 'WC_Composite_Products' ) && function_exists( 'WC_CP' ) ) {
			$required_version = self::$required[ 'cp' ];
			if ( version_compare( WCS_ATT()->plugin_version( true, WC_CP()->version ), $required_version ) < 0 ) {

				$extension      = __( 'Composite Products', 'woocommerce-all-products-for-subscriptions' );
				$extension_full = __( 'WooCommerce Composite Products', 'woocommerce-all-products-for-subscriptions' );
				$extension_url  = 'https://woocommerce.com/products/composite-products/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>All Products for WooCommerce Subscriptions</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-all-products-for-subscriptions' ), $extension, $extension_url, $extension_full, $required_version );

				WCS_ATT_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'cp_lt_' . $required_version, 'type' => 'native' ) );
			}
		}

		// PAO version check.
		if ( class_exists( 'WC_Product_Addons' ) ) {

			$required_version = self::$required[ 'addons' ];

			if ( ! defined( 'WC_PRODUCT_ADDONS_VERSION' ) || version_compare( WC_PRODUCT_ADDONS_VERSION, $required_version ) < 0 ) {

				$extension      = __( 'Product Add-Ons', 'woocommerce-all-products-for-subscriptions' );
				$extension_full = __( 'WooCommerce Product Add-Ons', 'woocommerce-all-products-for-subscriptions' );
				$extension_url  = 'https://woocommerce.com/products/product-add-ons/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>All Products for WooCommerce Subscriptions</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-all-products-for-subscriptions' ), $extension, $extension_url, $extension_full, $required_version );

				WCS_ATT_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'addons_lt_' . $required_version, 'type' => 'native' ) );
			}
		}
	}

	/**
	 * Whether the cart page contains the cart block.
	 *
	 * @since  3.3.0
	 *
	 * @param  string  $route
	 * @return boolean
	 */
	public static function is_block_based_cart() {

		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Package' ) ) {
			return false;
		}

		if ( is_null( self::$is_block_based_cart ) ) {

			self::$is_block_based_cart = false;

			$checkout_block_data = class_exists( 'WC_Blocks_Utils' ) ? WC_Blocks_Utils::get_blocks_from_page( 'woocommerce/cart', 'cart' ) : false;

			if ( ! empty( $checkout_block_data ) ) {
				self::$is_block_based_cart = true;
			}
		}

		return self::$is_block_based_cart;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated
	|--------------------------------------------------------------------------
	*/

	/**
	 * Checks if the passed product is of a supported bundle type. Returns the type if yes, or false if not.
	 *
	 * @param  WC_Product  $product
	 * @return boolean
	 */
	public static function is_bundle_type_product( $product ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::is_bundle_type_product()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::is_bundle_type_product( $product );
	}

	/**
	 * Given a bundle-type child cart item, find and return its container cart item or its cart id when the $return_id arg is true.
	 *
	 * @since  2.1.0
	 *
	 * @param  array    $cart_item
	 * @param  array    $cart_contents
	 * @param  boolean  $return_id
	 * @return mixed
	 */
	public static function get_bundle_type_cart_item_container( $cart_item, $cart_contents = false, $return_id = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::get_bundle_type_cart_item_container()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::get_bundle_type_cart_item_container( $cart_item, $cart_contents, $return_id );
	}

	/**
	 * Given a bundle-type container cart item, find and return its child cart items - or their cart ids when the $return_ids arg is true.
	 *
	 * @since  2.1.0
	 *
	 * @param  array    $cart_item
	 * @param  array    $cart_contents
	 * @param  boolean  $return_ids
	 * @return mixed
	 */
	public static function get_bundle_type_cart_items( $cart_item, $cart_contents = false, $return_ids = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::get_bundle_type_cart_items()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::get_bundle_type_cart_items( $cart_item, $cart_contents, $return_ids );
	}

	/**
	 * True if a cart item appears to be a bundle-type container item.
	 *
	 * @since  2.1.0
	 *
	 * @param  array  $cart_item
	 * @return boolean
	 */
	public static function is_bundle_type_container_cart_item( $cart_item ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::is_bundle_type_container_cart_item()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::is_bundle_type_container_cart_item( $cart_item );
	}

	/**
	 * True if a cart item is part of a bundle-type product.
	 *
	 * @since  2.1.0
	 *
	 * @param  array  $cart_item
	 * @param  array  $cart_contents
	 * @return boolean
	 */
	public static function is_bundle_type_cart_item( $cart_item, $cart_contents = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::is_bundle_type_cart_item()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::is_bundle_type_cart_item( $cart_item, $cart_contents );
	}

	/**
	 * Given a bundle-type child order item, find and return its container order item or its order item id when the $return_id arg is true.
	 *
	 * @since  2.1.0
	 *
	 * @param  array     $order_item
	 * @param  WC_Order  $order
	 * @param  boolean   $return_id
	 * @return mixed
	 */
	public static function get_bundle_type_order_item_container( $order_item, $order = false, $return_id = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::get_bundle_type_order_item_container()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::get_bundle_type_order_item_container( $order_item, $order, $return_id );
	}

	/**
	 * Given a bundle-type container order item, find and return its child order items - or their order item ids when the $return_ids arg is true.
	 *
	 * @since  2.1.0
	 *
	 * @param  array     $order_item
	 * @param  WC_Order  $order
	 * @param  boolean   $return_ids
	 * @return mixed
	 */
	public static function get_bundle_type_order_items( $order_item, $order = false, $return_ids = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::get_bundle_type_order_items()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::get_bundle_type_order_items( $order_item, $order, $return_ids );
	}

	/**
	 * True if an order item appears to be a bundle-type container item.
	 *
	 * @since  2.1.0
	 *
	 * @param  array     $order_item
	 * @param  WC_Order  $order
	 * @return boolean
	 */
	public static function is_bundle_type_container_order_item( $order_item, $order = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::is_bundle_type_container_order_item()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::is_bundle_type_container_order_item( $order_item, $order );
	}

	/**
	 * True if an order item is part of a bundle-type product.
	 *
	 * @since  2.1.0
	 *
	 * @param  array     $cart_item
	 * @param  WC_Order  $order
	 * @return boolean
	 */
	public static function is_bundle_type_order_item( $order_item, $order = false ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::is_bundle_type_order_item()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::is_bundle_type_order_item( $order_item, $order );
	}

	/**
	 * Set the active bundle scheme on a bundled item.
	 *
	 * @param  WC_Bundled_Item    $bundled_item
	 * @param  WC_Product_Bundle  $bundle
	 */
	public static function set_bundled_item_scheme( $bundled_item, $bundle ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::set_bundled_item_scheme()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::set_bundled_item_scheme( $bundled_item, $bundle );
	}

	/**
	 * Add bundles to subscriptions using 'WC_PB_Order::add_bundle_to_order'.
	 *
	 * @since  2.1.0
	 *
	 * @param  WC_Subscription  $subscription
	 * @param  array            $cart_item
	 * @param  WC_Cart          $recurring_cart
	 */
	public static function add_bundle_to_order( $subscription, $cart_item, $recurring_cart ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::add_bundle_to_order()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::add_bundle_to_order( $subscription, $cart_item, $recurring_cart );
	}

	/**
	 * Add composites to subscriptions using 'WC_CP_Order::add_composite_to_order'.
	 *
	 * @since  2.1.0
	 *
	 * @param  WC_Subscription  $subscription
	 * @param  array            $cart_item
	 * @param  WC_Cart          $recurring_cart
	 */
	public static function add_composite_to_order( $subscription, $cart_item, $recurring_cart ) {
		_deprecated_function( __METHOD__ . '()', '2.3.0', 'WCS_ATT_Integration_PB_CP::add_composite_to_order()' );
		if ( ! class_exists( 'WCS_ATT_Integration_PB_CP' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/integrations/class-wcs-att-integration-pb-cp.php' );
		}
		return WCS_ATT_Integration_PB_CP::add_composite_to_order( $subscription, $cart_item, $recurring_cart );
	}

	/**
	 * Checks if the passed cart item is a supported bundle type child. Returns the container item key name if yes, or false if not.
	 *
	 * @param  array  $cart_item
	 * @return boolean|string
	 */
	public static function has_bundle_type_container( $cart_item ) {
		_deprecated_function( __METHOD__ . '()', '2.1.0', 'WCS_ATT_Integrations::get_bundle_type_cart_item_container()' );
		return self::get_bundle_type_cart_item_container( $cart_item, false, true );
	}

	/**
	 * Checks if the passed cart item is a supported bundle type container. Returns the child item key name if yes, or false if not.
	 *
	 * @param  array  $cart_item
	 * @return boolean|string
	 */
	public static function has_bundle_type_children( $cart_item ) {
		_deprecated_function( __METHOD__ . '()', '2.1.0', 'WCS_ATT_Integrations::get_bundle_type_cart_items()' );
		return self::get_bundle_type_cart_items( $cart_item, false, true );
	}
}

add_action( 'plugins_loaded', array( 'WCS_ATT_Integrations', 'init' ), 20 );
