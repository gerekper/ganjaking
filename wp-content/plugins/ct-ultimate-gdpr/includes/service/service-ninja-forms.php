<?php

/**
 * Class CT_Ultimate_GDPR_Service_Ninja_Forms
 */
class CT_Ultimate_GDPR_Service_Ninja_Forms extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_ninja-forms/ninja-forms.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_ninja-forms/ninja-forms.php', '__return_true' );
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Ninja-Forms' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'Ninja_Forms' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
	}

	/**
	 * @return void
	 */
	public function forget() {
		$this->collect();
		/** @var object wp_post $entry */
		foreach ( $this->collected as $entry ) {
			wp_delete_post( $entry->ID );
		}
	}

	/**
	 *
	 */
	public function add_option_fields() {

		add_settings_section(
			"ct-ultimate-gdpr-services-ninjaforms_accordion-ninjaforms", // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-ninjaforms_accordion-ninjaforms" // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-ninjaforms_accordion-ninjaforms"
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-ninjaforms_accordion-ninjaforms"
		);

	}

	/**
	 *
	 */
	public function render_field_services_ninja_forms_consent_field() {
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
	public function front_action() {
		$inject = $this->get_admin_controller()->get_option_value( "services_ninja_forms_consent_field", false, $this->front_controller->find_controller('services')->get_id() );
		if ( $inject ) {
			add_filter( 'ninja_forms_display_before_field_type_submit', array(
				$this,
				'service_ninja_forms_display_before_field_type_submit'
			), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ), 1 );
		}
	}

	/**
	 *
	 */
	public function enqueue_static() {
		wp_enqueue_script( 'ct_ultimate_gdpr_service_ninja_forms', ct_ultimate_gdpr_url( 'assets/js/service-ninja-forms.js' ), array( 'jquery' ) );
	}

	/**
	 * @return string
	 */
	public function service_ninja_forms_display_before_field_type_submit() {
		$html = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-ninja-forms-consent-field', false ), false );

		return $html;
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Ninja Forms gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	public function collect() {
		$collected = array();
		$email     = $this->user->get_email();
		$args      = array(
			'post_status' => 'any',
			'post_type'   => array( 'nf_sub' ),
			'meta_value'  => $email,
			'numberposts' => - 1,
		);
		$ids       = get_posts( $args );

		if ( ! is_wp_error( $ids ) ) {
			$collected = $ids;
		}

		$this->set_collected( $collected );

		return $this;
	}

}