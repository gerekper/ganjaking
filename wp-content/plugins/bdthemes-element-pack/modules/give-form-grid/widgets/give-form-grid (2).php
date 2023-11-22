<?php

namespace ElementPack\Modules\GiveFormGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Give_Form_Grid extends Module_Base {

	public function get_name() {
		return 'bdt-give-form-grid';
	}

	public function get_title() {
		return BDTEP . __('Give Form Grid', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-give-form-grid';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['give', 'charity', 'donation', 'donor', 'history', 'wall', 'form'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-give-form-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/hq4ElaX0nrE';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'give_form_grid_settings',
			[
				'label' => __('Form Grid', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __('Show Title', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label' => __('Show Excerpt', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label' => __('Excerpt Length', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'default' => 16,
			]
		);

		$this->add_control(
			'show_goal',
			[
				'label' => __('Show Goal', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_featured_image',
			[
				'label' => __('Show Featured Image', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => __('Show Pagination', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-pagination-show-',
			]
		);

		$this->add_control(
			'forms_per_page',
			[
				'label'   => __('Forms Per Page', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::NUMBER,
				'default' => '6'
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default'        => '3',
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
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-grid' => 'display: grid;
					grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label' => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-grid' => 'grid-column-gap: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'items_row_gap',
			[
				'label' => esc_html__('Row Gap', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-grid' => 'grid-row-gap: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __('Order By', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'date'             => __('Date Created', 'bdthemes-element-pack'),
					'title'            => __('Form Title', 'bdthemes-element-pack'),
					'amount_donated'   => __('Amount Donated', 'bdthemes-element-pack'),
					'number_donations' => __('Number of Donations', 'bdthemes-element-pack'),
					'menu_order'       => __('Menu Order', 'bdthemes-element-pack'),
					'post__in'         => __('Form ID', 'bdthemes-element-pack'),
					'closest_to_goal'  => __('Closest to Goal', 'bdthemes-element-pack'),
				],
				'default' => 'date'
			]
		);

		$this->add_control(
			'order',
			[
				'label'       => __('Order', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'DESC' => __('Descending', 'bdthemes-element-pack'),
					'ASC'  => __('Ascending', 'bdthemes-element-pack')
				],
				'default' => 'DESC'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_form_grid_style',
			[
				'label' => esc_html__('Items', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tab_item_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_item_border',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card',
			]
		);

		$this->add_responsive_control(
			'tab_item_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'tab_item_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tab_item_bg_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tab_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_responsive_control(
			'section_content_padding',
			[
				'label' => __('Content Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_featured_image' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__media img'
			]
		);

		$this->add_control(
			'iamge_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__media img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__media img'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes'
				]
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__text' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__text' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-wrap .give-card__text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_goal_style',
			[
				'label' => esc_html__('Goal', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_goal' => 'yes'
				]
			]
		);

		$this->add_control(
			'income_heading',
			[
				'label' => esc_html__('Income Amount', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'income_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid span.income' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'income_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid span.income' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'income_typography',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid span.income',
			]
		);

		$this->add_control(
			'goal_amount_heading',
			[
				'label' => esc_html__('Goal Amount', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'goal_amount_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .raised' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'goal_amount_typography',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .raised',
			]
		);

		$this->add_control(
			'progress_bar_heading',
			[
				'label' => esc_html__('Progress Bar', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'progress_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-progress-bar>span' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_height',
			[
				'label' => __('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-progress-bar' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// Pagination
		$this->start_controls_section(
			'section_pagi_style',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'pagi_alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs('pagi_style');

		$this->start_controls_tab(
			'pagi_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagi_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagi_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagi_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a, {{WRAPPER}} .bdt-give-form-grid .give-page-numbers span.current',
			]
		);

		$this->add_responsive_control(
			'pagi_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a, {{WRAPPER}} .bdt-give-form-grid .give-page-numbers span.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagi_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a, {{WRAPPER}} .bdt-give-form-grid .give-page-numbers span.current' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagi_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagi_typography',
				'selector' => '{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a, {{WRAPPER}} .give-page-numbers span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagi_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagi_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagi_bg_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagi_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers a:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pagi_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagi_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagi_active_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers span.current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagi_bg_active_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers span.current' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagi_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form-grid .give-page-numbers span.current'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pagi_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		$attributes = [
			'forms_per_page'      => $settings['forms_per_page'],
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'show_title'          => $settings['show_title'],
			'show_goal'           => $settings['show_goal'],
			'show_excerpt'        => $settings['show_excerpt'],
			'excerpt_length'      => $settings['excerpt_length'],
			'show_featured_image' => $settings['show_featured_image'],
			'display_style'       => 'modal_reveal'
		];

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[give_form_grid %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {

		$this->add_render_attribute('give_wrapper', 'class', 'bdt-give-form-grid');

?>

		<div <?php echo $this->get_render_attribute_string('give_wrapper'); ?>>

			<?php echo do_shortcode($this->get_shortcode()); ?>

		</div>

<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
