<?php

class PAFE_ACF_Repeater_Sub_Field extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-acf-repeater-sub-field';
	}

	public function get_title() {
		return __( 'PAFE ACF Repeater Sub Field', 'pafe' );
	}

	public function get_icon() {
		return 'far fa-keyboard';
	}

	public function get_categories() {
		return [ 'pafe' ];
	}

	public function get_keywords() {
		return [ 'acf', 'repeater' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'pafe_acf_repeater_sub_field_section',
			[
				'label' => __( 'Sub Field', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_acf_repeater_sub_field_name',
			[
				'label' => __( 'Name', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'pafe_acf_repeater_sub_field_type',
			[
				'label' => __( 'Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'text' => __( 'Text', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
				],
			]
		);

		$this->add_responsive_control(
			'pafe_acf_repeater_sub_field_align',
			[
				'label' => __( 'Align', 'pafe' ),
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
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_acf_repeater_sub_field_typography',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}}',
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'text',
				]
			]
		);

		$this->add_control(
			'pafe_acf_repeater_sub_field_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'text',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => __( 'Max Width', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => __( 'Normal', 'elementor' ),
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => __( 'Opacity', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-image img',
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'elementor' ),
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => __( 'Opacity', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image:hover img' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .elementor-image:hover img',
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'transition-duration: {{SIZE}}s',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .elementor-image img',
				'separator' => 'before',
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elementor-image img',
				'condition' => [
					'pafe_acf_repeater_sub_field_type' => 'image',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$pafe_acf_repeater_sub_field_name = $settings['pafe_acf_repeater_sub_field_name'];
		$pafe_acf_repeater_sub_field_type = $settings['pafe_acf_repeater_sub_field_type'];

		if ( !empty( $pafe_acf_repeater_sub_field_name ) ) {
			if (function_exists('get_field')) :
					$inner_html = '';
					$post_id = get_the_ID();
					$pafe_acf_repeater_name = get_post_meta( $post_id, '_pafe_acf_repeater_name', true);
					$pafe_acf_repeater_preview_post_id = get_post_meta( $post_id, '_pafe_acf_repeater_preview_post_id', true);
					if (!empty($pafe_acf_repeater_name) && !empty($pafe_acf_repeater_preview_post_id)) {
						$pafe_acf_repeater_name = explode(',', $pafe_acf_repeater_name);
						$pafe_acf_repeater = get_field($pafe_acf_repeater_name[0], $pafe_acf_repeater_preview_post_id);
						if (!empty($pafe_acf_repeater)) {
							if (count($pafe_acf_repeater_name) == 1) {
								if (isset($pafe_acf_repeater[0][$pafe_acf_repeater_sub_field_name])) {
									$inner_html = $pafe_acf_repeater[0][$pafe_acf_repeater_sub_field_name];
								}
							} else {
								if (isset($pafe_acf_repeater[0][$pafe_acf_repeater_name[1]][0][$pafe_acf_repeater_sub_field_name])) {
									$inner_html = $pafe_acf_repeater[0][$pafe_acf_repeater_name[1]][0][$pafe_acf_repeater_sub_field_name];
								}
							}
	        			}

	        			if ($pafe_acf_repeater_sub_field_type == 'image') {
	        				$inner_html = '<div class="elementor-image"><img src="' . $inner_html['url'] . '"></div>';
	        			}
	        		}
				?>
					<div class="pafe-acf-repeater-sub-field" data-pafe-acf-repeater-sub-field="<?php echo $pafe_acf_repeater_sub_field_name; ?>" data-pafe-acf-repeater-sub-field-type="<?php echo $pafe_acf_repeater_sub_field_type; ?>"><?php echo $inner_html; ?></div>
        		<?php
        	endif;
		}

	}
}
