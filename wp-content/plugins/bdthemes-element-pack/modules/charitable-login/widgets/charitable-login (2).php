<?php
namespace ElementPack\Modules\CharitableLogin\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Charitable_Login extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-login';
	}

	public function get_title() {
		return BDTEP . __( 'Charitable Login', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-charitable-login';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'charitable', 'charity', 'donation', 'donor', 'history', 'charitable', 'wall', 'login' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-charitable-login'];
        }
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/c0A90DdfGGM';
	}

    protected function register_controls() {

		$this->start_controls_section(
			'section_charitable_login',
			[
				'label' => __( 'Charitable Login', 'bethemes-element-pack' ),
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
			'charitable_login_style',
			[
				'label' => __('Charitable Login', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'charitable_login_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'charitable_login_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form',
			]
		);

		$this->add_responsive_control(
			'charitable_login_border_radius',
			[
				'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);
		
		$this->add_responsive_control(
			'charitable_login_padding',
			[
				'label' => __( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'charitable_login_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form',
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form label',
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="text"],
					{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="password"]' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'input_field_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="text"], {{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="password"]' => 'background-color: {{VALUE}};',
				],
			]
        );
        
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="text"],
				{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="password"]',
			]
		);
		
		$this->add_responsive_control(
			'input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="text"],
					{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="password"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="text"],
					{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="password"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="text"],
				{{WRAPPER}} .bdt-charitable-login .charitable-login-form input[type="password"]',
			]
        );	
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Login Button', 'bdthemes-element-pack' ),
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'selector'  => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button',
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button:hover'  => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form .login-submit .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_password_text_style',
			[
				'label' => esc_html__( 'Register/Reset Text', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'password_text_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form p > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'password_divider_color',
			[
				'label' => esc_html__( 'Divider Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-login .charitable-login-form>p>a:nth-last-child(1):before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'password_text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-login .charitable-login-form p > a',
			]
		);

		$this->end_controls_section();

    }
    
    private function render_editor_content() {
        $settings = $this->get_settings_for_display();
        
        $registration_link_text = !empty( $settings['registration_link_text'] ) ? $settings['registration_link_text'] : 'Register';
        ?>
		<div class="charitable-login-form">
            <form name="loginform" id="loginform">
                
                <p class="login-username">
                    <label for="user_login">
                        <?php echo esc_html('Username or Email Address', 'bdthemes-element-pack'); ?>
                    </label>
                    <input type="text" name="log" id="user_login" class="input" value="" size="20">
                </p>
                <p class="login-password">
                    <label for="user_pass"><?php echo esc_html('Password', 'bdthemes-element-pack'); ?></label>
                    <input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
                </p>
                
                <p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"><?php echo esc_html(' Remember Me', 'bdthemes-element-pack'); ?></label></p>
                <p class="login-submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Log In">
                    <input type="hidden" name="redirect_to" value="">
                </p>
                <input type="hidden" name="charitable" value="1">
            </form>	
            <p>
                <a href="<?php echo get_site_url(); ?>/wp-login.php?action=register&amp;redirect_to"><?php echo $registration_link_text; ?></a>&nbsp;|&nbsp;
                <a href="<?php echo get_site_url(); ?>/wp-login.php?action=lostpassword"><?php echo esc_html('Forgot Password', 'bdthemes-element-pack'); ?></a>
            </p>
        </div>
        <?php
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

        $logged_in_message = !empty( $settings['logged_in_message'] ) ? $settings['logged_in_message'] : 'You are already logged in!';
        
        $registration_link_text = !empty( $settings['registration_link_text'] ) ? $settings['registration_link_text'] : 'Register';
        
		$redirect = !empty( $settings['redirect']['url'] ) ? $settings['redirect']['url'] : '';

		$attributes = [
			'logged_in_message'      => $logged_in_message,
			'registration_link_text' => $registration_link_text,
			'redirect'               => $redirect,
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[charitable_login %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {

        $this->add_render_attribute( 'charitable_wrapper', 'class', 'bdt-charitable-login' );
		
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