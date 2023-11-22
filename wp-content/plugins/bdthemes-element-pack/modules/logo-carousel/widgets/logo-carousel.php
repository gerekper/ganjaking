<?php
namespace ElementPack\Modules\LogoCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Utils;

use ElementPack\Traits\Global_Mask_Controls;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Logo_Carousel extends Module_Base {

	use Global_Mask_Controls;

	public function get_name() {
		return 'bdt-logo-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Logo Carousel', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-logo-carousel';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'logo', 'carousel', 'client', 'brand', 'showcase' ];
	}

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-logo-carousel', 'tippy'  ];
        }
    }
    
	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'ep-scripts'];
        } else {
			return [ 'popper', 'tippyjs', 'ep-logo-carousel' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/xe_SA0ZgAvA';
	}

    protected function register_controls() {
        $this->start_controls_section(
            'ep_section_layout',
            [
                'label' => __( 'Layout', 'bdthemes-element-pack' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-logo-carousel-wrapper.bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-logo-carousel-wrapper.bdt-grid > *' => 'padding-left: {{SIZE}}px',
				],
			]
		);

        $this->add_responsive_control(
            'height',
            [
                'label' => __( 'Item Height', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 500,
                        'min' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-item' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
		);
		
		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'large',
                'separator' => 'before',
                'exclude' => [
                    'custom'
                ]
            ]
		);
		
		$this->add_control(
            'image_mask_popover',
            [
                'label'        => esc_html__('Image Mask', 'bdthemes-element-pack') . BDTEP_NC,
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'render_type'  => 'template',
                'return_value' => 'yes',
            ]
        );

        //Global Image Mask Controls
        $this->register_image_mask_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'ep_section_logo',
            [
                'label' => __( 'Logo Carousel Item', 'bdthemes-element-pack' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'       => __( 'Logo Image', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

        $repeater->add_control(
            'link',
            [
                'label' => __('Website Url', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'show_external' => false,
                'label_block' => false,
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => __('Brand Name', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Brand Name', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __('Description', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Brand Short Description Type Here.', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'logo_tooltip',
            [
                'label'   => __( 'Tooltip', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SWITCHER,
            ]
        );

        $repeater->add_control(
            'logo_tooltip_placement',						
            [
                'label'   => esc_html__( 'Placement', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top-start'    => esc_html__( 'Top Left', 'bdthemes-element-pack' ),
                    'top'          => esc_html__( 'Top', 'bdthemes-element-pack' ),
                    'top-end'      => esc_html__( 'Top Right', 'bdthemes-element-pack' ),
                    'bottom-start' => esc_html__( 'Bottom Left', 'bdthemes-element-pack' ),
                    'bottom'       => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
                    'bottom-end'   => esc_html__( 'Bottom Right', 'bdthemes-element-pack' ),
                    'left'         => esc_html__( 'Left', 'bdthemes-element-pack' ),
                    'right'        => esc_html__( 'Right', 'bdthemes-element-pack' ),
                ],
                'condition'   => [
                    'logo_tooltip' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'logo_list',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ name }}}',
                'default' => [
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-5.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-6.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-7.svg' ]],
                    ['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-8.svg' ]],
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'section_tooltip_settings',
			[
				'label' => __( 'Tooltip Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'logo_tooltip_animation',
			[
				'label'   => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shift-toward',
				'options' => [
					'shift-away'   => esc_html__( 'Shift-Away', 'bdthemes-element-pack' ),
					'shift-toward' => esc_html__( 'Shift-Toward', 'bdthemes-element-pack' ),
					'fade'         => esc_html__( 'Fade', 'bdthemes-element-pack' ),
					'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack' ),
					'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
				],
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'logo_tooltip_x_offset',
			[
				'label'   => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'logo_tooltip_y_offset',
			[
				'label'   => esc_html__( 'Distance', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'logo_tooltip_arrow',
			[
				'label'        => esc_html__( 'Arrow', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'logo_tooltip_trigger',
			[
				'label'       => __( 'Trigger on Click', 'bdthemes-element-pack' ),
				'description' => __( 'Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'section_content_carousel_settins',
			[
				'label'     => esc_html__( 'Carousel Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__( 'Auto Play', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__( 'Autoplay Interval', 'bdthemes-element-pack' ),
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
				'label'   => esc_html__( 'Pause on Hover', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__( 'Loop', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'center_slide',
			[
				'label' => esc_html__( 'Center Slide', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label'     => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => __( 'Navigation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'both'   => __( 'Arrows and Dots', 'bdthemes-element-pack' ),
					'arrows' => __( 'Arrows', 'bdthemes-element-pack' ),
					'dots'   => __( 'Dots', 'bdthemes-element-pack' ),
					'none'   => __( 'None', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-navigation-type-',
				'render_type'  => 'template',				
			]
		);
		
		$this->add_control(
			'both_position',
			[
				'label'     => __( 'Arrows and Dots Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'both',
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'     => __( 'Arrows Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows',
				],				
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'     => __( 'Dots Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_pagination_position(),
				'condition' => [
					'navigation' => 'dots',
				],				
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__( 'Arrows Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => [
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6' => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7' => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8' => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9' => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10' => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11' => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12' => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13' => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14' => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15' => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16' => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17' => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18' => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['both', 'arrows'],
				],
			]
		);

		$this->add_control(
			'hide_arrow_on_mobile',
			[
				'label'     => __( 'Hide Arrow on Mobile', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],				
			]
		);

		$this->end_controls_section();

        //Style
        $this->start_controls_section(
            'ep_section_style_carousel',
            [
                'label' => __( 'Logo Carousel', 'bdthemes-element-pack' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'ep_image_effects' );

        $this->start_controls_tab(
            'ep_tab_image_effects_normal',
            [
                'label' => __( 'Normal', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'carousel_bg_color',
            [
                'label' => __( 'Background Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-figure' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector'    => '{{WRAPPER}} .bdt-logo-carousel-figure',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-logo-carousel-figure, {{WRAPPER}} .bdt-logo-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'padding',
            [
                'label' => __( 'Padding', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-figure' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => __( 'Opacity', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-figure img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters',
                'selector' => '{{WRAPPER}} .bdt-logo-carousel-figure img',
            ]
        );

		$this->add_responsive_control(
			'image_size',
			[
				'label'      => __( 'Image Size', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-logo-carousel-img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; object-fit: contain;'
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab( 'hover',
            [
                'label' => __( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'carousel_bg_hover_color',
            [
                'label' => __( 'Background Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-item:hover .bdt-logo-carousel-figure' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_border_hover_color',
            [
                'label' => __( 'Border Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-item:hover .bdt-logo-carousel-figure' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'item_border_border!' => '',
                ],
            ]
        );

        $this->add_control(
            'image_opacity_hover',
            [
                'label' => __( 'Opacity', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-figure:hover img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-logo-carousel-figure:hover img',
            ]
        );

        $this->add_control(
            'image_bg_hover_transition',
            [
                'label' => __( 'Transition Duration', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-figure:hover img' => 'transition-duration: {{SIZE}}s;',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => __( 'Hover Animation', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_width',
			[
				'label'      => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'logo_tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'logo_tooltip_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'logo_tooltip_text_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'logo_tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_control(
			'logo_tooltip_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'  => 'border-left-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-right-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'   => 'border-top-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'border-bottom-color: {{VALUE}}',

					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'logo_tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'logo_tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
					],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Arrows Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-navigation-next i' => 'font-size: {{SIZE || 24}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev,
					{{WRAPPER}} .bdt-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __( 'Hover Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover,
					{{WRAPPER}} .bdt-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __( 'Arrows Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __( 'Arrows Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover i,
					{{WRAPPER}} .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_space',
			[
				'label' => __( 'Space', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'arrows_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
				'selector'    => '{{WRAPPER}} .bdt-navigation-prev,
				{{WRAPPER}} .bdt-navigation-next',
			]
		);

		$this->add_control(
            'arrows_border_hover_color',
            [
                'label' => __( 'Border Hover Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-navigation-prev:hover,
					{{WRAPPER}} .bdt-navigation-next:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'arrows_border_border!' => '',
				],
            ]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev,
					{{WRAPPER}} .bdt-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);
		
		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev,
					{{WRAPPER}} .bdt-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __( 'Dots Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-dotnav a' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __( 'Dots Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-dotnav a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __( 'Active Dots Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-dotnav.bdt-active a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_ncx_position',
			[
				'label'   => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-arrows-ncx-h-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'arrows_ncy_position',
			[
				'label'   => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-arrows-ncx-v-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'arrows_acx_position',
			[
				'label'   => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nnx_position',
			[
				'label'   => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-dots-nnx-h-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'   => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-dots-nnx-v-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'both_ncx_position',
			[
				'label'   => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-both-h-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'both_ncy_position',
			[
				'label'   => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-both-v-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'both_cx_position',
			[
				'label'   => __( 'Arrows Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cy_position',
			[
				'label'   => __( 'Dots Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->end_controls_section();
        
    }
    
    public function render_header() {

		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$this->add_render_attribute('logo-carousel', 'id', 'bdt-logo-carousel-' . esc_attr($id) );
		$this->add_render_attribute('logo-carousel', 'class', ['bdt-logo-carousel-wrapper'] );
		$this->add_render_attribute('logo-carousel', 'data-bdt-grid', '');
		$this->add_render_attribute('logo-carousel', 'class', ['bdt-grid', 'bdt-grid-small'] );
		

		$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
		$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 3;
		$columns 		= isset($settings['columns']) ? $settings['columns'] : 4;

		$this->add_render_attribute('logo-carousel', 'class', 'bdt-slider-items');
		$this->add_render_attribute('logo-carousel', 'class', 'bdt-child-width-1-' . esc_attr($columns_mobile));
		$this->add_render_attribute('logo-carousel', 'class', 'bdt-child-width-1-' . esc_attr($columns_tablet) .'@s');
		$this->add_render_attribute('logo-carousel', 'class', 'bdt-child-width-1-' . esc_attr($columns) .'@m');

		$this->add_render_attribute(
			[
				'slider-settings' => [
					'class' => [
						( 'both' == $settings['navigation'] ) ? 'bdt-arrows-dots-align-' . $settings['both_position'] : '',
						( 'arrows' == $settings['navigation'] or 'arrows-thumbnavs' == $settings['navigation'] ) ? 'bdt-arrows-align-' . $settings['arrows_position'] : '',
						( 'dots' == $settings['navigation'] ) ? 'bdt-dots-align-'. $settings['dots_position'] : '',
					],
					'data-bdt-slider' => [
						wp_json_encode(array_filter([
							"autoplay"          => ( $settings["autoplay"] ) ? true : false,
							"autoplay-interval" => $settings["autoplay_interval"],
							"finite"            => ($settings["loop"]) ? false : true,
							"pause-on-hover"    => ( $settings["pause_on_hover"] ) ? true : false,
							"center"            => ( $settings["center_slide"] ) ? true : false
						]))
					]
				]
			]
		);

		?>
		<div <?php echo ( $this->get_render_attribute_string( 'slider-settings' ) ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'logo-carousel' ); ?>>
		<?php
	}

	public function render_footer($settings) {
        $settings = $this->get_settings_for_display();

		?>
		</div>
		<?php if ('both' == $settings['navigation']) : ?>
			<?php $this->render_both_navigation($settings); ?>

			<?php if ( 'center' === $settings['both_position'] ) : ?>
				<?php $this->render_dotnavs($settings); ?>
			<?php endif; ?>

		<?php elseif ('arrows' == $settings['navigation']) : ?>
			<?php $this->render_navigation($settings); ?>
		<?php elseif ('dots' == $settings['navigation']) : ?>
			<?php $this->render_dotnavs($settings); ?>
		<?php endif; ?>
	</div>
	<?php
	}

	public function render_navigation($settings) {

		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';

		if (('both' == $settings['navigation']) and ('center' == $settings['both_position'])) {
			$arrows_position = 'center';
		} else {
			$arrows_position = $settings['arrows_position'];
		}

		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($arrows_position); ?> <?php echo esc_attr($hide_arrow_on_mobile); ?>">
			<div class="bdt-arrows-container bdt-slidenav-container">
				<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" data-bdt-slider-item="previous">
					<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
				</a>
				<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slider-item="next">
					<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<?php
	}

	public function render_dotnavs($settings) {

		if (('both' == $settings['navigation']) and ('center' == $settings['both_position'])) {
			$dots_position = 'bottom-center';
		} else {
			$dots_position = $settings['dots_position'];
		}

		?>
		<div class="bdt-position-z-index bdt-visible@m bdt-position-<?php echo esc_attr($dots_position); ?>">
			<div class="bdt-dotnav-wrapper bdt-dots-container">
				<ul class="bdt-dotnav bdt-flex-center">

				    <?php		
					$bdt_counter = 0;

					foreach ( $settings['logo_list'] as $index => $item ) :
					      
						echo '<li class="bdt-slider-dotnav bdt-active" data-bdt-slider-item="' . esc_attr($bdt_counter) . '"><a href="#"></a></li>';
						$bdt_counter++;

					endforeach; ?>

				</ul>
			</div>
		</div>
		<?php
	}

	public function render_both_navigation($settings) {
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
			<div class="bdt-arrows-dots-container bdt-slidenav-container ">
				
				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr( $hide_arrow_on_mobile ); ?>">
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" bdt-slider-item="previous">
							<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>

					<?php if ('center' !== $settings['both_position']) : ?>
						<div class="bdt-dotnav-wrapper bdt-dots-container">
							<ul class="bdt-dotnav">
							    <?php		
								$bdt_counter = 0;

								foreach ( $settings['logo_list'] as $index => $item ) :								      
									echo '<li class="bdt-slider-dotnav bdt-active" bdt-slider-item="' . esc_attr($bdt_counter) . '"><a href="#"></a></li>';
									$bdt_counter++;
								endforeach; ?>

							</ul>
						</div>
					<?php endif; ?>
					
					<div class="<?php echo esc_attr( $hide_arrow_on_mobile ); ?>">
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" bdt-slider-item="next">
							<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>
					
				</div>
			</div>
		</div>		
		<?php
    }
    
    public function render_loop_item($settings) {
        $settings = $this->get_settings_for_display();

        if ( empty($settings['logo_list'] ) ) {
            return;
        }

        ?>

        <?php
        foreach ( $settings['logo_list'] as $index => $item ) :
            $image = wp_get_attachment_image_url( $item['image']['id'], $settings['thumbnail_size'] );
            $repeater_key = 'carousel_item' . $index;
            $tag = 'div';
            $image_alt = esc_html($item['name']) . ' : ' . esc_html($item['description']); 
            $this->add_render_attribute( $repeater_key, 'class', 'bdt-logo-carousel-item' );

            if ( $item['link']['url'] ) {
            	$target = ($item['link']['is_external']) ? '_blank' : '_self';
                $tag = 'a';
                $this->add_render_attribute( $repeater_key, 'class', 'bdt-logo-carousel-link' );
                $this->add_render_attribute( $repeater_key, 'target', $target );
                $this->add_render_attribute( $repeater_key, 'rel', 'noopener' );
                $this->add_render_attribute( $repeater_key, 'href', esc_url( $item['link']['url'] ) );
                $this->add_render_attribute( $repeater_key, 'title', $item['name'] );
            }

            if ($item['name'] and $item['description'] and $item['logo_tooltip']) {
                // Tooltip settings
            	$tooltip_content = '<div><strong>' . $item['name'] . '</strong></div>' . $item['description']; 
            	$this->add_render_attribute( $repeater_key, 'data-tippy-content', $tooltip_content, true);
                
                $this->add_render_attribute( $repeater_key, 'class', 'bdt-tippy-tooltip' );
                $this->add_render_attribute( $repeater_key, 'data-tippy', '', true );

                if ($item['logo_tooltip_placement']) {
                    $this->add_render_attribute( $repeater_key, 'data-tippy-placement', $item['logo_tooltip_placement'], true );
                }

                if ($settings['logo_tooltip_animation']) {
                    $this->add_render_attribute( $repeater_key, 'data-tippy-animation', $settings['logo_tooltip_animation'], true );
                }

                if ($settings['logo_tooltip_x_offset']['size'] or $settings['logo_tooltip_y_offset']['size']) {
                    $this->add_render_attribute( $repeater_key, 'data-tippy-offset', '[' . $settings['logo_tooltip_x_offset']['size'] .','. $settings['logo_tooltip_y_offset']['size']. ']', true );
                }

                if ('yes' == $settings['logo_tooltip_arrow']) {
                    $this->add_render_attribute( $repeater_key, 'data-tippy-arrow', 'true', true );
                }else{
                    $this->add_render_attribute( $repeater_key, 'data-tippy-arrow', 'false', true );
				}

                if ('yes' == $settings['logo_tooltip_trigger']) {
                    $this->add_render_attribute( $repeater_key, 'data-tippy-trigger', 'click', true );
                }

            }

			$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
			$this->add_render_attribute('image-wrap', 'class', 'bdt-logo-carousel-figure' . $image_mask);
            
            ?>
            <<?php echo $tag; ?> <?php $this->print_render_attribute_string( $repeater_key ); ?>>
                <figure <?php echo $this->get_render_attribute_string('image-wrap'); ?>>
                <?php if ( $image ) :

                        echo wp_get_attachment_image(
                            $item['image']['id'],
                            $settings['thumbnail_size'],
                            false,
                            [
                                'class' => 'bdt-logo-carousel-img elementor-animation-' . esc_attr($settings['hover_animation']),
                                'alt'=> esc_attr( $image_alt ),
                            ]
                        );
                    
                    else :
                        printf( '<img class="bdt-logo-carousel-img elementor-animation-%s" src="%s" alt="%s">',
                            esc_attr( $settings['hover_animation'] ),
                            BDTEP_ASSETS_URL . 'images/gallery/item-'. ($index + 1) .'.svg',
                            esc_attr( $image_alt )
                            );
                    endif; ?>

                </figure>
            </<?php echo $tag; ?>>
        <?php endforeach; ?>

        <?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->render_header();
		$this->render_loop_item($settings);
		$this->render_footer($settings);
	}


}
