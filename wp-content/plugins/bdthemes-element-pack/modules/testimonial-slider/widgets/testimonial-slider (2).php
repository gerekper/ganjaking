<?php

namespace ElementPack\Modules\TestimonialSlider\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

use ElementPack\Modules\TestimonialSlider\Skins;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Traits\Global_Swiper_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Testimonial_Slider extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;
	use Global_Swiper_Controls;
	private $_query = null;

	public function get_name() {
		return 'bdt-testimonial-slider';
	}

	public function get_title() {
		return BDTEP . esc_html__('Testimonial Slider', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-testimonial-slider';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['testimonial', 'slider'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-testimonial-slider'];
		}
	}
	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return ['ep-testimonial-slider'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/pI-DLKNlTGg';
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Thumb($this));
		$this->add_skin(new Skins\Skin_Single($this));
	}
	public function get_query() {
		return $this->_query;
	}
	public function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'thumb',
			[
				'label'     => esc_html__('Testimonial Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'company_name',
			[
				'label'   => esc_html__('Company Name/Address', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'meta_multi_line',
			[
				'label' => esc_html__('Meta Multiline', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_comma',
			[
				'label'   => esc_html__('Show Comma After Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'text_limit',
			[
				'label'       => esc_html__('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 80,
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'   => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating',
			[
				'label'   => esc_html__('Rating', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
            'show_review_platform',
            [
                'label'   => esc_html__('Review Platform', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
            ]
        );

		$this->add_control(
			'meta_position',
			[
				'label'   => __('Meta Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'before' => [
						'title' => __('Before', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'after'  => [
						'title' => __('After', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default' => 'after',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'meta_alignment',
			[
				'label'     => __('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'bdt-testi-meta-align-',
				'render_type'  => 'template',
				'toggle'       => false,
				'default'      => 'center',
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'     => __('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'toggle'       => false,
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->end_controls_section();
		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_source',
			[
				'label'     => __('Source', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HIDDEN,
				'options'   => $this->getGroupControlQueryPostTypes(),
				'default'   => 'bdthemes-testimonial',

			]
		);
		$this->update_control(
			'posts_per_page',
			[
				'default' => 4,
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_slider_settins',
			[
				'label' => esc_html__('Slider Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Auto Play', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__('Autoplay Speed', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 7000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'velocity',
			[
				'label'   => __('Animation Speed (ms)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		//Navigation Controls
        $this->start_controls_section(
			'section_content_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-element-pack'),
				'condition' => [
					'_skin!' => 'bdt-thumb',
				],
			]
		);

        //Global Navigation Controls
        $this->register_navigation_controls();

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumb',
			[
				'label' => __('Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'testimonial_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner, {{WRAPPER}} .bdt-testimonial-slider li.bdt-slider-thumbnav .bdt-slider-thumbnav-inner:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'testimonial_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner',
			]
		);

		$this->add_responsive_control(
			'testimonial_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_padding',
			[
				'label'     => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_iamge',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin!' => 'bdt-thumb',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'testimonial_iamge_background',
                'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'testimonial_iamge_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb, {{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb img',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'testimonial_image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb, {{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_image_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_image_size',
			[
				'label'     => esc_html__('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label'     => esc_html__('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 300
				],
				'range'     => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'testimonial_iamge_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_quatation',
			[
				'label' => esc_html__('Quotation', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'quatation_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-text:after, {{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'quatation_background_color',
                'selector' => '{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after',
				'condition' => [
					'_skin' => 'bdt-single',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'        => 'quatation_border',
                'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after',
				'condition' => [
					'_skin' => 'bdt-single',
				],
            ]
        );
        
        $this->add_responsive_control(
            'quatation_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
				'condition' => [
					'_skin' => 'bdt-single',
				],
            ]
        );
        
		$this->add_responsive_control(
			'testimonial_quatation_size',
			[
				'label'     => esc_html__('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: calc(20px + {{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quatation_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-text:after, {{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after',
			]
		);


		$this->add_control(
            'quatation_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'quatation_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
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
                        'min' => -300,
                        'step' => 1,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-quatation-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'quatation_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
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
                        'min' => -300,
                        'step' => 1,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-quatation-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'quatation_rotate_x',
            [
                'label' => esc_html__('Rotate X', 'bdthemes-element-pack'),
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
                        'min' => -360,
                        'max' => 360,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-quatation-rotate-x: {{SIZE}}deg;'
                ],
            ]
        );

        $this->add_responsive_control(
            'quatation_rotate_y',
            [
                'label' => esc_html__('Rotate Y', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 35,
                ],
                'tablet_default' => [
                    'size' => 35,
                ],
                'mobile_default' => [
                    'size' => 35,
                ],
                'range' => [
                    'px' => [
                        'min' => -360,
                        'max' => 360,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-quatation-rotate-y: {{SIZE}}deg;'
                ],
            ]
        );

        $this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => ['title' => 'yes'],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-title',
			]
		);
		
		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-text',
			]
		);

		$this->add_responsive_control(
			'text_cite_space',
			[
				'label'     => __('Meta Space', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-item-inner > div:first-child' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_address',
			[
				'label' => esc_html__('Address', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'company_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'address_color',
			[
				'label'     => esc_html__('Company Name/Address Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'address_typography',
				'selector'  => '{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-address',
			]
		);

		//address margin
		$this->add_responsive_control(
			'address_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[
				'label' => esc_html__('Rating', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'rating' => 'yes',
				],
			]
		);

		$this->add_control(
            'original_color',
            [
                'label'   => esc_html__('Enable Original Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_review_platform' => 'yes'
                ]
            ]
        );

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'original_color' => ''
                ]
			]
		);

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-child(1)'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-child(-n+2)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-child(-n+3)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-child(-n+4)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-child(-n+5)' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'original_color' => ''
                ]
			]
		);
		
		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'section_style_review_platform',
            [
                'label'      => __( 'Review Platform', 'bdthemes-element-pack' ),
                'tab'        => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_review_platform' => 'yes'
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_platform_style' );
        
        $this->start_controls_tab(
            'tab_platform_normal',
            [
                'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
            ]
        );
        
        $this->add_control(
            'platform_text_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform i' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'platform_background_color',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'        => 'platform_border',
                'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-review-platform',
            ]
        );
        
        $this->add_responsive_control(
            'platform_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-review-platform' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'platform_text_padding',
            [
                'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-review-platform' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'platform_text_margin',
            [
                'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-review-platform' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'platform_shadow',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'platform_typography',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'tab_platform_hover',
            [
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );
        
        $this->add_control(
            'platform_hover_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'platform_background_hover_color',
                'selector' => '{{WRAPPER}} .bdt-review-platform:hover',
            
            ]
        );
        
        $this->add_control(
            'platform_hover_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'platform_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbs',
			[
				'label' => esc_html__('Thumbs', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-thumb',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_thumbs_style' );

		$this->start_controls_tab(
			'tab_thumbs_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'hide_arrow_style',
			[
				'label'        => esc_html__('Hide Arrow Style', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-arrow-style-hide-',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumb_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img',
			]
		);

		$this->add_responsive_control(
			'thumb_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_box_shadow',
				'label'      => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img'
			]
		);

		$this->add_control(
			'thumb_opacity',
			[
				'label'     => __('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.05,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default'   => [
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'opacity: {{SIZE}};',
				],

			]
		);

		$this->add_responsive_control(
			'horizontal_spacing',
			[
				'label'     => esc_html__('Horizontal Space', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 20,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav:not(:first-child)' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_spacing',
			[
				'label'     => esc_html__('Vertical Space', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 0,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner' => 'padding-top: calc({{SIZE}}{{UNIT}} + 20px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbs_active',
			[
				'label' => __( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'active_thumb_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-active .bdt-slider-thumbnav-inner img' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumb_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_hover_box_shadow',
				'label'      => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-active .bdt-slider-thumbnav-inner img'
			]
		);

		$this->add_control(
			'active_thumb_opacity',
			[
				'label'     => __('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.05,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default'   => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-active .bdt-slider-thumbnav-inner img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => '_skin',
							'operator' => '!==',
							'value' => 'bdt-thumb'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name'  => 'navigation',
									'operator' => '!=',
									'value' => 'none',
								],
								[
									'name'     => 'show_scrollbar',
									'value'    => 'yes',
								]
							]
						]
					]
				]
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel');

		$this->end_controls_section();
	}

	public function render_query($posts_per_page) {
		$args = [];
		$args['posts_per_page'] = $posts_per_page;
		$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		return $this->_query = new \WP_Query($args);
	}

	public function render_header($skin, $id, $settings) {

		$this->add_render_attribute('testimonial-slider', 'id', 'bdt-testimonial-slider-' . esc_attr($id));
		$this->add_render_attribute('testimonial-slider', 'class', ['bdt-testimonial-slider', 'skin-' . esc_attr($skin)]);
		$id = 'bdt-testimonial-slider-' . $this->get_id();

		if ('arrows' == $settings['navigation']) {
			$this->add_render_attribute('testimonial-slider', 'class', 'bdt-arrows-align-' . $settings['arrows_position']);
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute('testimonial-slider', 'class', 'bdt-dots-align-' . $settings['dots_position']);
		} elseif ('both' == $settings['navigation']) {
			$this->add_render_attribute('testimonial-slider', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
		} elseif ('arrows-fraction' == $settings['navigation']) {
			$this->add_render_attribute('testimonial-slider', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position']);
		}

		if ('arrows-fraction' == $settings['navigation']) {
			$pagination_type = 'fraction';
		} elseif ('both' == $settings['navigation'] or 'dots' == $settings['navigation']) {
			$pagination_type = 'bullets';
		} elseif ('progressbar' == $settings['navigation']) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[
				'testimonial-slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'autoplay'     => ('yes' == $settings['autoplay']) ? ['delay' => $settings['autoplay_interval']] : false,
							'loop'         => ($settings['loop'] == 'yes') ? true : false,
							'speed'        => $settings['velocity'],
							'pauseOnHover' => isset($settings['pause_on_hover']) ? true : false,
							'navigation'   => [
								'nextEl' => '#' . $id . ' .bdt-navigation-next',
								'prevEl' => '#' . $id . ' .bdt-navigation-prev',
							],
							"pagination" => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'autoHeight'     => true,
								'dynamicBullets' => ("yes" == $settings["dynamic_bullets"]) ? true : false,
							],
							"scrollbar" => [
								"el"            => "#" . $id . " .swiper-scrollbar",
								"hide"          => "true",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

?>

		<div <?php echo $this->get_render_attribute_string('testimonial-slider'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">

					<?php
				}

				public function render_navigation() {
					$settings = $this->get_settings_for_display();
					$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';

					if ('arrows' == $settings['navigation']) : ?>
						<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_position'] . $hide_arrow_on_mobile); ?>">
							<div class="bdt-arrows-container bdt-slidenav-container">
								<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
									<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
								</a>
								<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
									<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
								</a>
							</div>
						</div>
					<?php endif;
				}

				public function render_pagination() {
					$settings = $this->get_settings_for_display();

					if ('dots' == $settings['navigation'] or 'arrows-fraction' == $settings['navigation']) : ?>
						<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['dots_position']); ?>">
							<div class="bdt-dots-container">
								<div class="swiper-pagination"></div>
							</div>
						</div>

					<?php elseif ('progressbar' == $settings['navigation']) : ?>
						<div class="swiper-pagination bdt-position-z-index bdt-position-<?php echo esc_attr($settings['progress_position']); ?>"></div>
					<?php endif;
				}

				public function render_both_navigation() {
					$settings = $this->get_settings_for_display();
					$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

					?>
					<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
						<div class="bdt-arrows-dots-container bdt-slidenav-container ">

							<div class="bdt-flex bdt-flex-middle">
								<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
									<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
										<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
									</a>
								</div>

								<?php if ('center' !== $settings['both_position']) : ?>
									<div class="swiper-pagination"></div>
								<?php endif; ?>

								<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
									<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
										<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
									</a>
								</div>

							</div>
						</div>
					</div>
				<?php
				}

				public function render_arrows_fraction() {
					$settings             = $this->get_settings_for_display();
					$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

				?>
					<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_fraction_position']); ?>">
						<div class="bdt-arrows-fraction-container bdt-slidenav-container ">

							<div class="bdt-flex bdt-flex-middle">
								<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
									<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
										<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
									</a>
								</div>

								<?php if ('center' !== $settings['arrows_fraction_position']) : ?>
									<div class="swiper-pagination"></div>
								<?php endif; ?>

								<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
									<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
										<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
									</a>
								</div>

							</div>
						</div>
					</div>
				<?php
				}

				public function render_footer() {
					$settings = $this->get_settings_for_display();

				?>
				</div>
				<?php if ('yes' === $settings['show_scrollbar']) : ?>
					<div class="swiper-scrollbar"></div>
				<?php endif; ?>
			</div>

			<?php if ('both' == $settings['navigation']) : ?>
				<?php $this->render_both_navigation(); ?>
				<?php if ('center' === $settings['both_position']) : ?>
					<div class="bdt-position-z-index bdt-position-bottom">
						<div class="bdt-dots-container">
							<div class="swiper-pagination"></div>
						</div>
					</div>
				<?php endif; ?>
			<?php elseif ('arrows-fraction' == $settings['navigation']) : ?>
				<?php $this->render_arrows_fraction(); ?>
				<?php if ('center' === $settings['arrows_fraction_position']) : ?>
					<div class="bdt-dots-container">
						<div class="swiper-pagination"></div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php $this->render_pagination(); ?>
				<?php $this->render_navigation(); ?>
			<?php endif; ?>

		</div>
	<?php
				}

				public function render_review_platform($post_id) {
					$settings = $this->get_settings_for_display();
			
					if (!$settings['show_review_platform']) {
						return;
					}
			
					$platform = get_post_meta($post_id, 'bdthemes_tm_platform', true);
					$review_link = get_post_meta($post_id, 'bdthemes_tm_review_link', true);

					if ( !$platform) {
						$platform = 'self';
					}
			
					if ( !$review_link) {
						$review_link = '#';
					}
			
					?>
					<a href="<?php echo $review_link; ?>" class="bdt-review-platform bdt-flex-inline" bdt-tooltip="<?php echo $platform; ?>">
						<i class="ep-icon-<?php echo strtolower($platform); ?> bdt-platform-icon bdt-flex bdt-flex-middle bdt-flex-center" aria-hidden="true"></i>
					</a>
					<?php
				}

				public function render_image() {
					$settings = $this->get_settings_for_display();

					if ('yes' != $settings['thumb']) {
						return;
					}

					$testimonial_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');

					if (!$testimonial_thumb) {
						$testimonial_thumb = BDTEP_ASSETS_URL . 'images/member.svg';
					} else {
						$testimonial_thumb = $testimonial_thumb[0];
					}

					?>
						<div class="bdt-testimonial-thumb-wrap bdt-flex bdt-position-relative">
							<div class="bdt-testimonial-thumb bdt-position-relative">
								<img src="<?php echo esc_url($testimonial_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
							</div>
							<?php $this->render_review_platform(get_the_ID()); ?>
						</div>
				   <?php
				}

				public function render_excerpt() {

					$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

					if (has_excerpt()) {
						the_excerpt();
					} else {
						echo element_pack_custom_excerpt($this->get_settings_for_display('text_limit'), $strip_shortcode);
					}
				}

				public function render_meta($element_key) {
					$settings = $this->get_settings_for_display();

					$this->add_render_attribute($element_key, 'class', ['bdt-rating', 'bdt-grid', 'bdt-grid-collapse']);
					$this->add_render_attribute($element_key, 'class', 'bdt-rating-' . get_post_meta(get_the_ID(), 'bdthemes_tm_rating', true));

					if (!$settings['thumb']) {
						$this->add_render_attribute($element_key, 'class', 'bdt-flex-' . $settings['alignment']);
					}


					if ($settings['title'] or $settings['company_name'] or $settings['rating']) : ?>
			<div class="bdt-testimonial-meta <?php echo ($settings['meta_multi_line']) ? 'bdt-meta-multi-line' : ''; ?>">
				<?php if ($settings['title']) : ?>
					<div class="bdt-testimonial-title">
						<?php echo get_the_title(); ?><?php if ($settings['show_comma']) {
															echo (($settings['title']) and ($settings['company_name'])) ? ', ' : '';
														} ?>
					</div>

				<?php endif ?>

				<?php if ($settings['company_name']) : ?>
					<div class="bdt-testimonial-address"><?php echo get_post_meta(get_the_ID(), 'bdthemes_tm_company_name', true); ?></div>
				<?php endif ?>

				<?php if ($settings['rating']) : ?>
					<ul <?php echo $this->get_render_attribute_string($element_key); ?>>
						<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
						<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
						<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
						<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
						<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
					</ul>
				<?php endif ?>

			</div>
		<?php endif;
				}

				public function render() {
					$settings = $this->get_settings_for_display();
					$id       = $this->get_id();
					$index    = 1;

					// TODO need to delete after v6.5
					if (isset($settings['posts']) and $settings['posts_per_page'] == 6) {
						$limit = $settings['posts'];
					} else {
						$limit = $settings['posts_per_page'];
					}
					$wp_query = $this->render_query($limit);

					if (!$wp_query->found_posts) {
						return;
					}

					$this->render_header('default', $id, $settings); ?>

		<?php while ($wp_query->have_posts()) : $wp_query->the_post(); 
		$platform = get_post_meta(get_the_ID(), 'bdthemes_tm_platform', true);
		?>

			<div class="swiper-slide bdt-review-<?php echo strtolower($platform); ?>">
				<div class="bdt-slider-item-inner">
					<?php if ('after' == $settings['meta_position']) : ?>
						<div class="bdt-testimonial-text">
							<?php $this->render_excerpt(); ?>
						</div>
					<?php endif; ?>

					<div class="bdt-info-details bdt-flex bdt-flex-center bdt-flex-middle">

						<?php $this->render_image(); ?>

						<?php $this->render_meta('testmonial-meta-' . $index); ?>

					</div>

					<?php if ('before' == $settings['meta_position']) : ?>
						<div class="bdt-testimonial-text">
							<?php $this->render_excerpt(); ?>
						</div>
					<?php endif; ?>
				</div>

			</div>


<?php

						$index++;

					endwhile;
					wp_reset_postdata();

					$this->render_footer();
				}
			}
