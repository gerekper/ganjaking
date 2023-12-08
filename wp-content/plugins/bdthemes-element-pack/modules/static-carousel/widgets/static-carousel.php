<?php

namespace ElementPack\Modules\StaticCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;

use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Mask_Controls;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Static_Carousel extends Module_Base
{

    use Global_Swiper_Controls;
    use Global_Mask_Controls;

    public function get_name()
    {
        return 'bdt-static-carousel';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('Static Carousel', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-static-carousel';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['interactive', 'image', 'services', 'card', 'box', 'features', 'static', 'client', 'carousel', 'slider'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-static-carousel'];
        }
    }
    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-static-carousel'];
        }
    }

    public function get_custom_help_url()
    {
        return 'https://youtu.be/8A2a8ws6364';
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'section_carousel_content',
            [
                'label' => __('Items', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'render_type' => 'template',
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your title', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'sub_title',
            [
                'label' => __('Sub Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your sub title', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'text',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
                'placeholder' => __('Enter your text', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'readmore_link',
            [
                'label' => esc_html__('Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'placeholder' => 'http://your-link.com',
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'carousel_items',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default' => [
                    [
                        'title' => __('This is a title', 'bdthemes-element-pack'),
                        'sub_title' => __('Sub Title', 'bdthemes-element-pack'),
                    ],
                    [
                        'title' => __('This is a title', 'bdthemes-element-pack'),
                        'sub_title' => __('Sub Title', 'bdthemes-element-pack'),
                    ],
                    [
                        'title' => __('This is a title', 'bdthemes-element-pack'),
                        'sub_title' => __('Sub Title', 'bdthemes-element-pack'),
                    ],
                    [
                        'title' => __('This is a title', 'bdthemes-element-pack'),
                        'sub_title' => __('Sub Title', 'bdthemes-element-pack'),
                    ],
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_carousel_additional_settings',
            [
                'label' => __('Additional Settings', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                ],
            ]
        );

        $this->add_control(
            'item_gap',
            [
                'label' => __('Item Gap', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
            ]
        );

        $this->add_control(
            'item_match_height',
            [
                'label' => __('Item Match Height', 'ultimate-post-kit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'prefix_class' => 'bdt-item-match-height--',
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Show Name', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_title' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'show_sub_title',
            [
                'label' => __('Show Sub Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'sub_title_tag',
            [
                'label' => __('Sub Title HTML Tag', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h4',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_sub_title' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'show_text',
            [
                'label' => __('Show Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'readmore_link_to',
            [
                'label' => __('Link to', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SELECT,
                'default' => 'button',
                'options' => [
                    'button' => __('Button', 'bdthemes-element-pack'),
                    'title' => __('Title', 'bdthemes-element-pack'),
                    'image' => __('Image', 'bdthemes-element-pack'),
                    'item' => __('Item Wrapper', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => __('Show Iamge', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail_size',
                'default' => 'medium',
                'condition' => [
                    'show_image' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'image_mask_popover',
            [
                'label' => esc_html__('Image Mask', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'render_type' => 'template',
                'return_value' => 'yes',
                'condition' => [
                    'show_image' => 'yes'
                ]
            ]
        );

        //Global Image Mask Controls
        $this->register_image_mask_controls();

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-item' => 'text-align: {{VALUE}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_readmore',
            [
                'label' => esc_html__('Read More', 'bdthemes-element-pack'),
                'condition' => [
                    'readmore_link_to' => 'button',
                ],
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label' => esc_html__('Read More Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read More', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_icon',
            [
                'label' => esc_html__('Icon', 'bdthemes-element-pack'),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline'
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label' => esc_html__('Icon Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'right',
                'toggle' => false,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'condition' => [
                    'readmore_icon[value]!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_indent',
            [
                'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 8,
                ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'readmore_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore .bdt-button-icon-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        //Navigation Controls
        $this->start_controls_section(
            'section_content_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
            ]
        );

        //Global Navigation Controls
        $this->register_navigation_controls();

        $this->end_controls_section();

        //Global Carousel Settings Controls
        $this->register_carousel_settings_controls();

        //Style
        $this->start_controls_section(
            'section_style_carousel_items',
            [
                'label' => esc_html__('Items', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_padding',
            [
                'label' => esc_html__('Content Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-item',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-item',
            ]
        );

        $this->add_responsive_control(
            'item_shadow_padding',
            [
                'label' => __('Match Padding', 'bdthemes-element-pack'),
                'description' => __('You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 50,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_hover_background',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-item:hover',
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-image img'
            ]
        );

        $this->add_control(
            'iamge_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'iamge_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_spacing',
            [
                'label' => __('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-image img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'img_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-image img'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_bottom_space',
            [
                'label' => __('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_shadow',
                'label' => __('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_sub_title',
            [
                'label' => __('Sub Title', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_sub_title' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'sub_title_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sub_title_bottom_space',
            [
                'label' => __('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-sub-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-sub-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_text',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_text' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_readmore',
            [
                'label' => esc_html__('Read More', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'readmore_link_to' => 'button',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_readmore_style');

        $this->start_controls_tab(
            'tab_readmore_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'readmore_background',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-readmore',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'readmore_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-readmore',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'readmore_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'readmore_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-readmore',
            ]
        );

        $this->add_responsive_control(
            'readmore_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'readmore_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'readmore_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-readmore',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_readmore_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_hover_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'readmore_hover_background',
                'selector' => '{{WRAPPER}} .bdt-ep-static-carousel-readmore:hover',
            ]
        );

        $this->add_control(
            'readmore_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'readmore_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-static-carousel-readmore:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_animation',
            [
                'label' => esc_html__('Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        //Navigation Style
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'navigation',
                            'operator' => '!=',
                            'value' => 'none',
                        ],
                        [
                            'name' => 'show_scrollbar',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        //Global Navigation Style Controls
        $this->register_navigation_style_controls('swiper-carousel');

        $this->end_controls_section();

    }

    public function render_image($item, $image_key)
    {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_image']) {
            return;
        }

        $this->add_render_attribute($image_key, 'class', 'bdt-ep-static-carousel-image-link bdt-position-z-index', true);
        if (!empty($item['readmore_link']['url'])) {
            $this->add_link_attributes($image_key, $item['readmore_link']);
        }

        $image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
        $this->add_render_attribute('image-wrap', 'class', 'bdt-ep-static-carousel-image' . $image_mask);
        ?>
        <div <?php echo $this->get_render_attribute_string('image-wrap'); ?>>

            <?php
            $thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
            if (!$thumb_url) {
                printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['title']));
            } else {
                print(
                    wp_get_attachment_image(
                        $item['image']['id'],
                        $settings['thumbnail_size_size'],
                        false,
                        [
                            'alt' => esc_html($item['title'])
                        ]
                    )
                );
            }
            ?>

            <?php if ($settings['readmore_link_to'] == 'image'): ?>
                <a <?php echo $this->get_render_attribute_string($image_key); ?>></a>
            <?php endif; ?>

        </div>
        <?php
    }

    public function render_title($item, $title_key)
    {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_title']) {
            return;
        }

        $this->add_render_attribute($title_key, 'class', 'bdt-ep-static-carousel-title-link', true);
        if (!empty($item['readmore_link']['url'])) {
            $this->add_link_attributes($title_key, $item['readmore_link']);
        }

        $this->add_render_attribute('title-wrap', 'class', 'bdt-ep-static-carousel-title', true);

        ?>
        <?php if ($item['title']): ?>
            <<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?>
                <?php echo $this->get_render_attribute_string('title-wrap'); ?>>
                <?php echo wp_kses($item['title'], element_pack_allow_tags('title')); ?>
                <?php if ($settings['readmore_link_to'] == 'title'): ?>
                    <a <?php echo $this->get_render_attribute_string($title_key); ?>></a>
                <?php endif; ?>
            </<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?>>
        <?php endif; ?>
    <?php
    }

    public function render_sub_title($item)
    {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_sub_title']) {
            return;
        }

        $this->add_render_attribute('sub-title-wrap', 'class', 'bdt-ep-static-carousel-sub-title', true);

        ?>
        <?php if ($item['sub_title']): ?>
            <<?php echo Utils::get_valid_html_tag($settings['sub_title_tag']); ?>
                <?php echo $this->get_render_attribute_string('sub-title-wrap'); ?>>
                <?php echo wp_kses($item['sub_title'], element_pack_allow_tags('sub_title')); ?>
            </<?php echo Utils::get_valid_html_tag($settings['sub_title_tag']); ?>>
        <?php endif; ?>
    <?php
    }

    public function render_text($item)
    {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_text']) {
            return;
        }

        ?>
        <?php if ($item['text']): ?>
            <div class="bdt-ep-static-carousel-text">
                <?php echo wp_kses_post($item['text']); ?>
            </div>
        <?php endif; ?>
    <?php
    }

    public function render_readmore($item, $readmore_key)
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute(
            [
                $readmore_key => [
                    'class' => [
                        'bdt-ep-static-carousel-readmore',
                        $settings['readmore_hover_animation'] ? 'elementor-animation-' . $settings['readmore_hover_animation'] : '',
                    ],
                ]
            ],
            '',
            '',
            true
        );
        if (!empty($item['readmore_link']['url'])) {
            $this->add_link_attributes($readmore_key, $item['readmore_link']);
        }

        ?>
        <?php if ((!empty($item['readmore_link']['url'])) && ($settings['readmore_link_to'] == 'button')): ?>
            <div class="bdt-ep-static-carousel-readmore-wrap">
                <a <?php echo $this->get_render_attribute_string($readmore_key); ?>>
                    <?php echo esc_html($settings['readmore_text']); ?>
                    <?php if ($settings['readmore_icon']['value']): ?>
                        <span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">
                            <?php Icons_Manager::render_icon($settings['readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif; ?>
    <?php
    }

    public function render_carosuel_item()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['carousel_items'])) {
            return;
        }

        $this->add_render_attribute('carosuel-item', 'class', 'bdt-ep-static-carousel-item swiper-slide', true);

        ?>

        <?php foreach ($settings['carousel_items'] as $index => $item):
            $item_key = 'item-' . $index;

            $this->add_render_attribute($item_key, 'class', 'bdt-ep-static-carousel-item-link bdt-position-z-index', true);
            if (!empty($item['readmore_link']['url'])) {
                $this->add_link_attributes($item_key, $item['readmore_link']);
            }

            ?>
            <div <?php echo $this->get_render_attribute_string('carosuel-item'); ?>>
                <?php $this->render_image($item, 'image_' . $index); ?>
                <div class="bdt-ep-static-carousel-content">
                    <?php $this->render_sub_title($item); ?>
                    <?php $this->render_title($item, 'title_' . $index); ?>
                    <?php $this->render_text($item); ?>
                    <?php $this->render_readmore($item, 'link_' . $index); ?>
                </div>

                <?php if ($settings['readmore_link_to'] == 'item'): ?>
                    <a <?php echo $this->get_render_attribute_string($item_key); ?>></a>
                <?php endif; ?>
            </div>
        <?php endforeach;
    }

    public function render_header()
    {
        $settings = $this->get_settings_for_display();

        //Global Function
        $this->render_swiper_header_attribute('static-carousel');

        $this->add_render_attribute('carousel', 'class', 'bdt-static-carousel');

        ?>
        <div <?php echo $this->get_render_attribute_string('carousel'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
                    <?php
    }

    public function render()
    {
        $this->render_header();
        $this->render_carosuel_item();
        $this->render_footer();
    }
}
