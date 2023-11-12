<?php
class PAFE_Column_Width extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-column-width';
	}

	public function pafe_register_controls( $element, $section_id, $args ) {
		static $sections = [
			'layout',
		];

		$element_name = $element->get_name();

		if ( ! in_array( $section_id, $sections ) ) {
			return;
		}

		if($element_name == 'column') {
			$element->add_responsive_control(
				'pafe_column_width',
				[
					'label' => __( 'PAFE Column Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'description' => 'E.g 100px, 20%, calc(100% - 100px)',
					'selectors' => [
						'{{WRAPPER}}' => 'width: {{VALUE}}',
					],
				]
			);
		}

	}

	protected function init_control() {
		add_action( 'elementor/element/before_section_end', [ $this, 'pafe_register_controls' ], 10, 3 );
	}

}
