<?php
/**
 * WooCommerce Address Validation
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Address Validation to newer
 * versions in the future. If you wish to customize WooCommerce Address Validation for your
 * needs please refer to http://docs.woocommerce.com/document/address-validation/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * WooCommerce Address Validation main plugin class.
 *
 * @since 1.0
 */
class WC_Address_Validation extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.8.2';

	/** @var \WC_Address_Validation single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'address_validation';

	/** @var \WC_Address_Validation_Handler instance */
	protected $handler;

	/** @var \WC_Address_Validation_Admin instance */
	protected $admin;

	/** @var bool debug mode status */
	protected $is_debug_mode;

	/**
	 * Sets up the plugin main class.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-address-validation',
			)
		);

		spl_autoload_register( array( $this, 'autoload_providers' ) );

		// postcode lookup AJAX
		add_action( 'wp_ajax_wc_address_validation_lookup_postcode',        [ $this, 'lookup_postcode' ] );
		add_action( 'wp_ajax_nopriv_wc_address_validation_lookup_postcode', [ $this, 'lookup_postcode' ] );
		add_action( 'wc_ajax_wc_address_validation_lookup_postcode',        [ $this, 'lookup_postcode' ] );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 */
	public function init_plugin() {

		$this->includes();
	}


	/**
	 * Auto-loads provider classes.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param string $class class name to load
	 */
	public function autoload_providers( $class ) {

		$class = strtolower( $class );

		if ( 0 === strpos( $class, 'wc_address_validation_provider_' ) ) {

			$path = $this->get_plugin_path() . '/src/providers/';
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {
				require_once( $path . $file );
			}
		}
	}


	/**
	 * Loads plugin classes after WooCommerce has loaded.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function includes() {

		// base validation provider abstract
		require_once( $this->get_plugin_path() . '/src/abstract-wc-address-validation-provider.php' );

		// load providers
		$this->handler = $this->load_class( '/src/class-wc-address-validation-handler.php', 'WC_Address_Validation_Handler' );

		if ( is_admin() && ! wp_doing_ajax() ) {

			// admin handler
			$this->admin = $this->load_class( '/src/admin/class-wc-address-validation-admin.php', 'WC_Address_Validation_Admin' );
		}
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 2.4.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\Address_Validation\Lifecycle( $this );
	}


	/**
	 * Return the admin handler instance.
	 *
	 * @since 1.9.0
	 *
	 * @return \WC_Address_Validation_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Return the provider handler instance.
	 *
	 * @since 1.9.0
	 *
	 * @return \WC_Address_Validation_Handler
	 */
	public function get_handler_instance() {

		return $this->handler;
	}


	/**
	 * Renders a notice for the user to switch to Loqate after upgrade from pre 2.0.0
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		if ( get_option( 'wc_address_validation_encourage_addressy_upgrade_switch' ) ) {

			// add notice to encourage users to switch to Addressy
			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf(
				/* translators: Placeholders: %1$s, %3$s - opening <a> tag, %2$s, %4$s - closing </a> tag */
					__( 'Address Validation has been upgraded! We\'ve added support for Loqate, which can perform address lookups for all countries you sell to. %1$sAdjust your settings%2$s or %3$ssign up for a free account%4$s.', 'woocommerce-address-validation' ),
					'<a href="' . esc_url( $this->get_settings_url() ) . '">', '</a>',
					'<a href="https://www.loqate.com/partners/ADRSY11126">', '</a>'
				),
				'addressy-switch-notice',
				array(
					'always_show_on_settings' => false,
					'notice_class'            => 'updated'
				)
			);
		}
	}


	/**
	 * Handles AJAX postcode lookup calls.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function lookup_postcode() {

		check_ajax_referer( 'wc_address_validation', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$postcode     = isset( $_GET['postcode'] )     ? sanitize_text_field( $_GET['postcode'] )     : '';
		$house_number = isset( $_GET['house_number'] ) ? sanitize_text_field( $_GET['house_number'] ) : '';
		$provider     = $this->get_handler_instance()->get_active_provider();

		/**
		 * Fires before a postcode lookup is issued to the active provider.
		 *
		 * Third party actors can intercept this hook to output alternative results earlier and exit.
		 *
		 * @since 2.7.0
		 *
		 * @param null|WC_Address_Validation_Provider $provider the address validation provider used for lookup
		 * @param string $postcode postcode
		 * @param string $house_number house number (optional)
		 */
		do_action( 'wc_address_validation_before_lookup_postcode', $provider, $postcode, $house_number );

		$results = $provider ? $provider->lookup_postcode( $postcode, $house_number ) : [];

		// add a helper notice to the top of the select box
		array_unshift( $results, [
			'value' => 'none',
			'name'  => __( 'Select your address to populate the form.', 'woocommerce-address-validation' ),
		] );

		echo json_encode( $results );

		exit;
	}


	/**
	 * Returns the main Address Validation instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.5.0
	 *
	 * @return \WC_Address_Validation
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the plugin documentation url.
	 *
	 * @since 1.6.1
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/address-validation/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/postcodeaddress-validation/';
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.1
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Address Validation', 'woocommerce-address-validation' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.1
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page.
	 *
	 * @since 1.1
	 *
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=address_validation' );
	}


	/**
	 * Checks if the plugin's debug mode is enabled.
	 *
	 * @since 2.7.2
	 *
	 * @return bool
	 */
	public function is_debug_mode_enabled() {

		if ( null === $this->is_debug_mode ) {
			$this->is_debug_mode = 'yes' === get_option( 'wc_address_validation_debug_mode' );
		}

		return $this->is_debug_mode;
	}


}


/**
 * Returns the One True Instance of Address Validation.
 *
 * @since 1.5.0
 *
 * @return \WC_Address_Validation
 */
function wc_address_validation() {

	return \WC_Address_Validation::instance();
}
