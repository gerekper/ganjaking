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
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * SmartyStreets Provider Class
 *
 * Extends abstract provider class to provide US address validation via SmartyStreets LiveAddress API
 *
 * @link https://smartystreets.com/kb
 * @since 1.0
 */
class WC_Address_Validation_Provider_SmartyStreets extends \WC_Address_Validation_Provider {


	/** @var string html key for API */
	public $html_key;


	/**
	 * Setup id/title/description and declare country / feature support
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->id = 'smartystreets';

		$this->title = __( 'SmartyStreets', 'woocommerce-address-validation' );

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$this->description = sprintf( __( 'SmartyStreets offers free (up to 250 lookups per month) address verification for both residential and commercial US addresses. %1$sSign up for a free account%2$s now to get started.', 'woocommerce-address-validation'), '<a href="https://smartystreets.com/account/create" target="_blank">', '</a>' );

		$this->countries = array( 'US' );

		$this->supports = array(
			'address_validation',
			'geocoding',
			'address_classification'
		);

		// setup form fields
		$this->init_form_fields();

		// load settings
		$this->init_settings();

		$this->html_key = $this->settings['html_key'];

		$this->plus_four_code = isset( $this->settings['plus_four_code'] ) ? $this->settings['plus_four_code'] : 'yes';

		// Save settings
		add_action( 'wc_address_validation_update_provider_options_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	/**
	 * Checks if provider is configured correctly.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$this->is_configured = ! empty( $this->html_key );

		return parent::is_configured();
	}


	/**
	 * Init settings
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'html_key'  => array(
				'title'       => __( 'HTML Key', 'woocommerce-address-validation' ),
				'type'        => 'text',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => sprintf( __( 'Enter your HTML Key. Login to your %1$sSmartyStreets%2$s account to find this.', 'woocommerce-address-validation' ), '<a target="_blank" href="https://smartystreets.com/account/keys">', '</a>' ),
				'default'     => '',
			),

			'plus_four_code'  => array(
				'title'   => __( 'Include Plus-four code?', 'woocommerce-address-validation' ),
				'label'   => __( 'Enable this to validate addresses including the ZIP+4 code. Disable to use standard 5 digit zip codes.', 'woocommerce-address-validation' ),
				'default' => 'yes',
				'type'    => 'checkbox',
			),

		);
	}


}
