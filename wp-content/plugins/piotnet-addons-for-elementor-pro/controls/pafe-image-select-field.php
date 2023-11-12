<?php

class PAFE_Image_Select_Field extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-image-select-field';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_image_select_field_section',
			[
				'label' => __( 'PAFE Image Select Field', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_image_select_field_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_image_select_field_id',
			[
				'label' => __( 'Image Select Field Custom ID', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'pafe_image_select_field_gallery',
			[
				'label' => __( 'Add Images', 'pafe' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'default' => [],
			]
		);

		$element->add_control(
			'pafe_image_select_field_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
			)
		);

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_image_select_field_typography',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .image_picker_selector .thumbnail p',
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_text_align',
			[
				'label' => __( 'Text Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .image_picker_selector .thumbnail p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_item_width',
			[
				'label' => __( 'Item Width (%)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 25,
				'min' => 1,
				'max' => 100,
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector li' => 'width: {{VALUE}}% !important;',
				],
			]
		);

		$columns_margin = is_rtl() ? '-{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};' : '-{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};';
		$columns_padding = is_rtl() ? '{{SIZE}}{{UNIT}} !important;' : '{{SIZE}}{{UNIT}} !important;';

		$element->add_responsive_control(
			'pafe_image_select_field_item_spacing',
			[
				'label' => __( 'Item Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector li' => 'padding:' . $columns_padding,
					'{{WRAPPER}} ul.thumbnails.image_picker_selector' => 'margin: ' . $columns_margin,
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_item_border_radius',
			[
				'label' => __( 'Item Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_image_border_radius',
			[
				'label' => __( 'Image Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .image_picker_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_image_padding',
			[
				'label' => __( 'Input Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .image_picker_image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_label_padding',
			[
				'label' => __( 'Input Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->start_controls_tabs('pafe_image_select_field_normal_active');

		$element->start_controls_tab(
			'pafe_image_select_field_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$element->add_control(
			'pafe_image_select_field_border_normal',
			[
				'label' => __( 'Item Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-style: {{VALUE}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_border_width_normal',
			[
				'label' => __( 'Item Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_image_select_field_border_normal!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_image_select_field_border_color_normal',
			[
				'label' => __( 'Item Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_image_select_field_border_normal!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_image_select_field_background_color_normal',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'background-color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'pafe_image_select_field_text_color_normal',
			[
				'label' => __( 'Text Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail p' => 'color: {{VALUE}};',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'pafe_image_select_field_active',
			[
				'label' => __( 'Active', 'elementor' ),
			]
		);

		$element->add_control(
			'pafe_image_select_field_border_active',
			[
				'label' => __( 'Item Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'border-style: {{VALUE}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_image_select_field_border_width_active',
			[
				'label' => __( 'Item Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_image_select_field_border_active!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_image_select_field_border_color_active',
			[
				'label' => __( 'Item Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_image_select_field_border_active!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_image_select_field_background_color_active',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'pafe_image_select_field_text_color_active',
			[
				'label' => __( 'Text Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected p' => 'color: {{VALUE}};',
				],
			]
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_image_select_field_enable'])) {
			if ( array_key_exists( 'pafe_image_select_field_list',$settings ) ) {
				$list = $settings['pafe_image_select_field_list'];	
				if( !empty($list[0]['pafe_image_select_field_id']) && !empty($list[0]['pafe_image_select_field_gallery']) ) {

					$element->add_render_attribute( '_wrapper', [
						'data-pafe-image-select-field' => json_encode($list),
					] );
				}
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_form_fields/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
