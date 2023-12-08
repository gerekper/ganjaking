<?php

namespace ElementPack\Modules\Weather\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Weather extends Module_Base {

	public $weather_data = [];

	public $weather_api_current_url = 'http://api.weatherstack.com/current';
	public $open_weather_api_current_url = 'http://api.openweathermap.org/data/2.5/weather';

	public function get_name() {
		return 'bdt-weather';
	}

	public function get_title() {
		return BDTEP . esc_html__('Weather', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-weather';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['weather', 'cloudy', 'sunny', 'morning', 'evening'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-weather'];
		}
	}
	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return ['ep-weather'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/Vjyl4AAAufg';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_weather',
			[
				'label' => esc_html__('Weather', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'select_api_service',
			[
				'label'   => esc_html__('Select API', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'weather-stack',
				'options' => [
					'weather-stack'   => esc_html__('Weather Stack', 'bdthemes-element-pack'),
					'open-weather'    => esc_html__('Open Weather Map', 'bdthemes-element-pack'),
				],
				'render_type' => 'template',
			]
		);


		$this->add_control(
			'view',
			[
				'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'simple',
				'options' => [
					'simple'   => esc_html__('Simple', 'bdthemes-element-pack'),
					'today'    => esc_html__('Today', 'bdthemes-element-pack'),
					'tiny'     => esc_html__('Tiny', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-weather-layout-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'weather_cache',
			[
				'label'   => esc_html__('Cache Weather', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => esc_html__('Note:- If are you using Free Plan of Weather Stack, please use this cache option to reduce your request of API Calls.', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'cache_refresh',
			array(
				'label'   => __('Reload Cache after ', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'15'  => __('15 Minutes', 'bdthemes-element-pack'),
					'30'  => __('30 Minutes', 'bdthemes-element-pack'),
					'1'  => __('1 Hour', 'bdthemes-element-pack'),
					'3'  => __('3 Hour', 'bdthemes-element-pack'),
					'6'  => __('6 Hour', 'bdthemes-element-pack'),
					'12'  => __('12 Hour', 'bdthemes-element-pack'),
					'24'  => __('24 Hour', 'bdthemes-element-pack'),
				),
				'condition' => [
					'weather_cache' => 'yes'
				]
			)
		);

		$this->add_control(
			'location',
			[
				'label'   => esc_html__('Location', 'bdthemes-element-pack'),
				'description'   => esc_html__('City and Region required, for example: Boston, MA', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => 'Bogra, BD',
			]
		);

		$this->add_control(
			'country',
			[
				'label'   => esc_html__('Country (optional)', 'bdthemes-element-pack'),
				'description'   => esc_html__('If you want to override country name, for example: USA', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'units',
			[
				'label'   => esc_html__('Units', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'metric',
				'options' => [
					'metric'   => esc_html__('Metric', 'bdthemes-element-pack'),
					'imperial' => esc_html__('Imperial', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'show_city',
			[
				'label'   => esc_html__('Show City Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_country',
			[
				'label'   => esc_html__('Show Country Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'view!' => ['tiny']
				]
			]
		);

		$this->add_control(
			'show_temperature',
			[
				'label'   => esc_html__('Show Temperature', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_weather_condition_name',
			[
				'label'   => esc_html__('Show Weather Condition Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_weather_icon',
			[
				'label'   => esc_html__('Show Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'view!' => ['forecast']
				]
			]
		);

		$this->add_control(
			'show_weather_desc',
			[
				'label'   => esc_html__('Show Description', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'view' => ['tiny']
				]
			]
		);

		$this->add_control(
			'show_today_name',
			[
				'label'   => esc_html__('Show Today Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'view!' => ['tiny', 'simple']
				]
			]
		);

		$this->add_control(
			'weather_details',
			[
				'label'   => esc_html__('Weather Details', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'view!' => ['tiny', 'simple']
				]
			]
		);

		$this->add_control(
			'weather_value_round',
			[
				'label'   => esc_html__('Round Number', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'weather_dynamic_bg',
			[
				'label'   => esc_html__('Dynamic Background', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dynamic_bg',
			[
				'label' => esc_html__('Dynamic Backgrounds', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'weather_dynamic_bg' => 'yes'
				]
			]
		);

		$this->add_control(
			'dynamic_bg_sunny',
			[
				'label'   => esc_html__('Sunny', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'dynamic_bg_cloudy',
			[
				'label'   => esc_html__('Cloudy', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'dynamic_bg_rain',
			[
				'label'   => esc_html__('Rain', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'dynamic_bg_mist',
			[
				'label'   => esc_html__('Mist', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'dynamic_bg_snow',
			[
				'label'   => esc_html__('Snow', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'dynamic_bg_fog',
			[
				'label'   => esc_html__('Fog', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_weather',
			[
				'label' => esc_html__('Weather', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather [class*="bdtw-"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tiny_text_typography',
				'selector' => '{{WRAPPER}} .bdt-weather',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_location_style',
			[
				'label'     => esc_html__('Location', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_city',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_country',
							'value'    => 'yes'
						]
					]
				]
			]
		);

		$this->add_control(
			'location_city_name_heading',
			[
				'label'     => esc_html__('City Name', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_city' => 'yes',
				],
			]
		);

		$this->add_control(
			'tiny_location_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-container .bdt-weather-city-name' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_city' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'location_city_name_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-container .bdt-weather-city-name',
				'condition' => [
					'show_city' => 'yes',
				],
			]
		);

		$this->add_control(
			'location_country_name_heading',
			[
				'label'     => esc_html__('Country Name', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_country' => 'yes',
					'view!' => 'tiny'
				],
			]
		);

		$this->add_control(
			'location_country_name_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-title .bdt-weather-country-name' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_country' => 'yes',
					'view!' => 'tiny'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'location_country_name_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-title .bdt-weather-country-name',
				'condition' => [
					'show_country' => 'yes',
					'view!' => 'tiny'
				],
			]
		);

		$this->add_responsive_control(
			'location_country_name_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-title .bdt-weather-country-name' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_country' => 'yes',
					'view!' => 'tiny'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_temperature_style',
			[
				'label'     => esc_html__('Temperature', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_temperature' => 'yes'
				]
			]
		);

		$this->add_control(
			'tiny_temp_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today-temp' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'temp_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-today-temp',
			]
		);

		$this->add_responsive_control(
			'temp_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today-temp' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'view!' => 'tiny'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_today_name_style',
			[
				'label'     => esc_html__('Today Name', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_today_name' => 'yes',
					'view' => 'today'
				]
			]
		);

		$this->add_control(
			'today_name_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today .bdt-weather-today-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'today_name_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-today .bdt-weather-today-name',
			]
		);

		$this->add_responsive_control(
			'today_name_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today .bdt-weather-today-name' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_condition_style',
			[
				'label'      => esc_html__('Weather Icon / Condition Name', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_weather_condition_name',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_weather_icon',
							'value'    => 'yes'
						]
					]
				]
			]
		);

		$this->add_control(
			'weather_icon_heading',
			[
				'label'     => esc_html__('Weather Icon', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_weather_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'tiny_icon_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today-icon [class*="bdtw-"]' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_weather_icon' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'weather_icon_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-today-icon',
				'condition' => [
					'show_weather_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'weather_condition_heading',
			[
				'label'     => esc_html__('Weather Condition Name', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_weather_condition_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'tiny_weather_desc',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today-desc' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_weather_condition_name' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'weather_condition_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-today-desc',
				'condition' => [
					'show_weather_condition_name' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'weather_condition_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-today-desc' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'view!' => 'tiny',
					'show_weather_condition_name' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_weather_details_style',
			[
				'label'     => esc_html__('Weather Details', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'weather_details' => 'yes',
					'view' => 'today'
				]
			]
		);

		$this->add_responsive_control(
			'weather_details_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-details' => 'margin-top: {{SIZE}}{{UNIT}}; padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'weather_details_border_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-details' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'weather_details_text_heading',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'weather_details_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-details [class*="bdt-weather-today-"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'weather_details_text_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-details [class*="bdt-weather-today-"]',
			]
		);

		$this->add_control(
			'weather_details_icon_heading',
			[
				'label'     => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'weather_details_icon_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-details [class*="bdtw-"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'weather_details_icon_typography',
				'selector' => '{{WRAPPER}} .bdt-weather .bdt-weather-details [class*="bdtw-"]',
			]
		);

		$this->add_responsive_control(
			'weather_details_icon_spacing',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-weather .bdt-weather-details [class*="bdtw-"]' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_transient_expire($settings) {
		$expire_value = $settings['cache_refresh'];
		$expire_time  = 1 * HOUR_IN_SECONDS;

		if ('1' === $expire_value) {
			$expire_time = 1 * HOUR_IN_SECONDS;
		} elseif ('3' === $expire_value) {
			$expire_time = 3 * HOUR_IN_SECONDS;
		} elseif ('6' === $expire_value) {
			$expire_time = 6 * HOUR_IN_SECONDS;
		} elseif ('12' === $expire_value) {
			$expire_time = 12 * HOUR_IN_SECONDS;
		} elseif ('24' === $expire_value) {
			$expire_time = 24 * HOUR_IN_SECONDS;
		} elseif ('15' === $expire_value) {
			$expire_time = 15 * MINUTE_IN_SECONDS;
		} elseif ('30' === $expire_value) {
			$expire_time = 30 * MINUTE_IN_SECONDS;
		}

		return $expire_time;
	}

	protected function render() {
		$settings           = $this->get_settings_for_display();

		if ($settings['select_api_service'] == 'open-weather') {
			$this->weather_api_current_url = $this->open_weather_api_current_url;
			$ep_api_settings = get_option('element_pack_api_settings');
			$api_key = !empty($ep_api_settings['open_weather_api_key']) ? $ep_api_settings['open_weather_api_key'] : '';
		} else {
			$ep_api_settings = get_option('element_pack_api_settings');
			$api_key = !empty($ep_api_settings['weatherstack_api_key']) ? $ep_api_settings['weatherstack_api_key'] : '';
		}

		if (!$api_key) {

			$message = esc_html__('Ops! I think you forget to set API key in Element Pack API settings.', 'bdthemes-element-pack');

			$this->weather_error_notice($message);

			return false;
		}

		$this->weather_data = $this->weather_data();

		$this->add_render_attribute('weather', 'class', 'bdt-weather');

		// start for bg dynamic

		$data       = $this->weather_data;
		$bg_dynamic = !empty($data) && ($settings['weather_dynamic_bg'] == 'yes') ? $data['today']['code'] : false;
		$id = 'bdt-weather-' . $this->get_id();

		$bg_url = false;

		if ($settings['weather_dynamic_bg'] == 'yes') :
			$bg_sunny = !empty($settings['dynamic_bg_sunny']['url']) ? $settings['dynamic_bg_sunny']['url'] : false;
			$bg_cloudy = !empty($settings['dynamic_bg_cloudy']['url']) ? $settings['dynamic_bg_cloudy']['url'] : false;
			$bg_rain = !empty($settings['dynamic_bg_rain']['url']) ? $settings['dynamic_bg_rain']['url'] : false;
			$bg_mist = !empty($settings['dynamic_bg_mist']['url']) ? $settings['dynamic_bg_mist']['url'] : false;
			$bg_snow = !empty($settings['dynamic_bg_snow']['url']) ? $settings['dynamic_bg_snow']['url'] : false;
			$bg_fog = !empty($settings['dynamic_bg_fog']['url']) ? $settings['dynamic_bg_fog']['url'] : false;

			switch ($bg_dynamic) {
				case "113":
				case "01d":
					$bg_url = $bg_sunny;
					break;
				case "116":
				case "119":
				case "122":
				case "02d":
				case "03d":
				case "10n":
				case "09d":
				case "04d":
					$bg_url = $bg_cloudy;
					break;
				case "176":
				case "182":
				case "185":
				case "200":
				case "263":
				case "266":
				case "281":
				case "284":
				case "293":
				case "296":
				case "299":
				case "302":
				case "305":
				case "308":
				case "311":
				case "314":
				case "317":
				case "320":
				case "353":
				case "356":
				case "359":
				case "362":
				case "365":
				case "377":
				case "386":
				case "389":
				case "10d":
				case "13d":
				case "09d":
				case "11d":
				case "10d":
				case "13d":
					$bg_url = $bg_rain;
					break;
				case "143":
				case "50d":
				case "04n":
				case "50n":
					$bg_url = $bg_mist;
					break;
				case "227":
				case "230":
				case "323":
				case "326":
				case "329":
				case "332":
				case "335":
				case "338":
				case "368":
				case "371":
				case "374":
				case "392":
				case "395":
				case "13d":
				case "09d":
					$bg_url = $bg_snow;
					break;
				case "248":
				case "260":
				case "350":
				case "50d":
					$bg_url = $bg_fog;
					break;

				default:
					$bg_url = false;
			}

		endif;


		$this->add_render_attribute(
			[
				'weather' => [
					'id'			=> $id,
					'data-settings' => [
						wp_json_encode([
							'id'		=> '#' . $id,
							'dynamicBG'  => $bg_dynamic,
							'url'		 => $bg_url
						])
					]
				]
			]
		);

		//end

?>

		<div <?php echo $this->get_render_attribute_string('weather'); ?>>
			<div class="bdt-weather-container">

				<?php if ('full' == $settings['view'] or 'simple' == $settings['view'] or 'today' == $settings['view']) : ?>
					<?php $this->render_weather_today(); ?>
				<?php elseif ('tiny' == $settings['view']) : ?>
					<?php $this->render_weather_tiny(); ?>
				<?php endif; ?>

			</div>
		</div>

	<?php
	}

	public function render_weather_today() {
		$settings   = $this->get_settings_for_display();
		$data       = $this->weather_data;


		$speed_unit = ('metric' === $settings['units']) ? esc_html_x('km/h', 'Weather String', 'bdthemes-element-pack') : esc_html_x('m/h', 'Weather String', 'bdthemes-element-pack');

		if ($settings['select_api_service'] == 'open-weather') {
			$speed      = ('metric' === $settings['units']) ? $data['today']['wind_speed']['kph'] : $data['today']['wind_speed']['mph'];
		} else {
			$speed      = ('metric' === $settings['units']) ? $data['today']['wind_speed']['kph'] : $data['today']['wind_speed']['mph'];
		}

	?>

		<div class="bdt-weather-today">
			<?php if ('yes' == $settings['show_city'] or 'yes' == $settings['show_country'] or 'yes' == $settings['show_temperature'] or 'yes' == $settings['show_today_name']) : ?>
				<div class="bdt-grid bdt-grid-collapse bdt-flex bdt-flex-middle">

					<div class="bdt-width-3-5">

						<?php $this->render_weather_title(); ?>

						<?php if ('yes' == $settings['show_temperature']) : ?>
							<div class="bdt-weather-today-temp"><?php echo $this->weather_temperature($data['today']['temp']); ?></div>
						<?php endif; ?>

						<?php if ('yes' == $settings['show_today_name']) : ?>
							<div class="bdt-weather-today-name"><?php echo esc_html($data['today']['week_day']); ?></div>
						<?php endif; ?>
					</div>

					<?php if ('yes' == $settings['show_weather_icon']) : ?>
						<div class="bdt-width-2-5 bdt-text-center">
							<div class="bdt-width-1-1">
								<div class="bdt-weather-today-icon">
									<?php
									if (!empty($data)) {
										echo $this->weather_icon($data['today']['code']);
									}
									?>
								</div>

								<?php if ('yes' == $settings['show_weather_condition_name']) : ?>
									<div class="bdt-weather-today-desc">
										<?php
										if (!empty($data)) {
											echo $this->weather_desc($data['today']['code']);
										}
										?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

				</div>
			<?php else : ?>
				<div class="bdt-text-center">
					<div class="bdt-weather-today-icon"><?php
														if (!empty($data)) {
															echo $this->weather_icon($data['today']['code']);
														}
														?>

					</div>
					<?php if ('yes' == $settings['show_weather_condition_name']) : ?>
						<div class="bdt-weather-today-desc">
							<?php
							if (!empty($data)) {
								echo $this->weather_desc($data['today']['code']);
							}
							?>

						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>
		<?php if ('yes' === $settings['weather_details']) : ?>
			<div class="bdt-weather-details bdt-grid bdt-grid-collapse">
				<div class="bdt-width-1-3">
					<div class="bdt-weather-today-humidity">
						<span class="bdtw-humidity"></span>
						<?php echo esc_html($data['today']['humidity']); ?>
					</div>
				</div>
				<div class="bdt-width-1-3">
					<div class="bdt-weather-today-pressure">
						<span class="bdtw-pressure"></span>
						<?php echo $this->get_weather_pressure($data['today']['pressure']); ?>
					</div>
				</div>
				<div class="bdt-width-1-3">
					<div class="bdt-weather-today-wind">
						<span class="bdtw-<?php echo element_pack_wind_code($data['today']['wind_deg']); ?>"></span>
						<?php echo esc_html($speed) . ' ' . esc_html($speed_unit); ?>
					</div>
				</div>
			</div>
		<?php endif;
	}

	public function render_weather_tiny() {
		$settings = $this->get_settings_for_display();
		$data     = $this->weather_data;
		?>

		<?php if ('yes' == $settings['show_city']) : ?>
			<span class="bdt-weather-city-name"><?php echo $this->weather_data['location']['city']; ?></span>
		<?php endif; ?>

		<?php if ('yes' == $settings['show_temperature']) : ?>
			<span class="bdt-weather-today-temp"><?php echo $this->weather_temperature($data['today']['temp']); ?></span>
		<?php endif; ?>

		<?php if ('yes' == $settings['show_weather_icon']) : ?>
			<span class="bdt-weather-today-icon"><?php echo $this->weather_icon($data['today']['code']); ?></span>
		<?php endif; ?>
		<?php if ('yes' == $settings['show_weather_desc']) : ?>
			<span class="bdt-weather-today-desc"><?php echo $this->weather_desc($data['today']['code']); ?></span>
		<?php endif; ?>

	<?php
	}

	public function render_weather_title() {
		$settings = $this->get_settings_for_display();
		$data     = $this->weather_data;
	?>
		<?php if ('yes' == $settings['show_city'] or 'yes' == $settings['show_country']) : ?>
			<div class="bdt-weather-title">
				<?php if ('yes' == $settings['show_city']) : ?>
					<span class="bdt-weather-city-name"><?php echo $this->weather_data['location']['city']; ?></span>
				<?php endif; ?>

				<?php if ('yes' == $settings['show_country']) : ?>
					<span class="bdt-weather-country-name">

						<?php if ($settings['country']) : ?>
							<?php echo esc_html($settings['country']); ?>
						<?php else : ?>
							<?php echo $this->weather_data['location']['country']; ?>
						<?php endif; ?>

					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	<?php
	}

	public function weather_data() {
		$settings = $this->get_settings_for_display();


		if ($settings['select_api_service'] == 'open-weather') {
			$this->weather_api_current_url = $this->open_weather_api_current_url;
			$ep_api_settings = get_option('element_pack_api_settings');
			$api_key = !empty($ep_api_settings['open_weather_api_key']) ? $ep_api_settings['open_weather_api_key'] : '';
		} else {
			$ep_api_settings = get_option('element_pack_api_settings');
			$api_key = !empty($ep_api_settings['weatherstack_api_key']) ? $ep_api_settings['weatherstack_api_key'] : '';
		}




		// return error message when api key not found
		if (!$api_key) {

			$message = esc_html__('Ops! I think you forget to set API key in Element Pack API settings.', 'bdthemes-element-pack');

			$this->weather_error_notice($message);

			return false;
		}

		$location = $settings['location'];

		if (empty($location)) {
			return false;
		}

		if ($settings['select_api_service'] == 'open-weather') {
			$transient_key = sprintf('bdt-open-weather-data-%s', md5($location));

			if ($settings['weather_cache'] == 'yes') {
				$data = get_transient($transient_key);
			} else {
				$data = '';
			}
		} else {
			$transient_key = sprintf('bdt-weather-data-%s', md5($location));

			if ($settings['weather_cache'] == 'yes') {
				$data = get_transient($transient_key);
			} else {
				$data = '';
			}
		}



		if (!$data) {
			// Prepare request data

			$location = esc_attr($location);
			$api_key  = esc_attr($api_key);

			if ($settings['select_api_service'] == 'open-weather') {
				$request_args = array(
					'appid'    => $api_key,
					'q'         => urlencode($location),
					// 'units'		=> $settings['units']
					//				'forecast_days' => 6,
					//				'hourly'        => 1,
					//				'units'         => ''
				);
			} else {
				$request_args = array(
					'access_key'    => $api_key,
					'query'         => urlencode($location),
					'forecast_days' => 6,
					'hourly'        => 1,
					'units'         => ''
				);
			}




			if ($settings['select_api_service'] == 'open-weather') {
				$request_url = add_query_arg(
					$request_args,
					$this->open_weather_api_current_url
				);
			} else {
				$request_url = add_query_arg(
					$request_args,
					$this->weather_api_current_url
				);
			}

			$weather = $this->weather_remote_request($request_url);

			if (!$weather) {
				return false;
			}


			if (isset($weather['error'])) {

				if (isset($weather['error']['info'])) {
					$message = $weather['error']['info'];
				} else {
					$message = esc_html__('Weather data of this location not found.', 'bdthemes-element-pack');
				}

				echo $this->weather_error_notice($message);
				return false;
			}

			$data = $this->transient_weather($weather);

			if (empty($data)) {
				return false;
			}

			$expireTime = $this->get_transient_expire($settings);

			if ($settings['weather_cache'] == 'yes') {
				set_transient($transient_key, $data, apply_filters('element-pack/weather/cached-time', $expireTime));
			}
			return $data;
		}

		return $data;
	}

	public function weather_remote_request($url) {

		$response = wp_remote_get($url, array('timeout' => 30));

		if (!$response || is_wp_error($response)) {
			return false;
		}

		$remote_data = wp_remote_retrieve_body($response);

		if (!$remote_data || is_wp_error($remote_data)) {
			return false;
		}

		$remote_data = json_decode($remote_data, true);

		if (empty($remote_data)) {
			return false;
		}
		return $remote_data;
	}

	public function transient_weather($weather = []) {
		$settings = $this->get_settings_for_display();

		if ($settings['select_api_service'] == 'open-weather') {
			$data = array(
				'location' => array(
					'city'    => $weather['name'],
					'country' => $weather['sys']['country'],
				),
				'today' => array(
					'code'   => $weather['weather'][0]['icon'],
					'temp' => $weather['main']['temp'] - 273.15,
					'wind_speed' => array(
						'mph' => $weather['wind']['speed'] * 2.237,
						'kph' => $weather['wind']['speed'] * 3.6,
					),
					'wind_deg' => $weather['wind']['deg'],
					// 'wind_dir' => $weather['current']['wind_dir'],

					'humidity' => $weather['main']['humidity'] . '%',
					'pressure' => $weather['main']['pressure'],

					'week_day' => date_i18n('l'),
				),
				'forecast' => [],
			);
		} else {
			$data = array(
				'location' => array(
					'city'    => $weather['location']['name'],
					'country' => $weather['location']['country'],
				),
				'today' => array(
					'code'   => $weather['current']['weather_code'],
					'temp' => $weather['current']['temperature'],
					'wind_speed' => array(
						'mph' => $weather['current']['wind_speed'] * 0.62,
						'kph' => $weather['current']['wind_speed'],
					),
					'wind_deg' => $weather['current']['wind_degree'],
					'wind_dir' => $weather['current']['wind_dir'],

					'humidity' => $weather['current']['humidity'] . '%',
					'pressure' => $weather['current']['pressure'],

					'week_day' => date_i18n('l'),
				),
				'forecast' => [],
			);
		}

		return $data;
	}

	public function readable_week($format = '', $date = '') {
		$date = date_create_from_format($format, $date);
		return date_i18n('l', date_timestamp_get($date));
	}

	public function weather_desc($code, $is_day = true) {
		$settings = $this->get_settings_for_display();
		// echo $code;
		if ($settings['select_api_service'] == 'open-weather') {
			$desc = element_pack_open_weather_code($code, 'desc', $is_day);
		} else {
			$desc = element_pack_weather_code($code, 'desc', $is_day);
		}
		// echo $desc;
		if (empty($desc)) {
			$desc = 'The description will appear after the cache period.';
		}


		if (empty($desc)) {
			return [];
		}

		return $desc;
	}

	public function weather_temperature($temp) {
		$units     = $this->get_settings_for_display('units');
		$temp_unit = ('metric' === $units) ? '&#176;C' : '&#176;F';

		if (is_array($temp)) {
			$temp = ('metric' === $units) ? $temp['c'] : $temp['f'];
		}
		$temp = ('metric' === $units) ? $temp : (($temp * 1.8) + 32); //°F = °C × 1.8 + 32

		$temp_format = apply_filters('element-pack/weather/temperature-format', '%1$s%2$s');

		$settings           = $this->get_settings_for_display();
		($settings['weather_value_round']) == 'yes'  ? $temp = round($temp) :  $temp;

		return sprintf($temp_format, $temp, $temp_unit);
	}

	public function get_weather_pressure($pressure) {
		$units = $this->get_settings_for_display('units');

		if (is_array($pressure)) {
			$pressure = ('metric' === $units) ? $pressure['mb'] : $pressure['in'];
		}

		$format = apply_filters('element-pack/weather/pressure-format', '%s');

		return sprintf($format, $pressure);
	}

	public function weather_icon($icon, $is_day = true) {
		$settings = $this->get_settings_for_display();

		if ($settings['select_api_service'] == 'open-weather') {
			$icon = element_pack_open_weather_code($icon, 'icon');
		} else {
			$icon = element_pack_weather_code($icon, 'icon');
		}

		if (empty($icon)) {
			$icon = 'empty';
		}

		$time = ($is_day) ? 'd' : 'n';

		$icon_class   = [];
		$icon_class[] = sprintf('bdtw-%s', esc_attr($icon));

		return sprintf('<span class="%1$s%2$s"></span>', implode(' ', $icon_class), $time);
	}

	public function weather_error_notice($message) {
	?>

		<div class="bdt-alert-warning" data-bdt-alert>
			<a class="bdt-alert-close" data-bdt-close></a>
			<p><?php echo esc_html($message); ?></p>
		</div>
<?php
	}
}
