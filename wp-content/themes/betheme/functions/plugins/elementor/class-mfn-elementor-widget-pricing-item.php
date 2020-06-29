<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Pricing_Item extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_pricing';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Pricing', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-dollar-sign';
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
			'price',
			[
				'label' => __( 'Price', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '99',
			]
		);

		$this->add_control(
			'currency',
			[
				'label' => __( 'Currency', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '$',
			]
		);

		$this->add_control(
			'currency_pos',
			[
				'label' => __( 'Currency position', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          '' => __('Left', 'mfn-opts'),
          'right' => __('Right', 'mfn-opts'),
        ),
        'defult' => '',
        'condition' => [
          'currency!' => '',
        ],
			]
		);

    $this->add_control(
      'period',
      [
        'label' => __( 'Period', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
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
			'subtitle',
			[
				'label' => __( 'Subtitle', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
        'default' => __( 'This is the subtitle', 'mfn-opts' ),
			]
		);

    $this->add_control(
      'content',
      [
        'label' => __( 'Content', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => "<ul>\n<li>This is the first line of description</li>\n<li>This is the second line of description</li>\n</ul>",
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
      'link_title',
      [
        'label' => __( 'Title', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'label_block' => true,
        'default' => __( 'Click here', 'mfn-opts' ),
      ]
    );

    $this->add_control(
      'icon',
      [
        'label' => __( 'Icon', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::ICONS,
        'label_block' => true,
        'condition' => [
          'link_title!' => '',
        ],
      ]
    );

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
        'condition' => [
          'link_title!' => '',
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
          'link!' => '',
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
      'featured',
      [
        'label' => __( 'Featured', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
        ],
        'default' => 0,
      ]
    );

    $this->add_control(
      'style',
      [
        'label' => __( 'Style', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
          'box'	=> __('Box', 'mfn-opts'),
          'label'	=> __('Table Label', 'mfn-opts'),
          'table'	=> __('Table', 'mfn-opts'),
        ],
        'default' => 'box',
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

		echo sc_pricing_item( $settings, $settings['content'] );

	}

}
