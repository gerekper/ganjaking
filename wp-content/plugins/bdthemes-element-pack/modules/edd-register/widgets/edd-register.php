<?php

namespace ElementPack\Modules\EddRegister\Widgets;

use Elementor\Repeater;
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

class EDD_Register extends Module_Base {

	public function get_name() {
		return 'bdt-edd-register';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Register', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-register bdt-new';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['edd', 'easy', 'digital', 'downlaod', 'register', 'login', 'user'];
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
			'section_edd_register_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'heading_form_register_after',
			[
				'label'     => __('AFTER REGISTER', 'plugin-domain'),
				'type'      => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'form_register_redirect_url',
			[
				'label'             => __('Redirect URL', 'bdthemes-element-pack'),
				'type'              => Controls_Manager::URL,
				'placeholder'       => __('https://your-domain.com', 'bdthemes-element-pack'),
				'separator' => 'after',
			]
		);
		$this->add_control(
			'edd_register_form_input_fullwidth',
			[
				'label' => esc_html__('Fullwidth Input', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_register_form input[type*="text"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form input[type*="email"]'    => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form input[type*="url"]'      => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form input[type*="number"]'   => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form input[type*="tel"]'      => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form input[type*="date"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form input[type*="password"]' => 'width: 100%;',
					'{{WRAPPER}} #edd_register_form .select.edd-select'      => 'width: 100%;',
				],
				'separator' => 'before'
			]
		);
		$this->add_control(
			'edd_register_form_button_fullwidth',
			[
				'label' => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_register_form .edd-submit' => 'width: 100%;',
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
				'label'     => __('Title', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} #edd_register_form legend' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'form_title_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_register_form legend',
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
					'{{WRAPPER}} #edd_register_form input[type*="text"]'     => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_register_form input[type*="email"]'    => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_register_form input[type*="password"]' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #edd_register_form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} #edd_register_form label',
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
					'{{WRAPPER}} #edd_register_form .edd-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_register_form .edd-input::placeholder'      => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_register_form .edd-input::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_register_form .edd-input' => 'background-color: {{VALUE}};',
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
				'selector'    => '#edd_register_form .edd-input',
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
					'{{WRAPPER}} #edd_register_form .edd-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_register_form .edd-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_register_form .edd-input',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '{{WRAPPER}} #edd_register_form .edd-input',
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
					'{{WRAPPER}} #edd_register_form .edd-input:focus' => 'color: {{VALUE}}; outline:none;',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color_focus',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_register_form .edd-input:focus::placeholder'      => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_register_form .edd-input:focus::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color_focus',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_register_form .edd-input:focus' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} #edd_register_form .edd-input:focus' => 'border-color: {{VALUE}}; outline:none;',
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
					'{{WRAPPER}} #edd_register_form .edd-submit' => 'color: {{VALUE}}; outline:none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_register_form .edd-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_register_form .edd-submit',
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
					'{{WRAPPER}} #edd_register_form .edd-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_register_form .edd-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_register_form .edd-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} #edd_register_form .edd-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #edd_register_form .edd-submit',
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
					'{{WRAPPER}} #edd_register_form .edd-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_register_form .edd-submit:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_register_form .edd-submit:hover' => 'border-color: {{VALUE}}; outline:none;',
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
				'selector' => '{{WRAPPER}} #edd_register_form .edd-submit:hover',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		if (is_user_logged_in() && Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			global $edd_register_redirect;;
			edd_print_errors(); ?>
			<form id="edd_register_form" class="edd_form" action="" method="post">
				<?php do_action('edd_register_form_fields_top'); ?>

				<fieldset>
					<legend><?php _e('Register New Account', 'easy-digital-downloads'); ?></legend>

					<?php do_action('edd_register_form_fields_before'); ?>

					<p>
						<label for="edd-user-login"><?php _e('Username', 'easy-digital-downloads'); ?></label>
						<input id="edd-user-login" class="required edd-input" type="text" name="edd_user_login" />
					</p>

					<p>
						<label for="edd-user-email"><?php _e('Email', 'easy-digital-downloads'); ?></label>
						<input id="edd-user-email" class="required edd-input" type="email" name="edd_user_email" />
					</p>

					<p>
						<label for="edd-user-pass"><?php _e('Password', 'easy-digital-downloads'); ?></label>
						<input id="edd-user-pass" class="password required edd-input" type="password" name="edd_user_pass" />
					</p>

					<p>
						<label for="edd-user-pass2"><?php _e('Confirm Password', 'easy-digital-downloads'); ?></label>
						<input id="edd-user-pass2" class="password required edd-input" type="password" name="edd_user_pass2" />
					</p>


					<?php do_action('edd_register_form_fields_before_submit'); ?>

					<p>
						<input type="hidden" name="edd_honeypot" value="" />
						<input type="hidden" name="edd_action" value="user_register" />
						<input type="hidden" name="edd_redirect" value="<?php echo esc_url($edd_register_redirect); ?>" />
						<input class="edd-submit" name="edd_register_submit" type="submit" value="<?php esc_attr_e('Register', 'easy-digital-downloads'); ?>" />
					</p>

					<?php do_action('edd_register_form_fields_after'); ?>
				</fieldset>

				<?php do_action('edd_register_form_fields_bottom'); ?>
			</form>
<?php	} else {
			echo do_shortcode('[edd_register redirect="' . $settings['form_register_redirect_url']['url'] . '"]');
		}
	}
}
