<?php

namespace ElementPack\Includes\Settings;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Tab_Base;
use Elementor\Group_Control_Border;
// use Elementor\Core\Experiments\Manager as Experiments_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Context_Menu_Controls extends Tab_Base {

    public function get_id() {
        return 'element-pack-context-menu';
    }
    public function get_title() {
        return __('Context Menu', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-context-menu bdt-new';
    }
    public function get_style_depends() {
        return ['ep-context-menu'];
    }

    public function get_help_url() {
        return '';
    }
    protected function register_tab_controls() {
        $this->start_controls_section(
            'element_pack_context_menu_settings',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
                'tab'   => 'element-pack-context-menu',
            ]
        );
        $this->add_control(
            'ep_context_menu_enable',
            [
                'label'         => __('Enable context Menu', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => __('Yes', 'bdthemes-element-pack'),
                'label_off'     => __('No', 'bdthemes-element-pack'),
                'default'       => 'no',
            ]
        );
        $this->add_control(
            'ep_context_menu_heading_display_condition',
            [
                'label'     => __('DISPLAY CONDITION', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'ep_context_menu_enable' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'ep_context_menu_only_loggin_in',
            [
                'label'         => __('Display only for logged in Users?', 'bdthemes-element-pack') . BDTEP_NC,
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => __('Yes', 'bdthemes-element-pack'),
                'label_off'     => __('No', 'bdthemes-element-pack'),
                'default'       => 'no',
                'condition' => [
                    'ep_context_menu_enable' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'ep_context_menu_specific_page',
            [
                'label'         => __('Display only for Specific Page', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => __('Yes', 'bdthemes-element-pack'),
                'label_off'     => __('No', 'bdthemes-element-pack'),
                'default'       => 'no',
                'condition' => [
                    'ep_context_menu_enable' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'ep_context_menu_page_ids',
            [
                'label'           => __('Page Ids', 'bdthemes-element-pack'),
                'type'            => Controls_Manager::TEXTAREA,
                'label_block'     => true,
                'rows'        => 5,
                'placeholder' => __('Enter Page ids separated by comma. for example 1, 2, 3, 4', 'bdthemes-element-pack'),
                'condition' => [
                    'ep_context_menu_enable' => 'yes',
                    'ep_context_menu_specific_page' => 'yes'
                ]
            ]
        );
        $repeater = new Repeater();

        $repeater->add_control(
            'menu_title',
            [
                'label'       => __('Menu Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
                'condition' => [
                    'menu_type!' => 'child_end'
                ]
            ]
        );

        $repeater->add_control(
            'menu_type',
            [
                'label'       => __('Select Item Type', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SELECT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
                'options'       => [
                    'item'      => 'Item',
                    'child_start' => 'Child Start',
                    'child_end'   => 'Child End',
                ],
                'default' => 'item',
            ]
        );

        $repeater->add_control(
            'menu_link',
            [
                'label'       => __('Link', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => ['active' => true],
                'default' => [
                    'url' => '#',
                ],
                'label_block' => true,
                'condition' => [
                    'menu_type!' => 'child_end'
                ]
            ]
        );

        $repeater->add_control(
            'menu_icon',
            [
                'label' => __('Icon', 'bdthemes-element-pack'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'condition' => [
                    'menu_type!' => 'child_end'
                ]
            ]
        );

        $this->add_control(
            'menus',
            [
                'label'   => __('Menu Items', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'separator' => 'before',
                'default' => [
                    [
                        'menu_title'   => __('About', 'bdthemes-element-pack'),
                        'menu_link'    => '#',
                    ],
                    [
                        'menu_title'   => __('Gallery', 'bdthemes-element-pack'),
                        'menu_link'    => '#',
                        'menu_type' => 'child_start'
                    ],
                    [
                        'menu_title'   => __('Gallery 01', 'bdthemes-element-pack'),
                        'menu_link'    => '#',
                    ],
                    [
                        'menu_title'   => __('Gallery 02', 'bdthemes-element-pack'),
                        'menu_link'    => '#',
                    ],
                    [
                        'menu_title'   => __('Gallery 03', 'bdthemes-element-pack'),
                        'menu_link'    => '#',
                    ],
                    [
                        'menu_type' => 'child_end'
                    ],
                    [
                        'menu_title'   => __('Contacts', 'bdthemes-element-pack'),
                        'menu_link'    => '#',
                    ],
                ],
                'title_field' => '{{{ elementor.helpers.renderIcon( this, menu_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} <# print( (menu_type == "child_start" ) ? "<b>[ Child Start:</b> " + menu_title : menu_title ) #><# print( (menu_type == "child_end" ) ? "<b>Child End ]</b>" : "" ) #>',
                'condition' => [
                    'ep_context_menu_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'hr_divider',
            [
                'type'    => Controls_Manager::DIVIDER,
                'condition' => [
                    'ep_context_menu_enable' => 'yes'
                ]
            ]
        );
        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'element_pack_context_menu_style',
            [
                'label' => esc_html__('Style', 'bdthemes-element-pack'),
                'tab'   => 'element-pack-context-menu',
                'condition' => [
                    'ep_context_menu_enable' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'menu_container',
            [
                'label'      => esc_html__('MENU CONTAINER', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'menu_background',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .bdt-context-menu .bdt-context',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => '#fff',
                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'menu_bg_border',
                'selector' => '{{WRAPPER}} .bdt-context-menu .bdt-context',
            ]
        );

        $this->add_responsive_control(
            'menu_bg_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_bg_link_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_items',
            [
                'label'      => esc_html__('MENU ITEMS', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->start_controls_tabs('menu_link_styles');

        $this->start_controls_tab(
            'menu_link_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack')
            ]
        );

        $this->add_control(
            'menu_link_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context li a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'menu_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context>li a .bdt-menu-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-context-menu .bdt-context>li a .bdt-menu-icon svg *' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'menu_indicator_color',
            [
                'label'     => esc_html__('Indicator Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context > li > .has-arrow i' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'menu_link_background',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .bdt-context-menu .bdt-context li a',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => '#e3e8eb',
                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'menu_border',
                'selector' => '{{WRAPPER}} .bdt-context-menu .bdt-context li a ',
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'menu_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_link_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_spacing',
            [
                'label' => esc_html__('Item Gap', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ]
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context>li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_icon_spacing',
            [
                'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context li a .bdt-menu-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'menu_typography_normal',
                'selector' => '{{WRAPPER}} .bdt-context-menu .bdt-context li a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'menu_link_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack')
            ]
        );

        $this->add_control(
            'menu_link_color_hover',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'link_background_hover',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .bdt-context-menu .bdt-context li a:hover',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => '#d7dee3',
                    ],
                ],
            ]
        );

        $this->add_control(
            'menu_border_color_hover',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context>li a:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'menu_border_border!' => '',
                ],
            ]
        );

        $this->add_control(
            'menu_icon_hover_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-context-menu .bdt-context>li a:hover .bdt-menu-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-context-menu .bdt-context>li a:hover .bdt-menu-icon svg *' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }
}
