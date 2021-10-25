<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;


use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/27/19
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Master Addons: Pricing Table
 */
class Pricing_Table extends Widget_Base
{

	public function get_name()
	{
		return 'ma-pricing-table';
	}

	public function get_title()
	{
		return __('Pricing Table', MELA_TD);
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-price-table';
	}

	public function get_keywords()
	{
		return [
			'pricing',
			'price',
			'cost table',
			'data table',
			'money table',
			'table',
			'value',
			'pricing table',
			'pricingtable',
			'rate',
			'comparision table'
		];
	}

	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/pricing-table/';
	}


	protected function _register_controls()
	{

		$this->start_controls_section(
			'ma_el_pricing_table_section_start',
			[
				'label' => __('Pricing Contents', MELA_TD),
			]
		);

		// Pricing Layout
		$this->add_control(
			'ma_el_pricing_table_layout',
			[
				'label'   => __('Layout', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'one',
				'options' => [
					'one'   => __('Default', MELA_TD),
					'two'   => __('Left Align Content', MELA_TD),
					'three' => __('Rounded Table', MELA_TD),
					'four'  => __('Table with BG Image', MELA_TD),
					'five'  => __('Skew BG Pattern', MELA_TD),
				],
			]
		);


		$this->add_control(
			'ma_el_pricing_table_highlight',
			[
				'label' => __('Highlight Table?', MELA_TD),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_show',
			[
				'label' => __('Show Features?', MELA_TD),
				'default'   => 'yes',
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();


		// Image
		$this->start_controls_section(
			'ma_el_pricing_table_section_content_image',
			[
				'label' => __('Image', MELA_TD),
				'condition' => ['ma_el_pricing_table_layout' => 'four']
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_pricing_table_image',
				'selector' => '{{WRAPPER}} .table-bg-image',
			]
		);

		$this->end_controls_section();



		//Heading

		$this->start_controls_section(
			'ma_el_pricing_table_section_header',
			[
				'label' => __('Header', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_pricing_table_head_color_scheme',
			[
				'label'   => __('Header BG Color', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'gradient-1',
				'options' => [
					'gradient-1'    => __('Gradient One', MELA_TD),
					'gradient-2'    => __('Gradient Two', MELA_TD),
					'gradient-3'    => __('Gradient Three', MELA_TD),
					'custom'        => __('Custom (Style Tab Settings)', MELA_TD)
				],
				'condition' => [
					'ma_el_pricing_table_layout!' => ['three', 'four', 'five']
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'far fa-lightbulb',
					'library'   => 'regular',
				],
				'render_type'      => 'template',
				'condition' => ['ma_el_pricing_table_layout' => 'four'],
			]
		);


		$this->add_control(
			'ma_el_pricing_table_heading',
			[
				'label'   => __('Title', MELA_TD),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Personal', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_tag',
			[
				'label'   => __('HTML Tag', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h3',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_sub_heading',
			[
				'label'     => __('Subtitle', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Suitable for single website', MELA_TD),
			]
		);

		$this->end_controls_section();


		//Pricing

		$this->start_controls_section(
			'ma_el_pricing_table_section_pricing',
			[
				'label' => __('Pricing', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_pricing_table_currency_symbol',
			[
				'label'   => __('Currency Symbol', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''             => __('None', MELA_TD),
					'dollar'       => '&#36; ' . _x('Dollar', 'Currency Symbol', MELA_TD),
					'euro'         => '&#128; ' . _x('Euro', 'Currency Symbol', MELA_TD),
					'baht'         => '&#3647; ' . _x('Baht', 'Currency Symbol', MELA_TD),
					'franc'        => '&#8355; ' . _x('Franc', 'Currency Symbol', MELA_TD),
					'guilder'      => '&fnof; ' . _x('Guilder', 'Currency Symbol', MELA_TD),
					'krona'        => 'kr ' . _x('Krona', 'Currency Symbol', MELA_TD),
					'lira'         => '&#8356; ' . _x('Lira', 'Currency Symbol', MELA_TD),
					'peseta'       => '&#8359 ' . _x('Peseta', 'Currency Symbol', MELA_TD),
					'peso'         => '&#8369; ' . _x('Peso', 'Currency Symbol', MELA_TD),
					'pound'        => '&#163; ' . _x('Pound Sterling', 'Currency Symbol', MELA_TD),
					'real'         => 'R$ ' . _x('Real', 'Currency Symbol', MELA_TD),
					'ruble'        => '&#8381; ' . _x('Ruble', 'Currency Symbol', MELA_TD),
					'rupee'        => '&#8360; ' . _x('Rupee', 'Currency Symbol', MELA_TD),
					'indian_rupee' => '&#8377; ' . _x('Rupee (Indian)', 'Currency Symbol', MELA_TD),
					'shekel'       => '&#8362; ' . _x('Shekel', 'Currency Symbol', MELA_TD),
					'yen'          => '&#165; ' . _x('Yen/Yuan', 'Currency Symbol', MELA_TD),
					'won'          => '&#8361; ' . _x('Won', 'Currency Symbol', MELA_TD),
					'custom'       => __('Custom', MELA_TD),
				],
				'default' => 'dollar',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_currency_symbol_custom',
			[
				'label'     => __('Custom Symbol', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'ma_el_pricing_table_currency_symbol' => 'custom',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_price',
			[
				'label'   => __('Price', MELA_TD),
				'type'    => Controls_Manager::TEXT,
				'default' => '29.99',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_sale',
			[
				'label' => __('Sale', MELA_TD),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_original_price',
			[
				'label'     => __('Original Price', MELA_TD),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '99',
				'condition' => [
					'ma_el_pricing_table_sale' => 'yes',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_period',
			[
				'label'   => __('Period', MELA_TD),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Monthly', MELA_TD),
			]
		);

		$this->end_controls_section();



		//Pricing Features

		$this->start_controls_section(
			'ma_el_pricing_table_section_content_features',
			[
				'label'     => __('Features', MELA_TD),
				'condition' => [
					'ma_el_pricing_table_features_show' => 'yes'
				]
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('ma_el_pricing_table_features_list_tabs');

		$repeater->start_controls_tab(
			'ma_el_pricing_table_features_list_tab_normal_text',
			[
				'label' => __('Normal Text', MELA_TD)
			]
		);

		$repeater->add_control(
			'ma_el_pricing_table_item_text',
			[
				'label'   => __('Text', MELA_TD),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Feature', MELA_TD),
			]
		);

		$repeater->add_control(
			'ma_el_pricing_table_item_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-check',
					'library'   => 'solid',
				],
				'render_type'      => 'template'
			]
		);


		$repeater->add_control(
			'ma_el_pricing_table_item_icon_color',
			[
				'label'     => __('Icon Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'ma_el_pricing_table_features_list_tab_tooltip_text',
			[
				'label' => __('Tooltip Text', MELA_TD)
			]
		);

		$repeater->add_control(
			'ma_el_pricing_table_tooltip_text',
			[
				'label' => __('Text', MELA_TD),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'ma_el_pricing_table_tooltip_placement',
			[
				'label'   => __('Placement', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'top'    => __('Top', MELA_TD),
					'bottom' => __('Bottom', MELA_TD),
					'left'   => __('Left', MELA_TD),
					'right'  => __('Right', MELA_TD),
				],
				'condition'   => [
					'ma_el_pricing_table_tooltip_text!' => '',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'ma_el_pricing_table_features_list',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'ma_el_pricing_table_item_text' => __('Feature #1', MELA_TD),
						'ma_el_pricing_table_item_icon' => 'fa fa-check',
					],
					[
						'ma_el_pricing_table_item_text' => __('Feature #2', MELA_TD),
						'ma_el_pricing_table_item_icon' => 'fa fa-check',
					],
					[
						'ma_el_pricing_table_item_text' => __('Feature #3', MELA_TD),
						'ma_el_pricing_table_item_icon' => 'fa fa-check',
					],
				],
				'title_field' => '{{{ ma_el_pricing_table_item_text }}}',
			]
		);

		$this->end_controls_section();



		//Pricing Footer

		$this->start_controls_section(
			'ma_el_pricing_table_section_content_footer',
			[
				'label' => __('Footer', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_text',
			[
				'label'   => __('Button Text', MELA_TD),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Purchase Now', MELA_TD),
			]
		);

		if (class_exists('Easy_Digital_Downloads')) {
			$edd_posts = get_posts(['numberposts' => 10, 'post_type'   => 'download']);
			$options = ['0' => __('Select EDD', MELA_TD)];
			foreach ($edd_posts as $edd_post) {
				$options[$edd_post->ID] = $edd_post->post_title;
			}
		} else {
			$options = ['0' => __('Not found', MELA_TD)];
		}

		$this->add_control(
			'ma_el_pricing_table_edd_as_button',
			[
				'label' => __('Easy Digital Download Integration', MELA_TD),
				'type'  => Controls_Manager::SWITCHER,
			]
		);


		$this->add_control(
			'ma_el_pricing_table_edd_id',
			[
				'label'       => __('Easy Digital Download Item', MELA_TD),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => $options,
				'label_block' => true,
				'condition'   => [
					'ma_el_pricing_table_edd_as_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_link',
			[
				'label'       => __('Link', MELA_TD),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'ma_el_pricing_table_edd_as_button' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_footer_additional_info',
			[
				'label'     => __('Additional Info', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('This is footer text', MELA_TD),
				'rows'      => 2,
			]
		);

		$this->end_controls_section();




		//Header Style

		$this->start_controls_section(
			'ma_el_pricing_table_section_style_header',
			[
				'label' => __('Header', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_header_bg_color_heading',
			[
				'label'     => __('Header Background', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_head_color_scheme' => 'custom',
					'ma_el_pricing_table_layout!'           => 'five'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_pricing_table_header_bg_color',
				'selector' => '{{WRAPPER}} .ma-el-price-table-head',
				'condition' => [
					'ma_el_pricing_table_head_color_scheme' => 'custom',
					'ma_el_pricing_table_layout!'           => 'five'
				]
			]
		);

		$this->add_control(
			'ma_el_pricing_table_header_rounded_bg_color_heading',
			[
				'label'     => __('Rounded Background', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_layout' => 'three'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_pricing_table_heading_rounded_bg_color',
				'label'     => __('Rounded BG Color', MELA_TD),
				'types' => ['gradient'],
				'selector' => '{{WRAPPER}} .table-active-zoom .ma-el-table-price-area',
				'condition' => [
					'ma_el_pricing_table_layout' => 'three'
				]
			]
		);

		$this->add_control(
			'ma_el_pricing_table_header_pattern_bg_color_heading',
			[
				'label'     => __('Pattern Background', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_layout' => 'five'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_pricing_table_heading_pattern_bg_color',
				'label'     => __('Pattern BG Color', MELA_TD),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .table-bg-pattern .ma-el-price-table:before, {{WRAPPER}} .table-bg-pattern .ma-el-price-table:after',
				'condition' => [
					'ma_el_pricing_table_layout' => 'five'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_heading_pattern_bg_height',
			[
				'label' => __('Height', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 280,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 25,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .table-bg-pattern .ma-el-price-table:before' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_heading_after_pattern_bg_ver_pos',
			[
				'label' => __('Vertical Position(Pattern Bar)', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 36,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 25,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .table-bg-pattern .ma-el-price-table:after' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_header_margin',
			[
				'label'      => __('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-head' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ma_el_pricing_table_header_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_heading_style',
			[
				'label'     => __('Title', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_pricing_table_heading_typography',
				'selector' => '{{WRAPPER}} .ma-el-price-table-title',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_sub_heading_style',
			[
				'label'     => __('Sub Title', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_sub_heading_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-subheading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_pricing_table_sub_heading_typography',
				'selector' => '{{WRAPPER}} .ma-el-price-table-subheading',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->end_controls_section();



		//Pricing Style

		$this->start_controls_section(
			'ma_el_pricing_table_section_style_pricing',
			[
				'label' => __('Pricing', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_pricing_element_bg_color',
			[
				'label'     => __('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-table-price-area' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_pricing_element_margin',
			[
				'label'      => __('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-table-price-area' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ma_el_pricing_table_pricing_element_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-table-price-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_price_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-table-price-area, {{WRAPPER}} .ma-el-price-table-original-price, {{WRAPPER}} .ma-el-table-price-currency, {{WRAPPER}} .ma-el-table-price-amount, {{WRAPPER}} .ma-el-fraction-price, {{WRAPPER}} .ma-el-price-amount-duration' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_pricing_table_price_typography',
				'selector' => '{{WRAPPER}} .ma-el-table-price-area',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_currency_style',
			[
				'label'     => __('Currency Symbol', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_currency_size',
			[
				'label' => __('Size', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-table-price-currency' => 'font-size: {{SIZE}}px',
				],
				'condition' => [
					'ma_el_pricing_table_currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_currency_vertical_position',
			[
				'label'   => __('Vertical Position', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', MELA_TD),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', MELA_TD),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', MELA_TD),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'selectors_dictionary' => [
					'top'    => 'top',
					'middle' => 'super',
					'bottom' => 'bottom',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-table-price-currency' => 'vertical-align: {{VALUE}}',
				],
				'condition' => [
					'ma_el_pricing_table_currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_fractional_part_style',
			[
				'label'     => __('Fractional Part', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_fractional-part_size',
			[
				'label' => __('Size', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-fraction-price' => 'font-size: {{SIZE}}px',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_fractional_part_vertical_position',
			[
				'label'   => __('Vertical Position', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', MELA_TD),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', MELA_TD),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', MELA_TD),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					//						'{{WRAPPER}} .ma-el-fraction-price' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_original_price_style',
			[
				'label'     => __('Original Price', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_sale'            => 'yes',
					'ma_el_pricing_table_original_price!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_original_price_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-original-price' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ma_el_pricing_table_sale'            => 'yes',
					'ma_el_pricing_table_original_price!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ma_el_pricing_table_original_price_typography',
				'selector'  => '{{WRAPPER}} .ma-el-price-table-original-price',
				'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => [
					'ma_el_pricing_table_sale'            => 'yes',
					'ma_el_pricing_table_original_price!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_original_price_vertical_position',
			[
				'label'   => __('Vertical Position', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', MELA_TD),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', MELA_TD),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', MELA_TD),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'default'   => 'bottom',
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-original-price' => 'align-self: {{VALUE}}',
				],
				'condition' => [
					'ma_el_pricing_table_sale'            => 'yes',
					'ma_el_pricing_table_original_price!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_period_style',
			[
				'label'     => __('Period', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_period!' => '',
				],
			]
		);


		$this->add_control(
			'ma_el_pricing_table_show_period',
			[
				'label'     => __('Show Dot', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'ma_el_pricing_table_period!' => '',
				],
			]
		);


		$this->add_control(
			'ma_el_pricing_table_period_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-amount-duration' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ma_el_pricing_table_period!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ma_el_pricing_table_period_typography',
				'selector'  => '{{WRAPPER}} .ma-el-price-amount-duration',
				'scheme'    => Scheme_Typography::TYPOGRAPHY_2,
				'condition' => [
					'ma_el_pricing_table_period!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_period_position',
			[
				'label'   => __('Position', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'below'  => 'Below',
					'beside' => 'Beside',
				],
				'default'   => 'below',
				'condition' => [
					'ma_el_pricing_table_period!' => '',
				],
			]
		);

		$this->end_controls_section();



		//Features Style

		$this->start_controls_section(
			'ma_el_pricing_table_section_style_features',
			[
				'label'     => __('Features', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_style_features');

		$this->start_controls_tab(
			'tab_features_normal_text',
			[
				'label' => __('Normal Text', MELA_TD)
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_list_bg_color',
			[
				'label'     => __('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_features_list_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_list_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details .ma-el-tooltip-content, {{WRAPPER}} .edd_price_options li span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_pricing_table_features_list_typography',
				'selector' => '{{WRAPPER}} .ma-el-price-table-details li .ma-el-tooltip-content, {{WRAPPER}} .edd_price_options li span',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_list_alignment',
			[
				'label'   => __('Alignment', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => __('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details li' => 'text-align: {{VALUE}}; align-items:{{VALUE}}; justify-content:{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_item_width',
			[
				'label' => __('Width', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 25,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details ul' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_list_divider',
			[
				'label'     => __('Divider', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_divider_style',
			[
				'label'   => __('Style', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'solid'  => __('Solid', MELA_TD),
					'double' => __('Double', MELA_TD),
					'dotted' => __('Dotted', MELA_TD),
					'dashed' => __('Dashed', MELA_TD),
				],
				'default'   => 'solid',
				'condition' => [
					'ma_el_pricing_table_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details li:before' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_divider_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ddd',
				'condition' => [
					'ma_el_pricing_table_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details li:before' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_divider_weight',
			[
				'label'   => __('Weight', MELA_TD),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'ma_el_pricing_table_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details li:before' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_divider_width',
			[
				'label'     => __('Width', MELA_TD),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'ma_el_pricing_table_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details li:before' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_divider_gap',
			[
				'label'   => __('Gap', MELA_TD),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'ma_el_pricing_table_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-details li:before' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_pricing_table_tab_features_tooltip_text',
			[
				'label' => __('Tooltip Text', MELA_TD)
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_features_tooltip_width',
			[
				'label'      => esc_html__('Width', MELA_TD),
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
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_pricing_table_features_tooltip_typography',
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_tooltip_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_tooltip_text_align',
			[
				'label'   => esc_html__('Text Alignment', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_pricing_table_features_tooltip_background',
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_features_tooltip_arrow_color',
			[
				'label'     => esc_html__('Arrow Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-top .ma-el-tooltip-text:after'  => 'border-color: {{VALUE}} transparent transparent transparent',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-right .ma-el-tooltip-text:after'  => 'border-color:
						transparent {{VALUE}} transparent transparent',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-left .ma-el-tooltip-text:after'  => 'border-color:
						transparent transparent transparent {{VALUE}}',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-bottom .ma-el-tooltip-text:after'  =>
					'border-color: transparent transparent {{VALUE}} transparent',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_features_tooltip_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ma_el_pricing_table_features_tooltip_border',
				'label'       => esc_html__('Border', MELA_TD),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text',
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_features_tooltip_border_radius',
			[
				'label'      => __('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_pricing_table_features_tooltip_box_shadow',
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();




		// Ribbon

		$this->start_controls_section(
			'ma_el_pricing_table_section_content_ribbon',
			[
				'label' => __('Ribbon', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_pricing_table_show_ribbon',
			[
				'label'     => __('Show', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_ribbon_title',
			[
				'label'     => __('Title', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Popular', MELA_TD),
				'condition' => [
					'ma_el_pricing_table_show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_ribbon_align',
			[
				'label'   => __('Align', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => __('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __('Justify', MELA_TD),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'default'   => 'left',
				'condition' => [
					'ma_el_pricing_table_show_ribbon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_ribbon_horizontal_position',
			[
				'label' => __('Horizontal Position', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
				],
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'ma_el_pricing_table_show_ribbon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_ribbon_vertical_position',
			[
				'label' => __('Vertical Position', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
				],
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'ma_el_pricing_table_show_ribbon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_ribbon_rotate',
			[
				'label'   => __('Rotate', MELA_TD),
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
				'selectors' => [
					'(desktop){{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'transform: translate({{ma_el_pricing_table_ribbon_horizontal_position.SIZE}}{{UNIT}}, {{ma_el_pricing_table_ribbon_vertical_position.SIZE}}{{UNIT}}) rotate({{SIZE}}deg);',
					'(tablet){{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'transform: translate({{ma_el_pricing_table_ribbon_horizontal_position_tablet.SIZE}}{{UNIT}}, {{ma_el_pricing_table_ribbon_vertical_position_tablet.SIZE}}{{UNIT}}) rotate({{SIZE}}deg);',
					'(mobile){{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'transform: translate({{ma_el_pricing_table_ribbon_horizontal_position_mobile.SIZE}}{{UNIT}}, {{ma_el_pricing_table_ribbon_vertical_position_mobile.SIZE}}{{UNIT}}) rotate({{SIZE}}deg);',
				],
				'condition' => [
					'ma_el_pricing_table_show_ribbon' => 'yes',
				],
			]
		);

		$this->end_controls_section();


		/* Header Style */





		/* Footer Style */

		$this->start_controls_section(
			'ma_el_pricing_table_section_style_footer',
			[
				'label' => __('Footer', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_pricing_table_footer_bg_color',
			[
				'label'     => __('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-footer' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_footer_margin',
			[
				'label'      => __('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-footer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ma_el_pricing_table_footer_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_heading_footer_button',
			[
				'label'     => __('Button', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_size',
			[
				'label'   => __('Size', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => [
					'md' => __('Default', MELA_TD),
					'sm' => __('Small', MELA_TD),
					'xs' => __('Extra Small', MELA_TD),
					'lg' => __('Large', MELA_TD),
					'xl' => __('Extra Large', MELA_TD),
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->start_controls_tabs('ma_el_pricing_table_tabs_button_style');

		$this->start_controls_tab(
			'ma_el_pricing_table_tab_button_normal',
			[
				'label'     => __('Normal', MELA_TD),
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_text_color',
			[
				'label'     => __('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-btn' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_pricing_table_button_background_color',
				'selector' => '{{WRAPPER}} .ma-el-price-table-btn',
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				]
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ma_el_pricing_table_button_border',
				'label'       => __('Border', MELA_TD),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ma-el-price-table-btn',
				'condition'   => [
					'ma_el_pricing_table_button_text!' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_border_radius',
			[
				'label'      => __('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_margin',
			[
				'label'      => __('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_text_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_pricing_table_button_shadow',
				'selector' => '{{WRAPPER}} .ma-el-price-table-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ma_el_pricing_table_button_typography',
				'label'     => __('Typography', MELA_TD),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .ma-el-price-table-btn',
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_pricing_table_tab_button_hover',
			[
				'label'     => __('Hover', MELA_TD),
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_hover_color',
			[
				'label'     => __('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-btn:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_background_hover_color',
			[
				'label'     => __('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-btn:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_hover_border_color',
			[
				'label'     => __('Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-btn:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'ma_el_pricing_table_button_text!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_button_hover_animation',
			[
				'label'     => __('Animation', MELA_TD),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ma_el_pricing_table_heading_additional_info',
			[
				'label'     => __('Additional Info', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_pricing_table_footer_additional_info!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_additional_info_color',
			[
				'label'     => __('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-additional_info p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ma_el_pricing_table_footer_additional_info!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ma_el_pricing_table_additional_info_typography',
				'selector'  => '{{WRAPPER}} .ma-el-price-table-additional_info',
				'scheme'    => Scheme_Typography::TYPOGRAPHY_3,
				'condition' => [
					'ma_el_pricing_table_footer_additional_info!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_additional_info_margin',
			[
				'label'      => __('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default'    => [
					'top'    => 15,
					'right'  => 30,
					'bottom' => 0,
					'left'   => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-additional_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'ma_el_pricing_table_footer_additional_info!' => '',
				],
			]
		);

		$this->end_controls_section();



		//Ribbon
		$this->start_controls_section(
			'ma_el_pricing_table_section_style_ribbon',
			[
				'label'     => __('Ribbon', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_pricing_table_show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_ribbon_bg_color',
			[
				'label'     => __('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6e3ded',
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_ribbon_text_color',
			[
				'label'     => __('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_pricing_table_ribbon_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_pricing_table_ribbon_border_radius',
			[
				'label'      => __('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-price-table-ribbon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'shadow',
				'selector' => '{{WRAPPER}} .ma-el-price-table-ribbon-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ribbon_typography',
				'selector' => '{{WRAPPER}} .ma-el-price-table-ribbon-inner',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/pricing-table/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/pricing-table-elementor-free-widget/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=_FUk1EfLBUs" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		//Upgrade to Pro
		
	}


	private function jltma_pt_get_currency_symbol($symbol_name)
	{
		$symbols = [
			'dollar'       => '&#36;',
			'baht'         => '&#3647;',
			'euro'         => '&#128;',
			'franc'        => '&#8355;',
			'guilder'      => '&fnof;',
			'indian_rupee' => '&#8377;',
			'krona'        => 'kr',
			'lira'         => '&#8356;',
			'peseta'       => '&#8359',
			'peso'         => '&#8369;',
			'pound'        => '&#163;',
			'real'         => 'R$',
			'ruble'        => '&#8381;',
			'rupee'        => '&#8360;',
			'shekel'       => '&#8362;',
			'won'          => '&#8361;',
			'yen'          => '&#165;',
		];
		return isset($symbols[$symbol_name]) ? $symbols[$symbol_name] : '';
	}


	public function jltma_pt_render_image()
	{

		$settings = $this->get_settings();

		if (empty($settings['ma_el_pricing_table_image']['url'])) {
			return;
		}

		$this->add_render_attribute('wrapper', 'class', 'ma-el-pricing-table-image');

?>
		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
			<?php echo Group_Control_Image_Size::get_attachment_image_html($settings); ?>
		</div>
		<?php
	}


	public function jltma_pt_render_heading()
	{
		$settings = $this->get_settings();

		$ma_el_pricing_table_layout = $settings['ma_el_pricing_table_layout'];

		if ($settings['ma_el_pricing_table_heading'] || $settings['ma_el_pricing_table_sub_heading']) { ?>

			<?php if ($ma_el_pricing_table_layout == 'four') { ?>
				<?php Master_Addons_Helper::jltma_fa_icon_picker('far fa-lightbulb', 'icon', $settings['ma_el_pricing_table_icon'], 'ma_el_pricing_table_icon', 'header-icon'); ?>
			<?php } ?>

			<?php if (!empty($settings['ma_el_pricing_table_heading'])) : ?>
				<<?php echo esc_attr($settings['ma_el_pricing_table_heading_tag']); ?> class="ma-el-price-table-title">
					<?php echo esc_html($settings['ma_el_pricing_table_heading']); ?>
				</<?php echo esc_attr($settings['ma_el_pricing_table_heading_tag']); ?>>
			<?php endif; ?>

			<?php if (!empty($settings['ma_el_pricing_table_sub_heading'])) { ?>
				<div class="ma-el-price-table-subheading"><?php echo esc_html($settings['ma_el_pricing_table_sub_heading']);
															?></div>
			<?php } ?>

		<?php }
	}


	public function jltma_pt_render_price_symbol()
	{
		$settings = $this->get_settings();
		$price_symbol = '';
		$price = explode('.', $settings['ma_el_pricing_table_price']);
		$intpart = $price[0];


		if (!empty($settings['ma_el_pricing_table_currency_symbol'])) {
			if ($settings['ma_el_pricing_table_currency_symbol'] !== 'custom') {
				$price_symbol = $this->jltma_pt_get_currency_symbol($settings['ma_el_pricing_table_currency_symbol']);
			} else {
				$price_symbol = $settings['ma_el_pricing_table_currency_symbol_custom'];
			}
		}

		if ($settings['ma_el_pricing_table_sale'] && !empty($settings['ma_el_pricing_table_original_price'])) { ?>
			<span class="ma-el-price-table-original-price">
				<?php echo esc_html($price_symbol . $settings['ma_el_pricing_table_original_price']); ?>
			</span>
		<?php }

		if (!empty($price_symbol) && is_numeric($intpart)) { ?>
			<span class="ma-el-table-price-currency">
				<?php echo esc_attr($price_symbol); ?>
			</span>
		<?php }
	}


	public function jltma_pt_render_price_amount()
	{
		$settings = $this->get_settings();
		$price = explode('.', $settings['ma_el_pricing_table_price']);
		$intpart = $price[0];

		if (!empty($intpart) || 0 <= $intpart) { ?>
			<span class="ma-el-table-price-amount">
				<?php echo esc_attr($intpart); ?>
			</span>
		<?php }

		$this->jltma_pt_render_price_fraction_period();
	}


	public function jltma_pt_render_price_period()
	{
		$settings = $this->get_settings();

		if (!empty($settings['ma_el_pricing_table_period'])) { ?>
			<span class="ma-el-price-amount-duration">
				<?php echo wp_kses_post($settings['ma_el_pricing_table_period']); ?>
			</span>
		<?php }
	}


	public function jltma_pt_render_price_fraction_period()
	{
		$settings = $this->get_settings();

		$price = explode('.', $settings['ma_el_pricing_table_price']);
		$intpart = $price[0];
		$fraction_part = '';

		if (2 === sizeof($price)) {
			$fraction_part = $price[1];
		}

		$period_position = $settings['ma_el_pricing_table_period_position'];
		$period_class    = ($period_position == 'below') ? ' ma-el-price-table-period-position-below' : ' ma-el-price-table-period-position-beside';
		$period_element  = '<span class="ma-el-price-table-period elementor-typo-excluded' . $period_class . '">' .
			$settings['ma_el_pricing_table_period'] . '</span>';


		if (
			0 < $fraction_part ||
			(!empty($settings['ma_el_pricing_table_period']) && 'beside' === $period_position)
		) { ?>
			<span class="ma-el-fraction-price">
				<?php

				if ($settings['ma_el_pricing_table_show_period'] == 'yes') {
					echo '.';
				}

				echo esc_html($fraction_part); ?>
			</span>
		<?php }
	}


	public function jltma_pt_render_price()
	{
		$settings = $this->get_settings();
		?>

		<div class="ma-el-table-price-area">

			<?php
			$ma_el_pricing_table_layout = $settings['ma_el_pricing_table_layout'];

			if ($ma_el_pricing_table_layout == 'one') {

				$this->jltma_pt_render_price_symbol();
				$this->jltma_pt_render_price_amount();
				$this->jltma_pt_render_price_period();
			} elseif ($ma_el_pricing_table_layout == 'two' || $ma_el_pricing_table_layout == 'three') {

				$this->jltma_pt_render_price_amount();
				$this->jltma_pt_render_price_symbol();
				$this->jltma_pt_render_price_period();
			} elseif ($ma_el_pricing_table_layout == 'four') {

				$this->jltma_pt_render_price_amount();
				$this->jltma_pt_render_price_symbol();
				$this->jltma_pt_render_price_period();
			} elseif ($ma_el_pricing_table_layout == 'five') {

				$this->jltma_pt_render_price_amount();
				$this->jltma_pt_render_price_symbol();
				$this->jltma_pt_render_price_period();
			}
			?>


		</div><!-- /.table-price-area -->
	<?php }


	public function jltma_pt_render_header()
	{
		$settings = $this->get_settings_for_display();

		$ma_el_table_head_class = $settings['ma_el_pricing_table_head_color_scheme'];

		$this->add_render_attribute(
			'ma_el_pricing_table_head',
			'class',
			[
				'ma-el-price-table-head',
				$ma_el_table_head_class
			]
		);

	?>

		<div <?php echo $this->get_render_attribute_string('ma_el_pricing_table_head'); ?>>
			<?php $this->jltma_pt_render_heading(); ?>
			<?php $this->jltma_pt_render_price(); ?>
		</div><!-- /.price-table-head -->
		<?php }


	public function render_features_list()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'ma_el_pricing_table_tooltip',
			[
				'class' => 'ma-el-tooltip'
			]
		);

		if (!empty($settings['ma_el_pricing_table_features_list'])) { ?>
			<ul>
				<?php foreach ($settings['ma_el_pricing_table_features_list'] as $item) {

					$this->add_render_attribute('features', 'class', 'ma-el-price-table-feature-text ma-el-display-inline-block', true);

					if ($item['ma_el_pricing_table_tooltip_text']) {
						// Tooltip settings
						$this->add_render_attribute(
							'features',
							'class',
							[
								'ma-el-tooltip-item',
								'tooltip-' . $item['ma_el_pricing_table_tooltip_placement']
							]
						);
					} ?>
					<li class="<?php if ($item['ma_el_pricing_table_tooltip_text']) {
									echo "ma-el-tooltip";
								}
								?> elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">

						<?php if (!empty($item['ma_el_pricing_table_item_icon'])) { ?>
							<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-check', 'icon', $item['ma_el_pricing_table_item_icon'], 'ma_el_pricing_table_item_icon'); ?>
						<?php } ?>

						<?php if (!empty($item['ma_el_pricing_table_item_text'])) { ?>
							<div <?php echo $this->get_render_attribute_string('features'); ?>>
								<div class="ma-el-tooltip-content">
									<?php echo $this->parse_text_editor($item['ma_el_pricing_table_item_text']); ?>
								</div>

								<?php if ($item['ma_el_pricing_table_tooltip_text']) { ?>
									<div class="ma-el-tooltip-text">
										<?php echo $this->parse_text_editor($item['ma_el_pricing_table_tooltip_text']); ?>
									</div>
								<?php } ?>
							</div>
						<?php } else {
							echo '&nbsp;';
						} ?>

					</li>
				<?php } ?>

			</ul>
			<?php }
	}



	public function render_button()
	{
		$settings         = $this->get_settings();
		$button_size      = ($settings['ma_el_pricing_table_button_size']) ? 'elementor-size-' . $settings['ma_el_pricing_table_button_size'] : '';
		$button_animation = (!empty($settings['button_hover_animation'])) ? ' elementor-animation-' . $settings['ma_el_pricing_table_button_hover_animation'] : '';

		$button_bg_color = ($settings['ma_el_pricing_table_head_color_scheme']) ? $settings['ma_el_pricing_table_head_color_scheme'] : '';

		$this->add_render_attribute(
			'button',
			'class',
			[
				'elementor-button',
				'ma-el-price-table-btn',
				$button_bg_color,
				$button_size,
			]
		);

		if (!empty($settings['ma_el_pricing_table_link']['url'])) {
			$this->add_render_attribute('button', 'href', $settings['ma_el_pricing_table_link']['url']);

			if (!empty($settings['ma_el_pricing_table_link']['is_external'])) {
				$this->add_render_attribute('button', 'target', '_blank');
			}
		}

		if (!empty($settings['ma_el_pricing_table_button_hover_animation'])) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		if ($settings['ma_el_pricing_table_edd_as_button']) {
			echo edd_get_purchase_link([
				'download_id' => $settings['ma_el_pricing_table_edd_id'],
				'price' => false,
				'text' => esc_html($settings['ma_el_pricing_table_button_text']),
				'class' => 'ma-el-price-table-button elementor-button ' . $button_size . $button_animation,
			]);
		} else {
			if (!empty($settings['ma_el_pricing_table_button_text'])) : ?>
				<div class="ma-el-price-table-bottom">
					<a <?php echo $this->get_render_attribute_string('button'); ?>>
						<?php echo esc_html($settings['ma_el_pricing_table_button_text']); ?>
					</a>
				</div><!-- /.price-table-bottom -->
			<?php endif;
		}
	}

	public function jltma_render_ribbon()
	{
		$settings = $this->get_settings();

		if ($settings['ma_el_pricing_table_show_ribbon'] && !empty($settings['ma_el_pricing_table_ribbon_title'])) :
			$this->add_render_attribute('ribbon-wrapper', 'class', 'ma-el-price-table-ribbon');

			if (!empty($settings['ma_el_pricing_table_ribbon_align'])) :
				$this->add_render_attribute('ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ma_el_pricing_table_ribbon_align']);
			endif; ?>

			<div <?php echo $this->get_render_attribute_string('ribbon-wrapper'); ?>>
				<div class="ma-el-price-table-ribbon-inner">
					<?php echo esc_html($settings['ma_el_pricing_table_ribbon_title']); ?>
				</div>
			</div>
		<?php endif;
	}


	public function jltma_render_footer()
	{
		$settings = $this->get_settings();

		if (!empty($settings['ma_el_pricing_table_button_text']) || !empty($settings['ma_el_pricing_table_footer_additional_info'])) { ?>

			<div class="ma-el-price-table-footer">

				<?php $this->render_button(); ?>

				<?php if (!empty($settings['ma_el_pricing_table_footer_additional_info'])) { ?>
					<div class="ma-el-price-table-additional_info">
						<p>
							<?php echo wp_kses_post($settings['ma_el_pricing_table_footer_additional_info']); ?>
						</p>
					</div>
				<?php } ?>
			</div>

		<?php }
	}


	protected function render()
	{
		$settings = $this->get_settings();

		$ma_el_pricing_table_layout = $settings['ma_el_pricing_table_layout'];

		$ma_el_pricing_table_highlight = $settings['ma_el_pricing_table_highlight'];

		$ma_el_table_class = '';
		if ($ma_el_pricing_table_layout == 'one') {

			$ma_el_table_class = "default-table text-center";
		} elseif ($ma_el_pricing_table_layout == 'two') {

			$ma_el_table_class = "table-left-align text-left";
		} elseif ($ma_el_pricing_table_layout == 'three') {

			$ma_el_table_class = "table-active-zoom text-center";
		} elseif ($ma_el_pricing_table_layout == 'four') {

			$ma_el_table_class = "table-bg-image text-left";
		} elseif ($ma_el_pricing_table_layout == 'five') {

			$ma_el_table_class = "table-bg-pattern text-left";
		}

		$this->add_render_attribute(
			'ma_el_pricing_table',
			'class',
			[
				'me-el-pricing-tables',
				$ma_el_table_class
			]
		);

		$this->add_render_attribute(
			'ma_el_pricing_table_container',
			'class',
			[
				'ma-el-price-table',
				($ma_el_pricing_table_highlight == 'yes') ? 'active gradient-1' : ''
			]
		);

		?>

		<section <?php echo $this->get_render_attribute_string('ma_el_pricing_table'); ?>>

			<div <?php echo $this->get_render_attribute_string('ma_el_pricing_table_container'); ?>>

				<?php if (
					$ma_el_pricing_table_layout == 'three' ||
					$ma_el_pricing_table_layout == 'four' ||
					$ma_el_pricing_table_layout == 'five'
				) { ?>
					<div class="ma-el-table-inner">
					<?php } ?>

					<?php $this->jltma_pt_render_header(); ?>

					<div class="ma-el-price-table-details">

						<?php $this->render_features_list(); ?>

						<?php $this->jltma_render_footer(); ?>

					</div><!-- /.price-table-details -->

					<?php if (
						$ma_el_pricing_table_layout == 'three' ||
						$ma_el_pricing_table_layout == 'four' ||
						$ma_el_pricing_table_layout == 'five'
					) { ?>
					</div><!-- /.ma-el-table-inner -->
				<?php } ?>


			</div>

		</section>


<?php
		$this->jltma_render_ribbon();
	}
}
