<?php

/**
 * Class CT_Ultimate_GDPR_Service_ARForms
 */
class CT_Ultimate_GDPR_Service_ARForms extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_arforms/arforms.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_arforms/arforms.php', '__return_true' );

	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT e.id as eid, e.*
				FROM {$wpdb->prefix}arf_entry_values as ev
					INNER JOIN {$wpdb->prefix}arf_entries as e
					ON e.id = ev.entry_id
				WHERE ev.entry_value = %s
				",
				$this->user->get_email()
			),
			ARRAY_A
		);

		foreach ( $results as $key => $result ) {

			$meta_results = $wpdb->get_results(
				$wpdb->prepare( "
				SELECT ev.entry_value
				FROM {$wpdb->prefix}arf_entry_values as ev
				WHERE ev.entry_id = %d		
				",
					$result['eid']
				),
				ARRAY_A
			);

			$results[ $key ][] = $meta_results;

		}

		$this->set_collected( $results );

		return $this;

	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'ARForms' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'arformcontroller' );
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

		global $wpdb;

		$this->collect();

		foreach ( $this->collected as $collected ) {

			$wpdb->delete(
				"{$wpdb->prefix}arf_entries",
				array( 'id' => $collected['eid'] )
			);

			$wpdb->delete(
				"{$wpdb->prefix}arf_entry_values",
				array( 'entry_id' => $collected['eid'] )
			);

		}
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-arforms_accordion-2', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);


		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-arforms_accordion-2' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-arforms_accordion-2' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[ARForms] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-arforms_accordion-2' // Section
		);

		add_settings_field(
			'services_arforms_consent_field', // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_arforms_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-arforms_accordion-2' // Section
		);

		add_settings_field(
			'breach_services_arforms',
			esc_html__( 'ARForms', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2' // Section
		);

	}

	/**
	 *
	 */
	public function render_field_breach_services() {

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

		global $wpdb;

		$results = $wpdb->get_results("
				SELECT entry_value
				FROM {$wpdb->prefix}arf_entry_values
				WHERE entry_value REGEXP '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$'
				",
			ARRAY_A
		);

		foreach ( $results as $result ) {

			if ( ! empty( $result['entry_value'] ) ) {
				$recipients[ $result['entry_value'] ] = $result['entry_value'];
			}

		}

		return $recipients;

	}

	/**
	 *
	 */
	public function render_field_services_arforms_consent_field() {

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
	public function render_field_services_arforms_consent_field_position_first() {

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
		add_filter( 'arfentryform', array( $this, 'add_consent_checkbox' ) );
	}

	public function add_consent_checkbox( $form ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_arforms_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {
			$form .= ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-arforms-consent-field', false ), false );
		}

		return $form;
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'ARForms gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}

}
