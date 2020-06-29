<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Info_Box extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_info_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Info box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-list-ul';
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
				'default' => __( 'This is the heading', 'mfn-opts' ),
				'label_block' => true,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '', 'mfn-opts' ),
				'label_block' => true,
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
						'content' => __( 'Lorem ipsum dolor sit amet' ),
					],
					[
						'content' => __( 'Lorem ipsum dolor sit amet' ),
					],
				],
				'title_field' => '{{{ content }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'adcanced_section',
			[
				'label' => __( 'Advanced', 'mfn-opts' ),
			]
		);

    $this->add_control(
			'image',
			[
				'label' => __( 'Background Image', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Additional content', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
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

		echo sc_info_box( $settings, $settings['content'] );

	}

}
