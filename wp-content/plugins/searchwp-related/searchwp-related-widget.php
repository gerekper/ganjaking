<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Searchwp_Related_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'searchwp_related_widget',
			'description' => 'SearchWP Related',
		);
		parent::__construct( 'searchwp_related_widget', 'SearchWP Related', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( class_exists( 'SearchWP_Related' ) ) {
			$related = new SearchWP_Related\Template();
			echo $related->get_template();
			?>
			<style>
				.searchwp-related > ol {
					display: block;
					margin: 0;
					padding: 0;
				}

				.searchwp-related > ol > li {
					margin: 0.5em 0 1em;
					padding: 0;
				}
			</style><?php
		} else {
			echo esc_html__( 'Error: SearchWP Related is not active!', 'searchwp-related' );
		}
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Searchwp_Related_Widget' );
});
