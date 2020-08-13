<?php

/**
 * Class CT_Ultimate_GDPR_Service_CT_Waitlist
 */
class CT_Ultimate_GDPR_Service_CT_Waitlist extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_ct-waitlist/ctWaitlist.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_ct-waitlist/ctWaitlist.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}ct_waitlist
				WHERE email = %s	
				",
				$this->user->get_email()
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
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Waitlist for WooCommerce - Back In Stock Notifier' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'ctWaitlist' );
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
				DELETE FROM {$wpdb->prefix}ct_waitlist
				WHERE email = %s		
				",
				$this->user->get_email()
			),
			ARRAY_A
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
			'ct-ultimate-gdpr-services-ctwaitlist_accordion-15', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ctwaitlist_accordion-15' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ctwaitlist_accordion-15' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ctwaitlist_accordion-15' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_auth_consent_field", // ID
			sprintf(
				esc_html__( "[%s] Display consent checkbox for logged in user", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, "render_field_services_{$this->get_id()}_auth_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-ctwaitlist_accordion-15' // Section
		);

		add_settings_field(
			'breach_services_ct_waitlist',
			esc_html__( 'Waitlist for WooCommerce - Back In Stock Notifier', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2'
		);

	}

	/**
	 *
	 */
	public function render_field_services_ct_waitlist_consent_field() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

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
	public function render_field_services_ct_waitlist_auth_consent_field() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

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
		$inject = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		}
	}

	/**
	 * @param $original_fields
	 *
	 * @return mixed
	 */

	public function wp_enqueue_scripts(  ) {

		$ct_auth        = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( "services_{$this->get_id()}_auth_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		if( ! is_user_logged_in() ){
			wp_enqueue_script( 'ct-ultimate-gdpr-service-ct-waitlist', ct_ultimate_gdpr_url( 'assets/js/service-ct-waitlist.js' ) );
			wp_localize_script( 'ct-ultimate-gdpr-service-ct-waitlist', 'ct_ultimate_gdpr_ct_waitlist', array(
				'checkbox'              => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-ct-waitlist-consent-field', false ) )
			) );
		}

		if( is_user_logged_in() ){
			if( $ct_auth == "on" ){
				wp_enqueue_script( 'ct-ultimate-gdpr-service-ct-waitlist', ct_ultimate_gdpr_url( 'assets/js/service-ct-waitlist.js' ) );
				wp_localize_script( 'ct-ultimate-gdpr-service-ct-waitlist', 'ct_ultimate_gdpr_ct_waitlist', array(
					'checkbox'              => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-ct-waitlist-consent-field', false ) )
				) );
			}
		}




	}

	/**
	 *
	 */
	public function render_field_breach_services() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
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
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Waitlist for WooCommerce - Back In Stock Notifier gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}
}