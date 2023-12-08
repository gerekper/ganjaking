<?php

namespace ElementPack\Modules\ReviewCard\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use ElementPack\Utils;

use ElementPack\Traits\Global_Mask_Controls;

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Review_Card extends Module_Base {

    use Global_Mask_Controls;
    
    public function get_name() {
        return 'bdt-review-card';
    }

    public function get_title() {
        return BDTEP . esc_html__('Review Card', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-review-card';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['interactive', 'review', 'image', 'services', 'card', 'box', 'features', 'testimonial', 'client'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-review-card'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/xFtjeR1qgSE';
    }

    protected function register_controls() {
    
        $this->start_controls_section(
            'section_reviewer_content',
            [
                'label' => __('Review Card', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
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

        $this->add_control(
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

        $this->add_control(
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

        $this->add_control(
			'rating_number',
			[
				'label' => __( 'Rating', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
				'default' => [
					'size' => 4.5,
				],
				'range' => [
					'px' => [
						'min' => .5,
						'max' => 5,
						'step' => .5,
					],
				],
				'dynamic' => [
					'active' => true,
                ],
			]
		);

        $this->add_control(
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

        $this->end_controls_section();

        $this->start_controls_section(
            'section_review_additional_settings',
            [
                'label' => __('Additional Settings', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_reviewer_name',
            [
                'label'   => __('Show Name', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'reviewer_name!' => '',
                ]
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
                    'reviewer_name!' => '',
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
                'condition' => [
                    'reviewer_job_title!' => '',
                ]
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
				'label'   => __( 'Rating Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
                'default' => 'star',
				'options' => [
					'star'   => __( 'Star', 'bdthemes-element-pack' ),
					'number' => __( 'Number', 'bdthemes-element-pack' ),
				],
                'condition' => [
                    'show_rating' => 'yes'
                ]
			]
		);

        $this->add_control(
			'rating_position',
			[
				'label'   => __( 'Rating Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
                'default' => 'before',
				'options' => [
					'before' => __( 'Before Review Text', 'bdthemes-element-pack' ),
					'after' => __( 'After Review Text', 'bdthemes-element-pack' ),
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
                'condition' => [
                    'review_text!' => '',
                ]
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
            'iamge_position',
            [
                'label'     => __('Image Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'top',
                'toggle' => false,
                'options'   => [
                    'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __( 'Top', 'bdthemes-element-pack' ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
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
					'iamge_position' => ['left', 'right'],
                    'show_reviewer_image' => 'yes'
				],
                'prefix_class' => 'bdt-review-img-inline--',
                'render_type' => 'template',
			]
		);

        $this->add_responsive_control(
            'iamge_alignment',
            [
                'label'     => __('Image Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'center',
                'toggle' => false,
                'options'   => [
					'flex-start' => [
						'title' => __( 'Top', 'bdthemes-element-pack' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __( 'Bottom', 'bdthemes-element-pack' ),
						'icon' => 'eicon-v-align-bottom',
					],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-image' => 'align-self: {{VALUE}};',
                ],
                'condition' => [
                    'iamge_position' => ['left', 'right'],
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
                    '{{WRAPPER}} .bdt-ep-review-card-item' => 'text-align: {{VALUE}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->end_controls_section();

        //Style

        $this->start_controls_section(
            'section_style_card_item',
            [
                'label' => __('Item', 'bdthemes-element-pack') . BDTEP_NC,
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // background global control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'card_item_background',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-item',
            ]
        );

        // border group control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'card_item_border',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-item',
            ]
        );

        // border radius group control
        $this->add_responsive_control(
            'card_item_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // box shadow group control
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'card_item_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-item',
            ]
        );

        $this->add_responsive_control(
            'card_item_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // margin
        $this->add_responsive_control(
            'card_item_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

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
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-image img'
            ]
        );

        $this->add_responsive_control(
            'iamge_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'iamge_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'iamge_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-review-card-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-image' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-image' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-image' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}}.bdt-review-img--top .bdt-ep-review-card-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-review-img--left .bdt-ep-review-card-item, {{WRAPPER}}.bdt-review-img--right .bdt-ep-review-card-item' => 'grid-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-review-img-inline--yes.bdt-review-img--left .bdt-ep-review-card-image' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-review-img-inline--yes.bdt-review-img--right .bdt-ep-review-card-image' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-image img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'img_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-image img'
            ]
        );

        $this->add_control(
            'image_offset_toggle',
            [
                'label'        => __('Offset', 'bdthemes-element-pack') . BDTEP_NC,
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
                    '{{WRAPPER}}' => '--ep-review-card-image-h-offset: {{SIZE}}px;'
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
                    '{{WRAPPER}}' => '--ep-review-card-image-v-offset: {{SIZE}}px;'
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
                    'reviewer_name!' => '',
                ]
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-name' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-name' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-name',
            ]
        );

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-review-card-name',
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
                    'reviewer_job_title!' => '',
                ]
            ]
        );

        $this->add_control(
            'job_title_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-job-title' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-job-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'job_title_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-job-title',
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
                    'review_text!' => '',
                ]
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-review-card-text',
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
                    '{{WRAPPER}} .bdt-ep-review-card-rating' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'rating_type' => 'number',
                ],
            ]
        );

        $this->add_control(
			'rating_background_color',
			[
				'label' => __( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
                'default'   => '#1e87f0',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-review-card-rating' => 'background-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-ep-review-card-rating',
                'condition' => [
                    'rating_type' => 'number',
                ],
			]
		);

		$this->add_responsive_control(
			'rating_border_radius',
			[
				'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-review-card-rating' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-rating' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-review-card-rating' => 'font-size: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-review-card-rating i + i' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-ep-review-card-rating span' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    public function render_reviewer_image() {
        $settings = $this->get_settings_for_display();

        if ( ! $settings['show_reviewer_image'] ) {
			return;
		}

        $image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
		$this->add_render_attribute('image-wrap', 'class', 'bdt-ep-review-card-image' . $image_mask);

        ?>
        <div <?php echo $this->get_render_attribute_string('image-wrap'); ?>>

            <?php 
            $thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'thumbnail_size', $settings);
            if (!$thumb_url) {
                printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($settings['reviewer_name']));
            } else {
                print(wp_get_attachment_image(
                    $settings['image']['id'],
                    $settings['thumbnail_size_size'],
                    false,
                    [
                        'alt' => esc_html($settings['reviewer_name'])
                    ]
                ));
            }
            ?>

        </div>
        <?php
    }

    public function render_reviewer_name() {
        $settings = $this->get_settings_for_display();

        if ( ! $settings['show_reviewer_name'] ) {
			return;
		}

        $this->add_render_attribute('review-name', 'class', 'bdt-ep-review-card-name');

        ?>
        <?php if ( $settings['reviewer_name'] ) : ?>
            <<?php echo Utils::get_valid_html_tag($settings['review_name_tag']); ?> <?php echo $this->get_render_attribute_string('review-name'); ?>>
                <?php echo wp_kses($settings['reviewer_name'], element_pack_allow_tags('title')); ?>
            </<?php echo Utils::get_valid_html_tag($settings['review_name_tag']); ?>>
        <?php endif; ?>
        <?php
    }

    public function render_reviewer_job_title() {
        $settings = $this->get_settings_for_display();

        if ( ! $settings['show_reviewer_job_title'] ) {
			return;
		}

        ?>
        <?php if ( $settings['reviewer_job_title'] ) : ?>
            <div class="bdt-ep-review-card-job-title">
                <?php echo esc_html($settings['reviewer_job_title']); ?>
            </div>
        <?php endif; ?>
        <?php
    }

    public function render_review_text() {
        $settings = $this->get_settings_for_display();

        if ( ! $settings['show_review_text'] ) {
			return;
		}

        ?>
        <?php if ( $settings['review_text'] ) : ?>
            <div class="bdt-ep-review-card-text">
                <?php echo wp_kses_post( $settings['review_text'] ); ?>
            </div>
        <?php endif; ?>
        <?php
    }

    public function render_review_rating() {
        $settings = $this->get_settings_for_display();

        if ( !$settings['show_rating'] ) {
            return;
        }

        $rating_number = $settings['rating_number']['size'];

        if (preg_match('/\./', $rating_number)) {
            $ratingValue = explode(".",$rating_number);
            $firstVal    = ( $ratingValue[0] <= 5 ) ? $ratingValue[0] : 5;
            $secondVal   = ( $ratingValue[1] < 5 ) ? 0 : 5;
        } else {
            $firstVal    = ( $rating_number <= 5 ) ? $rating_number : 5;
            $secondVal   = 0;
        }
        
        $score       = $firstVal . '-' . $secondVal;
        

        ?>
        <div class="bdt-ep-review-card-rating bdt-flex-inline bdt-flex-middle bdt-<?php echo esc_attr($settings['rating_type']) ?> bdt-<?php echo esc_attr($settings['rating_position']) ?>">
            <?php if ( $settings['rating_type'] === 'number' ) : ?>
                <span><?php echo esc_html( $settings['rating_number']['size'] ); ?></span>
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

        $this->add_render_attribute('review-item', 'class', 'bdt-ep-review-card-item');

        if ('right' == $settings['iamge_position']) {
			$this->add_render_attribute('image-inline', 'class', 'bdt-flex bdt-flex-row-reverse');
		} else {
            $this->add_render_attribute('image-inline', 'class', 'bdt-flex');
        }

        ?>
        <div <?php echo $this->get_render_attribute_string('review-item'); ?>>
            
            <?php if ('' == $settings['image_inline']) : ?>
                <?php $this->render_reviewer_image(); ?>
            <?php endif; ?>

            <div class="bdt-ep-review-card-content">

                <?php if ('yes' == $settings['image_inline']) : ?>
                    <div <?php echo $this->get_render_attribute_string('image-inline'); ?>>
                        
                        <?php $this->render_reviewer_image(); ?>
                        
                        <div class="bdt-flex bdt-flex-column bdt-flex-center">
                            <?php $this->render_reviewer_name(); ?>
                            <?php $this->render_reviewer_job_title(); ?>

                            <?php if ($settings['rating_position'] == 'before') : ?>
                                <?php $this->render_review_rating(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ('' == $settings['image_inline']) : ?>
                    <?php $this->render_reviewer_name(); ?>
                    <?php $this->render_reviewer_job_title(); ?>

                    <?php if ($settings['rating_position'] == 'before') : ?>
                        <?php $this->render_review_rating(); ?>
                    <?php endif; ?>
                <?php endif; ?>



                <?php $this->render_review_text(); ?>

                <?php if ($settings['rating_position'] == 'after') : ?>
                <?php $this->render_review_rating(); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function render() {
        $this->add_render_attribute('review-card', 'class', 'bdt-review-card bdt-review-card-style-1');

        ?>
        <div <?php echo $this->get_render_attribute_string('review-card'); ?>>
            <?php $this->render_review_item(); ?>
        </div>
        <?php
    }
}
