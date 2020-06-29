<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Shop extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_shop';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Shop', 'mfn-opts' );
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
			'limit',
			[
				'label' => __( 'Number of products', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

    $this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			]
		);

    $this->add_control(
			'type',
			[
				'label' => __( 'Display', 'mfn-opts' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
        'options'	=> array(
          'products' => __('-- Default --', 'mfn-opts'),
          'sale_products' => __('On sale', 'mfn-opts'),
          'best_selling_products' => __('Best selling (order by: Sales)', 'mfn-opts'),
          'top_rated_products' => __('Top-rated (order by: Rating)', 'mfn-opts'),
        ),
				'default' => 'products',
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
				'default' => '',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order by', 'mfn-opts' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' 	=> array(
          'date' => __('Date the product was published', 'mfn-opts'),
          'id' => __('ID of the product', 'mfn-opts'),
          'menu_order' => __('Menu order (if set)', 'mfn-opts'),
          'popularity' => __('Popularity (number of purchases)', 'mfn-opts'),
          'rating' => __('Rating', 'mfn-opts'),
          'title' => __('Title', 'mfn-opts'),
          'rand' => __('Random', 'mfn-opts'),
				),
        'condition' => [
          'type!' => ['best_selling_products', 'top_rated_products'],
        ],
				'default' => 'title',
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					'ASC' => __('Ascending', 'mfn-opts'),
					'DESC' => __('Descending', 'mfn-opts'),
				),
        'condition' => [
          'type!' => ['best_selling_products', 'top_rated_products'],
        ],
				'default' => 'ASC',
			]
		);

		$this->end_controls_section();

    $this->start_controls_section(
			'advanced_section',
			[
				'label' => __( 'Advanced', 'mfn-opts' ),
        'condition' => [
          'orderby!' => 'rand',
        ]
			]
		);

    $this->add_control(
			'paginate',
			[
				'label' => __( 'Pagination', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default' => '0',
			]
		);

    $this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

    if( class_exists( 'WC_Shortcode_Products' ) ){
      $shortcode = new WC_Shortcode_Products( $settings, $settings['type'] );
      echo $shortcode->get_content();
    }

	}

}
