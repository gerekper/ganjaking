<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Clients extends \Elementor\Widget_Base {

  /**
	 * Get script dependences
	 */

  public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_register_script( 'mfn-clients', get_theme_file_uri( '/functions/plugins/elementor/assets/widget-clients-preview.js' ), [ 'elementor-frontend' ], MFN_THEME_VERSION, true );
			return [ 'mfn-clients' ];
		}

		return [];
	}

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_clients';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Clients', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-users';
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
			'in_row',
			[
				'label' => __( 'Items in Row', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => 6,
			]
		);

    $this->add_control(
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          '' 	=> __('Default', 'mfn-opts'),
          'tiles' => __('Tiles', 'mfn-opts'),
				),
				'default' => '',
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
				'options'	=> mfn_get_categories('client-types'),
				'default' => "",
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order by', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' 	=> array(
					'date' => __('Date', 'mfn-opts'),
          'menu_order' => __('Menu order', 'mfn-opts'),
					'title' => __('Title', 'mfn-opts'),
					'rand' => __('Random', 'mfn-opts'),
				),
				'default' => "menu_order",
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
			'greyscale',
			[
				'label' => __( 'Greyscale Images', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options'	=> array(
          0 => __('No', 'mfn-opts'),
          1 => __('Yes', 'mfn-opts'),
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

		echo sc_clients( $settings );

	}

}
