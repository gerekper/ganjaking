<?php

/**
 * Class GPNF_GPML
 *
 * Compatibility class for Gravity Forms Multilingual (Add-on for WPML)
 */
class GPNF_GFML {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		add_filter( 'gpnf_get_nested_form', array( $this, 'process_nested_form_with_gfml_pre_render' ) );
	}

	/**
	 * GFML translates forms using the gform_pre_render hook. Nested Forms does not call pre_render in situations like
	 * form confirmation or when adding to a WooCommerce cart which prevents the Nested Form from being translated.
	 *
	 * @param $form array Current form to be translated
	 *
	 * @return array The current form
	 */
	public function process_nested_form_with_gfml_pre_render( $form ) {
		if ( isset( $GLOBALS['wpml_gfml_tm_api'] ) ) {
			if (
				! isset( $form['gpnf_ran_gfml_pre_render'] )
				&& method_exists( $GLOBALS['wpml_gfml_tm_api'], 'gform_pre_render' )
			) {
				$form['gpnf_ran_gfml_pre_render'] = true;

				return $GLOBALS['wpml_gfml_tm_api']->gform_pre_render( $form );
			}
		}

		return $form;
	}

}

function gpnf_gfml() {
	return GPNF_GFML::get_instance();
}
