<?php

namespace ElementorLayerSlider\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;


class LS_Elementor_Widget extends Widget_Base {

	public function get_name() {
		return 'layerslider';
	}

	public function get_title() {
		return 'LayerSlider';
	}

	public function get_icon() {
		return 'eicon-layerslider';
	}

	public function get_categories() {
		return [
			'general',
		];
	}

	public function get_script_depends() {
		return empty( $_GET['elementor-preview'] ) ? [] : [
			'ls-elementor-frontend',
		];
	}

	protected function _register_controls() {

		// ------------ CONTENT SECTION ------------
		$this->start_controls_section( 'content_section', [
			'label' => __( 'Content', 'LayerSlider' ),
			'tab' 	=> Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'identifier', [
			'classes' => 	'ls-id',
			'type' => 		Controls_Manager::HIDDEN,
		] );

		$this->add_control( 'slider-library', [
			'type' 	=> Controls_Manager::RAW_HTML,
			'raw' 	=>
				'<label class="elementor-control-title">' . __( 'Choose Slider', 'LayerSlider' ) . '</label>' .
				'<br><br>' .
				'<button type="button" class="elementor-button elementor-button-default" onclick="LS_Widget.chooseSlider()">' .
					'<i class="fa fa-folder-open"></i>' . __( 'Open Slider Library', 'LayerSlider' ) .
				'</button>',
		] );

		$this->add_control( 'sliderbuilder', [
			'type' 		=> Controls_Manager::RAW_HTML,
			'raw' 		=>
				'<label class="elementor-control-title">' . __( 'Edit Slider', 'LayerSlider' ) . '</label>' .
				'<br><br>' .
				'<button type="button" class="elementor-button elementor-button-default" onclick="LS_Widget.openEditor()">' .
					'<i class="fa fa-external-link-square"></i>' . __( 'Open Slider Builder', 'LayerSlider' ) .
				'</button>',
			'separator' => 'before',
			'condition' => [
				'identifier!' 	=> '',
			],
		] );

		$this->end_controls_section();


		// ------------ OVERRIDES SECTION ------------
		$this->start_controls_section( 'overrides_section', [
			'classes' 	=> 'ls-overrides',
			'label' 	=> __( 'Override Slider Settings', 'LayerSlider' ),
			'tab' 		=> Controls_Manager::TAB_CONTENT,
			'condition' => [
				'identifier!' 	=> '',
			],
		] );

		$this->add_control( 'type', [
			'label' 	=> __( 'Layout Mode', 'LayerSlider' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				''  			=> __( 'No override', 'LayerSlider' ),
				'fixedsize'  	=> __('Fixed size', 'LayerSlider'),
				'responsive' 	=> __('Responsive', 'LayerSlider'),
				'fullwidth' 	=> __('Full width', 'LayerSlider'),
				'fullsize' 		=> __('Full size', 'LayerSlider'),
				'hero' 			=> __('Hero scene', 'LayerSlider'),
			]
		] );

		$skins = \LS_Sources::getSkins();
		$skinsOptions = [ '' => __( 'No override', 'LayerSlider' ) ];
		foreach( $skins as $handle => $skin ) {
			$skinsOptions[ $handle ] = $skin['name'];
		}

		$this->add_control( 'skin', [
			'label' 	=> __( 'Skin', 'LayerSlider' ),
			'type' 		=> Controls_Manager::SELECT,
			'options' 	=> $skinsOptions,
		] );

		$this->add_control( 'autostart', [
			'label' 	=> __( 'Auto-Start', 'LayerSlider' ),
			'type' 		=> Controls_Manager::SELECT,
			'options' 	=> [
				''  			=> __( 'No override', 'LayerSlider' ),
				'enabled'  		=> __( 'Enabled', 'LayerSlider' ),
				'disabled'  	=> __( 'Disabled', 'LayerSlider' ),
			]
		] );

		$this->add_control( 'firstslide', [
			'classes' 		=> 'ls-firstslide',
			'label' 		=> __( 'Start with Slide', 'LayerSlider' ),
			'type' 			=> Controls_Manager::NUMBER,
			'placeholder' 	=> __( 'No override', 'LayerSlider' ),
			'min' 			=> 1,
		] );

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if( empty( $settings['identifier'] ) ) {
			return;
		}

		$options = [];
		$overrides = [
			'type',
			'skin',
			'autostart',
			'firstslide',
		];

		foreach( $overrides as $key ) {
			if( ! empty( $settings[ $key ] ) ) {
				$options[ $key ] = $settings[ $key ];
			}
		}

		layerslider( $settings['identifier'], '', $options );
	}

	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		if( ! empty( $_GET['elementor-preview'] ) ) {
			wp_enqueue_style(
				'ls-elementor',
				LS_ROOT_URL.'/static/admin/css/elementor.css',
				false,
				LS_PLUGIN_VERSION
			);
		}

		wp_register_script(
			'ls-elementor-frontend',
			LS_ROOT_URL.'/static/admin/js/elementor-frontend.js',
			['elementor-frontend'],
			LS_PLUGIN_VERSION,
			true
		);
	}
}
