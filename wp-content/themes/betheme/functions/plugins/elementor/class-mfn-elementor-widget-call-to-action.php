<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Call_To_Action extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_call_to_action';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Call to action', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-exclamation';
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

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'label_block' => true,
				'default' => [
					'value' => 'far fa-star',
					'library' => 'regular',
				],
			]
		);

    $this->add_control(
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'description' => __('Basic HTML tags allowed.', 'mfn-opts'),
				'label_block' => true,
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			]
		);

    $this->add_control(
			'button_title',
			[
				'label' => __( 'Button Title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				// 'label_block' => true,
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

		$this->start_controls_section(
			'advanced_section',
			[
				'label' => __( 'Advanced', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'class',
			[
				'label' => __( 'Class', 'mfn-opts' ),
				'description' => __( 'This option is useful when you want to use <b>scroll</b>', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				// 'label_block' => true,
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

		$settings['icon'] = $settings['icon']['value'];

		echo sc_call_to_action( $settings, $settings['content'] );

	}

}
