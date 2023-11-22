<?php

namespace ElementPack\Modules\UserLogin\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

use ElementPack\Modules\UserLogin\Skins;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class User_Login extends Module_Base
{

	public function get_name()
	{
		return 'bdt-user-login';
	}

	public function get_title()
	{
		return BDTEP . esc_html__('User Login', 'bdthemes-element-pack');
	}

	public function get_icon()
	{
		return 'bdt-wi-user-login';
	}

	public function get_categories()
	{
		return ['element-pack'];
	}

	public function get_keywords()
	{
		return ['user', 'login', 'form'];
	}

	public function get_style_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-user-login'];
		}
	}

	public function get_script_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['recaptcha', 'ep-google-login', 'ep-scripts'];
		} else {
			return ['recaptcha', 'ep-google-login', 'ep-user-login'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/JLdKfv_-R6c';
	}

	protected function register_skins()
	{
		$this->add_skin(new Skins\Skin_Dropdown($this));
		$this->add_skin(new Skins\Skin_Modal($this));
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'section_forms_layout',
			[
				'label' => esc_html__('Forms Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'labels_title',
			[
				'label'     => esc_html__('Labels', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label'   => esc_html__('Label', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'fields_title',
			[
				'label' => esc_html__('Fields', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'input_size',
			[
				'label'   => esc_html__('Input Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'small'   => esc_html__('Small', 'bdthemes-element-pack'),
					'default' => esc_html__('Default', 'bdthemes-element-pack'),
					'large'   => esc_html__('Large', 'bdthemes-element-pack'),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'button_title',
			[
				'label'     => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__('Log In', 'bdthemes-element-pack'),
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'   => esc_html__('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'small' => esc_html__('Small', 'bdthemes-element-pack'),
					''      => esc_html__('Default', 'bdthemes-element-pack'),
					'large' => esc_html__('Large', 'bdthemes-element-pack'),
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'start'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'end'     => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'stretch' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-button-align-',
				'default'      => '',
				'condition'    => [
					'_skin!' => ['bdt-modal'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_custom_nav',
			[
				'label'     => esc_html__('Dropdown Content', 'bdthemes-element-pack'),
				'condition' => [
					'_skin' => ['bdt-dropdown', 'bdt-modal'],
				],
			]
		);

		$this->start_controls_tabs('tabs_logged');

		$this->start_controls_tab(
			'tab_button_logged',
			[
				'label' => esc_html__('Logged In', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'header_heading',
			[
				'label'     => __('Header', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'show_domain_link',
			[
				'label'     => esc_html__('Show Domain Link', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'hr_7',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label'     => __('Content', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'custom_nav_title',
			[
				'name'    => '',
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__('Title', 'bdthemes-element-pack'),
				'dynamic' => ['active' => true],
			]
		);

		$repeater->add_control(
			'custom_nav_icon',
			[
				'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			]
		);

		$repeater->add_control(
			'custom_nav_link',
			[
				'label'   => esc_html__('Link', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::URL,
				'default' => ['url' => '#'],
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'custom_navs',
			[
				'label'   => esc_html__('Dropdown Menus', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'custom_nav_title' => esc_html__('Billing', 'bdthemes-element-pack'),
						'custom_nav_icon'  => ['value' => 'fas fa-dollar-sign', 'library' => 'fa-solid'],
						'custom_nav_link'  => [
							'url' => '#',
						]
					],
					[
						'custom_nav_title' => esc_html__('Settings', 'bdthemes-element-pack'),
						'custom_nav_icon'  => ['value' => 'fas fa-cog', 'library' => 'fa-solid'],
						'custom_nav_link'  => [
							'url' => '#',
						]
					],
					[
						'custom_nav_title' => esc_html__('Support', 'bdthemes-element-pack'),
						'custom_nav_icon'  => ['value' => 'far fa-life-ring', 'library' => 'fa-regular'],
						'custom_nav_link'  => [
							'url' => '#',
						]
					],
				],

				'title_field' => '{{{ custom_nav_title }}}',
			]
		);

		$this->add_control(
			'hr_8',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_edit_profile',
			[
				'label'   => __('Edit Profile', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_logged_out',
			[
				'label' => esc_html__('Logged Out', 'bdthemes-element-pack'),
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_forms_additional_options',
			[
				'label' => esc_html__('Additional Options', 'bdthemes-element-pack'),
			]
		);

		$this->start_controls_tabs('tabs_additional_options');

		$this->start_controls_tab(
			'tab_additional_options_logged_out',
			[
				'label' => esc_html__('Logged Out', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'redirect_after_logOut',
			[
				'label' => esc_html__('Redirect After Log Out', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'redirect_logOut_url',
			[
				'type'          => Controls_Manager::URL,
				'show_label'    => false,
				'show_external' => false,
				'separator'     => false,
				'placeholder'   => 'http://your-link.com/',
				'description'   => esc_html__('Note: Because of security reasons, you can ONLY use your current domain here.', 'bdthemes-element-pack'),
				'condition'     => [
					'redirect_after_logOut' => 'yes',
				],
				'dynamic'       => ['active' => true],
			]
		);

		$this->add_control(
			'hr_3',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'redirect_after_login' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_lost_password',
			[
				'label'   => esc_html__('Lost Password link', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'custom_lost_password',
			[
				'label'     => esc_html__('Custom Lost Password URL', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lost_password' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_lost_password_url',
			[
				'type'          => Controls_Manager::URL,
				'show_label'    => false,
				'show_external' => false,
				'separator'     => false,
				'placeholder'   => 'http://your-link.com/',
				'condition'     => [
					'custom_lost_password' => 'yes',
				],
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		if (get_option('users_can_register')) {
			$this->add_control(
				'show_register',
				[
					'label'   => esc_html__('Register Link', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'custom_register',
				[
					'label'     => esc_html__('Custom Register URL', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'show_register' => 'yes',
					],
				]
			);

			$this->add_control(
				'custom_register_url',
				[
					'type'          => Controls_Manager::URL,
					'show_label'    => false,
					'show_external' => false,
					'separator'     => false,
					'placeholder'   => 'http://your-link.com/',
					'condition'     => [
						'custom_register' => 'yes',
					],
				]
			);
		}

		$this->add_control(
			'hr_2',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_remember_me',
			[
				'label'   => esc_html__('Remember Me Checkbox', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'logout_text',
			[
				'label'     => esc_html__('Logout Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Logout', 'bdthemes-element-pack'),
				'dynamic'   => ['active' => true],
				'condition' => [
					'show_logged_in_content' => '',
					'_skin!'                 => ''
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'show_logged_in_content' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_additional_options_logged_in',
			[
				'label' => esc_html__('Logged In', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'redirect_after_login',
			[
				'label' => esc_html__('Redirect After Login', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'type'          => Controls_Manager::URL,
				'show_label'    => false,
				'show_external' => false,
				'separator'     => false,
				'placeholder'   => 'http://your-link.com/',
				'description'   => esc_html__('Note: Because of security reasons, you can ONLY use your current domain here.', 'bdthemes-element-pack'),
				'condition'     => [
					'redirect_after_login' => 'yes',
				],
				'dynamic'       => ['active' => true],
			]
		);

		$this->add_control(
			'show_logged_in_content',
			[
				'label'   => esc_html__('Logged in Content', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_logged_in_message',
			[
				'label'     => esc_html__('Welcome Message', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_logged_in_content' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_user_name',
			[
				'label'     => esc_html__('Show User Name', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_logged_in_content' => 'yes',
					'_skin!'                 => '',
				],
			]
		);

		$this->add_control(
			'show_avatar_in_button',
			[
				'label'       => esc_html__('Avatar in Button', 'bdthemes-element-pack'),
				'description' => esc_html__('When user logged in this avatar shown in dropdown/modal button.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => [
					'_skin!' => '',
					'show_avatar_icon_in_button!' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_avatar_icon_in_button',
			[
				'label'       => esc_html__('Avatar Icon in Button', 'bdthemes-element-pack') . BDTEP_NC,
				'description' => esc_html__('When user logged in this icon shown in dropdown/modal button.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => [
					'_skin!' => '',
				],
			]
		);

		$this->add_control(
			'avatar_icon',
			[
				'label'            => esc_html__('Choose Avatar Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'type'             => Controls_Manager::ICONS,
				'condition'   => [
					'_skin!' => '',
					'show_avatar_icon_in_button' => 'yes',
				],
				'default' => [
					'value' => 'fas fa-level-down-alt',
					'library' => 'fa-solid',
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'show_facebook_login',
			[
				'label'     => esc_html__('Show Facebook Login', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_google_login',
			[
				'label' => esc_html__('Show Google Login', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label'      => esc_html__('Show Separator', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SWITCHER,
				'default'    => 'yes',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_facebook_login',
							'value' => 'yes',
						],
						[
							'name'  => 'show_google_login',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'show_recaptcha_checker',
			[
				'label'        => esc_html__('reCAPTCHA Enable', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-show-recaptcha-badge-',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'hide_recaptcha_badge',
			[
				'label'   => esc_html__('Hide reCAPTCHA Bagde', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-hide-recaptcha-badge-',
				'condition' => [
					'show_recaptcha_checker' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_labels',
			[
				'label'     => esc_html__('Custom Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'user_label',
			[
				'label'     => esc_html__('Username Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Username or Email', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'   => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'user_placeholder',
			[
				'label'     => esc_html__('Username Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Username or Email', 'bdthemes-element-pack'),
				'condition' => [
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'password_label',
			[
				'label'     => esc_html__('Password Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Password', 'bdthemes-element-pack'),
				'condition' => [
					'show_labels'   => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'password_placeholder',
			[
				'label'     => esc_html__('Password Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Password', 'bdthemes-element-pack'),
				'condition' => [
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_password_text',
			[
				'label'     => esc_html__('Lost Password Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Lost Password?', 'bethemes-element-pack'),
				'condition' => [
					'show_lost_password' => 'yes',
					'custom_labels'      => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_register_text',
			[
				'label'     => esc_html__('Register Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Register', 'bethemes-element-pack'),
				'condition' => [
					'show_register' => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_remember_text',
			[
				'label'     => esc_html__('Remember Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Remember Me', 'bethemes-element-pack'),
				'condition' => [
					'show_remember_me' => 'yes',
					'custom_labels'    => 'yes',
				],
			]
		);

		$this->add_control(
			'logged_in_custom_message',
			[
				'label'     => esc_html__('Welcome Message', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Hey,', 'bdthemes-element-pack'),
				'condition' => [
					'show_logged_in_message' => 'yes',
					'custom_labels'          => 'yes',
					'show_logged_in_content' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_additional_options_dropdown',
			[
				'label' => esc_html__('Dropdown', 'bdthemes-element-pack'),
				'condition' => [
					'_skin' => ['bdt-dropdown'],
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_width',
			[
				'label'     => esc_html__('Dropdown Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '400',
				],
				'range'     => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'separator' => 'before',
				'condition' => [
					'_skin' => ['bdt-dropdown'],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login-skin-dropdown .bdt-dropdown' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'dropdown_offset',
			[
				'label'     => esc_html__('Dropdown Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				//'separator' => 'before',
				'condition' => [
					'_skin' => ['bdt-dropdown', 'bdt-modal'],
				],
			]
		);

		$this->add_control(
			'dropdown_position',
			[
				'label'     => esc_html__('Dropdown Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-right',
				'options'   => element_pack_drop_position(),
				'condition' => [
					'_skin' => ['bdt-dropdown', 'bdt-modal'],
				],
			]
		);

		$this->add_control(
			'dropdown_mode',
			[
				'label'     => esc_html__('Dropdown Mode', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hover',
				'options'   => [
					'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
					'click' => esc_html__('Clicked', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => ['bdt-dropdown', 'bdt-modal'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//style Modal Button
		$this->start_controls_section(
			'section_style_modal_button',
			[
				'label'     => esc_html__('Modal Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-modal'
				]
			]
		);

		$this->start_controls_tabs('tabs_modal_button_style');

		$this->start_controls_tab(
			'tab_modal_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'modal_button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_button_typography',
				'selector' => '{{WRAPPER}} .bdt-button-modal',
			]
		);

		$this->add_control(
			'modal_button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'modal_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-button-modal',
			]
		);

		$this->add_control(
			'modal_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-modal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'modal_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-modal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_modal_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'modal_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_button_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-modal:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'modal_button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'modal_button_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_control(
			'modal_avatar_size',
			[
				'label'   => esc_html__('Avatar Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 24,
				],
				'range'     => [
					'px' => [
						'min' => 8,
						'max' => 32,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login-button-avatar img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Form Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Rows Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '15',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'checkbox_color',
			[
				'label'     => esc_html__('Checkbox Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-form-stacked .bdt-field-group .bdt-checkbox' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'checkbox_active_color',
			[
				'label'     => esc_html__('Checkbox Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-form-stacked .bdt-field-group .bdt-checkbox:checked' => 'background-color: {{VALUE}};',
				],
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
					'#bdt-user-login{{ID}} .bdt-field-group > a'                              => 'color: {{VALUE}};',
					'#bdt-user-login{{ID}} .bdt-user-login-password a:not(:last-child):after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'links_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'links_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'  => '#bdt-user-login{{ID}} .bdt-field-group > a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			[
				'label'      => esc_html__('Form Label', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_labels',
							'value' => 'yes'
						],
						[
							'name'  => 'show_remember_me',
							'value' => 'yes'
						],
					]
				]
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
					'#bdt-user-login{{ID}} .bdt-field-group > label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-form-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '#bdt-user-login{{ID}} .bdt-form-label',
			]
		);

		$this->end_controls_section();

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
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input::placeholder'      => 'color: {{VALUE}};',
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input' => 'background-color: {{VALUE}};',
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
				'selector'    => '#bdt-user-login{{ID}} .bdt-field-group .bdt-input',
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
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '#bdt-user-login{{ID}} .bdt-field-group .bdt-input',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '#bdt-user-login{{ID}} .bdt-field-group .bdt-input',
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
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color_focus',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input:focus::placeholder'      => 'color: {{VALUE}};',
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input:focus::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color_focus',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input:focus' => 'background-color: {{VALUE}};',
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
					'#bdt-user-login{{ID}} .bdt-field-group .bdt-input:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

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
					'#bdt-user-login{{ID}} .bdt-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#bdt-user-login{{ID}} .bdt-button',
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
					'#bdt-user-login{{ID}} .bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'#bdt-user-login{{ID}} .bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-user-login{{ID}} .bdt-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '#bdt-user-login{{ID}} .bdt-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '#bdt-user-login{{ID}} .bdt-button',
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
					'#bdt-user-login{{ID}} .bdt-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-user-login{{ID}} .bdt-button:hover' => 'border-color: {{VALUE}};',
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
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '#bdt-user-login{{ID}} .bdt-button:hover',
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//style Dropdown Button
		$this->start_controls_section(
			'section_style_dropdown_button',
			[
				'label'     => esc_html__('Dropdown Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-dropdown'
				]
			]
		);

		$this->start_controls_tabs('tabs_dropdown_button_style');

		$this->start_controls_tab(
			'tab_dropdown_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dropdown_button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-button-dropdown svg'     => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_button_typography',
				'selector' => '{{WRAPPER}} .bdt-button-dropdown',
			]
		);

		$this->add_control(
			'hr_10',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'dropdown_button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dropdown_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-button-dropdown',
			]
		);

		$this->add_responsive_control(
			'dropdown_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-button-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_avatar_size',
			[
				'label'     => esc_html__('Avatar Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 8,
						'max' => 32,
					],
				],
				'default' => [
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login-button-avatar img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_button_icon_typography',
				'label'     => __('Icon Typography', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-button-dropdown-icon',
				'condition' => [
					'bdt_dropdown_user_login_dropdown_icon[value]!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dropdown_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-button-dropdown:hover svg'     => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_button_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'dropdown_button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'dropdown_button_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_style',
			[
				'label'     => esc_html__('Dropdown Content', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => ['bdt-dropdown', 'bdt-modal'],
				],
			]
		);

		$this->add_control(
			'dropdown_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dropdown_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-user-login .bdt-dropdown',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown, {{WRAPPER}} .bdt-user-login .bdt-dropdown .bdt-user-card-small' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown .bdt-user-card-small'                                            => 'margin-top: -{{TOP}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dropdown_link_color',
			[
				'label'     => esc_html__('Link Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown a' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'dropdown_link_hover_color',
			[
				'label'     => esc_html__('Link Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-dropdown a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hr_5',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'     => __('Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'  => '{{WRAPPER}} .bdt-user-login .bdt-dropdown-nav li a',
			]
		);

		$this->add_control(
			'hr_6',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label'     => __('Icon', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'  => '{{WRAPPER}} .bdt-user-login .bdt-dropdown-nav .bdt-ul-custom-nav-icon',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logged_style',
			[
				'label'     => esc_html__('Logged In Style', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin!' => ['bdt-dropdown', 'bdt-modal'],
				],
			]
		);

		$this->add_control(
			'looged_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'looged_link_color',
			[
				'label'     => esc_html__('Link Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'looged_link_hover_color',
			[
				'label'     => esc_html__('Link Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logout_button_style',
			[
				'label'     => esc_html__('Logout Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_logged_in_content' => '',
					'_skin!'                 => ''
				],
			]
		);

		$this->start_controls_tabs('tabs_logout_button_style');

		$this->start_controls_tab(
			'tab_logout_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'logout_button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'logout_button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'logout_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-user-login .bdt-logout-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'logout_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logout_button_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'logout_button_typography',
				'selector' => '{{WRAPPER}} .bdt-user-login .bdt-logout-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_logout_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'logout_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'logout_button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'logout_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-user-login .bdt-logout-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'logout_button_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	public function form_fields_render_attributes()
	{
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		if (!empty($settings['button_size'])) {
			$this->add_render_attribute('button', 'class', 'bdt-button-' . $settings['button_size']);
		}

		if ($settings['button_hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		$this->add_render_attribute(
			[
				'wrapper'      => [
					'class' => [
						'elementor-form-fields-wrapper',
					],
				],
				'field-group'  => [
					'class' => [
						'bdt-field-group',
						'bdt-width-1-1',
					],
				],
				'submit-group' => [
					'class' => [
						'elementor-field-type-submit',
						'bdt-field-group',
						'bdt-flex',
					],
				],

				'button'         => [
					'class' => [
						'elementor-button',
						'bdt-button',
						'bdt-button-primary',
					],
					'name'  => 'wp-submit',
				],
				'user_label'     => [
					'for'   => 'user' . esc_attr($id),
					'class' => [
						'bdt-form-label',
					]
				],
				'password_label' => [
					'for'   => 'password' . esc_attr($id),
					'class' => [
						'bdt-form-label',
					]
				],
				'user_input'     => [
					'type'        => 'text',
					'name'        => 'user_login',
					'id'          => 'user' . esc_attr($id),
					'placeholder' => ($settings['user_placeholder']) ? $settings['user_placeholder'] : 'Username or Email',
					'class'       => [
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
				'password_input' => [
					'type'        => 'password',
					'name'        => 'user_password',
					'id'          => 'password' . esc_attr($id),
					'placeholder' => ($settings['password_placeholder']) ? $settings['password_placeholder'] : 'Password',
					'class'       => [
						'bdt-input',
						'bdt-form-' . $settings['input_size'],
					],
				],
			]
		);

		if (!$settings['show_labels']) {
			$this->add_render_attribute('label', 'class', 'elementor-screen-only');
		}

		$this->add_render_attribute('field-group', 'class', 'elementor-field-required')
			->add_render_attribute('input', 'required', true)
			->add_render_attribute('input', 'aria-required', 'true');
	}

	public function render_loop_custom_nav_list($list)
	{

		$this->add_render_attribute('custom-nav-item', 'title', $list["custom_nav_title"], true);
		$this->add_render_attribute('custom-nav-item', 'href', $list['custom_nav_link']['url'], true);

		if ($list['custom_nav_link']['is_external']) {
			$this->add_render_attribute('custom-nav-item', 'target', '_blank', true);
		}

		if ($list['custom_nav_link']['nofollow']) {
			$this->add_render_attribute('custom-nav-item', 'rel', 'nofollow', true);
		}

		if (!isset($list['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$list['icon'] = 'fas fa-user';
		}

		$migrated = isset($list['__fa4_migrated']['custom_nav_icon']);
		$is_new   = empty($list['icon']) && Icons_Manager::is_migration_allowed();

?>
		<li class="bdt-user-login-custom-item">
			<a <?php
				echo $this->get_render_attribute_string('custom-nav-item'); ?>>
				<?php
				if ($list['custom_nav_icon']['value']) : ?>

					<span class="bdt-ul-custom-nav-icon">

						<?php
						if ($is_new || $migrated) :
							Icons_Manager::render_icon($list['custom_nav_icon'], [
								'aria-hidden' => 'true',
								'class'       => 'fa-fw'
							]);
						else : ?>
							<i class="<?php
										echo esc_attr($list['icon']); ?>" aria-hidden="true"></i>
						<?php
						endif; ?>

					</span>

				<?php
				endif; ?>

				<?php
				echo wp_kses($list['custom_nav_title'], element_pack_allow_tags('title')); ?>
			</a>
		</li>
	<?php
	}

	public function user_dropdown_menu()
	{
		$settings        = $this->get_settings_for_display();
		$current_user    = wp_get_current_user();
		$dropdown_offset = $settings['dropdown_offset'];
		$current_url     = remove_query_arg('fake_arg');

		$this->add_render_attribute(
			[
				'dropdown-settings' => [
					'data-bdt-dropdown' => [
						wp_json_encode(array_filter([
							"mode"   => $settings["dropdown_mode"],
							"pos"    => $settings["dropdown_position"],
							"offset" => $dropdown_offset["size"]
						]))
					]
				]
			]
		);

		$this->add_render_attribute('dropdown-settings', 'class', 'bdt-dropdown bdt-drop bdt-text-left bdt-overflow-hidden');

	?>

		<div <?php
				echo $this->get_render_attribute_string('dropdown-settings'); ?>>
			<div class="bdt-user-card-small">
				<div class="bdt-grid-small bdt-flex-middle" data-bdt-grid>
					<div class="bdt-width-auto">
						<?php
						echo get_avatar($current_user->user_email, 48); ?>
					</div>
					<div class="bdt-width-expand">
						<div class="bdt-card-title"><?php
													echo esc_html($current_user->display_name); ?></div>

						<?php if ('yes' == $settings["show_domain_link"]) : ?>
							<p class="bdt-text-meta bdt-margin-remove-top">
								<a href="<?php
											echo esc_url($current_user->user_url); ?>" target="_blank">
									<?php
									echo esc_url($current_user->user_url); ?>
								</a>
							</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<ul class="bdt-nav bdt-dropdown-nav">
				<?php
				if ($settings['show_edit_profile']) : ?>
					<li><a href="<?php
									echo get_edit_user_link(); ?>"><span class="bdt-ul-custom-nav-icon"><i class="ep-icon-edit fa-fw"></i></span> <?php
																																					esc_html_e('Edit Profile', 'bdthemes-element-pack'); ?>
						</a></li>
				<?php
				endif; ?>

				<?php
				foreach ($settings['custom_navs'] as $key => $nav) :
					$this->render_loop_custom_nav_list($nav);
				endforeach;
				?>
				<?php
				$logout_url = $current_url;
				if (
					isset($settings['redirect_after_logOut'])
					&& !empty($settings['redirect_logOut_url']['url'])
				) {
					$logout_url = $settings['redirect_logOut_url']['url'];
				}
				?>

				<li class="bdt-nav-divider"></li>

				<li>
					<a href="<?php
								echo wp_logout_url($logout_url); ?>" class="bdt-ul-logout-menu"><span class="bdt-ul-custom-nav-icon"><i class="ep-icon-lock fa-fw"></i></span> <?php
																																												esc_html_e('Logout', 'bdthemes-element-pack'); ?>
					</a>
				</li>
			</ul>
		</div>

		<?php
	}

	public function render()
	{
		$settings    = $this->get_settings_for_display();
		$current_url = remove_query_arg('fake_arg');

		if (is_user_logged_in() && !Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			if ($settings['show_logged_in_content']) {
				$current_user = wp_get_current_user();

		?>
				<div class="bdt-user-login bdt-text-center">
					<ul class="bdt-list bdt-list-divider">
						<li class="bdt-user-avatar bdt-margin-large-bottom">
							<?php
							echo get_avatar($current_user->user_email, 128); ?>
						</li>
						<li>
							<span class="bdt-user-name">
								<?php
								if ($settings['show_logged_in_message']) : ?>
									<?php
									if ($settings['logged_in_custom_message'] and $settings['custom_labels']) : ?>
										<?php
										echo esc_html($settings['logged_in_custom_message']); ?>
									<?php
									else : ?>
										<?php
										esc_html_e('Hi', 'bdthemes-element-pack'); ?>,
									<?php
									endif; ?>
								<?php
								endif; ?>
								<a href="<?php
											echo get_edit_user_link(); ?>"><?php
																			echo esc_html($current_user->display_name); ?></a>
							</span>
						</li>


						<li class="bdt-user-website">
							<?php
							esc_html_e('Website:', 'bdthemes-element-pack'); ?> <a href="<?php
																							echo esc_url($current_user->user_url); ?>" target="_blank"><?php
																																								echo esc_url($current_user->user_url); ?></a>
						</li>

						<li class="bdt-user-bio">
							<?php
							esc_html_e('Description:', 'bdthemes-element-pack') . ' '; ?><?php
																							echo esc_html($current_user->user_description); ?>
						</li>

						<li class="bdt-user-logged-out">
							<!-- <a href="<?php
											//echo wp_logout_url($current_url); 
											?>" -->
							<?php
							$logout_url = $current_url;
							if (
								isset($settings['redirect_after_logOut'])
								&& !empty($settings['redirect_logOut_url']['url'])
							) {
								$logout_url = $settings['redirect_logOut_url']['url'];
							}
							?>
							<a href="<?php
										echo wp_logout_url($logout_url); ?>" class="bdt-button bdt-button-primary"><?php
																													esc_html_e('Logout', 'bdthemes-element-pack'); ?></a>
						</li>
					</ul>

				</div>

		<?php
			}

			return;
		}

		$this->form_fields_render_attributes();

		?>
		<div class="bdt-user-login bdt-user-login-skin-default">
			<div class="elementor-form-fields-wrapper">

				<?php
				$this->user_login_form(); ?>

				<?php
				$this->social_login(); ?>

			</div>
		</div>

	<?php

	}

	public function social_login()
	{
		$settings = $this->get_settings_for_display();

		$options   = get_option('element_pack_api_settings');
		$fb_app_id = (isset($options['facebook_app_id']) && !empty($options['facebook_app_id'])) ? sanitize_text_field($options['facebook_app_id']) : '';
		$fb_secret = (isset($options['facebook_app_secret']) && !empty($options['facebook_app_secret'])) ? sanitize_text_field($options['facebook_app_secret']) : '';

		$gl_app_id = (isset($options['google_client_id']) && !empty($options['google_client_id'])) ? sanitize_text_field($options['google_client_id']) : '';

		if ('yes' == $settings['show_facebook_login'] or 'yes' == $settings['show_google_login']) {
			if ('yes' == $settings['show_separator']) {
				$this->add_render_attribute('separator', 'class', 'bdt-separator');
			}
		}

		if ('yes' == $settings['show_facebook_login'] && 'yes' == $settings['show_google_login']) {
			$this->add_render_attribute('facebook-login', 'class', 'bdt-facebook bdt-margin-bottom-30');
		} else {
			$this->add_render_attribute('facebook-login', 'class', 'bdt-facebook');
		}

	?>
		<?php
		if ('yes' == $settings['show_facebook_login'] or 'yes' == $settings['show_google_login']) : ?>
			<div class="bdt-width-1-1 bdt-width-1-2@s bdt-position-relative">
				<span <?php
						echo $this->get_render_attribute_string('separator'); ?>>
					<div class="bdt-social-wrapper bdt-flex bdt-flex-middle">
						<div class="bdt-social-login">

							<?php
							if ('yes' == $settings['show_facebook_login']) : ?>
								<div <?php
										echo $this->get_render_attribute_string('facebook-login'); ?>>
									<a href="javascript:void(0);" data-appid="<?php
																				echo esc_attr($gl_app_id) ?>" class="fb_btn_link"><span class="bdt-facebook-icon"><i class="ep-icon-facebook fa-fw"></i></span> <?php
																																																				echo esc_html('Facebook'); ?></a>
								</div>
							<?php
							endif; ?>

							<?php
							if ((!$fb_secret or !$fb_app_id) && 'yes' == $settings['show_facebook_login']) :
								$fb_message = ($fb_secret && $fb_app_id) ? '' : 'Facebook App ID or Secret Key';
							?>

								<div class="bdt-alert-warning" data-bdt-alert>
									<a class="bdt-alert-close" data-bdt-close></a>
									<p><?php
										echo sprintf(esc_html__('Ops! %1s Missing. Please add them from: Element Pack Settings > API Settings > Social Login Access. ', 'bdthemes-element-pack'), $fb_message); ?></p>
								</div>

							<?php endif; ?>

							<?php
							if ('yes' == $settings['show_google_login']) : ?>
								<div class="bdt-google">
									<a href="javascript:void(0);" data-clientid="<?php
																					echo esc_attr($gl_app_id) ?>" id="google_btn_link"><span class="bdt-google-icon"><i class="ep-icon-google fa-fw"></i></span> <?php
																																																					echo esc_html('Google'); ?></a>
								</div>
							<?php
							endif; ?>

							<?php
							if (!$gl_app_id && 'yes' == $settings['show_google_login']) :
								$google_app_message = ($gl_app_id) ? '' : 'Google App ID';
							?>

								<div class="bdt-alert-warning" data-bdt-alert>
									<a class="bdt-alert-close" data-bdt-close></a>
									<p><?php
										echo sprintf(esc_html__('Ops! %1s Missing. Please add them from: Element Pack Settings > API Settings > Social Login Access. ', 'bdthemes-element-pack'), $google_app_message); ?></p>
								</div>

							<?php
							endif; ?>

						</div>
					</div>
				</span>
			</div>
		<?php
		endif; ?>

	<?php
	}

	public function user_login_form()
	{
		$settings    = $this->get_settings_for_display();
		$current_url = remove_query_arg('fake_arg');

		if ($settings['redirect_after_login'] && !empty($settings['redirect_url']['url'])) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}

		$id = $this->get_id();

		if ('yes' == $settings['show_facebook_login'] or 'yes' == $settings['show_google_login']) {
			$this->add_render_attribute('login-form-wrapper', 'class', 'bdt-user-login-form bdt-form-stacked bdt-width-1-1 bdt-width-1-2@s bdt-padding-50');
		} else {
			$this->add_render_attribute('login-form-wrapper', 'class', 'bdt-user-login-form bdt-form-stacked bdt-width-1-1');
		}

	?>
		<form id="bdt-user-login<?php
								echo esc_attr($id); ?>" <?php
														echo $this->get_render_attribute_string('login-form-wrapper'); ?> method="post">
			<input type="hidden" name="action" value="element_pack_ajax_login">
			<?php
			if ($settings['show_recaptcha_checker']) {
				do_action('element_pack_google_rechatcha_render', $this, 'onLoadElementPackLoginCaptcha', 'button');
			}
			?>
			<input type="hidden" class="widget_id" name="widget_id" value="<?php
																			echo esc_attr($id); ?>" />
			<input type="hidden" class="page_id" name="page_id" value="<?php
																		echo get_the_ID() ?>" />
			<input type="hidden" name="redirect_after_login" value="<?php
																	echo esc_url($redirect_url) ?>" class="redirect_after_login" />
			<div class="bdt-user-login-status"></div>
			<div <?php
					echo $this->get_render_attribute_string('field-group'); ?>>
				<?php
				if ($settings['show_labels']) {
				?>
					<label <?php
							echo $this->get_render_attribute_string('user_label'); ?>>
						<?php
						if ('yes' == $settings['custom_labels']) {
							echo $settings['user_label'];
						} else {
							echo esc_html__('Username or Email', 'bdthemes-element-pack');
						} ?>
					</label>
				<?php
				}
				echo '<div class="bdt-form-controls">';
				echo '<input ' . $this->get_render_attribute_string('user_input') . ' required>';
				echo '</div>';

				?>
			</div>

			<div <?php
					echo $this->get_render_attribute_string('field-group'); ?>>
				<?php
				if ($settings['show_labels']) : ?>

					<label <?php
							echo $this->get_render_attribute_string('password_label'); ?>>
						<?php
						if ('yes' == $settings['custom_labels']) {
							echo $settings['password_label'];
						} else {
							echo esc_html__('Password', 'bdthemes-element-pack');
						}
						?>
					</label>

				<?php
				endif;
				echo '<div class="bdt-form-controls">';
				echo '<input ' . $this->get_render_attribute_string('password_input') . ' required>';
				echo '</div>';
				?>
			</div>

			<?php
			if ($settings['show_remember_me']) : ?>
				<div class="bdt-field-group bdt-remember-me">
					<label for="remember-me-<?php
											echo esc_attr($id); ?>" class="bdt-form-label">
						<input type="checkbox" id="remember-me-<?php
																echo esc_attr($id); ?>" class="bdt-checkbox" name="rememberme" value="forever">
						<?php
						if ($settings['custom_labels']) : ?>
							<?php
							echo esc_html($settings['custom_remember_text']); ?>
						<?php
						else : ?>
							<?php
							esc_html_e('Remember Me', 'bdthemes-element-pack'); ?>
						<?php
						endif; ?>
					</label>
				</div>
			<?php
			endif; ?>

			<div <?php
					echo $this->get_render_attribute_string('submit-group'); ?>>
				<button type="submit" <?php
										echo $this->get_render_attribute_string('button'); ?>>
					<?php
					if (!empty($settings['button_text'])) : ?>
						<span><?php
								echo wp_kses($settings['button_text'], element_pack_allow_tags('title')); ?></span>
					<?php
					endif; ?>
				</button>
			</div>

			<?php
			$show_lost_password = $settings['show_lost_password'];
			$show_register      = get_option('users_can_register') && $settings['show_register'];

			if ($show_lost_password || $show_register) : ?>
				<div class="bdt-field-group bdt-width-1-1 bdt-margin-remove-bottom bdt-user-login-password">

					<?php
					if ($show_lost_password) : ?>
						<?php
						if ($settings['custom_lost_password'] and $settings['custom_lost_password_url']['url']) {
							$lost_password_url = esc_url($settings['custom_lost_password_url']['url']);
						} else {
							$lost_password_url = wp_lostpassword_url($current_url);
						}
						?>
						<a class="bdt-lost-password" href="<?php
															echo esc_url($lost_password_url); ?>">
							<?php
							if ($settings['custom_labels']) : ?>
								<?php
								echo esc_html($settings['custom_password_text']); ?>
							<?php
							else : ?>
								<?php
								esc_html_e('Lost password?', 'bdthemes-element-pack'); ?>
							<?php
							endif; ?>
						</a>
					<?php
					endif; ?>

					<?php
					if ($show_register) : ?>
						<?php
						if ($settings['custom_register'] and $settings['custom_register_url']['url']) {
							$register_url = esc_url($settings['custom_register_url']['url']);
						} else {
							$register_url = wp_registration_url();
						}
						?>
						<a class="bdt-register" href="<?php
														echo esc_url($register_url); ?>">
							<?php
							if ($settings['custom_labels']) : ?>
								<?php
								echo esc_html($settings['custom_register_text']); ?>
							<?php
							else : ?>
								<?php
								esc_html_e('Register', 'bdthemes-element-pack'); ?>
							<?php
							endif; ?>
						</a>
					<?php
					endif; ?>

				</div>
			<?php
			endif; ?>

			<?php
			wp_nonce_field('ajax-login-nonce', 'bdt-user-login-sc'); ?>

		</form>
<?php
	}
}
