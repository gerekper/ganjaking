<?php

/**
 * One Page Navigation widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || die();

class One_Page_Nav extends Base {

    /**
     * Get widget title.
     *
     * @since 1.13.1
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('One Page Nav', 'happy-addons-pro');
    }

    /**
     * Get widget icon.
     *
     * @since 1.13.1
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-dot-navigation';
    }

    public function get_keywords() {
        return ['one', 'page', 'nav', 'scroll', 'on'];
    }

    protected function register_content_controls() {

        $this->start_controls_section(
            '_section_navigation',
            [
                'label' => __('Navigation', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'select_design',
            [
                'label' => __('Navigation Style', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ha-opn-design-default',
                'options' => [
                    'ha-opn-design-default'  => __('Default', 'happy-addons-pro'),
                    'ha-opn-design-berta' => __('Berta', 'happy-addons-pro'),
                    'ha-opn-design-hagos' => __('Hagos', 'happy-addons-pro'),
                    'ha-opn-design-magool' => __('Magool', 'happy-addons-pro'),
                    'ha-opn-design-maxamed' => __('Maxamed', 'happy-addons-pro'),
                    'ha-opn-design-shamso' => __('Shamso', 'happy-addons-pro'),
                    'ha-opn-design-ubax' => __('Ubax', 'happy-addons-pro'),
                    'ha-opn-design-xusni' => __('Xusni', 'happy-addons-pro'),
                    'ha-opn-design-zahi' => __('Zahi', 'happy-addons-pro'),
                ],
                'frontend_available' => true,
            ]
        );

        $navigation = new \Elementor\Repeater();

        $navigation->add_control(
            'section_id',
            [
                'label' => __('Section ID', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Add your section ID here', 'happy-addons-pro'),
                'label_block' => true,
                'dynamic' => ['active' => true],
            ]
        );

        $navigation->add_control(
            'nav_title',
            [
                'label' => __('Navigation Title', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Add your Navigation Title here', 'happy-addons-pro'),
                'classes' => 'ha-opn-design-refactor-others-title',
                'label_block' => true,
                'dynamic' => ['active' => true],
            ]
        );

        $navigation->add_control(
            'icon',
            [
                'label' => __('Icon', 'happy-addons-pro'),
                'type' => Controls_Manager::ICONS,
                'classes' => 'ha-opn-design-refactor-default',
            ]
        );

        $navigation->add_control(
            'tooltip_title',
            [
                'label' => __('Tooltip Title', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Add your Tooltip Title here', 'happy-addons-pro'),
                'classes' => 'ha-opn-design-refactor-default',
                'label_block' => true,
                'dynamic' => ['active' => true],
            ]
        );

        $navigation->add_control(
            'custom_style_enable',
            [
                'label' => __('Enable Custom Style?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $navigation->add_control(
            'nav_content_color',
            [
                'label' => __('Content Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'custom_style_enable' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav {{CURRENT_ITEM}}.ha-opn-dotted-item .ha-opn-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav {{CURRENT_ITEM}}.ha_opn__item .ha_opn__item-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi {{CURRENT_ITEM}}.ha_opn__item:not(:last-child)::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi {{CURRENT_ITEM}}.ha_opn__item::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool {{CURRENT_ITEM}}.ha_opn__item::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $navigation->add_control(
            'nav_content_color_hover',
            [
                'label' => __('Content Color Hover', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'custom_style_enable' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav {{CURRENT_ITEM}}.ha-opn-dotted-item:hover .ha-opn-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav {{CURRENT_ITEM}}.ha_opn__item:not(.ha_opn__item--current):hover .ha_opn__item-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi {{CURRENT_ITEM}}.ha_opn__item:not(.ha_opn__item--current):hover::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool {{CURRENT_ITEM}}.ha_opn__item:not(.ha_opn__item--current):hover::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $navigation->add_control(
            'nav_content_color_active',
            [
                'label' => __('Content Color Active', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'custom_style_enable' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav {{CURRENT_ITEM}}.ha-opn-dotted-item.ha_opn__item--current .ha-opn-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax {{CURRENT_ITEM}}.ha_opn__item.ha_opn__item--current::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi {{CURRENT_ITEM}}.ha_opn__item.ha_opn__item--current::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi {{CURRENT_ITEM}}.ha_opn__item::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav {{CURRENT_ITEM}}.ha_opn__item.ha_opn__item--current .ha_opn__item-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool {{CURRENT_ITEM}}.ha_opn__item.ha_opn__item--current::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'navigation_lists',
            [
                'label' => __('Navigation List', 'happy-addons-pro'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $navigation->get_controls(),
                'default' => [
                    [
                        'section_id' => 'section1',
                        'tooltip_title' => __('Section 1', 'happy-addons-pro'),
                    ],
                    [
                        'section_id' => 'section2',
                        'tooltip_title' => __('Section 2', 'happy-addons-pro'),
                    ],
                    [
                        'section_id' => 'section3',
                        'tooltip_title' => __('Section 3', 'happy-addons-pro'),
                    ],
                ],
            ]
        );

        $this->add_control(
            'nav_horizontal_align',
            [
                'label' => __('Horizontal Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'ha-opn-left-side' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'ha-opn-right-side' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'ha-opn-right-side',
                'toggle' => false,
            ]
        );

        $this->add_control(
            'nav_vertical_align',
            [
                'label' => __('Vertical Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'ha-opn-position-top' => [
                        'title' => __('Top', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'ha-opn-position-middle' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'ha-opn-position-bottom' => [
                        'title' => __('Bottom', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'ha-opn-position-middle',
                'toggle' => false,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_tooltip',
            [
                'label' => __('Tooltip', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
            ]
        );

        $this->add_control(
            'tooltip',
            [
                'label' => __('Enable Tooltip?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'happy-addons-pro'),
                'label_off' => __('Off', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tooltip_arrow',
            [
                'label' => __('Enable Tooltip Arrow?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'happy-addons-pro'),
                'label_off' => __('Off', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_settings',
            [
                'label' => __('Settings', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'scroll_wheel',
            [
                'label' => __('Scroll Wheel', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'happy-addons-pro'),
                'label_off' => __('Off', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Scroll to specific section with mouse wheel scroll.', 'happy-addons-pro'),
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'touch_swipe',
            [
                'label' => __('Touch Swipe', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'happy-addons-pro'),
                'label_off' => __('Off', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Scroll to specific section with touch swipe (Only for mobile).', 'happy-addons-pro'),
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'scroll_keys',
            [
                'label' => __('Scroll Keys', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'happy-addons-pro'),
                'label_off' => __('Off', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Scroll to specific section with keyboard up/down arrow keys.', 'happy-addons-pro'),
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'section_threshold',
            [
                'label' => __('Section Threshold', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0.01,
                'max' => 1,
                'step' => 0.01,
                'default' => 0.3,
                'template' => 'ui',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
			'section_threshold_notice',
			[
				'raw'             => '<strong>' . esc_html__( 'Please note:', 'happy-addons-pro' ) . '</strong> ' . esc_html__( 'Use greater value if your section is smaller and lower value if your section is bigger.', 'happy-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type'     => 'ui',
			]
		);

        $this->add_control(
            'row_to_offset',
            [
                'label' => __('Row To Offset (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1000,
                'step' => 1,
                'default' => 0,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'scrolling_speed',
            [
                'label' => __('Scrolling Speed (px)', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'max' => 10000,
                'step' => 50,
                'default' => 700,
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_controls() {
        $this->start_controls_section(
            '_section_style_navigation',
            [
                'label' => __('Navigation', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'nav_margin',
            [
                'label' => __('Margin', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'nav_border',
                'label' => __('Border', 'happy-addons-pro'),
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav',
            ]
        );

        $this->add_control(
            'nav_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'nav_box_shadow',
                'label' => __('Box Shadow', 'happy-addons-pro'),
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav',
            ]
        );

        $this->start_controls_tabs(
            '_section_style_tabs'
        );

        $this->start_controls_tab(
            '_section_style_tab_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'nav_background',
                'label' => __('Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_section_style_tab_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'nav_background_hover',
                'label' => __('Background', 'happy-addons-pro'),
                'types' => ['classic', 'gradient'],
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav:hover',
            ]
        );

        $this->add_control(
            'nav_border_hover_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'nav_background_hover_border!' => '',
                    'select_design' => 'ha-opn-design-default',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_content',
            [
                'label' => __('Navigation Content', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'nav_icon_size',
            [
                'label' => __('Icons/SVG Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 16,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'select_design' => ['ha-opn-design-default', 'ha-opn-design-hagos', 'ha-opn-design-magool', 'ha-opn-design-maxamed', 'ha-opn-design-shamso', 'ha-opn-design-ubax'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-hagos .ha_opn__item::before' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool .ha_opn__item::after' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-maxamed .ha_opn__item::before' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-shamso .ha_opn__item' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax .ha_opn__item::after' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'nav_fixed_height_width',
            [
                'label' => __('Fixed Height & Width', 'happy-addons-pro'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'happy-addons-pro'),
                'label_on' => __('Custom', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'select_design' => ['ha-opn-design-default'],
                ],
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'nav_fixed_height_width__width',
            [
                'label' => __('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_fixed_height_width__height',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_popover();

        $this->add_responsive_control(
            'nav_space_between',
            [
                'label' => __('Space Between Icon & Title', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 720,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 16,
                    ],
                ],
                'condition' => [
                    'select_design' => ['ha-opn-design-default', 'ha-opn-design-hagos', 'ha-opn-design-xusni', 'ha-opn-design-maxamed', 'ha-opn-design-shamso', 'ha-opn-design-ubax', 'ha-opn-design-zahi'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot i + span' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-xusni .ha_opn__item .ha_opn__item-title' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-hagos .ha_opn__item .ha_opn__item-title' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-maxamed .ha_opn__item .ha_opn__item-title' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-shamso .ha_opn__item .ha_opn__item-title' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax .ha_opn__item .ha_opn__item-title' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item .ha_opn__item-title' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'nav_content_margin',
            [
                'label' => __('Space between nav', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 16,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item' => 'margin-top: calc({{SIZE}}{{UNIT}}/2);margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha_opn__item' => 'margin-top: calc({{SIZE}}{{UNIT}}/2);margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item' => 'padding: {{SIZE}}{{UNIT}} 0; margin: 0 auto;',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item:not(:last-child)::before' => 'top: calc(1em + {{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_content_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'condition' => [
                    'select_design' => ['ha-opn-design-default'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'nav_content_border',
                'label' => __('Border', 'happy-addons-pro'),
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot',
            ]
        );

        $this->add_control(
            'nav_content_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 50,
                    'right' => 50,
                    'bottom' => 50,
                    'left' => 50,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'nav_content_box_shadow',
                'label' => __('Box Shadow', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot',
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'nav_content_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'condition' => [
                    'select_design!' => 'ha-opn-design-magool',
                ],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot, {{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav .ha_opn__item .ha_opn__item-title, {{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item',
            ]
        );

        $this->start_controls_tabs(
            '_section_content_style_tabs'
        );

        $this->start_controls_tab(
            '_section_content_style_tab_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'nav_content_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design!' => ['ha-opn-design-ubax', 'ha-opn-design-shamso', 'ha-opn-design-maxamed', 'ha-opn-design-hagos', 'ha-opn-design-berta', 'ha-opn-design-xusni'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav .ha_opn__item .ha_opn__item-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item:not(:last-child)::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool .ha_opn__item::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_content_background',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design!' => ['ha-opn-design-magool'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-dot' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax .ha_opn__item::after' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-shamso .ha_opn__item::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-maxamed .ha_opn__item::before' => 'box-shadow: inset 0 0 0 calc(1em - 0.6em) {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-hagos .ha_opn__item::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item::after' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-berta .ha_opn__item::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-xusni .ha_opn__item::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_section_content_style_tab_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'nav_content_color_hover',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design!' => ['ha-opn-design-ubax', 'ha-opn-design-shamso', 'ha-opn-design-maxamed', 'ha-opn-design-hagos', 'ha-opn-design-berta', 'ha-opn-design-xusni'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item:hover .ha-opn-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav .ha_opn__item:not(.ha_opn__item--current):hover .ha_opn__item-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item:not(.ha_opn__item--current):hover::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool .ha_opn__item:not(.ha_opn__item--current):hover::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_content_background_hover',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design!' => ['ha-opn-design-magool'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item:hover .ha-opn-dot' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax .ha_opn__item:not(.ha_opn__item--current):hover::after' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-shamso .ha_opn__item:not(.ha_opn__item--current):hover::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-maxamed .ha_opn__item:not(.ha_opn__item--current):hover::before' => 'box-shadow: inset 0 0 0 calc(1em - 0.6em) {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-hagos .ha_opn__item:not(.ha_opn__item--current):hover::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item:not(.ha_opn__item--current):hover::after' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-berta .ha_opn__item:not(.ha_opn__item--current):hover::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-xusni .ha_opn__item:not(.ha_opn__item--current):hover::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_content_border_color_hover',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                    'nav_content_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item:hover .ha-opn-dot' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_section_content_style_tab_active',
            [
                'label' => __('Active', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'nav_content_color_active',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item.ha_opn__item--current .ha-opn-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax .ha_opn__item.ha_opn__item--current::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item.ha_opn__item--current::after' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-nav .ha_opn__item.ha_opn__item--current .ha_opn__item-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-magool .ha_opn__item.ha_opn__item--current::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_content_background_active',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design!' => ['ha-opn-design-magool'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item.ha_opn__item--current .ha-opn-dot' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-ubax .ha_opn__item.ha_opn__item--current::after' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-shamso .ha_opn__item.ha_opn__item--current::after' => 'box-shadow: inset 0 0 0 3px {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-shamso .ha_opn__item.ha_opn__item--current::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-maxamed .ha_opn__item.ha_opn__item--current::before' => 'box-shadow: inset 0 0 0 calc(1em - 0.95em) {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-hagos .ha_opn__item.ha_opn__item--current::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-zahi .ha_opn__item.ha_opn__item--current::after' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-berta .ha_opn__item.ha_opn__item--current::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-design-xusni .ha_opn__item.ha_opn__item--current::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_content_border_color_active',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'select_design' => 'ha-opn-design-default',
                    'nav_content_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item.ha_opn__item--current .ha-opn-dot' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_Tooltip',
            [
                'label' => __('Tooltip', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'select_design' => 'ha-opn-design-default'
                ]
            ]
        );

        $this->add_responsive_control(
            'nav_tooltip_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'nav_tooltip_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
                'selector' => '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-tooltip',
            ]
        );

        $this->add_control(
            'nav_tooltip_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-tooltip' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_tooltip_background_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-tooltip' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav.ha-opn-right-side .ha-opn-dotted-item .ha-opn-arrow' => 'border-left-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-opn-dotted-nav.ha-opn-left-side .ha-opn-dotted-item .ha-opn-arrow' => 'border-right-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_tooltip_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-opn-dotted-nav .ha-opn-dotted-item .ha-opn-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $wrapper_class = $settings['select_design'];
        $wrapper_class .= " " . $settings['nav_horizontal_align'];
        $wrapper_class .= " " . $settings['nav_vertical_align'];
        if (ha_elementor()->editor->is_edit_mode()) :
?>
            <div class="ha-editor-placeholder">
                <h4 class="ha-editor-placeholder-title">
                    <?php esc_html_e('One Page Nav', 'happy-addons-pro'); ?>
                </h4>
                <div class="ha-editor-placeholder-content">
                    <?php esc_html_e('This placeholder text doesn\'t serve any purpose. It won\'t show up in the frontend either. Go to preview mode to see full functionalities.', 'happy-addons-pro'); ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="ha-opn-dotted-nav <?php echo esc_attr($wrapper_class); ?>">
            <?php if ($settings['select_design'] == 'ha-opn-design-default') : ?>
                <ul>
                    <?php if (is_array($settings['navigation_lists'])) :
                        foreach ($settings['navigation_lists'] as $i => $nav) :
                    ?>
                            <li class="ha-opn-dotted-item elementor-repeater-item-<?php echo $nav['_id']; ?> <?php echo esc_attr(($i == 0) ? 'ha_opn__item--current' : '') ?>">
                                <?php if (isset($settings['tooltip']) && $settings['tooltip'] == 'yes') : ?>
                                    <span class="ha-opn-tooltip">
                                        <?php echo esc_html($nav['tooltip_title']); ?>
                                        <?php if (isset($settings['tooltip_arrow']) && $settings['tooltip_arrow'] == 'yes') : ?>
                                            <div class="ha-opn-arrow"></div>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <a href="#" data-section-id="<?php echo esc_attr($nav['section_id']); ?>">
                                    <span class="ha-opn-dot">
                                        <?php if (!empty($nav['icon']['value'])) : ?>
                                            <?php Icons_Manager::render_icon($nav['icon']); ?>
                                        <?php endif; ?>
                                        <?php if (!empty($nav['nav_title'])) : ?>
                                            <span><?php echo esc_html($nav['nav_title']); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                    <?php endforeach;
                    endif; ?>
                </ul>
            <?php else : ?>
                <ul class="ha-opn-nav <?php echo esc_attr($settings['select_design']); ?>">
                    <?php if (is_array($settings['navigation_lists'])) :
                        foreach ($settings['navigation_lists'] as $i => $nav) :
                    ?>
                            <li class="ha_opn__item <?php echo esc_attr(($i == 0) ? 'ha_opn__item--current' : '') ?> elementor-repeater-item-<?php echo $nav['_id']; ?>" aria-label="<?php echo esc_html($nav['nav_title']); ?>">
                                <a href="#" data-section-id="<?php echo esc_attr($nav['section_id']); ?>"></a>
                                <?php if ($settings['select_design'] != 'ha-opn-design-magool') : ?>
                                    <span class="ha_opn__item-title">
                                        <?php
                                        if (empty($nav['nav_title']) && ($settings['select_design'] == 'ha-opn-design-berta' || $settings['select_design'] == 'ha-opn-design-xusni')) {
                                            echo esc_html__('Section ', 'happy-addons-pro') . ($i + 1);
                                        } else if (!empty($nav['nav_title'])) {
                                            echo esc_html($nav['nav_title']);
                                        }
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                    <?php endforeach;
                    endif; ?>
                </ul>
            <?php endif; ?>
        </div>
<?php
    }
}
