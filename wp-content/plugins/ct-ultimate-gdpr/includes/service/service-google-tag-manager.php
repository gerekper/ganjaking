<?php

/**
 * Class CT_Ultimate_GDPR_Service_Google_Tag_Manager
 */
class CT_Ultimate_GDPR_Service_Google_Tag_Manager extends CT_Ultimate_GDPR_Service_Abstract {
    /**
     * @return void
     */
    public function init() {}

    /**
     * @return mixed
     */
    public function get_name() {
        return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Google Tag Manager' );
    }

    /**
     * @return bool
     */
    public function is_active() {
        return true;
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
     * Collect data of a specific user
     *
     * @return $this
     */
    public function collect() {
        return $this;
    }

    /**
     * @return mixed
     */
    public function front_action() {

        // script for disabling GA tracking
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ), 1 );
        add_action('wp_footer', array( $this, 'enqueue_footer' ));
    }

    /**
     *
     */
    public function enqueue_static() {
        $id = $this->get_admin_controller()->get_option_value( 'cookie_services_google_tag_manager_gtm_id', '', $this->front_controller->find_controller('cookie')->get_id() );

        // no ga id was set in option
        if ( ! $id ) {
            return;
        }

        // consent given, no need to block ga
        /*if ( CT_Ultimate_GDPR::instance()->get_controller_by_id( $this->front_controller->find_controller('cookie')->get_id() )->is_consent_valid() ) {
            return;
        }*/

        wp_enqueue_script( 'ct-ultimate-gdpr-service-google-tag-manager', ct_ultimate_gdpr_url( '/assets/js/google-tag-manager.js' ) );
        wp_localize_script( 'ct-ultimate-gdpr-service-google-tag-manager', 'ct_ultimate_gdpr_service_gtm', array( 'id' => $id ) );
    }

    public  function enqueue_footer() {
        $id = $this->get_admin_controller()->get_option_value( 'cookie_services_google_tag_manager_gtm_id', '', $this->front_controller->find_controller('cookie')->get_id() );

        // no ga id was set in option
        if ( ! $id ) {
            return;
        }
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $id; ?>>"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }

    /**
     * @return mixed
     */
    public function add_option_fields() {
        add_settings_field(
            "cookie_services_{$this->get_id()}_gtm_id", // ID
            esc_html__( "Google Tag Manager ID, eg. GTM-PPK3P9S", 'ct-ultimate-gdpr' ), // Title
            array( $this, "render_field_cookie_services_{$this->get_id()}_gtm_id" ), // Callback
            $this->front_controller->find_controller('cookie')->get_id(), // Page
            'ct-ultimate-gdpr-cookie_tab-1_section-5' // Section
        );
    }

    /**
     *
     */
    public function render_field_cookie_services_google_tag_manager_gtm_id() {

        $admin = $this->get_admin_controller();

        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name )
        );

    }

}