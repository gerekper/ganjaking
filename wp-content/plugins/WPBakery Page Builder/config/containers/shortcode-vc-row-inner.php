<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Inner Row', 'js_composer' ),
	'content_element' => false,
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'weight' => 1000,
	'show_settings_on_create' => false,
	'description' => esc_html__( 'Place content elements inside the inner row', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Row ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter optional row ID. Make sure it is unique, and it is valid as w3c specification: %s (Must not have spaces)', 'js_composer' ), '<a target="_blank" href="https://www.w3schools.com/tags/att_global_id.asp">' . esc_html__( 'link', 'js_composer' ) . '</a>' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Equal height', 'js_composer' ),
			'param_name' => 'equal_height',
			'description' => esc_html__( 'If checked columns will be set to equal height.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Reverse columns in RTL', 'js_composer' ),
			'param_name' => 'rtl_reverse',
			'description' => esc_html__( 'If checked columns will be reversed in RTL.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Content position', 'js_composer' ),
			'param_name' => 'content_placement',
			'value' => array(
				esc_html__( 'Default', 'js_composer' ) => '',
				esc_html__( 'Top', 'js_composer' ) => 'top',
				esc_html__( 'Middle', 'js_composer' ) => 'middle',
				esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
			),
			'description' => esc_html__( 'Select content position within columns.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns gap', 'js_composer' ),
			'param_name' => 'gap',
			'value' => array(
				'0px' => '0',
				'1px' => '1',
				'2px' => '2',
				'3px' => '3',
				'4px' => '4',
				'5px' => '5',
				'10px' => '10',
				'15px' => '15',
				'20px' => '20',
				'25px' => '25',
				'30px' => '30',
				'35px' => '35',
			),
			'std' => '0',
			'description' => esc_html__( 'Select gap between columns in row.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Disable row', 'js_composer' ),
			'param_name' => 'disable_element',
			// Inner param name.
			'description' => esc_html__( 'If checked the row won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	),
	'js_view' => 'VcRowView',
);
