<?php

namespace ElementPack\Modules\ProtectedContent\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Modules\ProtectedContent\Module;
use ElementPack\Element_Pack_Loader;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Protected_Content extends Module_Base {

	public function get_name() {
		return 'bdt-protected-content';
	}

	public function get_title() {
		return BDTEP . esc_html__('Protected Content', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-protected-content';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['protected', 'content', 'safe'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/jcLWace-JpE';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'protection_type_section',
			[
				'label' => esc_html__('Protection Type', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'protection_type',
			[
				'label'       => esc_html__('Protection Type', 'bdthemes-element-pack'),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'user',
				'options'     => [
					'user'     => esc_html__('User Based', 'bdthemes-element-pack'),
					'password' => esc_html__('Password Based', 'bdthemes-element-pack')
				]
			]
		);

		$this->add_control(
			'user_type',
			[
				'label'       => __('Select User Type', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Module::pc_user_roles(),
				'condition'   => [
					'protection_type' => 'user'
				]
			]
		);

		$this->add_control(
			'content_password',
			[
				'label'     => esc_html__('Set Password', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => '123456',
				'condition' => [
					'protection_type' => 'password'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'protected_content',
			[
				'label' => esc_html__('Protected Content', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'protected_content_type',
			[
				'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom_content',
				'options' => [
					'custom_content' => esc_html__('Custom Content', 'bdthemes-element-pack'),
					'elementor'      => esc_html__('Elementor Template', 'bdthemes-element-pack'),
					'anywhere'       => esc_html__('AE Template', 'bdthemes-element-pack'),
				],
			]
		);
		$this->add_control(
			'protected_elementor_template',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'elementor_template',
				],
				'condition'   => ['protected_content_type' => "elementor"],
			]
		);
		$this->add_control(
			'protected_anywhere_template',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'anywhere_template',
				],
				'condition'   => ['protected_content_type' => "anywhere"],
			]
		);

		$this->add_control(
			'protected_custom_content',
			[
				'label'       => esc_html__('Custom Content', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('This is your content that you want to be protected by either user role or password.', 'bdthemes-element-pack'),
				'condition'   => [
					'protected_content_type' => 'custom_content',
				],
			]
		);

		$this->add_control(
			'bdt_show_content',
			[
				'label'       => __('Show Forcefully for Edit', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __('You can show your protected content in editor for design it.', 'bdthemes-element-pack'),
				'condition'   => [
					'protection_type'	=> 'password'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'warning_message',
			[
				'label' => esc_html__('Warning Message', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'warning_message_type',
			[
				'label'   => esc_html__('Message Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom_content',
				'options' => [
					'custom_content' => esc_html__('Custom Message', 'bdthemes-element-pack'),
					'elementor'      => esc_html__('Elementor Template', 'bdthemes-element-pack'),
					'anywhere'       => esc_html__('AE Template', 'bdthemes-element-pack'),
					'none'           => esc_html__('None', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'warning_message_template',
			[
				'label'       => __('Enter Template ID', 'bdthemes-element-pack'),
				'description' => __('Go to your template > Edit template > look at here: http://prntscr.com/md5qvr for template ID.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => 'true',
				'condition'   => ['warning_message_type' => 'elementor'],
			]
		);


		$this->add_control(
			'warning_message_anywhere_template',
			[
				'label'       => esc_html__('Enter Template ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => 'true',
				'condition'   => ['warning_message_type' => 'anywhere'],
				'render_type' => 'template',
			]

		);

		$this->add_control(
			'warning_message_text',
			[
				'label'     => esc_html__('Custom Message', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__('You don\'t have permission to see this content.', 'bdthemes-element-pack'),
				'dynamic'   => ['active' => true],
				'condition' => [
					'warning_message_type' => 'custom_content'
				]
			]
		);

		$this->add_control(
			'warning_message_close_button',
			[
				'label'   => esc_html__('Close Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'protected_content_style',
			[
				'label'     => esc_html__('Protected Content', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'protected_content_type' => 'custom_content'
				]
			]
		);

		$this->add_control(
			'protected_content_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-protected-content .protected-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'protected_content_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-protected-content .protected-content' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'protected_content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-protected-content .protected-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'protected_content_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-protected-content .protected-content' => 'Margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'protected_content_typography',
				'selector' => '{{WRAPPER}} .bdt-protected-content .protected-content',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'warning_message_style',
			[
				'label'     => esc_html__('Warning Message', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'warning_message_type' => 'custom_content'
				]
			]
		);

		$this->add_control(
			'warning_message_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-protected-content-message-text .bdt-alert' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'warning_message_close_button_color',
			[
				'label'     => esc_html__('Close Button Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-alert-close.bdt-close.bdt-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'warning_message_close_button' => 'yes'
				]
			]
		);

		$this->add_control(
			'warning_message_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-protected-content-message-text .bdt-alert' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'warning_message_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .bdt-protected-content-message-text .bdt-alert' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'warning_message_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-protected-content-message-text .bdt-alert' => 'Margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'warning_message_typography',
				'selector' => '{{WRAPPER}} .bdt-protected-content-message-text .bdt-alert'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'protected_content_password_input',
			[
				'label'     => esc_html__('Password Input', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'protection_type'	=> 'password'
				]
			]
		);

		$this->start_controls_tabs('protected_content_password_input_control_tabs');

		$this->start_controls_tab('protected_content_password_input_normal', [
			'label' => esc_html__('Normal', 'bdthemes-element-pack')
		]);

		$this->add_control(
			'protected_content_password_input_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'protected_content_password_input_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'protected_content_password_input_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'protected_content_password_input_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'protected_content_password_input_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password',
			]
		);

		$this->add_responsive_control(
			'protected_content_password_input_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'protected_content_password_input_shadow',
				'selector' => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'protected_content_password_input_typography',
				'selector' => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('protected_content_password_input_hover', [
			'label' => esc_html__('Hover', 'bdthemes-element-pack')
		]);

		$this->add_control(
			'protected_content_password_input_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'protected_content_password_input_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password:hover' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'protected_content_password_input_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'protected_content_password_input_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'protected_content_password_input_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-password:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'protected_content_submit_button',
			[
				'label'     => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'protection_type'	=> 'password'
				]
			]
		);

		$this->start_controls_tabs('protected_content_submit_button_control_tabs');

		$this->start_controls_tab('protected_content_submit_button_normal', [
			'label' => esc_html__('Normal', 'bdthemes-element-pack')
		]);

		$this->add_control(
			'protected_content_submit_button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'protected_content_submit_button_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'protected_content_submit_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'protected_content_submit_button_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'protected_content_submit_button_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button',
			]
		);

		$this->add_responsive_control(
			'protected_content_submit_button_border_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'protected_content_submit_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'protected_content_submit_button_typography',
				'selector' => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('protected_content_submit_button_hover', [
			'label' => esc_html__('Hover', 'bdthemes-element-pack')
		]);

		$this->add_control(
			'protected_content_submit_button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'protected_content_submit_button_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button:hover' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'protected_content_submit_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'protected_content_submit_button_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'protected_content_submit_button_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-password-protected-content-fields input.bdt-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	// Check current user rights.
	protected function current_user_rights() {
		if (!is_user_logged_in()) {
			return;
		}
		$user_type    = $this->get_settings('user_type');
		// $user_role    = reset(wp_get_current_user()->roles);
		$user_role    = wp_get_current_user()->roles;		
		$content_role = ($user_type) ? $user_type : [];
		$output       = array_intersect($user_role, $content_role) ? true : false;
		
		return $output;
	}

	// Output the protected message content
	protected function render_protected_message() {
		$settings = $this->get_settings_for_display();
		$close_button = ('yes' == $settings['warning_message_close_button']) ? true : false;
?>
		<div class="bdt-protected-content-message">
			<?php
			if ('custom_content' == $settings['warning_message_type']) { ?>

				<?php if (!isset($_POST['content_password'])) : ?>

					<?php if (!empty($settings['warning_message_text'])) : ?>
						<div class="bdt-protected-content-message-text">
							<?php element_pack_alert($settings['warning_message_text'], 'warning', $close_button); ?>
						</div>
					<?php endif; ?>

				<?php elseif (isset($_POST['content_password']) && ($settings['content_password'] !== $_POST['content_password'])) : ?>
					<?php element_pack_alert(esc_html__('Oops, you entered the wrong password!', 'bdthemes-element-pack'), 'warning', $close_button); ?>
				<?php endif; ?>

			<?php
			} elseif ('elementor' == $settings['warning_message_type'] and !empty($settings['warning_message_template'])) {
				echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['warning_message_template']);
				echo element_pack_template_edit_link($settings['warning_message_template']);
			} elseif ('anywhere' == $settings['warning_message_type'] and !empty($settings['warning_message_anywhere_template'])) {
				echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['warning_message_anywhere_template']);
				echo element_pack_template_edit_link($settings['warning_message_anywhere_template']);
			}
			?>
		</div>
	<?php
	}

	public function render_protected_form() {
	?>
		<div class="bdt-password-protected-content-fields">
			<form method="post" class="bdt-grid bdt-grid-small" bdt-grid>
				<div class="bdt-width-auto">
					<input type="password" name="content_password" class="bdt-input bdt-password bdt-form-width-medium" placeholder="<?php esc_html_e('Enter Password', 'bdthemes-element-pack'); ?>" />
				</div>
				<div class="bdt-width-auto">
					<input type="submit" value="<?php esc_html_e('Submit', 'bdthemes-element-pack'); ?>" class="bdt-button bdt-button-primary" />
				</div>
			</form>

		</div>
	<?php
	}

	// Output protected content
	public function render_protected_content() {
		$settings = $this->get_settings_for_display();
	?>
		<div class="protected-content">
			<?php
			if ('custom_content' == $settings['protected_content_type'] and !empty($settings['protected_custom_content'])) { ?>
				<div class="bdt-protected-content-message">
					<?php echo $this->parse_text_editor($settings['protected_custom_content']); ?>
				</div>
			<?php
			} elseif ('elementor' == $settings['protected_content_type'] and !empty($settings['protected_elementor_template'])) {
				echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['protected_elementor_template']);
				echo element_pack_template_edit_link($settings['protected_elementor_template']);
			} elseif ('anywhere' == $settings['protected_content_type'] and !empty($settings['protected_anywhere_template'])) {
				echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['protected_anywhere_template']);
				echo element_pack_template_edit_link($settings['protected_anywhere_template']);
			}
			?>
		</div>
	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
	?>

		<div class="bdt-protected-content">

			<?php
			if ('user' == $settings['protection_type']) {
				if (true === $this->current_user_rights()) {
					$this->render_protected_content();
				} else {
					$this->render_protected_message();
				}
			} elseif ('password' == $settings['protection_type']) {

				if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
					if ('yes' !== $settings['bdt_show_content']) {
						$this->render_protected_message();
						$this->render_protected_form();
					} else {
						$this->render_protected_content();
					}
				} else {

					if (!empty($settings['content_password'])) {

						if (isset($_POST['content_password']) && ($settings['content_password'] === $_POST['content_password'])) {
							if (!session_status()) {
								session_start();
							}
							$_SESSION['content_password'] = true;
							$this->render_protected_content();
						}
					} else {
						element_pack_alert(esc_html__('Ops, You Forget to set password!', 'bdthemes-element-pack'));
					}

					if (!isset($_SESSION['content_password'])) {
						$this->render_protected_message();
						$this->render_protected_form();
					}
				}
			} ?>
		</div>

<?php
	}
}
