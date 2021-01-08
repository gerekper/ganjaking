<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Image Widget
 *
 * Porto Elementor widget to display images section on the single product page when using custom product layout
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Image_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_image';
	}

	public function get_title() {
		return __( 'Product Image', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'image', 'media', 'thumbnail' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_cp_image',
			array(
				'label' => __( 'Product Image', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'style',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Style', 'porto-functionality' ),
				'options' => array(
					''                       => __( 'Default', 'porto-functionality' ),
					'extended'               => __( 'Extended', 'porto-functionality' ),
					'grid'                   => __( 'Grid Images', 'porto-functionality' ),
					'full_width'             => __( 'Thumbs on Image', 'porto-functionality' ),
					'sticky_info'            => __( 'List Images', 'porto-functionality' ),
					'transparent'            => __( 'Left Thumbs 1', 'porto-functionality' ),
					'centered_vertical_zoom' => __( 'Left Thumbs 2', 'porto-functionality' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_image( $settings );
		}
	}
}
