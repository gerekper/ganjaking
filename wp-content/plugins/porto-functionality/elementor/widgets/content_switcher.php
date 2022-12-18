<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Content Switcher Widget
 *
 * Porto Elementor widget to switch content
 *
 * @since 2.6.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Porto_Elementor_Content_Switcher_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_content_switcher';
	}

	public function get_title() {
		return __( 'Porto Content Switcher', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'toggle', 'switcher', 'content', 'shortcode' );
	}

	public function get_icon() {
		return 'fas fa-toggle-off';
	}

	public function get_script_depends() {
		$depends = array();
		if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
			$depends[] = 'porto-content-switch';
		}
		return $depends;
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/content-switcher-element/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_Switcher	',
			array(
				'label' => __( 'Switcher', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'first_label',
				array(
					'label'       => esc_html__( 'First Label', 'porto-functionality' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => 'First Label',
					'default'     => '',
				)
			);

			$this->add_control(
				'first_content',
				array(
					'label'   => esc_html__( 'Enter your html for first content.', 'porto-functionality' ),
					'type'    => Controls_Manager::TEXTAREA,
					'default' => 'Pellentesque pellentesque tempor tellus eget hendrerit. Morbi id aliquam ligula.',
				)
			);

			$this->add_control(
				'second_label',
				array(
					'label'       => esc_html__( 'Second Label', 'porto-functionality' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => 'Second',
					'default'     => '',
				)
			);

			$this->add_control(
				'second_content',
				array(
					'label'   => esc_html__( 'Enter your html for second content', 'porto-functionality' ),
					'type'    => Controls_Manager::TEXTAREA,
					'default' => 'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.',
				)
			);

			$this->add_control(
				'switch_align',
				array(
					'label'     => __( 'Switch Alignment', 'porto-functionality' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => __( 'Left', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'     => array(
							'title' => __( 'Center', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-center',
						),
						'flex-end'   => array(
							'title' => __( 'Right', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'default'   => 'center',
					'toggle'    => true,
					'selectors' => array(
						'.elementor-element-{{ID}} .content-switcher-wrapper .content-switch' => 'justify-content:{{VALUE}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'content_padding',
				array(
					'label'       => __( 'Content Padding', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array( 'px', '%', 'rem' ),
					'qa_selector' => '.tab-content',
					'selectors'   => array(
						'.elementor-element-{{ID}} .content-switcher-wrapper .tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'switcher_style',
			array(
				'label' => __( 'Switcher Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'typography',
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .switcher-label',

				)
			);

			$this->add_control(
				'label_spacing',
				array(
					'label'       => __( 'Label Spacing', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', 'rem' ),
					'range'       => array(
						'px'  => array(
							'min' => 0,
							'max' => 1000,
						),
						'rem' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} .switch-input' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
					),
					'qa_selector' => '.text-first',
				)
			);

			$this->add_control(
				'switch_size',
				array(
					'label'       => __( 'Switch Size', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', 'em', 'rem' ),
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 100,
						),
						'em' => array(
							'min' => 1,
							'max' => 50,
						),
						'em' => array(
							'min' => 1,
							'max' => 50,
						),
					),
					'qa_selector' => '.switch-input',
					'selectors'   => array(
						'{{WRAPPER}} .switch-input' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs(
				'label_tabs'
			);

				$this->start_controls_tab(
					'label_style_normal',
					array(
						'label' => __( 'Normal', 'porto-functionality' ),
					)
				);

				$this->add_control(
					'label_color',
					array(
						'label'     => __( 'Label Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switcher-label' => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'switch_color',
					array(
						'label'     => __( 'Switch Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switch-input .toggle-button:before' => 'background-color : {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'switch_background_color',
					array(
						'label'     => __( 'Background Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switch-input .toggle-button' => 'background-color : {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'switch_border_color',
					array(
						'label'     => __( 'Border Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switch-input .toggle-button' => 'border-color : {{VALUE}}',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'label_style_active',
					array(
						'label' => __( 'Active', 'porto-functionality' ),
					)
				);

				$this->add_control(
					'label_color_active',
					array(
						'label'     => __( 'Label Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switcher-label.active' => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'switch_acolor',
					array(
						'label'     => __( 'Switch Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switch-input .switch-toggle:checked+.toggle-button:before' => 'background-color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'switch_background_acolor',
					array(
						'label'     => __( 'Background Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switch-input .switch-toggle:checked+.toggle-button' => 'background-color : {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'switch_border_acolor',
					array(
						'label'     => __( 'Border Color', 'porto-functionality' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} .switch-input .switch-toggle:checked+.toggle-button' => 'border-color : {{VALUE}}',
						),
					)
				);

				$this->end_controls_tab();

			$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_content_switcher' ) ) {
			$atts['page_builder'] = 'elementor';
			include $template;
		}
	}
}
