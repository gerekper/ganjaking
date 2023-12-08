<?php

namespace ElementPack\Modules\GiveDonorWall\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Give_Donor_Wall extends Module_Base {

	public function get_name() {
		return 'bdt-give-donor-wall';
	}

	public function get_title() {
		return BDTEP . __('Give Donor Wall', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-give-donor-wall';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['give', 'charity', 'donation', 'donor', 'history', 'wall'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-give-donor-wall'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/W_RRrE4cmEo';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'give_donor_wall_settings',
			[
				'label' => __('Donor Wall', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_avatar',
			[
				'label' => __('Show Avatar', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_name',
			[
				'label' => __('Show Name', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_total',
			[
				'label' => __('Show Donation Amount', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_time',
			[
				'label' => __('Show Date', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'anonymous',
			[
				'label' => __('Show Anonymous Donations', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'only_comments',
			[
				'label' => __('Only Comments Donors', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__('Choose whether to display all donors or just donors who left comments.', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_comments',
			[
				'label' => __('Show Comments', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'comment_length',
			[
				'label' => __('Comment Length', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'default' => '30',
				'condition' => [
					'show_comments' => 'yes'
				]
			]
		);

		$this->add_control(
			'donors_per_page',
			[
				'label' => __('Donors per Page', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'default' => '6'
			]
		);

		$this->add_control(
			'all_forms',
			[
				'label' => __('Show All Donors?', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'yes' => [
						'title' => __('Yes', 'bdthemes-element-pack'),
						'icon' => 'eicon-check',
					],
					'no' => [
						'title' => __('No', 'bdthemes-element-pack'),
						'icon' => 'eicon-close-circle',
					],
				],
				'default' => 'yes',
				'toggle' => true
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __('Form ID', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_give_forms_options(),
				'condition' => [
					'all_forms' => 'no'
				],
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
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-grid.give-grid--best-fit' => 'display: grid;
					grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label' => esc_html__('Items Gap', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-grid.give-grid--best-fit' => 'grid-gap: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'            => __('Order By', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::SELECT,
				'options'          => [
					'post_date'       => __('Donation Date', 'bdthemes-element-pack'),
					'donation_amount' => __('Donation Amount', 'bdthemes-element-pack')
				],
				'default'          => 'post_date'
			]
		);

		$this->add_control(
			'order',
			[
				'label'       => __('Order', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'desc'       => __('Descending', 'bdthemes-element-pack'),
					'asc'        => __('Ascending', 'bdthemes-element-pack')
				],
				'default'     => 'desc'
			]
		);

		$this->add_control(
			'loadmore_text',
			[
				'label' => __('Load More Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Load More', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label' => __('Read More Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Read More', 'bdthemes-element-pack'),
				'label_block' => true,
				'condition' => [
					'show_comments' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_items_style',
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
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_item_border',
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card',
			]
		);

		$this->add_responsive_control(
			'tab_item_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_item_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card',
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
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover'  => 'border-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover',
			]
		);

		$this->add_control(
			'hr_item',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'image_item_hover_color',
			[
				'label' => esc_html__('Avatar Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover .give-donor__image' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_avatar' => 'yes'
				],
			]
		);

		$this->add_control(
			'title_item_hover_color',
			[
				'label' => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover .give-donor__name' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'amount_item_hover_color',
			[
				'label' => esc_html__('Amount Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover span.give-donor__total' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_total' => 'yes'
				]
			]
		);

		$this->add_control(
			'comment_item_hover_color',
			[
				'label' => esc_html__('Comment Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover .give-donor__content .give-donor__excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_comments' => 'yes'
				]
			]
		);

		$this->add_control(
			'date_item_hover_color',
			[
				'label' => esc_html__('Date Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-wrap .give-card:hover span.give-donor__timestamp' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_time' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_avatar_style',
			[
				'label' => esc_html__('Avatar', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_avatar' => 'yes'
				],
			]
		);

		$this->add_control(
			'image_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__image' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__image' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__image',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'image_typography',
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__image',
			]
		);

		$this->add_responsive_control(
			'avatar_size',
			[
				'label' => __('Avatar Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__image' => 'flex-basis: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__image' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__name' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'title_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__name' => 'font-size: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__name' => 'padding-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_amount_style',
			[
				'label' => esc_html__('Amount', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_total' => 'yes'
				]
			]
		);

		$this->add_control(
			'amount_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall span.give-donor__total' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'amount_typography',
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall span.give-donor__total',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_comment_style',
			[
				'label' => esc_html__('Comment', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_comments' => 'yes'
				]
			]
		);

		$this->add_control(
			'comment_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__content .give-donor__excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comment_link_color',
			[
				'label' => esc_html__('Link Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__content .give-donor__excerpt .give-donor__read-more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'comment_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__content .give-donor__excerpt' => 'padding-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'comment_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__content .give-donor__excerpt' => 'font-size: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_date_style',
			[
				'label' => esc_html__('Date', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_time' => 'yes'
				]
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall span.give-donor__timestamp' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'date_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall span.give-donor__timestamp' => 'padding-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'date_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall span.give-donor__timestamp' => 'font-size: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Load More Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'loadmore_alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'inherit' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more' => 'float: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-donor-wall .give-donor__load_more:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['form_id'] and 'yes' !== $settings['all_forms']) {
			return '<div class="bdt-alert bdt-alert-warning">' . __('Please select a Give Forms From Setting!', 'bdthemes-element-pack') . '</div>';
		}

		$attributes = [
			'form_id' => $settings['form_id'],
			'donors_per_page' => $settings['donors_per_page'],
			// 'columns' => $settings['columns'],
			'anonymous' => $settings['anonymous'],
			'show_avatar' => $settings['show_avatar'],
			'show_name' => $settings['show_name'],
			'show_total' => $settings['show_total'],
			'show_time' => $settings['show_time'],
			'order' => $settings['order'],
			'orderby' => $settings['orderby'],
			'loadmore_text' => esc_html($settings['loadmore_text']),
			'readmore_text' => esc_html($settings['readmore_text']),
			'show_comments' => ($settings["show_comments"] === "yes") ? true : false,
			'only_comments' => ($settings["only_comments"] === "yes") ? true : false,
			'comment_length' => $settings['comment_length'],
		];

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[give_donor_wall %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {

		$this->add_render_attribute('give_wrapper', 'class', 'bdt-give-donor-wall');

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
