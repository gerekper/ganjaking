<?php

/**
 * Class CT_Ultimate_GDPR_Service_Mailpoet
 */
class CT_Ultimate_GDPR_Service_Mailpoet extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {

		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_mailpoet/mailpoet.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_mailpoet/mailpoet.php', '__return_true' );

	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}mailpoet_subscribers
				WHERE email = %s
				OR wp_user_id = %d		
				",
				$this->user->get_email(),
				$this->user->get_id()
			),
			ARRAY_A
		);

		$this->set_collected( $results );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Mailpoet' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'mailpoet_deactivate_plugin' );
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

		$result = $wpdb->query(
			$wpdb->prepare( "
				DELETE FROM {$wpdb->prefix}mailpoet_subscribers
				WHERE email = %s
				OR wp_user_id = %d		
				",
				$this->user->get_email(),
				$this->user->get_id()
			),
			ARRAY_A
		);

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-mailpoet_accordion-mailpoet', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailpoet_accordion-mailpoet' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-mailpoet_accordion-mailpoet' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[Mailpoet] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailpoet_accordion-mailpoet' // Section
		);

		add_settings_field(
			'breach_services_mailpoet',
			esc_html__( 'Mailpoet', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2'
		);

	}

	/**
	 * @return bool
	 */
	public function is_subscribeable() {
		return true;
	}

	/**
	 *
	 */
	public function unsubscribe() {

		if (
			class_exists( 'MailPoet\Models\Subscriber' ) &&
			class_exists( 'MailPoet\Models\SubscriberSegment' )
		) {
			MailPoet\Models\SubscriberSegment::unsubscribeFromSegments(
				MailPoet\Models\Subscriber::findOne( $this->user->get_email() )
			);
		}

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

		$results = $wpdb->get_results(
			"
				SELECT email
				FROM {$wpdb->prefix}mailpoet_subscribers
				",
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return $recipients;
		}

		foreach ( $results as $result ) {

			if ( is_email( $result['email'] ) ) {

				$recipients[] = $result['email'];

			}

		}

		return $recipients;

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
		return esc_html__( 'Mailpoet gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}

}
