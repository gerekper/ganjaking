<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Events Widget
 *
 * Porto Elementor widget to display events.
 *
 * @since 5.4.2
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Events_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_events';
	}

	public function get_title() {
		return __( 'Porto Events', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'event', 'posts' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$order_by_values  = array_slice( porto_vc_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );

		$this->start_controls_section(
			'section_events',
			array(
				'label' => __( 'Events Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'event_type',
			array(
				'label'   => __( 'Event Type', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''         => __( 'Default', 'porto-functionality' ),
					'next'     => __( 'Next', 'porto-functionality' ),
					'upcoming' => __( 'Upcoming', 'porto-functionality' ),
					'past'     => __( 'Past', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'event_numbers',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Number of Events', 'porto-functionality' ),
				'condition' => array(
					'event_type' => array( 'upcoming', 'past', 'next' ),
				),
				'default'   => 1,
			)
		);

		$this->add_control(
			'event_skip',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Skip Number of Events', 'porto-functionality' ),
				'condition' => array(
					'event_type' => array( 'upcoming' ),
				),
			)
		);

		$this->add_control(
			'event_column',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Numbers of Columns', 'porto-functionality' ),
				'min'       => 1,
				'max'       => 2,
				'step'      => 1,
				'condition' => array(
					'event_type' => array( 'upcoming', 'past', 'next' ),
				),
			)
		);

		$this->add_control(
			'event_countdown',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Display Countdown', 'porto-functionality' ),
				'options'   => array(
					'show' => __( 'Yes', 'porto-functionality' ),
					'hide' => __( 'No', 'porto-functionality' ),
				),
				'default'   => 'show',
				'condition' => array(
					'event_type' => array( 'next' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_events' ) ) {
			include $template;
		}
	}

	protected function content_template() {}
}
