<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Sharing $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'select_alignment',
	array(
		'label'   => esc_html__('Select Alignment','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'align_left' => esc_html__('Left', 'gt3_themes_core'),
			'align_center' => esc_html__('Center', 'gt3_themes_core'),
			'align_right' => esc_html__('Right', 'gt3_themes_core'),
		),
		'default' => 'align_left'
	)
);

$widget->add_control(
	'sharing_type',
	array(
		'label'   => esc_html__('Type','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'type_icon' => esc_html__('Icons', 'gt3_themes_core'),
			'type_text' => esc_html__('Text', 'gt3_themes_core'),
		),
		'default' => 'type_icon',
		'description' => esc_html__('You can choose the type - icons or text.', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'sharing_label',
	array(
		'label' => esc_html__('Label', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'select_layout',
	array(
		'label'   => esc_html__('Label Layout','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'horizontal' => esc_html__('Horizontal', 'gt3_themes_core'),
			'vertical' => esc_html__('Vertical', 'gt3_themes_core'),
		),
		'default' => 'vertical',
		'condition' => array(
			'sharing_label!' => ''
		),
	)
);

$widget->add_control(
	'spacing_between_items',
	array(
		'label'       => esc_html__('Spacing between items', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 20,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter spacing in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_core span.gt3_sharing_label_title' => 'margin-right: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_core .gt3_sharing_links_block a' => 'margin-right: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'icon_facebook',
	array(
		'label' => esc_html__('Facebook?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'     => 'yes',
	)
);

$widget->add_control(
	'icon_twitter',
	array(
		'label' => esc_html__('Twitter?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'     => 'yes',
	)
);

$widget->add_control(
	'icon_pinterest',
	array(
		'label' => esc_html__('Pinterest?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'     => 'yes',
		'description' => esc_html__('It will be displayed only if the Featured Image is added to the post (page).', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'icon_google',
	array(
		'label' => esc_html__('Google?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'icon_email',
	array(
		'label' => esc_html__('Email?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'     => 'yes',
	)
);

$widget->add_control(
	'icon_linkedin',
	array(
		'label' => esc_html__('LinkedIn?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
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

	$widget->start_controls_tabs('style_items');

		$widget->start_controls_tab(
			'style_label',
			array(
				'label' => esc_html__('Label','gt3_themes_core'),
			)
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__('Label Typography','gt3_themes_core'),
				'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_label_title',
			)
		);

		$widget->add_control(
			'label_color',
			array(
				'label'       => esc_html__('Label Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_label_title' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'style_link',
			array(
				'label' => esc_html__('Link','gt3_themes_core'),
			)
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__('Link Typography','gt3_themes_core'),
				'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-sharing .link_type_text',
				'condition' => array(
					'sharing_type' => 'type_text'
				),
			)
		);

		$widget->add_control(
			'link_icon_size',
			array(
				'label'       => esc_html__('Icon Size', 'gt3_themes_core'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 18,
					'unit' => 'px',
				),
				'range'       => array(
					'px' => array(
						'min'  => 10,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units'  => array( 'px' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .link_type_icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'sharing_type' => 'type_icon'
				),
			)
		);

		$widget->add_control(
			'link_color',
			array(
				'label'       => esc_html__('Link Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'link_color_hover',
			array(
				'label'       => esc_html__('Link Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'custom_link_color',
			array(
				'label' => esc_html__('Custom Link Color?', 'gt3_themes_core'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$widget->add_control(
			'icon_facebook_color',
			array(
				'label'       => esc_html__('Facebook Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_facebook!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#3b5999',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_fb' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_facebook_color_hover',
			array(
				'label'       => esc_html__('Facebook Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_facebook!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#3b5999',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_fb:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_twitter_color',
			array(
				'label'       => esc_html__('Twitter Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_twitter!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#55acee',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_twitter' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_twitter_color_hover',
			array(
				'label'       => esc_html__('Twitter Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_twitter!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#55acee',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_twitter:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_pinterest_color',
			array(
				'label'       => esc_html__('Pinterest Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_pinterest!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#bd081c',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_pinterest' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_pinterest_color_hover',
			array(
				'label'       => esc_html__('Pinterest Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_pinterest!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#bd081c',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_pinterest:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_google_color',
			array(
				'label'       => esc_html__('Google Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_google!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#dd4b39',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_google' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_google_color_hover',
			array(
				'label'       => esc_html__('Google Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_google!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#dd4b39',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_google:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_email_color',
			array(
				'label'       => esc_html__('Email Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_email!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#d34436',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_email' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_email_color_hover',
			array(
				'label'       => esc_html__('Email Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_email!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#d34436',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_email:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_linkedin_color',
			array(
				'label'       => esc_html__('LinkedIn Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_linkedin!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#0077B5',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_linkedin' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'icon_linkedin_color_hover',
			array(
				'label'       => esc_html__('LinkedIn Color (Hover State)', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'condition' => array(
					'icon_linkedin!' => '',
					'custom_link_color!' => ''
				),
				'default' => '#0077B5',
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-sharing .gt3_sharing_links_block a.core_sharing_linkedin:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->end_controls_tab();

	$widget->end_controls_tabs();

$widget->end_controls_section();