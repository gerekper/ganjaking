<?php

namespace ElementPack\Modules\CryptoCurrencyCard\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Crypto_Currency_Card extends Module_Base {

	public function get_name() {
		return 'bdt-crypto-currency-card';
	}

	public function get_title() {
		return BDTEP . esc_html__('Crypto Currency Card', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-crypto-currency-card';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['cryptocurrency', 'crypto', 'currency', 'table'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-crypto-currency-card'];
		}
	}

	public function get_script_depends() {
		return ['ep-crypto-currency-card'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/TnSjwUKrw00';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_cryptocurrency',
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

		// $this->add_control(
		// 	'limit',
		// 	[
		// 		'label'   => esc_html__('Limit', 'bdthemes-element-pack'),
		// 		'type'    => Controls_Manager::NUMBER,
		// 		'default' => 1,
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_option',
			[
				'label' => __('Additional Option', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_currency_image',
			[
				'label'   => __('Show Currency Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			]
		);

		// name & short name inline with prefix class
		$this->add_control(
			'currency_name_short_name_inline',
			[
				'label'   => __('Name & Short Name Inline', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_currency_name' => 'yes',
					'show_currency_short_name' => 'yes',
				],
				'prefix_class' => 'ep-crypto-currency-name-short-name-inline-',
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

		$this->add_control(
			'show_currency_change_price',
			[
				'label'   => __('Show Change Price (%)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'show_currency_current_price' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_currency_marketing_rank',
			[
				'label'   => __('Show Marketing Rank', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_currency_market_cap',
			[
				'label'   => __('Show Market Cap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_currency_total_volume',
			[
				'label'   => __('Show Total Volume', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_currency_high_low',
			[
				'label'   => __('Show 24h Change(%)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_cryptocurrency_item_style',
			[
				'label' => __('Crypto Currency Card', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-crypto-currency-card',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'selector'  => '{{WRAPPER}} .bdt-ep-crypto-currency-card',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-crypto-currency-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-crypto-currency-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-crypto-currency-card',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cryptocurrency_image_style',
			[
				'label' => __('Logo', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_currency_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'currency_logo_image_width',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-currency-image img' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				],
			]
		);

		//margin
		$this->add_responsive_control(
			'currency_logo_image_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-currency-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'cryptocurrency_name_heading',
			[
				'label' => __('Name', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_currency_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'cryptocurrency_name_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-currency-name span' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-ep-currency-name span',
				'condition' => [
					'show_currency_name' => 'yes'
				]
			]
		);

		//margin
		$this->add_responsive_control(
			'currency_name_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-currency-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_currency_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'ccc_shortname_heading',
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
					'{{WRAPPER}} .bdt-ep-currency-short-name span' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-ep-currency-short-name span',
				'condition' => [
					'show_currency_short_name' => 'yes'
				]
			]
		);

		//margin
		$this->add_responsive_control(
			'currency_short_name_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-currency-short-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'condition' => [
					'show_currency_current_price' => 'yes'
				]
			]
		);

		$this->add_control(
			'ccc_price_heading',
			[
				'label' => __('Price', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_currency_current_price' => 'yes'
				]
			]
		);

		$this->add_control(
			'cryptocurrency_current_price_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-current-price .bdt-price' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_currency_current_price' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cryptocurrency_price_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-current-price .bdt-price',
				'condition' => [
					'show_currency_current_price' => 'yes'
				]
			]
		);

		$this->add_control(
			'ccc_percentage_heading',
			[
				'label' => __('Percentage (Hourly Change Price)', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_currency_change_price' => 'yes'
				]
			]
		);

		$this->add_control(
			'cryptocurrency_percentage_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-current-price .bdt-percentage' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_currency_change_price' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cryptocurrency_percentage_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-current-price .bdt-percentage',
				'condition' => [
					'show_currency_change_price' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ccc_change_price_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-current-price .bdt-percentage' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_currency_change_price' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cryptocurrency_text_style',
			[
				'label' => __('Currency List', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'show_currency_marketing_rank',
							'value' => 'yes',
						],
						[
							'name'     => 'show_currency_market_cap',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_currency_total_volume',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_currency_high_low',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'cryptocurrency_text_secondary_color',
			[
				'label' => __('Atribute Name Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-ccc-atribute .bdt-ep-item-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cryptocurrency_text_primary_color',
			[
				'label' => __('Atribute Value Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-ccc-atribute span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cryptocurrency_text_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-ccc-atribute span',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cryptocurrency_card_text_item_border',
				'selector' => '{{WRAPPER}} .bdt-ep-ccc-atribute',
			]
		);

		$this->add_control(
			'crypto_currency_card_test_item_border_color',
			[
				'label' => __('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-ccc-atribute' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cryptocurrency_text_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-ccc-atribute' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'cryptocurrency_text_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-ccc-atribute span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id = 'bdt-crypto-currency-' . $this->get_id();

		$crypto_currency = ($settings['crypto_currency']) ? $settings['crypto_currency'] : 'all';

		$this->add_render_attribute('crypto', [
			'class'         => 'bdt-ep-crypto-currency-card',
			'id'            => $id,
			'data-settings' => wp_json_encode(
				[
					'id'         => '#' . $id,
					'widgetId'   => $id,
					'ids'        => $crypto_currency,
					'currency'   => $settings['currency'],
					'limit'      => 1,
					'order'      => 'market_cap_desc',
					'pageLength' =>  1000,
					'showCurrencyImage' => ('yes' == $settings['show_currency_image']) ? true : false,
					'showCurrencyName' => ('yes' == $settings['show_currency_name']) ? true : false,
					'showCurrencyShortName' => ('yes' == $settings['show_currency_short_name']) ? true : false,
					'showCurrencyCurrentPrice' => ('yes' == $settings['show_currency_current_price']) ? true : false,
					'showCurrencyChangePrice' => ('yes' == $settings['show_currency_change_price']) ? true : false,
					'showMarketCapRank' => ('yes' == $settings['show_currency_marketing_rank']) ? true : false,
					'showMarketCap' => ('yes' == $settings['show_currency_market_cap']) ? true : false,
					'showTotalVolume' => ('yes' == $settings['show_currency_total_volume']) ? true : false,
					'showPriceChange' => ('yes' == $settings['show_currency_high_low']) ? true : false,
				]
			),
		]);

?>
		<div <?php $this->print_render_attribute_string('crypto'); ?>>
		<div><?php echo esc_html('Data Loading...', 'bdthemes-element-pack'); ?></div>
		</div>

<?php
	}
}
