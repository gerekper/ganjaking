<?php

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageBox $widget */

$widget->start_controls_section(
    'section_image',
    [
        'label' => __('Image Box', 'gt3_themes_core'),
    ]
);

$widget->add_control(
    'type',
    [
        'label' => __('Choose Type', 'gt3_themes_core'),
        'type' => Controls_Manager::CHOOSE,
        'default' => 'image',
        'options' => [
            'image' => [
                'title' => __('Image', 'gt3_themes_core'),
                'icon' => 'fa fa-image',
            ],
            'icon' => [
                'title' => __('Icon', 'gt3_themes_core'),
                'icon' => 'fa fa-star',
            ],
        ],
    ]
);

$widget->start_controls_tabs('image_state');
$widget->start_controls_tab('image_normal_state',
    [
        'label' => __('Normal', 'gt3_themes_core'),
    ]
);

$widget->add_control(
    'image',
    [
        'label' => __('Choose Image', 'gt3_themes_core'),
        'type' => Controls_Manager::MEDIA,
        'dynamic' => [
            'active' => true,
        ],
        'default' => [
            'url' => Utils::get_placeholder_image_src(),
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->end_controls_tab();

$widget->start_controls_tab('image_hover_state',
    [
        'label' => __('Hover', 'gt3_themes_core'),
    ]
);

$widget->add_control(
    'image_hover',
    [
        'label' => __('Choose Image', 'gt3_themes_core'),
        'type' => Controls_Manager::MEDIA,
        'dynamic' => [
            'active' => true,
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->end_controls_tab();

$widget->end_controls_tabs();

$widget->add_group_control(
    Group_Control_Image_Size::get_type(),
    [
        'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
        'default' => 'full',
        'separator' => 'none',
        'condition' => [
            'type' => 'image',
        ],
    ]
);


$widget->add_control(
    'icon',
    [
        'label' => __('Icon', 'gt3_themes_core'),
        'type' => Controls_Manager::ICON,
        'default' => 'fa fa-star',
        'condition' => [
            'type' => 'icon',
        ],
    ]
);

$widget->add_control(
    'view2',
    [
        'label' => __('View', 'gt3_themes_core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
            'default' => __('Default', 'gt3_themes_core'),
            'stacked' => __('Stacked', 'gt3_themes_core'),
            'framed' => __('Framed', 'gt3_themes_core'),
        ],
        'default' => 'default',
        'prefix_class' => 'elementor-view-',
        'condition' => [
            'icon!' => '',
            'type' => 'icon',
        ],
    ]
);

$widget->add_control(
    'shape',
    [
        'label' => __('Shape', 'gt3_themes_core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
            'circle' => __('Circle', 'gt3_themes_core'),
            'square' => __('Square', 'gt3_themes_core'),
        ],
        'default' => 'circle',
        'condition' => [
            'view2!' => 'default',
            'icon!' => '',
            'type' => 'icon',
        ],
        'prefix_class' => 'elementor-shape-',
    ]
);


$widget->add_control(
    'title_text',
    [
        'label' => __('Title & Description', 'gt3_themes_core'),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
            'active' => true,
        ],
        'default' => __('This is the heading', 'gt3_themes_core'),
        'placeholder' => __('Enter your title', 'gt3_themes_core'),
        'label_block' => true,
    ]
);

$widget->add_control(
    'description_text',
    [
        'label' => __('Content', 'gt3_themes_core'),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
            'active' => true,
        ],
        'default' => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
        'placeholder' => __('Enter your description', 'gt3_themes_core'),
        'separator' => 'none',
        'rows' => 10,
        'show_label' => false,
    ]
);

$widget->add_control(
    'link',
    [
        'label' => __('Link to', 'gt3_themes_core'),
        'type' => Controls_Manager::URL,
        'dynamic' => [
            'active' => true,
        ],
        'placeholder' => __('https://your-link.com', 'gt3_themes_core'),
        'separator' => 'before',
    ]
);

$widget->add_control(
    'position',
    [
        'label' => __('Image/Icon position', 'gt3_themes_core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
	        'default' => __('Default', 'gt3_themes_core'),
	        'beside' => __('Image/Icon beside Title', 'gt3_themes_core'),
	        'background' => __('Image/Icon in the background of Title', 'gt3_themes_core'),
        ],
        'default' => 'default',
    ]
);

$widget->add_control(
    'default_position',
    [
        'label' => __('Image/Icon Alignment', 'gt3_themes_core'),
        'type' => Controls_Manager::CHOOSE,
        'default' => 'top',
        'options' => [
            'left' => [
                'title' => __('Left', 'gt3_themes_core'),
                'icon' => 'fa fa-align-left',
            ],
            'top' => [
                'title' => __('Top', 'gt3_themes_core'),
                'icon' => 'fa fa-align-center',
            ],
            'right' => [
                'title' => __('Right', 'gt3_themes_core'),
                'icon' => 'fa fa-align-right',
            ],
        ],
        'prefix_class' => 'elementor-position-',
        'toggle' => false,
        'condition' => [
            'position' => 'default',
        ],
    ]
);


$widget->add_control(
    'title_size',
    [
        'label' => __('Title HTML Tag', 'gt3_themes_core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
            'h1' => 'H1',
            'h2' => 'H2',
            'h3' => 'H3',
            'h4' => 'H4',
            'h5' => 'H5',
            'h6' => 'H6',
            'div' => 'div',
            'span' => 'span',
            'p' => 'p',
        ],
        'default' => 'h6',
    ]
);

$widget->add_control(
    'view',
    [
        'label' => __('View', 'gt3_themes_core'),
        'type' => Controls_Manager::HIDDEN,
        'default' => 'traditional',
    ]
);

$widget->end_controls_section();

$widget->start_controls_section(
    'section_style_image',
    [
        'label' => __('Image', 'gt3_themes_core'),
        'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_responsive_control(
    'image_space',
    [
        'label' => __('Spacing', 'gt3_themes_core'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px' ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-wrapper .gt3-core-imagebox-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
            '{{WRAPPER}} .gt3-core-imagebox-wrapper .gt3-core-imagebox-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_responsive_control(
    'image_size',
    [
        'label' => __('Width', 'gt3_themes_core'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
            'size' => 30,
            'unit' => '%',
        ],
        'tablet_default' => [
            'unit' => '%',
        ],
        'mobile_default' => [
            'unit' => '%',
        ],
        'size_units' => [ 'px', '%' ],
        'range' => [
            '%' => [
                'min' => 5,
                'max' => 100,
            ],
            'px' => [
                'min' => 5,
                'max' => 1000,
            ]
        ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-wrapper .gt3-core-imagebox-img' => 'max-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_control(
    'hover_animation',
    [
        'label' => __('Hover Animation', 'gt3_themes_core'),
        'type' => Controls_Manager::HOVER_ANIMATION,
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->start_controls_tabs('image_effects');


$widget->start_controls_tab('normal',
    [
        'label' => __('Normal', 'gt3_themes_core'),
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_group_control(
    Group_Control_Css_Filter::get_type(),
    [
        'name' => 'css_filters',
        'selector' => '{{WRAPPER}} .gt3-core-imagebox-img img',
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_control(
    'image_opacity',
    [
        'label' => __('Opacity', 'gt3_themes_core'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
            'px' => [
                'max' => 1,
                'min' => 0.10,
                'step' => 0.01,
            ],
        ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-img img' => 'opacity: {{SIZE}};',
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_control(
    'background_hover_transition',
    [
        'label' => __('Transition Duration', 'gt3_themes_core'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
            'size' => 0.3,
        ],
        'range' => [
            'px' => [
                'max' => 3,
                'step' => 0.1,
            ],
        ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-img img' => 'transition-duration: {{SIZE}}s',
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->end_controls_tab();

$widget->start_controls_tab('hover',
    [
        'label' => __('Hover', 'gt3_themes_core'),
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_group_control(
    Group_Control_Css_Filter::get_type(),
    [
        'name' => 'css_filters_hover',
        'selector' => '{{WRAPPER}}:hover .gt3-core-imagebox-img img',
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->add_control(
    'image_opacity_hover',
    [
        'label' => __('Opacity', 'gt3_themes_core'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
            'px' => [
                'max' => 1,
                'min' => 0.10,
                'step' => 0.01,
            ],
        ],
        'selectors' => [
            '{{WRAPPER}}:hover .gt3-core-imagebox-img img' => 'opacity: {{SIZE}};',
        ],
        'condition' => [
            'type' => 'image',
        ],
    ]
);

$widget->end_controls_tab();

$widget->end_controls_tabs();

$widget->end_controls_section();


$widget->start_controls_section(
    'section_style_icon',
    [
        'label' => __( 'Icon', 'gt3_themes_core' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
            'icon!' => '',
            'type' => 'icon',
        ],
    ]
);

$widget->start_controls_tabs('icon_colors');

$widget->start_controls_tab(
    'icon_colors_normal',
    [
        'label' => __( 'Normal', 'gt3_themes_core' ),
        'condition' => [
            'type' => 'icon',
        ],
    ]
);

$widget->add_control(
    'primary_color',
    [
        'label' => __( 'Primary Color', 'gt3_themes_core' ),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
            'type' => Scheme_Color::get_type(),
            'value' => Scheme_Color::COLOR_1,
        ],
        'default' => '',
        'selectors' => [
            '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
            '{{WRAPPER}}.elementor-view-framed .elementor-icon,
            {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
        ],
        'condition' => [
            'type' => 'icon',
        ],
    ]
);

$widget->add_control(
    'secondary_color',
    [
        'label' => __( 'Secondary Color', 'gt3_themes_core' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
            'view!' => 'default',
            'type' => 'icon',
        ],
        'selectors' => [
            '{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
            '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
        ],
    ]
);

$widget->end_controls_tab();

$widget->start_controls_tab(
    'icon_colors_hover',
    [
        'label' => __( 'Hover', 'gt3_themes_core' ),
        'condition' => [
            'type' => 'icon',
        ],
    ]
);
$widget->add_control(
    'hover_primary_color',
    [
        'label' => __( 'Primary Color', 'gt3_themes_core' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'background-color: {{VALUE}};',
            '{{WRAPPER}}.elementor-view-framed .elementor-icon:hover, {{WRAPPER}}.elementor-view-default .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
        ],
        'condition' => [
            'type' => 'icon',
        ],
    ]
);

$widget->add_control(
    'hover_secondary_color',
    [
        'label' => __( 'Secondary Color', 'gt3_themes_core' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
            'view!' => 'default',
            'type' => 'icon',
        ],
        'selectors' => [
            '{{WRAPPER}}.elementor-view-framed .elementor-icon:hover' => 'background-color: {{VALUE}};',
            '{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'color: {{VALUE}};',
        ],
    ]
);

$widget->add_control(
    'hover_animation_icon',
    [
        'label' => __( 'Hover Animation', 'gt3_themes_core' ),
        'type' => Controls_Manager::HOVER_ANIMATION,
        'condition' => [
            'type' => 'icon',
        ],
    ]
);

$widget->end_controls_tab();

$widget->end_controls_tabs();


$this->add_responsive_control(
    'icon_space',
    [
        'label' => __( 'Spacing', 'gt3_themes_core' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px' ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-wrapper .gt3-core-imagebox-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            '{{WRAPPER}} .gt3-core-imagebox-wrapper .gt3-core-imagebox-title > .elementor-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
	        'view!' => 'default',
        ],
    ]
);

$this->add_responsive_control(
    'icon_size',
    [
        'label' => __( 'Size', 'gt3_themes_core' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
            'px' => [
                'min' => 6,
                'max' => 300,
            ],
        ],
        'selectors' => [
            '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
    ]
);

$this->add_control(
    'icon_padding',
    [
        'label' => __( 'Padding', 'gt3_themes_core' ),
        'type' => Controls_Manager::SLIDER,
        'selectors' => [
            '{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
        ],
        'range' => [
            'em' => [
                'min' => 0,
                'max' => 5,
            ],
        ],
        'condition' => [
            'view!' => 'default',
        ],
    ]
);

$this->add_control(
    'rotate',
    [
        'label' => __( 'Rotate', 'gt3_themes_core' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
            'size' => 0,
            'unit' => 'deg',
        ],
        'selectors' => [
            '{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
    ]
);

$this->add_control(
    'border_width',
    [
        'label' => __( 'Border Width', 'gt3_themes_core' ),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
            '{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
            'view' => 'framed',
        ],
    ]
);

$this->add_control(
    'border_radius',
    [
        'label' => __( 'Border Radius', 'gt3_themes_core' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
            '{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
            'view!' => 'default',
        ],
    ]
);


$widget->end_controls_section();


$widget->start_controls_section(
    'section_style_content',
    [
        'label' => __('Content', 'gt3_themes_core'),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
);

$widget->add_responsive_control(
    'text_align',
    [
        'label' => __('Alignment', 'gt3_themes_core'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
            'left' => [
                'title' => __('Left', 'gt3_themes_core'),
                'icon' => 'fa fa-align-left',
            ],
            'center' => [
                'title' => __('Center', 'gt3_themes_core'),
                'icon' => 'fa fa-align-center',
            ],
            'right' => [
                'title' => __('Right', 'gt3_themes_core'),
                'icon' => 'fa fa-align-right',
            ],
            'justify' => [
                'title' => __('Justified', 'gt3_themes_core'),
                'icon' => 'fa fa-align-justify',
            ],
        ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-wrapper' => 'text-align: {{VALUE}};',
        ],
    ]
);

$widget->add_control(
    'content_vertical_alignment',
    [
        'label' => __('Vertical Alignment', 'gt3_themes_core'),
        'type' => Controls_Manager::SELECT,
        'options' => [
            'top' => __('Top', 'gt3_themes_core'),
            'middle' => __('Middle', 'gt3_themes_core'),
            'bottom' => __('Bottom', 'gt3_themes_core'),
        ],
        'default' => 'top',
        'prefix_class' => 'elementor-vertical-align-',
    ]
);

$widget->add_control(
    'heading_title',
    [
        'label' => __('Title', 'gt3_themes_core'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
    ]
);

$widget->add_responsive_control(
    'title_bottom_space',
    [
        'label' => __('Spacing', 'gt3_themes_core'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
            'px' => [
                'min' => 0,
                'max' => 100,
            ],
        ],
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
    ]
);

$widget->start_controls_tabs('title_color_state');
$widget->start_controls_tab('title_color_normal_state',
    [
        'label' => __('Normal', 'gt3_themes_core'),
    ]
);

$widget->add_control(
    'title_color',
    [
        'label' => __('Color', 'gt3_themes_core'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-content .gt3-core-imagebox-title' => 'color: {{VALUE}};',
        ],
        'scheme' => [
            'type' => Scheme_Color::get_type(),
            'value' => Scheme_Color::COLOR_1,
        ],
    ]
);

$widget->end_controls_tab();

$widget->start_controls_tab(
    'title_color_hover_state',
    [
        'label' => __( 'Hover', 'gt3_themes_core' ),
    ]
);

$widget->add_control(
    'title_color_hover',
    [
        'label' => __('Color', 'gt3_themes_core'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}}:hover .gt3-core-imagebox-content .gt3-core-imagebox-title' => 'color: {{VALUE}};',
        ],
        'scheme' => [
            'type' => Scheme_Color::get_type(),
            'value' => Scheme_Color::COLOR_1,
        ],
    ]
);

$widget->end_controls_tab();
$widget->end_controls_tabs();








$widget->add_group_control(
    Group_Control_Typography::get_type(),
    [
        'name' => 'title_typography',
        'selector' => '{{WRAPPER}} .gt3-core-imagebox-content .gt3-core-imagebox-title',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
    ]
);

$widget->add_control(
    'heading_description',
    [
        'label' => __('Description', 'gt3_themes_core'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
    ]
);

$widget->start_controls_tabs('description_color_state');
$widget->start_controls_tab('description_color_normal_state',
    [
        'label' => __('Normal', 'gt3_themes_core'),
    ]
);

$widget->add_control(
    'description_color',
    [
        'label' => __('Color', 'gt3_themes_core'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .gt3-core-imagebox-content .gt3-core-imagebox-description' => 'color: {{VALUE}};',
        ],
        'scheme' => [
            'type' => Scheme_Color::get_type(),
            'value' => Scheme_Color::COLOR_3,
        ],
    ]
);

$widget->end_controls_tab();

$widget->start_controls_tab(
    'description_color_hover_state',
    [
        'label' => __( 'Hover', 'gt3_themes_core' ),
    ]
);

$widget->add_control(
    'description_color_hover',
    [
        'label' => __('Color', 'gt3_themes_core'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}}:hover .gt3-core-imagebox-content .gt3-core-imagebox-description' => 'color: {{VALUE}};',
        ],
        'scheme' => [
            'type' => Scheme_Color::get_type(),
            'value' => Scheme_Color::COLOR_3,
        ],
    ]
);

$widget->end_controls_tab();
$widget->end_controls_tabs();

$widget->add_group_control(
    Group_Control_Typography::get_type(),
    [
        'name' => 'description_typography',
        'selector' => '{{WRAPPER}} .gt3-core-imagebox-content .gt3-core-imagebox-description',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
    ]
);

$widget->end_controls_section();
