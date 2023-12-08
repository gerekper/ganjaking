<?php

namespace ElementPack\Modules\AdvancedCalculator\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Advanced_Calculator extends Module_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-advanced-calculator';
	}

	public function get_title() {
		return BDTEP . esc_html__('Advanced Calculator', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-advanced-calculator';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['custom', 'advanced', 'calculator', 'math', 'formula'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-advanced-calculator'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['jstat', 'formula', 'ep-scripts'];
		} else {
			return ['jstat', 'formula', 'ep-advanced-calculator'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/vw28HW6duXE';
	}

	protected function register_controls() {


		$this->start_controls_section(
			'section_form_fields',
			[
				'label' => esc_html__('Form Fields', 'bdthemes-element-pack'),
			]
		);

		$repeater = new Repeater();

		$field_types = [
			'text'     => esc_html__('Text', 'bdthemes-element-pack'),
			'number'   => esc_html__('Number', 'bdthemes-element-pack'),
			'hidden'   => esc_html__('Hidden', 'bdthemes-element-pack'),
			'disabled' => esc_html__('Disabled', 'bdthemes-element-pack'),
			'select'   => esc_html__('Select', 'bdthemes-element-pack'),
			'radio' => esc_html__('Radio', 'bdthemes-element-pack'),
			// 'checkbox' => esc_html__('Checkbox', 'bdthemes-element-pack'),
		];

		$repeater->start_controls_tabs('form_fields_tabs');

		$repeater->start_controls_tab(
			'form_fields_content_tab',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'field_type',
			[
				'label'   => esc_html__('Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => $field_types,
				'default' => 'number',
			]
		);

		$repeater->add_control(
			'field_label',
			[
				'label'   => esc_html__('Label', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label'      => esc_html__('Placeholder', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::TEXT,
				'default'    => '',
				'dynamic'     => ['active' => true],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => [
								'text',
								'number',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_options',
			[
				'label'       => esc_html__('Options', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'description' => esc_html__('Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name', 'bdthemes-element-pack'),
				'dynamic'     => ['active' => true],
				'conditions'  => [
					'terms' => [
						[
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => [
								'select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'inline_list',
			[
				'label'      => esc_html__('Inline List', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => [
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'width',
			[
				'label'      => esc_html__('Column Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					''    => esc_html__('Default', 'bdthemes-element-pack'),
					'100' => '100%',
					'80'  => '80%',
					'75'  => '75%',
					'70'  => '70%',
					'66'  => '66%',
					'60'  => '60%',
					'50'  => '50%',
					'40'  => '40%',
					'33'  => '33%',
					'30'  => '30%',
					'25'  => '25%',
					'20'  => '20%',
				],
				'default'    => '100',
				'conditions' => [
					'terms' => [
						[
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => [
								'hidden',
							],
						],
					],
				],
				'selectors'  => [
					'{{WRAPPER}}  .bdt-field-group{{CURRENT_ITEM}}' => 'width: {{VALUE}}%',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'form_fields_advanced_tab',
			[
				'label' => esc_html__('Advanced', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'field_value',
			[
				'label'      => esc_html__('Default Value', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::TEXT,
				'default'    => '',
				'dynamic'    => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => [
								'text',
								'number',
								'hidden',
								'disabled',
							],
						],
					],
				],
			]
		);

		// $repeater->add_control(
		// 	'custom_id',
		// 	[
		// 		'label' => esc_html__('ID', 'bdthemes-element-pack'),
		// 		'type' => Controls_Manager::TEXT,
		// 		'description' => esc_html__('Please make sure the ID is unique and not used elsewhere in this form. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
		// 		'render_type' => 'none',
		// 		'required' => true,
		// 	]
		// );

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();
		$this->add_control(
			'form_fields',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						// 'custom_id' => 'value1',
						'field_type'  => 'number',
						'field_label' => esc_html__('First Value', 'bdthemes-element-pack'),
						'placeholder' => esc_html__('Enter your value', 'bdthemes-element-pack'),
						'width'       => '100',
					],
					[
						// 'custom_id' => 'value2',
						'field_type'  => 'number',
						'field_label' => esc_html__('Second Value', 'bdthemes-element-pack'),
						'placeholder' => esc_html__('Enter your value', 'bdthemes-element-pack'),
						'width'       => '100',
					],
				],
				'title_field' => '{{{ field_label }}}',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_forms_layout',
			[
				'label' => esc_html__('Form Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label'   => esc_html__('Label', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'input_size',
			[
				'label'   => esc_html__('Input Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Default', 'bdthemes-element-pack'),
					'small'   => esc_html__('Small', 'bdthemes-element-pack'),
					'large'   => esc_html__('Large', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'     => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center'   => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}.bdt-all-field-inline--yes .bdt-ep-advanced-calculator-form' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'all_field_inline' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'     => esc_html__('Text Align', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-form, {{WRAPPER}} .bdt-ep-advanced-calculator-form input, {{WRAPPER}} .bdt-ep-advanced-calculator-form textarea' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_submit_button',
			[
				'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__('Submit', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'   => esc_html__('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''           => esc_html__('Default', 'bdthemes-element-pack'),
					'small'      => esc_html__('Small', 'bdthemes-element-pack'),
					'large'      => esc_html__('Large', 'bdthemes-element-pack'),
					'full-width' => esc_html__('Full Width', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => '',
				'options'      => [
					'start'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'end'     => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'stretch' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-button-align-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_formula',
			[
				'label' => esc_html__('Formula', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'form_formula',
			[
				'label'       => esc_html__('Formula', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CODE,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('SUM(f1 + f2)', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'form_formula_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__(
					'It\'s one of the most amazing features of this widget. There are lots of math formulas available for you. The fields will automatically detect their own indexing. If you insert 2 fields and want to add them then the formula would be <b>SUM(f1+f2)</b>.
				<br/>If you want to know details about formulas, please visit <a href="https://formulajs.info/functions/" target="_blank">Here</a>.',
					'bdthemes-element-pack'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_result',
			[
				'label' => esc_html__('Result', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'form_result_show',
			[
				'label'   => esc_html__('Result Show', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'submit',
				'options' => [
					'submit'    => esc_html__('On Submit', 'bdthemes-element-pack'),
					'change' => esc_html__('On Change', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'form_result_position',
			[
				'label'   => esc_html__('Result Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'top'    => esc_html__('Top', 'bdthemes-element-pack'),
					'bottom' => esc_html__('Bottom', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_responsive_control(
			'result_alignment',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left'    => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justify', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'     => is_rtl() ? 'right' : 'left',
				'toggle'      => false,
				'label_block' => false,
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-result' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_result_text',
			[
				'label'       => esc_html__('Result Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'description' => esc_html__('HTML also allowed.', 'bdthemes-element-pack'),
				'default'     => esc_html__('Result is: ', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'form_result_error',
			[
				'label'       => esc_html__('Error Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'description' => esc_html__('This message will appear when user will do something wrong.', 'bdthemes-element-pack'),
				'default'     => esc_html__('Error, invalid data format. please fix the data format and send again. thanks!', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Form Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Field Space', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '15',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group:not(:last-child)'               => 'margin-bottom: {{SIZE}}{{UNIT}};margin-top: 0;',
					'{{WRAPPER}} .bdt-name-email-inline + .bdt-name-email-inline' => 'padding-left: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'col_gap',
			[
				'label'     => esc_html__('Column Space', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '12',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-field-wrap' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .bdt-ep-advanced-calculator-form' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 );margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			[
				'label'     => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_labels!' => '',
				],
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group > label' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-form-label' => 'color: {{VALUE}};',
				],
				// 'scheme' => [
				// 	'type'  => Schemes\Color::get_type(),
				// 	'value' => Schemes\Color::COLOR_3,
				// ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-form-label',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			[
				'label' => esc_html__('Fields', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_field_style');

		$this->start_controls_tab(
			'tab_field_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select'  => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'field_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'field_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select',
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group .bdt-select',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_field_focus',
			[
				'label' => esc_html__('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_focus_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input:focus, {{WRAPPER}} .bdt-field-group textarea:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_focus_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input:focus, {{WRAPPER}} .bdt-field-group textarea:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'field_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_result_show' => 'submit'
				]
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
					'{{WRAPPER}} .bdt-ep-advanced-calculator-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-calculator-button'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-calculator-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-calculator-button',
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
			'button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-calculator-button:hover'
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_result_style',
			[
				'label' => esc_html__('Result', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'result_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-result' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'result_background_color',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-calculator-result'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'result_border',
				'label'    => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-calculator-result',
			]
		);

		$this->add_control(
			'result_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-result' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'result_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-calculator-result',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'result_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-calculator-result',
			]
		);

		$this->add_responsive_control(
			'result_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-result' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_error_style',
			[
				'label' => esc_html__('Error', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'error_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-error div' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'error_background_color',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-calculator-error div'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'error_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-calculator-error div',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'error_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-calculator-error div',
			]
		);

		$this->add_responsive_control(
			'error_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-calculator-error div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->end_controls_section();
	}

	public function form_fields_render_attributes() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$this->add_render_attribute(
			[
				'wrapper'         => [
					'class' => [
						'elementor-form-fields-wrapper',
					],
				],
				'field-group'     => [
					'class' => [
						'bdt-field-group',
						'bdt-width-1-1',
					],
				],
				'user_name_label' => [
					'for'   => 'user_name' . $id,
					'class' => [
						'bdt-form-label',
					]
				],
				'user_name_input' => [
					'type'  => 'text',
					'name'  => 'name',
					'id'    => 'user_name' . $id,
					'class' => [
						'bdt-input',
						'bdt-form-',
					],
				],

			]
		);
	}

	public function get_attribute_name($item) {
		return "form_fields[{$item['custom_id']}]";
	}

	public function get_attribute_id($item) {
		return $item['custom_id'];
	}

	protected function make_select_field($item, $item_index) {
		$this->add_render_attribute(
			[
				'select-wrapper' . $item_index => [
					'class' => [
						// 'elementor-field',
						// 'elementor-select-wrapper',
						// esc_attr( $item['css_classes'] ),
					],
				],
				'select' . $item_index         => [
					'name'  => $this->get_attribute_name($item) . (!empty($item['allow_multiple']) ? '[]' : ''),
					'id'    => $this->get_attribute_id($item),
					'class' => [
						'bdt-select',
						'bdt-form-' . $item['input_size'],
					],
				],
			]
		);

		$options = preg_split("/\\r\\n|\\r|\\n/", $item['field_options']);

		if (!$options) {
			return '';
		}

		ob_start();
?>
		<?php if ($this->get_settings_for_display('show_labels')) : ?>
			<label for="<?php echo $this->get_attribute_id($item) ?>" class="bdt-form-label bdt-display-block bdt-margin-small-bottom">
				<?php echo esc_html($item['field_label']); ?>
			</label>
		<?php endif; ?>
		<div <?php echo $this->get_render_attribute_string('select-wrapper' . $item_index); ?>>
			<select <?php echo $this->get_render_attribute_string('select' . $item_index); ?>>
				<?php
				$i = 1;
				foreach ($options as $key => $option) {
					$item['custom_id'] = $i++;
					$option_id         = $item['custom_id'] . $key . $item_index;
					$option_value      = esc_attr($option);
					$option_label      = esc_html($option);

					if (false !== strpos($option, '|')) {
						list($label, $value) = explode('|', $option);
						$option_value = esc_attr($value);
						$option_label = esc_html($label);
					}

					$this->add_render_attribute($option_id, 'value', $option_value);

					// Support multiple selected values
					// if (!empty($item['field_value']) && in_array($option_value, explode(',', $item['field_value']))) {
					// 	$this->add_render_attribute($option_id, 'selected', 'selected');
					// }
					echo '<option ' . $this->get_render_attribute_string($option_id) . '>' . $option_label . '</option>';
				}
				?>
			</select>
		</div>
	<?php

		$select = ob_get_clean();

		return $select;
	}


	protected function make_radio_checkbox_field($item, $item_index, $type) {
		$options = preg_split("/\\r\\n|\\r|\\n/", $item['field_options']);
		$html    = '';
		if ($this->get_settings_for_display('show_labels')) {
			$html .= '<label for="' . $this->get_attribute_id($item) . '" class="bdt-form-label bdt-display-block bdt-margin-small-bottom">
			' . $item['field_label'] . '
		</label>';
		}
		if ($options) {
			$html .= '<div class="elementor-field-subgroup bdt-radio-inline-' . $item['inline_list'] . '">';
			$id   = $this->get_attribute_id($item);
			foreach ($options as $key => $option) {
				$element_id        = $this->get_attribute_id($item) . $key;
				$html_id           = $this->get_attribute_id($item) . $key;
				$option_label      = $option;
				$option_value      = $option;
				if (false !== strpos($option, '|')) {
					list($option_label, $option_value) = explode('|', $option);
				}

				$this->add_render_attribute(
					$element_id,
					[
						'type'  => $type,
						'value' => $option_value,
						'class' => 'bdt-radio',
						'name'  => $id,
					]
				);

				if (!empty($item['field_value']) && $option_value === $item['field_value']) {
					$this->add_render_attribute($element_id, 'checked', 'checked');
				}

				$html .= '<label id="' . $html_id . '" class="elementor-field-option"><input ' . $this->get_render_attribute_string($element_id) . '> <span for="' . $html_id . '">' . $option_label . '</span></label>';
			}
			$html .= '</div>';
		}

		return $html;
	}

	private function render_result() {
		$settings = $this->get_settings_for_display();
	?>
		<div class="bdt-ep-advanced-calculator-field-wrap bdt-field-group bdt-width-1-1 bdt-first-column">
			<div class="bdt-margin-small-top bdt-ep-advanced-calculator-result">
				<?php echo wp_kses_post($settings['form_result_text']); ?> <span></span>
			</div>
		</div>
	<?php
	}

	public function render() {
		$settings   = $this->get_settings_for_display();
		$id         = $this->get_id();
		$element_id = 'bdt-ep-advanced-calculator-' . $id;
		$formula    = $settings['form_formula'];

		if (!empty($settings['button_size'])) {
			$this->add_render_attribute('button', 'class', 'bdt-button-' . $settings['button_size']);
		}

		if ($settings['button_hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}
		$this->add_render_attribute(
			[
				'button' => [
					'class' => [
						'elementor-button',
						'bdt-button',
						'bdt-button-primary',
						'bdt-ep-advanced-calculator-button',
					],
				],
			]
		);
		$this->add_render_attribute(
			[
				'calculator' => [
					'class'         => 'bdt-ep-advanced-calculator',
					'id'            => $element_id,
					'data-settings' => [
						wp_json_encode(
							array_filter(
								[
									'id'      => '#' . $element_id,
									"formula" => "formula:'" . $formula . "'",
									'resultShow' => $settings['form_result_show']
								]
							)
						),
					],
				],
			]
		);

	?>
		<div <?php echo $this->get_render_attribute_string('calculator'); ?>>
			<div class="bdt-ep-advanced-calculator-wrapper">
				<form class="bdt-ep-advanced-calculator-form bdt-flex bdt-flex-wrap">
					<?php
					if ('top' == $settings['form_result_position']) {
						$this->render_result();
					}
					?>
					<?php
					$i                      = 1;
					foreach ($settings['form_fields'] as $item_index => $item) :
						$item['custom_id'] = $id . '-' . $i++;
						$item['input_size'] = $settings['input_size'];
						$disabled_class     = $item['field_type'] == 'disabled' ? 'bdt-mouse-disabled' : '';
						$this->add_render_attribute(
							[
								'field_label' . $item_index => [
									'for'   => $item['custom_id'],
									'class' => [
										'bdt-form-label bdt-display-block bdt-margin-small-bottom',
									]
								],
								'field_input' . $item_index => [
									'type'        => $item['field_type'] != 'disabled' ? $item['field_type'] : 'text',
									'value'       => $item['field_value'],
									'id'          => $item['custom_id'],
									'placeholder' => ($item['placeholder']) ? $item['placeholder'] : '',
									'class'       => [
										'bdt-input',
										'bdt-form-' . $item['input_size'],
										$disabled_class
									],
								],
							],
							true
						);

					?>
						<div class="bdt-ep-advanced-calculator-field-wrap bdt-field-group bdt-width-1-1 bdt-first-column elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">

							<?php

							switch ($item['field_type']) {
								case 'text':
								case 'number':
									if ($settings['show_labels']) {
										echo '<label ' . $this->get_render_attribute_string('field_label' . $item_index) . '>' . $item['field_label'] . '</label>';
									}
									echo '<div class="bdt-form-controls">';
									echo '<input ' . $this->get_render_attribute_string('field_input' . $item_index) . '>';
									echo '</div>';
									break;

								case 'hidden':
									echo '<div class="bdt-form-controls">';
									echo '<input ' . $this->get_render_attribute_string('field_input' . $item_index) . '>';
									echo '</div>';
									break;

								case 'disabled':
									if ($settings['show_labels']) {
										echo '<label ' . $this->get_render_attribute_string('field_label' . $item_index) . '>' . $item['field_label'] . '</label>';
									}
									echo '<div class="bdt-form-controls">';
									echo '<input ' . $this->get_render_attribute_string('field_input' . $item_index) . ' disabled="disabled">';
									echo '</div>';
									break;

								case 'select':
									echo $this->make_select_field($item, $item_index);
									break;

								case 'radio':
								case 'checkbox':
									echo $this->make_radio_checkbox_field($item, $item_index, $item['field_type']);
									break;

								default:
									echo 'Something wrong!';
									break;
							}

							?>

						</div>
					<?php endforeach; ?>

					<?php if ($settings['form_result_show'] == 'submit') : ?>

						<div class="bdt-ep-advanced-calculator-field-wrap bdt-field-group bdt-width-1-1 bdt-first-column">
							<div class="elementor-field-type-submit bdt-margin-small-top bdt-flex">
								<button <?php echo $this->get_render_attribute_string('button'); ?> type="submit">
									<?php
									echo esc_html($settings['button_text']);
									?>
								</button>
							</div>
						</div>

					<?php endif; ?>


					<div class="bdt-ep-advanced-calculator-field-wrap bdt-field-group bdt-width-1-1 bdt-first-column  bdt-ep-advanced-calculator-error bdt-hidden">
						<div class="bdt-alert-warning" bdt-alert>
							<a class="bdt-alert-close" bdt-close></a>
							<p class="bdt-margin-remove">
								<?php echo esc_html($settings['form_result_error']); ?>
							</p>
						</div>
					</div>

					<?php
					if ('bottom' == $settings['form_result_position']) {
						$this->render_result();
					}
					?>

				</form>
			</div>
		</div>
<?php
	}
}
