<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Progress_Bar extends Widget_Base
{

	public function get_name()
	{
		return 'ma-progressbar';
	}

	public function get_title()
	{
		return esc_html__('Progressbar', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-skill-bar';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_script_depends()
	{
		return [
			'master-addons-progressbar',
			'master-addons-waypoints',
			'master-addons-scripts',
		];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/progress-bar/';
	}


	protected function _register_controls()
	{

		// Progressbar Content Section
		$this->start_controls_section(
			'ma_el_progress_bar_section_content',
			[
				'label' => __('Content', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_progress_bar_title',
			[
				'label' => __('Title', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'default' => __('Progress Bar', MELA_TD),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_progress_bar_value',
			[
				'label' => __('Value', MELA_TD),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 60,
			]
		);

		$this->end_controls_section();


		// Progressbar Style Section
		$this->start_controls_section(
			'ma_el_section_progress_bar_styles_preset',
			[
				'label' => __('General Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_preset',
			[
				'label' => __('Style Presets', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'line' => __('Line', MELA_TD),
					'line-bubble' => __('Line Bubble', MELA_TD),
					'circle' => __('Circle', MELA_TD),
					'fan' => __('Fan', MELA_TD)
				],
				'default' => 'line',
			]
		);

		$this->add_control(
			'ma_el_progress_bar_bubble_color',
			[
				'label' => __('Bubble Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#61ce70',
				'selectors' => [
					'{{WRAPPER}} [class*="ma-el-progress-bar-"].line-bubble .ldBar-label' => 'background: {{VALUE}};',
				],
				'condition' => [
					'ma_el_progress_bar_preset' => 'line-bubble'
				]

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_title_styles',
			[
				'label' => __('Title', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .ma-el-progress-bar-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ma-el-progress-bar-title',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_progress_bar_front_style',
			[
				'label' => __('Front Bar', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_stroke_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#704aff'
			]
		);

		$this->add_control(
			'ma_el_progress_bar_stroke_width',
			[
				'label' => __('Width', MELA_TD),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_back_style',
			[
				'label' => __('Back Bar', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_trail_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd'
			]
		);

		$this->add_control(
			'ma_el_progress_bar_trail_width',
			[
				'label' => __('Width', MELA_TD),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_progress_bar_value_styles',
			[
				'label' => __('Value', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_progress_bar_value_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} [class*="ma-el-progress-bar-"] .ldBar-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'value_typography',
				'selector' => '{{WRAPPER}} .ldBar-label',
			]
		);


		$this->end_controls_section();





		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/progress-bar/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/how-to-create-and-customize-progressbar-in-elementor/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=77-b1moRE8M" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();


		//Upgrade to Pro
		

		
	}



	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'ma-el-progress-bar',
			[
				'class' => [$settings['ma_el_progress_bar_preset'], 'ma-el-progress-bar-' . $this->get_id()],
				'data-id' => $this->get_id(),
				'data-type' => $settings['ma_el_progress_bar_preset'],
				'data-progress-bar-value' => $settings['ma_el_progress_bar_value'],
				'data-stroke-color' => $settings['ma_el_progress_bar_stroke_color'],
				'data-progress-bar-stroke-width' => $settings['ma_el_progress_bar_stroke_width'],
				'data-stroke-trail-color' => $settings['ma_el_progress_bar_trail_color'],
				'data-progress-bar-stroke-trail-width' => $settings['ma_el_progress_bar_trail_width']
			]
		);

		if ($settings['ma_el_progress_bar_preset'] == 'line' || $settings['ma_el_progress_bar_preset'] == 'line-bubble') {
			$this->add_render_attribute(
				'ma-el-progress-bar',
				[
					'data-preset' => 'line',
					'style' => 'width: 100%; height: 50px'
				]
			);
		}

		if ($settings['ma_el_progress_bar_preset'] == 'circle') {
			$this->add_render_attribute(
				'ma-el-progress-bar',
				[
					'data-preset' => 'circle',
					'style' => 'width: 100%; height: 100%'
				]
			);
		}

		if ($settings['ma_el_progress_bar_preset'] == 'fan') {
			$this->add_render_attribute(
				'ma-el-progress-bar',
				[
					'data-preset' => 'fan',
					'style' => 'width: 100%; height: 100%'
				]
			);
		}
?>

		<div <?php echo $this->get_render_attribute_string('ma-el-progress-bar') ?> data-progress-bar>
			<h6 class="ma-el-progress-bar-title">
				<?php echo $settings['ma_el_progress_bar_title'] ?>
			</h6>
		</div>

<?php
	}
}
