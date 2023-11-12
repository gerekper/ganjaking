<?php

class PAFE_Meta_Query extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-meta-query';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_meta_query_section',
			[
				'label' => __( 'PAFE Meta Query', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_meta_query_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_meta_query_relation',
			[
				'label' => __( 'relation', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'OR' => __( 'OR', 'pafe' ),
					'AND' => __( 'AND', 'pafe' ),
				],
				'default' => 'OR',
				'condition' => [
					'pafe_meta_query_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_meta_query_key',
			[
				'label' => __( 'key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'pafe_meta_query_compare',
			[
				'label' => __( 'compare', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'=' => '=',
					'!=' => '!=',
					'>' => '>',
					'>=' => '>=',
					'<' => '<',
					'<=' => '<=',
					'LIKE' => 'LIKE',
					'NOT LIKE' => 'NOT LIKE',
					'IN' => 'IN',
					'NOT IN' => 'NOT IN',
					'BETWEEN' => 'BETWEEN',
					'NOT BETWEEN' => 'NOT BETWEEN',
					'EXISTS' => 'NOW EXISTS',
				],
				'default' => '=',
			]
		);

		$element->add_control(
			'pafe_conditional_visibility_by_backend_list',
			[
				'type' => Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ pafe_conditional_visibility_roles_url_parameter }}} {{{ pafe_conditional_visibility_roles_custom_field_key }}} {{{ pafe_conditional_visibility_roles_custom_field_comparison_operators }}} {{{ pafe_conditional_visibility_roles_custom_field_value }}}',
				'condition' => [
					'pafe_meta_query_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_navigation_arrows_icon_enable'] ) ) {

			if ( $settings['pafe_navigation_arrows_icon_type'] == 'icon' && ! empty( $settings['pafe_navigation_arrows_icon_previous'] ) && ! empty( $settings['pafe_navigation_arrows_icon_next'] ) ) {

				$element->add_render_attribute( '_wrapper', [
					'class' => 'pafe-navigation-arrows-icon',
					'data-pafe-navigation-arrows-icon' => '',
					'data-pafe-navigation-arrows-icon-size' => $settings['pafe_navigation_arrows_icon_size']['size'] . $settings['pafe_navigation_arrows_icon_size']['unit'],
					'data-pafe-navigation-arrows-icon-opacity' => $settings['pafe_navigation_arrows_icon_opacity'] . $settings['pafe_navigation_arrows_icon_opacity'],
					'data-pafe-navigation-arrows-icon-position' => $settings['pafe_navigation_arrows_icon_position'],
					'data-pafe-navigation-arrows-icon-previous' => $settings['pafe_navigation_arrows_icon_previous'],
					'data-pafe-navigation-arrows-icon-next' => $settings['pafe_navigation_arrows_icon_next'],
				] );

			}

			if ( $settings['pafe_navigation_arrows_icon_type'] == 'image' && ! empty( $settings['pafe_navigation_arrows_icon_previous_image'] ) && ! empty( $settings['pafe_navigation_arrows_icon_next_image'] ) ) {

				$element->add_render_attribute( '_wrapper', [
					'class' => 'pafe-navigation-arrows-icon',
					'data-pafe-navigation-arrows-icon-image' => '',
					'data-pafe-navigation-arrows-icon-size' => $settings['pafe_navigation_arrows_icon_size']['size'] . $settings['pafe_navigation_arrows_icon_size']['unit'],
					'data-pafe-navigation-arrows-icon-opacity' => $settings['pafe_navigation_arrows_icon_opacity'] . $settings['pafe_navigation_arrows_icon_opacity'],
					'data-pafe-navigation-arrows-icon-position' => $settings['pafe_navigation_arrows_icon_position'],
					'data-pafe-navigation-arrows-icon-previous' => $settings['pafe_navigation_arrows_icon_previous_image']['url'],
					'data-pafe-navigation-arrows-icon-next' => $settings['pafe_navigation_arrows_icon_next_image']['url'],
				] );

			}

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/image-carousel/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/slides/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-slider-builder/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/media-carousel/section_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/testimonial-carousel/section_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
