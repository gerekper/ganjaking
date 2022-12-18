<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Social Icons widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

class Porto_Elementor_HB_Social_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_social';
	}

	public function get_title() {
		return __( 'Header Social Icons', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'social', 'icon' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-social-facebook';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/porto-social-icons-element/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_hb_social',
			array(
				'label' => __( 'Social Icons', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'description_social',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( esc_html__( 'Please see %1$sTheme Options -> Header -> Social Links%2$s panel.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'icon_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Size', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 6,
							'max'  => 50,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0.1,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#header .elementor-element-{{ID}} a' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_border_spacing',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Bg Size', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0.1,
							'max'  => 10,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#header .elementor-element-{{ID}} a' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_spacing',
				array(
					'label'      => esc_html__( 'Icon Margin', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#header .elementor-element-{{ID}} a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_icon_style' );
				$this->start_controls_tab(
					'tab_icon_normal',
					array(
						'label' => __( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'icon_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} a:not(:hover)' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'icon_color_bg',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} a:not(:hover)' => 'background: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'icon_color_border',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} a' => 'border-color: {{VALUE}};',
							),
							'condition' => array(
								'icon_border_style!' => '',
							),
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_icon_hover',
					array(
						'label' => __( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'icon_hover_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} a:hover' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'icon_hover_color_bg',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} a:hover' => 'background: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'icon_hover_color_border',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Border Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} a:hover' => 'border-color: {{VALUE}};',
							),
							'condition' => array(
								'icon_border_style!' => '',
							),
						)
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
				'icon_border_style',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Icon Border Style', 'porto-functionality' ),
					'options'   => array(
						''       => __( 'None', 'porto-functionality' ),
						'solid'  => __( 'Solid', 'porto-functionality' ),
						'dashed' => __( 'Dashed', 'porto-functionality' ),
						'dotted' => __( 'Dotted', 'porto-functionality' ),
						'double' => __( 'Double', 'porto-functionality' ),
						'inset'  => __( 'Inset', 'porto-functionality' ),
						'outset' => __( 'Outset', 'porto-functionality' ),
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} a' => 'border-style: {{VALUE}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'icon_border_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Border Width', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 10,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0.1,
							'max'  => 4,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} a' => 'border-width: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'icon_border_style!' => '',
					),
				)
			);

			$this->add_control(
				'icon_border_radius',
				array(
					'type'      => Controls_Manager::SLIDER,
					'label'     => __( 'Icon Border Radius (px)', 'porto-functionality' ),
					'range'     => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 50,
						),
					),
					'selectors' => array(
						'#header .elementor-element-{{ID}} a' => 'border-radius: {{SIZE}}{{UNIT}};',
					),
					'separator' => 'after',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'icon_box_shadow',
					'selector' => '#header .elementor-element-{{ID}} a',
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'porto_header_elements' ) ) {
			porto_header_elements( array( (object) array( 'social' => '' ) ) );
		}
	}
}
