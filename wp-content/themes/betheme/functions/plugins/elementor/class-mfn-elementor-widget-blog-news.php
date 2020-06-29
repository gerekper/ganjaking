<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Blog_News extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_blog_news';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Blog news', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-post-list';
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
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          '' => __('Default', 'mfn-opts'),
          'featured' => __('Featured 1st', 'mfn-opts'),
				),
				'default' => '',
			]
		);

    $this->add_control(
			'count',
			[
				'label' => __( 'Count', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
        'description' => __('Number of posts to show', 'mfn-opts'),
				'default' => 5,
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
			'category',
			[
				'label' => __( 'Category', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> mfn_get_categories('category'),
				'default' => "",
			]
		);

		$this->add_control(
			'category_multi',
			[
				'label' => __( 'Multiple Categories', 'mfn-opts' ),
				'description'	=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order by', 'mfn-opts' ),
				'description' => __('Do not use random order with pagination or load more', 'mfn-opts'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' 	=> array(
					'date' => __('Date', 'mfn-opts'),
					'title' => __('Title', 'mfn-opts'),
					'rand' => __('Random', 'mfn-opts'),
				),
				'default' => "date",
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					'ASC' 	=> __('Ascending', 'mfn-opts'),
					'DESC' 	=> __('Descending', 'mfn-opts'),
				),
				'default' => "DESC",
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
			'excerpt',
			[
				'label' => __( 'Excerpt', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __('Hide', 'mfn-opts'),
					1 => __('Show', 'mfn-opts'),
					'featured' => __('Show for Featured only', 'mfn-opts'),
				),
				'default' => 0,
				'label_block' => true,
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Button | Link', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'link_title',
			[
				'label' => __( 'Button | Title', 'mfn-opts' ),
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

		echo sc_blog_news( $settings );

	}

}
