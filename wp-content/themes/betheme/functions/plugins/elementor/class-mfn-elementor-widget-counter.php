<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Counter extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-counter', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-counter-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-counter' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_counter';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Counter', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-sort-numeric-up-alt';
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
        'label_block'	=> true,
				'default' => __( 'This is the heading', 'mfn-opts' ),
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
			'color',
			[
				'label' => __( 'Icon color', 'mfn-opts' ),
				'description' => __( 'or', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
        'condition' => [
          'icon[value]!' => '',
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
			'prefix',
			[
				'label' => __( 'Prefix', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

    $this->add_control(
			'number',
			[
				'label' => __( 'Number', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 99,
			]
		);

    $this->add_control(
			'label',
			[
				'label' => __( 'Label', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
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
			'type',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' 	=> array(
          'horizontal' => __('Horizontal', 'mfn-opts'),
          'vertical' => __('Vertical', 'mfn-opts'),
        ),
				'default' => 'vertical',
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

		echo sc_counter( $settings );

	}

}
