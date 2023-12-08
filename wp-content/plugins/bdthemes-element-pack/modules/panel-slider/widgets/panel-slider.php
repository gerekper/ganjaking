<?php

namespace ElementPack\Modules\PanelSlider\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Plugin;

use ElementPack\Utils;
use ElementPack\Traits\Global_Swiper_Controls;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/**
 * Class Panel Slider
 */
class Panel_Slider extends Module_Base {

	use Global_Swiper_Controls;

	public function get_name() {
		return 'bdt-panel-slider';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Panel Slider', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-panel-slider';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'panel', 'slider' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-font', 'ep-panel-slider' ];
		}
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'imagesloaded', 'bdt-parallax', 'ep-scripts' ];
		} else {
			return [ 'imagesloaded', 'bdt-parallax', 'ep-panel-slider' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/_piVTeJd0g4';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[ 
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'_skin',
			[ 
				'label'   => esc_html__( 'Style', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [ 
					''               => esc_html__( 'On Hover', 'bdthemes-element-pack' ),
					'bdt-middle'     => esc_html__( 'On Active', 'bdthemes-element-pack' ),
					'always-visible' => esc_html__( 'Always Visible', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[ 
				'label'              => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => [ 
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
				'condition'          => [ 
					'_skin!' => 'bdt-middle',
				],
			]
		);

		$this->add_responsive_control(
			'skin_columns',
			[ 
				'label'          => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '2',
				'options'        => [ 
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
				],
				'condition'      => [ 
					'_skin' => 'bdt-middle',
				],
			]
		);

		$this->add_responsive_control(
			'column_space',
			[ 
				'label' => esc_html__( 'Column Space', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
			]
		);

		$this->add_responsive_control(
			'slider_height',
			[ 
				'label'      => esc_html__( 'Slider Height', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [ 
					'px' => [ 
						'min'  => 100,
						'step' => 20,
						'max'  => 1600
					],
					'vh' => [ 
						'min'  => 1,
						'step' => 1,
						'max'  => 100
					]
				],
				'default'    => [ 
					'size' => 620,
				],
				'selectors'  => [ 
					'{{WRAPPER}} .swiper-wrapper' => 'height: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'show_title',
			[ 
				'label'   => esc_html__( 'Show Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[ 
				'label'     => __( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => element_pack_title_tags(),
				'condition' => [ 
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'button',
			[ 
				'label'       => esc_html__( 'Show Read More', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => 'It will work when link field no null.',
				'condition'   => [ 
					'_skin!' => 'bdt-middle',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'    => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [ 
						'title' => esc_html__( 'Justified', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				// 'prefix_class' => 'elementor%s-align-',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slider' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slide_skew',
			[ 
				'label'     => esc_html__( 'Slide Skew', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 30,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slider .bdt-panel-slide-item' => 'transform: skew(-{{SIZE}}deg);',
					'{{WRAPPER}} .bdt-panel-slider .bdt-panel-slide-desc' => 'transform: skew({{SIZE}}deg);',
					'{{WRAPPER}} .bdt-panel-slider .bdt-panel-slide-link' => 'transform: skew(-{{SIZE}}deg);',
					'{{WRAPPER}} .bdt-panel-slider span'                  => 'transform: skew({{SIZE}}deg); display: inline-block;',
				],
				'condition' => [ 
					'_skin!' => 'bdt-middle',
				]
			]
		);

		$this->add_control(
			'global_link',
			[ 
				'label'        => __( 'Item Wrapper Link', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-global-link-',
				'description'  => __( 'Be aware! When Item Wrapper Link activated then read more link will not work', 'bdthemes-element-pack' ),
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'mouse_interactivity',
			[ 
				'label'        => __( 'Item Mouse Interaction', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'prefix_class' => 'ep-mouse-interaction-',
				'render_type'  => 'template'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_sliders',
			[ 
				'label' => esc_html__( 'Sliders', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[ 
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'Slide Title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_image',
			[ 
				'label'       => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => [ 'active' => true ],
				'description' => __( 'Use same size ratio image', 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'tab_content',
			[ 
				'label'      => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => [ 'active' => true ],
				'default'    => esc_html__( 'Slide Content', 'bdthemes-element-pack' ),
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'tab_link',
			[ 
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'default'     => [ 
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'tabs',
			[ 
				'label'       => esc_html__( 'Slider Items', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'tab_title'   => esc_html__( 'Slide #1', 'bdthemes-element-pack' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text.', 'bdthemes-element-pack' ),
					],
					[ 
						'tab_title'   => esc_html__( 'Slide #2', 'bdthemes-element-pack' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text.', 'bdthemes-element-pack' ),
					],
					[ 
						'tab_title'   => esc_html__( 'Slide #3', 'bdthemes-element-pack' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text.', 'bdthemes-element-pack' ),
					],
					[ 
						'tab_title'   => esc_html__( 'Slide #4', 'bdthemes-element-pack' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text.', 'bdthemes-element-pack' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[ 
				'name'      => 'thumbnail_size',
				'label'     => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'exclude'   => [ 'custom' ],
				'default'   => 'full',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button',
			[ 
				'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'condition' => [ 
					'button' => 'yes',
					'_skin!' => 'bdt-middle',
				],
			]
		);

		$this->add_control(
			'button_text',
			[ 
				'label'       => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Read More', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'panel_slider_icon',
			[ 
				'label'            => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'label_block'      => false,
				'skin'             => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[ 
				'label'     => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [ 
					'left'  => esc_html__( 'Left', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'Right', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'panel_slider_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[ 
				'label'     => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 8,
				],
				'range'     => [ 
					'px' => [ 
						'max' => 50,
					],
				],
				'condition' => [ 
					'panel_slider_icon[value]!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slider .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-panel-slider .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[ 
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		//Style
		$this->start_controls_section(
			'section_style_slider',
			[ 
				'label' => esc_html__( 'Slider', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slider_overlay_background_color',
			[ 
				'label'     => esc_html__( 'Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-item:before' => 'background-color: {{VALUE}}; transition: all .3s ease;',
				],
			]
		);

		$this->add_control(
			'slider_active_overlay_color',
			[ 
				'label'     => esc_html__( 'Active Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-item.swiper-slide-active:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_background_color',
			[ 
				'label'     => esc_html__( 'Overlay Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-overlay-gradient' => 'background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 40%, {{VALUE}} 100%);',
				],
			]
		);

		$this->add_control(
			'slider_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 0.4,
				],
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-thumb img'                                                                => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .bdt-skin-middle .swiper-slide:not(.swiper-slide-active):hover .bdt-panel-slide-thumb img' => 'opacity: {{SIZE}} !important;',

				],
			]
		);

		$this->add_responsive_control(
			'desc_padding',
			[ 
				'label'     => esc_html__( 'Description Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-desc' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_control(
			'shadow_mode',
			[ 
				'label'        => esc_html__( 'Shadow Mode', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-shadow-mode-',
			]
		);

		$this->add_control(
			'shadow_color',
			[ 
				'label'     => esc_html__( 'Shadow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'shadow_mode' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}} .elementor-widget-container:before' => 'background: linear-gradient(to right,
					{{VALUE}} 5%,rgba(255,255,255,0) 100%);',
					'{{WRAPPER}} .elementor-widget-container:after'  => 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 95%);',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[ 
				'label'     => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-panel-slide-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'title_text_stroke',
				'label'    => __( 'Text_Stroke', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-panel-slide-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[ 
				'label' => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'text_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-panel-slide-text',
			]
		);

		$this->add_responsive_control(
			'text_top_spacing',
			[ 
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[ 
				'label'      => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'terms' => [ 
						[ 
							'name'  => 'button',
							'value' => 'yes'
						],
						[ 
							'name'     => '_skin',
							'operator' => '!=',
							'value'    => 'bdt-middle'
						],
					]
				]
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-link'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-panel-slide-link svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-panel-slide-link',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-panel-slide-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-panel-slide-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_spacing',
			[ 
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-link' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-panel-slide-link',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-panel-slide-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-link:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-panel-slide-link:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-link:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-panel-slide-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[ 
				'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[ 
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
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
		$this->register_navigation_style_controls( 'swiper-carousel' );

		$this->update_responsive_control(
			'arrows_acx_position',
			[ 
				'default'        => [ 
					'size' => 20,
				],
				'tablet_default' => [ 
					'size' => 20,
				],
				'mobile_default' => [ 
					'size' => 20,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_header() {
		$id         = 'bdt-panel-slider-' . $this->get_id();
		$settings   = $this->get_settings_for_display();
		$skin_class = '';

		$elementor_vp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_vp_md = get_option( 'elementor_viewport_md' );
		$viewport_lg     = ! empty( $elementor_vp_lg ) ? $elementor_vp_lg - 1 : 1023;
		$viewport_md     = ! empty( $elementor_vp_md ) ? $elementor_vp_md - 1 : 767;


		if ( 'arrows' == $settings['navigation'] ) {
			$this->add_render_attribute( 'panel-slider', 'class', 'bdt-arrows-align-' . $settings['arrows_position'] );
		} elseif ( 'dots' == $settings['navigation'] ) {
			$this->add_render_attribute( 'panel-slider', 'class', 'bdt-dots-align-' . $settings['dots_position'] );
		} elseif ( 'both' == $settings['navigation'] ) {
			$this->add_render_attribute( 'panel-slider', 'class', 'bdt-arrows-dots-align-' . $settings['both_position'] );
		} elseif ( 'arrows-fraction' == $settings['navigation'] ) {
			$this->add_render_attribute( 'panel-slider', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position'] );
		}

		if ( 'arrows-fraction' == $settings['navigation'] ) {
			$pagination_type = 'fraction';
		} elseif ( 'both' == $settings['navigation'] or 'dots' == $settings['navigation'] ) {
			$pagination_type = 'bullets';
		} elseif ( 'progressbar' == $settings['navigation'] ) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$columns        = ( $settings['_skin'] == 'bdt-middle' ) ? $settings['skin_columns'] : $settings['columns'];
		$columns_tablet = ( $settings['_skin'] == 'bdt-middle' ) && isset( $settings['skin_columns_tablet'] ) ? $settings['skin_columns_tablet'] : $settings['columns_tablet'];
		$columns_mobile = ( $settings['_skin'] == 'bdt-middle' ) && isset( $settings['skin_columns_mobile'] ) ? $settings['skin_columns_mobile'] : $settings['columns_mobile'];

		if ( $settings['_skin'] == 'bdt-middle' ) {
			$skin_class = 'bdt-skin-middle';
		} else if ( $settings['_skin'] == 'always-visible' ) {
			$skin_class = 'bdt-text-on-always';
		} else {
			$skin_class = 'bdt-skin-default';
		}

		$this->add_render_attribute(
			[ 
				'panel-slider' => [ 
					'data-settings' => [ 
						wp_json_encode( array_filter( [ 
							"autoplay"        => ( "yes" == $settings["autoplay"] ) ? [ "delay" => $settings["autoplay_speed"] ] : false,
							"loop"            => ( $settings["loop"] == "yes" ) ? true : false,
							"speed"           => $settings["speed"]["size"],
							"pauseOnHover"    => ( "yes" == $settings["pauseonhover"] ) ? true : false,
							"slidesPerView"   => (int) $columns_mobile,
							"slidesPerGroup"  => isset( $settings["slides_to_scroll_mobile"] ) ? (int) $settings["slides_to_scroll_mobile"] : 1,
							"spaceBetween"    => $settings['column_space']['size'] ?: 0,
							"centeredSlides"  => ( $settings["centered_slides"] === "yes" ) ? true : false,
							"grabCursor"      => ( $settings["grab_cursor"] === "yes" ) ? true : false,
							"freeMode"        => ( $settings["free_mode"] === "yes" ) ? true : false,
							"effect"          => $settings["skin"],
							"observer"        => ( $settings["observer"] ) ? true : false,
							"observeParents"  => ( $settings["observer"] ) ? true : false,
							"breakpoints"     => [ 
								(int) $viewport_md => [ 
									"slidesPerView"  => (int) $columns_tablet,
									"spaceBetween"   => $settings['column_space']['size'] ?: 0,
									"slidesPerGroup" => isset( $settings["slides_to_scroll_tablet"] ) ? (int) $settings["slides_to_scroll_tablet"] : 1,
								],
								(int) $viewport_lg => [ 
									"slidesPerView"  => (int) $columns,
									"spaceBetween"   => $settings['column_space']['size'] ?: 0,
									"slidesPerGroup" => isset( $settings["slides_to_scroll"] ) ? (int) $settings["slides_to_scroll"] : 1,
								]
							],
							"navigation"      => [ 
								"nextEl" => "#" . $id . " .bdt-navigation-next",
								"prevEl" => "#" . $id . " .bdt-navigation-prev",
							],
							"pagination"      => [ 
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ( "yes" == $settings["dynamic_bullets"] ) ? true : false,
							],
							"scrollbar"       => [ 
								"el"   => "#" . $id . " .swiper-scrollbar",
								"hide" => "true",
							],
							'coverflowEffect' => [ 
								'rotate'       => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_rotate"]["size"] : 50,
								'stretch'      => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_stretch"]["size"] : 0,
								'depth'        => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_depth"]["size"] : 100,
								'modifier'     => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_modifier"]["size"] : 1,
								'slideShadows' => true,
							],


						] ) )
					],
					'class'         => [ 
						'bdt-panel-slider',
						$skin_class,
					],
					'id'            => $id
				]
			]
		);

		$mouse_interactivity = isset( $settings['mouse_interactivity'] ) && $settings['mouse_interactivity'] == 'yes' ? true : false;

		$this->add_render_attribute(
			[ 
				'panel-slider' => [ 
					'data-widget-settings' => [ 
						wp_json_encode( [ 
							'id'                 => '#' . $id,
							'mouseInteractivity' => $mouse_interactivity
						] )
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute( 'swiper', 'class', 'swiper-carousel ' . $swiper_class );


		?>
		<div <?php echo $this->get_render_attribute_string( 'panel-slider' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
					<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$this->render_header();
		$counter = 1;

		?>

					<?php
					foreach ( $settings['tabs'] as $index=> $item ) : 

						$element_key = 'item_' . $index;

						$image_src = Group_Control_Image_Size::get_attachment_image_src( $item['tab_image']['id'], 'thumbnail_size', $settings );
						$image_url = $image_src ?: BDTEP_ASSETS_URL . '/images/panel-slider.svg';

						$this->add_render_attribute(
							[ 
								$element_key => [ 
									'class'  => [ 
										'bdt-panel-slide-link',
										'bdt-transition-slide-bottom',
										$settings['button_hover_animation'] ? 'elementor-animation-' . $settings['button_hover_animation'] : ''
									],
								]
							],
							'',
							'',
							true
						);
						$this->add_link_attributes( $element_key, $item['tab_link'] );

						$this->add_render_attribute( 'panel-slide-item', 'class', [ 'bdt-panel-slide-item', 'swiper-slide', 'bdt-transition-toggle' ], true );

						if ( 'yes' == $settings['global_link'] and $item['tab_link']['url'] ) {

							$target = $item['tab_link']['is_external'] ? '_blank' : '_self';

							$this->add_render_attribute( 'panel-slide-item', 'onclick', "window.open('" . $item['tab_link']['url'] . "', '$target')", true );
						}

						if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
							// add old default
							$settings['icon'] = 'fas fa-arrow-right';
						}

						$migrated = isset( $settings['__fa4_migrated']['panel_slider_icon'] );
						$is_new   = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

						$this->add_render_attribute( 'panel-slide-item-title', 'class', [ 'bdt-panel-slide-title bdt-transition-slide-bottom' ], true );

						?>
						<div <?php echo $this->get_render_attribute_string( 'panel-slide-item' ); ?>>

							<div class="bdt-panel-slide-thumb-wrapper">
								<div class="bdt-panel-slide-thumb bdt-background-cover" data-depth="0.2"
									style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>
							</div>
							<div class="bdt-panel-slide-desc bdt-position-bottom-left bdt-position-z-index">

								<?php if ( 'yes' == $settings['show_title'] ) : ?>
									<<?php echo Utils::get_valid_html_tag( $settings['title_tags'] ); ?>
										<?php echo $this->get_render_attribute_string( 'panel-slide-item-title' ); ?>>
										<?php echo esc_html( $item['tab_title'] ); ?>
									</<?php echo Utils::get_valid_html_tag( $settings['title_tags'] ); ?>>
								<?php endif; ?>

								<?php if ( '' !== $item['tab_content'] ) : ?>
									<div class="bdt-panel-slide-text bdt-transition-slide-bottom">
										<?php echo $this->parse_text_editor( $item['tab_content'] ); ?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $item['tab_link']['url'] ) ) : ?>
									<?php if ( $settings['button'] == 'yes' and 'bdt-middle' != $settings['_skin'] ) : ?>
										<a <?php echo $this->get_render_attribute_string( $element_key ); ?>>
											<span>
												<?php echo esc_html( $settings['button_text'] ); ?>
											</span>
											<?php if ( $settings['panel_slider_icon']['value'] ) : ?>
												<span class="bdt-button-icon-align-<?php echo esc_attr( $settings['icon_align'] ); ?>">

													<?php if ( $is_new || $migrated ) :
														Icons_Manager::render_icon( $settings['panel_slider_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
													else : ?>
														<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
													<?php endif; ?>

												</span>
											<?php endif; ?>
										</a>
									<?php endif; ?>

								<?php endif; ?>

							</div>

							<?php if ( '' !== $item['tab_content'] or 'yes' == $settings['show_title'] ) : ?>
								<div class="bdt-transition-fade bdt-position-cover bdt-overlay bdt-overlay-gradient"></div>
							<?php endif; ?>
						</div>
						<?php
						$counter++;
					endforeach; ?>

					<?php $this->render_footer();
	}
}
