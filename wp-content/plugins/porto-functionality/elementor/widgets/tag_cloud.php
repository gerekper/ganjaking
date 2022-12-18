<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Tag Cloud widget
 *
 * @since 2.6.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

class Porto_Elementor_Tag_Cloud_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_tag_cloud';
	}

	public function get_title() {
		return __( 'Porto Tag Cloud', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'tags', 'portfolio' );
	}

	public function get_icon() {
		return 'eicon-tags';
	}

	protected function register_controls() {
		$left = is_rtl() ? 'right' : 'left';

		$this->start_controls_section(
			'section_taxonomy',
			array(
				'label' => __( 'Taxonomy', 'porto-functionality' ),
			)
		);
			$this->add_control(
				'taxonomy',
				array(
					'label'       => __( 'Taxonomy Name', 'porto-functionality' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'post_tag', 'porto-functionality' ),
					'description' => __( 'Please input the tag taxonomy name. e.g: post_tag, product_tag and etc.', 'porto-functionality' ),
				)
			);
			$this->add_control(
				'show_count',
				array(
					'type'  => Controls_Manager::SWITCHER,
					'label' => __( 'Show Count', 'porto-functionality' ),
				)
			);
			$this->add_control(
				'hide_title',
				array(
					'type'      => Controls_Manager::SWITCHER,
					'label'     => __( 'Hide Title', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .widgettitle' => 'display:none;',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_tag_style',
			array(
				'label' => __( 'Tag Style', 'porto-functionality' ),
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'tag_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .widget .tagcloud a',
				)
			);
			$this->add_control(
				'tag_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Font Size', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'min' => 0,
							'max' => 72,
						),
						'rem' => array(
							'min' => 0,
							'max' => 72,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .widget .tagcloud a' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					),
				)
			);
			$this->add_control(
				'tag_margin',
				array(
					'label'      => esc_html__( 'Margin', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .widget .tagcloud a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'tag_padding',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .widget .tagcloud a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'tag_border',
				array(
					'label'      => esc_html__( 'Border', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .widget .tagcloud a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};border-style: solid;',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_tag' );
				$this->start_controls_tab(
					'tab_tag',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'tag_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .widget .tagcloud a' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'tag_bd_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .widget .tagcloud a' => 'border-color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'tag_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .widget .tagcloud a' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_tag_hover',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'tag_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .widget .tagcloud a:hover' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'tag_bd_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .widget .tagcloud a:hover' => 'border-color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'tag_bg_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .widget .tagcloud a:hover' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
				'tag_br',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Border Radius', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'%'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .widget .tagcloud a' => 'border-radius: {{SIZE}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_control(
				'tag_mb',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Bottom Spacing', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'rem' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .widget_tag_cloud' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tag_count_style',
			array(
				'label'     => __( 'Tag Count Style', 'porto-functionality' ),
				'condition' => array(
					'show_count' => 'yes',
				),
			)
		);
			$this->add_control(
				'tag_between',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Between Spacing', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'rem' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .tag-link-count' => "margin-{$left}: {{SIZE}}{{UNIT}};",
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_tag_cloud' ) ) {
			include $template;
		}
	}
}
