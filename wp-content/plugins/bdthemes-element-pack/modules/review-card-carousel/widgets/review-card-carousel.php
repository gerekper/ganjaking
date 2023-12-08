<?php

namespace ElementPack\Modules\ReviewCardCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Repeater;
use ElementPack\Utils;

use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Mask_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Review_Card_Carousel extends Module_Base {

    use Global_Swiper_Controls;
    use Global_Mask_Controls;

    public function get_name() {
        return 'bdt-review-card-carousel';
    }

    public function get_title() {
        return BDTEP . esc_html__('Review Card Carousel', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-review-card-carousel';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['interactive', 'review', 'image', 'services', 'card', 'box', 'features', 'testimonial', 'client', 'carosul', 'slider'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-review-card-carousel'];
        }
    }
    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-review-card-carousel'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/7kMyajVai6E';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_reviewer_content',
            [
                'label' => __('Review Card Items', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image',
            [
                'label'       => __('Image', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::MEDIA,
                'render_type' => 'template',
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'reviewer_name',
            [
                'label'       => __('Name', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => __('Adam Smith', 'bdthemes-element-pack'),
                'placeholder' => __('Enter reviewer name', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'reviewer_job_title',
            [
                'label'       => __('Job Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => __('SEO Expert', 'bdthemes-element-pack'),
                'placeholder' => __('Enter reviewer job title', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'rating_number',
            [
                'label' => __('Rating', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default' => [
                    'size' => 4.5,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .5,
                    ],
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'review_text',
            [
                'label'       => __('Review Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::WYSIWYG,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
                'placeholder' => __('Enter review text', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'review_items',
            [
                'show_label'  => false,
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ reviewer_name }}}',
                'default'     => [
                    [
                        'reviewer_name' => __('Adam Smith', 'bdthemes-element-pack'),
                        'reviewer_job_title' => __('SEO Expert', 'bdthemes-element-pack'),
                    ],
                    [
                        'reviewer_name' => __('Jhon Deo', 'bdthemes-element-pack'),
                        'reviewer_job_title' => __('Web Desiger', 'bdthemes-element-pack'),
                    ],
                    [
                        'reviewer_name' => __('Maria Mak', 'bdthemes-element-pack'),
                        'reviewer_job_title' => __('Web Expert', 'bdthemes-element-pack'),
                    ],
                    [
                        'reviewer_name' => __('Jackma Kalin', 'bdthemes-element-pack'),
                        'reviewer_job_title' => __('Elementor Expert', 'bdthemes-element-pack'),
                    ],
                    [
                        'reviewer_name' => __('Amily Moalin', 'bdthemes-element-pack'),
                        'reviewer_job_title' => __('WP Officer', 'bdthemes-element-pack'),
                    ],
                    [
                        'reviewer_name' => __('Enagol Ame', 'bdthemes-element-pack'),
                        'reviewer_job_title' => __('WP Developer', 'bdthemes-element-pack'),
                    ],
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_review_additional_settings',
            [
                'label' => __('Additional Settings', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => __('Columns', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SELECT,
                'default'        => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options'        => [
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
                'label'   => __('Item Gap', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range'   => [
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
                'label'        => __('Item Match Height', 'ultimate-post-kit'),
                'type'         => Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'prefix_class' => 'bdt-item-match-height--',
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'show_reviewer_name',
            [
                'label'   => __('Show Name', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'review_name_tag',
            [
                'label'   => __('Name HTML Tag', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_reviewer_name' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'show_reviewer_job_title',
            [
                'label'   => __('Show Job Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label'   => __('Show Rating', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'rating_type',
            [
                'label'   => __('Rating Type', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'star',
                'options' => [
                    'star'   => __('Star', 'bdthemes-element-pack'),
                    'number' => __('Number', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'show_rating' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'rating_position',
            [
                'label'   => __('Rating Position', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => [
                    'before' => __('Before Review Text', 'bdthemes-element-pack'),
                    'after' => __('After Review Text', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'show_rating' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'show_review_text',
            [
                'label'   => __('Show Review Text', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_reviewer_image',
            [
                'label'   => __('Show Image', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'    => 'thumbnail_size',
                'default' => 'medium',
                'condition' => [
                    'show_reviewer_image' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'image_position',
            [
                'label'     => __('Image Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'top',
                'toggle' => false,
                'options'   => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'top' => [
                        'title' => __('Top', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'bdt-review-img--',
                'render_type' => 'template',
                'condition' => [
                    'show_reviewer_image' => 'yes'
                ]
            ]
        );

        $this->add_control(
			'image_inline',
			[
				'label'        => esc_html__('Image Inline', 'bdthemes-element-pack') . BDTEP_NC,
				'type'         => Controls_Manager::SWITCHER,
				'condition'    => [
					'image_position' => ['left', 'right'],
                    'show_reviewer_image' => 'yes'
				],
                'prefix_class' => 'bdt-review-img-inline--',
                'render_type' => 'template',
			]
		);

        $this->add_responsive_control(
            'image_alignment',
            [
                'label'     => __('Image Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'center',
                'toggle' => false,
                'options'   => [
                    'flex-start' => [
                        'title' => __('Top', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-image' => 'align-self: {{VALUE}};',
                ],
                'condition' => [
                    'image_position' => ['left', 'right'],
                    'image_inline!' => 'yes',
                    'show_reviewer_image' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'image_mask_popover',
            [
                'label'        => esc_html__('Image Mask', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'render_type'  => 'template',
                'return_value' => 'yes',
                'condition' => [
                    'show_reviewer_image' => 'yes'
                ]
            ]
        );

        //Global Image Mask Controls
        $this->register_image_mask_controls();

        $this->add_responsive_control(
            'text_align',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'    => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item' => 'text-align: {{VALUE}};',
                ],
                'separator' => 'before'
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
            'section_style_review_items',
            [
                'label'     => esc_html__('Items', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
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
                'name'      => 'item_background',
                'selector'  => '{{WRAPPER}} .bdt-ep-review-card-carousel-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'item_border',
                'label'          => esc_html__('Border', 'bdthemes-element-pack'),
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width'  => [
                        'default' => [
                            'top'      => '1',
                            'right'    => '1',
                            'bottom'   => '1',
                            'left'     => '1',
                            'isLinked' => false,
                        ],
                    ],
                    'color'  => [
                        'default' => '#eee',
                    ],
                ],
                'selector'       => '{{WRAPPER}} .bdt-ep-review-card-carousel-item',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-item',
            ]
        );

        $this->add_responsive_control(
            'item_shadow_padding',
            [
                'label'       => __('Match Padding', 'bdthemes-element-pack'),
                'description' => __('You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'selectors'   => [
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
                'name'      => 'item_hover_background',
                'selector'  => '{{WRAPPER}} .bdt-ep-review-card-carousel-item:hover',
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_reviewer_image' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'image_border',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-image img'
            ]
        );

        $this->add_responsive_control(
            'image_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_size',
            [
                'label'     => __('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-image' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'image_size_popover!' => 'yes'
                ],
            ]
        );
        //advanced image size popover toggle
        $this->add_control(
            'image_size_popover',
            [
                'label'        => esc_html__('Advanced Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'render_type'  => 'ui',
                'return_value' => 'yes',
            ]
        );
        $this->start_popover();
        $this->add_responsive_control(
            'image_height',
            [
                'label'     => __('Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-image' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'image_size_popover' => 'yes'
                ],
                'render_type'  => 'ui',
            ]
        );
        $this->add_responsive_control(
            'image_width',
            [
                'label'     => __('Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-image' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'image_size_popover' => 'yes'
                ],
                'render_type'  => 'ui',
            ]
        );
        $this->end_popover();

        $this->add_responsive_control(
            'image_spacing',
            [
                'label'      => __('Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 15,
                ],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-review-img--top .bdt-ep-review-card-carousel-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-review-img--left .bdt-ep-review-card-carousel-item, {{WRAPPER}}.bdt-review-img--right .bdt-ep-review-card-carousel-item' => 'grid-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-review-img-inline--yes.bdt-review-img--left .bdt-ep-review-card-carousel-image' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-review-img-inline--yes.bdt-review-img--right .bdt-ep-review-card-carousel-image' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-image img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'img_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-image img'
            ]
        );

        $this->add_control(
            'image_offset_toggle',
            [
                'label'        => __('Offset', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'label_off'    => __('None', 'bdthemes-element-pack'),
                'label_on'     => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'image_horizontal_offset',
            [
                'label' => __('Horizontal', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'image_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-review-card-carousel-image-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'image_vertical_offset',
            [
                'label' => __('Vertical', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'image_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-review-card-carousel-image-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->end_popover();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_name',
            [
                'label' => __('Name', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_reviewer_name' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'name_hover_color',
            [
                'label'     => __('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item:hover .bdt-ep-review-card-carousel-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'name_bottom_space',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-name' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-name',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'name_shadow',
                'label' => __('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-name',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_job_title',
            [
                'label' => __('Job Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_reviewer_job_title' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'job_title_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-job-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'job_title_hover_color',
            [
                'label'     => __('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item:hover .bdt-ep-review-card-carousel-job-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'job_title_bottom_space',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-job-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'job_title_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-job-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_text',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_review_text' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_hover_color',
            [
                'label'     => __('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-item:hover .bdt-ep-review-card-carousel-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        //margin
        $this->add_responsive_control(
            'text_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_rating',
            [
                'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_rating' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#e7e7e7',
                'selectors' => [
                    '{{WRAPPER}} .epsc-rating-item' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'rating_type' => 'star',
                ],
            ]
        );

        $this->add_control(
            'active_rating_color',
            [
                'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFCC00',
                'selectors' => [
                    '{{WRAPPER}} .epsc-rating[class*=" epsc-rating-0"] .epsc-rating-item:nth-child(1) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-1"] .epsc-rating-item:nth-child(-n+1) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-2"] .epsc-rating-item:nth-child(-n+2) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-3"] .epsc-rating-item:nth-child(-n+3) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-4"] .epsc-rating-item:nth-child(-n+4) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-5"] .epsc-rating-item:nth-child(-n+5) i:after, .epsc-rating.epsc-rating-0-5 .epsc-rating-item:nth-child(1) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-1-5 .epsc-rating-item:nth-child(2) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-2-5 .epsc-rating-item:nth-child(3) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-3-5 .epsc-rating-item:nth-child(4) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-4-5 .epsc-rating-item:nth-child(5) i:after' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'rating_type' => 'star',
                ],
            ]
        );

        $this->add_control(
            'rating_number_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'rating_type' => 'number',
                ],
            ]
        );

        $this->add_control(
            'rating_background_color',
            [
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default'   => '#1e87f0',
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'rating_type' => 'number',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'rating_border',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-carousel-rating',
                'condition' => [
                    'rating_type' => 'number',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'rating_type' => 'number',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'rating_type' => 'number',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_space_between',
            [
                'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating i + i' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-ep-review-card-carousel-rating span.epsc-rating span' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        //Navigation Style
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'      => __('Navigation', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'     => 'navigation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ],
                        [
                            'name'  => 'show_scrollbar',
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

    public function render_reviewer_image($item) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_reviewer_image']) {
            return;
        }

        $image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
		$this->add_render_attribute('image-wrap', 'class', 'bdt-ep-review-card-carousel-image' . $image_mask);
        ?>
        <div <?php echo $this->get_render_attribute_string('image-wrap'); ?>>

            <?php 
            $thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
            if (!$thumb_url) {
                printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['reviewer_name']));
            } else {
                print(wp_get_attachment_image(
                    $item['image']['id'],
                    $settings['thumbnail_size_size'],
                    false,
                    [
                        'alt' => esc_html($item['reviewer_name'])
                    ]
                ));
            }
            ?>

        </div>
    <?php
    }

    public function render_reviewer_name($item) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_reviewer_name']) {
            return;
        }

        $this->add_render_attribute('review-name', 'class', 'bdt-ep-review-card-carousel-name', true);

    ?>
        <?php if ($item['reviewer_name']) : ?>
            <<?php echo Utils::get_valid_html_tag($settings['review_name_tag']); ?> <?php echo $this->get_render_attribute_string('review-name'); ?>>
                <?php echo wp_kses($item['reviewer_name'], element_pack_allow_tags('title')); ?>
            </<?php echo Utils::get_valid_html_tag($settings['review_name_tag']); ?>>
        <?php endif; ?>
    <?php
    }

    public function render_reviewer_job_title($item) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_reviewer_job_title']) {
            return;
        }

    ?>
        <?php if ($item['reviewer_job_title']) : ?>
            <div class="bdt-ep-review-card-carousel-job-title">
                <?php echo esc_html($item['reviewer_job_title']); ?>
            </div>
        <?php endif; ?>
    <?php
    }

    public function render_review_text($item) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_review_text']) {
            return;
        }

    ?>
        <?php if ($item['review_text']) : ?>
            <div class="bdt-ep-review-card-carousel-text">
                <?php echo wp_kses_post($item['review_text']); ?>
            </div>
        <?php endif; ?>
    <?php
    }

    public function render_review_rating($item) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_rating']) {
            return;
        }

        $rating_number = $item['rating_number']['size'];

        if (preg_match('/\./', $rating_number)) {
            $ratingValue = explode(".", $rating_number);
            $firstVal    = ($ratingValue[0] <= 5) ? $ratingValue[0] : 5;
            $secondVal   = ($ratingValue[1] < 5) ? 0 : 5;
        } else {
            $firstVal    = ($rating_number <= 5) ? $rating_number : 5;
            $secondVal   = 0;
        }

        $score       = $firstVal . '-' . $secondVal;

    ?>
        <div class="bdt-ep-review-card-carousel-rating bdt-flex-inline bdt-flex-middle bdt-<?php echo esc_attr($settings['rating_type']) ?> bdt-<?php echo esc_attr($settings['rating_position']) ?>">
            <?php if ($settings['rating_type'] === 'number') : ?>
                <span><?php echo esc_html($item['rating_number']['size']); ?></span>
                <i class="ep-icon-star-full" aria-hidden="true"></i>
            <?php else : ?>
                <span class="epsc-rating epsc-rating-<?php echo $score; ?>">
                    <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
                    <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
                    <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
                    <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
                    <span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
                </span>
            <?php endif; ?>
        </div>
    <?php
    }

    public function render_review_item() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['review_items'])) {
            return;
        }

        $this->add_render_attribute('review-item', 'class', 'bdt-ep-review-card-carousel-item swiper-slide', true);

        if ('right' == $settings['image_position']) {
			$this->add_render_attribute('image-inline', 'class', 'bdt-flex bdt-flex-row-reverse', true);
		} else {
            $this->add_render_attribute('image-inline', 'class', 'bdt-flex', true);
        }

        ?>

        <?php foreach ($settings['review_items'] as $index => $item) : ?>
            <div <?php echo $this->get_render_attribute_string('review-item'); ?>>

                <?php if ('' == $settings['image_inline']) : ?>
                    <?php $this->render_reviewer_image($item); ?>
                <?php endif; ?>

                <div class="bdt-ep-review-card-carousel-content">

                    <?php if ('yes' == $settings['image_inline']) : ?>
                        <div <?php echo $this->get_render_attribute_string('image-inline'); ?>>
                            
                            <?php $this->render_reviewer_image($item); ?>
                            
                            <div class="bdt-flex bdt-flex-column bdt-flex-center">
                                <?php $this->render_reviewer_name($item); ?>
                                <?php $this->render_reviewer_job_title($item); ?>

                                <?php if ($settings['rating_position'] == 'before') : ?>
                                    <?php $this->render_review_rating($item); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ('' == $settings['image_inline']) : ?>
                        <?php $this->render_reviewer_name($item); ?>
                        <?php $this->render_reviewer_job_title($item); ?>

                        <?php if ($settings['rating_position'] == 'before') : ?>
                            <?php $this->render_review_rating($item); ?>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php $this->render_review_text($item); ?>

                    <?php if ($settings['rating_position'] == 'after') : ?>
                        <?php $this->render_review_rating($item); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach;
    }

    public function render_header() {
        $settings = $this->get_settings_for_display();

        //Global Function
        $this->render_swiper_header_attribute('review-card-carousel');

        $this->add_render_attribute('carousel', 'class', 'bdt-review-card-carousel');

        ?>
        <div <?php echo $this->get_render_attribute_string('carousel'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
            <?php
        }

        public function render() {
            $this->render_header();
            $this->render_review_item();
            $this->render_footer();
        }
    }
