<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Shop_Slider extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-shop-slider', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-shop-slider-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-shop-slider' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_shop_slider';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Shop slider', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-shopping-bag';
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
				'default' => __( 'This is the heading', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'This is the heading', 'mfn-opts' ),
			]
		);


    $this->add_control(
			'count',
			[
				'label' => __( 'Number of items', 'mfn-opts' ),
				'description' => __( 'Large number of items may affect performance', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
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
				'options'	=> mfn_get_categories('product_cat'),
				'default' => "",
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order by', 'mfn-opts' ),
				'description' => __('Do not use random order with pagination', 'mfn-opts'),
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

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_shop_slider( $settings );

	}

}
