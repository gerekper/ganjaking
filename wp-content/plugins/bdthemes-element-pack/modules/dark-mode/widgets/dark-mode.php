<?php

namespace ElementPack\Modules\DarkMode\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Dark_Mode extends Module_Base {

    public function get_name() {
        return 'bdt-dark-mode';
    }

    public function get_title() {
        return BDTEP . esc_html__('Dark Mode', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-dark-mode';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['dark', 'mode', 'darkmode', 'dm'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-dark-mode'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['darkmode', 'ep-scripts'];
        } else {
            return ['darkmode', 'ep-dark-mode'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/nuYa-0sWFxU';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Dark Mode', 'bdthemes-dark-mode'),
            ]
        );

        $this->add_control(
            'default_mode',
            [
                'label'   => esc_html__('Default Mode', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SELECT,
                'default' => 'light',
                'options' => [
                    'light' => esc_html__('Light', 'bdthemes-element-pack'),
                    'dark'  => esc_html__('Dark', 'bdthemes-element-pack'),
                ],
                'frontend_available' => true,
                'render_type'        => 'none'
            ]
        );

        $this->add_control(
            'toggle_position',
            [
                'label'   => esc_html__('Toggle Position', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SELECT,
                'default' => 'bottom-right',
                'options' => [
                    'top-left'     => esc_html__('Top Left', 'bdthemes-element-pack'),
                    'top-right'    => esc_html__('Top Right', 'bdthemes-element-pack'),
                    'bottom-left'  => esc_html__('Bottom Left', 'bdthemes-element-pack'),
                    'bottom-right' => esc_html__('Bottom Right', 'bdthemes-element-pack'),
                ],
                // 'selectors_dictionary' => [
                //     'top-left'      => 'top:var(--bdt-vertical-offset, 32px); left:var(--bdt-horizontal-offset) !important; bottom:unset; right:unset !important;',
                //     'top-right'     => 'top:var(--bdt-vertical-offset, 32px); right:var(--bdt-horizontal-offset) !important; bottom:unset; left:unset !important;',
                //     'bottom-left'   => 'bottom:var(--bdt-vertical-offset, 32px); left:var(--bdt-horizontal-offset, 32px); top:unset; right:unset;',
                //     'bottom-right'  => 'bottom:var(--bdt-vertical-offset, 32px); right:var(--bdt-horizontal-offset, 32px); top:unset; left:unset;',
                // ],
                // 'selectors'            => [
                //     '.darkmode-toggle, .darkmode-layer' => '{{VALUE}}',
                // ],
                'frontend_available' => true,
                'render_type'        => 'none'
            ]
        );

        $this->add_responsive_control(
            'icon_horizontal_offset',
            [
                'label' => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '.darkmode-toggle, .darkmode-layer' => '--bdt-horizontal-offset: {{SIZE}}{{UNIT}};',
                ],
                // 'render_type'        => 'none',
            ]
        );

        $this->add_responsive_control(
            'icon_vertical_offset',
            [
                'label' => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '.darkmode-toggle, .darkmode-layer' => '--bdt-vertical-offset: {{SIZE}}{{UNIT}};',
                ],
                // 'render_type'        => 'none'
            ]
        );

        $this->add_control(
            'time',
            [
                'label' => esc_html__('Animation Time (ms)', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                //'size_units' => 's',
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1500,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
                'frontend_available' => true,
                'render_type'        => 'none'
            ]
        );

        $this->add_control(
            'ignore_element',
            [
                'label'       => esc_html__('Ignore Elements', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => '.my-image, .my-widget',
                'dynamic'     => [
                    'active' => true,
                ],
                'frontend_available' => true,
                'render_type'        => 'none'
            ]
        );

        $this->add_control(
            'ignore_element_notes',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Note: add Class ID of elements to exempt them from dark mode effect, i.e. images, special background, etc.', 'bdthemes-element-pack'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',

            ]
        );

        $this->add_control(
            'saveInCookies',
            [
                'label'              => esc_html__('Save User Action', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::SWITCHER,
                'return_value'       => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'save_cookies_notes',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Note: saves the last user action on the browser, i.e. loads the last browser condition whether dark mode was on/off. Cookie is exempted on Elementor Editor.', 'bdthemes-element-pack'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',

            ]
        );

        $this->add_control(
            'autoMatchOsTheme',
            [
                'label'              => esc_html__('Auto Match On Theme', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SWITCHER,
                'return_value'       => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Dark Mode', 'bdthemes-color-mode'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_dark_mode_style');

        $this->start_controls_tab(
            'tab_day_mode_normal',
            [
                'label' => esc_html__('Day Mode', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'default_background',
            [
                'label'              => esc_html__('Background', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::COLOR,
                'default'            => '#fff',
                'frontend_available' => true,
            ]
        );

        // $this->add_control(
        //     'icon_color_day',
        //     [
        //         'label' => esc_html__('Icon Color', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::COLOR,
        //         'default' => '#fff',
        //         'selectors' => [
        //             '.darkmode-toggle i' => 'color: {{VALUE}}',
        //         ],
        //     ]
        // );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dark_mode_normal',
            [
                'label' => esc_html__('Dark Mode', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'mix_color',
            [
                'label'              => esc_html__('Content Mix Color', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::COLOR,
                'default'            => '#fff',
                'frontend_available' => true,
            ]
        );

        // $this->add_control(
        //     'icon_color_dark',
        //     [
        //         'label' => esc_html__('Icon Color', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::COLOR,
        //         'default' => '#000',
        //         'selectors' => [
        //             '.darkmode-toggle.darkmode-toggle--white i' => 'color: {{VALUE}}',
        //         ],
        //     ]
        // );


        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->end_controls_section();

        $this->start_controls_section(
            'toggle_button_style',
            [
                'label' => esc_html__('Toggle Button', 'bdthemes-color-mode'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => esc_html__('Icon Size', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 18,
                ],
                'selectors' => [
                    '.darkmode-toggle' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'icon_button_width',
            [
                'label' => esc_html__('Toggle Size', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 54,
                ],
                'selectors' => [
                    '.darkmode-toggle, .darkmode-layer:not(.darkmode-layer--expanded)' => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'icon_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '.darkmode-toggle',
            ]
        );

        $this->add_control(
            'icon_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '.darkmode-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_toggle_button');

        $this->start_controls_tab(
            'toggle_button_day_mode',
            [
                'label' => esc_html__('Day Mode', 'bdthemes-element-pack'),
            ]
        );



        // $this->add_control(
        //     'icon_color_day',
        //     [
        //         'label' => esc_html__('Icon Color', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::COLOR,
        //         'default' => '#fff',
        //         'selectors' => [
        //             '.darkmode-toggle i' => 'color: {{VALUE}}',
        //         ],
        //     ]
        // );

        $this->add_control(
            'day_mode_icon_background',
            [
                'label'     => esc_html__('Icon Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#100f2c',
                'selectors' => [
                    '.darkmode-toggle' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'toggle_button_dark_mode',
            [
                'label' => esc_html__('Dark Mode', 'bdthemes-element-pack'),
            ]
        );


        // $this->add_control(
        //     'icon_color_dark',
        //     [
        //         'label' => esc_html__('Icon Color', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::COLOR,
        //         'default' => '#000',
        //         'selectors' => [
        //             '.darkmode-toggle.darkmode-toggle--white i' => 'color: {{VALUE}}',
        //         ],
        //     ]
        // );

        $this->add_control(
            'dark_mode_icon_background',
            [
                'label'     => esc_html__('Icon Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '.darkmode-toggle.darkmode-toggle--white' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function render() {

        $settings = $this->get_settings_for_display();

?>


    <?php
    }
}
