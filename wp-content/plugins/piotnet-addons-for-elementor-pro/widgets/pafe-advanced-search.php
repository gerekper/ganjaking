<?php

class PAFE_Advanced_Search extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-advanced-search';
	}

	public function get_title() {
		return __( 'PAFE Advanced Search', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-search-bold';
	}

	public function get_categories() {
		return [ 'pafe' ];
	}

	public function get_keywords() {
		return [ 'search'];
	}

	public function get_script_depends() {
		return [ 
			'pafe-widget'
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-widget-style'
		];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'pafe_advanced_search',
				[
					'label' => __( 'Settings', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->start_controls_tabs(
					'pafe_advanced_search_setting'
				);

			$this->start_controls_tab(
				'pafe_advanced_search_setting_input',
				[
					'label' => __( 'Input Setting', 'pafe' ),
				]
			);


				$post_types = get_post_types( [], 'objects' );
				$post_types_array = array();
				$taxonomy = array();
				foreach ( $post_types as $post_type ) {
					$post_types_array[$post_type->name] = $post_type->label;
					$taxonomy_of_post_type = get_object_taxonomies( $post_type->name, 'names' );
					$post_type_name = $post_type->name;
					if (!empty($taxonomy_of_post_type) && $post_type_name != 'nav_menu_item' && $post_type_name != 'elementor_library' && $post_type_name != 'elementor_font' ) {
						if ($post_type_name == 'post') {
							$taxonomy_of_post_type = array_diff( $taxonomy_of_post_type, ["post_format"] );
						}
						$taxonomy[$post_type_name] = $taxonomy_of_post_type;
					}
				}

			    $taxonomy_array = array();
			    foreach ($taxonomy as $key => $value) {
			    	foreach ($value as $key_item => $value_item) {
			    		$taxonomy_array[$value_item . '|' . $key] = $value_item . ' - ' . $key;
			    	}
			    }

			    $this->add_control(
					'pafe_advanced_search_taxonomy_enable',
					[
						'label' => __( 'Enable Taxonomy Filter', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Show', 'pafe' ),
						'label_off' => __( 'Hide', 'pafe' ),
						'return_value' => 'yes',
						'default' => 'yes',
					]
				);

				$this->add_control(
					'pafe_advanced_search_taxonomy',
					[
						'label' => __( 'Taxonomy Select', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $taxonomy_array,
						'default' => 'category|post',
						'condition' => [
							'pafe_advanced_search_taxonomy_enable' => 'yes',
						]	
					]
				);

				$this->add_control(
					'pafe_advanced_search_button_enable',
					[
						'label' => __( 'Enable Search Button', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Show', 'pafe' ),
						'label_off' => __( 'Hide', 'pafe' ),
						'return_value' => 'block',
						'default' => 'block',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__submit' => 'display: {{option}};',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_button_type',
					[
						'label' => __( 'Search Button Type', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => 'text',
						'options' => [
							'text'  => __( 'Text', 'pafe' ),
							'icon' => __( 'Icon', 'pafe' ),
						],
						'condition' => [
							'pafe_advanced_search_button_enable' => 'block',
						]
					]
				);

				$this->add_control(
					'pafe_advanced_search_button_text',
					[
						'label' => __( 'Button Text', 'pafe' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __( 'Search', 'pafe' ),
						'condition' => [
							'pafe_advanced_search_button_type' => 'text',
						]
					]
				);


				$this->add_control(
					'pafe_advanced_search_input_icon_enable',
					[
						'label' => __( 'Enable Icon', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Show', 'pafe' ),
						'label_off' => __( 'Hide', 'pafe' ),
						'return_value' => 'block',
						'default' => 'block',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search_form-input-icon' => 'display: {{option}};',
						],
					]
				);

			$this->end_controls_tab();
			$this->start_controls_tab(
				'pafe_advanced_search_setting_result',
				[
					'label' => __( 'Result Setting', 'pafe' ),
				]
			);

				$this->add_control(
					'pafe_advanced_search_result_pagination_posts_per_page',
					[
						'label' => __( 'Posts per Page', 'pafe' ),
						'type' => \Elementor\Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 50,
						'step' => 1,
						'default' => 4,
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_thumbnail_enable',
					[
						'label' => __( 'Enable Thumbnail', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Show', 'pafe' ),
						'label_off' => __( 'Hide', 'pafe' ),
						'return_value' => 'block',
						'default' => 'block',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-thumbnail' => 'display: {{option}};',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_footer_enable',
					[
						'label' => __( 'Enable Results Footer', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Show', 'pafe' ),
						'label_off' => __( 'Hide', 'pafe' ),
						'return_value' => 'flex',
						'default' => 'flex',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-footer' => 'display: {{option}};',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_footer_text',
					[
						'label' => __( 'Footer Text', 'pafe' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __( 'See all results', 'pafe' ),
						'condition' => [
							'pafe_advanced_search_result_footer_enable' => 'flex',
						]
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_no_post_message',
					[
						'label' => __( 'No Result Message', 'pafe' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __( 'No result found', 'pafe' ),
					]
				);

			$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section(); 

		$this->start_controls_section(
			'pafe_advanced_search_input_style',
			[
				'label' => __( 'Input Form', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			 $this->add_responsive_control(
				'pafe_advanced_search_width',
				[
					'label' => __( 'Form Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 50,
							'max' => 2000,
							'step' => 10,
						],

						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			); 

		    $this->add_responsive_control(
				'pafe_advanced_search_input_style_keywords_width',
				[
					'label' => __( 'Keywords Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 50,
							'max' => 1000,
							'step' => 10,
						],

						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 66,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-wrapper' => 'width: {{SIZE}}{{UNIT}};',
					],

					'condition' => [
						'pafe_advanced_search_taxonomy_enable' => 'yes',
					]
				]
			); 

		    $this->add_responsive_control(
				'pafe_advanced_search_input_style_terms_width',
				[
					'label' => __( 'Terms Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 50,
							'max' => 1000,
							'step' => 10,
						],

						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					
					'default' => [
						'unit' => '%',
						'size' => 32,
					],
					
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search-cat__wrapper' => 'width: {{SIZE}}{{UNIT}};',
					],
					
					'condition' => [
						'pafe_advanced_search_taxonomy_enable' => 'yes',
					]
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_icon',
				[
					'label' => __( 'Search icon', 'pafe' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'fa fa-search',
						'library' => 'solid',
					],

					'condition' => [
						'pafe_advanced_search_input_icon_enable' => 'block',
					]
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_placeholder',
				[
					'label' => __( 'Place Holder', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Enter Keywords', 'pafe' ),
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'label' => __( 'Keywords Typography', 'pafe' ),
					'name' => 'pafe_advanced_search_input_style_normal_keywords_typography',
					'selector' => '{{WRAPPER}} .pafe-advanced-search__input-typo',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_normal_background_color',
				[
					'label' => __( 'Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],

					'default' => '#fff',
					'selectors' => [
						'{{WRAPPER}} .pafe_advanced_search__select-items' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .pafe-advanced-search_form-input' => 'background-color: {{VALUE}}',
					],
				]
			);


			$this->add_control(
				'pafe_advanced_search_input_style_normal_text_color',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#77818c',
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-field' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pafe-advanced-search_form-input-field::placeholder' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pafe_advanced_search__select-selected' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pafe-advanced-search-cat__inner' => 'border-color: {{VALUE}}',
						'{{WRAPPER}} .pafe-advanced-search_form-input-icon i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pafe_advanced_search__categories-select-icon' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pafe_advanced_search__select-option' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_normal_border_color',
				[
					'label' => __( 'Border Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#e1e5eb',
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'pafe_advanced_search_input_style_normal_box_shadow',
					'label' => __( 'Box Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-advanced-search_form-input',
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_normal_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_normal_margin',
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_normal_border_width',
				[
					'label' => __( 'Border Width', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_normal_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_advanced_search_keywords_style',
			[
				'label' => __( 'Keyword Field', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'pafe_advanced_search_input_keywords_style_border_color',
				[
					'label' => __( 'Border Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-wrapper' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'pafe_advanced_search_input_keywords_style_box_shadow',
					'label' => __( 'Box Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-advanced-search_form-input-wrapper',
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_keywords_style_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_keywords_style_margin',
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_keywords_style_border_width',
				[
					'label' => __( 'Border Width', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-wrapper' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_keywords_style_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search_form-input-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section(); 

		$this->start_controls_section(
			'pafe_advanced_search_input_style_term-selection',
			[
				'label' => __( 'Term Selection', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_advanced_search_taxonomy_enable' => 'yes',
				]
			]
		);
			$this->start_controls_tabs(
					'pafe_advanced_search_input_style_term-selection-tabs'
				);

				$this->start_controls_tab(
					'pafe_advanced_search_input_style_term-selected',
					[
						'label' => __( 'Term Selected', 'pafe' ),
					]
				);

					$this->add_control(
						'pafe_advanced_search_input_style_term-selected_border_color',
						[
							'label' => __( 'Border Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search-cat__wrapper' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'pafe_advanced_search_input_style_term-selected__box_shadow',
							'label' => __( 'Box Shadow', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-advanced-search-cat__wrapper',
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-selected_padding',
						[
							'label' => __( 'Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search-cat__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-selected_margin',
						[
							'label' => __( 'Margin', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search-cat__wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-selected_border_width',
						[
							'label' => __( 'Border Width', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search-cat__wrapper' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-selected_border_radius',
						[
							'label' => __( 'Border Radius', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search-cat__wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();
				
				$this->start_controls_tab(
					'pafe_advanced_search_input_style_term-options',
					[
						'label' => __( 'Term Options', 'pafe' ),
					]
				);

					$this->add_control(
						'pafe_advanced_search_input_style_term-options_hover',
						[
							'label' => __( 'Hover Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'selectors' => [
								'{{WRAPPER}} .pafe_advanced_search__select-option:hover' => 'background-color: {{VALUE}}',
								'{{WRAPPER}} .pafe_advanced_search__same-as-selected-option' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'pafe_advanced_search_input_style_term-options_border_color',
						[
							'label' => __( 'Border Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'selectors' => [
								'{{WRAPPER}} .pafe_advanced_search__select-items' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'pafe_advanced_search_input_style_term-options__box_shadow',
							'label' => __( 'Box Shadow', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe_advanced_search__select-items',
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-options_padding',
						[
							'label' => __( 'Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe_advanced_search__select-items' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-options_margin',
						[
							'label' => __( 'Margin', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe_advanced_search__select-items' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-options_border_width',
						[
							'label' => __( 'Border Width', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe_advanced_search__select-items' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_input_style_term-options_border_radius',
						[
							'label' => __( 'Border Radius', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe_advanced_search__select-items' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();
			


		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_advanced_search_input_style_button',
			[
				'label' => __( 'Submit Button', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_advanced_search_button_enable' => 'block',
				]
			]
		);
			$this->add_control(
				'pafe_advanced_search_submit_button_icon',
				[
					'label' => __( 'Button Icon', 'pafe' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => [
						'value' => 'fa fa-search',
						'library' => 'solid',
					],

					'condition' => [
						'pafe_advanced_search_button_type' => 'icon',
					]
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_submit_button_icon_size',
				[
					'label' => __( 'Icon Size', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 16,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit i' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_advanced_search_button_type' => 'icon',
					]
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'label' => __( 'Button Typography', 'pafe' ),
					'name' => 'pafe_advanced_search_input_style_button_typography',
					'selector' => '{{WRAPPER}} .pafe-advanced-search__submit',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
					'condition' => [
						'pafe_advanced_search_button_type' => 'text',
					]
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_button_background-color',
				[
					'label' => __( 'Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_button_text-color',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],

					'default' => '#ffffff',
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'color: {{VALUE}}',
					],

					'condition' => [
						'pafe_advanced_search_button_type' => 'text',
					]
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_button_icon-color',
				[
					'label' => __( 'Icon Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],

					'default' => '#ffffff',
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit i' => 'color: {{VALUE}}',
					],

					'condition' => [
						'pafe_advanced_search_button_type' => 'icon',
					]
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_button_hover_background-color',
				[
					'label' => __( 'Hover Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#0D96D1',
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit:hover' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_button_hover_text-color',
				[
					'label' => __( 'Hover Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#CECECE',
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit:hover' => 'color: {{VALUE}}',
					],

					'condition' => [
						'pafe_advanced_search_button_type' => 'text',
					]
				]
			);

			$this->add_control(
				'pafe_advanced_search_input_style_button_border_color',
				[
					'label' => __( 'Border Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'border-color: {{VALUE}}',
					],

					'condition' => [
						'pafe_advanced_search_button_type' => 'text',
					]
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'pafe_advanced_search_input_style_button__box_shadow',
					'label' => __( 'Box Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-advanced-search__submit',
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_button_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_button_margin',
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_button_border_width',
				[
					'label' => __( 'Border Width', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_input_style_button_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_advanced_search_result_area_style',
			[
				'label' => __( 'Result Area', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

				$this->add_responsive_control(
					'pafe_advanced_search_result_style_area__width',
					[
						'label' => __( 'Width', 'pafe' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [

							'px' => [
								'min' => 200,
								'max' => 1000,
							],
						],

						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_style_area_background-color',
					[
						'label' => __( 'Background Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#ffffff',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_style_area_border_color',
					[
						'label' => __( 'Border Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#e1e5eb',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'border-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'pafe_advanced_search_result_style_area_background__box_shadow',
						'label' => __( 'Box Shadow', 'pafe' ),
						'selector' => '{{WRAPPER}} .pafe-advanced-search .show',
					]
				);

				$this->add_responsive_control(
					'pafe_advanced_search_result_style_area_background_padding',
					[
						'label' => __( 'Padding', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'pafe_advanced_search_result_style_area_background_margin',
					[
						'label' => __( 'Margin', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'pafe_advanced_search_result_style_area_background_border_width',
					[
						'label' => __( 'Border Width', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'pafe_advanced_search_result_style_area_background_border_radius',
					[
						'label' => __( 'Border Radius', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .show' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_advanced_search_result_pagination_style',
				[
					'label' => __( 'Result Pagination', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_control(
					'pafe_advanced_search_result_pagination_style_text_align',
					[
						'label' => __( 'Alignment', 'pafe' ),
						'type' => \Elementor\Controls_Manager::CHOOSE,
						'options' => [
							'flex-start' => [
								'title' => __( 'Left', 'pafe' ),
								'icon' => 'eicon-text-align-left',
								'value' => '-webkit-left'
							],
							'center' => [
								'title' => __( 'Center', 'pafe' ),
								'icon' => 'eicon-text-align-center',
								'value' => '-webkit-center'
							],
							'flex-end' => [
								'title' => __( 'Right', 'pafe' ),
								'icon' => 'eicon-text-align-right',
								'value' => '-webkit-right'
							],
						],
						'default' => 'flex-end',
						'toggle' => true,
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .pafe_pagination' => 'justify-content: {{options}}',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_pagination_style_background-color',
					[
						'label' => __( 'Background Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#ddd',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .page-link' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_pagination_style_text_color',
					[
						'label' => __( 'Number Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#77818c',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .page-link' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_pagination_style_active_background-color',
					[
						'label' => __( 'Active Background Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#6ec1e4',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .page-link-active' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_pagination_style_active_text_color',
					[
						'label' => __( 'Active Number Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#fff',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .page-link-active' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Number Typography', 'pafe' ),
						'name' => 'pafe_advanced_search_result_pagination_style_text_typography',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                        ],
						'selector' => '{{WRAPPER}} .pafe-advanced-search .page-link',
					]
				);
					
				$this->add_responsive_control(
						'pafe_advanced_search_result_pagination_style_padding',
						[
							'label' => __( 'Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search .pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
				);

				$this->add_responsive_control(
					'pafe_advanced_search_result_pagination_style_margin',
					[
						'label' => __( 'Margin', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search .pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs(
					'pafe_advanced_search_result_pagination_style_page'
				);

				$this->start_controls_tab(
					'pafe_advanced_search_result_pagination_style_page-item',
					[
						'label' => __( 'Page Item', 'pafe' ),
					]
				);

					$this->add_responsive_control(
						'pafe_advanced_search_result_pagination_style_page_item_padding',
						[
							'label' => __( 'Item Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search .page-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								'{{WRAPPER}} .pafe-advanced-search .pagination' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_pagination_style_number_padding',
						[
							'label' => __( 'Number Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search .page-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_pagination_style_border_radius',
						[
							'label' => __( 'Border Radius', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search .pagination .page-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();
				$this->end_controls_tabS();


			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_advanced_search_result_item_style',
				[
					'label' => __( 'Result Item', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
				$this->start_controls_tabs(
					'pafe_advanced_search_result_style_item-tabs'
				);

				$this->start_controls_tab(
					'pafe_advanced_search_result_style_item-normal',
					[
						'label' => __( 'Normal', 'pafe' ),
					]
				);

					$this->add_control(
						'pafe_advanced_search_result_style_item_background-color',
						[
							'label' => __( 'Background Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'default' => '#ffffff',
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'pafe_advanced_search_result_style_item_border_color',
						[
							'label' => __( 'Border Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '#e1e5eb',
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'pafe_advanced_search_result_item_area_background__box_shadow',
							'label' => __( 'Box Shadow', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-advanced-search__results-item',
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_background_padding',
						[
							'label' => __( 'Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_background_margin',
						[
							'label' => __( 'Margin', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_background_border_width',
						[
							'label' => __( 'Border Width', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_background_border_radius',
						[
							'label' => __( 'Border Radius', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();
				$this->start_controls_tab(
					'pafe_advanced_search_result_style_item-hover',
					[
						'label' => __( 'Hover', 'pafe' ),
					]
				);

					$this->add_control(
						'pafe_advanced_search_result_style_item_hover_background-color',
						[
							'label' => __( 'Background Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'pafe_advanced_search_result_style_item_hover_border_color',
						[
							'label' => __( 'Border Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                            ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item:hover' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'pafe_advanced_search_result_item_hover_area_background__box_shadow',
							'label' => __( 'Box Shadow', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-advanced-search__results-item:hover',
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_hover_background_padding',
						[
							'label' => __( 'Padding', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_hover_background_margin',
						[
							'label' => __( 'Margin', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_hover_background_border_width',
						[
							'label' => __( 'Border Width', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'pafe_advanced_search_result_style_item_hover_background_border_radius',
						[
							'label' => __( 'Border Radius', 'pafe' ),
							'type' => Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .pafe-advanced-search__results-item:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();
				$this->end_controls_tabs();

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'label' => __( 'No Result Typography', 'pafe' ),
							'name' => 'pafe_advanced_search_result_item_no_result_typography',
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                            ],
							'selector' => '{{WRAPPER}} .pafe-advanced-search__results-no-result-message',
						]
					);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_advanced_search_result_thumbnail_style',
			[
				'label' => __( 'Result Thumbnail', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_advanced_search_input_icon_enable' => 'block',
				]
			]
		);

			$this->add_control(
				'pafe_advanced_search_result_thumbnail_ratio',
				[
					'label' => __( 'Ratio', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '75%',
					'options' => [
						'75%'  => __( '4:3', 'pafe' ),
						'66.666666%' => __( '3:2', 'pafe' ),
						'70.72135785%' => __( '1.414:1 Silver Ratio', 'pafe' ),
						'61.804697157%' => __( '1.618:1 Golden Ratio', 'pafe' ),
						'52.356020942%' => __( '1.91 : 1 OGP Image Ratio', 'pafe' ),
						'56.25%%' => __( '16 : 9 HDTV', 'pafe' ),
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__results-item-thumbnail:after' => 'padding-top: {{option}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_result_thumbnail_width',
				[
					'label' => __( 'Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ '%' ],
					'range' => [

						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 25,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__results-item-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_result_thumbnail_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__results-item-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .pafe-advanced-search__results-item-thumbnail-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_advanced_search_result_title_style',
			[
				'label' => __( 'Result Title', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
					'pafe_advanced_search_result_title_style_tabs'
				);

			$this->start_controls_tab(
				'pafe_advanced_search_result_title_style_normal',
				[
					'label' => __( 'Normal', 'pafe' ),
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Typography', 'pafe' ),
						'name' => 'pafe_advanced_search_result_title_style_normal_typography',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                        ],
						'selector' => '{{WRAPPER}} .pafe-advanced-search__results-item-title',
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_title_style_normal_color',
					[
						'label' => __( 'Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#162b40',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-title' => 'color: {{VALUE}}',
						],
					]
				);
				
				$this->add_responsive_control(
					'ppafe_advanced_search_result_title_style_normal_margin',
					[
						'label' => __( 'Margin', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'pafe_advanced_search_result_title_style_hover',
				[
					'label' => __( 'Hover', 'pafe' ),
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Typography', 'pafe' ),
						'name' => 'pafe_advanced_search_result_title_style_hover_typography',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                        ],
						'selector' => '{{WRAPPER}} .pafe-advanced-search__results-item-title:hover',
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_title_style_hover_color',
					[
						'label' => __( 'Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#6ec1e4',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-title:hover' => 'color: {{VALUE}}',
						],
					]
				);
				
				$this->add_responsive_control(
					'ppafe_advanced_search_result_title_style_hover_margin',
					[
						'label' => __( 'Margin', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-title:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_advanced_search_result_content_style',
			[
				'label' => __( 'Result Content', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
					'pafe_advanced_search_result_content_style_tabs'
				);

			$this->start_controls_tab(
				'pafe_advanced_search_result_content_style_normal',
				[
					'label' => __( 'Normal', 'pafe' ),
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Typography', 'pafe' ),
						'name' => 'pafe_advanced_search_result_content_style_normal_typography',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                        ],
						'selector' => '{{WRAPPER}} .pafe-advanced-search__results-item-content,.pafe-single-price h2',
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_content_style_normal_color',
					[
						'label' => __( 'Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#6b7e92',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-content,.pafe-single-price>h2' => 'color: {{VALUE}}',
						],
					]
				);
				
				$this->add_responsive_control(
					'ppafe_advanced_search_result_content_style_normal_margin',
					[
						'label' => __( 'Margin', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-content,.pafe-single-price h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'pafe_advanced_search_result_content_style_hover',
				[
					'label' => __( 'Hover', 'pafe' ),
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Typography', 'pafe' ),
						'name' => 'pafe_advanced_search_result_content_style_hover_typography',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                        ],
						'selector' => '{{WRAPPER}} .pafe-advanced-search__results-item-content:hover',
					]
				);

				$this->add_control(
					'pafe_advanced_search_result_content_style_hover_color',
					[
						'label' => __( 'Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-content:hover' => 'color: {{VALUE}}',
						],
					]
				);
				
				$this->add_responsive_control(
					'ppafe_advanced_search_result_content_style_hover_margin',
					[
						'label' => __( 'Margin', 'pafe' ),
						'type' => Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-item-content:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_advanced_search_result_footer_style',
			[
				'label' => __( 'Result Footer', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_advanced_search_result_footer_enable' => 'flex',
				]
			]
		);
			$this->add_control(
				'pafe_advanced_search_result_footer_text_align',
				[
					'label' => __( 'Alignment', 'pafe' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => __( 'Left', 'pafe' ),
							'icon' => 'eicon-text-align-left',
							'value' => '-webkit-left'
						],
						'center' => [
							'title' => __( 'Center', 'pafe' ),
							'icon' => 'eicon-text-align-center',
							'value' => '-webkit-center'
						],
						'flex-end' => [
							'title' => __( 'Right', 'pafe' ),
							'icon' => 'eicon-text-align-right',
							'value' => '-webkit-right'
						],
					],
					'default' => 'flex-start',
					'toggle' => true,
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__results-footer' => 'justify-content: {{options}}',
					],
				]
			);

			$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'label' => __( 'Typography', 'pafe' ),
						'name' => 'pafe_advanced_search_result_footer_typography',
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                        ],
						'selector' => '{{WRAPPER}} .pafe-advanced-search__results-full',
					]
			);

			$this->add_control(
					'pafe_advanced_search_result_footer_color',
					[
						'label' => __( 'Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#6ec1e4',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-full' => 'color: {{VALUE}}',
						],
					]
			);

			$this->add_control(
					'pafe_advanced_search_result_footer_color_hover',
					[
						'label' => __( 'Hover Color', 'pafe' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'global' => [
                            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                        ],
						'default' => '#0D96D1',
						'selectors' => [
							'{{WRAPPER}} .pafe-advanced-search__results-full:hover' => 'color: {{VALUE}}',
						],
					]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_result_footer_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__results-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_advanced_search_result_footer_margin',
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-advanced-search__results-footer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}



	protected function render() {
		$settings = $this->get_settings_for_display();

		?>
		<div class="pafe-advanced-search">
			<form class="pafe-advanced-search_form" role="search" method="get" class="search-form" action=" <?php echo get_site_url();  ?>">
				<div class="pafe-advanced-search_form-input">
					<div class="pafe-advanced-search_form-input-wrapper">
						<div class="pafe-advanced-search_form-input-icon ">
							<?php \Elementor\Icons_Manager::render_icon( $settings['pafe_advanced_search_input_style_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input type="text" name="s" data-pafe-advanced-search-input placeholder="<?php echo $settings['pafe_advanced_search_input_style_placeholder'] ?>" class="pafe-advanced-search_form-input-field pafe-advanced-search__input-typo" autocomplete="off">
					</div>
					<?php if ($settings['pafe_advanced_search_taxonomy_enable'] == 'yes'): ?>
					<?php 
					$taxonomy_posttype = explode('|',$settings['pafe_advanced_search_taxonomy']);
					$taxonomy = $taxonomy_posttype[0];
					$taxonomy_details = get_taxonomy( $taxonomy );
					$post_type = $taxonomy_posttype[1];
					$terms = get_terms( $taxonomy, array(
					    'hide_empty' => true,
					));
					?>
					<input type="hidden" name="post_type" value="<?php echo $post_type; ?>">
					<input type="hidden" name="taxonomy" value="<?php echo $taxonomy; ?>">
					<input type="hidden" name="terms" data-pafe-advanced-search-terms value="">

					<div class="pafe-advanced-search-cat__wrapper">	
						<div class="pafe-advanced-search-cat__inner">
							<select  class="data-pafe-advanced-search-term-select" data-pafe-advanced-search-term-select data-pafe-advanced-search-taxonomy ="<?php echo $taxonomy; ?>">
									<option value="all">All <?php print_r($taxonomy_details->labels->name)  ?></option>
								<?php
								foreach ($terms as $key => $value) {
								 	echo "<option value='".$value->slug. "|" . $taxonomy . "'>".$value->name."</option>";
								 };
								 ?>
							</select>
							<i class="pafe_advanced_search__categories-select-icon fa fa-caret-down"></i>
						</div>
					</div>
					<?php endif ?>
				</div>

				<button data-pafe-advanced-search-submit class="pafe-advanced-search__submit">
					<?php if ($settings['pafe_advanced_search_button_type'] == 'text') {
						echo $settings['pafe_advanced_search_button_text'];
					} elseif ($settings['pafe_advanced_search_button_type'] == 'icon') {
						\Elementor\Icons_Manager::render_icon( $settings['pafe_advanced_search_submit_button_icon'], [ 'aria-hidden' => 'true' ] );
					} ?>
				</button>
				
			</form>
			<div class="pafe-advanced-search__results" data-pafe-advanced-search-results-per-page="<?php echo $settings['pafe_advanced_search_result_pagination_posts_per_page'] ?>" data-pafe-advanced-search-result-no-post-message="<?php echo $settings['pafe_advanced_search_result_no_post_message']?> " data-pafe-advanced-search-result-footer="<?php echo $settings['pafe_advanced_search_result_footer_text'] ?>"></div>
  		</div>
	<?php }

}?>
