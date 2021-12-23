<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Contact form7 vendor
 * =======
 * Plugin Contact form 7 vendor
 * To fix issues when shortcode doesn't exists in frontend editor. #1053, #1054 etc.
 * @since 4.3
 */
class Vc_Vendor_ContactForm7 {

	/**
	 * Add action when contact form 7 is initialized to add shortcode.
	 * @since 4.3
	 */
	public function load() {

		vc_lean_map( 'contact-form-7', array(
			$this,
			'addShortcodeSettings',
		) );
	}

	/**
	 * Mapping settings for lean method.
	 *
	 * @param $tag
	 *
	 * @return array
	 * @since 4.9
	 *
	 */
	public function addShortcodeSettings( $tag ) {
		/**
		 * Add Shortcode To WPBakery Page Builder
		 */
		$cf7 = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );

		$contact_forms = array();
		if ( $cf7 ) {
			foreach ( $cf7 as $cform ) {
				$contact_forms[ $cform->post_title ] = $cform->ID;
			}
		} else {
			$contact_forms[ esc_html__( 'No contact forms found', 'js_composer' ) ] = 0;
		}

		return array(
			'base' => $tag,
			'name' => esc_html__( 'Contact Form 7', 'js_composer' ),
			'icon' => 'icon-wpb-contactform7',
			'category' => esc_html__( 'Content', 'js_composer' ),
			'description' => esc_html__( 'Place Contact Form7', 'js_composer' ),
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Select contact form', 'js_composer' ),
					'param_name' => 'id',
					'value' => $contact_forms,
					'save_always' => true,
					'description' => esc_html__( 'Choose previously created contact form from the drop down list.', 'js_composer' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Search title', 'js_composer' ),
					'param_name' => 'title',
					'admin_label' => true,
					'description' => esc_html__( 'Enter optional title to search if no ID selected or cannot find by ID.', 'js_composer' ),
				),
			),
		);
	}
}
