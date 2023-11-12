<?php
	class PAFE_Form_Booking extends \Elementor\Widget_Base {

		public function get_name() {
			return 'pafe-form-booking';
		}

		public function get_title() {
			return __( 'Booking', 'pafe' );
		}

		public function get_icon() {
			return 'icon-w-booking';
		}

		public function get_categories() {
			return [ 'pafe-form-builder' ];
		}

		public function get_keywords() {
			return [ 'form', 'booking' ];
		}

		public function get_script_depends() {
			return [ 
				'pafe-form-builder',
				'pafe-form-builder-advanced-script'
			];
		}

		public function get_style_depends() {
			return [ 
				'pafe-form-builder-style'
			];
		}

		protected function _register_controls() {

			$this->start_controls_section(
				'pafe_form_booking_section',
				[
					'label' => __( 'Settings', 'pafe' ),
				]
			);

			$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;

			$this->add_control(
				'pafe_form_booking_form_id',
				[
					'label' => __( 'Form ID* (Required)', 'pafe' ),
					'type' => $pafe_forms ? \Elementor\Controls_Manager::HIDDEN : \Elementor\Controls_Manager::TEXT,
					'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
					'default' => $pafe_forms ? get_the_ID() : '',
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->add_control(
				'pafe_form_booking_id',
				[
					'label' => __( 'Booking ID* (Required)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'Latin character and no space. E.g booking_restaurant', 'pafe' ),
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->add_control(
				'shortcode',
				[
					'label' => __( 'Shortcode', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'classes' => 'pafe-forms-field-shortcode pafe-forms-field-shortcode--booking',
				]
			);

			$this->add_control(
				'pafe_form_booking_field_label',
				[
					'label' => __( 'Label', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				]
			);

			$this->add_control(
				'pafe_form_booking_field_label_show',
				[
					'label' => __( 'Show Label', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'elementor-pro' ),
					'label_off' => __( 'Hide', 'elementor-pro' ),
					'return_value' => 'true',
					'default' => 'true',
				]
			);

			$this->add_control(
				'form_booking_field_required',
				[
					'label' => __( 'Required', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'default' => '',
				]
			);

			$this->add_control(
				'form_booking_mark_required',
				[
					'label' => __( 'Required Mark', 'elementor-pro' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'elementor-pro' ),
					'label_off' => __( 'Hide', 'elementor-pro' ),
					'default' => '',
					'condition' => [
						'pafe_form_booking_field_label!' => '',
					],
				]
			);

			$this->add_control(
				'pafe_form_booking_date_type',
				[
					'label' => __( 'Date Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'date_picker',
					'options' => [
						'date_picker'  => __( 'Date Picker','pafe' ),
						'specify_date' => __( 'Specify Date','pafe' ),
					], 
				]
			);

			$this->add_control(
				'pafe_form_booking_date_field', 
				[
					'label' => __( 'Date Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'condition' => [
						'pafe_form_booking_date_type' => 'date_picker'	
					]
				]
			);
 
			$this->add_control(
				'pafe_form_booking_date', 
				[
					'label' => __( 'Date', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DATE_TIME,
					'label_block' => false,
					'picker_options' => [
						'enableTime' => false,
					],
					'condition' => [
						'pafe_form_booking_date_type' => 'specify_date'	
					]
				]
			);

			$this->add_control(
				'pafe_form_booking_field_allow_multiple',
				[
					'label' => __( 'Multiple Selection', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'default' => 'true',
				]
			);

			$this->add_control(
				'pafe_form_booking_slot_quantity_field',
				[
					'label' => __( 'Slot Quantity Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'condition' => [
						'pafe_form_booking_field_allow_multiple' => ''	
					]
				]
			);

			$this->add_control(
				'pafe_form_booking_before_number_of_slot',
				[
					'label' => __( 'Before Number Of Slot', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				]
			);

			$this->add_control(
				'pafe_form_booking_after_number_of_slot',
				[
					'label' => __( 'After Number Of Slot', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				]
			);

			$this->add_control(
				'pafe_form_booking_sold_out_text',
				[
					'label' => __( 'Sold Out Text', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				]
			);

			$this->add_control(
				'pafe_form_booking_field_slot_show',
				[
					'label' => __( 'Show Slot', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'elementor-pro' ),
					'label_off' => __( 'Hide', 'elementor-pro' ),
					'return_value' => 'true',
					'default' => 'true',
				]
			);

			$this->add_control(
				'pafe_form_booking_field_price_show',
				[
					'label' => __( 'Show Price', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'elementor-pro' ),
					'label_off' => __( 'Hide', 'elementor-pro' ),
					'return_value' => 'true',
					'default' => 'true',
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'pafe_form_booking_slot_id',
				[
					'label' => __( 'Slot ID* (Required)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);
			
			$repeater->add_control(
				'pafe_form_booking_slot',
				[
					'label' => __( 'Number of Slot', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 1,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$repeater->add_control(
				'pafe_form_booking_title',
				[
					'label' => __( 'Title', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 

			$repeater->add_control(
				'pafe_form_booking_price',
				[
					'label' => __( 'Price', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'pafe_form_booking_price_text',
				[
					'label' => __( 'Price Text', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->add_control(
				'pafe_form_booking',
				[
					'type' => \Elementor\Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater->get_controls(),
					'title_field' => '{{{ pafe_form_booking_title }}}',				
				]
			); 
			$this->end_controls_section();

			$this->start_controls_section(
			'section_style_piotnet_form_label',
			[
				'label' => __( 'Label', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_label',
			[
				'label' => __( 'Label', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label' => __( 'Spacing', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}} .elementor-field-group > label' => 'padding-left: {{SIZE}}{{UNIT}};',
					// for the label position = inline option
					'body:not(.rtl) {{WRAPPER}} .elementor-field-group > label' => 'padding-right: {{SIZE}}{{UNIT}};',
					// for the label position = inline option
					'body {{WRAPPER}} .elementor-field-group > label' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					// for the label position = above option
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group > label, {{WRAPPER}} .elementor-field-subgroup label' => 'color: {{VALUE}};',
				],
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
                ],
			]
		);

		$this->add_control(
			'mark_required_color',
			[
				'label' => __( 'Mark Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-mark-required .elementor-field-label:after' => 'color: {{COLOR}};',
				],
				'condition' => [
					'mark_required' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .elementor-field-group > label',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
			]
		);

		$this->end_controls_section();

			$this->start_controls_section(
				'pafe_form_booking_style_section',
				[
					'label' => __( 'Item Style', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'pafe_form_booking_item_width',
				[
					'label' => __( 'Item Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1, 
						],
						'%' => [
							'min' => 0,
							'max' => 100,
							'step' => 1, 
						],
					], 
					'default' => [ 
						'unit' => '%',
						'size' => 25,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
	   			'item_align',
    			[
	  				'label' => __( 'Alignment', 'elementor' ),
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
	   				'default' => 'center',
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item' => 'text-align: {{VALUE}};',
	 				],
	   			]
   			);

			$this->add_responsive_control(
				'pafe_form_booking_item_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			); 

			$this->add_responsive_control(
				'pafe_form_booking_item_margin',
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .pafe-form-booking__inner' => 'margin: 0 -{{RIGHT}}{{UNIT}} 0 -{{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_form_booking_item_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_typography',
					'label' => __( 'Title Typography', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-form-booking__title',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                    ],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'slot_typography',
					'label' => __( 'Slot Typography', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-form-booking__slot',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                    ],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'price_typography',
					'label' => __( 'Price Typography', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-form-booking__price',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                    ],
				]
			);

			$this->start_controls_tabs(
				'pafe_form_booking_style_tabs'
			);

			$this->start_controls_tab( 
				'normal_tab',
				[
					'label' => __( 'Normal', 'pafe' ),
				]
			);

			$this->add_control( 
				'pafe_form_booking_item_background',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#D53440',  
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item .pafe-form-booking__item-inner' => 'background-color: {{VALUE}}',       
					],
				]
			);

			$this->add_control( 
				'pafe_form_booking_item_text',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#fff',  
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item' => 'color: {{VALUE}}',       
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_form_booking_item_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-form-booking__item .pafe-form-booking__item-inner',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab( 
				'seleted_tab',
				[
					'label' => __( 'Seleted', 'pafe' ),
				]
			);

			$this->add_control( 
				'pafe_form_booking_item_background_seleted',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#931b23',  
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item.active .pafe-form-booking__item-inner' => 'background-color: {{VALUE}}',       
					],
				]
			);

			$this->add_control( 
				'pafe_form_booking_item_text_seleted',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#fff',  
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item.active' => 'color: {{VALUE}}',       
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_form_booking_item_border_seleted',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-form-booking__item.active .pafe-form-booking__item-inner',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab( 
				'sold_out_tab',
				[
					'label' => __( 'Sold Out', 'pafe' ),
				]
			);

			$this->add_control( 
				'pafe_form_booking_item_background_sold_out',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#ccc',  
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item.pafe-form-booking__item--disabled .pafe-form-booking__item-inner' => 'background-color: {{VALUE}}',       
					],
				]
			);

			$this->add_control( 
				'pafe_form_booking_item_text_sold_out',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-form-booking__item.pafe-form-booking__item--disabled' => 'color: {{VALUE}}',       
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_form_booking_item_border_sold_out',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-form-booking__item.pafe-form-booking__item--disabled .pafe-form-booking__item-inner',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();	

			$this->end_controls_section();
		} 

		protected function render() {
			$settings = $this->get_settings_for_display();
			$editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
			$class = '';
			if ( ! empty( $settings['form_booking_field_required'] ) ) {
				$class = ' elementor-field-required elementor-field-type-checkbox';
				if ( ! empty( $settings['form_booking_mark_required'] ) ) {
					$class .= ' elementor-mark-required';
				}
			}

			$post_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : get_the_ID();

			$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
			$form_id = $pafe_forms ? get_the_ID() : $settings['pafe_form_booking_form_id'];
			$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;

			$settings['pafe_form_booking_form_id'] = $form_id;

			if ($editor) {
				$this->add_render_attribute( 'wrapper', [
					'data-pafe-form-builder-field' => json_encode(
						[
							'field_label' => !empty($settings['pafe_form_booking_field_label']) ? $settings['pafe_form_booking_field_label'] : '',
							'field_id' => !empty($settings['pafe_form_booking_id']) ? $settings['pafe_form_booking_id'] : '',
							'widget_id' => $this->get_id(),
						]
					),
				] );
			}

			$this->add_render_attribute( 'wrapper', [
				'class' => 'elementor-field-group pafe-form-booking pafe-form-booking--loading ' . $class,
				'data-pafe-form-booking' => esc_attr(json_encode($settings)),
			] );
		?>		
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php
				require_once( __DIR__ . '/../inc/templates/template-form-booking.php' );

				pafe_template_form_booking($settings, $this->get_id(), $post_id, '', $form_id);
			?>
		</div>
		<?php
		}

		public function add_wpml_support() {
			add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
		}

		public function wpml_widgets_to_translate_filter( $widgets ) {
			$widgets[ $this->get_name() ] = [
				'conditions' => [ 'widgetType' => $this->get_name() ],
				'fields'     => [
					[
						'field'       => 'pafe_form_booking_field_label',
						'type'        => __( 'Booking Label', 'pafe' ),
						'editor_type' => 'LINE'
					],
					[
						'field'       => 'pafe_form_booking_sold_out_text',
						'type'        => __( 'Sold Out Text', 'pafe' ),
						'editor_type' => 'LINE'
					],
				],
			];

			return $widgets;
		}
	}
