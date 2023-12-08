<?php

namespace ElementPack\Modules\FloatingKnowledgebase\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit();
}

class Floating_Knowledgebase extends Module_Base {

    use Group_Control_Query;
    use Global_Widget_Controls;

    public $_query = null;
    public $data_json = [];

    public function get_name() {
        return 'bdt-floating-knowledgebase';
    }

    public function get_title() {
        return BDTEP . esc_html__('Floating Knowledgebase', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-floating-knowledgebase';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['floating', 'knowledgebase'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-floating-knowledgebase'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-floating-knowledgebase'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/02xNh5syhZ0';
    }

    public function get_query() {
        return $this->_query;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        //helper text switcher
        $this->add_control(
            'helper_text_switcher',
            [
                'label' => esc_html__('Helper Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'prefix_class' => 'bdt-helper-text-',
                'render_type' => 'template',
            ]
        );

        // text
        $this->add_control(
            'helper_text_heading_label',
            [
                'label' => esc_html__('Heading Label', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Have any queries?', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Have any queries?', 'bdthemes-element-pack'),
                'condition' => [
                    'helper_text_switcher' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'helper_text_label',
            [
                'label' => esc_html__('Text Label', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Check Help Center', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Check Help Center', 'bdthemes-element-pack'),
                'condition' => [
                    'helper_text_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'support_link_text',
            [
                'label' => esc_html__('Support Link Label', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Still no luck? We can help!', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Still no luck? We can help!', 'bdthemes-element-pack'),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'support_link',
            [
                'label' => esc_html__('Support Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'no_search_result',
            [
                'label' => esc_html__('No Search Result Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => esc_html__('Sorry, we don’t have any results. Updates are being added all the time.', 'bdthemes-element-pack'),
                'separator' => 'before',
            ]
        );

        //alignment
        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
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
                    'justify' => [
                        'title' => esc_html__('Justified', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-floating-knowledgebase' => 'text-align: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_header_content',
            [
                'label' => esc_html__('Header', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'logo',
            [
                'label' => esc_html__('Logo', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Knowledgebase', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Knowledgebase', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Search our knowledgebase or browse articles', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Search our knowledgebase or browse articles', 'bdthemes-element-pack'),
            ]
        );

        $this->end_controls_section();

        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();

        $this->end_controls_section();

        // Style
        //tigger button
        $this->start_controls_section(
            'section_trigger_button_style',
            [
                'label' => esc_html__('Trigger Button', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        // trigger button position choose control left right
        $this->add_control(
            'trigger_button_position',
            [
                'label' => esc_html__('Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'right',
                'toggle' => false,
                'prefix_class' => 'floating-help-center--btn-position-',
            ]
        );

        // trigger button tabs
        $this->start_controls_tabs('tabs_trigger_button_style');
        //normal
        $this->start_controls_tab(
            'tab_trigger_button_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'trigger_button_background',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .floating-help-center__btn .btn',
				'fields_options' => [
					'background' => [
						// 'label' => esc_html__('Overlay Color', 'pixel-gallery'),
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#20E2AD',
					],
                    'color_stop' => [
                        'default' => [
                            'unit' => '%',
                            'size' => 10,
                        ],
                    ],
					'color_b' => [
						'default' => '#0BB3E5',
					],
					'gradient_type' => [
						'default' => 'linear',
					],
					'gradient_angle' => [
						'default' => [
							'unit' => 'deg',
							'size' => 160,
						],
					],
  
				],
			]
		);

        //border group control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'trigger_button_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__btn .btn',
            ]
        );

        //border radius
        $this->add_responsive_control(
            'trigger_button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        //padding
        $this->add_responsive_control(
            'trigger_button_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'trigger_button_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'trigger_button_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__btn .btn',
            ]
        );
        $this->end_controls_tab();
        //active
        $this->start_controls_tab(
            'tab_trigger_button_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );
        // $this->add_control(
        //     'trigger_button_active_text_color',
        //     [
        //         'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .floating-help-center__btn.floating-help-center__popup--active .btn' => 'color: {{VALUE}};',
        //         ],
        //     ]
        // );
        //background group control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'trigger_button_active_background',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .floating-help-center__btn.floating-help-center__popup--active .btn',
            ]
        );
        //border color
        $this->add_control(
            'trigger_button_active_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn.floating-help-center__popup--active .btn' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_button_border_border!' => '',
                ],
            ]
        );
        //box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'trigger_button_active_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__btn.floating-help-center__popup--active .btn',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        //helper text style
        $this->start_controls_section(
            'section_helper_text_style',
            [
                'label' => esc_html__('Helper Text', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'helper_text_switcher' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'helper_text_color',
            [
                'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .helper-txt' => 'color: {{VALUE}};',
                ],
            ]
        );

        // arrow background color
        $this->add_control(
            'helper_text_arrow_background_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.floating-help-center--btn-position-right .helper-txt::before, {{WRAPPER}}.floating-help-center--btn-position-left .helper-txt::after, {{WRAPPER}} .floating-help-center__btn .helper-txt' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        //border group control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'helper_text_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__btn .helper-txt',
                'separator' => 'before',
            ]
        );
        //border radius
        $this->add_responsive_control(
            'helper_text_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .helper-txt' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //padding
        $this->add_responsive_control(
            'helper_text_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .helper-txt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'helper_text_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__btn .helper-txt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'helper_text_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__btn .helper-txt',
            ]
        );
        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'helper_text_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__btn .helper-txt',
            ]
        );

        $this->end_controls_section();

        // header section
        $this->start_controls_section(
            'section_header_style',
            [
                'label' => esc_html__('Header', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // header background type

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'header_background_type',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header',
				'fields_options' => [
					'background' => [
						// 'label' => esc_html__('Overlay Color', 'pixel-gallery'),
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#20E2AD',
					],
                    'color_stop' => [
                        'default' => [
                            'unit' => '%',
                            'size' => 10,
                        ],
                    ],
					'color_b' => [
						'default' => '#0BB3E5',
					],
					'gradient_type' => [
						'default' => 'linear',
					],
					'gradient_angle' => [
						'default' => [
							'unit' => 'deg',
							'size' => 160,
						],
					],
  
				],
			]
		);

        // border 
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'header_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header',
                'separator' => 'before',
            ]
        );

        // border radius
        $this->add_responsive_control(
            'header_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        // padding
        $this->add_responsive_control(
            'header_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        // margin
        $this->add_responsive_control(
            'header_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        // box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'header_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header',
            ]
        );

        $this->start_controls_tabs(
            'header_style_tabs'
        );
        
        $this->start_controls_tab(
            'header_style_logo_tab',
            [
                'label' => esc_html__( 'Logo', 'textdomain' ),
            ]
        );

        $this->add_responsive_control(
            'header_logo_width',
            [
                'label' => esc_html__('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-logo img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'header_logo_height',
            [
                'label' => esc_html__('Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-logo img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // border
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'header_logo_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header-logo img',
                'separator' => 'before',
            ]
        );

        // border radius
        $this->add_responsive_control(
            'header_logo_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        // margin
        $this->add_responsive_control(
            'header_logo_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'header_style_title_tab',
            [
                'label' => esc_html__( 'Title', 'textdomain' ),
            ]
        );

        $this->add_control(
            'header_title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        // text storke
        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'header_title_text_stroke',
                'label' => esc_html__('Text Stroke', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header-title',
            ]
        );
       
        // text box shadow
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'header_title_text_shadow',
                'label' => esc_html__('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header-title',
            ]
        );

        // typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'header_title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header-title',
            ]
        );

        $this->add_responsive_control(
            'header_title_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'header_style_text_tab',
            [
                'label' => esc_html__( 'Text', 'textdomain' ),
            ]
        );

        $this->add_control(
            'header_text_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        // typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'header_text_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .bdt-header-description',
            ]
        );

        $this->add_responsive_control(
            'header_text_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .bdt-header-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
       



        $this->end_controls_section();

        //wrapper style
        $this->start_controls_section(
            'section_wrapper_style',
            [
                'label' => esc_html__('Items Wrapper', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        //background group control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'wrapper_background',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .floating-help-center__popup',
            ]
        );

        //border group control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'wrapper_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup',
                'separator' => 'before',
            ]
        );
        //border radius
        $this->add_responsive_control(
            'wrapper_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //padding
        $this->add_responsive_control(
            'wrapper_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'wrapper_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'wrapper_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup',
            ]
        );

        $this->end_controls_section();

        //search style

        $this->start_controls_section(
            'section_search_style',
            [
                'label' => esc_html__('Search', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //color
        $this->add_control(
            'search_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__input' => 'color: {{VALUE}};',
                ],

            ]
        );
        $this->add_control(
            'placeholder_search_color',
            [
                'label' => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__input::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );
        //background
        $this->add_control(
            'search_background',
            [
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        //border
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'search_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .searchbox',
                'separator' => 'before',
            ]
        );
        //border radius
        $this->add_responsive_control(
            'search_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //padding
        $this->add_responsive_control(
            'search_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'search_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'search_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .searchbox',
            ]
        );

        // typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'search_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__input',
            ]
        );

        // search icon color
        $this->add_control(
            'search_icon_color',
            [
                'label' => esc_html__('Search Icon Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__search-icon svg' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );
        //size
        $this->add_responsive_control(
            'search_icon_size',
            [
                'label' => esc_html__('Search Icon Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__search-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        //search close icon color
        $this->add_control(
            'search_close_icon_color',
            [
                'label' => esc_html__('Search Close Icon Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__cross-icon svg' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );
        //size
        $this->add_responsive_control(
            'search_close_icon_size',
            [
                'label' => esc_html__('Search Close Icon Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .searchbox .searchbox__cross-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->end_controls_section();

        //item style
        $this->start_controls_section(
            'section_item_style',
            [
                'label' => esc_html__('Items', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //items tabs
        $this->start_controls_tabs('tabs_item_style');
        //normal
        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        // background group control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item',
            ]
        );
        //border group control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item',
                'separator' => 'before',
            ]
        );
        //border radius
        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //padding
        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        $this->add_responsive_control(
            'item_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'label' => esc_html__('Box Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item',
            ]
        );
        //end normal tab
        $this->end_controls_tab();
        //hover
        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );
        // background group control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_hover_background',
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item:hover',
            ]
        );

        //border color
        $this->add_control(
            'item_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'item_border_border!' => '',
                ],
                'separator' => 'before',
            ]
        );

        //end hover tab
        $this->end_controls_tab();
        //end tabs
        $this->end_controls_tabs();

        $this->end_controls_section();

        //item title style
        $this->start_controls_section(
            'section_item_title_style',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //item title color
        $this->add_control(
            'item_title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item-txt, {{WRAPPER}} .floating-help-center__popup .help-list .help-list__item-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );
        //item title hover color
        $this->add_control(
            'item_title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item:hover .help-list__item-txt, {{WRAPPER}} .floating-help-center__popup .help-list .help-list__item:hover .help-list .help-list__item-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        //item title typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item-txt, {{WRAPPER}} .floating-help-center__popup .help-list .help-list__item-arrow',
            ]
        );
        //text shadow
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'item_title_text_shadow',
                'label' => esc_html__('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item-txt',
            ]
        );
        //text stroke
        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'item_title_text_stroke',
                'label' => esc_html__('Text Stroke', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .help-list .help-list__item-txt',
            ]
        );
        $this->end_controls_section();

        //text style
        $this->start_controls_section(
            'section_inner_title_style',
            [
                'label' => esc_html__('Inner Title', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //inner_title color
        $this->add_control(
            'inner_title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-floating-knowledgebase .html-content__title' => 'color: {{VALUE}};',
                ],
            ]
        );
        //padding
        $this->add_responsive_control(
            'inner_title_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-floating-knowledgebase .html-content__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'inner_title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-floating-knowledgebase .html-content__title',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_text_style',
            [
                'label' => esc_html__('Text', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //text color
        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .html-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        // background color
        $this->add_control(
            'text_background_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .html-content' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        //padding
        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .html-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .html-content',
            ]
        );
        $this->end_controls_section();

        //external link style
        $this->start_controls_section(
            'section_external_link_style',
            [
                'label' => esc_html__('External Link', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //external link tabs
        $this->start_controls_tabs('tabs_external_link_style');
        //external link normal tab
        $this->start_controls_tab(
            'tab_external_link_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        //external link color
        $this->add_control(
            'external_link_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .external .external__link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'external_link_background',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .floating-help-center__popup .external .external__link',
				'fields_options' => [
					'background' => [
						// 'label' => esc_html__('Overlay Color', 'pixel-gallery'),
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#20E2AD',
					],
                    'color_stop' => [
                        'default' => [
                            'unit' => '%',
                            'size' => 10,
                        ],
                    ],
					'color_b' => [
						'default' => '#0BB3E5',
					],
					'gradient_type' => [
						'default' => 'linear',
					],
					'gradient_angle' => [
						'default' => [
							'unit' => 'deg',
							'size' => 160,
						],
					],
  
				],
			]
		);

        //border group control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'external_link_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .external .external__link',
            ]
        );
        //border radius
        $this->add_responsive_control(
            'external_link_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .external .external__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //padding
        $this->add_responsive_control(
            'external_link_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .external .external__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'external_link_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .external' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'external_link_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .floating-help-center__popup .external .external__link',
            ]
        );
        //end external link normal tab
        $this->end_controls_tab();
        //external link hover tab
        $this->start_controls_tab(
            'tab_external_link_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );
        //external link hover color
        $this->add_control(
            'external_link_hover_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .external .external__link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        //background group control

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'external_link_hover_background',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .floating-help-center__popup .external .external__link:hover',
				'fields_options' => [
					'background' => [
						// 'label' => esc_html__('Overlay Color', 'pixel-gallery'),
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#20E2AD',
					],
                    'color_stop' => [
                        'default' => [
                            'unit' => '%',
                            'size' => 10,
                        ],
                    ],
					'color_b' => [
						'default' => '#0BB3E5',
					],
					'gradient_type' => [
						'default' => 'linear',
					],
					'gradient_angle' => [
						'default' => [
							'unit' => 'deg',
							'size' => 360,
						],
					],
  
				],
			]
		);


        // border color
        $this->add_control(
            'external_link_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .floating-help-center__popup .external .external__link:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'external_link_border_border!' => '',
                ],
                'separator' => 'before',
            ]
        );
        //end external link hover tab
        $this->end_controls_tab();
        //end external link tabs
        $this->end_controls_tabs();

        $this->end_controls_section();

        // resizer section
        $this->start_controls_section(
            'section_resizer_style',
            [
                'label' => esc_html__('Resizer Icon', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // color
        $this->add_control(
            'resizer_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-resizer-icon svg' => 'color: {{VALUE}};',
                ],
            ]
        );

        // hover color
        $this->add_control(
            'resizer_hover_color',
            [
                'label' => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-resizer-icon:hover svg' => 'color: {{VALUE}};',
                ],
            ]
        );

        // size
        $this->add_responsive_control(
            'resizer_icon_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-resizer-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function get_taxonomies() {
        $taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

        $options = ['' => ''];

        foreach ($taxonomies as $taxonomy) {
            $options[$taxonomy->name] = $taxonomy->label;
        }

        return $options;
    }

    public function get_posts_tags() {
        $taxonomy = $this->get_settings('taxonomy');

        foreach ($this->_query->posts as $post) {
            if (!$taxonomy) {
                $post->tags = [];

                continue;
            }

            $tags = wp_get_post_terms($post->ID, $taxonomy);

            $tags_slugs = [];

            foreach ($tags as $tag) {
                $tags_slugs[$tag->term_id] = $tag;
            }

            $post->tags = $tags_slugs;
        }
    }

    /**
     * Get post query builder arguments
     */
    public function query_posts($posts_per_page) {
        $settings = $this->get_settings_for_display();

        $args = [];
        if ($posts_per_page) {
            $args['posts_per_page'] = $posts_per_page;
            $args['paged'] = max(1, get_query_var('paged'), get_query_var('page'));
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        $this->_query = new \WP_Query($args);
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-ep-floating-knowledgebase-' . $this->get_id();

        if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['posts_limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }

        $this->query_posts($limit);

        $wp_query = $this->get_query();

        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) :
                $wp_query->the_post();
                // convert outpur to json

                $post_id = get_the_ID();
                $post_title = get_the_title();
                $post_content = get_the_content();
                array_push($this->data_json, array('title' => $post_title, 'html' => $post_content));
            endwhile;
        endif;

        $this->add_render_attribute(
            [
                'floating-knowledgebase' => [
                    'class' => 'bdt-floating-knowledgebase',
                    'data-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id" => $id,
                                "helperTextLabel" => $settings['helper_text_heading_label'] . ' <br><strong>' . $settings['helper_text_label'] . '</strong>',
                                'data_source' => $this->data_json,
                                'supportLinkText' => $settings['support_link_text'],
                                'supportLink' => $settings['support_link']['url'],
                                'noSearchResultText' => $settings['no_search_result'],
                                'logo' => $settings['logo'],
                                'title' => $settings['title'],
                                'description' => $settings['description'],
                            ])
                        ),
                    ],
                ],
            ]
        );

?>
        <div <?php echo $this->get_render_attribute_string('floating-knowledgebase'); ?>>

            <div id="bdt-floating-help-center" class="floating-help-center"></div>
            <?php
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) :
                printf('<div class="bdt-alert-warning" bdt-alert><a class="bdt-alert-close" bdt-close></a><p>%s</p></div>', esc_html__('Floating Knowledgebase Widget Placed Here (Only Visible for Editor).', 'bdthemes-element-pack'));
            endif;
            ?>
        </div>
<?php
    }
}
