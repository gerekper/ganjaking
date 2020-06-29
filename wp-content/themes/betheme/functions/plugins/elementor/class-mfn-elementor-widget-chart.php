<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Chart extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-chart', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-chart-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-chart' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_chart';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Chart', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-circle-notch';
	}

	/**
	 * Get widget categories
	 */

	public function get_categories() {
		return [ 'mfn_builder' ];
	}

	/**
	 * Register widget controls
	 */

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'mfn-opts' ),
			]
		);

    $this->add_control(
			'title',
			[
				'label' => __( 'Title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'This is the heading', 'mfn-opts' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'chart_section',
			[
				'label' => __( 'Chart', 'mfn-opts' ),
			]
		);

    $this->add_control(
			'percent',
			[
				'label' => __( 'Percent', 'mfn-opts' ),
				'description' => __( 'Number between 0-100', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 90,
			]
		);

    $this->add_control(
			'label',
			[
				'label' => __( 'Label', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'icon[value]' => '',
					'image[url]' => '',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'label_block' => true,
				'condition' => [
					'label' => '',
					'image[url]' => '',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'mfn-opts' ),
				'description' => __( 'Recommended image size: <b>70px x 70px</b>', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'icon[value]' => '',
					'label' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'options_section',
			[
				'label' => __( 'Options', 'mfn-opts' ),
			]
		);

    $this->add_control(
			'color',
			[
				'label' => __( 'Color', 'mfn-opts' ),
				'description' => __( 'optional', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
			]
		);

    $this->add_control(
			'line_width',
			[
				'label' => __( 'Line width', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'alpha' => 'false',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		$settings['icon'] = $settings['icon']['value'];
		$settings['image'] = $settings['image']['url'];

		echo sc_chart( $settings );

	}

}
