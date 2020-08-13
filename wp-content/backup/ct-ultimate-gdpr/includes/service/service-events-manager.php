<?php

/**
 * Class CT_Ultimate_GDPR_Service_Events_Manager
 */
class CT_Ultimate_GDPR_Service_Events_Manager extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'em_ticket_booking_validate', array( $this, 'event_booking_validate_filter' ), 100, 2 );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_events-manager/events-manager.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_events-manager/events-manager.php', '__return_true' );

	}

	/**
	 * @return $this
	 */
	public function collect() {

		/** @var EM_Bookings $bookings */
		$bookings = EM_Bookings::get( array(
			'person' => $this->user->get_current_user_id(),
			'status' => range( 0, 5 ),
		) );

		$collected = array();
		foreach ( $bookings as $booking ) {
			$collected[] = $booking->to_array();
		}

		$this->set_collected( $collected );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", "Events Manager" );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'em_events' );
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

		/** @var EM_Bookings $bookings */
		$bookings = EM_Bookings::get( array(
			'person' => $this->user->get_current_user_id(),
			'status' => array( 0, 1, 2, 3 )
		) );

		/** @var EM_Bookings $booking */
		foreach ( $bookings as $booking ) {
			$booking->delete();
		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-eventmanager_accordion-5', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);


		add_settings_field(
			"services_{$this->get_id()}_header", // ID
			esc_html( $this->get_name() ), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-eventmanager_accordion-5' // Section
		);

		/*add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[Events Manager] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-eventmanager_accordion-5' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-eventmanager_accordion-5' // Section
        );

		add_settings_field(
			'services_events_manager_consent_field', // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_events_manager_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-eventmanager_accordion-5' // Section
		);

	}

	/**
	 *
	 */
	public function render_field_services_events_manager_consent_field() {

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
		add_action( 'em_booking_form_footer', array( $this, 'booking_form_action' ), 100 );
	}

	/**
	 * @param $event
	 */
	public function booking_form_action( $event ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_events_manager_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject && apply_filters( 'ct_ultimate_gdpr_service_events_manager_render_consent_field', true ) ) {
			ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-events-manager-consent-field', false ), true );

		}

	}

	/**
	 * @param $original_validation
	 * @param $booking
	 *
	 * @return mixed|void
	 */
	public function event_booking_validate_filter( $original_validation, $booking ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_mailchimp_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$validation = $original_validation;

		if ( $inject && empty( $_POST["ct-ultimate-gdpr-consent-field"] ) ) {
			$booking->errors[] = sprintf( esc_html__( "%s is required.", 'ct-ultimate-gdpr' ),
				esc_html__( 'Consent', 'ct-ultimate-gdpr' )
			);

			$validation = false;
		}

		$validation && $this->log_user_consent();

		return apply_filters( 'ct_ultimate_gdpr_service_events_manager_form_validation', $validation, $original_validation, $booking );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Events Manager gathers data entered by users in booking order forms', 'ct-ultimate-gdpr' );
	}

}