<?php

namespace ElementPack\Modules\EddTabs\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class EDD_Tabs extends Module_Base {

    public function get_name() {
        return 'bdt-edd-tabs';
    }

    public function get_title() {
        return BDTEP . esc_html__('EDD Tabs', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-edd-tabs bdt-new';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['tabs', 'toggle', 'accordion'];
    }

    public function is_reload_preview_required() {
        return false;
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-edd-tabs'];
        }
    }
    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-edd-tabs'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/1BmS_8VpBF4';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('EDD Tabs', 'bdthemes-element-pack'),
            ]
        );
        $repeater = new Repeater();

        $repeater->add_control(
            'tab_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Tab Title', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Tab Title', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tab_content',
            [
                'label' => esc_html__('Tab Content', 'bdthemes-element-pack'),
                'default' => esc_html__('Tab Content', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Tab Content', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'download_history' => esc_html__('Download History', 'bdthemes-element-pack'),
                    'download_discounts' => esc_html__('Download Discounts', 'bdthemes-element-pack'),
                    'purchase_history' => esc_html__('Purchase History', 'bdthemes-element-pack'),
                    'edd_profile_editor' => esc_html__('Profile Editor', 'bdthemes-element-pack'),
                    'edd_subscriptions' => esc_html__('Subscriptions', 'bdthemes-element-pack'),
                    'edd_wish_lists' => esc_html__('Wishlist Items', 'bdthemes-element-pack'),
                    'edd_wish_lists_edit' => esc_html__('Edit Wishlist Items', 'bdthemes-element-pack'),
                    'edd_deposit' => esc_html__('Deposits', 'bdthemes-element-pack'),
                    'edd_license_keys' => esc_html__('License Keys', 'bdthemes-element-pack')
                ],
                'default' => 'purchase_history'
            ]
        );
        $repeater->add_control(
            'tab_select_icon',
            [
                'label'            => __('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'tab_icon',
            ]
        );

        $this->add_control(
            'tabs',
            [
                'label' => esc_html__('Tabs Items', 'bdthemes-element-pack'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tab_title' => esc_html__('Purchase History', 'bdthemes-element-pack'),
                        'tab_content' => 'purchase_history',
                    ],
                    [
                        'tab_title' => esc_html__('Download History', 'bdthemes-element-pack'),
                        'tab_content' => 'download_history',
                    ],
                ],
                'title_field' => '{{{ tab_title }}}',
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'tab_layout',
            [
                'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Top (Default)', 'bdthemes-element-pack'),
                    'bottom'  => esc_html__('Bottom', 'bdthemes-element-pack'),
                    'left'    => esc_html__('Left', 'bdthemes-element-pack'),
                    'right'   => esc_html__('Right', 'bdthemes-element-pack'),
                ],
            ]
        );


        $this->add_control(
            'align',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    ''        => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'condition' => [
                    'tab_layout' => ['default', 'bottom']
                ],
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label'     => __('Nav Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item'                                                                 => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tab'                                                                                => 'margin-left: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tab.bdt-tab-left .bdt-tabs-item, {{WRAPPER}} .bdt-tab.bdt-tab-right .bdt-tabs-item' => 'padding-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tab.bdt-tab-left, {{WRAPPER}} .bdt-tab.bdt-tab-right'                               => 'margin-top: -{{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'nav_spacing',
            [
                'label'     => __('Nav Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-grid:not(.bdt-grid-stack) .bdt-tab-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'tab_layout' => ['left', 'right']
                ],
            ]
        );

        $this->add_responsive_control(
            'content_spacing',
            [
                'label'     => __('Content Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs-default .bdt-switcher-wrapper'                              => 'margin-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-bottom .bdt-switcher-wrapper'                               => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-left .bdt-grid:not(.bdt-grid-stack) .bdt-switcher-wrapper'  => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-right .bdt-grid:not(.bdt-grid-stack) .bdt-switcher-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-left .bdt-grid-stack .bdt-switcher-wrapper,
                    {{WRAPPER}} .bdt-tabs-right .bdt-grid-stack .bdt-switcher-wrapper'      => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => __('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'active_item',
            [
                'label' => __('Active Item No', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::NUMBER,
                'min'   => 1,
                'max'   => 20,
            ]
        );

        $this->add_control(
            'tab_transition',
            [
                'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => element_pack_transition_options(),
                'default' => '',
            ]
        );

        $this->add_control(
            'duration',
            [
                'label'     => __('Animation Duration', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 501,
                        'step' => 50,
                    ],
                ],
                'default'   => [
                    'size' => 200,
                ],
                'condition' => [
                    'tab_transition!' => ''
                ],
            ]
        );

        $this->add_control(
            'media',
            [
                'label'       => __('Turn On Horizontal mode', 'bdthemes-element-pack'),
                'description' => __('It means that tabs nav will switch vertical to horizontal on mobile mode', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'options'     => [
                    960 => [
                        'title' => __('On Tablet', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-device-tablet',
                    ],
                    768 => [
                        'title' => __('On Mobile', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-device-mobile',
                    ],
                ],
                'default'     => 960,
                'condition'   => [
                    'tab_layout' => ['left', 'right']
                ],
            ]
        );

        $this->add_control(
            'nav_sticky_mode',
            [
                'label'     => esc_html__('Tabs Nav Sticky', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'tab_layout!' => 'bottom',
                ],
            ]
        );

        $this->add_control(
            'nav_sticky_offset',
            [
                'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'condition' => [
                    'nav_sticky_mode' => 'yes',
                    'tab_layout!'     => 'bottom',
                ],
            ]
        );

        $this->add_control(
            'nav_sticky_on_scroll_up',
            [
                'label'       => esc_html__('Sticky on Scroll Up', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('Set sticky options when you scroll up your mouse.', 'bdthemes-element-pack'),
                'condition'   => [
                    'nav_sticky_mode' => 'yes',
                    'tab_layout!'     => 'bottom',
                ],
            ]
        );

        $this->add_control(
            'fullwidth_on_mobile',
            [
                'label'       => esc_html__('Fullwidth Nav on Mobile', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('If you have long test tab so this can help design issue.', 'bdthemes-element-pack')
            ]
        );

        $this->add_control(
            'swiping_on_mobile',
            [
                'label'       => esc_html__('Swiping Tab on Mobile', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('If you set yes so tab will swiping on mobile device by touch.', 'bdthemes-element-pack'),
                'default'     => 'yes',
            ]
        );

        $this->add_control(
            'active_hash',
            [
                'label'   => esc_html__('Hash Location', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'hash_top_offset',
            [
                'label'      => esc_html__('Top Offset ', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range'      => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 5,
                    ],

                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 70,
                ],
                'condition'  => [
                    'active_hash'      => 'yes',
                    'nav_sticky_mode!' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'hash_scrollspy_time',
            [
                'label'      => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['ms', ''],
                'range'      => [
                    'px' => [
                        'min'  => 500,
                        'max'  => 5000,
                        'step' => 1000,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 1500,
                ],
                'condition'  => [
                    'active_hash'      => 'yes',
                    'nav_sticky_mode!' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'tabs_match_height',
            [
                'label'       => esc_html__('Equal Tab Height', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('You can on/off tab item equal height feature.', 'bdthemes-element-pack'),
                'default'     => 'yes',
                'separator'   => 'before',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_title',
            [
                'label' => __('Tab', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'title_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'title_shadow',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-title',
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-title',
            ]
        );

        $this->add_control(
            'title_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_title_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tabs-item-title',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tabs-item-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_hover_border',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'title_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tabs-item-title' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_active',
            [
                'label' => __('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'active_style_color',
            [
                'label'     => __('Style Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'active_title_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'active_title_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'active_title_shadow',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'active_title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title',
            ]
        );

        $this->add_control(
            'active_title_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_content',
            [
                'label' => __('Content', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'content_background_color',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_link_color',
            [
                'label'     => __('Link Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'content_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content',
            ]
        );

        $this->add_control(
            'content_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => __('Icon', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_icon_style');

        $this->start_controls_tab(
            'tab_icon_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label'   => __('Alignment', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'  => [
                        'title' => __('Start', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => __('End', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default' => is_rtl() ? 'right' : 'left',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_space',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item-title .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item-title .bdt-button-icon-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item:hover .bdt-tabs-item-title i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item:hover .bdt-tabs-item-title svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_active',
            [
                'label' => __('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_active_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item.bdt-active .bdt-tabs-item-title i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item.bdt-active .bdt-tabs-item-title svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'section_tabs_sticky_style',
            [
                'label' => __('Sticky', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'sticky_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs > div > .bdt-sticky.bdt-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'sticky_shadow',
                'selector' => '{{WRAPPER}} .bdt-tabs > div > .bdt-sticky.bdt-active',
            ]
        );

        $this->add_control(
            'sticky_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs > div > .bdt-sticky.bdt-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_label',
            [
                'label' => esc_html__('Label', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} #edd_profile_editor_form label',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_input',
            [
                'label' => esc_html__('Input', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'input_placeholder_color',
            [
                'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form input::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} #edd_profile_editor_form textarea::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form input' => 'color: {{VALUE}};',
                    '{{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'others_type_input_text_color',
            [
                'label'     => esc_html__('Others Type Input Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'      => '#666666',
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form.select-state' => 'color: {{VALUE}};',
                    '{{WRAPPER}} #edd_profile_editor_form.select-gender' => 'color: {{VALUE}};',
                    '{{WRAPPER}} #edd_profile_editor_form.accept-this-1' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_text_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form input' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'textarea_height',
            [
                'label'   => esc_html__('Textarea Height', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 125,
                ],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea' => 'height: {{SIZE}}{{UNIT}}; display: block;',
                ],
                'separator' => 'before',

            ]
        );

        $this->add_control(
            'input_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} #edd_profile_editor_form input, {{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea, {{WRAPPER}} #edd_profile_editor_form .select.edd-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_show',
            [
                'label'        => esc_html__('Border Style', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'separator'    => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'input_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} #edd_profile_editor_form input, {{WRAPPER}} #edd_profile_editor_form textarea, {{WRAPPER}} #edd_profile_editor_form .select.edd-select',
                'condition' => [
                    'input_border_show' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'input_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} #edd_profile_editor_form input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} #edd_profile_editor_form textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} #edd_profile_editor_form .select.edd-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_submit_button',
            [
                'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_shadow',
                'selector' => '{{WRAPPER}} #edd_profile_editor_submit',
            ]
        );

        $this->add_control(
            'text_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'button_typography',
                'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
                'selector'  => '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_additional_option',
            [
                'label' => esc_html__('Profile Editor Additional', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'fullwidth_input',
            [
                'label' => esc_html__('Fullwidth Input', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'bdthemes-element-pack'),
                'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="text"]'     => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="email"]'    => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="url"]'      => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="number"]'   => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="tel"]'      => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="date"]'     => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form input[type*="password"]' => 'width: 100%;',
                    '{{WRAPPER}} #edd_profile_editor_form .select.edd-select'      => 'width: 100%;',
                ],
            ]
        );

        $this->add_control(
            'fullwidth_textarea',
            [
                'label' => esc_html__('Fullwidth Texarea', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'bdthemes-element-pack'),
                'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form textarea' => 'width: 100%;',
                ],
            ]
        );

        $this->add_control(
            'fullwidth_button',
            [
                'label' => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'bdthemes-element-pack'),
                'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
                'selectors' => [
                    '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'width: 100%;',
                ],
            ]
        );
        $this->add_control(
            'profile_fieldset_color',
            [
                'label'     => __('Fieldset Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-switcher-item-content fieldset' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'profile_fieldset_width',
            [
                'label'         => __('Fieldset Width', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 10,
                        'step'  => 1,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}}  .bdt-switcher-item-content fieldset' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'profile_editor_fieldset_padding',
            [
                'label'                 => __('Padding', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .bdt-switcher-item-content fieldset'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings    = $this->get_settings_for_display();
        $id          = $this->get_id();
        $stickyClass = '';
        if (isset($settings['nav_sticky_mode']) && $settings['nav_sticky_mode'] == 'yes') {
            $stickyClass = 'bdt-sticky-custom';
        }

        $this->add_render_attribute(
            [
                'tabs_sticky_data' => [
                    'data-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id"                => 'bdt-tabs-' . $this->get_id(),
                                "status"            => $stickyClass,
                                "activeHash"        => $settings['active_hash'],
                                "hashTopOffset"     => (isset($settings['hash_top_offset']['size']) && !empty($settings['hash_top_offset']['size'])) ? $settings['hash_top_offset']['size'] : 70,
                                "hashScrollspyTime" => (isset($settings['hash_scrollspy_time']['size']) ? $settings['hash_scrollspy_time']['size'] : 1500),
                                "navStickyOffset"   => (isset($settings['nav_sticky_offset']['size']) ? $settings['nav_sticky_offset']['size'] : 1),
                                "activeItem" => (!empty($settings['active_item'])) ? $settings['active_item'] : NULL,
                                "linkWidgetId" => $this->get_id(),
                            ])
                        ),
                    ],
                ],
            ]
        );

        $this->add_render_attribute('tabs', 'id', 'bdt-tabs-' . esc_attr($id));
        $this->add_render_attribute('tabs', 'class', 'bdt-tabs ');
        $this->add_render_attribute('tabs', 'class', 'bdt-tabs-' . $settings['tab_layout']);

        if ($settings['fullwidth_on_mobile']) {
            $this->add_render_attribute('tabs', 'class', 'fullwidth-on-mobile');
        }

?>


        <div class="bdt-tabs-area">

            <div <?php echo $this->get_render_attribute_string('tabs'); ?> <?php echo $this->get_render_attribute_string('tabs_sticky_data'); ?>>
                <?php
                if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {
                    echo '<div class="bdt-grid-collapse"  bdt-grid>';
                }
                ?>

                <?php if ('bottom' == $settings['tab_layout']) : ?>
                    <?php $this->tabs_content(); ?>
                <?php endif; ?>

                <?php $this->desktop_tab_items(); ?>


                <?php if ('bottom' != $settings['tab_layout']) : ?>
                    <?php $this->tabs_content(); ?>
                <?php endif; ?>

                <?php
                if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {
                    echo "</div>";
                }
                ?>
                <a href="#" id="bottom-anchor-<?php echo esc_attr($id); ?>" data-bdt-hidden></a>
            </div>
        </div>
    <?php
    }

    public function tabs_content() {
        $settings = $this->get_settings_for_display();
        $id       = $this->get_id();

        $this->add_render_attribute('switcher-width', 'class', 'bdt-switcher-wrapper');

        if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {

            if (768 == $settings['media']) {
                $this->add_render_attribute('switcher-width', 'class', 'bdt-width-expand@s');
            } else {
                $this->add_render_attribute('switcher-width', 'class', 'bdt-width-expand@m');
            }
        }

    ?>

        <div <?php echo $this->get_render_attribute_string('switcher-width'); ?>>
            <div id="bdt-tab-content-<?php echo esc_attr($id); ?>" class="bdt-switcher bdt-switcher-item-content">
                <?php foreach ($settings['tabs'] as $index => $item) : ?>
                    <?php

                    $tab_count = $index + 1;
                    $tab_count_active = '';
                    if ($tab_count === $settings['active_item']) {
                        $tab_count_active = 'bdt-active';
                    }

                    ?>
                    <div class="<?php echo $tab_count_active; ?>" data-content-id="<?php echo strtolower(preg_replace('#[ -]+#', '-', trim(preg_replace("![^a-z0-9]+!i", " ", esc_html($item['tab_title']))))); ?>">
                        <div>
                            <?php if (!empty($item['tab_content'])) {
                                echo do_shortcode('[' . $item['tab_content'] . ']');
                            } ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php
    }

    public function desktop_tab_items() {
        $settings = $this->get_settings_for_display();
        $id       = $this->get_id();

        if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {

            $this->add_render_attribute('tabs-width', 'class', 'bdt-tab-wrapper');

            if ('right' == $settings['tab_layout']) {
                $this->add_render_attribute('tabs-width', 'class', 'bdt-flex-last@m');
            }

            if (768 == $settings['media']) {
                $this->add_render_attribute('tabs-width', 'class', 'bdt-width-auto@s');
                if ('right' == $settings['tab_layout']) {
                    $this->add_render_attribute('tabs-width', 'class', 'bdt-flex-last');
                }
            } else {
                $this->add_render_attribute('tabs-width', 'class', 'bdt-width-auto@m');
            }
        }

        $this->add_render_attribute(
            [
                'tab-settings' => [
                    'class' => [
                        'bdt-tab',
                        ('' !== $settings['tab_layout']) ? 'bdt-tab-' . $settings['tab_layout'] : '',
                        ('' != $settings['align'] and 'left' != $settings['tab_layout'] and 'right' != $settings['tab_layout']) ? (('justify' != $settings['align']) ? 'bdt-flex-' . $settings['align'] : 'bdt-child-width-expand') : ''
                    ]
                ]
            ]
        );

        $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'connect: #bdt-tab-content-' . esc_attr($id) . ';');

        if (isset($settings['tab_transition']) and $settings['tab_transition']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'animation: bdt-animation-' . $settings['tab_transition'] . ';');
        }
        if (isset($settings['duration']['size']) and $settings['duration']['size']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'duration: ' . $settings['duration']['size'] . ';');
        }
        if (isset($settings['media']) and $settings['media']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'media: ' . intval($settings['media']) . ';');
        }
        if ('yes' != $settings['swiping_on_mobile']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'swiping: false;');
        }

        if ($settings['tabs_match_height']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-height-match', 'target: > .bdt-tabs-item > .bdt-tabs-item-title; row: false;');
        }

        if (isset($settings['nav_sticky_mode']) && 'yes' == $settings['nav_sticky_mode']) {
            $this->add_render_attribute('tabs-sticky', 'data-bdt-sticky', 'bottom: #bottom-anchor-' . $id . ';');

            if ($settings['nav_sticky_offset']['size']) {
                $this->add_render_attribute('tabs-sticky', 'data-bdt-sticky', 'offset: ' . $settings['nav_sticky_offset']['size'] . ';');
            }
            if ($settings['nav_sticky_on_scroll_up']) {
                $this->add_render_attribute('tabs-sticky', 'data-bdt-sticky', 'show-on-up: true; animation: bdt-animation-slide-top');
            }
        }

    ?>
        <div <?php echo ($this->get_render_attribute_string('tabs-width')); ?>>
            <div <?php echo ($this->get_render_attribute_string('tabs-sticky')); ?>>
                <div <?php echo ($this->get_render_attribute_string('tab-settings')); ?>>
                    <?php foreach ($settings['tabs'] as $index => $item) :

                        $tab_count = $index + 1;
                        $tab_id = ($item['tab_title']) ? element_pack_string_id($item['tab_title']) : $id . $tab_count;
                        // $tab_id = 'bdt-tab-' . $tab_id;
                        $tab_id = 'bdt-tab-' . strtolower(preg_replace('#[ -]+#', '-', trim(preg_replace("![^a-z0-9]+!i", " ", $tab_id))));

                        $this->add_render_attribute('tabs-item', 'class', 'bdt-tabs-item', true);
                        if (empty($item['tab_title'])) {
                            $this->add_render_attribute('tabs-item', 'class', 'bdt-has-no-title');
                        }
                        if ($tab_count === $settings['active_item']) {
                            $this->add_render_attribute('tabs-item', 'class', 'bdt-active');
                        }

                        if (!isset($item['tab_icon']) && !Icons_Manager::is_migration_allowed()) {
                            // add old default
                            $item['tab_icon'] = 'fas fa-book';
                        }

                        $migrated = isset($item['__fa4_migrated']['tab_select_icon']);
                        $is_new   = empty($item['tab_icon']) && Icons_Manager::is_migration_allowed();

                        $this->add_render_attribute('tab-link', 'data-title', strtolower(preg_replace('#[ -]+#', '-', trim(preg_replace("![^a-z0-9]+!i", " ", esc_html($item['tab_title']))))), true);

                        if (empty($item['tab_title'])) {
                            $this->add_render_attribute('tab-link', 'data-title', $this->get_id() . '-' . $tab_count, true);
                        }

                        $this->add_render_attribute('tab-link', 'class', 'bdt-tabs-item-title', true);
                        $this->add_render_attribute('tab-link', 'id', esc_attr($tab_id), true);
                        $this->add_render_attribute('tab-link', 'data-tab-index', esc_attr($index), true);
                        $this->add_render_attribute('tab-link', 'href', '#', true);

                    ?>
                        <div <?php echo $this->get_render_attribute_string('tabs-item'); ?>>
                            <a <?php echo $this->get_render_attribute_string('tab-link'); ?>>
                                <div class="bdt-tab-text-wrapper bdt-flex-column">

                                    <div class="bdt-tab-title-icon-wrapper">

                                        <?php if ('' != $item['tab_select_icon']['value'] and 'left' == $settings['icon_align']) : ?>
                                            <span class="bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

                                                <?php if ($is_new || $migrated) :
                                                    Icons_Manager::render_icon($item['tab_select_icon'], [
                                                        'aria-hidden' => 'true',
                                                        'class'       => 'fa-fw'
                                                    ]);
                                                else : ?>
                                                    <i class="<?php echo esc_attr($item['tab_icon']); ?>" aria-hidden="true"></i>
                                                <?php endif; ?>

                                            </span>
                                        <?php endif; ?>

                                        <?php if ($item['tab_title']) : ?>
                                            <span class="bdt-tab-text">
                                                <?php echo wp_kses($item['tab_title'], element_pack_allow_tags('title')); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ('' != $item['tab_select_icon']['value'] and 'right' == $settings['icon_align']) : ?>
                                            <span class="bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

                                                <?php if ($is_new || $migrated) :
                                                    Icons_Manager::render_icon($item['tab_select_icon'], [
                                                        'aria-hidden' => 'true',
                                                        'class'       => 'fa-fw'
                                                    ]);
                                                else : ?>
                                                    <i class="<?php echo esc_attr($item['tab_icon']); ?>" aria-hidden="true"></i>
                                                <?php endif; ?>

                                            </span>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php
    }
}
