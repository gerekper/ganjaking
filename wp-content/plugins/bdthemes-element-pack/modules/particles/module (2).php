<?php

namespace ElementPack\Modules\Particles;

use ElementPack\Base\Element_Pack_Module_Base;
use Elementor\Controls_Manager;
use ElementPack;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	private $sections_data;

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-particles';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'section_background_particles_controls',
			[
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => BDTEP_CP . esc_html__('Background Particles Effects', 'bdthemes-element-pack'),
			]
		);

		$element->end_controls_section();
	}

	public function register_controls($widget, $args) {

		$widget->add_control(
			'section_particles_on',
			[
				'label'              => BDTEP_CP . esc_html__('Particles Effects', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'return_value'       => 'yes',
				'prefix_class'       => 'bdt-particles-',
				'separator'          => ['before'],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$widget->add_control(
			'section_particles_js',
			[
				'label'              => esc_html__('Particles JSON', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::TEXTAREA,
				'condition'          => [
					'section_particles_on' => 'yes',
				],
				'description'        => __('Paste your particles JSON code here - Generate it from <a href="http://vincentgarreau.com/particles.js/#default" target="_blank">Here</a>.', 'bdthemes-element-pack'),
				'default'            => '',
				'dynamic'            => ['active' => true],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$widget->add_control(
			'section_particles_z_index',
			[
				'label'       => esc_html__('Z-Index', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'condition'   => [
					'section_particles_on' => 'yes',
				],
				'description' => __('If you need mouse activity, you can fix z-index.', 'bdthemes-element-pack'),
				'default'     => '',
				'dynamic'     => ['active' => true],
				'selectors'   => [
					'{{WRAPPER}} .bdt-particle-container' => 'z-index: {{VALUE}};',
				],
				'render_type' => 'template',
			]
		);
	}

	//	public function particles_before_render($section) {
	//		$settings = $section->get_settings_for_display();
	//		$id       = $section->get_id();
	//
	//		if( $settings['section_particles_on'] == 'yes' ) {
	//
	//			$particle_js = $settings['section_particles_js'];
	//
	//			if (empty($particle_js)) {
	//				$particle_js = '{"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}';
	//			}
	//
	//			$this->sections_data[$id] = [ 'particles_js' => $particle_js ];
	//
	//			ElementPack\element_pack_config()->elements_data['sections'] = $this->sections_data;
	//		}
	//	}


	public function enqueue_scripts() {
		wp_enqueue_script('particles-js', BDTEP_ASSETS_URL . 'vendor/js/particles.min.js', ['jquery'], '2.0.0', true);
	}
	public function should_script_enqueue($widget) {
		if ('yes' === $widget->get_settings_for_display('section_particles_on')) {
			$this->enqueue_scripts();
			wp_enqueue_script('ep-particles');
		}
	}

	protected function add_actions() {

		add_action('elementor/element/container/section_background/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/container/section_background_particles_controls/before_section_end', [$this, 'register_controls'], 10, 2);
		add_action('elementor/frontend/container/after_render', [$this, 'should_script_enqueue']);

		add_action('elementor/element/section/section_background/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/section/section_background_particles_controls/before_section_end', [$this, 'register_controls'], 10, 2);

		//render scripts
		add_action('elementor/frontend/section/after_render', [$this, 'should_script_enqueue']);
		add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
	}
}
