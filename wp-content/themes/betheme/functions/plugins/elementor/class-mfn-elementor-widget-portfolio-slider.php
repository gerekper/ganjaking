<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Portfolio_Slider extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-portfolio-slider', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-portfolio-slider-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-portfolio-slider' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_portfolio_slider';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Portfolio slider', 'mfn-opts' );
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
        'description' => __( 'Large number of items may affect performance', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
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
			'arrows',
			[
				'label' => __( 'Navigation', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          '' => __('None', 'mfn-opts'),
          'hover' => __('Show on Hover', 'mfn-opts'),
          'always' => __('Always Show', 'mfn-opts'),
				),
				'default' => '',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Image size', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          'small' => __('Small', 'mfn-opts'),
          'medium' => __('Medium', 'mfn-opts'),
          'large' => __('Large', 'mfn-opts'),
				),
				'default' => 'small',
			]
		);

		$this->add_control(
			'scroll',
			[
				'label' => __( 'Slides to scroll', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          'page' => __('One Page', 'mfn-opts'),
          'slide' => __('Single Slide', 'mfn-opts'),
				),
				'default' => 'page',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_portfolio_slider( $settings );

	}

}
