<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Icon_Box extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_icon_box';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Icon box', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'far fa-star';
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
        'label_block'	=> true,
 			]
 		);

 		$this->add_control(
 			'title_tag',
 			[
 				'label' => __( 'Title', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
        ),
        'default'	=> 'h4'
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

    $this->end_controls_section();

 		$this->start_controls_section(
 			'icon_section',
 			[
 				'label' => __( 'Icon', 'mfn-opts' ),
 			]
 		);

 		$this->add_control(
 			'icon',
 			[
 				'label' => __( 'Icon', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-star',
					'library' => 'regular',
				],
         'condition' => [
           'image[url]' => '',
         ],
 			]
 		);

     $this->add_control(
 			'image',
 			[
 				'label' => __( 'Image', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::MEDIA,
         'condition' => [
           'icon[value]' => '',
         ],
 			]
 		);

    $this->add_control(
 			'icon_position',
 			[
 				'label' => __( 'Icon/Image position', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          'left' => __('Left', 'mfn-opts'),
          'top'	=> __('Top', 'mfn-opts'),
        ),
        'default'	=> 'top',
 			]
 		);

    $this->add_control(
 			'border',
 			[
 				'label' => __( 'Border right', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
        ),
        'default'	=> 0,
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

    $this->add_control(
      'class',
      [
        'label' => __( 'Class', 'mfn-opts' ),
        'description' => __( 'This option is useful when you want to use <b>scroll</b>', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
      ]
    );

 		$this->end_controls_section();

 	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		$settings['icon'] = $settings['icon']['value'];
		$settings['image'] = $settings['image']['url'];

		echo sc_icon_box( $settings, $settings['content'] );

	}

}
