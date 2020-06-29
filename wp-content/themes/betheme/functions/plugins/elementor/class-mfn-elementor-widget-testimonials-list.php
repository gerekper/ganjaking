<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Testimonials_List extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_testimonials_list';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Testimonials list', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-testimonial';
	}

	/**
	 * Get widget categories
	 */

	public function get_categories() {
		return [ 'mfn_builder' ];
	}

	/**
	 * Register widget controls
	 */

   protected function _register_controls() {

 		$this->start_controls_section(
 			'options_section',
 			[
 				'label' => __( 'Options', 'mfn-opts' ),
 			]
 		);

 		$this->add_control(
 			'category',
 			[
 				'label' => __( 'Category', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
 				'options'	=> mfn_get_categories('testimonial-types'),
 				'default' => "",
 			]
 		);

 		$this->add_control(
 			'orderby',
 			[
 				'label' => __( 'Order by', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
 				'options' 	=> array(
 					'date' => __('Date', 'mfn-opts'),
 					'title' => __('Title', 'mfn-opts'),
 					'rand' => __('Random', 'mfn-opts'),
 				),
 				'default' => "date",
 			]
 		);

 		$this->add_control(
 			'order',
 			[
 				'label' => __( 'Order', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
 				'options'	=> array(
 					'ASC' 	=> __('Ascending', 'mfn-opts'),
 					'DESC' 	=> __('Descending', 'mfn-opts'),
 				),
 				'default' => "DESC",
 			]
 		);

 		$this->end_controls_section();

 		$this->start_controls_section(
 			'advanced_section',
 			[
 				'label' => __( 'Advanced', 'mfn-opts' ),
 			]
 		);

 		$this->add_control(
 			'style',
 			[
 				'label' => __( 'Style', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
 				'options'	=> array(
          '' => __('Default', 'mfn-opts'),
          'quote' => __('Quote above the author', 'mfn-opts'),
 				),
 				'default' => '',
 			]
 		);

 		$this->end_controls_section();

 	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_testimonials_list( $settings );

	}

}
