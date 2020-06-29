<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Gallery extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-gallery', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-gallery-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-gallery' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_gallery';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Gallery', 'mfn-opts' );
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
			'ids',
			[
				'label' => __( 'Images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
			]
		);

    $this->add_control(
			'size',
			[
				'label' => __( 'Size', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          'thumbnail' => __('Thumbnail', 'mfn-opts'),
          'medium' => __('Medium', 'mfn-opts'),
          'large' => __('Large', 'mfn-opts'),
          'full' => __('Full', 'mfn-opts'),
				),
				'default' => 'thumbnail',
			]
		);

    $this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
			]
		);

    $this->add_control(
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          '' => __('Default', 'mfn-opts'),
          'flat' => __('Flat', 'mfn-opts'),
          'fancy' => __('Fancy', 'mfn-opts'),
          'masonry' => __('Masonry', 'mfn-opts'),
				),
				'default' => '',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

    $ids = array();

    if( is_array( $settings['ids'] ) ){
      foreach( $settings['ids'] as $image ){
        $ids[] = $image['id'];
      }
    }

		$settings['ids'] = implode(',', $ids);
    $settings['link'] = 'file';

		echo sc_gallery( $settings );

	}

}
