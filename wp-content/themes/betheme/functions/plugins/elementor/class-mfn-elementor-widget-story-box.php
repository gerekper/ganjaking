<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Story_Box extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_story_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Story box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-image-box';
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
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          '' => __('Horizontal Image', 'mfn-opts'),
          'vertical' => __('Vertical Image', 'mfn-opts'),
        ),
        'default' => '',
        'condition' => [
          'image!' => '',
        ],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'mfn-opts' ),
				'default' => __( 'This is the heading', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
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

		echo sc_story_box( $settings, $settings['content'] );

	}

}
