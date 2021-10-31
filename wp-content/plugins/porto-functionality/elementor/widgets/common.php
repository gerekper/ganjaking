<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Common Widget
 *
 * Porto Elementor widget to give effects to all widgets.
 *
 * @since 6.2.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Common_Widget extends \Elementor\Widget_Common {
	public function __construct( array $data = [], array $args = null ) {
		parent::__construct( $data, $args );

		add_action( 'elementor/frontend/widget/before_render', array( $this, 'widget_before_render' ) );
	}

	protected function register_controls() {
		parent::register_controls();
		// Mouse Parallax
		porto_elementor_mpx_controls( $this );
	}

	public function widget_before_render( $widget ) {
		$atts = $widget->get_settings_for_display();

		$widget->add_render_attribute(
			'_wrapper',
			porto_get_mpx_options( $atts )
		);
	}
}
