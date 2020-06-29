<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_How_It_Works extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_how_it_works';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • How it works', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-circle';
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
			'number',
			[
				'label' => __( 'Number', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 1,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'mfn-opts' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				'type' => \Elementor\Controls_Manager::WYSIWYG,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'border',
			[
				'label' => __( 'Line right', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options'	=> array(
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
				),
				'default' => 0,
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'description' => __( 'Background Image style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          '' => __('Small centered image (image size: max 116px)', 'mfn-opts'),
          'fill' => __('Fill the circle (image size: 200px x 200px)', 'mfn-opts'),
				),
				'default' => '',
				'label_block' => true,
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

		echo sc_how_it_works( $settings, $settings['content'] );

	}

}
