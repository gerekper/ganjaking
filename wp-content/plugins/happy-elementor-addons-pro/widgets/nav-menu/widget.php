<?php

/**
 * Nav Menu widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Happy_Addons_Pro\Controls\Indicator_Selector;

defined('ABSPATH') || die();

class Nav_Menu extends Base {

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title() {
        return __('Happy Menu', 'happy-addons-pro');
    }

    public function get_custom_help_url() {
        return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/nav-menu/';
    }

    protected static function get_tiles_layout_options() {
        $dir = HAPPY_ADDONS_PRO_ASSETS . 'imgs/indicators/';

        $icons = array("caret-1", "caret-2", "caret-3", "caret-4", "line-1", "line-2", "line-3", "line-4", "line-5", "plus-1", "plus-2", "plus-3");

        $options = [];

        foreach ($icons as $i => $icon) {
            $title = ucwords(str_replace('-', ' ', $icon));
            $options[$i] = [
                'title' => sprintf(esc_attr__('%s', 'happy-addons-pro'), $title),
                'url'   => $dir . "{$icon}.svg",
                'value' => $icon
            ];
        }

        return $options;
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'hm hm-mega-menu';
    }

    public function get_keywords() {
        return ['menu', 'nav-menu', 'nav', 'navigation', 'navigation-menu', 'mega', 'megamenu', 'mega-menu'];
    }

    /**
     * Get a list of all Navigation Menu
     *
     * @return array
     */
    public function get_menus() {
        $list = [];
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            $list[$menu->slug] = $menu->name;
        }

        return $list;
    }

    /**
     * Register widget content controls
     */
    protected function register_content_controls() {
        $this->__navigation_menu_content_controls();
        $this->__hamburger_menu_content_controls();
    }

    protected function __navigation_menu_content_controls() {

        $this->start_controls_section(
            '_section_navigation_menu_settings',
            [
                'label' => esc_html__('Navigation Menu', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'nav_menu',
            [
                'label'     => esc_html__('Select menu', 'happy-addons-pro'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->get_menus(),
            ]
        );

        $this->add_responsive_control(
            'horizontal_menu_position',
            [
                'label' => esc_html__('Horizontal position', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ha-menu-po-left',
                'options' => [
                    'ha-menu-po-left'  => esc_html__('Left', 'happy-addons-pro'),
                    'ha-menu-po-center' => esc_html__('Center', 'happy-addons-pro'),
                    'ha-menu-po-right' => esc_html__('Right', 'happy-addons-pro'),
                    'ha-menu-po-justified'  => esc_html__('Justified', 'happy-addons-pro'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function __hamburger_menu_content_controls() {

        $this->start_controls_section(
            '_section_menu_settings',
            [
                'label' => esc_html__('Hamburger Menu', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ha_nav_menu_logo',
            [
                'label' => esc_html__('Menu Logo', 'happy-addons-pro'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'ha_nav_menu_logo_link_to',
            [
                'label' => esc_html__('Logo link', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'home',
                'options' => [
                    'home' => esc_html__('Default(Home)', 'happy-addons-pro'),
                    'custom' => esc_html__('Custom URL', 'happy-addons-pro'),
                ],
            ]
        );

        $this->add_control(
            'ha_nav_menu_logo_link',
            [
                'label' => esc_html__(' Custom Link', 'happy-addons-pro'),
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
                'condition' => [
                    'ha_nav_menu_logo_link_to' => 'custom',
                ],
                'show_label' => false,

            ]
        );

        $this->add_control(
            'ha_hamburger_icon',
            [
                'label' => __('Open Icon (Optional)', 'happy-addons-pro'),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'default' => [
                    'value' => 'fas fa-bars',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'exclude_inline_options' => ['svg']
            ]
        );

        $this->add_control(
            'ha_hamburger_icon_close',
            [
                'label' => __('Close Icon (Optional)', 'happy-addons-pro'),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'default' => [
                    'value' => 'hm hm-cross',
                    'library' => 'happy-icons',
                ],
                'skin' => 'inline',
                'exclude_inline_options' => ['svg']
            ]
        );

        $this->add_control(
            'submenu_click_area',
            [
                'label'         => esc_html__('Submenu Click Area', 'happy-addons-pro'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Icon', 'happy-addons-pro'),
                'label_off'     => esc_html__('Text', 'happy-addons-pro'),
                'return_value'  => 'icon',
                'default'       => 'icon',
            ]
        );

        $this->add_control(
            '_heading_one_page',
            [
                'label' => __('One Page Menu Settings', 'happy-addons-pro'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'one_page_enable',
            [
                'label' => esc_html__('Enable one page? ', 'happy-addons-pro'),
                'description'    => esc_html__('This works in the current page.', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => esc_html__('Yes', 'happy-addons-pro'),
                'label_off' => esc_html__('No', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'responsive_breakpoint',
            [
                'label' => __('Responsive Breakpoint', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ha_menu_responsive_tablet',
                'options' => [
                    'ha_menu_responsive_tablet'  => __('Tablet', 'happy-addons-pro'),
                    'ha_menu_responsive_mobile' => __('Mobile', 'happy-addons-pro'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget style controls
     */
    protected function register_style_controls() {
        $this->__menu_wrapper_style_controls();
        $this->__menu_item_style_controls();
        $this->__submenu_item_style_controls();
        $this->__submenu_panel_style_controls();
        $this->__hamburger_style_controls();
        $this->__mobile_menu_logo_style_controls();
    }

    protected function __menu_wrapper_style_controls() {

        $this->start_controls_section(
            '_style_menu_wrapper',
            [
                'label' => esc_html__('Menu Wrapper', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ha_menubar_height',
            [
                'label' => esc_html__('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 300,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop'],
                'desktop_default' => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menubar_background',
                'label' => esc_html__('Menu Panel Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'devices' => ['desktop'],
                'selector' => '{{WRAPPER}} .ha-menu-container',
            ]
        );

        //TODO: Make separate control group
        $this->add_responsive_control(
            'wrapper_color_mobile',
            [
                'label'     => esc_html__('Mobile Wrapper Background', 'happy-addons-pro'),
                'type'      => Controls_Manager::COLOR,
                'devices'   => ['desktop', 'tablet', 'mobile'],
                'selectors' => [
                    '(tablet) {{WRAPPER}} .ha-menu-container'   => 'background-color: {{VALUE}};',
                    '(mobile) {{WRAPPER}} .ha-menu-container'   => 'background-color: {{VALUE}};',
                ],
            ]
        );

        //TODO: Make separate control group
        $this->add_responsive_control(
            'ha_mobile_menu_panel_spacing',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'tablet_default' => [
                    'top' => '10',
                    'right' => '0',
                    'bottom' => '10',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'devices' => ['desktop', 'tablet'],
                'selectors' => [
                    '(tablet) {{WRAPPER}} .ha-nav-identity-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        //TODO: Make separate control group
        $this->add_responsive_control(
            'ha_mobile_menu_panel_width',
            [
                'label' => esc_html__('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'range' => [
                    'px' => [
                        'min' => 350,
                        'max' => 700,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'tablet_default' => [
                    'size' => 350,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '(tablet) {{WRAPPER}} .ha-menu-container' => 'max-width: {{SIZE}}{{UNIT}};',
                    '(tablet) {{WRAPPER}} .ha-menu-offcanvas-elements' => '--offcanvas-left-offset: -{{SIZE}}{{UNIT}};',
                    '(mobile) {{WRAPPER}} .ha-menu-container' => 'max-width: {{SIZE}}{{UNIT}};',
                    '(mobile) {{WRAPPER}} .ha-menu-offcanvas-elements' => '--offcanvas-left-offset: -{{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_border_radius',
            [
                'label' => esc_html__('Menu border radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'separator' => ['before'],
                'desktop_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function __menu_item_style_controls() {

        $this->start_controls_section(
            'ha_style_tab_menuitem',
            [
                'label' => esc_html__('Menu Item', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );



        $this->add_responsive_control(
            'ha_menu_item_spacing',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => ['before'],
                'desktop_default' => [
                    'top' => 0,
                    'right' => 15,
                    'bottom' => 0,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 10,
                    'right' => 15,
                    'bottom' => 10,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'ha_menu_icon_position',
            [
                'label' => esc_html__('Icon Position', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'top' => [
                        'title' => esc_html__('Top', 'happy-addons-pro'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-arrow-right',
                    ],
                ],
                'default' => 'left',
            ]
        );

        $this->add_control(
            'ha_menu_icon_size',
            [
                'label' => __('Icon Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-nav-link-icon-position-top .ha-menu-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ha_menu_icon_position' => 'top'
                ],
            ]
        );

        $this->add_control(
            'ha_menu_icon_spacing',
            [
                'label' => __('Icon Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-nav-link-icon-position-top .ha-menu-icon' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-menu-nav-link-icon-position-left .ha-menu-icon' => 'padding-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-menu-nav-link-icon-position-right .ha-menu-icon' => 'padding-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ha_content_typography',
                'label' => esc_html__('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'selector' => '{{WRAPPER}} .ha-navbar-nav > li > a',
            ]
        );



        // $this->add_control(
        //     'ha_menu_item_h',
        //     [
        //         'label' => esc_html__( 'Menu Item Style', 'happy-addons-pro' ),
        //         'type' => Controls_Manager::HEADING,
        //         'separator' => 'before',
        //     ]
        // );


        $this->start_controls_tabs(
            'ha_nav_menu_tabs'
        );
        // Normal
        $this->start_controls_tab(
            'ha_nav_menu_normal_tab',
            [
                'label' => esc_html__('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_item_background',
                'label' => esc_html__('Item background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav > li > a',
            ]
        );

        $this->add_control(
            'ha_menu_text_color',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'desktop_default' => '#000000',
                'tablet_default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav > li > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab(
            'ha_nav_menu_hover_tab',
            [
                'label' => esc_html__('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_item_background_hover',
                'label' => esc_html__('Item background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav > li > a:hover, {{WRAPPER}} .ha-navbar-nav > li > a:focus, {{WRAPPER}} .ha-navbar-nav > li > a:active, {{WRAPPER}} .ha-navbar-nav > li:hover > a',
            ]
        );

        $this->add_control(
            'ha_item_color_hover',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav > li > a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li > a:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li:hover > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li:hover > a .ha-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li > a:hover .ha-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li > a:focus .ha-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li > a:active .ha-submenu-indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        // active
        $this->start_controls_tab(
            'ha_nav_menu_active_tab',
            [
                'label' => esc_html__('Active', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'        => 'ha_nav_menu_active_bg_color',
                'label'     => esc_html__('Item background', 'happy-addons-pro'),
                'types'        => ['classic', 'gradient'],
                'selector'    => '{{WRAPPER}} .ha-navbar-nav > li.current-menu-item > a,{{WRAPPER}} .ha-navbar-nav > li.current-menu-ancestor > a'
            ]
        );

        $this->add_control(
            'ha_nav_menu_active_text_color',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav > li.current-menu-item > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li.current-menu-ancestor > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav > li.current-menu-ancestor > a .ha-submenu-indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __submenu_item_style_controls() {

        $this->start_controls_section(
            'ha_style_tab_dropdown_indicator',
            [
                'label' => esc_html__('Dropdown Indicator', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ha_style_tab_submenu_item_indicator',
            [
                'label'       => __('Style', 'happy-addons-pro'),
                'label_block' => true,
                'type'        => Indicator_Selector::TYPE,
                'default'     => 'line-3',
                'col'         => '5',
                'options'     => self::get_tiles_layout_options()
            ]
        );

        $this->add_control(
            'ha_style_tab_submenu_item_indicator_size',
            [
                'label' => __('Indicator Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav li a .ha-submenu-indicator-wrap svg' => 'height: {{SIZE}}{{UNIT}}; width: auto;',
                ],
            ]
        );

        $this->add_control(
            'ha_style_tab_submenu_indicator_color',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' =>  '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav li a .ha-submenu-indicator-wrap svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ha_style_tab_submenu_indicator_color_hover',
            [
                'label' => esc_html__('Hover Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' =>  '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav li a:hover .ha-submenu-indicator-wrap svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_submenu_indicator_spacing',
            [
                'label' => esc_html__('Margin', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav-default .ha-dropdown-has>a .ha-submenu-indicator-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_submenu_indicator_padding',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav-default .ha-dropdown-has>a .ha-submenu-indicator-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_submenu_indicator_border',
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'devices' => ['desktop', 'tablet', 'mobile'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav-default .ha-dropdown-has>a .ha-submenu-indicator-wrap',
            ]
        );

        $this->add_responsive_control(
            'ha_submenu_indicator_border_radius',
            [
                'label' => esc_html__('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav-default .ha-dropdown-has>a .ha-submenu-indicator-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'ha_style_tab_submenu_item',
            [
                'label' => esc_html__('Submenu Item', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ha_menu_item_typography',
                'label' => esc_html__('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a',
            ]
        );

        $this->add_responsive_control(
            'ha_submenu_item_spacing',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                // 'devices' => [ 'desktop', 'tablet', 'mobile' ],
                // 'desktop_default' => [
                //     'top' => 15,
                //     'right' => 15,
                //     'bottom' => 15,
                //     'left' => 15,
                //     'unit' => 'px',
                // ],
                // 'tablet_default' => [
                //     'top' => 15,
                //     'right' => 15,
                //     'bottom' => 15,
                //     'left' => 15,
                //     'unit' => 'px',
                // ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ha_menu_item_border_heading',
            [
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_item_border',
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'exclude' => ['color'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a',
            ]
        );

        $this->add_control(
            'ha_menu_item_border_first_child_heading',
            [
                'label' => esc_html__('First Child', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_item_border_first_child',
                'label' => esc_html__('First Child', 'happy-addons-pro'),
                'exclude' => ['color'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li:first-child > a',
            ]
        );

        $this->add_control(
            'ha_menu_item_border_last_child_heading',
            [
                'label' => esc_html__('Last Child', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_item_border_last_child',
                'label' => esc_html__('Border last Child', 'happy-addons-pro'),
                'exclude' => ['color'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li:last-child > a',
            ]
        );


        $this->add_control(
            'ha_sub_menu_item_colors_heading',
            [
                'label' => esc_html__('', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs(
            'ha_submenu_active_hover_tabs'
        );
        $this->start_controls_tab(
            'ha_submenu_normal_tab',
            [
                'label'    => esc_html__('Normal', 'happy-addons-pro')
            ]
        );

        $this->add_control(
            'ha_submenu_item_color',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a' => 'color: {{VALUE}}',
                ],

            ]
        );

        $this->add_control(
            'ha_nav_sub_menu__border_color',
            [
                'label' => esc_html__('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'ha_menu_item_border_border!' => ''
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menu_item_background',
                'label' => esc_html__('Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ha_submenu_hover_tab',
            [
                'label'    => esc_html__('Hover', 'happy-addons-pro')
            ]
        );

        $this->add_control(
            'ha_item_text_color_hover',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li:hover > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ha_nav_sub_menu_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:hover' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:focus' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:active' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li:hover > a' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'ha_menu_item_border_border!' => ''
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menu_item_background_hover',
                'label' => esc_html__('Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '
					{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:hover,
					{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:focus,
					{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li > a:active,
					{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li:hover > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ha_submenu_active_tab',
            [
                'label'    => esc_html__('Active', 'happy-addons-pro')
            ]
        );

        $this->add_control(
            'ha_nav_sub_menu_active_text_color',
            [
                'label' => esc_html__('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li.current-menu-item > a' => 'color: {{VALUE}} !important'
                ],
            ]
        );

        $this->add_control(
            'ha_nav_sub_menu_active_border_color',
            [
                'label' => esc_html__('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li.current-menu-item > a' => 'border-color: {{VALUE}} !important'
                ],
                'condition' => [
                    'ha_menu_item_border_border!' => ''
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'        => 'ha_nav_sub_menu_active_bg_color',
                'label'     => esc_html__('Background', 'happy-addons-pro'),
                'types'        => ['classic', 'gradient'],
                'selector'    => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel > li.current-menu-item > a',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __submenu_panel_style_controls() {

        $this->start_controls_section(
            'ha_style_tab_submenu_panel',
            [
                'label' => esc_html__('Submenu Panel', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'sub_panel_padding',
            [
                'label'         => esc_html__('Padding', 'happy-addons-pro'),
                'type'          => Controls_Manager::DIMENSIONS,
                'default'       => [
                    'top'       => '0',
                    'bottom'    => '0',
                    'left'      => '0',
                    'right'     => '0',
                    'isLinked'  => false,
                ],
                'selectors'     => [
                    '{{WRAPPER}} .ha-submenu-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_panel_submenu_border',
                'label' => esc_html__('Panel Menu Border', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_submenu_container_background',
                'label' => esc_html__('Container background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel',
            ]
        );

        $this->add_responsive_control(
            'ha_submenu_panel_border_radius',
            [
                'label' => esc_html__('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'desktop_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_submenu_container_width',
            [
                'label' => esc_html__('Container Width', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'devices' => ['desktop'],
                'desktop_default' => '220',
                'tablet_default' => '200',
                'selectors' => [
                    '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel' => 'min-width: {{VALUE}}px;',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ha_panel_box_shadow',
                'label' => esc_html__('Box Shadow', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-navbar-nav .ha-submenu-panel',
            ]
        );

        $this->end_controls_section();
    }

    protected function __hamburger_style_controls() {

        $this->start_controls_section(
            'ha_menu_toggle_style_tab',
            [
                'label' => esc_html__('Humburger', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ha_menu_toggle_style_title',
            [
                'label' => esc_html__('Humburger Toggle', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ha_menu_toggle_icon_position',
            [
                'label' => esc_html__('Position', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-chevron-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-chevron-right',
                    ],
                ],
                'default' => 'right',
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-hamburger' => 'float: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_menu_toggle_spacing',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px',],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-hamburger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_menu_toggle_width',
            [
                'label' => esc_html__('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 70,
                        'step' => 0.5,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-hamburger .ha-menu-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_menu_toggle_border_radius',
            [
                'label' => esc_html__('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-hamburger' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // $this->add_responsive_control(
        //     'ha_menu_open_typography',
        //     [
        //         'label' => esc_html__( 'Icon Size', 'happy-addons-pro' ),
        //         'type' => Controls_Manager::SLIDER,
        //         'size_units' => [ 'px' ],
        //         'range' => [
        //             'px' => [
        //                 'min' => 15,
        //                 'max' => 100,
        //                 'step' => 1,
        //             ],
        //         ],
        //         'selectors' => [
        //             '{{WRAPPER}} .ha-menu-hamburger > .ha-menu-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        //         ],
        //         'condition' => [
        //             'ha_hamburger_icon[value]!'    => '',
        //         ],
        //     ]
        // );

        $this->start_controls_tabs(
            'ha_menu_toggle_normal_and_hover_tabs'
        );

        $this->start_controls_tab(
            'ha_menu_toggle_normal',
            [
                'label' => esc_html__('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menu_toggle_background',
                'label' => esc_html__('Background', 'happy-addons-pro'),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} .ha-menu-hamburger',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_toggle_border',
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .ha-menu-hamburger',
            ]
        );

        $this->add_control(
            'ha_menu_toggle_icon_color',
            [
                'label' => esc_html__('Humber Icon Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-hamburger .ha-menu-hamburger-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-menu-hamburger > .ha-menu-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ha_menu_toggle_hover',
            [
                'label' => esc_html__('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menu_toggle_background_hover',
                'label' => esc_html__('Background', 'happy-addons-pro'),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} .ha-menu-hamburger:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_toggle_border_hover',
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .ha-menu-hamburger:hover',
            ]
        );

        $this->add_control(
            'ha_menu_toggle_icon_color_hover',
            [
                'label' => esc_html__('Humber Icon Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-hamburger:hover .ha-menu-hamburger-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-menu-hamburger:hover > .ha-menu-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->add_control(
            'ha_menu_close_style_title',
            [
                'label' => esc_html__('Close Toggle', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ha_menu_close_typography',
                'label' => esc_html__('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'selector' => '{{WRAPPER}} .ha-menu-close',
            ]
        );

        $this->add_responsive_control(
            'ha_menu_close_spacing',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px',],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_menu_close_margin',
            [
                'label' => esc_html__('Margin', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px',],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'top' => '12',
                    'right' => '12',
                    'bottom' => '12',
                    'left' => '12',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_menu_close_width',
            [
                'label' => esc_html__('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 45,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-close' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_menu_close_border_radius',
            [
                'label' => esc_html__('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-close' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'ha_menu_close_normal_and_hover_tabs'
        );

        $this->start_controls_tab(
            'ha_menu_close_normal',
            [
                'label' => esc_html__('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menu_close_background',
                'label' => esc_html__('Background', 'happy-addons-pro'),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} .ha-menu-close',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_close_border',
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .ha-menu-close',
            ]
        );

        $this->add_control(
            'ha_menu_close_icon_color',
            [
                'label' => esc_html__('Humber Icon Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'default' => 'rgba(51, 51, 51, 1)',
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-close' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ha_menu_close_hover',
            [
                'label' => esc_html__('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ha_menu_close_background_hover',
                'label' => esc_html__('Background', 'happy-addons-pro'),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} .ha-menu-close:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_menu_close_border_hover',
                'label' => esc_html__('Border', 'happy-addons-pro'),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .ha-menu-close:hover',
            ]
        );

        $this->add_control(
            'ha_menu_close_icon_color_hover',
            [
                'label' => esc_html__('Humber Icon Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .ha-menu-close:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function __mobile_menu_logo_style_controls() {

        $this->start_controls_section(
            'ha_mobile_menu_logo_style_tab',
            [
                'label' => esc_html__('Mobile Menu Logo', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ha_mobile_menu_logo_width',
            [
                'label' => esc_html__('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 5,
                    ],
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 160,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 120,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-nav-logo > img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_mobile_menu_logo_height',
            [
                'label' => esc_html__('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 60,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-nav-logo > img' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_mobile_menu_logo_margin',
            [
                'label' => esc_html__('Margin', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'tablet_default' => [
                    'top' => '5',
                    'right' => '0',
                    'bottom' => '5',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-nav-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ha_mobile_menu_logo_padding',
            [
                'label' => esc_html__('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'tablet_default' => [
                    'top' => '5',
                    'right' => '5',
                    'bottom' => '5',
                    'left' => '5',
                    'unit' => 'px',
                    'isLinked' => 'true',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-nav-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $hamburger_icon_value = '';
        $hamburger_icon_type = '';
        if ($settings['ha_hamburger_icon'] != '' && $settings['ha_hamburger_icon']) {
            if ($settings['ha_hamburger_icon']['library'] !== 'svg') {
                $hamburger_icon_value = esc_attr($settings['ha_hamburger_icon']['value']);
                $hamburger_icon_type = esc_attr('icon');
            } else {
                $hamburger_icon_value = esc_url($settings['ha_hamburger_icon']['value']['url']);
                $hamburger_icon_type = esc_attr('url');
            }
        }

        // Responsive menu breakpoint
        $responsive_menu_breakpoint = '';
        if ($settings['responsive_breakpoint'] === 'ha_menu_responsive_tablet') {
            $responsive_menu_breakpoint = "1024";
        } else {
            $responsive_menu_breakpoint = "767";
        }

        if ($this->get_menus()) {
            $placeholder_msg = __("Please Select a Menu.", 'happy-addons-pro');
        } else {
            $placeholder_msg =  __('You don\'t have any menu created. Please create a new one from Here', 'happy-addons-pro');
        }

        if (ha_elementor()->editor->is_edit_mode() && $settings['nav_menu'] == '') : ?>
            <div class="ha-editor-placeholder">
                <h4 class="ha-editor-placeholder-title">
                    <?php esc_html_e('Happy Menu', 'happy-addons-pro'); ?>
                </h4>
                <div class="ha-editor-placeholder-content">
                    <?php echo $placeholder_msg; ?>
                </div>
            </div>
        <?php endif;

        echo '<div class="ha-wid-con ha-menu-nav-link-icon-position-' . $settings['ha_menu_icon_position'] . ' ' . $settings['responsive_breakpoint'] . '" data-hamburger-icon="' . $hamburger_icon_value . '" data-hamburger-icon-type="' . $hamburger_icon_type . '" data-responsive-breakpoint="' . $responsive_menu_breakpoint . '">';
        $this->render_raw();
        echo '</div>';
    }

    protected function render_raw() {
        $settings = $this->get_settings_for_display();

        /**
         * Hamburger Toggler Button
         */
        ?>
        <button class="ha-menu-hamburger ha-menu-toggler">
            <?php
            /**
             * Show Default Icon
             */
            if ($settings['ha_hamburger_icon']['value'] === '') :
            ?>
                <span class="ha-menu-hamburger-icon"></span><span class="ha-menu-hamburger-icon"></span><span class="ha-menu-hamburger-icon"></span>
            <?php
            endif;

            /**
             * Show Icon or, SVG
             */
            Icons_Manager::render_icon($settings['ha_hamburger_icon'], ['aria-hidden' => 'true', 'class' => 'ha-menu-icon']);
            ?>
        </button>
<?php

        if ($settings['nav_menu'] != '' && wp_get_nav_menu_items($settings['nav_menu']) !== false && count(wp_get_nav_menu_items($settings['nav_menu'])) > 0) {
            $link = $target = $nofollow = '';

            if (isset($settings['ha_nav_menu_logo_link_to']) && $settings['ha_nav_menu_logo_link_to'] == 'home') {
                $link = get_home_url();
            } elseif (isset($settings['ha_nav_menu_logo_link'])) {
                $link = $settings['ha_nav_menu_logo_link']['url'];
                $target = ($settings['ha_nav_menu_logo_link']['is_external'] != "on" ? "" : "_blank");
                $nofollow = ($settings['ha_nav_menu_logo_link']['nofollow'] != "on" ? "" : "nofollow");
            }

            $metadata = ha_img_meta($settings['ha_nav_menu_logo']['id']);

            ob_start();
            \Elementor\Icons_Manager::render_icon($settings['ha_hamburger_icon_close'], ['aria-hidden' => 'true']);
            $hm_close_icon = ob_get_clean();

            $markup = '
				<div class="ha-nav-identity-panel">
					<div class="ha-site-title">
						<a class="ha-nav-logo" href="' . $link . '" target="' . (!empty($target) ? $target : '_self') . '" rel="' . $nofollow . '">
							<img src="' . $settings['ha_nav_menu_logo']['url'] . '" alt="' . (isset($metadata['alt']) ? $metadata['alt'] : '') . '">
						</a>
					</div>
                    <button class="ha-menu-close ha-menu-toggler" type="button">' . $hm_close_icon . '</button>
				</div>
			';
            $args = [
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>' . $markup,
                'container'       => 'div',
                'container_id'    => 'ha-megamenu-' . $settings['nav_menu'],
                'container_class' => 'ha-menu-container ha-menu-offcanvas-elements ha-navbar-nav-default ha-nav-menu-one-page-' . $settings['one_page_enable'],
                'menu_id'         => 'main-menu',
                'menu'            => $settings['nav_menu'],
                'menu_class'      => 'ha-navbar-nav ' . $settings['horizontal_menu_position'] . ' submenu-click-on-' . $settings['submenu_click_area'],
                'depth'           => 4,
                'link_before'     => '<span class="menu-item-title">',
                'link_after'      => '</span>',
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',
                'walker'          => (class_exists('\Happy_Addons_Pro\Happy_Menu_Walker') ? new \Happy_Addons_Pro\Happy_Menu_Walker() : ''),
                'sub_indicator'   => $settings['ha_style_tab_submenu_item_indicator']
            ];

            wp_nav_menu($args);
        }
    }
}
