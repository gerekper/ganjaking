<?php
/**
 * Settings: General.
 *
 * @since 1.8.0
 */

namespace Themesquad\WC_Currency_Converter\Admin\Settings;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Currency_Converter\Internal\Admin\Settings\Abstracts\Settings_API;

/**
 * General settings class.
 */
class General extends Settings_API {

	/**
	 * Gets the form title.
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return _x( 'Open Exchange Rates', 'settings page title', 'woocommerce-currency-converter-widget' );
	}

	/**
	 * Gets the form description.
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function get_form_description() {
		return _x( 'An integration for utilizing Open Exchange Rates to convert the product prices to different currencies.', 'settings page description', 'woocommerce-currency-converter-widget' );
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 1.8.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'app_id' => array(
				'type'        => 'text',
				'title'       => __( 'App ID', 'woocommerce-currency-converter-widget' ),
				'description' => sprintf(
					/* translators: %s: Open Exchange signup link */
					__( 'Enter your Open Exchange Rates App ID or create a new one <a href="%s" target="_blank">here</a>.', 'woocommerce-currency-converter-widget' ),
					'https://openexchangerates.org/signup'
				),
			),
		);
	}

	/**
	 * Processes and saves options.
	 *
	 * @since 1.8.0
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		$saved = parent::process_admin_options();

		delete_transient( 'woocommerce_currency_converter_rates' );

		return $saved;
	}
}
