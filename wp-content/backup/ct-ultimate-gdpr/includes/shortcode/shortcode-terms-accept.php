<?php

/**
 * Class CT_Ultimate_GDPR_Shortcode_Myaccount
 */
class CT_Ultimate_GDPR_Shortcode_Terms_Accept {

	/**
	 * @var string
	 */
	private $tag = 'ultimate_gdpr_terms_accept';

	/**
	 * CT_Ultimate_GDPR_Shortcode_Settings constructor.
	 */
	public function __construct() {
		add_shortcode( $this->tag, array( $this, 'process' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_action' ) );
	}

	/**
	 *
	 */
	public function wp_enqueue_scripts_action() {
		if ( get_post() && false !== strpos( get_post()->post_content, "[$this->tag]" ) ) {
			wp_enqueue_script(
				'ct-ultimate-gdpr-shortcode-terms-accept',
				ct_ultimate_gdpr_url( '/assets/js/shortcode-terms-accept.js' ),
				array( 'jquery' ),
				ct_ultimate_gdpr_get_plugin_version(),
				true
			);

			$redirect_page = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'terms_after_page', -1, CT_Ultimate_GDPR_Controller_Terms::ID, 'page' );
			if ( $redirect_page == -1 ) {
				$redirect = CT_Ultimate_GDPR_Controller_Terms::get_redirect_after_page();
			} else {
				$redirect = get_permalink( $redirect_page );
			}
			wp_localize_script( 'ct-ultimate-gdpr-shortcode-terms-accept', 'ct_ultimate_gdpr_terms',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'redirect' => $redirect,
				)
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
		return $this->render();
	}

	/**
	 * Render shortcode template
	 */
	public function render() {
		ob_start();
		ct_ultimate_gdpr_locate_template(
			"/shortcode/shortcode-terms-accept",
			true,
			CT_Ultimate_GDPR_Model_Front_View::instance()->to_array()
		);
		return ob_get_clean();
	}
}