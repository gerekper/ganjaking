<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Testimonials extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-testimonials', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-testimonials-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-testimonials' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_testimonials';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Testimonials', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-testimonial-carousel';
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
          'single-photo' 	=> __('Single Photo', 'mfn-opts'),
 				),
 				'default' => '',
 			]
 		);

 		$this->add_control(
 			'hide_photos',
 			[
 				'label' => __( 'Photos', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
 				'options'	=> array(
          0 => __('Show', 'mfn-opts'),
          1 => __('Hide', 'mfn-opts'),
 				),
 				'default' => '0',
 			]
 		);

 		$this->end_controls_section();

 	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_testimonials( $settings );

	}

}
