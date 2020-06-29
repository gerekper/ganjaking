<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Promo_Box extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_promo_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Promo box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'eicon-featured-image';
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
			'button_section',
			[
				'label' => __( 'Button', 'mfn-opts' ),
			]
		);

    $this->add_control(
      'btn_text',
      [
        'label' => __( 'Title', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
        'default' => __( 'Click here', 'mfn-opts' ),
      ]
    );

		$this->add_control(
			'btn_link',
			[
				'label' => __( 'Link', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
        'condition' => [
          'btn_text!' => '',
        ],
        'default' => '#',
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
          'btn_text!' => '',
          'btn_link!' => '',
        ],
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
      'position',
      [
        'label' => __( 'Image position', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          'left' 	=> __('Left', 'mfn-opts'),
          'right' => __('Right', 'mfn-opts'),
        ],
        'default' => 'left',
      ]
    );

    $this->add_control(
      'border',
      [
        'label' => __( 'Border right', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
        ],
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

		$settings['image'] = $settings['image']['url'];

		echo sc_promo_box( $settings, $settings['content'] );

	}

}
