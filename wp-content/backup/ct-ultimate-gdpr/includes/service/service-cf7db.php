<?php

/**
 * Class CT_Ultimate_GDPR_Service_CF7DB
 */
class CT_Ultimate_GDPR_Service_CF7DB extends CT_Ultimate_GDPR_Service_Abstract {
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_contact-form-cfdb7/contact-form-cfdb-7.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_contact-form-cfdb7/contact-form-cfdb-7.php', '__return_true' );
	}

	/**
	 *
	 */
	public function collect() {
		global $wpdb;

		$query_results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}db7_forms
				WHERE `form_value` LIKE %s
				",
				'%' . $this->user->get_email() . '%'
			),
			ARRAY_A
		);

		$results = array();
		foreach ( $query_results as $result ) {
			$result['form_value'] = unserialize($result['form_value']);
			foreach ( $result['form_value'] as $value ) {
				if($value == $this->user->get_email()){
					$results[] = $result;
				}
			}
		}

		$this->set_collected( $results );

		return $this;
	}

	/**
	 *
	 */
	public function forget() {
		$this->collect();

		$delete = array();
		foreach ( $this->collected as $collected) {
			$form_values = unserialize($collected['form_value']);
			foreach ( $form_values as $value ) {
				if($value == $this->user->get_email()){
					$delete[] = $collected['form_id'];
				}
			}
		}

		global $wpdb;

		$delete = implode(', ', $delete);
		$wpdb->get_results( "
				DELETE
				FROM {$wpdb->prefix}db7_forms
				WHERE `form_id` IN ( $delete )
			",
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
			SELECT *
			FROM {$wpdb->prefix}db7_forms
			",
			ARRAY_A
		);

		foreach ( $results as $result ) {
			$form_values = unserialize( $result['form_value'] );
			foreach ( $form_values as $value ) {
				if ( preg_match("^[A-Za-z0-9._%\-+!#$&/=?\^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$^", $value ) ) {
					$recipients[ $value ] = $value;
				}
			}
		}

		return $recipients;
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
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Contact Form CFDB7' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'cfdb7_create_table' );
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
	public function front_action() {
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Contact Form CFDB7 gathers data entered by users in Contact Form 7', 'ct-ultimate-gdpr' );
	}

}