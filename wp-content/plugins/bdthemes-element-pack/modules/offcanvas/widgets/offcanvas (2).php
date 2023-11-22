<?php

namespace ElementPack\Modules\Offcanvas\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Element_Pack_Loader;
use Elementor\Icons_Manager;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Offcanvas Widget
 * @since 1.2.0
 */
class Offcanvas extends Module_Base {

	public function get_name() {
		return 'bdt-offcanvas';
	}

	public function get_title() {
		return BDTEP . esc_html__('Offcanvas', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-offcanvas';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['offcanvas', 'menu', 'navigator'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-offcanvas'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return [ 'ep-offcanvas' ];
        }
  	}

	public function get_custom_help_url() {
		return 'https://youtu.be/CrrlirVfmQE';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Default', 'bdthemes-element-pack'),
					'custom'  => esc_html__('Custom Link', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'offcanvas_custom_id',
			[
				'label'       => esc_html__('Offcanvas Selector', 'bdthemes-element-pack'),
				'description' => __('Set your offcanvas selector here. For example: <b>.custom-link</b> or <b>#customLink</b>. Set this selector where you want to link this offcanvas.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '#bdt-custom-offcanvas',
				'condition'   => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'source',
			[
				'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sidebar',
				'options' => [
					'sidebar'   => esc_html__('Sidebar', 'bdthemes-element-pack'),
					'elementor' => esc_html__('Elementor Template', 'bdthemes-element-pack'),
					'anywhere'  => esc_html__('AE Template', 'bdthemes-element-pack'),
				],
			]
		);
		$this->add_control(
			'template_id',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'elementor_template',
				],
				'condition'   => ['source' => 'elementor'],
			]
		);

		$this->add_control(
			'sidebars',
			[
				'label'       => esc_html__('Choose Sidebar', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 0,
				'options'     => element_pack_sidebar_options(),
				'label_block' => 'true',
				'condition'   => ['source' => 'sidebar'],
			]
		);

		$this->add_control(
			'anywhere_id',
			[
				'label'       => esc_html__('Choose Template', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => element_pack_ae_options(),
				'label_block' => 'true',
				'condition'   => ['source' => 'anywhere'],
				'render_type' => 'template',
			]
		);


		$this->add_control(
			'custom_content_before_switcher',
			[
				'label' => esc_html__('Custom Content Before', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_content_after_switcher',
			[
				'label' => esc_html__('Custom Content After', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'offcanvas_overlay',
			[
				'label'        => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'offcanvas_animations',
			[
				'label'     => esc_html__('Animations', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'options'   => [
					'slide'  => esc_html__('Slide', 'bdthemes-element-pack'),
					'push'   => esc_html__('Push', 'bdthemes-element-pack'),
					'reveal' => esc_html__('Reveal', 'bdthemes-element-pack'),
					'none'   => esc_html__('None', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'offcanvas_flip',
			[
				'label'        => esc_html__('Flip', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'right',
			]
		);

		$this->add_control(
			'offcanvas_close_button',
			[
				'label'   => esc_html__('Close Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'offcanvas_close_button_text',
			[
				'label'       => esc_html__('Close Button Text', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				// 'default'     => esc_html__('Close', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Close', 'bdthemes-element-pack'),
				'condition' => [
					'offcanvas_close_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'button_close_icon_align',
			[
				'label'   => esc_html__('Close Button Align', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'offcanvas_close_button' => 'yes',
				],
				'selectors_dictionary' => [
					'left' => 'left: 10px; right: auto;',
					'center' => 'left: 50%; right: auto; transform: translateX(-50%);',
					'right' => 'right: 10px; left: auto;',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_bg_close',
			[
				'label'   => esc_html__('Close on Click Background', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'offcanvas_esc_close',
			[
				'label'   => esc_html__('Close on Press ESC', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'offcanvas_width',
			[
				'label'      => esc_html__('Width', 'bdthemes-element-pack'),
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
					'body:not(.bdt-offcanvas-flip) {{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar' => 'width: {{SIZE}}{{UNIT}};left: -{{SIZE}}{{UNIT}};',
					'body:not(.bdt-offcanvas-flip) {{WRAPPER}} .bdt-offcanvas.bdt-open>.bdt-offcanvas-bar' => 'left: 0;',
					'.bdt-offcanvas-flip {{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar' => 'width: {{SIZE}}{{UNIT}};right: -{{SIZE}}{{UNIT}};',
					'.bdt-offcanvas-flip {{WRAPPER}} .bdt-offcanvas.bdt-open>.bdt-offcanvas-bar' => 'right: 0;',
				],
				'condition' => [
					'offcanvas_animations!' => ['push', 'reveal'],
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'offcanvas_height',
			[
				'label'      => esc_html__('Height', 'bdthemes-element-pack'),
				'description' => esc_html__('This height option only needs for rare designs. When you will not get the proper height of Offcanvas, you may use this option in that situation.', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					]
				],
				'selectors' => [
					'body:not(.bdt-offcanvas-flip) {{WRAPPER}} .bdt-offcanvas' => 'height: {{SIZE}}{{UNIT}};',
					'.bdt-offcanvas-flip {{WRAPPER}} .bdt-offcanvas' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'offcanvas_animations!' => ['push', 'reveal'],
				]
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_custom_before',
			[
				'label'     => esc_html__('Custom Content Before', 'bdthemes-element-pack'),
				'condition' => [
					'custom_content_before_switcher' => 'yes',
				]
			]
		);

		$this->add_control(
			'custom_content_before',
			[
				'label'   => esc_html__('Custom Content Before', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => ['active' => true],
				'default' => esc_html__('This is your custom content for before of your offcanvas.', 'bdthemes-element-pack'),
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_content_custom_after',
			[
				'label'     => esc_html__('Custom Content After', 'bdthemes-element-pack'),
				'condition' => [
					'custom_content_after_switcher' => 'yes',
				]
			]
		);


		$this->add_control(
			'custom_content_after',
			[
				'label'   => esc_html__('Custom Content After', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => ['active' => true],
				'default' => esc_html__('This is your custom content for after of your offcanvas.', 'bdthemes-element-pack'),
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_content_offcanvas_button',
			[
				'label' => esc_html__('Button', 'bdthemes-element-pack'),
				'condition'   => [
					'layout' => 'default',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Offcanvas', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Offcanvas', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'   => esc_html__('Button Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default'      => 'left',
			]
		);

		$this->add_control(
            'button_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'button_offset',
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
                    '{{WRAPPER}}' => '--ep-offcanvas-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'button_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack') . BDTEP_NC,
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
                    '{{WRAPPER}}' => '--ep-offcanvas-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'button_rotate',
            [
                'label' => esc_html__('Rotate', 'bdthemes-element-pack') . BDTEP_NC,
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
                    '{{WRAPPER}}' => '--ep-offcanvas-rotate: {{SIZE}}deg;'
                ],
            ]
        );

        $this->end_popover();

		$this->add_control(
			'size',
			[
				'label'   => __('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => element_pack_button_sizes(),
			]
		);

		$this->add_control(
			'offcanvas_button_icon',
			[
				'label'       => esc_html__('Button Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_icon',
				'default' => [
					'value' => 'fas fa-bars',
					'library' => 'fa-solid',
				],
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$this->add_control(
			'button_icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__('Left', 'bdthemes-element-pack'),
					'right' => esc_html__('Right', 'bdthemes-element-pack'),
					'top' => esc_html__('Top', 'bdthemes-element-pack'),
					'bottom' => esc_html__('Bottom', 'bdthemes-element-pack'),
				],
				'condition' => [
					'offcanvas_button_icon[value]!' => '',
				],
				'selectors_dictionary' => [
					'left' => 'align-items: center;',
					'right' => 'align-items: center;',
					'top' => 'flex-direction: column; align-items: center;',
					'bottom' => 'flex-direction: column-reverse; align-items: center;',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button-content-wrapper' => '{{VALUE}};',
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'button_icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'offcanvas_button_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button .bdt-flex-align-right'  => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-offcanvas-button .bdt-flex-align-left'   => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-offcanvas-button .bdt-flex-align-top'    => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-offcanvas-button .bdt-flex-align-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_offcanvas_content',
			[
				'label' => esc_html__('Offcanvas', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'offcanvas_content_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_color',
			[
				'label'     => esc_html__('Link Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar a'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar a *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_hover_color',
			[
				'label'     => esc_html__('Link Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					// '{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'offcanvas_content_shadow',
				'selector'  => '{{WRAPPER}} .bdt-offcanvas > div',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'offcanvas_content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_offcanvas_widget',
			[
				'label'     => esc_html__('Widget', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'source' => 'sidebar',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'offcanvas_widget_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .widget',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'widget_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'offcanvas_widget_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'offcanvas_vertical_spacing',
			[
				'label'     => esc_html__('Vertical Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .widget:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_offcanvas_button',
			[
				'label' => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'layout' => 'default',
				],
			]
		);

		$this->start_controls_tabs('tabs_offcanvas_button_style');

		$this->start_controls_tab(
			'tab_offcanvas_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'offcanvas_button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-offcanvas-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'offcanvas_button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-offcanvas-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'offcanvas_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'offcanvas_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'offcanvas_button_typography',
				'selector'  => '{{WRAPPER}} .bdt-offcanvas-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'offcanvas_button_shadow',
				'selector'  => '{{WRAPPER}} .bdt-offcanvas-button',
			]
		);

		$this->add_responsive_control(
			'offcanvas_button_icon_size',
			[
				'label'     => esc_html__('Icon Size', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_offcanvas_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'offcanvas_button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-offcanvas-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'offcanvas_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__('Button Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close_button',
			[
				'label'     => esc_html__('Close Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'offcanvas_close_button' => 'yes'
				]
			]
		);

		$this->start_controls_tabs('tabs_close_button_style');

		$this->start_controls_tab(
			'tab_close_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close *' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'close_button_bg',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'close_button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'close_button_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'close_button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'  => '{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'close_button_shadow',
				'selector'  => '{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close:hover *' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'close_button_hover_bg',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'close_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-close:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = ('custom' == $settings['layout'] and !empty($settings['offcanvas_custom_id'])) ? $settings['offcanvas_custom_id'] : 'bdt-offcanvas-' . $this->get_id();

		$this->add_render_attribute('offcanvas', 'class', 'bdt-offcanvas');
		$this->add_render_attribute('offcanvas', 'id', $id);
		$this->add_render_attribute(
			[
				'offcanvas' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'id'      =>  $id,
							'layout'  => $settings['layout'],
						]))
					]
				]
			]
		);

		$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'mode: ' . $settings['offcanvas_animations'] . ';');

		if ($settings['offcanvas_overlay']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'overlay: true;');
		}

		if ('right' == $settings['offcanvas_flip']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'flip: true;');
		}

		if ('yes' !== $settings['offcanvas_bg_close']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'bg-close: false;');
		}

		if ('yes' !== $settings['offcanvas_esc_close']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'esc-close: false;');
		}



?>


		<?php $this->render_button(); ?>


		<div <?php echo $this->get_render_attribute_string('offcanvas'); ?>>
			<div class="bdt-offcanvas-bar">

				<?php if ($settings['offcanvas_close_button']) : ?>
					<button class="bdt-offcanvas-close" type="button" data-bdt-close>
					<?php if (!empty($settings['offcanvas_close_button_text'])) : ?>
						<span><?php echo wp_kses($settings['offcanvas_close_button_text'], element_pack_allow_tags('title')); ?></span>
					<?php endif; ?>
					</button>
				<?php endif; ?>


				<?php if ($settings['custom_content_before_switcher'] or $settings['custom_content_after_switcher'] or !empty($settings['source'])) : ?>
					<?php if ($settings['custom_content_before_switcher'] === 'yes' and !empty($settings['custom_content_before'])) : ?>
						<div class="bdt-offcanvas-custom-content-before widget">
							<?php echo wp_kses_post($settings['custom_content_before']); ?>
						</div>
					<?php endif; ?>

					<?php
					if ('sidebar' == $settings['source'] and !empty($settings['sidebars'])) {
						dynamic_sidebar($settings['sidebars']);
					} elseif ('elementor' == $settings['source'] and !empty($settings['template_id'])) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['template_id']);
						echo element_pack_template_edit_link($settings['template_id']);
					} elseif ('anywhere' == $settings['source'] and !empty($settings['anywhere_id'])) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['anywhere_id']);
						echo element_pack_template_edit_link($settings['anywhere_id']);
					}
					?>

					<?php if ($settings['custom_content_after_switcher'] === 'yes' and !empty($settings['custom_content_after'])) : ?>
						<div class="bdt-offcanvas-custom-content-after widget">
							<?php echo wp_kses_post($settings['custom_content_after']); ?>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<div class="bdt-offcanvas-custom-content-after widget">
						<div class="bdt-alert-warning" bdt-alert><?php esc_html_e('Ops you don\'t select or enter any content! Add your offcanvas content from editor.', 'bdthemes-element-pack'); ?></div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	<?php
	}

	protected function render_button() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-offcanvas-' . $this->get_id();

		if ('default' !== $settings['layout']) {
			return;
		}

		$this->add_render_attribute('button', 'class', ['bdt-offcanvas-button', 'elementor-button']);

		if (!empty($settings['size'])) {
			$this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
		}

		if ($settings['hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
		}

		$this->add_render_attribute('button', 'data-bdt-toggle', 'target: #' . esc_attr($id));
		$this->add_render_attribute('button', 'href', '#');

		$this->add_render_attribute('content-wrapper', 'class', 'elementor-button-content-wrapper');
		// $this->add_render_attribute('icon-align', 'class', 'bdt-offcanvas-button-icon');
		$this->add_render_attribute('icon-align', 'class', 'bdt-offcanvas-button-icon bdt-flex bdt-flex-align-' . $settings['button_icon_align']);

		$this->add_render_attribute('text', 'class', 'elementor-button-text');

		if (!isset($settings['button_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['button_icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset($settings['__fa4_migrated']['offcanvas_button_icon']);
		$is_new    = empty($settings['button_icon']) && Icons_Manager::is_migration_allowed();

	?>

		<div class="bdt-offcanvas-button-wrapper">
			<a <?php echo $this->get_render_attribute_string('button'); ?>>

				<span <?php echo $this->get_render_attribute_string('content-wrapper'); ?>>
					<?php if (!empty($settings['offcanvas_button_icon']['value'])) : ?>
						<span <?php echo $this->get_render_attribute_string('icon-align'); ?>>

							<?php if ($is_new || $migrated) :
								Icons_Manager::render_icon($settings['offcanvas_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
							else : ?>
								<i class="<?php echo esc_attr($settings['button_icon']); ?>" aria-hidden="true"></i>
							<?php endif; ?>

						</span>
					<?php endif; ?>
					<?php if (!empty($settings['button_text'])) : ?>
						<span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo wp_kses($settings['button_text'], element_pack_allow_tags('title')); ?></span>
					<?php endif; ?>
				</span>

			</a>
		</div>
<?php
	}
}
