<?php

namespace ElementPack\Modules\FloatingEffects;

use Elementor\Controls_Manager;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-floating-effects';
	}

	public function register_widget_control($widget, $args) {

		// start floating effect
		$widget->add_control(
			'ep_floating_effects_show',
			[
				'label'              => BDTEP_CP . esc_html__('Floating Effects', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'return_value'       => 'yes',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_translate_toggle',
			[
				'label'              => esc_html__('Translate', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'return_value'       => 'yes',
				//'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_floating_effects_translate_x',
			[
				'label'              => esc_html__('Translate X', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 0,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'             => 'yes',
					'ep_floating_effects_translate_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);



		$widget->add_control(
			'ep_floating_effects_translate_y',
			[
				'label'              => esc_html__('Translate Y', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 30,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'             => 'yes',
					'ep_floating_effects_translate_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_translate_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'          => [
					'ep_floating_effects_show'             => 'yes',
					'ep_floating_effects_translate_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_translate_delay',
			[
				'label'              => esc_html__('Delay', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'condition'          => [
					'ep_floating_effects_show'             => 'yes',
					'ep_floating_effects_translate_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'ep_floating_effects_rotate_toggle',
			[
				'label'              => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'return_value'       => 'yes',
				//'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_floating_effects_rotate_x',
			[
				'label'              => esc_html__('Rotate X', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 0,
					],
					'unit'  => 'deg',
				],
				'range'              => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'            => 'yes',
					'ep_floating_effects_rotate_toggle'   => 'yes',
					'ep_floating_effects_rotate_infinite' => ''
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_rotate_y',
			[
				'label'              => esc_html__('Rotate Y', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 0,
					],
					'unit'  => 'deg',
				],
				'range'              => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'            => 'yes',
					'ep_floating_effects_rotate_toggle'   => 'yes',
					'ep_floating_effects_rotate_infinite' => ''
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_rotate_z',
			[
				'label'              => esc_html__('Rotate Z', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 45,
					],
					'unit'  => 'deg',
				],
				'range'              => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'            => 'yes',
					'ep_floating_effects_rotate_toggle'   => 'yes',
					'ep_floating_effects_rotate_infinite' => ''
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_rotate_infinite',
			[
				'label'        => esc_html__('Rotate Infinite', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'condition'    => [
					'ep_floating_effects_show'          => 'yes',
					'ep_floating_effects_rotate_toggle' => 'yes',
				],
				//'render_type'        => 'none',
				'frontend_available' => true,
				'prefix_class' => 'bdt-floating-effect-infinite--'

			]
		);

		$widget->add_control(
			'ep_floating_effects_rotate_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 50000,
						'step' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 2000,
				],
				'condition'          => [
					'ep_floating_effects_show'          => 'yes',
					'ep_floating_effects_rotate_toggle' => 'yes',
				],
				//					'render_type'        => 'none',
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}}' => '--bdt-floating-effect-rotate-duration: {{SIZE}}ms;',
				],
			]
		);

		$widget->add_control(
			'ep_floating_effects_rotate_delay',
			[
				'label'              => esc_html__('Delay', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'ep_floating_effects_show'          => 'yes',
					'ep_floating_effects_rotate_toggle' => 'yes',
				],
				//'render_type'        => 'none',
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}}' => '--bdt-floating-effect-rotate-delay: {{SIZE}}ms;',
				],
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'ep_floating_effects_scale_toggle',
			[
				'label'              => esc_html__('Scale', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'return_value'       => 'yes',
				//'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_floating_effects_scale_x',
			[
				'label'              => esc_html__('Scale X', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.5,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => .1
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'         => 'yes',
					'ep_floating_effects_scale_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_scale_y',
			[
				'label'              => esc_html__('Scale Y', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.5,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => .1
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'         => 'yes',
					'ep_floating_effects_scale_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_scale_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'          => [
					'ep_floating_effects_show'         => 'yes',
					'ep_floating_effects_scale_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_scale_delay',
			[
				'label'              => esc_html__('Delay', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'ep_floating_effects_show'         => 'yes',
					'ep_floating_effects_scale_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'ep_floating_effects_skew_toggle',
			[
				'label'              => esc_html__('Skew', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'return_value'       => 'yes',
				//'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_floating_effects_skew_x',
			[
				'label'              => esc_html__('Skew X', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.5,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'        => 'yes',
					'ep_floating_effects_skew_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_skew_y',
			[
				'label'              => esc_html__('Skew Y', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.5,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'        => 'yes',
					'ep_floating_effects_skew_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_skew_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'          => [
					'ep_floating_effects_show'        => 'yes',
					'ep_floating_effects_skew_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_skew_delay',
			[
				'label'              => esc_html__('Delay', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'ep_floating_effects_show'        => 'yes',
					'ep_floating_effects_skew_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'ep_floating_effects_border_radius_toggle',
			[
				'label'              => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'return_value'       => 'yes',
				//'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_floating_effects_border_radius',
			[
				'label'              => esc_html__('Start and End', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 50,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 5
					]
				],
				'labels'             => [
					esc_html__('From', 'bdthemes-element-pack'),
					esc_html__('To', 'bdthemes-element-pack'),
				],
				//'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'ep_floating_effects_show'                 => 'yes',
					'ep_floating_effects_border_radius_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		//			$widget->add_control(
		//				'ep_floating_effects_border_radius_reverse',
		//				[
		//					'label'              => esc_html__( 'Reverse', 'bdthemes-element-pack' ),
		//					'type'               => Controls_Manager::SWITCHER,
		//					'return_value'       => 'yes',
		//					'frontend_available' => true,
		//				]
		//			);


		$widget->add_control(
			'ep_floating_effects_border_radius_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'          => [
					'ep_floating_effects_show'                 => 'yes',
					'ep_floating_effects_border_radius_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_border_radius_delay',
			[
				'label'              => esc_html__('Delay', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'ep_floating_effects_show'                 => 'yes',
					'ep_floating_effects_border_radius_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'ep_floating_effects_opacity_toggle',
			[
				'label'              => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'return_value'       => 'yes',
				// 'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_floating_effects_opacity_start',
			[
				'label'              => esc_html__('Start', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 1,
				],
				'condition'          => [
					'ep_floating_effects_show'                 => 'yes',
					'ep_floating_effects_opacity_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_opacity_end',
			[
				'label'              => esc_html__('End', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'          => [
					'ep_floating_effects_show'                 => 'yes',
					'ep_floating_effects_opacity_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$widget->add_control(
			'ep_floating_effects_opacity_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'          => [
					'ep_floating_effects_show'                 => 'yes',
					'ep_floating_effects_opacity_toggle' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$widget->end_popover();

		$widget->add_control(
			'ep_floating_effects_easing',
			[
				'label'              => esc_html__('Easing', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'easeInOutQuad'   => esc_html__('Ease In Out Quad', 'bdtheme-element-pack'),
					'easeInOutCubic'  => esc_html__('Ease In Out Cubic', 'bdtheme-element-pack'),
					'easeInOutQuart'  => esc_html__('Ease In Out Quart', 'bdtheme-element-pack'),
					'easeInOutQuint'  => esc_html__('Ease In Out Quint', 'bdtheme-element-pack'),
					'easeInOutSine'   => esc_html__('Ease In Out Sine', 'bdtheme-element-pack'),
					'easeInOutExpo'   => esc_html__('Ease In Out Expo', 'bdtheme-element-pack'),
					'easeInOutCirc'   => esc_html__('Ease In Out Circ', 'bdtheme-element-pack'),
					'easeInOutBack'   => esc_html__('Ease In Out Back', 'bdtheme-element-pack'),
					'easeInOutBounce' => esc_html__('Ease In Out Bounce', 'bdtheme-element-pack'),
				],
				'default'            => 'easeInOutQuad',
				'condition'          => [
					'ep_floating_effects_show' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_floating_effects_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
	}

	// public function widget_floating_effects_before_render( $widget ) {
	// 	$settings = $widget->get_settings_for_display();
	// 	if ( $settings['ep_floating_effects_show'] == 'yes' ) {
	// 		wp_enqueue_script( 'anime' );
	// 	}
	// }

	public function enqueue_scripts() {
		/**
		 * Please use only the min file of anime js.
		 * Suffix will give error
		 * BDTU-011
		 */
		wp_enqueue_script('anime', BDTEP_ASSETS_URL . 'vendor/js/anime.min.js', [], '3.2.1', true);
		wp_enqueue_script('ep-floating-effects');
	}
	public function should_script_enqueue($widget) {
		if ('yes' === $widget->get_settings_for_display('ep_floating_effects_show')) {
			$this->enqueue_scripts();
		}
	}

	protected function add_actions() {

		add_action('elementor/element/section/section_effects/after_section_start', [$this, 'register_widget_control'], 10, 11);
		add_action('elementor/element/column/section_effects/after_section_start', [$this, 'register_widget_control'], 10, 11);
		add_action('elementor/element/common/section_effects/after_section_start', [$this, 'register_widget_control'], 10, 11);
		//render scripts
		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue'], 10, 1);
		add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
	}
}
