<?php

namespace ElementPack\Modules\Timeline\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use ElementPack\Utils;
use Elementor\Icons_Manager;

use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Modules\Timeline\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Timeline extends Module_Base {

    use Group_Control_Query;

    private $_query = null;

    public function get_name() {
        return 'bdt-timeline';
    }

    public function get_title() {
        return BDTEP . esc_html__('Timeline', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-timeline';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['timeline', 'history', 'statistics'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-timeline', 'ep-font'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['timeline', 'ep-scripts'];
        } else {
            return ['timeline', 'ep-timeline'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/lp4Zqn6niXU';
    }

    public function register_skins() {
        $this->add_skin(new Skins\Skin_Olivier($this));
    }

    public function get_query() {
        return $this->_query;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'timeline_source',
            [
                'label'   => esc_html__('Source', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'post',
                'options' => [
                    'post'   => __('Post', 'bdthemes-element-pack'),
                    'custom' => __('Custom Content', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'timeline_align',
            [
                'label'     => esc_html__('Layout', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'center',
                'toggle'    => false,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'condition' => [
                    '_skin' => '',
                ]
            ]
        );

        $this->add_control(
            'visible_items',
            [
                'label'     => esc_html__('Visible Items', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 4,
                'condition' => [
                    '_skin' => 'bdt-olivier',
                ]
            ]
        );

        $this->end_controls_section();

        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'timeline_source' => 'post'
                ]
            ]
        );

        $this->register_query_builder_controls();

        $this->update_control(
            'posts_per_page',
            [
                'default' => 4,
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_custom_content',
            [
                'label'     => esc_html__('Custom Content', 'bdthemes-element-pack'),
                'condition' => [
                    'timeline_source' => 'custom'
                ]
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'timeline_title',
            [
                'label'   => esc_html__('Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('This is Timeline Item 1 Title', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'timeline_date',
            [
                'label'   => esc_html__('Date', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::TEXT,
                'default' => '31 December 2018',
            ]
        );

        $repeater->add_control(
            'timeline_image',
            [
                'label'   => esc_html__('Image', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'timeline_text',
            [
                'label'   => esc_html__('Content', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::WYSIWYG,
                'default' => esc_html__('I am timeline item content. Click edit button to change this text. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'timeline_link',
            [
                'label'       => esc_html__('Button Link', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://bdthemes.com',
                'default'     => 'https://bdthemes.com',
            ]
        );

        $repeater->add_control(
            'timeline_select_icon',
            [
                'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'timeline_icon',
                'default'          => [
                    'value'   => 'fas fa-file-alt',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'timeline_items',
            [
                'label'       => esc_html__('Timeline Items', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'timeline_title'       => esc_html__('This is Timeline Item 1 Title', 'bdthemes-element-pack'),
                        'timeline_text'        => esc_html__('I am timeline item content. Click edit button to change this text. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', 'bdthemes-element-pack'),
                        'timeline_select_icon' => ['value' => 'fas fa-file-alt', 'library' => 'fa-solid'],
                    ],
                    [
                        'timeline_title'       => esc_html__('This is Timeline Item 2 Title', 'bdthemes-element-pack'),
                        'timeline_text'        => esc_html__('I am timeline item content. Click edit button to change this text. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', 'bdthemes-element-pack'),
                        'timeline_select_icon' => ['value' => 'fas fa-file-alt', 'library' => 'fa-solid'],
                    ],
                    [
                        'timeline_title'       => esc_html__('This is Timeline Item 3 Title', 'bdthemes-element-pack'),
                        'timeline_text'        => esc_html__('I am timeline item content. Click edit button to change this text. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', 'bdthemes-element-pack'),
                        'timeline_select_icon' => ['value' => 'fas fa-file-alt', 'library' => 'fa-solid'],
                    ],
                    [
                        'timeline_title'       => esc_html__('This is Timeline Item 4 Title', 'bdthemes-element-pack'),
                        'timeline_text'        => esc_html__('I am timeline item content. Click edit button to change this text. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', 'bdthemes-element-pack'),
                        'timeline_select_icon' => ['value' => 'fas fa-file-alt', 'library' => 'fa-solid'],
                    ],
                ],
                'title_field' => '{{{ timeline_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_button',
            [
                'label'     => esc_html__('Readmore Button', 'bdthemes-element-pack'),
                'condition' => [
                    'show_readmore' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label'       => esc_html__('Read More Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label'   => __('Button Size', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'sm',
                'options' => [
                    'xs' => __('Extra Small', 'bdthemes-element-pack'),
                    'sm' => __('Small', 'bdthemes-element-pack'),
                    'md' => __('Medium', 'bdthemes-element-pack'),
                    'lg' => __('Large', 'bdthemes-element-pack'),
                    'xl' => __('Extra Large', 'bdthemes-element-pack'),
                ]
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label'            => esc_html__('Button Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'label_block' => false,
                'skin' => 'inline'
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'right',
                'options'   => [
                    'left'  => esc_html__('Left', 'bdthemes-element-pack'),
                    'right' => esc_html__('Right', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'button_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'icon_indent',
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
                    'button_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-timeline .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => esc_html__('Additional Options', 'bdthemes-element-pack')
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label'   => esc_html__('Image', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'   => esc_html__('Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_tags',
            [
                'label'     => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'h4',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_title' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'title_link',
            [
                'label'     => esc_html__('Title Link', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_meta',
            [
                'label'   => esc_html__('Meta Data', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label'   => esc_html__('Show Text', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label'       => esc_html__('Text Limit', 'bdthemes-element-pack'),
                'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 15,
                'condition'   => [
                    'show_excerpt'    => 'yes',
                    'timeline_source' => 'post',
                ],
            ]
        );

        $this->add_control(
            'strip_shortcode',
            [
                'label'     => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'show_excerpt'    => 'yes',
                    'timeline_source' => 'post',
                ],
            ]
        );

        $this->add_control(
            'show_readmore',
            [
                'label'   => esc_html__('Read More', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'item_animation',
            [
                'label' => esc_html__('Scroll Animation', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_item',
            [
                'label' => esc_html__('Item', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#f3f3f3',
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-item-main'                  => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-arrow'                      => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-timeline-item--top .bdt-timeline-content:after'    => 'border-top-color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-timeline-item--bottom .bdt-timeline-content:after' => 'border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-timeline--mobile .bdt-timeline-content:after'      => 'border-right-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-item-main',
            ]
        );

        $this->add_control(
            'timeline_line_color',
            [
                'label'     => esc_html__('Line Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline-divider, {{WRAPPER}} .bdt-timeline .bdt-timeline-line span, {{WRAPPER}} .bdt-timeline:not(.bdt-timeline--horizontal):before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-item:after, {{WRAPPER}} .bdt-timeline.bdt-timeline-skin-default .bdt-timeline-item-main-wrapper .bdt-timeline-icon span' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_line_width',
            [
                'label'     => __('Line Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'default'   => [
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline-divider'                               => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-line span'               => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-timeline-skin-olivier .bdt-timeline-item:after, {{WRAPPER}} .bdt-timeline.bdt-timeline-skin-default .bdt-timeline-item-main-wrapper .bdt-timeline-icon span' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-timeline .bdt-timeline-item-main',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-item-main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_icon',
            [
                'label'     => esc_html__('Icon', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => ''
                ]
            ]
        );

        $this->start_controls_tabs('tabs_icon_style');

        $this->start_controls_tab(
            'tab_icon_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon'     => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'icon_shadow',
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span'
            ]
        );

        $this->add_responsive_control(
            'icon_width',
            [
                'label'     => __('Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_show',
            [
                'label'   => esc_html__('Show Icon', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label'     => __('Icon Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 35,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span i, {{WRAPPER}} .bdt-timeline .bdt-timeline-icon span' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_show' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'icon_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span',
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label'     => __('Border Radius', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon:hover, {{WRAPPER}} .bdt-timeline .bdt-timeline-icon span:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-icon span:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_date',
            [
                'label' => esc_html__('Date', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => '',
                ]
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-date span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#f3f3f3;',
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-date span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'date_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-timeline .bdt-timeline-date span',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'date_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default'    => [
                    'top'    => '2',
                    'right'  => '2',
                    'bottom' => '2',
                    'left'   => '2',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-date span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'date_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default'    => [
                    'top'    => '10',
                    'right'  => '15',
                    'bottom' => '10',
                    'left'   => '15',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-date span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'date_typography',
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-date',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'date_shadow',
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-date span',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label'     => esc_html__('Image', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'         => 'thumbnail_size',
                'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
                'exclude'      => ['custom'],
                'default'      => 'medium',
                'prefix_class' => 'bdt-timeline-thumbnail-size-',
            ]
        );

        $this->add_responsive_control(
            'image_ratio',
            [
                'label'     => esc_html__('Image Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 265,
                ],
                'range'     => [
                    'px' => [
                        'min'  => 50,
                        'max'  => 500,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-thumbnail img' => 'height: {{SIZE}}px',
                ],
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label'     => esc_html__('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min'  => 0.1,
                        'max'  => 1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-thumbnail img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default'    => [
                    'top'    => '20',
                    'right'  => '20',
                    'bottom' => '0',
                    'left'   => '20',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-thumbnail' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'image_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-timeline .bdt-timeline-thumbnail',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label'     => esc_html__('Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'title_bg_color',
				'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-title a',
                'fields_options' => [
                    'background' => [
                        'label' => esc_html__('Background Type', 'bdthemes-element-pack') . BDTEP_NC,
                    ],
                ],
			]
		);

        $this->add_responsive_control(
            'title_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-timeline .bdt-timeline-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_meta',
            [
                'label'     => esc_html__('Meta', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#bbbbbb',
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-meta *' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-meta *',
            ]
        );

        $this->add_control(
            'meta_spacing',
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-meta' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_excerpt',
            [
                'label'     => esc_html__('Text', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#888888',
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'excerpt_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-excerpt',
            ]
        );

        $this->add_control(
            'excerpt_spacing',
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_readmore',
            [
                'label'     => esc_html__('Readmore Button', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_readmore' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_readmore_style');

        $this->start_controls_tab(
            'tab_readmore_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore svg'     => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'readmore_shadow',
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore',
            ]
        );

        $this->add_control(
            'readmore_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'readmore_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore',
            ]
        );

        $this->add_responsive_control(
            'readmore_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow: hidden;',
                ],
            ]
        );

        $this->add_control(
            'readmore_spacing',
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'readmore_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_readmore_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore:hover'     => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore:hover svg'     => 'fill: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'readmore_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-readmore:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_animation',
            [
                'label' => esc_html__('Animation', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_navigation_button',
            [
                'label'     => esc_html__('Navigation Button', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => 'bdt-olivier',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_navigation_button_style');

        $this->start_controls_tab(
            'tab_navigation_button_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'navigation_button_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-nav-button:before' => 'border-top-color: {{VALUE}}; border-left-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'navigation_button_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline-nav-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'navigation_button_shadow',
                'selector' => '{{WRAPPER}} .bdt-timeline-nav-button',
            ]
        );

        $this->add_responsive_control(
            'navigation_button_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline-nav-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'navigation_button_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-timeline-nav-button',
            ]
        );

        $this->add_responsive_control(
            'navigation_button_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-timeline-nav-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_navigation_button_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'navigation_button_hover_color',
            [
                'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline .bdt-timeline-nav-button:hover:before' => 'border-top-color: {{VALUE}}; border-left-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'navigation_button_hover_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline-nav-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'navigation_button_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'navigation_button_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-timeline-nav-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function render_excerpt($item = []) {

        if (!$this->get_settings('show_excerpt')) {
            return;
        }

        $settings = $this->get_settings_for_display();

        if ('post' == $settings['timeline_source']) {
            $strip_shortcode = $this->get_settings_for_display('strip_shortcode');

?>
            <div class="bdt-timeline-excerpt">
                <?php
                if (has_excerpt()) {
                    the_excerpt();
                } else {
                    echo element_pack_custom_excerpt($this->get_settings_for_display('excerpt_length'), $strip_shortcode);
                }
                ?>
            </div>
        <?php

        } else {
        ?>
            <div class="bdt-timeline-excerpt">
                <?php echo do_shortcode($item['timeline_text']); ?>
            </div>
        <?php
        }
    }

    public function render_readmore($item = []) {

        if (!$this->get_settings('show_readmore')) {
            return;
        }

        $settings = $this->get_settings_for_display();

        if ('post' == $settings['timeline_source']) {
            $readmore_link = get_permalink();
        } else {
            $readmore_link = $item['timeline_link'];
        }

        $this->add_render_attribute(
            [
                'timeline-readmore' => [
                    'href'  => esc_url($readmore_link),
                    'class' => [
                        'bdt-timeline-readmore',
                        'elementor-button',
                        'elementor-size-' . esc_attr($settings['button_size']),
                        $settings['readmore_hover_animation'] ? 'elementor-animation-' . $settings['readmore_hover_animation'] : ''
                    ],
                ]
            ],
            '',
            '',
            true
        );

        if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
            // add old default
            $settings['icon'] = 'fas fa-arrow-right';
        }

        $migrated = isset($settings['__fa4_migrated']['button_icon']);
        $is_new   = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

        ?>
        <a <?php echo $this->get_render_attribute_string('timeline-readmore'); ?>>
            <?php echo esc_html($settings['readmore_text']); ?>

            <?php if ($settings['button_icon']['value']) : ?>
                <span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

                    <?php if ($is_new || $migrated) :
                        Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                    else : ?>
                        <i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
                    <?php endif; ?>

                </span>
            <?php endif; ?>
        </a>
        <?php

    }

    public function render_image($item = []) {

        if (!$this->get_settings('show_image')) {
            return;
        }

        $settings = $this->get_settings_for_display();

        if ('post' == $settings['timeline_source']) {
            $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if (is_array($image_url)) {
                $image_url = $image_url[0];
            }
            $title     = get_the_title();
        } else {
            $image_url = ($item['timeline_image']['url']) ?: '';
            $title     = $item['timeline_title'];
        }

        if ($image_url) {
        ?>
            <div class="bdt-timeline-thumbnail">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
        <?php
        }
    }

    public function render_title($item = []) {

        if (!$this->get_settings('show_title')) {
            return;
        }

        $settings = $this->get_settings_for_display();

        if ('post' == $settings['timeline_source']) {
            $title_link = get_permalink();
            $title      = get_the_title();
        } else {
            $title_link = $item['timeline_link'];
            $title      = $item['timeline_title'];
        }

        $this->add_render_attribute('bdt-timeline-title', 'class', 'bdt-timeline-title', true);

        ?>
        <<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-timeline-title'); ?>>
            <?php if ($settings['title_link']) : ?>
                <a href="<?php echo esc_url($title_link); ?>" title="<?php echo esc_html($title); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            <?php else : ?>
                <span><?php echo esc_html($title); ?></span>
            <?php endif; ?>
        </<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
    <?php

    }

    public function render_meta($align, $item = []) {

        if (!$this->get_settings('show_meta')) {
            return;
        }

        $settings = $this->get_settings_for_display();

        $hidden_class = ('center' == $align) ? 'bdt-hidden@m' : '';
        $meta_date    = '<li class="' . $hidden_class . '">' . esc_attr(get_the_date('d F Y')) . '</li>';
        $meta_list    = '<li>' . get_the_category_list(', ') . '</li>';

    ?>
        <ul class="bdt-timeline-meta bdt-subnav bdt-flex-middle">

            <?php if ('post' == $settings['timeline_source']) {
                echo wp_kses_post($meta_date);
                echo wp_kses_post($meta_list);
            } else {
            ?>
                <li><?php echo esc_attr($item['timeline_date']); ?></li>
            <?php
            }

            ?>
        </ul>
    <?php

    }

    public function render_item($item_parallax, $align, $item = []) {

    ?>
        <div class="bdt-timeline-item-main" <?php printf($item_parallax); ?>>
            <span class="bdt-timeline-arrow"></span>

            <?php $this->render_image($item); ?>

            <div class="bdt-timeline-desc bdt-padding">
                <?php $this->render_title($item); ?>
                <?php $this->render_meta($align, $item); ?>
                <?php $this->render_excerpt($item); ?>
                <?php $this->render_readmore($item); ?>
            </div>
        </div>
    <?php

    }

    public function render_date($align = 'left', $item = []) {

        $settings      = $this->get_settings_for_display();
        $date_parallax = '';

        if ('post' == $settings['timeline_source']) {
            $timeline_date = get_the_date('d F Y');
        } else {
            $timeline_date = $item['timeline_date'];
        }

        if ($settings['item_animation']) {
            if ($align == 'right') {
                $date_parallax = ' bdt-parallax="opacity: 0,1;x: -200,0;viewport: 0.5;"';
            } else {
                $date_parallax = ' bdt-parallax="opacity: 0,1;x: 200,0;viewport: 0.5;"';
            }
        }

    ?>
        <div class="bdt-timeline-item bdt-width-1-2@m bdt-visible@m">
            <div class="bdt-timeline-date bdt-text-<?php echo esc_attr($align); ?>" <?php echo esc_attr($date_parallax); ?>>
                <span><?php echo esc_attr($timeline_date); ?></span>
            </div>
        </div>
        <?php

    }

    /**
     * Get post query builder arguments
     */
    public function query_posts($posts_per_page) {
        $settings = $this->get_settings();

        $args = [];
        if ($posts_per_page) {
            $args['posts_per_page'] = $posts_per_page;
            $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        $this->_query = new \WP_Query($args);
    }

    function render_post_format() {
		$settings = $this->get_settings_for_display();
		
		//$icon_parallax = ($settings['item_animation']) ? ' bdt-parallax="scale: 0.5,1; viewport: 0.5;"' : '';

        $this->add_render_attribute('timeline-icon', 'class', 'bdt-timeline-icon');

        if ($settings['item_animation']) {
            $this->add_render_attribute('timeline-icon', 'bdt-parallax', 'scale: 0.5,1; viewport: 0.5;');
        }

		?>
        <div <?php $this->print_render_attribute_string('timeline-icon'); ?>>
            <?php if ( has_post_format( 'aside' ) ) : ?>
                <span><i class="ep-icon-aside" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'gallery' ) ) : ?>
                <span><i class="ep-icon-gallery" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'link' ) ) : ?>
                <span><i class="ep-icon-link" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'image' ) ) : ?>
                <span><i class="ep-icon-image" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'quote' ) ) : ?>
                <span><i class="ep-icon-quote" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'status' ) ) : ?>
                <span><i class="ep-icon-status" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'video' ) ) : ?>
                <span><i class="ep-icon-video" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'audio' ) ) : ?>
                <span><i class="ep-icon-music" aria-hidden="true"></i></span>
            <?php elseif ( has_post_format( 'chat' ) ) : ?>
                <span><i class="ep-icon-chat" aria-hidden="true"></i></span>
            <?php else : ?>
                <span><i class="ep-icon-post" aria-hidden="true"></i></span>
            <?php endif; ?>
        </div>
		<?php
	}

    public function render_post() {
        $settings               = $this->get_settings_for_display();
        $id                     = $this->get_id();
        $align                  = $settings['timeline_align'];
        
        // $vertical_line_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity: 0,1;viewport: 0.5;"' : '';

        if ($settings['item_animation']) {
            $this->add_render_attribute('vertical_line_parallax', 'bdt-parallax', 'opacity: 0,1;viewport: 0.5;"');
        }

        // TODO need to delete after v6.5
        if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['posts_limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }

        $this->query_posts($limit);

        $wp_query = $this->get_query();

        if (!$wp_query->found_posts) {
            return;
        }

        if ($wp_query->have_posts()) :

            $this->add_render_attribute(
                [
                    'bdt-timeline' => [
                        'id'    => 'bdt-timeline-' . esc_attr($id),
                        'class' => [
                            'bdt-timeline',
                            'bdt-timeline-skin-default',
                            'bdt-timeline-' . esc_attr($align)
                        ]
                    ]
                ]
            );

            if ('yes' == $settings['icon_show']) {
                $this->add_render_attribute('bdt-timeline', 'class', 'bdt-timeline-icon-yes');
            }

        ?>
            <div <?php echo $this->get_render_attribute_string('bdt-timeline'); ?>>
                <div class="bdt-grid bdt-grid-collapse">
                    <?php
                    $bdt_count = 0;
                    while ($wp_query->have_posts()) : $wp_query->the_post();

                        $bdt_count++;
                        $post_format = get_post_format() ?: 'standard';
                        $item_part   = ($bdt_count % 2 === 0) ? 'right' : 'left';

                        if ('center' == $align) {
                            $parallax_direction = ($bdt_count % 2 === 0) ? '' : '-';

                            $item_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity:0,1;x:' . $parallax_direction . '200,0;viewport: 0.5;"' : '';
                        } elseif ('right' == $align) {
                            $item_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity: 0,1;x: -200,0;viewport: 0.5;"' : '';
                        } else {
                            $item_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity: 0,1;x: 200,0;viewport: 0.5;"' : '';
                        }

                        if ($bdt_count % 2 === 0 and 'center' == $align) : ?>
                            <?php $this->render_date('right', ''); ?>
                        <?php endif; ?>

                        <div class="<?php echo ('center' == $align) ? ' bdt-width-1-2@m ' : 'bdt-width-1-1 '; ?>bdt-timeline-item <?php echo esc_attr($item_part) . '-part'; ?>">

                            <div class="bdt-timeline-item-main-wrapper">
                                <div class="bdt-timeline-line">
                                    <span <?php $this->print_render_attribute_string('vertical_line_parallax'); ?>></span>
                                </div>
                                <div class="bdt-timeline-item-main-container">
                                    
                                    <?php $this->render_post_format(); ?>

                                    <?php $this->render_item($item_parallax, $align, ''); ?>

                                </div>
                            </div>
                        </div>

                        <?php if ($bdt_count % 2 === 1 and ('center' == $align)) : ?>
                            <?php $this->render_date('left', ''); ?>
                        <?php endif; ?>

                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            </div>

        <?php endif;
    }

    public function render_custom() {
        $id             = $this->get_id();
        $settings       = $this->get_settings_for_display();
        $timeline_items = $settings['timeline_items'];

        $align                  = $settings['timeline_align'];
        $vertical_line_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity: 0,1;viewport: 0.2;"' : '';

        $this->add_render_attribute('bdt-timeline-custom', 'id', 'bdt-timeline-' . esc_attr($id));
        $this->add_render_attribute('bdt-timeline-custom', 'class', 'bdt-timeline bdt-timeline-skin-default');
        $this->add_render_attribute('bdt-timeline-custom', 'class', 'bdt-timeline-' . esc_attr($align));

        ?>
        <div <?php echo $this->get_render_attribute_string('bdt-timeline-custom'); ?>>
            <div class="bdt-grid bdt-grid-collapse" bdt-grid>
                <?php
                $bdt_count = 0;
                foreach ($timeline_items as $item) :
                    $bdt_count++;

                    if (!isset($item['timeline_icon']) && !Icons_Manager::is_migration_allowed()) {
                        // add old default
                        $item['timeline_icon'] = 'fas fa-file-alt';
                    }

                    $migrated = isset($item['__fa4_migrated']['timeline_select_icon']);
                    $is_new   = empty($item['timeline_icon']) && Icons_Manager::is_migration_allowed();

                    if ('center' == $align) {
                        $parallax_direction = ($bdt_count % 2 === 0) ? '' : '-';
                        $item_parallax      = ($settings['item_animation']) ? ' bdt-parallax="opacity:0,1;x:' . $parallax_direction . '200,0;viewport: 0.5;"' : '';
                    } elseif ('right' == $align) {
                        $item_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity: 0,1;x: -200,0;viewport: 0.5;"' : '';
                    } else {
                        $item_parallax = ($settings['item_animation']) ? ' bdt-parallax="opacity: 0,1;x: 200,0;viewport: 0.5;"' : '';
                    }

                    $item_part = ($bdt_count % 2 === 0) ? 'right' : 'left';

                    if ($bdt_count % 2 === 0 and 'center' == $align) : ?>
                        <?php $this->render_date('right', $item); ?>
                    <?php endif; ?>


                    <div class="<?php echo ('center' == $align) ? ' bdt-width-1-2@m ' : ' '; ?>bdt-timeline-item <?php echo esc_attr($item_part) . '-part'; ?>">

                        <div class="bdt-timeline-item-main-wrapper">
                            <div class="bdt-timeline-line">
                                <span <?php echo esc_attr($vertical_line_parallax);?> ></span>
                            </div>
                            <div class="bdt-timeline-item-main-container">
                                <?php $item_scrollspy = ($settings['item_animation']) ? ' bdt-scrollspy="cls: bdt-animation-scale-up;"' : ''; ?>

                                <div class="bdt-timeline-icon" <?php echo esc_attr($item_scrollspy); ?>>

                                    <span>

                                        <?php if ('yes' == $settings['icon_show']) : ?>
                                            <?php if ($is_new || $migrated) :
                                                Icons_Manager::render_icon($item['timeline_select_icon'], ['aria-hidden' => 'true']);
                                            else : ?>
                                                <i class="<?php echo esc_attr($item['timeline_icon']); ?>" aria-hidden="true"></i>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                    </span>

                                </div>
                                <?php $this->render_item($item_parallax, $align, $item); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($bdt_count % 2 === 1 and ('center' == $align)) : ?>
                        <?php $this->render_date('', $item); ?>
                    <?php endif; ?>

                <?php endforeach; ?>

                <?php wp_reset_postdata(); ?>

            </div>
        </div>
<?php
    }

    public function render() {

        $settings = $this->get_settings_for_display();

        if ('post' === $settings['timeline_source']) {
            $this->render_post();
        } else if ('custom' === $settings['timeline_source']) {
            $this->render_custom();
        } else {
            return;
        }
    }
}
