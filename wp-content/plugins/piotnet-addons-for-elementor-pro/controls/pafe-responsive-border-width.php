<?php
class PAFE_Responsive_Border_Width extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-border-width';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element_name = $element->get_name();
		
		if($element_name == 'column') {
			$element->add_responsive_control(
				'pafe_responsive_border_width',
				[
					'label' => __( 'PAFE Responsive Border Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} > .elementor-column-wrap,{{WRAPPER}} > .elementor-widget-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		} elseif ($element_name == 'section') {
			$element->add_responsive_control(
				'pafe_responsive_border_width',
				[
					'label' => __( 'PAFE Responsive Border Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		} else {
			$element->add_responsive_control(
				'pafe_responsive_border_width',
				[
					'label' => __( 'PAFE Responsive Border Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} > .elementor-widget-container' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_border/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_border/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_border/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_border/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
