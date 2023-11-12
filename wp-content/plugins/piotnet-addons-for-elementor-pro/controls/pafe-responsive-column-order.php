<?php
class PAFE_Responsive_Column_Order extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-column-order';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_responsive_column_order_section',
			[
				'label' => __( 'PAFE Responsive Column Order', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$element->add_responsive_control(
			'pafe_responsive_column_order',
			[
				'label' => __( 'Order', 'piotnet-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}}' => '-webkit-order: {{VALUE}}; -ms-flex-order: {{VALUE}}; order: {{VALUE}};',
				],
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/column/section_typo/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	    add_action( 'elementor/element/container/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
