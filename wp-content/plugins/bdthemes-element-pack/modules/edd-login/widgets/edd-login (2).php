<?php

namespace ElementPack\Modules\EddLogin\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class EDD_Login extends Module_Base {

	public function get_name() {
		return 'bdt-edd-login';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Login', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-login bdt-new';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['user', 'login', 'form'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-edd-login'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['recaptcha', 'ep-google-login', 'ep-scripts'];
		} else {
			return ['recaptcha', 'ep-google-login', 'ep-edd-login'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/JLdKfv_-R6c';
	}

	protected function register_controls() {
		$this->register_form_controls_layout();
		$this->register_form_controls_style();
		$this->register_form_controls_label();
		$this->register_form_controls_fields();
		$this->register_form_submit_button();
	}
	protected function register_form_controls_layout() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'edd_login_form_input_fullwidth',
			[
				'label' => esc_html__('Fullwidth Input', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_login_form input[type*="text"]'     => 'width: 100%;',
					// '{{WRAPPER}} #edd_login_form input[type*="email"]'    => 'width: 100%;',
					// '{{WRAPPER}} #edd_login_form input[type*="url"]'      => 'width: 100%;',
					// '{{WRAPPER}} #edd_login_form input[type*="number"]'   => 'width: 100%;',
					// '{{WRAPPER}} #edd_login_form input[type*="tel"]'      => 'width: 100%;',
					// '{{WRAPPER}} #edd_login_form input[type*="date"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_login_form input[type*="password"]' => 'width: 100%;',
					// '{{WRAPPER}} #edd_login_form .select.edd-select'      => 'width: 100%;',
				],
			]
		);
		$this->add_control(
			'edd_login_form_button_fullwidth',
			[
				'label' => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-submit' => 'width: 100%;',
				],
			]
		);
		$this->end_controls_section();
	}
	protected function register_form_controls_style() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Form Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'form_style_title',
			[
				'label'     => __('T I T L E', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'form_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-plack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form legend' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'form_title_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_login_form legend',
			]
		);

		$this->add_control(
			'links_heading',
			[
				'label'     => esc_html__('L I N K S', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} #edd_login_form .edd-lost-password a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'links_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-lost-password a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'links_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_login_form .edd-lost-password a',
			]
		);

		$this->end_controls_section();
	}
	protected function register_form_controls_label() {
		$this->start_controls_section(
			'section_style_labels',
			[
				'label'      => esc_html__('Form Label', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} #edd_login_form .edd-input' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #edd_login_form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} #edd_login_form label',
			]
		);

		$this->end_controls_section();
	}
	protected function register_form_controls_fields() {
		$this->start_controls_section(
			'section_field_style',
			[
				'label' => esc_html__('Form Fields', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} #edd_login_form input[type="text"], {{WRAPPER}} #edd_login_form input[type="password"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-input::placeholder'      => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_login_form .edd-input::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'field_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_login_form .edd-input',
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
					'{{WRAPPER}} #edd_login_form .edd-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_login_form .edd-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_login_form .edd-input',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '{{WRAPPER}} #edd_login_form .edd-input',
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
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color_focus',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form input:focus::placeholder'      => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_login_form input:focus::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color_focus',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form input:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form input:focus' => 'border-color: {{VALUE}}; outline:none;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	protected function register_form_submit_button() {
		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label' => esc_html__('Form Submit Button', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} #edd_login_form #edd_login_submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_login_form #edd_login_submit',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_login_form #edd_login_submit',
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
					'{{WRAPPER}} #edd_login_form #edd_login_submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_login_form #edd_login_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_login_form #edd_login_submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} #edd_login_form #edd_login_submit',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #edd_login_form #edd_login_submit',
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
					'{{WRAPPER}} #edd_login_form #edd_login_submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_login_form #edd_login_submit:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form #edd_login_submit:hover' => 'border-color: {{VALUE}};',
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
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #edd_login_form #edd_login_submit:hover',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		if (is_user_logged_in() && Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			global $edd_login_redirect;
			edd_print_errors(); ?>
			<div class="bdt-edd-login">
				<form id="edd_login_form" class="edd_form" action="" method="post">
					<fieldset>
						<legend><?php _e('Log into Your Account', 'easy-digital-downloads'); ?></legend>
						<?php do_action('edd_login_fields_before'); ?>
						<p class="edd-login-username">
							<label for="edd_user_login"><?php _e('Username or Email', 'easy-digital-downloads'); ?></label>
							<input name="edd_user_login" id="edd_user_login" class="edd-required edd-input" type="text" />
						</p>
						<p class="edd-login-password">
							<label for="edd_user_pass"><?php _e('Password', 'easy-digital-downloads'); ?></label>
							<input name="edd_user_pass" id="edd_user_pass" class="edd-password edd-required edd-input" type="password" />
						</p>
						<p class="edd-login-remember">
							<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e('Remember Me', 'easy-digital-downloads'); ?></label>
						</p>
						<p class="edd-login-submit">
							<input type="hidden" name="edd_redirect" value="<?php echo esc_url($edd_login_redirect); ?>" />
							<input type="hidden" name="edd_login_nonce" value="<?php echo wp_create_nonce('edd-login-nonce'); ?>" />
							<input type="hidden" name="edd_action" value="user_login" />
							<input id="edd_login_submit" type="submit" class="edd-submit" value="<?php _e('Log In', 'easy-digital-downloads'); ?>" />
						</p>
						<p class="edd-lost-password">
							<a href="<?php echo esc_url(edd_get_lostpassword_url()); ?>">
								<?php _e('Lost Password?', 'easy-digital-downloads'); ?>
							</a>
						</p>
						<?php do_action('edd_login_fields_after'); ?>
					</fieldset>
				</form>
			</div>
		<?php	} else {
		?>
			<div class="bdt-edd-login">
				<?php echo do_shortcode('[edd_login]'); ?>
			</div>
<?php
		}
	}
}
