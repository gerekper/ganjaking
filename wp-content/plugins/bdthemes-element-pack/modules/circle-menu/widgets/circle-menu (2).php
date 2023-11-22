<?php

namespace ElementPack\Modules\CircleMenu\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Circle_Menu extends Module_Base {
    public function get_name() {
        return 'bdt-circle-menu';
    }

    public function get_title() {
        return BDTEP . esc_html__('Circle Menu', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-circle-menu';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['circle', 'menu', 'rounded'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['tippy', 'ep-styles'];
        } else {
            return ['tippy', 'ep-font', 'ep-circle-menu'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'circle-menu', 'ep-scripts'];
        } else {
            return ['popper', 'tippyjs', 'circle-menu', 'ep-circle-menu'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/rfW22T-U7Ag';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content_iconnav',
            [
                'label' => esc_html__('Circle Menu', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'toggle_icon',
            [
                'label'       => esc_html__('Choose Toggle Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => true,
                'options'     => [
                    'plus' => [
                        'title' => esc_html__('Plus', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-plus',
                    ],
                    'plus-circle' => [
                        'title' => esc_html__('Plus Circle', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-plus-circle',
                    ],
                    'close' => [
                        'title' => esc_html__('Close', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-times',
                    ],
                    'cog' => [
                        'title' => esc_html__('Settings', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-cog',
                    ],
                    'menu' => [
                        'title' => esc_html__('Bars', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-bars',
                    ],
                    'custom' => [
                        'title' => esc_html__('Custom', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-edit',
                    ],
                ],
                'default' => 'plus',
            ]
        );

        $this->add_control(
            'custom_icon',
            [
                'label'   => esc_html__('Custom Icon', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'far fa-times-circle',
                    'library' => 'fa-regular',
                ],
                'condition' => [
                    'toggle_icon' => 'custom',
                ],
                'label_block' => false,
                'skin'        => 'inline',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label'   => esc_html__('Menu Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => 'Home',
            ]
        );

        $repeater->add_control(
            'circle_menu_icon',
            [
                'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default'          => [
                    'value'   => 'fas fa-home',
                    'library' => 'fa-solid',
                ],
                'label_block' => false,
                'skin'        => 'inline',
            ]
        );

        $repeater->add_control(
            'iconnav_link',
            [
                'label'       => esc_html__('Link', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'default'     => ['url' => '#'],
                'dynamic'     => ['active' => true],
                'description' => 'Add your section id WITH the # key. e.g: #my-id also you can add internal/external URL',
            ]
        );

        $repeater->add_control(
            'custom_style_popover',
            [
                'label'        => esc_html__('Custom Style', 'bdthemes-element-pack') . BDTEP_NC,
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'render_type'  => 'ui',
                'return_value' => 'yes',
            ]
        );

        $repeater->start_popover();

        $repeater->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu {{CURRENT_ITEM}}.bdt-menu-icon a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'icon_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu {{CURRENT_ITEM}}.bdt-menu-icon:hover a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'icon_background_color',
            [
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu {{CURRENT_ITEM}}.bdt-menu-icon' => 'background: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'icon_hover_background_color',
            [
                'label'     => esc_html__('Hover Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu {{CURRENT_ITEM}}.bdt-menu-icon:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $repeater->end_popover();

        $this->add_control(
            'circle_menu',
            [
                'label'     => esc_html__('Circle Menu Items', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::REPEATER,
                'fields'    => $repeater->get_controls(),
                'separator' => 'before',
                'default'   => [
                    [
                        'circle_menu_icon' => ['value' => 'fas fa-home', 'library' => 'fa-solid'],
                        'iconnav_link'     => [
                            'url' => '#',
                        ],
                        'title' => esc_html__('Home', 'bdthemes-element-pack'),
                    ],
                    [
                        'circle_menu_icon' => ['value' => 'fas fa-shopping-bag', 'library' => 'fa-solid'],
                        'iconnav_link'     => [
                            'url' => '#',
                        ],
                        'title' => esc_html__('Products', 'bdthemes-element-pack'),
                    ],
                    [
                        'circle_menu_icon' => ['value' => 'fas fa-wrench', 'library' => 'fa-solid'],
                        'iconnav_link'     => [
                            'url' => '#',
                        ],
                        'title' => esc_html__('Settings', 'bdthemes-element-pack'),
                    ],
                    [
                        'circle_menu_icon' => ['value' => 'fas fa-book', 'library' => 'fa-solid'],
                        'iconnav_link'     => [
                            'url' => '#',
                        ],
                        'title' => esc_html__('Documentation', 'bdthemes-element-pack'),
                    ],
                    [
                        'circle_menu_icon' => ['value' => 'fas fa-envelope', 'library' => 'fa-solid'],
                        'iconnav_link'     => [
                            'url' => '#',
                        ],
                        'title' => esc_html__('Contact Us', 'bdthemes-element-pack'),
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'toggle_icon_position',
            [
                'label'   => esc_html__('Toggle Icon Position', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => '',
                'options' => element_pack_position(),
            ]
        );

        $this->add_control(
            'toggle_icon_alignment',
            [
                'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-circle-menu' => 'justify-content: {{VALUE}}; display: flex;',
                ],
                'condition' => [
                    'toggle_icon_position' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_icon_x_position',
            [
                'label'   => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min'  => -500,
                        'step' => 10,
                        'max'  => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-circle-menu-h-offset: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_icon_y_position',
            [
                'label'   => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min'  => -500,
                        'step' => 10,
                        'max'  => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-circle-menu-v-offset: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional_settings',
            [
                'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'direction',
            [
                'label'   => esc_html__('Menu Direction', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'bottom-right',
                'options' => [
                    'top'          => esc_html__('Top', 'bdthemes-element-pack'),
                    'right'        => esc_html__('Right', 'bdthemes-element-pack'),
                    'bottom'       => esc_html__('Bottom', 'bdthemes-element-pack'),
                    'left'         => esc_html__('Left', 'bdthemes-element-pack'),
                    'top'          => esc_html__('Top', 'bdthemes-element-pack'),
                    'full'         => esc_html__('Full', 'bdthemes-element-pack'),
                    'top-left'     => esc_html__('Top-Left', 'bdthemes-element-pack'),
                    'top-right'    => esc_html__('Top-Right', 'bdthemes-element-pack'),
                    'top-half'     => esc_html__('Top-Half', 'bdthemes-element-pack'),
                    'bottom-left'  => esc_html__('Bottom-Left', 'bdthemes-element-pack'),
                    'bottom-right' => esc_html__('Bottom-Right', 'bdthemes-element-pack'),
                    'bottom-half'  => esc_html__('Bottom-Half', 'bdthemes-element-pack'),
                    'left-half'    => esc_html__('Left-Half', 'bdthemes-element-pack'),
                    'right-half'   => esc_html__('Right-Half', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'item_diameter',
            [
                'label'   => esc_html__('Circle Menu Size', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 35,
                ],
                'range' => [
                    'px' => [
                        'min'  => 20,
                        'step' => 1,
                        'max'  => 50,
                    ],
                ],
            ]
        );

        $this->add_control(
            'circle_radius',
            [
                'label'   => esc_html__('Circle Menu Distance', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                ],
                'range' => [
                    'px' => [
                        'min'  => 20,
                        'step' => 5,
                        'max'  => 500,
                    ],
                ],
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'   => esc_html__('Speed', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 500,
                ],
                'range' => [
                    'px' => [
                        'min'  => 100,
                        'step' => 10,
                        'max'  => 1000,
                    ],
                ],
            ]
        );

        $this->add_control(
            'delay',
            [
                'label'   => esc_html__('Delay', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1000,
                ],
                'range' => [
                    'px' => [
                        'min'  => 100,
                        'step' => 10,
                        'max'  => 2000,
                    ],
                ],
            ]
        );

        $this->add_control(
            'step_out',
            [
                'label'   => esc_html__('Step Out', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min'  => -200,
                        'step' => 5,
                        'max'  => 200,
                    ],
                ],
            ]
        );

        $this->add_control(
            'step_in',
            [
                'label'   => esc_html__('Step In', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => -20,
                ],
                'range' => [
                    'px' => [
                        'min'  => -200,
                        'step' => 5,
                        'max'  => 200,
                    ],
                ],
            ]
        );

        $this->add_control(
            'trigger',
            [
                'label'   => esc_html__('Trigger', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'hover',
                'options' => [
                    'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
                    'click' => esc_html__('Click', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'tooltip_on_trigger',
            [
                'label'   => esc_html__('Show Tooltip', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tooltip_settings',
            [
                'label'     => esc_html__('Tooltip Settings', 'bdthemes-element-pack'),
                'condition' => [
                    'tooltip_on_trigger' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tooltip_text',
            [
                'label'       => esc_html__('Tooltip Text', 'bdthemes-element-pack') . BDTEP_NC,
                'label_block' => true,
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Click me to show menus', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'tooltip_animation',
            [
                'label'   => esc_html__('Animation', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'shift-toward',
                'options' => [
                    'shift-away'   => esc_html__('Shift-Away', 'bdthemes-element-pack'),
                    'shift-toward' => esc_html__('Shift-Toward', 'bdthemes-element-pack'),
                    'fade'         => esc_html__('Fade', 'bdthemes-element-pack'),
                    'scale'        => esc_html__('Scale', 'bdthemes-element-pack'),
                    'perspective'  => esc_html__('Perspective', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'tooltip_x_offset',
            [
                'label'   => esc_html__('X Offset', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
            ]
        );

        $this->add_control(
            'tooltip_y_offset',
            [
                'label'   => esc_html__('Y Offset', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
            ]
        );

        $this->add_control(
            'tooltip_arrow',
            [
                'label' => esc_html__('Arrow', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'tooltip_trigger',
            [
                'label'       => esc_html__('Trigger on Click', 'bdthemes-element-pack'),
                'description' => esc_html__('Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_toggle_icon',
            [
                'label' => esc_html__('Toggle Icon', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_toggle_icon_style');

        $this->start_controls_tab(
            'tab_toggle_icon_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'toggle_icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_icon_background',
            [
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'toggle_icon_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon',
            ]
        );

        $this->add_responsive_control(
            'toggle_icon_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_icon_padding',
            [
                'label'     => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'toggle_icon_shadow',
                'selector' => '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon',
            ]
        );

        $this->add_responsive_control(
            'toggle_icon_size',
            [
                'label' => esc_html__('Icon Size', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 48,
                    ],
                ],
                'default' => [
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon a svg' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon a i'   => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'transition_function',
            [
                'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'ease',
                'options' => [
                    'ease'        => esc_html__('Ease', 'bdthemes-element-pack'),
                    'linear'      => esc_html__('Linear', 'bdthemes-element-pack'),
                    'ease-in'     => esc_html__('Ease-In', 'bdthemes-element-pack'),
                    'ease-out'    => esc_html__('Ease-Out', 'bdthemes-element-pack'),
                    'ease-in-out' => esc_html__('Ease-In-Out', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'toggle_icon_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'toggle_icon_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_icon_hover_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_icon_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'toggle_icon_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'toggle_icon_shadow_hover',
                'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
                'selector' => '{{WRAPPER}} .bdt-circle-menu li.bdt-toggle-icon:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_circle_menu_icon',
            [
                'label' => esc_html__('Circle Icon', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_circle_menu_icon_style');

        $this->start_controls_tab(
            'tab_circle_menu_icon_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'circle_menu_icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu-container .bdt-menu-icon a'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-circle-menu-container .bdt-menu-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'circle_menu_icon_background',
            [
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-menu-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'circle_menu_icon_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-circle-menu li.bdt-menu-icon',
            ]
        );

        $this->add_responsive_control(
            'circle_menu_icon_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-menu-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        // $this->add_responsive_control(
        //     'circle_menu_icon_padding',
        //     [
        //         'label' => esc_html__('Padding', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::DIMENSIONS,
        //         'selectors' => [
        //             '{{WRAPPER}} .bdt-circle-menu li.bdt-menu-icon' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
        //         ],
        //     ]
        // );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'circle_menu_icon_shadow',
                'selector' => '{{WRAPPER}} .bdt-circle-menu li.bdt-menu-icon',
            ]
        );

        $this->add_responsive_control(
            'circle_menu_icon_size',
            [
                'label' => esc_html__('Icon Size', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li.bdt-menu-icon' => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'circle_menu_icon_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'circle_menu_icon_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu-container .bdt-menu-icon:hover a'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-circle-menu-container .bdt-menu-icon:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'circle_menu_icon_hover_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'circle_menu_icon_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'circle_menu_icon_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-circle-menu li:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_tooltip',
            [
                'label'     => esc_html__('Tooltip', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'tooltip_on_trigger' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'tooltip_width',
            [
                'label'      => esc_html__('Width', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [
                    'px',
                    'em',
                ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'max-width: calc({{SIZE}}{{UNIT}} - 10px) !important;',
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tooltip_typography',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
            ]
        );

        $this->add_control(
            'tooltip_title_color',
            [
                'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"] .bdt-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tooltip_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tooltip_text_align',
            [
                'label'   => esc_html__('Text Alignment', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tooltip_background',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
            ]
        );

        $this->add_control(
            'tooltip_arrow_color',
            [
                'label'     => esc_html__('Arrow Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'tooltip_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'tooltip_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
            ]
        );

        $this->add_responsive_control(
            'tooltip_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'tooltip_box_shadow',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
            ]
        );

        $this->end_controls_section();
    }

    public function render_loop_iconnav_list($settings, $list) {

        $this->add_render_attribute(
            [
                'iconnav-link' => [
                    'class' => [
                        'bdt-position-center',
                    ],
                    'target' => [
                        $list['iconnav_link']['is_external'] ? '_blank' : '_self',
                    ],
                    'rel' => [
                        $list['iconnav_link']['nofollow'] ? 'nofollow' : '',
                    ],
                    // 'title' => [
                    //     esc_html($list['title']),
                    // ],
                    'href' => [
                        esc_url($list['iconnav_link']['url']),
                    ],
                ],
            ],
            '',
            '',
            true
        );

        if (isset($settings['tooltip_on_trigger']) && $settings['tooltip_on_trigger'] == 'yes') {
            $this->add_render_attribute(
                [
                    'iconnav-link' => [
                        // 'title' => [
                        //     esc_html($list['title']),
                        // ],
                        'class' => 'bdt-tippy-tooltip',
                        'data-tippy' => '',
                        'data-tippy-content' => $list['title'],
                    ],
                ],
                '',
                '',
                true
            );
            if ($settings['tooltip_x_offset']['size'] or $settings['tooltip_y_offset']['size']) {
                $this->add_render_attribute('iconnav-link', 'data-tippy-offset', '[' . $settings['tooltip_x_offset']['size'] . ',' . $settings['tooltip_y_offset']['size'] . ']', true);
            }
            if ('yes' == $settings['tooltip_arrow']) {
                $this->add_render_attribute('iconnav-link', 'data-tippy-arrow', 'true', true);
            } else {
                $this->add_render_attribute('iconnav-link', 'data-tippy-arrow', 'false', true);
            }
            if ($settings['tooltip_animation']) {
                $this->add_render_attribute('iconnav-link', 'data-tippy-animation', $settings['tooltip_animation'], true);
            }
            if ('yes' == $settings['tooltip_trigger']) {
                $this->add_render_attribute('iconnav-link', 'data-tippy-trigger', 'click', true);
            }
            // if ($item['tooltip_placement']) {
            //     $this->add_render_attribute($repeater_key, 'data-tippy-placement', $item['tooltip_placement'], true);
            // }
        }

        if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
            // add old default
            $settings['icon'] = 'fas fa-arrow-right';
        }

        $migrated = isset($list['__fa4_migrated']['circle_menu_icon']);
        $is_new = empty($list['icon']) && Icons_Manager::is_migration_allowed();

        $this->add_render_attribute('menu-item', 'class', 'bdt-menu-icon elementor-repeater-item-' . esc_attr($list['_id']), true);

?>
        <li <?php echo ($this->get_render_attribute_string('menu-item')); ?>>
            <a <?php echo $this->get_render_attribute_string('iconnav-link'); ?>>
                <?php if ($list['circle_menu_icon']['value']) : ?>
                    <span>

                        <?php if ($is_new || $migrated) :
                            Icons_Manager::render_icon($list['circle_menu_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                        else : ?>
                            <i class="<?php echo esc_attr($list['icon']); ?>" aria-hidden="true"></i>
                        <?php endif; ?>

                    </span>

                <?php endif; ?>
            </a>
        </li>
    <?php
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-circle-menu-' . $this->get_id();
        $toggle_icon = ($settings['toggle_icon']) ?: 'plus';

        $this->add_render_attribute(
            [
                'circle-menu-container' => [
                    'id' => [
                        esc_attr($id),
                    ],
                    'class' => [
                        'bdt-circle-menu-container',
                        $settings['toggle_icon_position'] ? 'bdt-position-fixed bdt-position-' . $settings['toggle_icon_position'] : '',
                    ],
                ],
            ]
        );

        if ('custom' == $settings['toggle_icon']) {
            $this->add_render_attribute(
                [
                    'toggle-icon' => [
                        'href' => [
                            'javascript:void(0)',
                        ],
                        'class' => [
                            'bdt-icon bdt-link-reset',
                            'bdt-position-center',
                        ],
                    ],
                ]
            );
        } else {
            $this->add_render_attribute(
                [
                    'toggle-icon' => [
                        'href' => [
                            'javascript:void(0)',
                        ],
                        'class' => [
                            'bdt-icon bdt-link-reset',
                            'bdt-position-center',
                        ],
                    ],
                ]
            );
        }

        if (isset($settings['tooltip_on_trigger']) && !empty($settings['tooltip_text']) && $settings['tooltip_on_trigger'] == 'yes') {
            $this->add_render_attribute(
                [
                    'toggle-icon' => [
                        // 'title' => [
                        //     esc_html__('Click me to show menus.', 'bdthemes-element-pack'),
                        // ],
                        'class' => 'bdt-tippy-tooltip',
                        'data-tippy' => '',
                        'data-tippy-content' => $settings['tooltip_text'],
                    ],
                ]
            );
            if ($settings['tooltip_x_offset']['size'] or $settings['tooltip_y_offset']['size']) {
                $this->add_render_attribute('toggle-icon', 'data-tippy-offset', '[' . $settings['tooltip_x_offset']['size'] . ',' . $settings['tooltip_y_offset']['size'] . ']', true);
            }
            if ('yes' == $settings['tooltip_arrow']) {
                $this->add_render_attribute('toggle-icon', 'data-tippy-arrow', 'true', true);
            } else {
                $this->add_render_attribute('toggle-icon', 'data-tippy-arrow', 'false', true);
            }
            if ($settings['tooltip_animation']) {
                $this->add_render_attribute('toggle-icon', 'data-tippy-animation', $settings['tooltip_animation'], true);
            }
            if ('yes' == $settings['tooltip_trigger']) {
                $this->add_render_attribute('toggle-icon', 'data-tippy-trigger', 'click', true);
            }
            // if ($item['tooltip_placement']) {
            //     $this->add_render_attribute($repeater_key, 'data-tippy-placement', $item['tooltip_placement'], true);
            // }
        }

        $circle_menu_settings = wp_json_encode(
            array_filter([
                "direction" => $settings["direction"],
                "item_diameter" => $settings["item_diameter"]["size"],
                "circle_radius" => $settings["circle_radius"]["size"],
                "speed" => $settings["speed"]["size"],
                "delay" => $settings["delay"]["size"],
                "step_out" => $settings["step_out"]["size"],
                "step_in" => $settings["step_in"]["size"],
                "trigger" => $settings["trigger"],
                "transition_function" => $settings["transition_function"],
            ])
        );

        $this->add_render_attribute('circle-menu-settings', 'data-settings', $circle_menu_settings);

    ?>
        <div <?php echo $this->get_render_attribute_string('circle-menu-container'); ?>>
            <ul class="bdt-circle-menu" <?php echo $this->get_render_attribute_string('circle-menu-settings'); ?>>
                <li class="bdt-toggle-icon">

                    <?php if ('custom' == $settings['toggle_icon']) { ?>
                        <a <?php echo $this->get_render_attribute_string('toggle-icon'); ?>>
                            <?php Icons_Manager::render_icon($settings['custom_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                    <?php } else { ?>
                        <a <?php echo $this->get_render_attribute_string('toggle-icon'); ?>>
                            <i class="ep-icon-<?php echo esc_attr($toggle_icon); ?>" aria-hidden="true"></i>
                        </a>
                    <?php } ?>

                </li>
                <?php
                foreach ($settings['circle_menu'] as $key => $nav) :
                    $this->render_loop_iconnav_list($settings, $nav);
                endforeach;
                ?>
            </ul>
        </div>
<?php
    }
}
