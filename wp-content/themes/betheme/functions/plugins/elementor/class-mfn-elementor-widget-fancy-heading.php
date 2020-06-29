<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Fancy_Heading extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_fancy_heading';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Fancy heading', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-animated-headline';
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
        'label_block'	=> true,
				'default' => __( 'This is the heading', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'h1',
			[
				'label' => __( 'Use H1 tag', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options'	=> [
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
        ],
        'default' => '0',
			]
		);

    $this->add_control(
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'description' => __( 'Some Shortcodes and HTML tags allowed', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
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
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          'icon'		=> __('Icon', 'mfn-opts'),
          'line'		=> __('Line', 'mfn-opts'),
          'arrows' 	=> __('Arrows', 'mfn-opts'),
        ),
				'default' => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-star',
					'library' => 'regular',
				],
        'condition' => [
          'style' => 'icon',
        ],
			]
		);

    $this->add_control(
			'slogan',
			[
				'label' => __( 'Slogan', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        'label_block'	=> true,
				'default' => __( 'This is the slogan', 'mfn-opts' ),
        'condition' => [
          'style' => 'line',
        ],
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

		echo sc_fancy_heading( $settings, $settings['content'] );

	}

}
