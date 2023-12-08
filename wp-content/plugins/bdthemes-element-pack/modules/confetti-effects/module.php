<?php

namespace ElementPack\Modules\ConfettiEffects;

use Elementor\Controls_Manager;
use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-confetti-effects';
	}

	public function register_section( $element ) {
		$element->start_controls_section(
			'section_element_pack_confetti_controls',
			[ 
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => BDTEP_CP . esc_html__( 'Confetti Effects', 'bdthemes-element-pack' ),
			]
		);
		$element->end_controls_section();
	}


	public function register_controls( $widget, $args ) {

		$widget->add_control(
			'ep_widget_cf_confetti',
			[ 
				'label'              => esc_html__( 'Use Confetti Effects?', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'default'            => '',
				'return_value'       => 'yes',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_widget_cf_type',
			[ 
				'label'              => esc_html__( 'Confetti Type', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [ 
					'basic'        => esc_html__( 'Basic', 'bdthemes-element-pack' ),
					'random'       => esc_html__( 'Random Direction', 'bdthemes-element-pack' ),
					'fireworks'    => esc_html__( 'Fireworks', 'bdthemes-element-pack' ),
					'snow'         => esc_html__( 'Snow', 'bdthemes-element-pack' ),
					'school-pride' => esc_html__( 'School Pride', 'bdthemes-element-pack' ),
				],
				'default'            => 'basic',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_fireworks_duration',
			[ 
				'label'              => esc_html__( 'Animation End Time (ms)', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min' => 1000,
						'max' => 10000,
					],
				],
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_type'     => [ 'fireworks', 'snow', 'school-pride' ],
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_anim_infinite',
			[ 
				'label'              => esc_html__( 'Infinite End Time', 'bdthemes-element-pack' ) . BDTEP_NC,
				'description'        => esc_html__( 'The result will be shown in Preview.', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_type'     => [ 'snow' ],
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_particle_count',
			[ 
				'label'              => esc_html__( 'Particle Count', 'bdthemes-element-pack' ),
				'description'        => esc_html__( 'The number of confetti to launch. More is always fun... but be cool, there\'s a lot of math involved. (default: 50)', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min' => 1,
						'max' => 1000,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_start_velocity',
			[ 
				'label'              => esc_html__( 'Start Velocity', 'bdthemes-element-pack' ),
				'description'        => esc_html__( 'How fast the confetti will start going, in pixels. (default: 45)', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min' => 10,
						'max' => 100,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_spread',
			[ 
				'label'              => esc_html__( 'Spread', 'bdthemes-element-pack' ),
				'description'        => esc_html__( 'How far off center the confetti can go, in degrees. 45 means the confetti will launch at the defined angle plus or minus 22.5 degrees.', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min' => 0,
						'max' => 360,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_angle',
			[ 
				'label'              => esc_html__( 'Angle', 'bdthemes-element-pack' ),
				'description'        => esc_html__( 'The angle in which to launch the confetti, in degrees. 90 is straight up', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min' => 0,
						'max' => 360,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_type'     => [ 'random', 'school-pride' ],
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_colors',
			[ 
				'label'              => esc_html__( 'Colors', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::TEXTAREA,
				'description'        => 'Input your colors. example: red, #bada55, #ffffff (Colors must be not empty.)',
				'default'            => '#D30C5C, #0EBCDC, #EAED41, #ED5A78, #DF33DF',
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

        $widget->add_control(
			'ep_widget_cf_shape_type',
			[ 
				'label'              => esc_html__( 'Shape Type', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'               => Controls_Manager::SELECT,
				'options'            => [ 
					'basic'         => esc_html__( 'Basic', 'bdthemes-element-pack' ),
					'emoji'         => esc_html__( 'Emoji', 'bdthemes-element-pack' ),
					'svg'           => esc_html__( 'SVG Path', 'bdthemes-element-pack' ),
				],
				'default'            => 'basic',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_shapes',
			[ 
				'label'              => esc_html__( 'Shapes', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::TEXTAREA,
				'description'        => esc_html__( 'The possible values are square and circle. The default is to use both shapes in an even mix. You can even change the mix by providing a value such as (circle, circle, square) to use two third circles and one third squares.', 'bdthemes-element-pack' ),
				'default'			 => 'square|circle',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_shape_type' => 'basic',
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_shapes_emoji',
			[ 
				'label'              => esc_html__( 'Emoji', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::TEXTAREA,
				'description'        => esc_html__( 'Place Emoji.', 'bdthemes-element-pack' ),
				'default'			 => '🎃|🎄|💜',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_shape_type' => 'emoji',
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_shapes_svg',
			[ 
				'label'              => esc_html__( 'SVG Path', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::TEXTAREA,
				'description'        => esc_html__( 'Place SVG paths.', 'bdthemes-element-pack' ),
				'default'			 => 'M167 72c19,-38 37,-56 75,-56 42,0 76,33 76,75 0,76 -76,151 -151,227 -76,-76 -151,-151 -151,-227 0,-42 33,-75 75,-75 38,0 57,18 76,56z|M120 240c-41,14 -91,18 -120,1 29,-10 57,-22 81,-40 -18,2 -37,3 -55,-3 25,-14 48,-30 66,-51 -11,5 -26,8 -45,7 20,-14 40,-30 57,-49 -13,1 -26,2 -38,-1 18,-11 35,-25 51,-43 -13,3 -24,5 -35,6 21,-19 40,-41 53,-67 14,26 32,48 54,67 -11,-1 -23,-3 -35,-6 15,18 32,32 51,43 -13,3 -26,2 -38,1 17,19 36,35 56,49 -19,1 -33,-2 -45,-7 19,21 42,37 67,51 -19,6 -37,5 -56,3 25,18 53,30 82,40 -30,17 -79,13 -120,-1l0 41 -31 0 0 -41z|M449.4 142c-5 0-10 .3-15 1a183 183 0 0 0-66.9-19.1V87.5a17.5 17.5 0 1 0-35 0v36.4a183 183 0 0 0-67 19c-4.9-.6-9.9-1-14.8-1C170.3 142 105 219.6 105 315s65.3 173 145.7 173c5 0 10-.3 14.8-1a184.7 184.7 0 0 0 169 0c4.9.7 9.9 1 14.9 1 80.3 0 145.6-77.6 145.6-173s-65.3-173-145.7-173zm-220 138 27.4-40.4a11.6 11.6 0 0 1 16.4-2.7l54.7 40.3a11.3 11.3 0 0 1-7 20.3H239a11.3 11.3 0 0 1-9.6-17.5zM444 383.8l-43.7 17.5a17.7 17.7 0 0 1-13 0l-37.3-15-37.2 15a17.8 17.8 0 0 1-13 0L256 383.8a17.5 17.5 0 0 1 13-32.6l37.3 15 37.2-15c4.2-1.6 8.8-1.6 13 0l37.3 15 37.2-15a17.5 17.5 0 0 1 13 32.6zm17-86.3h-82a11.3 11.3 0 0 1-6.9-20.4l54.7-40.3a11.6 11.6 0 0 1 16.4 2.8l27.4 40.4a11.3 11.3 0 0 1-9.6 17.5z',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_shape_type' => 'svg',
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_scalar',
			[ 
				'label'              => esc_html__( 'Scalar', 'bdthemes-element-pack' ) . BDTEP_NC,
				'description'        => esc_html__( 'Scale factor for each confetti particle. Use decimals to make the confetti smaller. Go on, try teeny tiny confetti, they are adorable!', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min' => 1,
						'max' => 20,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_origin',
			[ 
				'label'              => esc_html__( 'Origin', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
				],
				'return_value'       => 'yes',
				//'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'ep_widget_cf_origin_x',
			[ 
				'label'              => esc_html__( 'X', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 1,
						'step' => .1
					],
				],
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_origin'   => 'yes',
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_origin_y',
			[ 
				'label'              => esc_html__( 'Y', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 1,
						'step' => .1
					],
				],
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
					'ep_widget_cf_origin'   => 'yes',
				],
			]
		);

		$widget->end_popover();

		$widget->add_control(
			'ep_widget_cf_trigger_type',
			[ 
				'label'              => esc_html__( 'Trigger Action', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [ 
					'load'         => esc_html__( 'On Load', 'bdthemes-element-pack' ),
					'onview'       => esc_html__( 'On View', 'bdthemes-element-pack' ),
					'click'        => esc_html__( 'On Click', 'bdthemes-element-pack' ),
					'mouseenter'   => esc_html__( 'On Hover', 'bdthemes-element-pack' ),
					'delay'        => esc_html__( 'Delay', 'bdthemes-element-pack' ),
					'ajax-success' => esc_html__( 'Ajax Success', 'bdthemes-element-pack' ),
				],
				'default'            => 'load',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes',
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_trigger_selector',
			[ 
				'label'              => esc_html__( 'Trigger Selector', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::TEXT,
				'description'        => esc_html__( 'Place your selector. example:- #test-id, .test-class', 'bdthemes-element-pack' ),
				'dynamic'            => [ 
					'active' => true,
				],
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti'     => 'yes',
					'ep_widget_cf_trigger_type' => [ 'click', 'mouseenter' ],
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_trigger_delay',
			[ 
				'label'              => esc_html__( 'Delay Time (ms)', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [ 
					'size' => 3000,
				],
				'range'              => [ 
					'px' => [ 
						'min' => 1000,
						'max' => 10000,
					],
				],
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti'     => 'yes',
					'ep_widget_cf_trigger_type' => 'delay',
				],
			]
		);

		$widget->add_control(
			'ep_widget_cf_z_index',
			[ 
				'label'              => esc_html__( 'Z-index', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'               => Controls_Manager::NUMBER,
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => [ 
					'ep_widget_cf_confetti' => 'yes'
				],
			]
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'confetti', BDTEP_ASSETS_URL . 'vendor/js/confetti.browser.min.js', [], 'v5.19.2', true );
	}
	public function should_script_enqueue( $widget ) {
		if ( 'yes' === $widget->get_settings_for_display( 'ep_widget_cf_confetti' ) ) {
			$this->enqueue_scripts();
			wp_enqueue_script( 'ep-confetti-effects' );
		}
	}

	protected function add_actions() {

		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/common/section_element_pack_confetti_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		// Add section for settings
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/section/section_element_pack_confetti_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/container/section_element_pack_confetti_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'should_script_enqueue' ], 10, 1 );


		// render scripts
		add_action( 'elementor/frontend/section/before_render', [ $this, 'should_script_enqueue' ], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'should_script_enqueue' ], 10, 1 );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}
}