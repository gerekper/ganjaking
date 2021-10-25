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

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Blockquote $widget */

$widget->start_controls_section(
    'section_main',
    [
        'label' => __('Blockquote', 'gt3_themes_core'),
    ]
);
    
$widget->add_control(
    'tstm_author',
    array(
        'label' => esc_html__('Author Name', 'gt3_themes_core'),
        'type'  => Controls_Manager::TEXT,
    )
);

$widget->add_control(
    'sub_name',
    array(
        'label' => esc_html__('Author Position', 'gt3_themes_core'),
        'type'  => Controls_Manager::TEXT,
    )
);

$widget->add_control(
    'image',
    array(
        'label'   => esc_html__('Photo'),
        'type'    => Controls_Manager::MEDIA,
        'default' => array(
            'url' => Utils::get_placeholder_image_src(),
        ),
    )
);

$widget->add_control(
    'image_size',
    array(
        'label' => __( 'Image Size', 'gt3_themes_core' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => array( 'px' ),
        'range' => array(
            'px' => array(
                'min' => 20,
                'max' => 200,
            ),
        ),
        'default' => array(
            'size' => 32
        ),
        'selectors' => array(
            '{{WRAPPER}} .gt3_blockquote__author_container .gt3_blockquote__author_photo img' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;',
        ),
    )
);

$widget->add_control(
    'link',
    [
        'label' => __( 'Link', 'gt3_themes_core' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
            'active' => true,
        ],
        'placeholder' => __( 'https://your-link.com', 'gt3_themes_core' ),
    ]
);

$widget->add_control(
    'quote_icon',
    array(
        'label'       => esc_html__('Show Quote Icon', 'gt3_themes_core'),
        'type'        => Controls_Manager::SWITCHER,
    )
);

$widget->add_control(
    'item_align',
    array(
        'label'   => esc_html__('Alignment', 'gt3_themes_core'),
        'type'    => Controls_Manager::CHOOSE,
        'options' => array(
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
        'label_block' => false,
        'style_transfer' => true,
    )
);

$widget->add_control(
    'content',
    array(
        'label' => esc_html__('Description', 'gt3_themes_core'),
        'type'  => Controls_Manager::WYSIWYG,
    )
);

$widget->end_controls_section();

//////////
$widget->start_controls_section(
    'section_style',
    array(
        'label' => __( 'Style', 'gt3_themes_core' ),
        'tab' => Controls_Manager::TAB_STYLE,
    )
);

$widget->add_control(
    'color_tstm_author',
    array(
        'label'     => esc_html__('Author Name Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .gt3_blockquote__author_wrapper' => 'color: {{VALUE}};',
        )
    )
);

$widget->add_group_control(
    Group_Control_Typography::get_type(),
    array(
        'name'     => 'tstm_author_typography',
        'label'     => esc_html__('Author Name Typography', 'gt3_themes_core'),
        'selector' => '{{WRAPPER}} .gt3_blockquote__author_container',
    )
);

$widget->add_control(
    'color_sub_name',
    array(
        'label'     => esc_html__('Author Position Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .gt3_blockquote__author_sub_name' => 'color: {{VALUE}};',
        )
    )
);

$widget->add_group_control(
    Group_Control_Typography::get_type(),
    array(
        'name'     => 'sub_name_typography',
        'label'     => esc_html__('Author Position Typography', 'gt3_themes_core'),
        'selector' => '{{WRAPPER}} .gt3_blockquote__author_sub_name',
    )
);

$widget->add_control(
    'color_content',
    array(
        'label'     => esc_html__('Description Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .gt3_blockquote__text' => 'color: {{VALUE}};',
        )
    )
);

$widget->add_group_control(
    Group_Control_Typography::get_type(),
    array(
        'name'     => 'content_typography',
        'label'     => esc_html__('Description Typography', 'gt3_themes_core'),
        'selector' => '{{WRAPPER}} .gt3_blockquote__text',
    )
);

$widget->add_control(
    'quote_color',
    array(
        'label'     => esc_html__('Quote Icon Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'description' => esc_html__('Reloads page completly on every change', 'gt3_themes_core'),
        'selectors' => array(
            '{{WRAPPER}} .gt3_blockquote__quote_icon' => 'color: {{VALUE}};',
        )
    )
);



$widget->end_controls_section();