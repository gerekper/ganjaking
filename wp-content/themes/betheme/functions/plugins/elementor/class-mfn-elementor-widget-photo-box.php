<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Photo_Box extends \Elementor\Widget_Base {

  /**
   * Get script dependences
   */

  public function get_script_depends() {
    if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
      wp_register_script( 'mfn-photo-box', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-photo-box-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
      return [ 'mfn-photo-box' ];
    }

    return [];
  }


	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_photo_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Photo box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-image';
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
				'default' => __( 'This is the heading', 'mfn-opts' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Content', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'link_section',
			[
				'label' => __( 'Link', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
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

		$this->end_controls_section();

		$this->start_controls_section(
			'greyscale_section',
			[
				'label' => __( 'Greyscale', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'greyscale_info',
			[
				// 'label' => __( 'Greyscale images', 'mfn-opts' ),
        'raw' => __('Works only for images with link', 'mfn-opts'),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
        'condition' => [
          'link' => '',
        ],
			]
		);

		$this->add_control(
			'greyscale',
			[
				'label' => __( 'Greyscale images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
				),
				'default' => 0,
        'condition' => [
          'link!' => '',
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

		$settings['image'] = $settings['image']['url'];

		echo sc_photo_box( $settings, $settings['content'] );

	}

}
