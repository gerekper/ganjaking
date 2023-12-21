<?php
/**
 * Settings: General.
 *
 * @since 1.8.0
 */

namespace KoiLab\WC_Currency_Converter\Admin\Settings;

defined( 'ABSPATH' ) || exit;

use Exception;
use KoiLab\WC_Currency_Converter\Internal\Admin\Settings\Abstracts\Settings_API;

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
		$form_fields = array(
			'app_id' => array(
				'type'              => 'password',
				'title'             => __( 'App ID', 'woocommerce-currency-converter-widget' ),
				'description'       => sprintf(
					/* translators: %s: Open Exchange signup link */
					__( 'Enter your Open Exchange Rates App ID or create a new one <a href="%s" target="_blank">here</a>.', 'woocommerce-currency-converter-widget' ),
					'https://openexchangerates.org/signup'
				),
				'custom_attributes' => array(
					'pattern' => '[0-9A-Fa-f]+',
				),
			),
		);

		if ( get_option( $this->get_option_key( 'app_id' ) ) ) {
			$form_fields['rates_refresh_period'] = array(
				'type'              => 'number',
				'title'             => __( 'Rates refresh period', 'woocommerce-currency-converter-widget' ),
				'description'       => __( 'Set the rates refresh period in hours.', 'woocommerce-currency-converter-widget' ),
				'css'               => 'width:65px;',
				'default'           => 12,
				'custom_attributes' => array(
					'min' => '1',
					'max' => '24',
				),
			);
		}

		$this->form_fields = $form_fields;
	}

	/**
	 * Validates the App ID.
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception When the field value is invalid.
	 *
	 * @param  string $key   Field key.
	 * @param  string $value Posted Value.
	 * @return string
	 */
	public function validate_app_id_field( $key, $value ) {
		$value = sanitize_text_field( $value );

		if ( $value ) {
			$provider = new \KoiLab\WC_Currency_Converter\Exchange\Providers\Open_Exchange_Provider( $value );

			if ( ! $provider->validate_credentials() ) {
				throw new Exception( esc_html__( 'Invalid App ID.', 'woocommerce-currency-converter-widget' ) );
			}
		}

		return $value;
	}
}

class_alias( General::class, 'Themesquad\WC_Currency_Converter\Admin\Settings\General' );
