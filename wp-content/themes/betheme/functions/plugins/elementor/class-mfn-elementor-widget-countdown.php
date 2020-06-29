<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Countdown extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-countdown', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-countdown-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-countdown' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_countdown';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Countdown', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-stopwatch';
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
			'date',
			[
				'label' => __( 'Launch Date', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'default' => '2020/12/31 12:00',
			]
		);

    $this->add_control(
			'timezone',
			[
				'label' => __( 'UTC Timezone', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> mfna_utc(),
				'label_block'	=> true,
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
			'show',
			[
				'label' => __( 'Show', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          ''				=> __('days hours minutes seconds', 'mfn-opts'),
          'dhm' 		=> __('days hours minutes', 'mfn-opts'),
          'dh' 			=> __('days hours', 'mfn-opts'),
          'd' 			=> __('days', 'mfn-opts'),
        ),
				'default' => '',
        'label_block'	=> true,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_countdown( $settings );

	}

}
