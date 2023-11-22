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
 * Addressy Provider Class.
 *
 * Extends abstract provider class to provide address verification via Addressy API.
 *
 * TODO Addressy has been acquired by Loqate and this handler will require an update and name changes accordingly; but also other internal references, assets, etc. {FN 2018-08-06}
 *
 * @link https://www.loqate.com/
 *
 * @since 2.0.0
 */
class WC_Address_Validation_Provider_Addressy extends \WC_Address_Validation_Provider {


	/** @var string service key for API */
	public $service_key;

	/** @var string service key for API */
	public $validate_international_addresses;


	/**
	 * Setup id/title/description and declare country / feature support
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->id = 'addressy';

		$this->title = __( 'Loqate', 'woocommerce-address-validation' );

		/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
		$this->description = sprintf( __( 'Loqate offers address verification for both residential and commercial US and can perform lookups for addresses in any country. %1$sSign up for a Loqate account%2$s now to get started. .', 'woocommerce-address-validation'), '<a href="https://account.loqate.com/register/" target="_blank">', '</a>' );

		$this->supports = array(
			'address_validation',
			'address_classification',
		);

		// setup form fields
		$this->init_form_fields();

		// load settings
		$this->init_settings();

		$this->service_key                       = $this->settings['service_key'];
		$this->validate_international_addresses  = isset( $this->settings['validate_international_addresses'] ) ? $this->settings['validate_international_addresses'] : 'no';

		// Save settings
		add_action( 'wc_address_validation_update_provider_options_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	/**
	 * Checks if provider is configured correctly.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$this->is_configured = ! empty( $this->service_key );

		return parent::is_configured();
	}


	/**
	 * Init settings
	 *
	 * @since 2.0.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'service_key'  => array(
				'title'    => __( 'Service Key', 'woocommerce-address-validation' ),
				'type'     => 'text',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => sprintf( __( 'Enter your service key, which can be obtained by following the %1$sLoqate Setup Guide%2$s.', 'woocommerce-address-validation' ), '<a href="' . esc_url( wc_address_validation()->get_documentation_url() ) . '#loqate">', '</a>' ),
				'default'  => '',
			),

			'validate_international_addresses'  => array(
				'title'    => __( 'Validate international addresses', 'woocommerce-address-validation' ),
				'type'     => 'checkbox',
				'label'    => __( 'Enable lookup for customers outside the US (Requires a paid Loqate account)', 'woocommerce-address-validation' ),
				'default'  => 'no',
			),
		);
	}


}
