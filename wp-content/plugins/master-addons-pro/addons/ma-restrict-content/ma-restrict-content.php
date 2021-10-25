<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;
//	use MasterAddons\Inc\Classes\Controls\Templates\Master_Addons_Template_Controls as MA_Templates;

/*
	* TODO: Restrict Country Based Popup
	*/

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/12/19
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Restrict_Content extends Widget_Base
{

	public function get_name()
	{
		return 'ma-el-restrict-content';
	}

	public function get_title()
	{
		return __('Restrict Content', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-lock-user';
	}

	public function get_keywords()
	{
		return ['password', 'password protected', 'Restrict content', 'protected content', 'age restriction', 'safe', 'age gate'];
	}

	public function get_categories()
	{
		return ['master-addons'];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/restrict-content-for-elementor/';
	}

	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim',
			'fancybox'
		];
	}

	public function get_script_depends()
	{
		return [
			'fancybox',
			'master-addons-scripts'
		];
	}

	protected function _register_controls()
	{

		/*
			 * Tab: Content
			 */

		$this->start_controls_section(
			'ma_el_restrict_content_section',
			[
				'label' => __('Restrict Type', MELA_TD)
			]
		);


		$this->add_control(
			'ma_el_restrict_content_layout_type',
			[
				'label'       => __('Restrict Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'onpage',
				'options'     => [
					'onpage'     		=> __('On Page', MELA_TD),
					'popup'     		=> __('Popup', MELA_TD)
				]
			]
		);



		$this->add_control(
			'ma_el_restrict_content_type',
			[
				'label'       => __('Restrict Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'user',
				'options'     => [
					'user'      		=> __('User Based', MELA_TD),
					'password'  		=> __('Password Based', MELA_TD),
					'age_restrict'      => __('Age Restriction', MELA_TD),
					'math_captcha'      => __('Math Captcha', MELA_TD)
				]
			]
		);


		$this->add_control(
			'ma_el_restrict_content_math_type',
			[
				'label'       => __('Math Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'add',
				'options'     => [
					'add'      			=> __('Add', MELA_TD),
					'subtract'  		=> __('Subtract', MELA_TD),
					'multiply'      	=> __('Multiply', MELA_TD)
				],
				'condition'   => [
					'ma_el_restrict_content_type' => 'math_captcha'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_user_role',
			[
				'label'       => __('Select User Roles', MELA_TD),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Master_Addons_Helper::jltma_user_roles(),
				'condition'   => [
					'ma_el_restrict_content_type' => 'user'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_pass',
			[
				'label'     => __('Set Password', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => '123456',
				'condition' => [
					'ma_el_restrict_content_type' => ['password']
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_age',
			[
				'label'     => __('Minimum Age', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => '19',
				'condition'   => [
					'ma_el_restrict_content_type' => 'age_restrict'
				]
			]
		);


		$this->add_control(
			'ma_el_restrict_age_type',
			[
				'label'       => __('Age Restrict Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'input_age',
				'options'     => [
					'input_age'      		=> __('Select Birthday', MELA_TD),
					'enter_age'     		=> __('Enter Age', MELA_TD),
					'age_checkbox'     		=> __('Checkbox', MELA_TD),
					'button_confirm'     	=> __('Submit Confirm', MELA_TD),

				],
				'condition'   => [
					'ma_el_restrict_content_type' => 'age_restrict'
				]
			]
		);


		//			MA_Templates::add_controls( $this, [
		//				'condition' => [
		//					'popup_type' => 'template',
		//				],
		//				'prefix' => 'popup_',
		//			] );


		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_restrict_content_popup_section',
			[
				'label' => __('Popup Settings', MELA_TD),
				'condition' => [
					'ma_el_restrict_content_layout_type' => 'popup'
				]
			]
		);


		$this->add_control(
			'ma_el_restrict_content_popup_type',
			[
				'label'       => __('Popup Type', MELA_TD),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default'     			=> __('Default Modal (Button Click)', MELA_TD),
					'windowload'     		=> __('Window Load & Default Modal', MELA_TD),
					'buttonfullscreen'     	=> __('Button Click & Full Screen', MELA_TD),
					'windowloadfullscreen'  => __('Window Load & Full Screen', MELA_TD)
				],
				'condition' => [
					'ma_el_restrict_content_layout_type' => ['popup']
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_popup_editor',
			[
				'label'     => __('Show in Editor', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		// $this->add_control(
		// 	'ma_el_restrict_content_age_animation',
		// 	[
		// 		'label'     => __( 'Popup Animation', MELA_TD ),
		// 		'type'      => Controls_Manager::ANIMATION,
		// 		'default'   => 'zoom-in',
		// 		'prefix_class' => 'animated ',
		// 		'selector' => '{{WRAPPER}} .fancybox-container'
		// 	]
		// );

		$this->end_controls_section();




		$this->start_controls_section(
			'ma_el_restrict_content',
			[
				'label' => __('Restrict Content', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_restrict_content_source',
			[
				'label'   => __('Select Source', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'         => __('Custom Content', MELA_TD),
					'elementor'      => __('Elementor Template', MELA_TD)
				],
			]
		);


		$this->add_control(
			'ma_el_restrict_content_elementor_source',
			[
				'label'   => __('Select Source', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'section',
				'options' => [
					'section'   => __('Saved Section', MELA_TD),
					'widget'    => __('Saved Widget', MELA_TD),
					'template'  => __('Saved Page Template', MELA_TD),
				],
				'condition'   => ['ma_el_restrict_content_source' => 'elementor'],
			]
		);


		$this->add_control(
			'ma_el_restrict_content_saved_widget',
			[
				'label'                 => __('Choose Widget', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('widget'),
				'default'               => '-1',
				'condition'   => ['ma_el_restrict_content_source' => 'elementor'],
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'ma_el_restrict_content_elementor_source',
							'operator'  => '==',
							'value'     => 'widget',
						],
					],
				],
			]
		);

		$this->add_control(
			'ma_el_restrict_content_saved_section',
			[
				'label'                 => __('Choose Section', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('section'),
				'default'               => '-1',
				'condition'   => ['ma_el_restrict_content_source' => 'elementor'],

				'conditions'        => [
					'terms' => [
						[
							'name'      => 'ma_el_restrict_content_elementor_source',
							'operator'  => '==',
							'value'     => 'section',
						],
					],
				],
			]
		);


		$this->add_control(
			'ma_el_restrict_content_elementor_template',
			[
				'label'                 => __('Choose Template', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('page'),
				'default'               => '-1',
				'condition'   			=> ['ma_el_restrict_content_source' => 'elementor'],
				'conditions'        	=> [
					'terms' => [
						[
							'name'      => 'ma_el_restrict_content_elementor_source',
							'operator'  => '==',
							'value'     => 'template',
						],
					]
				],
			]
		);


		$this->add_control(
			'ma_el_restrict_content_custom',
			[
				'label'       => __('Custom Content', MELA_TD),
				'type'        => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'dynamic'     => ['active' => true],
				'default'     => __('This is your content that you want to be restricted by either user role or password.', MELA_TD),
				'condition'   => [
					'ma_el_restrict_content_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'ma_el_restrict_content_show',
			[
				'label'       => __('Show Forcefully for Edit', MELA_TD),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __('You can show your restricted content in editor for design it.', MELA_TD),
				'condition'   => [
					'ma_el_restrict_content_type'	=> 'password'
				]
			]
		);

		$this->end_controls_section();





		//Checkbox Settings
		// $this->start_controls_section(
		// 	'ma_el_restrict_age_checkbox',
		// 	[
		// 		'label' => __( 'Age Restric Contents', MELA_TD ),
		// 		'condition' => [
		// 			'ma_el_restrict_content_type' 	=> 'age_restrict',
		// 			'ma_el_restrict_age_type'		=> ['age_checkbox','button_confirm','input_age','enter_age']
		// 		]
		// 	]
		// );
		// $this->end_controls_section();




		/*
			* Warning Messages
			*/
		$this->start_controls_section(
			'ma_el_warning_message',
			[
				'label' => __('Warning Message', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_warning_type',
			[
				'label'   => __('Message Type', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'         => __('Custom Message', MELA_TD),
					'elementor'      => __('Elementor Template', MELA_TD),
					'none'           => __('None', MELA_TD),
				],
			]
		);

		$this->add_control(
			'ma_el_warning_message_template',
			[
				'label'                 => __('Choose Template', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('page'),
				'default'               => '-1',
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'ma_el_warning_type',
							'operator'  => '==',
							'value'     => 'elementor',
						],
					],
				],
			]
		);

		$this->add_control(
			'ma_el_warning_message_title',
			[
				'label'     => __('Custom Title', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('Age Verification', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_warning_type' => 'custom',
					'ma_el_restrict_content_type!'	=> 'password',
					'ma_el_restrict_age_type' => ['input_age', 'button_confirm', 'input_age', 'enter_age', 'age_checkbox']
				]
			]
		);

		$this->add_control(
			'ma_el_warning_message_text',
			[
				'label'     => __('Custom Message', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('You don\'t have permission to see this content.', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_warning_type' => 'custom',
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_age_checkbox_label',
			[
				'label'     => __('Label', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Confirm your Age',
				'condition' => [
					'ma_el_restrict_content_type' 	=> 'age_restrict',
					'ma_el_restrict_age_type'		=> ['enter_age']
				]
			]
		);

		$this->add_control(
			'ma_el_warning_checkbox_message',
			[
				'label'     => __('Checkbox Message', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('I confirm that I am 18 years old or over', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_restrict_age_type' => ['age_checkbox', 'button_confirm']
				]
			]
		);

		$this->add_control(
			'ma_el_warning_show',
			[
				'label'   => __('Show Warnings?', MELA_TD),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);


		$this->add_control(
			'ma_el_warning_message_close_button',
			[
				'label'   => __('Close Button', MELA_TD),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->end_controls_section();



		/*
			* Buttons
			*/
		$this->start_controls_section(
			'ma_el_rc_submit',
			[
				'label' => __('Buttons', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_popup_open_content',
			[
				'label'     => __('Popup Button', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Open Content', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_restrict_content_layout_type' => 'popup'
				]
			]
		);

		$this->add_control(
			'ma_el_submit_button',
			[
				'label'     => __('Submit Button', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Submit', MELA_TD),
				'dynamic'   => ['active' => true],
				// 'condition' => [
				// 	'ma_el_restrict_age_type' => ['age_checkbox','button_confirm','input_age','enter_age']
				// ]
			]
		);


		$this->add_control(
			'ma_el_exit_button',
			[
				'label'     => __('Exit Button', MELA_TD),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Exit', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_restrict_age_type' => ['button_confirm']
				]
			]
		);
		$this->end_controls_section();


		/*
			* Error Message
			*/
		$this->start_controls_section(
			'ma_el_error_message_section',
			[
				'label' => __('Error Message', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_error_empty_bday_message',
			[
				'label'     => __('Empty Birthday Message', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('Please enter your birthday', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_restrict_content_type' => 'age_restrict',
					'ma_el_restrict_age_type' => ['input_age']
				]
			]
		);
		$this->add_control(
			'ma_el_error_non_exist_date_message',
			[
				'label'     => __('Non Exist Date', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__('Oops it looks like that date doesn\'t exist.', MELA_TD),
				'dynamic'   => ['active' => true],
				'condition' => [
					'ma_el_restrict_content_type' => 'age_restrict',
					'ma_el_restrict_age_type' => ['input_age']
				]
			]
		);

		$this->add_control(
			'ma_el_error_message',
			[
				'label'     => __('Error Message', MELA_TD),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('Please confirm if you are 18 years old or over.', MELA_TD),
				'dynamic'   => ['active' => true]
			]
		);
		$this->end_controls_section();




		/* Style */

		$this->start_controls_section(
			'ma_el_restrict_content_style',
			[
				'label'     => esc_html__('Restrict Content', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_restrict_content_source' => 'custom'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-wrap .ma-el-restrict-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_restrict_content_background',
			[
				'label'     => esc_html__('Background', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-wrap .ma-el-restrict-content' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-wrap .ma-el-restrict-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_margin',
			[
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-wrap .ma-el-restrict-content' => 'Margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_restrict_content_typography',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-wrap .ma-el-restrict-content',
			]
		);

		$this->end_controls_section();


		/*
			* Popup Style
			*/
		$this->start_controls_section(
			'ma_el_restrict_content_popup_style_section',
			[
				'label' => __('Popup Style', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_restrict_content_layout_type' => 'popup'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ma_el_restrict_content_popup_color',
				'label'     => __('Background', MELA_TD),
				'type'		=> 'background',
				'types' => ['classic', 'gradient'],
				'selector' => '.fancybox-is-open .fancybox-bg, {{WRAPPER}} .fancybox-content',
				'default' => [
					'background' => 'classic',
					'color'      => '#555'
				]
			]
		);

		$this->add_control(
			'ma_el_rc_select_color',
			[
				'label'     => esc_html__('Select Box Background', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ma_el_ra_select, .ma_el_ra_options.ma_el_ra_active' => 'background: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma_el_ra_triangle_up' => 'border-bottom: 10px solid {{VALUE}};'

				],
				'condition' => [
					'ma_el_restrict_content_type'	=> ['math_captcha', 'age_restrict'],
					'ma_el_restrict_age_type'		=> 'input_age'
				]
			]
		);

		$this->add_control(
			'ma_el_rc_select_text_color',
			[
				'label'     => esc_html__('Select Box Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#ma_el_dob *' => 'color: {{VALUE}};'
				],
				'condition' => [
					'ma_el_restrict_content_type'	=> ['math_captcha', 'age_restrict'],
					'ma_el_restrict_age_type'		=> 'input_age'
				]
			]
		);


		$this->end_controls_section();



		/* Warning Style */
		$this->start_controls_section(
			'ma_el_rc_warning_message_style',
			[
				'label'     => esc_html__('Message Contents', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_warning_type' => 'custom'
				]
			]
		);


		// Label
		$this->add_control(
			'ma_el_rc_warning_label_heading',
			[
				'label'     => __('Label Warning', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_warning_type' 			=> 'custom',
					'ma_el_restrict_content_type'	=> ['math_captcha', 'age_restrict'],
					'ma_el_restrict_age_type'		=> 'enter_age'

				]
			]
		);

		$this->add_control(
			'ma_el_rc_warning_label_color',
			[
				'label'     => esc_html__('Label Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-form label.ma_el_rc_answer,
						{{WRAPPER}} .ma-el-restrict-form label.ma_el_ra_input_year' => 'color: {{VALUE}};'
				],
				'condition' => [
					'ma_el_warning_type' 			=> 'custom',
					'ma_el_restrict_content_type'	=> ['math_captcha', 'age_restrict'],
					'ma_el_restrict_age_type'		=> 'enter_age'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_rc_warning_label_typography',
				'selector' => '{{WRAPPER}} .ma-el-restrict-form label.ma_el_rc_answer, {{WRAPPER}} .ma-el-restrict-form label.ma_el_ra_input_year',
				'condition' => [
					'ma_el_warning_type' 			=> 'custom',
					'ma_el_restrict_content_type'	=> ['math_captcha', 'age_restrict'],
					'ma_el_restrict_age_type'		=> 'enter_age'
				]
			]
		);



		// Title
		$this->add_control(
			'ma_el_rc_warning_title_heading',
			[
				'label'     => __('Title Warning', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_warning_type' 			=> 'custom',
					'ma_el_restrict_content_type!'	=> 'password',
					'ma_el_restrict_age_type' 		=> ['input_age', 'button_confirm', 'input_age', 'enter_age', 'age_checkbox']
				]
			]
		);

		$this->add_control(
			'ma_el_rc_warning_title_color',
			[
				'label'     => esc_html__('Title Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-form .card-title,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .card-title,
						{{WRAPPER}} .ma-el-alert .elementor-alert-title' => 'color: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_rc_warning_title_typography',
				'selector' => '{{WRAPPER}} .ma-el-alert .elementor-alert-title,{{WRAPPER}} .ma-el-restrict-form .card-title, {{WRAPPER}} .ma-el-restrict-modal .card-title,{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .card-title'
			]
		);


		// Message
		$this->add_control(
			'ma_el_rc_warning_title_typo_desc_heading',
			[
				'label'     => __('Description', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_rc_warning_message_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-message-text .ma-el-alert,
						{{WRAPPER}} .ma-el-restrict-form .card-text,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .card-text' => 'color: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_rc_warning_message_typography',
				'selector' => '{{WRAPPER}} .ma-el-alert .elementor-alert-description, {{WRAPPER}} .ma-el-restrict-form .card-text,{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .card-text',
				'separator' => 'after',
			]
		);


		// Checkbox
		$this->add_control(
			'ma_el_rc_warning_checkbox_heading',
			[
				'label'     => __('Checkbox Message', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ma_el_restrict_content_type'	=> 'age_restrict',
					'ma_el_restrict_age_type'	=> ['age_checkbox', 'button_confirm']
				]
			]
		);

		$this->add_control(
			'ma_el_rc_warning_checkbox_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-form .card-body,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .card-body' => 'color: {{VALUE}};'
				],
				'condition' => [
					'ma_el_restrict_content_type'	=> 'age_restrict',
					'ma_el_restrict_age_type'	=> ['age_checkbox', 'button_confirm']
				]

			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_rc_warning_checkbox_typography',
				'selector' => '{{WRAPPER}} .ma-el-restrict-form .card-body, {{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .card-body',
				'condition' => [
					'ma_el_restrict_content_type'	=> 'age_restrict',
					'ma_el_restrict_age_type'	=> ['age_checkbox', 'button_confirm']
				]
			]
		);




		$this->add_control(
			'ma_el_rc_warning_message_close_button_color',
			[
				'label'     => esc_html__('Close Button Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert-dismiss, {{WRAPPER}} {{CURRENT_ITEM}} .fancybox-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ma_el_warning_message_close_button' => 'yes',
					'ma_el_restrict_content_type!'	=> ['password', 'user'],
				]
			]
		);

		$this->add_control(
			'ma_el_rc_warning_message_background',
			[
				'label'     => esc_html__('Warning Message BG', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-message-text .ma-el-alert,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-message-text .ma-el-alert' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_rc_warning_message_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-message-text .ma-el-alert,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-message-text .ma-el-alert' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_rc_warning_message_margin',
			[
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-message-text .ma-el-alert,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-message-text .ma-el-alert' => 'Margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);





		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_restrict_content_password_input',
			[
				'label'     => esc_html__('Password Input', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_restrict_content_type'	=> 'password'
				]
			]
		);

		$this->start_controls_tabs('ma_el_restrict_content_password_input_control_tabs');

		$this->start_controls_tab('ma_el_restrict_content_password_input_normal', [
			'label' => esc_html__('Normal', MELA_TD)
		]);

		$this->add_control(
			'ma_el_restrict_content_password_input_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_password_input_background',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_password_input_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_password_input_margin',
			[
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ma_el_restrict_content_password_input_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass',
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_password_input_radius',
			[
				'label'      => esc_html__('Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_restrict_content_password_input_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_restrict_content_password_input_typography',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_restrict_content_password_input_hover', [
			'label' => esc_html__('Hover', MELA_TD)
		]);

		$this->add_control(
			'ma_el_restrict_content_password_input_hover_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_password_input_hover_background',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass:hover' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_password_input_hover_border_color',
			[
				'label'     => esc_html__('Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'ma_el_restrict_content_password_input_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_restrict_content_password_input_hover_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields input.ma-el-input-pass:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		/*
			* Submit Button
			*/
		$this->start_controls_section(
			'ma_el_restrict_content_submit_button',
			[
				'label'     => esc_html__('Submit Button', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				// 'condition' => [
				// 	'ma_el_restrict_content_type'	=> 'password'
				// ]
			]
		);

		$this->start_controls_tabs('ma_el_restrict_content_submit_button_control_tabs');

		$this->start_controls_tab('ma_el_restrict_content_submit_button_normal', [
			'label' => esc_html__('Normal', MELA_TD)
		]);

		$this->add_control(
			'ma_el_restrict_content_submit_button_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_submit_button_background',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_submit_button_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_submit_button_margin',
			[
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ma_el_restrict_content_submit_button_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
									{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn',
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_submit_button_border_radius',
			[
				'label'      => esc_html__('Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_restrict_content_submit_button_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
									{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_restrict_content_submit_button_typography',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn,
									{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_restrict_content_submit_button_hover', [
			'label' => esc_html__('Hover', MELA_TD)
		]);

		$this->add_control(
			'ma_el_restrict_content_submit_button_hover_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn:hover,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_submit_button_hover_background',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn:hover,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn:hover' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_submit_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn:hover,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'ma_el_restrict_content_submit_button_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_restrict_content_submit_button_hover_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields button.ma-el-btn:hover,
									{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields button.ma-el-btn:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();





		/*
			* Exit Button
			*/
		$this->start_controls_section(
			'ma_el_restrict_content_exit_button',
			[
				'label'     => esc_html__('Exit Button', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_restrict_content_type'	=> 'age_restrict',
					'ma_el_restrict_age_type'	=> 'button_confirm'
				]
			]
		);

		$this->start_controls_tabs('ma_el_restrict_content_exit_button_control_tabs');

		$this->start_controls_tab('ma_el_restrict_content_exit_button_normal', [
			'label' => esc_html__('Normal', MELA_TD)
		]);

		$this->add_control(
			'ma_el_restrict_content_exit_button_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_exit_button_background',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_exit_button_padding',
			[
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_exit_button_margin',
			[
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ma_el_restrict_content_exit_button_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
									{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn',
			]
		);

		$this->add_responsive_control(
			'ma_el_restrict_content_exit_button_border_radius',
			[
				'label'      => esc_html__('Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_restrict_content_exit_button_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn, {{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_restrict_content_exit_button_typography',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn,
									{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_restrict_content_exit_button_hover', [
			'label' => esc_html__('Hover', MELA_TD)
		]);

		$this->add_control(
			'ma_el_restrict_content_exit_button_hover_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_exit_button_hover_background',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_restrict_content_exit_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'ma_el_restrict_content_exit_button_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_restrict_content_exit_button_hover_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover,
									{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-content-fields .ma-el-exit.ma-el-btn:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		/*
			* Error Message
			*/
		$this->start_controls_section(
			'ma_el_error_message_style_section',
			[
				'label' => __('Error Message', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_error_message_style_color',
			[
				'label'     => esc_html__('Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-restrict-form .ma_el_rc_result,
						{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .ma_el_rc_result' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_error_message_style_shadow',
				'selector' => '{{WRAPPER}} .ma-el-restrict-form .ma_el_rc_result,
					{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-restrict-form .ma_el_rc_result',
			]
		);
		$this->end_controls_section();




		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/restrict-content-for-elementor/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/restrict-content-for-elementor/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=Alc1R_W5_Z8" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		//Upgrade to Pro
		
	}



	protected function jltma_current_user_role()
	{
		if (!is_user_logged_in()) {
			return;
		}
		$user_type    = $this->get_settings('ma_el_restrict_content_user_role');
		$user_role    = reset(wp_get_current_user()->roles);
		$content_role = ($user_type) ? $user_type : [];
		$output       = in_array($user_role, $content_role);
		return $output;
	}

	protected function jltma_restrict_content_msg()
	{
		$settings = $this->get_settings_for_display();
		$close_button = ($settings['ma_el_warning_message_close_button'] == 'yes') ? true : false;
?>
		<div class="ma-el-restrict-content-message">
			<?php if ($settings['ma_el_warning_type'] == 'custom') { ?>

				<?php if (!isset($_POST['ma_el_restrict_content_pass'])) : ?>

					<?php if (!empty($settings['ma_el_warning_message_text'])) : ?>
						<?php if ($settings['ma_el_warning_show'] == 'yes') { ?>
							<div class="ma-el-restrict-content-message-text">
								<?php Master_Addons_Helper::jltma_warning_messaage(
									$settings['ma_el_warning_message_text'],
									'warning',
									$close_button
								); ?>
							</div>
						<?php } ?>
					<?php endif; ?>

				<?php elseif (isset($_POST['ma_el_restrict_content_pass']) && ($settings['ma_el_restrict_content_pass'] !== $_POST['ma_el_restrict_content_pass'])) : ?>
					<?php if ($settings['ma_el_warning_show'] == 'yes') { ?>
						<?php Master_Addons_Helper::jltma_warning_messaage(esc_html__('Ops, You entered wrong password!', MELA_TD), 'warning', $close_button); ?>
					<?php } ?>
				<?php endif; ?>

			<?php } elseif ('elementor' == $settings['ma_el_warning_type'] and !empty($settings['ma_el_warning_message_template'])) {
				echo Master_Addons_Helper::jltma_elementor()->frontend->get_builder_content_for_display(
					$settings['ma_el_warning_message_template']
				);
			}
			?>
		</div>
	<?php
	}



	// Restrict Content
	public function jltma_restrict_content()
	{
		$settings = $this->get_settings_for_display();
	?>
		<div class="ma-el-restrict-content">
			<?php
			if ($settings['ma_el_restrict_content_source'] == 'custom' and !empty($settings['ma_el_restrict_content_custom'])) { ?>
				<div class="ma-el-restrict-content-message">
					<?php echo $this->parse_text_editor($settings['ma_el_restrict_content_custom']); ?>
				</div>
			<?php
			} elseif ($settings['ma_el_restrict_content_source'] == 'elementor' and !empty($settings['ma_el_restrict_content_elementor_template'])) {
				echo Master_Addons_Helper::jltma_elementor()->frontend->get_builder_content_for_display(
					$settings['ma_el_restrict_content_elementor_template']
				);
			} elseif ($settings['ma_el_restrict_content_elementor_source'] == 'section' and !empty($settings['ma_el_restrict_content_saved_section'])) {
				echo Master_Addons_Helper::jltma_elementor()->frontend->get_builder_content_for_display(
					$settings['ma_el_restrict_content_saved_section']
				);
			} elseif ($settings['ma_el_restrict_content_elementor_source'] == 'widget' and !empty($settings['ma_el_restrict_content_saved_widget'])) {
				echo Master_Addons_Helper::jltma_elementor()->frontend->get_builder_content_for_display(
					$settings['ma_el_restrict_content_saved_widget']
				);
			}
			?>
		</div>
	<?php
	}

	public function jltma_rc_password()
	{ ?>

		<div class="form-group mb-2">
			<input type="password" name="ma_el_restrict_content_pass" class="form-control ma-el-input-pass mr-3 mt-2" placeholder="<?php esc_html_e('Enter Password', MELA_TD); ?>" size="20" />

			<?php $this->jltma_render_submit_form(); ?>

		</div>

	<?php }


	public function jltma_rc_checkbox()
	{
		$settings = $this->get_settings_for_display();
	?>
		<h5 class="card-title">
			<?php echo $settings['ma_el_warning_message_title']; ?>
		</h5>
		<p class="card-text">
			<?php echo $settings['ma_el_warning_message_text']; ?>
		</p>

		<div id="ma_el_rc_checkbox" class="form-group form-check">
			<input type='checkbox' id='ma_el_rc_check' name='ma_el_rc_check' />
			<label for='ma_el_rc_check' class="ma_el_rc_check pl-2">
				<?php echo $settings['ma_el_warning_checkbox_message']; ?>
			</label>
		</div>

	<?php }


	public function jltma_rc_age_button_submit()
	{
		$settings = $this->get_settings_for_display();
	?>
		<h5 class="card-title">
			<?php echo $settings['ma_el_warning_message_title']; ?>
		</h5>
		<p class="card-text">
			<?php echo $settings['ma_el_warning_message_text']; ?>
		</p>
		<div class="card-body">
			<?php echo $settings['ma_el_warning_checkbox_message']; ?>
		</div>

	<?php
	}


	public function jltma_rc_enter_age()
	{
		$settings = $this->get_settings_for_display();
	?>
		<div class="form-group my-1 mr-2">
			<label class="mr-sm-2 ma_el_ra_input_year" for="ma_el_ra_year">
				<?php echo $settings['ma_el_restrict_age_checkbox_label']; ?>
			</label>

			<input name="ma_el_ra_year" class="ma_el_ra_year" type="text" placeholder="<?php //echo $settings['ma_el_restrict_age_checkbox_label'];
																						?>" /><br />
		</div>
	<?php }

	public function jltma_rc_input_age()
	{
		$settings = $this->get_settings_for_display();
		$jltma_month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	?>
		<div id="ma_el_dob">

			<div class="ma_el_ra_select_wrap">
				<div class='ma_el_ra_select'>
					<span class="ma_el_ra_select_val"><?php echo apply_filters('ma_el_ra_select_day', 'DD'); ?></span>
					<select name="ma_el_ra_day">
						<option value=''><?php echo apply_filters('ma_el_ra_select_day', 'DD'); ?></option>
						<?php for ($x = 1; $x <= 31; $x++) {
							echo "<option value='$x'>" . sprintf("%02d", $x) . "</option>";
						} ?>
					</select>
				</div>
				<div class='ma_el_ra_options'>
					<div class='ma_el_ra_triangle_up'></div>
					<ul>
						<?php for ($x = 1; $x <= 31; $x++) {
							echo "<li data-val='$x'>" . sprintf("%02d", $x) . "</li>";
						} ?>
					</ul>
				</div>
			</div>


			<div class="ma_el_ra_select_wrap">
				<div class='ma_el_ra_select'>
					<span class="ma_el_ra_select_val"><?php echo apply_filters('ma_el_ra_select_month', 'MM'); ?></span>
					<select name="ma_el_ra_month">
						<option value=''><?php echo apply_filters('ma_el_ra_select_month', 'MM'); ?></option>
						<?php
						for ($x = 1; $x <= 12; $x++) {
							echo "<option value='$x'>" . sprintf("%02d", $x) . "</option>";
						} ?>
					</select>
				</div>
				<div class='ma_el_ra_options'>
					<div class='ma_el_ra_triangle_up'></div>
					<ul>
						<?php for ($x = 1; $x <= 12; $x++) {
							echo "<li data-val='$x'>" . sprintf("%02d", $x) . "</li>";
						} ?>
					</ul>
				</div>
			</div>


			<div class="ma_el_ra_select_wrap">
				<div class='ma_el_ra_select'>
					<span class="ma_el_ra_select_val"><?php echo apply_filters('ma_el_ra_select_year', 'YYYY'); ?></span>
					<select name="ma_el_ra_year">
						<option value=''><?php echo apply_filters('ma_el_ra_select_year', 'YYYY'); ?></option>
						<?php for ($x = date("Y"); $x >= (date("Y") - 100); $x--) {
							echo "<option value='$x'>" . sprintf("%02d", $x) . "</option>";
						} ?>
					</select>
				</div>
				<div class='ma_el_ra_options'>
					<div class='ma_el_ra_triangle_up'></div>
					<ul>
						<?php for ($x = date("Y"); $x >= (date("Y") - 100); $x--) {
							echo "<li data-val='$x'>" . sprintf("%02d", $x) . "</li>";
						} ?>
					</ul>
				</div>
			</div>

		</div>

	<?php }


	public function jltma_render_submit_form()
	{
		global $wp;
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute([
			'button_wrapper' => [
				'class'	=> [
					'ma-el-btn',
					'jltma-btn',
					'jltma-btn-primary',
					'mb-2',
					'mt-3'
				],
				'id' => 'ma-el-btn',
			],
		]);

		$this->add_render_attribute([
			'exit_button_wrapper' => [
				'class'	=> [
					'ma-el-exit',
					'ma-el-btn',
					'jltma-btn',
					'jltma-btn-danger',
					'mb-2',
					'mt-3'
				],
				'id' 			=> 'ma-el-btn',
				'onclick'		=> "javascript:parent.jQuery.fancybox.close();"
			],
		]);
	?>
		<div class="ma_el_rc_submit">
			<button type="submit" name="submit" value="Submit" <?php echo $this->get_render_attribute_string('button_wrapper'); ?>>
				<?php echo $settings['ma_el_submit_button']; ?>
			</button>

			<input type="hidden" name="action" value="ma_el_restrict_content" />

			<?php if ($settings['ma_el_restrict_age_type'] == 'button_confirm') { ?>
				<a href="<?php echo home_url($wp->request); ?>" <?php echo $this->get_render_attribute_string('exit_button_wrapper'); ?>>
					<?php echo $settings['ma_el_exit_button']; ?>
				</a>
			<?php } ?>
		</div>

	<?php }

	public function jltma_rc_math_captcha()
	{
		$settings = $this->get_settings_for_display();
		$math_type = $settings['ma_el_restrict_content_math_type'];


		$digit1 = mt_rand(1, 20);
		$digit2 = mt_rand(1, 20);

		// $math_type = mt_rand(1,3);

		if ($math_type == "add") {
			$math = "$digit1 + $digit2";
			$math_hd = $digit1 + $digit2;
		} else if ($math_type == 'subtract') {
			$math = "$digit1 - $digit2";
			$math_hd = $digit1 - $digit2;
		} else if ($math_type == 'multiply') {
			$math = "$digit1 x $digit2";
			$math_hd = $digit1 * $digit2;
		}
	?>

		<div class="form-group text-center ma_el_rc_answer">
			<label for="ma_el_rc_answer" class="ma_el_rc_answer">
				<?php echo $settings['ma_el_warning_message_title']; ?> <?php echo $math . ' =   '; ?>
			</label>
			<input class="mt-1 mr-3 ml-3" name="ma_el_rc_answer" type="text" size="8" /><br />
			<input name="ma_el_rc_answer_hd" type="hidden" value="<?php echo $math_hd; ?>" />

			<?php $this->jltma_render_submit_form(); ?>

		</div>

	<?php
	}


	public function jltma_restrict_content_form()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute([
			'form_wrapper' => [
				'class'	=> [
					'ma-el-restrict-form',
					'w-100',
					'form-inline'
				],
				'id' 			=> 'ma-el-restrict-form-' . $this->get_id(),
				'data-form-id' 	=> 'ma-el-restrict-form-' . $this->get_id(),
				'method'		=> "post"
			],
		]);

		$this->add_render_attribute([
			'button_wrapper' => [
				'class'	=> [
					'ma-el-btn',
					'jltma-btn',
					'jltma-btn-primary',
					'mb-2',
					'mt-3'
				],
				'id' => 'ma-el-btn'
			],
		]);



		if ($settings['ma_el_restrict_content_type'] == 'age_checkbox') {
			$this->add_render_attribute([
				'button_wrapper', 'class', 'rounded-pill'
			]);
		}

	?>
		<div class="ma-el-restrict-content-fields">
			<form <?php echo $this->get_render_attribute_string('form_wrapper'); ?>>
				<div class="card-body">

					<?php if ($settings['ma_el_restrict_age_type'] == 'input_age' || $settings['ma_el_restrict_age_type'] == 'enter_age') { ?>
						<h5 class="card-title">
							<?php echo $settings['ma_el_warning_message_title']; ?>
						</h5>
						<p class="card-text">
							<?php echo $settings['ma_el_warning_message_text']; ?>
						</p>

						<div class="card-text form-row justify-content-center">

						<?php } ?>

						<?php if ($settings['ma_el_restrict_content_type'] == 'password') {
							$this->jltma_rc_password();
						} ?>

						<?php if ($settings['ma_el_restrict_content_type'] == 'math_captcha') {
							$this->jltma_rc_math_captcha();
						} ?>

						<?php if ($settings['ma_el_restrict_age_type'] == 'age_checkbox') {
							$this->jltma_rc_checkbox();
						} ?>

						<?php if ($settings['ma_el_restrict_age_type'] == 'button_confirm') {
							$this->jltma_rc_age_button_submit();
						} ?>

						<?php if ($settings['ma_el_restrict_age_type'] == 'input_age') {
							$this->jltma_rc_input_age();
						} ?>

						<?php if ($settings['ma_el_restrict_age_type'] == 'enter_age') {
							$this->jltma_rc_enter_age();
						} ?>


						<?php if ($settings['ma_el_restrict_age_type'] == 'input_age' || $settings['ma_el_restrict_age_type'] == 'enter_age') { ?>
						</div>
					<?php }

						$ma_el_restrict_content_array = array("password", "math_captcha");
						$ma_el_restrict_content_type_data = $settings['ma_el_restrict_content_type'];

						if (!in_array($ma_el_restrict_content_type_data, $ma_el_restrict_content_array)) {
							$this->jltma_render_submit_form();
						} ?>


				</div>



			</form>
		</div>

	<?php
	}


	protected function jltma_rc_render_user()
	{
		// User Based Restrict Content
		if (true === $this->jltma_current_user_role()) {
			$this->jltma_restrict_content();
		} else {
			$this->jltma_restrict_content_msg();
		}
	}



	protected function render()
	{

		$settings = $this->get_settings_for_display();

		$popup_content_link = '#ma-el-restrict-content-popup-trigger-' . $this->get_id();

		$this->add_render_attribute([
			'wrapper' => [
				'class'						=> ['ma-el-restrict-content-wrap'],
				'id' 						=> 'ma-el-restrict-content-' . $this->get_id(),
				'data-restrict-layout-type' => $settings['ma_el_restrict_content_layout_type'],
				'data-restrict-type' 		=> $settings['ma_el_restrict_content_type'],
				'data-error-message'		=> $settings['ma_el_error_message'],
				'data-content-pass' 		=> ($settings['ma_el_restrict_content_pass']) ? $settings['ma_el_restrict_content_pass'] : "",
			],

			'restrict_age' 	=> [
				'class'						=> ['ma-el-restrict-age-wrapper', 'card', 'text-center'],
				'id' 						=> 'ma-el-restrict-age-' . $this->get_id(),
				'data-min-age' 				=> $settings['ma_el_restrict_content_age'],
				'data-age-type' 			=> $settings['ma_el_restrict_age_type'],
				'data-age-title' 			=> $settings['ma_el_warning_message_title'],
				'data-age-content' 			=> $settings['ma_el_warning_message_text'],
				'data-empty-bday' 			=> $settings['ma_el_error_empty_bday_message'],
				'data-non-exist-bday' 		=> $settings['ma_el_error_non_exist_date_message']
			],

			'popup_trigger' => [
				'class'	=> [
					'ma-el-restrict-content-popup-trigger',
					'ma-el-popup-trigger',
					$settings['ma_el_restrict_content_popup_type'] . '-popup'
				],
				'href' => $popup_content_link
			],

			'popup_content' => [
				'class'	=> [
					'ma-el-popup-content',
					'ma-el-restrict-content-popup-content'
				],
				'id' => '#ma-el-restrict-content-popup-trigger-' . $this->get_id(),
				'data-content-pass' => $settings['ma_el_restrict_content_pass'],
				'data-popup-type' => $settings['ma_el_restrict_content_popup_type']
			]
		]);

		if ($settings['ma_el_restrict_age_type'] == "age_checkbox") {
			$this->add_render_attribute([
				'restrict_age' 	=> [
					//Checkbox
					'data-checkbox-msg' 	=> ($settings['ma_el_warning_checkbox_message']) ? $settings['ma_el_warning_checkbox_message'] : "",
					'data-checkbox-label' 	=> ($settings['ma_el_restrict_age_checkbox_label']) ? $settings['ma_el_restrict_age_checkbox_label'] : "",
					'data-checkbox-error' 	=> ($settings['ma_el_error_message']) ? $settings['ma_el_error_message'] : "",
				]
			]);
		}
	?>

		<section <?php echo $this->get_render_attribute_string('wrapper'); ?>>

			<?php
			// On Page Restrict Contents
			if ($settings['ma_el_restrict_content_layout_type'] == 'onpage') {

				if ($settings['ma_el_restrict_content_type'] == 'user') {

					$this->jltma_rc_render_user();
				} elseif ($settings['ma_el_restrict_content_type'] == 'password') {

					// Password Based Restrict Content
					if (Master_Addons_Helper::jltma_elementor()->editor->is_edit_mode()) {

						if ($settings['ma_el_restrict_content_show'] !== 'yes') {
							// $this->jltma_restrict_content_msg();
							$this->jltma_restrict_content_form();
						} else {
							$this->jltma_restrict_content();
						}
					} else {

						$this->jltma_restrict_content_form();  ?>

						<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
							<?php $this->jltma_restrict_content(); ?>
						</div>
					<?php
					}
				} elseif ($settings['ma_el_restrict_content_type'] == 'math_captcha') {

					// Math Captcha Restrict Content

					if (Master_Addons_Helper::jltma_elementor()->editor->is_edit_mode()) {
						$this->jltma_restrict_content_msg();
						$this->jltma_restrict_content_form();
					} else {


						$this->jltma_restrict_content_form(); ?>

						<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
							<?php $this->jltma_restrict_content(); ?>
						</div>

					<?php


					}
				} elseif ($settings['ma_el_restrict_content_type'] == 'age_restrict') { ?>

					<div <?php echo $this->get_render_attribute_string('restrict_age'); ?>>

						<?php if ($settings['ma_el_restrict_age_type'] == "age_checkbox") {

							//Check Box Age Restrict
							if (Master_Addons_Helper::jltma_elementor()->editor->is_edit_mode()) {
								$this->jltma_restrict_content_form();
								// $this->jltma_restrict_content();
							} else {

								$this->jltma_restrict_content_form(); ?>

								<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
									<?php $this->jltma_restrict_content(); ?>
								</div>

							<?php
							}
						} else if ($settings['ma_el_restrict_age_type'] == "button_confirm") {


							//Check Submit Confirm Button
							if (Master_Addons_Helper::jltma_elementor()->editor->is_edit_mode()) {

								$this->jltma_restrict_content_form();
							} else {

								$this->jltma_restrict_content_form(); ?>

								<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
									<?php $this->jltma_restrict_content(); ?>
								</div>

							<?php
							}
						} elseif ($settings['ma_el_restrict_age_type'] == 'input_age') {

							// Input Birthday Enter
							if (Master_Addons_Helper::jltma_elementor()->editor->is_edit_mode()) {

								$this->jltma_restrict_content_form();
							} else {

								$this->jltma_restrict_content_form(); ?>

								<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
									<?php $this->jltma_restrict_content(); ?>
								</div>

							<?php
							}
						} elseif ($settings['ma_el_restrict_age_type'] == 'enter_age') {

							// Enter Age
							if (Master_Addons_Helper::jltma_elementor()->editor->is_edit_mode()) {

								$this->jltma_restrict_content_form();
							} else {

								$this->jltma_restrict_content_form(); ?>

								<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
									<?php $this->jltma_restrict_content(); ?>
								</div>

						<?php

							}
						} ?>


					</div>

				<?php } //End of Age Restrict

			}



			// If Popup Layout
			if ($settings['ma_el_restrict_content_layout_type'] == 'popup') { ?>

				<a <?php echo $this->get_render_attribute_string('popup_trigger'); ?>></a>

				<div <?php echo $this->get_render_attribute_string('popup_content'); ?>>

					<p class="mb-0 ma-el-rc-button d-block">
						<a data-fancybox data-width="800" data-height="450" data-animation-duration="700" data-animation-effect="material" data-modal="<?php if ($settings['ma_el_restrict_content_popup_type'] == "buttonfullscreen" || $settings['ma_el_restrict_content_popup_type'] == "windowloadfullscreen") {
																																							echo "true";
																																						} else {
																																							echo 'false';
																																						} ?>" data-src="#ma-el-rc-modal-<?php echo $this->get_id(); ?>" data-options='{
												"animationDuration" : 750,
												"animationEffect"   : "material",
												"closeBtn"    : false,
												"smallBtn" : false,
												"closeClickOutside" : false,
												"dblclickOutside": false
											}' data-helpers='{
												"overlay": {
													"closeClick": false
												}
											}' href="javascript:;" class="jltma-btn ma-el-rc-button" id="<?php if ($settings['ma_el_restrict_content_popup_type'] == "windowload" || $settings['ma_el_restrict_content_popup_type'] == "windowloadfullscreen") {
																												echo "ma-el-rc-modal-hidden";
																											} else {
																												echo "ma-el-rc-button";
																											} ?>">
							<?php if ($settings['ma_el_restrict_content_popup_type'] == "default" || $settings['ma_el_restrict_content_popup_type'] == "buttonfullscreen") {
								echo $settings['ma_el_popup_open_content'];
							} ?>
						</a>
					</p>

					<div class="restrict-content d-none" id="restrict-content-<?php echo $this->get_id(); ?>">
						<?php $this->jltma_restrict_content(); ?>
					</div>

					<div style="display: none;" id="ma-el-rc-modal-<?php echo $this->get_id(); ?>" class="ma-el-restrict-modal <?php echo $settings['ma_el_restrict_content_popup_type']; ?>">
						<div class="ma-el-status"></div>
						<!-- <button	data-fancybox-close>Close</button> -->

						<?php if ($settings['ma_el_restrict_content_type'] == 'password') {

							$this->jltma_restrict_content_form();
						} elseif ($settings['ma_el_restrict_content_type'] == 'math_captcha') {

							$this->jltma_restrict_content_form();
						} else if ($settings['ma_el_restrict_content_type'] == 'age_restrict') { ?>

							<div <?php echo $this->get_render_attribute_string('restrict_age'); ?>>

								<?php if ($settings['ma_el_restrict_age_type'] == "button_confirm") {

									$this->jltma_restrict_content_form();
								} elseif ($settings['ma_el_restrict_age_type'] == 'enter_age') {

									$this->jltma_restrict_content_form();
								} elseif ($settings['ma_el_restrict_age_type'] == 'input_age') {

									$this->jltma_restrict_content_form();
								} elseif ($settings['ma_el_restrict_age_type'] == "age_checkbox") {
									$this->jltma_restrict_content_form();
								} ?>

							</div>
						<?php } ?>



					</div>
				</div>


			<?php } ?>




		</section>

<?php
	}
}
