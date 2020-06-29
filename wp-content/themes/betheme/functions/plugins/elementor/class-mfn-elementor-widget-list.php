<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_List extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_list';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • List', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-list';
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
 			'title',
 			[
 				'label' => __( 'Title', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::TEXT,
        'label_block'	=> true,
				'default' => __( 'This is the heading', 'mfn-opts' ),
 			]
 		);

 		$this->add_control(
 			'content',
 			[
 				'label' => __( 'Content', 'mfn-opts' ),
 				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing.',
 				'type' => \Elementor\Controls_Manager::WYSIWYG,
 			]
 		);

    $this->add_control(
 			'style',
 			[
 				'label' => __( 'Style', 'mfn-opts' ),
 				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          1 => __('With background', 'mfn-opts'),
          2 => __('Transparent', 'mfn-opts'),
          3 => __('Vertical', 'mfn-opts'),
          4 => __('Ordered list', 'mfn-opts'),
        ),
        'default'	=> 1,
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

		$settings['icon'] = $settings['icon']['value'];
		$settings['image'] = $settings['image']['url'];

		echo sc_list( $settings, $settings['content'] );

	}

}
