<?php

/**
 * Class CT_Ultimate_GDPR_Service_Mailster
 */
class CT_Ultimate_GDPR_Service_Mailster extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {

		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_mailster/mailster.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_mailster/mailster.php', '__return_true' );
		add_filter( 'mailster_submit', array( $this, 'validate_consent' ), 10, 2 );

		// this happens also in ajax
		add_filter( 'mailster_form_fields', array( $this, 'add_form_fields' ), 10, 3 );
		add_filter( 'mailster_unsubscribe_form', array( $this, 'add_form_fields_unsubscribe' ), 10, 2 );

	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT s.*
				FROM {$wpdb->prefix}mailster_subscribers as s
				WHERE s.email = %s AND s.wp_id = %d		
				",
				$this->user->get_email(),
				$this->user->get_id()
			),
			ARRAY_A
		);

		foreach ( $results as $result ) {

			$meta_results = $wpdb->get_results(
				$wpdb->prepare( "
				SELECT sm.meta_key as sm_key, sm.meta_value as sm_value 
				FROM {$wpdb->prefix}mailster_subscriber_meta as sm
				WHERE sm.subscriber_id = %d		
				",
					$result['ID']
				),
				ARRAY_A
			);

			$results[] = $meta_results;

			$meta_results = $wpdb->get_results(
				$wpdb->prepare( "
				SELECT sf.meta_key as sf_key, sf.meta_value as sf_value 
				FROM {$wpdb->prefix}mailster_subscriber_fields as sf
				WHERE sf.subscriber_id = %d		
				",
					$result['ID']
				),
				ARRAY_A
			);

			$results[] = $meta_results;

		}

		$this->set_collected( $results );

		return $this;
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
	public function unsubscribe(  ) {

		global $mailster;
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT s.ID
				FROM {$wpdb->prefix}mailster_subscribers as s
				WHERE s.email = %s OR s.wp_id = %d		
				",
				$this->user->get_email(),
				$this->user->get_id()
			),
			ARRAY_A
		);

		foreach ( $results as $result ) {
			$mailster && $mailster->subscribers()->remove( ct_ultimate_gdpr_get_value( 'ID', $result ) );
		}

	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Mailster' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'mailster' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

		global $wpdb;

		$result = $wpdb->query(
			$wpdb->prepare( "
				DELETE s, sf, sm 
				FROM {$wpdb->prefix}mailster_subscribers as s
				LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta as sm
					ON s.ID = sm.subscriber_id
				LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields as sf
					ON s.ID = sf.subscriber_id
				WHERE s.email = %s OR s.ID = %d		
				",
				$this->user->get_email(),
				$this->user->get_id()
			)
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( 'There were problems forgetting data for user: %s', 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-mailster_accordion-14', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);


		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailster_accordion-14' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-mailster_accordion-14'// Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailster_accordion-14'// Section
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to subscribe forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailster_accordion-14' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field_position_first", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox as the first field instead of the last", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field_position_first" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailster_accordion-14' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field_unsubscribe", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to unsubscribe forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field_unsubscribe" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-mailster_accordion-14' // Section
		);


		add_settings_field(
			'breach_services_mailster',
			esc_html__( 'Mailster', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2'
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
	public function render_field_services_mailster_consent_field() {

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
	public function render_field_services_mailster_consent_field_unsubscribe() {

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
	public function render_field_services_mailster_consent_field_position_first() {

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
	}

	/**
	 * Legacy form
	 *
	 * @param $html
	 * @param $campaign_id
	 *
	 * @return mixed
	 */
	public function add_form_fields_unsubscribe( $html, $campaign_id ) {

		$original = $html;

		$inject = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field_unsubscribe", false, $this->front_controller->find_controller('services')->get_id() );

		// option set not to inject a checkbox
		if ( ! $inject ) {
			return $html;
		}

		$content = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-mailster-consent-field', false ) ) . '</form>';
		$html    = str_replace( '</form>', $content, $html );

		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_form_unsubscribe_content", $html, $inject, $original );

	}

	/**
	 *
	 * @param $fields
	 * @param $form_id
	 * @param array $form
	 *
	 * @return mixed
	 */
	public function add_form_fields( $fields, $form_id, $form ) {

		// unsubscribe form
		$inject_unsubscribe = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field_unsubscribe", false, $this->front_controller->find_controller('services')->get_id() );
		if ( 'unsubscribe' == get_query_var( '_mailster_page' ) && ! $inject_unsubscribe ) {
			return $fields;
		}

		// other forms
		$position_first = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field_position_first", false, $this->front_controller->find_controller('services')->get_id() );
		$inject         = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		// option set not to inject a checkbox
		if ( ! $inject ) {
			return $fields;
		}

		if ( $position_first ) {

			$fields = array_merge( array( 'ct_consent' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-mailster-consent-field', false ) ) ), $fields );

		} else {

			$position = count( $fields );

			foreach ( $fields as $key => $field ) {

				if ( stripos( $key, 'submit' ) !== false ) {
					$position = array_search( $key, array_keys( $fields ) );
					break;
				}

			}

			$head   = array_splice( $fields, 0, $position );
			$fields = array_merge( $head, array( 'ct_consent' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-mailster-consent-field', false ) ) ), $fields );
		}

		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_form_content", $fields, $inject, $position_first, $form_id, $form );
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

		return array_merge( $recipients, $this->get_all_users_emails() );
	}


	/**
	 * @return array
	 */
	private function get_all_users_emails() {

		global $wpdb;

		$results = $wpdb->get_results( "
				SELECT s.email
				FROM {$wpdb->prefix}mailster_subscribers as s		
				",
			ARRAY_A
		);

		$emails = array();

		foreach ( $results as $result ) {
			$emails[] = $result['email'];
		}

		return $emails;

	}


	/**
	 * @param array $form_data
	 *
	 * @return array
	 */
	public function validate_consent( $form_data ) {

		$inject = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject && empty( $_POST['ct-ultimate-gdpr-consent-field'] ) ) {

			if ( empty( $form_data['errors'] ) ) {
				$form_data['errors'] = array();
			}

			$form_data['errors']['ct_consent'] = esc_html__( 'Consent is missing', 'ct-ultimate-gdpr' );

		} elseif ( $inject ) {
			$this->log_user_consent();
		}

		return $form_data;
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Mailster collects signed in user data', 'ct-ultimate-gdpr' );
	}
}