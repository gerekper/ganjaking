<?php

namespace ElementPack\Modules\Faq\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FAQ extends Module_Base {
    use Group_Control_Query;
    use Global_Widget_Controls;

    private $_query = null;

    public function get_name() {
        return 'bdt-faq';
    }

    public function get_title() {
        return BDTEP . esc_html__('FAQ', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-faq';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['faq', 'accordion', 'tabs', 'toggle'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-faq'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-faq'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/jGGdCuSjesY';
    }

    public function on_import($element) {
        if (is_plugin_active('bdthemes-faq/bdthemes-faq.php')) {
            if (!get_post_type_object($element['settings']['posts_post_type'])) {
                $element['settings']['posts_post_type'] = 'faq';
            }
        } else {
            if (!get_post_type_object($element['settings']['posts_post_type'])) {
                $element['settings']['posts_post_type'] = 'post';
            }
        }

        return $element;
    }

    public function get_query() {
        return $this->_query;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'multicolumns',
            [
                'label' => __('Multi Columns', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label'     => __('Item Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item + .bdt-faq-item' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_column_gap',
            [
                'label'     => __('Column Gap', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq.bdt-faq-multi-columns' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'multicolumns' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'collapsible',
            [
                'label'     => __('Collapsible All Item', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'multiple',
            [
                'label' => __('Multiple Open', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'icon',
            [
                'label'   => __('Show Icon', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'closed_icon',
            [
                'label'       => __('Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::ICONS,
                // 'default'          => [
                //     'value'   => 'far fa-question-circle',
                //     'library' => 'fa-regular',
                // ],
                'recommended' => [
                    'fa-solid'   => [
                        'question',
                        'question-circle',
                        'plus',
                        'plus-circle',
                        'plus-square',
                    ],
                    'fa-regular' => [
                        'question-circle',
                        'plus-square',
                        'arrow-alt-circle-right',
                        'caret-square-right',
                    ],
                ],
                'skin'        => 'inline',
                'label_block' => false,
                'condition'   => [
                    'icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'opened_icon',
            [
                'label'       => __('Active Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::ICONS,
                // 'default'          => [
                //     'value'   => 'fas fa-check',
                //     'library' => 'fa-solid',
                // ],
                'recommended' => [
                    'fa-solid'   => [
                        'check',
                        'check-circle',
                        'check-double',
                        'check-square',
                        'calendar-check',
                        'clipboard-check',
                        'spell-check',
                        'user-check',
                    ],
                    'fa-regular' => [
                        'check-circle',
                        'check-square',
                        'calendar-check',
                        'arrow-alt-circle-down',
                        'caret-square-down',
                    ],
                ],
                'skin'        => 'inline',
                'label_block' => false,
                'condition'   => [
                    'icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_filter_bar',
            [
                'label'     => esc_html__('Filter Bar', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );
        $post_types = $this->getGroupControlQueryPostTypes();

        foreach ($post_types as $key => $post_type) {
            $taxonomies = $this->get_taxonomies($key);
            if (!$taxonomies[$key]) {
                continue;
            }
            $this->add_control(
                'taxonomy_' . $key,
                [
                    'label'     => __('Taxonomies', 'bdthemes-element-pack'),
                    'type'      => Controls_Manager::SELECT,
                    'options'   => $taxonomies[$key],
                    'default'   => key($taxonomies[$key]),
                    'condition' => [
                        'posts_source' => $key,
                        'show_filter_bar' => 'yes'
                    ],
                ]
            );
        }
        $this->add_control(
            'active_hash',
            [
                'label'     => esc_html__('Hash Location', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'show_filter_bar' => 'yes',
                ],
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
                    'active_hash'     => 'yes',
                    'show_filter_bar' => 'yes',
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
                    'size' => 1000,
                ],
                'condition'  => [
                    'active_hash'     => 'yes',
                    'show_filter_bar' => 'yes',
                ],

            ]
        );


        $this->add_control(
            'excerpt_length',
            [
                'label'       => __('Text Limit', 'bdthemes-element-pack'),
                'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 50,
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'strip_shortcode',
            [
                'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'show_read_more',
            [
                'label'   => __('Read More', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
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
            'schema_activity',
            [
                'label'       => __('Schema Active', 'bdthemes-element-pack'),
                'description' => __('Warning: If you have multiple FAQ widgets on the same page so don\'t activate schema for both FAQ widgets so you will get errors on the google index. Activate the only one which you want to show on google search.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'yes',
                'separator'   => 'before',
            ]
        );

        $this->end_controls_section();
        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack') . BDTEP_NC,
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->register_query_builder_controls();
        if (is_plugin_active('bdthemes-faq/bdthemes-faq.php')) {
            $this->update_control(
                'posts_source',
                [
                    'label'   => __('Source', 'bdthemes-element-pack'),
                    'type'    => Controls_Manager::SELECT,
                    'options' => $this->getGroupControlQueryPostTypes(),
                    'default' => 'faq',
                ]
            );
        }

        $this->end_controls_section();


        $this->start_controls_section(
            'section_content_button',
            [
                'label'     => esc_html__('Read More Button', 'bdthemes-element-pack'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'more_button_button_text',
            [
                'label'       => esc_html__('Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
                'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'faq_more_button_icon',
            [
                'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'more_button_icon',
            ]
        );

        $this->add_control(
            'more_button_icon_align',
            [
                'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'right',
                'options'   => [
                    'left'  => esc_html__('Before', 'bdthemes-element-pack'),
                    'right' => esc_html__('After', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'faq_more_button_icon[value]!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'more_button_icon_indent',
            [
                'label'     => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 8,
                ],
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'faq_more_button_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label'       => __('Alignment', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'options'     => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'     => 'left',
                'toggle'      => false,
                'label_block' => false,
                'render_type' => 'template',
                'selectors'   => [
                    '{{WRAPPER}} .bdt-faq-title-text' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'glassmorphism_effect',
            [
                'label'       => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::SWITCHER,
                'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

            ]
        );

        $this->add_control(
            'glassmorphism_blur_level',
            [
                'label'     => __('Blur Level', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'default'   => [
                    'size' => 5
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-title' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
                ],
                'condition' => [
                    'glassmorphism_effect' => 'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'title_background',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-accordion-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-title' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title',
            ]
        );

        $this->add_responsive_control(
            'title_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-title-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'title_shadow',
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-accordion-title',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_active',
            [
                'label' => __('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'active_title_background',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-title',
            ]
        );

        $this->add_control(
            'active_title_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-title' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'active_title_shadow',
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'active_title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-title',
            ]
        );

        $this->add_responsive_control(
            'active_title_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
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
                'name'     => 'hover_title_background',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title:hover',
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title:hover' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'hover_title_shadow',
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'hover_title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title:hover',
            ]
        );

        $this->add_responsive_control(
            'hover_title_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item .bdt-accordion-title:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_icon',
            [
                'label' => __('Icon', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label'       => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::CHOOSE,
                'options'     => [
                    'left'  => [
                        'title' => __('Start', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => __('End', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default'     => is_rtl() ? 'right' : 'left',
                'toggle'      => false,
                'label_block' => false,
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
            'icon_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-title .bdt-accordion-icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-title .bdt-accordion-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'icon_background_color',
                'selector' => '{{WRAPPER}} .bdt-accordion-icon'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'icon_border',
                'selector' => '{{WRAPPER}} .bdt-accordion-icon',
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_space',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 10
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion-icon.bdt-flex-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-accordion-icon.bdt-flex-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label'     => __('Icon Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion-title .bdt-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'icon_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-accordion-icon',
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
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item.bdt-open .bdt-accordion-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'icon_active_background_color',
                'selector' => '{{WRAPPER}} .bdt-faq-item.bdt-open .bdt-accordion-icon'
            ]
        );

        $this->add_control(
            'icon_active_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'icon_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq-item.bdt-open .bdt-accordion-icon' => 'border-color: {{VALUE}};',
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
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item:hover .bdt-accordion-icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-accordion .bdt-faq-item:hover .bdt-accordion-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'icon_hover_background_color',
                'selector' => '{{WRAPPER}} .bdt-faq-item:hover .bdt-accordion-icon'
            ]
        );

        $this->add_control(
            'icon_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'icon_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq-item:hover .bdt-accordion-icon' => 'border-color: {{VALUE}};',
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

        $this->add_control(
            'content_glassmorphism_effect',
            [
                'label'       => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::SWITCHER,
                'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

            ]
        );

        $this->add_control(
            'content_glassmorphism_blur_level',
            [
                'label'     => __('Blur Level', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'default'   => [
                    'size' => 5
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-content' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
                ],
                'condition' => [
                    'content_glassmorphism_effect' => 'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'content_background_color',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-accordion-content',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-content' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'content_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-accordion .bdt-accordion-content',
            ]
        );

        $this->add_responsive_control(
            'content_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
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
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_spacing',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-accordion .bdt-accordion-content' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'content_shadow',
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-accordion-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .bdt-accordion .bdt-accordion-content',
            ]
        );

        $this->end_controls_section();

        $this->register_style_controls_filter();

        $this->start_controls_section(
            'section_style_more_button',
            [
                'label'     => esc_html__('Read More Button', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'more_button_spacing',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 35,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_more_button_style');

        $this->start_controls_tab(
            'tab_more_button_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'more_button_text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'more_button_background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'more_button_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'more_button_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'more_button_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'more_button_shadow',
                'selector' => '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'more_button_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_more_button_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'more_button_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button:hover'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'more_button_background_hover_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'more_button_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'more_button_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-faq .bdt-faq-item .bdt-faq-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'more_button_hover_animation',
            [
                'label' => esc_html__('Animation', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function get_taxonomies($post_type = '') {
        $_taxonomies = [];
        if ($post_type) {
            $taxonomies = get_taxonomies(['public' => true, 'object_type' => [$post_type]], 'object');
            $tax = array_diff_key(wp_list_pluck($taxonomies, 'label', 'name'), []);
            $_taxonomies[$post_type] = count($tax) !== 0 ? $tax : '';
        }
        return $_taxonomies;
    }


    /**
     * Get post query builder arguments
     */
    public function query_posts($posts_per_page) {
        $settings = $this->get_settings();
        $args = [];
        if ($posts_per_page) {
            $args['posts_per_page'] = $posts_per_page;
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        $this->_query = new \WP_Query($args);
    }

    public function filter_menu_terms() {
        $settings = $this->get_settings_for_display();
        if (isset($settings['taxonomy_' . $settings['posts_source']])) :
            $taxonomy = $settings['taxonomy_' . $settings['posts_source']];
            $categories = get_the_terms(get_the_ID(), $taxonomy);
            $_categories = [];
            if ($categories) {
                foreach ($categories as $category) {
                    $_categories[$category->slug] = strtolower($category->slug);
                }
            }
            return implode(' ', $_categories);
        endif;
    }
    protected function filter_menu_categories() {
        $settings = $this->get_settings_for_display();
        $include_Categories = $settings['posts_include_term_ids'];
        $exclude_Categories = $settings['posts_exclude_term_ids'];
        $post_options = [];
        if (isset($settings['taxonomy_' . $settings['posts_source']])) {
            $taxonomy = $settings['taxonomy_' . $settings['posts_source']];
            $params = [
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
                'include' => $include_Categories,
                'exclude' => $exclude_Categories,
            ];
            $post_categories = get_terms($params);
            if (is_wp_error($post_categories)) {
                return $post_options;
            }
            if (false !== $post_categories and is_array($post_categories)) {
                foreach ($post_categories as $category) {
                    $post_options[$category->slug] = $category->name;
                }
            }
        }

        return $post_options;
    }

    public function render_title() {
        $settings = $this->get_settings_for_display();
        $faq_icon = get_post_meta(get_the_ID(), 'bdthemes_faq_icon', true);
        $faq_icon = (!empty($faq_icon)) ? $faq_icon : 'ep-icon-question';

        // if ('left' == $settings['align']) {
        // 	$this->add_render_attribute('faq_title', 'class', 'bdt-accordion-title bdt-faq-align-left', true);
        // } elseif ('right' == $settings['align']) {
        // 	$this->add_render_attribute('faq_title', 'class', 'bdt-accordion-title bdt-faq-align-right', true);
        // } elseif ('center' == $settings['align']) {
        // 	$this->add_render_attribute('faq_title', 'class', 'bdt-accordion-title bdt-faq-align-center', true);
        // } else {
        // }
        $this->add_render_attribute('faq_title', 'class', 'bdt-accordion-title', true);

        $this->add_render_attribute('faq_title', 'itemprop', 'name', true);

?>
        <div role="main" <?php echo $this->get_render_attribute_string('faq_title'); ?>>

            <?php if ($settings['icon']) : ?>
                <span class="bdt-accordion-icon bdt-flex-align-<?php echo esc_attr($settings['icon_align']); ?>" aria-hidden="true">
                    <?php if ($settings['closed_icon']['value']) : ?>
                        <span class="bdt-accordion-icon-closed">
                            <?php Icons_Manager::render_icon($settings['closed_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                    <?php else : ?>
                        <i class="bdt-accordion-icon-closed <?php echo esc_attr($faq_icon); ?>"></i>
                    <?php endif; ?>

                    <?php if ($settings['opened_icon']['value']) : ?>
                        <span class="bdt-accordion-icon-opened">
                            <?php Icons_Manager::render_icon($settings['opened_icon'], ['aria-hidden' => 'true']); ?>
                        </span>
                    <?php else : ?>
                        <i class="bdt-accordion-icon-opened ep-icon-checkmark"></i>
                    <?php endif; ?>
                </span>
            <?php endif; ?>

            <span class="bdt-faq-title-text bdt-flex bdt-flex-middle bdt-width">
                <?php echo esc_html(get_the_title()); ?>
            </span>

        </div>
    <?php
    }

    public function render_excerpt() {
        $settings = $this->get_settings_for_display();

        $strip_shortcode = $this->get_settings_for_display('strip_shortcode');

    ?>
        <div class="bdt-faq-excerpt" <?php if ($settings['schema_activity'] == 'yes') : ?> itemprop="text" <?php endif; ?>>
            <?php
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo element_pack_custom_excerpt($this->get_settings_for_display('excerpt_length'), $strip_shortcode);
            }
            ?>
        </div>
        <?php

    }

    public function render_more_button_button($post) {
        $settings = $this->get_settings_for_display();

        $animation = ($settings['more_button_hover_animation']) ? ' elementor-animation-' . $settings['more_button_hover_animation'] : '';

        if (!isset($settings['more_button_icon']) && !Icons_Manager::is_migration_allowed()) {
            // add old default
            $settings['more_button_icon'] = 'fas fa-arrow-right';
        }

        $migrated = isset($settings['__fa4_migrated']['faq_more_button_icon']);
        $is_new   = empty($settings['more_button_icon']) && Icons_Manager::is_migration_allowed();


        if ('yes' == $settings['show_read_more']) : ?>
            <div class="bdt-clearfix"></div>
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="bdt-faq-button<?php echo esc_attr($animation); ?>"><?php echo esc_html($settings['more_button_button_text']); ?>
                <?php if ($settings['faq_more_button_icon']['value']) : ?>
                    <span class="bdt-button-icon-align-<?php echo esc_attr($settings['more_button_icon_align']); ?>">
                        <?php if ($is_new || $migrated) :
                            Icons_Manager::render_icon($settings['faq_more_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                        else : ?>
                            <i class="<?php echo esc_attr($settings['more_button_icon']); ?>" aria-hidden="true"></i>
                        <?php endif; ?>

                    </span>
                <?php endif; ?>

            </a>
        <?php endif;
    }



    public function render_filter_menu() {
        $settings  = $this->get_settings_for_display();
        $faq_categories = $this->filter_menu_categories();

        $this->add_render_attribute(
            [
                'bdt-faq-hash-data' => [
                    'data-hash-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id"                => 'bdt-faq-' . $this->get_id(),
                                'activeHash'        => isset($settings['active_hash']) ? $settings['active_hash'] : 'no',
                                'hashTopOffset'     => isset($settings['hash_top_offset']) ? $settings['hash_top_offset']['size'] : 70,
                                'hashScrollspyTime' => isset($settings['hash_scrollspy_time']) ? $settings['hash_scrollspy_time']['size'] : 1000,
                            ])
                        ),
                    ],
                ],
            ]
        );
        ?>

        <div class="bdt-ep-grid-filters-wrapper" id="<?php echo 'bdt-faq-' . $this->get_id(); ?>" <?php echo $this->get_render_attribute_string('bdt-faq-hash-data'); ?>>
            <button class="bdt-button bdt-button-default bdt-hidden@m" type="button"><?php esc_html_e('Filter', 'bdthemes-element-pack'); ?></button>
            <div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-hidden@m">
                <ul class="bdt-nav bdt-dropdown-nav">
                    <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
                        <a href="#"><?php esc_html_e('All', 'bdthemes-element-pack'); ?></a>
                    </li>
                    <?php foreach ($faq_categories as $key => $category) :
                        printf('<li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*=\'%1$s\']"><a href="#">%2$s</a></li>', $key, $category);
                    endforeach; ?>


                </ul>
            </div>
            <ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
                <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
                    <a href="#"><?php esc_html_e('All', 'bdthemes-element-pack'); ?></a>
                </li>

                <?php foreach ($faq_categories as $key => $category) :
                    printf('<li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*=\'%1$s\']"><a href="#">%2$s</a></li>', $key, $category);
                endforeach; ?>
            </ul>
        </div>
    <?php
    }

    public function render_header($settings, $id) {

        $this->add_render_attribute(
            [
                'bdt-faq-settings' => [
                    'id'                 => $id,
                    'class'              => 'bdt-accordion',
                    'data-bdt-accordion' => [
                        wp_json_encode([
                            "targets"      => "> div > .bdt-faq-item",
                            "collapsible" => $settings["collapsible"] ? true : false,
                            "multiple"    => $settings["multiple"] ? true : false,
                            "transition"  => "ease-in-out",
                            "active"      => ("" !== $settings["active_item"]) ? $settings["active_item"] - 1 : false,
                        ]),
                    ],
                ],
            ]
        );

        $this->add_render_attribute(
            [
                'bdt-faq-settings' => [
                    'class' => ('yes' != $settings["multicolumns"]) ? 'bdt-faq' : 'bdt-faq bdt-faq-multi-columns',
                ],
            ]
        );

        $this->add_render_attribute('faq-wrapper', 'class', 'bdt-faq-wrapper');
        if ($settings['show_filter_bar']) {
            $this->add_render_attribute('faq-wrapper', 'data-bdt-filter', 'target: #bdt-accordion-' . $this->get_id() . ' > div');
        }
        if ($settings['multicolumns']) {
            $this->add_render_attribute('faq-multicolumns', 'data-bdt-filter', 'target: .bdt-multicolumns-' . $this->get_id());
        }
    ?>
        <div <?php $this->print_render_attribute_string('faq-multicolumns') ?>>
            <div <?php echo $this->get_render_attribute_string('faq-wrapper'); ?>>
                <?php if ($settings['show_filter_bar']) {
                    $this->render_filter_menu();
                } ?>
                <div <?php echo $this->get_render_attribute_string('bdt-faq-settings'); ?> itemscope <?php if ($settings['schema_activity'] == 'yes') : ?> itemtype="https://schema.org/FAQPage" <?php endif; ?>>
                    <?php if ('yes' != $settings["multicolumns"]) { ?>
                        <div><?php
                            }
                        }
                        public function render_footer() { ?>
                        </div>
                </div>
            </div>
        </div>
    <?php
                        }

                        public function render_post($settings) {
                            $settings = $this->get_settings_for_display();
                            global $post;
                            $element_key = 'faq-item-' . $post->ID;
                            $this->add_render_attribute($element_key, 'class', 'bdt-faq-item');

                            if ($settings['schema_activity'] == 'yes') {
                                $this->add_render_attribute($element_key, 'itemscope');
                                $this->add_render_attribute($element_key, 'itemprop', 'mainEntity');
                                $this->add_render_attribute($element_key, 'itemtype', 'https://schema.org/Question');
                            }
                            if ($settings['show_filter_bar']) {
                                $this->add_render_attribute($element_key, 'data-filter', $this->filter_menu_terms(), true);
                            }
                            if ('left' == $settings['align']) {
                                $this->add_render_attribute('faq_content', 'class', 'bdt-accordion-content bdt-faq-align-left', true);
                            } elseif ('right' == $settings['align']) {
                                $this->add_render_attribute('faq_content', 'class', 'bdt-accordion-content bdt-faq-align-right', true);
                            } elseif ('center' == $settings['align']) {
                                $this->add_render_attribute('faq_content', 'class', 'bdt-accordion-content bdt-faq-align-center', true);
                            } else {
                                $this->add_render_attribute('faq_content', 'class', 'bdt-accordion-content', true);
                            }
                            if ($settings['schema_activity'] == 'yes') {
                                $this->add_render_attribute('faq_content', 'itemscope');
                                $this->add_render_attribute('faq_content', 'itemprop', 'acceptedAnswer', true);
                                $this->add_render_attribute('faq_content', 'itemtype', 'https://schema.org/Answer', true);
                            } ?>

        <div <?php echo $this->get_render_attribute_string($element_key); ?>>
            <?php $this->render_title(); ?>
            <div <?php echo $this->get_render_attribute_string('faq_content'); ?>>
                <?php
                            $this->render_excerpt();
                            $this->render_more_button_button($post);
                ?>
            </div>
        </div>
<?php
                        }

                        protected function render() {
                            /**
                             * !TODO
                             * post meta aded by talib if you need to use image in faq, use it
                             * $image_id = get_post_meta($post->ID, 'bdt_faq_image_id', true);
                             * $image = wp_get_attachment_image($image_id, 'large');
                             */
                            $settings = $this->get_settings_for_display();
                            $id       = 'bdt-accordion-' . $this->get_id();
                            $this->query_posts($settings['posts_per_page']);
                            $wp_query = $this->get_query();
                            if (!$wp_query->found_posts) {
                                return;
                            }
                            $this->render_header($settings, $id);

                            $total_item = $settings['posts_per_page'];
                            $half_item  = ceil($settings['posts_per_page'] / 2);
                            $count      = 0;

                            $this->add_render_attribute('accordion-settings', 'class', 'bdt-faq-column');

                            while ($wp_query->have_posts()) {
                                $wp_query->the_post();

                                if ('yes' != $settings["multicolumns"]) {
                                    $this->render_post($settings);
                                } else {
                                    if ($count == 0) {
                                        echo '<div ' . $this->get_render_attribute_string('accordion-settings') . '>';
                                        $this->render_post($settings);
                                    } elseif ($count < $half_item) {
                                        $this->render_post($settings);
                                    } elseif ($count == $half_item) {
                                        printf('</div><div class="%s">', 'bdt-faq-column bdt-multicolumns-' . $this->get_id());
                                        $this->render_post($settings);
                                    } elseif ($count > $half_item) {
                                        $this->render_post($settings);
                                    } elseif ($count == $total_item) {
                                        echo '</div>';
                                    }

                                    $count++;
                                }
                            }

                            $this->render_footer();

                            wp_reset_postdata();
                        }
                    }
