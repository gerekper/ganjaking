<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Accordion', 'js_composer' ),
	'base' => 'vc_tta_accordion',
	'icon' => 'icon-wpb-ui-accordion',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Collapsible content panels', 'js_composer' ),
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
			'description' => esc_html__( 'Select accordion display style.', 'js_composer' ),
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
			'description' => esc_html__( 'Select accordion shape.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'color',
			'value' => vc_get_shared( 'colors-dashed' ),
			'std' => 'grey',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'description' => esc_html__( 'Select accordion color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'checkbox',
			'param_name' => 'no_fill',
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
			'description' => esc_html__( 'Select accordion spacing.', 'js_composer' ),
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
			'description' => esc_html__( 'Select accordion gap.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'c_align',
			'value' => array(
				esc_html__( 'Default', 'js_composer' ) => 'default',
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Right', 'js_composer' ) => 'right',
				esc_html__( 'Center', 'js_composer' ) => 'center',
			),
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'description' => esc_html__( 'Select accordion section title alignment.', 'js_composer' ),
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
			'description' => esc_html__( 'Select auto rotate for accordion in seconds (Note: disabled by default).', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'param_name' => 'collapsible_all',
			'heading' => esc_html__( 'Allow collapse all?', 'js_composer' ),
			'description' => esc_html__( 'Allow collapse all accordion sections.', 'js_composer' ),
		),
		// Control Icons
		array(
			'type' => 'dropdown',
			'param_name' => 'c_icon',
			'value' => array(
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Chevron', 'js_composer' ) => 'chevron',
				esc_html__( 'Plus', 'js_composer' ) => 'plus',
				esc_html__( 'Triangle', 'js_composer' ) => 'triangle',
			),
			'std' => 'plus',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'description' => esc_html__( 'Select accordion navigation icon.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'c_position',
			'value' => array(
				esc_html__( 'Default', 'js_composer' ) => 'default',
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Right', 'js_composer' ) => 'right',
			),
			'dependency' => array(
				'element' => 'c_icon',
				'not_empty' => true,
			),
			'heading' => esc_html__( 'Position', 'js_composer' ),
			'description' => esc_html__( 'Select accordion navigation icon position.', 'js_composer' ),
		),
		// Control Icons END
		array(
			'type' => 'textfield',
			'param_name' => 'active_section',
			'heading' => esc_html__( 'Active section', 'js_composer' ),
			'value' => 1,
			'description' => esc_html__( 'Enter active section number (Note: to have all sections closed on initial load enter non-existing number).', 'js_composer' ),
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
	'js_view' => 'VcBackendTtaAccordionView',
	'custom_markup' => '
<div class="vc_tta-container" data-vc-action="collapseAll">
	<div class="vc_general vc_tta vc_tta-accordion vc_tta-color-backend-accordion-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-o-shape-group vc_tta-controls-align-left vc_tta-gap-2">
	   <div class="vc_tta-panels vc_clearfix {{container-class}}">
	      {{ content }}
	      <div class="vc_tta-panel vc_tta-section-append">
	         <div class="vc_tta-panel-heading">
	            <h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left">
	               <a href="javascript:;" aria-expanded="false" class="vc_tta-backend-add-control">
	                   <span class="vc_tta-title-text">' . esc_html__( 'Add Section', 'js_composer' ) . '</span>
	                    <i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i>
					</a>
	            </h4>
	         </div>
	      </div>
	   </div>
	</div>
</div>',
	'default_content' => '[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 1 ) . '"][/vc_tta_section][vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 2 ) . '"][/vc_tta_section]',
);
