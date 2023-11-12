<?php
/**
 * UAEL Login Form.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\LoginForm\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Login Form.
 */
class LoginForm extends Common_Widget {


	/**
	 * Retrieve Login Form Widget name.
	 *
	 * @since 1.20.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'LoginForm' );
	}

	/**
	 * Retrieve Login Form Widget title.
	 *
	 * @since 1.20.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'LoginForm' );
	}

	/**
	 * Retrieve Login Form Widget icon.
	 *
	 * @since 1.20.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'LoginForm' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.20.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'LoginForm' );
	}

	/**
	 * Retrieve the list of scripts the login form widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.20.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script', 'uael-video-subscribe' );
	}

	/**
	 * Retrieve Button sizes.
	 *
	 * @since 1.20.0
	 * @access public
	 *
	 * @return array Button Sizes.
	 */
	public static function get_button_sizes() {
		return Widget_Button::get_button_sizes();
	}

	/**
	 * Register Login Form controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_presets_control( 'LoginForm', $this );

		$this->register_general_controls();

		$this->register_social_controls();

		$this->register_separator_controls();

		$this->register_button_controls();

		$this->register_additional_options_controls();

		$this->register_spacing_controls();

		$this->register_fields_style_controls();

		$this->register_social_style_controls();

		$this->register_button_style_controls();

		$this->register_validation_controls();

		$this->register_helpful_information();
	}

	/**
	 * Register Login Form General Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_general_controls() {

		$this->start_controls_section(
			'section_general_field',
			array(
				'label' => __( 'Form Fields', 'uael' ),
			)
		);

			$this->add_control(
				'show_labels',
				array(
					'label'   => __( 'Field Label', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'default' => __( 'Default', 'uael' ),
						'custom'  => __( 'Custom', 'uael' ),
						'none'    => __( 'None', 'uael' ),
					),
					'default' => 'default',
				)
			);

				$this->add_control(
					'user_label',
					array(
						'label'       => __( 'Username Label', 'uael' ),
						'default'     => __( 'Username or Email Address', 'uael' ),
						'type'        => Controls_Manager::TEXT,
						'dynamic'     => array(
							'active' => true,
						),
						'label_block' => true,
						'condition'   => array(
							'show_labels' => 'custom',
						),
					)
				);

				$this->add_control(
					'password_label',
					array(
						'label'       => __( 'Password Label', 'uael' ),
						'default'     => __( 'Password', 'uael' ),
						'type'        => Controls_Manager::TEXT,
						'dynamic'     => array(
							'active' => true,
						),
						'label_block' => true,
						'condition'   => array(
							'show_labels' => 'custom',
						),
					)
				);

				$this->add_control(
					'show_placeholder',
					array(
						'label'        => __( 'Field Placeholder', 'uael' ),
						'type'         => Controls_Manager::SWITCHER,
						'default'      => 'yes',
						'label_off'    => __( 'Hide', 'uael' ),
						'label_on'     => __( 'Show', 'uael' ),
						'return_value' => 'yes',
					)
				);

				$this->add_control(
					'user_placeholder',
					array(
						'label'       => __( 'Username Placeholder', 'uael' ),
						'default'     => __( 'Username or Email Address', 'uael' ),
						'type'        => Controls_Manager::TEXT,
						'dynamic'     => array(
							'active' => true,
						),
						'label_block' => true,
						'condition'   => array(
							'show_labels'      => 'custom',
							'show_placeholder' => 'yes',
						),
					)
				);

				$this->add_control(
					'password_placeholder',
					array(
						'label'       => __( 'Password Placeholder', 'uael' ),
						'default'     => __( 'Password', 'uael' ),
						'type'        => Controls_Manager::TEXT,
						'dynamic'     => array(
							'active' => true,
						),
						'label_block' => true,
						'condition'   => array(
							'show_labels'      => 'custom',
							'show_placeholder' => 'yes',
						),
					)
				);

			$this->add_control(
				'input_size',
				array(
					'label'   => __( 'Input Size', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'xs' => __( 'Extra Small', 'uael' ),
						'sm' => __( 'Small', 'uael' ),
						'md' => __( 'Medium', 'uael' ),
						'lg' => __( 'Large', 'uael' ),
						'xl' => __( 'Extra Large', 'uael' ),
					),
					'default' => 'sm',
				)
			);

			$this->add_control(
				'show_remember_me',
				array(
					'label'     => __( 'Remember Me', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
				)
			);

			$this->add_control(
				'enable_ajax',
				array(
					'label'     => __( 'Enable AJAX Form Submission', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_on'  => __( 'Yes', 'uael' ),
					'label_off' => __( 'No', 'uael' ),
				)
			);

			$this->add_control(
				'inline_control',
				array(
					'label'        => __( 'Layout', 'uael' ),
					'description'  => __( 'Enable this to make Remember Me and Login inline.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_off'    => __( 'stack', 'uael' ),
					'label_on'     => __( 'inline', 'uael' ),
					'prefix_class' => 'uael-login-form-inline-',
					'condition'    => array(
						'show_remember_me' => 'yes',
					),
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'fields_icon',
				array(
					'label'        => __( 'Fields Icon', 'uael' ),
					'description'  => __( 'Enable icon for fields.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_off'    => __( 'Hide', 'uael' ),
					'label_on'     => __( 'Show', 'uael' ),
					'return_value' => 'yes',
					'render_type'  => 'template',
					'prefix_class' => 'uael-login-form-icon-',
				)
			);

			$this->add_control(
				'icon_divider',
				array(
					'label'        => __( 'Divider', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_off'    => __( 'Hide', 'uael' ),
					'label_on'     => __( 'Show', 'uael' ),
					'return_value' => 'yes',
					'prefix_class' => 'uael-login-form-divider-',
					'condition'    => array(
						'fields_icon' => 'yes',
					),
				)
			);

			$this->add_control(
				'divider_style',
				array(
					'label'     => __( 'Style', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'solid'  => __( 'Solid', 'uael' ),
						'dotted' => __( 'Dotted', 'uael' ),
						'dashed' => __( 'Dashed', 'uael' ),
					),
					'default'   => 'solid',
					'selectors' => array(
						'{{WRAPPER}} .uael-fields-icon' => 'border-right-style: {{VALUE}};',
					),
					'condition' => array(
						'icon_divider' => 'yes',
						'fields_icon'  => 'yes',
					),
				)
			);

			$this->add_control(
				'divider_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#d4d4d4',
					'selectors' => array(
						'{{WRAPPER}} .uael-fields-icon' => 'border-right-color: {{VALUE}};',
					),
					'condition' => array(
						'icon_divider' => 'yes',
						'fields_icon'  => 'yes',
					),
				)
			);

			$this->add_control(
				'divider_weight',
				array(
					'label'     => __( 'Thickness', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 1,
						'unit' => 'px',
					),
					'range'     => array(
						'px' => array(
							'min' => 1,
							'max' => 10,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-fields-icon' => 'border-right-width: {{SIZE}}{{UNIT}};',
					),
					'separator' => 'after',
					'condition' => array(
						'icon_divider' => 'yes',
						'fields_icon'  => 'yes',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Register Login Form Social login Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_social_controls() {
		$this->start_controls_section(
			'section_social_field',
			array(
				'label' => __( 'Social Login', 'uael' ),
			)
		);

			$this->add_control(
				'google_login',
				array(
					'label'       => __( 'Enable Google Login', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'default'     => 'no',
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'render_type' => 'template',
				)
			);

				$integration_options = UAEL_Helper::get_integrations_options();
				$widget_list         = UAEL_Helper::get_widget_list();
				$admin_link          = $widget_list['LoginForm']['setting_url'];
				$admin_link          = esc_url( $admin_link );

		if ( ! isset( $integration_options['google_client_id'] ) || '' === $integration_options['google_client_id'] ) {
			$this->add_control(
				'google_clientid_setting',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
						'raw'         => sprintf( __( 'Please configure Google Client ID from <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
					'condition'       => array(
						'google_login' => 'yes',
					),
				)
			);
		}

			$this->add_control(
				'facebook_login',
				array(
					'label'       => __( 'Enable Facebook Login', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'default'     => 'no',
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'render_type' => 'template',
				)
			);

		if ( ! isset( $integration_options['facebook_app_secret'] ) || '' === $integration_options['facebook_app_secret'] || ! isset( $integration_options['facebook_app_id'] ) || '' === $integration_options['facebook_app_id'] ) {
			$this->add_control(
				'facebook_app_secret_setting',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
						'raw'         => sprintf( __( 'Please configure Facebook App settings from <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
					'condition'       => array(
						'facebook_login' => 'yes',
					),
				)
			);
		}

		if ( ( isset( $integration_options['google_client_id'] ) && '' !== $integration_options['google_client_id'] ) && ( isset( $integration_options['facebook_app_id'] ) && '' !== $integration_options['facebook_app_id'] ) && ( isset( $integration_options['facebook_app_secret'] ) && '' !== $integration_options['facebook_app_secret'] ) ) {
			$this->add_control(
				'social_login_backend',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Note: To avoid any issues while logging in with Google or Facebook, make sure you correctly configure the settings <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'conditions'      => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);
		}

			$this->add_control(
				'send_email',
				array(
					'label'       => __( 'Send Email', 'uael' ),
					'description' => __( 'Send an Email to the user / site admin after a new user is successful logged in with Facebook / Google.', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'no',
					'options'     => array(
						'no'    => __( 'No', 'uael' ),
						'admin' => __( 'Admin', 'uael' ),
						'user'  => __( 'User', 'uael' ),
						'both'  => __( 'Admin & User', 'uael' ),
					),
					'conditions'  => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'hide_custom_form',
				array(
					'label'      => __( 'Hide Custom Form', 'uael' ),
					'type'       => Controls_Manager::SWITCHER,
					'default'    => 'no',
					'label_on'   => __( 'Yes', 'uael' ),
					'label_off'  => __( 'No', 'uael' ),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'social_layout',
				array(
					'label'        => __( 'Layout', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'inline' => __( 'Inline', 'uael' ),
						'stack'  => __( 'Stack', 'uael' ),
					),
					'default'      => 'inline',
					'conditions'   => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
					'prefix_class' => 'uael-login-form-social-',
				)
			);

			$this->add_control(
				'responsive_support',
				array(
					'label'        => __( 'Responsive Support', 'uael' ),
					'description'  => __( 'Enable this option to stack the social login buttons on mobile.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'On', 'uael' ),
					'label_off'    => __( 'Off', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'prefix_class' => 'uael-lf-responsive-',
					'conditions'   => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'social_layout',
								'operator' => '==',
								'value'    => 'inline',
							),
						),
					),
				)
			);

			$this->add_control(
				'social_theme',
				array(
					'label'        => __( 'Select Theme', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'light' => __( 'Light', 'uael' ),
						'dark'  => __( 'Dark', 'uael' ),
					),
					'default'      => 'dark',
					'conditions'   => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
					'prefix_class' => 'uael-lf-social-theme-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'social_position',
				array(
					'label'      => __( 'Position', 'uael' ),
					'type'       => Controls_Manager::SELECT,
					'options'    => array(
						'top'    => __( 'Top', 'uael' ),
						'bottom' => __( 'Bottom', 'uael' ),
					),
					'default'    => 'bottom',
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'facebook_login',
										'operator' => '==',
										'value'    => 'yes',
									),
									array(
										'name'     => 'google_login',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'name'     => 'hide_custom_form',
								'operator' => '!==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Login Form Social login separator Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_separator_controls() {
		$this->start_controls_section(
			'section_separator_field',
			array(
				'label'      => __( 'Separator', 'uael' ),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'hide_custom_form',
									'operator' => '!==',
									'value'    => 'yes',
								),
							),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'facebook_login',
									'operator' => '==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'google_login',
									'operator' => '==',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

			$this->add_control(
				'enable_separator',
				array(
					'label'     => __( 'Enable Separator', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'no',
					'label_on'  => __( 'Yes', 'uael' ),
					'label_off' => __( 'No', 'uael' ),
				)
			);

			$this->add_control(
				'heading_line_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => array(
						'enable_separator' => 'yes',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-separator, {{WRAPPER}} .uael-separator-line > span, {{WRAPPER}} .uael-divider-text' => 'border-top-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'heading_line_thickness',
				array(
					'label'      => __( 'Thickness', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 1,
							'max' => 20,
						),
					),
					'default'    => array(
						'size' => 2,
						'unit' => 'px',
					),
					'condition'  => array(
						'enable_separator' => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-separator, {{WRAPPER}} .uael-separator-line > span ' => 'border-top-width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'heading_line_width',
				array(
					'label'              => __( 'Width', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'size_units'         => array( '%', 'px' ),
					'range'              => array(
						'px' => array(
							'max' => 1000,
						),
					),
					'default'            => array(
						'size' => 30,
						'unit' => '%',
					),
					'tablet_default'     => array(
						'unit' => '%',
					),
					'mobile_default'     => array(
						'unit' => '%',
					),
					'label_block'        => true,
					'condition'          => array(
						'enable_separator' => 'yes',
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-separator, {{WRAPPER}} .uael-separator-wrap' => 'width: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'separator_heading',
				array(
					'label'     => __( 'Separator Text', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'enable_separator' => 'yes',
					),
				)
			);

			/* Separator line with text */
			$this->add_control(
				'separator_line_text',
				array(
					'label'     => __( 'Enter Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Or', 'uael' ),
					'condition' => array(
						'enable_separator' => 'yes',
					),
					'dynamic'   => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'separator_text_color',
				array(
					'label'     => __( 'Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'condition' => array(
						'enable_separator' => 'yes',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-divider-text' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'separator_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					),
					'condition' => array(
						'enable_separator' => 'yes',
					),
					'selector'  => '{{WRAPPER}} .uael-divider-text',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Login Form button Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_button_controls() {

		$this->start_controls_section(
			'section_button_field',
			array(
				'label' => __( 'Button', 'uael' ),
			)
		);

			$this->add_control(
				'button_text',
				array(
					'label'   => __( 'Text', 'uael' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array(
						'active' => true,
					),
					'default' => __( 'Log In', 'uael' ),
				)
			);

			$this->add_control(
				'button_size',
				array(
					'label'   => __( 'Size', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'xs' => __( 'Extra Small', 'uael' ),
						'sm' => __( 'Small', 'uael' ),
						'md' => __( 'Medium', 'uael' ),
						'lg' => __( 'Large', 'uael' ),
						'xl' => __( 'Extra Large', 'uael' ),
					),
					'default' => 'sm',
				)
			);

			$this->add_responsive_control(
				'align',
				array(
					'label'              => __( 'Alignment', 'uael' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => array(
						'start'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-text-align-center',
						),
						'end'     => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-text-align-right',
						),
						'stretch' => array(
							'title' => __( 'Justified', 'uael' ),
							'icon'  => 'eicon-text-align-justify',
						),
					),
					'prefix_class'       => 'elementor%s-button-align-',
					'default'            => '',
					'frontend_available' => true,
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {
			$this->add_control(
				'button_icon',
				array(
					'label'       => __( 'Icon', 'uael' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => true,
				)
			);
		} else {
			$this->add_control(
				'button_icon',
				array(
					'label'       => __( 'Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
				)
			);
		}

				$this->add_control(
					'button_icon_align',
					array(
						'label'     => __( 'Icon Position', 'uael' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => 'left',
						'options'   => array(
							'left'  => __( 'Before', 'uael' ),
							'right' => __( 'After', 'uael' ),
						),
						'condition' => array(
							'button_icon[value]!' => '',
						),
					)
				);

				$this->add_control(
					'button_icon_indent',
					array(
						'label'     => __( 'Icon Spacing', 'uael' ),
						'type'      => Controls_Manager::SLIDER,
						'range'     => array(
							'px' => array(
								'max' => 50,
							),
						),
						'condition' => array(
							'button_icon[value]!' => '',
						),
						'selectors' => array(
							'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
						),
					)
				);

		$this->end_controls_section();
	}

	/**
	 * Register Login Form additional Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_additional_options_controls() {

		$show_reg_condition = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'     => 'show_lost_password',
					'operator' => '==',
					'value'    => 'yes',
				),
			),
		);

		$this->start_controls_section(
			'section_additional_options',
			array(
				'label' => __( 'Additional Options', 'uael' ),
			)
		);

			$this->add_control(
				'redirect_after_login',
				array(
					'label'     => __( 'Redirect After Login', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => '',
					'label_off' => __( 'Off', 'uael' ),
					'label_on'  => __( 'On', 'uael' ),
				)
			);

			$this->add_control(
				'redirect_url',
				array(
					'type'          => Controls_Manager::URL,
					'show_label'    => false,
					'show_external' => false,
					'separator'     => false,
					'placeholder'   => __( 'https://your-link.com', 'uael' ),
					'description'   => __( 'Note: For security reasons, you can ONLY use your current domain here.', 'uael' ),
					'condition'     => array(
						'redirect_after_login' => 'yes',
					),
					'separator'     => 'after',
				)
			);

			$this->add_control(
				'redirect_after_logout',
				array(
					'label'     => __( 'Redirect After Logout', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => '',
					'label_off' => __( 'Off', 'uael' ),
					'label_on'  => __( 'On', 'uael' ),
				)
			);

			$this->add_control(
				'redirect_logout_url',
				array(
					'type'          => Controls_Manager::URL,
					'show_label'    => false,
					'show_external' => false,
					'separator'     => false,
					'placeholder'   => __( 'https://your-link.com', 'uael' ),
					'description'   => __( 'Note: For security reasons, you can ONLY use your current domain here.', 'uael' ),
					'condition'     => array(
						'redirect_after_logout' => 'yes',
					),
					'separator'     => 'after',
				)
			);

		if ( get_option( 'users_can_register' ) ) {
			$this->add_control(
				'show_register',
				array(
					'label'     => __( 'Register', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
				)
			);

			$this->add_control(
				'show_register_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array(
						'active' => true,
					),
					'default'   => __( 'Register', 'uael' ),
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);

			$this->add_control(
				'show_register_select',
				array(
					'label'     => __( 'Link to', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'default' => __( 'Default WordPress Page', 'uael' ),
						'custom'  => __( 'Custom URL', 'uael' ),
					),
					'default'   => 'default',
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);

			$this->add_control(
				'show_register_url',
				array(
					'label'     => __( 'Enter URL', 'uael' ),
					'type'      => Controls_Manager::URL,
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_register_select' => 'custom',
						'show_register'        => 'yes',
					),
					'separator' => 'after',
				)
			);

			$show_reg_condition['terms'][] = array(
				'name'     => 'show_register',
				'operator' => '==',
				'value'    => 'yes',
			);
		}

			$this->add_control(
				'show_lost_password',
				array(
					'label'     => __( 'Lost your password?', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
				)
			);

			$this->add_control(
				'show_lost_password_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array(
						'active' => true,
					),
					'default'   => __( 'Lost your password?', 'uael' ),
					'condition' => array(
						'show_lost_password' => 'yes',
					),
				)
			);

			$this->add_control(
				'lost_password_select',
				array(
					'label'     => __( 'Link to', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'default' => __( 'Default WordPress Page', 'uael' ),
						'custom'  => __( 'Custom URL', 'uael' ),
					),
					'default'   => 'default',
					'condition' => array(
						'show_lost_password' => 'yes',
					),
				)
			);

			$this->add_control(
				'lost_password_url',
				array(
					'label'     => __( 'Enter URL', 'uael' ),
					'type'      => Controls_Manager::URL,
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'lost_password_select' => 'custom',
						'show_lost_password'   => 'yes',
					),
				)
			);

		if ( get_option( 'users_can_register' ) ) {
			$this->add_control(
				'footer_divider',
				array(
					'label'      => __( 'Divider', 'uael' ),
					'type'       => Controls_Manager::TEXT,
					'default'    => '|',
					'selectors'  => array(
						'{{WRAPPER}} .uael-login-form-footer a.uael-login-form-footer-link:not(:last-child) span:after' => 'content: "{{VALUE}}"; margin: 0 0.4em;',
					),
					'separator'  => 'after',
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'show_lost_password',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'show_register',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);
		}

			$this->add_control(
				'show_logged_in_message',
				array(
					'label'     => __( 'Logged in Message', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
				)
			);

			$this->add_responsive_control(
				'footer_text_align',
				array(
					'label'              => __( 'Alignment', 'uael' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => array(
						'flex-start' => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'     => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-text-align-center',
						),
						'flex-end'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'separator'          => 'before',
					'default'            => 'flex-start',
					'selectors'          => array(
						'{{WRAPPER}} .uael-login-form-footer' => 'justify-content: {{VALUE}};',
					),
					'conditions'         => $show_reg_condition,
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'footer_text_color',
				array(
					'label'      => __( 'Text Color', 'uael' ),
					'type'       => Controls_Manager::COLOR,
					'global'     => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-login-form-footer, {{WRAPPER}} .uael-login-form-footer a' => 'color: {{VALUE}};',
					),
					'conditions' => $show_reg_condition,
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'       => 'footer_text_typography',
					'selector'   => '{{WRAPPER}} .uael-login-form-footer',
					'global'     => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'conditions' => $show_reg_condition,
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Login Form docs link.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

				$this->add_control(
					'help_doc_1',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/new-login-form-widget-for-elementor/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_2',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s How to create a Google Client ID? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/create-google-client-id-for-login-form-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_3',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s How to create a Facebook App ID? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/create-facebook-app-id-for-login-form-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_4',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s Create a Login Form only with social buttons. » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/new-login-form-widget-for-elementor/#hide-custom-form-fields--login-only-with-social-buttons?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

			$this->end_controls_section();
		}
	}

	/**
	 * Register Login Form General Style Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_spacing_controls() {
		$this->start_controls_section(
			'section_spacing_fields',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'row_gap',
				array(
					'label'              => __( 'Rows Gap', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'default'            => array(
						'size' => 20,
					),
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-login-form .elementor-field-group:not( :first-child ),
						{{WRAPPER}}.uael-login-form-social-stack .elementor-field-group:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'label_spacing',
				array(
					'label'              => __( 'Label Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-field-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'          => array(
						'show_labels!'      => 'none',
						'hide_custom_form!' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'separator_top_spacing',
				array(
					'label'      => __( 'Separator Top Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-separator-parent' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0{{UNIT}};',
					),
					'conditions' => array(
						'relation'           => 'and',
						'terms'              => array(
							array(
								'terms' => array(
									array(
										'name'     => 'enable_separator',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'social_position',
										'operator' => '==',
										'value'    => 'bottom',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'hide_custom_form',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'facebook_login',
										'operator' => '==',
										'value'    => 'yes',
									),
									array(
										'name'     => 'google_login',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
						),
						'frontend_available' => true,
					),
				)
			);

			$this->add_responsive_control(
				'separator_bottom_spacing',
				array(
					'label'              => __( 'Separator Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-separator-parent' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0{{UNIT}};',
					),
					'conditions'         => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'enable_separator',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'social_position',
										'operator' => '==',
										'value'    => 'top',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'hide_custom_form',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'facebook_login',
										'operator' => '==',
										'value'    => 'yes',
									),
									array(
										'name'     => 'google_login',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'social_login_top_spacing',
				array(
					'label'              => __( 'Social Login Top Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-lf-custom-form-show.uael-login-form-social,
						{{WRAPPER}}.uael-login-form-social-stack .uael-lf-custom-form-show.uael-login-form-social' => 'margin-top: {{SIZE}}{{UNIT}};  margin-bottom: 0{{UNIT}};',

					),
					'conditions'         => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'hide_custom_form',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'social_position',
										'operator' => '===',
										'value'    => 'bottom',
									),
								),
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'facebook_login',
										'operator' => '==',
										'value'    => 'yes',
									),
									array(
										'name'     => 'google_login',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'social_login_bottom_spacing',
				array(
					'label'              => __( 'Social Login Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-lf-custom-form-show.uael-login-form-social,
						{{WRAPPER}}.uael-login-form-social-stack .uael-lf-custom-form-show.uael-login-form-social' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0{{UNIT}};',

					),
					'conditions'         => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'hide_custom_form',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'social_position',
										'operator' => '===',
										'value'    => 'top',
									),
								),
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'facebook_login',
										'operator' => '==',
										'value'    => 'yes',
									),
									array(
										'name'     => 'google_login',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'social_buttons_spacing',
				array(
					'label'              => __( 'Spacing between Social Login', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'default'            => array(
						'size' => 10,
					),
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 60,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}}.uael-login-form-social-inline .uael-login-form-social .elementor-field-group:first-child' => 'padding-right: {{SIZE}}{{UNIT}};',
						'(mobile){{WRAPPER}}.uael-lf-responsive-yes.uael-login-form-social-inline .elementor-field-group:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
						'(mobile){{WRAPPER}}.uael-lf-responsive-yes.uael-login-form-social-inline .uael-login-form-social .elementor-field-group:first-child' => 'padding-right: 0px;',
					),
					'conditions'         => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'social_layout',
								'operator' => '==',
								'value'    => 'inline',
							),
						),
					),
					'frontend_available' => true,
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Login Form Input Fields Style Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_fields_style_controls() {
		$this->start_controls_section(
			'section_form_fields_style',
			array(
				'label'     => __( 'Form Fields', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'hide_custom_form!' => 'yes',
				),
			)
		);

			$this->add_control(
				'form_label_style',
				array(
					'label'      => __( 'Label', 'uael' ),
					'type'       => Controls_Manager::HEADING,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_labels',
								'operator' => '!==',
								'value'    => 'none',
							),
							array(
								'name'     => 'show_remember_me',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'label_color',
				array(
					'label'      => __( 'Color', 'uael' ),
					'type'       => Controls_Manager::COLOR,
					'global'     => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'default'    => '',
					'selectors'  => array(
						'{{WRAPPER}} .elementor-field-label, {{WRAPPER}} .uael-login-form-remember, {{WRAPPER}} .uael-logged-in-message' => 'color: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_labels',
								'operator' => '!==',
								'value'    => 'none',
							),
							array(
								'name'     => 'show_remember_me',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'       => 'label_typo',
					'global'     => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'   => '{{WRAPPER}} .elementor-field-label, {{WRAPPER}} .uael-loginform-error, {{WRAPPER}} .uael-logged-in-message',
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_labels',
								'operator' => '!==',
								'value'    => 'none',
							),
							array(
								'name'     => 'show_remember_me',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label'     => __( 'Remember Me Typography', 'uael' ),
					'name'      => 'rememberme_typo',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'  => '{{WRAPPER}} .uael-login-form-remember',
					'condition' => array(
						'show_remember_me'  => 'yes',
						'hide_custom_form!' => 'yes',
					),
				)
			);

			$this->add_control(
				'label_style_heading',
				array(
					'type'       => Controls_Manager::DIVIDER,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_labels',
								'operator' => '!==',
								'value'    => 'none',
							),
							array(
								'name'     => 'show_remember_me',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'form_field_style',
				array(
					'label' => __( 'Input Field', 'uael' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'field_text_color',
				array(
					'label'     => __( 'Text / Placeholder Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .elementor-field, {{WRAPPER}} .elementor-field::placeholder,
						{{WRAPPER}} .uael-login-form input[type="checkbox"]:checked + span:before' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'input_bgcolor',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fafafa',
					'selectors' => array(
						'{{WRAPPER}} .elementor-field,
						{{WRAPPER}} .uael-login-form input[type="checkbox"] + span:before' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'field_typo',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .elementor-field, {{WRAPPER}} .elementor-field::placeholder',
				)
			);

			$this->add_responsive_control(
				'input_padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', 'em', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'input_border',
				array(
					'label'              => __( 'Border Width', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', 'em', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-field' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'input_border_color',
				array(
					'label'     => __( 'Border Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .elementor-field,
						{{WRAPPER}} .uael-login-form input[type="checkbox"] + span:before' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'input_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .elementor-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'fields_icon_heading',
				array(
					'label'     => __( 'Fields Icon', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'fields_icon' => 'yes',
					),
				)
			);

			$this->add_control(
				'fields_icon_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-fields-icon i' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'fields_icon' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'fields_icon_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 15,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-fields-icon i' => 'font-size: calc( {{SIZE}}{{UNIT}} / 4 );',
					),
					'condition' => array(
						'fields_icon' => 'yes',
					),
				)
			);

			$this->add_control(
				'eye_icon_heading',
				array(
					'label'     => __( 'Eye Icon', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
			$this->add_control(
				'eye_color',
				array(
					'label'     => __( 'Eye Icon Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} span.field-icon.toggle-password' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'eye_icon_size',
				array(
					'label'     => __( 'Eye Icon Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 15,
							'max' => 40,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} span.field-icon.toggle-password' => 'font-size: calc( {{SIZE}}{{UNIT}} / 2 );',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Register Login Form Social button style Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_social_style_controls() {
		$this->start_controls_section(
			'section_social_style',
			array(
				'label'      => __( 'Social Login', 'uael' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'facebook_login',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'google_login',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

			$this->add_responsive_control(
				'social_align',
				array(
					'label'      => __( 'Alignment', 'uael' ),
					'type'       => Controls_Manager::CHOOSE,
					'options'    => array(
						'flex-start' => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'     => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-text-align-center',
						),
						'flex-end'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'default'    => 'center',
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-login-form-social,
						{{WRAPPER}}.uael-login-form-social-stack .uael-login-form-social .elementor-field-group,
						{{WRAPPER}}.uael-lf-responsive-yes .uael-login-form-social .elementor-field-group' => 'justify-content: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'social_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uaelFacebookContentWrapper,
						{{WRAPPER}}.uael-lf-social-theme-light .uaelGoogleContentWrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-lf-social-theme-dark .uaelGoogleButtonIcon' => 'border-radius: {{TOP}}{{UNIT}} 0{{UNIT}} 0{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-lf-social-theme-dark .uael-google-text' => 'border-radius: 0{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0{{UNIT}};',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'facebook_login',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'google_login',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'social_box_shadow',
					'selector' => '{{WRAPPER}} .uaelFacebookContentWrapper, {{WRAPPER}} .uaelGoogleContentWrapper',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Login Form button style Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_button_style_controls() {

		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Button', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'hide_custom_form!' => 'yes',
				),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'button_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector' => '{{WRAPPER}} .elementor-button, {{WRAPPER}} .elementor-button svg',
				)
			);

		$this->add_control(
			'button_top_spacing',
			array(
				'label'      => __( 'Top Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-login-form .elementor-field-group.elementor-button-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'inline_control!' => 'inline',
				),
			)
		);

			$this->add_responsive_control(
				'button_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( '_button_style' );

				$this->start_controls_tab(
					'_button_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'button_text_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
								'{{WRAPPER}} .elementor-button svg' => 'fill: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'button_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'button_border',
							'label'    => __( 'Border', 'uael' ),
							'selector' => '{{WRAPPER}} .elementor-button',
						)
					);

					$this->add_control(
						'button_border_radius',
						array(
							'label'      => __( 'Border Radius', 'uael' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%' ),
							'selectors'  => array(
								'{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'     => 'button_box_shadow',
							'selector' => '{{WRAPPER}} .elementor-button',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'button_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'button_hover_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .elementor-button:hover' => 'color: {{VALUE}};',
								'{{WRAPPER}} .elementor-button:hover svg' => 'fill: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'button_background_hover_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} .elementor-button:hover' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'label'    => __( 'Box Shadow', 'uael' ),
							'name'     => 'button_box_hover_shadow',
							'selector' => '{{WRAPPER}} .elementor-button:hover',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Login Form field validation error message style Controls.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function register_validation_controls() {

		$this->start_controls_section(
			'section_fields_validate_style',
			array(
				'label'     => __( 'Field Validation Message', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'hide_custom_form!' => 'yes',
				),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'validation_message_typo',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-loginform-error',
				)
			);

			$this->add_control(
				'validation_message_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#d9534f',
					'selectors' => array(
						'{{WRAPPER}} .uael-loginform-error' => 'color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Display Separator.
	 *
	 * @since 1.20.0
	 * @access public
	 * @param string $position for separator position.
	 * @param object $settings for settings.
	 */
	public function render_separator( $position, $settings ) {
		if ( 'yes' === $settings['enable_separator'] ) {
			?>
			<div class="uael-module-content uael-separator-parent uael-lf-separator-<?php echo esc_attr( $position ); ?>">
				<div class="uael-separator-wrap">
					<div class="uael-separator-line uael-side-left">
						<span></span>
					</div>
					<div class="uael-divider-content">
						<?php
						if ( '' !== $settings['separator_line_text'] ) {
							echo '<span class="uael-divider-text elementor-inline-editing" data-elementor-setting-key="separator_line_text" data-elementor-inline-editing-toolbar="basic">' . wp_kses_post( $settings['separator_line_text'] ) . '</span>';
						}
						?>
					</div>
					<div class="uael-separator-line uael-side-right">
						<span></span>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Display Social Login.
	 *
	 * @since 1.20.0
	 * @access public
	 * @param string  $position for separator position.
	 * @param boolean $is_hidden for hidden custom form.
	 * @param string  $hide_custom_class for CSS class.
	 * @param object  $settings for settings.
	 * @param string  $google_clientid for Google Client ID.
	 * @param string  $facebook_appid for Facebook App ID.
	 * @param string  $facebook_secret for Facebook App Secret.
	 * @param boolean $is_editor for is Elementor Editor.
	 * @param string  $node_id for Current widget ID.
	 */
	public function render_social_login( $position, $is_hidden, $hide_custom_class, $settings, $google_clientid, $facebook_appid, $facebook_secret, $is_editor, $node_id ) {

		$is_google_valid = ( 'yes' === $settings['google_login'] && '' !== $google_clientid );
		$is_fb_valid     = ( 'yes' === $settings['facebook_login'] && '' !== $facebook_appid && '' !== $facebook_secret );

		if ( $is_google_valid || $is_fb_valid ) {
			if ( ! $is_hidden && 'bottom' === $position ) {
				$this->render_separator( 'bottom', $settings );
			}
			?>
			<div class="uael-login-form-social-wrapper uael-lf-social-<?php echo esc_attr( $position ); ?>" data-send-email="<?php echo esc_attr( $settings['send_email'] ); ?>">
				<div class="uael-login-form-social <?php echo esc_attr( $hide_custom_class ); ?>">
					<?php
					if ( $is_google_valid ) {
						$google_string   = __( 'Google', 'uael' );
						$google_filters  = apply_filters( 'uael_login_form_google_button', $google_string );
						$google_scope_id = 'uael-google-login-' . $node_id;
						?>
						<div class="elementor-field-group uael-login-form-google">
							<div class="uaelGoogleContentWrapper" id="<?php echo esc_attr( $google_scope_id ); ?>" data-clientid="<?php echo esc_attr( $google_clientid ); ?>">
								<div class="uaelGoogleButtonIcon">
									<div class="uaelGoogleButtonIconImage">
										<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="uaelGoogleButtonSvg"><g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path><path fill="none" d="M0 0h48v48H0z"></path></g></svg>
									</div>
								</div>
								<span class="uael-google-text"><?php echo wp_kses_post( $google_filters ); ?></span>
							</div>
						</div>
					<?php } ?>

					<?php
					if ( $is_fb_valid ) {
						$facebook_string  = __( 'Facebook', 'uael' );
						$facebook_filters = apply_filters( 'uael_login_form_facebook_button', $facebook_string );
						?>
						<div class="elementor-field-group uael-login-form-facebook">
							<div class="uaelFacebookContentWrapper" id="uael-fbLink" data-appid="<?php echo esc_attr( $facebook_appid ); ?>">
								<div class="uaelFacebookButtonIcon">
									<div class="uaelFacebookButtonIconImage">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 216 216" class="_5h0m"><path d="M204.1 0H11.9C5.3 0 0 5.3 0 11.9v192.2c0 6.6 5.3 11.9 11.9 11.9h103.5v-83.6H87.2V99.8h28.1v-24c0-27.9 17-43.1 41.9-43.1 11.9 0 22.2.9 25.2 1.3v29.2h-17.3c-13.5 0-16.2 6.4-16.2 15.9v20.8h32.3l-4.2 32.6h-28V216h55c6.6 0 11.9-5.3 11.9-11.9V11.9C216 5.3 210.7 0 204.1 0z"></path></svg>
									</div>
								</div>
								<span class="uael-facebook-text"><?php echo wp_kses_post( $facebook_filters ); ?></span>
							</div>
						</div>
					<?php } ?>
					<div class="status"></div>
				</div>
			</div>

			<?php if ( ! $is_fb_valid && ( 'yes' === $settings['facebook_login'] ) && $is_editor ) { ?>
				<div class="uael-login-form-alert elementor-alert elementor-alert-warning">
					<?php
					/* translators: %s: Error String */
					echo esc_html__( 'Please configure Facebook App settings correctly from Dashboard -> Settings -> UAE -> Login Form - Facebook App Details.', 'uael' );
					?>
				</div>
			<?php } ?>

			<?php
			if ( ! $is_hidden && 'top' === $position ) {
				$this->render_separator( 'top', $settings );
			}
		}
	}

	/**
	 * Render Login-Form output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.20.0
	 * @access protected
	 */
	protected function render() {

		$settings        = $this->get_settings_for_display();
		$node_id         = $this->get_id();
		$is_hidden       = false;
		$logout_redirect = '';
		$is_editor       = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$google_clientid = '';
		$facebook_appid  = '';

		$invalid_username = '';
		$invalid_password = '';
		$session_error    = isset( $_SESSION['uael_error'] ) ? $_SESSION['uael_error'] : ''; // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.session___SESSION
		$session_id       = session_id(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.session_session_id

		if ( ! empty( $session_id ) ) {
			if ( isset( $_SESSION['uael_error'] ) ) {
				if ( isset( $session_error ) ) {
					if ( 'invalid_username' === $session_error ) {
						$invalid_username = __( 'Unknown Username. Check again or try your email address.', 'uael' );
					} elseif ( 'invalid_email' === $session_error ) {
						$invalid_username = __( 'Unknown Email address. Check again or try your username.', 'uael' );
					} elseif ( 'incorrect_password' === $session_error ) {
						$invalid_password = __( 'Error: The Password you have entered is incorrect.', 'uael' );
					}
					unset( $_SESSION['uael_error'] ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.session___SESSION
				}
			}
		}

		$hide_custom_class = 'uael-lf-custom-form-show';
		if ( 'yes' === $settings['hide_custom_form'] && ( 'yes' === $settings['google_login'] || 'yes' === $settings['facebook_login'] ) ) {
			$is_hidden         = true;
			$hide_custom_class = 'uael-lf-custom-form-hidden';
		}

		$user_placeholder_text     = __( 'Username or Email Address', 'uael' );
		$password_placeholder_text = __( 'Password', 'uael' );

		$user_placeholder = ( 'custom' === $settings['show_labels'] ) ? wp_kses_post( $settings['user_placeholder'] ) : $user_placeholder_text;

		$pass_placeholder = ( 'custom' === $settings['show_labels'] ) ? wp_kses_post( $settings['password_placeholder'] ) : $password_placeholder_text;

		$this->add_render_attribute(
			array(
				'wrapper'         => array(
					'class' => array(
						'elementor-form-fields-wrapper',
					),
				),
				'uael_login_wrap' => array(
					'class' => array(
						'uael-login-form-wrapper',
					),
				),
				'field-group'     => array(
					'class' => array(
						'elementor-field-type-text',
						'elementor-field-group',
						'elementor-column',
						'elementor-col-100',
					),
				),
				'submit-group'    => array(
					'class' => array(
						'elementor-field-group',
						'elementor-column',
						'elementor-button-wrapper',
						'elementor-field-type-submit',
						'elementor-col-100',
					),
				),

				'button'          => array(
					'class'            => array(
						'elementor-button',
						'uael-login-form-submit',
					),
					'name'             => 'uael-login-submit',
					'data-ajax-enable' => $settings['enable_ajax'],
				),
				'user_input'      => array(
					'type'        => 'text',
					'name'        => 'username',
					'id'          => 'user',
					'placeholder' => ( 'yes' === $settings['show_placeholder'] ) ? $user_placeholder : '',
					'class'       => array(
						'elementor-field',
						'elementor-field-textual',
						'elementor-size-' . $settings['input_size'],
						'uael-login-form-username',
					),
				),
				'password_input'  => array(
					'type'        => 'password',
					'name'        => 'password',
					'id'          => 'password',
					'placeholder' => ( 'yes' === $settings['show_placeholder'] ) ? $pass_placeholder : '',
					'class'       => array(
						'elementor-field',
						'elementor-field-textual',
						'elementor-size-' . $settings['input_size'],
						'uael-login-form-password',
					),
				),
				// TODO: add unique ID.
				'user_label'      => array(
					'for'   => 'user',
					'class' => 'elementor-field-label',
				),

				'password_label'  => array(
					'for'   => 'password',
					'class' => 'elementor-field-label',
				),
			)
		);

		if ( ! empty( $settings['button_icon'] ) ) {
			$this->add_render_attribute(
				'icon-align',
				'class',
				array(
					empty( $settings['button_icon_align'] ) ? '' :
							'elementor-align-icon-' . $settings['button_icon_align'],
					'elementor-button-icon',
				)
			);
			$this->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
		}

		if ( ! empty( $settings['button_size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['button_size'] );
		}

		if ( 'none' === $settings['show_labels'] ) {
			$this->add_render_attribute( 'label', 'class', 'elementor-screen-only' );
		}

		$this->add_render_attribute( 'field-group', 'class', 'elementor-field-required' )
			->add_render_attribute( 'input', 'required', true )
			->add_render_attribute( 'input', 'aria-required', 'true' );

			$this->add_render_attribute( 'uael_login_wrap', 'data-nonce', esc_attr( wp_create_nonce( 'uael-login-form-nonce' ) ) );

		if ( 'yes' === $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) {
			$this->add_render_attribute( 'uael_login_wrap', 'data-redirect-url', $settings['redirect_url']['url'] );
		}

		if ( 'yes' === $settings['redirect_after_logout'] && ! empty( $settings['redirect_logout_url']['url'] ) ) {
			$logout_redirect = $settings['redirect_logout_url']['url'];
		}

		if ( is_user_logged_in() && ! $is_editor ) {
			if ( 'yes' === $settings['show_logged_in_message'] ) {
				$current_user = wp_get_current_user();
				?>
				<div class="uael-logged-in-message">
				<?php
				$user_name   = $current_user->display_name;
				$a_tag       = '<a href="' . esc_url( wp_logout_url( $logout_redirect ) ) . '">';
				$close_a_tag = '</a>';
				/* translators: %1$s user name */
				printf( esc_html__( 'You are Logged in as %1$s (%2$sLogout%3$s)', 'uael' ), wp_kses_post( $user_name ), wp_kses_post( $a_tag ), wp_kses_post( $close_a_tag ) );
				?>
				</div>
				<?php
			}
			return;
		}

		if ( 'yes' === $settings['google_login'] || 'yes' === $settings['facebook_login'] ) {
			$integration_settings = UAEL_Helper::get_integrations_options();
			$google_clientid      = $integration_settings['google_client_id'];
			$facebook_appid       = $integration_settings['facebook_app_id'];
			$facebook_secret      = $integration_settings['facebook_app_secret'];
		}

		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_login_wrap' ) ); ?>>
			<?php
			if ( 'top' === $settings['social_position'] ) {
				$this->render_social_login( 'top', $is_hidden, $hide_custom_class, $settings, $google_clientid, $facebook_appid, $facebook_secret, $is_editor, $node_id );
			}
			?>
			<?php
			if ( ! $is_hidden ) {
				?>
				<form class="uael-form uael-login-form" method="post">
					<?php if ( 'yes' === $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) { ?>
						<input type="hidden" name="redirect_to" value=<?php echo esc_url( $settings['redirect_url']['url'] ); ?>>
					<?php } ?>

					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'field-group' ) ); ?>>
							<?php
							if ( 'custom' === $settings['show_labels'] && '' !== $settings['user_label'] ) {
								echo '<label ' . wp_kses_post( $this->get_render_attribute_string( 'user_label' ) ) . '>' . wp_kses_post( $settings['user_label'] ) . '</label>';
							} elseif ( 'default' === $settings['show_labels'] ) {
								echo '<label ' . wp_kses_post( $this->get_render_attribute_string( 'user_label' ) ) . '>';
								echo esc_attr__( 'Username or Email Address', 'uael' );
								echo '</label>';
							}
							if ( 'yes' === $settings['fields_icon'] ) {
								echo '<div class="uael-username-wrapper">';
							}
							echo '<input size="1" ' . wp_kses_post( $this->get_render_attribute_string( 'user_input' ) ) . '>';
							if ( 'yes' === $settings['fields_icon'] ) {
								echo '<span class="uael-fields-icon"><i class="fa fa-user"></i></span>';
								echo '</div>';
							}
							?>
							<?php if ( '' !== $invalid_username ) { ?>
								<span class="uael-register-field-message"><span class="uael-loginform-error"><?php echo wp_kses_post( $invalid_username ); ?></span></span>
							<?php } ?>
						</div>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'field-group' ) ); ?>>
							<?php
							if ( 'custom' === $settings['show_labels'] && '' !== $settings['password_label'] ) {
								echo '<label ' . wp_kses_post( $this->get_render_attribute_string( 'password_label' ) ) . '>' . wp_kses_post( $settings['password_label'] ) . '</label>';
							} elseif ( 'default' === $settings['show_labels'] ) {
								echo '<label ' . wp_kses_post( $this->get_render_attribute_string( 'password_label' ) ) . '>';
								echo esc_attr__( 'Password', 'uael' );
								echo '</label>';
							}
							echo '<div class="uael-password-wrapper">';
							echo '<input size="1" ' . wp_kses_post( $this->get_render_attribute_string( 'password_input' ) ) . '>';
							if ( 'yes' === $settings['fields_icon'] ) {
								echo '<span class="uael-fields-icon"><i class="fa fa-lock"></i></span>';
							}
							echo '<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>';
							echo '</div>';
							?>
							<?php if ( '' !== $invalid_password ) { ?>
								<span class="uael-register-field-message"><span class="uael-loginform-error"><?php echo wp_kses_post( $invalid_password ); ?></span></span>
							<?php } ?>
						</div>

						<?php if ( 'yes' === $settings['show_remember_me'] ) { ?>
							<?php $remember_me_text = apply_filters( 'uael_login_remember_me', __( 'Remember Me', 'uael' ) ); ?>
							<div class="elementor-field-type-checkbox elementor-field-group elementor-column elementor-col-100 elementor-remember-me">
								<label for="uael-login-remember-me">
									<input type="checkbox" id="uael-login-remember-me" class="uael-login-form-remember" name="rememberme" value="forever">
									<span class="uael-login-form-remember"><?php echo esc_html( $remember_me_text ); ?></span>
								</label>
							</div>
						<?php } ?>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'submit-group' ) ); ?>>
							<button type="submit" <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
								<?php if ( ( ! empty( $settings['button_icon'] ) && ! UAEL_Helper::is_elementor_updated() ) || ( '' !== $settings['button_icon']['value'] && UAEL_Helper::is_elementor_updated() ) ) { ?>
									<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-wrapper' ) ); ?>>
										<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align' ) ); ?>>
											<?php
											if ( $settings['button_icon']['value'] && UAEL_Helper::is_elementor_updated() ) {
												\Elementor\Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) );
											} elseif ( ! empty( $settings['button_icon'] ) && ! UAEL_Helper::is_elementor_updated() ) {
												?>
												<i class="<?php echo esc_attr( $settings['button_icon'] ); ?>" aria-hidden="true"></i>
											<?php } ?>
										</span>
								<?php } ?>
								<?php if ( ! empty( $settings['button_text'] ) ) { ?>
									<span class="elementor-button-text"><?php echo wp_kses_post( $settings['button_text'] ); ?></span>
								<?php } ?>
								<?php if ( ( ! empty( $settings['button_icon'] ) && ! UAEL_Helper::is_elementor_updated() ) || ( '' !== $settings['button_icon']['value'] && UAEL_Helper::is_elementor_updated() ) ) { ?>
									</span>
								<?php } ?>
							</button>
							<?php
							if ( 'yes' !== $settings['enable_ajax'] ) {
								wp_nonce_field( 'uael-login', 'uael-login-nonce' );
							}
							?>
						</div>

						<?php
						$show_lost_password = 'yes' === $settings['show_lost_password'];
						$show_register      = get_option( 'users_can_register' ) && 'yes' === $settings['show_register'];

						if ( $show_lost_password || $show_register ) :
							?>
							<div class="elementor-field-group elementor-column elementor-col-100 uael-login-form-footer">
								<?php
								if ( $show_register ) :
									$register_url = wp_registration_url();
									$this->add_render_attribute( 'register_var', 'class', 'uael-login-form-footer-link' );

									if ( 'custom' === $settings['show_register_select'] && ! empty( $settings['show_register_url'] ) ) {
										$this->add_render_attribute( 'register_var', 'href', $settings['show_register_url']['url'] );

										if ( $settings['show_register_url']['is_external'] ) {
											$this->add_render_attribute( 'register_var', 'target', '_blank' );
										}

										if ( $settings['show_register_url']['nofollow'] ) {
											$this->add_render_attribute( 'register_var', 'rel', 'nofollow' );
										}
									} else {
										$this->add_render_attribute( 'register_var', 'href', $register_url );
									}
									?>
									<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'register_var' ) ); ?>>
										<span class="elementor-inline-editing" data-elementor-setting-key="show_register_text" data-elementor-inline-editing-toolbar="basic"><?php echo wp_kses_post( $settings['show_register_text'] ); ?></span>
									</a>
								<?php endif; ?>

								<?php
								if ( $show_lost_password ) :
									$lost_pass_url = wp_lostpassword_url();
									$this->add_render_attribute( 'lost_pass', 'class', 'uael-login-form-footer-link' );

									if ( 'custom' === $settings['lost_password_select'] && ! empty( $settings['lost_password_url'] ) ) {
										$this->add_render_attribute( 'lost_pass', 'href', $settings['lost_password_url']['url'] );

										if ( $settings['lost_password_url']['is_external'] ) {
											$this->add_render_attribute( 'lost_pass', 'target', '_blank' );
										}

										if ( $settings['lost_password_url']['nofollow'] ) {
											$this->add_render_attribute( 'lost_pass', 'rel', 'nofollow' );
										}
									} else {
										$this->add_render_attribute( 'lost_pass', 'href', $lost_pass_url );
									}
									?>
									<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'lost_pass' ) ); ?>>
										<span class="elementor-inline-editing" data-elementor-setting-key="show_lost_password_text" data-elementor-inline-editing-toolbar="basic"><?php echo wp_kses_post( $settings['show_lost_password_text'] ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</div>
				</form>
			<?php } ?>
			<?php
			if ( 'bottom' === $settings['social_position'] || $is_hidden ) {
				$this->render_social_login( 'bottom', $is_hidden, $hide_custom_class, $settings, $google_clientid, $facebook_appid, $facebook_secret, $is_editor, $node_id );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render Login Form widgets output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var is_hidden = false;
			var hide_custom_class = 'uael-lf-custom-form-show';

			if ( 'yes' === settings.hide_custom_form && ( 'yes' === settings.google_login || 'yes' === settings.facebook_login ) ) {
				is_hidden = true;
				hide_custom_class = 'uael-lf-custom-form-hidden';
			}
		#>

		<?php
		$google_clientid = '';
		$facebook_appid  = '';
		?>
		<# if( 'yes' === settings.google_login || 'yes' === settings.facebook_login ) { #>
			<?php
				$integration_settings = UAEL_Helper::get_integrations_options();
				$google_clientid      = $integration_settings['google_client_id'];
				$facebook_appid       = $integration_settings['facebook_app_id'];
				$facebook_app_secret  = $integration_settings['facebook_app_secret'];
			?>
		<# } #>

		<#
			var is_google_valid = ( 'yes' === settings.google_login );
			var is_fb_valid = ( 'yes' === settings.facebook_login );
			var position = settings.social_position;
		#>

		<#
		function render_separator() {
			if ( 'yes' === settings.enable_separator ) {
			#>
				<div class="uael-module-content uael-separator-parent uael-lf-separator-{{ position }}">
					<div class="uael-separator-wrap">
						<div class="uael-separator-line uael-side-left">
							<span></span>
						</div>
						<div class="uael-divider-content">
							<# if ( '' !== settings.separator_line_text ) { #>
								<span class="uael-divider-text elementor-inline-editing" data-elementor-setting-key="separator_line_text" data-elementor-inline-editing-toolbar="basic">{{ settings.separator_line_text }}</span>
							<# } #>
						</div>
						<div class="uael-separator-line uael-side-right">
							<span></span>
						</div>
					</div>
				</div>
			<#
			}
		}
		#>

		<# function render_social_login() { #>
			<# if ( is_google_valid || is_fb_valid ) { #>
				<#
				if( ! is_hidden && 'bottom' === position ) {
					render_separator();
				}
				#>
				<div class="uael-login-form-social-wrapper uael-lf-social-{{ position }}">
					<div class="uael-login-form-social {{ hide_custom_class }}">
						<# if ( is_google_valid ) { #>
							<?php if ( '' !== $google_clientid ) { ?>
								<#
									var google_string = 'Google';
								#>
								<div class="elementor-field-group uael-login-form-google">
									<div class="uaelGoogleContentWrapper" id="uael-google-login" data-clientid="<?php echo esc_attr( $google_clientid ); ?>">
										<div class="uaelGoogleButtonIcon">
											<div class="uaelGoogleButtonIconImage">
												<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="uaelGoogleButtonSvg"><g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path><path fill="none" d="M0 0h48v48H0z"></path></g></svg>
											</div>
										</div>
										<span class="uael-google-text">{{ google_string }}</span>
									</div>
								</div>
							<?php } ?>
						<# } #>

						<# if ( is_fb_valid ) { #>
							<?php if ( '' !== $facebook_appid && '' !== $facebook_app_secret ) { ?>
								<#
									var facebook_string = 'Facebook';
								#>
								<div class="elementor-field-group uael-login-form-facebook">
									<div class="uaelFacebookContentWrapper" id="uael-fbLink" data-appid="<?php echo esc_attr( $facebook_appid ); ?>">
										<div class="uaelFacebookButtonIcon">
											<div class="uaelFacebookButtonIconImage">
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 216 216" class="_5h0m"><path d="M204.1 0H11.9C5.3 0 0 5.3 0 11.9v192.2c0 6.6 5.3 11.9 11.9 11.9h103.5v-83.6H87.2V99.8h28.1v-24c0-27.9 17-43.1 41.9-43.1 11.9 0 22.2.9 25.2 1.3v29.2h-17.3c-13.5 0-16.2 6.4-16.2 15.9v20.8h32.3l-4.2 32.6h-28V216h55c6.6 0 11.9-5.3 11.9-11.9V11.9C216 5.3 210.7 0 204.1 0z"></path></svg>
											</div>
										</div>
										<span class="uael-facebook-text">{{ facebook_string }}</span>
									</div>
								</div>
							<?php } ?>
							<div class="status"></div>
						<# } #>
					</div>
				</div>

				<# if ( ! is_fb_valid && ( 'yes' === settings.facebook_login ) ) { #>
					<div class="elementor-alert elementor-alert-warning">
						<?php
						/* translators: %s: Error String */
						echo esc_attr_e( 'Please configure Facebook App settings correctly from Dashboard -> Settings -> UAE -> Login Form - Facebook App Details.', 'uael' );
						?>
					</div>
				<# } #>

				<#
				if( ! is_hidden && 'top' === position ) {
					render_separator();
				}
				#>
			<# } #>
		<# } #>

		<div class="uael-login-form-wrapper">
			<# if( 'top' === position ) {
				render_social_login();
			} #>
			<# if( ! is_hidden ) { #>
				<div class="uael-login-form uael-form">
					<div class="elementor-form-fields-wrapper">
						<#
							fieldGroupClasses = 'elementor-field-type-text elementor-field-group elementor-column elementor-col-100';
							var user_placeholder = ( 'yes' == settings.show_placeholder ) ? settings.user_placeholder : '';
							var pass_placeholder = ( 'yes' == settings.show_placeholder ) ? settings.password_placeholder : '';
						#>

						<div class="{{ fieldGroupClasses }}">
							<# if ( 'custom' === settings.show_labels && '' !== settings.user_label ) { #>
								<label class="elementor-field-label" for="user"> {{ settings.user_label }} </label>
							<# } else if ( 'default' === settings.show_labels ) { #>
								<label class="elementor-field-label" for="user">
									<?php echo esc_attr_e( 'Username or Email Address', 'uael' ); ?>
								</label>
							<# } #>


							<# if( 'yes' === settings.fields_icon ) { #>
								<div class="uael-username-wrapper">
							<# } #>

							<# if ( 'custom' === settings.show_labels ) { #>

								<input size="1" type="text" id="user" placeholder="{{ user_placeholder }}" class="uael-login-form-username elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" />

							<# } else if ( 'default' === settings.show_labels || 'none' === settings.show_labels ) { #>

								<# if ( 'yes' == settings.show_placeholder ) { #>

									<input size="1" type="text" id="user" placeholder="<?php echo esc_attr_e( 'Username or Email Address', 'uael' ); ?>" class="uael-login-form-username elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" />

								<# } else { #>

									<input size="1" type="text" id="user" class="uael-login-form-username elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" />

								<# } #>

							<# } #>

							<# if( 'yes' === settings.fields_icon ) { #>
								<span class="uael-fields-icon"><i class="fa fa-user"></i></span>
								</div>
							<# } #>


						</div>

						<div class="{{ fieldGroupClasses }}">
							<# if ( 'custom' === settings.show_labels && '' !== settings.password_label ) { #>
								<label class="elementor-field-label" for="password"> {{ settings.password_label }} </label>
							<# } else if ( 'default' === settings.show_labels ) { #>
								<label class="elementor-field-label" for="password">
									<?php echo esc_attr_e( 'Password', 'uael' ); ?>
								</label>
							<# } #>
							<div class="uael-password-wrapper">

								<# if ( 'custom' === settings.show_labels ) { #>

									<input size="1" type="password" id="password" placeholder="{{ pass_placeholder }}" class="uael-login-form-password elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" />

								<# } else if ( 'default' === settings.show_labels || 'none' === settings.show_labels ) { #>

									<# if ( 'yes' == settings.show_placeholder ) { #>

										<input size="1" type="password" id="password" placeholder="<?php echo esc_attr_e( 'Password', 'uael' ); ?>" class="uael-login-form-password elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" />

									<# } else { #>

										<input size="1" type="password" id="password" class="uael-login-form-password elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" />

									<# } #>

								<# } #>

							<# if( 'yes' === settings.fields_icon ) { #>
								<span class="uael-fields-icon"><i class="fa fa-lock"></i></span>
							<# } #>
							<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
							</div>
						</div>

						<# if ( settings.show_remember_me ) { #>
							<div class="elementor-field-type-checkbox elementor-field-group elementor-column elementor-col-100 elementor-remember-me">
								<label for="uael-login-remember-me">
									<input type="checkbox" id="uael-login-remember-me" class="uael-login-form-remember" name="rememberme" value="forever">
									<span class="uael-login-form-remember"><?php echo esc_html__( 'Remember Me', 'uael' ); ?></span>
								</label>
							</div>
						<# } #>

						<div class="elementor-field-group elementor-button-wrapper elementor-column elementor-field-type-submit elementor-col-100">
							<button type="submit" class="uael-login-form-submit elementor-button elementor-size-{{ settings.button_size }}" data-ajax-enable="{{ settings.enable_ajax }}">
								<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
									<# if ( settings.button_icon || settings.button_icon ) { #>
										<span class="elementor-button-content-wrapper">
											<span class="elementor-button-icon elementor-align-icon-{{ settings.button_icon_align }}">
												<# var iconHTML = elementor.helpers.renderIcon( view, settings.button_icon, { 'aria-hidden': true }, 'i' , 'object' );
												migrated = elementor.helpers.isIconMigrated( settings, 'button_icon' );
												#>
												<# if ( iconHTML && iconHTML.rendered && ( settings.button_icon || migrated ) ) { #>
													{{{ iconHTML.value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
												<# } else if( ! settings.button_icon ) { #>
													<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
												<# } #>
											</span>
									<# } #>
								<?php } ?>
									<# if ( settings.button_text ) { #>
										<span class="elementor-button-text">{{ settings.button_text }}</span>
									<# } #>
									<# if ( settings.button_icon || settings.button_icon ) { #>
										</span>
									<#  } #>
							</button>
						</div>

						<# if ( settings.show_lost_password || settings.show_register ) { #>
							<div class="uael-login-form-footer elementor-field-group elementor-column elementor-col-100">
								<?php if ( get_option( 'users_can_register' ) ) { ?>
									<# if ( settings.show_register ) { #>
											<a class="uael-login-form-footer-link" href="{{ settings.show_register_url.url }}">
										<# if ( 'custom' === settings.show_register_select ) { #>
										<# } else { #>
											<a class="uael-login-form-footer-link" href="<?php echo esc_url( wp_registration_url() ); ?>">
										<# } #>
											<span class="elementor-inline-editing" data-elementor-setting-key="show_register_text" data-elementor-inline-editing-toolbar="basic">{{ settings.show_register_text }}</span>
										</a>
									<# } #>
								<?php } ?>

								<# if ( settings.show_lost_password ) { #>
									<# if ( 'custom' === settings.lost_password_select ) { #>
										<a class="uael-login-form-footer-link" href="{{ settings.lost_password_url.url }}">
									<# } else { #>
										<a class="uael-login-form-footer-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
									<# } #>
										<span class="elementor-inline-editing" data-elementor-setting-key="show_lost_password_text" data-elementor-inline-editing-toolbar="basic">{{ settings.show_lost_password_text }}</span>
									</a>
								<# } #>

							</div>
						<# } #>
					</div>
				</div>
			<# } #>
			<# if( 'bottom' === position || is_hidden ) {
				render_social_login();
			} #>
			</div>
		<?php
	}
}
