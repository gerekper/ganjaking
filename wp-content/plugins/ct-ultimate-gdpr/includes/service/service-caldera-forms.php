<?php

/**
 * Class CT_Ultimate_GDPR_Service_Caldera_Forms
 */
class CT_Ultimate_GDPR_Service_Caldera_Forms extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_caldera-forms/caldera-core.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_caldera-forms/caldera-core.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}cf_form_entry_values
				WHERE entry_id IN (
			           SELECT entry_id
			           FROM   {$wpdb->prefix}cf_form_entry_values
			           WHERE `value` = %s
			       )
				",
				$this->user->get_email()
			),
			ARRAY_A
		);

		$this->set_collected( $results );

		return $this;
	}


	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
		global $wpdb;

		$wpdb->get_results(
			$wpdb->prepare( "
				DELETE *
				FROM {$wpdb->prefix}cf_form_entry_values
				WHERE entry_id IN (
					SELECT entry_id
				    FROM   {$wpdb->prefix}cf_form_entry_values
				    WHERE `value` = %s
					)
				",
					$this->user->get_email()
			),
			ARRAY_A
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
				SELECT `value` as email
				FROM {$wpdb->prefix}cf_form_entry_values
				WHERE `value` REGEXP '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$'
				",
			ARRAY_A
		);

		foreach ( $results as $result ) {

			if ( ! empty( $result['email'] ) ) {
				$recipients[ $result['email'] ] = $result['email'];
			}

		}

		return $recipients;

	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Caldera Forms' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'Caldera_Forms' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		add_settings_section(
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}", // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field", // ID
			sprintf( esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ), $this->get_name() ),
			array( $this, 'render_field_services_caldera_forms_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
		);

		add_settings_field(
			"breach_services_{$this->get_id()}",
			esc_html( $this->get_name() ), // Title
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
	 *
	 */
	public function render_field_services_caldera_forms_consent_field() {

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
		$inject = $this->get_admin_controller()->get_option_value( 'services_caldera_forms_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		if($inject){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ), 1 );
		}
	}

	public function enqueue_static( ) {
		wp_enqueue_script( 'ct_ultimate_gdpr_service_caldera_forms', ct_ultimate_gdpr_url( 'assets/js/service-caldera-forms.js' ), array( 'jquery' ) );
		wp_localize_script( 'ct_ultimate_gdpr_service_caldera_forms', 'ct_ultimate_gdpr_caldera_forms', array(
			'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-caldera-forms-consent-field', false ) ),
		) );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Caldera Forms gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}
}