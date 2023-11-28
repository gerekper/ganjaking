<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Plugin;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Pro\Classes\Helper;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// If this file is called directly, abort.

class Protected_Content extends Widget_Base {
	public function get_name() {
		return 'eael-protected-content';
	}

	public function get_title() {
		return esc_html__( 'Protected Content', 'essential-addons-elementor' );
	}

	public function get_icon() {
		return 'eaicon-protected-content';
	}

	public function get_categories() {
		return [ 'essential-addons-elementor' ];
	}

	public function get_keywords() {
		return [
			'ea protected content',
			'age restriction',
			'restricted',
			'restriction',
			'locked',
			'ea',
			'essential addons',
		];
	}

	public function get_custom_help_url() {
		return 'https://essential-addons.com/elementor/docs/ea-protected-content/';
	}

	protected function register_controls() {

		/**
		 * Content Settings
		 */
		$this->start_controls_section(
			'eael_protected_content',
			[
				'label' => esc_html__( 'Protected Content', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_protected_content_type',
			[
				'label'   => __( 'Content Type', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'content'  => __( 'Content', 'essential-addons-elementor' ),
					'template' => __( 'Saved Templates', 'essential-addons-elementor' ),
				],
				'default' => 'content',
			]
		);

		$this->add_control(
			'eael_protected_content_field',
			[
				'label'       => esc_html__( 'Protected Content', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'This is the content that you want to be protected by either role or password.', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_protected_content_type' => 'content',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_template',
			[
				'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'post_type',
				'source_type' => 'elementor_library',
				'label_block' => true,
				'condition'   => [
					'eael_protected_content_type' => 'template',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Select protection type
		 */
		$this->start_controls_section(
			'eael_protected_content_protection',
			[
				'label' => esc_html__( 'Protection Type', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_protected_content_protection_type',
			[
				'label'       => esc_html__( 'Protection Type', 'essential-addons-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'role'     => esc_html__( 'User role', 'essential-addons-elementor' ),
					'password' => esc_html__( 'Password protected', 'essential-addons-elementor' ),
				],
				'default'     => 'role',
			]
		);

		$this->add_control(
			'eael_protected_content_role',
			[
				'label'       => __( 'Select Roles', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Helper::user_roles(),
				'condition'   => [
					'eael_protected_content_protection_type' => 'role',
				],
			]
		);

		$this->add_control(
			'eael_show_fallback_message',
			[
				'label'        => __( 'Show Preview of Error Message', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Show', 'essential-addons-elementor' ),
				'label_off'    => __( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'description'  => 'You can force show message in order to style them properly.',
				'condition'    => [
					'eael_protected_content_protection_type' => 'role',
				],
			]
		);

		$this->add_control(
			'protection_password',
			[
				'label'      => esc_html__( 'Set Password', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::TEXT,
				'input_type' => 'password',
				'condition'  => [
					'eael_protected_content_protection_type' => 'password',
				],
				'dynamic'    => [ 'active' => true ],
				'ai'         => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'protection_password_placeholder',
			[
				'label'     => esc_html__( 'Input Placehlder', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => 'Enter Password',
				'condition' => [
					'eael_protected_content_protection_type' => 'password',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'protection_password_submit_btn_txt',
			[
				'label'     => esc_html__( 'Submit Button Text', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => 'Submit',
				'condition' => [
					'eael_protected_content_protection_type' => 'password',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_show_content',
			[
				'label'        => __( 'Show Content', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Show', 'essential-addons-elementor' ),
				'label_off'    => __( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'description'  => 'You can force show content in order to style them properly.',
				'condition'    => [
					'eael_protected_content_protection_type' => 'password',
				],
			]
		);

		$this->add_control(
			'eael_scroll_to_section',
			[
				'label'        => __( 'Scroll to Section', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'    => __( 'No', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'eael_protected_content_protection_type' => 'password',
				],
			]
		);

		$this->add_control(
			'eael_remember_cookie',
			[
				'label'        => __( 'Remember Cookie', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Show', 'essential-addons-elementor' ),
				'label_off'    => __( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'eael_protected_content_protection_type' => 'password',
				],
			]
		);

		$this->add_control(
			'eael_remember_cookie_expire_time',
			[
				'label'       => __( 'Expire Time', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 60,
				"min"         => 10,
				'description' => __( 'Cookie expiration time (Minutes)', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_remember_cookie' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Show message
		 */
		$this->start_controls_section(
			'eael_protected_content_message',
			[
				'label' => esc_html__( 'Message', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_protected_content_message_type',
			[
				'label'       => esc_html__( 'Message Type', 'essential-addons-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Set a message or a saved template when the content is protected.', 'essential-addons-elementor' ),
				'options'     => [
					'none'     => esc_html__( 'None', 'essential-addons-elementor' ),
					'text'     => esc_html__( 'Message', 'essential-addons-elementor' ),
					'template' => esc_html__( 'Saved Templates', 'essential-addons-elementor' ),
				],
				'default'     => 'text',
			]
		);

		$this->add_control(
			'eael_protected_content_message_text',
			[
				'label'     => esc_html__( 'Public Text', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => esc_html__( 'You do not have permission to see this content.', 'essential-addons-elementor' ),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'eael_protected_content_message_type' => 'text',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_message_template',
			[
				'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'post_type',
				'source_type' => 'elementor_library',
				'label_block' => true,
				'condition'   => [
					'eael_protected_content_message_type' => 'template',
				],
			]
		);

		$this->add_control(
			'password_incorrect_heading',
			[
				'label'     => __( 'Incorrect Password', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'password_incorrect_message',
			[
				'label'   => esc_html__( 'Message', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Password does not match.', 'essential-addons-elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_protected_content_style',
			[
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_protected_content_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-protected-content .protected-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_protected_content_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY
				],
				'selector' => '{{WRAPPER}} .eael-protected-content .protected-content',
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_alignment',
			[
				'label'       => esc_html__( 'Text Alignment', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'left'   => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .eael-protected-content .protected-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-protected-content .protected-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_protected_content_message_style',
			[
				'label' => esc_html__( 'Message', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'eael_protected_content_general_message',
			[
				'label' => __( 'Permission Message', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_control(
			'eael_protected_content_message_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-protected-content-message' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_protected_content_message_type' => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_protected_content_message_text_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY
				],
				'selector'  => '{{WRAPPER}} .eael-protected-content-message',
				'condition' => [
					'eael_protected_content_message_type' => 'text',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_message_text_alignment',
			[
				'label'       => esc_html__( 'Text Alignment', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'left'   => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .eael-protected-content-message' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_message_text_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-protected-content-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_protected_content_message_type' => 'text',
				],
			]
		);

        $this->add_control(
			'eael_protected_content_error_message',
			[
				'label' => __( 'Error Message', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'eael_protected_content_error_message_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .protected-content-error-msg' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_protected_content_message_type' => 'text',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_protected_content_error_message_text_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_SECONDARY
                ],
                'selector' => '{{WRAPPER}} .protected-content-error-msg',
                'condition' => [
                    'eael_protected_content_message_type' => 'text',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_protected_content_error_message_text_alignment',
            [
                'label' => esc_html__('Text Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .protected-content-error-msg' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_protected_content_error_message_text_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .protected-content-error-msg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_protected_content_message_type' => 'text',
                ],
            ]
        );

		$this->end_controls_section();

		// password field style
		$this->start_controls_section(
			'eael_protected_content_password_field_style',
			[
				'label'     => esc_html__( 'Password Field', 'essential-addons-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_protected_content_protection_type' => 'password',
				],

			]
		);

		$this->add_control(
			'eael_protected_content_input_width',
			[
				'label'     => esc_html__( 'Input Width', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'width: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_input_alignment',
			[
				'label'       => esc_html__( 'Input Alignment', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .eael-password-protected-content-fields > form' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_password_input_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_password_input_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_input_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->start_controls_tabs( 'eael_protected_content_password_input_style_tab' );

		$this->start_controls_tab( 'eael_protected_content_password_input_normal_style', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_protected_content_password_input_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_password_input_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_protected_content_password_input_border',
				'label'    => esc_html__( 'Border', 'essential-addons-elementor' ),
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_protected_content_password_input_shadow',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_protected_content_password_input_hover_style', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_protected_content_password_input_hover_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_password_input_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_protected_content_password_input_hover_border',
				'label'    => esc_html__( 'Border', 'essential-addons-elementor' ),
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_protected_content_password_input_hover_shadow',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//submit button style
		$this->start_controls_section(
			'eael_protected_content_submit_button',
			[
				'label'     => esc_html__( 'Button', 'essential-addons-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_protected_content_protection_type' => 'password',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_protected_content_submit_button_typography',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit',
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_submit_padding',
			[
				'label'      => esc_html__( 'Button Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_protected_content_submit_margin',
			[
				'label'      => esc_html__( 'Button Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_submit_button_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->start_controls_tabs( 'eael_protected_content_submit_button_control_tabs' );

		$this->start_controls_tab( 'eael_protected_content_submit_button_normal_tab', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_protected_content_submit_button_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_submit_button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_protected_content_submit_button_border',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_protected_content_submit_button_box_shadow',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_protected_content_submit_button_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_protected_content_submit_button_hover_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_protected_content_submit_button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_protected_content_submit_button_hover_border',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_protected_content_submit_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Check current user role exists inside of the roles array
	 *
	 * @return boolean
	 */
	protected function current_user_privileges() {
		if ( !is_user_logged_in() ) {
			return false;
		}

		$user_role = wp_get_current_user()->roles ;
		return !empty( array_intersect( $user_role,(array)$this->get_settings( 'eael_protected_content_role' ) ));
	}

	protected function eael_render_message( $settings ) {
		ob_start(); ?>
        <div class="eael-protected-content-message">
			<?php
			if ( 'none' == $settings[ 'eael_protected_content_message_type' ] ) {
				//nothing happen
			} elseif ( 'text' == $settings[ 'eael_protected_content_message_type' ] ) { ?>
				<?php if ( !empty( $settings[ 'eael_protected_content_message_type' ] ) ) : ?>
					<div class="eael-protected-content-message-text"><?php echo Helper::eael_wp_kses( $settings['eael_protected_content_message_text'] ); ?></div>
				<?php endif; ?>
			<?php } else {
				if ( ! empty( $settings['eael_protected_content_message_template'] ) ) {
					// WPML Compatibility
					if ( ! is_array( $settings['eael_protected_content_message_template'] ) ) {
						$settings['eael_protected_content_message_template'] = apply_filters( 'wpml_object_id', $settings['eael_protected_content_message_template'], 'wp_template', true );
					}
					echo Plugin::$instance->frontend->get_builder_content( $settings['eael_protected_content_message_template'], true );
				}
			}
			?>
        </div>
		<?php echo ob_get_clean();
	}

	protected function eael_render_content( $settings ) {
		$editor_content = $this->get_settings_for_display( 'eael_protected_content_field' );
		$editor_content = $this->parse_text_editor( $editor_content );
		ob_start();
		?>
        <div id="eael-protected-content-render-<?php echo $this->get_id(); ?>" class="protected-content">
			<?php
			if ( 'content' === $settings[ 'eael_protected_content_type' ] ) {
				if ( !empty( $editor_content ) ) {
					printf( "<p>%s</p>", $this->parse_text_editor( $editor_content ) );
				}
			} elseif ( 'template' === $settings[ 'eael_protected_content_type' ] ) {
				if ( ! empty( $settings['eael_protected_content_template'] ) ) {
					// WPML Compatibility
					if ( ! is_array( $settings['eael_protected_content_template'] ) ) {
						$settings['eael_protected_content_template'] = apply_filters( 'wpml_object_id', $settings['eael_protected_content_template'], 'wp_template', true );
					}
					echo Plugin::$instance->frontend->get_builder_content( $settings['eael_protected_content_template'], true );
				}
			}
			?>
        </div>
		<?php
		return ob_get_clean();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$force_view = \Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['eael_show_content'] === 'yes';
		if ( 'role' == $settings[ 'eael_protected_content_protection_type' ] ) :
			?>
            <div class="eael-protected-content"> <?php

				if ( $this->current_user_privileges() ) {
					echo $this->eael_render_content( $settings );
				} else {
					$this->eael_render_message( $settings );
				}

				if ( 'yes' == $settings[ 'eael_show_fallback_message' ] ) {
					$this->eael_render_message( $settings );
				}
				?>
            </div>
		<?php else : ?>
			<?php
			if ( ! empty( $settings['protection_password'] ) ) {
				$unlocked = false;
				if ( isset( $_POST[ 'protection_password_' . $this->get_id() ] )
				     && ( $settings['protection_password'] === $_POST[ 'protection_password_' . $this->get_id() ] )
				     && ( wp_verify_nonce( $_POST[ 'eael_protected_content_nonce_' . $this->get_id() ], 'eael_protected_nonce' ) ) ) {
					$unlocked = true;
					$token    = md5( 'eael_pc_' . $_POST[ 'protection_password_' . $this->get_id() ] );
					$this->eael_remember_cookie( $token );
				}

				if ( ( ! empty( $_COOKIE[ 'protection_password_' . $this->get_id() ] ) && $_COOKIE[ 'protection_password_' . $this->get_id() ] === md5( 'eael_pc_' . $settings['protection_password'] ) ) || $unlocked || $force_view ) {
					echo '<div class="eael-protected-content">'
					     . $this->eael_render_content( $settings ) .
					     '</div>';
					$this->eael_protected_content_scroll();
				} else {
					$this->eael_render_message( $settings );
					$this->get_block_pass_protected_form( $this->get_id(), $settings );
				}
			}
			?>
		<?php endif; ?>
		<?php
	}

	/**
	 * get_block_pass_protected_form
	 * @param $widget_id
	 * @param $settings
	 */
	public function get_block_pass_protected_form( $widget_id, $settings ) {
		?>
        <div class="eael-password-protected-content-fields">
            <form id ="eael_protected_content_form_<?php echo $widget_id; ?>" name="eael_protected_content_form_<?php echo $widget_id; ?>" method="post">
                <input type="password" name="protection_password_<?php echo $widget_id; ?>" class="eael-password"
                       placeholder="<?php echo Helper::eael_wp_kses( $settings[ 'protection_password_placeholder' ] ); ?>">
                <input type="hidden" name="eael_protected_content_nonce_<?php echo $widget_id; ?>"
                       value="<?php echo esc_attr( wp_create_nonce( 'eael_protected_nonce' ) ); ?>">
                <input type="submit" value="<?php echo Helper::eael_wp_kses( $settings[ 'protection_password_submit_btn_txt' ] ); ?>"
                       class="eael-submit">
				<?php

				if ( 'template' === $settings[ 'eael_protected_content_type' ] ) {
					echo sprintf( '<input name="eael_protected_content_id" value="%s" type="hidden">', $widget_id );
				}
				?>
            </form>
	        <?php
	        if ( isset( $_POST[ 'protection_password_' . $widget_id ] ) && ( $settings[ 'protection_password' ] !== $_POST[ 'protection_password_' . $widget_id ] ) ) {
		        echo sprintf(
			        __( '<p class="protected-content-error-msg">%s</p>', 'essential-addons-elementor' ),
			        Helper::eael_wp_kses( $settings[ 'password_incorrect_message' ] )
		        );
	        }
	        ?>
        </div>
		<?php
		$this->eael_protected_content_scroll( 'form' );
	}


	/**
	 * eael_remember_cookie
	 */
	public function eael_remember_cookie( $token ) {
		if ( !isset( $_POST[ 'protection_password_' . $this->get_id() ] )){
			return false;
		}
		$remember_cookie = $this->get_settings( 'eael_remember_cookie' );
		if ( $remember_cookie == 'yes' ) {
			$expire_time = (int)$this->get_settings( 'eael_remember_cookie_expire_time' ) * 60 * 1000;
			echo "<script>
                var expires = new Date();
                var expires_time = expires.getTime() + parseInt(" . $expire_time . ");
                expires.setTime(expires_time);
                document.cookie = 'protection_password_{$this->get_id()}={$token};expires=' + expires.toUTCString();
            </script>";
		}
	}

	public function eael_protected_content_scroll( $content = 'content' ){

		if ( $this->get_settings_for_display( 'eael_scroll_to_section' ) !== 'yes' ) return;

		if ( isset( $_POST[ 'protection_password_' . $this->get_id() ] )){

			$form_id = "eael-protected-content-render-" . $this->get_id();

			if ( $content === 'form' ) {
				$form_id = "eael_protected_content_form_" . $this->get_id();
			}
			?>
            <script>
                jQuery(document).ready(function ($) {
                    var id = "#<?php echo $form_id; ?>";
                    $('html, body').animate({
                        scrollTop: $(id).offset().top
                    }, 2000);
                });
            </script>
			<?php
		}
	}
}
