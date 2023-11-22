<?php

namespace ElementPack\Modules\UserRegister\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

use ElementPack\Modules\UserRegister\Skins;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class User_Register extends Module_Base {

	public function get_name() {
		return 'bdt-user-register';
	}

	public function get_title() {
		return BDTEP . esc_html__('User Register', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-user-register';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['user', 'register', 'form'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-user-register'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['recaptcha', 'ep-scripts'];
        } else {
			return ['recaptcha', 'ep-user-register'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/hTjZ1meIXSY';
	}

	protected function register_skins() {
		$this->add_skin(new Skins\Skin_Dropdown($this));
		$this->add_skin(new Skins\Skin_Modal($this));
	}

	protected function register_controls() {
		$this->register_layout_section_controls();
	}

	private function register_layout_section_controls() {
		$this->start_controls_section(
			'section_forms_layout',
			[
				'label' => esc_html__('Forms Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'labels_title',
			[
				'label'     => esc_html__('Labels', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
			'fields_title',
			[
				'label' => esc_html__('Fields', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'input_size',
			[
				'label'   => esc_html__('Input Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'small'   => esc_html__('Small', 'bdthemes-element-pack'),
					'default' => esc_html__('Default', 'bdthemes-element-pack'),
					'large'   => esc_html__('Large', 'bdthemes-element-pack'),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'button_title',
			[
				'label'     => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__('Register', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'   => esc_html__('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'small' => esc_html__('Small', 'bdthemes-element-pack'),
					''      => esc_html__('Default', 'bdthemes-element-pack'),
					'large' => esc_html__('Large', 'bdthemes-element-pack'),
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
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
				'default'      => '',
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
			'redirect_after_register',
			[
				'label' => esc_html__('Redirect After Register', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
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
					'redirect_after_register' => 'yes',
				],
			]
		);

		$this->add_control(
			'auto_login_after_register',
			[
				'label' => esc_html__('Auto Login After Register', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_lost_password',
			[
				'label'   => esc_html__('Lost your password?', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);


		$this->add_control(
			'show_login',
			[
				'label' => esc_html__('Login', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);


		$this->add_control(
			'show_logged_in_message',
			[
				'label'   => esc_html__('Logged in Message', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'is_needed_password_input',
			[
				'label' => esc_html__('Add password field', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'password_strength',
			[
				'label'   => esc_html__('Show Password Strength', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'force_strong_password',
			[
				'label'     => esc_html__('Must Strong Password', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'password_strength' => 'yes'
				]
			]
		);

		$this->add_control(
			'remove_first_name',
			[
				'label'     => esc_html__('Remove First Name', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'remove_last_name',
			[
				'label' => esc_html__('Remove Last Name', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_labels',
			[
				'label'     => esc_html__('Custom Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);


		$this->add_control(
			'first_name_label',
			[
				'label'     => esc_html__('First Name Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('First Name', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'        => 'yes',
					'custom_labels'      => 'yes',
					'remove_first_name!' => 'yes',
				],
			]
		);


		$this->add_control(
			'first_name_placeholder',
			[
				'label'     => esc_html__('First Name Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('John', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'        => 'yes',
					'custom_labels'      => 'yes',
					'remove_first_name!' => 'yes',
				],
			]
		);

		$this->add_control(
			'last_name_label',
			[
				'label'     => esc_html__('Last Name Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Last Name', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'       => 'yes',
					'custom_labels'     => 'yes',
					'remove_last_name!' => 'yes',
				],
			]
		);

		$this->add_control(
			'last_name_placeholder',
			[
				'label'     => esc_html__('Last Name Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Doe', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'       => 'yes',
					'custom_labels'     => 'yes',
					'remove_last_name!' => 'yes',
				],
			]
		);

		$this->add_control(
			'email_label',
			[
				'label'     => esc_html__('Email Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Email', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'   => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'email_placeholder',
			[
				'label'     => esc_html__('Email Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('example@email.com', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'   => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'password_label',
			[
				'label'     => esc_html__('Password Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Password', 'bdthemes-element-pack'),
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'show_labels',
							'value' => 'yes',
						],
						[
							'name'     => 'custom_labels',
							'value'    => 'yes',
						],
						[
							'name'     => 'is_needed_password_input',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'password_placeholder',
			[
				'label'     => esc_html__('Password Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Enter password', 'bdthemes-element-pack'),
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'show_labels',
							'value' => 'yes',
						],
						[
							'name'     => 'custom_labels',
							'value'    => 'yes',
						],
						[
							'name'     => 'is_needed_password_input',
							'value'    => 'yes',
						],
					],
				],
			]
		);


		$this->add_control(
			'show_additional_message',
			[
				'label' => esc_html__('Additional Bottom Message', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'additional_message',
			[
				'label'     => esc_html__('Additional Message', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Note: Your password will be generated automatically and sent to your email address.', 'bdthemes-element-pack'),
				'condition' => [
					'show_additional_message' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_recaptcha_checker',
			[
				'label'        => esc_html__('Show reCAPTCHA Checker', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-show-recaptcha-badge-',
				'separator'    => 'before',
			]
		);

		$this->end_controls_section();

		//Style Dropdown Button
		$this->start_controls_section(
			'section_style_dropdown_button',
			[
				'label' => esc_html__('Dropdown Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-dropdown'
				]
			]
		);

		$this->start_controls_tabs('tabs_dropdown_button_style');

		$this->start_controls_tab(
			'tab_dropdown_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dropdown_button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_button_typography',
				'selector' => '{{WRAPPER}} .bdt-button-dropdown',
			]
		);

		$this->add_control(
			'dropdown_button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dropdown_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-button-dropdown',
			]
		);

		$this->add_responsive_control(
			'dropdown_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dropdown_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dropdown_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_button_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'dropdown_button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'dropdown_button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Style Modal Button
		$this->start_controls_section(
			'section_style_modal_button',
			[
				'label' => esc_html__('Modal Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-modal'
				]
			]
		);

		$this->start_controls_tabs('tabs_modal_button_style');

		$this->start_controls_tab(
			'tab_modal_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'modal_button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_button_typography',
				'selector' => '{{WRAPPER}} .bdt-button-modal',
			]
		);

		$this->add_control(
			'modal_button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'modal_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-button-modal',
			]
		);

		$this->add_control(
			'modal_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-modal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'modal_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-modal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_modal_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'modal_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_button_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'modal_button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'modal_button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
				'label'     => esc_html__('Rows Gap', 'bdthemes-element-pack'),
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
					'#bdt-user-register{{ID}} .bdt-field-group:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'links_heading',
			[
				'label'     => esc_html__( 'L I N K S', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'links_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group > a' => 'color: {{VALUE}};',
					'#bdt-user-register{{ID}} .bdt-user-register-password a:not(:last-child):after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'links_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'links_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector'  => '#bdt-user-register{{ID}} .bdt-field-group > a',
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

		$this->add_responsive_control(
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
					'#bdt-user-register{{ID}} .bdt-field-group > label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-form-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '#bdt-user-register{{ID}} .bdt-form-label',
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
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input::placeholder'      => 'color: {{VALUE}};',
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input,
					#bdt-user-register{{ID}} .bdt-field-group .bdt-checkbox' => 'background-color: {{VALUE}};',
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
				'selector'    => '#bdt-user-register{{ID}} .bdt-field-group .bdt-input',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'field_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '#bdt-user-register{{ID}} .bdt-field-group .bdt-input',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '#bdt-user-register{{ID}} .bdt-field-group .bdt-input',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_field_hover',
			[
				'label' => esc_html__('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_text_color_focus',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color_focus',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input:focus::placeholder'      => 'color: {{VALUE}};',
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input:focus::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color_focus',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input:focus,
					#bdt-user-register{{ID}} .bdt-field-group .bdt-checkbox:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'field_border_border!' => '',
				],
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-field-group .bdt-input:focus' => 'border-color: {{VALUE}};',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#bdt-user-register{{ID}} .bdt-button',
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
					'#bdt-user-register{{ID}} .bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'#bdt-user-register{{ID}} .bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-user-register{{ID}} .bdt-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '#bdt-user-register{{ID}} .bdt-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '#bdt-user-register{{ID}} .bdt-button',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '#bdt-user-register{{ID}} .bdt-button:hover',
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
			'section_additional_style',
			[
				'label'     => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_additional_message!' => '',
				],
			]
		);

		$this->add_control(
			'additional_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-register{{ID}} .bdt-register-additional-message' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'additional_text_typography',
				'label'     => esc_html__('Additional Message Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '#bdt-user-register{{ID}} .bdt-register-additional-message',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pass_progress_style',
			[
				'label'     => esc_html__('Password Progress Bar', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'password_strength' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
            'progress_bar_height',
            [
                'label'     => esc_html__('Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                        'step' => .5,
                    ],
                ],
                'selectors' => [
					'{{WRAPPER}} .bdt-progress' => 'height: {{SIZE}}px',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'progress_background',
				'label' => esc_html__('Background', 'elementor'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Progress Background', 'elementor'),
					],
				],
				'selector' => '{{WRAPPER}} .bdt-progress',
			]
		);

		$this->end_controls_section();
	}


	public function form_fields_render_attributes() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		if (!empty($settings['button_size'])) {
			$this->add_render_attribute('button', 'class', 'bdt-button-' . $settings['button_size']);
		}

		if ($settings['button_hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		$this->add_render_attribute(
			[
				'wrapper'      => [
					'class' => [
						'elementor-form-fields-wrapper',
					],
				],
				'field-group'  => [
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
					],
				],

				'button'              => [
					'class' => [
						'elementor-button',
						'bdt-button',
						'bdt-button-primary',
					],
					'name'  => 'submit',
				],
				'first_name_label'    => [
					'for'   => 'first_name' . esc_attr($id),
					'class' => [
						'bdt-form-label',
					]
				],
				'last_name_label'     => [
					'for'   => 'last_name' . esc_attr($id),
					'class' => [
						'bdt-form-label',
					]
				],
				'email_label'         => [
					'for'   => 'user_email' . esc_attr($id),
					'class' => [
						'bdt-form-label',
					]
				],
				'password_label' => [
					'for'   => 'password' . esc_attr($id),
					'class' => [
						'bdt-form-label',
					]
				],
				'first_name_input'    => [
					'type'        => 'text',
					'name'        => 'first_name',
					'id'          => 'first_name' . esc_attr($id),
					'placeholder' => ($settings['first_name_placeholder']) ? $settings['first_name_placeholder'] : esc_html__('Jhon', 'bdthemes-element-pack'),
					'class'       => [
						'first_name',
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'last_name_input'     => [
					'type'        => 'text',
					'name'        => 'last_name',
					'id'          => 'last_name' . esc_attr($id),
					'placeholder' => ($settings['last_name_placeholder']) ? $settings['last_name_placeholder'] : esc_html__('Doe', 'bdthemes-element-pack'),
					'class'       => [
						'last_name',
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'email_address_input' => [
					'type'        => 'email',
					'name'        => 'user_email',
					'id'          => 'user_email' . esc_attr($id),
					'placeholder' => ($settings['email_placeholder']) ? $settings['email_placeholder'] : esc_html__('example@email.com', 'bdthemes-element-pack'),
					'class'       => [
						'user_email',
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
			]
		);

		if (isset($settings['is_needed_password_input'])) {
			$this->add_render_attribute(
				[
					'password_input' => [
						'type'        => 'password',
						'name'        => 'user_password',
						'id'          => 'user_password' . esc_attr($id),
						'placeholder' => ($settings['password_placeholder']) ? $settings['password_placeholder'] : esc_html__('Enter password', 'bdthemes-element-pack'),
						'class'       => [
							'user_password',
							'bdt-input',
							'bdt-form-' . $settings['input_size'],
						],
					]
				]
			);
		}

		$this->add_render_attribute('field-group', 'class', 'elementor-field-required')
			->add_render_attribute('input', 'required', true)
			->add_render_attribute('input', 'aria-required', 'true');
	}

	public function render() {
		$settings    = $this->get_settings_for_display();
		$current_url = remove_query_arg('fake_arg');

		if (is_user_logged_in() && !Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			if ($settings['show_logged_in_message']) {
				$current_user = wp_get_current_user();

				echo '<div class="bdt-user-register">' .
					sprintf(__('You are Logged in as %1$s (<a href="%2$s">Logout</a>)', 'bdthemes-element-pack'), $current_user->display_name, wp_logout_url($current_url)) .
					'</div>';
			}

			return;
		} elseif (!get_option('users_can_register')) {
?>
			<div class="bdt-alert bdt-alert-warning" bdt-alert>
				<a class="bdt-alert-close"><i class="ep-icon-close" aria-hidden="true"></i></a>
				<p><?php esc_html_e('Registration option not enabled in your general settings.', 'bdthemes-element-pack'); ?></p>
			</div>
		<?php
			return;
		}

		$this->form_fields_render_attributes();

		$this->add_render_attribute(
			[
				'user_register' => [
					'class' => 'bdt-user-register bdt-user-register-skin-default',
				]
			]
		);

		if (isset($settings['password_strength']) && 'yes' == $settings['password_strength']) {
			$this->add_render_attribute(
				[
					'user_register' => [
						'data-settings' => [
							wp_json_encode(
								array_filter([
									"id"                  => 'bdt-user-register' . $this->get_id(),
									"passStrength"    => true,
									"forceStrongPass" => 'yes' == $settings['force_strong_password']  ? true : false,
								])
							),
						],
					],
				]
			);
		}

		?>
		<div <?php $this->print_render_attribute_string('user_register'); ?>>
			<div class="elementor-form-fields-wrapper">
				<?php $this->user_register_form(); ?>
			</div>
		</div>

	<?php
	}

	public function user_register_form() {
		$settings = $this->get_settings_for_display();

		$id          = $this->get_id();
		$current_url = remove_query_arg('fake_arg');

		if ($settings['redirect_after_register'] && !empty($settings['redirect_url']['url'])) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}

		$is_needed_password_input = '';
	?>
		<form id="bdt-user-register<?php echo esc_attr($id); ?>" class="bdt-form-stacked bdt-width-1-1 bdt-user-register-widget" method="post">
			<?php
			if ($settings['show_recaptcha_checker']) {
				do_action('element_pack_google_rechatcha_render', $this, 'onLoadElementPackRegisterCaptcha', 'button');
			}
			?>
			<input type="hidden" class="page_id" name="page_id" value="<?php echo get_the_ID() ?>" />

			<?php if ('yes' !== $settings['remove_first_name']) : ?>
				<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
					<?php
					if ($settings['show_labels']) {
						// echo '<label ' . $this->get_render_attribute_string( 'first_name_label' ) . '>' . $settings['first_name_label'] . '</label>';
					?>
						<label <?php echo $this->get_render_attribute_string('first_name_label'); ?>>
							<?php if ('yes' == $settings['custom_labels']) {
								echo $settings['first_name_label'];
							} else {
								echo esc_html__('First Name', 'bdthemes-element-pack');
							} ?>
						</label>
					<?php
					}
					echo '<div class="bdt-form-controls">';
					echo '<input ' . $this->get_render_attribute_string('first_name_input') . ' required>';
					echo '</div>';

					?>
				</div>
			<?php endif; ?>

			<?php if ('yes' !== $settings['remove_last_name']) : ?>
				<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
					<?php
					if ($settings['show_labels']) {
						// echo '<label ' . $this->get_render_attribute_string( 'last_name_label' ) . '>' . $settings['last_name_label'] . '</label>';
					?>
						<label <?php echo $this->get_render_attribute_string('last_name_label'); ?>>
							<?php if ('yes' == $settings['custom_labels']) {
								echo $settings['last_name_label'];
							} else {
								echo esc_html__('Last Name', 'bdthemes-element-pack');
							} ?>
						</label>
					<?php
					}
					echo '<div class="bdt-form-controls">';
					echo '<input ' . $this->get_render_attribute_string('last_name_input') . ' required>';
					echo '</div>';

					?>
				</div>
			<?php endif; ?>

			<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
				<?php
				if ($settings['show_labels']) :
					// echo '<label ' . $this->get_render_attribute_string( 'email_label' ) . '>' . $settings['email_label'] . '</label>';
				?>
					<label <?php echo $this->get_render_attribute_string('email_label'); ?>>
						<?php if ('yes' == $settings['custom_labels']) {
							echo $settings['email_label'];
						} else {
							echo esc_html__('Email', 'bdthemes-element-pack');
						} ?>
					</label>
				<?php
				endif;
				echo '<div class="bdt-form-controls">';
				echo '<input ' . $this->get_render_attribute_string('email_address_input') . ' required>';
				echo '</div>';
				?>
			</div>

			<?php if (isset($settings['is_needed_password_input'])) : ?>
				<?php $is_needed_password_input = 'yes'; ?>
				<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
					<?php
					if ($settings['show_labels']) :
						// echo '<label ' . $this->get_render_attribute_string( 'password_label' ) . '>' . $settings['password_label'] . '</label>';
					?>
						<label <?php echo $this->get_render_attribute_string('password_label'); ?>>
							<?php if ('yes' == $settings['custom_labels']) {
								echo $settings['password_label'];
							} else {
								echo esc_html__('Password', 'bdthemes-element-pack');
							} ?>
						</label>
					<?php
					endif;
					echo '<div class="bdt-form-controls">';
					echo '<input ' . $this->get_render_attribute_string('password_input') . ' required>';
					echo '</div>';

					if(isset($settings['password_strength']) && 'yes' == $settings['password_strength']){
						echo '<div class="bdt-progress bdt-width-1-1"> <div class="bdt-progress-bar" value="0" max="100" style="width:0;"></div> </div>';
					}

					?>
				</div>
			<?php endif; ?>

			<?php if ($settings['show_additional_message']) : ?>
				<div <?php echo $this->get_render_attribute_string('field-group'); ?>>
					<span class="bdt-register-additional-message"><?php echo wp_kses($settings['additional_message'], element_pack_allow_tags('text')); ?></span>
				</div>
			<?php endif; ?>
			<?php $redirect_after_register = ($settings['redirect_after_register'] && !empty($settings['redirect_url']['url'])) ? $settings['redirect_url']['url'] : ''; ?>
			<input type="hidden" name="is_password_required" class="is_password_required" value="<?php echo esc_attr($is_needed_password_input) ?>" />
			<input type="hidden" name="redirect_after_register" class="redirect_after_register" value="<?php echo esc_url($redirect_after_register) ?>" />
			<input type="hidden" name="bdt_spinner_message" class="bdt_spinner_message" value="<?php esc_html_e("We are registering you, please wait...", "bdthemes-element-pack"); ?>" />
			<div <?php echo $this->get_render_attribute_string('submit-group'); ?>>
				<button type="submit" <?php echo $this->get_render_attribute_string('button'); ?>>
					<?php if (!empty($settings['button_text'])) : ?>
						<span><?php echo wp_kses($settings['button_text'], element_pack_allow_tags('title')); ?></span>
					<?php endif; ?>
				</button>
			</div>

			<?php
			$show_lost_password = $settings['show_lost_password'];
			$show_login         = $settings['show_login'];

			if ($show_lost_password || $show_login) : ?>
				<div class="bdt-field-group bdt-width-1-1 bdt-margin-remove-bottom bdt-user-register-password">

					<?php if ($show_lost_password) : ?>
						<a class="bdt-lost-password" href="<?php echo wp_lostpassword_url($redirect_url); ?>">
							<?php esc_html_e('Lost your password?', 'bdthemes-element-pack'); ?>
						</a>
					<?php endif; ?>

					<?php if ($show_login) : ?>
						<a class="bdt-login" href="<?php echo wp_login_url(); ?>">
							<?php esc_html_e('Login', 'bdthemes-element-pack'); ?>
						</a>
					<?php endif; ?>

				</div>
			<?php endif; ?>

			<?php wp_nonce_field('ajax-login-nonce', 'bdt-user-register-sc'); ?>

		</form>
<?php
	}
}
