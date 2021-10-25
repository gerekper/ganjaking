<?php

/**
 * Class CT_Ultimate_GDPR_Service_Yikes_Inc_Easy_Mailchimp_Extender
 */
class CT_Ultimate_GDPR_Service_Yikes_Inc_Easy_Mailchimp_Extender extends CT_Ultimate_GDPR_Service_Abstract implements CT_Ultimate_CCPA_Service_Interface {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php', '__return_true' );
		// add_action( 'yikes-mailchimp-additional-form-fields', array( $this, 'wpcf7_mail_components_filter' ), 10, 1 );
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

	/** Used for data access. Returns all the data that user upload using this plugin
	 * @return $this
	 */
	public function collect() {
		$currentUserEmail = $this->user->get_email();
		$currentMemberData = array();
		$currentMemberId = null;
		$list_helper = yikes_get_mc_api_manager()->get_list_handler();
		$lists = $list_helper->get_lists();
		
		foreach($lists as $list){
			$members = $list_helper->get_members($list['id']);
			foreach($members as $email => $member){
				if($email === $currentUserEmail){
					$currentMemberId = $member['id'];
					$userData = $list_helper->get_member( $list['id'], $currentMemberId );
					$userData['merge_fields']['member_id'] = $currentMemberId;
					$currentMemberData[$list['id']] = $userData['merge_fields'];
					break;
				}
			}
		}
		
		$this->set_collected( $currentMemberData );
		return $this;
	}

	/** 
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Easy Forms for Mailchimp' );
	}

	/** Determine if the plugin is installed and activated
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'yikes_mailchimp_plugin_textdomain' ); // just check a core function of the plugin if it exists or not to determine if the plugin is enabled
	}
	/** Get the emails uploaded to the plugin
	 * 
	 */
	public function breach_recipients_filter( $recipients ) {
		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}
		$list_helper = yikes_get_mc_api_manager()->get_list_handler();
		$lists = $list_helper->get_lists();
		
		foreach($lists as $list){
			$members = $list_helper->get_members($list['id']);
			foreach($members as $email => $member){
				$recipients[$email] = $email;
			}
		}
		return $recipients;

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
		$this->collect();
		$list_helper = yikes_get_mc_api_manager()->get_list_handler();
		foreach((array) $this->collected as $listId => $list){
			$list_helper->member_unsubscribe($listId, $list['member_id']);
		}
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		$sectionName = 'ct-ultimate-gdpr-services-yikesinceasymailchimpextender_accordion-24'; // the 24 is not a random number in universe. It represents its placement in service page accordion. To be safe, always place new plugins at the bottom. If other plugin has the same number, options will not show up. If there are 30 plugin service registered in total, and you write 32, options will not show up
		/* SERVICE PAGE */
		// Add the Accordion Container
		add_settings_section(
			$sectionName, // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);
		// Option: Inject Consent Checbox to All Easy Forms
		add_settings_field(
			'services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field', // field name
			sprintf( // field label
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field' ), // render field
			$this->front_controller->find_controller('services')->get_id(), // Page
			$sectionName
		);
		// Option: Consent Text
		add_settings_field(
			'services_Yikes_Inc_Easy_Mailchimp_Extender_consent_text_field', // field name
			sprintf( // field label
				esc_html__( "[%s] Compliance Consent Text", 'ct-ultimate-gdpr'),
				$this->get_name()
			),
			array( $this, 'render_field_services_Yikes_Inc_Easy_Mailchimp_Extender_consent_text_field' ), // render field
			$this->front_controller->find_controller('services')->get_id(), // Page
			$sectionName
		);
		/* DATA BREACH PAGE: This field only appear in Data Breach Page */
		// A toggle to also include email collected from this plugin or not
		add_settings_field(
			"breach_services_{$this->get_id()}", // field name
			esc_html( $this->get_name() ), // field label
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2' // Section
		);

    }
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
	public function render_field_services_Yikes_Inc_Easy_Mailchimp_Extender_consent_text_field() {
        $admin      = $this->get_admin_controller();
        $field_name = "services_Yikes_Inc_Easy_Mailchimp_Extender_consent_text_field";
        printf(
            "<textarea 
				class='ct-ultimate-gdpr-accepted' 
				id='%s' 
				name='%s' 
				rows='1' 
				cols='100' 
				placeholder='I consent to the storage of my data according to the Privacy Policy'
			>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, $this->get_gdpr_accepted())
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
	public function render_field_services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field() {

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
	public function render_field_services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field_position_first() {

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
	public function front_action() { // Add the GDPR consent box on every easy form
		//GDPRCheckbox on every form
		add_action( 'yikes-mailchimp-additional-form-fields', array( $this, 'insert_gpdr_compliance_checkbox_filter' ), 10, 2 );
	}

	/**
	 * @param $original_fields
	 *
	 * @return mixed
	 */
	public function insert_gpdr_compliance_checkbox_filter( $original_fields ) {
		
		$inject = $this->get_admin_controller()->get_option_value( 'services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$consentText = $this->get_admin_controller()->get_option_value( 'services_Yikes_Inc_Easy_Mailchimp_Extender_consent_text_field', false, $this->front_controller->find_controller('services')->get_id() );
		$fields = $original_fields;

		
		// $data = apply_filters( 'ct_ultimate_gdpr_service_Yikes_Inc_Easy_Mailchimp_Extender_form_content', $fields, $original_fields, $inject, $position_first );
		$data = array(
			'consent_text' => $consentText ? $consentText : 'I consent to the storage of my data according to the Privacy Policy'
		);
		if ( $inject ) {
			echo ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-yikes-inc-easy-mailchimp-extender-consent-field', false ), false, $data );
		}
	}

    public function enqueue_static(  ) {

        wp_enqueue_script( 'ct-ultimate-gdpr-service-yikes-inc-easy-mailchimp-extender', ct_ultimate_gdpr_url( 'assets/js/service-yikes-inc-easy-mailchimp-extender.js' ) );
        wp_localize_script( 'ct-ultimate-gdpr-service-yikes-inc-easy-mailchimp-extender', 'ct_ultimate_gdpr_Yikes_Inc_Easy_Mailchimp_Extender', array(
            'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-yikes-inc-easy-mailchimp-extender-consent-field', false ) ),
        ) );

    }

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Easy Form for Mailchimp gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}
}