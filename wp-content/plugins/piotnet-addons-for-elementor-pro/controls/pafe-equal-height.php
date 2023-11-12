<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Equal_Height extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-equal-height';
	}

	public function pafe_register_controls( $element, $args ) {

		$description = '';

		$element->start_controls_section(
			'pafe_equal_height_section',
			[
				'label' => __( 'PAFE Equal Height', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_equal_height_enable',
			[
				'label' => __( 'Enable Equal Height', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => 'This feature only works on the frontend.',
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_equal_height_widget_container_enable',
			[
				'label' => __( 'Enable Equal Height Widget Container', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_equal_height_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_equal_height_enable_desktop',
			[
				'label' => __( 'Desktop', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_equal_height_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_equal_height_enable_tablet',
			[
				'label' => __( 'Tablet', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_equal_height_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_equal_height_enable_mobile',
			[
				'label' => __( 'Mobile', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_equal_height_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_equal_height_slug',
			[
				'label' => __( 'Slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Set the same slug for the equal height element. E.g 1,2,3 or widget-1',
				'condition' => [
					'pafe_equal_height_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_equal_height_enable'] ) ) {

			$class = '';

			if ( ! empty( $settings['pafe_equal_height_enable_desktop'] ) ) {
				$class .= ' pafe-equal-height-desktop';
			}

			if ( ! empty( $settings['pafe_equal_height_enable_tablet'] ) ) {
				$class .= ' pafe-equal-height-tablet';
			}

			if ( ! empty( $settings['pafe_equal_height_enable_mobile'] ) ) {
				$class .= ' pafe-equal-height-mobile';
			}

			$element->add_render_attribute( '_wrapper', [
				'class' => $class,
				'data-pafe-equal-height' => $settings['pafe_equal_height_slug'],
				'data-pafe-equal-height-widget-container' => $settings['pafe_equal_height_widget_container_enable'],
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
