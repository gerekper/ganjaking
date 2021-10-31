<?php
// Porto Events
add_action( 'vc_after_init', 'porto_load_events_shortcode' );

function porto_load_events_shortcode() {
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Events', 'porto-functionality' ),
			'base'        => 'porto_events',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show events by beautiful layouts as portfolio', 'porto-functionality' ),
			'icon'        => 'porto-sc Simple-Line-Icons-event',
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Event Type', 'porto-functionality' ),
					'param_name' => 'event_type',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Next', 'porto-functionality' ) => 'next',
						__( 'Upcoming', 'porto-functionality' ) => 'upcoming',
						__( 'Past', 'porto-functionality' ) => 'past',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Number of Events', 'porto-functionality' ),
					'param_name' => 'event_numbers',
					'dependency' => array(
						'element' => 'event_type',
						'value'   => array( 'upcoming', 'past', 'next' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Skip Number of Events', 'porto-functionality' ),
					'param_name' => 'event_skip',
					'dependency' => array(
						'element' => 'event_type',
						'value'   => array( 'upcoming' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Numbers of Columns', 'porto-functionality' ),
					'param_name' => 'event_column',
					'value'      => array(
						__( '1', 'porto-functionality' ) => '1',
						__( '2', 'porto-functionality' ) => '2',
					),
					'dependency' => array(
						'element' => 'event_type',
						'value'   => array( 'upcoming', 'past', 'next' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Display Countdown', 'porto-functionality' ),
					'param_name' => 'event_countdown',
					'value'      => array(
						__( 'YES', 'porto-functionality' ) => 'show',
						__( 'NO', 'porto-functionality' )  => 'hide',
					),
					'dependency' => array(
						'element' => 'event_type',
						'value'   => array( 'next' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra class name', 'porto-functionality' ),
					'param_name'  => 'el_class',
					'description' => 'Style particular content element differently - add a class name and refer to it in custom CSS.',
				),
			),

		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Events' ) ) {
		class WPBakeryShortCode_Porto_Events extends WPBakeryShortCode {
		}
	}
}
