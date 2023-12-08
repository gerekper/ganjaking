<?php
namespace ElementPack\Modules\CharitableRegistration\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Charitable_Registration extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-registration';
	}

	public function get_title() {
		return BDTEP . __( 'Charitable Registration', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-charitable-registration';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'charitable', 'charity', 'donation', 'donor', 'history', 'charitable', 'wall', 'registration' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-charitable-registration'];
        }
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/N-IMBmjGJsA';
	}

    protected function register_controls() {

		$this->start_controls_section(
			'section_charitable_registration',
			[
				'label' => __( 'Charitable Registration', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'logged_in_message',
            [
                'label' => esc_html__( 'Logged In Message', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'You are already logged in!', 'bdthemes-element-pack' ),
                'label_block' => true,
            ]
        );
        
        $this->add_control(
            'registration_link_text',
            [
                'label' => esc_html__( 'Registration Link Text', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label' => esc_html__( 'Redirect', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::URL,
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        //Style
		$this->start_controls_section(
			'charitable_ragistration_style',
			[
				'label' => __('Charitable Ragistration', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'charitable_ragistration_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'charitable_ragistration_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration',
			]
		);

		$this->add_responsive_control(
			'charitable_ragistration_border_radius',
			[
				'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);
		
		$this->add_responsive_control(
			'charitable_ragistration_padding',
			[
				'label' => __( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'charitable_ragistration_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style',
			[
				'label' => esc_html__( 'Label', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Input Fields', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration input[type="email"], {{WRAPPER}} .bdt-charitable-registration input[type="text"], {{WRAPPER}} .bdt-charitable-registration input[type="password"]' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'input_field_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration input[type="email"], {{WRAPPER}} .bdt-charitable-registration input[type="text"], {{WRAPPER}} .bdt-charitable-registration input[type="password"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration input[type="email"], {{WRAPPER}} .bdt-charitable-registration input[type="text"], {{WRAPPER}} .bdt-charitable-registration input[type="password"]',
			]
		);
		
		$this->add_responsive_control(
			'input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-registration input[type="email"], {{WRAPPER}} .bdt-charitable-registration input[type="text"], {{WRAPPER}} .bdt-charitable-registration input[type="password"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
		$this->add_responsive_control(
			'input_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration input[type="email"], {{WRAPPER}} .bdt-charitable-registration input[type="text"], {{WRAPPER}} .bdt-charitable-registration input[type="password"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration input[type="email"], {{WRAPPER}} .bdt-charitable-registration input[type="text"], {{WRAPPER}} .bdt-charitable-registration input[type="password"]',
			]
        );	
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Register Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'selector'  => '{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button:hover'  => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration .charitable-submit-field .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_password_text_style',
			[
				'label' => esc_html__( 'Signed Up Text', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'password_text_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-registration p > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'password_text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-registration p > a',
			]
		);

		$this->end_controls_section();

    }
    
    private function render_editor_content() {
        $settings = $this->get_settings_for_display();
        
        $registration_link_text = !empty( $settings['registration_link_text'] ) ? $settings['registration_link_text'] : 'Signed up already? Login instead.';
        ?>
		<form method="post" id="charitable-registration-form" class="charitable-form">
            <div class="charitable-form-fields cf">

                <input type="hidden" name="charitable_form_id" value="5f9879f573388" autocomplete="off"><input type="text" name="5f9879f573388" class="charitable-hidden" value="" autocomplete="off"><input type="hidden" name="_charitable_user_registration_nonce" value="24fc353406"><input type="hidden" name="_wp_http_referer" value="/element-pack-dev/default-test-page-f/?"><input type="hidden" name="charitable_action" value="save_registration">

                <div id="charitable_field_user_email" class="charitable-form-field charitable-form-field-email required-field odd">
                    <label for="charitable_field_user_email_element">
                    <?php echo esc_html('Email', 'bdthemes-element-pack'); ?><abbr class="required" title="required">*</abbr></label>
                    <input type="email" name="user_email" id="charitable_field_user_email_element" value="" required="required">
                </div>

                <div id="charitable_field_user_login" class="charitable-form-field charitable-form-field-text required-field even">
                    <label for="charitable_field_user_ragistration_element"><?php echo esc_html('Username', 'bdthemes-element-pack'); ?><abbr class="required" title="required">*</abbr></label>
                    <input type="text" name="user_login" id="charitable_field_user_ragistration_element" value="" required="required">
                </div>

                <div id="charitable_field_user_pass" class="charitable-form-field charitable-form-field-password required-field odd">
                    <label for="charitable_field_user_pass_element"><?php echo esc_html('Password', 'bdthemes-element-pack'); ?><abbr class="required" title="required">*</abbr></label>
                    <input type="password" name="user_pass" id="charitable_field_user_pass_element" value="" required="required">
                </div>

            </div>
            <!-- .charitable-form-fields -->
            <div class="charitable-form-field charitable-submit-field">
                <button class="button charitable-button registration-button" type="submit" name="register"><?php echo esc_html('Register', 'bdthemes-element-pack'); ?></button>
            </div>
            <input type="hidden" id="charitable-submit-button-value">
        </form>
        <p><a href="#"><?php echo $registration_link_text; ?></a></p>
        <?php
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

        $logged_in_message = !empty( $settings['logged_in_message'] ) ? $settings['logged_in_message'] : 'You are already logged in!';
        
        $registration_link_text = !empty( $settings['registration_link_text'] ) ? $settings['registration_link_text'] : 'Signed up already? Login instead.';
        
		$redirect = !empty( $settings['redirect']['url'] ) ? $settings['redirect']['url'] : '';

		$attributes = [
			'logged_in_message'      => $logged_in_message,
			'registration_link_text' => $registration_link_text,
			'redirect'               => $redirect,
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[charitable_registration %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {

        $this->add_render_attribute( 'charitable_wrapper', 'class', 'bdt-charitable-registration' );
		
		?>

		<div <?php echo $this->get_render_attribute_string('charitable_wrapper'); ?>>

            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                <?php echo do_shortcode( $this->get_shortcode() ); ?>
            <?php } else { ?>
                <?php $this->render_editor_content(); ?>
            <?php } ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
	
}