<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Feature_List extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_feature_list';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Feature list', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-border-all';
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
			'columns',
			[
				'label' => __( 'Columns', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '3',
			]
		);

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'icon',
      [
        'label' => __( 'Icon', 'mfn-opts' ),
        'type' => \Elementor\Controls_Manager::ICONS,
        'default' => [
					'value' => 'far fa-star',
					'library' => 'solid',
				],
      ]
    );

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'List item', 'mfn-opts' ),
				'label_block' => true,
			]
		);

    $repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

    $repeater->add_control(
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
			'tabs',
			[
				'label' => __( 'Items', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'List item #1', 'mfn-opts' ),
						'icon' => [
    					'value' => 'far fa-star',
    					'library' => 'solid',
    				],
						'target' => 0,
					],
					[
            'title' => __( 'List item #2', 'mfn-opts' ),
            'icon' => [
              'value' => 'far fa-star',
              'library' => 'solid',
            ],
            'target' => 0,
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_feature_list( $settings );

	}

}
