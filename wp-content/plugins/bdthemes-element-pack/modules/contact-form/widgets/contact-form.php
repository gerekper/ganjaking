<?php

namespace ElementPack\Modules\ContactForm\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Contact_Form extends Module_Base {

	protected $_has_template_content = false;

	public function get_name()
	{
		return 'bdt-contact-form';
	}

	public function get_title()
	{
		return BDTEP . esc_html__('Simple Contact Form', 'bdthemes-element-pack');
	}

	public function get_icon()
	{
		return 'bdt-wi-contact-form';
	}

	public function get_categories()
	{
		return ['element-pack'];
	}

	public function get_keywords()
	{
		return ['simple', 'contact', 'form', 'email'];
	}

	public function get_style_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-contact-form'];
		}
	}

	public function get_script_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			$ep_api_settings = get_option('element_pack_api_settings');
			if (!empty($ep_api_settings['recaptcha_site_key']) and !empty($ep_api_settings['recaptcha_secret_key'])) {
				return ['recaptcha', 'ep-contact-form'];
			} else {
				return ['ep-contact-form'];
			}
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/faIeyW7LOJ8';
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_forms_layout',
			[
				'label' => esc_html__('Forms Layout', 'bdthemes-element-pack'),
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
			'contact_number',
			[
				'label' => esc_html__('Contact Number Field', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_subject',
			[
				'label'   => esc_html__('Subject Field', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_message',
			[
				'label'   => esc_html__('Message Field', 'bdthemes-element-pack') . BDTEP_NC,
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

		$this->add_control(
			'two_columns',
			[
				'label' => esc_html__('Two Columns', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'all_field_inline' => ''
				]
			]
		);

		$this->add_control(
			'name_email_field_inline',
			[
				'label' => esc_html__('Name/Email Field Inline', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'all_field_inline' => '',
					'two_columns' => ''
				]
			]
		);

		$this->add_control(
			'all_field_inline',
			[
				'label' => esc_html__('All Field Inline', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-all-field-inline--',
				'condition' => [
					'two_columns' => ''
				]
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.bdt-all-field-inline--yes .bdt-contact-form-form' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'all_field_inline' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'   => esc_html__('Text Align', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
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
				'selectors'  => [
					'{{WRAPPER}} .bdt-contact-form-form, {{WRAPPER}} .bdt-contact-form-form input, {{WRAPPER}} .bdt-contact-form-form textarea' => 'text-align: {{VALUE}};',
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
				'default' => esc_html__('Send Message', 'bdthemes-element-pack'),
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
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'start' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'end' => [
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
			'section_forms_additional_options',
			[
				'label' => esc_html__('Additional Options', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'custom_text',
			[
				'label'     => esc_html__('Custom Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'user_name_heading',
			[
				'label'     => esc_html__('Name Field', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'user_name_label',
			[
				'label'       => esc_html__('Label', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Name*', 'bdthemes-element-pack'),
				'condition'   => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);


		$this->add_control(
			'user_name_placeholder',
			[
				'label'     => esc_html__('Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Your Name', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'contact_heading',
			[
				'label'     => esc_html__('Contact Number Field', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'contact_label',
			[
				'label'     => esc_html__('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Contact Number', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'contact_placeholder',
			[
				'label'     => esc_html__('Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Your contact number', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'subject_heading',
			[
				'label'     => esc_html__('Subject Field', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'subject_label',
			[
				'label'     => esc_html__('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Subject*', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'subject_placeholder',
			[
				'label'     => esc_html__('Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Your message subject', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'email_address_heading',
			[
				'label'     => esc_html__('Email Field', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'email_address_label',
			[
				'label'     => esc_html__('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Email*', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'email_placeholder',
			[
				'label'     => esc_html__('Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('example@email.com', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'message_label_heading',
			[
				'label'     => esc_html__('Message Field', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'message_label',
			[
				'label'     => esc_html__('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Your Message*', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'message_placeholder',
			[
				'label'     => esc_html__('Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Your Message Here', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels' => 'yes',
					'custom_text' => 'yes',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'show_additional_message',
			[
				'label'     => esc_html__('Additional Bottom Message', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'additional_message',
			[
				'label'     => esc_html__('Message', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Note: You have to fill-up above all respective field, then click below button for send your message', 'bdthemes-element-pack'),
				'condition' => [
					'show_additional_message' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_recaptcha',
			[
				'label'       => esc_html__('reCAPTCHA Enable', 'bdthemes-element-pack') . BDTEP_NC,
				'description' => esc_html__('Make sure you set the invisible reCAPTCHA key in settings.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default' 	  => 'yes',
			]
		);

		$this->add_control(
			'hide_recaptcha_badge',
			[
				'label'   => esc_html__('Hide reCAPTCHA Bagde', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-hide-recaptcha-badge-',
				'condition' => [
					'show_recaptcha' => 'yes',
				],
			]
		);

		$this->add_control(
			'message_rows',
			[
				'label'   => esc_html__('Message Rows', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => [
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
				],
			]
		);

		$this->add_control(
			'redirect_after_submit',
			[
				'label' => esc_html__('Redirect After Submit', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'type'          => Controls_Manager::URL,
				'show_label'    => false,
				'show_external' => false,
				'separator'     => false,
				'placeholder'   => 'http://your-link.com/',
				'description'   => esc_html__('Note: Because of security reasons, you can ONLY use your current domain here.', 'bdthemes-element-pack'),
				'condition'     => [
					'redirect_after_submit' => 'yes',
				],
				'dynamic'       => ['active' => true],
			]
		);

		$this->add_control(
			'reset_after_submit',
			[
				'label' => esc_html__('Reset After Submit', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
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
				'label'   => esc_html__('Field Space', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '15',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-top: 0;',
					'{{WRAPPER}} .bdt-name-email-inline + .bdt-name-email-inline' => 'padding-left: {{SIZE}}px',
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
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group > label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-field-group .bdt-input' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-field-group textarea'   => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-field-group textarea::placeholder'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-field-group textarea'   => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group textarea',
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
					'{{WRAPPER}} .bdt-field-group .bdt-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-field-group textarea'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group textarea',
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-field-group .bdt-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .bdt-field-group textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-field-group .bdt-input, {{WRAPPER}} .bdt-field-group textarea',
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
					'{{WRAPPER}} .bdt-field-group .bdt-input:focus' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-field-group textarea:focus'   => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_focus_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-field-group .bdt-input:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-field-group textarea:focus'   => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-button',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-button'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-button',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .bdt-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-button:hover'
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-button:hover' => 'border-color: {{VALUE}};',
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
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_style',
			[
				'label'     => esc_html__('Additional Message', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_additional_message!' => '',
				],
			]
		);

		$this->add_control(
			'additional_text_color',
			[
				'name'      => 'additional_text_color',
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-contact-form-additional-message' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'additional_text_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-contact-form-additional-message',
			]
		);

		$this->end_controls_section();
	}

	public function form_fields_render_attributes()
	{
		$settings        = $this->get_settings_for_display();
		$id              = $this->get_id();
		$ep_api_settings = get_option('element_pack_api_settings');

		if (!empty($settings['button_size'])) {
			$this->add_render_attribute('button', 'class', 'bdt-button-' . $settings['button_size']);
		}

		if ($settings['button_hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		$this->add_render_attribute(
			[
				'wrapper' => [
					'class' => [
						'elementor-form-fields-wrapper',
					],
				],
				'field-group' => [
					'class' => [
						'bdt-field-group',
						'bdt-width-1-1',
					],
				],
				'submit-group' => [
					'class' => [
						'elementor-field-type-submit',
						'bdt-field-group',
						'bdt-flex',
						'bdt-width-1-1',
					],
				],

				'button' => [
					'class' => [
						'elementor-button',
						'bdt-button',
						'bdt-button-primary',
					],
					'name' => 'submit',
				],
				'user_name_label' => [
					'for'   => 'user_name' . $id,
					'class' => [
						'bdt-form-label',
					]
				],
				'contact_label' => [
					'for'   => 'contact' . $id,
					'class' => [
						'bdt-form-label',
					]
				],
				'subject_label' => [
					'for'   => 'subject' . $id,
					'class' => [
						'bdt-form-label',
					]
				],
				'email_address_label' => [
					'for'   => 'email' . $id,
					'class' => [
						'bdt-form-label',
					]
				],
				'message_label' => [
					'for'   => 'message' . $id,
					'class' => [
						'bdt-form-label',
					]
				],
				'user_name_input' => [
					'type'        => 'text',
					'name'        => 'name',
					'id'          => 'user_name' . $id,
					'placeholder' => ($settings['user_name_placeholder']) ? $settings['user_name_placeholder'] : esc_html__('Your Name', 'bdthemes-element-pack'),
					'class'       => [
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'contact_input' => [
					'type'        => 'tel',
					'name'        => 'contact',
					'id'          => 'contact' . $id,
					'placeholder' => ($settings['contact_placeholder']) ? $settings['contact_placeholder'] : esc_html__('Your Contact Number', 'bdthemes-element-pack'),
					'class'       => [
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'subject_input' => [
					'type'        => 'text',
					'name'        => 'subject',
					'id'          => 'subject' . $id,
					'placeholder' => ($settings['subject_placeholder']) ? $settings['subject_placeholder'] : esc_html__('Your Message Subject', 'bdthemes-element-pack'),
					'class'       => [
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'email_address_input' => [
					'type'        => 'email',
					'name'        => 'email',
					'id'          => 'email' . $id,
					'placeholder' => ($settings['email_placeholder']) ? $settings['email_placeholder'] : esc_html__('example@email.com', 'bdthemes-element-pack'),
					'class'       => [
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'message_area' => [
					//'type'        => 'textarea',
					'name'        => 'message',
					'id'          => 'message' . $id,
					'rows'		  => $settings['message_rows'],
					'placeholder' => ($settings['message_placeholder']) ? $settings['message_placeholder'] : esc_html__('Your Message Here', 'bdthemes-element-pack'),
					'class'       => [
						'bdt-textarea',
						'bdt-form-' . $settings['input_size'],
					],
				],
			]
		);

		if (isset($settings['show_recaptcha']) && $settings['show_recaptcha'] == 'yes') {
			if (!empty($ep_api_settings['recaptcha_site_key']) and !empty($ep_api_settings['recaptcha_secret_key'])) {
				$this->add_render_attribute('button', 'data-sitekey', $ep_api_settings['recaptcha_site_key']);
				$this->add_render_attribute('button', 'data-callback', 'elementPackGICCB');
				$this->add_render_attribute('button', 'class', 'g-recaptcha');
			}
		}

		if (!$settings['show_labels']) {
			$this->add_render_attribute('label', 'class', 'elementor-screen-only');
		}

		$this->add_render_attribute('field-group', 'class', 'elementor-field-required')
			->add_render_attribute('input', 'required', 'required')
			->add_render_attribute('input', 'aria-required', 'true');
	}



	public function render()
	{
		$this->form_fields_render_attributes();

?>
		<div class="bdt-contact-form bdt-contact-form-skin-default">
			<div class="elementor-form-fields-wrapper">
				<?php $this->contact_form_html(); ?>
			</div>
		</div>
	<?php
	}

	public function contact_form_html()
	{
		$settings = $this->get_settings_for_display();
		$ep_api_settings = get_option('element_pack_api_settings');
		$id       = $this->get_id();
		$form_id  = !empty($settings['_element_id']) ? 'bdt-sf-' . $settings['_element_id'] :  'bdt-sf-' . $id;

		$this->add_render_attribute('contact-form', 'class', ['bdt-contact-form-form', 'bdt-form-stacked', 'bdt-grid', 'bdt-grid-small']);
		$this->add_render_attribute('contact-form', 'data-bdt-grid', '');
		$this->add_render_attribute('contact-form', 'action', site_url() . '/wp-admin/admin-ajax.php');
		$this->add_render_attribute('contact-form', 'method', 'post');


		if (isset($settings['show_recaptcha']) && $settings['show_recaptcha'] == 'yes') {
			if (empty($ep_api_settings['recaptcha_site_key']) and empty($ep_api_settings['recaptcha_secret_key'])) {
				$this->add_render_attribute('contact-form', 'class', 'without-recaptcha');
			}
		} else {
			$this->add_render_attribute('contact-form', 'class', 'without-recaptcha');
		}


		$this->add_render_attribute('name-email-field-group', 'class', ['bdt-field-group', 'elementor-field-required']);

		if ($settings['name_email_field_inline']) {
			$this->add_render_attribute('name-email-field-group', 'class', ['bdt-width-1-2@m', 'bdt-name-email-inline']);
		} else {
			$this->add_render_attribute('name-email-field-group', 'class', 'bdt-width-1-1');
		}

	?>
		<div class="bdt-contact-form-wrapper">
			<form <?php echo $this->get_render_attribute_string('contact-form'); ?>>

				<?php if ($settings['two_columns']) : ?>
					<div class="bdt-width-1-2">
					<?php endif; ?>

					<div <?php echo $this->get_render_attribute_string('name-email-field-group'); ?>>
						<?php
						if ($settings['show_labels']) {
							if ('yes' == $settings['custom_text']) {
								echo '<label ' . $this->get_render_attribute_string('user_name_label') . '>' . $settings['user_name_label'] . '</label>';
							} else {
								echo '<label ' . $this->get_render_attribute_string('user_name_label') . '>' . esc_html__('Name*', 'bdthemes-element-pack') . '</label>';
							}
						}


						echo '<div class="bdt-form-controls">';
						echo '<input ' . $this->get_render_attribute_string('user_name_input') . ' required ="required">';
						echo '</div>';

						?>
					</div>

					<div <?php echo $this->get_render_attribute_string('name-email-field-group'); ?>>
						<?php
						if ($settings['show_labels']) {
							if ('yes' == $settings['custom_text']) {
								echo '<label ' . $this->get_render_attribute_string('email_address_label') . '>' . $settings['email_address_label'] . '</label>';
							} else {
								echo '<label ' . $this->get_render_attribute_string('email_address_label') . '>' . esc_html__('Email*', 'bdthemes-element-pack') . '</label>';
							}
						}

						echo '<div class="bdt-form-controls">';
						echo '<input ' . $this->get_render_attribute_string('email_address_input') . ' required="required">';
						echo '</div>';
						?>
					</div>

					<?php if ($settings['contact_number']) : ?>
						<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
							<?php

							if ($settings['show_labels']) {
								if ('yes' == $settings['custom_text']) {
									echo '<label ' . $this->get_render_attribute_string('contact_label') . '>' . $settings['contact_label'] . '</label>';
								} else {
									echo '<label ' . $this->get_render_attribute_string('contact_label') . '>' . esc_html__('Contact Number*', 'bdthemes-element-pack') . '</label>';
								}
							}
							echo '<div class="bdt-form-controls">';
							echo '<input ' . $this->get_render_attribute_string('contact_input') . '>';
							echo '</div>';

							?>
						</div>
					<?php endif; ?>

					<?php if ($settings['show_subject']) : ?>
						<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
							<?php
							if ($settings['show_labels']) {
								if ('yes' == $settings['custom_text']) {
									echo '<label ' . $this->get_render_attribute_string('subject_label') . '>' . $settings['subject_label'] . '</label>';
								} else {
									echo '<label ' . $this->get_render_attribute_string('subject_label') . '>' . esc_html__('Subject*', 'bdthemes-element-pack') . '</label>';
								}
							}
							echo '<div class="bdt-form-controls">';
							echo '<input ' . $this->get_render_attribute_string('subject_input') . ' required="required">';
							echo '</div>';

							?>
						</div>
					<?php endif; ?>

					<?php if ($settings['two_columns']) : ?>
					</div>
					<div class="bdt-width-1-2">
					<?php endif; ?>

					<?php if ($settings['show_message']) : ?>
						<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
							<?php
							if ($settings['show_labels']) {
								if ('yes' == $settings['custom_text']) {
									echo '<label ' . $this->get_render_attribute_string('message_label') . '>' . $settings['message_label'] . '</label>';
								} else {
									echo '<label ' . $this->get_render_attribute_string('message_label') . '>' . esc_html__('Your Message*', 'bdthemes-element-pack') . '</label>';
								}
							}
							echo '<div class="bdt-form-controls">';
							echo '<textarea ' . $this->get_render_attribute_string('message_area') . ' required="required"></textarea>';
							echo '</div>';
							?>
						</div>
					<?php endif; ?>

					<?php if ('yes' === $settings['show_additional_message']) : ?>
						<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
							<span class="bdt-contact-form-additional-message"><?php echo wp_kses($settings['additional_message'], element_pack_allow_tags('title')); ?></span>
						</div>
					<?php endif; ?>


					<?php if (($settings['redirect_after_submit'] == 'yes') && !empty($settings['redirect_url']['url'])) :
						$redirect_url = $settings['redirect_url']['url'];
						$redirect_extarnal = (isset($settings['redirect_url']['is_external']) && ($settings['redirect_url']['is_external']) == 'on') ? '_blank' : '_self';
					?>
						<input type="hidden" name="redirect-url" value="<?php
																		echo esc_url($redirect_url) ?>" />

						<input type="hidden" name="is-external" value="<?php
																		echo esc_html($redirect_extarnal) ?>" />

					<?php endif; ?>

					<?php if ($settings['reset_after_submit'] == 'yes') : ?>
						<input type="hidden" name="reset-after-submit" value="<?php echo $settings['reset_after_submit'] ?>" />
					<?php endif; ?>

					<input type="hidden" class="widget_id" name="widget_id" value="<?php
																					echo esc_attr($id); ?>" />
					<input type="hidden" name="<?php echo esc_attr($form_id); ?>" value="true" />
					<input type="hidden" class="page_id" name="page_id" value="<?php
																				echo get_the_ID() ?>" />

					<div <?php echo $this->get_render_attribute_string('submit-group'); ?>>
						<button type="submit" <?php echo $this->get_render_attribute_string('button'); ?>>
							<?php if (!empty($settings['button_text'])) : ?>
								<span><?php echo esc_html($settings['button_text']); ?></span>
							<?php endif; ?>
						</button>
					</div>

					<input name="_wpnonce" value="<?php echo wp_create_nonce("simpleContactForm"); ?>" type="hidden">

					<input type="hidden" name="action" value="element_pack_contact_form" />

					<?php if ($settings['two_columns']) : ?>
					</div>
				<?php endif; ?>

			</form>
		</div>
<?php
	}
}
