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
 * @copyright   Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * Address Validation Admin class
 *
 * @since 2.0.0
 */
class WC_Address_Validation_Admin {


	/** @var string Address Validation WooCommerce settings page name. */
	protected $wc_settings_page_name;

	/** @var \WC_Settings_Address_Validation Settings page instance. */
	private $settings_page;


	/**
	 * Setup admin class
	 *
	 * @since  1.0
	 */
	public function __construct() {

		// init settings page
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

		// scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_footer',          array( $this, 'localize_scripts' ) );

		add_action( 'init', array( $this, 'set_wc_settings_page_name' ) );

		// add providers information to the WooCommerce system status report page
		add_action( 'woocommerce_system_status_report', array( $this, 'add_system_status_report' ) );
	}


	/**
	 * Sets the settings page name in case "WooCommerce" is translated.
	 *
	 * The constructor is too early to set this value.
	 * We have to do this since WP core will use the sanitized menu title for screen ID, not the slug ಠ_ಠ
	 * which essentially breaks get_current_screen()->id when "WooCommerce" is translated.
	 * @see https://core.trac.wordpress.org/ticket/21454 for details
	 *
	 * TODO: Can be removed when https://core.trac.wordpress.org/ticket/18857 is fixed {BR 2016-12-04}
	 *
	 * @since 2.0.0
	 */
	public function set_wc_settings_page_name() {

		$this->wc_settings_page_name = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id();
	}


	/**
	 * Add Address Validation settings page to WooCommerce settings
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 * @param array $settings
	 * @return array
	 */
	public function add_settings_page( $settings ) {

		$settings[] = $this->get_settings_page();

		return $settings;
	}


	/**
	 * Get the settings page instance.
	 *
	 * @since 2.0.1
	 * @return \WC_Settings_Address_Validation
	 */
	public function get_settings_page() {

		if ( ! $this->settings_page instanceof WC_Settings_Address_Validation ) {

			if ( ! class_exists( 'WC_Settings_Page' ) ) {
				require_once( WC()->plugin_path() . '/includes/admin/class-wc-admin-settings.php' );
				require_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );
			}

			$this->settings_page = wc_address_validation()->load_class( '/src/admin/class-wc-settings-address-validation.php', 'WC_Settings_Address_Validation' );
		}

		return $this->settings_page;
	}


	/**
	 * Enqueue Address Validation scripts
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( $this->wc_settings_page_name === $screen->id ) {

			$current_tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : null;

			if ( 'address_validation' === $current_tab ) {
				wp_enqueue_script( 'wc_address_validation_settings', wc_address_validation()->get_plugin_url() . '/assets/js/admin/wc-address-validation-settings.min.js', array( 'jquery' ), WC_Address_Validation::VERSION, true );
			}
		}

	}


	/**
	 * Localize Address Validation admin scripts
	 *
	 * Localization takes place in `admin_footer` because the `admin_enqueue_scripts`
	 * is too early to get the active provider: WC_Admin_Settings saves the settings
	 * after scripts are enqueued.
	 *
	 * @since 2.0.0
	 */
	public function localize_scripts() {

		$screen = get_current_screen();

		if ( $this->wc_settings_page_name === $screen->id ) {

			$current_tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : null;

			if ( 'address_validation' === $current_tab ) {
				$provider_data = array();

				foreach ( wc_address_validation()->get_handler_instance()->get_providers() as $provider ) {
					$provider_data[ get_class( $provider ) ] = array(
						'id'       => $provider->id,
						'supports' => $provider->get_supported_features(),
					);
				}

				// may be null (no chosen active provider) on new installations
				$active_provider = wc_address_validation()->get_handler_instance()->get_active_provider();

				wp_localize_script( 'wc_address_validation_settings', 'wc_address_validation_settings', array(
					'providers'       => $provider_data,
					'active_provider' => null !== $active_provider ? get_class( $active_provider ) : '',
					'i18n' => array(
						'save_settings_first' => __( 'Please save changes to configure provider settings', 'woocommerce-address-validation' ),
					),
				) );
			}
		}
	}


	/**
	 * Prints providers information HTML in the WooCommerce System Status Report page.
	 *
	 * @internal
	 *
	 * @since 2.3.2
	 */
	public function add_system_status_report() {

		$provider = wc_address_validation()->get_handler_instance()->get_active_provider();

		include( wc_address_validation()->get_plugin_path() . '/src/admin/views/html-admin-status-report.php' );
	}


}
