<?php

namespace ElementPack\Modules\GridLine;

use Elementor\Controls_Manager;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-grid-line';
	}

	public function register_controls($section) {

		$section->start_controls_section(
			'element_pack_grid_line_section',
			[
				'tab'   => Controls_Manager::TAB_SETTINGS,
				'label' => BDTEP_CP . esc_html__('Grid Line', 'bdthemes-element-pack'),
			]
		);

		$section->add_control(
			'ep_grid_line_enable',
			[
				'label'       => esc_html__('Grid Line?', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			]
		);

		$section->add_control(
			'ep_grid_line_line_color',
			[
				'label'     => esc_html__('Line Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-grid-line-color: {{VALUE}}',
				],
			]
		);

		$section->add_control(
			'ep_grid_line_column_color',
			[
				'label'     => esc_html__('Column Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-grid-line-column-color: {{VALUE}}',
				],
			]
		);


		$section->add_responsive_control(
			'ep_grid_line_columns',
			[
				'label'           => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 12,
				'step'            => 1,
				'devices'         => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => 12,
				'tablet_default'  => 12,
				'mobile_default'  => 12,
				'render_type'     => 'none',
				'condition'       => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => ['{{WRAPPER}}' => '--ep-grid-line-columns: {{VALUE}}']
			]
		);


		$section->add_control(
			'ep_grid_line_outline',
			[
				'label'       => esc_html__('Outline', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'condition'   => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => ['body::before' => 'outline: var(--ep-grid-line-width, 1px) solid var(--ep-grid-line-color, #eee)']
			]
		);

		$section->add_responsive_control(
			'ep_grid_line_max_width',
			[
				'label'      => esc_html__('Max Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 3800,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'render_type' => 'none',
				'condition'   => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => ['{{WRAPPER}}' => '--ep-grid-line-max-width: {{SIZE}}{{UNIT}}']
			]
		);

		$section->add_responsive_control(
			'ep_grid_line_line_width',
			[
				'label'      => esc_html__('Line Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'render_type' => 'none',
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'condition'   => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => ['{{WRAPPER}}' => '--ep-grid-line-width: {{SIZE}}{{UNIT}}']
			]
		);

		$section->add_control(
			'ep_grid_line_direction',
			[
				'label' => esc_html__('Line Direction (deg)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 15,
					],
				],
				'render_type' => 'none',
				'condition'   => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-grid-line-direction: {{SIZE}}deg;',
				],
			]
		);

		$section->add_control(
			'ep_grid_line_z_index',
			[
				'label'       => esc_html__('Z-index', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 9999,
				'render_type' => 'none',
				'condition'   => [
					'ep_grid_line_enable' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-grid-line-z-index: {{VALUE}};',
				],
			]
		);

		// $section->add_control(
		// 	'ep_grid_line_animation',
		// 	[
		// 		'label'              => esc_html__('Entrance Animation', 'bdthemes-element-pack'),
		// 		'type'               => Controls_Manager::SWITCHER,
		// 		'condition' => [
		// 			'ep_grid_line_enable' => 'yes'
		// 		],
		// 		'selectors' => [
		// 			'body:before' => 'animation-name: ep-grid-line-animation-top-to-bottom; animation-duration: 4s;',
		// 		],
		// 	]
		// );

		$section->add_control(
			'ep_grid_line_output',
			[
				'type'      => Controls_Manager::HIDDEN,
				'default'   => '1',
				'selectors' => [
					'body'         => 'position: relative;',
					'body::before' => '
									content       : "";
									position      : absolute;
									top           : 0;
									right         : 0;
									bottom        : 0;
									left          : 0;
									margin-right  : auto;
									margin-left   : auto;
									pointer-events: none;
									z-index       : var(--ep-grid-line-z-index, 0);
									min-height    : 100vh;

									width           : calc(100% - (2 * 0px));
									max-width       : var(--ep-grid-line-max-width, 100%);
									background-size : calc(100% + var(--ep-grid-line-width, 1px)) 100%;
									background-image: repeating-linear-gradient(var(--ep-grid-line-direction, 90deg), var(--ep-grid-line-column-color, transparent), var(--ep-grid-line-column-color, transparent) calc((100% / var(--ep-grid-line-columns, 12)) - var(--ep-grid-line-width, 1px)), var(--ep-grid-line-color, #eee) calc((100% / var(--ep-grid-line-columns, 12)) - var(--ep-grid-line-width, 1px)), var(--ep-grid-line-color, #eee) calc(100% / var(--ep-grid-line-columns, 12)));'

				],
				'condition' => [
					'ep_grid_line_enable' => 'yes'
				]
			]
		);

		$section->end_controls_section();
	}

	protected function add_actions() {

		add_action('elementor/documents/register_controls', [$this, 'register_controls'], 1, 1);
	}
}
