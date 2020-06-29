<?php

/**
 * Class CT_Ultimate_GDPR_Service_Hotjar
 */
class CT_Ultimate_GDPR_Service_Hotjar extends CT_Ultimate_GDPR_Service_Abstract {

    /**
     * @return void
     */
    public function init() {
    }

    /**
     * @return $this
     */

    /**
     * @return mixed
     */
    public function get_name() {
        return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Hotjar' );
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
     * @return mixed
     */
    public function add_option_fields() {

        /* Cookie section */

        add_settings_section(
            'ct-ultimate-gdpr-services-hotjar_accordion-hotjar', // ID
            esc_html( $this->get_name() ), // Title
            null, // callback
            $this->front_controller->find_controller('services')->get_id() // Page
        );

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-hotjar_accordion-hotjar' // Section
        );

        add_settings_field(
            "services_{$this->get_id()}_description", // ID
            sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Description
            array( $this, "render_description_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-hotjar_accordion-hotjar'
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
        if ( $force || CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_hotjar_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {
            $cookies_to_block = array(
                '_hjIncludedInSample',
                '_hjClosedSurveyInvites',
                '_hjDonePolls',
                '_hjMinimizedPolls',
                '_hjDoneTestersWidgets',
                '_hjMinimizedTestersWidgets',
                '_hjShownFeedbackMessage'
            );
        }

        $cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

        if ( is_array( $cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ] ) ) {
            $cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ] = array_merge( $cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ], $cookies_to_block );
        }

        return $cookies;

    }

    /**
     * @return mixed
     */
    public function front_action() {

    }


    /**
     *
     */
    public function enqueue_static() {

    }

    /**
     * @return string
     */
    protected function get_default_description() {
        return esc_html__('Hotjar works most accurately if the Hotjar Tracking Code is in the Head tag (<head>) of every page you want to track on your site. This ensures that Hotjar will start tracking visitors as soon as possible.', 'ct-ultimate-gdpr' );
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
     * @param array $scripts
     *
     * @param bool $force
     *
     * @return array
     */
    public function script_blacklist_filter( $scripts, $force = false ) {

        $scripts_to_block = array();

        if ( $force || CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_hotjar_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

            $scripts_to_block = array(
                "hotjar",
            );

        }

        $scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

        if ( is_array( $scripts[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ] ) ) {
            $scripts[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ] = array_merge( $scripts[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ], $scripts_to_block );
        }

        return $scripts;
    }
}