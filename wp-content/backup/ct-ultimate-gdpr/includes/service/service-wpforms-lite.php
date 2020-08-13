<?php

/**
 * Class CT_Ultimate_GDPR_Service_WPForms_Lite
 */
class CT_Ultimate_GDPR_Service_WPForms_Lite extends CT_Ultimate_GDPR_Service_Abstract implements CT_Ultimate_CCPA_Service_Interface {

    /**
     * @return void
     */
    public function init() {
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
        return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'WPForms Lite' );
    }

    /**
     * @return bool
     */
    public function is_active() {
        return function_exists( 'load_textdomain' );
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
            'ct-ultimate-gdpr-services-wpforms_lite_accordion-23', // ID
            esc_html( $this->get_name() ), // Title
            null, // callback
            $this->front_controller->find_controller('services')->get_id() // Page
        );


        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpforms_lite_accordion-23' // Section
        );

        add_settings_field(
            "services_{$this->get_id()}_description", // ID
            sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_description_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpforms_lite_accordion-23'
        );

        add_settings_field(
            'services_wpforms_lite_consent_field', // ID
            sprintf(
                esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
                $this->get_name()
            ),
            array( $this, 'render_field_services_wpforms_lite_consent_field' ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpforms_lite_accordion-23'
        );



    }

    /**
     *
     */
    public function render_field_services_wpforms_lite_consent_field() {

        $admin = $this->get_admin_controller();

        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
        );

    }


    public function front_action() {
        add_action( 'wpforms_display_submit_after', array($this, 'wpforms_frontend_output_before_filter'),10, 1 ); // inside HTML INPUT
    }
    public function wpforms_frontend_output_before_filter() {
        $inject         = $this->get_admin_controller()->get_option_value( 'services_wpforms_lite_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
        $html = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-wpforms-lite-consent-field', false ), false );
        if ( $inject ) {
            echo $html;
        }
    }



    /**
     * @return string
     */
    protected function get_default_description() {
        return esc_html__( 'WPForms Lite gathers data entered by users in forms', 'ct-ultimate-gdpr' );
    }
}