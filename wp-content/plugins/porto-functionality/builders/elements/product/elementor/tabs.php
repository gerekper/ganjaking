<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Tabs Widget
 *
 * Porto Elementor widget to display product tabs on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

class Porto_Elementor_CP_Tabs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_tabs';
	}

	public function get_title() {
		return __( 'Product Tabs', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'tabs', 'single', 'store', 'description' );
	}

	public function get_icon() {
		return 'eicon-product-tabs';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {

		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'section_tab_direction',
			array(
				'label'     => __( 'Direction', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'style' => '',
				),
			)
		);
			$this->add_control(
				'is_flex',
				array(
					'type'      => Controls_Manager::SWITCHER,
					'label'     => __( 'Is Flex?', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .woocommerce-tabs'        => 'display: flex !important;',
						'.elementor-element-{{ID}} .resp-tabs-list'          => 'flex: 0 0 20%;overflow: hidden;',
						'.elementor-element-{{ID}} .resp-tabs-container'     => 'flex: 1;',
						'.elementor-element-{{ID}} .resp-tabs-list li'       => 'position: relative;clear:both;',
						'.elementor-element-{{ID}} .resp-tabs-list li:after' => 'content: "";position: absolute;width: 30vw;left: 0;bottom: -3px;border-bottom: 1px solid #dae2e6;',
					),
				)
			);
			$this->add_control(
				'tab_title_bottom_space',
				array(
					'label'     => __( 'Tab Bottom Spacing', 'porto-functionality' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => array(
						'.elementor-element-{{ID}} .resp-tabs-list li' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
					),
					'condition' => array(
						'is_flex!' => '',
					),
				)
			);
			$this->add_control(
				'tab_title_width',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Width', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'min' => 1,
							'max' => 80,
						),
						'%'  => array(
							'min' => 1,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .resp-tabs-list' => 'flex-basis: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'is_flex!' => '',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cp_tabs',
			array(
				'label' => __( 'Tabs', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'style',
				array(
					'type'        => Controls_Manager::SELECT,
					'label'       => __( 'Layout', 'porto-functionality' ),
					'description' => __( 'Controls the layout of tabs.', 'porto-functionality' ),
					'options'     => array(
						''         => __( 'Default', 'porto-functionality' ),
						'vertical' => __( 'Vertical', 'porto-functionality' ),
					),
				)
			);

			$this->start_controls_tabs( 'tabs_style' );

				$this->start_controls_tab(
					'normal_tabs_style',
					array(
						'label' => __( 'Normal', 'porto-functionality' ),
					)
				);

					$this->add_control(
						'tab_text_color',
						array(
							'label'     => __( 'Text Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} .resp-accordion' => 'color: {{VALUE}} !important',
							),
						)
					);

					$this->add_control(
						'tab_bg_color',
						array(
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'alpha'     => false,
							'selectors' => array(
								'.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} .woocommerce-tabs .resp-accordion' => 'background-color: {{VALUE}} !important',
							),
						)
					);

					$this->add_control(
						'tab_border_color',
						array(
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} h2.resp-accordion' => 'border-color: {{VALUE}} !important',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'active_tabs_style',
					array(
						'label' => __( 'Active', 'porto-functionality' ),
					)
				);

					$this->add_control(
						'active_tab_text_color',
						array(
							'label'     => __( 'Text Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .resp-tabs-list li.resp-tab-active, .elementor-element-{{ID}} .resp-accordion.resp-tab-active' => 'color: {{VALUE}} !important',
							),
						)
					);

					$this->add_control(
						'active_tab_bg_color',
						array(
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'alpha'     => false,
							'selectors' => array(
								'.elementor-element-{{ID}} .resp-tabs-list li.resp-tab-active, .elementor-element-{{ID}} .resp-accordion.resp-tab-active' => 'background-color: {{VALUE}} !important',
							),
						)
					);

					$this->add_control(
						'active_tab_border_color',
						array(
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .resp-tabs-list li.resp-tab-active, .elementor-element-{{ID}} .resp-tabs-list li:hover, .elementor-element-{{ID}} .resp-accordion.resp-tab-active, .elementor-element-{{ID}} h2.resp-accordion:hover' => 'border-color: {{VALUE}} !important',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'separator_tabs_style',
				array(
					'type' => Controls_Manager::DIVIDER,
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tab_typography',
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} .resp-accordion',
				)
			);

			$this->add_control(
				'tab_border_radius',
				array(
					'label'     => __( 'Border Radius', 'porto-functionality' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => array(
						'.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} .resp-accordion' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0 !important',
					),
				)
			);

			$this->add_control(
				'tab_title_border',
				array(
					'label'       => __( 'Border Width', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'selectors'   => array(
						'.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} h2.resp-accordion' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;border-style: solid !important;',
						'.elementor-element-{{ID}} .resp-tabs-list li:after' => 'bottom: calc(-1 * {{BOTTOM}}{{UNIT}} - 1px);',
					),
					'qa_selector' => '.resp-tabs-list li:nth-child(2), .resp-accordion:nth-of-type(2)',
				)
			);

			$this->add_control(
				'tab_title_padding',
				array(
					'label'       => __( 'Padding', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'selectors'   => array(
						'.elementor-element-{{ID}} .resp-tabs-list li, .elementor-element-{{ID}} .resp-accordion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
					),
					'qa_selector' => '.resp-tabs-list li:first-child, .resp-accordion:first-child',
				)
			);

			$this->add_control(
				'tab_title_space',
				array(
					'label'       => __( 'Tab Space', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'selectors'   => array(
						'.elementor-element-{{ID}} .resp-tabs-list li' => "margin-{$right}: {{SIZE}}{{UNIT}}",
					),
					'qa_selector' => '.resp-tabs-list li:nth-child(3)',
					'condition'   => array(
						'style' => '',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_panel_style',
			array(
				'label' => __( 'Panel', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'text_color',
				array(
					'label'     => __( 'Text Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .tab-content' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'content_bg_color',
				array(
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .tab-content' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'content_typography',
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .tab-content, .elementor-element-{{ID}} .tab-content p',
				)
			);

			$this->add_control(
				'heading_panel_heading_style',
				array(
					'type'      => Controls_Manager::HEADING,
					'label'     => __( 'Heading', 'porto-functionality' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'heading_color',
				array(
					'label'     => __( 'Text Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .tab-content h2' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'content_heading_typography',
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .tab-content h2',
				)
			);

			$this->add_control(
				'separator_panel_style',
				array(
					'type' => Controls_Manager::DIVIDER,
				)
			);

			$this->add_responsive_control(
				'panel_padding',
				array(
					'label'     => __( 'Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .woocommerce-tabs .tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

			$this->add_control(
				'panel_border_width',
				array(
					'label'       => __( 'Border Width', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'selectors'   => array(
						'.elementor-element-{{ID}} .tab-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid',
					),
					'qa_selector' => '.tab-content',
				)
			);

			$this->add_control(
				'panel_border_radius',
				array(
					'label'     => __( 'Border Radius', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .tab-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'panel_box_shadow',
					'selector' => '.elementor-element-{{ID}} .tab-content',
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_tabs( $settings );
		}
	}
}
