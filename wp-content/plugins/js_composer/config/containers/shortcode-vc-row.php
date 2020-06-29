<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Row', 'js_composer' ),
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Place content elements inside the row', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Row stretch', 'js_composer' ),
			'param_name' => 'full_width',
			'value' => array(
				esc_html__( 'Default', 'js_composer' ) => '',
				esc_html__( 'Stretch row', 'js_composer' ) => 'stretch_row',
				esc_html__( 'Stretch row and content', 'js_composer' ) => 'stretch_row_content',
				esc_html__( 'Stretch row and content (no paddings)', 'js_composer' ) => 'stretch_row_content_no_spaces',
			),
			'description' => esc_html__( 'Select stretching options for row and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).', 'js_composer' ),
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
			'heading' => esc_html__( 'Full height row?', 'js_composer' ),
			'param_name' => 'full_height',
			'description' => esc_html__( 'If checked row will be set to full height.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns position', 'js_composer' ),
			'param_name' => 'columns_placement',
			'value' => array(
				esc_html__( 'Middle', 'js_composer' ) => 'middle',
				esc_html__( 'Top', 'js_composer' ) => 'top',
				esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
				esc_html__( 'Stretch', 'js_composer' ) => 'stretch',
			),
			'description' => esc_html__( 'Select columns position within row.', 'js_composer' ),
			'dependency' => array(
				'element' => 'full_height',
				'not_empty' => true,
			),
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
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use video background?', 'js_composer' ),
			'param_name' => 'video_bg',
			'description' => esc_html__( 'If checked, video will be used as row background.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'YouTube link', 'js_composer' ),
			'param_name' => 'video_bg_url',
			'value' => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
			// default video url
			'description' => esc_html__( 'Add YouTube link.', 'js_composer' ),
			'dependency' => array(
				'element' => 'video_bg',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Parallax', 'js_composer' ),
			'param_name' => 'video_bg_parallax',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Simple', 'js_composer' ) => 'content-moving',
				esc_html__( 'With fade', 'js_composer' ) => 'content-moving-fade',
			),
			'description' => esc_html__( 'Add parallax type background for row.', 'js_composer' ),
			'dependency' => array(
				'element' => 'video_bg',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Parallax', 'js_composer' ),
			'param_name' => 'parallax',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Simple', 'js_composer' ) => 'content-moving',
				esc_html__( 'With fade', 'js_composer' ) => 'content-moving-fade',
			),
			'description' => esc_html__( 'Add parallax type background for row (Note: If no image is specified, parallax will use background image from Design Options).', 'js_composer' ),
			'dependency' => array(
				'element' => 'video_bg',
				'is_empty' => true,
			),
		),
		array(
			'type' => 'attach_image',
			'heading' => esc_html__( 'Image', 'js_composer' ),
			'param_name' => 'parallax_image',
			'value' => '',
			'description' => esc_html__( 'Select image from media library.', 'js_composer' ),
			'dependency' => array(
				'element' => 'parallax',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Parallax speed', 'js_composer' ),
			'param_name' => 'parallax_speed_video',
			'value' => '1.5',
			'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'js_composer' ),
			'dependency' => array(
				'element' => 'video_bg_parallax',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Parallax speed', 'js_composer' ),
			'param_name' => 'parallax_speed_bg',
			'value' => '1.5',
			'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'js_composer' ),
			'dependency' => array(
				'element' => 'parallax',
				'not_empty' => true,
			),
		),
		vc_map_add_css_animation( false ),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Row ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
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
