<?php

namespace ElementPack\Modules\CryptoCurrencyList\Widgets;

use Elementor\Controls_Manager;
use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use ElementPack\Utils;

if (!defined('ABSPATH')) {
	exit;
}

// Exit if accessed directly

class Crypto_Currency_List extends Module_Base {

	public function get_name() {
		return 'bdt-crypto-currency-list';
	}

	public function get_title() {
		return BDTEP . esc_html__('Crypto Currency List', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-crypto-currency-list';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-crypto-currency-list'];
		}
	}

	public function get_script_depends() {
		return ['chart', 'ep-crypto-currency-list'];
	}

	public function get_keywords() {
		return ['crypto-currency'];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Crypto Currency', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'crypto_currency',
			[
				'label'       => __('Crypto Currency', 'bdthemes-element-pack'),
				'description'       => __('If you want to show any selected crypto currency in your table so type those currency name here. For example: bitcoin,ethereum,litecoin', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('bitcoin,ethereum', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
			]
		);

		$this->add_control(
			'currency',
			[
				'label'   => esc_html__('Currency', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'USD',
				'options' => [
					'USD' => esc_html__('USD', 'bdthemes-element-pack'),
					'EUR' => esc_html__('EUR', 'bdthemes-element-pack'),
					'CRC' => esc_html__('CRC', 'bdthemes-element-pack'),
					'GBP' => esc_html__('GBP', 'bdthemes-element-pack'),
					'INR' => esc_html__('INR', 'bdthemes-element-pack'),
					'JPY' => esc_html__('JPY', 'bdthemes-element-pack'),
					'KRW' => esc_html__('KRW', 'bdthemes-element-pack'),
					'NGN' => esc_html__('NGN', 'bdthemes-element-pack'),
					'PHP' => esc_html__('PHP', 'bdthemes-element-pack'),
					'PLN' => esc_html__('PLN', 'bdthemes-element-pack'),
					'PYG' => esc_html__('PYG', 'bdthemes-element-pack'),
					'THB' => esc_html__('THB', 'bdthemes-element-pack'),
					'UAH' => esc_html__('UAH', 'bdthemes-element-pack'),
					'VND' => esc_html__('VND', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'limit',
			[
				'label'   => esc_html__('Limit', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_option',
			[
				'label' => __('Additional Option', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'           => __('Columns', 'bdthemes-element-pack'),
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'options'         => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Row Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_currency_image',
			[
				'label'   => __('Show Currency Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_currency_name',
			[
				'label'   => __('Show Currency Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_currency_short_name',
			[
				'label'   => __('Show Currency Short Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'show_currency_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_currency_current_price',
			[
				'label'   => __('Show Current Price', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_items',
			[
				'label' => __('Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// $this->start_controls_tabs('tabs_item_style');

		// $this->start_controls_tab(
		// 	'tab_item_normal',
		// 	[
		// 		'label' => esc_html__('Normal', 'bdthemes-element-pack'),
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'selector'  => '{{WRAPPER}} .bdt-crypto-currency-list-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'selector'  => '{{WRAPPER}} .bdt-crypto-currency-list-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-crypto-currency-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-crypto-currency-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-crypto-currency-list-item',
			]
		);

		// $this->end_controls_tab();

		// $this->start_controls_tab(
		// 	'tab_item_hover',
		// 	[
		// 		'label' => esc_html__('Hover', 'bdthemes-element-pack'),
		// 	]
		// );

		// $this->add_control(
		// 	'item_hover_border_color',
		// 	[
		// 		'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'condition' => [
		// 			'item_border_border!' => '',
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-crypto-currency-list-item:hover' => 'border-color: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_group_control(
		// 	Group_Control_Box_Shadow::get_type(),
		// 	[
		// 		'name'     => 'item_hover_box_shadow',
		// 		'selector' => '{{WRAPPER}} .bdt-crypto-currency-list-item:hover',
		// 	]
		// );

		// $this->end_controls_tab();

		// $this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cryptocurrency_image_style',
			[
				'label' => __('Currency Logo', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_currency_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'currency_logo_background',
				'selector'  => '{{WRAPPER}} .bdt-crypto-currency-list-img img',
			]
		);

		$this->add_responsive_control(
			'currency_logo_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'currency_logo_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-img img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'currency_logo_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-img img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'currency_logo_image_width',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-img img' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cryptocurrency_name_style',
			[
				'label' => __('Currency Name', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'show_currency_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_currency_short_name',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'currency_name_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cryptocurrency_name_heading',
			[
				'label' => __('Name', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_currency_name' => 'yes'
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cryptocurrency_name_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_currency_name' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .bdt-crypto-currency-list-title',
				'condition' => [
					'show_currency_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'ccurrency_shortname_heading',
			[
				'label' => __('Short Name', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_currency_short_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'cryptocurrency_short_name_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-title span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_currency_short_name' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'short_name_typography',
				'selector' => '{{WRAPPER}} .bdt-crypto-currency-list-title span',
				'condition' => [
					'show_currency_short_name' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cryptocurrency_current_price_style',
			[
				'label' => __('Currency Price', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'show_currency_current_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'cryptocurrency_current_price_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-crypto-currency-list-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cryptocurrency_price_typography',
				'selector' => '{{WRAPPER}} .bdt-crypto-currency-list-price',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id = 'bdt-crypto-currency-' . $this->get_id();

		$crypto_currency = ($settings['crypto_currency']) ? $settings['crypto_currency'] : 'all';

		$this->add_render_attribute('crypto', [
			'class'         => 'bdt-crypto-currency-list',
			'id'            => $id,
			'data-settings' => wp_json_encode(
				[
					'id'         => '#' . $id,
					'widgetId'   => $id,
					'ids'        => $crypto_currency,
					'currency'   => $settings['currency'],
					'limit'      => !empty($settings['limit']) ? $settings['limit'] : 100,
					'order'      => 'market_cap_desc',
					'pageLength' =>  1000,
					'showCurrencyImage' => ('yes' == $settings['show_currency_image']) ? true : false,
					'showCurrencyName' => ('yes' == $settings['show_currency_name']) ? true : false,
					'showCurrencyShortName' => ('yes' == $settings['show_currency_short_name']) ? true : false,
					'showCurrencyCurrentPrice' => ('yes' == $settings['show_currency_current_price']) ? true : false,
				]
			),
		]);

		$this->add_render_attribute('crypto-item', 'class', 'bdt-crypto-currency-list-item');

?>
		<div <?php $this->print_render_attribute_string('crypto'); ?>>

			<div><?php echo esc_html('Data Loading...', 'bdthemes-element-pack'); ?></div>

		</div>
<?php
	}
}
