<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to add gravity forms shortcode into WPBakery Page Builder
 */
add_action( 'plugins_loaded', 'vc_init_vendor_gravity_forms' );
function vc_init_vendor_gravity_forms() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require class-vc-wxr-parser-plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'gravityforms/gravityforms.php' ) || class_exists( 'RGForms' ) || class_exists( 'RGFormsModel' ) ) {
		// Call on map
		add_action( 'vc_after_init', 'vc_vendor_gravityforms_load' );
	} // if gravityforms active
}

function vc_vendor_gravityforms_load() {
	$gravity_forms_array[ esc_html__( 'No Gravity forms found.', 'js_composer' ) ] = '';
	$gravity_forms = array();
	if ( class_exists( 'RGFormsModel' ) && 'vc_edit_form' === vc_request_param( 'action' ) ) {
		/** @noinspection PhpUndefinedClassInspection */
		$gravity_forms = RGFormsModel::get_forms( 1, 'title' );
		if ( $gravity_forms ) {
			$gravity_forms_array = array( esc_html__( 'Select a form to display.', 'js_composer' ) => '' );
			foreach ( $gravity_forms as $gravity_form ) {
				$gravity_forms_array[ $gravity_form->title ] = $gravity_form->id;
			}
		}
	}
	vc_map( array(
		'name' => esc_html__( 'Gravity Form', 'js_composer' ),
		'base' => 'gravityform',
		'icon' => 'icon-wpb-vc_gravityform',
		'category' => esc_html__( 'Content', 'js_composer' ),
		'description' => esc_html__( 'Place Gravity form', 'js_composer' ),
		'params' => array(
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Form', 'js_composer' ),
				'param_name' => 'id',
				'value' => $gravity_forms_array,
				'save_always' => true,
				'description' => esc_html__( 'Select a form to add it to your post or page.', 'js_composer' ),
				'admin_label' => true,
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Display Form Title', 'js_composer' ),
				'param_name' => 'title',
				'value' => array(
					esc_html__( 'No', 'js_composer' ) => 'false',
					esc_html__( 'Yes', 'js_composer' ) => 'true',
				),
				'save_always' => true,
				'description' => esc_html__( 'Would you like to display the forms title?', 'js_composer' ),
				'dependency' => array(
					'element' => 'id',
					'not_empty' => true,
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Display Form Description', 'js_composer' ),
				'param_name' => 'description',
				'value' => array(
					esc_html__( 'No', 'js_composer' ) => 'false',
					esc_html__( 'Yes', 'js_composer' ) => 'true',
				),
				'save_always' => true,
				'description' => esc_html__( 'Would you like to display the forms description?', 'js_composer' ),
				'dependency' => array(
					'element' => 'id',
					'not_empty' => true,
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Enable AJAX?', 'js_composer' ),
				'param_name' => 'ajax',
				'value' => array(
					esc_html__( 'No', 'js_composer' ) => 'false',
					esc_html__( 'Yes', 'js_composer' ) => 'true',
				),
				'save_always' => true,
				'description' => esc_html__( 'Enable AJAX submission?', 'js_composer' ),
				'dependency' => array(
					'element' => 'id',
					'not_empty' => true,
				),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Tab Index', 'js_composer' ),
				'param_name' => 'tabindex',
				'description' => esc_html__( '(Optional) Specify the starting tab index for the fields of this form. Leave blank if you\'re not sure what this is.', 'js_composer' ),
				'dependency' => array(
					'element' => 'id',
					'not_empty' => true,
				),
			),
		),
	) );
}
