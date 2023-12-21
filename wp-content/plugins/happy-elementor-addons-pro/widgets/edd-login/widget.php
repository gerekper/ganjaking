<?php
/**
 * Easy Digital Downloads checkout widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

defined( 'ABSPATH' ) || die();

class EDD_Login extends Base {

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Login', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-checkout-2';
	}

	public function get_keywords() {
		return [ 'edd', 'commerce', 'ecommerce', 'login', 'register', 'shop' ];
	}

	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_general',
			[
				'label' => __( 'General', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'label'       => __( 'Redirect URL After Login', 'happy-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://example.com/', 'happy-addons-pro' ),
				'separator'   => 'after',
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__general_style_controls();
		$this->__inputs_style_controls();
		$this->__labels_style_controls();
		$this->__button_style_controls();

	}

	protected function __general_style_controls() {

		$this->start_controls_section(
			'_general_style_section',
			[
				'label' => __( 'General', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// edd-lost-password
		$this->add_control(
			'forms_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'forms_heading_typography',
				'selector' => '{{WRAPPER}} #edd_login_form.edd_form legend',
			]
		);

		$this->add_control(
			'forms_heading_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .edd_form legend' => 'color: {{VALUE}};',
				],
			]
		);

		// edd-lost-password
		$this->add_control(
			'links_heading',
			[
				'label' => __( 'Links', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'links_typography',
				'selector' => '{{WRAPPER}} .edd_form a',
			]
		);

		$this->start_controls_tabs( 'tabs_links_style' );

		$this->start_controls_tab(
			'tab_links_normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'links_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .edd_form a' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_links_hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'links_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd_form a:hover' => 'color: {{VALUE}};',
				],
			]
		);	

		// $this->add_control(
		// 	'hover_animation',
		// 	[
		// 		'label' => esc_html__( 'Hover Animation', 'happy-addons-pro' ),
		// 		'type' => Controls_Manager::HOVER_ANIMATION,
		// 	]
		// );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __inputs_style_controls() {
		$this->start_controls_section(
			'_section_style_inputs',
			[
				'label' => __( 'Inputs', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'inputs_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'inputs_height',
			[
				'label'     => __( 'Input Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'inputs_gap',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'inputs_border',
				'selector' => '{{WRAPPER}} #edd_login_form .edd-input',
			]
		);

		$this->add_responsive_control(
			'inputs_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}  #edd_login_form .edd-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'inputs_text_align',
			[
				'label'       => __( 'Text Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form .edd-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'inputs_typography',
				'selector' => '{{WRAPPER}} #edd_login_form .edd-input',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'inputs_box_shadow',
				'selector' => '{{WRAPPER}} #edd_login_form .edd-input',
			]
		);

		$this->end_controls_section();
	}
	protected function __labels_style_controls() {
		$this->start_controls_section(
			'_section_style_labels',
			[
				'label' => __( 'Labels', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'labels_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_login_form label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'labels_height',
		// 	[
		// 		'label'   => __( 'Label Height', 'happy-addons-pro' ),
		// 		'type'    => Controls_Manager::SLIDER,
		// 		'default' => [
		// 			'size' => '',
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} #edd_login_form label' => 'height: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		$this->add_responsive_control(
			'labels_gap',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'labels_border',
				'selector' => '{{WRAPPER}} #edd_login_form label',
			]
		);

		$this->add_control(
			'labels_text_align',
			[
				'label'       => __( 'Text Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} #edd_login_form label' => 'text-align: {{VALUE}};display: block;',
				],
			]
		);

		$this->add_control(
			'label_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form label' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labels_typography',
				'selector' => '{{WRAPPER}} #edd_login_form label',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'labels_box_shadow',
				'selector' => '{{WRAPPER}} #edd_login_form label',
			]
		);

		$this->end_controls_section();
	}


	protected function __headings_style_controls() {
		$this->start_controls_section(
			'_section_style_headings',
			[
				'label' => __( 'Headings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'headings_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'headings_typography',
				'selector' => '{{WRAPPER}} #edd_login_form legend',
			]
		);

		$this->add_responsive_control(
			'headings_spacing',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_login_form legend' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __button_style_controls() {
		$this->start_controls_section(
			'_section_style_button',
			[
				'label' => __( 'Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit',
			]
		);

		$this->add_control(
			'btn_align',
			[
				'label'       => __( 'Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'toggle'      => true,
				'selectors'   => [
					'{{WRAPPER}} #edd_login_form .edd-login-submit' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'background',
				'label'          => esc_html__( 'Background', 'happy-addons-pro' ),
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-submit:hover, {{WRAPPER}} .edd-submit:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .edd-submit:hover svg, {{WRAPPER}} .edd-submit:focus svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'button_background_hover',
				'label'          => esc_html__( 'Background', 'happy-addons-pro' ),
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} .edd-submit:hover, {{WRAPPER}} .edd-submit:focus',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .edd-submit:hover, {{WRAPPER}} .edd-submit:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'hover_animation',
		// 	[
		// 		'label' => esc_html__( 'Hover Animation', 'happy-addons-pro' ),
		// 		'type' => Controls_Manager::HOVER_ANIMATION,
		// 	]
		// );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'border',
				'selector'  => '{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd-purchase-button,{{WRAPPER}} .edd-submit,{{WRAPPER}} [type=submit].edd-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	public static function show_edd_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'Easy Digital Downloads is missing! Please install and activate Easy Digital Downloads.', 'happy-addons-pro' )
			);
		}
	}

	protected function render() {
		if ( ! function_exists( 'EDD' ) ) {
			self::show_edd_missing_alert();
			return;
		}

		$settings = $this->get_settings_for_display();
		$redirect = isset( $settings['redirect_url']['url'] ) ? $settings['redirect_url']['url'] : '';

		$atts = [
			'redirect' => $redirect,
		];

		if ( ha_elementor()->editor->is_edit_mode() ) {

			add_filter( 'edd_login_form', [$this, 'add_login_edd'] );

			echo ha_do_shortcode( 'edd_login' );

			remove_filter( 'edd_login_form', [$this, 'add_login_edd'] );

		} else {

			echo ha_do_shortcode( 'edd_login', $atts );

		}

	}

	function add_login_edd( $template ) {
		global $edd_login_redirect;

			// Show any error messages after form submission
			edd_print_errors(); ?>
			<form id="edd_login_form" class="edd_form" action="" method="post">
				<fieldset>
					<legend><?php _e( 'Log into Your Account', 'happy-addons-pro' ); ?></legend>
					<?php do_action( 'edd_login_fields_before' ); ?>
					<p class="edd-login-username">
						<label for="edd_user_login"><?php _e( 'Username or Email', 'happy-addons-pro' ); ?></label>
						<input name="edd_user_login" id="edd_user_login" class="edd-required edd-input" type="text"/>
					</p>
					<p class="edd-login-password">
						<label for="edd_user_pass"><?php _e( 'Password', 'happy-addons-pro' ); ?></label>
						<input name="edd_user_pass" id="edd_user_pass" class="edd-password edd-required edd-input" type="password"/>
					</p>
					<p class="edd-login-remember">
						<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember Me', 'happy-addons-pro' ); ?></label>
					</p>
					<p class="edd-login-submit">
						<input type="hidden" name="edd_redirect" value="<?php echo esc_url( $edd_login_redirect ); ?>"/>
						<input type="hidden" name="edd_login_nonce" value="<?php echo wp_create_nonce( 'edd-login-nonce' ); ?>"/>
						<input type="hidden" name="edd_action" value="user_login"/>
						<input id="edd_login_submit" type="submit" class="edd-submit" value="<?php _e( 'Log In', 'happy-addons-pro' ); ?>"/>
					</p>
					<p class="edd-lost-password">
						<a href="<?php echo esc_url( edd_get_lostpassword_url() ); ?>">
							<?php _e( 'Lost Password?', 'happy-addons-pro' ); ?>
						</a>
					</p>
					<?php do_action( 'edd_login_fields_after' ); ?>
				</fieldset>
			</form>
		<?php
	}
}
