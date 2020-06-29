<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Our_Team extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_our_team';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Our team', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-user';
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
			'heading',
			[
				'label' => __( 'Heading', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'This is the heading', 'mfn-opts' ),
        'condition' => [
          'style!' => 'list',
        ],
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
				'default' => __( 'This is the title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'mfn-opts' ),
				'default' => __( 'This is the subtitle', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->end_controls_section();

    $this->start_controls_section(
      'description_section',
      [
        'label' => __( 'Description', 'mfn-opts' ),
      ]
    );

    $this->add_control(
      'phone',
      [
        'label' => __( 'Phone', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
        'default' => '+00 000 000 000',
      ]
    );

    $this->add_control(
      'content',
      [
        'label' => __( 'Content', 'mfn-opts' ),
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'type' => \Elementor\Controls_Manager::WYSIWYG,
      ]
    );

    $this->add_control(
      'email',
      [
        'label' => __( 'Email', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
				'default' => 'email@wordpress.org',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'social_section',
      [
        'label' => __( 'Social', 'mfn-opts' ),
      ]
    );

    $this->add_control(
      'facebook',
      [
        'label' => __( 'Facebook', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
				'default' => 'facebook.com',
      ]
    );

    $this->add_control(
      'twitter',
      [
        'label' => __( 'Twitter', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
				'default' => 'twitter.com',
      ]
    );

    $this->add_control(
      'linkedin',
      [
        'label' => __( 'LinkedIn', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
					'default' => 'linkedin.com',
      ]
    );

    $this->add_control(
      'vcard',
      [
        'label' => __( 'vCard', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
					'default' => 'vcard.com',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'other_section',
      [
        'label' => __( 'Other', 'mfn-opts' ),
      ]
    );

    $this->add_control(
      'blockquote',
      [
        'label' => __( 'Blockquote', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'This is the blockquote', 'mfn-opts' ),
      ]
    );

    $this->add_control(
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          'circle' => __('Circle', 'mfn-opts'),
          'vertical' => __('Vertical', 'mfn-opts'),
          'horizontal' => __('Horizontal', 'mfn-opts'),
          'list' => __('List (no inner section)', 'mfn-opts'),
				),
				'default' => 'vertical',
        'label_block' => true,
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

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		$settings['image'] = $settings['image']['url'];

    if( 'list' == $settings['style'] ){
      echo sc_our_team_list( $settings, $settings['content'] );
    } else {
      echo sc_our_team( $settings, $settings['content'] );
    }

	}

}
