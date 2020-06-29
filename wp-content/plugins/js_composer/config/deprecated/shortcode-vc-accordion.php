<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Old Accordion', 'js_composer' ),
	'base' => 'vc_accordion',
	'show_settings_on_create' => false,
	'is_container' => true,
	'icon' => 'icon-wpb-ui-accordion',
	'deprecated' => '4.6',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Collapsible content panels', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Active section', 'js_composer' ),
			'param_name' => 'active_tab',
			'value' => 1,
			'description' => esc_html__( 'Enter section number to be active on load or enter "false" to collapse all sections.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Allow collapse all sections?', 'js_composer' ),
			'param_name' => 'collapsible',
			'description' => esc_html__( 'If checked, it is allowed to collapse all sections.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Disable keyboard interactions?', 'js_composer' ),
			'param_name' => 'disable_keyboard',
			'description' => esc_html__( 'If checked, disables keyboard arrow interactions (Keys: Left, Up, Right, Down, Space).', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
	'custom_markup' => '
<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
%content%
</div>
<div class="tab_controls">
    <a class="add_tab" title="' . esc_html__( 'Add section', 'js_composer' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . esc_html__( 'Add section', 'js_composer' ) . '</span></a>
</div>
',
	'default_content' => '
    [vc_accordion_tab title="' . esc_html__( 'Section 1', 'js_composer' ) . '"][/vc_accordion_tab]
    [vc_accordion_tab title="' . esc_html__( 'Section 2', 'js_composer' ) . '"][/vc_accordion_tab]
',
	'js_view' => 'VcAccordionView',
);
