<?php

namespace MasterAddons\Addons;

use \Elementor\Controls_Manager as Controls_Manager;
use \Elementor\Group_Control_Border as Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Scheme_Typography as Scheme_Typography;
use \Elementor\Widget_Base as Widget_Base;
use MasterAddons\Inc\Helper\Master_Addons_Helper;


/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/25/19
 */


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.


class weforms extends Widget_Base
{

	public function get_name()
	{
		return 'ma-weforms';
	}

	public function get_title()
	{
		return esc_html__('weForms', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon fa fa-envelope-o';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/wp-forms/';
	}

	protected function _register_controls()
	{

		$this->start_controls_section(
			'ma_el_section_weform',
			[
				'label' => esc_html__('Select weForm', MELA_TD)
			]
		);



		$this->add_control(
			'wpuf_contact_form',
			[
				'label'       => esc_html__('Select weForm', MELA_TD),
				'description' => esc_html__('Please save and refresh the page after selecting the form', MELA_TD),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => Master_Addons_Helper::ma_el_get_weforms(),
				'default'     => '0',
			]
		);


		$this->end_controls_section();



		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/wp-forms/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/how-to-edit-contact-form-7/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=1fU6lWniRqo" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		$this->start_controls_section(
			'ma_weform_section_style',
			[
				'label' => esc_html__('Design Layout', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);


		// Premium Version Codes
		

			$this->add_control(
				'ma_weform_layout_style',
				[
					'label' => __('Design Variations', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'default' => '1',
					'options' => [
						'1'   => __('Style One', MELA_TD),
						'2'   => __('Style Two', MELA_TD),
						'3'   => __('Style Three', MELA_TD),
						'4'   => __('Style Four', MELA_TD),
						'5'   => __('Style Five', MELA_TD),
						'6'   => __('Style Six', MELA_TD),
						'7'   => __('Style Seven', MELA_TD),
						'8'   => __('Style Eight', MELA_TD),
						'9'   => __('Style Nine', MELA_TD),
						'10'   => __('Style Ten', MELA_TD),
						'11'   => __('Style Eleven', MELA_TD),
					],
				]
			);
		


		$this->end_controls_section();



		$this->start_controls_section(
			'ma_el_section_weform_styles',
			[
				'label' => esc_html__('Form Container Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_weform_background',
			[
				'label' => esc_html__('Form Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_alignment',
			[
				'label' => esc_html__('Form Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'default' => [
						'title' => __('Default', MELA_TD),
						'icon' => 'fa fa-ban',
					],
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'default',
				'prefix_class' => 'eael-contact-form-align-',
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_width',
			[
				'label' => esc_html__('Form Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_max_width',
			[
				'label' => esc_html__('Form Max Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_weform_margin',
			[
				'label' => esc_html__('Form Margin', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_padding',
			[
				'label' => esc_html__('Form Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'ma_el_weform_border_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_el_weform_border',
				'selector' => '{{WRAPPER}} .eael-weform-container',
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_weform_box_shadow',
				'selector' => '{{WRAPPER}} .eael-weform-container',
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'ma_el_section_weform_field_styles',
			[
				'label' => esc_html__('Form Fields Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_weform_input_background',
			[
				'label' => esc_html__('Input Field Background', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_weform_input_width',
			[
				'label' => esc_html__('Input Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_textarea_width',
			[
				'label' => esc_html__('Textarea Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_input_padding',
			[
				'label' => esc_html__('Fields Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->add_control(
			'ma_el_weform_input_border_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_el_weform_input_border',
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea',
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_weform_input_box_shadow',
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea',
			]
		);

		$this->add_control(
			'ma_el_weform_focus_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__('Focus State Style', MELA_TD),
				'separator' => 'before',
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_weform_input_focus_box_shadow',
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea:focus',
			]
		);

		$this->add_control(
			'ma_el_weform_input_focus_border',
			[
				'label' => esc_html__('Border Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"]:focus,
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea:focus' => 'border-color: {{VALUE}};',
				],
			]
		);



		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_section_weform_typography',
			[
				'label' => esc_html__('Color & Typography', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);


		$this->add_control(
			'ma_el_weform_label_color',
			[
				'label' => esc_html__('Label Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container, {{WRAPPER}} .eael-weform-container .wpuf-label label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_weform_field_color',
			[
				'label' => esc_html__('Field Font Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_weform_placeholder_color',
			[
				'label' => esc_html__('Placeholder Font Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-weform-container ::-moz-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-weform-container ::-ms-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'ma_el_weform_label_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__('Label Typography', MELA_TD),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_weform_label_typography',
				'selector' => '{{WRAPPER}} .eael-weform-container, {{WRAPPER}} .eael-weform-container .wpuf-label label',
			]
		);


		$this->add_control(
			'ma_el_weform_heading_input_field',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__('Input Fields Typography', MELA_TD),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_weform_input_field_typography',
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="text"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="password"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="email"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="url"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields input[type="number"],
					 {{WRAPPER}} .eael-weform-container ul.wpuf-form li .wpuf-fields textarea',
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'ma_el_section_weform_submit_button_styles',
			[
				'label' => esc_html__('Submit Button Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_submit_btn_width',
			[
				'label' => esc_html__('Button Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_submit_btn_alignment',
			[
				'label' => esc_html__('Button Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'default' => [
						'title' => __('Default', MELA_TD),
						'icon' => 'fa fa-ban',
					],
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'default',
				'prefix_class' => 'eael-contact-form-btn-align-',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_weform_submit_btn_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]',
			]
		);

		$this->add_responsive_control(
			'ma_el_weform_submit_btn_margin',
			[
				'label' => esc_html__('Margin', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_weform_submit_btn_padding',
			[
				'label' => esc_html__('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->start_controls_tabs('ma_el_weform_submit_button_tabs');

		$this->start_controls_tab('normal', ['label' => esc_html__('Normal', MELA_TD)]);

		$this->add_control(
			'ma_el_weform_submit_btn_text_color',
			[
				'label' => esc_html__('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]' => 'color: {{VALUE}};',
				],
			]
		);



		$this->add_control(
			'ma_el_weform_submit_btn_background_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_el_weform_submit_btn_border',
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]',
			]
		);

		$this->add_control(
			'ma_el_weform_submit_btn_border_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]' => 'border-radius: {{SIZE}}px;',
				],
			]
		);



		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_weform_submit_btn_hover', ['label' => esc_html__('Hover', MELA_TD)]);

		$this->add_control(
			'ma_el_weform_submit_btn_hover_text_color',
			[
				'label' => esc_html__('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_weform_submit_btn_hover_background_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_weform_submit_btn_hover_border_color',
			[
				'label' => esc_html__('Border Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_weform_submit_btn_box_shadow',
				'selector' => '{{WRAPPER}} .eael-weform-container ul.wpuf-form .wpuf-submit input[type="submit"]',
			]
		);


		$this->end_controls_section();


		
	}

	protected function render()
	{

		$settings = $this->get_settings();

		// if We Forms Missing
		if (!class_exists('WeForms')) {
			Master_Addons_Helper::jltma_elementor_plugin_missing_notice(array('plugin_name' => esc_html__('WeForms', MELA_TD)));
			return;
		} ?>


		<?php if (!empty($settings['wpuf_contact_form'])) : ?>
			<div class="eael-weform-container ma-cf ma-cf-<?php echo
															$settings['ma_weform_layout_style']; ?>">
				<?php echo do_shortcode('[weforms id="' . $settings['wpuf_contact_form'] . '" ]'); ?>
			</div>
		<?php endif; ?>

<?php

	}



	protected function _content_template()
	{
	}
}
