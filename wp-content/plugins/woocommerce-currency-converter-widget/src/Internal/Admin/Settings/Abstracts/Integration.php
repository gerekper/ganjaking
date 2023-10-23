<?php
/**
 * Abstract Integration class.
 *
 * @since 1.8.0
 */

namespace KoiLab\WC_Currency_Converter\Internal\Admin\Settings\Abstracts;

defined( 'ABSPATH' ) || exit;

use WC_Integration;

/**
 * Integration class.
 */
abstract class Integration extends WC_Integration {

	/**
	 * The settings API instance.
	 *
	 * @since 1.8.0
	 *
	 * @var Settings_API
	 */
	protected $settings_api;

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init_settings_api' ) );
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 1.8.0
	 */
	abstract public function init_settings_api();

	/**
	 * Output the settings screen.
	 *
	 * @since 1.8.0
	 */
	public function admin_options() {
		$this->settings_api->admin_options();
	}

	/**
	 * Processes and saves options.
	 *
	 * @since 1.8.0
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		return $this->settings_api->process_admin_options();
	}

	/**
	 * Generates a Text Input HTML.
	 *
	 * @since 1.8.0
	 *
	 * @param string $key  Field key.
	 * @param array  $data Field data.
	 * @return string
	 */
	public function generate_text_html( $key, $data ) {
		// Checks if the field type is defined in the settings form.
		if ( isset( $data['type'] ) && method_exists( $this->settings_api, "generate_{$data['type']}_html" ) ) {
			return call_user_func( array( $this->settings_api, "generate_{$data['type']}_html" ), $key, $data );
		}

		return parent::generate_text_html( $key, $data );
	}

	/**
	 * Validates a Text Field.
	 *
	 * @since 1.8.0
	 *
	 * @param string $key   Field key.
	 * @param string $value Posted Value.
	 * @return string
	 */
	public function validate_text_field( $key, $value ) {
		// Checks if the key validation is defined in the settings API instance.
		if ( is_callable( array( $this->settings_api, 'validate_' . $key . '_field' ) ) ) {
			return $this->settings_api->{'validate_' . $key . '_field'}( $key, $value );
		}

		$field = $this->settings_api->get_form_field( $key );

		// Checks if the type validation is defined in the settings API instance.
		if ( $field && isset( $field['type'] ) && is_callable( array( $this->settings_api, 'validate_' . $field['type'] . '_field' ) ) ) {
			return $this->settings_api->{'validate_' . $field['type'] . '_field'}( $key, $value );
		}

		return parent::validate_text_field( $key, $value );
	}
}
