<?php
namespace ElementPack\Modules\Marker\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use ElementPack\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Marker extends Module_Base
{

    public function get_name()
    {
        return 'bdt-marker';
    }

    public function get_title()
    {
        return BDTEP . __('Marker', 'bdthemes-element-pack');
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['marker', 'pointer', 'hotspots', 'spot', 'hot'];
    }

    public function get_icon()
    {
        return 'bdt-wi-marker';
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-marker', 'tippy'];
        }
    }

    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'ep-scripts'];
        } else {
            return ['popper', 'tippyjs', 'ep-marker'];
        }
    }

    public function get_custom_help_url()
    {
        return 'https://youtu.be/aH4QiD6v-lk';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_marker_image',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Choose Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => ['active' => true],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Actually its `image_size`.
                'label' => __('Image Size', 'bdthemes-element-pack'),
                'default' => 'large',
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'caption',
            [
                'label' => __('Caption', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => '',
                'placeholder' => __('Enter your caption about the image', 'bdthemes-element-pack'),
                'title' => __('Input image caption here', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Link to', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'placeholder' => __('http://your-link.com', 'bdthemes-element-pack'),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_sliders',
            [
                'label' => esc_html__('Markers', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs('tabs_markers');

        $repeater->start_controls_tab(
            'tab_marker',
            [
                'label' => __('Marker', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'select_type',
            [
                'label' => __('Select Type', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'none' => [
                        'title' => __('None', 'bdthemes-element-pack'),
                        'icon' => 'eicon-editor-close',
                    ],
                    'text' => [
                        'title' => __('Text', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-area',
                    ],
                    'icon' => [
                        'title' => __('Icon', 'bdthemes-element-pack'),
                        'icon' => 'eicon-star',
                    ],
                    'image' => [
                        'title' => __('Image', 'bdthemes-element-pack'),
                        'icon' => 'eicon-image',
                    ],
                ],
                'default' => 'icon',
                'toggle' => false,
            ]
        );

        $repeater->add_control(
            'text',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __('Text', 'bdthemes-element-pack'),
                'default' => 'Marker',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'select_type' => 'text',
                ],
            ]
        );

        $repeater->add_control(
            'marker_select_icon',
            [
                'label' => esc_html__('Icon', 'bdthemes-element-pack'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'condition' => [
                    'select_type' => 'icon',
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'type' => Controls_Manager::MEDIA,
                'show_label' => false,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'select_type' => 'image',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'marker_invisible_height',
            [
                'label' => __('Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'tablet_default' => [
                    'size' => 20,
                ],
                'mobile_default' => [
                    'size' => 20,
                ],
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'select_type' => 'none',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'marker_invisible_width',
            [
                'label' => __('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'tablet_default' => [
                    'size' => 20,
                ],
                'mobile_default' => [
                    'size' => 20,
                ],
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker-item' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'select_type' => 'none',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'marker_x_position',
            [
                'label' => esc_html__('X Postion', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker-item' => 'left: {{SIZE}}%;',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'marker_y_position',
            [
                'label' => esc_html__('Y Postion', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker-item' => 'top: {{SIZE}}%;',
                ],
            ]
        );

        $repeater->add_control(
            'link_to',
            [
                'label' => esc_html__('Link to', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => __('None', 'bdthemes-element-pack'),
                    'custom' => __('Custom URL', 'bdthemes-element-pack'),
                    'lightbox' => __('Lightbox', 'bdthemes-element-pack'),
                ],
            ]
        );

        $repeater->add_control(
            'marker_link',
            [
                'label' => esc_html__('Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'placeholder' => 'http://your-link.com',
                'default' => [
                    'url' => '#',
                ],
                'condition' => [
                    'link_to' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'image_link',
            [
                'label' => esc_html__('Choose Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'link_to' => 'lightbox',
                ],
            ]
        );

        $repeater->add_control(
            'advanced_option_toggle',
            [
                'label' => __('Advanced Settings', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $repeater->start_popover();

        $repeater->add_control(
            'css_id',
            [
                'label' => __('CSS ID', 'bdthemes-element-pack'),
                'title' => __('Add your custom id. { e.g: bdt-custom-id }', 'bdthemes-element-pack'),
                'separator' => 'before',
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'render_type' => 'ui',
                'condition' => [
                    'advanced_option_toggle' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'css_classes',
            [
                'label' => __('CSS Classes', 'bdthemes-element-pack'),
                'title' => __('Add your custom class without the dot. { e.g: bdt-custom-class }', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'prefix_class' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'render_type' => 'ui',
                'condition' => [
                    'advanced_option_toggle' => 'yes',
                ],
                'separator' => 'after',
            ]
        );

        $repeater->add_control(
            'marker_heading',
            [
                'label' => esc_html__('Marker Style', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'render_type' => 'ui',
                'condition' => [
                    'advanced_option_toggle' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'repeater_marker_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker' => 'color: {{VALUE}};',
                ],
                'render_type' => 'ui',
                'condition' => [
                    'advanced_option_toggle' => 'yes',
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'repeater_marker_background',
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker',
                'render_type' => 'ui',
                'condition' => [
                    'advanced_option_toggle' => 'yes',
                ],
            ]
        );

        $repeater->end_popover();

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tab_tooltip',
            [
                'label' => __('Tooltip', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'marker_tooltip',
            [
                'label' => __('Tooltip', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $repeater->add_control(
            'marker_title',
            [
                'label' => esc_html__('Tooltip Text', 'bdthemes-element-pack'),
                'default' => esc_html__('Tooltip Text Here', 'bdthemes-element-pack'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => ['active' => true],
                'label_block' => true,
                'condition' => [
                    'marker_tooltip' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'marker_tooltip_placement',
            [
                'label' => esc_html__('Placement', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top-start' => esc_html__('Top Left', 'bdthemes-element-pack'),
                    'top' => esc_html__('Top', 'bdthemes-element-pack'),
                    'top-end' => esc_html__('Top Right', 'bdthemes-element-pack'),
                    'bottom-start' => esc_html__('Bottom Left', 'bdthemes-element-pack'),
                    'bottom' => esc_html__('Bottom', 'bdthemes-element-pack'),
                    'bottom-end' => esc_html__('Bottom Right', 'bdthemes-element-pack'),
                    'left' => esc_html__('Left', 'bdthemes-element-pack'),
                    'right' => esc_html__('Right', 'bdthemes-element-pack'),
                ],
                'render_type' => 'template',
                'condition' => [
                    'marker_tooltip' => 'yes',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'markers',
            [
                'label' => esc_html__('Marker Items', 'bdthemes-element-pack'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'marker_title' => esc_html__('Marker #1', 'bdthemes-element-pack'),
                        'marker_x_position' => [
                            'size' => 50,
                            'unit' => '%',
                        ],
                        'marker_y_position' => [
                            'size' => 50,
                            'unit' => '%',
                        ],
                    ],
                    [
                        'marker_title' => esc_html__('Marker #2', 'bdthemes-element-pack'),
                        'marker_x_position' => [
                            'size' => 30,
                            'unit' => '%',
                        ],
                        'marker_y_position' => [
                            'size' => 30,
                            'unit' => '%',
                        ],
                    ],
                    [
                        'marker_title' => esc_html__('Marker #3', 'bdthemes-element-pack'),
                        'marker_x_position' => [
                            'size' => 80,
                            'unit' => '%',
                        ],
                        'marker_y_position' => [
                            'size' => 20,
                            'unit' => '%',
                        ],
                    ],
                ],
                'title_field' => '{{{ marker_title }}}',
            ]
        );

        $this->add_control(
            'marker_animation',
            [
                'label' => __('Pulse Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        // $this->add_control(
        //     'marker_invisible',
        //     [
        //         'label'   => __( 'Marker Invisible', 'bdthemes-element-pack' ),
        //         'type'    => Controls_Manager::SWITCHER,
        //     ]
        // );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tooltip_settings',
            [
                'label' => __('Tooltip Settings', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'marker_tooltip_animation',
            [
                'label' => esc_html__('Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'shift-toward',
                'options' => [
                    'shift-away' => esc_html__('Shift-Away', 'bdthemes-element-pack'),
                    'shift-toward' => esc_html__('Shift-Toward', 'bdthemes-element-pack'),
                    'fade' => esc_html__('Fade', 'bdthemes-element-pack'),
                    'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
                    'perspective' => esc_html__('Perspective', 'bdthemes-element-pack'),
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'marker_tooltip_x_offset',
            [
                'label' => esc_html__('Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
            ]
        );

        $this->add_control(
            'marker_tooltip_y_offset',
            [
                'label' => esc_html__('Distance', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
            ]
        );

        $this->add_control(
            'marker_tooltip_arrow',
            [
                'label' => esc_html__('Arrow', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'marker_tooltip_trigger',
            [
                'label' => __('Trigger on Click', 'bdthemes-element-pack'),
                'description' => __('Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label' => __('Size (%)', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'opacity',
            [
                'label' => __('Opacity', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper > img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'label' => __('Image Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper > img',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_radius',
            [
                'label' => __('Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_shadow',
                'exclude' => [
                    'shadow_position',
                ],
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper > img',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_caption',
            [
                'label' => __('Caption', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'caption_align',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'default' => '',
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .marker-caption-text' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .marker-caption-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .marker-caption-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_marker',
            [
                'label' => __('Marker', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_marker_style');

        $this->start_controls_tab(
            'tab_marker_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'marker_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'marker_background_color',
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'marker_border',
                'label' => __('Image Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'marker_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-marker-animated .bdt-marker:before, {{WRAPPER}} .bdt-marker-animated .bdt-marker:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'marker_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'marker_size',
            [
                'label' => __('Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker > img' => 'width: calc({{SIZE}}{{UNIT}} - 12px); height: auto;',
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker > i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'marker_opacity',
            [
                'label' => __('Opacity (%)', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'marker_shadow',
                'exclude' => [
                    'shadow_position',
                ],
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker',
            ]
        );

        $this->add_control(
            'marker_pulse_color',
            [
                'label' => esc_html__('Pulse Animated Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-animated .bdt-marker:before, {{WRAPPER}} .bdt-marker-animated .bdt-marker:after' => 'border-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_marker_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );

        $this->add_control(
            'marker_hover_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'marker_hover_background_color',
                'selector' => '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker:hover',
            ]
        );

        $this->add_control(
            'marker_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-marker-wrapper .bdt-marker:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'marker_border_border!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_tooltip',
            [
                'label' => esc_html__('Tooltip', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'marker_tooltip_width',
            [
                'label' => esc_html__('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [
                    'px', 'em',
                ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'marker_tooltip_typography',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
            ]
        );

        $this->add_control(
            'marker_tooltip_color',
            [
                'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_tooltip_text_align',
            [
                'label' => esc_html__('Text Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
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
                'name' => 'marker_tooltip_background',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
            ]
        );

        $this->add_control(
            'marker_tooltip_arrow_color',
            [
                'label' => esc_html__('Arrow Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tippy-popper[x-placement^=left] .tippy-arrow' => 'border-left-color: {{VALUE}}',
                    '{{WRAPPER}} .tippy-popper[x-placement^=right] .tippy-arrow' => 'border-right-color: {{VALUE}}',
                    '{{WRAPPER}} .tippy-popper[x-placement^=top] .tippy-arrow' => 'border-top-color: {{VALUE}}',
                    '{{WRAPPER}} .tippy-popper[x-placement^=bottom] .tippy-arrow' => 'border-bottom-color: {{VALUE}}',

                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'marker_tooltip_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'marker_tooltip_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
            ]
        );

        $this->add_responsive_control(
            'marker_tooltip_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'marker_tooltip_box_shadow',
                'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
            ]
        );

        $this->add_control(
            'tooltip_size',
            [
                'label' => esc_html__('Tooltip Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('Default', 'bdthemes-element-pack'),
                    'large' => esc_html__('Large', 'bdthemes-element-pack'),
                    'small' => esc_html__('small', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-marker-' . $this->get_id();

        if (empty($settings['image']['url'])) {
            return;
        }

        $has_caption = !empty($settings['caption']);

        $this->add_render_attribute('wrapper', 'class', 'bdt-marker-wrapper bdt-inline bdt-dark');
        $this->add_render_attribute('wrapper', 'id', esc_attr($id));

        if ('yes' === $settings['marker_animation']) {
            $this->add_render_attribute('wrapper', 'class', 'bdt-marker-animated');
            $this->add_render_attribute('wrapper', 'data-bdt-scrollspy', 'target: .bdt-marker-wrapper > a.bdt-marker-item; cls:bdt-animation-scale-up; delay: 300;');
        }

        $this->add_render_attribute('wrapper', 'data-bdt-lightbox', 'toggle: .bdt-marker-lightbox-item; animation: slide;');

        if ($has_caption): ?>
			<figure class="marker-caption">
		<?php endif;?>

		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

			<?php

        echo Group_Control_Image_Size::get_attachment_image_html($settings);

        foreach ($settings['markers'] as $marker) {

            // $this->add_render_attribute('marker', 'class',  ['bdt-marker-item bdt-position-absolute bdt-transform-center bdt-marker bdt-icon'], true);

            $this->add_render_attribute('marker', 'class', 'bdt-marker-item bdt-position-absolute bdt-marker bdt-icon elementor-repeater-item-' . esc_attr($marker['_id']), true);

            if (!empty($marker['css_classes'])) {
                $this->add_render_attribute('marker', 'class', esc_attr($marker['css_classes']));
            }

            if (!empty($marker['css_id'])) {
                $this->add_render_attribute('marker', 'id', esc_attr($marker['css_id']), true);
            }

            if ('none' == $marker['select_type']) {
                $this->add_render_attribute('marker', 'class', 'bdt-marker-invisible');
            }

            // $this->add_render_attribute('marker', 'style', 'left:' . $marker['marker_x_position']['size'] . '%;', true);
            // $this->add_render_attribute('marker', 'style', 'top:' . $marker['marker_y_position']['size'] . '%;');
            $this->add_render_attribute('marker', 'data-tippy-content', [$marker['marker_title']], true);

            if ('lightbox' == $marker['link_to']) {
                $this->add_render_attribute('marker', 'data-elementor-open-lightbox', 'no', true);
                $this->add_render_attribute('marker', 'data-caption', $marker['marker_title'], true);
                $this->add_render_attribute('marker', 'class', 'bdt-marker-lightbox-item');
                $this->add_render_attribute('marker', 'href', $marker['image_link']['url'], true);
            } elseif ('custom' == $marker['link_to']) {
                $this->add_render_attribute('marker', 'href', $marker['marker_link']['url'], true);
                if (!empty($marker['marker_link']['is_external'])) {
                    $this->add_render_attribute('marker', 'target', ['_blank'], true);
                }
                if (!empty($marker['marker_link']['nofollow'])) {
                    $this->add_render_attribute('marker', 'rel', ['nofollow'], true);
                }
            } else {
                $this->add_render_attribute('marker', 'target', ['_self'], true);
                $this->add_render_attribute('marker', 'href', 'javascript:void(0);', true);
            }

            if ($marker['marker_title'] and $marker['marker_tooltip']) {
                // Tooltip settings
                $this->add_render_attribute('marker', 'class', 'bdt-tippy-tooltip');
                $this->add_render_attribute('marker', 'data-tippy', '', true);

                if ($marker['marker_tooltip_placement']) {
                    $this->add_render_attribute('marker', 'data-tippy-placement', $marker['marker_tooltip_placement'], true);
                }

                if ($settings['marker_tooltip_animation']) {
                    $this->add_render_attribute('marker', 'data-tippy-animation', $settings['marker_tooltip_animation'], true);
                }

                if ($settings['marker_tooltip_x_offset']['size'] or $settings['marker_tooltip_y_offset']['size']) {
                    $this->add_render_attribute('marker', 'data-tippy-offset', '[' . $settings['marker_tooltip_x_offset']['size'] . ',' . $settings['marker_tooltip_y_offset']['size'] . ']', true);
                }

                if ('yes' == $settings['marker_tooltip_arrow']) {
                    $this->add_render_attribute('marker', 'data-tippy-arrow', 'true', true);
                } else {
                    $this->add_render_attribute('marker', 'data-tippy-arrow', 'false', true);
                }

                if ('yes' == $settings['marker_tooltip_trigger']) {
                    $this->add_render_attribute('marker', 'data-tippy-trigger', 'click', true);
                }
            }

            $migrated = isset($marker['__fa4_migrated']['marker_select_icon']);
            $is_new = empty($marker['marker_icon']) && Icons_Manager::is_migration_allowed();

            ?>
				<a <?php echo $this->get_render_attribute_string('marker'); ?>>

					<?php if ($marker['select_type'] === 'icon') {?>
						<?php if (($is_new or $migrated) and $marker['marker_select_icon']['value']):
                    Icons_Manager::render_icon($marker['marker_select_icon'], ['aria-hidden' => 'true']);
                else: ?>
							<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="marker"><rect x="9" y="4" width="1" height="11"></rect><rect x="4" y="9" width="11" height="1"></rect></svg>
						<?php endif;
            } elseif ($marker['select_type'] === 'image') {
                echo wp_get_attachment_image($marker['image']['id']);
            } elseif ($marker['select_type'] === 'none') {
                // echo '';
            } else {
                echo esc_html($marker['text']);
            }
            ?>

				</a>
				<?php
}?>

		</div>

		<?php if ($has_caption): ?>
			<figcaption class="marker-caption-text"><?php echo esc_html($settings['caption']); ?></figcaption>
		<?php endif;?>

		<?php if ($has_caption): ?>
			</figure>
		<?php endif;?>

		<?php
}

}
