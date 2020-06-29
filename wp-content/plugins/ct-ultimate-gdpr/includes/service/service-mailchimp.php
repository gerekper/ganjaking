<?php

/**
 * Class CT_Ultimate_GDPR_Service_Mailchimp
 */
class CT_Ultimate_GDPR_Service_Mailchimp extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {

		add_filter( 'mc4wp_form_errors', array( $this, 'form_errors_filter' ), 100, 2 );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_mailchimp-for-wp/mailchimp-for-wp.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_mailchimp-for-wp/mailchimp-for-wp.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		$this->set_collected( array() );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", "Mailchimp" );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'mc4wp_get_api_v3' );
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
	 * @return bool
	 */
	public function is_subscribeable() {
		return true;
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {


		add_settings_section(
			'ct-ultimate-gdpr-services-mailchimp_accordion-13', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailchimp_accordion-13' // Section
		);*/

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailchimp_accordion-13' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[Mailchimp] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailchimp_accordion-13' // Section
		);

		add_settings_field(
			'services_mailchimp_consent_field', // ID
			esc_html__( '[Mailchimp] Inject consent checkbox to order fields', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_mailchimp_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailchimp_accordion-13' // Section
		);

		add_settings_field(
			'services_mailchimp_consent_field_position_first', // ID
			esc_html__( '[Mailchimp] Inject consent checkbox as the first field instead of the last', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_mailchimp_consent_field_position_first' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailchimp_accordion-13' // Section
		);

	}

	/**
	 * @throws MC4WP_API_Exception
	 */
	public function unsubscribe() {

		$lists_data = mc4wp_get_api_v3()->get_lists( array(
			'email'  => $this->user->get_email(),
			'fields' => 'lists.id'
		) );

		$list_ids = wp_list_pluck( $lists_data, 'id' );

		foreach ( $list_ids as $list_id ) {

			mc4wp_get_api_v3()->delete_list_member( $list_id, $this->user->get_email() );

		}

	}

	/**
	 *
	 */
	public function render_field_services_mailchimp_consent_field() {

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
	 *
	 */
	public function render_field_services_mailchimp_consent_field_position_first() {

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
		add_filter( 'mc4wp_form_content', array( $this, 'form_content_filter' ), 100, 3 );
	}

	/**
	 * @param $original_content
	 * @param $form
	 * @param $element
	 *
	 * @return mixed
	 */
	public function form_content_filter( $original_content, $form, $element ) {

		$inject         = $this->get_admin_controller()->get_option_value( 'services_mailchimp_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$position_first = $this->get_admin_controller()->get_option_value( 'services_mailchimp_consent_field_position_first', false, $this->front_controller->find_controller('services')->get_id() );

		$content = $original_content;
		if ( $inject ) {

			if ( $position_first ) {
				$content = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-mailchimp-consent-field', false ) ) . $content;
			} else {
				$content .= ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-mailchimp-consent-field', false ) );
			}
		}

		return apply_filters( 'ct_ultimate_gdpr_service_mailchimp_form_content', $content, $original_content, $form, $element );
	}

	/**
	 * @param $errors
	 * @param $form
	 *
	 * @return mixed
	 */
	public function form_errors_filter( $errors, $form ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_mailchimp_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject && empty( $_POST["ct-ultimate-gdpr-consent-field"] ) ) {
			$errors[] = esc_html__( "Consent is mandatory to proceed", 'ct-ultimate-gdpr' );
		}

		if ( $inject && ! empty( $_POST["ct-ultimate-gdpr-consent-field"] ) ) {
			$this->log_user_consent();
		}

		return apply_filters( 'ct_ultimate_gdpr_service_mailchimp_form_errors', $errors, $form );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Mailchimp gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}


}