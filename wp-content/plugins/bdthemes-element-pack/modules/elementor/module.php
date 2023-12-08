<?php
namespace ElementPack\Modules\Elementor;

use Elementor;
use Elementor\Elementor_Base;
use Elementor\Controls_Manager;
use Elementor\Element_Base;
use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use ElementPack;
use ElementPack\Plugin;
use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Element_Pack_Loader;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public $sections_data = [];

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-elementor';
	}


	protected function add_actions() {

		add_action( 'elementor/element/after_section_end', [$this, 'lightbox_settings'],10, 3);
		add_action( 'elementor/element/after_section_end', [$this, 'tooltip_settings'],10, 3);
		
	}

	public function lightbox_settings($section, $section_id) {

		static $layout_sections = [ 'section_page_style'];

		if ( ! in_array( $section_id, $layout_sections ) ) { return; }

		$section->start_controls_section(
			'element_pack_lightbox_style',
			[
				'label' => BDTEP_CP . esc_html__( 'Lightbox Global Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$section->add_control(
			'element_pack_lightbox_bg',
			[
				'label'     => esc_html__( 'Lightbox Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox' => 'background-color: {{VALUE}};',
				],
			]
		);


		$section->add_control(
			'element_pack_cb_color',
			[
				'label'     => esc_html__( 'Close Button Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-close.bdt-icon' => 'color: {{VALUE}};',
				],
			]
		);
		
		$section->add_control(
			'element_pack_cb_bg',
			[
				'label'     => esc_html__( 'Close Button Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-close.bdt-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Border::get_type(), [
				'name'        => 'element_pack_cb_border',
				'label'       => esc_html__( 'Close Button Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-lightbox .bdt-close.bdt-icon',
			]
		);

		$section->add_control(
			'element_pack_cb_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.bdt-lightbox .bdt-close.bdt-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$section->add_control(
			'element_pack_cb_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'.bdt-lightbox .bdt-close.bdt-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$section->add_control(
			'element_pack_toolbar_color',
			[
				'label'     => esc_html__( 'Toolbar Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-lightbox-toolbar' => 'color: {{VALUE}};',
				],
			]
		);
		
		$section->add_control(
			'element_pack_toolbar_bg',
			[
				'label'     => esc_html__( 'Toolbar Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-lightbox-toolbar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'element_pack_toolbar_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '.bdt-lightbox .bdt-lightbox-toolbar',
			]
		);

		$section->add_control(
			'element_pack_lightbox_max_height',
			[
				'label'      => esc_html__( 'Max Height (vh)', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'vh',
				],
				'range'      => [
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'.bdt-lightbox .bdt-lightbox-items>*>*' => 'max-height: {{SIZE}}vh;',
				],
				'render_type'=> 'template',
				'separator'  => 'before',
			]
		);

		$section->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		
		$section->add_control(
			'element_pack_navigation_heading',
			[
				'label'     => esc_html__( 'N a v i g a t i o n', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::HEADING,
			]
		);

		$section->start_controls_tabs( 'element_pack_navigation_tabs_style' );

		$section->start_controls_tab(
			'element_pack_navigation_tab_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$section->add_control(
			'element_pack_navigation_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-lightbox-button' => 'color: {{VALUE}};',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'element_pack_navigation_bg_color',
				'selector' => '.bdt-lightbox .bdt-lightbox-button',
			]
		);

		$section->add_group_control(
			Group_Control_Border::get_type(), [
				'name'        => 'element_pack_navigation_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-lightbox .bdt-lightbox-button',
			]
		);

		$section->add_control(
			'element_pack_navigation_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.bdt-lightbox .bdt-lightbox-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$section->add_control(
			'element_pack_navigation_size',
			[
				'label'      => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'.bdt-lightbox .bdt-lightbox-button' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$section->add_control(
			'element_pack_navigation_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'.bdt-lightbox .bdt-lightbox-button' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$section->end_controls_tab();

		$section->start_controls_tab(
			'element_pack_navigation_tab_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$section->add_control(
			'element_pack_navigation_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-lightbox-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'element_pack_navigation_hover_bg_color',
				'selector' => '.bdt-lightbox .bdt-lightbox-button:hover',
			]
		);

		$section->add_control(
			'element_pack_navigation_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-lightbox .bdt-lightbox-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'element_pack_navigation_border_border!' => ''
				]
			]
		);

		$section->end_controls_tab();

		$section->end_controls_tabs();
		
		$section->end_controls_section();
	}

	public function tooltip_settings($section, $section_id) {
		
		static $layout_sections = [ 'section_page_style'];

		if ( ! in_array( $section_id, $layout_sections ) ) { return; }


		$section->start_controls_section(
			'element_pack_global_tooltip_style',
			[
				'label' => BDTEP_CP . esc_html__( 'Tooltip Global Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$section->add_responsive_control(
			'element_pack_global_tooltip_width',
			[
				'label'      => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range'      => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [
					'.elementor-widget .tippy-tooltip' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$section->add_control(
			'element_pack_global_tooltip_color',
			[
				'label'  => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor-widget .tippy-tooltip' => 'color: {{VALUE}}',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'element_pack_global_tooltip_background',
				'selector' => '.elementor-widget .tippy-tooltip, .elementor-widget .tippy-tooltip .tippy-backdrop',
			]
		);

		$section->add_control(
			'element_pack_global_tooltip_arrow_color',
			[
				'label'  => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor-widget .tippy-popper[x-placement^=left] .tippy-arrow'  => 'border-left-color: {{VALUE}}',
					'.elementor-widget .tippy-popper[x-placement^=right] .tippy-arrow' => 'border-right-color: {{VALUE}}',
					'.elementor-widget .tippy-popper[x-placement^=top] .tippy-arrow'   => 'border-top-color: {{VALUE}}',
					'.elementor-widget .tippy-popper[x-placement^=bottom] .tippy-arrow'=> 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'element_pack_global_tooltip'       => 'yes',
				],
			]
		);

		$section->add_responsive_control(
			'element_pack_global_tooltip_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.elementor-widget .tippy-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);

		$section->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'element_pack_global_tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.elementor-widget .tippy-tooltip',
			]
		);

		$section->add_responsive_control(
			'element_pack_global_tooltip_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.elementor-widget .tippy-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$section->add_control(
			'element_pack_global_tooltip_text_align',
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
					'.elementor-widget .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);


		$section->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'element_pack_global_tooltip_box_shadow',
				'selector' => '.elementor-widget .tippy-tooltip',
			]
		);
		
		$section->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'element_pack_global_tooltip_typography',
				'selector' => '.elementor-widget .tippy-tooltip .tippy-content',
			]
		);

		$section->end_controls_section();

	}

}
