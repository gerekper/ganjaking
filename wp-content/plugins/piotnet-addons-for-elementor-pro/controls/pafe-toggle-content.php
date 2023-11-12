<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Toggle_Content extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-toggle-content';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_toggle_content_section',
			[
				'label' => __( 'PAFE Toggle Content', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_toggle_content_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_toggle_content_type',
			[
				'label' => __( 'Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'content' => __( 'Content', 'pafe' ),
					'trigger-open' => __( 'Trigger Open', 'pafe' ),
					'trigger-close' => __( 'Trigger Close', 'pafe' ),
				],
				'default' => 'content',
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_toggle_content_height_type',
			[
				'label' => __( 'Content Height Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'1' => __( '> 0px', 'pafe' ),
					'2' => __( '0px', 'pafe' ),
				],
				'default' => '1',
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_toggle_content_height',
			[
				'label' => __( 'Content Height', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Note: Value > 0px. E.g 10px, 100px', 'pafe' ),
				'selectors' => [
					'{{WRAPPER}}' => 'height: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
					'pafe_toggle_content_type' => 'content',
					'pafe_toggle_content_height_type' => '1',
				],
			]
		);

		$element->add_control(
			'pafe_toggle_content_speed',
			[
				'label' => __( 'Speed', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 100, 1000, slow, fast' ),
				'default' => 400,
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
					'pafe_toggle_content_type' => 'content',
				],
			]
		);

		$element->add_control(
			'pafe_toggle_content_easing',
			[
				'label' => __( 'Easing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g swing, linear' ),
				'default' => 'swing',
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
					'pafe_toggle_content_type' => 'content',
				],
			]
		);

		$element->add_control(
			'pafe_toggle_content_slug',
			[
				'label' => __( 'Slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Set the same slug for the content and trigger. E.g 1,2,3 or widget-1', 'pafe' ),
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_toggle_content_slug_post_id',
			[
				'label' => __( 'Add Post ID to Slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'description' => __( 'You should enable this if you want Toggle Content feature in a loop', 'pafe' ),
				'condition' => [
					'pafe_toggle_content_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function after_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_toggle_content_enable'] ) ) {

			$slug = $settings['pafe_toggle_content_slug'];

			if (!empty($settings['pafe_toggle_content_slug_post_id'])) {
				$slug = $settings['pafe_toggle_content_slug'] . '-' . get_the_ID();
			}

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-toggle-content-type' => $settings['pafe_toggle_content_type'],
				'data-pafe-toggle-content-slug' => $slug,
			] );

			if ( $settings['pafe_toggle_content_type'] == 'content' ) {

				$speed = 400;
				if (!empty($settings['pafe_toggle_content_speed'])) {
					$speed = $settings['pafe_toggle_content_speed'];
				}

				$easing = 'swing';
				if (!empty($settings['pafe_toggle_content_easing'])) {
					$easing = $settings['pafe_toggle_content_easing'];
				}

				if ( $settings['pafe_toggle_content_height_type'] == '1' ) {
					$element->add_render_attribute( '_wrapper', [
						'data-pafe-toggle-content-height' => !empty($settings['pafe_toggle_content_height']) ? $settings['pafe_toggle_content_height'] : '',
						'data-pafe-toggle-content-height-tablet' => !empty($settings['pafe_toggle_content_height_tablet']) ? $settings['pafe_toggle_content_height_tablet'] : '',
						'data-pafe-toggle-content-height-mobile' => !empty($settings['pafe_toggle_content_height_mobile']) ? $settings['pafe_toggle_content_height_mobile'] : '',
						'data-pafe-toggle-content-speed' => $settings['pafe_toggle_content_speed'],
						'data-pafe-toggle-content-easing' => $settings['pafe_toggle_content_easing'],
					] );
				}

				if ( $settings['pafe_toggle_content_height_type'] == '2' ) {
					$element->add_render_attribute( '_wrapper', [
						'style' => 'height:0px; overflow:hidden;',
						'data-pafe-toggle-content-height' => '0px',
						'data-pafe-toggle-content-height-tablet' => '0px',
						'data-pafe-toggle-content-height-mobile' => '0px',
						'data-pafe-toggle-content-speed' => $settings['pafe_toggle_content_speed'],
						'data-pafe-toggle-content-easing' => $settings['pafe_toggle_content_easing'],
					] );
				}

			}

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'after_render_element'], 10, 1 );
	}

}
