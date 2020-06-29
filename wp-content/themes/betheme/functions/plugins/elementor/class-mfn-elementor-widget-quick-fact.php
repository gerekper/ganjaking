<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Quick_Fact extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  // public function get_script_depends() {
	// 	if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
	// 		wp_register_script( 'mfn-counter', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-counter-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
	// 		return [ 'mfn-counter' ];
	// 	}
  //
	// 	return [];
	// }

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_quick_fact';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Quick fact', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-lightbulb';
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
			'heading',
			[
				'label' => __( 'Heading', 'mfn-opts' ),
				'default' => __( 'This is the heading', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

    $this->add_control(
			'title',
			[
				'label' => __( 'Title', 'mfn-opts' ),
				'default' => __( 'This is the title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

    $this->add_control(
      'content',
      [
        'label' => __( 'Content', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'fact_section',
      [
        'label' => __( 'Quick fact', 'mfn-opts' ),
      ]
    );

    $this->add_control(
			'prefix',
			[
				'label' => __( 'Prefix', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

    $this->add_control(
			'number',
			[
				'label' => __( 'Number', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => 99,
			]
		);

    $this->add_control(
			'label',
			[
				'label' => __( 'Postfix', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
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
			'align',
			[
				'label' => __( 'Align', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          ''			=> __('Center', 'mfn-opts'),
          'left'		=> __('Left', 'mfn-opts'),
          'right'		=> __('Right', 'mfn-opts'),
        ),
				'default' => '',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_quick_fact( $settings, $settings['content'] );

	}

}
