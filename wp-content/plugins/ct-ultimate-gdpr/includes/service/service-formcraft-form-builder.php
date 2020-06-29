<?php

/**
 * Class CT_Ultimate_GDPR_Service_Formcraft
 */
class CT_Ultimate_GDPR_Service_Formcraft_Form_Builder extends CT_Ultimate_GDPR_Service_Abstract {

    /**
     * @return void
     */
    public function init() {
        add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_formcraft-form-builder/formcraft-main.php', '__return_true' );
        add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_formcraft-form-builder/formcraft-main.php', '__return_true' );

    }

    /**
     * @return $this
     */
    public function collect() {

        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("
				SELECT *
				FROM {$wpdb->prefix}formcraft_b_submissions
				WHERE content LIKE %s
				",
                "%" . $this->user->get_email() . "%"
            ),
            ARRAY_A
        );

        /* items table */

        $this->set_collected( $results );

        return $this;
    }

    /**
     * @return mixed
     */
    public function get_name() {
        return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Formcraft' );
    }

    /**
     * @return bool
     */
    public function is_active() {
        return function_exists( 'formcraft_basic_activate' );
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
        $query = $wpdb->prepare( "
				DELETE FROM {$wpdb->prefix}formcraft_b_submissions
				WHERE content LIKE %s
			",
            "%" . $this->user->get_email() . "%"
        );
        $wpdb->query( $query );


    }

    /**
     * @return mixed
     */
    public function add_option_fields() {

        add_settings_field(
            'services_formcraft_form_builder_consent_field', // ID
            sprintf(
                esc_html__( "[%s] Inject consent checkbox to all forms (Basic)", 'ct-ultimate-gdpr' ),
                $this->get_name()
            ),
            array( $this, 'render_field_services_formcraft_form_builder_consent_field' ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-formcraft_accordion-formcraft'
        );

    }

    /**
     *
     */
    public function render_field_services_formcraft_form_builder_consent_field() {

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
        $inject = $this->get_admin_controller()->get_option_value( 'services_formcraft_form_builder_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

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

        wp_enqueue_script( 'ct-ultimate-gdpr-service-formcraft-basic', ct_ultimate_gdpr_url( 'assets/js/service-formcraft.js' ) );
        wp_localize_script( 'ct-ultimate-gdpr-service-formcraft-basic', 'ct_ultimate_gdpr_formcraft_basic', array(
            'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-formcraft-consent-field', false ) ),
        ) );

    }

}