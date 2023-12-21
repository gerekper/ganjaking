<?php

/**
 * Advanced Slider widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Happy_Addons_Pro\Controls\Group_Control_Mask_Image;

defined('ABSPATH') || die();

class Advanced_Slider extends Base {

    /**
     * Get widget title.
     *
     * @since 1.13.1
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Advanced Slider', 'happy-addons-pro');
    }

    /**
     * Get widget icon.
     *
     * @since 1.13.1
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-slider';
    }

    public function get_keywords() {
        return ['hero slider', 'advanced', 'slider', 'carousel'];
    }

    /**
     * Register widget content controls
     */
    protected function register_content_controls() {
        $this->__slides_content_controls();
        $this->__settings_content_controls();
    }

    protected function __slides_content_controls() {

        $this->start_controls_section(
            '_section_slides',
            [
                'label' => __('Slides', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slider_type',
            [
                'label' => __('Slider Type', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'single'  => __('Single', 'happy-addons-pro'),
                    'multiple' => __('Multiple', 'happy-addons-pro'),
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_responsive_control(
            'slides_per_view',
            [
                'label' => __('Slides Per View', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 2,
                'condition' => [
                    'slider_type' => 'multiple'
                ],
                'frontend_available' => true,
                'style_transfer' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'slider_direction',
            [
                'label' => __('Slider Direction', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal'  => __('Horizontal', 'happy-addons-pro'),
                    'vertical' => __('Vertical', 'happy-addons-pro'),
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'effect',
            [
                'label' => __('Slider Effect', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'false',
                'options' => [
                    'false'  => __('Slide', 'happy-addons-pro'),
                    'fade' => __('Fade', 'happy-addons-pro'),
                    // 'cube' => __('Cube', 'happy-addons-pro'),
                    'flip' => __('Flip', 'happy-addons-pro'),
                ],
                'condition' => [
                    'slider_type' => 'single'
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'effect_multiple',
            [
                'label' => __('Slider Effect', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'false',
                'options' => [
                    'false'  => __('Slide', 'happy-addons-pro'),
                    'coverflow' => __('Cover Flow', 'happy-addons-pro'),
                ],
                'condition' => [
                    'slider_type' => 'multiple'
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'effect_speed',
            [
                'label' => __('Effect Speed (ms)', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
                'frontend_available' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'slides_control_separator',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $slides = new \Elementor\Repeater();

        $slides->add_control(
            'content_type',
            [
                'label' => __('Content Type', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'  => __('Default', 'happy-addons-pro'),
                    'template' => __('Template', 'happy-addons-pro'),
                ],
            ]
        );

        $slides->start_controls_tabs(
            'slide_content_tabs'
        );

        $slides->start_controls_tab(
            'slide_background_tabs',
            [
                'label' => __('Background', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
            ]
        );

        $slides->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slider_background',
                'label' => __('Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-slide, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-gallery-slide',
                'separator' => 'before',
                'style_transfer' => true,
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => '#71D7F7',
                    ],
                    'position' => [
                        'default' => 'center center',
                    ],
                    'size' => [
                        'default' => 'cover',
                    ],
                ],
                'condition' => [
                    'content_type' => 'default'
                ],
            ]
        );

        $slides->end_controls_tab();

        $slides->start_controls_tab(
            'slide_content_tabs_content',
            [
                'label' => __('Content', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
            ]
        );

        $slides->add_control(
            'slide_content_icon',
            [
                'label' => __('Icon Type', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'icon' => [
                        'title' => __('Icon', 'happy-addons-pro'),
                        'icon' => 'eicon-nerd',
                    ],
                    'image' => [
                        'title' => __('Image', 'happy-addons-pro'),
                        'icon' => 'eicon-image',
                    ],
                ],
                'condition' => [
                    'content_type' => 'default',
                ],
                'default' => 'icon',
                'toggle' => false,
                'style_transfer' => true,
            ]
        );

        $slides->add_control(
            'image',
            [
                'label' => __('Image', 'happy-addons-pro'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_icon' => 'image',
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $slides->add_group_control(
            Group_Control_Mask_Image::get_type(),
            [
                'name' => 'image_masking',
                'label' => 'Masking',
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_icon' => 'image',
                ],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-figure--image',
            ]
        );

        $slides->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'medium_large',
                'separator' => 'none',
                'exclude' => [
                    'full',
                    'custom',
                    'large',
                    'shop_catalog',
                    'shop_single',
                    'shop_thumbnail'
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_icon' => 'image',
                ]
            ]
        );

        $slides->add_control(
            'icon',
            [
                'label' => __('Icon', 'happy-addons-pro'),
                'type' => Controls_Manager::ICONS,
                // 'label_block' => true,
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_icon' => 'icon',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_title',
            [
                'label' => __('Title', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('', 'happy-addons-pro'),
                'placeholder' => __('Type your title here', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->add_control(
            'slide_content_sub_title',
            [
                'label' => __('Sub Title', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('', 'happy-addons-pro'),
                'placeholder' => __('Type your sub title here', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->add_control(
            'slide_content_description',
            [
                'label' => __('Description', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('', 'happy-addons-pro'),
                'placeholder' => __('Type your description here', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->add_control(
            'slide_content_button_1_text',
            [
                'label' => __('Button 1 Text', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->add_control(
            'slide_content_button_1_link',
            [
                'label' => __('Button 1 Link', 'happy-addons-pro'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'happy-addons-pro'),
                'show_external' => true,
                'default' => [
                    'url' => 'https://happyaddons.com/',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_button_1_text!' => '',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->add_control(
            'slide_content_button_2_text',
            [
                'label' => __('Button 2 Text', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->add_control(
            'slide_content_button_2_link',
            [
                'label' => __('Button 2 Link', 'happy-addons-pro'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'happy-addons-pro'),
                'show_external' => true,
                'default' => [
                    'url' => 'https://happyaddons.com/',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_button_2_text!' => '',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $slides->end_controls_tab();

        $slides->start_controls_tab(
            'slide_content_tabs_style',
            [
                'label' => __('Style', 'happy-addons-pro'),
                'condition' => [
                    'content_type' => 'default'
                ],
            ]
        );

        $slides->add_control(
            'slide_content_custom',
            [
                'label' => __('Custom', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'description' => __('Set custom style that will only affect this specific slide.', 'happy-addons-pro'),
                'default' => 'no',
            ]
        );

        $slides->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slide_content_background',
                'label' => __('Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content',
                'separator' => 'before',
                'style_transfer' => true,
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_horizontal_align',
            [
                'label' => __('Horizontal Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content-wrapper' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_vertical_align',
            [
                'label' => __('Vertical Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Top', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content-wrapper' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_text_align',
            [
                'label' => __('Text Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_color',
            [
                'label' => __('Content Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-title, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-sub-title, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_icon_color',
            [
                'label' => __('Icon Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-figure--icon i, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-figure--icon svg' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                    'slide_content_icon' => 'icon',
                ],
            ]
        );

        $slides->add_responsive_control(
            'slide_content_icon_size',
            [
                'label' => __('Icon/ Image Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 40,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-figure' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'slide_content_text_shadow',
                'label' => __('Text Shadow', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-icon, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-title, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-sub-title, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-content-description, {{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content .ha-slider-button',
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );


        $slides->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_content_border',
                'label' => __('Border', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content',
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->add_control(
            'slide_content_border_radius',
            [
                'label' => __('Bordar radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ha-slider-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'content_type' => 'default',
                    'slide_content_custom' => 'yes',
                ],
            ]
        );

        $slides->end_controls_tab();

        $slides->end_controls_tabs();


        $slides->add_control(
            'slide_content_template',
            [
                'label' => __('Choose Template', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => false,
                'options' => ha_pro_get_elementor_templates(),
                'condition' => [
                    'content_type' => 'template'
                ],
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __('Slides', 'happy-addons-pro'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $slides->get_controls(),
                'default' => [
                    [
                        'content_type' => 'default',
                        'slide_content_title' => __('Advanced Slider 1 Title', 'happy-addons-pro'),
                        'slide_content_sub_title' => __('Sub Title', 'happy-addons-pro'),
                        'slide_content_description' => __('Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'happy-addons-pro'),
                        'slider_background_background' => 'classic',
                        'slider_background_color' => '#1F2363',
                        'slide_content_button_1_text' => esc_html__('Button 1', 'happy-addons-pro'),
                        'slide_content_button_1_link' => ['url' => 'https://happyaddons.com/'],
                        'slide_content_button_2_text' => esc_html__('Button 2', 'happy-addons-pro'),
                        'slide_content_button_2_link' => ['url' => 'https://happyaddons.com/'],
                    ],
                    [
                        'content_type' => 'default',
                        'slide_content_title' => __('Advanced Slider 2 Title', 'happy-addons-pro'),
                        'slide_content_sub_title' => __('Sub Title', 'happy-addons-pro'),
                        'slide_content_description' => __('Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'happy-addons-pro'),
                        'slider_background_background' => 'classic',
                        'slider_background_color' => '#5636D1',
                        'slide_content_button_1_text' => esc_html__('Button 1', 'happy-addons-pro'),
                        'slide_content_button_1_link' => ['url' => 'https://happyaddons.com/'],
                        'slide_content_button_2_text' => esc_html__('Button 2', 'happy-addons-pro'),
                        'slide_content_button_2_link' => ['url' => 'https://happyaddons.com/'],
                    ],
                    [
                        'content_type' => 'default',
                        'slide_content_title' => __('Advanced Slider 3 Title', 'happy-addons-pro'),
                        'slide_content_sub_title' => __('Sub Title', 'happy-addons-pro'),
                        'slide_content_description' => __('Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'happy-addons-pro'),
                        'slider_background_background' => 'classic',
                        'slider_background_color' => '#8D0F70',
                        'slide_content_button_1_text' => esc_html__('Button 1', 'happy-addons-pro'),
                        'slide_content_button_1_link' => ['url' => 'https://happyaddons.com/'],
                        'slide_content_button_2_text' => esc_html__('Button 2', 'happy-addons-pro'),
                        'slide_content_button_2_link' => ['url' => 'https://happyaddons.com/'],
                    ],
                ],
            ]
        );

        $this->add_control(
            'slides_style_control_separator',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_responsive_control(
            'slider_height',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', 'em'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 3000,
                        'step' => 5,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.1,
                        'max' => 16,
                        'step' => 0.1,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 60,
                    'unit' => 'vh',
                ],
                'tablet_default' => [
                    'size' => 45,
                    'unit' => 'vh',
                ],
                'mobile_default' => [
                    'size' => 60,
                    'unit' => 'vh',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'space_between_slides',
            [
                'label' => __('Space Between Slides', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => 0,
                'frontend_available' => true,
                'description' => esc_html__('Slides space in pixel(px)', 'happy-addons-pro'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function __settings_content_controls() {

        $this->start_controls_section(
            '_section_slider_settings',
            [
                'label' => __('Slider Settings', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slider_content_animation',
            [
                'label' => __('Content Animation', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ha_fadeInUp',
                'options' => [
                    'none'  => __('None', 'happy-addons-pro'),
                    'ha_fadeInUp'  => __('FadeInUp', 'happy-addons-pro'),
                    'ha_fadeInDown' => __('FadeInDown', 'happy-addons-pro'),
                    'ha_fadeInLeft' => __('FadeInLeft', 'happy-addons-pro'),
                    'ha_fadeInRight' => __('FadeInRight', 'happy-addons-pro'),
                    'ha_zoomIn' => __('ZoomIn', 'happy-addons-pro'),
                    'ha_rollIn' => __('RollIn', 'happy-addons-pro'),
                ],
                'condition' => [
                    'slider_type!' => 'multiple',
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'content_animation_speed',
            [
                'label' => __('Animation Speed (ms)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['ms'],
                'range' => [
                    'ms' => [
                        'min' => 100,
                        'max' => 5000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 1250,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content' => 'animation-duration: {{SIZE}}{{UNIT}};',
                ],
                'description' => __('Slide speed in miliseconds', 'happy-addons-pro'),
                'condition' => [
                    'slider_type!' => 'multiple',
                    'slider_content_animation!' => 'none',
                ],

            ]
        );

        $this->add_control(
            'arrow_navigation',
            [
                'label' => __('Arrow Navigation?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'arrow_navigation_prev',
            [
                'label' => __('Previous Icon', 'happy-addons-pro'),
                'label_block' => false,
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'default' => [
                    'value' => 'hm hm-play-previous',
                    'library' => 'happy-icons',
                ],
                'condition' => [
                    'arrow_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrow_navigation_next',
            [
                'label' => __('Next Icon', 'happy-addons-pro'),
                'label_block' => false,
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'default' => [
                    'value' => 'hm hm-play-next',
                    'library' => 'happy-icons',
                ],
                'condition' => [
                    'arrow_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __('Pagination Type', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none'  => __('None', 'happy-addons-pro'),
                    'dots'  => __('Dots', 'happy-addons-pro'),
                    'numbers' => __('Numbers', 'happy-addons-pro'),
                    'progressbar' => __('Progressbar', 'happy-addons-pro'),
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'number_pagination_type',
            [
                'label' => __('Number Type', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'bullets',
                'options' => [
                    'bullets'  => __('Bullets', 'happy-addons-pro'),
                    'fraction' => __('Fraction', 'happy-addons-pro'),
                ],
                'condition' => [
                    'pagination_type' => 'numbers',
                ],
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'scroll_bar',
            [
                'label' => __('Scroll Bar?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'no',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'scroll_bar_visibility',
            [
                'label' => __('Scroll Bar', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'false',
                'options' => [
                    'false'  => __('Always show', 'happy-addons-pro'),
                    'true' => __('Automatic hide', 'happy-addons-pro'),
                ],
                'condition' => [
                    'scroll_bar' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'thumbs_navigation',
            [
                'label' => __('Thumbnail Navigation?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'no',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'space_between_thumbs',
            [
                'label' => __('Space Between Thumbs', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => 10,
                'condition' => [
                    'thumbs_navigation' => 'yes',
                ],
                'frontend_available' => true,
                'description' => esc_html__('Thumbs space in pixel(px)', 'happy-addons-pro'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'infinity_loop',
            [
                'label' => __('Infinity Loop?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => true,
                'default' => true,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay Speed', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 15000,
                'step' => 5,
                'default' => 5000,
                'description' => __('Autoplay speed in milliseconds', 'happy-addons-pro'),
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'frontend_available' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget style controls
     */
    protected function register_style_controls() {
        $this->__slider_content_style_controls();
        $this->__icon_image_style_controls();
        $this->__title_style_controls();
        $this->__sub_title_style_controls();
        $this->__desc_style_controls();
        $this->__button_style_controls();
        $this->__arrow_style_controls();
        $this->__dots_style_controls();
        $this->__pagination_number_style_controls();
        $this->__pagination_progressbar_style_controls();
        $this->__scroll_bar_style_controls();
        $this->__nav_thumbnails_style_controls();
    }

    protected function __slider_content_style_controls() {

        $this->start_controls_section(
            '_section_slider_style',
            [
                'label' => __('Slider Content', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slider_content_width',
            [
                'label' => __('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 1500,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.1,
                        'max' => 15,
                        'step' => 0.1,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'tablet_default' => [],
                'mobile_default' => [
                    'size' => 70,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slider_content_margin',
            [
                'label' => __('Margin', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slider_content_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'slide_content_horizontal_align',
            [
                'label' => __('Horizontal Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-wrapper' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'slide_content_vertical_align',
            [
                'label' => __('Vertical Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Top', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-wrapper' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'slide_content_text_align',
            [
                'label' => __('Text Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'slide_content_text_shadow',
                'label' => __('Text Shadow', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-slider-content .ha-slider-content-title, {{WRAPPER}} .ha-slider-content .ha-slider-content-sub-title, {{WRAPPER}} .ha-slider-content .ha-slider-content-description, {{WRAPPER}} .ha-slider-content .ha-slider-button',
            ]
        );

        $this->end_controls_section();
    }

    protected function __icon_image_style_controls() {

        $this->start_controls_section(
            '_section_content_icon_style',
            [
                'label' => __('Icon/ Image', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Size (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-figure' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => __('Padding (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-figure' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label' => __('Bottom Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 150,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-figure' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'selector' => '{{WRAPPER}} .ha-slider-figure'
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-figure, {{WRAPPER}} .ha-slider-figure img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .ha-slider-figure'
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-figure' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-figure' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function __title_style_controls() {

        $this->start_controls_section(
            '_section_content_title_style',
            [
                'label' => __('Title', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __('Bottom Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 150,
                    ]
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'tablet_default' => [],
                'mobile_default' => [],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title',
                'selector' => '{{WRAPPER}} .ha-slider-content-title',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->end_controls_section();
    }

    protected function __sub_title_style_controls() {

        $this->start_controls_section(
            '_section_content_sub_title_style',
            [
                'label' => __('Sub Title', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'sub_title_spacing',
            [
                'label' => __('Bottom Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 150,
                    ]
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'tablet_default' => [],
                'mobile_default' => [],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sub_title_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title',
                'selector' => '{{WRAPPER}} .ha-slider-content-sub-title',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->end_controls_section();
    }

    protected function __desc_style_controls() {

        $this->start_controls_section(
            '_section_content_description_style',
            [
                'label' => __('Description', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'description_spacing',
            [
                'label' => __('Bottom Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 150,
                    ]
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'tablet_default' => [],
                'mobile_default' => [],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-content-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description',
                'selector' => '{{WRAPPER}} .ha-slider-content-description',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->end_controls_section();
    }

    protected function __button_style_controls() {

        $this->start_controls_section(
            '_section_content_button_style',
            [
                'label' => __('Button', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_between_space',
            [
                'label' => __('Button Between Space (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'tablet_default' => [],
                'mobile_default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '(desktop){{WRAPPER}} .ha-slider-buttons .button-1' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2) 0 0;',
                    '(desktop){{WRAPPER}} .ha-slider-buttons .button-2' => 'margin: 0 0 0 calc({{SIZE}}{{UNIT}}/2);',
                    '(mobile){{WRAPPER}} .ha-slider-buttons .button-1' => 'margin: 0 0 calc({{SIZE}}{{UNIT}}/2) 0;',
                    '(mobile){{WRAPPER}} .ha-slider-buttons .button-2' => 'margin: calc({{SIZE}}{{UNIT}}/2) 0 0 0;',
                ],
            ]
        );

        $this->add_control(
            'slider_content_button_1_heading',
            [
                'label' => __('Button 1', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_responsive_control(
            'button_1_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_1_border',
                'selector' => '{{WRAPPER}} .ha-slider-buttons .button-1'
            ]
        );

        $this->add_responsive_control(
            'button_1_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_1_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-slider-buttons .button-1',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_1_box_shadow',
                'selector' => '{{WRAPPER}} .ha-slider-buttons .button-1'
            ]
        );

        $this->start_controls_tabs('_tabs_button_1');

        $this->start_controls_tab(
            '_tab_button_1_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'button_1_text_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_1_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tabs_button_1_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'button_1_hover_text_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_1_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#47B7F0',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_1_hover_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-1:hover' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'button_1_border_border!' => ''
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'slider_content_button_2_heading',
            [
                'label' => __('Button 2', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_responsive_control(
            'button_2_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_2_border',
                'selector' => '{{WRAPPER}} .ha-slider-buttons .button-2'
            ]
        );

        $this->add_responsive_control(
            'button_2_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_2_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-slider-buttons .button-2',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_2_box_shadow',
                'selector' => '{{WRAPPER}} .ha-slider-buttons .button-2'
            ]
        );

        $this->start_controls_tabs('_tabs_button_2');

        $this->start_controls_tab(
            '_tab_button_2_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'button_2_text_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_2_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#943FF8',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tabs_button_2_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'button_2_hover_text_color',
            [
                'label' => __('Text Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_2_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F5E897',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_2_hover_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-buttons .button-2:hover' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'button_2_border_border!' => ''
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __arrow_style_controls() {

        $this->start_controls_section(
            '_section_navigation_arrow_style',
            [
                'label' => __('Navigation - Arrow', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'arrow_navigation' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'arrow_position_toggle',
            [
                'label' => __('Position', 'happy-addons-pro'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'happy-addons-pro'),
                'label_on' => __('Custom', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'arrow_sync_position',
            [
                'label' => __('Sync Position', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'yes' => [
                        'title' => __('Yes', 'happy-addons-pro'),
                        'icon' => 'eicon-sync',
                    ],
                    'no' => [
                        'title' => __('No', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-stretch',
                    ]
                ],
                'condition' => [
                    'arrow_position_toggle' => 'yes'
                ],
                'default' => 'no',
                'toggle' => false,
                'prefix_class' => 'ha-arrow-sync-'
            ]
        );

        $this->add_responsive_control(
            'arrow_position_y',
            [
                'label' => __('Vertical (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'arrow_position_toggle' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-horizontal .ha-slider-prev, {{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-horizontal .ha-slider-next' => 'top: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-vertical .ha-slider-prev' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
                    '{{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-vertical .ha-slider-next' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',

                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-horizontal .ha-slider-prev, {{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-horizontal .ha-slider-next' => 'top: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-vertical .ha-slider-prev, {{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-vertical .ha-slider-next' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_position_x',
            [
                'label' => __('Horizontal (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'arrow_position_toggle' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-horizontal .ha-slider-prev' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
                    '{{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-horizontal .ha-slider-next' => 'right: {{SIZE}}{{UNIT}}; left: auto;',

                    '{{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-vertical .ha-slider-prev, {{WRAPPER}}.ha-arrow-sync-no .ha-slider-direction-vertical .ha-slider-next' => 'left: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-horizontal .ha-slider-prev, {{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-horizontal .ha-slider-next' => 'left: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-vertical .ha-slider-prev, {{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-vertical .ha-slider-next' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_spacing',
            [
                'label' => __('Space Between Arrows (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'arrow_position_toggle' => 'yes',
                    'arrow_sync_position' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-horizontal .ha-slider-next' => 'margin-left: calc({{SIZE}}{{UNIT}}/ 2);',
                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-horizontal .ha-slider-prev' => 'margin-right: calc({{SIZE}}{{UNIT}}/ 2);',

                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-vertical .ha-slider-next' => 'margin-top: calc({{SIZE}}{{UNIT}}/ 2);',
                    '{{WRAPPER}}.ha-arrow-sync-yes .ha-slider-direction-vertical .ha-slider-prev' => 'margin-bottom: calc({{SIZE}}{{UNIT}}/ 2);',
                ],
            ]
        );

        $this->end_popover();

        $this->add_control(
            'arrow_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-next, {{WRAPPER}} .ha-slider-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_icon_size',
            [
                'label' => __('Icon Size (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-slider-next' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrow_border',
                'selector' => '{{WRAPPER}} .ha-slider-prev, {{WRAPPER}} .ha-slider-next',
            ]
        );

        $this->add_responsive_control(
            'arrow_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev, {{WRAPPER}} .ha-slider-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->start_controls_tabs('_tabs_arrow');

        $this->start_controls_tab(
            '_tab_arrow_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev, {{WRAPPER}} .ha-slider-next' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev, {{WRAPPER}} .ha-slider-next' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_arrow_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF96',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev:hover, {{WRAPPER}} .ha-slider-next:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev:hover, {{WRAPPER}} .ha-slider-next:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'arrow_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-prev:hover, {{WRAPPER}} .ha-slider-next:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __dots_style_controls() {

        $this->start_controls_section(
            '_section_pagination_dots_style',
            [
                'label' => __('Pagination - Dots', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type' => 'dots',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_position_y',
            [
                'label' => __('Vertical Position (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 800,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_spacing',
            [
                'label' => __('Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_size',
            [
                'label' => __('Size (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_align',
            [
                'label' => __('Alignment', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => true,
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination' => 'justify-content: {{VALUE}}'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'dots_nav_box_shadow',
                'label' => __('Box Shadow', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-slider-pagination span',
            ]
        );

        $this->start_controls_tabs('_tabs_dots');
        $this->start_controls_tab(
            '_tab_dots_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'dots_nav_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_dots_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'dots_nav_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_dots_active',
            [
                'label' => __('Active', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'dots_nav_active_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span.swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __pagination_number_style_controls() {

        $this->start_controls_section(
            '_section_pagination_number_style',
            [
                'label' => __('Pagination - Number', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type' => 'numbers',
                ],
            ]
        );


        $this->add_responsive_control(
            'numbers_nav_position_y',
            [
                'label' => __('Vertical Position (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 800,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'numbers_nav_spacing',
            [
                'label' => __('Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'numbers_nav_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'selector' => '{{WRAPPER}} .ha-slider-pagination span',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'numbers_nav_box_shadow',
                'label' => __('Box Shadow', 'happy-addons-pro'),
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
                'selector' => '{{WRAPPER}} .ha-slider-pagination span',
            ]
        );

        $this->add_responsive_control(
            'numbers_nav_align',
            [
                'label' => __('Alignment', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => true,
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination' => 'justify-content: {{VALUE}}'
                ]
            ]
        );

        $this->start_controls_tabs('_tabs_numbers');
        $this->start_controls_tab(
            '_tab_numbers_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F5F5F540',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_numbers_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#3871E8',
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_numbers_active',
            [
                'label' => __('Active', 'happy-addons-pro'),
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_active_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span.swiper-pagination-bullet-active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'numbers_nav_active_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#3871E8',
                'condition' => [
                    'number_pagination_type' => 'bullets'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination span.swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __pagination_progressbar_style_controls() {

        $this->start_controls_section(
            '_section_pagination_progressbar_style',
            [
                'label' => __('Pagination - Progressbar', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type' => 'progressbar',
                ],
            ]
        );

        $this->add_responsive_control(
            'progressbar_height',
            [
                'label' => __('Height (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination.swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}}; width: 100%',
                ],
                'condition' => [
                    'slider_direction' => 'horizontal',
                ],
            ]
        );

        $this->add_responsive_control(
            'progressbar_width',
            [
                'label' => __('Width (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination.swiper-pagination-progressbar' => 'width: {{SIZE}}{{UNIT}}; height: 100%',
                ],
                'condition' => [
                    'slider_direction' => 'vertical',
                ],
            ]
        );

        $this->start_controls_tabs('_tabs_progressbar');
        $this->start_controls_tab(
            '_tab_progressbar_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'progressbar_nav_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-pagination.swiper-pagination-progressbar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_progressbar_active',
            [
                'label' => __('Active', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'progressbar_nav_active_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ECDA6A',
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __scroll_bar_style_controls() {

        $this->start_controls_section(
            '_section_scroll_bar_style',
            [
                'label' => __('Scroll Bar', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'scroll_bar' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'scrollbar_height',
            [
                'label' => __('Height (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-scrollbar.swiper-scrollbar' => 'height: {{SIZE}}{{UNIT}}; width: 100%',
                ],
                'condition' => [
                    'slider_direction' => 'horizontal',
                ],
            ]
        );

        $this->add_responsive_control(
            'scrollbar_width',
            [
                'label' => __('Width (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-scrollbar.swiper-scrollbar' => 'width: {{SIZE}}{{UNIT}}; height: 100%',
                ],
                'condition' => [
                    'slider_direction' => 'vertical',
                ],
            ]
        );

        $this->start_controls_tabs('_tabs_scrollbar');
        $this->start_controls_tab(
            '_tab_scrollbar_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'scrollbar_nav_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-scrollbar.swiper-scrollbar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_scrollbar_active',
            [
                'label' => __('Active', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'scrollbar_nav_active_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ECDA6A',
                'selectors' => [
                    '{{WRAPPER}} .swiper-scrollbar-drag' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __nav_thumbnails_style_controls() {

        $this->start_controls_section(
            '_section_thumbs_navigation_style',
            [
                'label' => __('Navigation - Thumbnails', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'thumbs_navigation' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbs_top_spacing',
            [
                'label' => __('Top Spacing (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-gallery-thumbs' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbs_align',
            [
                'label' => __('Alignment', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => true,
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-gallery-thumbs .swiper-wrapper' => 'justify-content: {{VALUE}}'
                ]
            ]
        );

        $this->add_responsive_control(
            'thumbs_height',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 500,
                        'step' => 5,
                    ],
                    'em' => [
                        'min' => 0.1,
                        'max' => 16,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-gallery-slide' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbs_width',
            [
                'label' => __('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 500,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.1,
                        'max' => 16,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 150,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-gallery-slide' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'thumbs_border',
                'label' => __('Border', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-slider-gallery-slide',
            ]
        );

        $this->add_control(
            'thumbs_active_border_color',
            [
                'label' => __('Active Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-slider-gallery-slide.swiper-slide-thumb-active' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'thumbs_border_border!' => ''
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $animation_class = (isset($settings['slider_content_animation']) && ($settings['slider_content_animation'] != 'none')) ? $settings['slider_content_animation'] : '';
?>
        <div class="ha-slider-widget-wrapper ha-unique-widget-id-<?php echo esc_attr($this->get_id()); ?> ha-slider-direction-<?php echo esc_attr($settings['slider_direction']); ?>">
            <div class="swiper-container gallery-top ha-slider-container">
                <div class="swiper-wrapper ha-slider-wrapper">
                    <?php if (is_array($settings['slides'])) :
                        foreach ($settings['slides'] as $slide) :
                    ?>
                            <div class="swiper-slide ha-slider-slide elementor-repeater-item-<?php echo $slide['_id']; ?>">
                                <?php if ($slide['content_type'] == 'template') :
                                    echo ha_elementor()->frontend->get_builder_content_for_display($slide['slide_content_template']);
                                elseif ($slide['content_type'] == 'default') : ?>
                                    <div class="ha-slider-content-wrapper elementor-repeater-item-<?php echo $slide['_id']; ?>">
                                        <div class="ha-slider-content elementor-repeater-item-<?php echo $slide['_id']; ?> <?php echo esc_attr($animation_class); ?>">
                                            <?php if ($slide['slide_content_icon'] === 'image' && ($slide['image']['url'] || $slide['image']['id'])) :
                                                $slide['hover_animation'] = 'disable-animation'; // hack to prevent image hover animation
                                            ?>
                                                <figure class="ha-slider-figure ha-slider-figure--image">
                                                    <?php echo Group_Control_Image_Size::get_attachment_image_html($slide, 'thumbnail', 'image'); ?>
                                                </figure>
                                            <?php elseif (!empty($slide['icon']['value'])) : ?>
                                                <figure class="ha-slider-figure ha-slider-figure--icon">
                                                    <?php Icons_Manager::render_icon($slide['icon']); ?>
                                                </figure>
                                            <?php endif; ?>

                                            <?php if (!empty($slide['slide_content_title'])) : ?>
                                                <h2 class="ha-slider-content-title"><?php echo esc_html($slide['slide_content_title']); ?></h2>
                                            <?php endif; ?>
                                            <?php if (!empty($slide['slide_content_sub_title'])) : ?>
                                                <h3 class="ha-slider-content-sub-title"><?php echo esc_html($slide['slide_content_sub_title']); ?></h3>
                                            <?php endif; ?>
                                            <?php if (!empty($slide['slide_content_description'])) : ?>
                                                <div class="ha-slider-content-description"><?php echo esc_html($slide['slide_content_description']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($slide['slide_content_button_1_text']) || !empty($slide['slide_content_button_2_text'])) : ?>
                                                <div class="ha-slider-buttons">
                                                    <?php if (!empty($slide['slide_content_button_1_text'])) : ?>
                                                        <a class="ha-slider-button button-1" href="<?php echo esc_url(isset($slide['slide_content_button_1_link']['url']) ? $slide['slide_content_button_1_link']['url'] : ''); ?>" <?php echo esc_attr(($slide['slide_content_button_1_link']['is_external']) ? 'target="_blank"' : ''); ?>><?php echo esc_html($slide['slide_content_button_1_text']); ?></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($slide['slide_content_button_2_text'])) : ?>
                                                        <a class="ha-slider-button button-2" href="<?php echo esc_url(isset($slide['slide_content_button_2_link']['url']) ? $slide['slide_content_button_2_link']['url'] : ''); ?>" <?php echo esc_attr(($slide['slide_content_button_2_link']['is_external']) ? 'target="_blank"' : ''); ?>><?php echo esc_html($slide['slide_content_button_2_text']); ?></a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <?php if (!empty($settings['pagination_type']) && ($settings['pagination_type'] != 'none')) : ?>
                    <div class="swiper-pagination ha-slider-pagination"></div>
                <?php endif; ?>
                <?php if (!empty($settings['scroll_bar']) && ($settings['scroll_bar'] == 'yes')) : ?>
                    <div class="swiper-scrollbar ha-slider-scrollbar"></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($settings['arrow_navigation']) && ($settings['arrow_navigation'] == 'yes')) : ?>
                <div class="ha-slider-prev"><?php Icons_Manager::render_icon($settings['arrow_navigation_prev'], ['aria-hidden' => 'true']); ?></div>
                <div class="ha-slider-next"><?php Icons_Manager::render_icon($settings['arrow_navigation_next'], ['aria-hidden' => 'true']); ?></div>
            <?php endif; ?>


            <?php if (!empty($settings['thumbs_navigation']) && ($settings['thumbs_navigation'] == 'yes')) : ?>
                <div class="swiper-container ha-slider-gallery-thumbs">
                    <div class="swiper-wrapper">
                        <?php if (is_array($settings['slides'])) :
                            foreach ($settings['slides'] as $slide) :
                        ?>
                                <div class="swiper-slide ha-slider-gallery-slide elementor-repeater-item-<?php echo $slide['_id']; ?>">
                                    <?php if ($slide['content_type'] == 'template') :
                                        echo ha_elementor()->frontend->get_builder_content_for_display($slide['slide_content_template']);
                                    endif; ?>
                                </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

<?php
    }
}
