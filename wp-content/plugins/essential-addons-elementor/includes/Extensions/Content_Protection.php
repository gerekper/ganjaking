<?php

namespace Essential_Addons_Elementor\Pro\Extensions;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Plugin;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Essential_Addons_Elementor\Pro\Classes\Helper;
use http\Message\Body;

class Content_Protection {
	private $wdiget_id;

	public function __construct() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ], 10 );
		add_action( 'elementor/widget/render_content', [ $this, 'render_content' ], 10, 2 );
	}

	public function register_controls( $element ) {
		$element->start_controls_section(
			'eael_ext_content_protection_section',
			[
				'label' => __( '<i class="eaicon-logo"></i> Content Protection', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'eael_ext_content_protection',
			[
				'label'        => __( 'Enable Content Protection', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'    => __( 'No', 'essential-addons-elementor' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'eael_ext_content_protection_type',
			[
				'label'       => esc_html__( 'Protection Type', 'essential-addons-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'role'     => esc_html__( 'User role', 'essential-addons-elementor' ),
					'password' => esc_html__( 'Password protected', 'essential-addons-elementor' ),
				],
				'default'     => 'role',
				'condition'   => [
					'eael_ext_content_protection' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_role',
			[
				'label'       => __( 'Select Roles', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Helper::user_roles(),
				'condition'   => [
					'eael_ext_content_protection'      => 'yes',
					'eael_ext_content_protection_type' => 'role',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_password',
			[
				'label'      => esc_html__( 'Set Password', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::TEXT,
				'input_type' => 'password',
				'condition'  => [
					'eael_ext_content_protection'      => 'yes',
					'eael_ext_content_protection_type' => 'password',
				],
				'dynamic'    => [ 'active' => true ],
				'ai'         => [
					'active' => false,
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_password_placeholder',
			[
				'label'     => esc_html__( 'Input Placehlder', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => 'Enter Password',
				'condition' => [
					'eael_ext_content_protection'      => 'yes',
					'eael_ext_content_protection_type' => 'password',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_password_submit_btn_txt',
			[
				'label'     => esc_html__( 'Submit Button Text', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => 'Submit',
				'condition' => [
					'eael_ext_content_protection'      => 'yes',
					'eael_ext_content_protection_type' => 'password',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$element->add_control(
			'eael_ext_scroll_to_section',
			[
				'label'        => __( 'Scroll to Section', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'    => __( 'No', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_content_protection_cookie',
			[
				'label'        => __( 'Remember Cookie', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Show', 'essential-addons-elementor' ),
				'label_off'    => __( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_content_protection_cookie_expire_time',
			[
				'label'       => __( 'Expire Time', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 60,
				"min"         => 10,
				'description' => __( 'Cookie expiration time (Minutes)', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_content_protection_cookie' => 'yes',
				],
			]
		);

		$element->start_controls_tabs(
			'eael_ext_content_protection_tabs',
			[
				'condition' => [
					'eael_ext_content_protection' => 'yes',
				],
			]
		);

		$element->start_controls_tab(
			'eael_ext_content_protection_tab_message',
			[
				'label' => __( 'Message', 'essential-addons-elementor' ),
			]
		);

		$element->add_control(
			'eael_ext_content_protection_message_type',
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

		$element->add_control(
			'eael_ext_content_protection_message_text',
			[
				'label'     => esc_html__( 'Public Text', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => esc_html__( 'You do not have permission to see this content.', 'essential-addons-elementor' ),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'eael_ext_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_message_template',
			[
				'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'label_block' => true,
				'source_type' => 'elementor_library',
				'condition'   => [
					'eael_ext_content_protection_message_type' => 'template',
				],
			]
		);

        $element->add_control(
            'eael_ext_content_protection_password_incorrect_heading',
            [
                'label' => __('Incorrect Password', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $element->add_control(
            'eael_ext_content_protection_password_incorrect_message',
            [
                'label' => esc_html__('Message', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Password does not match.', 'essential-addons-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
				'ai' => [
					'active' => false,
				],
            ]
        );

		$element->end_controls_tab();

		$element->start_controls_tab(
			'eael_ext_content_protection_tab_style',
			[
				'label' => __( 'Style', 'essential-addons-elementor' ),
			]
		);

		# message
		$element->add_control(
			'eael_ext_content_protection_general_message',
			[
				'label' => __( 'Permission Message', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);

		$element->add_control(
			'eael_ext_content_protection_message_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-protected-content-message' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_message_type' => 'text',
				],
                'separator' => 'before',
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_ext_content_protection_message_text_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY
				],
				'selector'  => '{{WRAPPER}} .eael-protected-content-message',
				'condition' => [
					'eael_ext_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'eael_ext_content_protection_message_text_alignment',
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
				'condition'   => [
					'eael_ext_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'eael_ext_content_protection_message_text_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-protected-content-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_ext_content_protection_message_type' => 'text',
				],
			]
		);

        $element->add_control(
			'eael_ext_content_protection_error_message',
			[
				'label' => __( 'Error Message', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $element->add_control(
            'eael_ext_content_protection_error_message_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .protected-content-error-msg' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_ext_content_protection_message_type' => 'text',
                ],
                'separator' => 'before',
            ]
        );

        $element->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_ext_content_protection_error_message_text_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_SECONDARY
                ],
                'selector' => '{{WRAPPER}} .protected-content-error-msg',
                'condition' => [
                    'eael_ext_content_protection_message_type' => 'text',
                ],
            ]
        );

        $element->add_responsive_control(
            'eael_ext_content_protection_error_message_text_alignment',
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
                'condition' => [
                    'eael_ext_content_protection_message_type' => 'text',
                ],
            ]
        );

        $element->add_responsive_control(
            'eael_ext_content_protection_error_message_text_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .protected-content-error-msg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_ext_content_protection_message_type' => 'text',
                ],
                'separator' => 'after',
            ]
        );

		# password field
		$element->add_control(
			'eael_ext_content_protection_input_styles',
			[
				'label'     => __( 'Password Field', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_input_width',
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
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
                'separator' => 'before',
			]
		);

		$element->add_responsive_control(
			'eael_ext_content_protection_input_alignment',
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
				'condition'   => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'eael_ext_content_protection_password_input_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'eael_ext_content_protection_password_input_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_input_border_radius',
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
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_password_input_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_password_input_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'eael_ext_content_protection_password_input_border',
				'label'     => esc_html__( 'Border', 'essential-addons-elementor' ),
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'eael_ext_content_protection_password_input_shadow',
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		# password field hover
		$element->add_control(
			'eael_ext_content_protection_input_styles_hover',
			[
				'label'     => __( 'Password Field Hover', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_protected_content_password_input_hover_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_protected_content_password_input_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields input.eael-password:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'eael_ext_protected_content_password_input_hover_border',
				'label'     => esc_html__( 'Border', 'essential-addons-elementor' ),
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password:hover',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'eael_ext_protected_content_password_input_hover_shadow',
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-password"hover',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		# submit button
		$element->add_control(
			'eael_ext_content_protection_submit_button_styles',
			[
				'label'     => __( 'Submit Button', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_submit_button_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_submit_button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'eael_ext_content_protection_submit_button_border',
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'eael_ext_content_protection_submit_button_box_shadow',
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_submit_button_styles_hover',
			[
				'label'     => __( 'Submit Button Hover', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_submit_button_hover_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'eael_ext_content_protection_submit_button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'eael_ext_content_protection_submit_button_hover_border',
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'eael_ext_content_protection_submit_button_hover_box_shadow',
				'selector'  => '{{WRAPPER}} .eael-password-protected-content-fields .eael-submit:hover',
				'condition' => [
					'eael_ext_content_protection_type' => 'password',
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();
	}

	# Check current user role exists inside of the roles array.
	protected function current_user_privileges( $settings ) {
		if ( !is_user_logged_in() ) {
			return;
		}

        $user_role = wp_get_current_user()->roles ;
        return !empty( array_intersect( $user_role,(array)$settings['eael_ext_content_protection_role'] ));
	}

	/**
	 * render message
	 * @param $settings
	 * @return string
	 */
	protected function render_message( $settings ) {
		$html = '<div class="eael-protected-content-message">';

		if ( $settings[ 'eael_ext_content_protection_message_type' ] == 'text' ) {
			$html .= '<div class="eael-protected-content-message-text">' . $settings[ 'eael_ext_content_protection_message_text' ] . '</div>';
		} elseif ( $settings[ 'eael_ext_content_protection_message_type' ] == 'template' ) {
			if ( ! empty( $settings['eael_ext_content_protection_message_template'] ) ) {
				// WPML Compatibility
				if ( ! is_array( $settings['eael_ext_content_protection_message_template'] ) ) {
					$settings['eael_ext_content_protection_message_template'] = apply_filters( 'wpml_object_id', $settings['eael_ext_content_protection_message_template'], 'wp_template', true );
				}
				$html .= Plugin::$instance->frontend->get_builder_content( $settings['eael_ext_content_protection_message_template'], true );
			}
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * password input form
	 * @param $widget_id
	 * @param $settings
	 * @return string
	 */
	public function password_protected_form( $widget_id, $settings ) {
		$html = '<div class="eael-password-protected-content-fields">
            <form method="post">
                <input type="password" name="eael_ext_content_protection_password_' . $widget_id . '" class="eael-password" placeholder="' . $settings[ 'eael_ext_content_protection_password_placeholder' ] . '">
                <input type="hidden" name="eael_content_protection_nonce_' . $widget_id . '" value="' . esc_attr( wp_create_nonce( 'eael_protected_nonce' ) ) . '" >
                <input type="submit" value="' . $settings[ 'eael_ext_content_protection_password_submit_btn_txt' ] . '" class="eael-submit">
            </form>';

		if ( isset( $_POST[ 'eael_ext_content_protection_password_' . $widget_id ] ) ) {
			if ( $settings[ 'eael_ext_content_protection_password' ] != $_POST[ 'eael_ext_content_protection_password_' . $widget_id ] ) {
				$html .= sprintf(
                    __('<p class="protected-content-error-msg">%s</p>', 'essential-addons-elementor'),
                    Helper::eael_wp_kses( $settings['eael_ext_content_protection_password_incorrect_message'] )
                );
			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * render_content
	 * @param $content
	 * @param $widget
	 * @return string
	 */
	public function render_content( $content, $widget ) {
		$widget_id = $widget->get_id();
		$settings  = $widget->get_settings_for_display();
		$html      = '';

		if ( $settings[ 'eael_ext_content_protection' ] == 'yes' ) {
			if ( $settings[ 'eael_ext_content_protection_type' ] == 'role' ) {
				if ( $this->current_user_privileges( $settings ) === true ) {
					$html .= $content;
				} else {
					$html .= '<div class="eael-protected-content jjjjß">' . $this->render_message( $settings ) . '</div>';
				}
			} elseif ( $settings[ 'eael_ext_content_protection_type' ] == 'password' ) {
				if ( empty( $settings[ 'eael_ext_content_protection_password' ] ) ) {
					$html .= $content;
				} else {
					$unlocked = false;

					if ( isset( $_POST[ 'eael_ext_content_protection_password_' . $widget_id ] ) ) {
						if ( ( $settings[ 'eael_ext_content_protection_password' ] == $_POST[ 'eael_ext_content_protection_password_' . $widget_id ] ) && wp_verify_nonce( $_POST[ 'eael_content_protection_nonce_' . $widget_id ], 'eael_protected_nonce' ) ) {
							$unlocked = true;
							$token = md5( 'eael_ext_pc_' . $_POST[ 'eael_ext_content_protection_password_' . $widget_id ] );
							$this->eael_content_protection_remember_cookie( $widget, $token );
						}
					}

					if ( ( ! empty( $_COOKIE[ 'eael_ext_content_protection_password_' . $widget_id ] ) && $_COOKIE[ 'eael_ext_content_protection_password_' . $widget_id ] === md5( 'eael_ext_pc_' . $settings['eael_ext_content_protection_password'] ) ) || $unlocked ) {
						$html .= $content;
						$html .= $this->eael_content_protection_scroll( $widget );
					} else {
						$html .= '<div class="eael-protected-content">' . $this->render_message( $settings ) . $this->password_protected_form( $widget_id, $settings ) . '</div>';
						$html .= $this->eael_content_protection_scroll( $widget );
					}
				}
			}
		} else {
			$html .= $content;
		}

		return $html;
	}


	/**
	 * eael_content_protection_remember_cookie
	 * @param $widget
	 * @return false|string
	 */
	public function eael_content_protection_remember_cookie( $widget, $token ) {
		if ( !isset( $_POST[ 'eael_ext_content_protection_password_' . $widget->get_id() ] ) ) {
			return false;
		}
		$remember_cookie = $widget->get_settings( 'eael_content_protection_cookie' );
		if ( $remember_cookie == 'yes' ) {
			$expire_time = (int)$widget->get_settings( 'eael_content_protection_cookie_expire_time' ) * 60 * 1000;
			echo "<script>
                var expires = new Date();
                var expires_time = expires.getTime() + parseInt(" . $expire_time . ");
                expires.setTime(expires_time);
                document.cookie = 'eael_ext_content_protection_password_{$widget->get_id()}={$token};expires=' + expires.toUTCString();
            </script>";
		}
	}

	/**
	 * Scroll down exact location
	 * @param $widget
	 * @return false|string
	 */
	public function eael_content_protection_scroll( $widget ) {

		if ( $widget->get_settings_for_display( 'eael_ext_scroll_to_section' ) !== 'yes' ) return;

		if ( isset( $_POST[ 'eael_ext_content_protection_password_' . $widget->get_id() ] ) ) {
			ob_start();
			$form_id = "elementor-element-" . $widget->get_id();
			?>
			<script>
                jQuery(document).ready(function ($) {
                    var id = ".<?php echo $form_id; ?>";
                    $('html, body').animate({
                        scrollTop: $(id).offset().top
                    }, 2000);
                });
			</script>
			<?php
			return ob_get_clean();
		}
		return false;
	}

}
