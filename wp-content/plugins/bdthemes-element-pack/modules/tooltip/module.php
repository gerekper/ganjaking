<?php

namespace ElementPack\Modules\Tooltip;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-tooltip';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'section_element_pack_tooltip_controls',
			[
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => BDTEP_CP . esc_html__('Tooltip', 'bdthemes-element-pack'),
			]
		);
		$element->end_controls_section();
	}


	public function register_controls($widget, $args) {

		$widget->add_control(
			'element_pack_widget_tooltip',
			[
				'label'              => esc_html__('Use Tooltip?', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__('Yes', 'bdthemes-element-pack'),
				'label_off'          => esc_html__('No', 'bdthemes-element-pack'),
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$widget->start_controls_tabs('element_pack_widget_tooltip_tabs');

		$widget->start_controls_tab(
			'element_pack_widget_tooltip_settings_tab',
			[
				'label'     => esc_html__('Settings', 'bdthemes-element-pack'),
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_text',
			[
				'label'              => esc_html__('Description', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::TEXTAREA,
				'default'            => 'This is Tooltip',
				'dynamic'            => ['active' => true],
				'condition'          => [
					'element_pack_widget_tooltip' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_placement',
			[
				'label'              => esc_html__('Placement', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => [
					'' => esc_html__('Top (Default)', 'bdthemes-element-pack'),

					'top-start' => esc_html__('Top Start', 'bdthemes-element-pack'),
					'top-end'   => esc_html__('Top End', 'bdthemes-element-pack'),

					'right'       => esc_html__('Right', 'bdthemes-element-pack'),
					'right-start' => esc_html__('Right Start', 'bdthemes-element-pack'),
					'right-end'   => esc_html__('Right End', 'bdthemes-element-pack'),

					'bottom'       => esc_html__('Bottom', 'bdthemes-element-pack'),
					'bottom-start' => esc_html__('Bottom Start', 'bdthemes-element-pack'),
					'bottom-end'   => esc_html__('Bottom End', 'bdthemes-element-pack'),

					'left'       => esc_html__('Left', 'bdthemes-element-pack'),
					'left-start' => esc_html__('Left Start', 'bdthemes-element-pack'),
					'left-end'   => esc_html__('Left End', 'bdthemes-element-pack'),

					'auto'       => esc_html__('Auto', 'bdthemes-element-pack'),
					'auto-start' => esc_html__('Auto Start', 'bdthemes-element-pack'),
					'auto-end'   => esc_html__('Auto End', 'bdthemes-element-pack'),
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip'               => 'yes',
					'element_pack_widget_tooltip_follow_cursor' => ''
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_follow_cursor',
			[
				'label'              => esc_html__('Follow Cursor', 'bdthemes-element-pack') . BDTEP_NC,
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip'               => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_animation',
			[
				'label'              => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => [
					'none'         => esc_html__('None', 'bdthemes-element-pack'),
					''             => esc_html__('Fade', 'bdthemes-element-pack'),
					'shift-away'   => esc_html__('Shift-Away', 'bdthemes-element-pack'),
					'shift-toward' => esc_html__('Shift-Toward', 'bdthemes-element-pack'),
					'scale'        => esc_html__('Scale', 'bdthemes-element-pack'),
					'perspective'  => esc_html__('Perspective', 'bdthemes-element-pack'),
					'fill'         => esc_html__('Fill Effect', 'bdthemes-element-pack'),
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_trigger',
			[
				'label'              => esc_html__('Trigger', 'bdthemes-element-pack') . BDTEP_NC,
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					''       => esc_html__('Hover', 'bdthemes-element-pack'),
					'click'  => esc_html__('Click', 'bdthemes-element-pack'),
					'manual' => esc_html__('Custom Trigger', 'bdthemes-element-pack'),

				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_custom_trigger',
			[
				'label'              => esc_html__('Custom Trigger', 'bdthemes-element-pack'),
				'placeholder'        => '.class-name',
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => ['active' => true],
				'condition'          => [
					'element_pack_widget_tooltip'         => 'yes',
					'element_pack_widget_tooltip_trigger' => 'manual',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		//			$widget->add_control(
		//				'element_pack_widget_tooltip_animation_duration',
		//				[
		//					'label'   => esc_html__('Animation Duration', 'bdthemes-element-pack'),
		//					'type'               => Controls_Manager::SLIDER,
		//					'default'            => [
		//						'sizes' => [
		//							'from' => 0,
		//							'to'   => 0,
		//						],
		//						'unit'  => 'ms',
		//					],
		//					'range'              => [
		//						'ms' => [
		//							'min'  => 0,
		//							'max'  => 5000,
		//							'step' => 100
		//						]
		//					],
		//					'labels'             => [
		//						esc_html__( 'From', 'bdthemes-element-pack' ),
		//						esc_html__( 'To', 'bdthemes-element-pack' ),
		//					],
		//					'scales'             => 1,
		//					'handles'            => 'range',
		//					'condition'          => [
		//						'element_pack_widget_tooltip'         => 'yes',
		//					],
		//					'render_type'        => 'none',
		//					'frontend_available' => true,
		//				]
		//			);


		$widget->add_control(
			'element_pack_widget_tooltip_x_offset',
			[
				'label'              => esc_html__('X Offset', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_y_offset',
			[
				'label'              => esc_html__('Y Offset', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_arrow',
			[
				'label'              => esc_html__('Arrow', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'element_pack_widget_tooltip' => 'yes',
					'element_pack_widget_tooltip_animation!' => 'fill'
				],
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'element_pack_widget_tooltip_styles_tab',
			[
				'label'     => esc_html__('Style', 'bdthemes-element-pack'),
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_tooltip_width',
			[
				'label'       => esc_html__('Max Width', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [
					'px',
					'em',
				],
				'range'       => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'          => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'max-width: calc({{SIZE}}{{UNIT}} - 10px) !important;',
				],
				'condition'   => [
					'element_pack_widget_tooltip' => 'yes',
				],
				//					'render_type' => 'none',
				//					'frontend_available' => true,
			]
		);


		$widget->add_control(
			'element_pack_widget_tooltip_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'element_pack_widget_tooltip_background',
				'selector'  => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_arrow_color',
			[
				'label'     => esc_html__('Arrow Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
				],
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
				'separator' => 'after',
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_tooltip_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'element_pack_widget_tooltip_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				'condition'   => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_tooltip_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_text_align',
			[
				'label'     => esc_html__('Text Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'element_pack_widget_tooltip_box_shadow',
				'selector'  => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'element_pack_widget_tooltip_typography',
				'selector'  => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				'condition' => [
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();
	}

	public function enqueue_scripts() {
		$suffix       = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$direction_suffix = is_rtl() ? '.rtl' : '';
		wp_enqueue_style('tippy-css', BDTEP_ASSETS_URL . 'css/tippy' . $direction_suffix . '.css', [], BDTEP_VER);
		wp_enqueue_script('popper-js', BDTEP_ASSETS_URL . 'vendor/js/popper.min.js', ['jquery'], null, true);
		wp_enqueue_script('tippy-js', BDTEP_ASSETS_URL . 'vendor/js/tippy.all.min.js', ['jquery'], null, true);
	}
	public function should_script_enqueue($widget) {
		if ('yes' === $widget->get_settings_for_display('element_pack_widget_tooltip')) {
			$this->enqueue_scripts();
			wp_enqueue_script('ep-tooltip');
		}
	}

	protected function add_actions() {

		add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);

		add_action('elementor/element/common/section_element_pack_tooltip_controls/before_section_end', [$this, 'register_controls'], 10, 2);

		//render scripts
		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
		add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
	}
}
