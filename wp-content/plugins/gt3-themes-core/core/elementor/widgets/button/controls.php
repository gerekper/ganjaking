<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Button $widget */

$theme_color = esc_attr(gt3_option("theme-custom-color"));

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('General', 'gt3_themes_core')
	)
);

$widget->add_control(
	'button_title',
	array(
		'label'   => esc_html__('Button title', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => esc_html__('Button title', 'gt3_themes_core')
	)
);

$widget->add_control(
	'link',
	array(
		'label'       => esc_html__('Button link', 'gt3_themes_core'),
		'type'        => Controls_Manager::URL,
		'description' => esc_html__('Add link to button.', 'gt3_themes_core'),
		'default'     => array(
			'url'         => '#',
			'is_external' => false,
			'nofollow'    => false,
		),
	)
);

$widget->add_control(
	'button_size_elementor',
	array(
		'label'   => esc_html__('Button size', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'mini'   => esc_html__('Mini', 'gt3_themes_core'),
			'small'  => esc_html__('Small', 'gt3_themes_core'),
			'normal' => esc_html__('Normal', 'gt3_themes_core'),
			'large'  => esc_html__('Large', 'gt3_themes_core'),
			'custom' => esc_html__('Custom', 'gt3_themes_core'),
		),
		'default' => 'normal',
	)
);

$widget->add_control(
	'padding_size',
	array(
		'label'      => esc_html__('Padding', 'gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type6'                                                               => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type5 .gt3_module_button__container'                                 => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type4'                                                               => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type3'                                                               => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type2 .gt3_module_button__container .gt3_module_button__cover.front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type2 .gt3_module_button__container .gt3_module_button__cover.back'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type1.btn_icon_position_left'                                        => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type1.btn_icon_position_left:hover'                                  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} calc({{LEFT}}{{UNIT}} + 15px);',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type1.btn_icon_position_right'                                       => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .hover_type1.btn_icon_position_right:hover'                                 => 'padding: {{TOP}}{{UNIT}} calc({{RIGHT}}{{UNIT}} + 15px) {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.size_custom .button_size_elementor_custom:not(.hover_type5)'                            => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

		),
		'default'    => array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
		),
		'condition'  => array(
			'button_size_elementor' => 'custom',
		),
	)
);

$widget->add_control(
	'button_alignment',
	array(
		'label'       => esc_html__('Alignment', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'left'   => esc_html__('Left', 'gt3_themes_core'),
			'right'  => esc_html__('Right', 'gt3_themes_core'),
			'center' => esc_html__('Center', 'gt3_themes_core'),
			'block'  => esc_html__('Block', 'gt3_themes_core'),
			'inline' => esc_html__('Inline', 'gt3_themes_core'),
		),
		'description' => esc_html__('Select button alignment.', 'gt3_themes_core'),
		'default'     => 'center',
	)
);

$widget->add_responsive_control(
	'text_align',
	array(
		'label'     => esc_html__('Alignment', 'gt3_themes_core'),
		'type'      => Controls_Manager::CHOOSE,
		'options'   => array(
			'left'   => array(
				'title' => esc_html__('Left', 'gt3_themes_core'),
				'icon'  => 'fa fa-align-left',
			),
			'center' => array(
				'title' => esc_html__('Center', 'gt3_themes_core'),
				'icon'  => 'fa fa-align-center',
			),
			'right'  => array(
				'title' => esc_html__('Right', 'gt3_themes_core'),
				'icon'  => 'fa fa-align-right',
			),
		),
		'default'   => '',
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button a' => 'text-align: {{VALUE}};',
		),
		'condition' => array(
			'button_alignment' => 'block',
		),
	)
);

$widget->add_control(
	'btn_border_style',
	array(
		'label'       => esc_html__('Button Border Style', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'none'   => esc_html__('None', 'gt3_themes_core'),
			'solid'  => esc_html__('Solid', 'gt3_themes_core'),
			'double' => esc_html__('Double', 'gt3_themes_core'),
			'dotted' => esc_html__('Dotted', 'gt3_themes_core'),
			'dashed' => esc_html__('Dashed', 'gt3_themes_core'),
			'groove' => esc_html__('Groove', 'gt3_themes_core'),
		),
		'description' => esc_html__('Select button style.', 'gt3_themes_core'),
		'default'     => 'solid',
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2) a'                                                      => 'border-style: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'btn_border_rounded',
	array(
		'label'     => esc_html__('Border Rounded', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'condition' => array(
			'btn_border_style!' => 'none',
		),
	)
);

$widget->add_control(
	'btn_border_radius',
	array(
		'label'       => esc_html__('Button Border Radius', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'unset' => esc_html__('None', 'gt3_themes_core'),
			'1px'   => esc_html__('1px', 'gt3_themes_core'),
			'2px'   => esc_html__('2px', 'gt3_themes_core'),
			'3px'   => esc_html__('3px', 'gt3_themes_core'),
			'4px'   => esc_html__('4px', 'gt3_themes_core'),
			'5px'   => esc_html__('5px', 'gt3_themes_core'),
			'10px'  => esc_html__('10px', 'gt3_themes_core'),
			'15px'  => esc_html__('15px', 'gt3_themes_core'),
			'20px'  => esc_html__('20px', 'gt3_themes_core'),
			'25px'  => esc_html__('25px', 'gt3_themes_core'),
			'30px'  => esc_html__('30px', 'gt3_themes_core'),
			'35px'  => esc_html__('20px', 'gt3_themes_core'),
			'40px'  => esc_html__('25px', 'gt3_themes_core'),
			'45px'  => esc_html__('30px', 'gt3_themes_core'),
			'50px'  => esc_html__('30px', 'gt3_themes_core'),
		),
		'description' => esc_html__('Select button radius.', 'gt3_themes_core'),
		'default'     => 'unset',
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.rounded a'                                     => 'border-radius: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4 .gt3_module_button__cover:before' => 'border-radius: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4 .gt3_module_button__cover:after'  => 'border-radius: {{VALUE}};',
		),
		'condition'   => array(
			'btn_border_style!'   => 'none',
			'btn_border_rounded!' => '',
		),
	)
);

$widget->add_control(
	'btn_border_width',
	array(
		'label'       => esc_html__('Button Border Width', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'0'    => esc_html__('None', 'gt3_themes_core'),
			'1px'  => esc_html__('1px', 'gt3_themes_core'),
			'2px'  => esc_html__('2px', 'gt3_themes_core'),
			'3px'  => esc_html__('3px', 'gt3_themes_core'),
			'4px'  => esc_html__('4px', 'gt3_themes_core'),
			'5px'  => esc_html__('5px', 'gt3_themes_core'),
			'6px'  => esc_html__('6px', 'gt3_themes_core'),
			'7px'  => esc_html__('7px', 'gt3_themes_core'),
			'8px'  => esc_html__('8px', 'gt3_themes_core'),
			'9px'  => esc_html__('9px', 'gt3_themes_core'),
			'10px' => esc_html__('10px', 'gt3_themes_core'),
		),
		'description' => esc_html__('Select button border width.', 'gt3_themes_core'),
		'default'     => '1px',
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor a'                                                                     => 'border-width: {{VALUE}} !important;',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor a.hover_type2 .gt3_module_button__container .gt3_module_button__cover' => 'border-width: {{VALUE}} !important;',
		),
		'condition'   => array(
			'btn_border_style!' => 'none',
		),
	)
);

$widget->add_control(
	'btn_icon',
	array(
		'label'   => esc_html__('Button Icon', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'none'    => esc_html__('None', 'gt3_themes_core'),
			'default' => esc_html__('Default', 'gt3_themes_core'),
			'icon'    => esc_html__('Icon', 'gt3_themes_core'),
			'image'   => esc_html__('Image', 'gt3_themes_core'),
		),
		'default' => 'none',
	)
);

$widget->add_control(
	'icon_position',
	array(
		'label'     => esc_html__('Icon position', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'left'  => esc_html__('Left', 'gt3_themes_core'),
			'right' => esc_html__('Right', 'gt3_themes_core'),
		),
		'default'   => 'left',
		'condition' => array(
			'btn_icon!' => 'none',
		),
	)
);

$widget->add_control(
	'button_icon',
	array(
		'label'     => esc_html__('Icon:', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'btn_icon' => 'icon',
		),
	)
);

$widget->add_control(
	'image_size',
	array(
		'label'      => esc_html__('Image Width', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 32,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 8,
				'max'  => 64,
				'step' => 1,
			),
		),
		'size_units' => array( 'px' ),
		'condition'  => array(
			'btn_icon' => 'image'
		),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.button_icon_image .elementor_btn_icon_container img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .icon_svg_btn'                                                                    => 'width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_height',
	array(
		'label'      => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'em',
		),
		'range'      => array(
			'px'  => array(
				'min'  => 8,
				'max'  => 64,
				'step' => 1,
			),
			'em'  => array(
				'min'  => 0.1,
				'max'  => 5,
				'step' => 0.1,
			),
			'rem' => array(
				'min'  => 0.1,
				'max'  => 5,
				'step' => 0.1,
			),

		),
		'size_units' => array( 'px', 'em', 'rem' ),
		'condition'  => array(
			'btn_icon' => 'icon',
		),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.button_icon_icon:not(.hover_type2) .elementor_gt3_btn_icon'                                                                                                 => 'font-size: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .elementor-widget-gt3-addon-advanced-button .gt3_module_button_elementor.button_icon_icon a.hover_type2 .gt3_module_button__cover .elementor_btn_icon_container .elementor_gt3_btn_icon' => 'font-size: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_lh',
	array(
		'label'      => esc_html__('Icon Line Height', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'em',
		),
		'range'      => array(
			'px'  => array(
				'min'  => 8,
				'max'  => 64,
				'step' => 1,
			),
			'em'  => array(
				'min'  => 0.1,
				'max'  => 5,
				'step' => 0.1,
			),
			'rem' => array(
				'min'  => 0.1,
				'max'  => 5,
				'step' => 0.1,
			),
		),
		'size_units' => array( 'px', 'em', 'rem' ),
		'condition'  => array(
			'btn_icon' => 'icon',
		),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.button_icon_icon:not(.hover_type2) .elementor_gt3_btn_icon'                                                                                                 => 'line-height: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .elementor-widget-gt3-addon-advanced-button .gt3_module_button_elementor.button_icon_icon a.hover_type2 .gt3_module_button__cover .elementor_btn_icon_container .elementor_gt3_btn_icon' => 'line-height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'image',
	array(
		'label'     => esc_html__('Button Image', 'gt3_themes_core'),
		'type'      => Controls_Manager::MEDIA,
		'default'   => array(
			'url' => Utils::get_placeholder_image_src(),
		),
		'condition' => array(
			'btn_icon' => 'image'
		),
	)
);

$widget->add_control(
	'button_hover',
	array(
		'label'   => esc_html__('Background effect', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'none'  => esc_html__('No effect', 'gt3_themes_core'),
			'type1' => esc_html__('Effect 1 (only with icon)', 'gt3_themes_core'),
			'type2' => esc_html__('Effect 2', 'gt3_themes_core'),
			'type3' => esc_html__('Effect 3', 'gt3_themes_core'),
			'type4' => esc_html__('Effect 4', 'gt3_themes_core'),
			'type5' => esc_html__('Effect 5', 'gt3_themes_core'),
			'type6' => esc_html__('Effect 6', 'gt3_themes_core'),
		),
		'default' => 'none',
	)
);

$widget->add_control(
	'button_is_modal',
	array(
		'label' => esc_html__('Modal', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'modal_header',
	array(
		'label'     => esc_html__('Modal Header', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'condition' => array(
			'button_is_modal!' => ''
		),
	)
);

$widget->add_control(
	'modal_content',
	array(
		'label'     => esc_html__('Modal Content', 'gt3_themes_core'),
		'type'      => Controls_Manager::WYSIWYG,
		'condition' => array(
			'button_is_modal!' => ''
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'style',
	array(
		'label' => esc_html__('Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'button_style_header',
	array(
		'label' => esc_html__('Button', 'gt3_themes_core'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-button .elementor_gt3_btn_text',
	)
);

$widget->start_controls_tabs('style_tabs');
$widget->start_controls_tab('default_tab',
	array(
		'label' => esc_html__('Default', 'gt3_themes_core'),
	)
);

$widget->add_responsive_control(
	'icon_color',
	array(
		'label'       => esc_html__('Icon color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.button_icon_icon:not(.hover_type2) .elementor_gt3_btn_icon'                                                                                                       => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .elementor-widget-gt3-addon-advanced-button .gt3_module_button_elementor.button_icon_icon a.hover_type2 .gt3_module_button__cover.front .elementor_btn_icon_container .elementor_gt3_btn_icon' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .icon_svg_btn'                                                                                                                                                                                 => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_icon_default'                                                                                                                                                                             => 'color: {{VALUE}};',
		),
		'label_block' => true,
		'default'     => '#ffffff',
	)
);

$widget->add_responsive_control(
	'border_color',
	array(
		'label'       => esc_html__('Border color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'btn_border_style!' => 'none',
		),
		'label_block' => true,
		'default'     => $theme_color,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2) a'                                                            => 'border-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.front' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'button_title_color',
	array(
		'label'       => esc_html__('Title color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .elementor_gt3_btn_text'                                                                                                         => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container .gt3_module_button__cover.front'                         => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4 .gt3_module_button__container .gt3_module_button__cover.front .elementor_gt3_btn_text' => 'color: {{VALUE}};',
		),
		'label_block' => true,
		'default'     => '#ffffff',
	)
);

$widget->add_group_control(
	\Elementor\Group_Control_Background::get_type(),
	array(
		'label'          => esc_html__('Background color', 'gt3_themes_core'),
		'name'           => 'button_background_color',
//		'types'     => [ 'gradient' ],
		'selector'       => '{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2):not(.hover_type4):not(.hover_type5) a,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.front,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4 .gt3_module_button__cover:before,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type5 .gt3_module_button__container .gt3_module_button__cover.front:before,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type5 .gt3_module_button__container .gt3_module_button__cover.front:after,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type6',
		'fields_options' => [
			'color' => [
				'default' => $theme_color,
			],
		],
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab('hover_tab',
	array(
		'label' => esc_html__('Hover', 'gt3_themes_core'),
	)
);

$widget->add_responsive_control(
	'icon_color_hover',
	array(
		'label'       => esc_html__('Icon color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor.button_icon_icon:not(.hover_type2) a:hover .elementor_gt3_btn_icon'                                                                  => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.back .elementor_btn_icon_container .elementor_gt3_btn_icon' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button a:hover .icon_svg_btn'                                                                                                                                            => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button a:hover .gt3_icon_default'                                                                                                                                        => 'color: {{VALUE}};',
		),
		'label_block' => true,
		'default'     => $theme_color,
	)
);

$widget->add_responsive_control(
	'border_color_hover',
	array(
		'label'       => esc_html__('Border color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'btn_border_style!' => 'none',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2) a:hover'                                                     => 'border-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.back' => 'border-color: {{VALUE}};',
		),
		'label_block' => true,
		'default'     => $theme_color,
	)
);

$widget->add_responsive_control(
	'button_title_color_hover',
	array(
		'label'       => esc_html__('Title color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-button a:not(.hover_type2):hover .elementor_gt3_btn_text'                                                                                     => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container .gt3_module_button__cover.back .elementor_gt3_btn_text'        => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4:hover .gt3_module_button__container .gt3_module_button__cover.front .elementor_gt3_btn_text' => 'color: {{VALUE}};',
		),
		'label_block' => true,
		'default'     => $theme_color,
	)
);

$widget->add_group_control(
	\Elementor\Group_Control_Background::get_type(),
	array(
		'label'          => esc_html__('Background color', 'gt3_themes_core'),
		'name'           => 'button_background_color_hover',
//		'types'     => [ 'gradient' ],
		'selector'       => '
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2):not(.hover_type3):not(.hover_type4):not(.hover_type5):not(.hover_type6) a:hover,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.back,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type3:after,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4:hover .gt3_module_button__cover:after,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type5 .gt3_module_button__container .gt3_module_button__cover.back:before,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type5 .gt3_module_button__container .gt3_module_button__cover.back:after,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type6:hover:before,
		{{WRAPPER}}.elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type6:hover:after',
		'fields_options' => [
			'color' => [
				'default' => '#ffffff',
			],
		],
	)
);

$widget->end_controls_tab();
$widget->end_controls_tabs();

$widget->end_controls_section();
