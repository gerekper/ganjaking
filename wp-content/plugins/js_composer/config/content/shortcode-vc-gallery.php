<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Image Gallery', 'js_composer' ),
	'base' => 'vc_gallery',
	'icon' => 'icon-wpb-images-stack',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Responsive image gallery', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Gallery type', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				esc_html__( 'Flex slider fade', 'js_composer' ) => 'flexslider_fade',
				esc_html__( 'Flex slider slide', 'js_composer' ) => 'flexslider_slide',
				esc_html__( 'Nivo slider', 'js_composer' ) => 'nivo',
				esc_html__( 'Image grid', 'js_composer' ) => 'image_grid',
			),
			'description' => esc_html__( 'Select gallery type.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Auto rotate', 'js_composer' ),
			'param_name' => 'interval',
			'value' => array(
				3,
				5,
				10,
				15,
				esc_html__( 'Disable', 'js_composer' ) => 0,
			),
			'description' => esc_html__( 'Auto rotate slides each X seconds.', 'js_composer' ),
			'dependency' => array(
				'element' => 'type',
				'value' => array(
					'flexslider_fade',
					'flexslider_slide',
					'nivo',
				),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Image source', 'js_composer' ),
			'param_name' => 'source',
			'value' => array(
				esc_html__( 'Media library', 'js_composer' ) => 'media_library',
				esc_html__( 'External links', 'js_composer' ) => 'external_link',
			),
			'std' => 'media_library',
			'description' => esc_html__( 'Select image source.', 'js_composer' ),
		),
		array(
			'type' => 'attach_images',
			'heading' => esc_html__( 'Images', 'js_composer' ),
			'param_name' => 'images',
			'value' => '',
			'description' => esc_html__( 'Select images from media library.', 'js_composer' ),
			'dependency' => array(
				'element' => 'source',
				'value' => 'media_library',
			),
		),
		array(
			'type' => 'exploded_textarea_safe',
			'heading' => esc_html__( 'External links', 'js_composer' ),
			'param_name' => 'custom_srcs',
			'description' => esc_html__( 'Enter external link for each gallery image (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
			'dependency' => array(
				'element' => 'source',
				'value' => 'external_link',
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Image size', 'js_composer' ),
			'param_name' => 'img_size',
			'value' => 'thumbnail',
			'description' => esc_html__( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'js_composer' ),
			'dependency' => array(
				'element' => 'source',
				'value' => 'media_library',
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Image size', 'js_composer' ),
			'param_name' => 'external_img_size',
			'value' => '',
			'description' => esc_html__( 'Enter image size in pixels. Example: 200x100 (Width x Height).', 'js_composer' ),
			'dependency' => array(
				'element' => 'source',
				'value' => 'external_link',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'On click action', 'js_composer' ),
			'param_name' => 'onclick',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Link to large image', 'js_composer' ) => 'img_link_large',
				esc_html__( 'Open Lightbox', 'js_composer' ) => 'link_image',
				esc_html__( 'Open custom link', 'js_composer' ) => 'custom_link',
			),
			'description' => esc_html__( 'Select action for click action.', 'js_composer' ),
			'std' => 'link_image',
		),
		array(
			'type' => 'exploded_textarea_safe',
			'heading' => esc_html__( 'Custom links', 'js_composer' ),
			'param_name' => 'custom_links',
			'description' => esc_html__( 'Enter links for each slide (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
			'dependency' => array(
				'element' => 'onclick',
				'value' => array( 'custom_link' ),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Custom link target', 'js_composer' ),
			'param_name' => 'custom_links_target',
			'description' => esc_html__( 'Select where to open  custom links.', 'js_composer' ),
			'dependency' => array(
				'element' => 'onclick',
				'value' => array(
					'custom_link',
					'img_link_large',
				),
			),
			'value' => vc_target_param_list(),
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
