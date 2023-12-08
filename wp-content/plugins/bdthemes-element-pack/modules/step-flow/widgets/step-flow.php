<?php

namespace ElementPack\Modules\StepFlow\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use ElementPack\Utils;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Step_Flow extends Module_Base {

    public function get_name() {
        return 'bdt-step-flow';
    }

    public function get_title() {
        return BDTEP . esc_html__('Step Flow', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-step-flow';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['step', 'process', 'icon', 'features'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-step-flow'];
        }
    }
    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-step-flow'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/YNjbt-5GO4k';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_step_flow',
            [
                'label' => __('Step Flow', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_type',
            [
                'label' => esc_html__('Icon Type', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'default' => 'icon',
                'prefix_class' => 'bdt-icon-type-',
                'render_type' => 'template',
                'options' => [
                    'icon' => [
                        'title' => esc_html__('Icon', 'bdthemes-element-pack'),
                        'icon' => 'eicon-star'
                    ],
                    'image' => [
                        'title' => esc_html__('Image', 'bdthemes-element-pack'),
                        'icon' => 'eicon-image'
                    ]
                ]
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => __('Icon', 'bdthemes-element-pack'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-directions',
                    'library' => 'fa-solid',
                ],
                'render_type' => 'template',
                'condition' => [
                    'icon_type' => 'icon',
                ]
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Image Icon', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'render_type' => 'template',
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'icon_type' => 'image'
                ]
            ]
        );
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'default'      => 'full',
				'condition' => [
					'icon_type' => 'image'
				]
			]
		);
        $this->add_control(
            'title_text',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Step Flow Heading', 'bdthemes-element-pack'),
                'placeholder' => __('Enter your title', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'title_link',
            [
                'label' => __('Title Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-title-link-'
            ]
        );


        $this->add_control(
            'title_link_url',
            [
                'label' => __('Title Link URL', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'placeholder' => 'http://your-link.com',
                'condition' => [
                    'title_link' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'description_text',
            [
                'label' => __('Description', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
                'placeholder' => __('Enter your description', 'bdthemes-element-pack'),
                'rows' => 10,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_size',
            [
                'label' => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => element_pack_title_tags(),
            ]
        );

        $this->add_responsive_control(
            'text_align',
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
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'show_separator',
            [
                'label' => __('Title Separator', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_indicator',
            [
                'label' => __('Show Direction', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'readmore',
            [
                'label' => __('Read More', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'badge',
            [
                'label' => __('Badge (Step)', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'global_link',
            [
                'label' => __('Global Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-global-link-',
                'description' => __('Be aware! When Global Link activated then title link and read more link will not work', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'global_link_url',
            [
                'label' => __('Global Link URL', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'placeholder' => 'http://your-link.com',
                'condition' => [
                    'global_link' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_readmore',
            [
                'label' => __('Read More', 'bdthemes-element-pack'),
                'condition' => [
                    'readmore' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => __('Read More', 'bdthemes-element-pack'),
                'placeholder' => __('Read More', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_link',
            [
                'label' => __('Link to', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'separator' => 'before',
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
                'default' => [
                    'url' => '#',
                ],
                'condition' => [
                    'readmore' => 'yes',
                    //'readmore_text!' => '',
                ]
            ]
        );

        $this->add_control(
            'onclick',
            [
                'label' => esc_html__('OnClick', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'readmore' => 'yes',
                    //'readmore_text!' => '',
                ]
            ]
        );

        $this->add_control(
            'onclick_event',
            [
                'label' => esc_html__('OnClick Event', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'myFunction()',
                'description' => sprintf(esc_html__('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp'),
                'condition' => [
                    'readmore' => 'yes',
                    //'readmore_text!' => '',
                    'onclick' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'advanced_readmore_icon',
            [
                'label' => __('Icon', 'bdthemes-element-pack'),
                'type' => Controls_Manager::ICONS,
                'separator' => 'before',
                'label_block' => false,
                'condition' => [
                    'readmore' => 'yes'
                ],
                'skin' => 'inline'
            ]
        );

        $this->add_control(
            'readmore_icon_align',
            [
                'label' => __('Icon Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'right',
                'options' => [
                    'left' => __('Left', 'bdthemes-element-pack'),
                    'right' => __('Right', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'advanced_readmore_icon[value]!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'readmore_icon_indent',
            [
                'label' => __('Icon Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 8,
                ],
                'condition' => [
                    'advanced_readmore_icon[value]!' => '',
                    'readmore_text!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-step-flow-readmore .bdt-button-icon-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_on_hover',
            [
                'label' => __('Show on Hover', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-readmore-on-hover-',
            ]
        );

        $this->add_responsive_control(
            'readmore_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => -50,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'readmore_on_hover' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-readmore-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'readmore_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-readmore-v-offset: {{SIZE}}px;'
                ],
                'condition' => [
                    'readmore_on_hover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_css_id',
            [
                'label' => __('Button ID', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack'),
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_badge',
            [
                'label' => __('Badge (Step)', 'bdthemes-element-pack'),
                'condition' => [
                    'badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_text',
            [
                'label' => __('Badge Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Step 01',
                'placeholder' => 'Type Step Here',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'badge_position',
            [
                'label' => esc_html__('Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'top-center',
                'options' => element_pack_position(),
            ]
        );

        $this->add_control(
            'badge_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'badge_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -300,
                        'step' => 2,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'badge_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-badge-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -300,
                        'step' => 2,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'badge_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-badge-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_rotate',
            [
                'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -360,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'condition' => [
                    'badge_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-badge-rotate: {{SIZE}}deg;'
                ],
            ]
        );

        $this->end_popover();

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_step_flow',
            [
                'label' => __('Icon/Image', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'selected_icon[value]',
                            'operator' => '!=',
                            'value' => ''
                        ],
                        [
                            'name' => 'image[url]',
                            'operator' => '!=',
                            'value' => ''
                        ],
                    ]
                ]
            ]
        );

        $this->start_controls_tabs('icon_colors');

        $this->start_controls_tab(
            'icon_colors_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'icon_type!' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_background',
                'selector' => '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper',
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'placeholder' => '1px',
                'separator' => 'before',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper'
            ]
        );

        $this->add_responsive_control(
            'icon_radius',
            [
                'label' => esc_html__('Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
                'condition' => [
                    'icon_radius_advanced_show!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_radius_advanced_show',
            [
                'label' => __('Advanced Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_responsive_control(
            'icon_radius_advanced',
            [
                'label' => esc_html__('Radius', 'bdthemes-element-pack'),
                'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
                'type' => Controls_Manager::TEXT,
                'size_units' => ['px', '%'],
                'separator' => 'after',
                'default' => '75% 25% 43% 57% / 46% 29% 71% 54%',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'border-radius: {{VALUE}}; overflow: hidden;',
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper img' => 'border-radius: {{VALUE}}; overflow: hidden;'
                ],
                'condition' => [
                    'icon_radius_advanced_show' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'selector' => '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper'
            ]
        );

        // $this->add_group_control(
        // 	Group_Control_Typography::get_type(),
        // 	[
        // 		'name'      => 'icon_typography',
        // 		'selector'  => '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper',
        // 		'condition' => [
        // 			'icon_type!' => 'image',
        // 		],
        // 	]
        // );

        $this->add_responsive_control(
            'icon_space',
            [
                'label' => __('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'separator' => 'before',
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_fullwidth',
            [
                'label' => __('Image Fullwidth', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'width: 100%;box-sizing: border-box;',
                ],
                'condition' => [
                    'icon_type' => 'image'
                ]
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'vh', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'image_fullwidth',
                            'operator' => '==',
                            'value' => ''
                        ],
                        [
                            'name' => 'icon_type',
                            'operator' => '==',
                            'value' => 'icon'
                        ],
                    ]
                ]
            ]
        );


        $this->add_control(
            'rotate',
            [
                'label' => __('Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'range' => [
                    'deg' => [
                        'max' => 360,
                        'min' => -360,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper i' => 'transform: rotate({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper img' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'icon_background_rotate',
            [
                'label' => __('Background Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'range' => [
                    'deg' => [
                        'max' => 360,
                        'min' => -360,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-icon-wrapper' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'image_icon_heading',
            [
                'label' => __('Image Effect', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-step-flow img',
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => __('Opacity', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow img' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->add_control(
            'background_hover_transition',
            [
                'label' => __('Transition Duration', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow img' => 'transition-duration: {{SIZE}}s',
                ],
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label' => __('Icon Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'icon_type!' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_hover_background',
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper:after',
            ]
        );

        $this->add_control(
            'icon_effect',
            [
                'label' => __('Effect', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'prefix_class' => 'bdt-icon-effect-',
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'bdthemes-element-pack'),
                    'a' => __('Effect A', 'bdthemes-element-pack'),
                    'b' => __('Effect B', 'bdthemes-element-pack'),
                    'c' => __('Effect C', 'bdthemes-element-pack'),
                    'd' => __('Effect D', 'bdthemes-element-pack'),
                    'e' => __('Effect E', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'icon_hover_border_color',
            [
                'label' => __('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'icon_border_border!' => '',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_radius',
            [
                'label' => esc_html__('Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper'
            ]
        );

        $this->add_control(
            'icon_hover_rotate',
            [
                'label' => __('Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'deg',
                ],
                'range' => [
                    'deg' => [
                        'max' => 360,
                        'min' => -360,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper i' => 'transform: rotate({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper img' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_background_rotate',
            [
                'label' => __('Background Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'deg',
                ],
                'range' => [
                    'deg' => [
                        'max' => 360,
                        'min' => -360,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'image_icon_hover_heading',
            [
                'label' => __('Image Effect', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper img',
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->add_control(
            'image_opacity_hover',
            [
                'label' => __('Opacity', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-icon-wrapper img' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'icon_type' => 'image',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'icon_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
                'separator' => 'before'
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'icon_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'icon_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-icon-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'icon_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-icon-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->end_popover();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_style_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'title_bottom_space',
            [
                'label' => __('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-content .bdt-step-flow-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-step-flow-content .bdt-step-flow-title',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_style_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-step-flow-content .bdt-step-flow-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography_hover',
                'selector' => '{{WRAPPER}} .bdt-step-flow:hover .bdt-step-flow-content .bdt-step-flow-title',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_description',
            [
                'label' => __('Description', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_description_style');

        $this->start_controls_tab(
            'tab_description_style_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'description_bottom_space',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-content .bdt-step-flow-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-content .bdt-step-flow-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bdt-step-flow-content .bdt-step-flow-description',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_description_style_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'description_color_hover',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow:hover .bdt-step-flow-content .bdt-step-flow-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography_hover',
                'selector' => '{{WRAPPER}} .bdt-step-flow:hover .bdt-step-flow-content .bdt-step-flow-description',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_title_separator',
            [
                'label' => __('Title Separator', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_separator' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_separator_type',
            [
                'label' => esc_html__('Select Separator Type', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'line',
                'options' => [
                    'line' => esc_html__('Line', 'bdthemes-element-pack'),
                    'line-circle' => esc_html__('Line Circle', 'bdthemes-element-pack'),
                    'line-cross' => esc_html__('Line Cross', 'bdthemes-element-pack'),
                    'line-star' => esc_html__('Line Star', 'bdthemes-element-pack'),
                    'line-dashed' => esc_html__('Line Dashed', 'bdthemes-element-pack'),
                    'heart' => esc_html__('Heart', 'bdthemes-element-pack'),
                    'dashed' => esc_html__('Dashed', 'bdthemes-element-pack'),
                    'floret' => esc_html__('Floret', 'bdthemes-element-pack'),
                    'rectangle' => esc_html__('Rectangle', 'bdthemes-element-pack'),
                    'leaf' => esc_html__('Leaf', 'bdthemes-element-pack'),
                    'slash' => esc_html__('Slash', 'bdthemes-element-pack'),
                    'triangle' => esc_html__('Triangle', 'bdthemes-element-pack'),
                    'wave' => esc_html__('Wave', 'bdthemes-element-pack'),
                    'kiss-curl' => esc_html__('Kiss-curl', 'bdthemes-element-pack'),
                    'zemik' => esc_html__('Zemik', 'bdthemes-element-pack'),
                    'finest' => esc_html__('Finest', 'bdthemes-element-pack'),
                    'furrow' => esc_html__('Furrow', 'bdthemes-element-pack'),
                    'peak' => esc_html__('Peak', 'bdthemes-element-pack'),
                    'melody' => esc_html__('Melody', 'bdthemes-element-pack'),
                    'bloomstar' => esc_html__('Bloomstar', 'bdthemes-element-pack'),
                    'bobbleaf' => esc_html__('Bobbleaf', 'bdthemes-element-pack'),
                    'demaxa' => esc_html__('Demaxa', 'bdthemes-element-pack'),
                    'fill-circle' => esc_html__('Fill Circle', 'bdthemes-element-pack'),
                    'finalio' => esc_html__('Finalio', 'bdthemes-element-pack'),
                    'jemik' => esc_html__('Jemik', 'bdthemes-element-pack'),
                    'separk' => esc_html__('Separk', 'bdthemes-element-pack'),
                    'zigzag-dot' => esc_html__('Zigzag Dot', 'bdthemes-element-pack'),
                    'zozobe' => esc_html__('Zozobe', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'divider_align',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'default' => 'center',
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
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper' => 'text-align: {{VALUE}}; margin: 0 auto; margin-{{VALUE}}: 0;',
                ],
                'condition' => [
                    'title_separator_type!' => ['line', 'dashed', 'line-circle', 'line-cross', 'line-dashed', 'line-star', 'slash', 'rectangle', 'triangle', 'wave', 'kiss-curl', 'zemik', 'finest', 'furrow']
                ],
                'render_type' => 'template'
            ]
        );

        $this->add_responsive_control(
            'divider_line_align',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'default' => 'center',
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
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper' => 'text-align: {{VALUE}}; margin: 0 auto; margin-{{VALUE}}: 0;',
                ],
                'condition' => [
                    'title_separator_type' => ['line', 'dashed', 'line-circle', 'line-cross', 'line-dashed', 'line-star', 'slash', 'rectangle', 'triangle', 'wave', 'kiss-curl', 'zemik', 'finest', 'furrow']
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'title_separator_border_style',
            [
                'label' => esc_html__('Separator Style', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'solid',
                'options' => [
                    'solid' => esc_html__('Solid', 'bdthemes-element-pack'),
                    'dotted' => esc_html__('Dotted', 'bdthemes-element-pack'),
                    'dashed' => esc_html__('Dashed', 'bdthemes-element-pack'),
                    'groove' => esc_html__('Groove', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'title_separator_type' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator' => 'border-top-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_separator_line_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'title_separator_type' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_separator_height',
            [
                'label' => __('Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 15,
                    ]
                ],
                'condition' => [
                    'title_separator_type' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'title_separator_width',
            [
                'label' => __('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 300,
                    ]
                ],
                'condition' => [
                    'title_separator_type' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'title_separator_svg_fill_color',
            [
                'label' => esc_html__('Fill Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper svg *' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_separator_svg_stroke_color',
            [
                'label' => esc_html__('Stroke Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper svg *' => 'stroke: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'max_width',
            [
                'label' => __('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1200,
                        'min' => 100,
                    ],
                ],
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'line_cap',
            [
                'label' => esc_html__('Line Cap', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ep_square',
                'options' => [
                    'ep_square' => esc_html__('Square', 'bdthemes-element-pack'),
                    'ep_round' => esc_html__('Rounded', 'bdthemes-element-pack'),
                    'ep_butt' => esc_html__('Butt', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_svg_stroke_width',
            [
                'label' => __('Stroke Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 10,
                        'min' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper svg *' => 'stroke-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_crop',
            [
                'label' => __('Divider Crop', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper svg' => 'transform: scale({{SIZE}}) scale(0.01)',
                ],
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
            ]
        );

        $this->add_responsive_control(
            'max_height',
            [
                'label' => __('Match Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper svg' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title_separator_type!' => 'line'
                ],
            ]
        );

        $this->add_control(
            'title_separator_spacing',
            [
                'label' => __('Separator Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow .bdt-title-separator-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_readmore',
            [
                'label' => __('Read More', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'readmore' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'readmore_attention',
            [
                'label' => __('Attention', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->start_controls_tabs('tabs_readmore_style');

        $this->start_controls_tab(
            'tab_readmore_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_text_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-readmore' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-step-flow-readmore svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'readmore_background',
                'selector' => '{{WRAPPER}} .bdt-step-flow-readmore',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'readmore_border',
                'placeholder' => '1px',
                'separator' => 'before',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-step-flow-readmore'
            ]
        );

        $this->add_responsive_control(
            'readmore_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'readmore_shadow',
                'selector' => '{{WRAPPER}} .bdt-step-flow-readmore',
            ]
        );

        $this->add_responsive_control(
            'readmore_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'readmore_typography',
                'selector' => '{{WRAPPER}} .bdt-step-flow-readmore',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_readmore_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_hover_text_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-readmore:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-step-flow-readmore:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'readmore_hover_background',
                'selector' => '{{WRAPPER}} .bdt-step-flow-readmore:hover',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'readmore_hover_border_color',
            [
                'label' => __('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-readmore:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'readmore_border_border!' => ''
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'readmore_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-step-flow-readmore:hover',
            ]
        );

        $this->add_control(
            'readmore_hover_animation',
            [
                'label' => __('Hover Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_direction_style',
            [
                'label' => __('Direction', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_indicator' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'direction_animation',
            [
                'label'     => __('Hover Animation', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-direction-animation--',
                'condition' => [
                    'infinite_animation' => ''
                ]

            ]
        );

        $this->add_control(
            'infinite_animation',
            [
                'label'     => __('Infinite Animation', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-infinite-animation--',
            ]
        );

        $this->add_control(
            'hr2',
            [
                'type'      => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'direction_style',
            [
                'label' => __('Style', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => __('Style 1', 'bdthemes-element-pack'),
                    '2' => __('Style 2', 'bdthemes-element-pack'),
                    '3' => __('Style 3', 'bdthemes-element-pack'),
                    '4' => __('Style 4', 'bdthemes-element-pack'),
                    '5' => __('Style 5', 'bdthemes-element-pack'),
                    '6' => __('Style 6', 'bdthemes-element-pack'),
                    '7' => __('Style 7', 'bdthemes-element-pack'),
                    '8' => __('Style 8', 'bdthemes-element-pack'),
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_responsive_control(
            'direction_width',
            [
                'label' => __('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 150,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-direction-svg svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'direction_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'direction_offset_y',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'condition' => [
                    'direction_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}} .bdt-direction-svg' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'direction_offset_x',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'direction_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}} .bdt-direction-svg' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'direction_rotate',
            [
                'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -360,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-step-flow-direction-rotate: {{SIZE}}deg;'
                ],
                'condition' => [
                    'direction_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
            ]
        );

        $this->end_popover();

        $this->add_control(
            'direction_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-direction-svg svg *' => 'stroke: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_badge',
            [
                'label' => __('Badge (Step)', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-step-flow .bdt-step-flow-badge span' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'badge_background',
                'selector' => '{{WRAPPER}} .bdt-step-flow-badge span',
                
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'badge_border',
                'placeholder' => '1px',
                'separator' => 'before',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-step-flow-badge span',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'badge_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-step-flow-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'badge_shadow',
                'selector' => '{{WRAPPER}} .bdt-step-flow-badge span',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'selector' => '{{WRAPPER}} .bdt-step-flow-badge span',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_additional',
            [
                'label' => __('Additional', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => esc_html__('Content Inner Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-step-flow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );


        $this->end_controls_section();
    }

    protected function render_icon() {
        $settings = $this->get_settings_for_display();

        $has_icon = !empty($settings['selected_icon']);

        $has_image = !empty($settings['image']['url']);

        ?>

        <?php if ($has_icon or $has_image) : ?>
            <div class="bdt-step-flow-icon">
                <span class="bdt-icon-wrapper">


                    <?php if ('icon' == $settings['icon_type']) { ?>

                        <?php Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']); ?>

                    <?php } elseif ($has_image and 'image' == $settings['icon_type']) { 
                        $thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'thumbnail_size', $settings);
						if (!$thumb_url) {
						printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($settings['title_text']));
						} else {
							print(wp_get_attachment_image(
								$settings['image']['id'],
								$settings['thumbnail_size_size'],
								false,
								[
									'alt' => esc_html($settings['title_text'])
								]
							));
						}
                    } ?>
                </span>
            </div>
        <?php endif; ?>

    <?php
    }

    protected function render_title() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('step-flow-title', 'class', 'bdt-step-flow-title');

        if ('yes' == $settings['title_link'] and $settings['title_link_url']['url']) {

            $target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

            $this->add_render_attribute('step-flow-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
        }
    ?>

        <?php if ($settings['title_text']) : ?>
            <<?php echo Utils::get_valid_html_tag($settings['title_size']) . ' '; ?><?php echo $this->get_render_attribute_string('step-flow-title'); ?>>
                <span <?php echo $this->get_render_attribute_string('title_text'); ?>>
                    <?php echo wp_kses($settings['title_text'], element_pack_allow_tags('title')); ?>
                </span>
            </<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
        <?php endif; ?>

    <?php

    }

    public function render_separator() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('svg-image', 'class', 'bdt-animation-stroke');
        $this->add_render_attribute('svg-image', 'bdt-svg', 'stroke-animation: true;');

        $align = ('left' == $settings['divider_align'] or 'right' == $settings['divider_align']) ? '-' . $settings['divider_align'] : '';
        $svg_image = BDTEP_ASSETS_URL . 'images/divider/' . $settings['title_separator_type'] . $align . '.svg';

        $line_cap = $settings['line_cap'];

    ?>

        <img class="bdt-animation-stroke <?php echo $line_cap; ?>" src="<?php echo $svg_image; ?>" alt="advanced divider">

    <?php
    }

    public function render_direction() {
        $settings = $this->get_settings_for_display();

        $svg_image = BDTEP_ASSETS_URL . 'images/direction/step-' . $settings['direction_style'] . '.svg';

        $this->add_render_attribute('direction', 'class', 'bdt-direction-svg');

    ?>

        <div <?php echo $this->get_render_attribute_string('direction'); ?>>
            <img class="bdt-animation-stroke" data-bdt-svg="stroke-animation: true" src="<?php echo $svg_image; ?>" alt="Direction Arrows">
        </div>

    <?php
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('description_text', 'class', 'bdt-step-flow-description');

        $this->add_inline_editing_attributes('title_text', 'none');
        $this->add_inline_editing_attributes('description_text');


        $this->add_render_attribute('readmore', 'class', ['bdt-step-flow-readmore', 'bdt-display-inline-block']);

        if (!empty($settings['readmore_link']['url'])) {
            $this->add_link_attributes( 'readmore', $settings['readmore_link'] );
        }

        if ($settings['readmore_attention']) {
            $this->add_render_attribute('readmore', 'class', 'bdt-ep-attention-button');
        }

        if ($settings['readmore_hover_animation']) {
            $this->add_render_attribute('readmore', 'class', 'elementor-animation-' . $settings['readmore_hover_animation']);
        }

        if ($settings['onclick']) {
            $this->add_render_attribute('readmore', 'onclick', $settings['onclick_event']);
        }

        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('readmore', 'id', $settings['button_css_id']);
        }

        $this->add_render_attribute('step-flow', 'class', 'bdt-step-flow');

        if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

            $target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

            $this->add_render_attribute('step-flow', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')");
        }


    ?>
        <div <?php echo $this->get_render_attribute_string('step-flow'); ?>>

            <?php $this->render_icon(); ?>

            <div class="bdt-step-flow-content">

                <?php $this->render_title(); ?>

                <?php if ($settings['show_separator']) : ?>
                    <?php if ('line' == $settings['title_separator_type']) : ?>
                        <div class="bdt-title-separator-wrapper">
                            <div class="bdt-title-separator"></div>
                        </div>
                    <?php elseif ('line' != $settings['title_separator_type']) : ?>
                        <div class="bdt-title-separator-wrapper">
                            <?php $this->render_separator(); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($settings['description_text']) : ?>
                    <div <?php echo $this->get_render_attribute_string('description_text'); ?>>
                        <?php echo wp_kses($settings['description_text'], element_pack_allow_tags('text')); ?>
                    </div>
                <?php endif; ?>

                <?php if ($settings['readmore']) : ?>
                    <a <?php echo $this->get_render_attribute_string('readmore'); ?>>
                        <?php echo esc_html($settings['readmore_text']); ?>

                        <?php if ($settings['advanced_readmore_icon']['value']) : ?>

                            <span class="bdt-button-icon-align-<?php echo $settings['readmore_icon_align'] ?>">

                                <?php Icons_Manager::render_icon($settings['advanced_readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>

                            </span>

                        <?php endif; ?>
                    </a>
                <?php endif ?>

            </div>

            <?php if ($settings['show_indicator'] === 'yes') : ?>
                <?php $this->render_direction(); ?>

            <?php endif; ?>

        </div>

        <?php if ($settings['badge'] and '' != $settings['badge_text']) : ?>
            <div class="bdt-step-flow-badge bdt-position-<?php echo esc_attr($settings['badge_position']); ?>">
                <span class="bdt-badge"><?php echo esc_html($settings['badge_text']); ?></span>
            </div>
        <?php endif; ?>

<?php
    }
}
