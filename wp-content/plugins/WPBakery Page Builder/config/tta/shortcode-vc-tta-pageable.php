<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Pageable Container', 'js_composer' ),
	'base' => 'vc_tta_pageable',
	'icon' => 'icon-wpb-ui-pageable',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Pageable content container', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'param_name' => 'title',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'hidden',
			'param_name' => 'no_fill_content_area',
			'std' => true,
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'autoplay',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => 'none',
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'10' => '10',
				'20' => '20',
				'30' => '30',
				'40' => '40',
				'50' => '50',
				'60' => '60',
			),
			'std' => 'none',
			'heading' => esc_html__( 'Autoplay', 'js_composer' ),
			'description' => esc_html__( 'Select auto rotate for pageable in seconds (Note: disabled by default).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'param_name' => 'active_section',
			'heading' => esc_html__( 'Active section', 'js_composer' ),
			'value' => 1,
			'description' => esc_html__( 'Enter active section number (Note: to have all sections closed on initial load enter non-existing number).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'pagination_style',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Square Dots', 'js_composer' ) => 'outline-square',
				esc_html__( 'Radio Dots', 'js_composer' ) => 'outline-round',
				esc_html__( 'Point Dots', 'js_composer' ) => 'flat-round',
				esc_html__( 'Fill Square Dots', 'js_composer' ) => 'flat-square',
				esc_html__( 'Rounded Fill Square Dots', 'js_composer' ) => 'flat-rounded',
			),
			'heading' => esc_html__( 'Pagination style', 'js_composer' ),
			'description' => esc_html__( 'Select pagination style.', 'js_composer' ),
			'std' => 'outline-round',
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'pagination_color',
			'value' => vc_get_shared( 'colors-dashed' ),
			'heading' => esc_html__( 'Pagination color', 'js_composer' ),
			'description' => esc_html__( 'Select pagination color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
			'std' => 'grey',
			'dependency' => array(
				'element' => 'pagination_style',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'tab_position',
			'value' => array(
				esc_html__( 'Top', 'js_composer' ) => 'top',
				esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
			),
			'std' => 'bottom',
			'heading' => esc_html__( 'Pagination position', 'js_composer' ),
			'description' => esc_html__( 'Select pageable navigation position.', 'js_composer' ),
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
			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	),
	'js_view' => 'VcBackendTtaPageableView',
	'custom_markup' => '
<div class="vc_tta-container vc_tta-o-non-responsive" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-pageable vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container">' . '<ul class="vc_tta-tabs-list">' . '<li class="vc_tta-tab" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>' . '</ul>
		</div>
		<div class="vc_tta-panels vc_clearfix {{container-class}}">
		  {{ content }}
		</div>
	</div>
</div>',
	'default_content' => '
[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 1 ) . '"][/vc_tta_section]
[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 2 ) . '"][/vc_tta_section]
	',
	'admin_enqueue_js' => array(
		vc_asset_url( 'lib/vc_tabs/vc-tabs.min.js' ),
	),
);
