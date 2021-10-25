<?php

namespace Elementor;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Master_Addons_Piechart extends Widget_Base
{

	public function get_name()
	{
		return 'ma-piecharts';
	}

	public function get_title()
	{
		return esc_html__('Piecharts', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-counter-circle';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_script_depends()
	{
		return [
			'elementor-waypoints',
			//				'master-addons-scripts',
			//				'master-addons-waypoints',
			'jquery-stats'
		];
	}




	protected function _register_controls()
	{

		$this->start_controls_section(
			'section_piecharts',
			[
				'label' => __('Piecharts', MELA_TD),
			]
		);

		$this->add_responsive_control(
			'per_line',
			[
				'label' => __('Piecharts per row', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'piecharts',
			[
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'stats_title' => __('Web Design', MELA_TD),
						'percentage_value' => 89,
					],
					[
						'stats_title' => __('SEO Services', MELA_TD),
						'percentage_value' => 77,
					],
					[
						'stats_title' => __('WordPress Development', MELA_TD),
						'percentage_value' => 95,
					],
					[
						'stats_title' => __('Brand Marketing', MELA_TD),
						'percentage_value' => 45,
					],
				],
				'fields' => [
					[
						'name' => 'stats_title',
						'label' => __('Stats Title', MELA_TD),
						'type' => Controls_Manager::TEXT,
						'default' => __('My stats title', MELA_TD),
						'description' => __('The title for the piechart', MELA_TD),
						'dynamic' => [
							'active' => true,
						],
					],
					[
						'name' => 'percentage_value',
						'label' => __('Percentage Value', MELA_TD),
						'type' => Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 100,
						'step' => 1,
						'default' => 30,
						'description' => __('The percentage value for the stats.', MELA_TD),
					],

				],
				'title_field' => '{{{ stats_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_styling',
			[
				'label' => __('Piechart Styling', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bar_color',
			[
				'label' => __('Bar color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#704aff',
			]
		);


		$this->add_control(
			'track_color',
			[
				'label' => __('Track color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#dddddd',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_stats_title',
			[
				'label' => __('Stats Title', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'stats_title_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-piechart .ma-el-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'stats_title_typography',
				'selector' => '{{WRAPPER}} .ma-el-piechart .ma-el-label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stats_percentage',
			[
				'label' => __('Stats Percentage', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'stats_percentage_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-piechart .ma-el-percentage span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'stats_percentage_typography',
				'selector' => '{{WRAPPER}} .ma-el-piechart .ma-el-percentage span',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stats_percentage_symbol',
			[
				'label' => __('Stats Percentage Symbol', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'stats_percentage_symbol_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-piechart .ma-el-percentage sup' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'stats_percentage_symbol_typography',
				'selector' => '{{WRAPPER}} .ma-el-piechart .ma-el-percentage sup',
			]
		);
	}

	function ma_el_get_grid_classes($settings, $columns_field = 'per_line')
	{
		$grid_classes = ' ma-el-grid-desktop-';
		$grid_classes .= $settings[$columns_field];
		$grid_classes .= ' ma-el-grid-tablet-';
		$grid_classes .= $settings[$columns_field . '_tablet'];
		$grid_classes .= ' ma-el-grid-mobile-';
		$grid_classes .= $settings[$columns_field . '_mobile'];
		return apply_filters(
			'ma_el_grid_classes',
			$grid_classes,
			$settings,
			$columns_field
		);
	}


	protected function render()
	{

		$settings = $this->get_settings_for_display();

		$settings = apply_filters('ma_el_piecharts_' . $this->get_id() . '_settings', $settings);

		$bar_color = ' data-bar-color="' . esc_attr($settings['bar_color']) . '"';
		$track_color = ' data-track-color="' . esc_attr($settings['track_color']) . '"';

		$output = '<div class="ma-el-piecharts ma-el-grid-container ' . $this->ma_el_get_grid_classes($settings) .
			'">';

		foreach ($settings['piecharts'] as $piechart) :

			$child_output = '<div class="ma-el-grid-item ma-el-piechart">';

			$child_output .= '<div class="ma-el-percentage"' . $bar_color . $track_color . ' data-percent="' . round($piechart['percentage_value']) . '">';

			$child_output .= '<span>' . round($piechart['percentage_value']) . '<sup>%</sup>' . '</span>';

			$child_output .= '</div>';

			$child_output .= '<div class="ma-el-label">' . esc_html($piechart['stats_title']) . '</div>';

			$child_output .= '</div><!-- .ma-el-piechart -->';

			$output .= apply_filters('ma_el_piechart_output', $child_output, $piechart, $settings);

		endforeach;

		$output .= '</div><!-- .ma-el-piecharts -->';

		$output .= '<div class="ma-el-clear"></div>';

		echo apply_filters('ma_el_piecharts_output', $output, $settings);
	}

	protected function content_template()
	{
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new Master_Addons_Piechart());
