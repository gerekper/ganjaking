<?php
/**
 * Extra Product Options Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
abstract class THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * The element namespace
	 *
	 * @var string
	 */
	public $elements_namespace = THEMECOMPLETE_EPO_ELEMENTS_NAMESPACE;

	/**
	 * The name of the element for the internal array
	 *
	 * @var string
	 */
	public $element_name;

	/**
	 * If this is an addon element or not
	 *
	 * @var bool
	 */
	public $is_addon;

	/**
	 * The element namespace
	 *
	 * @var string
	 */
	public $namespace;

	/**
	 * The element name in the builder
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The element description in the builder
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The builder initial width class
	 *
	 * @var string
	 */
	public $width;

	/**
	 * The builder inital width in percent
	 *
	 * @var string
	 */
	public $width_display;

	/**
	 * The icon class name for the icon in the builder
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * If the element can be posted
	 * Can either be 'display' or 'post'
	 *
	 * @var bool
	 */
	public $is_post;

	/**
	 * The type of this element
	 * Can either be '', 'single', 'variations', 'multipleall'or 'multiple'
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The name prefix for this element
	 *
	 * @var string
	 */
	public $post_name_prefix;

	/**
	 * The type of fee this element can take
	 * Can be '', 'single' or 'multiple'
	 *
	 * @var string
	 */
	public $fee_type;

	/**
	 * Tags for registring the element to the popup
	 *
	 * @var string
	 */
	public $tags;

	/**
	 * If the element can be shown on the builder as a selectable element
	 *
	 * @var bool
	 */
	public $show_on_backend;

	/**
	 * If the field should only be one time in the builder
	 *
	 * @var bool
	 */
	public $one_time_field = false;

	/**
	 * If the element cannot be selected in the builder
	 *
	 * @var bool
	 */
	public $no_selection = false;

	/**
	 * If the element should not be displayed in the frontend
	 *
	 * @var bool
	 */
	public $no_frontend_display = false;

	/**
	 * Holder for the element properties
	 *
	 * @var array
	 */
	public $properties = [];

	/**
	 * Holder for the element attributes
	 * Used for magic methods
	 *
	 * @var array
	 */
	private $attributes = [];

	/**
	 * Sets an attribute
	 *
	 * @param string $name The attribute name.
	 * @param string $value The attribute value.
	 */
	public function __set( $name, $value ) {
		$this->attributes[ $name ] = $value;
	}

	/**
	 * Gets an attribute if it exists
	 *
	 * @param string $name The attribute name.
	 */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->attributes ) ) {
			return $this->attributes[ $name ];
		}
	}

	/**
	 * Sets the element attributes
	 *
	 * @param array $attributes The attributes array.
	 * @return void
	 */
	public function set_attributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {

	}

	/**
	 * Add tab callback
	 *
	 * @param string $id The element name.
	 * @param mixed  $label The element label.
	 * @param string $closed The closed class.
	 * @param string $boxclass The box class.
	 * @since 6.0
	 * @access public
	 */
	public function prepend_tab_callback( $id = '', $label = '', $closed = 'closed', $boxclass = '' ) {

		$datatab = $label;
		if ( is_array( $label ) ) {
			$datatab = $label[1];
		}
		echo "<div class='tm-box" . esc_attr( $boxclass ) . "'>"
			. "<h4 tabindex='0' data-id='" . esc_attr( $id ) . "-tab' data-tab='" . esc_attr( sanitize_key( $datatab ) ) . "-tab' class='tab-header" . esc_attr( $closed ) . "'>";
		if ( is_array( $label ) ) {
			echo '<i class="tab-header-icon ' . esc_attr( $label[0] ) . '"></i>' . "<span class='tab-header-label'>" . esc_html( $label[1] ) . '</span>';
		} else {
			echo "<span class='tab-header-label'>" . esc_html( $label ) . '</span>';
		}
		echo "<span class='tcfa tcfa-angle-down tm-arrow'></span></h4></div>";
	}

	/**
	 * Add tab
	 *
	 * @param string $id The element name.
	 * @param mixed  $label The element label.
	 * @param string $closed The closed class.
	 * @param string $boxclass The box class.
	 * @since 6.0
	 * @access public
	 */
	public function prepend_tab( $id = '', $label = '', $closed = 'closed', $boxclass = '' ) {
		if ( ! empty( $closed ) ) {
			$closed = ' ' . $closed;
		}
		if ( ! empty( $boxclass ) ) {
			$boxclass = ' ' . $boxclass;
		}

		return [
			[
				'id'      => $id . '_custom_tabstart',
				'default' => '',
				'type'    => 'custom',
				'nodiv'   => 1,
				'html'    => [ [ $this, 'prepend_tab_callback' ], [ $id, $label, $closed, $boxclass ] ],
				'label'   => '',
				'desc'    => '',
			],
		];
	}

	/**
	 * Add div callback
	 *
	 * @param string $id The element name.
	 * @param string $tmtab The tab class.
	 * @since 6.0
	 * @access public
	 */
	public function prepend_div_callback( $id = '', $tmtab = 'tm-tab' ) {
		// Remove empty values.
		$classes = array_filter( [ 'transition', $tmtab, $id ] );
		$classes = implode( ' ', $classes );
		echo '<div class="' . esc_attr( $classes ) . '">';
	}

	/**
	 * Start div
	 *
	 * @param string $id The element name.
	 * @param string $tmtab The tab class.
	 * @since 6.0
	 * @access public
	 */
	public function prepend_div( $id = '', $tmtab = 'tm-tab' ) {
		if ( ! empty( $id ) ) {
			$id .= '-tab';
		}

		return [
			[
				'id'      => $id . '_custom_divstart',
				'default' => '',
				'type'    => 'custom',
				'nodiv'   => 1,
				'html'    => [ [ $this, 'prepend_div_callback' ], [ $id, $tmtab ] ],
				'label'   => '',
				'desc'    => '',
			],
		];
	}

	/**
	 * End div callback
	 *
	 * @since 6.0
	 * @access public
	 */
	public function append_div_callback() {
		echo '</div>';
	}

	/**
	 * End div
	 *
	 * @param string $id The element name.
	 * @since 6.0
	 * @access private
	 */
	public function append_div( $id = '' ) {
		return [
			[
				'id'      => $id . '_custom_divend',
				'default' => '',
				'type'    => 'custom',
				'nodiv'   => 1,
				'html'    => [ [ $this, 'append_div_callback' ], [] ],
				'label'   => '',
				'desc'    => '',
			],
		];
	}

	/**
	 * Prepend logic elements
	 *
	 * @param string $id The element name.
	 * @since 6.0
	 * @access public
	 */
	public function prepend_logic( $id = '' ) {
		return apply_filters(
			'wc_epo_admin_element_conditional_logic',
			[
				[
					'id'      => $id . '_uniqid',
					'default' => '',
					'nodiv'   => 1,
					'type'    => 'hidden',
					'tags'    => [
						'class' => 'tm-builder-element-uniqid',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_uniqid][]',
						'value' => '',
					],
					'label'   => '',
					'desc'    => '',
				],
				[
					'id'      => $id . '_clogic',
					'default' => '',
					'nodiv'   => 1,
					'type'    => 'hidden',
					'tags'    => [
						'class' => 'tm-builder-clogic',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_clogic][]',
						'value' => '',
					],
					'label'   => '',
					'desc'    => '',
				],
				[
					'id'        => $id . '_logic',
					'default'   => '',
					'leftclass' => 'align-self-start',
					'type'      => 'checkbox',
					'tags'      => [
						'class' => 'c activate-element-logic',
						'id'    => 'builder_' . $id . '_logic',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_logic][]',
						'value' => '1',
					],
					'extra'     => [ [ $this, 'builder_showlogic' ], [] ],
					'label'     => esc_html__( 'Element Conditional Logic', 'woocommerce-tm-extra-product-options' ),
					'desc'      => esc_html__( 'Enable conditional logic for showing or hiding this element.', 'woocommerce-tm-extra-product-options' ),
				],
			]
		);
	}

	/**
	 * Common element options.
	 *
	 * @param string $id element internal id (key from THEMECOMPLETE_EPO_BUILDER()->all_elements).
	 * @param string $type element or section.
	 * @param string $name element type.
	 * @return array List of common element options adjusted by element internal id.
	 *
	 * @since 6.0
	 * @access private
	 */
	public function get_header_array( $id = 'header', $type = '', $name = '' ) {
		return apply_filters(
			'wc_epo_admin_element_label_options',
			[
				[
					'id'          => $id . '_size',
					'wpmldisable' => 1,
					'default'     => ( 'section_header' === $id ) ? '2' : '3',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_' . $id . '_size',
						'name' => 'tm_meta[tmfbuilder][' . $id . '_size][]',
					],
					'options'     =>
						( 'section_header' !== $id ) ?
							[
								[
									'text'  => esc_html__( 'H1', 'woocommerce-tm-extra-product-options' ),
									'value' => '1',
								],
								[
									'text'  => esc_html__( 'H2', 'woocommerce-tm-extra-product-options' ),
									'value' => '2',
								],
								[
									'text'  => esc_html__( 'H3', 'woocommerce-tm-extra-product-options' ),
									'value' => '3',
								],
								[
									'text'  => esc_html__( 'H4', 'woocommerce-tm-extra-product-options' ),
									'value' => '4',
								],
								[
									'text'  => esc_html__( 'H5', 'woocommerce-tm-extra-product-options' ),
									'value' => '5',
								],
								[
									'text'  => esc_html__( 'H6', 'woocommerce-tm-extra-product-options' ),
									'value' => '6',
								],
								[
									'text'  => esc_html__( 'p', 'woocommerce-tm-extra-product-options' ),
									'value' => '7',
								],
								[
									'text'  => esc_html__( 'div', 'woocommerce-tm-extra-product-options' ),
									'value' => '8',
								],
								[
									'text'  => esc_html__( 'span', 'woocommerce-tm-extra-product-options' ),
									'value' => '9',
								],
								[
									'text'  => esc_html__( 'label', 'woocommerce-tm-extra-product-options' ),
									'value' => '10',
								],
							] :
							[
								[
									'text'  => esc_html__( 'H1', 'woocommerce-tm-extra-product-options' ),
									'value' => '1',
								],
								[
									'text'  => esc_html__( 'H2', 'woocommerce-tm-extra-product-options' ),
									'value' => '2',
								],
								[
									'text'  => esc_html__( 'H3', 'woocommerce-tm-extra-product-options' ),
									'value' => '3',
								],
								[
									'text'  => esc_html__( 'H4', 'woocommerce-tm-extra-product-options' ),
									'value' => '4',
								],
								[
									'text'  => esc_html__( 'H5', 'woocommerce-tm-extra-product-options' ),
									'value' => '5',
								],
								[
									'text'  => esc_html__( 'H6', 'woocommerce-tm-extra-product-options' ),
									'value' => '6',
								],
								[
									'text'  => esc_html__( 'p', 'woocommerce-tm-extra-product-options' ),
									'value' => '7',
								],
								[
									'text'  => esc_html__( 'div', 'woocommerce-tm-extra-product-options' ),
									'value' => '8',
								],
								[
									'text'  => esc_html__( 'span', 'woocommerce-tm-extra-product-options' ),
									'value' => '9',
								],
							],
					'label'       => esc_html__( 'Label type', 'woocommerce-tm-extra-product-options' ),
					'desc'        => '',
				],
				[
					'id'               => $id . '_title',
					'default'          => '',
					'type'             => 'text',
					'message0x0_class' => ( 'element' === $type && 'variations' === $name ) ? 'builder_hide_for_variation' : '',
					'tags'             => [
						'class' => 't tm-header-title',
						'id'    => 'builder_' . $id . '_title',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_title][]',
						'value' => '',
					],
					'label'            => esc_html__( 'Label', 'woocommerce-tm-extra-product-options' ),
					'desc'             => '',
				],
				[
					'id'          => $id . '_title_position',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'select',
					'tags'        => [
						'class' => 'tc-label-position',
						'id'    => 'builder_' . $id . '_title_position',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_title_position][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Above field', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => esc_html__( 'Left of the field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'left',
						],
						[
							'text'  => esc_html__( 'Right of the field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'right',
						],
						[
							'text'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
							'value' => 'disable',
						],
					],
					'label'       => esc_html__( 'Label position', 'woocommerce-tm-extra-product-options' ),
					'desc'        => '',
					'required'    => ( 'section' === $type ) ? [
						'.sections_style' => [
							'operator' => 'is',
							'value'    => '',
						],
					] : [],
				],
				[
					'id'          => $id . '_title_color',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'data-show-input'            => 'true',
						'data-show-initial'          => 'true',
						'data-allow-empty'           => 'true',
						'data-show-alpha'            => 'false',
						'data-show-palette'          => 'false',
						'data-clickout-fires-change' => 'true',
						'data-show-buttons'          => 'false',
						'data-preferred-format'      => 'hex',
						'class'                      => 'tm-color-picker',
						'id'                         => 'builder_' . $id . '_title_color',
						'name'                       => 'tm_meta[tmfbuilder][' . $id . '_title_color][]',
						'value'                      => '',
					],
					'label'       => esc_html__( 'Label color', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'      => $id . '_subtitle',
					'default' => '',
					'type'    => 'textarea',
					'tags'    => [
						'id'   => 'builder_' . $id . '_subtitle',
						'name' => 'tm_meta[tmfbuilder][' . $id . '_subtitle][]',
					],
					'label'   => esc_html__( 'Subtitle', 'woocommerce-tm-extra-product-options' ),
					'desc'    => '',
				],
				[
					'id'          => $id . '_subtitle_position',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_' . $id . '_subtitle_position',
						'name' => 'tm_meta[tmfbuilder][' . $id . '_subtitle_position][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Above field', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => esc_html__( 'Below field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'below',
						],
						[
							'text'  => esc_html__( 'Tooltip', 'woocommerce-tm-extra-product-options' ),
							'value' => 'tooltip',
						],
						[
							'text'  => esc_html__( 'Icon tooltip left', 'woocommerce-tm-extra-product-options' ),
							'value' => 'icontooltipleft',
						],
						[
							'text'  => esc_html__( 'Icon tooltip right', 'woocommerce-tm-extra-product-options' ),
							'value' => 'icontooltipright',
						],
					],
					'label'       => esc_html__( 'Subtitle position', 'woocommerce-tm-extra-product-options' ),
					'desc'        => '',
				],
				[
					'id'          => $id . '_subtitle_color',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 'tm-color-picker',
						'id'    => 'builder_' . $id . '_subtitle_color',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_subtitle_color][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Subtitle color', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
				],
			]
		);
	}

	/**
	 * Sets element divider option.
	 *
	 * @param string  $id element internal id (key from THEMECOMPLETE_EPO_BUILDER()->all_elements).
	 * @param integer $noempty If the empty choice should be printed.
	 *
	 * @return array Element divider options adjusted by element internal id.
	 *
	 * @since 6.0
	 * @access public
	 */
	public function get_divider_array( $id = 'divider', $noempty = 1 ) {
		$_divider = [
			[
				'id'               => $id . '_type',
				'wpmldisable'      => 1,
				'message0x0_class' => 'builder_hide_for_variation',
				'default'          => 'hr',
				'type'             => 'select',
				'tags'             => [
					'id'   => 'builder_' . $id . '_type',
					'name' => 'tm_meta[tmfbuilder][' . $id . '_type][]',
				],
				'options'          => [
					[
						'text'  => esc_html__( 'Horizontal rule', 'woocommerce-tm-extra-product-options' ),
						'value' => 'hr',
					],
					[
						'text'  => esc_html__( 'Divider', 'woocommerce-tm-extra-product-options' ),
						'value' => 'divider',
					],
					[
						'text'  => esc_html__( 'Padding', 'woocommerce-tm-extra-product-options' ),
						'value' => 'padding',
					],
				],
				'label'            => esc_html__( 'Divider type', 'woocommerce-tm-extra-product-options' ),
				'desc'             => '',
				'required'         => [
					'.tc-label-position' => [
						'operator' => 'is',
						'value'    => [ '', 'disable' ],
					],
				],
			],
		];
		if ( empty( $noempty ) ) {
			$_divider[0]['default'] = 'none';
			array_push(
				$_divider[0]['options'],
				[
					'text'  => esc_html__( 'None', 'woocommerce-tm-extra-product-options' ),
					'value' => 'none',
				]
			);
		}

		return $_divider;
	}

	/**
	 * Add element
	 *
	 * @param string  $name The element name.
	 * @param array   $settings_array Array of settings.
	 * @param boolean $is_addon If this is an addon element.
	 * @param array   $tabs_override Tabs array overrides.
	 * @param array   $header_names Header names array.
	 * @since 6.0
	 */
	public function add_element( $name = '', $settings_array = [], $is_addon = false, $tabs_override = [], $header_names = [] ) {

		$settings_array        = apply_filters( 'tc_element_settings_override', $settings_array, $name );
		$tabs_override         = apply_filters( 'tc_element_tabs_override', $tabs_override, $name, $settings_array, $is_addon );
		$options               = [];
		$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();

		if ( ! isset( $settings_array['_tabs'] ) ) {
			$tabs_array = [ 'general_options' => $settings_array ];
		} else {
			$tabs_array = $settings_array['_tabs'];
		}
		$tabs = [];
		foreach ( $tabs_array as $tab => $settings ) {
			$tabs[] = $tab;
			foreach ( $settings as $key => $value ) {
				if ( is_array( $value ) && count( $value ) > 2 ) {
					if ( isset( $value['id'] ) ) {
						THEMECOMPLETE_EPO_BUILDER()->default_attributes[] = $value['id'];
						if ( $is_addon ) {
							$value['id'] = $this->remove_prefix( $value['id'], $name . '_' );

							THEMECOMPLETE_EPO_BUILDER()->addons_attributes[] = $value['id'];

							$value['id'] = $name . '_' . $value['id'];

							if ( ! isset( $value['tags'] ) ) {
								$value['tags'] = [];
							}
							$value['tags'] = array_merge(
								$value['tags'],
								[
									'id'    => 'builder_' . $value['id'],
									'name'  => 'tm_meta[tmfbuilder][' . $value['id'] . '][]',
									'value' => '',
								]
							);
						}
					}
					$options[ $tab ][] = $value;
				} else {
					$args = false;

					if ( is_array( $value ) && 1 === count( $value ) && isset( $value['_multiple_values'] ) ) {

						foreach ( $value['_multiple_values'] as $mkey => $mvalue ) {
							$r = $this->add_element_helper( $name, $value, $mvalue, $additional_currencies, $is_addon );
							foreach ( $r as $rkey => $rvalue ) {
								$options[ $tab ][] = $rvalue;
							}
						}
					} else {

						if ( is_array( $value ) && 2 === count( $value ) ) {
							$args  = $value[1];
							$value = $value[0];
						}

						$method = apply_filters( 'wc_epo_add_element_method', 'add_setting_' . $value, $key, $value, $name, $settings, $is_addon, $tabs_override );

						$class_to_use = apply_filters( 'wc_epo_add_element_class', THEMECOMPLETE_EPO_BUILDER(), $key, $value, $name, $settings, $is_addon, $tabs_override );

						if ( is_callable( [ $class_to_use, $method ] ) ) {
							if ( $args ) {
								$_value = $class_to_use->$method( $name, $args );
							} else {
								$_value = $class_to_use->$method( $name );
							}

							if ( isset( $_value['_multiple_values'] ) ) {
								foreach ( $_value['_multiple_values'] as $mkey => $mvalue ) {
									$r = $this->add_element_helper( $name, $value, $mvalue, $additional_currencies, $is_addon );
									foreach ( $r as $rkey => $rvalue ) {
										$options[ $tab ][] = $rvalue;
									}
								}
							} else {
								$r = $this->add_element_helper( $name, $value, $_value, $additional_currencies, $is_addon );
								foreach ( $r as $rkey => $rvalue ) {
									$options[ $tab ][] = $rvalue;
								}
							}
						}
					}
				}
			}
		}

		if ( ! empty( $tabs_override ) ) {
			if ( ! isset( $tabs_override['label_options'] ) ) {
				$tabs_override['label_options'] = 0;
			}
			if ( ! isset( $tabs_override['conditional_logic'] ) ) {
				$tabs_override['conditional_logic'] = 0;
			}
			if ( ! isset( $tabs_override['css_settings'] ) ) {
				$tabs_override['css_settings'] = 0;
			}
			if ( ! isset( $tabs_override['woocommerce_settings'] ) ) {
				$tabs_override['woocommerce_settings'] = 0;
			}
			if ( ! isset( $tabs_override['repeater_settings'] ) ) {
				$tabs_override['repeater_settings'] = 0;
			}
			foreach ( $tabs as $tab ) {
				if ( ! isset( $tabs_override[ $tab ] ) ) {
					$tabs_override[ $tab ] = 0;
				}
			}
		} else {
			$tabs_override['label_options']        = 1;
			$tabs_override['conditional_logic']    = 1;
			$tabs_override['css_settings']         = 1;
			$tabs_override['woocommerce_settings'] = 1;
			$tabs_override['repeater_settings']    = 1;
			foreach ( $tabs as $tab ) {
				$tabs_override[ $tab ] = 1;
			}
		}

		if ( 'multiple_file_upload' === $name ) {
			$tabs_override['repeater_settings'] = 0;
		}

		$post = isset( $_GET ) && isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post = get_post( $post );
		if ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post->post_type ) {
			$tabs_override['conditional_logic'] = 0;
		}

		$counter       = 1;
		$options_array = [];
		$header_array  = [];
		foreach ( $options as $tab => $option ) {

			if ( ! empty( $tabs_override[ $tab ] ) ) {
				$counter ++;
				$options_array = array_merge(
					$options_array,
					// add $tab options.
					$this->prepend_div( $name . $counter ),
					apply_filters( 'wc_epo_admin_element_general_options', $option ),
					$this->append_div( $name . $counter )
				);
				if ( 'general_options' === $tab ) {
					$header_name = esc_html__( 'General options', 'woocommerce-tm-extra-product-options' );
					$header_icon = 'tcfa tcfa-cog';
					$header_slug = 'tma-tab-general';
				} elseif ( isset( $header_names[ $tab ] ) ) {
					$header_name = isset( $header_names[ $tab ]['name'] ) ? $header_names[ $tab ]['name'] : esc_html__( 'General Settings', 'woocommerce-tm-extra-product-options' );
					$header_icon = isset( $header_names[ $tab ]['icon'] ) ? $header_names[ $tab ]['icon'] : 'tcfa tcfa-cog';
					$header_slug = isset( $header_names[ $tab ]['slug'] ) ? $header_names[ $tab ]['slug'] . ' tma-tab-extra' : 'tma-tab-general tma-tab-extra';
				}
				$header_array = array_merge(
					$header_array,
					// add $tab options.
					$this->prepend_tab(
						$name . $counter,
						[ $header_icon, $header_name ],
						'closed',
						$header_slug
					)
				);
			}
		}

		return array_merge(
			$this->prepend_div( '', 'tm-tabs' ),
			// add headers.
			$this->prepend_div( $name, 'tm-tab-headers' ),
			! empty( $tabs_override['label_options'] ) ? $this->prepend_tab(
				$name . '1',
				[
					'tcfa tcfa-tag',
					esc_html__( 'Label options', 'woocommerce-tm-extra-product-options' ),
				],
				'open',
				'tma-tab-label'
			) : [],
			$header_array,
			! empty( $tabs_override['conditional_logic'] ) ? $this->prepend_tab(
				$name . ( $counter + 1 ),
				[
					'tcfa tcfa-lightbulb',
					esc_html__( 'Conditional Logic', 'woocommerce-tm-extra-product-options' ),
				],
				'closed',
				'tma-tab-logic'
			) : [],
			! empty( $tabs_override['css_settings'] ) ? $this->prepend_tab(
				$name . ( $counter + 2 ),
				[
					'tcfa tcfa-file-alt',
					esc_html__( 'CSS settings', 'woocommerce-tm-extra-product-options' ),
				],
				'closed',
				'tma-tab-css'
			) : [],
			! empty( $tabs_override['woocommerce_settings'] ) ? $this->prepend_tab(
				$name . ( $counter + 3 ),
				[
					'tcfa tcfa-shopping-bag',
					esc_html__( 'WooCommerce settings', 'woocommerce-tm-extra-product-options' ),
				],
				'closed',
				'tma-tab-woocommerce'
			) : [],
			! empty( $tabs_override['repeater_settings'] ) ? $this->prepend_tab(
				$name . ( $counter + 4 ),
				[
					'tcfa tcfa-redo',
					esc_html__( 'Repeater settings', 'woocommerce-tm-extra-product-options' ),
				],
				'closed',
				'tma-tab-repeater'
			) : [],
			$this->append_div( $name ),
			// add Label options.
			! empty( $tabs_override['label_options'] ) ? $this->prepend_div( $name . '1' ) : [],
			! empty( $tabs_override['label_options'] ) ? $this->get_header_array( $name . '_header', 'element', $name ) : [],
			! empty( $tabs_override['label_options'] ) ? $this->get_divider_array( $name . '_divider', 0 ) : [],
			! empty( $tabs_override['label_options'] ) ? $this->append_div( $name . '1' ) : [],
			// add options.
			$options_array,
			// add Contitional logic.
			'variations' !== $name && ! empty( $tabs_override['conditional_logic'] ) ? $this->prepend_div( $name . ( $counter + 1 ) ) : [],
			'variations' !== $name && ! empty( $tabs_override['conditional_logic'] ) ? $this->prepend_logic( $name ) : [],
			'variations' !== $name && ! empty( $tabs_override['conditional_logic'] ) ? $this->append_div( $name . ( $counter + 1 ) ) : [],
			// add CSS settings.
			'variations' !== $name && ! empty( $tabs_override['css_settings'] ) ? $this->prepend_div( $name . ( $counter + 2 ) ) : [],
			'variations' !== $name && ! empty( $tabs_override['css_settings'] ) ? apply_filters(
				'wc_epo_admin_element_css_settings',
				[
					[
						'id'      => $name . '_class',
						'default' => '',
						'type'    => 'text',
						'tags'    => [
							'class' => 't',
							'id'    => 'builder_' . $name . '_class',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_class][]',
							'value' => '',
						],
						'label'   => esc_html__( 'Element class name', 'woocommerce-tm-extra-product-options' ),
						'desc'    => esc_html__( 'Enter an extra class name to add to this element', 'woocommerce-tm-extra-product-options' ),
					],
					[
						'id'      => $name . '_container_id',
						'default' => '',
						'type'    => 'text',
						'tags'    => [
							'class' => 't',
							'id'    => 'builder_' . $name . '_container_id',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_container_id][]',
							'value' => '',
						],
						'label'   => esc_html__( 'Element container id', 'woocommerce-tm-extra-product-options' ),
						'desc'    => esc_html__( 'Enter an id for the container of the element.', 'woocommerce-tm-extra-product-options' ),
					],
				]
			) : [],
			! empty( $tabs_override['css_settings'] ) ? $this->append_div( $name . ( $counter + 2 ) ) : [],
			// add WooCommerce settings.
			'variations' !== $name && ! empty( $tabs_override['woocommerce_settings'] ) ? $this->prepend_div( $name . ( $counter + 3 ) ) : [],
			'variations' !== $name && ! empty( $tabs_override['woocommerce_settings'] ) ? apply_filters(
				'wc_epo_admin_element_woocommerce_settings',
				[
					[
						'id'          => $name . '_include_tax_for_fee_price_type',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_include_tax_for_fee_price_type',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_include_tax_for_fee_price_type][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'Inherit product setting', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'yes',
							],
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => 'no',
							],
						],
						'label'       => esc_html__( 'Include tax for Fee price type', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to include tax for Fee price type on this element.', 'woocommerce-tm-extra-product-options' ),
						'required'    => [
							'.tc-element-setting-fee' => [
								'operator' => 'is',
								'value'    => '1',
							],
						],
					],
					[
						'id'          => $name . '_tax_class_for_fee_price_type',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_tax_class_for_fee_price_type',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_tax_class_for_fee_price_type][]',
						],
						'options'     => $this->get_tax_classes(),
						'label'       => esc_html__( 'Tax class for Fee price type', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose the tax class for Fee price type on this element.', 'woocommerce-tm-extra-product-options' ),
						'required'    => [
							'.tc-element-setting-fee' => [
								'operator' => 'is',
								'value'    => '1',
							],
						],
					],
					[
						'id'          => $name . '_hide_element_label_in_cart',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_hide_element_label_in_cart',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_hide_element_label_in_cart][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'hidden',
							],
						],
						'label'       => esc_html__( 'Hide element label in cart', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to hide the element label in the cart or not.', 'woocommerce-tm-extra-product-options' ),
					],
					[
						'id'          => $name . '_hide_element_value_in_cart',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_hide_element_value_in_cart',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_hide_element_value_in_cart][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'No, but hide price', 'woocommerce-tm-extra-product-options' ),
								'value' => 'noprice',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'hidden',
							],
							[
								'text'  => esc_html__( 'Yes, but show price', 'woocommerce-tm-extra-product-options' ),
								'value' => 'price',
							],
						],
						'label'       => esc_html__( 'Hide element value in cart', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to hide the element value in the cart or not.', 'woocommerce-tm-extra-product-options' ),
					],
					[
						'id'          => $name . '_hide_element_label_in_order',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_hide_element_label_in_order',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_hide_element_label_in_order][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'hidden',
							],
						],
						'label'       => esc_html__( 'Hide element label in order', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to hide the element label in the order or not.', 'woocommerce-tm-extra-product-options' ),
					],
					[
						'id'          => $name . '_hide_element_value_in_order',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_hide_element_value_in_order',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_hide_element_value_in_order][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'No, but hide price', 'woocommerce-tm-extra-product-options' ),
								'value' => 'noprice',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'hidden',
							],
							[
								'text'  => esc_html__( 'Yes, but show price', 'woocommerce-tm-extra-product-options' ),
								'value' => 'price',
							],
						],
						'label'       => esc_html__( 'Hide element value in order', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to hide the element value in the order or not.', 'woocommerce-tm-extra-product-options' ),
					],
					[
						'id'          => $name . '_hide_element_label_in_floatbox',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_hide_element_label_in_floatbox',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_hide_element_label_in_floatbox][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'hidden',
							],
						],
						'label'       => esc_html__( 'Hide element label in floating totals box', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to hide the element label in the floating totals box or not.', 'woocommerce-tm-extra-product-options' ),
					],
					[
						'id'          => $name . '_hide_element_value_in_floatbox',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'select',
						'tags'        => [
							'id'   => 'builder_' . $name . '_hide_element_value_in_floatbox',
							'name' => 'tm_meta[tmfbuilder][' . $name . '_hide_element_value_in_floatbox][]',
						],
						'options'     => [
							[
								'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
								'value' => '',
							],
							[
								'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
								'value' => 'hidden',
							],
						],
						'label'       => esc_html__( 'Hide element value in floating totals box', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Choose whether to hide the element value in the floating totals box or not.', 'woocommerce-tm-extra-product-options' ),
					],
				]
			) : [],
			! empty( $tabs_override['woocommerce_settings'] ) ? $this->append_div( $name . ( $counter + 3 ) ) : [],
			// add Repeater settings.
			'variations' !== $name && ! empty( $tabs_override['repeater_settings'] ) ? $this->prepend_div( $name . ( $counter + 4 ) ) : [],
			'variations' !== $name && ! empty( $tabs_override['repeater_settings'] ) ? apply_filters(
				'wc_epo_admin_element_repeater_settings',
				[
					[
						'id'       => $name . '_repeater',
						'default'  => '',
						'type'     => 'checkbox',
						'tags'     => [
							'class' => 'c activate-element-repeater',
							'id'    => 'builder_' . $name . '_repeater',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_repeater][]',
							'value' => '1',
						],
						'label'    => esc_html__( 'Enable repeater', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'This will make this element repeatable.', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.element-connector' => [
								'operator' => 'is',
								'value'    => '',
							],
						],
					],
					[
						'id'       => $name . '_repeater_quantity',
						'default'  => '',
						'type'     => 'checkbox',
						'tags'     => [
							'class' => 'c activate-element-repeater-quantity',
							'id'    => 'builder_' . $name . '_repeater_quantity',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_repeater_quantity][]',
							'value' => '1',
						],
						'label'    => esc_html__( 'Bind repeater with product quantity', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'This will make this element repeatable based on the product quantity.', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.activate-element-repeater' => [
								'operator' => 'isnot',
								'value'    => '',
							],
							'.element-connector'         => [
								'operator' => 'is',
								'value'    => '',
							],
						],
					],
					[
						'id'          => $name . '_repeater_min_rows',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'number',
						'tags'        => [
							'class' => 'n',
							'id'    => 'builder_' . $name . '_repeater_min_rows',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_repeater_min_rows][]',
							'value' => '',
							'min'   => 1,
						],
						'label'       => esc_html__( 'Repeater minimum limit', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Set the minimum number of rows a user can add for the repeater field.', 'woocommerce-tm-extra-product-options' ),
						'required'    => [
							'.activate-element-repeater' => [
								'operator' => 'isnot',
								'value'    => '',
							],
							'.element-connector'         => [
								'operator' => 'is',
								'value'    => '',
							],
						],
					],
					[
						'id'          => $name . '_repeater_max_rows',
						'wpmldisable' => 1,
						'default'     => '',
						'type'        => 'number',
						'tags'        => [
							'class' => 'n',
							'id'    => 'builder_' . $name . '_repeater_max_rows',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_repeater_max_rows][]',
							'value' => '',
							'min'   => 1,
						],
						'label'       => esc_html__( 'Repeater maximum limit', 'woocommerce-tm-extra-product-options' ),
						'desc'        => esc_html__( 'Set the maximum number of rows a user can add for the repeater field.', 'woocommerce-tm-extra-product-options' ),
						'required'    => [
							'.activate-element-repeater' => [
								'operator' => 'isnot',
								'value'    => '',
							],
							'.element-connector'         => [
								'operator' => 'is',
								'value'    => '',
							],
						],
					],
					[
						'id'       => $name . '_repeater_button_label',
						'default'  => '',
						'type'     => 'text',
						'tags'     => [
							'class' => 't',
							'id'    => 'builder_' . $name . '_repeater_button_label',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_repeater_button_label][]',
							'value' => '',
						],
						'label'    => esc_html__( 'Repeater Button Label', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'Leave this blank for the default label.', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.activate-element-repeater' => [
								'operator' => 'isnot',
								'value'    => '',
							],
							'.element-connector'         => [
								'operator' => 'is',
								'value'    => '',
							],
						],
					],
				]
			) : [],
			// Radio buttons connector setting.
			'radiobuttons' === $name && ! empty( $tabs_override['repeater_settings'] ) ? apply_filters(
				'wc_epo_admin_element_connector_settings',
				[
					[
						'id'       => $name . '_connector',
						'default'  => '',
						'type'     => 'text',
						'tags'     => [
							'class' => 't element-connector',
							'id'    => 'builder_' . $name . '_connector',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_connector][]',
							'value' => '',
						],
						'label'    => esc_html__( 'Radio Button Connector ID', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'Enter a custom id to connect different radio button elements.', 'woocommerce-tm-extra-product-options' ),
						'required' => [
							'.activate-element-repeater' => [
								'operator' => 'is',
								'value'    => '',
							],
						],
					],
				]
			) : [],
			! empty( $tabs_override['repeater_settings'] ) ? $this->append_div( $name . ( $counter + 4 ) ) : [],
			$this->append_div( '' )
		);
	}

	/**
	 * Get tax classes
	 *
	 * @since 6.0
	 */
	public function get_tax_classes() {
		// Get tax class options.
		$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
		if ( empty( $tax_classes ) && class_exists( 'WC_Tax' ) ) {
			$tax_classes = WC_Tax::get_tax_classes();
		}
		$classes_options      = [];
		$classes_options['']  = esc_html__( 'Inherit product tax class', 'woocommerce-tm-extra-product-options' );
		$classes_options['@'] = esc_html__( 'Standard', 'woocommerce-tm-extra-product-options' );
		if ( $tax_classes ) {
			foreach ( $tax_classes as $class ) {
				$classes_options[ sanitize_title( $class ) ] = esc_html( $class );
			}
		}
		$classes = [];

		foreach ( $classes_options as $value => $label ) {
			$classes[] = [
				'text'  => esc_html( $label ),
				'value' => esc_attr( $value ),
			];
		}

		return $classes;
	}

	/**
	 * Remove prefix
	 *
	 * @param string $str The string to remove the prefix from.
	 * @param string $prefix The prefix to remove from the string.
	 * @since 6.0
	 * @access private
	 */
	private function remove_prefix( $str = '', $prefix = '' ) {
		if ( substr( $str, 0, strlen( $prefix ) ) === $prefix ) {
			$str = substr( $str, strlen( $prefix ) );
		}

		return $str;
	}

	/**
	 * Add element helper
	 *
	 * @param string        $name The element type.
	 * @param mixed         $value The setting value.
	 * @param array         $_value Array of element settings.
	 * @param array|boolean $additional_currencies Additional currencies for the price.
	 * @param boolean       $is_addon If this is an addon element.
	 * @since 6.0
	 * @access private
	 */
	private function add_element_helper( $name = '', $value = '', $_value = [], $additional_currencies = false, $is_addon = false ) {

		$return = [];

		if ( 'price' === $value ) {

			if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
				$_copy_value      = $_value;
				$_value['label'] .= ' <span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span>';
				$return[]         = $_value;
				foreach ( $additional_currencies as $ckey => $currency ) {
					$copy_value           = $_copy_value;
					$copy_value['id']    .= '_' . $currency;
					$copy_value['label'] .= ' <span class="tm-choice-currency">' . $currency . '</span>';
					/* translators: %s the base currency */
					$copy_value['desc']         = sprintf( esc_html__( 'Leave it blank to calculate it automatically from the %s price', 'woocommerce-tm-extra-product-options' ), THEMECOMPLETE_EPO_HELPER()->wc_base_currency() );
					$copy_value['tags']['id']   = 'builder_' . $name . '_price_' . $currency;
					$copy_value['tags']['name'] = 'tm_meta[tmfbuilder][' . $name . '_price_' . $currency . '][]';
					$return[]                   = $copy_value;
				}
			} else {
				$return[] = $_value;
			}
		} elseif ( 'sale_price' === $value ) {

			if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
				$_copy_value      = $_value;
				$_value['label'] .= ' <span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span>';
				$return[]         = $_value;
				foreach ( $additional_currencies as $ckey => $currency ) {
					$copy_value           = $_copy_value;
					$copy_value['id']    .= '_' . $currency;
					$copy_value['label'] .= ' <span class="tm-choice-currency">' . $currency . '</span>';
					/* translators: %s the base currency */
					$copy_value['desc']         = sprintf( esc_html__( 'Leave it blank to calculate it automatically from the %s sale price', 'woocommerce-tm-extra-product-options' ), THEMECOMPLETE_EPO_HELPER()->wc_base_currency() );
					$copy_value['tags']['id']   = 'builder_' . $name . '_sale_price_' . $currency;
					$copy_value['tags']['name'] = 'tm_meta[tmfbuilder][' . $name . '_sale_price_' . $currency . '][]';
					$return[]                   = $copy_value;
				}
			} else {
				$return[] = $_value;
			}
		} else {
			$return[] = $_value;
		}

		if ( isset( $_value['id'] ) ) {
			if ( $is_addon ) {
				THEMECOMPLETE_EPO_BUILDER()->addons_attributes[] = $this->remove_prefix( $_value['id'], $name . '_' );
			}
			THEMECOMPLETE_EPO_BUILDER()->default_attributes[] = $this->remove_prefix( $_value['id'], $name . '_' );
		}

		return $return;
	}

	/**
	 * Show logic select box
	 *
	 * @since 6.0
	 * @access public
	 */
	public function builder_showlogic() {
		?>
		<div class="builder-logic-div">
		<div class="tc-row nopadding">
			<select class="epo-rule-toggle">
				<option value="show"><?php esc_html_e( 'Show', 'woocommerce-tm-extra-product-options' ); ?></option>
				<option value="hide"><?php esc_html_e( 'Hide', 'woocommerce-tm-extra-product-options' ); ?></option>
			</select>
			<span><?php esc_html_e( 'this field if', 'woocommerce-tm-extra-product-options' ); ?></span>
			<select class="epo-rule-what">
				<option value="all"><?php esc_html_e( 'all', 'woocommerce-tm-extra-product-options' ); ?></option>
				<option value="any"><?php esc_html_e( 'any', 'woocommerce-tm-extra-product-options' ); ?></option>
			</select>
			<span><?php esc_html_e( 'of these rules match', 'woocommerce-tm-extra-product-options' ); ?>:</span>
		</div>
		<div class="tm-logic-wrapper"></div>
		</div>
		<?php
	}
}
