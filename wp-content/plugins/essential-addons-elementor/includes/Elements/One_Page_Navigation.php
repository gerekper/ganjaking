<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use Essential_Addons_Elementor\Classes\Helper;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * One Page Navigation Widget
 */
class One_Page_Navigation extends Widget_Base
{

    /**
     * Retrieve one page navigation widget name.
     */
    public function get_name()
    {
        return 'eael-one-page-nav';
    }

    /**
     * Retrieve one page navigation widget title.
     */
    public function get_title()
    {
        return __('One Page Navigation', 'essential-addons-elementor');
    }

    /**
     * Retrieve the list of categories the one page navigation widget belongs to.
     */
    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'single page navigation',
            'ea single page navigation',
            'one page navigation',
            'ea one page navigation',
            'ea onepage navigation',
            'spa',
            'single page application',
            'page scroller',
            'page navigator',
            'ea',
            'essential addons'
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/one-page-navigation/';
    }

    /**
     * Retrieve one page navigation widget icon.
     */
    public function get_icon()
    {
        return 'eaicon-one-page-navigaton';
    }

    /**
     * Register one page navigation widget controls.
     */
    protected function register_controls()
    {

        /*-----------------------------------------------------------------------------------*/
        /*	CONTENT TAB
        /*-----------------------------------------------------------------------------------*/

        /**
         * Content Tab: Navigation Dots
         */
        $this->start_controls_section(
            'section_nav_dots',
            [
                'label'                 => __('Navigation Dots', 'essential-addons-elementor'),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'section_title',
            [
                'label'                 => __('Section Title', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'            => true,
                ],
                'default'               => __('Section Title', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'section_id',
            [
                'label'                 => __('Section ID', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'            => true,
                ],
                'default'               => '',
                'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'dot_icon_new',
            [
                'label'                 => __('Navigation Dot', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::ICONS,
                'fa4compatibility' => 'dot_icon',
                'default' => [
                    'value' => 'fas fa-circle',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'nav_dots',
            [
                'label'                 => '',
                'type'                  => Controls_Manager::REPEATER,
                'default'               => [
                    [
                        'section_title'   => __('Section #1', 'essential-addons-elementor'),
                        'section_id'      => 'section-1',
                        'dot_icon'        => 'fa fa-circle',
                    ],
                    [
                        'section_title'   => __('Section #2', 'essential-addons-elementor'),
                        'section_id'      => 'section-2',
                        'dot_icon'        => 'fa fa-circle',
                    ],
                    [
                        'section_title'   => __('Section #3', 'essential-addons-elementor'),
                        'section_id'      => 'section-3',
                        'dot_icon'        => 'fa fa-circle',
                    ],
                ],
                'fields'                =>  $repeater->get_controls(),
                'title_field'           => '{{{ section_title }}}',
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Settings
         */
        $this->start_controls_section(
            'section_onepage_nav_settings',
            [
                'label'                 => __('Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'nav_tooltip',
            [
                'label'                 => __('Tooltip', 'essential-addons-elementor'),
                'description'           => __('Show tooltip on hover', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __('Yes', 'essential-addons-elementor'),
                'label_off'             => __('No', 'essential-addons-elementor'),
                'return_value'          => 'yes',
            ]
        );

        $this->add_control(
            'tooltip_arrow',
            [
                'label'                 => __('Tooltip Arrow', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __('Show', 'essential-addons-elementor'),
                'label_off'             => __('Hide', 'essential-addons-elementor'),
                'return_value'          => 'yes',
                'condition'             => [
                    'nav_tooltip'   => 'yes',
                ],
            ]
        );

        $this->add_control(
            'scroll_wheel',
            [
                'label'                 => __('Scroll Wheel', 'essential-addons-elementor'),
                'description'           => __('Use mouse wheel to navigate from one row to another', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'off',
                'label_on'              => __('On', 'essential-addons-elementor'),
                'label_off'             => __('Off', 'essential-addons-elementor'),
                'return_value'          => 'on',
            ]
        );

        $this->add_control(
            'scroll_touch',
            [
                'label'                 => __('Touch Swipe', 'essential-addons-elementor'),
                'description'           => __('Use touch swipe to navigate from one row to another in mobile devices', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'off',
                'label_on'              => __('On', 'essential-addons-elementor'),
                'label_off'             => __('Off', 'essential-addons-elementor'),
                'return_value'          => 'on',
                'condition'             => [
                    'scroll_wheel'   => 'on',
                ],
            ]
        );

        $this->add_control(
            'scroll_keys',
            [
                'label'                 => __('Scroll Keys', 'essential-addons-elementor'),
                'description'           => __('Use UP and DOWN arrow keys to navigate from one row to another', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'off',
                'label_on'              => __('On', 'essential-addons-elementor'),
                'label_off'             => __('Off', 'essential-addons-elementor'),
                'return_value'          => 'on',
            ]
        );

        $this->add_control(
            'top_offset',
            [
                'label'                 => __('Row Top Offset', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => ['size' => '0'],
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 300,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => ['px'],
            ]
        );

        $this->add_control(
            'scrolling_speed',
            [
                'label'                 => __('Scrolling Speed', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => '700',
            ]
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*	STYLE TAB
        /*-----------------------------------------------------------------------------------*/

        /**
         * Style Tab: Navigation Box
         */
        $this->start_controls_section(
            'section_nav_box_style',
            [
                'label'                 => __('Navigation Box', 'essential-addons-elementor'),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_alignment',
            [
                'label'                 => __('Alignment', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'top'          => [
                        'title'    => __('Top', 'essential-addons-elementor'),
                        'icon'     => 'eicon-v-align-top',
                    ],
                    'bottom'       => [
                        'title'    => __('Bottom', 'essential-addons-elementor'),
                        'icon'     => 'eicon-v-align-bottom',
                    ],
                    'left'         => [
                        'title'    => __('Left', 'essential-addons-elementor'),
                        'icon'        => 'eicon-h-align-left',
                    ],
                    'right'        => [
                        'title'    => __('Right', 'essential-addons-elementor'),
                        'icon'        => 'eicon-h-align-right',
                    ],
                ],
                'default'               => 'right',
                'prefix_class'          => 'nav-align-',
                'frontend_available'    => true,
                'selectors'             => [
                    '{{WRAPPER}} .eael-caldera-form-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'nav_container_background',
                'types'             => ['classic', 'gradient'],
                'selector'          => '{{WRAPPER}} .eael-one-page-nav',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'nav_container_border',
                'label'                 => __('Border', 'essential-addons-elementor'),
                'placeholder'           => '1px',
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .eael-one-page-nav'
            ]
        );

        $this->add_control(
            'nav_container_border_radius',
            [
                'label'                 => __('Border Radius', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_container_margin',
            [
                'label'                 => __('Margin', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_container_padding',
            [
                'label'                 => __('Padding', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'                  => 'nav_container_box_shadow',
                'selector'              => '{{WRAPPER}} .eael-one-page-nav',
                'separator'             => 'before',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Navigation Dots
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label'                 => __('Navigation Dots', 'essential-addons-elementor'),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dots_size',
            [
                'label'                 => __('Size', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => ['size' => '10', 'unit'=>'px'],
                'range'                 => [
                    'px' => [
                        'min'   => 5,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => ['px'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} img.eael-nav-dot' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-nav-dot-wrap svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_spacing',
            [
                'label'                 => __('Spacing', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => ['size' => '10'],
                'range'                 => [
                    'px' => [
                        'min'   => 2,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => ['px'],
                'selectors'             => [
                    '{{WRAPPER}}.nav-align-right .eael-one-page-nav-item, {{WRAPPER}}.nav-align-left .eael-one-page-nav-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.nav-align-top .eael-one-page-nav-item, {{WRAPPER}}.nav-align-bottom .eael-one-page-nav-item' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0',
                    '.rtl {{WRAPPER}}.nav-align-top .eael-one-page-nav-item, {{WRAPPER}}.nav-align-bottom .eael-one-page-nav-item' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: 0;',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_padding',
            [
                'label'                 => __('Padding', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'                  => 'dots_box_shadow',
                'selector'              => '{{WRAPPER}} .eael-nav-dot-wrap',
                'separator'             => 'before',
            ]
        );

        $this->start_controls_tabs('tabs_dots_style');

        $this->start_controls_tab(
            'tab_dots_normal',
            [
                'label'                 => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'dots_color_normal',
            [
                'label'                 => __('Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} svg.eael-nav-dot' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .eael-nav-dot-wrap svg' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .eael-nav-dot-wrap svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_bg_color_normal',
            [
                'label'                 => __('Background Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'dots_border',
                'label'                 => __('Border', 'essential-addons-elementor'),
                'placeholder'           => '1px',
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .eael-nav-dot-wrap'
            ]
        );

        $this->add_control(
            'dots_border_radius',
            [
                'label'                 => __('Border Radius', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_hover',
            [
                'label'                 => __('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'dots_color_hover',
            [
                'label'                 => __('Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-item .eael-nav-dot-wrap:hover .eael-nav-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-one-page-nav-item .eael-nav-dot-wrap:hover svg.eael-nav-dot' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .eael-one-page-nav-item .eael-nav-dot-wrap:hover svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_bg_color_hover',
            [
                'label'                 => __('Background Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-item .eael-nav-dot-wrap:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_border_color_hover',
            [
                'label'                 => __('Border Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-item .eael-nav-dot-wrap:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_active',
            [
                'label'                 => __('Active', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'dots_color_active',
            [
                'label'                 => __('Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-item.active .eael-nav-dot' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-one-page-nav-item.active svg.eael-nav-dot' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .eael-one-page-nav-item.active .eael-nav-dot-wrap svg' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .eael-one-page-nav-item.active .eael-nav-dot-wrap svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_bg_color_active',
            [
                'label'                 => __('Background Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-item.active .eael-nav-dot-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_border_color_active',
            [
                'label'                 => __('Border Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-one-page-nav-item.active .eael-nav-dot-wrap' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Tooltip
         */
        $this->start_controls_section(
            'section_tooltips_style',
            [
                'label'                 => __('Tooltip', 'essential-addons-elementor'),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'nav_tooltip'  => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tooltip_bg_color',
            [
                'label'                 => __('Background Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot-tooltip-content' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .eael-nav-dot-tooltip' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'nav_tooltip'  => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tooltip_color',
            [
                'label'                 => __('Text Color', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot-tooltip-content' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'nav_tooltip'  => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'tooltip_typography',
                'label'                 => __('Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'              => '{{WRAPPER}} .eael-nav-dot-tooltip',
                'condition'             => [
                    'nav_tooltip'  => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'tooltip_padding',
            [
                'label'                 => __('Padding', 'essential-addons-elementor'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%'],
                'selectors'             => [
                    '{{WRAPPER}} .eael-nav-dot-tooltip-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('onepage-nav-container', 'class', 'eael-one-page-nav-container');

        $this->add_render_attribute('onepage-nav', 'class', 'eael-one-page-nav');

        $this->add_render_attribute('onepage-nav', 'id', 'eael-one-page-nav-' . $this->get_id());

        $this->add_render_attribute('onepage-nav', 'data-section-id', 'eael-one-page-nav-' . $this->get_id());

        $this->add_render_attribute('onepage-nav', 'data-top-offset', $settings['top_offset']['size']);

        $this->add_render_attribute('onepage-nav', 'data-scroll-speed', $settings['scrolling_speed']);

        $this->add_render_attribute('onepage-nav', 'data-scroll-wheel', $settings['scroll_wheel']);

        $this->add_render_attribute('onepage-nav', 'data-scroll-touch', $settings['scroll_touch']);

        $this->add_render_attribute('onepage-nav', 'data-scroll-keys', $settings['scroll_keys']);

        $this->add_render_attribute('onepage-nav', 'data-section-ids', wp_json_encode( wp_list_pluck( $settings['nav_dots'], 'section_id') ) );

        $this->add_render_attribute('tooltip', 'class', 'eael-nav-dot-tooltip');

        if ($settings['tooltip_arrow'] == 'yes') {
            $this->add_render_attribute('tooltip', 'class', 'eael-tooltip-arrow');
        }

?>
        <div <?php echo $this->get_render_attribute_string('onepage-nav-container'); ?>>
            <ul <?php echo $this->get_render_attribute_string('onepage-nav'); ?>>
                <?php
                $i = 1;
                foreach ($settings['nav_dots'] as $index => $dot) {
                    $dot_icon_migrated = isset($dot['__fa4_migrated']['dot_icon_new']);
                    $dot_icon_is_new = empty($dot['dot_icon']);

                    $eael_section_title = $dot['section_title'];
                    $eael_section_id = $dot['section_id'];
                    $eael_dot_icon = ((isset($settings['__fa4_migrated']['dot_icon_new']) || empty($dot['dot_icon']) || !empty($dot['dot_icon_new']['value'])) ? Helper::get_render_icon($dot['dot_icon_new'], ['class'=>'eael-nav-dot']) : '<i class="'.$dot['dot_icon'].' eael-nav-dot"></i>');
                    $eael_dot_icon = (( $dot_icon_migrated || $dot_icon_is_new || !empty($dot['dot_icon_new']['value'])) ? $dot['dot_icon_new']['value'] : $dot['dot_icon']);

                    if ($settings['nav_tooltip'] == 'yes') {
                        $eael_dot_tooltip = sprintf('<span %1$s><span class="eael-nav-dot-tooltip-content">%2$s</span></span>', $this->get_render_attribute_string('tooltip'), $eael_section_title);
                    } else {
                        $eael_dot_tooltip = '';
                    }

                    if ($dot_icon_is_new || $dot_icon_migrated) {
                        printf( '<li class="eael-one-page-nav-item">%1$s<a href="#" data-row-id="%2$s"><span class="eael-nav-dot-wrap">', $eael_dot_tooltip, $eael_section_id );
                        echo Helper::get_render_icon($dot['dot_icon_new'], ['class'=>'eael-nav-dot'] );
                        echo '</span></a></li>';
                    } else {
                        printf('<li class="eael-one-page-nav-item">%1$s<a href="#" data-row-id="%2$s"><span class="eael-nav-dot-wrap">%3$s</span></a></li>', $eael_dot_tooltip, $eael_section_id, $eael_dot_icon);
                    }

                    $i++;
                }
                ?>
            </ul>
        </div>
<?php
    }

    /**
     * Render one page navigation widget output in the editor.
     */
    protected function content_template()
    {
    }
}
