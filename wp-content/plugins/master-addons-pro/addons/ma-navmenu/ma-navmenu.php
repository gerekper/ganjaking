<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Controls\MA_Control_Visual_Select;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/29/19
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Master Mega Menu Addon
 */
class Nav_Menu extends Widget_Base
{

    public function get_name()
    {
        return 'ma-navmenu';
    }
    public function get_title()
    {
        return __('Nav Menu', MELA_TD);
    }

    public function get_categories()
    {
        return ['master-addons'];
    }

    public function get_icon()
    {
        return 'ma-el-icon eicon-nav-menu';
    }

    public function get_keywords()
    {
        return ['nav', 'navigation', 'menu', 'nav menu', 'header', 'footer', 'sidebar'];
    }


    public function get_script_depends()
    {
        return ['jltma-bootstrap'];
    }


    public function get_style_depends()
    {
        return ['jltma-bootstrap'];
    }


    public function get_help_url()
    {
        return 'https://master-addons.com/elementor-mega-menu/';
    }


    public function get_available_menus()
    {
        $menus = wp_get_nav_menus();
        $options = array();
        foreach ($menus as $menu) {
            $options[$menu->slug] = $menu->name;
        }
        return $options;
    }


    protected function _register_controls()
    {

        $this->start_controls_section(
            'jltma_content_tab',
            [
                'label' => esc_html__('Menu Settings', MELA_TD),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_responsive_control(
            'jltma_main_menu_type',
            [
                'label' => esc_html__('Layout Type', MELA_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'  => esc_html__('Default', MELA_TD),
                    'onepage' => esc_html__('One Page', MELA_TD)
                ],
            ]
        );

        $menus = $this->get_available_menus();
        if (!empty($menus)) {
            $this->add_control(
                'jltma_nav_menu',
                [
                    'label'     => esc_html__('Select Menu', MELA_TD),
                    'type'      => Controls_Manager::SELECT,
                    'options'   => $this->get_available_menus(),
                    'description' => sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', MELA_TD), admin_url('nav-menus.php')),
                ]
            );
        } else {
            $this->add_control(
                'jltma_nav_menu',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => sprintf(__('<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', MELA_TD), admin_url('nav-menus.php?action=edit&menu=0')),
                    'separator' => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );
        }

        $this->add_control(
            'jltma_main_menu_sticky',
            [
                'label' => esc_html__('Sticky Navbar? ', MELA_TD),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => esc_html__('Yes', MELA_TD),
                'label_off' => esc_html__('No', MELA_TD)
            ]
        );


        $this->add_responsive_control(
            'jltma_main_menu_sticky_type',
            [
                'label' => esc_html__('Sticky Type', MELA_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'               => esc_html__('Default', MELA_TD),
                    'sticky-top'            => esc_html__('On Scroll Sticky', MELA_TD),
                    'smart-scroll'          => esc_html__('Smart Scroll', MELA_TD),
                    'fixed-onscroll'        => esc_html__('Fixed On Scroll', MELA_TD),
                    'nav-fixed-top'         => esc_html__('Fixed Top', MELA_TD),
                ],
                'condition' => array(
                    'jltma_main_menu_sticky' => 'yes'
                )
            ]
        );


        $this->add_control(
            'jltma_main_menu_sticky_id',
            array(
                'label'       => __('Container ID', MELA_TD),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'Place the same ID here',
                'description' => __('Check how to Place ID.', MELA_TD),
                'label_block' => true,
                'condition' => array(
                    'jltma_main_menu_sticky' => 'yes'
                )
            )
        );


        $this->add_control(
            'jltma_menu_layout_type',
            array(
                'label'       => __('Layout Type', MELA_TD),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'horizontal',
                'options'     => array(
                    'horizontal' => __('Horizontal', MELA_TD),
                    'vertical'   => __('Vertical', MELA_TD),
                    'burger'     => __('Burger', MELA_TD)
                ),
                'condition' => array(
                    'jltma_nav_menu!' => ''
                )
            )
        );



        $this->add_control(
            'jltma_main_menu_position',
            [
                'label'         => __('Menu Alignment', MELA_TD),
                'type'          => Controls_Manager::CHOOSE,
                'default'       => 'flex-end',
                'options'       => [
                    'flex-start'  => [
                        'title' => __('Left', MELA_TD),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center'    => [
                        'title' => __('Center', MELA_TD),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'flex-end'     => [
                        'title' => __('Right', MELA_TD),
                        'icon'  => 'fa fa-align-right',
                    ],
                    // 'space-around'     => [
                    //     'title' => __( 'Justify', MELA_TD ),
                    //     'icon'  => 'fa fa-align-justify',
                    // ],

                ],
                'condition' => [
                    'jltma_nav_menu!' => '',
                    'jltma_menu_layout_type' => 'horizontal'
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-menu-container .jltma-navbar-nav-default' => 'justify-content: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'jltma_menu_trigger_effect',
            [
                'label' => esc_html__('Menu Trigger', MELA_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'hover',
                'options' => [
                    'hover'  => esc_html__('Hover', MELA_TD),
                    'click' => esc_html__('Click', MELA_TD)
                ],
            ]
        );

        $this->add_control(
            'jltma_submenu_indicator',
            [
                'label' => esc_html__('Submenu Indicator', MELA_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'small-down',
                'options' => [
                    'medium-up'      => esc_html__('Medium Up', MELA_TD),
                    'medium-down'    => esc_html__('Medium Down', MELA_TD),
                    'medium-left'    => esc_html__('Medium Left', MELA_TD),
                    'medium-right'   => esc_html__('Medium Right', MELA_TD),

                    'small-up'      => esc_html__('Small Up', MELA_TD),
                    'small-down'    => esc_html__('Small Down', MELA_TD),
                    'small-left'    => esc_html__('Small Left', MELA_TD),
                    'small-right'   => esc_html__('Small Right', MELA_TD),

                    'h-small-up'      => esc_html__('H Small Up', MELA_TD),
                    'h-small-down'    => esc_html__('H Small Down', MELA_TD),
                    'h-small-left'    => esc_html__('H Small Left', MELA_TD),
                    'h-small-right'   => esc_html__('H Small Right', MELA_TD),

                    'thick-plus'      => esc_html__('Thick Plus', MELA_TD),
                    'large-plus'      => esc_html__('Large Plus', MELA_TD),
                    'medium-plus'     => esc_html__('Medium Plus', MELA_TD),
                    'small-plus'      => esc_html__('Small Plus', MELA_TD),
                ],
            ]
        );


        $this->add_control(
            'jltma_menu_splitter',
            array(
                'label'        => __('Display Menu Splitter', MELA_TD),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('On', MELA_TD),
                'label_off'    => __('Off', MELA_TD),
                'return_value' => 'yes',
                'default'      => 'Off',
                'separator'    => 'before',
                'condition' => array(
                    'jltma_menu_layout_type' => 'horizontal'
                )
            )
        );


        $this->end_controls_section();





        /*
            * Menu Item Style
            */
        $this->start_controls_section(
            'jltma_mobile_menu',
            [
                'label' => esc_html__('Mobile Menu', MELA_TD)
            ]
        );


        $this->add_control(
            'jltma_nav_menu_logo',
            [
                'label' => esc_html__('Choose Mobile Menu Logo', MELA_TD),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'jltma_nav_menu_logo_link_to',
            [
                'label' => esc_html__('Link', MELA_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'home',
                'options' => [
                    'home' => esc_html__('Home', MELA_TD),
                    'custom' => esc_html__('Custom URL', MELA_TD),
                ],
            ]
        );

        $this->add_control(
            'jltma_nav_menu_logo_link',
            [
                'label' => esc_html__('Link', MELA_TD),
                'type' => Controls_Manager::URL,
                'separator' => 'before',
                'placeholder' => 'https://master-addons.com',
                'dynamic'   => ['active' => true],
                'condition' => [
                    'jltma_nav_menu_logo_link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $this->end_controls_section();







        /*
            * Menu Item Style
            */
        $this->start_controls_section(
            'jltma_hamburger_menu',
            [
                'label' => esc_html__('Hamburger Menu', MELA_TD)
            ]
        );

        $this->add_control(
            'jltma_display_burger',
            array(
                'label'       => __('Display Burger Toggle on', MELA_TD),
                'type'        => Controls_Manager::SELECT,
                'default'     => '768',
                'options'     => array(
                    '1025'   => __('Desktop', MELA_TD),
                    '1024'   => __('Tablet', MELA_TD),
                    '768'    => __('Mobile', MELA_TD),
                    'custom' => __('Custom', MELA_TD)
                ),
                'condition' => array(
                    'jltma_nav_menu!' => ''
                )
            )
        );



        $this->add_control(
            'jltma_burger_menu_position',
            [
                'label'         => __('Menu Alignment', MELA_TD),
                'type'          => Controls_Manager::CHOOSE,
                'default'       => 'flex-end',
                'options'       => [
                    'jltma-row'  => [
                        'title' => __('Left', MELA_TD),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'column'    => [
                        'title' => __('Center', MELA_TD),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'jltma-row-reverse'     => [
                        'title' => __('Right', MELA_TD),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'condition' => [
                    'jltma_display_burger' => '1025'
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-menu-container nav' => 'flex-direction: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'jltma_breakpoint',
            array(
                'label'      => __('BreakPoint', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => array(
                        'min'  => 1,
                        'max'  => 1600,
                        'step' => 1
                    )
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 768,
                ),
                // 'separator'    => 'before',
                'condition' => array(
                    // 'jltma_menu_layout_type!'           => 'burger',
                    'jltma_display_burger'              => 'custom'
                )
            )
        );

        $this->add_control(
            'jltma_burger_menu_location',
            array(
                'label'       => __('Burger Menu Location', MELA_TD),
                'type'        => 'jltma-visual-select',
                'style_items' => 'max-width:45%;',
                'options'     => array(
                    'toggle-bar' => array(
                        'label'  => __('Expandable Under Top Header', MELA_TD),
                        'image'  => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-menu-location-1.svg'
                    ),
                    'overlay'    => array(
                        'label'  => __('FullScreen on Entire Page', MELA_TD),
                        'image'  => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-menu-location-3.svg'
                    ),
                    'offcanvas'  => array(
                        'label'  => __('Offcanvas Panel', MELA_TD),
                        'image'  => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-menu-location-2.svg'
                    )
                ),
                'default'     => 'toggle-bar',
                'seperator'   => 'before'
            )
        );

        $this->add_control(
            'jltma_offcanvas_align',
            array(
                'label'       => __('Offcanvas Alignment', MELA_TD),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'left',
                'options'     => array(
                    'left'  => __('Left', MELA_TD),
                    'right' => __('Right', MELA_TD)
                ),
                'condition' => array(
                    'jltma_burger_menu_location' => 'offcanvas'
                )
            )
        );


        $this->add_control(
            'jltma_burger_type',
            array(
                'label'   => __('Burger Type', MELA_TD),
                'type'    => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => array(
                    'default'   => __('Default', MELA_TD),
                    'custom'   => __('Custom', MELA_TD),
                ),
            )
        );

        $this->start_controls_tabs(
            'burger_color',
            array(
                'condition' => array(
                    'jltma_burger_type' => 'default'
                )
            )
        );

        $this->start_controls_tab(
            'burger_color_normal',
            array(
                'label'     => __('Normal', MELA_TD)
            )
        );

        $this->add_control(
            'burger_btn_color',
            array(
                'label' => __('Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .jltma-burger:before,  {{WRAPPER}} .jltma-burger:after, {{WRAPPER}} .jltma-burger .mid-line' => 'border-color: {{VALUE}} !important;',
                ),
                'seperartor' => 'after'
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'burger_color_hover',
            array(
                'label'     => __('Hover', MELA_TD)
            )
        );

        $this->add_control(
            'burger_btn_hover_color',
            array(
                'label' => __('Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .jltma-burger:hover:before,  {{WRAPPER}} .jltma-burger:hover:after, {{WRAPPER}} .jltma-burger:hover .mid-line' => 'border-color: {{VALUE}} !important;',
                ),
                'seperartor' => 'after'
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'burger_btn_style',
            array(
                'label'       => __('Burger Button Style', MELA_TD),
                'type'        => 'jltma-visual-select',
                'style_items' => 'max-width:45%;',
                'options'     => array(
                    'jltma-lite-large'     => array(
                        'label'          => __('Expandable under top header', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-lite-large.svg'
                    ),
                    'jltma-regular-large'  => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-regular-large.svg'
                    ),
                    'jltma-thick-large'    => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-thick-large.svg'
                    ),
                    'jltma-lite-medium'    => array(
                        'label'          => __('FullScreen on entire page', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-lite-medium.svg'
                    ),
                    'jltma-regular-medium' => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-regular-medium.svg'
                    ),
                    'jltma-thick-medium'   => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-thick-medium.svg'
                    ),
                    'jltma-lite-small'     => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-lite-small.svg'
                    ),
                    'jltma-regular-small'  => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-regular-small.svg'
                    ),
                    'jltma-thick-small'    => array(
                        'label'          => __('Offcanvas panel', MELA_TD),
                        'image'          => MELA_PLUGIN_URL . '/assets/images/visual-select/burger-thick-small.svg'
                    )
                ),
                'default'     => 'jltma-lite-small',
                'seperator'   => 'before',
                'condition' => array(
                    'jltma_burger_type' => 'default'
                )
            )
        );

        $this->add_control(
            'burger_custom',
            array(
                'label'       => '',
                'type'        => Controls_Manager::CODE,
                'default'     => '',
                'placeholder' => __('Enter inline SVG content here', MELA_TD),
                'show_label'  => false,
                'condition' => array(
                    'jltma_burger_type' => 'custom'
                )
            )
        );

        $this->add_control(
            'jltma_burger_toggle_type',
            array(
                'label'       => __('Burger Menu Toggle Type', MELA_TD),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'toggle',
                'options'     => array(
                    'toggle'    => __('Toggle', MELA_TD),
                    'accordion' => __('Accordion', MELA_TD)
                ),
            )
        );


        $this->end_controls_section();



        /*
            * Main Menu Style
            */
        $this->start_controls_section(
            'jltma_style_main_menu',
            [
                'label' => esc_html__('Menu Style', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'jltma_menubar_height',
            [
                'label' => esc_html__('Menu Height', MELA_TD),
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
                'devices' => ['desktop', 'tablet'],
                'desktop_default' => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-menu-container nav' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'jltma_menu_layout_type!' => 'vertical'
                ]
            ]
        );

        // $this->add_responsive_control(
        //     'jltma_border_radius',
        //     [
        //         'label' => esc_html__( 'Border radius', MELA_TD ),
        //         'type' => Controls_Manager::DIMENSIONS,
        //         'devices' => [ 'desktop', 'tablet' ],
        //         'size_units' => [ 'px' ],
        //         'desktop_default' => [
        //             'top' => 0,
        //             'right' => 0,
        //             'bottom' => 0,
        //             'left' => 0,
        //             'unit' => 'px',
        //         ],
        //         'tablet_default' => [
        //             'top' => 0,
        //             'right' => 0,
        //             'bottom' => 0,
        //             'left' => 0,
        //             'unit' => 'px',
        //         ],
        //         'selectors' => [
        //             '{{WRAPPER}} .jltma-menu-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        //         ],
        //     ]
        // );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_menubar_background',
                'label' => esc_html__('Background', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .jltma-menu-container',
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_panel_background',
            [
                'label' => esc_html__('Background Color(Tablet)', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'tablet_default' => '',
                'devices' => ['tablet'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-menu-container' => 'background-image: linear-gradient(180deg, {{VALUE}} 0%, {{VALUE}} 100%);',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_panel_spacing',
            [
                'label' => esc_html__('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'tablet_default' => [
                    'top' => '10',
                    'right' => '0',
                    'bottom' => '10',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'devices' => ['tablet'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-nav-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_panel_width',
            [
                'label' => esc_html__('Width', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'devices' => ['tablet'],
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
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-menu-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();





        /*
            * Menu Item Style
            */
        $this->start_controls_section(
            'jltma_style_tab_menuitem',
            [
                'label' => esc_html__('Menu Item', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'jltma_content_typography',
                'label' => esc_html__('Typography', MELA_TD),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .jltma-navbar-nav > li > a',
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_item_spacing',
            [
                'label' => esc_html__('Inner Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'devices' => ['desktop', 'tablet'],
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
                    '{{WRAPPER}} .jltma-navbar-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_item_gap',
            [
                'label' => esc_html__('Item Gap', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'devices' => ['desktop', 'tablet'],
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
                    '{{WRAPPER}} .jltma-navbar-nav > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'jltma_menu_item_padding',
            [
                'label' => esc_html__('Item Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_item_icon_spacing',
            [
                'label' => esc_html__('Menu Icon Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav li a .jltma-menu-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_item_border_radius',
            [
                'label' => esc_html__('Border Radius', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'all',
                'size_units' => array('px', 'em', '%'),
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav > li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('jltma_nav_menu_tabs');

        // Normal
        $this->start_controls_tab(
            'jltma_nav_menu_normal_tab',
            [
                'label' => esc_html__('Normal', MELA_TD),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_item_background',
                'label' => esc_html__('Item background', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .jltma-navbar-nav > li',
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_text_color',
            [
                'label' => esc_html__('Item Text Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'desktop_default' => '#000000',
                'tablet_default' => '#000000',
                'devices' => ['desktop', 'tablet'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav > li > a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'jltma_menu_item_box_shadow',
                'label' => esc_html__('Box Shadow', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav > li',
            ]
        );

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab(
            'jltma_nav_menu_hover_tab',
            [
                'label' => esc_html__('Hover', MELA_TD),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_item_background_hover',
                'label' => esc_html__('Item background', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .jltma-navbar-nav > li:hover, {{WRAPPER}} .jltma-navbar-nav > li:focus,
                                        {{WRAPPER}} .jltma-navbar-nav > li:active, {{WRAPPER}} .jltma-navbar-nav > li:hover',
            ]
        );

        $this->add_responsive_control(
            'jltma_item_color_hover',
            [
                'label' => esc_html__('Text Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'devices' => ['desktop', 'tablet'],
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav > li > a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li > a:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li:hover > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li:hover > a .jltma-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li > a:hover .jltma-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li > a:focus .jltma-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav > li > a:active .jltma-submenu-indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'jltma_menu_item_hover_box_shadow',
                'label' => esc_html__('Box Shadow', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav > li:hover',
            ]
        );

        $this->end_controls_tab();

        // active
        $this->start_controls_tab(
            'jltma_nav_menu_active_tab',
            [
                'label' => esc_html__('Active', MELA_TD),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'        => 'jltma_nav_menu_active_bg_color',
                'label'     => esc_html__('Item background', MELA_TD),
                'types'        => ['classic', 'gradient'],
                'selector'    => '{{WRAPPER}} .jltma-navbar-nav-default > li.current-menu-item > a,{{WRAPPER}} .jltma-navbar-nav-default > li.current-menu-ancestor > a'
            ]
        );

        $this->add_responsive_control(
            'jltma_nav_menu_active_text_color',
            [
                'label' => esc_html__('Text Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'devices' => ['desktop', 'tablet'],
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav-default > li.current-menu-item > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav-default > li.current-menu-ancestor > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav-default > li.current-menu-ancestor > a .jltma-submenu-indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'jltma_menu_item_active_box_shadow',
                'label' => esc_html__('Box Shadow', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav > li.current-menu-item',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();



        /*
            * Sub Menu
            */
        $this->start_controls_section(
            'jltma_style_tab_submenu_panel',
            [
                'label' => esc_html__('Sub Menu', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_responsive_control(
            'jltma_submenu_container_width',
            [
                'label' => esc_html__('Container Width', MELA_TD),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'devices' => ['desktop', 'tablet'],
                'desktop_default' => [
                    'size' => 250,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 250,
                    'unit' => 'px'
                ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu' => 'min-width: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        // $this->add_control(
        //     'jltma_submenu_animation',
        //     [
        //         'label' => __( 'Submenu Animation Effect', MELA_TD ),
        //         'type' => \Elementor\Controls_Manager::ANIMATION
        //     ]
        // );

        $this->add_responsive_control(
            'jltma_submenu_animation',
            [
                'label' => esc_html__('Submenu Animation Effect', MELA_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'       => esc_html__('Default', MELA_TD),
                    'fade-up'       => esc_html__('Fade Up', MELA_TD),
                    'fade-down'     => esc_html__('Fade Down', MELA_TD)
                ],
            ]
        );

        $this->start_controls_tabs('jltma_sub_background_tab');

        $this->start_controls_tab(
            'jltma_sub_bg_normal',
            array(
                'label' => __('Normal', MELA_TD)
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_submenu_container_background',
                'label' => esc_html__('Container Background', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'jltma_panel_box_shadow',
                'label' => esc_html__('Box Shadow', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu',
            ]
        );

        $this->add_responsive_control(
            'jltma_submenu_panel_border_radius',
            [
                'label' => esc_html__('Border Radius', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'all',
                'size_units' => array('px', 'em', '%'),
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_submenu_panel_padding',
            [
                'label' => esc_html__('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'all',
                'size_units' => array('px', 'em', '%'),
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'sub_bg_hover',
            array(
                'label' => __('Hover', MELA_TD)
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_submenu_hover_bg',
                'label' => esc_html__('Background', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'jltma_submenu_panel_hover_box_shadow',
                'label' => esc_html__('Box Shadow', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav-default .jltma-sub-menu:hover',
            ]
        );


        $this->add_responsive_control(
            'jltma_submenu_panel_hover_border_radius',
            [
                'label' => esc_html__('Border Radius', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'all',
                'size_units' => array('px', 'em', '%'),
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'jltma_panel_hover_submenu_border',
                'label' => esc_html__('Panel Menu Border', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu:hover',
            ]
        );


        $this->add_responsive_control(
            'jltma_submenu_panel_hover_padding',
            [
                'label' => esc_html__('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'all',
                'size_units' => array('px', 'em', '%'),
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();





        $this->end_controls_section();



        /*
            * Sub Menu Item
            */
        $this->start_controls_section(
            'jltma_style_tab_submenu_item',
            [
                'label' => esc_html__('Sub Menu Item', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'jltma_style_tab_submenu_indicator_color',
            [
                'label' => esc_html__('Indicator color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'devices' => ['desktop', 'tablet'],
                'default' =>  '#000000',
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav > li > a .jltma-submenu-indicator' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'jltma_submenu_indicator!' => 'jltma_none'
                ]
            ]
        );
        $this->add_responsive_control(
            'jltma_submenu_indicator_spacing',
            [
                'label' => esc_html__('Indicator Margin', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav-default .jltma-menu-has-children>a .jltma-submenu-indicator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'jltma_submenu_indicator!' => 'jltma_none'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'jltma_menu_item_typography',
                'label' => esc_html__('Typography', MELA_TD),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a',
            ]
        );

        $this->add_responsive_control(
            'jltma_submenu_item_spacing',
            [
                'label' => esc_html__('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'devices' => ['desktop', 'tablet'],
                'desktop_default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'jltma_submenu_active_hover_tabs'
        );
        $this->start_controls_tab(
            'jltma_submenu_normal_tab',
            [
                'label'    => esc_html__('Normal', MELA_TD)
            ]
        );

        $this->add_responsive_control(
            'jltma_submenu_item_color',
            [
                'label' => esc_html__('Item text color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'devices' => ['desktop', 'tablet'],
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a' => 'color: {{VALUE}}',
                ],

            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_menu_item_background',
                'label' => esc_html__('Item background', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'jltma_submenu_hover_tab',
            [
                'label'    => esc_html__('Hover', MELA_TD)
            ]
        );

        $this->add_responsive_control(
            'jltma_item_text_color_hover',
            [
                'label' => esc_html__('Text Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'devices' => ['desktop', 'tablet'],
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li:hover > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_menu_item_background_hover',
                'label' => esc_html__('Item background (hover)', MELA_TD),
                'types' => ['classic', 'gradient'],
                'selector' => '
                        {{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a:hover,
                        {{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a:focus,
                        {{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a:active,
                        {{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li:hover,
                        {{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li:hover > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'jltma_submenu_active_tab',
            [
                'label'    => esc_html__('Active', MELA_TD)
            ]
        );

        $this->add_responsive_control(
            'jltma_nav_sub_menu_active_text_color',
            [
                'label' => esc_html__('Item text color (Active)', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'devices' => ['desktop', 'tablet'],
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li.current-menu-item > a' => 'color: {{VALUE}} !important'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'        => 'jltma_nav_sub_menu_active_bg_color',
                'label'     => esc_html__('Item background (Active)', MELA_TD),
                'types'        => ['classic', 'gradient'],
                'selector'    => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li.current-menu-item > a',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'jltma_menu_item_border_heading',
            [
                'label' => esc_html__('Sub Menu Items Border', MELA_TD),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'jltma_menu_item_border',
                'label' => esc_html__('Border', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li > a',
            ]
        );

        $this->add_control(
            'jltma_menu_item_border_last_child_heading',
            [
                'label' => esc_html__('Border Last Child', MELA_TD),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'jltma_menu_item_border_last_child',
                'label' => esc_html__('Border last Child', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li:last-child > a',
            ]
        );

        $this->add_control(
            'jltma_menu_item_border_first_child_heading',
            [
                'label' => esc_html__('Border First Child', MELA_TD),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'jltma_menu_item_border_first_child',
                'label' => esc_html__('Border First Child', MELA_TD),
                'selector' => '{{WRAPPER}} .jltma-navbar-nav .jltma-sub-menu > li:first-child > a,
                        {{WRAPPER}} .navbar-soft .dropdown-menu',
            ]
        );

        $this->end_controls_section();



        /*
                * Mobile Menu Logo
                */

        $this->start_controls_section(
            'jltma_mobile_menu_logo_style_tab',
            [
                'label' => esc_html__('Mobile Logo', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_logo_width',
            [
                'label' => esc_html__('Width', MELA_TD),
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
                    '{{WRAPPER}} .jltma-nav-logo > img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_logo_height',
            [
                'label' => esc_html__('Height', MELA_TD),
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
                    '{{WRAPPER}} .jltma-nav-logo > img' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_logo_margin',
            [
                'label' => esc_html__('Margin', MELA_TD),
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
                    '{{WRAPPER}} .jltma-nav-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_mobile_menu_logo_padding',
            [
                'label' => esc_html__('Padding', MELA_TD),
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
                    '{{WRAPPER}} .jltma-nav-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section(
            'jltma_menu_toggle_style_tab',
            [
                'label' => esc_html__('Hamburger Style', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'jltma_menu_toggle_style_title',
            [
                'label' => esc_html__('Hamburger Toggle', MELA_TD),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_toggle_spacing',
            [
                'label' => esc_html__('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px',],
                'devices' => ['tablet'],
                'tablet_default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-burger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_toggle_width',
            [
                'label' => esc_html__('Width', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 45,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'devices' => ['tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-burger' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_menu_toggle_border_radius',
            [
                'label' => esc_html__('Border Radius', MELA_TD),
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
                'devices' => ['tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-burger' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'jltma_menu_toggle_normal_and_hover_tabs'
        );

        $this->start_controls_tab(
            'jltma_menu_toggle_normal',
            [
                'label' => esc_html__('Normal', MELA_TD),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_menu_toggle_background',
                'label' => esc_html__('Background', MELA_TD),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} .jltma-burger',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'jltma_menu_toggle_border',
                'label' => esc_html__('Border', MELA_TD),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .jltma-burger',
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'jltma_menu_toggle_hover',
            [
                'label' => esc_html__('Hover', MELA_TD),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'jltma_menu_toggle_background_hover',
                'label' => esc_html__('Background', MELA_TD),
                'types' => ['classic'],
                'selector' => '{{WRAPPER}} .jltma-burger:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'jltma_menu_toggle_border_hover',
                'label' => esc_html__('Border', MELA_TD),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .jltma-burger:hover',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();




        /**
         * Content Tab: Docs Links
         */
        $this->start_controls_section(
            'jltma_section_help_docs',
            [
                'label' => esc_html__('Help Docs', MELA_TD),
            ]
        );


        $this->add_control(
            'help_doc_1',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/elementor-mega-menu/" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_2',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/navigation-menu/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_3',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=WhA5YnE4yJg" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );
        $this->end_controls_section();




        //Upgrade to Pro
       
    }


    protected function render()
    {
        echo '<div class="jltma-menu-container">';
        $this->render_raw();
        echo '</div>';
    }
    protected function render_raw()
    {
        $settings = $this->get_settings_for_display();

        $breakpoint = 'custom' === $settings['jltma_display_burger'] ? $settings['jltma_breakpoint']['size'] : $settings['jltma_display_burger'];
        $jltma_one_page_enable =  ($settings['jltma_main_menu_type'] == 'onepage') ? "enabled" : "disabled";

        $jltma_offcanvas_align = $settings['jltma_offcanvas_align'] ? $settings['jltma_offcanvas_align'] : "";


        if (!isset($settings['jltma_nav_menu'])) {
            return _e('There are no menus in your site.', MELA_TD);
        }

        $offcanvas_output  = '';
        $fullscreen_output = '';
        $toggle_bar_output = '';

        $indicator  = $settings['jltma_submenu_indicator'];
        $splitter  = Master_Addons_Helper::jltma_is_true($settings['jltma_menu_splitter']);

        $menu_class  = '';
        $menu_class .= $settings['jltma_submenu_animation'] ? $settings['jltma_submenu_animation'] : '';

        $splitter_class = $splitter ? ' jltma-with-splitter' : '';

        if ($settings['jltma_nav_menu'] != '' && wp_get_nav_menu_items($settings['jltma_nav_menu']) !== false && count(wp_get_nav_menu_items($settings['jltma_nav_menu'])) > 0) {

            $this->add_render_attribute('menu_link', 'class', ['jltma-nav-logo', 'navbar-brand', 'jltma-col-xs-3', 'jltma-col-sm-3']);

            if ($settings['jltma_nav_menu_logo_link_to'] == 'home') {

                $this->add_render_attribute('menu_link', 'href', get_home_url());
            } else {

                if (!empty($settings['jltma_nav_menu_logo_link']['url'])) {
                    $this->add_render_attribute('menu_link', 'href', $settings['jltma_nav_menu_logo_link']['url']);

                    if ($settings['jltma_nav_menu_logo_link']['is_external']) {
                        $this->add_render_attribute('menu_link', 'target', '_blank');
                    }

                    if ($settings['jltma_nav_menu_logo_link']['nofollow']) {
                        $this->add_render_attribute('menu_link', 'rel', 'nofollow');
                    }
                }
            }

            // Device Widths
            if ($settings['jltma_display_burger'] == "1025") {
                $offcanvas_type = 'desktop';
            } elseif ($settings['jltma_display_burger'] == "1024") {
                $offcanvas_type = 'tablet';
            } elseif ($settings['jltma_display_burger'] == "768") {
                $offcanvas_type = 'mobile';
            } else {
                $offcanvas_type = 'custom';
            }

            $menu_logo = '<div class="jltma-nav-panel ' . $offcanvas_type . '">
                                <a ' . $this->get_render_attribute_string('menu_link') . '>
                                    <img src="' . $settings['jltma_nav_menu_logo']['url'] . '" alt="" >
                                </a>
                                <button
                                    class="navbar-toggler ml-md-0 mr-auto py-3"
                                    type="button"
                                    data-toggle="collapse"
                                    data-trigger="#' . 'jltma-nav-menu-elementor-' . $this->get_id() . '"
                                    data-target="#' . 'jltma-nav-menu-elementor-' . $this->get_id() . '"
                                    aria-expanded="false"
                                    aria-label="Toggle Navigation">
                                    <div class="jltma-burger ' . $settings['burger_btn_style'] . '"><span class="mid-line"></span></div>
                                </button>
                                <button class="jltma-close ' . $jltma_offcanvas_align . '"> X </button>
                            </div>';



            // <button class="jltma-close jltma-menu-toggler" type="button">X</button>

            // $this->add_render_attribute(
            //     'jltma_nav_menu_wrapper',
            //     [
            //         'class' => [ 'ma-el-image-carousel' ],
            //         // 'data-carousel-nav' => $settings['ma_el_image_carousel_nav'],
            //         // 'data-slidestoshow' => $settings['ma_el_image_carousel_per_view'],
            //         // 'data-slidestoscroll' => $settings['ma_el_image_carousel_slides_to_scroll'],
            //         // 'data-speed' => $settings['ma_el_image_carousel_transition_duration'],
            //     ]
            // );



            $mobile_menu_target = '.elementor-element-' . $this->get_id();

            switch ($settings['jltma_burger_menu_location']) {
                case 'overlay':
                    $fullscreen_output  = '<section class="jltma-fs-popup jltma-fs-menu-layout-center jltma-indicator">';
                    $fullscreen_output .= '<div class="jltma-panel-close"><div class="jltma-close jltma-cross-symbol jltma-thick-medium"></div></div>';
                    $fullscreen_output .= '<div class="jltma-fs-menu"></div>';
                    $fullscreen_output .= '</section>';
                    break;

                case 'offcanvas':
                    $offcanvas_output = '<button class="jltma-close float-right"> X </button>';
                    break;

                case 'toggle-bar':
                    $toggle_bar_output = '<div class="offcanvas-header mt-3">
                        <button class="jltma-btn jltma-btn-outline-danger jltma-close float-right"> X </button>
                        <h5 class="py-2 text-white">Main navbar</h5>
                    </div>';
                    break;

                default:
            }

            $sticky_nav = $settings['jltma_main_menu_sticky_type'] ? $settings['jltma_main_menu_sticky_type'] : "not-sticky";



            printf(
                '<nav
                    id="jltma-nav-menu-element-%2$s"
                    class="jltma-elementor-header jltma-nav-menu-element navbar navbar-expand-md navbar-soft %1$s %5$s %3$s %7$s %10$s"
                    data-menu-id="jltma-nav-menu-element-%2$s"
                    data-sticky-type="%1$s"
                    data-menu-layout="%9$s"
                    data-menu-trigger="%3$s"
                    data-menu-offcanvas="%4$s"
                    data-menu-toggletype="%6$s"
                    data-menu-animation="%8$s"
                    data-menu-container-id="%10$s">',
                $sticky_nav,
                $this->get_id(),
                $settings['jltma_menu_trigger_effect'],
                $settings['jltma_burger_menu_location'],
                $offcanvas_type,
                $settings['jltma_burger_toggle_type'],
                $settings['jltma_menu_layout_type'],
                $menu_class,
                $settings['jltma_main_menu_type'],
                $settings['jltma_main_menu_sticky_id']
            );

            // echo $toggle_bar_output;

            // echo '<div class="jltma-burger-container">';
            echo $menu_logo;
            // echo $offcanvas_output;
            //     if ( 'default' === $settings['jltma_burger_type'] ) {
            //         $burger_content = '<div class="jltma-burger ' . $settings['burger_btn_style'] . '"><span class="mid-line"></span></div>';
            //     } else {
            //         $burger_content = '<div class="jltma-burger jltma-custom-burger">' . $settings['burger_custom'] . '</div>';
            //     }

            //     $burger_btn_output = printf( '<div class="jltma-burger-box %s" data-target-content="%s">%s</div>',
            //         $settings['jltma_burger_menu_location'],
            //         '.elementor-element-' . $this->get_id() . ' .jltma-master-menu',
            //         $burger_content
            //     );
            // echo '</div>';

            $args = [
                'items_wrap'      => '<ul id="%1$s" class="navbar-nav %2$s">%3$s</ul>',
                'container'       => 'div',
                'container_id'    => 'jltma-nav-menu-elementor-' . $this->get_id(),
                // 'container_class' => 'jltma-menu-offcanvas-elements collapse navbar-collapse jltma-navbar-nav-default ' . $indicator . ' jltma-one-page-' . $jltma_one_page_enable .' '. $settings['jltma_burger_menu_location'] .' '.$jltma_offcanvas_align,

                'container_class' => 'jltma-menu-offcanvas-elements jltma-navbar-nav-default ' . $offcanvas_type . '-offcanvas collapse navbar-collapse jltma-svg-arrow jltma-' . $indicator . ' jltma-one-page-' . $jltma_one_page_enable . ' ' . $settings['jltma_burger_menu_location'] . ' ' . $jltma_offcanvas_align,

                // 'container_class' => 'jltma-menu-offcanvas-elements jltma-navbar-nav-default collapse navbar-collapse ' . $indicator . ' jltma-one-page-' . $jltma_one_page_enable .' '. $settings['jltma_burger_menu_location'] .' '.$jltma_offcanvas_align,

                'menu_id'         => 'jltma-main-menu',
                'menu'               => $settings['jltma_nav_menu'],
                'menu_class'      => 'jltma-navbar-nav jltma-menu-position-' . $settings['jltma_main_menu_position'] . $splitter_class . ' ' . $settings['jltma_burger_toggle_type'],
                'depth'           => 4,
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',

                'walker'          => (class_exists('\JLTMA_Megamenu_Nav_Walker') ? new \JLTMA_Megamenu_Nav_Walker() : '')
            ];

            wp_nav_menu($args);

            echo '</nav>';


            echo $fullscreen_output;

            if (($offcanvas_type == "desktop") && (($settings['jltma_burger_menu_location'] == 'offcanvas') && ($jltma_offcanvas_align == "left"))) {
                echo '<style>
                    .desktop .navbar-nav.jltma-navbar-nav{
                        flex-direction: column;
                        overflow: hidden;
                    }

                    body.offcanvas-active{
                        overflow:hidden;
                    }

                    .screen-overlay {
                      width:0%;
                      height: 100%;
                      z-index: 30;
                      position: fixed;
                      top: 0;
                      left: 0;
                      opacity:0;
                      visibility:hidden;
                      background-color: rgba(34, 34, 34, 0.6);
                      transition:opacity .2s linear, visibility .1s, width 1s ease-in;
                       }
                    .screen-overlay.show {
                        transition:opacity .5s ease, width 0s;
                        opacity:1;
                        width:100%;
                        visibility:visible;
                    }

                    .offcanvas{
                        width:350px;
                        visibility: hidden;
                        transform:translateX(-100%);
                        transition:all .2s;
                        border-radius:0;
                        display:block;
                        position: fixed;
                        top: 0;
                        left: 0;
                        height: 100%;
                        z-index: 1200;
                        background-color: #fff;
                        overflow: hidden;
                        border: 1px solid #eee;
                        padding-right: 2rem;
                    }

                    .offcanvas.offcanvas-right {
                       right: 0;
                       left: auto;
                       transform: translateX(100%);
                     }

                    .offcanvas.show{
                        visibility: visible;
                        transform: translateX(0);
                        transition: transform .2s;
                    }

                    .offcanvas .jltma-close{ position: absolute; right:15px; top:15px; }

                    </style>';
            }

            if (($offcanvas_type == "desktop" || $offcanvas_type == "tablet") && (($settings['jltma_burger_menu_location'] == 'offcanvas') && ($jltma_offcanvas_align == "right"))) {
                echo '<style>
                        .desktop .navbar-nav.jltma-navbar-nav{
                            flex-direction: column;
                            overflow: hidden;
                        }

                        body.offcanvas-active{
                            overflow:hidden;
                        }
                        .elementor-element-' . $breakpoint . ' .jltma-nav-menu-element .offcanvas-overlay {
                            width:0%;
                            height: 100%;
                            z-index: 30;
                            position: fixed;
                            top: 0;
                            left: 0;
                            opacity:0;
                            visibility:hidden;
                            background-color: black;
                            transition:opacity .2s linear, visibility .1s, width 1s ease-in;
                        }
                        .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .offcanvas-overlay.show {
                            transition:opacity .5s ease, width 0s;
                            opacity:1;
                            width:100%;
                            visibility:visible;
                        }
                        .elementor-element-' . $this->get_id() . ' .jltma-menu-container .jltma-navbar-nav-default{
                            justify-content: flex-start !important;
                            padding-left: 2%;
                        }

                        .offcanvas{
                            width:350px;
                            visibility: hidden;
                            transform:translateX(-100%);
                            transition:all .2s;
                            border-radius:0;
                            box-shadow: 0 5px 10px rgba(0,0,0, .2);
                                display:block;
                            position: fixed;
                            top: 0;
                            left: 0;
                            height: 100%;
                            z-index: 1200;
                            background-color: #fff;
                            overflow-y: scroll;
                            overflow-x: hidden;
                        }

                        .offcanvas.offcanvas-right {
                           right: 0;
                           left: auto;
                           transform: translateX(100%);
                         }

                        @media all and (min-width:' . $breakpoint . 'px) {

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .desktop-offcanvas.offcanvas{
                                width:350px;
                                visibility: hidden;
                                transform:translateX(-100%);
                                transition:all .2s;
                                border-radius:0;
                                box-shadow: 0 5px 10px rgba(0,0,0, .2);
                                    display:block;
                                position: fixed;
                                top: 0;
                                left: 0;
                                height: 100%;
                                z-index: 1200;
                                overflow-y: scroll;
                                overflow-x: hidden;
                            }

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .desktop-offcanvas.offcanvas.right {
                                right: 0;
                                left: auto;
                                transform: translateX(100%);
                            }

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .desktop-offcanvas.offcanvas.show{
                                visibility: visible;
                                transform: translateX(0);
                                transition: transform .2s;
                            }

                        }</style>';
            }




            if (($offcanvas_type == "mobile") && (($settings['jltma_burger_menu_location'] == 'offcanvas') && ($jltma_offcanvas_align == "left"))) {
                echo '<style>
                        .elementor-element-' . $breakpoint . ' .jltma-nav-menu-element .offcanvas-overlay {
                            width:0%;
                            height: 100%;
                            z-index: 30;
                            position: fixed;
                            top: 0;
                            left: 0;
                            opacity:0;
                            visibility:hidden;
                            background-color: rgba(34, 34, 34, 0.6);
                            transition:opacity .2s linear, visibility .1s, width 1s ease-in;
                        }
                        .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .offcanvas-overlay.show {
                            transition:opacity .5s ease, width 0s;
                            opacity:1;
                            width:100%;
                            visibility:visible;
                        }

                        @media all and (max-width:' . $breakpoint . 'px) {
                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .mobile-offcanvas.left{
                                visibility: hidden;
                                transform:translateX(-100%);
                                border-radius:0;
                                display:block;
                                position: fixed;
                                top: 0; left:0;
                                height: 100%;
                                z-index: 1200;
                                width:80%;
                                overflow-x: hidden;
                                transition: visibility .2s ease-in-out, transform .2s ease-in-out;
                            }

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .mobile-offcanvas.left.show{
                                visibility: visible;
                                transform: translateX(0);
                            }
                        }</style>';
            }


            if (($offcanvas_type == "mobile") && (($settings['jltma_burger_menu_location'] == 'offcanvas') && ($jltma_offcanvas_align == "right"))) {
                echo '<style>
                        .elementor-element-' . $breakpoint . ' .jltma-nav-menu-element .jltma-navbar-nav-default.overlay {
                            width:0%;
                            height: 100%;
                            z-index: 30 ;
                            position: fixed;
                            top: 0;
                            left: 0;
                            opacity:0;
                            visibility:hidden;
                            background-color: rgb(0, 0, 0, .9);
                            transition:opacity .2s linear, visibility .1s, width 1s ease-in;
                        }
                        .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .offcanvas-overlay.show {
                            transition:opacity .5s ease, width 0s;
                            opacity:1;
                            width:100%;
                            visibility:visible;
                        }

                        @media all and (max-width:' . $breakpoint . 'px) {

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .mobile-offcanvas.offcanvas{
                                justify-content: center !important;
                                width:80%;
                                visibility: hidden;
                                transform:translateX(-100%);
                                transition:all .2s;
                                border-radius:0;
                                box-shadow: 0 5px 10px rgba(0,0,0, .2);
                                    display:block;
                                position: fixed;
                                top: 0;
                                left: 0;
                                height: 100%;
                                z-index: 1200;
                                overflow-y: scroll;
                                overflow-x: hidden;
                            }

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .mobile-offcanvas.offcanvas.right {
                                right: 0;
                                left: auto;
                                transform: translateX(100%);
                            }

                            .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .mobile-offcanvas.offcanvas.show{
                                visibility: visible;
                                transform: translateX(0);
                                transition: transform .2s;
                            }

                        }</style>';
            }

            if (($offcanvas_type == "desktop") && ($settings['jltma_burger_menu_location'] == 'overlay')) {

                echo '<style>
                        .desktop .navbar-nav.jltma-navbar-nav{
                            flex-direction: column;
                            overflow: hidden;
                        }

                        body.offcanvas-active{
                            overflow:hidden;
                        }

                        .elementor-element-' . $breakpoint . ' .jltma-nav-menu-element .offcanvas-overlay {
                            width:0%;
                            height: 100%;
                            z-index: 30;
                            position: fixed;
                            top: 0;
                            left: 0;
                            opacity:0;
                            visibility:hidden;
                            background-color: rgba(34, 34, 34, 0.6);
                            transition:opacity .2s linear, visibility .1s, width 1s ease-in;
                        }
                        .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .offcanvas-overlay.show {
                            transition:opacity .5s ease, width 0s;
                            opacity:1;
                            width:100%;
                            visibility:visible;
                        }



                        .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .desktop-offcanvas{
                            visibility: hidden;
                            transform:translateX(-100%);
                            border-radius:0;
                            display:block;
                            position: fixed;
                            top: 0; left:0;
                            height: 100%;
                            z-index: 1200;
                            width:100%;
                            background-color: black;
                            overflow-y: scroll;
                            overflow-x: hidden;
                            transition: visibility .2s ease-in-out, transform .2s ease-in-out;
                        }

                        .elementor-element-' . $this->get_id() . ' .jltma-menu-container .jltma-navbar-nav-default{
                            justify-content: center !important;
                            padding-left: 2%;
                        }
                        .elementor-element-' . $this->get_id() . ' .jltma-nav-menu-element .desktop-offcanvas.show{
                            visibility: visible;
                            transform: translateX(0);
                        }</style>';
            }



            if ('burger' !== $settings['jltma_menu_layout_type']) {
                printf('<style>@media only screen and (min-width: %spx) { .elementor-element-%s .jltma-burger-box { display: none } }</style>', $breakpoint,  $this->get_id());
            }
        }
    }
}
