<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Timeline extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_timeline';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Timeline', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-stream';
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
			'date',
			[
				'label' => __( 'Date', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '2020',
				'dynamic' => [
					'active' => true,
				],
			]
		);

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
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'Content', 'mfn-opts' ),
				'show_label' => false,
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
						'date' => '2018',
						'title' => __( 'Title #1', 'mfn-opts' ),
						'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
					],
					[
            'date' => '2019',
						'title' => __( 'Title #2', 'mfn-opts' ),
						'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
					],
				],
				'title_field' => '{{{ date }}} - {{{ title }}}',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

    foreach( $settings['tabs'] as $key => $tab ){
      $settings['tabs'][$key]['title'] = '<span>'. $tab['date'] .'</span>'. $tab['title'];
    }

		echo sc_timeline( $settings );

	}

}
