<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Video Player', 'js_composer' ),
	'base' => 'vc_video',
	'icon' => 'icon-wpb-film-youtube',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Embed YouTube/Vimeo player', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Video link', 'js_composer' ),
			'param_name' => 'link',
			'value' => 'https://vimeo.com/channels/staffpicks/181907337',
			'admin_label' => true,
			'description' => sprintf( esc_html__( 'Enter link to video (Note: read more about available formats at WordPress %1$scodex page%2$s).', 'js_composer' ), '<a href="https://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">', '</a>' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Video width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => array(
				'100%' => '100',
				'90%' => '90',
				'80%' => '80',
				'70%' => '70',
				'60%' => '60',
				'50%' => '50',
				'40%' => '40',
				'30%' => '30',
				'20%' => '20',
				'10%' => '10',
			),
			'description' => esc_html__( 'Select video width (percentage).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Video aspect ratio', 'js_composer' ),
			'param_name' => 'el_aspect',
			'value' => array(
				'16:9' => '169',
				'4:3' => '43',
				'2.35:1' => '235',
				'9:16' => '916',
				'3:4' => '34',
				'1:2.35' => '1235',
			),
			'description' => esc_html__( 'Select video aspect ratio.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'description' => esc_html__( 'Select video alignment.', 'js_composer' ),
			'value' => array(
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Right', 'js_composer' ) => 'right',
				esc_html__( 'Center', 'js_composer' ) => 'center',
			),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
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
