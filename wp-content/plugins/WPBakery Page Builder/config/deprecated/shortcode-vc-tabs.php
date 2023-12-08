<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Old Tabs', 'js_composer' ),
	'base' => 'vc_tabs',
	'show_settings_on_create' => false,
	'is_container' => true,
	'icon' => 'icon-wpb-ui-tab-content',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'deprecated' => '4.6',
	'description' => esc_html__( 'Tabbed content', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Auto rotate', 'js_composer' ),
			'param_name' => 'interval',
			'value' => array(
				esc_html__( 'Disable', 'js_composer' ) => 0,
				3,
				5,
				10,
				15,
			),
			'std' => 0,
			'description' => esc_html__( 'Auto rotate tabs each X seconds.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
	'custom_markup' => '
<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
<ul class="tabs_controls">
</ul>
%content%
</div>',
	'default_content' => '
[vc_tab title="' . esc_html__( 'Tab 1', 'js_composer' ) . '" tab_id=""][/vc_tab]
[vc_tab title="' . esc_html__( 'Tab 2', 'js_composer' ) . '" tab_id=""][/vc_tab]
',
	'js_view' => 'VcTabsView',
);
