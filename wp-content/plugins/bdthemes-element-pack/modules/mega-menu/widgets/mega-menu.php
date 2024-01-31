<?php

namespace ElementPack\Modules\MegaMenu\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ElementPack\Includes\MegaMenu\Mega_Menu_Walker;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Mega_menu extends Module_Base {

    public function show_in_panel() {
        return get_post_type() !== 'ep_megamenu_content';
    }

    public function get_name() {
        return 'bdt-mega-menu';
    }

    public function get_title() {
        return esc_html__('Mega Menu', 'bdthemes-element-pack');
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['mega', 'menu', 'navigation', 'vertical'];
    }

    public function get_icon() {
        return 'bdt-wi-mega-menu bdt-new';
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-mega-menu', 'ep-font'];
        }
    }

    public function get_script_depends() {
        return ['ep-mega-menu', 'bdt-uikit', 'fontawesome'];
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/ZOBLWIZvGLs';
    }

    public function register_controls() {
        $this->register_controls_layout();
        $this->register_layout_controls_animation();
        $this->register_vertical_menu_toggle_style();
        $this->register_style_controls_vertical_menu();
        $this->register_menu_item_tabs();
        $this->register_style_controls_submenu();
        $this->register_style_controls_toggle();
        $this->register_style_controls_badge();
    }

    protected function register_controls_layout() {
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'navbar',
            [
                'label'   => esc_html__('Select Menu', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => element_pack_get_menu(),
                'default' => 0,
            ]
        );
        $this->add_control(
            'ep_megamenu_direction',
            [
                'label'              => esc_html__('Menu Directioin', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    'horizontal' => esc_html__('Horizontal', 'bdthemes-element-pack'),
                    'vertical'   => esc_html__('Vertical', 'bdthemes-element-pack'),
                ],
                'default'            => 'horizontal',
                'dynamic'            => ['active' => true],
                'frontend_available' => true,
                'render_type'        => 'template',
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_header',
            [
                'label'         => esc_html__('Display Menu as a Toggle', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('No', 'bdthemes-element-pack'),
                'return_value'  => 'yes',
                'default'       => 'no',
                'separator'     => 'before',
                'condition' => [
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_bar_icon',
            [
                'label'         => esc_html__('Show Bar Icon', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('No', 'bdthemes-element-pack'),
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical',
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_show_text',
            [
                'label'         => esc_html__('Show Text', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('No', 'bdthemes-element-pack'),
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical',
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_text',
            [
                'label'         => esc_html__('Text', 'bdthemes-element-pack'),
                'label_block'   => true,
                'type'          => Controls_Manager::TEXT,
                'default'       => esc_html__('Browse Categories', 'bdthemes-element-pack'),
                'placeholder'   => esc_html__('Browse Categories', 'bdthemes-element-pack'),
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical',
                    'ep_megamenu_vertical_dropdown_show_text' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_arrows',
            [
                'label'         => esc_html__('Show Arrows', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('No', 'bdthemes-element-pack'),
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical',
                ]
            ]
        );

        $this->add_control(
            'ep_megamenu_vertical_dropdown_offset',
            [
                'label'         => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::NUMBER,
                'default'       => 10,
                'frontend_available' => true,
                'render_type' => 'none',
                'separator' => 'before',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_animation_type',
            [
                'label'   => esc_html__('Animation Type', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => $this->megamenu_animation_type(),
                'default' => 'bdt-animation-fade',
                'frontend_available' => true,
                'render_type' => 'none',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_animate_out',
            [
                'label'         => esc_html__('Animate Out', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'render_type' => 'none',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_animation_duration',
            [
                'label'   => esc_html__('Duration', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 200,
                'frontend_available' => true,
                'render_type' => 'none',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );
        $this->add_control(
            'ep_megamenu_vertical_dropdown_mode',
            [
                'label'      => esc_html__('Trigger Type', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SELECT,
                'options'    => [
                    'click'  => esc_html__('Click', 'bdthemes-element-pack'),
                    'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
                ],
                'separator' => 'after',
                'default'    => 'click',
                'frontend_available' => true,
                'render_type' => 'none',
                'condition'     => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );

        $this->add_responsive_control(
            'default_menu_alignment',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start'   => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'flex-end'  => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ]
                ],
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu.ep-megamenu-horizontal .bdt-navbar-nav' => 'justify-content: {{VALUE}}',
                ],
                'condition' => [
                    'ep_megamenu_direction' => 'horizontal'
                ]
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_hamburger_menu',
            [
                'label' => esc_html__('Hamburger Menu', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        // $this->add_control(
        //     'show_hamburger_menu',
        //     [
        //         'label'     => esc_html__('Start From', 'bdthemes-element-pack'),
        //         'type'      => Controls_Manager::SELECT,
        //         'options'   => [
        //             's' => esc_html__('Mobile', 'bdthemes-element-pack'),
        //             'm' => esc_html__('Tablet', 'bdthemes-element-pack'),
        //             'none' => esc_html__('None', 'bdthemes-element-pack'),
        //         ],
        //         'default'   => 's',
        //         'separator' => 'before',
        //     ]
        // );
        $this->add_control(
            'show_hamburger_menu',
            [
                'label' => esc_html__('Breakpoint', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 's',
                'options' => [
                    's' => esc_html__('Mobile (> 767px)', 'bdthemes-element-pack'),
                    'm' => esc_html__('Tablet (> 1024px)', 'bdthemes-element-pack'),
                    'none' => esc_html__('None', 'bdthemes-element-pack'),
                ],
                'prefix_class' => 'bdt-mega-menu-hamburger-',
                'separator' => 'before',

            ]
        );
        $this->add_responsive_control(
            'hamburger_menu_alignment',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start'   => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'flex-end'  => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ]
                ],
                'default'   => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile' => 'justify-content: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function register_style_controls_vertical_menu() {
        $this->start_controls_section(
            'section_style_vertical_menu',
            [
                'label' => esc_html__('Vertical Menu', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'  => [
                    'ep_megamenu_direction' => 'vertical',
                ],
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_width',
            [
                'label'      => esc_html__('Width', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range'      => [
                    'px' => [
                        'min'  => 200,
                        'max'  => 600,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu' => '--ep-megamenu-vertical-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'vertical_menu_background',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-megamenu-vertical .bdt-navbar-nav,
                                {{WRAPPER}} .ep-megamenu .ep-default-submenu-panel',
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu-vertical .bdt-navbar-nav,
                    {{WRAPPER}} .ep-megamenu .ep-default-submenu-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'vertical_menu_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu-vertical .bdt-navbar-nav, {{WRAPPER}} .ep-megamenu .ep-default-submenu-panel',
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu-vertical .bdt-navbar-nav, {{WRAPPER}} .ep-megamenu .ep-default-submenu-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function register_layout_controls_animation() {
        $this->start_controls_section(
            'section_megamenu_layout_animation',
            [
                'label' => esc_html__('Dropdown Settings', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'ep_megamenu_offset',
            [
                'label'         => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'frontend_available' => true,
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 300,
                        'step'  => 1,
                    ]
                ],
                'devices' => ['desktop', 'mobile'],
                'desktop_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_control(
            'ep_megamenu_animation_type',
            [
                'label'   => esc_html__('Animation Type', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => $this->megamenu_animation_type(),
                'default' => 'bdt-animation-fade',
                'frontend_available' => true,
                'render_type' => 'none',
            ]
        );
        $this->add_control(
            'ep_megamenu_animate_out',
            [
                'label'         => esc_html__('Animate Out', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'render_type' => 'none',
            ]
        );
        $this->add_control(
            'ep_megamenu_animation_duration',
            [
                'label'   => esc_html__('Duration', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 200,
                'frontend_available' => true,
                'render_type' => 'none',
            ]
        );
        $this->add_control(
            'ep_megamenu_mode',
            [
                'label'      => esc_html__('Trigger Type (Desktop)', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SELECT,
                'options'    => [
                    'click'  => esc_html__('Click', 'bdthemes-element-pack'),
                    'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
                ],
                'default'    => 'hover',
                'frontend_available' => true,
                'render_type' => 'none',
            ]
        );
        $this->end_controls_section();
    }

    protected function register_vertical_menu_toggle_style() {
        $this->start_controls_section(
            'section_style_vertical_menu_toggle',
            [
                'label' => esc_html__('Toggle Button', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ep_megamenu_vertical_header' => 'yes',
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );
        $this->add_control(
            'vertical_menu_toggle_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'vertical_menu_toggle_background',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn',
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_toggle_padding',
            [
                'label'                 => esc_html__('Paddiing', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_toggle_margin',
            [
                'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_toggle_spacing',
            [
                'label'         => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu-vertical-toggle-btn span' => 'margin: 0px {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'vertical_menu_toggle_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn',
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_toggle_radius',
            [
                'label'                 => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'vertical_menu_toggle_typography',
                'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu .ep-megamenu-vertical-toggle-btn',
                'separator' => 'after'
            ]
        );

        $this->add_responsive_control(
            'vertical_menu_toggle_bar_size',
            [
                'label'         => esc_html__('Bar Size', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu-vertical-toggle-btn svg' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => [
                    'ep_megamenu_vertical_dropdown_bar_icon' => 'yes'
                ]
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_toggle_arrow_size',
            [
                'label'         => esc_html__('Arrow Size', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu-vertical-toggle-btn i' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ep_megamenu_vertical_dropdown_arrows' => 'yes'
                ]
            ]
        );
        $this->end_controls_section();
    }

    protected function register_menu_item_tabs() {

        $this->start_controls_section(
            'section_content_style_items',
            [
                'label' => esc_html__('Menu Items', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'tab_menu_item_style'
        );
        $this->start_controls_tab(
            'menu_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_responsive_control(
            'menu_text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a,  #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_arrow_color',
            [
                'label'     => esc_html__('Arrow Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav li .bdt-megamenu-indicator,  #ep-megamenu-{{ID}}-virtual.bdt-accordion li .bdt-megamenu-indicator' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'menu_background_color',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a,
                #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link',
            ]
        );

        $this->add_responsive_control(
            'menu_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a,
                    #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'menu_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a,
                    #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_gap',
            [
                'label'         => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav' => 'grid-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-accordion' => 'grid-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'menu_border',
                'selector' => '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a,
                #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link',
            ]
        );

        $this->add_responsive_control(
            'menu_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a,
                    #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_responsive_control(
            'menu_arrow_spacing',
            [
                'label'      => esc_html__('Arrow Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                        'step' => 1,
                    ],
                ],
                'separator' => 'before',
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu.ep-megamenu-horizontal .bdt-navbar-nav .bdt-megamenu-indicator' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '#ep-megamenu-{{ID}}-virtual.bdt-accordion .bdt-accordion-title .bdt-megamenu-indicator' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ep_megamenu_direction' => 'horizontal'
                ]
            ]
        );
        $this->add_responsive_control(
            'vertical_menu_arrow_spacing',
            [
                'label'      => esc_html__('Arrow Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                        'step' => 1,
                    ],
                ],
                'separator' => 'before',
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu.ep-megamenu-vertical li .bdt-megamenu-indicator' => 'right: {{SIZE}}{{UNIT}};',
                    '#ep-megamenu-{{ID}}-virtual.bdt-accordion .bdt-accordion-title .bdt-megamenu-indicator' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );

        $this->add_control(
            'ep_megamenu_dropdown_arrows',
            [
                'label'         => esc_html__('Hide Dropdown Arrows', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('No', 'bdthemes-element-pack'),
                'return_value'  => 'none',
                'default'       => 'block',
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .bdt-navbar-nav .bdt-megamenu-indicator' => 'display:{{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'vertical_menu_alignment',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start'   => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'flex-end'  => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ]
                ],
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu.ep-megamenu-vertical .bdt-navbar-nav li .ep-menu-nav-link' => 'justify-content: {{VALUE}}',
                ],
                'condition' => [
                    'ep_megamenu_direction' => 'vertical'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'menu_item_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-megamenu .bdt-navbar-nav > li > a,
                #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'menu_item_hover',
            [
                'label' => esc_html__('Hover/Active', 'bdthemes-element-pack'),
            ]
        );
        $this->add_responsive_control(
            'menu_text_h_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .bdt-navbar-nav > li > a:hover,
                    {{WRAPPER}} .ep-megamenu .bdt-navbar-nav > li > a.active,
                    #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'menu_h_background_color',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'default'   => '',
                'selector'  => '{{WRAPPER}} .ep-megamenu .megamenu-header-default .bdt-navbar-nav > li > a:hover,
                                {{WRAPPER}} .ep-megamenu .bdt-navbar-nav > li > a.active,
                                #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'menu_h_border',
                'selector' => '{{WRAPPER}} .ep-megamenu .bdt-navbar-nav > li > a:hover,
                                {{WRAPPER}} .ep-megamenu .bdt-navbar-nav > li > a.active,
                                #ep-megamenu-{{ID}}-virtual.bdt-accordion li a.ep-menu-nav-link:hover',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_controls_submenu() {
        $this->start_controls_section(
            'style_tab_submenu_item',
            [
                'label' => esc_html__('Dropdown', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'submenu_active_hover_tabs'
        );
        $this->start_controls_tab(
            'submenu_normal_tab',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'menu_item_background',
                'label'    => esc_html__('Item background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-megamenu .menu-item-has-children .bdt-drop,
                               {{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop,
                               #ep-megamenu-{{ID}}-virtual.bdt-accordion',
            ]
        );

        $this->add_responsive_control(
            'menu_item_dropdown_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'separator' => 'before',
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop, #ep-megamenu-{{ID}}-virtual.bdt-accordion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'menu_item_dropdown_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop, #ep-megamenu-{{ID}}-virtual.bdt-accordion' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'sub_menu_item_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop, #ep-megamenu-{{ID}}-virtual.bdt-accordion',
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'menu_item_dropdown_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop, #ep-megamenu-{{ID}}-virtual.bdt-accordion' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'sub_menu_item_shadow',
                'label'     => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop, #ep-megamenu-{{ID}}-virtual.bdt-accordion',
            ]
        );
        $this->add_responsive_control(
            'ep_megamenu_full_width_offset',
            [
                'label'         => esc_html__('Full Width Offset (px)', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 400,
                        'step'  => 1,
                    ]
                ],
                'default'       => [
                    'unit'      => 'px',
                    'size'      => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .ep-megamenu-panel' => '--bdt-position-viewport-offset: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => [
                    'ep_megamenu_direction' => 'horizontal'
                ]
            ]
        );

        $this->add_control(
            'submenu_wp_default_subitem',
            [
                'label'     => esc_html__('Classic Submenu', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'submenu_item_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .menu-item-has-children .bdt-drop li a,
                    #ep-megamenu-{{ID}}-virtual .bdt-accordion-content li a ' => 'color: {{VALUE}}',
                ]
            ]
        );
        $this->add_responsive_control(
            'submenu_item_bg_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .menu-item-has-children .bdt-drop li a,
                    #ep-megamenu-{{ID}}-virtual .bdt-accordion-content li a' => 'background-color: {{VALUE}}',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'submene_item_bg_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu .menu-item-has-children .bdt-drop li a,
                #ep-megamenu-{{ID}}-virtual .bdt-accordion-content li a',
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'menu_item_submenu_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-megamenu .menu-item-has-children .bdt-drop li a,
                #ep-megamenu-{{ID}}-virtual .bdt-accordion-content li a',
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'submenu_hover_tab',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'menu_item_hover_background',
                'label'    => esc_html__('Item background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-megamenu .menu-item-has-children .bdt-drop,:hover
                               {{WRAPPER}} .ep-megamenu .ep-megamenu-panel.bdt-drop:hover,
                               #ep-megamenu-{{ID}}-virtual.bdt-accordion:hover',
            ]
        );

        $this->add_control(
            'submenu_wp_hover_default_subitem',
            [
                'label'     => esc_html__('Classic Submenu', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'item_text_color_hover',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .menu-item-has-children .ep-megamenu-panel > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ep-megamenu .menu-item-has-children .ep-megamenu-panel > li:hover > a'  => 'color: {{VALUE}}',
                    '#ep-megamenu-{{ID}}-virtual .bdt-accordion-content li:hover a'  => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'item_text_hover_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .menu-item-has-children .ep-megamenu-panel > li > a:active' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ep-megamenu .menu-item-has-children .ep-megamenu-panel > li:hover > a'  => 'background-color: {{VALUE}}',
                    '#ep-megamenu-{{ID}}-virtual .bdt-accordion-content li:hover a'  => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_controls_toggle() {
        $this->start_controls_section(
            'section_style_hamburger_menu',
            [
                'label' => esc_html__('Hamburger Menu', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'hamburger_menu_toggle_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile .bdt-navbar-toggle svg' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hamburger_menu_toggle_background',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile .bdt-navbar-toggle',
            ]
        );
        $this->add_responsive_control(
            'hamburger_menu_toggle_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile .bdt-navbar-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'hamburger_menu_toggle_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile .bdt-navbar-toggle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'hamburger_menu_toggle_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile .bdt-navbar-toggle',
            ]
        );

        $this->add_responsive_control(
            'hamburger_menu_toggle_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-megamenu .megamenu-header-mobile .bdt-navbar-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function register_style_controls_badge() {
        $this->start_controls_section(
            'section_style_badge',
            [
                'label' => esc_html__('Badge', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'badge_position_x',
            [
                'label'     => esc_html__('Offset (X)', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'     => [
                    'px'    => [
                        'min'   => -200,
                        'max'   => 200,
                        'step'  => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-badge-label, #ep-megamenu-{{ID}}-virtual .ep-badge-label' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'badge_position',
            [
                'label'     => esc_html__('Offset (Y)', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'     => [
                    'px'    => [
                        'min'   => -200,
                        'max'   => 200,
                        'step'  => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-badge-label, #ep-megamenu-{{ID}}-virtual .ep-badge-label' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'badge_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-badge-label, #ep-megamenu-{{ID}}-virtual .ep-badge-label',
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'badge_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-badge-label, #ep-megamenu-{{ID}}-virtual .ep-badge-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'badge_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-badge-label, #ep-megamenu-{{ID}}-virtual .ep-badge-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'badge_typography',
                'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-badge-label, #ep-megamenu-{{ID}}-virtual .ep-badge-label',
            ]
        );

        $this->end_controls_section();
    }

    protected function megamenu_animation_type() {
        $animation_type = [
            'bdt-animation-fade'                => esc_html__('Fade', 'bdthemes-element-pack'),
            'bdt-animation-scale-up'            => esc_html__('Scale UP', 'bdthemes-element-pack'),
            'bdt-animation-slide-top'           => esc_html__('Slide Top', 'bdthemes-element-pack'),
            'bdt-animation-slide-bottom'        => esc_html__('Slide Bottom', 'bdthemes-element-pack'),
            'bdt-animation-slide-left'          => esc_html__('Slide Left', 'bdthemes-element-pack'),
            'bdt-animation-slide-right'         => esc_html__('Slide Right', 'bdthemes-element-pack'),
            'bdt-animation-slide-top-small'     => esc_html__('Slide Top Small', 'bdthemes-element-pack'),
            'bdt-animation-slide-bottom-small'  => esc_html__('Slide Bottom Small', 'bdthemes-element-pack'),
            'bdt-animation-slide-left-small'    => esc_html__('Slide Left Small', 'bdthemes-element-pack'),
            'bdt-animation-slide-right-small'   => esc_html__('Slide Right Small', 'bdthemes-element-pack'),
            'bdt-animation-slide-top-medium'    => esc_html__('Slide Top Medium', 'bdthemes-element-pack'),
            'bdt-animation-slide-bottom-medium' => esc_html__('Slide Bottom Medium', 'bdthemes-element-pack'),
            'bdt-animation-slide-left-medium'   => esc_html__('Slide Left Medium', 'bdthemes-element-pack'),
            'bdt-animation-slide-right-medium'  => esc_html__('Slide Right Medium', 'bdthemes-element-pack'),
            'bdt-animation-kenburns'            => esc_html__('Kenburns', 'bdthemes-element-pack'),
            'bdt-animation-shake'               => esc_html__('Shake', 'bdthemes-element-pack'),
            'reveal-top'                        => esc_html__('Reveal Top', 'bdthemes-element-pack'),
            'reveal-bottom'                     => esc_html__('Reveal Bottom', 'bdthemes-element-pack'),
            'reveal-left'                       => esc_html__('Reveal Left', 'bdthemes-element-pack'),
            'reveal-right'                      => esc_html__('Reveal Right', 'bdthemes-element-pack'),
        ];
        return $animation_type;
    }



    public function render() {
        $mega_menu               = element_pack_option('mega-menu', 'element_pack_other_settings', 'off');

        if ('on' === $mega_menu) {
            $rtl = is_rtl();
            $settings = $this->get_settings_for_display();
            if (!$settings['navbar']) {
                element_pack_alert(__('Please select a Menu from layout setting!', 'bdthemes-element-pack'));
            }

            $this->add_render_attribute('vertical-dropmenu', ['class' => ['ep-megamenu-vertical-dropdown']], null, true);

            $this->add_render_attribute(
                'ep-megamenu',
                [
                    'class' => ['ep-megamenu', 'initialized', 'ep-megamenu-' . $settings['ep_megamenu_direction'] . '']
                ],
                null,
                true
            );



            if ($rtl) {
                $this->add_render_attribute('ep-megamenu', ['data-is-rtl' => $rtl,], null, true);
            }
?>
            <div <?php $this->print_render_attribute_string('ep-megamenu'); ?>>
                <div class="megamenu-header-default">
                    <?php if (('yes' === $settings['ep_megamenu_vertical_header']) && ('vertical' === $settings['ep_megamenu_direction'])) { ?>
                        <button class="ep-megamenu-vertical-toggle-btn" type="button">
                            <?php
                            if ('yes' === $settings['ep_megamenu_vertical_dropdown_bar_icon']) {
                            ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                                </svg>
                            <?php
                            }; ?>

                            <?php if ('yes' === $settings['ep_megamenu_vertical_dropdown_show_text']) { ?>
                                <span> <?php esc_html_e($settings['ep_megamenu_vertical_dropdown_text'], 'bdthemes-element-pack'); ?></span>
                            <?php }; ?>

                            <?php
                            if ('yes' === $settings['ep_megamenu_vertical_dropdown_arrows']) { ?>
                                <i class="ep-icon-arrow-down-3"></i>
                            <?php
                            }; ?>
                        </button>
                        <div <?php $this->print_render_attribute_string('vertical-dropmenu'); ?>>
                            <?php $this->ep_megamenu_dynamic_content_default(); ?>
                        </div>
                    <?php
                    } else {
                        $this->ep_megamenu_dynamic_content_default();
                    } ?>

                </div>
                <div class="megamenu-header-mobile" style="display: none;">
                    <a class="bdt-navbar-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                        </svg>
                    </a>
                </div>


            </div>
<?php
        } else {
            element_pack_alert(__('Please Enable Mega Menu Modules from Element Pack pro > other setting > Mega Menu', 'bdthemes-element-pack'));
        }
    }

    public function ep_megamenu_dynamic_content_default() {
        $settings = $this->get_settings_for_display();
        $nav_menu = !empty($settings['navbar']) ? wp_get_nav_menu_object($settings['navbar']) : false;
        $id       = $this->get_id();
        if (!$nav_menu) {
            return;
        }
        $nav_menu_args = [
            'fallback_cb'        => false,
            'container'          => false,
            'menu_id'            => 'ep-megamenu-' . $id . '',
            'menu_class'         => 'bdt-navbar-nav',
            'theme_location'     => 'default_navmenu',
            'menu'               => $nav_menu,
            'echo'               => true,
            'depth'              => 0,
            'walker'             => new Mega_Menu_Walker,
        ];
        wp_nav_menu(apply_filters('widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings));
    }
}
