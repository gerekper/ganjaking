<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Zoom_Box extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_zoom_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Zoom box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-images';
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
      'image',
      [
        'label' => __( 'Image', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
      ]
    );

    $this->add_control(
      'bg_color',
      [
        'label' => __( 'Overlay color', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#0898ec',
      ]
    );

    $this->add_control(
      'content_image',
      [
        'label' => __( 'Content image', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::MEDIA,
      ]
    );

    $this->add_control(
      'content',
      [
        'label' => __( 'Content', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
      ]
    );

		$this->end_controls_section();

    $this->start_controls_section(
			'link_section',
			[
				'label' => __( 'Link', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'target',
			[
				'label' => __( 'Target', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __('_self', 'mfn-opts'),
					1 => __('_blank', 'mfn-opts'),
				),
				'default' => 0,
        'condition' => [
          'link!' => '',
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

		$settings['image'] = $settings['image']['url'];
		$settings['content_image'] = $settings['content_image']['url'];

		echo sc_zoom_box( $settings, $settings['content'] );

	}

}
