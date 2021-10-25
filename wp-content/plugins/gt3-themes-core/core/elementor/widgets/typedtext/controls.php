<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_TypedText $widget */

$widget->start_controls_section(
	'section_content',
	array(
		'label' => esc_html__('Content', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'prefix_text',
	array(
		'label'       => esc_html__('Prefix Text', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'label_block' => false,
	)
);

$widget->add_control(
	'suffix_text',
	array(
		'label'       => esc_html__('Suffix Text', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'label_block' => false,
	)
);

$widget->add_control(
	'typed_text',
	array(
		'label'       => esc_html__('Text', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(
			array(
				'string' => esc_html__('Type out sentences line 1', 'gt3_themes_core'),
			),
			array(
				'string' => esc_html__('Type out sentences line 2', 'gt3_themes_core'),
			),
			array(
				'string' => esc_html__('Type out sentences line 3', 'gt3_themes_core'),
			),
		),
		'fields'      => array(
			array(
				'name'        => 'string',
				'label'       => esc_html__('String', 'gt3_themes_core'),
				'show_label'  => false,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__('new line', 'gt3_themes_core'),
				'label_block' => true,
			)
		),
		'title_field' => '{{{ string }}}',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'options_section',
	array(
		'label' => 'Options',
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);

$widget->add_control(
	'typeSpeed',
	array(
		'label'       => esc_html__('Type Speed (ms)', 'gt3_themes_core'),
		'type'        => Controls_Manager::NUMBER,
		'description' => esc_html__('Type speed in milliseconds', 'gt3_themes_core'),
		'step'        => 10,
		'default'     => 40,
		'min'         => 0,
	)
);

$widget->add_control(
	'startDelay',
	array(
		'label'       => esc_html__('Start Delay (ms)', 'gt3_themes_core'),
		'type'        => Controls_Manager::NUMBER,
		'description' => esc_html__('Time before typing starts in milliseconds', 'gt3_themes_core'),
		'step'        => 50,
		'default'     => '0',
		'min'         => 0,
	)
);

$widget->add_control(
	'fadeOut',
	array(
		'label'   => esc_html__('Backspacing Type', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1' => esc_html__('Fade out', 'gt3_themes_core'),
			''  => esc_html__('Backspacing', 'gt3_themes_core'),
		),
		'default' => '',
	)
);

$widget->add_control(
	'backSpeed',
	array(
		'label'       => esc_html__('Backspacing Speed (ms)', 'gt3_themes_core'),
		'type'        => Controls_Manager::NUMBER,
		'description' => esc_html__('Backspacing speed in milliseconds', 'gt3_themes_core'),
		'default'     => 10,
		'min'         => 0,
		'condition'   => array(
			'fadeOut' => '',
		),
	)
);

$widget->add_control(
	'smartBackspace',
	array(
		'label'       => esc_html__('Smart Backspace', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('Only backspace what doesn\'t match the previous string', 'gt3_themes_core'),
		'condition'   => array(
			'fadeOut' => '',
		),
	)
);

$widget->add_control(
	'backDelay',
	array(
		'label'       => esc_html__('Backspacing Delay (ms)', 'gt3_themes_core'),
		'type'        => Controls_Manager::NUMBER,
		'description' => esc_html__('Time before backspacing in milliseconds', 'gt3_themes_core'),
		'step'        => 10,
		'default'     => 700,
		'min'         => 0,
	)
);

$widget->add_control(
	'loop',
	array(
		'label'       => esc_html__('Loop', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('Loop strings', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'loopCount',
	array(
		'label'       => esc_html__('Loop Count', 'gt3_themes_core'),
		'type'        => Controls_Manager::NUMBER,
		'default'     => '0',
		'min'         => 0,
		'description' => esc_html__('Amount of loops. "0" for Infinite', 'gt3_themes_core'),
		'condition'   => array(
			'loop!' => '',
		),
	)
);

$widget->add_control(
	'showCursor',
	array(
		'label'   => esc_html__('Show Cursor', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'cursorChar',
	array(
		'label'       => esc_html__('Cursor Char', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'default'     => '|',
		'description' => esc_html__('character for cursor', 'gt3_themes_core'),
		'condition'   => array(
			'showCursor!' => '',
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'style_section',
	array(
		'label' => esc_html__('Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'prefix_typography_heading',
	array(
		'label' => esc_html__('Prefix:'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_control(
	'prefix_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .typing-effect-prefix' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'prefix_typography',
		'selector' => '{{WRAPPER}} .typing-effect-prefix',
	)
);

$widget->add_control(
	'strings_typography_heading',
	array(
		'label' => esc_html__('Strings:'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_control(
	'strings_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .typing-effect-strings' => 'color: {{VALUE}};',
			'{{WRAPPER}} .typed-cursor' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'strings_typography',
		'selectors' => array(
			'{{WRAPPER}} .typing-effect-strings',
			'selector' => '{{WRAPPER}} .typed-cursor',
		),
	)
);

$widget->add_control(
	'suffix_typography_heading',
	array(
		'label' => esc_html__('Suffix:'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_control(
	'suffix_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .typing-effect-suffix' => 'color: {{VALUE}};'
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'suffix_typography',
		'selector' => '{{WRAPPER}} .typing-effect-suffix',
	)
);

$widget->end_controls_section();
