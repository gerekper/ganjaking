<?php
class PAFE_Responsive_Hide_Column extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-hide-column';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_responsive_hide_column_section',
			[
				'label' => __( 'PAFE Responsive Hide Column', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$element->add_control(
			'pafe_responsive_hide_column_desktop',
			[
				'label' => __( 'Hide On Desktop', 'elementor' ),
				'type' => Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => __( 'Hide', 'elementor' ),
				'label_off' => __( 'Show', 'elementor' ),
				'return_value' => 'hidden-desktop',
			]
		);

		$element->add_control(
			'pafe_responsive_hide_column_tablet',
			[
				'label' => __( 'Hide On Tablet', 'elementor' ),
				'type' => Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => __( 'Hide', 'elementor' ),
				'label_off' => __( 'Show', 'elementor' ),
				'return_value' => 'hidden-tablet',
			]
		);

		$element->add_control(
			'pafe_responsive_hide_column_mobile',
			[
				'label' => __( 'Hide On Mobile', 'elementor' ),
				'type' => Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => __( 'Hide', 'elementor' ),
				'label_off' => __( 'Show', 'elementor' ),
				'return_value' => 'hidden-phone',
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/column/section_typo/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
