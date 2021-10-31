<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 *
 * Register elementor custom addons for elements and widgets.
 *
 * @since 6.2.0
 */

use Elementor\Controls_Manager;

/* Mouse Parallax Options */
if ( ! function_exists( 'porto_elementor_mpx_controls' ) ) :
	function porto_elementor_mpx_controls( $self ) {
		$self->start_controls_section(
			'_porto_section_floating_effect',
			array(
				'label' => __( 'Floating Effects', 'porto-functionality' ),
				'tab'   => Porto_Elementor_Editor_Custom_Tabs::TAB_CUSTOM,
			)
		);
			$self->add_control(
				'mouse_parallax',
				array(
					'label'       => esc_html__( 'Mouse Parallax?', 'porto-functionality' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'Animate your elements chasing your mouse move.', 'porto-functionality' ),
				)
			);

			$self->add_control(
				'mouse_parallax_inverse',
				array(
					'label'       => esc_html__( 'Mouse Parallax Inverse?', 'porto-functionality' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'Animate your elements inversely chasing your mouse move.', 'porto-functionality' ),
					'condition'   => array(
						'mouse_parallax' => 'yes',
					),
				)
			);

			$self->add_control(
				'mouse_parallax_speed',
				array(
					'label'       => esc_html__( 'Mouse Parallax Speed', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => esc_html__( 'Control your elements mouse chasing speed.', 'porto-functionality' ),
					'default'     => array(
						'size' => '0.5',
					),
					'range'       => array(
						'' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'condition'   => array(
						'mouse_parallax' => 'yes',
					),
				)
			);

		$self->end_controls_section();
	}
endif;
