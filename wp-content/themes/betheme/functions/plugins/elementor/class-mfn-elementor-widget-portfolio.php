<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Portfolio extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-portfolio', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-portfolio-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-portfolio' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_portfolio';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Portfolio', 'mfn-opts' );
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
				'default' => 3,
			]
		);

    $this->add_control(
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					'flat' => __('Flat', 'mfn-opts'),
					'grid' => __('Grid', 'mfn-opts'),
					'masonry' => __('Masonry Blog Style', 'mfn-opts'),
					'masonry-hover' => __('Masonry Hover Description', 'mfn-opts'),
					'masonry-minimal' => __('Masonry Minimal', 'mfn-opts'),
					'masonry-flat' => __('Masonry Flat', 'mfn-opts'),
					'list' => __('List', 'mfn-opts'),
					'exposure' => __('Exposure', 'mfn-opts'),
				),
        'label_block' => true,
				'default' => 'grid',
			]
		);

    $this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          2	=> 2,
          3	=> 3,
          4	=> 4,
          5	=> 5,
          6	=> 6,
				),
				'default' => 3,
        'condition' => [
          'style' => ['flat', 'grid', 'masonry', 'masonry-hover'],
        ],
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

		$this->start_controls_section(
			'advanced_section',
			[
				'label' => __( 'Advanced', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'exclude_id',
			[
				'label' => __( 'Exclude Posts', 'mfn-opts' ),
				'description'		=> __('IDs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'related',
			[
				'label' => __( 'Use as related posts', 'mfn-opts' ),
				'description' => __( 'Use on single project page to exclude current project', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __('No', 'mfn-opts'),
					1 => __('Yes', 'mfn-opts'),
				),
				'default' => '0',
			]
		);

		$this->add_control(
			'filters',
			[
				'label' => __( 'Filters', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __('No', 'mfn-opts'),
					1 => __('Yes', 'mfn-opts'),
				),
				'default' => "0",
				'condition' => [
          'category' => '',
					'pagination' => '0',
        ],
			]
		);

		$this->add_control(
			'greyscale',
			[
				'label' => __( 'Greyscale images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => 'No',
					1 => 'Yes'
				),
				'default' => 0,
				'condition' => [
					'style!' => ['masonry-hover', 'exposure'],
        ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_section',
			[
				'label' => __( 'Pagination', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' => __( 'Pagination', 'mfn-opts' ),
				'description' => __( 'Pagination will <strong>not</strong> work if you put item on Homepage of WordPress Multilingual Site.', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => 'No',
					1 => 'Yes'
				),
				'default' => 0,
			]
		);

		$this->add_control(
			'load_more',
			[
				'label' => __( 'Load more', 'mfn-opts' ),
				'description' => __( '<b>Sliders</b> will be replaced with featured images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => 'No',
					1 => 'Yes'
				),
				'default' => 0,
				'condition' => [
          'pagination' => '1',
        ],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_portfolio( $settings );

	}

}
