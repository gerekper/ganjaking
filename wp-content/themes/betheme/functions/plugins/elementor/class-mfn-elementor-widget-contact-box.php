<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Contact_Box extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_contact_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Contact box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-envelope';
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
			'address',
			[
				'label' => __( 'Address', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'This is the address', 'mfn-opts' ),
			]
		);

    $this->add_control(
			'telephone',
			[
				'label' => __( 'Phone', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '+00 000 000 000',
			]
		);

    $this->add_control(
			'telephone_2',
			[
				'label' => __( 'Phone 2nd', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '+00 000 000 000',
			]
		);

    $this->add_control(
			'fax',
			[
				'label' => __( 'Fax', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '+00 000 000 000',
			]
		);

    $this->add_control(
			'email',
			[
				'label' => __( 'Email', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'email@wordpress.org',
        'label_block' => true,
			]
		);

    $this->add_control(
			'www',
			[
				'label' => __( 'WWW', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'www.wordpress.org',
        'label_block' => true,
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

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		$settings['image'] = $settings['image']['url'];
		$settings['address'] = nl2br($settings['address']);

		echo sc_contact_box( $settings );

	}

}
