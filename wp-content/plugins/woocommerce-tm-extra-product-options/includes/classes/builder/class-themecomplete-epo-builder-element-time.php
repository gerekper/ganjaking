<?php
/**
 * Time Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Time Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_TIME extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * Class Constructor
	 *
	 * @param string $name The element name.
	 * @since 6.0
	 */
	public function __construct( $name = '' ) {
		$this->element_name     = $name;
		$this->is_addon         = false;
		$this->namespace        = $this->elements_namespace;
		$this->name             = esc_html__( 'Time', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfar tcfa-clock';
		$this->is_post          = 'post';
		$this->type             = 'single';
		$this->post_name_prefix = 'time';
		$this->fee_type         = 'single';
		$this->tags             = 'price content';
		$this->show_on_backend  = true;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			[
				'enabled',
				'required',
				'price_type6',
				'lookuptable',
				'price',
				'sale_price',
				'fee',
				'hide_amount',
				'text_before_price',
				'text_after_price',
				'quantity',
				THEMECOMPLETE_EPO_BUILDER()->add_setting_button_type(
					'time',
					[
						'message0x0_class' => 'tm-epo-switch-wrapper',
						'type'             => 'radio',
						'tags'             => [
							'class' => 'time-button-type',
							'id'    => 'builder_time_button_type',
							'name'  => 'tm_meta[tmfbuilder][time_button_type][]',
						],
						'default'          => 'custom',
						'options'          => [
							[
								'text'  => esc_html__( 'System style', 'woocommerce-tm-extra-product-options' ),
								'value' => 'system',
							],
							[
								'text'  => esc_html__( 'Custom style', 'woocommerce-tm-extra-product-options' ),
								'value' => 'custom',
							],
						],
						'label'            => esc_html__( 'Time picker style', 'woocommerce-tm-extra-product-options' ),
					]
				),
				THEMECOMPLETE_EPO_BUILDER()->add_setting_time_format(
					'time',
					[
						'required' => [
							'.time-button-type' => [
								'operator' => 'is',
								'value'    => 'custom',
							],
						],
					]
				),
				THEMECOMPLETE_EPO_BUILDER()->add_setting_custom_time_format(
					'time',
					[
						'required' => [
							'.time-button-type' => [
								'operator' => 'is',
								'value'    => 'custom',
							],
						],
					]
				),

				[
					'id'          => 'time_min_time',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 't',
						'id'    => 'builder_time_min_time',
						'name'  => 'tm_meta[tmfbuilder][time_min_time][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Minimum selectable time', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Enter the time the following format: 8:00 am', 'woocommerce-tm-extra-product-options' ),
					'required'    => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'          => 'time_max_time',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 't',
						'id'    => 'builder_time_max_time',
						'name'  => 'tm_meta[tmfbuilder][time_max_time][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Maximum selectable time', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Enter the time the following format: 8:00 am', 'woocommerce-tm-extra-product-options' ),
					'required'    => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'          => 'time_theme',
					'wpmldisable' => 1,
					'default'     => 'epo',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_time_theme',
						'name' => 'tm_meta[tmfbuilder][time_theme][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Epo White', 'woocommerce-tm-extra-product-options' ),
							'value' => 'epo',
						],
						[
							'text'  => esc_html__( 'Epo Black', 'woocommerce-tm-extra-product-options' ),
							'value' => 'epo-black',
						],
					],
					'label'       => esc_html__( 'Theme', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Select the theme for the timepicker.', 'woocommerce-tm-extra-product-options' ),
					'required'    => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'          => 'time_theme_size',
					'wpmldisable' => 1,
					'default'     => 'medium',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_time_theme_size',
						'name' => 'tm_meta[tmfbuilder][time_theme_size][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Small', 'woocommerce-tm-extra-product-options' ),
							'value' => 'small',
						],
						[
							'text'  => esc_html__( 'Medium', 'woocommerce-tm-extra-product-options' ),
							'value' => 'medium',
						],
						[
							'text'  => esc_html__( 'Large', 'woocommerce-tm-extra-product-options' ),
							'value' => 'large',
						],
					],
					'label'       => esc_html__( 'Size', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Select the size of the timepicker.', 'woocommerce-tm-extra-product-options' ),
					'required'    => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'          => 'time_theme_position',
					'wpmldisable' => 1,
					'default'     => 'normal',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_time_theme_position',
						'name' => 'tm_meta[tmfbuilder][time_theme_position][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
							'value' => 'normal',
						],
						[
							'text'  => esc_html__( 'Top of screen', 'woocommerce-tm-extra-product-options' ),
							'value' => 'top',
						],
						[
							'text'  => esc_html__( 'Bottom of screen', 'woocommerce-tm-extra-product-options' ),
							'value' => 'bottom',
						],
					],
					'label'       => esc_html__( 'Position', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Select the position of the timepicker.', 'woocommerce-tm-extra-product-options' ),
					'required'    => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'               => 'time_tranlation_custom',
					'type'             => 'custom',
					'label'            => esc_html__( 'Translations', 'woocommerce-tm-extra-product-options' ),
					'desc'             => '',
					'nowrap_end'       => 1,
					'noclear'          => 1,
					'message0x0_class' => 'justify-content-flex-end',
					'required'         => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'                   => 'time_tranlation_hour',
					'default'              => '',
					'type'                 => 'text',
					'tags'                 => [
						'class' => 't',
						'id'    => 'builder_time_tranlation_hour',
						'name'  => 'tm_meta[tmfbuilder][time_tranlation_hour][]',
						'value' => '',
					],
					'label'                => '',
					'desc'                 => '',
					'prepend_element_html' => '<span class="prepend_span">' . esc_html__( 'Hour', 'woocommerce-tm-extra-product-options' ) . '</span> ',
					'nowrap_start'         => 1,
					'nowrap_end'           => 1,
					'required'             => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'                   => 'time_tranlation_minute',
					'default'              => '',
					'type'                 => 'text',
					'nowrap_start'         => 1,
					'nowrap_end'           => 1,
					'tags'                 => [
						'class' => 't',
						'id'    => 'builder_time_tranlation_month',
						'name'  => 'tm_meta[tmfbuilder][time_tranlation_minute][]',
						'value' => '',
					],
					'label'                => '',
					'desc'                 => '',
					'prepend_element_html' => '<span class="prepend_span">' . esc_html__( 'Minute', 'woocommerce-tm-extra-product-options' ) . '</span> ',
					'required'             => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
				[
					'id'                   => 'time_tranlation_second',
					'default'              => '',
					'type'                 => 'text',
					'tags'                 => [
						'class' => 't',
						'id'    => 'builder_time_tranlation_second',
						'name'  => 'tm_meta[tmfbuilder][time_tranlation_second][]',
						'value' => '',
					],
					'label'                => '',
					'desc'                 => '',
					'prepend_element_html' => '<span class="prepend_span">' . esc_html__( 'Second', 'woocommerce-tm-extra-product-options' ) . '</span> ',
					'nowrap_start'         => 1,
					'required'             => [
						'.time-button-type' => [
							'operator' => 'is',
							'value'    => 'custom',
						],
					],
				],
			]
		);
	}
}
