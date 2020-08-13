<?php

/**
 * Class CT_Ultimate_GDPR_Service_Gravity_Forms
 */
class CT_Ultimate_GDPR_Service_Gravity_Forms extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'gform_validation', array( $this, 'gform_validation_filter' ), 100 );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_gravity-forms/gravityforms.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_gravity-forms/gravityforms.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_gravityforms/gravityforms.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_gravityforms/gravityforms.php', '__return_true' );

	}

	/**
	 * @return $this
	 */
	public function collect() {

		$search_criteria = array();
		$search_criteria['field_filters'][] = array( 'value' => $this->user->get_email() );
		$entries = class_exists( 'GFAPI' ) ? GFAPI::get_entries( 0, $search_criteria ) : array();

		if ( ! is_array( $entries ) ) {
			$entries = array();
		}

		$this->set_collected( $entries );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Gravity Forms' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'gf_apply_filters' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true && $this->is_active();
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

		$this->collect();

		/** @var array $entry */
		foreach ( $this->collected as $entry ) {
			class_exists( 'GFAPI' ) && GFAPI::delete_entry( $entry['id'] );
		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-gravityforms_accordion-12', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-gravityforms_accordion-12' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-gravityforms_accordion-12' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[Gravity Forms] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-gravityforms_accordion-12' // Section
		);

		add_settings_field(
			'services_gravity_forms_consent_field', // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_gravity_forms_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-gravityforms_accordion-12' // Section
		);

		add_settings_field(
			'services_gravity_forms_consent_field_position_first', // ID
			esc_html__( '[Gravity Forms] Inject consent checkbox as the first field instead of the last', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_gravity_forms_consent_field_position_first' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-gravityforms_accordion-12' // Section
		);

		add_settings_field(
			'breach_services_gravity_forms',
			esc_html__( 'Gravity Forms', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2'
		);

	}

	/**
	 *
	 */
	public function render_field_breach_services(  ) {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array(), $this->front_controller->find_controller('breach')->get_id() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	/**
	 * @param array $recipients
	 *
	 * @return array
	 */
	public function breach_recipients_filter( $recipients ) {

		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}

		$entries = class_exists( 'GFAPI' ) ? GFAPI::get_entries( 0 ) : array();

		if ( ! is_array( $entries ) ) {
			$entries = array();
		}

		foreach ( $entries as $form ) {

			foreach ( $form as $form_item_value ) {

				if ( is_email( $form_item_value ) ) {

					$recipients[] = $form_item_value;

				}

			}

		}

		return $recipients;

	}

	/**
	 *
	 */
	public function render_field_services_gravity_forms_consent_field() {

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
	public function render_field_services_gravity_forms_consent_field_position_first() {

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
		add_filter( 'gform_pre_render', array( $this, 'gform_pre_render_filter' ), 100 );
	}

	/**
	 * @return int
	 */
	private function get_field_id() {
		return 2355;
	}

	/**
	 * @param $original_form
	 *
	 * @return mixed
	 */
	public function gform_pre_render_filter( $original_form ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_gravity_forms_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$position_first = $this->get_admin_controller()->get_option_value( 'services_gravity_forms_consent_field_position_first', false, $this->front_controller->find_controller('services')->get_id() );
		$form = $original_form;

		if ( $inject && isset( $form['fields']) ) {

			$field_options = array(
				'id'          => $this->get_field_id(),
				'type'        => 'checkbox',
				'description' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-gravity-forms-consent-field-description', false ), false ),
				'adminLabel'  => '',
				'size'        => 'medium',
				'isRequired'  => false,
				'visibility'  => 'visible',
				'choices'     => array(
					array(
						'text'       => $this->render_template( ct_ultimate_gdpr_locate_template( 'service/service-gravity-forms-consent-field-label', false ), false ),
						'value'      => esc_html__( 'Yes', 'ct-ultimate-gdpr' ),
						'isSelected' => false,
						'price'      => '',
					)
				),
			);

			if(!isset( $_POST["input_{$this->get_field_id()}_1"] ) && sizeof( $_POST ) > 0) {
				$field_options['failed_validation'] = true;
				$field_options['validation_message'] = $this->render_template( ct_ultimate_gdpr_locate_template( 'service/service-gravity-forms-consent-field-error-message', false ), false );
			}

			$field = GF_Fields::create($field_options);

			if ( $position_first ) {
				array_unshift( $form['fields'], $field );
			} else {
				array_push( $form['fields'], $field );
			}


		}

		return apply_filters( 'ct_ultimate_gdpr_service_gravity_forms_form_content', $form, $original_form );
	}

	private function render_template( $path ) {
		ob_start();
		include $path;
		$rendered = ob_get_clean();
		return $rendered;
	}

	/**
	 * @param $validation_result
	 *
	 * @return mixed
	 */
	public function gform_validation_filter( $validation_result ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_gravity_forms_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$consent_given = ! empty( $_POST["input_{$this->get_field_id()}_1"] );

		if ( ! $consent_given && $inject ) {
			$validation_result['is_valid'] = false;
		}

		if ( $consent_given ) {
			$this->log_user_consent();
		}

		return apply_filters( 'ct_ultimate_gdpr_service_gravity_forms_form_validation', $validation_result );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Gravity forms gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}

}
