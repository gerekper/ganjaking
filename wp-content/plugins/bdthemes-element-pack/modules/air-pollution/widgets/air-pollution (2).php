<?php

namespace ElementPack\Modules\AirPollution\Widgets;

use Elementor\Controls_Manager;
use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use ElementPack\Utils;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Air_Pollution extends Module_Base {

    public $weather_data = [];

    public $open_weather_api_current_url = 'http://api.openweathermap.org/data/2.5/air_pollution';

    public function get_name() {
        return 'bdt-air-pollution';
    }

    public function get_title() {
        return BDTEP . esc_html__('Air Pollution', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-air-pollution';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['weather', 'air', 'pollution'];
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/m38ddVi52-Q';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_air_pollution',
            [
                'label' => esc_html__('Air Pollution', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'weather_cache',
            [
                'label'       => esc_html__('Cache Weather', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'yes',
                'description' => esc_html__('Note:- If are you using Free Plan of Open Weather, please use this cache option to reduce your request of API Calls.', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'cache_refresh',
            array(
                'label'     => esc_html__('Reload Cache after ', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => '1',
                'options'   => array(
                    '15' => esc_html__('15 Minutes', 'bdthemes-element-pack'),
                    '30' => esc_html__('30 Minutes', 'bdthemes-element-pack'),
                    '1'  => esc_html__('1 Hour', 'bdthemes-element-pack'),
                    '3'  => esc_html__('3 Hour', 'bdthemes-element-pack'),
                    '6'  => esc_html__('6 Hour', 'bdthemes-element-pack'),
                    '12' => esc_html__('12 Hour', 'bdthemes-element-pack'),
                    '24' => esc_html__('24 Hour', 'bdthemes-element-pack'),
                ),
                'condition' => [
                    'weather_cache' => 'yes',
                ],
            )
        );

        $this->add_control(
            'location_lat',
            [
                'label'       => esc_html__('Latitude', 'bdthemes-element-pack'),
                'description' => esc_html__('Latitude of place required, for example: 24.844955182035367', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'default'     => '24.844955182035367',
            ]
        );

        $this->add_control(
            'location_lon',
            [
                'label'       => esc_html__('Longitude', 'bdthemes-element-pack'),
                'description' => esc_html__('Longitude of place required, for example: 89.37432773212058', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'default'     => '89.37432773212058',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label'     => esc_html__('Show Date', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_html_tag',
            [
                'label'   => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => element_pack_title_tags(),
                'default' => 'h4',
            ]
        );

        $this->add_control(
            'list_stripe',
            [
                'label'   => esc_html__('List Stripe', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'list_divider',
            [
                'label' => esc_html__('List Divider', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_list',
            [
                'label' => esc_html__('List', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'style_list_tabs'
        );

        $this->start_controls_tab(
            'style_list_normal_tab',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'style_title_heading',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ap-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-ap-title',
            ]
        );

        $this->add_control(
            'style_value_heading',
            [
                'label'     => esc_html__('Value', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ap-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'value_typography',
                'selector' => '{{WRAPPER}} .bdt-ap-value',
            ]
        );

        $this->add_control(
            'style_list_heading',
            [
                'label'     => esc_html__('List', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'list_bg',
                'label'    => esc_html__('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-list li',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_list_hover_tab',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-list li:hover .bdt-ap-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_color_hover',
            [
                'label'     => esc_html__('Value Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-list li:hover .bdt-ap-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'style_list_heading_hover',
            [
                'label'     => esc_html__('List', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'list_bg_hover',
                'label'    => esc_html__('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} ul.bdt-list li:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // $this->add_responsive_control(
        //     'list_spacing',
        //     [
        //         'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
        //         'type'    => Controls_Manager::SLIDER,
        //         'separator' => 'before',
        //         'selectors' => [
        //             '{{WRAPPER}} .bdt-list li:not(:first-child)'  => 'margin-top: {{SIZE}}{{UNIT}};',
        //         ],
        //     ]
        // );

        $this->add_responsive_control(
            'list_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'separator'  => 'before',
                'selectors'  => [
                    '{{WRAPPER}} .bdt-list li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'heading_stripe',
            [
                'label'     => esc_html__('List Stripe', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => ['list_stripe' => 'yes'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'list_stripe_bg',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} ul.bdt-list-striped>:nth-of-type(odd)',
                'condition' => ['list_stripe' => 'yes']
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_date',
            [
                'label'     => esc_html__('Date', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => ['show_date' => 'yes']
            ]
        );

        $this->add_responsive_control(
            'date_align',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bdt-ap-date-wrapper .bdt-text-lead' => 'text-align: {{VALUE}} !important;',
                ],

            ]
        );

        $this->add_control(
            'date_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ap-date-wrapper .bdt-text-lead' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'date_bg',
                'label'    => esc_html__('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-ap-date-wrapper .bdt-text-lead',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'date_typography',
                'selector' => '{{WRAPPER}} .bdt-ap-date-wrapper .bdt-text-lead',
            ]
        );

        $this->add_responsive_control(
            'date_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ap-date-wrapper .bdt-text-lead' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'date_margin',
            [
                'label'     => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ap-date-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_divider',
            [
                'label'     => esc_html__('Divider', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => ['list_divider' => 'yes']
            ]
        );

        $this->add_control(
            'divider_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-list-divider>:nth-child(n+2)' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bdt-list-divider>:nth-child(n+2)' => 'border-top-width: {{SIZE}}{{UNIT}};',
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
        $settings = $this->get_settings_for_display();

        $ep_api_settings = get_option('element_pack_api_settings');
        $api_key         = !empty($ep_api_settings['open_weather_api_key']) ? $ep_api_settings['open_weather_api_key'] : '';

        if (!$api_key) {

            $message = esc_html__('Ops! I think you forget to set API key in Element Pack API settings. Please set your API key on Open Weather Map access box', 'bdthemes-element-pack');

            $this->weather_error_notice($message);

            return false;
        }

        $this->weather_data = $this->weather_data();

        $this->add_render_attribute('air-pollution', 'class', 'bdt-air-pollution');

        $data         = $this->weather_data;
        $weather_date = NULL;
        if (!empty($data)) {
            $weather_date_timestamp = $data['dt'];
            $weather_date           = date_i18n(get_option('date_format'), $weather_date_timestamp);
        }
        $components_tag = Utils::get_valid_html_tag($settings['title_html_tag']);
        $list_stripe    = $settings['list_stripe'] == 'yes' ? ' bdt-list-striped ' : '';
        $list_divider   = $settings['list_divider'] == 'yes' ? ' bdt-list-divider ' : '';

        $this->add_render_attribute('list', 'class', 'bdt-list bdt-list-large bdt-margin-remove ' . $list_stripe . $list_divider);

?>

        <div <?php echo $this->get_render_attribute_string('air-pollution'); ?>>
            <?php if ($settings['show_date'] == 'yes') : ?>
                <div class="bdt-ap-date-wrapper">
                    <div class="bdt-text-lead bdt-text-center bdt-margin-bottom">
                        <?php echo esc_html($weather_date); ?>
                    </div>
                </div>
            <?php endif; ?>
            <ul <?php echo $this->get_render_attribute_string('list'); ?>>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove
                        bdt-ap-title">
                            <?php _e('Carbon monoxide', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['co']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Nitrogen monoxide', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['no']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Nitrogen dioxide', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['no2']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Ozone', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['o3']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Sulphur dioxide', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['so2']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Fine particles matter', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['pm2_5']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Coarse particulate matter', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['pm10']);
                            } ?>
                        </span>
                    </div>
                </li>
                <li class="bdt-flex bdt-flex-between  bdt-flex-middle bdt-padding-small">
                    <div>
                        <<?php echo esc_attr($components_tag); ?> class="bdt-text-default bdt-text-bold bdt-margin-remove bdt-ap-title">
                            <?php _e('Ammonia', 'bdthemes-element-pack'); ?>
                        </<?php echo esc_attr($components_tag); ?>>
                    </div>
                    <div>
                        <span class="bdt-text-default bdt-text-normal bdt-ap-value">
                            <?php if (!empty($data)) {
                                echo esc_html($data['components']['nh3']);
                            } ?>
                        </span>
                    </div>
                </li>
            </ul>
        </div>

    <?php
    }

    public function weather_data() {
        $settings = $this->get_settings_for_display();


        $ep_api_settings = get_option('element_pack_api_settings');
        $api_key         = !empty($ep_api_settings['open_weather_api_key']) ? $ep_api_settings['open_weather_api_key'] : '';

        // return error message when api key not found
        if (!$api_key) {

            $message = esc_html__('Ops! I think you forget to set API key in Element Pack API settings. Please set your API key on Open Weather Map access box', 'bdthemes-element-pack');

            $this->weather_error_notice($message);

            return false;
        }

        $location = $settings['location_lat'];

        if (empty($settings['location_lat']) || empty($settings['location_lon'])) {
            return false;
        }

        $transient_key = sprintf('bdt-open-weather-air-pollution-data-%s', md5($location));

        if ($settings['weather_cache'] == 'yes') {
            $data = get_transient($transient_key);
        } else {
            $data = '';
        }

        if (!$data) {
            // Prepare request data

            $api_key = esc_attr($api_key);
            $lat     = esc_attr($settings['location_lat']);
            $lon     = esc_attr($settings['location_lon']);

            $request_args = array(
                'appid' => $api_key,
                'lat'   => $lat,
                'lon'   => $lon,
            );

            $request_url = add_query_arg(
                $request_args,
                $this->open_weather_api_current_url
            );

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

                $this->weather_error_notice($message);

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

        $data = array(
            'location'   => array(
                'lon' => $weather['coord']['lon'],
                'lat' => $weather['coord']['lat'],
            ),
            'components' => array(
                'co'    => $weather['list'][0]['components']['co'],
                'no'    => $weather['list'][0]['components']['no'],
                'no2'   => $weather['list'][0]['components']['no2'],
                'o3'    => $weather['list'][0]['components']['o3'],
                'so2'   => $weather['list'][0]['components']['so2'],
                'pm2_5' => $weather['list'][0]['components']['pm2_5'],
                'pm10'  => $weather['list'][0]['components']['pm10'],
                'nh3'   => $weather['list'][0]['components']['nh3'],
            ),
            'dt'         => $weather['list'][0]['dt'],
            'week_day'   => date_i18n('l'),
        );

        return $data;
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
