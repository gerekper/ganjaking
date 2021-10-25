<?php

namespace MasterAddons\Modules;

/**
 * Content protection class
 *
 * @package MasterAddons\Modules
 */


use \Elementor\Controls_Manager;
use \Elementor\Frontend;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;

use MasterAddons\Inc\Helper\Master_Addons_Helper;
use \MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;

/**
 * Class Content_Protection
 *
 * @package MasterAddons\Modules
 */
class Extension_Content_Protection extends JLTMA_Extension_Prototype
{

	private static $instance = null;
	public $name = 'Content Protection';
	/**
	 * Content_Protection constructor.
	 */
	public function __construct()
	{
		add_action('elementor/element/common/_section_style/after_section_end', array($this, 'register_controls'), 10);
		add_action('elementor/widget/render_content', array($this, 'render_content'), 10, 2);
	}

	/**
	 * Register Content Protection Controls.
	 *
	 * @param Object $element Elementor instance.
	 */
	public function register_controls($element)
	{
		$element->start_controls_section(
			'jltma_content_protection_section',
			[
				'label' => esc_html__('Content Protection', MELA_TD),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'jltma_content_protection',
			[
				'label'        => __('Enable Content Protection', MELA_TD),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __('Yes', MELA_TD),
				'label_off'    => __('No', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'jltma_content_protection_type',
			[
				'label'       => esc_html__('Protection Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'role'             => esc_html__('User role', MELA_TD),
					'password'         => esc_html__('Password protected', MELA_TD),
					'logged-in'        => esc_html__('User is logged', MELA_TD),
					'start-end-date'   => esc_html__('Start / End date', MELA_TD),
					'days-of-the-week' => esc_html__('Days of the week', MELA_TD),
				],
				'default'     => 'role',
				'condition'   => [
					'jltma_content_protection' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_role',
			[
				'label'       => __('Select Roles', MELA_TD),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_user_roles(),
				'condition'   => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'role',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password',
			[
				'label'      => esc_html__('Set Password', MELA_TD),
				'type'       => Controls_Manager::TEXT,
				'input_type' => 'password',
				'condition'  => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_placeholder',
			[
				'label'     => esc_html__('Input Placeholder', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Enter Password',
				'condition' => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_submit_btn_txt',
			[
				'label'     => esc_html__('Submit Button Text', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Submit',
				'condition' => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$date_format  = get_option('date_format');
		$time_format  = get_option('time_format');
		$current_time = gmdate($date_format . ' ' . $time_format);
		/* translators: %s is the current time */
		$description = sprintf(__('Current time: %s', MELA_TD), $current_time);

		$element->add_control(
			'server_time_note',
			[
				'type'       => Controls_Manager::RAW_HTML,
				'raw'        => $description,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'jltma_content_protection_type',
							'operator' => '===',
							'value'    => 'start-end-date',
						],
						[
							'name'     => 'jltma_content_protection_type',
							'operator' => '===',
							'value'    => 'days-of-the-week',
						],
					],
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_period_date',
			[
				'label'          => __('Period', MELA_TD),
				'type'           => Controls_Manager::DATE_TIME,
				'condition'      => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'start-end-date',
				],
				'picker_options' => [
					'mode' => 'range',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_days_of_week',
			[
				'label'       => __('Every', MELA_TD),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_days_of_week(),
				'condition'   => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'days-of-the-week',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_days_of_week_time_from',
			[
				'label'          => __('From', MELA_TD),
				'type'           => Controls_Manager::DATE_TIME,
				'condition'      => [
					'jltma_content_protection'               => 'yes',
					'jltma_content_protection_type'          => 'days-of-the-week',
					'jltma_content_protection_days_of_week!' => '',
				],
				'picker_options' => [
					'noCalendar' => true,
					'enableTime' => true,
					'dateFormat' => 'h:i K',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_days_of_week_time_to',
			[
				'label'          => __('To', MELA_TD),
				'type'           => Controls_Manager::DATE_TIME,
				'condition'      => [
					'jltma_content_protection'               => 'yes',
					'jltma_content_protection_type'          => 'days-of-the-week',
					'jltma_content_protection_days_of_week!' => '',
					'jltma_content_protection_days_of_week_time_from!' => '',
				],
				'picker_options' => [
					'noCalendar' => true,
					'enableTime' => true,
					'dateFormat' => 'h:i K',
				],
			]
		);

		$element->start_controls_tabs(
			'jltma_content_protection_tabs',
			[
				'condition' => [
					'jltma_content_protection' => 'yes',
				],
			]
		);

		$element->start_controls_tab(
			'jltma_content_protection_tab_message',
			[
				'label' => __('Message', MELA_TD),
			]
		);

		$element->add_control(
			'jltma_content_protection_message_type',
			[
				'label'       => esc_html__('Message Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__('Set a message or a saved template when the content is protected.', MELA_TD),
				'options'     => [
					'none'     => esc_html__('None', MELA_TD),
					'text'     => esc_html__('Message', MELA_TD),
					'template' => esc_html__('Saved Templates', MELA_TD),
				],
				'default'     => 'text',
			]
		);

		$element->add_control(
			'jltma_content_protection_message_text',
			[
				'label'     => esc_html__('Public Text', MELA_TD),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => esc_html__('You do not have permission to see this content.', MELA_TD),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_message_template',
			[
				'label'     => __('Choose Template', MELA_TD),
				'type'      => Controls_Manager::SELECT,
				'options'   => Master_Addons_Helper::get_page_template_options(),
				'condition' => [
					'jltma_content_protection_message_type' => 'template',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'jltma_content_protection_tab_style',
			[
				'label' => __('Style', MELA_TD),
			]
		);

		$element->add_control(
			'jltma_content_protection_message_styles',
			[
				'label'     => __('Message', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_message_text_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .neb-protected-content-message' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'jltma_content_protection_message_text_typography',
				'scheme'    => Scheme_Typography::TYPOGRAPHY_2,
				'selector'  => '{{WRAPPER}} .neb-protected-content-message, {{WRAPPER}} .protected-content-error-msg',
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_message_text_alignment',
			[
				'label'       => esc_html__('Text Alignment', MELA_TD),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'left'   => [
						'title' => esc_html__('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .neb-protected-content-message, {{WRAPPER}} .protected-content-error-msg' => 'text-align: {{VALUE}};',
				],
				'condition'   => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_message_text_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .neb-protected-content-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_styles',
			[
				'label'     => __('Password Field', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_width',
			[
				'label'     => esc_html__('Input Width', MELA_TD),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'width: {{SIZE}}px;',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_input_alignment',
			[
				'label'       => esc_html__('Input Alignment', MELA_TD),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'flex-start' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center'     => [
						'title' => esc_html__('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .neb-password-protected-content-fields > form' => 'justify-content: {{VALUE}};',
				],
				'condition'   => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_password_input_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_password_input_margin',
			[
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_border_radius',
			[
				'label'     => esc_html__('Border Radius', MELA_TD),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'border-radius: {{SIZE}}px;',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_input_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_input_bg_color',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'jltma_content_protection_password_input_border',
				'label'     => esc_html__('Border', MELA_TD),
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'jltma_content_protection_password_input_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_styles_hover',
			[
				'label'     => __('Password Field Hover', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_protected_content_password_input_hover_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_protected_content_password_input_hover_bg_color',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'neb_protected_content_password_input_hover_border',
				'label'     => esc_html__('Border', MELA_TD),
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'neb_protected_content_password_input_hover_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_styles',
			[
				'label'     => __('Submit Button', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_bg_color',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit' => 'background: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_border',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_box_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_styles_hover',
			[
				'label'     => __('Submit Button Hover', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_hover_text_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_hover_bg_color',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover' => 'background: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_hover_border',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_hover_box_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();
	}

	/**
	 * Render Content Protection Message.
	 *
	 * @param array $settings Widget Settings.
	 *
	 * @return string
	 */
	protected function render_message($settings)
	{
		$html = '<div class="neb-protected-content-message">';

		if ($settings['jltma_content_protection_message_type'] === 'text') {
			$html .= '<div class="neb-protected-content-message-text">' . $settings['jltma_content_protection_message_text'] . '</div>';
		} elseif ($settings['jltma_content_protection_message_type'] === 'template') {
			if (!empty($settings['jltma_content_protection_message_template'])) {
				$template_id = $settings['jltma_content_protection_message_template'];
				$frontend    = new Frontend();

				$html .= $frontend->get_builder_content($template_id, true);
			}
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render Content Protection form.
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return string
	 */
	public function password_protected_form($settings)
	{
		$html = '<div class="neb-password-protected-content-fields">
            <form method="post">
                <input type="password" name="jltma_content_protection_password" class="neb-password" placeholder="' . $settings['jltma_content_protection_password_placeholder'] . '">
                <input type="submit" value="' . $settings['jltma_content_protection_password_submit_btn_txt'] . '" class="neb-submit">
            </form>';

		if (isset($_POST['jltma_content_protection_password'])) {
			if ($settings['jltma_content_protection_password'] !== $_POST['jltma_content_protection_password']) {
				/* translators: %s is Incorrect password message */
				$html .= sprintf(
					'<p class="">%s</p>',
					__('Password does not match.', MELA_TD)
				);
			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render Content Protection.
	 *
	 * @param string $content Content.
	 * @param Object $widget Widget instance.
	 *
	 * @return string
	 */
	public function render_content($content, $widget)
	{
		$settings = $widget->get_settings_for_display();

		if ($settings['jltma_content_protection'] !== 'yes') {
			return $content;
		}

		if ($settings['jltma_content_protection_type'] === 'role') {
			if ($this->current_user_privileges($settings) === true) {
				return $content;
			}
			return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
		}

		if ($settings['jltma_content_protection_type'] === 'password') {
			if (empty($settings['jltma_content_protection_password'])) {
				return $content;
			}

			$html     = '';
			$unlocked = false;

			if (isset($_POST['jltma_content_protection_password'])) {
				if ($settings['jltma_content_protection_password'] === $_POST['jltma_content_protection_password']) {
					$unlocked = true;

					$html .= "<script>
                        var expires = new Date();
                        expires.setTime( expires.getTime() + ( 60 * 60 * 1000 ) );
                        document.cookie = 'jltma_content_protection_password=true;expires=' + expires.toUTCString();
                    </script>";
				}
			}

			if (isset($_COOKIE['jltma_content_protection_password']) || $unlocked) {
				$html .= $content;
			} else {
				$html .= '<div class="neb-protected-content">' . $this->render_message($settings) . $this->password_protected_form($settings) . '</div>';
			}
			return $html;
		}

		if ($settings['jltma_content_protection_type'] === 'logged-in') {
			if (is_user_logged_in()) {
				return $content;
			}

			return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
		}

		$current_time = strtotime(gmdate('Y-m-d H:i'));

		if ($settings['jltma_content_protection_type'] === 'start-end-date') {
			$period = $settings['jltma_content_protection_period_date'];
			if (empty($period)) {
				return $content;
			}

			$start_end = explode(' to ', $period);
			if (sizeof($start_end) !== 2) {
				return $content;
			}

			$start_date = strtotime($start_end[0]);
			$end_date   = strtotime($start_end[1]);
			if ($start_date <= $current_time && $current_time <= $end_date) {
				return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
			}

			return $content;
		}

		if ($settings['jltma_content_protection_type'] === 'days-of-the-week') {
			$current_day  = gmdate('w', $current_time);
			$blocked_days = !empty($settings['jltma_content_protection_days_of_week']) ? $settings['jltma_content_protection_days_of_week'] : array();
			if (in_array($current_day, $blocked_days, true)) {
				if (isset($settings['jltma_content_protection_days_of_week_time_from']) && isset($settings['jltma_content_protection_days_of_week_time_to'])) {
					$start = strtotime('today ' . $settings['jltma_content_protection_days_of_week_time_from']);
					$end   = strtotime('today ' . $settings['jltma_content_protection_days_of_week_time_to']);
					if ($start <= $current_time && $current_time <= $end) {
						return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
					}

					return $content;
				}

				return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
			}

			return $content;
		}

		return $content;
	}

	/**
	 * Get user roles.
	 *
	 * @return array
	 */
	private function get_user_roles()
	{
		global $wp_roles;
		$roles = $wp_roles->roles;
		if (empty($roles)) {
			return array();
		}

		$all_roles = array();
		foreach ($roles as $key => $value) {
			$all_roles[$key] = $roles[$key]['name'];
		}

		return $all_roles;
	}

	/**
	 * Check current user role exists inside of the roles array.
	 *
	 * @param array $settings Current widget settings.
	 *
	 * @return bool
	 */
	private function current_user_privileges($settings)
	{
		if (!is_user_logged_in()) {
			return false;
		}

		$user_role = reset(wp_get_current_user()->roles);

		return in_array($user_role, (array) $settings['jltma_content_protection_role'], true);
	}

	/**
	 * Return an array with days of the week.
	 *
	 * @return array
	 */
	private function get_days_of_week()
	{
		return array(
			6 => __('Saturday', MELA_TD),
			0 => __('Sunday', MELA_TD),
			1 => __('Monday', MELA_TD),
			2 => __('Tuesday', MELA_TD),
			3 => __('Wednesday', MELA_TD),
			4 => __('Thursday', MELA_TD),
			5 => __('Friday', MELA_TD),
		);
	}

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
Extension_Content_Protection::get_instance();
