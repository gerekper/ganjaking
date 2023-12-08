<?php
namespace ElementPack\Modules\TrailerBox\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use ElementPack\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Trailer_Box extends Module_Base {

	public function get_name() {
		return 'bdt-trailer-box';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Trailer Box', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-trailer-box';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'trailer', 'box' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-trailer-box' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/3AR5RlBAAYg';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'pre_title',
			[
				'label'       => esc_html__( 'Pre Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'Trailer box pre title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'Trailer box title', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Trailer Box Title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'content',
			[
				'label'       => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'Trailer box text' , 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'I am Trailer Box Description Text. You can change me anytime from settings.' , 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__( 'Content Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box-desc' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'origin',
			[
				'label'   => esc_html__( 'Origin', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-left',
				'options' => element_pack_position(),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
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
					'justify' => [
						'title' => esc_html__( 'Justified', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'description'  => 'Use align for matching position',
				'default'      => '',
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Maximum Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'separator'	=> 'before',
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'   => esc_html__( 'Minimum Height', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1024,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => __( 'Link', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''       => __( 'None', 'bdthemes-element-pack' ),
					'button' => __( 'Button', 'bdthemes-element-pack' ),
					'item'   => __( 'Item', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'button',
			[
				'label'       => esc_html__( 'link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'link_type!' => '',
				],
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button',
			[
				'label'     => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'condition' => [
					'link_type' => 'button',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'View Details', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'View Details', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Before', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'After', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'button_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label'   => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'button_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container .bdt-trailer-box-button-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-container .bdt-trailer-box-button-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_position',
			[
				'label'   => esc_html__( 'Button Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => element_pack_position(),
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => __( 'Button ID', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//Style

		$this->start_controls_section(
			'section_trailer_box_style',
			[
				'label' => esc_html__( 'Trailer Box', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_animation',
			[
				'label'        => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'content',
				'prefix_class' => 'bdt-item-transition-',				
				'render_type'  => 'ui',
				'options'      => [
					'content'    => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'scale-up'   => esc_html__( 'Image Scale Up', 'bdthemes-element-pack' ),
					'scale-down' => esc_html__( 'Image Scale Down', 'bdthemes-element-pack' ),
					'none'       => esc_html__( 'None', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_responsive_control(
			'trailer_box_content_padding',
			[
				'label'      => esc_html__( 'Content Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pre_title',
			[
				'label'     => esc_html__( 'Pre Tilte', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pre_title!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_pre_title_style' );

		$this->start_controls_tab(
			'tab_pre_title_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'pre_title_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-pre-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pre_title_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-pre-title' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'pre_title_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'selector'    => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-pre-title',
			]
		);

		$this->add_control(
			'pre_title_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-pre-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'tb_pre_title_shadow',
				'selector' => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-desc-inner .bdt-trailer-box-pre-title',
			]
		);

		$this->add_responsive_control(
			'tb_pre_title_spacing',
			[
				'label' => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-desc-inner .bdt-trailer-box-pre-title' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'tb_pre_title_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-desc-inner .bdt-trailer-box-pre-title' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'pre_title_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-pre-title',
			]
		);

		$this->add_control(
			'pre_title_offset_toggle',
			[
				'label' => __('Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('None', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
			]
		);
		
		$this->start_popover();

		$this->add_responsive_control(
			'pre_title_x_position',
			[
				'label'   => __( 'X Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
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
						'min' => -800,
						'max' => 800,
					],
				],
				'condition' => [
					'pre_title_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-pre-title-x-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'pre_title_y_position',
			[
				'label'   => __( 'Y Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
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
						'min' => -800,
						'max' => 800,
					],
				],
				'condition' => [
					'pre_title_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-pre-title-y-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'pre_title_rotate',
			[
				'label'   => __( 'Rotate', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
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
						'min'  => -180,
						'max'  => 180,
						'step' => 5,
					],
				],
				'condition' => [
					'pre_title_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-pre-title-rotate: {{SIZE}}deg;'
				],
			]
		);

		// $this->add_responsive_control(
		// 	'pre_title_scale',
		// 	[
		// 		'label'   => __( 'Scale', 'bdthemes-element-pack' ),
		// 		'type'    => Controls_Manager::SLIDER,
		// 		'range' => [
		// 			'px' => [
		// 				'min'  => 0,
		// 				'max'  => 2,
		// 				'step' => 0.5,
		// 			],
		// 		],
		// 		'condition' => [
		// 			'pre_title_offset_toggle' => 'yes'
		// 		],
		// 		'render_type' => 'ui',
		// 		'selectors' => [
		// 			'{{WRAPPER}}' => '--ep-trailer-box-pre-title-scale: {{SIZE}};'
		// 		],
		// 	]
		// );

		$this->end_popover();

		$this->add_control(
			'pre_title_hide',
			[
				'label'       => __( 'Hide at', 'bdthemes-element-pack' ),
				'description' => __( 'Some cases you need to hide it because when you set heading at outer position mobile device can show wrong width in that case you can hide it at mobile or tablet device. if you set overflow hidden on section or body so you don\'t need it.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'm',
				'options'     => [
					''  => esc_html__('Nothing', 'bdthemes-element-pack'),
					'm' => esc_html__('Tablet and Mobile ', 'bdthemes-element-pack'),
					's' => esc_html__('Mobile', 'bdthemes-element-pack'),
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pre_title_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'pre_title_color_hover',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-pre-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pre_title_bg__hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-pre-title' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pre_title_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'pre_title_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-pre-title' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'tb_pre_title_shadow_hover',
				'selector' => '{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-desc-inner .bdt-trailer-box-pre-title',
			]
		);

		$this->add_control(
			'tb_pre_title_opacity_hover',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-desc-inner .bdt-trailer-box-pre-title' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'pre_title_hover_offset_toggle',
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
			'pre_title_hover_x_position',
			[
				'label'   => __( 'X Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -800,
						'max' => 800,
					],
				],
				'condition' => [
					'pre_title_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-pre-title-hover-x-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'pre_title_hover_y_position',
			[
				'label'   => __( 'Y Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -800,
						'max' => 800,
					],
				],
				'condition' => [
					'pre_title_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-pre-title-hover-y-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'pre_title_hover_rotate',
			[
				'label'   => __( 'Rotate', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
						'step' => 5,
					],
				],
				'condition' => [
					'pre_title_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-pre-title-hover-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->add_control(
			'pre_title_transition_delay',
			[
				'label' => __( 'Transition Delay', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-desc-inner .bdt-trailer-box-pre-title' => 'transition-delay: {{SIZE}}s;',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__( 'Tilte', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title',
			]
		);

		$this->add_control(
			'title_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'title_offset_toggle',
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
			'title_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'title_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-title-x-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'title_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'title_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-title-y-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'title_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'title_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-title-rotate: {{SIZE}}deg;'
				],
			]
		);
		
		$this->end_popover();

		$this->add_control(
			'title_advanced_style',
			[
				'label' => esc_html__('Advanced Style', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_background',
				'selector' => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 	   => 'title_border',
				'selector' => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'		 => __('Border Radius', 'bdthemes-element-pack'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'title_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_padding',
			[
				'label' 	 => __('Padding', 'bdthemes-element-pack'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_hover_background',
				'selector' => '{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-title',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'title_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-pre-title' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-title' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_offset_toggle',
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
			'title_hover_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'title_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-title-hover-x-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'title_hover_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'title_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-title-hover-y-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'title_hover_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'title_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-title-hover-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->add_control(
			'title_transition_delay',
			[
				'label' => __( 'Transition Delay', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-desc-inner .bdt-trailer-box-title' => 'transition-delay: {{SIZE}}s;',
				],
			]
		);
		
		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_text_style' );

		$this->start_controls_tab(
			'tab_text_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-text' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'text_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-text',
			]
		);

		$this->add_control(
			'text_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-text' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'text_offset_toggle',
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
			'text_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'text_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-text-x-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'text_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'text_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-text-y-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'text_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'text_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-text-rotate: {{SIZE}}deg;'
				],
			]
		);
		
		$this->end_popover();

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_text_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_bg_color_hover',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_hover_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-text' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'text_hover_offset_toggle',
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
			'text_hover_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'text_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-text-hover-x-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'text_hover_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'text_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-text-hover-y-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'text_hover_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'text_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-text-hover-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->add_control(
			'text_transition_delay',
			[
				'label' => __( 'Transition Delay', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-text' => 'transition-delay: {{SIZE}}s;',
				],
			]
		);
		
		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'link_type' => 'button',
				],
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
					'{{WRAPPER}} .elementor-widget-container a.bdt-trailer-box-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-container a.bdt-trailer-box-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.bdt-trailer-box-button' => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} a.bdt-trailer-box-button',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} a.bdt-trailer-box-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} a.bdt-trailer-box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} a.bdt-trailer-box-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} a.bdt-trailer-box-button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} a.bdt-trailer-box-button',
			]
		);

		$this->add_control(
			'button_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box .bdt-trailer-box-button' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'button_offset_toggle',
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
			'button_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'button_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-button-x-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'button_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'button_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-button-y-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'button_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'button_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-button-rotate: {{SIZE}}deg;'
				],
			]
		);
		
		$this->end_popover();

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
					'{{WRAPPER}}.elementor-widget-bdt-trailer-box:hover a.bdt-trailer-box-button' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-bdt-trailer-box:hover a.bdt-trailer-box-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-bdt-trailer-box:hover a.bdt-trailer-box-button' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}}.elementor-widget-bdt-trailer-box:hover a.bdt-trailer-box-button' => 'border-color: {{VALUE}};',
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

		$this->add_control(
			'button_hover_opacity',
			[
				'label' => __( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-button' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_offset_toggle',
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
			'button_hover_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'button_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-button-hover-x-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'button_hover_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'button_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-button-hover-y-offset: {{SIZE}}px;'
				],
			]
		);
		
		$this->add_responsive_control(
			'button_hover_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'button_hover_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-trailer-box-button-hover-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->add_control(
			'button_transition_delay',
			[
				'label' => __( 'Transition Delay', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-trailer-box:hover .bdt-trailer-box-button' => 'transition-delay: {{SIZE}}s;',
				],
			]
		);
		
		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Background Overlay
		$this->start_controls_section(
			'section_advanced_background_overlay',
			[
				'label'     => esc_html__( 'Background Overlay', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_ADVANCED,
				'condition' => [
					'_background_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->start_controls_tabs( 'tabs_background_overlay' );

		$this->start_controls_tab(
			'tab_background_overlay_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'background_overlay',
				'selector' => '{{WRAPPER}} .elementor-widget-container > .elementor-background-overlay',
			]
		);

		$this->add_control(
			'background_overlay_opacity',
			[
				'label'   => esc_html__( 'Opacity (%)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container > .elementor-background-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_background_overlay_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'background_overlay_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container > .elementor-background-overlay',
			]
		);

		$this->add_control(
			'background_overlay_hover_opacity',
			[
				'label'   => esc_html__( 'Opacity (%)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}}:hover .elementor-widget-container > .elementor-background-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_hover_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	public function render_button() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset( $settings['__fa4_migrated']['button_icon'] );
		$is_new    = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		?>

		<?php if ('button' === $settings['link_type']) : ?>
			<?php if (( '' !== $settings['button']['url'] ) and ('' !== $settings['button_text'] )) :

				$this->add_link_attributes( 'trailer-box-button', $settings['button'] );
				$this->add_render_attribute(
					[
						'trailer-box-button' => [
							'class'  => [
								'bdt-trailer-box-button',
								$settings['button_hover_animation'] ? 'elementor-animation-'.$settings['button_hover_animation'] : ''
							]
						]
					]
				);
				$this->add_render_attribute(
					[
						'trailer-box-button-position' => [
							'class'  => [
								'bdt-trailer-box-button-position',
								' bdt-position-' . $settings['button_position'],
							]
						]
					]
				);

				if ( ! empty( $settings['button_css_id'] ) ) {
					$this->add_render_attribute( 'trailer-box-button', 'id', $settings['button_css_id'] );
				}

				?>
				<div <?php echo $this->get_render_attribute_string( 'trailer-box-button-position' ); ?>>
					<a <?php echo $this->get_render_attribute_string( 'trailer-box-button' ); ?>>
						<?php echo esc_html($settings['button_text']); ?>
	
						<?php if ($settings['button_icon']['value']) : ?>
							<span class="bdt-trailer-box-button-icon-<?php echo esc_attr($settings['icon_align']); ?>">
	
								<?php if ( $is_new || $migrated ) :
									Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
								else : ?>
									<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
								<?php endif; ?>
	
							</span>
						<?php endif; ?>
	
					</a>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php
	}

	public function render() {
		$settings               = $this->get_settings_for_display();
		$target   = (isset($settings['button']['is_external'])  && $settings['button']['is_external'] == 'on' ) ? '_blank' : '_self';
		
		$origin                 = ' bdt-position-' . $settings['origin'];
		$has_background_overlay = in_array( $settings['background_overlay_background'], [ 'classic', 'gradient' ] ) ||
		in_array( $settings['background_overlay_hover_background'], [ 'classic', 'gradient' ] );
		

		if ( $has_background_overlay ) : ?>
			<div class="elementor-background-overlay"></div>
		<?php endif; ?>

		<?php if ('item' === $settings['link_type']) : ?>
			<div onclick="window.open('<?php echo esc_url($settings['button']['url']); ?>','<?php echo esc_attr($target); ?>');" style="cursor: pointer;">
		<?php endif; ?>

		<?php if ($settings['pre_title']) {

			$this->add_render_attribute(
				[
					'avd-hclass' => [
						'class' => [
							'bdt-trailer-box-pre-title',
							$settings['pre_title_hide'] ? 'bdt-visible@'. $settings['pre_title_hide'] : '',
						],
					],
				]
			);
		} 
		
		$this->add_render_attribute('bdt-trailer-box-title', 'class', 'bdt-trailer-box-title');
		
		?>

			<div class="bdt-trailer-box bdt-position-relative">
				<div class="bdt-trailer-box-desc <?php echo esc_attr($origin); ?>">
					<div class="bdt-trailer-box-desc-inner">
						<?php if ( '' !== $settings['pre_title'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'avd-hclass' );?>>
								<?php echo wp_kses( $settings['pre_title'], element_pack_allow_tags('title') ); ?>
							</div>
						<?php endif; ?>

						<?php if ( '' !== $settings['title'] ) : ?>
							<div class="bdt-trailer-box-title-wrap">
								<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-trailer-box-title'); ?>>
									<?php echo wp_kses( $settings['title'], element_pack_allow_tags('title') ); ?>
								</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
							</div>
						<?php endif; ?>

						<?php if ( '' !== $settings['content'] ) : ?>
							<div class="bdt-trailer-box-text"><?php echo wp_kses_post($settings['content']); ?></div>
						<?php endif; ?>

						<?php if ( ! $settings['button_position']) : ?>
							<?php $this->render_button(); ?>
						<?php endif; ?>
					</div>
					
				</div>
				
			</div>

			<?php if ( $settings['button_position']) : ?>
				<?php $this->render_button(); ?>
			<?php endif; ?>

		<?php if ('item' === $settings['link_type']) : ?>
			</div>
		<?php endif; ?>

		<?php
	}
}
