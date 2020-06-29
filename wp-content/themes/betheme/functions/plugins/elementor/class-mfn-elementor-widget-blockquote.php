<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Blockquote extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_blockquote';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Blockquote', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-quote-right';
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
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => __('Basic HTML tags allowed.', 'mfn-opts'),
				'label_block' => true,
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			]
		);

		$this->add_control(
			'author',
			[
				'label' => __( 'Author', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'This is the author',
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

    // print_r($settings);

		echo sc_blockquote( $settings, $settings['content'] );

	}

}
