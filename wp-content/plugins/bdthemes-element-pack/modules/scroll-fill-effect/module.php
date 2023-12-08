<?php

namespace ElementPack\Modules\ScrollFillEffect;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
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
		return 'bdt-scroll-fill-effect';
	}

	public function register_section( $element ) {
		$element->start_controls_section(
			'section_element_pack_sfe_controls',
			[ 
				'tab'   => Controls_Manager::TAB_CONTENT,
				'label' => BDTEP_CP . esc_html__( 'Scroll Fill Effect', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);
		$element->end_controls_section();
	}


	public function register_controls( $widget, $args ) {

		$widget->add_control(
			'ep_widget_sf_fx_enable',
			[ 
				'label'              => esc_html__( 'Use Scroll Fill Effect?', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'default'            => '',
				'return_value'       => 'yes',
				'frontend_available' => true,
				'prefix_class'       => 'bdt-scroll-effect-',
			]
		);

		$widget->add_control(
			'ep_widget_sf_fx_base_color',
			[ 
				'label'       => __( 'Base Color', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Set the base color for the text. Must Select Transparent Color.', 'bdthemes-element-pack' ),
				'alpha'       => true,
				'condition'   => [ 
					'ep_widget_sf_fx_enable' => 'yes'
				],
				'selectors'   => [ 
					'{{WRAPPER}} .elementor-heading-title'                  => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-heading-tag span'                     => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-advanced-heading-main-title-inner' => '-webkit-text-fill-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'           => 'ep_widget_sf_fx_fill_bg',
				'types'          => [ 'gradient' ],
				'fields_options' => [ 
					'background' => [ 
						'label' => 'Fill Color'
					],
				],
				'condition'      => [ 
					'ep_widget_sf_fx_enable' => 'yes'
				],
				'selector'       => '{{WRAPPER}} .elementor-widget-container .elementor-heading-title, {{WRAPPER}} .elementor-widget-container .bdt-heading-tag span, {{WRAPPER}} .bdt-ep-advanced-heading-main-title-inner',
			]
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'gsap', BDTEP_ASSETS_URL . 'vendor/js/gsap.min.js', [], '3.12.2', true );
		wp_enqueue_script( 'scroll-trigger-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollTrigger.min.js', [ 'gsap' ], '3.9.1', true );
	}
	public function should_script_enqueue( $widget ) {
		if ( 'yes' === $widget->get_settings_for_display( 'ep_widget_sf_fx_enable' ) ) {
			$this->enqueue_scripts();
			wp_enqueue_script( 'ep-scroll-fill-effect' );
		}
	}

	protected function add_actions() {

		add_action( 'elementor/element/heading/section_title/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/heading/section_element_pack_sfe_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		add_action( 'elementor/element/bdt-animated-heading/section_content_heading/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/bdt-animated-heading/section_element_pack_sfe_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		add_action( 'elementor/element/bdt-advanced-heading/section_content_advanced_heading/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/bdt-advanced-heading/section_element_pack_sfe_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		// render scripts
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'should_script_enqueue' ], 10, 1 );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}
}