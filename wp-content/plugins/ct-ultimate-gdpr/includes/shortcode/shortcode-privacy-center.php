<?php

/**
 * Class CT_Ultimate_GDPR_Shortcode_Privacy_Center
 */
class CT_Ultimate_GDPR_Shortcode_Privacy_Center {

	/**
	 * @var string
	 */
	private $tag = 'ultimate_gdpr_center';

	/**
	 * CT_Ultimate_GDPR_Shortcode_Privacy_Center constructor.
	 */
	public function __construct() {
		add_shortcode( $this->tag, array( $this, 'process' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_action' ) );
	}

    /**
     *
     */
    public function wp_enqueue_scripts_action() {

	    if ( get_post() && false !== strpos( get_post()->post_content, "[$this->tag" ) ) {

		    wp_enqueue_script(
			    'ct-ultimate-gdpr-shortcode-privacy-center',
			    ct_ultimate_gdpr_url( '/assets/js/shortcode-privacy-center.js' ),
			    array( 'jquery' ),
			    ct_ultimate_gdpr_get_plugin_version(),
			    true
		    );
	    }

    }

    /**
	 * Shortcode callback
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function process( $atts ) {

		$myaccount_url = get_permalink( ct_ultimate_gdpr_get_value( 'myaccount_page', $atts, 0 ) );
		$contact_url   = get_permalink( ct_ultimate_gdpr_get_value( 'contact_page', $atts, 0 ) );
		$policy_url    = get_permalink( CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'policy_target_page', 0, CT_Ultimate_GDPR_Controller_Policy::ID, 'page' ) );
		$terms_url     = get_permalink( CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'terms_target_page', 0, CT_Ultimate_GDPR_Controller_Terms::ID, 'page' ) );

		$icon_color    = ct_ultimate_gdpr_get_value( 'icon_color', $atts, '' );

		foreach ( get_defined_vars() as $key => $val ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->set( $key, $val );
		}

		return $this->render();

	}

	/**
	 * Render shortcode template
	 */
	public function render() {

		ob_start();
		ct_ultimate_gdpr_render_template(
			ct_ultimate_gdpr_locate_template(
				"/shortcode/shortcode-privacy-center",
				false
			),
			true,
			CT_Ultimate_GDPR_Model_Front_View::instance()->to_array()
		);

		return ob_get_clean();

	}
}