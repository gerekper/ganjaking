<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Toggle Container', 'js_composer' ),
	'base' => 'vc_tta_toggle',
	'icon' => 'icon-wpb-tta-toggle',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Pageable content container', 'js_composer' ),

	'params' => array_merge(
		array(
			array(
				'type' => 'colorpicker',
				'value' => '#5188F1',
				'heading' => esc_html__( 'On color', 'js_composer' ),
				'param_name' => 'color',
				'description' => esc_html__( 'Select custom toggle color.', 'js_composer' ),
			),
			array(
				'type' => 'colorpicker',
				'value' => '#898989',
				'heading' => esc_html__( 'Off color', 'js_composer' ),
				'param_name' => 'hover_color',
				'description' => esc_html__( 'Select custom toggle hover color.', 'js_composer' ),
			),
			array(
				'type' => 'dropdown',
				'param_name' => 'tab_position',
				'value' => array(
					esc_html__( 'Top', 'js_composer' ) => 'top',
					esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
				),
				'std' => 'top',
				'heading' => esc_html__( 'Toggle position', 'js_composer' ),
				'description' => esc_html__( 'Select pageable navigation position.', 'js_composer' ),
			),
			array(
				'type' => 'hidden',
				'param_name' => 'no_fill_content_area',
				'std' => true,
			),
			// we need this hidden values cos we use pagination when switch container with toggle
			array(
				'type' => 'hidden',
				'param_name' => 'active_section',
				'value' => 1,
			),
			array(
				'type' => 'hidden',
				'param_name' => 'pagination_style',
				'value' => 'outline-square',
			),
		),
		array(
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
		)
	),
	'js_view' => 'VcBackendTtaPageableView',
	'custom_markup' => '
<div class="vc_tta-container vc_tta-o-non-responsive" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-pageable vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container">' . '<ul class="vc_tta-tabs-list">' . '<li class="vc_tta-tab" data-hide-add-control="true" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>' . '</ul>
		</div>
		<div class="vc_tta-panels vc_clearfix {{container-class}}">
		  {{ content }}
		</div>
	</div>
</div>',
	'default_content' => '
[vc_tta_toggle_section section_index=1 title="' . esc_html__( 'Monthly', 'js_composer' ) . '"][/vc_tta_toggle_section]
[vc_tta_toggle_section section_index=2 title="' . esc_html__( 'Yearly', 'js_composer' )  . '"][/vc_tta_toggle_section]
	',
	'admin_enqueue_js' => array(
		vc_asset_url( 'lib/vc_tabs/vc-tabs.min.js' ),
	),
);
