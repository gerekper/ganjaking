<?php

/**
 * Class CT_Ultimate_GDPR_Service_Contact_Form_7
 */
class CT_Ultimate_GDPR_Service_Contact_Form_7 extends CT_Ultimate_GDPR_Service_Abstract implements CT_Ultimate_CCPA_Service_Interface {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_contact-form-7/wp-contact-form-7.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_contact-form-7/wp-contact-form-7.php', '__return_true' );
		add_action( 'wpcf7_mail_components', array( $this, 'wpcf7_mail_components_filter' ), 10, 3 );
	}

	/** Add 'gdpr accepted' note to admin mails
	 *
	 * @param $components
	 * @param $form
	 * @param $mailer
	 *
	 * @return mixed
	 */
	public function wpcf7_mail_components_filter( $components, $form, $mailer ) {

		if ( isset( $components['body'] ) ) {

		    $mailSentText = $this->get_gdpr_accepted();

            if(!empty($mailSentText)){

                $components['body'] .= PHP_EOL . PHP_EOL . ''.$mailSentText.' : ' . date( get_option( 'date_format' ) ) . ' ' . date( get_option( 'time_format' ) );

            }

		}

		return $components;

	}

	/**
	 * @return $this
	 */
	public function collect() {
		$this->set_collected( array() );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Contact Form 7' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'wpcf7' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-cform7_accordion-4', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-cform7_accordion-4'
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-cform7_accordion-4' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-cform7_accordion-4'
		);

		add_settings_field(
			'services_contact_form_7_consent_field', // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_contact_form_7_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-cform7_accordion-4'
		);

		add_settings_field(
			'services_contact_form_7_consent_field_position_first', // ID
			esc_html__( '[Contact Form 7] Inject consent checkbox as the first field instead of the last', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_contact_form_7_consent_field_position_first' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-cform7_accordion-4'
		);

        add_settings_field(
            "services_{$this->get_id()}_gdpr_accepted", // ID
            sprintf( esc_html__( "[%s] Filter for Email sent at the bottom of contact form 7", 'ct-ultimate-gdpr' ), "GDPR Accepted" ), // Title
            array( $this, "render_gdpr_accepted_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-cform7_accordion-4' // Section
        );

	}

    public function render_gdpr_accepted_field() {


        $admin      = $this->get_admin_controller();
        $field_name = "services_{$this->get_id()}_service_gdpr_accepted";
        printf(
            "<textarea class='ct-ultimate-gdpr-accepted' id='%s' name='%s' rows='1' cols='100'>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, $this->get_gdpr_accepted())
        );

    }


    public function get_gdpr_accepted() {
        $gdpr_filter   = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( "services_{$this->get_id()}_service_gdpr_accepted", '', CT_Ultimate_GDPR_Controller_Services::ID );
        return $gdpr_filter;
    }

	/**
	 *
	 */
	public function render_field_services_contact_form_7_consent_field() {

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
	public function render_field_services_contact_form_7_consent_field_position_first() {

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
		add_filter( 'wpcf7_form_elements', array( $this, 'wpcf7_form_elements_filter' ), 100 );
	}

	/**
	 * @param $original_fields
	 *
	 * @return mixed
	 */
	public function wpcf7_form_elements_filter( $original_fields ) {

		$inject         = $this->get_admin_controller()->get_option_value( 'services_contact_form_7_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$position_first = $this->get_admin_controller()->get_option_value( 'services_contact_form_7_consent_field_position_first', false, $this->front_controller->find_controller('services')->get_id() );
		$fields         = $original_fields;

		if ( $inject ) {

			if ( $position_first ) {
				$fields = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-contact-form-7-consent-field', false ), false ) . $fields;
			} else {
				$fields .= ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-contact-form-7-consent-field', false ), false );

			}
		}

		return apply_filters( 'ct_ultimate_gdpr_service_contact_form_7_form_content', $fields, $original_fields, $inject, $position_first );
	}

    public function enqueue_static(  ) {

        wp_enqueue_script( 'ct-ultimate-gdpr-service-contact-form-7', ct_ultimate_gdpr_url( 'assets/js/service-contact-form-7.js' ) );
        wp_localize_script( 'ct-ultimate-gdpr-service-contact-form-7', 'ct_ultimate_gdpr_contact_form_7', array(
            'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-contact-form-7-consent-field', false ) ),
        ) );

    }

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Contact Form 7 gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}
}