<?php

namespace ElementPack\Modules\SubMenu\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Modules\SubMenu\ep_sub_menu_menu_walker;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Sub_Menu extends Module_Base {

    public function get_name() {
        return 'bdt-sub-menu';
    }

    public function get_title() {
        return __('Sub Menu', 'bdthemes-element-pack');
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['sub', 'menu', 'navigation', 'mega'];
    }

    public function get_icon() {
        return 'bdt-wi-sub-menu bdt-new';
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-sub-menu'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/YuwB964kQMw';
    }

    public function register_controls() {
        $this->register_controls_layout();
        $this->render_layout_static_menus();
        $this->register_style_controls_heading();
        $this->register_style_controls_items();
        $this->register_style_controls_title();
        $this->register_style_controls_sub_title();
        $this->register_style_controls_icons();
        $this->register_style_controls_badge();
        $this->register_style_controls_arrows();
        $this->register_layout_controls_additional();
    }

    protected function register_controls_layout() {
        $this->start_controls_section(
            'section_static_menu',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'submenu_style',
            [
                'label'   => __('Style', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '1' => __('Style 1', 'bdthemes-element-pack'),
                    '2' => __('Style 2', 'bdthemes-element-pack'),
                    '3' => __('Style 3', 'bdthemes-element-pack'),
                    '4' => __('Style 4', 'bdthemes-element-pack'),
                ],
                'default' => '1',
            ]
        );
        $this->add_responsive_control(
            'submenu_columns',
            [
                'label'     => __('Columns', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'default'   => '2',
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-sub-menu-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'submenu_columns_gap',
            [
                'label'     => __('Columns Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-sub-menu-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'submenu_rows_gap',
            [
                'label'     => __('Rows Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-sub-menu-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'show_submenu_heading',
            [
                'label'        => __('Show Heading', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            ]
        );
        $this->add_control(
            'submenu_header_title',
            [
                'label'       => __('Heading Text', 'bdthemes-element-pack'),
                // 'label_block' => true,
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Advanced Sub Menu', 'bdthemes-element-pack'),
                'placeholder' => __('Type your sub menu heading title here', 'bdthemes-element-pack'),
                'condition'   => [
                    'show_submenu_heading' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'submenu_header_alignment',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading' => 'text-align:{{VALUE}}',
                ],
                'condition' => [
                    'show_submenu_heading' => 'yes',
                ],
                'default'   => 'left',
                'toggle'    => false,

            ]
        );
        $this->add_control(
            'show_sub_menu_sub_title',
            [
                'label'        => __('Show Sub title', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
                'condition'    => [
                    'dynamic_menu!' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'show_sub_menu_arrows',
            [
                'label'        => __('Show Arrows', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
                'condition'    => [
                    'dynamic_menu!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dynamic_menu',
            [
                'label'     => esc_html__('Dynamic Menu', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'navbar',
            [
                'label'     => esc_html__('Select Menu', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'options'   => element_pack_get_menu(),
                'default'   => 0,
                'condition' => ['dynamic_menu' => 'yes'],
            ]
        );
        $this->end_controls_section();
    }

    protected function render_layout_static_menus() {
        $this->start_controls_section(
            'section_static_menus',
            [
                'label'     => __('Static Menus', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'dynamic_menu' => '',
                ],
            ]
        );
        $repeater = new Repeater();
        $repeater->start_controls_tabs(
            'section_static_menus_tabs'
        );
        $repeater->start_controls_tab(
            'section_static_menus_tabs_layout',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
            ]
        );
        $repeater->add_control(
            'menu_title',
            [
                'label'       => __('Menu Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'menu_sub_title',
            [
                'label'       => __('Menu Sub Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'menu_badge',
            [
                'label'       => __('Menu Badge', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'menu_link',
            [
                'label'       => __('Link', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => ['active' => true],
                'default'     => [
                    'url' => '#',
                ],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'menu_icon',
            [
                'label'       => __('Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );
        $repeater->end_controls_tab();
        $repeater->start_controls_tab(
            'section_static_menus_tabs_style',
            [
                'label' => __('Style', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'menu_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}} .ep-icon-inner'          => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}} .ep-icon-inner svg'      => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}} .ep-icon-inner svg path' => 'fill: {{VALUE}} !important;',
                ],
            ]
        );
        $repeater->add_control(
            'menu_icon_background',
            [
                'label'     => esc_html__('Icon Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}} .ep-icon-inner' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );
        $repeater->add_control(
            'menu_badge_color',
            [
                'label'     => esc_html__('Badge Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}} .ep-badge' => 'color: {{VALUE}} !important;',
                ],
                'separator' => 'before'
            ]
        );
        $repeater->add_control(
            'menu_badge_background',
            [
                'label'     => esc_html__('Badge Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}} .ep-badge' => 'background: {{VALUE}} !important;',
                ],
                'separator' => 'after'
            ]
        );

        $repeater->add_control(
            'repeater_menu_heading_hover',
            [
                'label'     => __('HOVER', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $repeater->add_control(
            'menu_icon_h_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap {{CURRENT_ITEM}}:hover .ep-icon-inner'          => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap {{CURRENT_ITEM}}:hover .ep-icon-inner svg'      => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap {{CURRENT_ITEM}}:hover .ep-icon-inner svg path' => 'fill: {{VALUE}} !important;',
                ],
            ]
        );
        $repeater->add_control(
            'menu_icon_h_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap {{CURRENT_ITEM}}:hover .ep-icon-inner' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );
        $repeater->add_control(
            'menu_badge_h_color',
            [
                'label'     => esc_html__('Badge Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}}:hover .ep-badge' => 'color: {{VALUE}} !important;',
                ],
                'separator' => 'before'
            ]
        );
        $repeater->add_control(
            'menu_badge_h_background',
            [
                'label'     => esc_html__('Badge Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  {{CURRENT_ITEM}}:hover .ep-badge' => 'background: {{VALUE}} !important;',
                ],
            ]
        );

        $repeater->add_control(
            'menu_arrow_color',
            [
                'label'     => __('Arrow Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap {{CURRENT_ITEM}} .ep-hover-icon' => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();

        $this->add_control(
            'menus',
            [
                'label'       => __('Menu Items', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'condition'   => ['dynamic_menu' => ''],
                'separator'   => 'before',
                'default'     => [
                    [
                        'menu_title'     => __('About', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('Gallery 01', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('About', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('Gallery 01', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('About', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('Gallery 01', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('About', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('Gallery 01', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('About', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                    [
                        'menu_title'     => __('Gallery 01', 'bdthemes-element-pack'),
                        'menu_sub_title' => __('Trending Design inspire to you'),
                        'menu_link'      => '#',
                    ],
                ],
                'title_field' => '{{{ elementor.helpers.renderIcon( this, menu_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} <# print(menu_title)#>',
            ]
        );
        $this->end_controls_section();
    }

    protected function register_style_controls_heading() {
        $this->start_controls_section(
            'section_heading_style',
            [
                'label'     => __('Heading', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_submenu_heading' => 'yes',
                ],
            ]
        );
        $this->start_controls_tabs(
            'tab_submenu_heading_style'
        );
        $this->start_controls_tab(
            'submenu_heading_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_heading_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_heading_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading',
            ]
        );

        $this->add_responsive_control(
            'submenu_heading_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'submenu_heading_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submenu_heading_border',
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading',
            ]
        );

        $this->add_control(
            'submenu_heading_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submenu_heading_typography',
                'label'    => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'submenu_heading_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_heading_h_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_heading_h_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading:hover',
            ]
        );

        $this->add_control(
            'submenu_heading_h_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-heading:hover' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'submenu_heading_border_border!' => '',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_title() {
        $this->start_controls_section(
            'section_submenu_title_style',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'tab_submenu_title_style'
        );
        $this->start_controls_tab(
            'submenu_title_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_title_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_title_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title',
            ]
        );

        $this->add_responsive_control(
            'submenu_title_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'submenu_title_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // $this->add_group_control(

        //     Group_Control_Border::get_type(),

        //     [

        //         'name'     => 'submenu_title_border',

        //         'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title',

        //     ]
        // );

        $this->add_control(
            'submenu_title_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submenu_title_typography',
                'label'    => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'submenu_title_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_title_h_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap  .ep-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'submenu_title_h_background_color',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // $this->add_group_control(

        //     Group_Control_Border::get_type(),

        //     [

        //         'name'     => 'submenu_title_hover_border',

        //         'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title:hover',

        //     ]
        // );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_controls_items() {
        $this->start_controls_section(
            'section_style_submenu_items',
            [
                'label' => esc_html__('Items', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('item_tabs');

        $this->start_controls_tab(
            'submenu_items_tab_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_items_background',
                'label'    => esc_html__('Item background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item',
            ]
        );

        $this->add_responsive_control(
            'submenu_items_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submenu_items_border',
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item',
            ]
        );

        $this->add_responsive_control(
            'submenu_items_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'submenu_items_shadow',
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'submenu_items_tab_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_items_h_background',
                'label'    => esc_html__('Item background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover',
            ]
        );
        $this->add_control(
            'submenu_items_h_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'submenu_items_border_border!' => [
                        '',
                        'none',
                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'submenu_item_hover_shadow',
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function register_style_controls_title() {
        $this->start_controls_section(
            'section_style_submenu_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'section_style_title_tabs'
        );
        $this->start_controls_tab(
            'submenu_title_tab_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_title_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title',
            ]
        );
        $this->add_responsive_control(
            'submenu_title_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'submenu_title_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submenu_title_border',
                'label'    => __('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title',
            ]
        );
        $this->add_responsive_control(
            'submenu_title_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'separator' => 'after',
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submenu_title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-title',
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'submenu_title_tab_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_title_h_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_title_hover_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-title',
            ]
        );

        // $this->add_group_control(

        //     Group_Control_Border::get_type(),

        //     [

        //         'name'      => 'submenu_title_hover_border',

        //         'label'     => __('Border', 'bdthemes-element-pack'),

        //         'selector'  => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-title',

        //         'conditon' => [

        //             ''

        //         ]

        //     ]
        // );
        $this->add_control(
            'submenu_title_hover_border',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-title' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'submenu_title_border_border!' => '',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function register_style_controls_sub_title() {
        $this->start_controls_section(
            'section_style_submenu_sub_title',
            [
                'label'     => esc_html__('Sub Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_sub_menu_sub_title' => 'yes',
                    'dynamic_menu!' => 'yes'
                ],
            ]
        );


        $this->start_controls_tabs(
            'tabs_style_submenu_sub_title'
        );
        $this->start_controls_tab(
            'tab_submenu_sub_title_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'submenu_sub_title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-sub-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'submenu_sub_title_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submenu_sub_title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-sub-title',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_submenu_sub_title_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_sub_title_h_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_controls_icons() {
        $this->start_controls_section(
            'section_style_submenu_icon',
            [
                'label' => esc_html__('Icons', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'dynamic_menu!' => 'yes'
                ]
            ]
        );
        $this->start_controls_tabs(
            'tab_style_submenu_icons'
        );
        $this->start_controls_tab(
            'submenu_icon_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner'          => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner svg'      => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_icon_background',
                'label'    => __('Icon Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner',
            ]
        );
        $this->add_control(
            'submenu_icon_ordering',
            [
                'label'         => __('Icon Position', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'flex-start'      => [
                        'title' => __('Top', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'center'    => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'flex-end'     => [
                        'title' => __('Bottom', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'default'       => 'center',
                'toggle'        => false,
                'seprator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-advance-menu .ep-item' => 'align-items:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'submenu_icon_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submenu_icon_border',
                'label'    => __('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner',
            ]
        );

        $this->add_responsive_control(
            'submenu_icon_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'submenu_icon_size',
            [
                'label'     => __('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-icon-inner svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'submenu_icon_spacing',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-advance-menu .ep-item' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'submenu_icon_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_icon_h_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-item:hover .ep-icon-inner'          => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ep-sub-menu .ep-item:hover .ep-icon-inner svg'      => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .ep-sub-menu .ep-item:hover .ep-icon-inner svg path' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_icon_h_background',
                'label'    => __('Icon Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-item:hover .ep-icon-inner',
            ]
        );
        $this->add_control(
            'submenu_icon_h_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-item:hover .ep-icon-inner' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'submenu_icon_border_border!' => '',
                ],
            ]
        );
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_controls_badge() {
        $this->start_controls_section(
            'section_style_submenu_badge',
            [
                'label' => __('Badge', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'dynamic_menu!' => 'yes'
                ]
            ]
        );
        $this->start_controls_tabs(
            'tabs_style_submenu_badge'
        );
        $this->start_controls_tab(
            'tab_submenu_badge_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_badge_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge' => 'color: {{VALUE}}',
                ],
            ]

        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submenu_badge_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge',

            ]
        );
        $this->add_responsive_control(
            'submenu_badge_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'submenu_badge_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submenu_badge_border',
                'label'    => __('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge',
            ]
        );
        $this->add_responsive_control(
            'submenu_badge_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submenu_badge_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-badge',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_submenu_badge_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'submenu_badge_h_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-badge' => 'color: {{VALUE}}',
                ],
            ]

        );
        $this->add_group_control(
            Group_Control_Background::get_type(),

            [
                'name'     => 'submenu_badge_h_background',
                'label'    => __('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-item:hover .ep-badge',

            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function register_style_controls_arrows() {
        $this->start_controls_section(
            'section_style_sub_menu_arrows',
            [
                'label'     => __('Arrows', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_sub_menu_arrows' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'sub_menu_arrows_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-hover-icon' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'sub_menu_arrows_size',
            [
                'label'     => __('Arrows Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .ep-sub-menu .ep-sub-menu-wrap .ep-hover-icon' => 'font-size: {{VALUE}}px',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function register_layout_controls_additional() {
        $this->start_controls_section(
            'section_layout_controls_additional_settings',
            [
                'label' => __('Additional Options', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'icons_hide_on',
            [
                'label'    => __('Icons Hide on', 'bdthemes-element-pack'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => [
                    'desktop' => __('Desktop', 'bdthemes-element-pack'),
                    'tablet'  => __('Tablet', 'bdthemes-element-pack'),
                    'mobile'  => __('Mobile', 'bdthemes-element-pack'),
                ],
            ]
        );
        $this->add_control(
            'sub_title_hide_on',
            [
                'label'    => __('Sub Title Hide on', 'bdthemes-element-pack'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => [
                    'desktop' => __('Desktop', 'bdthemes-element-pack'),
                    'tablet'  => __('Tablet', 'bdthemes-element-pack'),
                    'mobile'  => __('Mobile', 'bdthemes-element-pack'),
                ],
            ]
        );
        // $this->add_control(
        //     'badge_hide_on',
        //     [
        //         'label'    => __('Icons Hide on', 'bdthemes-element-pack'),
        //         'type'     => Controls_Manager::SELECT2,
        //         'multiple' => true,
        //         'options'  => [
        //             'desktop' => __('Desktop', 'bdthemes-element-pack'),
        //             'tablet'  => __('Tablet', 'bdthemes-element-pack'),
        //             'mobile'  => __('Mobile', 'bdthemes-element-pack'),
        //         ],
        //     ]
        // );
        $this->end_controls_section();
    }

    public function render() {
        $settings = $this->get_settings_for_display(); ?>
        <div class="one-menu ep-sub-menu">
            <div class="ep-sub-menu-wrap">
                <?php

                if ($settings['show_submenu_heading'] === 'yes') :
                    printf('<div class="ep-heading">%s</div>', wp_kses($settings['submenu_header_title'], element_pack_allow_tags('title')));
                endif; ?>
                <div class="ep-sub-menu-grid ep-advance-menu ep-menu-style-<?php esc_attr_e($settings['submenu_style'], 'bdthemes-element-pack'); ?>">
                    <?php
                    if ($settings['dynamic_menu'] === 'yes') {
                        $this->render_dynamic_menu();
                    } else {
                        $this->render_static_menu();
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    }

    protected function render_static_menu() {
        $settings = $this->get_settings_for_display();
    ?>
        <?php
        foreach ($settings['menus'] as $index => $item) :
            $link_key = 'link_' . $index;
            $this->add_render_attribute($link_key, 'class', [
                'ep-item',
                'elementor-repeater-item-' . $item['_id'],
            ]);
            $this->add_link_attributes($link_key, $item['menu_link']);

            $icons_hide_on = element_pack_hide_on_class($this->get_settings('icons_hide_on'));
            $sub_title_hide_on = element_pack_hide_on_class($this->get_settings('sub_title_hide_on'));
            $this->add_render_attribute('icons', 'class', ['ep-icon', $icons_hide_on], true);

        ?>

            <a <?php $this->print_render_attribute_string($link_key); ?>>
                <?php
                if (!empty($item['menu_icon']['value'])) : ?>
                    <span <?php $this->print_render_attribute_string('icons'); ?>>
                        <div class="ep-icon-inner">
                            <?php Icons_Manager::render_icon($item['menu_icon'], ['aria-hidden' => 'true']); ?>
                        </div>
                    </span>
                <?php endif; ?>
                <span class="ep-content">
                    <span class="ep-title"><?php echo wp_kses($item['menu_title'], element_pack_allow_tags('title')); ?>
                        <?php

                        if (!empty($item['menu_badge'])) : ?>
                            <span class="ep-badge"><?php echo wp_kses($item['menu_badge'], element_pack_allow_tags('text')); ?></span>
                        <?php endif; ?>
                    </span>
                    <?php
                    if ((!empty($item['menu_sub_title'])) && ($settings['show_sub_menu_sub_title'] === 'yes')) :
                        printf('<p class="ep-sub-title %2$s">%1$s</p>', wp_kses($item['menu_sub_title'], element_pack_allow_tags('title')), $sub_title_hide_on);
                    endif; ?>
                </span>
                <?php
                if ($settings['show_sub_menu_arrows'] === 'yes') : ?>
                    <span class="ep-hover-icon ep-icon-arrow-right"></span>
                <?php
                endif; ?>
            </a>
        <?php endforeach; ?>
    <?php
    }

    protected function render_dynamic_menu() {

        $settings = $this->get_settings_for_display();
        $id       = 'ep-sub-menu-' . $this->get_id();

        if (!$settings['navbar']) {
            element_pack_alert(__('Please select a Menu From Setting!', 'bdthemes-element-pack'));
        }

        $nav_menu = !empty($settings['navbar']) ? wp_get_nav_menu_object($settings['navbar']) : false;

        if (!$nav_menu) {
            return;
        }

        $nav_menu_args = [
            'items_wrap'     => '%3$s',
            'fallback_cb'    => false,
            'container'      => '',
            'menu_id'        => $id,
            'theme_location' => 'default_navmenu', // creating a fake location for better functional control
            'menu' => $nav_menu,
            'echo'           => true,
            'depth'          => 1,
            'walker'         => new ep_sub_menu_menu_walker,
        ];
    ?>
        <?php wp_nav_menu(apply_filters('widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings)); ?>
<?php
    }
}
