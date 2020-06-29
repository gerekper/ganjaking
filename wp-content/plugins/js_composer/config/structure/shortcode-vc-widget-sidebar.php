<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Widgetised Sidebar', 'js_composer' ),
	'base' => 'vc_widget_sidebar',
	'class' => 'wpb_widget_sidebar_widget',
	'icon' => 'icon-wpb-layout_sidebar',
	'category' => esc_html__( 'Structure', 'js_composer' ),
	'description' => esc_html__( 'WordPress widgetised sidebar', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'widgetised_sidebars',
			'heading' => esc_html__( 'Sidebar', 'js_composer' ),
			'param_name' => 'sidebar_id',
			'description' => esc_html__( 'Select widget area to display.', 'js_composer' ),
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
