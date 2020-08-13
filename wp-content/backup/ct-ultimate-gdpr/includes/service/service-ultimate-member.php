<?php

/**
 * Class CT_Ultimate_GDPR_Service_Ultimate_Member
 */
class CT_Ultimate_GDPR_Service_Ultimate_Member extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_ultimate-member/ultimate-member.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_ultimate-member/ultimate-member.php', '__return_true' );
		add_action( 'um_add_error_on_form_submit_validation', array( $this, 'validate' ) );
	}

	/**
	 * @return $this
	 */
	public function collect() {

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Ultimate Member' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'is_ultimatemember' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {


		add_settings_section(
			'ct-ultimate-gdpr-services-ultimate-member_accordion-ultimate-member', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_header", // ID
			esc_html( $this->get_name() ), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ultimate-member_accordion-ultimate-member'// Section
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ultimate-member_accordion-ultimate-member'// Section
		);

		add_settings_field(
			'services_ultimate_member_consent_field', // ID
			esc_html__( "Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_services_ultimate_member_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ultimate-member_accordion-ultimate-member' // Section
		);

	}

	public function render_field_services_ultimate_member_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 * @return mixed
	 */
	public function front_action() {

		add_action( 'um_after_register_fields', array( $this, 'add_form_fields' ), 990 );

	}

	public function validate( $array ) {

		$inject = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {

			if ( ! ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-consent-field', $_POST ) ) {

				UM()->form()->add_error(
					$this->get_id(),
					esc_html__( 'Consent is required', 'ct-ultimate-gdpr' )
				);

			} else {

				$this->log_user_consent();

			}

		}

	}

	public function add_form_fields() {

		$inject = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		// option set not to inject a checkbox
		if ( ! $inject ) {
			return;
		}

		echo ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-ultimate-member-consent-field', false ), false );

		if ( ct_ultimate_gdpr_get_value( $this->get_id(), UM()->form()->errors) ) {

			echo '<div class="um-field-error"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>';
			echo UM()->form()->errors[ $this->get_id() ];
			echo '</div><br>';

		}

	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return sprintf( esc_html__( '%s gathers data entered by users in forms', 'ct-ultimate-gdpr' ), 'Ultimate Member' );
	}
}