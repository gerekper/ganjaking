<?php

/**
 * Class CT_Ultimate_GDPR_Service_Newsletter
 */
class CT_Ultimate_GDPR_Service_Newsletter extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_newsletter/plugin.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_newsletter/plugin.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}newsletter
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
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Newsletter' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'TNP_User' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true && $this->is_active();
	}

	/**
	 * @return bool
	 */
	public function is_subscribeable() {
		return true;
	}

	/**
	 * Unsubscribe user from all newsletters
	 *
	 * @throws Exception
	 * @return void
	 */
	public function unsubscribe() {

		$controller = NewsletterUnsubscription::instance();

		$user = $controller->get_user( $this->user->get_id() );

		if ( $user ) {

			if ( $user->status == TNP_User::STATUS_UNSUBSCRIBED ) {
				return;
			}

			$user = $controller->refresh_user_token( $user );
			$user = $controller->set_user_status( $user, TNP_User::STATUS_UNSUBSCRIBED );

			$controller->add_user_log( $user, 'unsubscribe' );

		}

		do_action( 'newsletter_unsubscribed', $user );

		global $wpdb;

		$email = $this->user->get_email();
		if ( $email ) {
			$wpdb->update( NEWSLETTER_USERS_TABLE, array(
				'unsub_email_id' => 0,
				'unsub_time'     => time()
			), array( 'id' => $user ? $user->id : 0 ) );
		}

		$controller->send_unsubscribed_email( $user );

		NewsletterSubscription::instance()->notify_admin( $user, 'Newsletter unsubscription' );

	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

		global $wpdb;

		$result = $wpdb->query(
			$wpdb->prepare( "
				DELETE FROM {$wpdb->prefix}newsletter
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
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion", // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion" // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion"
		);

		add_settings_field(
			"services_newsletter_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_newsletter_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion"
		);

		add_settings_field(
			"services_newsletter_consent_field_position_first", // ID
			esc_html__( '[Newsletter] Inject consent checkbox as the first field instead of the last', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_newsletter_consent_field_position_first' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion"
		);

		add_settings_field(
			'breach_services_newsletter',
			esc_html__( 'Newsletter', 'ct-ultimate-gdpr' ),
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
				SELECT DISTINCT email
				FROM {$wpdb->prefix}newsletter
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
	 *
	 */
	public function render_field_services_newsletter_consent_field() {

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
	public function render_field_services_newsletter_consent_field_position_first() {

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
	 * @param $cookies
	 * @param bool $force
	 *
	 * @return mixed
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {

		$cookies_to_block = array();
		if ( $force ) {
			$cookies_to_block = array(
				'newsletter_*',
			);
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_necessary() ] ) ) {
			$cookies[ $this->get_group()->get_level_necessary() ] = array_merge( $cookies[ $this->get_group()->get_level_necessary() ], $cookies_to_block );
		}

		return $cookies;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {
		add_action( 'wp_enqueue_scripts', array( $this, 'newsletter_form_elements_filter' ) );
	}

	/**
	 * @param $attrs
	 * @param $original_fields
	 *
	 * @return mixed
	 */
	public function newsletter_form_elements_filter() {

		$inject         = $this->get_admin_controller()->get_option_value( 'services_newsletter_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$position_first = $this->get_admin_controller()->get_option_value( 'services_newsletter_consent_field_position_first', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {
			wp_enqueue_script( 'ct-ultimate-gdpr-service-newsletter', ct_ultimate_gdpr_url( 'assets/js/service-newsletter.js' ) );
			if ( $position_first ) {
				wp_localize_script( 'ct-ultimate-gdpr-service-newsletter', 'ct_ultimate_gdpr_newsletter', array(
					'checkbox'                => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-newsletter-consent-field', false ) ),
					'checkbox_widget'         => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-newsletter-consent-field-widget', false ) ),
					'checkbox_widget_minimal' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-newsletter-consent-field-widget-minimal', false ) ),
					'checkbox_top'            => true,
				) );
			} else {
				wp_localize_script( 'ct-ultimate-gdpr-service-newsletter', 'ct_ultimate_gdpr_newsletter', array(
					'checkbox'                => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-newsletter-consent-field', false ) ),
					'checkbox_widget'         => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-newsletter-consent-field-widget', false ) ),
					'checkbox_widget_minimal' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-newsletter-consent-field-widget-minimal', false ) ),
					'checkbox_top'            => false,
				) );
			}
		}
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Newsletter gathers data entered by users in newsletter forms', 'ct-ultimate-gdpr' );
	}
}