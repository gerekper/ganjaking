<?php

namespace ElementPack\Modules\GiveProfileEditor\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Give_Profile_Editor extends Module_Base {

	public function get_name() {
		return 'bdt-give-profile-editor';
	}

	public function get_title() {
		return BDTEP . __('Give Profile Editor', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-give-profile-editor';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['give', 'charity', 'donation', 'donor', 'history', 'wall', 'form', 'goal', 'profile', 'editor'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-give-profile-editor'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/oaUUPA7eX2A';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'give_profile_editor_style',
			[
				'label' => __('Give Profile Editor', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'give_profile_editor_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form > fieldset' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'give_profile_editor_border',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form > fieldset',
			]
		);

		$this->add_responsive_control(
			'give_profile_editor_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form > fieldset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'give_profile_editor_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form > fieldset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'main_title_heading',
			[
				'label' => esc_html__('Main Title', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);


		$this->add_control(
			'main_title_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'main_title_border',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form legend',
			]
		);

		$this->add_responsive_control(
			'main_title_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form legend' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'main_title_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form legend' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'main_title_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form legend' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_title_typography',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form legend',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give-section-break' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give-section-break' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give-section-break' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form .give-section-break',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Input Fields', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_field_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=email], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=password], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=tel], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=text], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=url], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row select, {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=email], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=password], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=tel], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=text], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=url], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row select, {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row textarea' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=email], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=password], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=tel], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=text], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=url], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row select, {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row textarea',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=email], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=password], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=tel], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=text], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=url], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row select, {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_inner_padding',
			[
				'label' => esc_html__('Inner Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=email], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=password], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=tel], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=text], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=url], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row select, {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=email], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=password], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=tel], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=text], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row input[type=url], {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row select, {{WRAPPER}} .bdt-give-profile-editor .give-form .form-row textarea',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit',
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
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give-form .give_submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_password_text_style',
			[
				'label' => esc_html__('Reset Password Notice', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'password_text_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-profile-editor .give_password_change_notice' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'password_text_typography',
				'selector' => '{{WRAPPER}} .bdt-give-profile-editor .give_password_change_notice',
			]
		);

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		$attributes = [];

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[give_profile_editor %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {

		$this->add_render_attribute('give_wrapper', 'class', 'bdt-give-profile-editor');

?>

		<div <?php echo $this->get_render_attribute_string('give_wrapper'); ?>>

			<?php echo do_shortcode($this->get_shortcode()); ?>

		</div>

<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
