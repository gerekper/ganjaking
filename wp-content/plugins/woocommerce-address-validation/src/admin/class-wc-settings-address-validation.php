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
 * Address Validation Settings class
 *
 * @since 2.0.0
 */
class WC_Settings_Address_Validation extends \WC_Settings_Page {


	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->id    = 'address_validation';
		$this->label = __( 'Address Validation', 'woocommerce-address-validation' );

		parent::__construct();
	}


	/**
	 * Get sections
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'' => __( 'General Options', 'woocommerce-address-validation' )
		);

		// Load providers
		$providers = wc_address_validation()->get_handler_instance()->get_providers();

		foreach ( $providers as $provider ) {
			$sections[ strtolower( $provider->id ) ] = esc_html( $provider->get_title() );
		}

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}


	/**
	 * Returns settings array for use by output/save functions
	 *
	 * @since  2.0.0
	 * @return array settings
	 */
	public function get_settings() {

		$provider_options = array();

		foreach ( wc_address_validation()->get_handler_instance()->get_providers() as $provider ) {
			$provider_options[ get_class( $provider ) ] = $provider->get_title();
		}

		$settings = array(

			array(
				'name' => __( 'General Options', 'woocommerce-address-validation' ),
				'type' => 'title',
			),

			array(
				'name'     => __( 'Provider', 'woocommerce-address-validation' ),
				'desc'     => sprintf( '%s <br> %s', __( 'Select your provider, then save.', 'woocommerce-address-validation' ), __( 'Complete setup by navigating to the provider’s tab next to General Options and entering your account info.', 'woocommerce-address-validation') ),
				'id'       => 'wc_address_validation_active_provider',
				'default'  => 'addressy',
				'type'     => 'select',
				'options'  => $provider_options,
				'class'    => 'wc-enhanced-select',
			),

			array(
				'name'     => __( 'Debug Mode', 'woocommerce-address-validation' ),
				'desc'     => __( 'Enable this to output debug messages on the checkout page to help with troubleshooting.', 'woocommerce-address-validation' ),
				'id'       => 'wc_address_validation_debug_mode',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'name'     => __( 'Geocode Addresses?', 'woocommerce-address-validation' ),
				'desc'     => __( 'Enable this to save customer\'s latitude and longitude to their order.', 'woocommerce-address-validation' ),
				'id'       => 'wc_address_validation_geocode_addresses',
				'default'  => 'no',
				'type'     => 'checkbox',
				'class'    => 'feature-field supports-geocoding',
			),

			array(
				'name'     => __( 'Classify Addresses?', 'woocommerce-address-validation' ),
				'desc'     => __( 'Enable this to save a customer\'s address classification (e.g. Residential or Commercial) to their order.', 'woocommerce-address-validation' ),
				'id'       => 'wc_address_validation_classify_addresses',
				'default'  => 'no',
				'type'     => 'checkbox',
				'class'    => 'feature-field supports-address_classification',
			),

			array(
				'name'     => __( 'Force Customer to look-up address via Postcode?', 'woocommerce-address-validation' ),
				'desc'     => __( 'Enable this to force customers to look-up their address via Postcode before displaying the address fields. ', 'woocommerce-address-validation' ),
				'id'       => 'wc_address_validation_force_postcode_lookup',
				'default'  => 'no',
				'type'     => 'checkbox',
				'class'    => 'feature-field supports-postcode_lookup',
			),

			array( 'type' => 'sectionend' ),

		);


		/**
		 * Filter Address Validation Settings
		 *
		 * @since 2.0.0
		 * @param array $settings Array of the plugin settings
		 */
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}


	/**
	 * Output the settings
	 *
	 * @since 2.0.0
	 */
	public function output() {
		global $current_section;

		// Load providers.
		$providers = wc_address_validation()->get_handler_instance()->get_providers();

		if ( $current_section ) {
			foreach ( $providers as $provider ) {
				if ( in_array( $current_section, array( $provider->id, sanitize_title( get_class( $provider ) ) ) ) ) {
					$provider->admin_options();
					break;
				}
			}
		} else {
			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}


	/**
	 * Save settings
	 *
	 * @since 2.0.0
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {

			WC_Admin_Settings::save_fields( $this->get_settings() );

		} else {

			$providers = wc_address_validation()->get_handler_instance()->get_providers();

			foreach ( $providers as $provider ) {
				if ( in_array( $current_section, array( $provider->id, sanitize_title( get_class( $provider ) ) ) ) ) {

					do_action( 'wc_address_validation_update_provider_options_' . $provider->id );
				}
			}

		}
	}


}
