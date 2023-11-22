<?php

namespace ElementPack\Modules\EddProfileEditor\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class EDD_Profile_Editor extends Module_Base {

	public function get_name() {
		return 'bdt-easy-digital-profile-editor';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Profile Editor', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-profile-editor';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['easy', 'digital', 'downloads', 'software', 'eshop', 'estore', 'profile', 'editor'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/z6MSJtvbxPQ';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style_fieldset',
			[
				'label' => esc_html__('Fieldset', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'fieldset_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} fieldset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'fieldset_border_style',
			[
				'label'   => __('Border Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __('None', 'bdthemes-element-pack'),
					'solid'  => __('Solid', 'bdthemes-element-pack'),
					'double' => __('Double', 'bdthemes-element-pack'),
					'dotted' => __('Dotted', 'bdthemes-element-pack'),
					'dashed' => __('Dashed', 'bdthemes-element-pack'),
					'groove' => __('Groove', 'bdthemes-element-pack'),
				],
				'selectors' => [
					'{{WRAPPER}} fieldset' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'fieldset_border_width',
			[
				'label' => esc_html__('Border Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'px' => [
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} fieldset' => 'border-width: {{SIZE}}px;',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'fieldset_border_radius',
		// 	[
		// 		'label'      => esc_html__('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => ['px', '%'],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} fieldset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		
		$this->add_responsive_control(
			'fieldset_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} fieldset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'fieldset_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} fieldset' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_color',
			[
				'label'     => esc_html__('Caption Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'caption_border',
				'label'       => esc_html__('Caption Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} legend',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'caption_radius',
			[
				'label'      => esc_html__('Caption Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} legend' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[
				'label'      => esc_html__('Caption Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} legend' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'caption_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} legend',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} #edd_profile_editor_form label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_profile_editor_form textarea::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form input' => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'others_type_input_text_color',
			[
				'label'     => esc_html__('Others Type Input Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'      => '#666666',
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form.select-state' => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_profile_editor_form.select-gender' => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_profile_editor_form.accept-this-1' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form input' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label'   => esc_html__('Textarea Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 125,
				],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea' => 'height: {{SIZE}}{{UNIT}}; display: block;',
				],
				'separator' => 'before',

			]
		);

		$this->add_control(
			'input_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_profile_editor_form input, {{WRAPPER}} #edd_profile_editor_form .wpcf7-textarea, {{WRAPPER}} #edd_profile_editor_form .select.edd-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_space',
			[
				'label'   => esc_html__('Element Space', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 25,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form-control' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_profile_editor_form'         => 'margin-top: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'input_border_show',
			[
				'label'        => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'input_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_profile_editor_form input, {{WRAPPER}} #edd_profile_editor_form textarea, {{WRAPPER}} #edd_profile_editor_form .select.edd-select',
				'condition' => [
					'input_border_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'input_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_profile_editor_form input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #edd_profile_editor_form textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #edd_profile_editor_form .select.edd-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_submit_button',
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
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} #edd_profile_editor_submit',
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit',
				'separator' => 'before',
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
			'hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional_option',
			[
				'label' => esc_html__('Additional Option', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'fullwidth_input',
			[
				'label' => esc_html__('Fullwidth Input', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form input[type*="text"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form input[type*="email"]'    => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form input[type*="url"]'      => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form input[type*="number"]'   => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form input[type*="tel"]'      => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form input[type*="date"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form input[type*="password"]' => 'width: 100%;',
					'{{WRAPPER}} #edd_profile_editor_form .select.edd-select'      => 'width: 100%;',
				],
			]
		);

		$this->add_control(
			'fullwidth_textarea',
			[
				'label' => esc_html__('Fullwidth Texarea', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form textarea' => 'width: 100%;',
				],
			]
		);

		$this->add_control(
			'fullwidth_button',
			[
				'label' => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_profile_editor_form #edd_profile_editor_submit' => 'width: 100%;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		echo do_shortcode('[edd_profile_editor]');
	}
}
