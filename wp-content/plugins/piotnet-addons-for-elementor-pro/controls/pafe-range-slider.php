<?php

class PAFE_Range_Slider extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-range-slider';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_range_slider_section',
			[
				'label' => __( 'PAFE Range Slider', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_range_slider_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_range_slider_field_custom_id',
			[
				'label' => __( 'Text Field Custom ID', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g price', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_range_slider_field_options',
			[
				'label' => __( 'Range Slider Options', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'skin: "round", type: "double", grid: true, min: 0, max: 1000, from: 200, to: 800, prefix: "$"',
				'description' => 'Demo: <a href="http://ionden.com/a/plugins/ion.rangeSlider/demo.html" target="_blank">http://ionden.com/a/plugins/ion.rangeSlider/demo.html</a>',
			]
		);

		$element->add_control(
			'pafe_range_slider_field_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
			)
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_range_slider_enable'])) {
			if ( array_key_exists( 'pafe_range_slider_field_list',$settings ) ) {
				$list = $settings['pafe_range_slider_field_list'];	
				if( !empty($list[0]['pafe_range_slider_field_custom_id']) && !empty($list[0]['pafe_range_slider_field_options']) ) {

					$element->add_render_attribute( '_wrapper', [
						'data-pafe-range-slider' => json_encode($list),
					] );
				}
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_form_fields/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
