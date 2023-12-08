<?php 
/*
Widget Name: Protected Content
Description: Protected Content
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Frontend;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Protected_Content extends Widget_Base {
		
	public function get_name() {
		return 'tp-protected-content';
	}

    public function get_title() {
        return esc_html__('Protected Content', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-lock theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		/*start Protected Content*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Protected Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'content_type',
            [
                'label' => esc_html__( 'Content Source', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content'  => esc_html__( 'Content', 'theplus' ),
					'page_template' => esc_html__( 'Page Template', 'theplus' ),
				],
                'default'               => 'content',
            ]
        );
		$this->add_control(
			'protected_content_field',
			[
				'label' => esc_html__( 'Protected Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'dynamic' => [
					'active' => true
				],
				'default' => esc_html__( 'This is the content that you want to be protected.', 'theplus' ),
				'condition'             => [
					'content_type'      => 'content',
				],
			]
		);
		$this->add_control(
            'protected_content_template',
            [
                'label'                 => esc_html__( 'Elementor Templates', 'theplus' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => theplus_get_templates(),
				'condition'             => [
					'content_type'      => 'page_template',
				],
            ]
        );
		$this->end_controls_section();
		
		/*protected content protection start*/
		$this->start_controls_section(
			'pc_protection',
			[
				'label' => esc_html__( 'Protection Type', 'theplus' )
			]
		);
		$this->add_control(
			'pc_protection_type',
			[
				'label'			=> esc_html__('Protection Type', 'theplus'),
				'label_block'	=> false,
				'type'			=> Controls_Manager::SELECT,
				'options'		=> [
					'role'			=> esc_html__('User role', 'theplus'),
					'password'		=> esc_html__('Single Password', 'theplus'),
					'multiple_password'		=> esc_html__('Multiple Password', 'theplus')					
				],
				'default'		=> 'password'
			]
		);
		$this->add_control(
            'pc_role',
            [
                'label'                 => esc_html__( 'Select Roles', 'theplus' ),
				'type'                  => Controls_Manager::SELECT2,
				'label_block'			=> true,
				'multiple' 				=> true,
				'options'				=> theplus_user_roles(),
				'dynamic' => [
					'active' => true
				],
				'condition'	=> [
					'pc_protection_type'	=> 'role'
				]				
            ]
		);
		$this->add_control(
			'pc_error_message', 
			[
				'label' => esc_html__( 'Preview of Error Message', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'description' => 'Show error message',
				'dynamic' => [
					'active' => true
				],
				'condition'	=> [
					'pc_protection_type'	=> 'role'
				]
			]
		);
		$this->add_control(
			'protection_password',
			[
				'label' => esc_html__( 'Set Password', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true
				],
				'condition'	=> [
					'pc_protection_type'	=> 'password'			
				]
			]
		);	
		/*multiple password field start*/
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'protection_password_multi',
			[
				'label' => esc_html__( 'Set Password', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'password',
				'dynamic' => [
					'active' => true
				],
			]
		);
		$this->add_control(
			'protection_password_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'protection_password_multi' => esc_html__( '1234', 'theplus' ),
					],
					
				],
				'title_field' => '{{{ protection_password_multi }}}',
				'condition'	=> [
					'pc_protection_type'	=> 'multiple_password'			
				]
			]
		);
		/*multiple password field end*/
			
		$this->add_control(
			'show_content',
			[
				'label' => esc_html__( 'Show Content', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'condition'	=> [
					'pc_protection_type'	=> ['password','multiple_password']
				]
			]
		);
		
		$this->add_control(
			'show_cookie',
			[
				'label' => esc_html__( 'Cookie', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'condition'	=> [
					'pc_protection_type'	=> ['password','multiple_password']
				]
			]
		);
		$this->add_control(
			'days',
			[
				'label' => esc_html__( 'Days', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'dynamic' => [
					'active' => true,
				],
				'min' => 1,
				'max' => 365,
				'default' => 1,
				'condition' => [
					'show_cookie' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*protected content protection end*/
		
		/*protected content message start*/
		$this->start_controls_section(
			'pc_message',
			[
				'label' => esc_html__( 'Message' , 'theplus' ),
			]
		);
		$this->add_control(
			'pc_message_source',
			[
				'label'			=> esc_html__('Message Source', 'theplus'),
				'label_block'	=> false,
				'type'			=> Controls_Manager::SELECT,
                'description'   => esc_html__('Set a message or a elementor template when the content is protected.', 'theplus'),
				'options'		=> [
					'none'			=> esc_html__('None', 'theplus'),
					'text'			=> esc_html__('Message', 'theplus'),
					'page_template'		=> esc_html__('Elementor Templates', 'theplus')
				],
				'default'		=> 'text'
			]
		);
		$this->add_control(
			'pc_message_text',
			[
				'label'			=> esc_html__('Text', 'theplus'),
				'type'			=> Controls_Manager::WYSIWYG,
				'default'		=> esc_html__('You do not have permission to see this content.','theplus'),
				'dynamic' => [
					'active' => true
				],
				'condition'		=> [
					'pc_message_source' => 'text'
				]
			]
		);
		$this->add_control(
            'pc_message_template',
            [
                'label'                 => esc_html__( 'Choose Elementor Template', 'theplus' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => theplus_get_templates(),
				'condition'             => [
					'pc_message_source'      => 'page_template',
				],
            ]
        );
		$this->end_controls_section();
		/*form input start*/
		$this->start_controls_section(
			'pc_form_input_section',
			[
				'label' => esc_html__( 'Form Text' , 'theplus' ),				
			]
		);
		$this->add_control(
			'form_input_text',
			[
				'label' => esc_html__( 'Input text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter Password', 'theplus' ),
				'placeholder' => esc_html__( 'Your Text Here', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'form_button_text',
			[
				'label' => esc_html__( 'Button text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,	
				'default' => esc_html__( 'Submit', 'theplus' ),
				'placeholder' => esc_html__( 'Submit', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->end_controls_section();
		/*form input end*/
		/*error message start*/		
		$this->start_controls_section(
			'pc_error_message_section',
			[
				'label' => esc_html__( 'Error Message' , 'theplus' ),				
			]
		);
		$this->add_control(
			'error_message_text',
			[
				'label' => esc_html__( 'Error Message', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Wrong password, please try again.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your Error Message here', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->end_controls_section();
		/*error message end*/
		/*protected content message end*/
		/*text color style start*/
		$this->start_controls_section(
			'section_message',
			[
				'label' => esc_html__( 'Message', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_responsive_control(
			'message_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-pc-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'message_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-pc-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'selector' => '{{WRAPPER}} .theplus-pc-message .theplus-pc-message-text',
			]
		);
		$this->start_controls_tabs( 'tabs_textarea_field_style' );
				$this->start_controls_tab(
					'message_normal',
					[
						'label' => esc_html__( 'Normal', 'theplus' ),
					]
				);
				$this->add_control(
					'message_color',
					[
						'label'     => esc_html__( 'Color', 'theplus' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .theplus-pc-message .theplus-pc-message-text' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'message_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .theplus-pc-message',
					]
				);
				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'message_border',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .theplus-pc-message',
					]
				);
				$this->add_responsive_control(
					'message_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-pc-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'message_box_shadow',
						'selector' => '{{WRAPPER}} .theplus-pc-message',
					]
				);
				$this->end_controls_tab();
				
				$this->start_controls_tab(
					'message_hover',
					[
						'label' => esc_html__( 'Hover', 'theplus' ),
					]
				);
				$this->add_control(
					'message_color_hover',
					[
						'label'     => esc_html__( 'Color', 'theplus' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .theplus-pc-message .theplus-pc-message-text:hover' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'message_bg_hover',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .theplus-pc-message:hover',
					]
				);
				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'message_border_hover',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .theplus-pc-message:hover',
					]
				);
				$this->add_responsive_control(
					'message_border_radius_hover',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-pc-message:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'message_box_shadow_hover',
						'selector' => '{{WRAPPER}} .theplus-pc-message:hover',
					]
				);
				$this->end_controls_tab();
				
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*message style end*/
		
		/*form input start*/
		$this->start_controls_section(
			'form_input',
			[
				'label' => esc_html__( 'Form Input' , 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pc_protection_type' => ['password','multiple_password'],
				],
				]
		);		
		$this->add_responsive_control(
			'form_input_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'form_input_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px', '%'  ],
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
					'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'tab_form_input_placeholder',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-password-pc-fields input::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_input_typography',
				'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password',
			]
		);		
		$this->start_controls_tabs( 'tabs_form_input' );
			$this->start_controls_tab(
				'tab_form_input',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);
			$this->add_control(
				'form_input_color',
				[
					'label'     => esc_html__( 'Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'form_input_bg',
				[
					'label'     => esc_html__( 'Background', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'form_input_border',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password',
					]
				);
				$this->add_responsive_control(
					'form_input_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'form_input_box_shadow',
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password',
					]
				);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_form_input_focus',
				[
					'label' => esc_html__( 'Focus', 'theplus' ),
				]
			);
			$this->add_control(
				'form_input_color_focus',
				[
					'label'     => esc_html__( 'Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password:focus' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'form_input_bg_focus',
				[
					'label'     => esc_html__( 'Background', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password:focus' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'form_input_border_focus',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password:focus',
					]
				);
				$this->add_responsive_control(
					'form_input_border_radius_focus',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'form_input_box_shadow_focus',
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-password:focus',
					]
				);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*form input end*/
		/*form button start*/
		$this->start_controls_section(
			'form_submit',
			[
				'label' => esc_html__( 'Submit Button' , 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pc_protection_type' => ['password','multiple_password'],
				],
			]
		);	
		$this->add_responsive_control(
			'form_submit_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .plus_pc_wrapper .theplus-password-pc-fields input + input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_button_typography',
				'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit',
			]
		);
		$this->start_controls_tabs( 'tabs_form_button' );
			$this->start_controls_tab(
				'tab_form_button',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);
			$this->add_control(
				'form_button_color',
				[
					'label'     => esc_html__( 'Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'form_button_bg',
				[
					'label'     => esc_html__( 'Background', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'form_button_border',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit',
					]
				);
				$this->add_responsive_control(
					'form_button_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'form_button_box_shadow',
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit',
					]
				);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_form_button_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_control(
				'form_button_color_hover',
				[
					'label'     => esc_html__( 'Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit:hover' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'form_button_hover_bg',
				[
					'label'     => esc_html__( 'Background', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit:hover' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'form_button_hover_border',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit:hover',
					]
				);
				$this->add_responsive_control(
					'form_button_hover_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'form_button_hover_box_shadow',
						'selector' => '{{WRAPPER}} .theplus-password-pc-fields form.theplus-pc-form input.theplus-pc-submit:hover',
					]
				);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*form button end*/
		
		/*form error message start*/
		$this->start_controls_section(
			'form_err_msg',
			[
				'label' => esc_html__( 'Error Message' , 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pc_protection_type' => ['password','multiple_password'],
				],
			]
		);	
		$this->add_responsive_control(
			'form_err_msg_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-pc-error-msg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'form_err_msg_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-pc-error-msg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_err_msg_typography',
				'selector' => '{{WRAPPER}} .theplus-pc-error-msg',
			]
		);
		$this->add_control(
			'form_err_msg_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-pc-error-msg' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'form_err_msg_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-pc-error-msg',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_err_msg_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-pc-error-msg',
			]
		);
		$this->add_responsive_control(
			'form_err_msg_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-pc-error-msg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*form error message end*/
		/*front content start*/
		$this->start_controls_section(
			'pro_con_front',
			[
				'label' => esc_html__( 'Front Content' , 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_responsive_control(
            'pro_con_front_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width', 'theplus'),
				'size_units' => [ 'px', '%'  ],
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
					'{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap' => 'max-width: {{SIZE}}{{UNIT}}',
				],
                                'condition' => [
					'pc_protection_type!' => 'role',
				],				
            ]
        );
		$this->add_control(
			'pro_con_front_align',
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
                                'selectors' => [
					'{{WRAPPER}} .theplus-protected-content .theplus-pc-message-text' => 'text-align: {{VALUE}}',
				],
				'default' => 'center',
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'pro_con_front_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap,{{WRAPPER}} .plus_pc_wrapper .theplus-pc-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);		
		$this->add_responsive_control(
			'pro_con_front_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap,{{WRAPPER}} .plus_pc_wrapper .theplus-pc-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		
		$this->start_controls_tabs( 'tabs_pro_con_front_style' );
			$this->start_controls_tab(
				'pro_con_front_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);			
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'pro_con_front_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'pro_con_front_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap',
				]
			);
			$this->add_responsive_control(
				'pro_con_front_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'pro_con_front_box_shadow',
					'selector' => '{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap',
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'pro_con_front_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'pro_con_front_bg_hover',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'pro_con_front_border_hover',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap:hover',
				]
			);
			$this->add_responsive_control(
				'pro_con_front_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'pro_con_front_box_shadow_hover',
					'selector' => '{{WRAPPER}} .plus_pc_wrapper .plus_pc_inner_wrap:hover',
				]
			);
			$this->end_controls_tab();
			
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*front content end*/
		
		/*protected content start*/
		$this->start_controls_section(
			'pro_con',
			[
				'label' => esc_html__( 'Protected Content' , 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,				
			]
		);	
		$this->add_responsive_control(
		'pro_con_max_width',
		[
			'type' => Controls_Manager::SLIDER,
			'label' => esc_html__('Max Width', 'theplus'),
			'size_units' => [ 'px', '%'  ],
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
				'{{WRAPPER}} .plus_pc_wrapper .theplus-protected-content-main,{{WRAPPER}} .plus_pc_wrapper .theplus-protected-content' => 'max-width: {{SIZE}}{{UNIT}}',
			],				
		]
        );
		$this->add_control(
			'pro_con_align',
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
                                'condition' => [
					'pc_protection_type!' => 'role',
				],
				'default' => 'center',
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'pro_con_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-protected-content-main,{{WRAPPER}} .theplus-protected-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'pro_con_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-protected-content-main,{{WRAPPER}} .theplus-protected-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pro_con_typography',
				'selector' => '{{WRAPPER}} .theplus-protected-content-main .protected-content,{{WRAPPER}} .theplus-protected-content-main .protected-content p',
				'condition' => [
					'content_type' => 'content',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_input_field_style' );
			$this->start_controls_tab(
				'pro_con_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'pro_con_color',
				[
					'label'     => esc_html__( 'Text Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-protected-content-main .protected-content,{{WRAPPER}} .theplus-protected-content-main .protected-content p' => 'color: {{VALUE}};',
					],
					'condition' => [
						'content_type' => 'content',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'pro_con_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .theplus-protected-content-main,{{WRAPPER}} .theplus-protected-content',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'pro_con_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .theplus-protected-content-main,{{WRAPPER}} .theplus-protected-content',
				]
			);
			$this->add_responsive_control(
				'pro_con_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .theplus-protected-content-main,{{WRAPPER}} .theplus-protected-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'pro_con_box_shadow',
					'selector' => '{{WRAPPER}} .theplus-protected-content-main,{{WRAPPER}} .theplus-protected-content',
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'pro_con_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_control(
				'pro_con_color_hover',
				[
					'label'     => esc_html__( 'Text Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .theplus-protected-content-main .protected-content:hover,{{WRAPPER}} .theplus-protected-content-main .protected-content:hover p,{{WRAPPER}} .theplus-protected-content:hover' => 'color: {{VALUE}};',
					],
					'condition' => [
						'content_type' => 'content',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'pro_con_bg_hover',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .theplus-protected-content-main:hover,{{WRAPPER}} .theplus-protected-content:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'pro_con_border_hover',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .theplus-protected-content-main:hover,{{WRAPPER}} .theplus-protected-content:hover',
				]
			);
			$this->add_responsive_control(
				'pro_con_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .theplus-protected-content-main:hover,{{WRAPPER}} .theplus-protected-content:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'pro_con_box_shadow_hover',
					'selector' => '{{WRAPPER}} .theplus-protected-content-main:hover,{{WRAPPER}} .theplus-protected-content:hover',
				]
			);
			$this->end_controls_tab();
			
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*protected content end*/
		
	}
	
	/*Check current user role*/
	protected function current_user_writes() {
		if( ! is_user_logged_in() ) return;
		$user_role = reset(wp_get_current_user()->roles);
		if($this->get_settings('pc_role')){
			return in_array($user_role, $this->get_settings('pc_role'));
		}
		
	}
	
	
		protected function theplus_render_message($settings){
		$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['message_bg_image'],$settings['message_bg_hover_image']) : '';
		ob_start();?>
		<div class="theplus-pc-message <?php echo $lz1; ?>">
			<?php 
				if('none' == $settings['pc_message_source']){
				}
				elseif('text' == $settings['pc_message_source']) {?>
						<?php if ( ! empty( $settings['pc_message_source'] ) ) : ?>
							<div class="theplus-pc-message-text"><?php echo wp_kses_post($settings['pc_message_text']); ?></div>
						<?php endif; ?>
				<?php } 
				else {
					if ( !empty( $settings['pc_message_template'] ) ) {
						$theplus_et_id = $settings['pc_message_template'];
						$theplus_frontend = new Frontend;
						
						echo $theplus_frontend->get_builder_content( $theplus_et_id, true );
					}
				}
			?>
		</div>  
		<?php echo ob_get_clean();
	}
	
	protected function theplus_render_content($settings){
		ob_start(); ?>
			 <div class="protected-content">
				<?php if( 'content' === $settings['content_type'] ) : ?>
					<?php if ( ! empty( $settings['protected_content_field'] ) ) : ?>
						<p><?php echo wp_kses_post($settings['protected_content_field']); ?></p>
					<?php endif; ?>
				<?php elseif( 'page_template' === $settings['content_type'] ) :
					if ( !empty( $settings['protected_content_template'] ) ) {
						$theplus_et_id = $settings['protected_content_template'];
						$theplus_frontend = new Frontend;
						
						echo $theplus_frontend->get_builder_content( $theplus_et_id, true );
					}
				endif; ?>
			</div>
		<?php echo ob_get_clean();
	}
	
	 protected function render() {

       
		$settings = $this->get_settings_for_display();
		$widget_id=$this->get_id();
		$pro_con_front_align='text-'.$settings['pro_con_front_align']. ' align'.$settings['pro_con_front_align'];
		$pro_con_align='text-'.$settings['pro_con_align']. ' align'.$settings['pro_con_align'];
		
		$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['pro_con_bg_image'],$settings['pro_con_bg_hover_image']) : '';
		
		echo '<div class="plus_pc_wrapper">';
			if ('role' == $settings['pc_protection_type']) :
				echo '<div class="theplus-protected-content '.esc_attr($lz3).'">';
					if( true === $this->current_user_writes() ) :
						$this->theplus_render_content($this->get_settings_for_display());
					else :
						$this->theplus_render_message($this->get_settings_for_display());
					endif;

					if( 'yes' == $settings['pc_error_message']) : 
						$this->theplus_render_message($this->get_settings_for_display());
					endif;
				echo '</div>';
			else:
				if($settings['pc_protection_type']=='multiple_password' && !empty($settings['protection_password_list'])){
					foreach (  $settings['protection_password_list'] as $item ) {
						if( ! session_status() ) { session_start(); }
						if( isset($_POST['protection_password'.$widget_id]) && sanitize_text_field($_POST['protection_password'.$widget_id]) && ($item['protection_password_multi'] == $_POST['protection_password'.$widget_id]) ) {
							$_SESSION['protection_password'.$widget_id] = true;
						} 
					}
				}else{
					if( !empty($settings['protection_password'])  ) {
						if( ! session_status() ) { session_start(); }
						if( isset($_POST['protection_password'.$widget_id]) && sanitize_text_field($_POST['protection_password'.$widget_id]) && ($settings['protection_password'] == $_POST['protection_password'.$widget_id]) ) {
							$_SESSION['protection_password'.$widget_id] = true;
						}
					}
				}	
				
				if( ! isset($_SESSION['protection_password'.$widget_id]) && !isset($_COOKIE['protection_password'.$widget_id])) {
					$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['pro_con_front_bg_image'],$settings['pro_con_front_bg_hover_image']) : '';
					echo '<div class="plus_pc_inner_wrap '.esc_attr($pro_con_front_align).' '.esc_attr($lz2).'">';
						if( 'yes' !== $settings['show_content'] ) {
							$this->theplus_render_message($this->get_settings_for_display()); 
							theplus_pc_form($settings,$widget_id);
							echo '</div></div>';
							return;
						}
					echo '</div>';
				}
				
				$show_cookie = isset($settings['show_cookie']) ? $settings['show_cookie'] : '';
				$days= !empty($settings['days']) ? $settings['days'] : '';
				if($show_cookie=='yes' && !empty($days) && !\Elementor\Plugin::$instance->editor->is_edit_mode()){					
					 echo "<script type='text/javascript'>
						var expires = new Date();
						expires.setTime(expires.getTime() + (3600 * 1000 * 24 * {$days}));
						document.cookie = 'protection_password{$widget_id}=true;expires=' + expires.toUTCString();
					</script>";
				}
				
				if($show_cookie=='yes' && !empty($days) && \Elementor\Plugin::$instance->editor->is_edit_mode()){	
					 echo "<script type='text/javascript'>
						delete_cookie('protection_password{$widget_id}');
						alert('hiii');
					</script>";
				}
				
				if (isset($_COOKIE['protection_password'.$widget_id]) && $show_cookie=='yes' && !empty($days) && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {					
					echo '<div class="theplus-protected-content-main '.esc_attr($pro_con_align).' '.esc_attr($lz3).'">';
						$this->theplus_render_content($this->get_settings_for_display());
					echo '</div>';
				}else{
					echo '<div class="theplus-protected-content-main '.esc_attr($pro_con_align).' '.esc_attr($lz3).'">';
						$this->theplus_render_content($this->get_settings_for_display());
					echo '</div>';
				}
				
			endif; ?>
		</div><?php
	}
}