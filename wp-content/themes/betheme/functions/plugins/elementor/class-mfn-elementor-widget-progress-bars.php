<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Progress_Bars extends \Elementor\Widget_Base {

	/**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-progress-bars', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-progress-bars-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-progress-bars' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_progress_bars';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Progress bars', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-skill-bar';
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

    $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Event title', 'mfn-opts' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

    $repeater->add_control(
      'value',
      [
        'label' => __( 'Value', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => '50',
      ]
    );

    $repeater->add_control(
      'size',
      [
        'label' => __( 'Size', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => '20',
      ]
    );

    $repeater->add_control(
      'color',
      [
        'label' => __( 'Color', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::COLOR,
      ]
    );

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Items', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Bar #1', 'mfn-opts' ),
						'value' => 50,
						'size' => 20,
					],
					[

						'title' => __( 'Bar #2', 'mfn-opts' ),
						'value' => 70,
						'size' => 20,
					],
				],
				'title_field' => '{{{ title }}} - {{{ value }}}%',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_progress_bars( $settings );

	}

}
