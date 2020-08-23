<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'base' => 'vc_flickr',
	'name' => esc_html__( 'Flickr Widget', 'js_composer' ),
	'icon' => 'icon-wpb-flickr',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Image feed from Flickr account', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Flickr ID', 'js_composer' ),
			'param_name' => 'flickr_id',
			'value' => '95572727@N00',
			'admin_label' => true,
			'description' => sprintf( esc_html__( 'To find your flickID visit %s.', 'js_composer' ), '<a href="https://www.webfx.com/tools/idgettr/" target="_blank">idGettr</a>' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Number of photos', 'js_composer' ),
			'param_name' => 'count',
			'value' => array(
				20,
				19,
				18,
				17,
				16,
				15,
				14,
				13,
				12,
				11,
				10,
				9,
				8,
				7,
				6,
				5,
				4,
				3,
				2,
				1,
			),
			'std' => 9, // bc
			'description' => esc_html__( 'Select number of photos to display.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Type', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				esc_html__( 'User', 'js_composer' ) => 'user',
				esc_html__( 'Group', 'js_composer' ) => 'group',
			),
			'description' => esc_html__( 'Select photo stream type.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Display order', 'js_composer' ),
			'param_name' => 'display',
			'value' => array(
				esc_html__( 'Latest first', 'js_composer' ) => 'latest',
				esc_html__( 'Random', 'js_composer' ) => 'random',
			),
			'description' => esc_html__( 'Select photo display order.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
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
);
