<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => 'WP ' . esc_html__( 'RSS' ),
	'base' => 'vc_wp_rss',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'Entries from any RSS or Atom feed', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'RSS feed URL', 'js_composer' ),
			'param_name' => 'url',
			'description' => esc_html__( 'Enter the RSS feed URL.', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Items', 'js_composer' ),
			'param_name' => 'items',
			'value' => array(
				esc_html__( '10 - Default', 'js_composer' ) => 10,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9,
				10,
				11,
				12,
				13,
				14,
				15,
				16,
				17,
				18,
				19,
				20,
			),
			'description' => esc_html__( 'Select how many items to display.', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Options', 'js_composer' ),
			'param_name' => 'options',
			'value' => array(
				esc_html__( 'Item content', 'js_composer' ) => 'show_summary',
				esc_html__( 'Display item author if available?', 'js_composer' ) => 'show_author',
				esc_html__( 'Display item date?', 'js_composer' ) => 'show_date',
			),
			'description' => esc_html__( 'Select display options for RSS feeds.', 'js_composer' ),
		),
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
	),
);
