<?php
/**
 * All Elementor widget init
 * @package Appside
 * @since 1.0.0
 */
if ( !defined('ABSPATH') ){
	exit(); // exit if access directly
}


if ( !class_exists('Appside_Elementor_Section_Extends') ){

	class Appside_Elementor_Section_Extends{

		/**
		* $instance
		* @since 2.0.0
		* */
		private static $instance;

		/**
		* construct()
		* @since 2.0.0
		* */
		public function __construct() {
			add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'section_tab_advanced_controls' ), 10, 2 );
			add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'section_tab_advanced_controls' ), 10, 2 );
		}

		/**
	   * getInstance()
	   * @since 2.0.0
	   * */
		public static function getInstance(){
			if ( null == self::$instance ){
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * section_tab_advanced_controls
		 * @since 2.0.0
		 * */

		public function section_tab_advanced_controls($instance, $args){
			$instance->start_controls_section(
				'appside_sec_extends_animation_section',
				[
					'label' => esc_html__( 'Aapside Animation', 'aapside-master' ),
					'tab'   => Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);

			$instance->add_control(
				'appside_sec_extends_is_scrollme',
				[
					'label'        => esc_html__( 'Scroll Animation', 'aapside-master' ),
					'description'  => esc_html__( 'Add animation to element when scrolling through page contents', 'aapside-master' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'aapside-master' ),
					'label_off'    => esc_html__( 'No', 'aapside-master' ),
					'return_value' => 'true',
					'default'      => 'false',
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_disable',
				[
					'label'       => esc_html__( 'Disable for', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SELECT,
					'default' => 'mobile',
					'options' => [
						'none' => __( 'None', 'aapside-master' ),
						'tablet' => __( 'Mobile and Tablet', 'aapside-master' ),
						'mobile' => __( 'Mobile', 'aapside-master' ),
					],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_smoothness',
				[
					'label' => __( 'Smoothness', 'aapside-master' ),
					'description' => __( 'factor that slowdown the animation, the more the smoothier', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 30,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 5,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_scalex',
				[
					'label' => __( 'Scale X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_scaley',
				[
					'label' => __( 'Scale Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_scalez',
				[
					'label' => __( 'Scale Z', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_rotatex',
				[
					'label' => __( 'Rotate X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_rotatey',
				[
					'label' => __( 'Rotate Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_rotatez',
				[
					'label' => __( 'Rotate Z', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_translatex',
				[
					'label' => __( 'Translate X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_translatey',
				[
					'label' => __( 'Translate Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_scrollme_translatez',
				[
					'label' => __( 'Translate Z', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_scrollme' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_is_smoove',
				[
					'label'        => esc_html__( 'Entrance Animation', 'aapside-master' ),
					'description'  => esc_html__( 'Add custom entrance animation to element', 'aapside-master' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'aapside-master' ),
					'label_off'    => esc_html__( 'No', 'aapside-master' ),
					'return_value' => 'true',
					'default'      => 'false',
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_disable',
				[
					'label'       => esc_html__( 'Disable for', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SELECT,
					'default' => 1,
					'options' => [
						1 => __( 'None', 'aapside-master' ),
						769 => __( 'Mobile and Tablet', 'aapside-master' ),
						415 => __( 'Mobile', 'aapside-master' ),
					],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_easing',
				[
					'label'       => esc_html__( 'Easing', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SELECT,
					'default' => '0.250, 0.250, 0.750, 0.750',
					'options' => [
						'0.250, 0.250, 0.750, 0.750' => __( 'linear', 'aapside-master' ),
						'0.250, 0.100, 0.250, 1.000' => __( 'ease', 'aapside-master' ),
						'0.420, 0.000, 1.000, 1.000' => __( 'ease-in', 'aapside-master' ),
						'0.000, 0.000, 0.580, 1.000' => __( 'ease-out', 'aapside-master' ),
						'0.420, 0.000, 0.580, 1.000' => __( 'ease-in-out', 'aapside-master' ),
						'0.550, 0.085, 0.680, 0.530' => __( 'easeInQuad', 'aapside-master' ),
						'0.550, 0.055, 0.675, 0.190' => __( 'easeInCubic', 'aapside-master' ),
						'0.895, 0.030, 0.685, 0.220' => __( 'easeInQuart', 'aapside-master' ),
						'0.755, 0.050, 0.855, 0.060' => __( 'easeInQuint', 'aapside-master' ),
						'0.470, 0.000, 0.745, 0.715' => __( 'easeInSine', 'aapside-master' ),
						'0.950, 0.050, 0.795, 0.035' => __( 'easeInExpo', 'aapside-master' ),
						'0.600, 0.040, 0.980, 0.335' => __( 'easeInCirc', 'aapside-master' ),
						'0.600, -0.280, 0.735, 0.045' => __( 'easeInBack', 'aapside-master' ),
						'0.250, 0.460, 0.450, 0.940' => __( 'easeOutQuad', 'aapside-master' ),
						'0.215, 0.610, 0.355, 1.000' => __( 'easeOutCubic', 'aapside-master' ),
						'0.165, 0.840, 0.440, 1.000' => __( 'easeOutQuart', 'aapside-master' ),
						'0.230, 1.000, 0.320, 1.000' => __( 'easeOutQuint', 'aapside-master' ),
						'0.390, 0.575, 0.565, 1.000' => __( 'easeOutSine', 'aapside-master' ),
						'0.190, 1.000, 0.220, 1.000' => __( 'easeOutExpo', 'aapside-master' ),
						'0.075, 0.820, 0.165, 1.000' => __( 'easeOutCirc', 'aapside-master' ),
						'0.175, 0.885, 0.320, 1.275' => __( 'easeOutBack', 'aapside-master' ),
						'0.455, 0.030, 0.515, 0.955' => __( 'easeInOutQuad', 'aapside-master' ),
						'0.645, 0.045, 0.355, 1.000' => __( 'easeInOutCubic', 'aapside-master' ),
						'0.770, 0.000, 0.175, 1.000' => __( 'easeInOutQuart', 'aapside-master' ),
						'0.860, 0.000, 0.070, 1.000' => __( 'easeInOutQuint', 'aapside-master' ),
						'0.445, 0.050, 0.550, 0.950' => __( 'easeInOutSine', 'aapside-master' ),
						'1.000, 0.000, 0.000, 1.000' => __( 'easeInOutExpo', 'aapside-master' ),
						'0.785, 0.135, 0.150, 0.860' => __( 'easeInOutCirc', 'aapside-master' ),
						'0.680, -0.550, 0.265, 1.550' => __( 'easeInOutBack', 'aapside-master' ),
					],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => false,
					'selectors' => [
						'.elementor-element.elementor-element-{{ID}}' => 'transition-timing-function: cubic-bezier({{VALUE}}) !important',
					],
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_delay',
				[
					'label' => __( 'Animation Delay (ms)', 'aapside-master' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 5000,
					'step' => 5,
					'default' => 0,
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => false,
					'selectors' => [
						'.elementor-element.elementor-element-{{ID}}' => 'transition-delay: {{VALUE}}ms !important',
					],
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_duration',
				[
					'label' => __( 'Animation Duration (ms)', 'aapside-master' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 5000,
					'step' => 5,
					'default' => 400,
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_opacity',
				[
					'label' => __( 'Opacity', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1,
							'step' => 0.1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => false,
					'selectors' => [
						'.elementor-widget.elementor-element-{{ID}}' => 'opacity: {{SIZE}}',
					],
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_scalex',
				[
					'label' => __( 'Scale X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_scaley',
				[
					'label' => __( 'Scale Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_rotatex',
				[
					'label' => __( 'Rotate X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_rotatey',
				[
					'label' => __( 'Rotate Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_rotatez',
				[
					'label' => __( 'Rotate Z', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_translatex',
				[
					'label' => __( 'Translate X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_translatey',
				[
					'label' => __( 'Translate Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_translatez',
				[
					'label' => __( 'Translate Z', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_skewx',
				[
					'label' => __( 'Skew X', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_skewy',
				[
					'label' => __( 'Skew Y', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 360,
							'step' => 1,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_smoove_perspective',
				[
					'label' => __( 'Perspective', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 1000,
					],
					'range' => [
						'px' => [
							'min' => 5,
							'max' => 4000,
							'step' => 5,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_smoove' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_is_parallax_mouse',
				[
					'label'        => esc_html__( 'Mouse Parallax', 'aapside-master' ),
					'description'  => esc_html__( 'Add parallax to element when moving mouse position', 'aapside-master' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'aapside-master' ),
					'label_off'    => esc_html__( 'No', 'aapside-master' ),
					'return_value' => 'true',
					'default'      => 'false',
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_is_parallax_mouse_depth',
				[
					'label' => __( 'Depth', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.2,
					],
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.05,
						]
					],
					'size_units' => [ 'px' ],
					'condition' => [
						'appside_sec_extends_is_parallax_mouse' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_is_infinite',
				[
					'label'        => esc_html__( 'Infinite Animation', 'aapside-master' ),
					'description'  => esc_html__( 'Add custom infinite animation to element', 'aapside-master' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'aapside-master' ),
					'label_off'    => esc_html__( 'No', 'aapside-master' ),
					'return_value' => 'true',
					'default'      => 'false',
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_infinite_animation',
				[
					'label'       => esc_html__( 'Easing', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SELECT,
					'default' => 'if_bounce',
					'options' => [
						'if_swing2' => __( 'Swing', 'aapside-master' ),
						'if_wave' 	=> __( 'Wave', 'aapside-master' ),
						'if_tilt' 	=> __( 'Tilt', 'aapside-master' ),
						'if_bounce' => __( 'Bounce', 'aapside-master' ),
						'if_scale' 	=> __( 'Scale', 'aapside-master' ),
						'if_spin' 	=> __( 'Spin', 'aapside-master' ),
					],
					'condition' => [
						'appside_sec_extends_is_infinite' => 'true',
					],
					'frontend_available' => true,
				]
			);

			$instance->add_control(
				'appside_sec_extends_infinite_easing',
				[
					'label'       => esc_html__( 'Easing', 'aapside-master' ),
					'type' => Elementor\Controls_Manager::SELECT,
					'default' => '0.250, 0.250, 0.750, 0.750',
					'options' => [
						'0.250, 0.250, 0.750, 0.750' => __( 'linear', 'aapside-master' ),
						'0.250, 0.100, 0.250, 1.000' => __( 'ease', 'aapside-master' ),
						'0.420, 0.000, 1.000, 1.000' => __( 'ease-in', 'aapside-master' ),
						'0.000, 0.000, 0.580, 1.000' => __( 'ease-out', 'aapside-master' ),
						'0.420, 0.000, 0.580, 1.000' => __( 'ease-in-out', 'aapside-master' ),
						'0.550, 0.085, 0.680, 0.530' => __( 'easeInQuad', 'aapside-master' ),
						'0.550, 0.055, 0.675, 0.190' => __( 'easeInCubic', 'aapside-master' ),
						'0.895, 0.030, 0.685, 0.220' => __( 'easeInQuart', 'aapside-master' ),
						'0.755, 0.050, 0.855, 0.060' => __( 'easeInQuint', 'aapside-master' ),
						'0.470, 0.000, 0.745, 0.715' => __( 'easeInSine', 'aapside-master' ),
						'0.950, 0.050, 0.795, 0.035' => __( 'easeInExpo', 'aapside-master' ),
						'0.600, 0.040, 0.980, 0.335' => __( 'easeInCirc', 'aapside-master' ),
						'0.600, -0.280, 0.735, 0.045' => __( 'easeInBack', 'aapside-master' ),
						'0.250, 0.460, 0.450, 0.940' => __( 'easeOutQuad', 'aapside-master' ),
						'0.215, 0.610, 0.355, 1.000' => __( 'easeOutCubic', 'aapside-master' ),
						'0.165, 0.840, 0.440, 1.000' => __( 'easeOutQuart', 'aapside-master' ),
						'0.230, 1.000, 0.320, 1.000' => __( 'easeOutQuint', 'aapside-master' ),
						'0.390, 0.575, 0.565, 1.000' => __( 'easeOutSine', 'aapside-master' ),
						'0.190, 1.000, 0.220, 1.000' => __( 'easeOutExpo', 'aapside-master' ),
						'0.075, 0.820, 0.165, 1.000' => __( 'easeOutCirc', 'aapside-master' ),
						'0.175, 0.885, 0.320, 1.275' => __( 'easeOutBack', 'aapside-master' ),
						'0.455, 0.030, 0.515, 0.955' => __( 'easeInOutQuad', 'aapside-master' ),
						'0.645, 0.045, 0.355, 1.000' => __( 'easeInOutCubic', 'aapside-master' ),
						'0.770, 0.000, 0.175, 1.000' => __( 'easeInOutQuart', 'aapside-master' ),
						'0.860, 0.000, 0.070, 1.000' => __( 'easeInOutQuint', 'aapside-master' ),
						'0.445, 0.050, 0.550, 0.950' => __( 'easeInOutSine', 'aapside-master' ),
						'1.000, 0.000, 0.000, 1.000' => __( 'easeInOutExpo', 'aapside-master' ),
						'0.785, 0.135, 0.150, 0.860' => __( 'easeInOutCirc', 'aapside-master' ),
						'0.680, -0.550, 0.265, 1.550' => __( 'easeInOutBack', 'aapside-master' ),
					],
					'condition' => [
						'appside_sec_extends_is_infinite' => 'true',
					],
					'frontend_available' => true
				]
			);

			$instance->add_control(
				'appside_sec_extends_infinite_duration',
				[
					'label' => __( 'Animation Duration (s)', 'aapside-master' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 15,
					'step' => 1,
					'default' => 4,
					'condition' => [
						'appside_sec_extends_is_infinite' => 'true',
					],
					'frontend_available' => true
				]
			);

			$instance->end_controls_section();
		}

	}

	if ( class_exists('Appside_Elementor_Section_Extends') ){
		Appside_Elementor_Section_Extends::getInstance();
	}

}//end if
