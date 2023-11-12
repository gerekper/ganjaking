<?php

class PAFE_Forms extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-forms';
	}

	public function get_title() {
		return __( 'Form Embedding', 'pafe' );
	}

	public function get_icon() {
		return 'icon-w-form';
	}

	public function get_categories() {
		return [ 'pafe-form-builder' ];
	}

	public function get_keywords() {
		return [ 'input', 'form', 'field', 'forms' ];
	}

	public function get_script_depends() {
		return [ 
			'pafe-form-builder',
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-form-builder-style'
		];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'form_section',
			[
				'label' => __( 'Form', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$form_id_query = get_posts(
			array(
				'post_type' => 'pafe-forms',
				'posts_per_page' => -1,
				'fields' => 'ids',
			)
		);

		$forms = [];

		foreach ($form_id_query as $form_id) {
			$forms[ $form_id ] = get_the_title($form_id);
		}

		$this->add_control(
			'form_id',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Select a Form', 'pafe' ),
				'options' => $forms,
				'default' => '',
				'description' => '<a href="' . admin_url() . 'edit.php?post_type=pafe-forms" target="_blank">Create a new form</a>',
			]
		);

		$this->add_control(
			'multi_step_enable',
			[
				'label' => __( 'Multi Step Form', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-pro' ),
				'label_off' => __( 'No', 'elementor-pro' ),
				'return_value' => 'true',
				'default' => '',
				'description' => __( 'This feature only works on the frontend', 'pafe' ),
			]
		);

		$this->add_control(
			'woocommerce_checkout_enable',
			[
				'label' => __( 'Woocommerce Checkout', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-pro' ),
				'label_off' => __( 'No', 'elementor-pro' ),
				'return_value' => 'true',
				'default' => '',
			]
		);

		$this->add_control(
			'pafe_woocommerce_checkout_product_id',
			[
				'label' => __( 'Product ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'woocommerce_checkout_enable' => 'true',
				],
			]
		);

		

	    $this->add_control(
			'pafe_woocommerce_checkout_remove_fields',
			[
				'label' => __( 'Remove fields from Checkout Form', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'billing_first_name' => __( 'Billing First Name', 'pafe' ),
					'billing_last_name' => __( 'Billing Last Name', 'pafe' ),
					'billing_company' => __( 'Billing Company', 'pafe' ),
					'billing_address_1' => __( 'Billing Address 1', 'pafe' ),
					'billing_address_2' => __( 'Billing Address 2', 'pafe' ),
					'billing_city' => __( 'Billing City', 'pafe' ),
					'billing_postcode' => __( 'Billing Post Code', 'pafe' ),
					'billing_country' => __( 'Billing Country', 'pafe' ),
					'billing_state' => __( 'Billing State', 'pafe' ),
					'billing_phone' => __( 'Billing Phone', 'pafe' ),
					'billing_email' => __( 'Billing Email', 'pafe' ),
					'order_comments' => __( 'Order Comments', 'pafe' ),
					'shipping_first_name' => __( 'Shipping First Name', 'pafe' ),
					'shipping_last_name' => __( 'Shipping Last Name', 'pafe' ),
					'shipping_company' => __( 'Shipping Company', 'pafe' ),
					'shipping_address_1' => __( 'Shipping Address 1', 'pafe' ),
					'shipping_address_2' => __( 'Shipping Address 2', 'pafe' ),
					'shipping_city' => __( 'Shipping City', 'pafe' ),
					'shipping_postcode' => __( 'Shipping Post Code', 'pafe' ),
					'shipping_country' => __( 'Shipping Country', 'pafe' ),
					'shipping_state' => __( 'Shipping State', 'pafe' ),
				],
			    'condition' => [
				'woocommerce_checkout_enable' => 'true',
			    ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'multi_step_form_scroll_to_top_section',
			[
				'label' => __( 'Multi Step Form Scroll To Top', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'multi_step_enable' => 'true',
				],
			]
		);

		$this->add_control(
			'pafe_multi_step_form_scroll_to_top',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			] 
		);

		$this->add_control(
			'pafe_multi_step_form_scroll_to_top_offset_desktop',
			[
				'label' => __( 'Desktop Negative Offset Top (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'pafe_multi_step_form_scroll_to_top' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_multi_step_form_scroll_to_top_offset_tablet',
			[
				'label' => __( 'Tablet Negative Offset Top (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'pafe_multi_step_form_scroll_to_top' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_multi_step_form_scroll_to_top_offset_mobile',
			[
				'label' => __( 'Mobile Negative Offset Top (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'pafe_multi_step_form_scroll_to_top' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'multi_step_form_style_section',
			[
				'label' => __( 'Multi Step Form Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'multi_step_enable' => 'true',
				],
			]
		);

		$this->add_control(
            'progress_bar_show',
            [
                'label' => __( 'Show Progress Bar', 'elementor' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'elementor' ),
                'label_off' => __( 'Hide', 'elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography_step_number',
				'label' => 'Step Number',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                ],
				'selector' => '{{WRAPPER}} .pafe-multi-step-form__progressbar-item-step',
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography_step_title',
				'label' => 'Step Title',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                ],
				'selector' => '{{WRAPPER}} .pafe-multi-step-form__progressbar-item-title',
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_title_hide_desktop',
			[
				'label' => __( 'Hide Step Title On Desktop', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Hide', 'elementor' ),
				'label_off' => __( 'Show', 'elementor' ),
				'return_value' => 'pafe-multi-step-form__progressbar--hide-title-desktop',
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_title_hide_tablet',
			[
				'label' => __( 'Hide Step Title On Tablet', 'elementor' ),
				'type' =>\Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Hide', 'elementor' ),
				'label_off' => __( 'Show', 'elementor' ),
				'return_value' => 'pafe-multi-step-form__progressbar--hide-title-tablet',
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_title_hide_mobile',
			[
				'label' => __( 'Hide Step Title On Mobile', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Hide', 'elementor' ),
				'label_off' => __( 'Show', 'elementor' ),
				'return_value' => 'pafe-multi-step-form__progressbar--hide-title-mobile',
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_responsive_control(
			'progress_bar_step_width',
			[
				'label' => __( 'Step Number Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item-step' => 'width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],

			]
		);

		$this->add_control(
			'progress_bar_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item-step' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->start_controls_tabs( 'tabs_progress_bar_style' );

		$this->start_controls_tab(
			'tab_progress_bar_normal',
			[
				'label' => __( 'Normal', 'pafe' ),
				 'condition'   => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_title_color',
			[
				'label' => __( 'Step Title Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item-title' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_number_color',
			[
				'label' => __( 'Step Number Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item-step' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_number_background_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ececec',
				'selectors' => [
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item-step' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item-step-number::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_progress_bar_active',
			[
				'label' => __( 'Active', 'pafe' ),
				'condition'   => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_title_color_active',
			[
				'label' => __( 'Step Title Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .active .pafe-multi-step-form__progressbar-item-title' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_number_color_active',
			[
				'label' => __( 'Step Number Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .active .pafe-multi-step-form__progressbar-item-step' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->add_control(
			'progress_bar_step_number_background_color_active',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#27AE60',
				'selectors' => [
					'{{WRAPPER}} .active .pafe-multi-step-form__progressbar-item-step' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .pafe-multi-step-form__progressbar-item.active .pafe-multi-step-form__progressbar-item-step-number::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
                    'progress_bar_show' => 'yes',
                ],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		$editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$form_id = !empty($settings['form_id']) ? $settings['form_id'] : '';
		$shortcode = '[pafe-forms id=' . $form_id . ']';
		$multi_step = !empty($settings['multi_step_enable']) ? true : false;

		if ($multi_step) {
			$this->add_render_attribute( 'wrapper', 'data-pafe-forms-multistep', '' );
			$this->add_render_attribute( 'wrapper', 'class', 'pafe-multi-step-form' );
            $this->add_render_attribute( 'wrapper', 'style', 'visibility:hidden' );


			if( !empty($settings['pafe_multi_step_form_scroll_to_top'] ) ) {
				$this->add_render_attribute( 'wrapper', [
					'data-pafe-multi-step-form-scroll-to-top' => '',
					'data-pafe-multi-step-form-scroll-to-top-offset-desktop' => $settings['pafe_multi_step_form_scroll_to_top_offset_desktop'],
					'data-pafe-multi-step-form-scroll-to-top-offset-tablet' => $settings['pafe_multi_step_form_scroll_to_top_offset_tablet'],
					'data-pafe-multi-step-form-scroll-to-top-offset-mobile' => $settings['pafe_multi_step_form_scroll_to_top_offset_mobile'],
				] );
			}

			wp_enqueue_script( 'pafe-form-builder-multi-step-script' );
			wp_enqueue_style( 'pafe-form-builder-multi-step-style' ); 
		}

		$GLOBALS['pafe_form_id'] = $form_id;
		if ( ! empty( $form_id ) ) {
			?>
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ;?>>
				<?php if ($multi_step) : ?>
				<?php if ($editor) : ?><div style="text-align: center; margin-bottom: 20px;">This is demo progress bar content</div><?php endif; ?>
				<div class="pafe-multi-step-form__progressbar <?php if ( $settings['progress_bar_show'] != 'yes') { echo ('pafe-progressbar-hidden');} echo $settings['progress_bar_step_title_hide_desktop'] . ' ' . $settings['progress_bar_step_title_hide_tablet'] . ' ' . $settings['progress_bar_step_title_hide_mobile']; ?>" data-pafe-forms-multistep-progressbar>
					<?php if ($editor) : ?>
						<div class="pafe-multi-step-form__progressbar-item active">
							<div class="pafe-multi-step-form__progressbar-item-step-number">
								<div class="pafe-multi-step-form__progressbar-item-step">1</div>
							</div>
							<div class="pafe-multi-step-form__progressbar-item-title">Step 1</div>
						</div>
						<div class="pafe-multi-step-form__progressbar-item">
							<div class="pafe-multi-step-form__progressbar-item-step-number">
								<div class="pafe-multi-step-form__progressbar-item-step">2</div>
							</div>
							<div class="pafe-multi-step-form__progressbar-item-title">Step 2</div>
						</div>
						<div class="pafe-multi-step-form__progressbar-item">
							<div class="pafe-multi-step-form__progressbar-item-step-number">
								<div class="pafe-multi-step-form__progressbar-item-step">3</div>
							</div>
							<div class="pafe-multi-step-form__progressbar-item-title">Step 3</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="pafe-multi-step-form__content">
					<?php echo do_shortcode( $shortcode ); ?>
				</div>
				<?php else : ?>
					<?php echo do_shortcode( $shortcode ); ?>
				<?php endif; ?>
			</div>
			<?php
		}
		unset($GLOBALS['pafe_form_id']);
	}
}
