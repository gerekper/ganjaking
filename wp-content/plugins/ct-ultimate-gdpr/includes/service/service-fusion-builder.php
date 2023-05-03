<?php

/**
 * Class CT_Ultimate_GDPR_Service_Fusion_Builder
 */
class CT_Ultimate_GDPR_Service_Fusion_Builder extends CT_Ultimate_GDPR_Service_Abstract implements CT_Ultimate_CCPA_Service_Interface {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_fusion-builder/fusion-builder.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_fusion-builder/fusion-builder.php', '__return_true' );
    }

	/**
	 * @return $this
	 */
	public function collect() {
        global $wpdb;
        $userEmail = $this->user->get_email();

        $results = $wpdb->get_results("
				SELECT *
				FROM {$wpdb->prefix}fusion_form_entries AS ffe
                INNER JOIN {$wpdb->prefix}fusion_form_fields AS fff ON ffe.field_id = fff.id
                INNER JOIN {$wpdb->prefix}fusion_form_submissions AS ffs ON ffe.submission_id =  ffs.id
				WHERE value = '{$userEmail}'
            ");

        $this->set_collected($results);

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Avada Builder' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'FusionBuilder' );
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

        foreach($this->collected as $data){
            (new Fusion_Form_DB_Submissions())->delete($data->submission_id);
            (new Fusion_Form_DB_Entries())->delete($data->id);
        }
	}

    public function breach_recipients_filter( $recipients ) {
        if ( ! $this->is_breach_enabled() ) { // check if the admin toggled the plugin
          return $recipients;
        }
      
        $dataOwners = (new Fusion_Form_DB_Entries())->get([
            'what' => 'value'
        ]);

        foreach($dataOwners as $email){
            if(!empty($email->value) && preg_match("^[A-Za-z0-9._%\-+!#$&/=?\^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$^", $email->value )) {
                $recipients[$email->value] = $email->value;
            }
        }
        
        return $recipients;
      }
      

    public function add_option_fields() {
        $section_id = 'ct-ultimate-gdpr-services-fusion-builder_accordion-45';

        add_settings_section(
			$section_id, // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

        add_settings_field(
            "services_fusion_builder_consent_field", // ID
            sprintf( esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ), "GDPR Accepted" ), // Title
			array($this,'render_field_services_fusion_builder_consent_field'), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            $section_id // Section
        );

        add_settings_field(
			"breach_services_{$this->get_id()}",
			esc_html( $this->get_name() ), // Title
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2' // Section
		);
    }

    public function render_field_services_fusion_builder_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
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

    public function front_action() {
		add_filter( 'fusion_element_button_content', array( $this, 'fusion_button_filter' ), 100, 4);
    }

    public function fusion_button_filter($content, $args) {
        $inject = $this->get_admin_controller()->get_option_value( "services_fusion_builder_consent_field", false, $this->front_controller->find_controller('services')->get_id() );
        $res = '';

        if (!empty($args['button_el_type']) && $args['button_el_type'] === 'submit' && $inject) {
            $res = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-contact-form-7-consent-field', false ), false );
            $res .= $content;
        } else {
            $res = $content;
        } 

        return $res;
    }
}