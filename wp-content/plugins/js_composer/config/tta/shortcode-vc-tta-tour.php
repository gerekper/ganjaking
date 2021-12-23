<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Tour', 'js_composer' ),
	'base' => 'vc_tta_tour',
	'icon' => 'icon-wpb-ui-tab-content-vertical',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Vertical tabbed content', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'param_name' => 'title',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'title_tag',
			'value' => array(
				'h1' => 'h1',
				'h2' => 'h2',
				'h3' => 'h3',
				'h4' => 'h4',
				'h5' => 'h5',
				'h6' => 'h6',
				'p' => 'p',
			),
			'std' => 'h2',
			'heading' => esc_html__( 'Widget title tag', 'js_composer' ),
			'description' => esc_html__( 'Select widget title tag.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'section_title_tag',
			'value' => array(
				'h1' => 'h1',
				'h2' => 'h2',
				'h3' => 'h3',
				'h4' => 'h4',
				'h5' => 'h5',
				'h6' => 'h6',
				'p' => 'p',
			),
			'std' => 'h4',
			'heading' => esc_html__( 'Section title tag', 'js_composer' ),
			'description' => esc_html__( 'Select section title tag.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'style',
			'value' => array(
				esc_html__( 'Classic', 'js_composer' ) => 'classic',
				esc_html__( 'Modern', 'js_composer' ) => 'modern',
				esc_html__( 'Flat', 'js_composer' ) => 'flat',
				esc_html__( 'Outline', 'js_composer' ) => 'outline',
			),
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'description' => esc_html__( 'Select tour display style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'shape',
			'value' => array(
				esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
				esc_html__( 'Square', 'js_composer' ) => 'square',
				esc_html__( 'Round', 'js_composer' ) => 'round',
			),
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'description' => esc_html__( 'Select tour shape.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'color',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'description' => esc_html__( 'Select tour color.', 'js_composer' ),
			'value' => vc_get_shared( 'colors-dashed' ),
			'std' => 'grey',
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'checkbox',
			'param_name' => 'no_fill_content_area',
			'heading' => esc_html__( 'Do not fill content area?', 'js_composer' ),
			'description' => esc_html__( 'Do not fill content area with color.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'spacing',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
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
			'heading' => esc_html__( 'Spacing', 'js_composer' ),
			'description' => esc_html__( 'Select tour spacing.', 'js_composer' ),
			'std' => '1',
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'gap',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
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
			'heading' => esc_html__( 'Gap', 'js_composer' ),
			'description' => esc_html__( 'Select tour gap.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'tab_position',
			'value' => array(
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Right', 'js_composer' ) => 'right',
			),
			'heading' => esc_html__( 'Position', 'js_composer' ),
			'description' => esc_html__( 'Select tour navigation position.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'alignment',
			'value' => array(
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Right', 'js_composer' ) => 'right',
				esc_html__( 'Center', 'js_composer' ) => 'center',
			),
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'description' => esc_html__( 'Select tour section title alignment.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'controls_size',
			'value' => array(
				esc_html__( 'Auto', 'js_composer' ) => '',
				esc_html__( 'Extra large', 'js_composer' ) => 'xl',
				esc_html__( 'Large', 'js_composer' ) => 'lg',
				esc_html__( 'Medium', 'js_composer' ) => 'md',
				esc_html__( 'Small', 'js_composer' ) => 'sm',
				esc_html__( 'Extra small', 'js_composer' ) => 'xs',
			),
			'heading' => esc_html__( 'Navigation width', 'js_composer' ),
			'description' => esc_html__( 'Select tour navigation width.', 'js_composer' ),
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
			'description' => esc_html__( 'Select auto rotate for tour in seconds (Note: disabled by default).', 'js_composer' ),
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
			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	),
	'js_view' => 'VcBackendTtaTourView',
	'custom_markup' => '
<div class="vc_tta-container" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-left vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container">' . '<ul class="vc_tta-tabs-list">' . '<li class="vc_tta-tab" data-vc-tab data-vc-target-model-id="{{ model_id }}"><a href="javascript:;" data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}" data-vc-tabs>{{ section_title }}</a></li>' . '</ul>
		</div>
		<div class="vc_tta-panels {{container-class}}">
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
