<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Portfolio_Photo extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_portfolio_photo';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Portfolio photo', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-gallery-grid';
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
			'count',
			[
				'label' => __( 'Number of posts', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
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
				'options'	=> mfn_get_categories('portfolio-types'),
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

    $this->add_control(
			'margin',
			[
				'label' => __( 'Margin', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
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

		echo sc_portfolio_photo( $settings );

	}

}
