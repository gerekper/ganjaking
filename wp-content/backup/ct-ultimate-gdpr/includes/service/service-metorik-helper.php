<?php

/**
 * Class CT_Ultimate_GDPR_Service_Metorik_Helper
 */
class CT_Ultimate_GDPR_Service_Metorik_Helper extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * Run on init
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_metorik-helper/metorik-helper.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_metorik-helper/metorik-helper.php', '__return_true' );
	}

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	public function collect() {
		return $this;
	}

	/**
	 * Get service name
	 *
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Metorik Helper' );
	}

	/**
	 * Is it active, eg. whether related plugin is enabled. Used mainly by Data Access controller
	 *
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'metorik_check_headers_agent' );
	}

	/**
	 * Can data be forgotten by this service?
	 *
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * Forget specific user data
	 *
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
	}

	/**
	 * Add admin option fields
	 *
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-metorik-helper_accordion-metorik-helper', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_header", // ID
			esc_html( $this->get_name() ), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-metorik-helper_accordion-metorik-helper' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to subscribe forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-metorik-helper_accordion-metorik-helper' // Section
		);

	}

	/**
	 * Do optional action on front
	 *
	 * @return mixed
	 */
	public function front_action() {

		if ( $this->get_admin_controller()->get_option_value( 'services_metorik_helper_consent_field', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			// add consent checkbox adding script
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		}

	}

	/**
	 * Add consent checkbox adding script
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'ct-ultimate-gdpr-service-metorik-helper', ct_ultimate_gdpr_url( '/assets/js/service-metorik-helper.js' ) );
		wp_localize_script( 'ct-ultimate-gdpr-service-metorik-helper', 'ct_ultimate_gdpr_service_metorik_helper', array(
			'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-metorik-helper-consent-field', false ), false )
		) );
	}

	/**
	 *
	 */
	public function render_field_services_metorik_helper_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

}