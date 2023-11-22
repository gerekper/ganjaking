<?php

namespace ElementPack\Modules\TableOfContent\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Table_Of_Content extends Module_Base {
	public function get_name() {
		return 'bdt-table-of-content';
	}

	public function get_title() {
		return BDTEP . esc_html__('Table of Content', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-table-of-content';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['table', 'content', 'index'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-table-of-content'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['jquery-ui-widget', 'tocify', 'ep-scripts'];
        } else {
			return ['jquery-ui-widget', 'tocify', 'ep-table-of-content'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/DbPrqUD8cOY';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_table_of_content',
			[
				'label' => esc_html__('Table of Content', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'    => __('Layout', 'bdthemes-element-pack'),
				'type'     => Controls_Manager::SELECT,
				'default'  => 'offcanvas',
				'options'  => [
					'offcanvas' => esc_html__('Offcanvas', 'bdthemes-element-pack'),
					'fixed'     => esc_html__('Fixed', 'bdthemes-element-pack'),
					'dropdown'  => esc_html__('Dropdown', 'bdthemes-element-pack'),
					'regular'   => esc_html__('Regular', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'index_align',
			[
				'label'    => __('Offcanvas Position', 'bdthemes-element-pack'),
				'type'     => Controls_Manager::SELECT,
				'default'  => 'left',
				'options'  => [
					'left'     => esc_html__('Left', 'bdthemes-element-pack'),
					'right'    => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'layout' => 'offcanvas',
				]
			]
		);

		$this->add_control(
			'fixed_position',
			[
				'label'    => __('Content Position', 'bdthemes-element-pack'),
				'type'     => Controls_Manager::SELECT,
				'default'  => 'top-left',
				'options'  => [
					'top-left'     => esc_html__('Top-Left', 'bdthemes-element-pack'),
					'top-right'    => esc_html__('Top-Right', 'bdthemes-element-pack'),
					'bottom-left'  => esc_html__('Bottom-Left', 'bdthemes-element-pack'),
					'bottom-right' => esc_html__('Bottom-Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'layout' => 'fixed',
				]
			]
		);

		$this->add_control(
			'selectors',
			[
				'label'       => __('Index Tags', 'bdthemes-element-pack'),
				'description' => __('Want to ignore any specific heading? Go to that heading advanced tab and enter <b>ignore-this-tag</b> class in <a href="http://prntscr.com/lvw4iy" target="_blank">CSS Classes</a> input field.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'default'     => ['h2', 'h3', 'h4'],
				'options'     => element_pack_heading_size(),
			]
		);

		$this->add_control(
			'fixed_index_offset_toggle',
			[
				'label' => __('Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('None', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
				'separator' => 'before',
				'condition' => [
					'layout' => 'fixed'
				]
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'fixed_index_horizontal_offset',
			[
				'label'     => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
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
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
					'layout' => 'fixed',
					'fixed_index_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-fixed-index-h-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'fixed_index_vertical_offset',
			[
				'label'   => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
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
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
					'layout' => 'fixed',
					'fixed_index_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-fixed-index-v-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->end_popover();

		$this->add_control(
			'offset',
			[
				'label'     => __('Scroll to Indexed Top Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'description'     => __('Scroll will stop after this offset from top', 'bdthemes-element-pack'),
				'default'   => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
					],
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label'      => esc_html__('Content Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range'      => [
					'px' => [
						'min' => 240,
						'max' => 1200,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					]
				],
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-offcanvas-bar' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-card-secondary'    => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_ofc_btn',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'condition' => [
					'layout!' => ['fixed', 'regular']
				]
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Table Of Index',
				'placeholder' => 'Table of Index',
			]
		);

		$this->add_control(
			'table_button_icon',
			[
				'label'       => __('Button Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_icon',
				'default' => [
					'value' => 'fas fa-book',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'button_icon_align',
			[
				'label'   => __('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => __('Left', 'bdthemes-element-pack'),
					'right'  => __('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'table_button_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_icon_indent',
			[
				'label' => __('Icon Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'condition' => [
					'table_button_icon[value]!' => '',
					'button_text[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-button-icon-align-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-button-icon-align-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_position',
			[
				'label'   => esc_html__('Button Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_position(),
				'default' => 'top-left',
				'condition' => [
					'layout' => ['offcanvas', 'dropdown'],
				]
			]
		);

		$this->add_control(
            'button_offset_toggle',
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
            'btn_horizontal_offset',
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
                        'step' => 1,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'button_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-btn-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_vertical_offset',
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
                        'step' => 1,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'button_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-btn-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'button_rotate',
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
                    'button_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-btn-rotate: {{SIZE}}deg;'
                ],
            ]
        );

        $this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_option',
			[
				'label'     => esc_html__('Dropdown Options', 'bdthemes-element-pack'),
				'condition' => [
					'layout' => 'dropdown',
				]
			]
		);

		$this->add_control(
			'drop_position',
			[
				'label'   => esc_html__('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-left',
				'options' => element_pack_drop_position(),
			]
		);

		$this->add_control(
			'drop_mode',
			[
				'label'   => esc_html__('Mode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'click'    => esc_html__('Click', 'bdthemes-element-pack'),
					'hover'  => esc_html__('Hover', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'drop_flip',
			[
				'label' => esc_html__('Flip Dropbar', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'drop_offset',
			[
				'label'   => esc_html__('Dropbar Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'max' => 100,
						'step' => 5,
					],
				],
			]
		);

		$this->add_control(
			'drop_animation',
			[
				'label'     => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => element_pack_transition_options(),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'drop_duration',
			[
				'label'   => esc_html__('Animation Duration', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'max' => 4000,
						'step' => 50,
					],
				],
				'condition' => [
					'drop_animation!' => '',
				],
			]
		);

		$this->add_control(
			'drop_show_delay',
			[
				'label'   => esc_html__('Show Delay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'max' => 1000,
						'step' => 100,
					],
				],
			]
		);

		$this->add_control(
			'drop_hide_delay',
			[
				'label'   => esc_html__('Hide Delay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range' => [
					'px' => [
						'max' => 10000,
						'step' => 100,
					],
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_table_of_content',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'context',
			[
				'label'       => __('Index Area (any class/id selector)', 'bdthemes-element-pack'),
				'description'       => __('Any class or ID selector accept here for your table of content.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '.elementor',
				'placeholder' => '.elementor / #container',
			]
		);

		$this->add_control(
			'auto_collapse',
			[
				'label'     => esc_html__('Auto Collapse Sub Index', 'bdthemes-element-pack'),
				'separator' => 'before',
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'history',
			[
				'label' => esc_html__('Click History', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'description'       => __('Click History is the list of web pages a user has visited recently of Table Of Content.', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'toc_index_header',
			[
				'label'       => __('Index Header Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'Table of Content',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'toc_sticky_mode',
			[
				'label'   => esc_html__('Index Sticky', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'layout' => 'regular',
				],
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'toc_sticky_offset',
			[
				'label'   => esc_html__('Sticky Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'toc_sticky_mode' => 'yes',
					'layout' => 'regular',
				],
			]
		);

		$this->add_control(
			'toc_sticky_on_scroll_up',
			[
				'label'        => esc_html__('Sticky on Scroll Up', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => esc_html__('Set sticky options when you scroll up your mouse.', 'bdthemes-element-pack'),
				'condition' => [
					'toc_sticky_mode' => 'yes',
					'layout' => 'regular',
				],
			]
		);

		$this->add_control(
			'toc_sticky_edge',
			[
				'label'       => __('Scrolling Area', 'bdthemes-element-pack'),
				'description' => __('Set the css class/ID scrolling area section parent tag class/ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => '#parent-section',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_offcanvas',
			[
				'label' => esc_html__('Box', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'index_background',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-toc-{{ID}} > div' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'index_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#bdt-toc-{{ID}} > div'
			]
		);

		$this->add_control(
			'index_radius',
			[
				'label'      => __('Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'#bdt-toc-{{ID}} > div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'index_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-toc-{{ID}} > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'index_min_height',
			[
				'label' => __('Min Height', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'#bdt-toc-{{ID}} > div' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'index_box_shadow',
				'label'    => __('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '#bdt-toc-{{ID}} > div',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_style',
			[
				'label' => __('Header', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'toc_index_header!' => '',
				],
			]
		);

		$this->add_control(
			'header_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-table-of-content-header h4' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'header_background_color',
				'selector' => '#bdt-toc-{{ID}} .bdt-table-of-content-header h4',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'header_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#bdt-toc-{{ID}} .bdt-table-of-content-header'
			]
		);

		$this->add_control(
			'header_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'#bdt-toc-{{ID}} .bdt-table-of-content-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-toc-{{ID}} .bdt-table-of-content-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-toc-{{ID}} .bdt-table-of-content-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'header_box_shadow',
				'label'    => __('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '#bdt-toc-{{ID}} .bdt-table-of-content-header',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_typography',
				'selector' => '#bdt-toc-{{ID}} .bdt-table-of-content-header, #bdt-toc-{{ID}} .bdt-table-of-content-header h4',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_list',
			[
				'label' => esc_html__('List', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'index_spacing',
			[
				'label'   => esc_html__('Title Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'size_units' => ['px'],
				'selectors'  => [
					'.bdt-table-of-content .bdt-nav li a, .bdt-table-of-content .bdt-nav>.bdt-nav li a' => 'padding: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'index_typography',
				'selector' => '#bdt-toc-{{ID}} .bdt-nav > li > a',
			]
		);

		$this->start_controls_tabs('index_title_style');

		$this->start_controls_tab(
			'normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-nav > li > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'index_title_underline_normal',
			[
				'label'     => __('Underline', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-nav > li > a' => 'text-decoration: underline',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'index_title_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-nav > li > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'index_title_underline_hover',
			[
				'label'     => __('Underline', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-nav > li > a:hover' => 'text-decoration: underline',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-nav > li.bdt-active > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'index_title_underline_active',
			[
				'label'     => __('Underline', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'#bdt-toc-{{ID}} .bdt-nav > li.bdt-active > a' => 'text-decoration: underline',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_ofc_btn',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout!' => ['fixed', 'regular']
				]
			]
		);

		$this->start_controls_tabs('tabs_ofc_btn_style');

		$this->start_controls_tab(
			'tab_ofc_btn_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button'
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button'
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_ofc_btn_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'ofc_btn_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ofc_btn_hover_bg',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ofc_btn_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ofc_btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-wrapper a.bdt-toggle-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ('dropdown' == $settings['layout']) {
			$this->layout_dropdown();
		} elseif ('fixed' == $settings['layout']) {
			$this->layout_fixed();
		} elseif ('regular' == $settings['layout']) {
			$this->layout_regular();
		} else {
			$this->layout_offcanvas();
		}
	}

	private function render_toggle_button_content() {
		$settings    = $this->get_settings_for_display();

		if (!isset($settings['button_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['button_icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset($settings['__fa4_migrated']['table_button_icon']);
		$is_new    = empty($settings['button_icon']) && Icons_Manager::is_migration_allowed();

		$settings['button_icon'] = 'fa fa-book';

?>
		<span class="elementor-button-content-wrapper">

			<?php if ($settings['button_text']) : ?>
				<span class="bdt-toggle-button-text">
					<?php echo esc_html($settings['button_text']); ?>
				</span>
			<?php endif; ?>

			<?php if ($is_new || $migrated || $settings['button_icon']) : ?>
				<span class="bdt-toggle-button-icon elementor-button-icon bdt-button-icon-align-<?php echo esc_attr($settings['button_icon_align']); ?>">

					<?php if ($is_new || $migrated) :
						Icons_Manager::render_icon($settings['table_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
					else : ?>
						<i class="<?php echo esc_attr($settings['button_icon']); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</span>
			<?php endif; ?>

		</span>
	<?php
	}

	private function layout_fixed() {
		$settings    = $this->get_settings_for_display();
		$id       = 'bdt-toc-' . $this->get_id();
	?>
		<div class="table-of-content-layout-fixed bdt-position-<?php echo esc_attr($settings['fixed_position']); ?>" id="<?php echo esc_attr($id); ?>">
			<div class="bdt-card bdt-card-secondary bdt-card-body">
				<?php $this->table_of_content_header(); ?>
				<?php $this->table_of_content(); ?>
			</div>
		</div>
	<?php
	}

	private function layout_regular() {
		$settings    = $this->get_settings_for_display();
		$id       = 'bdt-toc-' . $this->get_id();

		$this->add_render_attribute('toc-regular', 'class', 'table-of-content-layout-regular');
		$this->add_render_attribute('toc-regular', 'id', esc_attr($id));

		if ('yes' == $settings['toc_sticky_mode']) {

			$this->add_render_attribute('toc-regular', 'data-bdt-sticky', 'media: 1024;');

			if ($settings['toc_sticky_offset']['size']) {
				$this->add_render_attribute('toc-regular', 'data-bdt-sticky', 'offset: ' . $settings['toc_sticky_offset']['size'] . ';');
			}
			if ($settings['toc_sticky_on_scroll_up']) {
				$this->add_render_attribute('toc-regular', 'data-bdt-sticky', 'show-on-up: true; animation: bdt-animation-slide-top;');
			}
			if ($settings['toc_sticky_edge']) {
				$this->add_render_attribute('toc-regular', 'data-bdt-sticky', 'bottom: ' . esc_attr($settings['toc_sticky_edge']) . ';');
			}
		}

	?>
		<div <?php echo $this->get_render_attribute_string('toc-regular'); ?>>
			<div>
				<?php $this->table_of_content_header(); ?>
				<?php $this->table_of_content(); ?>
			</div>
		</div>
	<?php
	}

	private function layout_dropdown() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-toc-' . $this->get_id();

		$this->add_render_attribute(
			[
				'drop-settings' => [
					'class'    => ['bdt-drop', 'bdt-card', 'bdt-card-secondary'],
					'id' => 'bdt-toc-' . $this->get_id(),
					'bdt-drop' => [
						wp_json_encode([
							"toggle"     => "#" . $id,
							"pos"        => $settings["drop_position"],
							"mode"       => $settings["drop_mode"],
							"delay-show" => $settings["drop_show_delay"]["size"],
							"delay-hide" => $settings["drop_hide_delay"]["size"],
							"flip"       => $settings["drop_flip"] ? true : false,
							"offset"     => $settings["drop_offset"]["size"],
							"animation"  => $settings["drop_animation"] ? "bdt-animation-" . $settings["drop_animation"] : false,
							"duration"   => ($settings["drop_duration"]["size"] and $settings["drop_animation"]) ? $settings["drop_duration"]["size"] : "0"
						]),
					],
				],
			]
		);


	?>
		<div class="table-of-content-layout-dropdown">
			<div class="bdt-toggle-button-wrapper bdt-position-fixed bdt-position-<?php echo esc_attr($settings['button_position']); ?>">
				<a id="<?php echo esc_attr($id); ?>" class="bdt-toggle-button elementor-button elementor-size-sm" href="#">
					<?php $this->render_toggle_button_content(); ?>
				</a>
			</div>
			<div <?php echo $this->get_render_attribute_string('drop-settings'); ?>>
				<div class="bdt-card-body">
					<?php $this->table_of_content_header(); ?>
					<?php $this->table_of_content(); ?>
				</div>
			</div>
		</div>
	<?php
	}

	private function table_of_content_header() {
		$settings    = $this->get_settings_for_display();

		if (empty($settings['toc_index_header'])) {
			return;
		}
	?>
		<div class="bdt-table-of-content-header">
			<h4><?php echo esc_html($settings['toc_index_header']); ?></h4>
		</div>
	<?php
	}

	private function layout_offcanvas() {

		$settings    = $this->get_settings_for_display();
		$id          = 'bdt-toc-' . $this->get_id();
		$index_align = $settings['index_align'] ?: 'right';

		$this->add_render_attribute('offcanvas', 'id',  $id);
		$this->add_render_attribute('offcanvas', 'class',  ['bdt-offcanvas', 'bdt-ofc-table-of-content', 'bdt-flex', 'bdt-flex-middle']);

		if ('right' == $index_align) {
			$this->add_render_attribute('offcanvas', 'bdt-offcanvas',  'flip: true');
		} else {
			$this->add_render_attribute('offcanvas', 'bdt-offcanvas',  '');
		}

	?>
		<div class="table-of-content-layout-offcanvas">
			<div class="bdt-toggle-button-wrapper bdt-position-fixed bdt-position-<?php echo esc_attr($settings['button_position']); ?>">
				<a class="bdt-toggle-button elementor-button elementor-size-sm" bdt-toggle="target: #<?php echo esc_attr($id); ?>" href="#">
					<?php $this->render_toggle_button_content(); ?>
				</a>
			</div>

			<div <?php echo $this->get_render_attribute_string('offcanvas'); ?>>
				<div class="bdt-offcanvas-bar bdt-offcanvas-push">
					<button class="bdt-offcanvas-close" type="button" bdt-close></button>
					<?php $this->table_of_content_header(); ?>
					<?php $this->table_of_content(); ?>
				</div>
			</div>
		</div>
	<?php
	}

	private function table_of_content() {
		$settings    = $this->get_settings_for_display();

		$this->add_render_attribute(
			[
				'table-of-content' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"context"        => $settings["context"],
							"selectors"      => implode(",", $settings["selectors"]),
							"ignoreSelector" => ".ignore-this-tag [class*='-heading-title']",
							"showAndHide"    => $settings["auto_collapse"] ? true : false,
							"scrollTo"       => $settings["offset"]["size"],
							"history"        => $settings["history"] ? true : false,
						]))
					]
				]
			]
		);

	?>
		<div class="bdt-table-of-content" <?php echo $this->get_render_attribute_string('table-of-content'); ?>></div>
<?php
	}
}
