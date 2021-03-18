<?php

/**
 * Class CT_Ultimate_GDPR_Shortcode_Myaccount
 */
class CT_Ultimate_GDPR_Shortcode_Myaccount {
	
	/**
	 * @var string
	 */
	private $tag = 'ultimate_gdpr_myaccount';
	
	/**
	 * CT_Ultimate_GDPR_Shortcode_Myaccount constructor.
	 */
	public function __construct() {
		add_shortcode( $this->tag, array( $this, 'process' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_action' ) );
	}
	
	/**
	 *
	 */
	public function wp_enqueue_scripts_action() {
        wp_register_script( 'ct-ultimate-gdpr-tabs', ct_ultimate_gdpr_url( 'assets/js/shortcode-myaccount.js' ), array( 'jquery-ui-tabs' ), ct_ultimate_gdpr_get_plugin_version() );
        wp_enqueue_style( 'ct-ultimate-gdpr-jquery-ui', ct_ultimate_gdpr_url( 'assets/css/jquery-ui.min.css' ), ct_ultimate_gdpr_get_plugin_version() );

        if ( CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_recaptcha_secret', '', CT_Ultimate_GDPR_Controller_Services::ID ) ) {
            wp_register_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );
        }

        $userAgeLimit = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'age_limit_to_enter', '', CT_Ultimate_GDPR_Controller_Age::ID );
        $guardAgeLimit = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'age_limit_to_sell', '', CT_Ultimate_GDPR_Controller_Age::ID );

        wp_localize_script( 'ct-ultimate-gdpr-tabs', 'ct_ultimate_gdpr_myaccount', array(
                'ajaxurl'       => admin_url( 'admin-ajax.php' ),
                'error_message' => esc_html__( "There was an error in your request", 'ct-ultimate-gdpr' ),
                'user_age_limit' => $userAgeLimit,
                'guard_age_limit' => $guardAgeLimit,
                'age_limit_message'   => sprintf( esc_html__( 'You need to be at least %s years old to make full use of the website.', 'ct-ultimate-gdpr' ), $userAgeLimit )
            )
        );
	}
	
	/**
	 * Shortcode callback
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function process( $atts ) {
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'ct-ultimate-gdpr-tabs' );

        if ( CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_recaptcha_secret', '', CT_Ultimate_GDPR_Controller_Services::ID ) ) {
            wp_enqueue_script( 'recaptcha' );
        }

		$services = CT_Ultimate_GDPR_Model_Services::instance()->get_services( array() );
		CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'services', $services );
		CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'recaptcha_key', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_recaptcha_key', '', CT_Ultimate_GDPR_Controller_Services::ID ) );
		CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'form_shape', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'forgotten_skin_shape', 'ct-ultimate-gdpr-simple-form', CT_Ultimate_GDPR_Controller_Forgotten::ID ) );
		CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'unsubscribe_hide_unsubscribe_tab', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value_escaped( 'unsubscribe_hide_unsubscribe_tab', '', CT_Ultimate_GDPR_Controller_Unsubscribe::ID ) );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'my_account_disclaimer', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'cookie_my_account_disclaimer', ct_ultimate_gdpr_get_value('cookie_my_account_disclaimer',CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )->get_default_options()),CT_Ultimate_GDPR_Controller_Cookie::ID ) );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'age_enabled', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'age_enabled', false,CT_Ultimate_GDPR_Controller_Age::ID ) );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'age_is_user_underage', ct_ultimate_gdpr_is_user_underage() );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'age_date_array', ct_ultimate_gdpr_get_user_age_data_array() );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'age_placeholder', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'age_placeholder', '', CT_Ultimate_GDPR_Controller_Age::ID ) );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'age_limit_to_enter', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'age_limit_to_enter', '', CT_Ultimate_GDPR_Controller_Age::ID ) );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'age_limit_to_sell', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'age_limit_to_sell', '', CT_Ultimate_GDPR_Controller_Age::ID ) );
        CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'unsubscribe_subheader', CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'unsubscribe_subheader', '', CT_Ultimate_GDPR_Controller_Unsubscribe::ID ) );

        return $this->render();
	}
	
	/**
	 * Render shortcode template
	 */
	public function render() {
		ob_start();
		ct_ultimate_gdpr_render_template(
			ct_ultimate_gdpr_locate_template(
				"/shortcode/shortcode-myaccount",
				false
			),
			true,
			CT_Ultimate_GDPR_Model_Front_View::instance()->to_array()
		);
		return ob_get_clean();
	}
}