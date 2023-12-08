<?php 
/*
Widget Name: WP Login Register
Description: WP Login Register
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Wp_Login_Register extends Widget_Base {
		
	public function get_name() {
		return 'tp-wp-login-register';
	}

    public function get_title() {
        return esc_html__('WP Login & Register', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-user-circle-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	public function get_keywords() {
		return ['login', 'signup', 'password', 'login header bar', 'signup header bar', 'login signup panel', 'login panel', 'signup panel' ,'forgot' , 'reset' ,'register'];
	}
	
    protected function register_controls() {	
		$this->start_controls_section(
			'section_forms_layout',
			[
				'label' => esc_html__( 'Forms Layout', 'theplus' ),
			]
		);		
		$this->add_control(
			'form_selection',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_login',
				'options' => [
					'tp_login'  => esc_html__( 'Login', 'theplus' ),
					'tp_register'  => esc_html__( 'Register', 'theplus' ),
					'tp_login_register'  => esc_html__( 'Login and Register', 'theplus' ),
					'tp_forgot_password'  => esc_html__( 'Forgot Password', 'theplus' ),					
				],
			]
		);
		
		$this->add_control(
			'_skin',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Standard Form', 'theplus' ),
					'tp-dropdown'  => esc_html__( 'Button Hover', 'theplus' ),
					'tp-modal'  => esc_html__( 'Button Click', 'theplus' ),
					'tp-popup'  => esc_html__( 'Button Popup', 'theplus' ),
				],
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
				],
			]
		);
		$this->add_control(
			'layout_start_from',
			[
				'label' => esc_html__( 'Drop Down Alignment', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp-lrfp-lyot-con-left',
				'options' => [					
					'tp-lrfp-lyot-con-left'  => esc_html__( 'Left', 'theplus' ),
					'tp-lrfp-lyot-con-right'  => esc_html__( 'Right', 'theplus' ),
					'tp-lrfp-lyot-con-center'  => esc_html__( 'Center', 'theplus' ),
				],				
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'form_selection',
							'operator' => '!==',
							'value' => 'tp_forgot_password',
						],
						[
							'name' => '_skin',
							'operator' => '!==',
							'value' => 'default',
						],
						[
						'name' => '_skin',
							'operator' => '!==',
							'value' => 'tp-popup',
						],
					],
				],
			]
		);		
		$this->add_control(
			'f_p_opt',
			[
				'label' => esc_html__( 'Password Reset Page', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'f_p_frontend'  => esc_html__( 'Custom', 'theplus' ),					
				],
				'separator' => 'before',
				'condition' => [
					'form_selection!' => 'tp_register',
				],
			]
		);
		$this->add_control(
			'f_p_opt_cst_desc',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : You can choose custom Password Reset page using this option.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'form_selection!' => 'tp_register',
					'f_p_opt' => ['f_p_frontend'],
				],	
			]
		);
		$this->add_control(
			'reset_pass_url',
			[
				'label' => esc_html__( 'Select Page', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'options'     => the_plus_get_term_options(),
				'condition' => [
					'form_selection!' => 'tp_register',
					'f_p_opt' => 'f_p_frontend',
				],
			]
		);
		$this->add_control(
			'hcp_button_align',
			[
				'label' => esc_html__( 'Button Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'unset' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					],
				'separator' => 'before',
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-user-login,{{WRAPPER}} .tp-user-register,{{WRAPPER}} .tp-lr-combo' => 'float: {{VALUE}} !important;',					
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'form_selection',
							'operator' => '!==',
							'value' => 'tp_forgot_password',
						],
						[
							'name' => '_skin',
							'operator' => '!==',
							'value' => 'default',
						],						
					],
				],
				
			]
		);
		$this->add_control(
			'form_align',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					],
				'separator' => 'before',
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf,
					{{WRAPPER}} .tp-wp-lrcf .tp-button,
					{{WRAPPER}} .tp-wp-lrcf input,{{WRAPPER}} .tp-wp-lrcf input::placeholder' => 'text-align: {{VALUE}};',					
				],
				
			]
		);
		$this->end_controls_section();
		/*form content end*/
		
		/*Login Options start*/
		$this->start_controls_section(
			'section_forms_login_additional_options',
			[
				'label' => esc_html__( 'Login Options', 'theplus' ),
				'condition' => [					
					'form_selection' => ['tp_login','tp_login_register'],					
				],
			]
		);		
		$this->add_control(
			'tab_com_login',
			[
				'label'   => esc_html__( 'Login Tab Title', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Login', 'theplus' ),
				'separator' => 'after',
				'condition' => [
					'form_selection' => 'tp_login_register',
				],
			]
		);
		
		$this->add_control(
			'show_labels',
			[
				'label'   => esc_html__( 'Labels', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',				
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
				],
			]
		);
		/*login custom label*/
		$this->add_control(
			'custom_labels',
			[
				'label'     => esc_html__( 'Custom Labels', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'show_labels' => 'yes',
				],
			]
		);
		$this->add_control(
			'user_label',
				[
				'label'     => esc_html__( 'Username', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Username or Email', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'show_labels'   => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);
		$this->add_control(
			'password_label',
			[
				'label'     => esc_html__( 'Password', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Password', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'show_labels'   => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);
		$this->add_control(
			'custom_placeholder_l',
			[
				'label'     => esc_html__( 'Custom Placeholders', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'user_placeholder',
			[
				'label'     => esc_html__( 'Username', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Username or Email', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'custom_placeholder_l' => 'yes',
				],
			]
		);
		$this->add_control(
			'password_placeholder',
			[
				'label'     => esc_html__( 'Password', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Password', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'custom_placeholder_l' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Login Button Text', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Log In', 'theplus' ),
				'separator' => 'before',
				'condition' => [
					'form_selection!' => ['tp_register','tp_forgot_password'],
				],
			]
		);
		$this->add_control(
			'show_remember_me',
			[
				'label'   => esc_html__( 'Remember Me', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'remember_me_text',
			[
				'label'   => esc_html__( 'Content', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Remember Me', 'theplus' ),				
				'condition' => [
					'show_remember_me' => 'yes',
				],
			]
		);
		/*login custom label*/
		$this->add_control(
			'show_lost_password',
			[
				'label'   => esc_html__( 'Lost your password?', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'bottom_lost_pass_text',
			[
				'label'     => esc_html__( 'Content', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Lost Password', 'theplus' ),
				'condition' => [
				'show_lost_password' => 'yes',
				],
			]
		);
		if ( get_option( 'users_can_register' ) ) {
			$this->add_control(
				'show_register',
				[
					'label'   => esc_html__( 'Register', 'theplus' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
				]
			);
		}
		$this->add_control(
			'bottom_register_text',
			[
				'label'     => esc_html__( 'Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Register', 'theplus' ),
				'condition' => [
					'show_register' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_register_opt',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'custom'  => esc_html__( 'Custom', 'theplus' ),
				],
				'condition' => [
					'show_register' => 'yes',	
				],
			]
		);
		$this->add_control(
			'show_register_opt_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
				'show_external' => false,
				'condition' => [
					'show_register' => 'yes',	
					'show_register_opt' => 'custom',
				],
			]
		);
		$this->add_control(
			'redirect_after_login',
			[
				'label' => esc_html__( 'Redirect After Login', 'theplus' ),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',				
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
				'description'   => esc_html__( 'Note: Because of security reasons, you can ONLY use your current domain here.', 'theplus' ),
				'condition'     => [
					'redirect_after_login' => 'yes',
				],
			]
		);			
		$this->end_controls_section();
		/*login option end*/
		
		/*Register Extra Options start*/
		$this->start_controls_section(
			'section_signup_extra_options',
			[
				'label' => esc_html__( 'Register Extra Options', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'tp_dis_name_field',
			[
				'label' => esc_html__( 'Name Field', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',				
			]
		);
		$this->add_control(
			'tp_dis_fname_field',
			[
				'label' => esc_html__( 'First Name Field', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',				
				'condition' => [					
					'tp_dis_name_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_dis_lname_field',
			[
				'label' => esc_html__( 'Last Name Field', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',				
				'condition' => [					
					'tp_dis_name_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_dis_username_field',
			[
				'label' => esc_html__( 'User Name Field', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_control(
			'tp_dis_pass_field',
			[
				'label' => esc_html__( 'Password Field', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'tp_dis_conf_pass_field',
			[
				'label' => esc_html__( 'Confirm Password Field', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_dis_show_pass_icon',
			[
				'label' => esc_html__( 'Show/Hide Password Toggle', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'showicon',
			[
				'label' => esc_html__( 'Show Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-eye',
					'library' => 'solid',
				],
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_show_pass_icon' => 'yes',
				],
			]
		);
		$this->add_control(
			'hideicon',
			[
				'label' => esc_html__( 'Hide Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-eye-slash',
					'library' => 'solid',
				],
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_show_pass_icon' => 'yes',
				],
			]
		);		
		$this->add_control(
			'tp_dis_pass_field_strong',
			[
				'label' => esc_html__( 'Strong Password', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_dis_pass_pattern',
			[
				'label' => esc_html__( 'Pattern', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pattern-1',
				'options' => [
					'pattern-1'  => esc_html__( 'Pattern 1', 'theplus' ),
					'pattern-2'  => esc_html__( 'Pattern 2', 'theplus' ),
					'pattern-3'  => esc_html__( 'Pattern 3', 'theplus' ),
					'pattern-4'  => esc_html__( 'Pattern 4', 'theplus' ),
					'pattern-5'  => esc_html__( 'Pattern 5', 'theplus' ),
				],
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
				],
			]
		);		
		$this->add_control(
			'pattern_desc_1',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Minimum eight characters, at least one letter, one number and one special character', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => 'pattern-1',
				],	
			]
		);
		$this->add_control(
			'pattern_desc_2',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Minimum four and Maximum eight characters, at least one numeric digit', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => 'pattern-2',
				],	
			]
		);	
		$this->add_control(
			'pattern_desc_3',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Maximum six characters, at least one letter and one number', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => 'pattern-3',
				],	
			]
		);
		$this->add_control(
			'pattern_desc_4',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Minimum eight characters long. - At least one uppercase, one lowercase, one digit OR one 1 alphanumeric', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => 'pattern-4',
				],	
			]
		);
		$this->add_control(
			'pattern_desc_5',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Minimum eight characters long. - At least one capital letter, one lowercase, one number OR special character', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => 'pattern-5',
				],	
			]
		);		
		$this->add_control(
			'tp_dis_pass_hint',
			[
				'label' => esc_html__( 'Password Hint', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
				],
			]
		);
		$this->add_control(
			'dis_pass_hint_on',
			[
				'label' => esc_html__( 'Visibility On', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pshd',
				'options' => [
					'pshd'  => esc_html__( 'Default', 'theplus' ),
					'pshf'  => esc_html__( 'Focus', 'theplus' ),
					'pshc'  => esc_html__( 'Click', 'theplus' ),
				],
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'dis_pass_hint_layout',
			[
				'label' => esc_html__( 'Hint Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'phdefault',
				'options' => [
					'phdefault'  => esc_html__( 'Default', 'theplus' ),
					'phinline'  => esc_html__( 'Inline', 'theplus' ),
				],
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'showiconh',
			[
				'label' => esc_html__( 'Click Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-info-circle',
					'library' => 'solid',
				],
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_hint' => 'yes',
					'dis_pass_hint_on' => 'pshc',
				],
			]
		);
		$this->add_control(
			'pattern_text_label_head',
			[
				'label' => esc_html__( 'Pattern Hint Label', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',					
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);	
		$this->add_control(
			'label_1_145',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Minimum eight characters', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-1','pattern-4','pattern-5'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_2_123',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 number (0-9)', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-1','pattern-2','pattern-3'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_3_13',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 letter (Aa-Zz)', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-1','pattern-3'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_4_1',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 Special Character (!@#$%^&*)', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-1'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_5_2',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Four to eight characters', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-2'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_6_3',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Minimum six characters', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-3'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_7_45',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 lowercase(a-z) & 1 uppercase(A-Z)', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-4','pattern-5'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_8_4',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 alphanumeric (1Aa-9Zz)', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-4'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'label_9_5',
			[
				'label'     => esc_html__( 'Hint Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '1 number(0-9) Or 1 special character  (!@#$%^&*)', 'theplus' ),
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_pattern' => ['pattern-5'],
					'tp_dis_pass_hint' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_dis_pass_meter',
			[
				'label' => esc_html__( 'Password Strength Meter', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',	
				'condition' => [
					'tp_dis_pass_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'display_captcha_swtch',
			[
				'label' => esc_html__( 'reCAPTCHA v3', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'description'   => esc_html__( 'Note : Add recaptcha key in The Plus Settings to activate it.', 'theplus' ),
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'tp_honeypot_opt',
			[
				'label' => esc_html__( 'Honeypot', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',			
			]
		);
		$this->add_control(
			'tp_mail_chimp_subscribe_opt',
			[
				'label' => esc_html__( 'MailChimp Subscribe', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'tp_mail_chimp_subscribe_disable',
			[
				'label' => esc_html__( 'UnCheck', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'mcl_double_opt_in',
			[
				'label' => esc_html__( 'Double Opt-In', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'mc_cst_group',
			[
				'label' => esc_html__( 'Groups', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'mc_cst_group_value',
			[
				'label' => esc_html__( 'Enter Group ID', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Display multiple Groups use separator e.g. id1 | id2 | id3', 'theplus' ),
				'description' => 'How to <a href="https://api.mailchimp.com/playground/" class="theplus-btn" target="_blank">Get Group ID?</a>',
				'dynamic' => ['active' => true,], 
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
					'mc_cst_group' => 'yes',
				]
			]
		);
		$this->add_control(
			'mc_cst_tag',
			[
				'label' => esc_html__( 'Tags', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'mc_cst_tags_value',
			[
				'label' => esc_html__( 'Enter Tag', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Display multiple Tags use separator e.g. tag1 | tag2 | tag3', 'theplus' ),  
				'dynamic' => ['active' => true,],
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
					'mc_cst_tag' => 'yes',
				]
			]
		);		
		$this->add_control(
			'tp_terms_condition_opt',
			[
				'label' => esc_html__( 'Terms & Conditions', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'r_terms_conition_label',
			[
				'label'     => esc_html__( 'Terms & Conditions', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'I agree, With Terms and Conditions.', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_terms_condition_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_cst_email_opt',
			[
				'label' => esc_html__( 'Custom Email', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);	
		$EMtitle = get_option( 'blogname' );
		/* translators: %s: Site title. */
		$subject = sprintf( __( 'Thank you for registering with %s', 'theplus' ), $EMtitle );
		
		$this->add_control(
			'tp_cst_email_subject',
			[
				'label'       => __( 'Email Subject', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $subject,
				'default'     => $subject,
				'label_block' => true,
				'render_type' => 'none',
				'condition' => [
					'tp_cst_email_opt' => 'yes',
				],
			]
		);		
		$this->add_control(
			'tp_cst_email_message',
			[
				'label' => esc_html__( 'Email Message', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => __( 'Enter Email Message', 'theplus' ),
				/* translators: %1$s: Site title. */
				'default'     => sprintf( __( 'Dear User,<br/>You have successfully created your %1$s account. Thank you for registering with us! <br/>And here\'s the password [tp_password] to log in to the account.', 'theplus' ), $EMtitle ),
				'description' => 'Fields : [tp_firstname] , [tp_lastname] , [tp_username] , [tp_email] ,[tp_password]',
				'label_block' => true,
				'render_type' => 'none',
				'condition' => [
					'tp_cst_email_opt' => 'yes',
				]
			]
		);
		$this->end_controls_section();
		/*Register Extra Options end*/
		
		/*register option start*/
		$this->start_controls_section(
			'section_forms_register_options',
			[
				'label' => esc_html__( 'Register Options', 'theplus' ),
				'condition' => [					
					'form_selection' => ['tp_register','tp_login_register'],					
				],
			]
		);		
		$this->add_control(
			'tab_com_signup',
			[
				'label'   => esc_html__( 'Register Tab Title', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Sign Up', 'theplus' ),
				'condition' => [
					'form_selection' => 'tp_login_register',
				],
			]
		);
		$this->add_control(
			'show_labels_reg',
			[
				'label'   => esc_html__( 'Labels', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
				],
			]
		);
		/*register custom label*/
		$this->add_control(
			'custom_labels_reg',
			[
				'label'     => esc_html__( 'Custom Labels', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'show_labels_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'first_name_label',
				[
				'label'     => esc_html__( 'First Name', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'First Name', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'show_labels_reg'   => 'yes',
					'custom_labels_reg' => 'yes',
				],
			]
		);		
		$this->add_control(
			'last_name_label',
				[
				'label'     => esc_html__( 'Last Name', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Last Name', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'show_labels_reg'   => 'yes',
					'custom_labels_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'user_name_label',
				[
				'label'     => esc_html__( 'User Name', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'User Name', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'show_labels_reg'   => 'yes',
					'custom_labels_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'email_label',
			[
				'label'     => esc_html__( 'Email', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Email', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'show_labels_reg' => 'yes',
					'custom_labels_reg' => 'yes',
				],
			]
		);		
		$this->add_control(
			'r_password_label',
			[
				'label'     => esc_html__( 'Password', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Password', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_dis_pass_field' => 'yes',
					'show_labels_reg' => 'yes',
					'custom_labels_reg' => 'yes',
				],
			]
		);		
		$this->add_control(
			'r_conf_password_label',
			[
				'label'     => esc_html__( 'Confirm Password', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Confirm Password', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_dis_pass_field' => 'yes',
					'show_labels_reg' => 'yes',
					'custom_labels_reg' => 'yes',
				],
			]
		);		
		$this->add_control(
			'r_mail_chimp_label',
			[
				'label'     => esc_html__( 'MailChimp', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Yes, Please subscribe me for Newsletters.', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_mail_chimp_subscribe_opt' => 'yes',					
				],
			]
		);			
		/*register custom label*/
		
		/*register custom placeholder*/
		$this->add_control(
			'custom_placeholder_reg',
			[
				'label'     => esc_html__( 'Custom Placeholders', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'first_name_placeholder',
			[
				'label'     => esc_html__( 'First Name', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'John', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'custom_placeholder_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'last_name_placeholder',
			[
				'label'     => esc_html__( 'Last Name', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Doe', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'custom_placeholder_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'user_name_placeholder',
			[
				'label'     => esc_html__( 'User Name', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'doe', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'custom_placeholder_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'email_placeholder',
			[
				'label'     => esc_html__( 'Email', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'example@email.com', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'custom_placeholder_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'r_password_placeholder',
			[
				'label'     => esc_html__( 'Password', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '****', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_dis_pass_field' => 'yes',
					'custom_placeholder_reg' => 'yes',
				],
			]
		);
		$this->add_control(
			'r_conf_password_placeholder',
			[
				'label'     => esc_html__( 'Confirm Password', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '****', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_dis_pass_field' => 'yes',
					'custom_placeholder_reg' => 'yes',
				],
			]
		);		
		/*register custom placeholder*/
		
		$this->add_control(
			'redirect_after_register',
			[
				'label' => esc_html__( 'Redirect After Register', 'theplus' ),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'redirect_url_reg',
			[
				'type'          => Controls_Manager::URL,
				'show_label'    => false,
				'show_external' => false,
				'separator'     => false,
				'placeholder'   => 'http://your-link.com/',
				'description'   => esc_html__( 'Note: Because of security reasons, you can ONLY use your current domain here.', 'theplus' ),
				'condition'     => [
					'redirect_after_register' => 'yes',
				],
			]
		);
		$this->add_control(
			'auto_loggedin',
			[
				'label' => esc_html__( 'Auto Login After Register ', 'theplus' ),
				'type'  => Controls_Manager::SWITCHER,
				'condition'     => [
					'tp_dis_pass_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_login',
			[
				'label' => esc_html__( 'Login', 'theplus' ),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'bottom_login_text',
			[
				'label'     => esc_html__( 'Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Login', 'theplus' ),
				'condition' => [
					'show_login' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_login_opt',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'custom' => esc_html__( 'Custom', 'theplus' ),
				],
				'condition'     => [
					'show_login' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_login_opt_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
				'show_external' => false,
				'condition' => [
					'show_login' => 'yes',	
					'show_login_opt' => 'custom',
				],
				]
		);
		$this->add_control(
			'login_before_text',
			[
				'label'     => esc_html__( 'Before Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Already have an account?', 'theplus' ),
				'condition' => [
					'show_login' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_logged_in_message_reg',
			[
				'label'   => esc_html__( 'My Account menu', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'show_additional_message',
			[
				'label' => esc_html__( 'Bottom Message', 'theplus' ),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'additional_message',
			[
				'label'     => esc_html__( 'Message', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Note: Your password will be generated automatically and sent to your email address.', 'theplus' ),
				'condition' => [
					'show_additional_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_text_reg',
			[
				'label'   => esc_html__( 'Register Button Text', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Register', 'theplus' ),
				'separator' => 'before',
				'condition' => [
					'form_selection!' => ['tp_login','tp_forgot_password'],
				],
			]
		);
		$this->end_controls_section();
		
		/*template select start*/
		$this->start_controls_section(
			'section_forms_layout_left_temp',
			[
				'label' => esc_html__( 'Left Side Template', 'theplus' ),				
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'form_selection','operator' => '==','value'    => 'tp_login_register',
								],
								[
									'name'     => '_skin','operator' => '!==','value'    => 'default',
								],								
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'select_template',
			[
				'label' => esc_html__( 'Left Side Template', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'separator' => 'before',
				'options'     => theplus_get_templates(),
			]
		);
		$this->end_controls_section();
		/*template select end*/
		
		/*Click/Hover/Popup Button start*/
		$this->start_controls_section(
			'section_dropdown_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,				
				'conditions'   => [
					'terms' => [						
						[
							'relation' => 'or',
							'terms'    => [
								[
									'terms' => [
										[ 'name'  => 'form_selection','operator' => '==','value' => 'tp_login' ],
										[ 'name'  => '_skin','operator' => '!=','value' => 'default', ],
									],
								],
								[
									'terms' => [
										[ 'name'  => 'form_selection','operator' => '==','value' => 'tp_register' ],
										[ 'name'  => '_skin','operator' => '!=','value' => 'default',],
									],
								],
								[
									'terms' => [
										[ 'name'  => 'form_selection','operator' => '==','value' => 'tp_login_register', ],
										[ 'name'  => '_skin','operator' => '!=','value' => 'default', ],
									],
								],
							],
						],
					],
				],				
			]
		);
		
		$this->add_control(
			'dropdown_button_text',
			[
				'label'   => esc_html__( 'Button Text', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Login/Signup', 'theplus' ),
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
					'_skin!' => 'default',
				],
			]
		);
		$this->add_control(
			'loop_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-user',
					'library' => 'solid',
				],
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
					'_skin!' => 'default',
				],
				
			]
		);
		$this->add_control(
			'modal_close_button',
			[
				'label'   => esc_html__( 'Close Icon', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [					
					'_skin!' => 'tp-dropdown',
				],
				
			]
		);
		$this->add_control(
			'modal_close_button_icon',
			[
				'label' => esc_html__( 'Choose Icon', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => THEPLUS_ASSETS_URL .'images/tp-close.png',
				],
				'condition' => [					
					'_skin!' => 'tp-dropdown',
					'modal_close_button' => 'yes',					
				],
			]
		);		
		$this->end_controls_section();
		/*Click/Hover/Popup Button end*/
		
		/*form heading start*/
		$this->start_controls_section(
			'section_forms_heading_options',
			[
				'label' => esc_html__( 'Form Heading', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'modal_header',
			[
				'label'   => esc_html__( 'Heading Content', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'modal_header_description_log',
			[
				'label' => esc_html__( 'Login Heading', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Heading Description', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'modal_header' => 'yes',
				],
			]
		);
		$this->add_control(
			'modal_header_description_reg',
			[
				'label' => esc_html__( 'Registration Heading', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Heading Description', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'modal_header' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*form heading end*/		
		/*social login start*/
		$this->start_controls_section(
			'content_social_login',
			[
				'label' => esc_html__( 'Social Login/Register', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'form_selection!' => 'tp_forgot_password',
				],
				
			]
		);		
		$this->add_control(
			'content_social_login_heading',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : You need to add App id(Facebook) and Client Id(Google) from The Plus Settings - Extra Options to make social login/register working.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
			'tp_sl_facebook',
			[
				'label' => esc_html__( 'Facebook', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control('tp_sl_layout_opt',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_sl_layout_opt_1',
				'options' => [
					'tp_sl_layout_opt_1'  => esc_html__( 'Layout 1', 'theplus' ),					
					'tp_sl_layout_opt_2' => esc_html__( 'Layout 2', 'theplus' ),
				],
				'condition' => [
					'tp_sl_facebook' => 'yes'
				]
			]
		);
		$this->add_control('tp_sl_google',
			[
				'label' => esc_html__( 'Google', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => '',
				'separator' => 'before',
			]
		);
		$this->add_control('tp_google_onetap',
			[
				'label' => esc_html__( 'Google One Tap Login', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => '',
				'separator' => 'before',
				'condition' => [
					'tp_sl_google' => 'yes',
				],
			]
		);
		$this->add_control('gl_onetap_msg',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : By enabling this option, You will be able to use Google Login`s "One Tap Popup" to make google login easy for users.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_google_onetap' => 'yes'
				],
			]
		);	
		$this->add_control('tp_sl_google_type',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard'  => esc_html__( 'Standard', 'theplus' ),					
					'icon' => esc_html__( 'Icon', 'theplus' ),
				],
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_google_onetap!' => 'yes'
				],
			]
		);

		$this->add_control('tp_sl_google_theme',
			[
				'label' => esc_html__( 'Theme', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outline',
				'options' => [
					'outline' => esc_html__( 'Light', 'theplus' ),
					'filled_blue'  => esc_html__( 'Dark Blue', 'theplus' ),					
					'filled_black' => esc_html__( 'Dark Black', 'theplus' ),
				],
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_google_onetap!' => 'yes',
				],
			]
		);
		$this->add_control('tp_sl_google_shape',
			[
				'label' => esc_html__( 'Shape', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rectangular',
				'options' => [
					'rectangular'  => esc_html__( 'Rectangular', 'theplus' ),					
					'pill' => esc_html__( 'Pill', 'theplus' ),
				],
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_google_onetap!' => 'yes',
				],
			]
		);
		$this->add_control('tp_sl_google_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'signin_with',
				'options' => [
					'signin_with'  => esc_html__( 'Sign in with Google', 'theplus' ),					
					'signup_with' => esc_html__( 'Sign up with Google', 'theplus' ),
					'continue_with' => esc_html__( 'Continue with Google', 'theplus' ),
					'signin' => esc_html__( 'Sign in', 'theplus' ),
				],
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_sl_google_type' => 'standard',
					'tp_google_onetap!' => 'yes',
				],
			]
		);
		$this->add_control('tp_sl_google_size',
			[
				'label' => esc_html__( 'Size', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => [
					'large'  => esc_html__( 'Large', 'theplus' ),					
					'medium' => esc_html__( 'Medium', 'theplus' ),
					'small' => esc_html__( 'Small', 'theplus' ),
					'custom' => esc_html__( 'Custom', 'theplus' ),
				],
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_google_onetap!' => 'yes'
				],
			]
		);
		$this->add_control('tp_sl_google_custom_width',
			[
				'label' => esc_html__( 'Width', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 5,
				'condition' => [
					'tp_sl_google' => 'yes',
					'tp_sl_google_size' => 'custom',
					'tp_google_onetap!' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_sl_google_longtitle',
			[
				'label' => esc_html__( 'Long Title', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'tp_sl_google' => 'yes',
				],
			]
		);
		$this->add_control(
			's_icon_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					],
				'separator' => 'before',
				'default' => 'flex-start',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-social-login-wrapper' => 'justify-content: {{VALUE}};',					
				],
				
			]
		);
		$this->add_control(
			'redirect_url_social_head',
			[
				'label' => esc_html__( 'Redirect URL', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'redirect_url_social',
			[
				'type'          => Controls_Manager::URL,
				'show_label'    => false,
				'show_external' => false,
				'separator'     => false,
				'placeholder'   => 'http://your-link.com/',				
			]
		);	
		$this->add_control(
			'hide_form',
			[
				'label' => esc_html__( 'Hide Form', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'form_selection' => ['tp_register','tp_login'],
					'_skin' => 'default',
				],
			]
		);
		$this->end_controls_section();
		/*social login end*/
		
		/*Password Strength Meter start*/
		$this->start_controls_section(
			'content_password_strength_meter',
			[
				'label' => esc_html__( 'Password Strength Meter', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_meter' => 'yes',
				],				
			]
		);
		$this->add_control(
			'psm_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'psm_style2_in',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-label',
				'options' => [
					'after-label'  => esc_html__( 'Default', 'theplus' ),
					'after-field'  => esc_html__( 'Bottom', 'theplus' ),
					'inline-filed'  => esc_html__( 'Inline', 'theplus' ),
				],
				'condition' => [
					'psm_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'psm_text_switch',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'psm_text',
			[
				'label'     => esc_html__( 'Label Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Password Strength : ', 'theplus' ),
				'placeholder'   => esc_html__( 'Enter Label', 'theplus' ),
				'condition' => [
					'psm_text_switch' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Password Strength Meter end*/
		
		/*Mailchimp Individual List ID & API Key start*/
		$this->start_controls_section(
			'mc_i_li_ak',
			[
				'label' => esc_html__( 'Mailchimp Individual List ID & API Key', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'tp_mail_chimp_subscribe_opt' => 'yes',
				],
				
			]
		);
		$this->add_control(
			'mc_i_li_ak_swtch',
			[
				'label' => esc_html__( 'Individual List ID & API Key', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'mc_custom_apikey',
			[
				'label'     => esc_html__( 'API Key', 'theplus' ),
				'type'      => Controls_Manager::TEXT,				
				'separator' => 'before',
				'condition' => [
					'mc_i_li_ak_swtch' => 'yes',
				],
			]
		);
		$this->add_control(
			'mc_custom_apikey_desc',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Go to your Mailchimp > Account > Extras > API Keys then create a key and paste here', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'mc_i_li_ak_swtch' => 'yes',
				],	
			]
		);
		$this->add_control(
			'mc_custom_listid',
			[
				'label'     => esc_html__( 'List ID', 'theplus' ),
				'type'      => Controls_Manager::TEXT,				
				'separator' => 'before',
				'condition' => [
					'mc_i_li_ak_swtch' => 'yes',
				],
			]
		);
		$this->add_control(
			'mc_custom_listid_desc',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Go to your Mailchimp > List > Settings > List name and default > Copy the list ID and paste here.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'mc_i_li_ak_swtch' => 'yes',
				],	
			]
		);
		$this->end_controls_section();
		/*Mailchimp Individual List ID & API Key end*/
		
		/*reset password start*/
		$this->start_controls_section(
			'content_reset_pass_section',
			[
				'label' => esc_html__( 'Reset Password', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'form_selection!' => 'tp_register',
					'f_p_opt' => 'f_p_frontend',
				],
			]
		);
		$this->add_control(
			'res_pass_label_switch',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_control(
			'res_pass_label',
			[
				'label'     => esc_html__( 'Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Reset Password', 'theplus' ),
				'condition' => [
					'res_pass_label_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'res_pass_placeholder',
			[
				'label'     => esc_html__( 'Placeholder', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '****', 'theplus' ),
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'res_conf_pass_placeholder',
			[
				'label'     => esc_html__( 'Confirm Password Placeholder', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '****', 'theplus' ),
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'reset_pass_btn',
			[
				'label'   => esc_html__( 'Password Button Text', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Reset Password', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*reset password end*/
		
		/*forgot password start*/
		$this->start_controls_section(
			'section_forms_loast_pass_options',
			[
				'label' => esc_html__( 'Lost Password Options', 'theplus' ),
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'form_selection','operator' => '==','value'    => 'tp_forgot_password',
								],
								[
									'name'     => 'show_lost_password','operator' => '==','value'    => 'yes',
								],								
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'lost_pass_label_switch',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_control(
			'lost_pass_label',
			[
				'label'     => esc_html__( 'Label', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Username/Email', 'theplus' ),
				'condition' => [
					'lost_pass_label_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'lost_pass_placeholder',
			[
				'label'     => esc_html__( 'Placeholder', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Username/Email', 'theplus' ),				
			]
		);
		$this->add_control(
			'forgot_pass_btn',
			[
				'label'   => esc_html__( 'Button Text', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Email Reset Link', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lost_password_heading_desc',
			[
				'label' => esc_html__( 'Heading', 'theplus' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'rows' => 10,
				'default' => esc_html__( 'Lost your password?', 'theplus' ),
				'placeholder' => esc_html__( 'Type your Lost password description here', 'theplus' ),
			]
		);
		
		$this->add_control(
			'tp_cst_email_lost_opt',
			[
				'label' => esc_html__( 'Custom Email', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$EMtitle1 = get_option( 'blogname' );
		/* translators: %s: Site title. */
		$subject1 = sprintf( __( 'Someone has requested a password reset for the following account from %s!', 'theplus' ), $EMtitle1 );
		
		$this->add_control(
			'tp_cst_email_lost_subject',
			[
				'label'       => __( 'Email Subject', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $subject1,
				'default'     => $subject1,
				'label_block' => true,
				'render_type' => 'none',
				'condition' => [
					'tp_cst_email_lost_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_cst_email_lost_message',
			[
				'label' => esc_html__( 'Email Message', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => __( 'Enter Email Message', 'theplus' ),
				/* translators: %1$s: Site title. */
				'default'     => sprintf( __( 'Dear [tplr_username],<br/>Someone has requested a password reset for the following %1$s account.<br/>If this was a mistake, just ignore this email and nothing will happen.<br/>To reset your password, visit the following address:<br/> [tplr_link]', 'theplus' ), $EMtitle1 ),
				'description' => 'Fields : [tplr_sitename] , [tplr_username] , [tplr_link]',
				'label_block' => true,
				'render_type' => 'none',
				'condition' => [
					'tp_cst_email_lost_opt' => 'yes',
				]
			]
		);
		$this->end_controls_section();
		/*forgot password end*/
		
		/*My Account Menu*/
		$this->start_controls_section(
			'section_forms_after_login_panel_options',
			[
				'label' => esc_html__( 'My Account Menu', 'theplus' ),
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
				],
			]
		);
		$this->add_control(
			'show_logged_in_message',
			[
				'label'   => esc_html__( 'My Account Menu', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description'   => esc_html__( 'Note : This feature will not work in backend. Only logged-in users of your website will be able to see that at frontend.', 'theplus' ),				
			]
		);		
		$this->add_control(
			'after_login_panel_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'flex-start',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf.aflp' => 'justify-content: {{VALUE}};display:flex',					
				],
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'standard_layout',
			[
				'label' => esc_html__( 'Inline Layout', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'hide_u_avtar',
			[
				'label' => esc_html__( 'User Avatar', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'hide_u_name',
			[
				'label' => esc_html__( 'User Name', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'edit_profile_text_switch',
			[
				'label' => esc_html__( 'Edit Pofile', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'edit_profile_text',
			[
				'label'   => esc_html__( 'Title', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Edit Profile', 'theplus' ),
				'condition' => [
					'show_logged_in_message' => 'yes',
					'edit_profile_text_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_text_logout_switch',
			[
				'label' => esc_html__( 'Logout', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_text_logout',
			[
				'label'   => esc_html__( 'Title', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Logout', 'theplus' ),
				'condition' => [
					'show_logged_in_message' => 'yes',
					'button_text_logout_switch' => 'yes',
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'loop_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Download', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'loop_url_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$this->add_control(
            'loop_content',
            [
				'label' => esc_html__( 'Extra Menu', 'theplus' ),
                'type' => Controls_Manager::REPEATER,               
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ loop_title }}}',
				'condition' => [
					'show_logged_in_message' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		/*my account panel end*/
		
		/*notification start*/
		$this->start_controls_section(
			'section_msg_options',
			[
				'label' => esc_html__( 'Notification Message', 'theplus' ),
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
				],
			]
		);
		$this->add_control(
			'login_msg',
			[
				'label' => esc_html__( 'Login Message Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'login_msg_loading_txt',
			[
				'label' => esc_html__( 'Loading text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Please Wait...', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'login_msg_success',
			[
				'label' => esc_html__( 'Success text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Login successful.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'login_msg_validation',
			[
				'label' => esc_html__( 'Validation text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Ops! Wrong username or password!', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'login_msg_error',
			[
				'label' => esc_html__( 'Error text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Something went wrong. Please try again.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'register_msg',
			[
				'label' => esc_html__( 'Register Message Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'reg_msg_loading',
			[
				'label' => esc_html__( 'Loading text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Please wait...', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		
		$this->add_control(
			'reg_msg_success',
			[
				'label' => esc_html__( 'Success text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Registration Successful.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'reg_msg_email_duplication',
			[
				'label' => esc_html__( 'Email Validate', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'An account already exists with this email address.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'reg_msg_error',
			[
				'label' => esc_html__( 'Error Text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Something went wrong. Please try again.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		
		$this->add_control(
			'forgot_msg',
			[
				'label' => esc_html__( 'Lost Password Message Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'fp_msg_loading',
			[
				'label' => esc_html__( 'Loading text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Please wait...', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),				
			]
		);		
		$this->add_control(
			'fp_msg_success',
			[
				'label' => esc_html__( 'Success text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Mail sent. Please check your mailbox.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),				
			]
		);
		$this->add_control(
			'fp_msg_error',
			[
				'label' => esc_html__( 'Error text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Something went wrong. Please try again.', 'theplus' ),
				'placeholder' => esc_html__( 'Type here', 'theplus' ),
			]
		);
		$this->end_controls_section();
		/*section  end*/
		
		/*Layout width option login start*/
		$this->start_controls_section(
			'log_section_layout_size_options',
			[
				'label' => esc_html__( 'Login Fields Width', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
				],
			]
		);		
		$this->add_responsive_control(
            'l_ls_user_name',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('User Name', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-l-lr-user-name' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'l_ls_password',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Password', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-l-lr-password' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'l_ls_rememberme',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Remember Me', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-remember-me' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->end_controls_section();
		/*Layout width option login end*/
		
		/*Layout width option register start*/
		$this->start_controls_section(
			'section_layout_size_options',
			[
				'label' => esc_html__( 'Register Fields Width', 'theplus' ),
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_responsive_control(
            'ls_fisrt_name',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('First Name', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-first-name' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_last_name',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Last Name', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-last-name' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_user_name',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('User Name', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-user-name' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_email_field',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Email', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-email' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_password_field',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Password', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-user-pass,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-user-conf-pass' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_mail_chimp_sub_field',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('MailChimp Subscribe', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-mail-chimp-sub' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_terms_condition_field',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Terms & Conditions', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-tac' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'ls_aditional_msg_field',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Additional Message', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked .tp-lr-f-add-msg' => 'width: {{SIZE}}%;display: inline-flex;flex-direction: column;',
				],
				'separator' => 'before',
            ]
        );
		$this->end_controls_section();
		/*Layout width option end*/	
		
		/*custom validation reg form start*/
		$this->start_controls_section(
			'section_cst_validation_options',
			[
				'label' => esc_html__( 'Validation Error Messages', 'theplus' ),	
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
				],
			]
		);
		$this->add_control(
			'cst_validation_switch',
			[
				'label' => esc_html__( 'Override Validation Error Message', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'v_efn',
			[
				'label'   => esc_html__( 'First Name', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Invalid First Name Value.', 'theplus' ),
				'condition' => [
					'cst_validation_switch' => 'yes',
					'tp_dis_name_field' => 'yes',
					'tp_dis_fname_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'v_eln',
			[
				'label'   => esc_html__( 'Last Name', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Invalid Last Name Value.', 'theplus' ),
				'condition' => [
					'cst_validation_switch' => 'yes',
					'tp_dis_name_field' => 'yes',
					'tp_dis_lname_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'v_eun',
			[
				'label'   => esc_html__( 'User Name', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter User Name', 'theplus' ),
				'condition' => [
					'cst_validation_switch' => 'yes',
					'tp_dis_username_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'v_eemail',
			[
				'label'   => esc_html__( 'Email', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Invalid Email Address.', 'theplus' ),
				'condition' => [
					'cst_validation_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'v_epass',
			[
				'label'   => esc_html__( 'Password', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Password doesn\'t pass required criteria.',
				'condition' => [
					'cst_validation_switch' => 'yes',
					'tp_dis_pass_field' => 'yes',
				],
			]
		);
		$this->add_control(
			'v_erepass',
			[
				'label'   => esc_html__( 'Confirm Password', 'theplus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter Confirm Password', 'theplus' ),
				'condition' => [
					'cst_validation_switch' => 'yes',
					'tp_dis_pass_field' => 'yes',
					'tp_dis_conf_pass_field' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*custom validation reg form end*/
		
		/*Reset Password start*/
		$this->start_controls_section(
			'section_reset_pass_options',
			[
				'label' => esc_html__( 'Reset Password Option', 'theplus' ),				
			]
		);
		$this->add_control(
			'tp_dp_reset_field_strong',
			[
				'label' => esc_html__( 'Strong Password Required', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'tp_dp_reset_field_strong_pattern',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'tp_dp_reset_field_strong' => 'yes',
				],	
			]
		);
		$this->add_control(
			'tp_convert_rest_form',
			[
				'label' => esc_html__( 'Login/Register Form Override', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',				
			]
		);
		$this->end_controls_section();
		/*Layout width option login end*/
		
		/*style start*/
		/*label style start*/
		$this->start_controls_section(
            'section_label_style',
            [
                'label' => esc_html__('Form Label', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .tp-field-group .tp-form-label,{{WRAPPER}} .tp-form-stacked-fp .tp-form-label,
				{{WRAPPER}} .tp-form-stacked-reset .tp-form-label',
			]
		);		
		$this->add_control(
            'label_color',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '{{WRAPPER}} .tp-field-group .tp-form-label,{{WRAPPER}} .tp-form-stacked-fp .tp-form-label,
				{{WRAPPER}} .tp-form-stacked-reset .tp-form-label' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->end_controls_section();
		/*label style end*/
		
		/*field style start*/
		$this->start_controls_section(
            'section_field_style',
            [
                'label' => esc_html__('Form Input Fields', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input',
			]
		);	
		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input::placeholder,
					{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input::placeholder,
					{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input::placeholder' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_responsive_control(
			'input_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}  !important;',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'input_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_input_field_style' );
		$this->start_controls_tab(
			'tab_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'input_field_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'input_field_focus_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input:focus,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input:focus,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input:focus' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input:focus,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input:focus,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'input_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'border-style: {{VALUE}} !important;',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input:focus,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input:focus,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input:focus' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input:focus,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input:focus,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_shadow_style' );
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .tp-field-group .tp-form-controls .tp-input:focus,{{WRAPPER}} .tp-form-stacked-fp .tp-ulp-input-group .tp-input:focus,{{WRAPPER}} .tp-form-stacked-reset .tp-ulp-input-group .tp-input:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*field style end*/
		
		/*button style start*/
		$this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__('Form Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'button_align',
			[
				'label' => esc_html__( 'Button Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .elementor-field-type-submit.tp-field-group' => 'text-align: {{VALUE}};',					
				],
			]
		);
		$this->add_control(
			'button_text_align',
			[
				'label' => esc_html__( 'Text Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .elementor-field-type-submit.tp-field-group .tp-button,
					{{WRAPPER}} .tp-forg-pass-form .tp-form-stacked-fp button.tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'text-align: {{VALUE}};',					
				],
			]
		);
		$this->add_responsive_control(
            'button_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,
					{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'max-width: {{SIZE}}{{UNIT}} !important',
				],
				'separator' => 'after',
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass',
			]
		);
		$this->add_responsive_control(
			'button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button:hover,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp:hover,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button:hover,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp:hover,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'button_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'button_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'border-style: {{VALUE}} !important;',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_border_style' );
		$this->start_controls_tab(
			'tab_button_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button:hover,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp:hover,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass:hover' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button:hover,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp:hover,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_button_shadow_style' );
		$this->start_controls_tab(
			'tab_button_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .tp-form-stacked .elementor-field-type-submit .tp-button:hover,{{WRAPPER}} .tp-form-stacked-fp  .tp-button-fp:hover,{{WRAPPER}} .tp-reset-pass-form .tp-form-stacked-reset button.tp-button-reset-pass:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*button style end*/
		
		/*hover/click/popup style start*/
		$this->start_controls_section(
            'section_hover_click_popup_style',
            [
                'label' => esc_html__('Hover/Click/Popup Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
					'_skin!' => 'default',
				],
            ]
        );
		$this->add_responsive_control(
            'tab_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Right Padding', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .elementor-button-content-wrapper i,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn i' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-wp-lrcf .elementor-button-content-wrapper svg,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'tab_icon_size_font',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .elementor-button-content-wrapper i,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-wp-lrcf .elementor-button-content-wrapper svg,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'tab_icon_color_n',
			[
				'label' => esc_html__( 'Normal Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .elementor-button-content-wrapper i,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-wp-lrcf .elementor-button-content-wrapper svg,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'tab_icon_color_h',
			[
				'label' => esc_html__( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf a:hover .elementor-button-content-wrapper i,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-wp-lrcf a:hover .elementor-button-content-wrapper svg,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'hcp_button_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'max-width: {{SIZE}}{{UNIT}} !important;width: {{SIZE}}{{UNIT}} !important',
				],
				'separator' => 'before',
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hcp_button_typography',
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn',
			]
		);
		$this->add_responsive_control(
			'hcp_button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'hcp_button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_hcp_button_style' );
		$this->start_controls_tab(
			'tab_hcp_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'hcp_button_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'hcp_button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_hcp_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'hcp_button_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'hcp_button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'hcp_button_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'hcp_button_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'hcp_button_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'border-style: {{VALUE}} !important;',
				],
				'condition' => [
					'hcp_button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'hcp_button_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'hcp_button_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_hcp_button_border_style' );
		$this->start_controls_tab(
			'tab_hcp_button_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'hcp_button_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'hcp_button_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'hcp_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_hcp_button_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'hcp_button_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'hcp_button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'hcp_button_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'hcp_button_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_hcp_button_shadow_style' );
		$this->start_controls_tab(
			'tab_hcp_button_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hcp_button_shadow',
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
							{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
							{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
							{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_hcp_button_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hcp_button_hover_shadow',
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
						{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
						{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
						{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
						{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
						{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*hover/click/popup style end*/
		
		/*close image option start*/
		$this->start_controls_section(
            'section_close_img_style',
            [
                'label' => esc_html__('Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'_skin' => ['tp-modal','tp-popup'],
				],
            ]
        );
		$this->add_responsive_control(
			'close_icon_size',
			[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .lr-close-custom_img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'close_icon_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .lr-close-custom_img',
			]
		);
		$this->add_responsive_control(
			'close_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .lr-close-custom_img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'close_icon_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-modal .lr-close-custom_img,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .lr-close-custom_img',
			]
		);
		
		$this->end_controls_section();
		/*close image option end*/
		
		/*form heading option start*/		
		$this->start_controls_section(
            'section_form_heading_style',
            [
                'label' => esc_html__('Heading Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );	
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_heading_typography',
				'selector' => '{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-header h2,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-popup-header h2,
					{{WRAPPER}} .tp-l-r-main-wrapper .tp-tab-content-inner.tab-signup .tp-popup-header h2,
					{{WRAPPER}} .tp-user-login tp-user-login-skin-modal .tp-modal-header h2,
					{{WRAPPER}} .tp-user-login tp-user-login-skin-popup .tp-popup-header h2,
					{{WRAPPER}} .tp-l-r-main-wrapper .tp-tab-content-inner.tab-login .tp-popup-header h2,
					{{WRAPPER}} .tp-form-stacked-fp .tp-forgot-password-label,
					{{WRAPPER}} .tp-form-stacked-fp .tp-forgot-password-label p,
					{{WRAPPER}} .tp-wp-lrcf .tp-modal-title,{{WRAPPER}} .tp-wp-lrcf .tp-modal-title p,
					{{WRAPPER}} .tp-wp-lrcf .tp-popup-header,{{WRAPPER}} .tp-wp-lrcf .tp-popup-header p',
			]
		);
		$this->add_control(
			'form_heading_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-header h2,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-popup-header h2,
					{{WRAPPER}} .tp-l-r-main-wrapper .tp-tab-content-inner.tab-signup .tp-popup-header h2,
					{{WRAPPER}} .tp-user-login tp-user-login-skin-modal .tp-modal-header h2,
					{{WRAPPER}} .tp-user-login tp-user-login-skin-popup .tp-popup-header h2,
					{{WRAPPER}} .tp-l-r-main-wrapper .tp-tab-content-inner.tab-login .tp-popup-header h2,
					{{WRAPPER}} .tp-form-stacked-fp .tp-forgot-password-label,
					{{WRAPPER}} .tp-form-stacked-fp .tp-forgot-password-label p,
					{{WRAPPER}} .tp-wp-lrcf .tp-modal-title,{{WRAPPER}} .tp-wp-lrcf .tp-modal-title p,
					{{WRAPPER}} .tp-wp-lrcf .tp-popup-header,{{WRAPPER}} .tp-wp-lrcf .tp-popup-header p' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->end_controls_section();		
		/*form heading option end*/
		
		/*register  Additional msg option start*/
		$this->start_controls_section(
            'section_form_reg_adi_msg_style',
            [
                'label' => esc_html__('Register Additional Message', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_additional_message' => 'yes',
				],
            ]
        );	
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_reg_adi_msg_typography',
				'selector' => '{{WRAPPER}} .tp-field-group .tp-register-additional-message',
			]
		);
		$this->add_control(
			'form_reg_adi_msgcolor',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .tp-register-additional-message' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->end_controls_section();		
		/*register  Additional msg option end*/
		
		/*rememberme start*/
		$this->start_controls_section(
            'section_remember_me_style',
            [
                'label' => esc_html__('Remember Me', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection' => ['tp_login','tp_login_register'],
					'show_remember_me' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'checkbox_typography',
				'selector' => '{{WRAPPER}} .tp-field-group.tp-remember-me .tp-form-label .remember-me-label',
			]
		);
		$this->add_control(
			'checked_txt_color',
			[
				'label'     => esc_html__( 'Remember Me color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-group.tp-remember-me .tp-form-label .remember-me-label' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-group.tp-remember-me [type="checkbox"]:checked + label:before,
					{{WRAPPER}} .tp-field-group.tp-remember-me [type="checkbox"]:not(:checked) + label:before' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'checked_field_bgcolor',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-group.tp-remember-me [type="checkbox"]:checked + label:after,
					{{WRAPPER}} .tp-field-group.tp-remember-me [type="checkbox"]:not(:checked) + label:after' => 'background: {{VALUE}};',
				],
			]
		);		
		$this->end_controls_section();
		/*remember me end*/
		
		/*show/hide password start*/		
		$this->start_controls_section(
            'section_shp_style',
            [
                'label' => esc_html__('Show Hide Password Toggle', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_dis_show_pass_icon' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'sphsize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'sphcolor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'sphtopoffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Top Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'sphrightffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Right Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show' => 'right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'sphtraN',
			[
				'label' => esc_html__( 'Transform css Normal', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'skew(-25deg)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};'
				],	
			]
		);
		$this->add_control(
			'sphtraH',
			[
				'label' => esc_html__( 'Transform css Hover', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'skew(-25deg)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-show:hover' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};'
				],	
			]
		);
		$this->end_controls_section();
		/*show/hide password start*/
		
		/*Lost Password/Register Text Styling option start*/		
		$this->start_controls_section(
            'section_extra_link_opt_style',
            [
                'label' => esc_html__('Lost Password/Register Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_extra_link_typography',
				'selector' => '{{WRAPPER}} .tp-user-login-password .tp-lost-password,
				{{WRAPPER}} .tp-user-login-password .tp-register,
				{{WRAPPER}} .tp-user-register-password .tp-login',
			]
		);
		$this->add_control(
			'form_extra_link_color',
			[
				'label' => esc_html__( 'Text color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-user-login-password .tp-lost-password,
				{{WRAPPER}} .tp-user-login-password .tp-register,
				{{WRAPPER}} .tp-user-register-password .tp-login' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_responsive_control(
            'form_extra_link_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Right Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-user-login-password .tp-lost-password,
				{{WRAPPER}} .tp-user-login-password .tp-register,
				{{WRAPPER}} .tp-user-register-password .tp-login' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'form_extra_link_before_text',
			[
				'label' => __( 'Before Text Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_extra_link_before_text_typography',
				'selector' => '{{WRAPPER}} .tp-field-group .login-before-text',
			]
		);
		$this->add_control(
			'form_extra_link_before_text_color',
			[
				'label' => esc_html__( 'Text color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-field-group .login-before-text' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->end_controls_section();		
		/*Lost Password/Register Text Styling option end*/
		
		/*Lost Password Back Arrow Styling start*/
		$this->start_controls_section(
            'section_lost_pass_btn_style',
            [
                'label' => esc_html__('Lost Password Back Arrow', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
					'show_lost_password' => 'yes',
				],
            ]
        );		
		$this->add_control(
			'lpba_icon',
			[
				'label' => esc_html__( 'Back Arrow', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-arrow-circle-left',
					'library' => 'solid',
				],
			]
		);
		$this->add_responsive_control(
            'lost_pass_btn_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked-fp .tp-lpu-back i:before' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-form-stacked-fp .tp-lpu-back svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'lost_pass_btn_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-form-stacked-fp .tp-lpu-back i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-form-stacked-fp .tp-lpu-back svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*Lost Password Back Arrow Styling end*/
		
		/*mailchimp style start*/
		$this->start_controls_section(
            'section_mail_chimp_dyn_style',
            [
                'label' => esc_html__('MailChimp', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'tp_mail_chimp_subscribe_opt' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mail_chimp_dyn_typography',
				'selector' => '{{WRAPPER}} .tp-wp-lrcf .tp-lr-f-mail-chimp-sub .tp-form-label',
			]
		);		
		$this->add_control(
            'mail_chimp_dyn_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '{{WRAPPER}} .tp-wp-lrcf .tp-lr-f-mail-chimp-sub .tp-form-label' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_control(
			'mail_chimp_chk_box_opt',
			[
				'label' => __( 'Checkbox Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->start_controls_tabs( 'mc_ckhbox_tabs' );
		$this->start_controls_tab(
			'mc_unchk_tab',
			[
				'label' => esc_html__( 'Uncheck', 'theplus' ),					
			]
		);
		$this->add_control(
			'mc_unchk_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .user_mail_chimp_subscribe_checkbox' => 'background: {{VALUE}}',
				],					
			]
		);
		$this->add_control(
			'mc_unchk_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .user_mail_chimp_subscribe_checkbox' => 'border-color: {{VALUE}}',
				],					
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'mc_chk_tab',
			[
				'label' => esc_html__( 'Check', 'theplus' ),					
			]
		);
		$this->add_control(
			'mc_chk_bg',
			[
				'label' => esc_html__( 'Check Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .user_mail_chimp_subscribe_checkbox:before,{{WRAPPER}} .tp-wp-lrcf .user_mail_chimp_subscribe_checkbox:after' => 'background: {{VALUE}}',
				],					
			]
		);		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*mailchimp style end*/	
		
		/*password hint start*/
		$this->start_controls_section(
            'section_pass_hint_style',
            [
                'label' => esc_html__('Password Hint', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_hint' => 'yes',
				],
            ]
        );
		$this->add_control(
			'ph_click_icon_h',
			[
				'label' => esc_html__( 'Click Icon Styling', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [					
					'dis_pass_hint_on' => 'pshc',
				],				
			]
		);
		$this->add_responsive_control(
            'ph_click_icon_topoffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Top Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'dis_pass_hint_on' => 'pshc',
				],
            ]
        );
		$this->add_responsive_control(
            'ph_click_icon_rightoffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Right Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'dis_pass_hint_on' => 'pshc',
				],
            ]
        );
		$this->add_responsive_control(
            'ph_click_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh svg' => 'widht: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'dis_pass_hint_on' => 'pshc',
				],
            ]
        );
		$this->add_control(
			'ph_click_icon_n',
			[
				'label' => esc_html__( 'Icon Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'dis_pass_hint_on' => 'pshc',
				],
			]
		);
		$this->add_control(
			'ph_click_icon_s',
			[
				'label' => esc_html__( 'Icon Success Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh.tp-done' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-lr-f-user-pass .tp-password-field-showh.tp-done svg' => 'fill: {{VALUE}};',
				],
				'separator' => 'after',
				'condition' => [
					'dis_pass_hint_on' => 'pshc',
				],
			]
		);
		$this->add_control(
			'ph_list_h',
			[
				'label' => esc_html__( 'List Styling', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,											
			]
		);
		$this->add_responsive_control(
			'ph_list_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ph_list_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ph_list_typography',
				'selector' => '{{WRAPPER}} .tp-user-register .tp-pass-indicator li',
			]
		);
		$this->add_control(
			'ph_list_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator li' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ph_listicon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator li i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ph_listfill_color',
			[
				'label' => esc_html__( 'Success Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator li .tp-pass-success-ind i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ph_list_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-user-register .tp-pass-indicator li',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ph_list_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-user-register .tp-pass-indicator li',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ph_list_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ph_list_shadow',
				'selector' => '{{WRAPPER}} .tp-user-register .tp-pass-indicator li',				
			]
		);
		$this->add_control(
			'ph_list_box_h',
			[
				'label' => esc_html__( 'List Box Styling', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_field_strong' => 'yes',
					'tp_dis_pass_hint' => 'yes',
				],				
			]
		);
		$this->add_responsive_control(
			'ph_listb_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ph_listb_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ph_listb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-user-register .tp-pass-indicator',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ph_listb_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-user-register .tp-pass-indicator',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ph_listb_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-register .tp-pass-indicator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ph_listb_shadow',
				'selector' => '{{WRAPPER}} .tp-user-register .tp-pass-indicator',				
			]
		);
		$this->end_controls_section();
		/*password hint end*/
		
		/*terms & condition style start*/
		$this->start_controls_section(
            'section_t_a_c_style',
            [
                'label' => esc_html__('Terms & Conditions', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'tp_terms_condition_opt' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tac_typography',
				'selector' => '{{WRAPPER}} .tp-wp-lrcf .tp-lr-f-tac .tp-form-label',
			]
		);		
		$this->add_control(
            'tac_dyn_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '{{WRAPPER}} .tp-wp-lrcf .tp-lr-f-tac .tp-form-label' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_control(
			'tac_chk_box_opt',
			[
				'label' => __( 'Checkbox Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->start_controls_tabs( 'tac_ckhbox_tabs' );
		$this->start_controls_tab(
			'tac_unchk_tab',
			[
				'label' => esc_html__( 'Uncheck', 'theplus' ),					
			]
		);
		$this->add_control(
			'tac_unchk_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .user_tac_checkbox' => 'background: {{VALUE}}',
				],					
			]
		);
		$this->add_control(
			'tac_unchk_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .user_tac_checkbox' => 'border-color: {{VALUE}}',
				],					
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tac_chk_tab',
			[
				'label' => esc_html__( 'Check', 'theplus' ),					
			]
		);
		$this->add_control(
			'tac_chk_bg',
			[
				'label' => esc_html__( 'Check Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .user_tac_checkbox:before,{{WRAPPER}} .tp-wp-lrcf .user_tac_checkbox:after' => 'background: {{VALUE}}',
				],					
			]
		);		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*mailchimp style end*/
		
		/*LR combo style start*/
		$this->start_controls_section(
            'section_lr_tabbing_style',
            [
                'label' => esc_html__('Login/Register Tabbing', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection' => 'tp_login_register',
				],
            ]
        );
		$this->add_responsive_control(
			'lr_tabbing_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'lr_tabbing_typo',
						'label' => esc_html__( 'Typography', 'theplus' ),				
						'selector' => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab',
				'separator' => 'before',
			]
		);	
		$this->add_responsive_control(
            'lr_tabbing_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab' => 'max-width: {{SIZE}}{{UNIT}} !important;min-width: {{SIZE}}{{UNIT}} !important',
				],
				'separator' => 'before',
            ]
        );	
		$this->start_controls_tabs('lr_combo_tabs');
		$this->start_controls_tab('lr_combo_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
		'lr_combo_color_normal',
		[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'lr_combo_bg_normal',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab',
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lr_combo_border_normal',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'lr_combo_border_radious_normal',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lr_combo_shadow_normal',
				'selector' => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab',			
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('lr_combo_active_tab',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
		'lr_combo_color_active',
		[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab.active' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'lr_combo_bg_active',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab.active',				
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lr_combo_border_active',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab.active',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'lr_combo_border_radious_active',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lr_combo_shadow_active',
				'selector' => '{{WRAPPER}} .tp-lr-cl-100per .tp-l-r-main-wrapper .tp-l-r-tab.active',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*LR combo style start*/
		
		/*Notification Message style start*/
		$this->start_controls_section(
            'section_ajax_msg_option_style',
            [
                'label' => esc_html__('Notification Message Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );		
		$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'ajax_msg_typography',
						'label' => esc_html__( 'Typography', 'theplus' ),				
						'selector' => '{{WRAPPER}} .theplus-notification.active .tp-lr-response',				
			]
		);	
		$this->add_control(
				'ajax_msg_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .theplus-notification.active .tp-lr-response' => 'color: {{VALUE}}',
					],
					
				]
			);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ajax_bg',
				'label' => esc_html__( 'Notification Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-notification.active',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Notification Message style END*/
		
		/*Custom Validation Styling start*/
		$this->start_controls_section(
            'section_custom_validation_style',
            [
                'label' => esc_html__('Custom Validation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'cst_validation_switch' => 'yes',
				],
            ]
        );	
		$this->add_responsive_control(
			'rcv_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-form-stacked .tp-reg-form-error-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'rcv_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-form-stacked .tp-reg-form-error-field' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rcv_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),				
				'selector' => '{{WRAPPER}} .tp-wp-lrcf .tp-form-controls .tp-reg-form-error-field',
			]
		);	
		$this->add_control(
			'rcv_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-wp-lrcf .tp-form-controls .tp-reg-form-error-field' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'rcv_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .tp-form-stacked .tp-reg-form-error-field' => 'background: {{VALUE}}',
					'{{WRAPPER}} .tp-user-register .tp-form-stacked .tp-reg-form-error-field:before' => 'border-color: {{VALUE}}  transparent transparent transparent',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'rcv_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-user-register .tp-form-stacked .tp-reg-form-error-field',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'rcv_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-register .tp-form-stacked .tp-reg-form-error-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->end_controls_section();
		/*Custom Validation Styling end*/
		
		/*Reset mail Message style start*/
		$this->start_controls_section(
            'section_reset_mail_option_style',
            [
                'label' => esc_html__('Reset Mail Expired/Invalid', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection!' => 'tp_register',
					'f_p_opt' => 'f_p_frontend',
				],
            ]
        );
		$this->add_responsive_control(
			'rmt_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-invalid-expired-key' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'rmt_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-invalid-expired-key' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',				
			]
		);
		$this->add_responsive_control(
			'rmt_alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors'  => [
					'{{WRAPPER}} .tp-invalid-expired-key' => 'text-align: {{VALUE}};',
				],				
			]
		);
		$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'rmt_typography',
						'label' => esc_html__( 'Typography', 'theplus' ),				
						'selector' => '{{WRAPPER}} .tp-invalid-expired-key',
			]
		);	
		$this->add_control(
				'rmt_msg_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-invalid-expired-key' => 'color: {{VALUE}}',
					],
					
				]
			);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'rmt_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-invalid-expired-key',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'rmt_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-invalid-expired-key',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'rmt_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-invalid-expired-key' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'rmt_shadow',
				'selector' => '{{WRAPPER}} .tp-invalid-expired-key',
			]
		);
		$this->end_controls_section();
		/*reset mail Message style END*/
		
		/*password strength start*/
		$this->start_controls_section(
            'section_psm_style',
            [
                'label' => esc_html__('Password Strength Meter', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection' => ['tp_register','tp_login_register'],
					'tp_dis_pass_field' => 'yes',
					'tp_dis_pass_meter' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'psm_margin_box',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'psm_label_heading',
			[
				'label' => 'Label Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'psm_label_typography',
				'selector' => '{{WRAPPER}} .tp-user-register .password-strength-wrapper',
			]
		);
		$this->add_control(
			'psm_label_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'psm_label_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-user-register .password-strength-wrapper',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'psm_label_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-user-register .password-strength-wrapper',
			]
		);
		$this->add_responsive_control(
			'psm_label_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'psm_label_shadow',
				'selector' => '{{WRAPPER}} .tp-user-register .password-strength-wrapper',
			]
		);		
		$this->add_control(
			'psm_message_heading',
			[
				'label' => 'Message Common Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_responsive_control(
			'psm_c_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'psm_c_typography',
				'selector' => '{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength',
			]
		);
		$this->add_control(
			'psm_c_border_style',
			[
				'label' => esc_html__( 'Border Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
                    '' => esc_html__('None', 'theplus'),
                    'solid' => esc_html__('Solid', 'theplus'),
                    'dashed' => esc_html__('Dashed', 'theplus'),
                    'dotted' => esc_html__('Dotted', 'theplus'),
                    'groove' => esc_html__('Groove', 'theplus'),
                    'inset' => esc_html__('Inset', 'theplus'),
                    'outset' => esc_html__('Outset', 'theplus'),
                    'ridge' => esc_html__('Ridge', 'theplus'),
                ],
				'selectors'  => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength' => 'border-style: {{VALUE}};',
				],				
			]
		);
		$this->add_responsive_control(
			'psm_c_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'psm_short_heading',
			[
				'label' => 'Short Message Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'psm_short_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.short' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_short_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.short' => 'background:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_short_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.short' => 'border-color:{{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'psm_short_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.short' => 'width: {{SIZE}}%;',
				],
            ]
        );
		$this->add_control(
			'psm_bad_heading',
			[
				'label' => 'Bad Message Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'psm_bad_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.bad' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_bad_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.bad' => 'background:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_bad_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.bad' => 'border-color:{{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'psm_bad_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.bad' => 'width: {{SIZE}}%;',
				],
            ]
        );
		$this->add_control(
			'psm_good_heading',
			[
				'label' => 'Good Message Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'psm_good_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.good' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_good_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.good' => 'background:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_good_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.good' => 'border-color:{{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'psm_good_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.good' => 'width: {{SIZE}}%;',
				],
            ]
        );
		$this->add_control(
			'psm_strong_heading',
			[
				'label' => 'Strong Message Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'psm_strong_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.strong' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_strong_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.strong' => 'background:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'psm_strong_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.strong' => 'border-color:{{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'psm_strong_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-user-register .password-strength-wrapper.show #password-strength.strong' => 'width: {{SIZE}}%;',
				],
            ]
        );
		$this->end_controls_section();
		/*password strength end*/
		
		/*logoutstyle start*/
		$this->start_controls_section(
            'section_logout_style',
            [
                'label' => esc_html__('My Account Menu', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'after_login_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .tp-user-login' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'after_login_panel_text',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .tp-user-login ul li' => 'text-align: {{VALUE}};justify-content: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'after_login_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),				
				'selector' => '{{WRAPPER}} .after_login_btn_wrapper *,
				{{WRAPPER}} .after_login_btn_wrapper .tp-user-login .tp-list .tp-user-logged-out .tp-button,
				{{WRAPPER}} .after_login_btn_wrapper .tp-user-login ul .tp-user-name a,
				{{WRAPPER}} .after_login_btn_wrapper .tp-user-login ul .after_login_panel_link a',
			]
		);	
		$this->add_control(
			'after_login_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper *,
					{{WRAPPER}} .after_login_btn_wrapper .tp-user-login .tp-list .tp-user-logged-out .tp-button,
					{{WRAPPER}} .after_login_btn_wrapper .tp-user-login ul .tp-user-name a,
					{{WRAPPER}} .after_login_btn_wrapper .tp-user-login ul .after_login_panel_link a' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_control(
			'after_login_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .tp-user-login ul.tp-list li:hover a' => 'color: {{VALUE}} !important',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'after_login_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .after_login_btn_wrapper .tp-user-login',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'after_login_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .after_login_btn_wrapper .tp-user-login',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'after_login_border_radious',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .after_login_btn_wrapper .tp-user-login' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'after_login_shadow',
				'selector' => '{{WRAPPER}} .after_login_btn_wrapper .tp-user-login',
			]
		);
		$this->add_control(
			'lr_al_img_head',
			[
				'label' => esc_html__( 'Image Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'lr_al_img_margin_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Right Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main span .avatar' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'lr_al_img_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main span .avatar' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'lr_al_img_border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-wp-lrcf .after_login_btn_wrapper .elementor-button-text .avatar',
			]
		);
		$this->add_responsive_control(
			'lr_al_img_border_radius_n',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-wp-lrcf .after_login_btn_wrapper .elementor-button-text .avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lr_al_img_shadow_n',
				'selector' => '{{WRAPPER}} .tp-wp-lrcf .after_login_btn_wrapper .elementor-button-text .avatar',
			]
		);
		$this->add_control(
			'lr_al_head',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'lr_al_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'lr_al_typo',
						'label' => esc_html__( 'Typography', 'theplus' ),				
						'selector' => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main span',
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs('lr_al_tabs');
		$this->start_controls_tab('lr_al_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
		'lr_al_color_normal',
		[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'lr_al_bg_normal',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main',
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lr_al_border_normal',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'lr_al_border_radious_normal',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lr_al_shadow_normal',
				'selector' => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main',			
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('lr_al_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
		'lr_al_color_active',
		[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main:hover  span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'lr_al_bg_active',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main:hover',				
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lr_al_border_active',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main:hover',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'lr_al_border_radious_active',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lr_al_shadow_active',
				'selector' => '{{WRAPPER}} .after_login_btn_wrapper .after_login_btn_main:hover',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*logout style END*/
		/*box content login register forgot option start*/
		$this->start_controls_section(
            'section_box_content_lrf_option_style',
            [
                'label' => esc_html__('Box Content Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection!' => ['tp_login_register'],
				],
            ]
        );		
		$this->add_responsive_control(
			'bc_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div  .tp-form-stacked-fp,	
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bc_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div  .tp-form-stacked-fp,		
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bc_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div  .tp-form-stacked-fp,	
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset',
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'bc_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'bc_border_radious',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
						{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bc_shadow',
				'selector' => '{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div  .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,		
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset',
			]
		);
		$this->add_control(
			'olcbf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'olcbf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'olcbf' => 'yes',
				],
			]
		);
		$this->add_control(
			'olcbf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div,{{WRAPPER}} .tp-user-login.tp-user-login-skin-dropdown .lr-extra-div  .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login-skin-default .tp-form-stacked-fp,		
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-modal,
					{{WRAPPER}} .tp-user-login.tp-user-login-skin-popup .tp-form-stacked-fp,
					{{WRAPPER}} .tp-user-register-skin-default .tp-form-stacked,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-dropdown .lr-extra-div,	
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-modal .tp-modal-dialog,
					{{WRAPPER}} .tp-user-register.tp-user-register-skin-popup .tp-modal,
					{{WRAPPER}} .tp-wp-lrcf .tp-forg-pass-form .tp-form-stacked-fp,{{WRAPPER}} .tp-wp-lrcf .tp-form-stacked-reset' => '-webkit-backdrop-filter:grayscale({{olcbf_grayscale.SIZE}})  blur({{olcbf_blur.SIZE}}{{olcbf_blur.UNIT}}) !important;backdrop-filter:grayscale({{olcbf_grayscale.SIZE}})  blur({{olcbf_blur.SIZE}}{{olcbf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'olcbf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*box content login register forgot option end*/
		
		/*box content login register combo option start*/
		$this->start_controls_section(
            'section_box_content_lrcom_option_style',
            [
                'label' => esc_html__('Box Content Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection' => 'tp_login_register',
				],
            ]
        );
		$this->add_responsive_control(
            'lrcom_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => ['px','vw'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 5,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per' => 'max-width: {{SIZE}}{{UNIT}} !important;min-width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'_skin!' => 'default',
				],
				'separator' => 'after',
            ]
        );
		$this->add_responsive_control(
            'lrcom_max_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Height', 'theplus'),
				'size_units' => ['px','vh'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 5,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per' => 'max-height: {{SIZE}}{{UNIT}} !important;min-height: {{SIZE}}{{UNIT}} !important',
				],
				'condition' => [
					'_skin!' => 'default',
				],
				'separator' => 'after',
            ]
        );
		$this->add_responsive_control(
			'bc_com_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per .tp-form-stacked-fp,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per .tp-form-stacked-fp,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per .tp-form-stacked-fp' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bc_com_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per .tp-form-stacked-fp,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per .tp-form-stacked-fp,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
					{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal .tp-form-stacked-fp,
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per, 
					{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per .tp-form-stacked-fp' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bc_com_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per,
				{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per .tp-form-stacked-fp,
				{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
				{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per .tp-form-stacked-fp,
				{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
				{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal .tp-form-stacked-fp,
				{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per,
				{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per .tp-form-stacked-fp',
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'bc_com_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
						{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'bc_com_border_radious',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
						{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],					
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bc_com_shadow',
				'selector' => '{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per,
						{{WRAPPER}} .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal,
						{{WRAPPER}} .tp-wp-lrcf .tp-lr-comm-wrap:not(.tp-lr-combo) .tp-lr-cl-100per',
			]
		);
		$this->end_controls_section();
		/*box content login register combo option end*/		
		
		/*extra option*/
		$this->start_controls_section(
            'section_extra_option',
            [
                'label' => esc_html__('Extra Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'form_selection!' => ['tp_forgot_password'],
				],
            ]
        );
		$this->add_control(
			'bl_al_sticky',
			[
				'label' => esc_html__( 'Sticky Navigation Connection', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_control(
			'bl_sticky_heading',
			[
				'label' => esc_html__( 'Before Login', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);		
		$this->start_controls_tabs( 'tabs_bl_sticky_style' , [
			'condition' => [
				'bl_al_sticky' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'tab_bl_sticky_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_control(
			'bl_sticky_icon_color_n',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-wp-lrcf .elementor-button-content-wrapper i,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_control(
			'bl_sticky_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bl_sticky_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bl_sticky_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'bl_sticky_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bl_sticky_shadow',
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_bl_sticky_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_control(
			'bl_sticky_icon_color_h',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-wp-lrcf a:hover .elementor-button-content-wrapper i,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-wp-lrcf .tp-lr-comm-wrap .tp-ursp-btn:hover i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_control(
			'bl_sticky_text_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bl_sticky_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover',
			'condition' => [
				'bl_al_sticky' => 'yes',
			],
			]			
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bl_sticky_hover_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'bl_sticky_hover_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bl_sticky_hover_shadow',
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-modal .tp-lr-model-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-dropdown .tp-button-dropdown:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-login.tp-user-login-skin-popup .tp-ulsp-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-modal .tp-lr-model-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-dropdown .tp-button-dropdown:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-user-register.tp-user-register-skin-popup .tp-ursp-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-btn:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-hover .tp-button-dropdown:hover,
							.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn:hover',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'al_sticky_heading',
			[
				'label' => esc_html__( 'After Login', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_control(
			'al_sticky_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .after_login_btn_wrapper *,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .after_login_btn_wrapper .tp-user-login .tp-list .tp-user-logged-out .tp-button,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .after_login_btn_wrapper .tp-user-login ul .tp-user-name a,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .after_login_btn_wrapper .tp-user-login ul .after_login_panel_link a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
				
			]
		);
		$this->start_controls_tabs( 'tabs_al_sticky_style', [
			'condition' => [
				'bl_al_sticky' => 'yes',
			],
		] );
		$this->start_controls_tab(
			'tab_al_sticky_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'al_sticky_bg_normal',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .after_login_btn_wrapper .after_login_btn_main',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_al_sticky_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'al_sticky_bg_hover',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-wp-login-register .after_login_btn_wrapper .after_login_btn_main:hover',
				'condition' => [
					'bl_al_sticky' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*extra option*/
		/*style start*/
	}
	
	public function form_fields_render_attributes() {
		$settings = $this->get_settings();
		$id       = 'lr'.$this->get_id();

		$this->add_render_attribute(
			[
				'submit-group' => [
					'class' => [
						'elementor-field-type-submit',
						'tp-field-group',
					],
				],							
				'dropdown-button-settings' => [
					'class' => [
						'elementor-button',
						'tp-button-dropdown',
					],
					'href' => 'javascript:void(0)',
				],
				'modal-button' => [
					'class' => [
						'elementor-button',
						'tp-button-modal',
					],					
				],
			]
		);

		if ( ! $settings['show_labels'] || ! $settings['show_labels_reg'] ) {
			$this->add_render_attribute( 'label', 'class', 'elementor-screen-only' );
		}

		$this->add_render_attribute( 'field-group', 'class' )
			->add_render_attribute( 'input', 'required', true )
			->add_render_attribute( 'input', 'aria-required', 'true' );

	}
		
		
	protected function render_text_reg_dropdown() {
	
		$settings    = $this->get_settings();
		if(!empty($settings['loop_icon_fontawesome'])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['loop_icon_fontawesome'], [ 'aria-hidden' => 'true' ]);
			$list_img = ob_get_contents();
			ob_end_clean();						
		}
		
		if ( is_user_logged_in() && ! Theplus_Element_Load::elementor()->editor->is_edit_mode() ) {
			$button_text = wp_kses_post($settings['button_text_logout']);
		} else {
			$button_text = wp_kses_post($settings['dropdown_button_text']);
		}
		
		?>

		<span class="elementor-button-content-wrapper">				
			<span class="elementor-button-text">
				<?php echo $list_img.esc_html($button_text); ?>
			</span>
		</span>
		<?php
	}
		
	protected function render_text() {
		$settings    = $this->get_settings();
		$list_img='';
		if(!empty($settings['loop_icon_fontawesome'])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['loop_icon_fontawesome'], [ 'aria-hidden' => 'true' ]);
			$list_img = ob_get_contents();
			ob_end_clean();						
		}
		
		if ( is_user_logged_in() && ! Theplus_Element_Load::elementor()->editor->is_edit_mode() ) {
			$button_text = wp_kses_post( $settings['button_text_logout'] );
		} else {
			$button_text = wp_kses_post( $settings['dropdown_button_text'] );
		}
		
		?>
		<span class="elementor-button-content-wrapper">				
			<span class="elementor-button-text">
				<?php echo $list_img.esc_html($button_text); ?>
			</span>
		</span>
		<?php
	}
	protected function render_text_model() {
		$settings    = $this->get_settings();
		if(!empty($settings['loop_icon_fontawesome'])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['loop_icon_fontawesome'], [ 'aria-hidden' => 'true' ]);
			$list_img = ob_get_contents();
			ob_end_clean();						
		}
		if ( is_user_logged_in() && ! Theplus_Element_Load::elementor()->editor->is_edit_mode() ) {
			$button_text = wp_kses_post( $settings['button_text_logout'] );
		} else {
			$button_text = wp_kses_post( $settings['dropdown_button_text'] );
		}
		
		?>
		<span class="elementor-button-content-wrapper">				
			<span class="elementor-button-text">
				<?php echo $list_img.esc_html($button_text); ?>
			</span>
		</span>
		<?php
	}
	
	
	public function render() {
	
		//$settings    = $this->get_settings();
		$settings = $this->get_settings_for_display();
		$current_url = remove_query_arg( 'fake_arg' );
		$id          = 'lr'.$this->get_id();
		$list_img='';
		if(!empty($settings['loop_icon_fontawesome'])){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['loop_icon_fontawesome'], [ 'aria-hidden' => 'true' ]);
			$list_img = ob_get_contents();
			ob_end_clean();						
		}
		
		
		if ( $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}
		
		
		if($settings['form_selection']=='tp_login_register'){
					$tp_login_registe_script ='jQuery(document).ready(function(){
							jQuery("#'.esc_attr($id).'.tp-l-r-main-wrapper .tp-l-r-tab").on("click", function(event) {
								event.preventDefault();							
								jQuery("#'.esc_attr($id).'.tp-l-r-main-wrapper .tp-l-r-tab").removeClass("active");
								jQuery(this).addClass("active");
								var active = jQuery(this).data("active");
								jQuery(this).closest(".tp-l-r-main-wrapper").find(".tp-tab-content-inner").removeClass("active");
								jQuery(this).closest(".tp-l-r-main-wrapper").find(".tp-tab-content-inner.tab-"+active).addClass("active");
							});	
							
							/*hover*/
					jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-hover").on( "mouseenter",function() {
						jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per").show("slow")
					}).on( "mouseleave",function() {
							setTimeout(function() {
							if(!(jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-hover:hover").length > 0))
								jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-hover .tp-lr-cl-100per").hide("slow");
							}, 200);
						});				
							/*hover*/
							
							/*click*/
							jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-model-btn").on("click",function(){
							jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per").toggle("slow");
					});
							/*close icon*/
					jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-click  .lr-close-custom_img").on("click",function(){					
						jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-click .tp-lr-cl-100per").toggle("slow");
					});				
					/*close icon*/
							/*click*/
							
							/*popup*/
						jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-trigger").on("click",function(event) {
							event.preventDefault();	
							jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-popup .tp-modal-wrapper").toggleClass("open");
							jQuery("#'.esc_attr($id).'.tp-lr-combo.tp-lr-comnbo-skin-popup .tp-ursp-page-wrapper").toggleClass("blur");
							return false;
						});
					/*popup*/
						
					});';
			
			echo wp_print_inline_script_tag($tp_login_registe_script);
		
		}
		
		$aflp='';
		if ( is_user_logged_in() && ! Theplus_Element_Load::elementor()->editor->is_edit_mode() ) {
			$aflp = 'aflp';
		}
		echo '<div class="tp-wp-lrcf '.$aflp.'">';
		
		$current_user = wp_get_current_user();
		
		
	$after_login_panel ='<div class="after_login_btn_wrapper">';
							if((!empty($settings['hide_u_avtar']) && $settings['hide_u_avtar']=='yes') || (!empty($settings['hide_u_name']) && $settings['hide_u_name']=='yes')){
								$after_login_panel .='<a class="after_login_btn_main"  aria-expanded="true">
								<span class="elementor-button-text">';
									if(!empty($settings['hide_u_avtar']) && $settings['hide_u_avtar']=='yes'){
										$after_login_panel .=get_avatar( $current_user->user_email, 128 );
									}									
									if(!empty($settings['hide_u_name']) && $settings['hide_u_name']=='yes'){
										$after_login_panel .=$current_user->display_name;
									}
								$after_login_panel .='</span>
								</a>';
							}
					
					if ( (!empty($settings['show_logged_in_message']) && $settings['show_logged_in_message']=='yes') && 
						((!empty($settings['edit_profile_text_switch']) && $settings['edit_profile_text_switch']=='yes') ||
						(!empty($settings['button_text_logout_switch']) && $settings['button_text_logout_switch']=='yes') ||
						(!empty($settings["loop_content"])))) {
					$after_login_panel .='<div class="tp-user-login '.$settings['layout_start_from'].'"';
						if((!empty($settings['standard_layout']) && $settings['standard_layout']=='yes')){
							$after_login_panel .=' style="display: block"';
						}
					
					$after_login_panel .=' >
							<ul class="tp-list">';
					}
								if(!empty($settings['edit_profile_text_switch']) && $settings['edit_profile_text_switch']=='yes'){
								$after_login_panel .='<li class="tp-user-name"><a href="'.get_edit_user_link().'" class="tp-text-bold">'.esc_html($settings['edit_profile_text']).'</a>
								</li>';	
								}
								$i=0;
								if (!empty($settings["loop_content"])) {
									foreach($settings["loop_content"] as $item) {
										if ( ! empty( $item['loop_url_link']['url'] ) ) {
											$this->add_render_attribute( 'loop_box_link'.$i, 'href', $item['loop_url_link']['url'] );
											if ( $item['loop_url_link']['is_external'] ) {
												$this->add_render_attribute( 'loop_box_link'.$i, 'target', '_blank' );
											}
											if ( $item['loop_url_link']['nofollow'] ) {
												$this->add_render_attribute( 'loop_box_link'.$i, 'rel', 'nofollow' );
											}
										}
										
										$title_a_start=$title_a_end='';
										if(!empty($item['loop_title'])){
											if (!empty($item['loop_url_link']['url'])){
												$title_a_start = '<a '.$this->get_render_attribute_string( "loop_box_link".$i ).'>';
												$title_a_end = '</a>';
											}
											$after_login_panel .= '<li class="after_login_panel_link">'.$title_a_start.' '.$item['loop_title'].' '.$title_a_end.'</li>';
										}
										$i++;
									}
								}
						if(!empty($settings['button_text_logout_switch']) && $settings['button_text_logout_switch']=='yes'){
						$after_login_panel .= '<li class="tp-user-logged-out">
													<a href="'.wp_logout_url( $current_url ).'" class="tp-button tp-button-primary">
													'.esc_html__($settings['button_text_logout']).'</a>
											   </li>';
						}
						if ( (!empty($settings['show_logged_in_message']) && $settings['show_logged_in_message']=='yes') && 
						((!empty($settings['edit_profile_text_switch']) && $settings['edit_profile_text_switch']=='yes') ||
						(!empty($settings['button_text_logout_switch']) && $settings['button_text_logout_switch']=='yes') ||
						(!empty($settings["loop_content"])))) {
						$after_login_panel .= '</ul>
				</div>';
						}
	$after_login_panel .='</div></div>';
		
		if((empty($_GET['action']))|| (!empty($_GET['action']) && $_GET['action'] !='theplusrpf')){
		if(!empty($settings['form_selection']) && ($settings['form_selection']=='tp_login' || $settings['form_selection']=='tp_login_register')){
			
			if ( is_user_logged_in() && ! Theplus_Element_Load::elementor()->editor->is_edit_mode() ) {
				if ( $settings['show_logged_in_message'] ) {
					echo $after_login_panel;
				}
				return;
			}
			
			
			$this->form_fields_render_attributes();
			
			if($settings['_skin']=='default' && $settings['form_selection']=='tp_login'){ ?>
			
				<div id="<?php echo esc_attr($id); ?>" class="tp-user-login tp-user-login-skin-default">
					<div class="elementor-form-fields-wrapper">						
						<?php if ($settings['modal_header']=='yes' && (!empty($settings['hide_form']) && $settings['hide_form']=='no')) : ?>
						<div class="tp-modal-header">
							<h2 class="tp-modal-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_log']; ?></h2>
						</div>
						<?php endif; ?>
						<?php $this->user_login_form(); ?>
					</div>
				</div>
				<?php }else if($settings['_skin']=='tp-dropdown'  && $settings['form_selection']=='tp_login'){ ?>
				<div id="<?php echo esc_attr($id); ?>" class="tp-user-login tp-user-login-skin-dropdown">
					<a <?php echo $this->get_render_attribute_string('dropdown-button-settings'); ?>>
						<?php $this->render_text(); ?>
					</a>
					<div <?php echo $this->get_render_attribute_string('dropdown-settings'); ?>>
						<div class="elementor-form-fields-wrapper">
							<div class="lr-extra-div <?php echo esc_attr($settings['layout_start_from']); ?>">
							<?php if ($settings['modal_header']=='yes') : ?>
							<div class="tp-modal-header">
								<h2 class="tp-modal-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_log']; ?></h2>
							</div>
							<?php endif; ?>
							<?php $this->user_login_form(); ?>
							</div>
						</div>

					</div>
				</div>
					
			<?php }else if($settings['_skin']=='tp-modal' && $settings['form_selection']=='tp_login'){ ?>
			
				<div id="<?php echo esc_attr($id); ?>" class="tp-user-login tp-user-login-skin-modal">
					<a class="tp-lr-model-btn" <?php echo $this->get_render_attribute_string('modal-button-settings'); ?>>
						<?php $this->render_text_model(); ?>
					</a>

					<div id="<?php echo esc_attr($id); ?>" class="tp-user-login-modal">
						<div class="tp-modal-dialog <?php echo esc_attr ( $settings['layout_start_from'] ) ; ?>">
							<?php if ($settings['modal_close_button']=='yes') :
								$image_id = $settings["modal_close_button_icon"]["id"];
								echo tp_get_image_rander( $image_id,'full',[ 'class' => 'lr-close-custom_img' ]);
							?>
							<?php endif; ?>
							
							<div class="elementor-form-fields-wrapper tp-modal-body">
								<?php if ($settings['modal_header']=='yes') : ?>
							<div class="tp-modal-header">
								<h2 class="tp-modal-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_log']; ?></h2>
							</div>
							<?php endif; ?>
								<?php $this->user_login_form(); ?>
							</div>
						</div>
					</div>
				</div>
				
		<?php }else if($settings['_skin']=='tp-popup' && $settings['form_selection']=='tp_login'){ ?>
		
				<div id="<?php echo esc_attr($id); ?>" class="tp-user-login tp-user-login-skin-popup">
					<div class="tp-ulsp-page-wrapper">
						<a class="tp-ulsp-btn tp-ulsp-trigger" href="javascript:;">
							<?php $this->render_text_model(); ?>
						</a>
					</div>
					
					<div class="tp-modal-wrapper">
					  <div class="tp-modal">						
						  <a class="tp-ulsp-btn-close tp-ulsp-trigger" href="javascript:;"> <?php
						  $image_id = $settings["modal_close_button_icon"]["id"];
						  echo tp_get_image_rander( $image_id,'full',[ 'class' => 'lr-close-custom_img' ]); ?>
						  </a>
						<div class="tp-ulsp-content">
							<div class="elementor-form-fields-wrapper tp-popup-body">
								<?php if ($settings['modal_header']=='yes') : ?>
									<div class="tp-popup-header">												
										<h2 class="tp-popup-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_log']; ?></h2>
									</div>
								<?php endif; ?>
								<?php $this->user_login_form(); ?>
							</div>
						</div>
					  </div>
					</div>
				</div>
				
		<?php }
		}
		}else if((isset($_GET['action']) && !empty($_GET['action']) && $_GET['action']=='theplusrpf') && (!empty($settings['tp_convert_rest_form']) && $settings['tp_convert_rest_form']=='yes')){
				$this->user_reset_password_form();
		}
		
		if(!empty($settings['form_selection']) && ($settings['form_selection']=='tp_forgot_password')){
			if((empty($_GET['action']))|| (!empty($_GET['action']) && $_GET['action'] !='theplusrpf')){
				$this->user_lost_password_form('login-time-fp'); 
			}			
		}
		
		if((empty($_GET['action']))|| (!empty($_GET['action']) && $_GET['action'] !='theplusrpf')){
		if(!empty($settings['form_selection']) && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')){
			
			if ( is_user_logged_in() && ! Theplus_Element_Load::elementor()->editor->is_edit_mode() ) {
				if ( $settings['show_logged_in_message_reg'] ) {
					echo $after_login_panel;
				}					

				return;

			} elseif ( !get_option('users_can_register') ) {
				?>
					<div class="tp-alert tp-alert-warning" tp-alert>
						<a class="tp-alert-close" tp-close></a>
						<p><?php esc_html_e( 'Registration option not enbled in your general settings.', 'theplus' ); ?></p>
					</div>
				<?php 
				return;
			}
			

			$this->form_fields_render_attributes();

				
			if($settings['_skin']=='default' && $settings['form_selection']=='tp_register'){	?>
			<div class="tp-user-register tp-user-register-skin-default">
				<div class="elementor-form-fields-wrapper">
					<?php if ($settings['modal_header']=='yes' && (!empty($settings['hide_form']) && $settings['hide_form']=='no')) : ?>
						<div class="tp-modal-header">									
							<h2 class="tp-modal-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_reg']; ?></h2>
						</div>
					<?php endif; ?>
					<?php $this->user_register_form(); ?>
				</div>
			</div>
		<?php 
		
		}else if($settings['_skin']=='tp-dropdown' && $settings['form_selection']=='tp_register'){
			?>
			<div id="<?php echo esc_attr($id); ?>" class="tp-user-register tp-user-register-skin-dropdown">
				<a <?php echo $this->get_render_attribute_string( 'dropdown-button-settings' ); ?>>
					<?php $this->render_text_reg_dropdown(); ?>
				</a>

				<div <?php echo $this->get_render_attribute_string( 'dropdown-settings' ); ?>>
					<div class="elementor-form-fields-wrapper">
						<div class="lr-extra-div <?php echo esc_attr( $settings['layout_start_from'] ); ?>">
							<?php if ($settings['modal_header']=='yes') : ?>
								<div class="tp-modal-header">									
									<h2 class="tp-modal-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_reg']; ?></h2>
								</div>
							<?php endif; ?>
							<?php $this->user_register_form(); ?>
						</div>
					</div>
				</div>
			</div>
		
		<?php }
		
		
		if($settings['_skin']=='tp-modal' && $settings['form_selection']=='tp_register'){ ?>

			<div id="<?php echo esc_attr($id); ?>" class="tp-user-register tp-user-register-skin-modal">
				<a class="tp-lr-model-btn" <?php echo $this->get_render_attribute_string('modal-button-settings'); ?>>
					<?php $this->render_text_reg_dropdown(); ?>
				</a>				
				<div id="<?php echo esc_attr($id); ?>" class="tp-user-register-modal">
					<div class="tp-modal-dialog <?php echo esc_attr( $settings['layout_start_from'] ); ?>">
						<?php if ($settings['modal_close_button']=='yes') :
							$image_id = $settings["modal_close_button_icon"]["id"];
							echo tp_get_image_rander( $image_id,'full',[ 'class' => 'lr-close-custom_img' ]);
						endif; ?>
						
						<div class="elementor-form-fields-wrapper tp-modal-body">
							<?php if ($settings['modal_header']=='yes') : ?>
								<div class="tp-modal-header">									
									<h2 class="tp-modal-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_reg']; ?></h2>
								</div>
							<?php endif; ?>
							<?php $this->user_register_form(); ?>
						</div>
					</div>
				</div>
			</div>
		<?php
		
		
		}else if($settings['_skin']=='tp-popup' && $settings['form_selection']=='tp_register'){ ?>
			
			<div id="<?php echo esc_attr($id); ?>" class="tp-user-register tp-user-register-skin-popup">
				<div class="tp-ursp-page-wrapper">
				  <a class="tp-ursp-btn tp-ursp-trigger" href="javascript:;">
					<?php $this->render_text_reg_dropdown(); ?>
					</a>
				</div>
				<div class="tp-modal-wrapper">
				  <div class="tp-modal">						
					  <a class="tp-ursp-btn-close tp-ursp-trigger" href="javascript:;"> <?php
					  $image_id = $settings["modal_close_button_icon"]["id"];
					 echo tp_get_image_rander( $image_id,'full',[ 'class' => 'lr-close-custom_img' ]); ?>
					  </a>
					<div class="tp-ursp-content">
						<div class="elementor-form-fields-wrapper tp-popup-body">
							<?php if ($settings['modal_header']=='yes') : ?>
								<div class="tp-popup-header">
									<h2 class="tp-popup-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_reg']; ?></h2>
								</div>
							<?php endif; ?>
							<?php $this->user_register_form(); ?>
						</div>
					</div>
				  </div>
				</div>
			</div>
				
		<?php }
		
			
			$lr_popup_start='<div id="'.esc_attr($id).'" class="tp-lr-comm-wrap tp-lr-combo tp-lr-comnbo-skin-popup">
									<div class="tp-ursp-page-wrapper">
										<a class="tp-ursp-btn tp-ursp-trigger" href="javascript:;">'.$list_img.' '.$settings['dropdown_button_text'].'</a>
									</div>
							<div class="tp-modal-wrapper">
								<div class="tp-modal">						
									<a class="tp-ursp-btn-close tp-ursp-trigger" href="javascript:;"><img src="'.esc_url(!empty($settings['modal_close_button_icon']['url']) ? $settings['modal_close_button_icon']['url'] : '' ).'" class="lr-close-custom_img"/></a>						
									<div class="tp-ursp-content">';
								
			$lr_popup_close='</div>
							</div>
					  </div>
					</div>';
		
			$lr_hover_start='<div id="'.esc_attr($id).'" class="tp-lr-comm-wrap tp-lr-combo tp-lr-comnbo-skin-hover">
								<a class="elementor-button tp-button-dropdown" href="javascript:void(0)">
									<span class="elementor-button-content-wrapper">				
										'.$list_img.'<span class="elementor-button-text">'.$settings['dropdown_button_text'].'</span>
									</span>
								</a>';	
			$lr_hover_close='</div>';
			
			$lr_click_start='<div id="'.esc_attr($id).'" class="tp-lr-comm-wrap tp-lr-combo tp-lr-comnbo-skin-click">
								<a class="tp-lr-model-btn">
									<span class="elementor-button-content-wrapper">				
										'.$list_img.'<span class="elementor-button-text">'.$settings['dropdown_button_text'].'</span>
									</span>
								</a>';
			$lr_click_close='</div>';
			
			
			if(!empty($settings['form_selection']) && $settings['form_selection']=='tp_login_register'){
				if(!empty($settings['_skin']) && $settings['_skin']=='tp-popup'){
					echo $lr_popup_start;
				}
				if(!empty($settings['_skin']) && $settings['_skin']=='tp-dropdown'){
					echo $lr_hover_start;
				}
				if(!empty($settings['_skin']) && $settings['_skin']=='tp-modal'){
					echo $lr_click_start;
				}
				
				if(!empty($settings['_skin']) && $settings['_skin']=='default'){
					echo '<div class="tp-lr-comm-wrap">';
				}
				?>			
				<div id="<?php echo esc_attr($id); ?>" class="tp-lr-cl-100per <?php echo esc_attr( $settings['layout_start_from'] ); ?>">
					<?php if(!empty($settings['_skin']) && $settings['_skin']=='tp-modal'){
					if ($settings['modal_close_button']=='yes') :
						$image_id = $settings["modal_close_button_icon"]["id"];
						echo tp_get_image_rander( $image_id,'full',[ 'class' => 'lr-close-custom_img' ]);
					endif; 
					} ?>
							
					<?php if(!empty($settings['select_template'])){ ?>
						<div class="cl-50per">
						<?php
							echo '<div class="temp">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $settings['select_template'] ).'</div>';
						?>
						</div>
						<div class="cl-50per">
					<?php }else{ ?>
							<div class="cl-100per">
					<?php } ?>
							<div id="<?php echo esc_attr($id); ?>" class="tp-l-r-main-wrapper">
								  <ul id="<?php echo esc_attr($id); ?>" class="tp-l-r-tab-group">
									<li class="tp-l-r-tab active" data-active="login"><?php echo esc_html($settings['tab_com_login']); ?></li>
									<li class="tp-l-r-tab" data-active="signup"><?php echo esc_html($settings['tab_com_signup']); ?></li>
								  </ul>      
								  
									  <div class="tp-l-r-tab-content">
											<div class="tp-tab-content-inner tab-login active">   
													<?php if ($settings['modal_header']=='yes') : ?>
														<div class="tp-popup-header">													
															<h2 class="tp-popup-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_log']; ?></h2>
														</div>
													<?php endif; ?>
													<?php $this->user_login_form(); ?>
											</div>  
											<div class="tp-tab-content-inner tab-signup">
												<?php if ($settings['modal_header']=='yes') : ?>
														<div class="tp-popup-header">													
															<h2 class="tp-popup-title"><span tp-icon="user"></span> <?php echo $settings['modal_header_description_reg']; ?></h2>
														</div>
													<?php endif; ?>
											  <?php $this->user_register_form(); ?>
											</div>
									  </div>      
							</div>
						</div>
					</div>
			<?php
				
				if(!empty($settings['_skin']) && $settings['_skin']=='default'){
					echo '</div>';
				}
				if(!empty($settings['_skin']) && $settings['_skin']=='tp-popup'){
					echo $lr_popup_close;
				}
				if(!empty($settings['_skin']) && $settings['_skin']=='tp-dropdown'){
					echo $lr_hover_close;
				}
				if(!empty($settings['_skin']) && $settings['_skin']=='tp-modal'){
					echo $lr_click_close;
				}
				
			}
			
		}
		}
		if(!empty($settings['form_selection']) && $settings['form_selection']=='tp_login'){
			$this->user_login_ajax_script();	
		}else if(!empty($settings['form_selection']) && $settings['form_selection']=='tp_register'){
			$this->user_register_ajax_script();
		}else if(!empty($settings['form_selection']) && $settings['form_selection']=='tp_login_register'){
			$this->user_login_ajax_script();
			$this->user_register_ajax_script();
		}
		?>		
		</div>
		<?php
		
	}
	
	/*User Forgot Password Form*/
	public function user_lost_password_form( $value = '' ) {
	
		$settings    = $this->get_settings();
		$current_url = remove_query_arg( 'fake_arg' );
		$id          = 'lr'.$this->get_id();
		if($settings['form_selection']=='tp_forgot_password'){
			echo '<div class="tp-forg-pass-form">';
		}
		
		
		if(!empty($_GET['expired']) && $_GET['expired']=='expired'){
			$key_msg = 'The entered key has expired. Please start reset process again.';
		}else if(!empty($_GET['invalid']) && $_GET['invalid']=='invalid'){
			$key_msg = 'The entered key is invalid. Please start reset process again.';
		}else{
			$key_msg ='';
		}
		if(!empty($key_msg)){
			echo '<div class="tp-invalid-expired-key">'.esc_html($key_msg).'</div>';
		}
		
		
		?>
		<form id="tp-user-lost-password<?php echo esc_attr($id); ?>" class="tp-form-stacked-fp" method="post" action="forgot-password" >
			<?php 
				$lpba_icon='';
				if(!empty($settings['lpba_icon'])){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['lpba_icon'], [ 'aria-hidden' => 'true' ]);
					$lpba_icon = ob_get_contents();
					ob_end_clean();						
				}
				
				if(!empty($settings['form_selection']) && $settings['form_selection']!=='tp_forgot_password'){
					echo '<a class="tp-lpu-back">'.$lpba_icon.'</a>';
				}
			?>
			<?php 
				if( !empty($value) && $value=='login-time-fp' ){
					echo '<span class="tp-forgot-password-label">';
						echo esc_attr( $settings['lost_password_heading_desc'] );
					echo '</span>';
				}
			if((!empty($settings['lost_pass_label_switch']) && $settings['lost_pass_label_switch']=='yes') && !empty($settings['lost_pass_label'])){
			?>
			<label for="user_login<?php echo esc_attr($id); ?>" class="tp-form-label"><?php echo esc_html( $settings['lost_pass_label'] ); ?></label>
			<?php } ?>
			<div class="tp-ulp-input-group">
				<input type="text" name="user_login" id="user_login<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($settings['lost_pass_placeholder']); ?>" class="tp-input" required>
			</div>
			<?php do_action( 'lostpassword_form' ); ?>
			<input type="hidden" name="_tp_login_form" value="lostPassword">
			<button type="submit" class="tp-button-fp"><?php echo esc_html( $settings['forgot_pass_btn'] ); ?></button>			
			<div class="theplus-notification"><div class="tp-lr-response"></div></div>
			
		</form>
		
		<?php
		if($settings['form_selection']=='tp_forgot_password'){
			echo '</div>';
		}
		
		$this->user_forgot_pass_ajax_script();
		
	}
	/*User Forgot Password Form*/
	
	/*User Reset Password Form*/
	public function user_reset_password_form() {
	
		$settings    = $this->get_settings();		
		$id          = 'lr'.$this->get_id();
		
		$attributes = array();
		if ( is_user_logged_in() ) {
			echo  esc_html( 'You are already signed in.', 'theplus' );
		} else {			
			if ( isset( $_GET['datakey'] )) {
				$forgotresdata = tp_check_decrypt_key($_GET['datakey']);
				$forgotresdata = json_decode(stripslashes($forgotresdata),true);
				$attributes['login'] = wp_unslash( $forgotresdata['login'] );
				$attributes['key'] = wp_unslash( $forgotresdata['key'] );
				$attributes['forgoturl'] = wp_unslash( $forgotresdata['forgoturl'] );
			}
		}
		if(!empty($attributes)){
			
			$pattern_pass_reset='';
			$tp_dp_reset_field_strong = ( $settings['tp_dp_reset_field_strong'] == 'yes' ) ? 'yes' : 'no';			
			if(!empty($tp_dp_reset_field_strong) && $tp_dp_reset_field_strong=='yes'){
				$pattern_pass_reset='pattern=(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}';
			}
			
			$data_forgotres = [];
			$data_forgotres['login'] = $attributes['login'];
			$data_forgotres['forgoturl'] = $attributes['forgoturl'];
			$data_forgotres['key'] = $attributes['key'];
			$data_forgotres['noncesecure'] = wp_create_nonce( 'tp_reset_action' );
			
			$data_forgotreskey= tp_plus_simple_decrypt( json_encode($data_forgotres), 'ey' );
		?>		
			<div class="tp-reset-pass-form">
			<form id="tp-user-reset-password<?php echo esc_attr($id); ?>" class="tp-form-stacked-reset" method="post">				
				<?php
				if((!empty($settings['res_pass_label_switch']) && $settings['res_pass_label_switch']=='yes') && !empty($settings['res_pass_label'])){
				?>
				<label for="user_login<?php echo esc_attr($id); ?>" class="tp-form-label"><?php echo esc_html( $settings['res_pass_label'] ); ?></label>
				<?php } ?>
				<div class="tp-ulp-input-group">
					<input type="password" name="user_reset_pass" id="user_reset_pass<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($settings['res_pass_placeholder']); ?>" class="tp-input" required <?php echo esc_attr( $pattern_pass_reset ); ?> style="margin-bottom:15px">
				</div>
				<div class="tp-ulp-input-group">
					<input type="password" name="user_reset_pass_conf" id="user_reset_pass_conf<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($settings['res_conf_pass_placeholder']); ?>" class="tp-input" required>
				</div>
					
				<button type="submit" class="tp-button-reset-pass"><?php echo esc_html( $settings['reset_pass_btn'] ); ?></button>
				<div class="theplus-notification"><div class="tp-lr-response"></div></div>
			</form>
			</div>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				
				var reset_pass_form = 'form#tp-user-reset-password<?php echo esc_attr($id); ?>';
				var fp_loading='<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>';
				var forgot_url = '<?php echo $data_forgotres["forgoturl"]; ?>';
			    
			    $(reset_pass_form).on('submit', function(e){
			        $.ajax({
			            type: 'POST',
			            dataType: 'json',
			            url: theplus_ajax_url,
			            data: { 
			                'action': 'theplus_ajax_reset_password',
			                'tpresetdata': '<?php echo $data_forgotreskey; ?>',							
							'user_pass': $(reset_pass_form + ' #user_reset_pass<?php echo esc_attr($id); ?>').val(), 
							'user_pass_conf': $(reset_pass_form + ' #user_reset_pass_conf<?php echo esc_attr($id); ?>').val(),
			            },
						beforeSend: function(){
							$("#tp-user-reset-password<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
							$("#tp-user-reset-password<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html('Please Wait...');
						},
			            success: function(data) {
								$("#tp-user-reset-password<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
								$("#tp-user-reset-password<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(fp_loading + data.message);
								if(data.reset_pass=='success'){
									if( forgot_url != ''){
										window.location = forgot_url;
									}
								}
								if(data.reset_pass=='empty'){
									$(reset_pass_form + ' #user_reset_pass<?php echo esc_attr($id); ?>').value='';
									$(reset_pass_form + ' #user_reset_pass_conf<?php echo esc_attr($id); ?>').value='';
								}
								if(data.reset_pass=='mismatch'){
									$(reset_pass_form + ' #user_reset_pass_conf<?php echo esc_attr($id); ?>').value='';
								}
								if(data.reset_pass=='expire'){
									if( forgot_url != ''){
										window.location = forgot_url;
									}
								}
								if(data.reset_pass=='invalid'){
									if( forgot_url != ''){
										window.location = forgot_url;
									}
								}
			            },
						complete: function(){
							setTimeout(function(){
								$("#tp-user-reset-password<?php echo esc_attr($id);?> .theplus-notification").removeClass("active");	
							}, 3200);
						}
			        });
			        e.preventDefault();					
			    });			
			});
		</script>
		<?php
		}
	}
	/*User Reset Password Form*/
	
	/*User Login Form*/
	public function user_login_form() {
	
		$settings    = $this->get_settings();

		$current_url = remove_query_arg( 'fake_arg' );
		$id          = 'lr'.$this->get_id();

		if ( $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}
		$hide_form = ( $settings['hide_form'] == 'yes' ) ? 'yes' : 'no';
		
		if(!empty($hide_form) && $hide_form != 'yes'){
		?>
		
		<form id="tp-user-login<?php echo esc_attr($id); ?>" class="tp-form-stacked " method="post" action="login">
			<div class="tp-user-login-status"></div>
			<div class="tp-field-group tp-l-lr-user-name">
				<?php
				
				if ( $settings['show_labels']=='yes' && ($settings['form_selection']=='tp_login' || $settings['form_selection']=='tp_login_register')) {					
					echo '<label for="user'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['user_label'] ).'</label>';					
				}
				echo '<div class="tp-form-controls">';
				echo '<input type="text" name="log" id="user'.esc_attr($id).'" placeholder="'.esc_html( $settings['user_placeholder'] ).'" class="tp-input" required>';
				echo '</div>';

				?>
			</div>

			<div class="tp-field-group tp-l-lr-password">
				<?php
				if ( $settings['show_labels']=='yes' && ($settings['form_selection']=='tp_login' || $settings['form_selection']=='tp_login_register'))  :					
					echo '<label for="password'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['password_label'] ).'</label>';
				endif;
				echo '<div class="tp-form-controls">';				
				echo '<input type="password" name="pwd" id="password'.esc_attr($id).'" placeholder="'.esc_html( $settings['password_placeholder'] ).'" class="tp-input" required>';
				echo '</div>';
				?>
			</div>

			<?php if ( $settings['show_remember_me']=='yes' ) : ?>
				<div class="tp-field-group tp-remember-me">
					<label for="remember-me-<?php echo esc_attr($id); ?>" class="tp-form-label">
						<input type="checkbox" id="remember-me-<?php echo esc_attr($id); ?>" class="tp-checkbox" name="rememberme" value="forever"> 
						<label class="remember-me-label" for="remember-me-<?php echo esc_attr($id); ?>"><?php echo esc_html($settings['remember_me_text']); ?></label>
					</label>
				</div>
			<?php endif; ?>
			
			<div <?php echo $this->get_render_attribute_string( 'submit-group' ); ?>>
				<button type="submit" class="tp-button" name="wp-submit">
					<?php if ( ! empty( $settings['button_text'] ) ) : ?>
						<span><?php echo esc_html( $settings['button_text'] ); ?></span>
					<?php endif; ?>
				</button>
			</div>

			<?php
			$show_lost_password = $settings['show_lost_password'];
			$show_register      = get_option( 'users_can_register' ) && $settings['show_register'];

			if ( $show_lost_password || $show_register ) : ?>
				<div class="tp-field-group  tp-user-login-password">
					   
					<?php if ( $show_lost_password=='yes' ) : ?>
						<a  href="#" class="tp-lost-password"><?php echo esc_html($settings['bottom_lost_pass_text']); ?></a>
					<?php endif; ?>

					<?php if ( $show_register=='yes' ) : ?>
						<a class="tp-register" href="<?php 
							if($settings['show_register_opt']=='default'){
								echo wp_registration_url(); 
							}else if($settings['show_register_opt']=='custom'){						
								if ( ! empty( $settings['show_register_opt_link']['url'] ) ) {
									echo esc_url ( $settings['show_register_opt_link']['url'] );
								}
							}
						?>"><?php echo esc_html( $settings['bottom_register_text'] ); ?></a>
					<?php endif; ?>
					
				</div>
			<?php endif; ?>
			
			<?php wp_nonce_field( 'ajax-login-nonce', 'tp-user-login-sc' ); 
				echo '<div class="theplus-notification"><div class="tp-lr-response"></div></div>'; ?>
		</form>
		<?php
		}
		$this->user_social_log_reg('login');
		
		if(!empty($settings['show_lost_password']) && $settings['show_lost_password']=='yes'){
			if((empty($_GET['action'])) || (!empty($_GET['action']) && $_GET['action'] !='theplusrpf')){
				$this->user_lost_password_form('login-time-fp'); 
			}
			if(isset($_GET['action']) && !empty($_GET['action']) && $_GET['action']=='theplusrpf'){
				$this->user_reset_password_form(); 
			}
		}
		
	}
	/*User Login Form*/
	
	/*User Register Form*/
	public function user_register_form() {
		$settings    = $this->get_settings();

		$id          = 'lr'.$this->get_id();
		$current_url = remove_query_arg( 'fake_arg' );

		if ( $settings['redirect_after_register'] && ! empty( $settings['redirect_url_reg']['url'] ) ) {
			$redirect_url_reg = $settings['redirect_url_reg']['url'];
		} else {
			$redirect_url_reg = $current_url;
		}
		
		
		$dis_cap = ( $settings['display_captcha_swtch'] == 'yes' ) ? 'yes' : 'no';
		$hide_form = ( $settings['hide_form'] == 'yes' ) ? 'yes' : 'no';
		
		$dis_password = ( $settings['tp_dis_pass_field'] == 'yes' ) ? 'yes' : 'no';
		$dis_password_conf = ( $settings['tp_dis_conf_pass_field'] == 'yes' ) ? 'yes' : 'no';
		
		$dis_mail_chimp = ( $settings['tp_mail_chimp_subscribe_opt'] == 'yes' ) ? 'yes' : 'no';
		$mc_custom_apikey=$mc_custom_listid='';
		if(!empty($dis_mail_chimp) && $dis_mail_chimp=='yes'){
			if((!empty($settings['mc_i_li_ak_swtch']) && $settings['mc_i_li_ak_swtch']=='yes') && (!empty($settings['mc_custom_apikey']) && !empty($settings['mc_custom_listid']))){
				$mc_custom_apikey = $settings['mc_custom_apikey'];
				$mc_custom_listid = $settings['mc_custom_listid'];			
			}
		}
		
		$dis_cap = ( $settings['display_captcha_swtch'] == 'yes' ) ? 'yes' : 'no';
				
		$tceo=array();
		if(!empty($settings['tp_cst_email_opt']) && $settings['tp_cst_email_opt'] == 'yes'){
			$tpces = !empty($settings['tp_cst_email_subject']) ? $settings['tp_cst_email_subject'] : '';
			$tpcem = !empty($settings['tp_cst_email_message']) ? $settings['tp_cst_email_message'] : '';
			
			$tceo["tp_cst_email_opt"] = $settings['tp_cst_email_opt'];
			$tceo["tp_cst_email_subject"] = $tpces;
			$tceo["tp_cst_email_message"] = $tpcem;
		}
		$data_tceo=json_encode($tceo);
		$redirect_url_reg = $settings['redirect_url_reg']['url']  ? $settings['redirect_url_reg']['url'] : '';
		
		?>
		
		<form id="tp-user-register<?php echo esc_attr($id); ?>" name="tp-user-registration" class="tp-form-stacked " method="post" action="" data-dis_cap="<?php echo esc_attr( $dis_cap ); ?>" ' data-tceo="<?php echo htmlspecialchars($data_tceo, ENT_QUOTES, 'UTF-8'); ?>" data-dis_password="<?php echo esc_attr( $dis_password ); ?>" data-dis_password_conf="<?php echo esc_attr( $dis_password_conf ); ?>" data-after_reg_redirect="<?php echo esc_attr( $redirect_url_reg ); ?>" data-dis_mail_chimp="<?php echo esc_attr( $dis_mail_chimp ); ?>" data-mc_custom_apikey="<?php echo esc_attr( $mc_custom_apikey ); ?>" data-mc_custom_listid="<?php echo esc_attr( $mc_custom_listid ); ?>">
			
			<?php if(!empty($hide_form) && $hide_form != 'yes'){
				if(!empty($settings['tp_dis_name_field']) && $settings['tp_dis_name_field']=='yes'){ 
				if(!empty($settings['tp_dis_fname_field']) && $settings['tp_dis_fname_field']=='yes'){ 	
					?>
			
			<div class="tp-field-group tp-lr-f-first-name">
				<?php				
				if ( $settings['show_labels_reg']=='yes' && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')) {					
					echo '<label for="first_name'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['first_name_label'] ).'</label>';					
				}
				echo '<div class="tp-form-controls">';
				echo '<input type="text" name="first_name" id="first_name'.esc_attr($id).'" placeholder="'.esc_html( $settings['first_name_placeholder'] ).'" class="tp-input tp-reg-f-load" required>';				
				if(!empty($settings['tp_honeypot_opt']) && $settings['tp_honeypot_opt']=='yes'){
					echo '<input type="text" name="tphoney-first_name" id="tphoney_first_name'.esc_attr($id).'" class="tp-honey-input">';
				}
				echo '</div>';

			?>
			</div>
			<?php }
			if(!empty($settings['tp_dis_lname_field']) && $settings['tp_dis_lname_field']=='yes'){ 	?>
			<div class="tp-field-group tp-lr-f-last-name">
				<?php
				if ( $settings['show_labels_reg']=='yes' && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')) {					
					echo '<label for="last_name'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['last_name_label'] ).'</label>';									
				}
				echo '<div class="tp-form-controls">';				
				echo '<input type="text" name="last_name" id="last_name'.esc_attr($id).'" placeholder="'.esc_html( $settings['last_name_placeholder'] ).'" class="tp-input tp-reg-f-load" required>';
				if(!empty($settings['tp_honeypot_opt']) && $settings['tp_honeypot_opt']=='yes'){
					echo '<input type="text" name="tphoney-last_name" id="tphoney_last_name'.esc_attr($id).'" class="tp-honey-input">';
				}
				echo '</div>';

				?>
			</div>
			<?php }
				} 
				
			/*username field start*/
			if(!empty($settings['tp_dis_username_field']) && $settings['tp_dis_username_field']=='yes'){
				echo '<div class="tp-field-group tp-lr-f-user-name">';
						if ( $settings['show_labels_reg']=='yes' && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')) {
							echo '<label for="user_login'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['user_name_label'] ).'</label>';
						}
					echo '<div class="tp-form-controls">';				
						echo '<input type="text" name="user_login" id="user_login'.esc_attr($id).'" placeholder="'.esc_html( $settings['user_name_placeholder'] ).'" class="tp-input tp-reg-f-load" required>';
						if(!empty($settings['tp_honeypot_opt']) && $settings['tp_honeypot_opt']=='yes'){
							echo '<input type="text" name="tphoney-user_login" id="tphoney_user_login'.esc_attr($id).'" class="tp-honey-input">';
						}
					echo '</div>';
				echo '</div>';				
			}
			/*username field end*/
			?>
			
			<div class="tp-field-group tp-lr-f-email">
				<?php
				if ( $settings['show_labels_reg']=='yes' && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')){					
					echo '<label for="user_email'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['email_label'] ).'</label>';						
				}
				echo '<div class="tp-form-controls">';				
				echo '<input type="email" name="user_email" id="user_email'.esc_attr($id).'" placeholder="'.esc_html( $settings['email_placeholder'] ).'" class="tp-input tp-reg-f-load" required>';
				if(!empty($settings['tp_honeypot_opt']) && $settings['tp_honeypot_opt']=='yes'){
					echo '<input type="text" name="tphoney-user_email" id="tphoney_user_email'.esc_attr($id).'" class="tp-honey-input">';
				}
				echo '</div>';
				?>
			</div>
			
			<?php 
			/*password field start*/ 
			
			if(!empty($settings['tp_dis_pass_field']) && $settings['tp_dis_pass_field']=='yes'){
			
			$pattern_pass_reg='';
			$tp_dis_pass_field_strong = ( $settings['tp_dis_pass_field_strong'] == 'yes' ) ? 'yes' : 'no';			
			if(!empty($tp_dis_pass_field_strong) && $tp_dis_pass_field_strong=='yes'){
				if(!empty($settings['tp_dis_pass_pattern'])){
					if($settings['tp_dis_pass_pattern']=='pattern-1'){
						$pattern_pass_reg='pattern=(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}';
					}else if($settings['tp_dis_pass_pattern']=='pattern-2'){
						$pattern_pass_reg='pattern=^(?=.*\d).{4,8}$';
					}else if($settings['tp_dis_pass_pattern']=='pattern-3'){
						$pattern_pass_reg='pattern=^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*)[0-9a-zA-Z]{6,}$';
					}else if($settings['tp_dis_pass_pattern']=='pattern-4'){
						//$pattern_pass_reg='pattern="(?-i)(?=^.{8,}$)((?!.*\s)(?=.*[A-Z])(?=.*[a-z]))(?=(1)(?=.*\d)|.*[^A-Za-z0-9])^.*$"';
						$pattern_pass_reg='pattern=(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z0-9]+.*).{8,}';
					}else if($settings['tp_dis_pass_pattern']=='pattern-5'){
						//$pattern_pass_reg='pattern="(?-i)(?=^.{8,}$)((?!.*\s)(?=.*[A-Z])(?=.*[a-z]))((?=(.*\d){1,})|(?=(.*\W){1,}))^.*$"';
						$pattern_pass_reg='pattern=(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}';
					}
				}				
			}
			
			$dpm_div='';
			if(!empty($settings['tp_dis_pass_meter']) && $settings['tp_dis_pass_meter']=='yes'){
				$dpm_div ='<div class="password-strength-wrapper '.$settings['psm_style'].' '.$settings['psm_style2_in'].'">';
					if((!empty($settings['psm_text_switch']) && $settings['psm_text_switch']=='yes') && !empty($settings['psm_text'])){
						$dpm_div .= esc_html($settings['psm_text']);
					}					
				$dpm_div .= '<span id="password-strength"></span></div>';
			}			
			?>			
									
			<div class="tp-field-group tp-lr-f-user-pass">
				<?php
				if ( $settings['show_labels_reg']=='yes' && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')){					
					echo '<label for="user_password'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['r_password_label'] ).'</label>';	
					if((!empty($settings['psm_style']) && $settings['psm_style']=='style-2') && (!empty($settings['psm_style2_in']) && $settings['psm_style2_in']=='after-label')){
						echo $dpm_div;
					}
				}
				$rfpm_class='';
				if((!empty($settings['psm_style']) && $settings['psm_style']=='style-2') && (!empty($settings['psm_style2_in']) && ($settings['psm_style2_in']=='inline-filed' ))){
					$rfpm_class='tp-form-rf-meter';
				}
				echo '<div class="tp-form-controls '.$rfpm_class.'">';				
				echo '<input type="password" name="user_password" id="user_password'.esc_attr($id).'" placeholder="'.esc_html( $settings['r_password_placeholder'] ).'" class="tp-input tp-reg-f-load tp-reg-pass-hint" required '.$pattern_pass_reg.'>';
				
				$passshowicon=$passhideicon='';
				if(!empty($settings['tp_dis_show_pass_icon']) && $settings['tp_dis_show_pass_icon']=='yes'){
					if(!empty($settings['showicon'])){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['showicon'], [ 'aria-hidden' => 'true' ]);
						$passshowicon = ob_get_contents();
						ob_end_clean();	
					}
					if(!empty($settings['hideicon'])){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['hideicon'], [ 'aria-hidden' => 'true' ]);
						$passhideicon = ob_get_contents();
						ob_end_clean();	
					}
					echo '<span toggle="#user_password'.esc_attr($id).'" class="tp-password-field-show tpsi" data-passshowicon="'.esc_html($passshowicon).'" data-passhideicon="'.esc_html($passhideicon).'">'.$passshowicon.'</span>';					
				}
				//hint
				$passshowiconh='';
				if((!empty($settings['tp_dis_pass_pattern'])) && (!empty($settings['tp_dis_pass_hint']) && $settings['tp_dis_pass_hint']=='yes') && !empty($settings['dis_pass_hint_on']) && $settings['dis_pass_hint_on']=='pshc'){	
					if(!empty($settings['showiconh'])){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['showiconh'], [ 'aria-hidden' => 'true' ]);
						$passshowiconh = ob_get_contents();
						ob_end_clean();	
					}					
					echo '<span toggle="#user_password'.esc_attr($id).'" class="tp-password-field-showh tpsi">'.$passshowiconh.'</span>';					
				}
				
				if(!empty($settings['tp_honeypot_opt']) && $settings['tp_honeypot_opt']=='yes'){
					echo '<input type="password" name="tphoney-user_password" id="tphoney_user_password'.esc_attr($id).'" class="tp-honey-input">';
				}
				if((!empty($settings['psm_style']) && $settings['psm_style']=='style-2') && (!empty($settings['psm_style2_in']) && ($settings['psm_style2_in']=='inline-filed' || $settings['psm_style2_in']=='after-field'))){
					echo $dpm_div;
				}
				echo '</div>';
				?>
			</div>
			<?php
			if((!empty($settings['tp_dis_pass_pattern'])) && (!empty($settings['tp_dis_pass_hint']) && $settings['tp_dis_pass_hint']=='yes') && !empty($settings['dis_pass_hint_on'])){
				
				$label_1_145 = !empty($settings['label_1_145']) ? $settings['label_1_145'] : '';
				$label_2_123 = !empty($settings['label_2_123']) ? $settings['label_2_123'] : '';
				$label_3_13 = !empty($settings['label_3_13']) ? $settings['label_3_13'] : '';
				$label_4_1 = !empty($settings['label_4_1']) ? $settings['label_4_1'] : '';
				$label_5_2 = !empty($settings['label_5_2']) ? $settings['label_5_2'] : '';
				$label_6_3 = !empty($settings['label_6_3']) ? $settings['label_6_3'] : '';
				$label_7_45 = !empty($settings['label_7_45']) ? $settings['label_7_45'] : '';
				$label_8_4 = !empty($settings['label_8_4']) ? $settings['label_8_4'] : '';
				$label_9_5 = !empty($settings['label_9_5']) ? $settings['label_9_5'] : '';

				echo '<ul class="tp-pass-indicator '.esc_attr( $settings['dis_pass_hint_on']).' '.esc_attr( $settings['tp_dis_pass_pattern'] ).' '.esc_attr( $settings['dis_pass_hint_layout'] ).'">';
				if($settings['tp_dis_pass_pattern']=='pattern-1' || $settings['tp_dis_pass_pattern']=='pattern-4' || $settings['tp_dis_pass_pattern']=='pattern-5'){
					echo '<li><span class="tp-min-eight-character"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_1_145).'</li>';
				}
				if($settings['tp_dis_pass_pattern']=='pattern-1' || $settings['tp_dis_pass_pattern']=='pattern-2' || $settings['tp_dis_pass_pattern']=='pattern-3'){					
					echo '<li><span class="tp-one-number"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_2_123).'</li>';
				}
				if($settings['tp_dis_pass_pattern']=='pattern-1' || $settings['tp_dis_pass_pattern']=='pattern-3'){
					echo '<li><span class="tp-low-lat-case"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_3_13).'</li>';
				}
				
				if($settings['tp_dis_pass_pattern']=='pattern-1'){					
					echo '<li><span class="tp-one-special-char"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_4_1).'</li>';
				}
				
				if($settings['tp_dis_pass_pattern']=='pattern-2'){
					echo '<li class=""><span class="tp-four-eight-character"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_5_2).'</li>';
				}
				
				if($settings['tp_dis_pass_pattern']=='pattern-3'){
					echo '<li class=""><span class="tp-min-six-character"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_6_3).'</li>';
				}
				
				if($settings['tp_dis_pass_pattern']=='pattern-4' || $settings['tp_dis_pass_pattern']=='pattern-5'){	
					echo '<li><span class="tp-low-upper-case"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_7_45).'</li>';
				}
				if($settings['tp_dis_pass_pattern']=='pattern-4'){
					echo '<li><span class="tp-digit-alpha"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_8_4).'</li>';
				}
				
				if($settings['tp_dis_pass_pattern']=='pattern-5'){
					echo '<li><span class="tp-number-special"><i class="fas fa-question-circle" aria-hidden="true"></i></span>'.esc_html($label_9_5).'</li>';
				}
								
				echo '</ul>';
			}			
			
			if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){
			?>
				<div class="tp-field-group tp-lr-f-user-conf-pass">
					<?php
					if ( $settings['show_labels_reg']=='yes' && ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register')){
						echo '<label for="user_conf_password'.esc_attr($id).'" class="tp-form-label">'.esc_html( $settings['r_conf_password_label'] ).'</label>';
					}
					echo '<div class="tp-form-controls">';				
					echo '<input type="password" name="user_conf_password" id="user_conf_password'.esc_attr($id).'" placeholder="'.esc_html( $settings['r_conf_password_placeholder'] ).'" class="tp-input tp-reg-f-load" required >';
					if(!empty($settings['tp_honeypot_opt']) && $settings['tp_honeypot_opt']=='yes'){
						echo '<input type="password" name="tphoney-user_conf_password" id="tphoney_user_conf_password'.esc_attr($id).'" class="tp-honey-input">';
					}
					echo '</div>';
					?>
				</div>			
			<?php 
			}
			
			if((!empty($settings['psm_style']) && $settings['psm_style']=='style-1')){
				echo $dpm_div;
			}
			if(!empty($settings['tp_dis_pass_meter']) && $settings['tp_dis_pass_meter']=='yes'){
				wp_enqueue_script( 'password-strength-meter' ); ?>
				
				<script>
				jQuery( document ).ready( function( $ ) {
					var rflm = 'form#tp-user-register<?php echo esc_attr($id); ?>';
					$( 'body' ).on( 'keyup', 'form#tp-user-register<?php echo esc_attr($id); ?> input[name=user_password]<?php if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){ ?>, form#tp-user-register<?php echo esc_attr($id); ?> input[name=user_conf_password] <?php } ?>', function( event ) {
						wdmChkPwdStrength(
							$('form#tp-user-register<?php echo esc_attr($id); ?> input[name=user_password]'),
							
							<?php if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){ ?>
								$('form#tp-user-register<?php echo esc_attr($id); ?> input[name=user_conf_password]'),
							<?php } ?>
							
							$('form#tp-user-register<?php echo esc_attr($id); ?> #password-strength'),
						   $('input[type=submit]'),
						   ['admin', 'happy', 'hello', '1234']
						);
					  });
					  <?php if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){ ?>
							  function wdmChkPwdStrength( $pwd,  $confirmPwd, $strengthStatus, $submitBtn, blacklistedWords ) {
					  <?php }else{ ?>
								 function wdmChkPwdStrength( $pwd,$strengthStatus, $submitBtn, blacklistedWords ) {
					  <?php } ?>
							var pwd = $pwd.val();
							<?php if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){ ?>
								var confirmPwd = $confirmPwd.val();
							 <?php } ?>
							blacklistedWords = blacklistedWords.concat( wp.passwordStrength.userInputDisallowedList() )
							$submitBtn.attr( 'disabled', 'disabled' );
							$strengthStatus.removeClass( 'short bad good strong' );
							
							<?php if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){ ?>
								var pwdStrength = wp.passwordStrength.meter( pwd, blacklistedWords, confirmPwd );
							<?php }else{ ?>
								var pwdStrength = wp.passwordStrength.meter( pwd, blacklistedWords );
							<?php } ?>
							
							switch ( pwdStrength ) {
								case 2:
								$strengthStatus.addClass( 'bad' ).html( pwsL10n.bad );
								$strengthStatus.closest('.tp-user-register').find('.password-strength-wrapper').addClass( 'show' );
								break;
								
								case 3:
								$strengthStatus.addClass( 'good' ).html( pwsL10n.good );
								$strengthStatus.closest('.tp-user-register').find('.password-strength-wrapper').addClass( 'show' );
								break;

								case 4:
								$strengthStatus.addClass( 'strong' ).html( pwsL10n.strong );
								$strengthStatus.closest('.tp-user-register').find('.password-strength-wrapper').addClass( 'show' );
								break;

								case 5:
								$strengthStatus.addClass( 'short' ).html( pwsL10n.mismatch );
								$strengthStatus.closest('.tp-user-register').find('.password-strength-wrapper').addClass( 'show' );

								default:
								$strengthStatus.addClass( 'short' ).html( pwsL10n.short );
								$strengthStatus.closest('.tp-user-register').find('.password-strength-wrapper').addClass( 'show' );

							}
							<?php if(!empty($settings['tp_dis_conf_pass_field']) && $settings['tp_dis_conf_pass_field']=='yes'){ ?>
								if ( (4 === pwdStrength && '' !== confirmPwd.trim()) ) {
									$submitBtn.removeAttr( 'disabled' );
								}
							<?php }else{ ?>
								if (4 === pwdStrength) {
									$submitBtn.removeAttr( 'disabled' );
								}
							<?php } ?> 
							return pwdStrength;
						}
					});
				</script>
			<?php
				}
				
				
				}	/*password field end*/ ?>
			<?php if ( $settings['show_additional_message'] ) : ?>
				<div class="tp-field-group tp-lr-f-add-msg">
					<span class="tp-register-additional-message"><?php echo esc_html( $settings['additional_message'] ); ?></span>
				</div>
			<?php endif;
				$id = 'lr'.$this->get_id();
				
				if( !empty($settings['form_selection']) && (($settings['form_selection']=='tp_register' && (!empty($settings['display_captcha_swtch']) && $settings['display_captcha_swtch']=='yes')) 
					|| ($settings['form_selection']=='tp_login_register' && (!empty($settings['display_captcha_swtch']) && $settings['display_captcha_swtch']=='yes')))	){
				
				$check_recaptcha= get_option( 'theplus_api_connection_data' );
				if(!empty($check_recaptcha['theplus_site_key_recaptcha'])){
					$site_key_captcha = $check_recaptcha['theplus_site_key_recaptcha'];
				?>
				<div class="tp-plus-re-captcha tp-lrfp-ff-<?php echo esc_attr( $settings['form_align'] ); ?>">
					<div id="inline-badge-<?php echo esc_attr($id); ?>"></div>
					
					<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=plus_onLoadReCaptcha<?php echo esc_attr($id); ?>"></script>
						<script>
						window.plus_onLoadReCaptcha<?php echo esc_attr($id); ?> = function() {
							var clientId = grecaptcha.render('inline-badge-<?php echo esc_attr($id); ?>', {
								'sitekey': '<?php echo $site_key_captcha; ?>',
								'badge': 'inline',
								'size': 'invisible'
							  });
							grecaptcha.ready(function() {
								grecaptcha.execute(clientId, {
								  action: 'register'
								})
								  .then(function(token) {
									jQuery('#tp-user-register<?php echo esc_attr($id); ?>').prepend('<input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response-<?php echo esc_attr($id); ?>" value="' + token + '">');
								});
							});
						}
						</script>		
						
				</div>
				<?php } } ?>
				
				<?php
			/*mailchimp field start*/ 
			if(!empty($settings['tp_mail_chimp_subscribe_opt']) && $settings['tp_mail_chimp_subscribe_opt']=='yes'){
				$tp_mail_chimp_subscribe_disable = isset($settings['tp_mail_chimp_subscribe_disable']) ? $settings['tp_mail_chimp_subscribe_disable'] : '';

				$tp_mcsd = 'checked';
				if($tp_mail_chimp_subscribe_disable=='yes'){
					$tp_mcsd = '';
				}
			?>
			<div class="tp-field-group tp-lr-f-mail-chimp-sub tp-lrfp-ff-<?php echo esc_attr( $settings['form_align'] ); ?>">
				<?php
				
				echo '<div class="tp-form-controls " style="display: flex;">';	
				echo '<input type="checkbox" name="user_mail_chimp_subscribe" id="user_mail_chimp_subscribe'.esc_attr($id).'"  class="tp-input" '.$tp_mcsd.'>';
				if(!empty($settings['form_align']) && $settings['form_align']=='left' || !empty($settings['form_align']) && $settings['form_align']=='center'){
					echo '<label class="user_mail_chimp_subscribe_checkbox" for="user_mail_chimp_subscribe'.esc_attr($id).'"></label>';
				}
				if ($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register'){
					echo '<label class="tp-form-label" for="user_mail_chimp_subscribe'.esc_attr($id).'">'.esc_html( $settings['r_mail_chimp_label'] ).'</label>';						
				}
				if(!empty($settings['form_align']) && $settings['form_align']=='right'){
					echo '<label class="user_mail_chimp_subscribe_checkbox" for="user_mail_chimp_subscribe'.esc_attr($id).'" style="padding-left:15px;margin-left:15px;"></label>';					
				}
				echo '</div>';
				echo '</div>';
			}
			/*mailchimp field start*/
			
			
			/*Terms of Conditions start*/ 
			if(!empty($settings['tp_terms_condition_opt']) && $settings['tp_terms_condition_opt']=='yes'){
			?>
			<div class="tp-field-group tp-lr-f-tac tp-lrfp-ff-<?php echo esc_attr( $settings['form_align'] ); ?>">
				<?php				
				echo '<div class="tp-form-controls " style="display: flex;">';	
				echo '<input type="checkbox" name="user_tac" id="user_tac'.esc_attr($id).'" class="tp-input" required>';
				if(!empty($settings['form_align']) && $settings['form_align']=='left' || !empty($settings['form_align']) && $settings['form_align']=='center'){
					echo '<label class="user_tac_checkbox" for="user_tac'.esc_attr($id).'"></label>';
				}
				if($settings['form_selection']=='tp_register' || $settings['form_selection']=='tp_login_register'){					
					echo '<label class="tp-form-label" for="user_tac'.esc_attr($id).'">'.$settings['r_terms_conition_label'].'</label>';						
				}
				if(!empty($settings['form_align']) && $settings['form_align']=='right'){
					echo '<label class="user_tac_checkbox" for="user_tac'.esc_attr($id).'" style="padding-left:15px;margin-left:15px;"></label>';
				}
				echo '</div>';
				echo '</div>';
			}
			/*Terms of Conditions end*/
			?>
			
			
			<div <?php echo $this->get_render_attribute_string( 'submit-group' ); ?>>				
				<button type="submit" class="tp-button" name="wp-submit">
					<?php if ( ! empty( $settings['button_text_reg'] ) ) : ?>
						<span><?php echo esc_html( $settings['button_text_reg'] ); ?></span>
					<?php endif; ?>
				</button>
			</div>
				
			<?php			
			$show_login = $settings['show_login'];
			if ( $show_login ) : ?>
			
				<div class="tp-field-group tp-user-register-password">
					<?php if (!empty($show_login) && $show_login=='yes') :
						if(!empty($settings['login_before_text'])){
							echo '<div class="login-before-text">'.esc_html( $settings['login_before_text'] ).'</div>';
						}
						 ?><a class="tp-login" href="<?php 
						if($settings['show_login_opt']=='default'){
							echo wp_login_url();
						}else if($settings['show_login_opt']=='custom'){	
							if ( ! empty( $settings['show_login_opt_link']['url'] ) ) {
								echo $settings['show_login_opt_link']['url'];								
							}
						}
						?>">
							<?php echo esc_html($settings['bottom_login_text']); ?>
						</a>
					<?php endif; ?>					
				</div>
				
			<?php endif; ?>
			
			<?php wp_nonce_field( 'ajax-login-nonce', 'tp-user-register-sc' ); 
				echo '<div class="theplus-notification"><div class="tp-lr-response"></div></div>';
			}
			?>
		</form>
		
		<?php
		
		$this->user_social_log_reg('register');
	}
	/*User Register Form*/	
	
	/*Login Ajax*/
	public function user_login_ajax_script() { 
	
		$settings    = $this->get_settings();
		$current_url = remove_query_arg( 'fake_arg' );
		$id          = 'lr'.$this->get_id();

		if ( $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}
		?>
		
		<script type="text/javascript">
			jQuery(document).ready(function($) {				
				//login start
				var login_form = 'form#tp-user-login<?php echo esc_attr($id); ?>';
				var loading_text='<span class="loading-spinner-log"><i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i></span><?php echo esc_html($settings["login_msg_loading_txt"]); ?>';
				var notverify='<span class="loading-spinner-log"><i class="far fa-times-circle" aria-hidden="true"></i></span><?php echo esc_html($settings["login_msg_validation"]); ?>';
				var incorrect_text='<span class="loading-spinner-log"><i class="far fa-times-circle" aria-hidden="true"></i></span><?php echo esc_html($settings["login_msg_error"]); ?>';
				var correct_text='<span class="loading-spinner-log"><i class="far fa-envelope" aria-hidden="true"></i></span><?php echo esc_html($settings["login_msg_success"]); ?>';
							
			    
			    $(login_form).on('submit', function(e){			        
			        $.ajax({
			            type: 'POST',
			            dataType: 'json',
			            url: theplus_ajax_url,
			            data: { 
			                'action': 'theplus_ajax_login',
			                'username': $(login_form + ' #user<?php echo esc_attr($id); ?>').val(), 
			                'password': $(login_form + ' #password<?php echo esc_attr($id); ?>').val(), 
			                'security': $(login_form + ' #tp-user-login-sc').val() 
			            },
						beforeSend: function(){							
							$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
							$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(loading_text);
						},
			            success: function(data) {							
			                if (data.loggedin == true){
								$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
								$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(correct_text);
			                    document.location.href = '<?php echo esc_url( $redirect_url ); ?>';
			                } else {
								$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
								$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(notverify);
			                }
			            },
			            error: function(data) {
							$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
							$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(incorrect_text);
						},
						complete: function(){
							setTimeout(function(){
										$("#tp-user-login<?php echo esc_attr($id);?> .theplus-notification").removeClass("active");	
									}, 1500);
						}
			        });
			        e.preventDefault();
				
			    });
				
				/*hover*/				
				$("#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-dropdown,#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-dropdown .lr-extra-div").on( "mouseenter",function() {
					$('#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-dropdown .lr-extra-div').show('slow')
				}).on( "mouseleave",function() {
					setTimeout(function() {
					if(!($('#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-dropdown:hover').length > 0))
						$('#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-dropdown .lr-extra-div').hide('slow');
					}, 200);
				});
				/*hover*/
				/*click popup*/
				$("#<?php echo esc_attr($id); ?>.tp-user-login .tp-lr-model-btn").on("click",function(){
					$("#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-modal .tp-modal-dialog").toggle('slow');
				});
				/*close icon*/
				$("#<?php echo esc_attr($id); ?>.tp-user-login .lr-close-custom_img").on("click",function(){					
					$("#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-modal .tp-modal-dialog").toggle('slow');					
				});
				
				/*close icon*/
				/*click popup*/
				/*popup*/
				$('#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-popup .tp-ulsp-trigger').on("click",function() {
					$('#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-popup .tp-modal-wrapper').toggleClass('open');
					$('#<?php echo esc_attr($id); ?>.tp-user-login.tp-user-login-skin-popup .tp-ulsp-page-wrapper').toggleClass('blur');
					return false;
   			    });
				/*popup*/
			
				/*lost password*/
					$("#tp-user-login<?php echo esc_attr($id); ?> .tp-lost-password").on("click",function(){					
						$("#tp-user-lost-password<?php echo esc_attr($id); ?>.tp-form-stacked-fp ").toggle();
					});
					  
					/*back*/
					$("#tp-user-lost-password<?php echo esc_attr($id); ?>.tp-form-stacked-fp .tp-lpu-back").on("click",function(){					
						$("#tp-user-lost-password<?php echo esc_attr($id); ?>.tp-form-stacked-fp").hide();
					});
					/*back*/
				/*lost password*/
			});
		</script>
		<?php
	}
	/*Login Ajax*/
	
	/*Register Ajax*/
	public function user_register_ajax_script() { 
	
		$settings = $this->get_settings();
		$id       = 'lr'.$this->get_id();
		if(!empty($settings['auto_loggedin']) && $settings['auto_loggedin']=='yes' && $settings['tp_dis_pass_field']=='yes'){
			$auto_loggedin = true;
		}else{
			$auto_loggedin = false;
		}
		
		$mc_custom_apikey=$mc_custom_listid='';
		$dis_mail_chimp=$settings['tp_mail_chimp_subscribe_opt'];
		if(!empty($dis_mail_chimp) && $dis_mail_chimp=='yes'){			
			if(!empty($settings['mc_custom_apikey']) && $settings['mc_custom_listid']){
				$mc_custom_apikey = $settings['mc_custom_apikey'];
				$mc_custom_listid = $settings['mc_custom_listid'];
			}				
		}
		
		if(!empty($settings['redirect_after_register']) && $settings['redirect_after_register']=='yes' && !empty($settings['redirect_url_reg']['url'])){
			$reg_redirect_page = $settings['redirect_url_reg']['url'];
		}else{
			$reg_redirect_page = '';
		}
		
		?>
		
		<script type="text/javascript">		
			jQuery(document).ready(function($) {				
				//register start
				var register_form = 'form#tp-user-register<?php echo esc_attr($id); ?>';
				var reg_loading_text='<span class="loading-spinner-reg"><i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i></span><?php echo esc_html($settings["reg_msg_loading"]); ?>';
				var reg_email_duplicate='<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span><?php echo esc_html($settings["reg_msg_email_duplication"]); ?>';
				var reg_incorrect_text='<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span><?php echo esc_html($settings["reg_msg_error"]); ?>';
				var reg_correct_text='<span class="loading-spinner-reg"><i class="far fa-envelope" aria-hidden="true"></i></span><?php echo esc_html($settings["reg_msg_success"]); ?>';
				var reg_redirect_page = "<?php echo $reg_redirect_page; ?>";
				
				<?php if((!empty($settings['cst_validation_switch']) && $settings['cst_validation_switch']=='yes')){ 					
					if(!empty($settings['tp_dis_name_field']) && $settings['tp_dis_name_field']=='yes'){
							if(!empty($settings['tp_dis_fname_field']) && $settings['tp_dis_fname_field']=='yes'){ ?>
								$(register_form + ' #first_name<?php echo esc_attr($id); ?>').keyup(function() {					
									var first_name = $(this).val();
									if($(this).hasClass('tp-reg-f-load')){
										$(this).removeClass( "tp-reg-f-load" );
									}
									$(".tp-reg-form-fn-error").remove();									
									if (first_name=='' || first_name== undefined) {
										$(this).after('<span class="tp-reg-form-fn-error tp-reg-form-error-field"><?php echo esc_html($settings["v_efn"]); ?></span>');
									}
								});
							<?php }
							if(!empty($settings['tp_dis_lname_field']) && $settings['tp_dis_lname_field']=='yes'){ ?>
								$(register_form + ' #last_name<?php echo esc_attr($id); ?>').keyup(function() {					
									var last_name = $(this).val();
									$(".tp-reg-form-ln-error").remove();
									if($(this).hasClass('tp-reg-f-load')){
										$(this).removeClass( "tp-reg-f-load" );
									}
									if (last_name=='' || last_name== undefined) {
										$(this).after('<span class="tp-reg-form-ln-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eln"]); ?></span>');
									}
								});
							<?php }
					} 
					
					if(!empty($settings['tp_dis_username_field']) && $settings['tp_dis_username_field']=='yes'){ ?>
						$(register_form + ' #user_login<?php echo esc_attr($id); ?>').keyup(function() {					
							var user_login = $(this).val();
							$(".tp-reg-form-un-error").remove();
							if($(this).hasClass('tp-reg-f-load')){
								$(this).removeClass( "tp-reg-f-load" );
							}
							if (user_login=='' || user_login== undefined) {
								$(this).after('<span class="tp-reg-form-un-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eun"]); ?></span>');
							}
						});
					<?php }
					
					if(!empty($settings['tp_dis_pass_field']) && $settings['tp_dis_pass_field']=='yes'){ ?>
						$(register_form + ' #user_password<?php echo esc_attr($id); ?>').keyup(function() {					
							var user_password = $(this).val();
							$(".tp-reg-form-pass-error").remove();
							if($(this).hasClass('tp-reg-f-load')){
								$(this).removeClass( "tp-reg-f-load" );
							}
							if (user_password=='' || user_password== undefined) {
								$(this).after('<span class="tp-reg-form-pass-error tp-reg-form-error-field"><?php echo esc_html($settings["v_epass"]); ?></span>');
							}
						});
						<?php if(!empty($settings['tp_dis_pass_field']) && $settings['tp_dis_pass_field']=='yes'){ ?>
							$(register_form + ' #user_conf_password<?php echo esc_attr($id); ?>').keyup(function() {					
								var user_conf_password = $(this).val();
								$(".tp-reg-form-repass-error").remove();
								if($(this).hasClass('tp-reg-f-load')){
									$(this).removeClass( "tp-reg-f-load" );
								}
								if (user_conf_password=='' || user_conf_password== undefined) {
									$(this).after('<span class="tp-reg-form-repass-error tp-reg-form-error-field"><?php echo esc_html($settings["v_erepass"]); ?></span>');
								}
							});
						<?php } ?>
					<?php } ?>					
					
					
					$(register_form + ' #user_email<?php echo esc_attr($id); ?>').keyup(function() {						
						var mailformat = /^w+([.-]?w+)*@w+([.-]?w+)*(.w{2,3})+$/;
						var user_email = $(this).val();
						$(".tp-reg-form-email-error").remove();
						if($(this).hasClass('tp-reg-f-load')){
							$(this).removeClass( "tp-reg-f-load" );
						}						
						if (user_email=='' || user_email== undefined) {
							$(this).after('<span class="tp-reg-form-email-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eemail"]); ?></span>');
						}else if(!user_email.match(mailformat)){
							$(this).after('<span class="tp-reg-form-email-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eemail"]); ?></span>');
						}						
					});				
					
					
				<?php } ?>
				
			    $(register_form).on('submit', function(e){
			       if($(register_form + ' #user_mail_chimp_subscribe<?php echo esc_attr($id); ?>').prop('checked')== true){
						var mail_chimp_check = 'yes';
				    }else{
						var mail_chimp_check = 'no';
					}
					
					<?php
					if(!empty($settings['mcl_double_opt_in']) && $settings['mcl_double_opt_in']=='yes'){
						$mcl_double_opt_in = 'yes';
					}else{
						$mcl_double_opt_in = 'no';
					}
					
					$mc_cst_group_value=$mc_cst_tags_value='';
					if((!empty($settings['mc_cst_group']) && $settings['mc_cst_group']=='yes') && !empty($settings["mc_cst_group_value"])){
						$mc_cst_group_value=$settings["mc_cst_group_value"];
					}
					if((!empty($settings['mc_cst_tag']) && $settings['mc_cst_tag']=='yes') && !empty($settings["mc_cst_tags_value"])){
						$mc_cst_tags_value=$settings["mc_cst_tags_value"];
					}
					?>
					var validate = true;
					
					<?php if((!empty($settings['cst_validation_switch']) && $settings['cst_validation_switch']=='yes')){ 
						
					?>
						
						var first_name = $(register_form + ' #first_name<?php echo esc_attr($id); ?>').val();						
						var last_name = $(register_form + ' #last_name<?php echo esc_attr($id); ?>').val();						
						var user_login = $(register_form + ' #user_login<?php echo esc_attr($id); ?>').val();						
						var user_email = $(register_form + ' #user_email<?php echo esc_attr($id); ?>').val();						
						var user_password = $(register_form + ' #user_password<?php echo esc_attr($id); ?>').val();						
						var user_conf_password = $(register_form + ' #user_conf_password<?php echo esc_attr($id); ?>').val();	
						
						<?php if(!empty($settings['tp_dis_name_field']) && $settings['tp_dis_name_field']=='yes'){
								if(!empty($settings['tp_dis_fname_field']) && $settings['tp_dis_fname_field']=='yes'){  ?>
									$(".tp-reg-form-fn-error").remove();
									if($(this).hasClass('tp-reg-f-load')){
										$(this).removeClass( "tp-reg-f-load" );
									}
									if (first_name=='' || first_name== undefined) {
										validate = false;
									  $('#first_name<?php echo esc_attr($id);?>').after('<span class="tp-reg-form-fn-error tp-reg-form-error-field"><?php echo esc_html($settings["v_efn"]); ?></span>');
									}
								<?php }
								if(!empty($settings['tp_dis_lname_field']) && $settings['tp_dis_lname_field']=='yes'){ ?>
									$(".tp-reg-form-ln-error").remove();
									if($(this).hasClass('tp-reg-f-load')){
										$(this).removeClass( "tp-reg-f-load" );
									}
									if (last_name=='' || last_name== undefined) {
										validate = false;
									  $('#last_name<?php echo esc_attr($id);?>').after('<span class="tp-reg-form-ln-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eln"]); ?></span>');
									}
								<?php }
						}
						
						if(!empty($settings['tp_dis_username_field']) && $settings['tp_dis_username_field']=='yes'){ ?>
							$(".tp-reg-form-un-error").remove();
							if($(this).hasClass('tp-reg-f-load')){
								$(this).removeClass( "tp-reg-f-load" );
							}
							if (user_login=='' || user_login== undefined) {
								validate = false;
							  $('#user_login<?php echo esc_attr($id);?>').after('<span class="tp-reg-form-un-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eun"]); ?></span>');
							}
						<?php } ?>
						
						var mailformat = /^w+([.-]?w+)*@w+([.-]?w+)*(.w{2,3})+$/;
						$(".tp-reg-form-email-error").remove();
						if($(this).hasClass('tp-reg-f-load')){
							$(this).removeClass( "tp-reg-f-load" );
						}						
						if (user_email=='' || user_email== undefined) {
							validate = false;
						  $('#user_email<?php echo esc_attr($id);?>').after('<span class="tp-reg-form-email-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eemail"]); ?></span>');
						}else if(!user_email.match(mailformat)){
							$(this).after('<span class="tp-reg-form-email-error tp-reg-form-error-field"><?php echo esc_html($settings["v_eemail"]); ?></span>');
						}
						
						<?php if(!empty($settings['tp_dis_pass_field']) && $settings['tp_dis_pass_field']=='yes'){ ?>
								$(".tp-reg-form-pass-error").remove();
								if($(this).hasClass('tp-reg-f-load')){
									$(this).removeClass( "tp-reg-f-load" );
								}
								if (user_password=='' || user_password== undefined) {
									validate = false;
								  $('#user_password<?php echo esc_attr($id);?>').after('<span class="tp-reg-form-pass-error tp-reg-form-error-field"><?php echo esc_html($settings["v_epass"]); ?></span>');
								}
								
								$(".tp-reg-form-repass-error").remove();
								if($(this).hasClass('tp-reg-f-load')){
									$(this).removeClass( "tp-reg-f-load" );
								}
								if (user_conf_password=='' || user_conf_password== undefined) {
									validate = false;
								  $('#user_conf_password<?php echo esc_attr($id);?>').after('<span class="tp-reg-form-repass-error tp-reg-form-error-field"><?php echo esc_html($settings["v_erepass"]); ?></span>');
								}
						<?php } 
						} ?>
					
					if(validate){
						$(register_form + ' button.tp-button').attr("disabled", true);
			        $.ajax({
			            type: 'POST',
			            dataType: 'json',
			            url: theplus_ajax_url,
			            data: { 
			                'action': 'theplus_ajax_register', //calls wp_ajax_nopriv
			                'first_name': $(register_form + ' #first_name<?php echo esc_attr($id); ?>').val(), 
			                'last_name': $(register_form + ' #last_name<?php echo esc_attr($id); ?>').val(), 
			                'user_login': $(register_form + ' #user_login<?php echo esc_attr($id); ?>').val(), 
			                'email': $(register_form + ' #user_email<?php echo esc_attr($id); ?>').val(), 
			                'password': $(register_form + ' #user_password<?php echo esc_attr($id); ?>').val(), 
			                'conf_password': $(register_form + ' #user_conf_password<?php echo esc_attr($id); ?>').val(), 
			                'security': $(register_form + ' #tp-user-register-sc').val(),
							'token':$(register_form + ' .g-recaptcha-response-<?php echo esc_attr($id); ?>').val(),
							'dis_cap':$(register_form).data('dis_cap'), 
							'tceo':$(register_form).data('tceo'), 
							'dis_password':$(register_form).data('dis_password'), 
							'dis_password_conf':$(register_form).data('dis_password_conf'), 
							'dis_mail_chimp':$(register_form).data('dis_mail_chimp'),
							'mail_chimp_check': mail_chimp_check,
							'mcl_double_opt_in':"<?php echo $mcl_double_opt_in; ?>",
							'mc_cst_group_value':"<?php echo $mc_cst_group_value; ?>",
							'mc_cst_tags_value':"<?php echo $mc_cst_tags_value; ?>",
							'auto_loggedin': '<?php echo $auto_loggedin; ?>',
							'mc_custom_apikey': '<?php echo $mc_custom_apikey; ?>',
							'mc_custom_listid': '<?php echo $mc_custom_listid; ?>',
			            },
						beforeSend: function(){							
							$(register_form+" .theplus-notification").addClass("active");
							$(register_form+" .theplus-notification .tp-lr-response").html(reg_loading_text);
						},
			            success: function(data) {						
			                if (data.registered == true){
								$(register_form+" .theplus-notification").addClass("active");
								$(register_form+" .theplus-notification .tp-lr-response").html(reg_correct_text);
			                	if(reg_redirect_page!='' && reg_redirect_page!= undefined){
									document.location.href = reg_redirect_page;
			                	}else{
									location.reload(true);
								}
			                }else if(data.registered == false){								
								$(register_form+" .theplus-notification").addClass("active");
								$(register_form+" .theplus-notification .tp-lr-response").html(data.message);
			                }
			        		$(register_form + ' button.tp-button').removeAttr("disabled");
			            },
						 error: function(data) {
							$(register_form+" .theplus-notification").addClass("active");
							$(register_form+" .theplus-notification .tp-lr-response").html(reg_incorrect_text);
						},
						complete: function(){
							setTimeout(function(){
								$(register_form+" .theplus-notification").removeClass("active");	
							}, 1500);
						}
			        });
					}
			        e.preventDefault();

			    });
				
				/*hover*/
				$("#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-dropdown,#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-dropdown .lr-extra-div").on( "mouseenter",function() {
					$('#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-dropdown .lr-extra-div').show('slow')
				}).on( "mouseleave",function() {
					setTimeout(function() {
					if(!($('#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-dropdown:hover').length > 0))
						$('#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-dropdown .lr-extra-div').hide('slow');
					}, 200);
				});
				/*hover*/
				
				/*click popup*/
				$("#<?php echo esc_attr($id); ?>.tp-user-register .tp-lr-model-btn").on("click",function(){
					$("#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-modal .tp-modal-dialog").toggle('slow');
				});
				/*close icon*/
				$("#<?php echo esc_attr($id); ?>.tp-user-register .lr-close-custom_img").on("click",function(){					
					$("#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-modal .tp-modal-dialog").toggle('slow');					
				});
				
				/*close icon*/
				/*click popup*/
				
				/*popup*/
				$('#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-popup .tp-ursp-trigger').on("click",function() {
					$('#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-popup .tp-modal-wrapper').toggleClass('open');
					$('#<?php echo esc_attr($id); ?>.tp-user-register.tp-user-register-skin-popup .tp-ursp-page-wrapper').toggleClass('blur');
					return false;
   			    });
				/*popup*/
				
				/*lost password*/
					$("#tp-user-register<?php echo esc_attr($id); ?> .tp-lost-password").on("click",function(){					
						$("#tp-user-lost-password<?php echo esc_attr($id); ?>.tp-form-stacked-fp ").toggle();
					});
					/*back*/
					$("#tp-user-lost-password<?php echo esc_attr($id); ?>.tp-form-stacked-fp .tp-lpu-back").on("click",function(){					
						$("#tp-user-lost-password<?php echo esc_attr($id); ?>.tp-form-stacked-fp").hide();
					});
					/*back*/
				/*lost password*/
			});
		</script>
		<?php
	}
	/*Register Ajax*/
	
	/*social login start*/	
	public function user_social_log_reg($type='') {
		$settings = $this->get_settings();
		$tp_sl_google = !empty($settings["tp_sl_google"]) ? $settings["tp_sl_google"] : "";
		$tp_sl_google_type = !empty($settings["tp_sl_google_type"]) ? $settings["tp_sl_google_type"] : "standard";
		$tp_sl_google_theme = !empty($settings["tp_sl_google_theme"]) ? $settings["tp_sl_google_theme"] : "outline";
		$tp_sl_google_shape = !empty($settings["tp_sl_google_shape"]) ? $settings["tp_sl_google_shape"] : "rectangular";
		$tp_sl_google_text = !empty($settings["tp_sl_google_text"]) ? $settings["tp_sl_google_text"] : "signin_with";
		$tp_sl_google_size = !empty($settings["tp_sl_google_size"]) ? $settings["tp_sl_google_size"] : "large";
		$tp_sl_google_custom_width = !empty($settings["tp_sl_google_custom_width"]) ? $settings["tp_sl_google_custom_width"] : "100";
		$tp_google_onetap = !empty($settings["tp_google_onetap"]) ? $settings["tp_google_onetap"] : "";
		$tp_sl_layout_opt = !empty($settings['tp_sl_layout_opt']) ? $settings['tp_sl_layout_opt'] : '';
		$tp_sl_google_theme = !empty($settings['tp_sl_google_theme']) ? $settings['tp_sl_google_theme'] : '';
		$tp_sl_google_longtitle = !empty($settings['tp_sl_google_longtitle']) ? $settings['tp_sl_google_longtitle'] : '';
		$redirect_url_social = !empty($settings['redirect_url_social']['url']) ? $settings['redirect_url_social']['url'] : '';
		
		/*condition*/
		$id          = 'lr'.$this->get_id();
		$gid='';
		if($type=='login'){
			$action = 'theplus_ajax_facebook_login';
			$gid = 'login'.$this->get_id();
		}else if($type=='register'){
			$action = 'theplus_ajax_facebook_login';
			$gid = 'register'.$this->get_id();
		}
				
		if(((!empty($settings['tp_sl_facebook']) && $settings['tp_sl_facebook']=="yes") || (!empty($settings['tp_sl_google']) && $settings['tp_sl_google']=="yes"))){
			
			if(!empty($settings['tp_sl_layout_opt']) && $settings['tp_sl_layout_opt']=='tp_sl_layout_opt_1'){
				echo '<style>div#g-signin2-'.esc_attr( $gid ).' .abcRioButton,div#g-signin2-'.esc_attr( $gid ).' .abcRioButton{height:24px!important;width:70px!important}div#g-signin2-'.$gid.' .abcRioButton .abcRioButtonIcon,div#g-signin2-'.esc_attr( $gid ).' .abcRioButton .abcRioButtonIcon{padding:5px!important}div#g-signin2-'.esc_attr( $gid ).' .abcRioButton .abcRioButtonIcon .abcRioButtonSvgImageWithFallback,div#g-signin2-'.esc_attr( $gid ).' .abcRioButton .abcRioButtonIcon .abcRioButtonSvgImageWithFallback{width:16px!important;height:16px!important}div#g-signin2-'.esc_attr( $gid ).' .abcRioButtonContentWrapper .abcRioButtonContents,div#g-signin2-'.esc_attr( $gid ).' .abcRioButtonContentWrapper .abcRioButtonContents{font-size:11px!important;line-height:1!important;margin:0}div#g-signin2-'.$gid.' .abcRioButtonContentWrapper,div#g-signin2-'.esc_attr( $gid ).' .abcRioButtonContentWrapper{display:flex;align-items:center}div#g-signin2-'.esc_attr( $gid ).',div#g-signin2-'.esc_attr( $gid ).'{margin-left:15px;border-radius:3px!important;overflow:hidden}</style>';
			}	
			if(!empty($settings['tp_sl_layout_opt']) && $settings['tp_sl_layout_opt']=='tp_sl_layout_opt_2'){
				echo '<style>.tp-social-login-wrapper > div{margin-right: 10px;}.tp-wp-lrcf .tp-social-login-wrapper{margin-top:10px;}</style>';
			}			
		
		$mcl_double_opt_in = (!empty($settings['mcl_double_opt_in'])) ? $settings['mcl_double_opt_in'] : 'no';
		
		$mc_cst_group_value=$mc_cst_tags_value='';
		if((!empty($settings['mc_cst_group']) && $settings['mc_cst_group']=='yes') && !empty($settings["mc_cst_group_value"])){
			$mc_cst_group_value=$settings["mc_cst_group_value"];
		}
		if((!empty($settings['mc_cst_tag']) && $settings['mc_cst_tag']=='yes') && !empty($settings["mc_cst_tags_value"])){
			$mc_cst_tags_value=$settings["mc_cst_tags_value"];
		}
		
		echo '<div class="tp-social-login-wrapper">';
		if((!empty($settings['tp_sl_facebook']) && $settings['tp_sl_facebook']=='yes')){
		$check_fb_appid= get_option( 'theplus_api_connection_data' );
		$facebook_appid = (!empty($check_fb_appid['theplus_facebook_app_id'])) ? $check_fb_appid['theplus_facebook_app_id'] : '';		
		$nonce = wp_create_nonce( 'ajax-login-nonce' );
		?>
		<script>		
		  
		  function statusChangeCallback(response,type='') {  			
			if (response.status === 'connected') { 
			  facebook_fetch_info(response, type);			  
			} else { 
			}
		  }
			
		  function checkloginstatus(type='') {
			  FB.login(function(e) {
					e.authResponse && statusChangeCallback(e,type);
			  }, {
				scope: "email"
              });
			/*FB.getLoginStatus(function(response) {
			  statusChangeCallback(response,type);
			});*/
		  }


		  window.fbAsyncInit = function() {
			FB.init({
			  appId      : '<?php echo esc_attr( $facebook_appid ); ?>',
			  cookie     : true,
			  xfbml      : true,
			  version    : 'v7.0'
			});
			/*FB.login(function(e) {
					e.authResponse && statusChangeCallback(e);
			  }, {
				scope: "email"
			  });
			FB.getLoginStatus(function(response) {   
			  statusChangeCallback(response);        
			});*/
		  };
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			  }(document, 'script', 'facebook-jssdk')); 
		 
		  function facebook_fetch_info(response,type) {
			FB.api('/me',{ fields: 'id, name, first_name, last_name, email, link, gender, locale, picture' }, function(res) {			 
			var action = 'theplus_ajax_facebook_login';
			if(response.authResponse.accessToken && res.id && res.email && action){
				var facebook_fetch_data = {							
							'action' : action,
							'accessToken'  : response.authResponse.accessToken,
							'id'  : res.id,
							'name' : res.name,
							'first_name' : res.first_name,
							'last_name' : res.last_name,
							'email' : res.email,
							'link' : res.link,
							'nonce' : "<?php echo $nonce; ?>",
						};
				jQuery.ajax( {
						type: 'POST',
			            dataType: 'json',
			            url: theplus_ajax_url,
			            data: facebook_fetch_data,
						success: function( data ) {				
							if( data.loggedin === true || data.registered === true) {
								//$scope.find( '.status' ).addClass( 'success' ).text( 'Thanks for logging in, ' + res.name + '!' );
								if( '<?php echo !empty(esc_url( $settings['redirect_url_social']['url'])); ?>'){
									window.location = '<?php echo esc_url( $settings['redirect_url_social']['url'] ); ?>';
								}else{
									location.reload();
								}	
							}
						}
				});
			}
			});
			
		  }
		
		</script>
		<?php
		 if(!empty($settings['tp_sl_layout_opt']) && $settings['tp_sl_layout_opt']=='tp_sl_layout_opt_2'){ ?>
			 <div class="fb-login-button" data-size="large" data-button-type="continue_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="false" data-width="" data-height="200px" onlogin="checkloginstatus('<?php echo esc_attr( $type ); ?>');"></div>
		<?php }else { ?>
			 <fb:login-button scope="public_profile,email" onlogin="checkloginstatus('<?php echo esc_attr( $type ); ?>');"></fb:login-button>
		<?php } ?>		
			<div id="status"></div>
		<?php
		}
		/*facebook login end*/
		
		echo '</div>';		
		}
		
	}
	/*social login end*/
	
	/*Forgot Password Ajax*/
	public function user_forgot_pass_ajax_script() {
	
		$settings = $this->get_settings();
		$id       = 'lr'.$this->get_id();
		
		if(!empty($settings['reset_pass_url'])){	
			$reset_url = get_permalink($settings['reset_pass_url']);
			$forgot_url = get_the_permalink();
		}else{
			$reset_url = get_the_permalink();
			$forgot_url = get_the_permalink();
		}
		
		$tceol=array();
		if(!empty($settings['tp_cst_email_lost_opt']) && $settings['tp_cst_email_lost_opt'] == 'yes'){
			$tpces = !empty($settings['tp_cst_email_lost_subject']) ? $settings['tp_cst_email_lost_subject'] : '';
			$tpcem = !empty($settings['tp_cst_email_lost_message']) ? $settings['tp_cst_email_lost_message'] : '';
			
			$tceol["tp_cst_email_lost_opt"] = $settings['tp_cst_email_lost_opt'];
			$tceol["tp_cst_email_lost_subject"] = $tpces;
			$tceol["tp_cst_email_lost_message"] = $tpcem;
		}		

		$data_forgot = [];
		$data_forgot['f_p_opt'] =$settings['f_p_opt'];		
		$data_forgot['reset_url'] =$reset_url;
		$data_forgot['forgot_url'] =$forgot_url;
		$data_forgot['tceol'] = $tceol;
		$data_forgot['fp_correct_email'] = $settings['fp_msg_success'];
		$data_forgot['fp_err_msg'] = $settings['fp_msg_error'];
		$data_forgot['noncesecure'] =wp_create_nonce( 'tp_user_lost_password_action' );
		
		$generate_key= tp_plus_simple_decrypt( json_encode($data_forgot), 'ey' );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				
				var forgot_pass_form = 'form#tp-user-lost-password<?php echo esc_attr($id); ?>';
				var fp_loading_text='<span class="loading-spinner-fp"><i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i></span><?php echo esc_html($settings["fp_msg_loading"]); ?>';
				var fp_loading='<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>';
				var fp_correct_email='<span class="loading-spinner-reg"><i class="far fa-envelope" aria-hidden="true"></i></span><?php echo esc_html($settings["fp_msg_success"]); ?>';
				var fp_err_msg='<span class="loading-spinner-reg"><i class="far fa-envelope" aria-hidden="true"></i></span><?php echo esc_html($settings["fp_msg_error"]); ?>';
			    
			    $(forgot_pass_form).on('submit', function(e){
			        $.ajax({
			            type: 'POST',
			            dataType: 'json',
			            url: theplus_ajax_url,
			            data: { 
			                'action': 'theplus_ajax_forgot_password',
			                'user_login': $(forgot_pass_form + ' #user_login<?php echo esc_attr($id); ?>').val(),
							'tpforgotdata': '<?php echo esc_html( $generate_key ); ?>',
			            },
						beforeSend: function(){							
							$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
							$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(fp_loading_text);
						},
			            success: function(data) {
						
								if(data.message){
									$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
									$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(fp_loading + data.message);
								}else{
									$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification").addClass("active");
									$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification .tp-lr-response").html(fp_loading + 'Is Not Working Server Issue...');
								}
			            },
						complete: function(){
							setTimeout(function(){
								$("#tp-user-lost-password<?php echo esc_attr($id);?> .theplus-notification").removeClass("active");	
							}, 3200);
						}
			        });
			        e.preventDefault();
					
			    });
			
			});
		</script>
		<?php
	}
}

