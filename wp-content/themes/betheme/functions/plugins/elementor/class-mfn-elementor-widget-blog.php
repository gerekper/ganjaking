<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Blog extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-blog', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-blog-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-blog' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_blog';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Blog', 'mfn-opts' );
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
          'classic' => __('Classic - 1 column', 'mfn-opts'),
          'grid' => __('Grid - 2-4 columns', 'mfn-opts'),
          'masonry' => __('Masonry Blog Style - 2-4 columns', 'mfn-opts'),
          'masonry tiles'	=> __('Masonry Tiles (vertical images) - 2-4 columns', 'mfn-opts'),
          'photo' => __('Photo (horizontal images) - 1 column', 'mfn-opts'),
          'photo2' => __('Photo 2 - 1-3 columns', 'mfn-opts'),
          'timeline' => __('Timeline - 1 column', 'mfn-opts'),
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
          'style' => ['grid', 'masonry', 'masonry tiles', 'photo2'],
        ],
			]
		);

    $this->add_control(
			'title_tag',
			[
				'label' => __( 'Title tag', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default' => 'h2',
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
			'filters',
			[
				'label' => __( 'Filters', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					'0' => __('Hide', 'mfn-opts'),
					'1' => __('Show', 'mfn-opts'),
					'only-categories' => __('Show only Categories', 'mfn-opts'),
					'only-tags' => __('Show only Tags', 'mfn-opts'),
					'only-authors' => __('Show only Authors', 'mfn-opts'),
				),
				'default' => "0",
				'label_block' => true,
				'condition' => [
          'category' => '',
          'style' => ['masonry', 'masonry tiles'],
					'pagination' => '0',
        ],
			]
		);

		$this->add_control(
			'excerpt',
			[
				'label' => __( 'Excerpt', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __( 'Hide', 'mfn-opts' ),
					1 => __( 'Show', 'mfn-opts' ),
				),
				'default' => 1,
			]
		);

		$this->add_control(
			'more',
			[
				'label' => __( 'Read more', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __( 'Hide', 'mfn-opts' ),
					1 => __( 'Show', 'mfn-opts' ),
				),
				'default' => 1,
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
					0 => __( 'Hide', 'mfn-opts' ),
					1 => __( 'Show', 'mfn-opts' ),
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
					0 => __( 'Hide', 'mfn-opts' ),
					1 => __( 'Show', 'mfn-opts' ),
				),
				'default' => 0,
				'condition' => [
          'pagination' => '1',
        ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'images',
			[
				'label' => __( 'Images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					'' => 'Default',
					'images-only' => 'Featured Images only (replace sliders and videos with featured image)',
				),
				'default' => '',
				'label_block' => true,
        'condition' => [
          'style!' => 'masonry tiles',
        ],
			]
		);

		$this->add_control(
			'greyscale',
			[
				'label' => __( 'Greyscale images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => __( 'No', 'mfn-opts' ),
					1 => __( 'Yes', 'mfn-opts' ),
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
					0 => 'No',
					1 => 'Yes'
				),
				'default' => 0,
				'condition' => [
          'style' => 'masonry tiles',
        ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'plugins_section',
			[
				'label' => __( 'Plugins', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'events',
			[
				'label' => __( 'Include events', 'mfn-opts' ),
				'description' => __( 'requires free The Events Calendar plugin', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
					0 => 'No',
					1 => 'Yes'
				),
				'default' => 0,
				'condition' => [
          'category' => '',
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

		echo sc_blog( $settings );

	}

}
