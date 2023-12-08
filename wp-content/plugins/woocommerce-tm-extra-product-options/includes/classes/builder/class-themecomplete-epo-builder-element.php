<?php
/**
 * Extra Product Options Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.4
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
	 * @var boolean
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
	 * @var string
	 */
	public $is_post;

	/**
	 * The type of this element
	 * Can either be '', 'single', 'variations', 'multipleall'or 'multiple'
	 *
	 * @var string|boolean
	 */
	public $type;

	/**
	 * The name prefix for this element
	 *
	 * @var string|boolean
	 */
	public $post_name_prefix;

	/**
	 * The type of fee this element can take
	 * Can be '', 'single' or 'multiple'
	 *
	 * @var string|boolean
	 */
	public $fee_type;

	/**
	 * Tags for registring the element to the popup
	 *
	 * @var string|boolean
	 */
	public $tags;

	/**
	 * If the element can be shown on the builder as a selectable element
	 *
	 * @var boolean
	 */
	public $show_on_backend;

	/**
	 * If the field should only be one time in the builder
	 *
	 * @var boolean
	 */
	public $one_time_field = false;

	/**
	 * If the element cannot be selected in the builder
	 *
	 * @var boolean
	 */
	public $no_selection = false;

	/**
	 * If the element should not be displayed in the frontend
	 *
	 * @var boolean
	 */
	public $no_frontend_display = false;

	/**
	 * Holder for the element properties
	 *
	 * @var array<mixed>
	 */
	public $properties = [];

	/**
	 * Holder for the element attributes
	 * Used for magic methods
	 *
	 * @var array<mixed>
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
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->attributes ) ) {
			return $this->attributes[ $name ];
		}
	}

	/**
	 * Sets the element attributes
	 *
	 * @param array<mixed> $attributes The attributes array.
	 * @return void
	 */
	public function set_attributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 * @return void
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
	 * @return void
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
	 * @return array<mixed>
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
	 * @return void
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
	 * @return array<mixed>
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
	 * @return void
	 */
	public function append_div_callback() {
		echo '</div>';
	}

	/**
	 * End div
	 *
	 * @param string $id The element name.
	 * @since 6.0
	 * @return array<mixed>
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
	 * @return array<mixed>
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
					'clogic'  => true,
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
					'id'         => $id . '_logicrules',
					'logicrules' => true,
					'default'    => '',
					'nodiv'      => 1,
					'type'       => 'hidden',
					'tags'       => [
						'class' => 'tm-builder-logicrules',
						'name'  => 'tm_meta[tmfbuilder][' . $id . '_logicrules][]',
						'value' => '',
					],
					'label'      => '',
					'desc'       => '',
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
	 *
	 * @since 6.0
	 * @return array<mixed> List of common element options adjusted by element internal id.
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
							'text'  => ( 'section' === $type ) ? esc_html__( 'Above the fields', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Above field', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => ( 'section' === $type ) ? esc_html__( 'Left of the fields', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Left of the field', 'woocommerce-tm-extra-product-options' ),
							'value' => 'left',
						],
						[
							'text'  => ( 'section' === $type ) ? esc_html__( 'Right of the fields', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Right of the field', 'woocommerce-tm-extra-product-options' ),
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
							'text'  => ( 'section' === $type ) ? esc_html__( 'Above the fields', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Above field', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => ( 'section' === $type ) ? esc_html__( 'Below the fields', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Below field', 'woocommerce-tm-extra-product-options' ),
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
	 * @since 6.0
	 * @return array<mixed> Element divider options adjusted by element internal id.
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
	 * Generate each tab status
	 *
	 * @param array<mixed> $epo_tabs_ids The tab array ids.
	 * @param string       $name The element name.
	 * @param array<mixed> $settings_array Array of settings.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @param array<mixed> $extra_tabs Extra tabs data.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function get_element_tabs( $epo_tabs_ids = [], $name = '', $settings_array = [], $tabs_override = [], $extra_tabs = [] ) {
		$epo_tabs = array_combine( $epo_tabs_ids, array_fill( 0, count( $epo_tabs_ids ), 0 ) );

		$settings_tabs_array = [ 'general_options' => $settings_array ];

		$header_names = [];

		if ( ! empty( $extra_tabs ) && is_array( $extra_tabs ) ) {
			foreach ( $extra_tabs as $tab => $data ) {
				if ( isset( $data['tab_data'] ) ) {
					$settings_tabs_array[ $tab ] = $data['tab_data'];
				}
				if ( isset( $data['header_data'] ) ) {
					$header_names[ $tab ] = $data['header_data'];
				}
			}
		}

		$tabs     = array_keys( $settings_tabs_array );
		$epo_tabs = array_merge( $epo_tabs, array_fill_keys( $tabs, 0 ) );

		if ( ! empty( $tabs_override ) ) {
			// Get the missing keys in epo_tabs.
			$missing_keys = array_diff( array_keys( $epo_tabs ), array_keys( $tabs_override ) );
			// Create a new array with the missing keys set to 0.
			$new_keys = array_fill_keys( $missing_keys, 0 );
			// Merge the new array with tabs_override.
			$tabs_override = array_merge( $tabs_override, $new_keys );
		} else {
			// Set all tabs to 1.
			$tabs_override = array_fill_keys( array_keys( $epo_tabs ), 1 );
		}

		// Disable repeater tab for multiple upload element.
		if ( 'multiple_file_upload' === $name ) {
			$tabs_override['repeater_settings'] = 0;
		}

		// Disable conditional logic tab for Option Templates.

		// @phpstan-ignore-next-line
		$post = isset( $_GET ) && isset( $_GET['post'] ) ? absint( stripslashes_deep( $_GET['post'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $post ) {
			$post = get_post( $post );
			if ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post->post_type ) {
				$tabs_override['conditional_logic'] = 0;
			}
		}

		return [
			'tabs_override'       => $tabs_override,
			'settings_tabs_array' => $settings_tabs_array,
			'header_names'        => $header_names,
		];
	}

	/**
	 * Generate label_options tab
	 *
	 * @param string       $name The element name.
	 * @param integer      $counter The current counter.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function generate_label_options( $name = '', $counter = 1, $tabs_override = [] ) {
		// We override the counter to have the label options as the first tab.
		$counter = 1;

		$label_options_data = [];
		if ( ! empty( $tabs_override['label_options'] ) ) {
			$label_options_data = array_merge(
				$label_options_data,
				$this->prepend_div( $name . $counter ),
				$this->get_header_array( $name . '_header', 'element', $name ),
				$this->get_divider_array( $name . '_divider', 0 ),
				$this->append_div( $name . $counter )
			);
		}
		$label_options_data = apply_filters( 'wc_epo_admin_add_element_label_options_data', $label_options_data, $name, $tabs_override, $counter );

		$header_data = ! empty( $tabs_override['label_options'] ) ? $this->prepend_tab(
			$name . $counter,
			[
				'tcfa tcfa-tag',
				esc_html__( 'Label options', 'woocommerce-tm-extra-product-options' ),
			],
			'open',
			'tma-tab-label'
		) : [];

		return [
			'tab_data'    => $label_options_data,
			'header_data' => $header_data,
		];
	}

	/**
	 * Generate conditional_logic tab
	 *
	 * @param string       $name The element name.
	 * @param integer      $counter The current counter.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function generate_conditional_logic( $name = '', $counter = 1, $tabs_override = [] ) {

		$conditional_logic_data = [];
		if ( 'variations' !== $name ) {
			if ( ! empty( $tabs_override['conditional_logic'] ) ) {
				$conditional_logic_data = array_merge(
					$conditional_logic_data,
					$this->prepend_div( $name . ( $counter + 1 ) ),
					$this->prepend_logic( $name ),
					$this->append_div( $name . ( $counter + 1 ) )
				);
			}
		}
		$conditional_logic_data = apply_filters( 'wc_epo_admin_add_element_conditional_logic_data', $conditional_logic_data, $name, $tabs_override, $counter );

		$header_data = ! empty( $tabs_override['conditional_logic'] ) ? $this->prepend_tab(
			$name . ( $counter + 1 ),
			[
				'tcfa tcfa-lightbulb',
				esc_html__( 'Conditional Logic', 'woocommerce-tm-extra-product-options' ),
			],
			'closed',
			'tma-tab-logic'
		) : [];

		return [
			'tab_data'    => $conditional_logic_data,
			'header_data' => $header_data,
		];
	}

	/**
	 * Generate css_settings tab
	 *
	 * @param string       $name The element name.
	 * @param integer      $counter The current counter.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function generate_css_settings( $name = '', $counter = 1, $tabs_override = [] ) {

		$css_settings_data = [];
		if ( 'variations' !== $name ) {
			if ( ! empty( $tabs_override['css_settings'] ) ) {
				$css_settings_data = array_merge(
					$css_settings_data,
					$this->prepend_div( $name . ( $counter + 2 ) ),
					apply_filters(
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
					),
					$this->append_div( $name . ( $counter + 2 ) )
				);
			}
		}
		$css_settings_data = apply_filters( 'wc_epo_admin_add_element_css_settings_data', $css_settings_data, $name, $tabs_override, $counter );

		$header_data = ! empty( $tabs_override['css_settings'] ) ? $this->prepend_tab(
			$name . ( $counter + 2 ),
			[
				'tcfa tcfa-file-alt',
				esc_html__( 'CSS settings', 'woocommerce-tm-extra-product-options' ),
			],
			'closed',
			'tma-tab-css'
		) : [];

		return [
			'tab_data'    => $css_settings_data,
			'header_data' => $header_data,
		];
	}

	/**
	 * Generate woocommerce_settings tab
	 *
	 * @param string       $name The element name.
	 * @param integer      $counter The current counter.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function generate_woocommerce_settings( $name = '', $counter = 1, $tabs_override = [] ) {

		$woocommerce_settings_data = [];
		if ( 'variations' !== $name ) {
			if ( ! empty( $tabs_override['woocommerce_settings'] ) ) {
				$woocommerce_settings_data = array_merge(
					$woocommerce_settings_data,
					$this->prepend_div( $name . ( $counter + 3 ) ),
					apply_filters(
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
					),
					$this->append_div( $name . ( $counter + 3 ) )
				);
			}
		}
		$woocommerce_settings_data = apply_filters( 'wc_epo_admin_add_element_woocommerce_settings_data', $woocommerce_settings_data, $name, $tabs_override, $counter );

		$header_data = ! empty( $tabs_override['woocommerce_settings'] ) ? $this->prepend_tab(
			$name . ( $counter + 3 ),
			[
				'tcfa tcfa-shopping-bag',
				esc_html__( 'WooCommerce settings', 'woocommerce-tm-extra-product-options' ),
			],
			'closed',
			'tma-tab-woocommerce'
		) : [];

		return [
			'tab_data'    => $woocommerce_settings_data,
			'header_data' => $header_data,
		];
	}

	/**
	 * Generate repeater_settings tab
	 *
	 * @param string       $name The element name.
	 * @param integer      $counter The current counter.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function generate_repeater_settings( $name = '', $counter = 1, $tabs_override = [] ) {

		$repeater_settings_data = [];
		if ( 'variations' !== $name ) {
			if ( ! empty( $tabs_override['repeater_settings'] ) ) {
				$repeater_settings_data = array_merge(
					$repeater_settings_data,
					$this->prepend_div( $name . ( $counter + 4 ) ),
					apply_filters(
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
									'.element-connector' => [
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
									'.element-connector' => [
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
									'.element-connector' => [
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
									'.element-connector' => [
										'operator' => 'is',
										'value'    => '',
									],
								],
							],
						]
					)
				);
				// Radio buttons connector setting.
				if ( 'radiobuttons' === $name ) {
					$repeater_settings_data = array_merge(
						$repeater_settings_data,
						apply_filters(
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
						)
					);
				}
				$repeater_settings_data = array_merge(
					$repeater_settings_data,
					$this->append_div( $name . ( $counter + 4 ) )
				);
			}
		}
		$repeater_settings_data = apply_filters( 'wc_epo_admin_add_element_repeater_settings_data', $repeater_settings_data, $name, $tabs_override, $counter );

		$header_data = ! empty( $tabs_override['repeater_settings'] ) ? $this->prepend_tab(
			$name . ( $counter + 4 ),
			[
				'tcfa tcfa-redo',
				esc_html__( 'Repeater settings', 'woocommerce-tm-extra-product-options' ),
			],
			'closed',
			'tma-tab-repeater'
		) : [];

		return [
			'tab_data'    => $repeater_settings_data,
			'header_data' => $header_data,
		];
	}

	/**
	 * Fetch shipping methods
	 * for use in a select box
	 *
	 * @param array<mixed> $selected_methods The selected shipping methods.
	 * @since  6.4
	 * @return array<mixed>
	 */
	public function fetch_shipping_methods_array( $selected_methods = [] ) {
		$list = [];

		$shipping_methods = wp_cache_get( 'wc_epo_shipping_methods' );

		if ( false === $shipping_methods ) {
			$shipping_methods = WC()->shipping()->load_shipping_methods();
			wp_cache_set( 'wc_epo_shipping_methods', $shipping_methods );
		}

		foreach ( $shipping_methods as $method_id => $method ) {
			if ( ! $method || $method instanceof __PHP_Incomplete_Class ) {
				continue;
			}
			if ( method_exists( $method, 'supports' ) && $method->supports( 'shipping-zones' ) ) {
				$list[] = [
					'optgroupstart' => $method->get_method_title(),
				];
				$list[] = [
					/* translators: %s Shipping method title */
					'text'     => sprintf( esc_html__( 'All &quot;%s&quot; Method Instances', 'woocommerce-tm-extra-product-options' ), esc_html( $method->get_method_title() ) ),
					'value'    => $method_id,
					'selected' => in_array( $method_id, $selected_methods ), // phpcs:ignore WordPress.PHP.StrictInArray
				];

				$zones = wp_cache_get( 'wc_epo_shipping_zones' );

				if ( false === $zones ) {

					$zones = WC_Shipping_Zones::get_zones();

					if ( ! isset( $zones[0] ) ) {
						$rest_of_world                = WC_Shipping_Zones::get_zone_by();
						$zones[0]                     = $rest_of_world->get_data();
						$zones[0]['shipping_methods'] = $rest_of_world->get_shipping_methods();
					}

					wp_cache_set( 'wc_epo_shipping_zones', $zones );
				}

				foreach ( $zones as $zone ) {

					if ( ! empty( $zone['shipping_methods'] ) ) {

						$zone_name = $zone['zone_name'];

						foreach ( $zone['shipping_methods'] as $instance_id => $method_instance ) {

							if ( $method_instance->id !== $method->id ) {
								continue;
							}

							$option_id = $method_instance->get_rate_id();
							/* translators: %s%1$s Shipping method title %2$s Method id */
							$method_title = sprintf( __( '&quot;%1$s&quot; (Instance ID: %2$s)', 'woocommerce-tm-extra-product-options' ), $method_instance->get_title(), $instance_id );
							/* translators: %s%1$s Shipping zone name %2$s Method title */
							$option_name = sprintf( __( '%1$s &ndash; %2$s', 'woocommerce-tm-extra-product-options' ), $zone_name, $method_title );

							$list[] = [
								'text'     => $option_name,
								'value'    => $option_id,
								'selected' => in_array( $option_id, $selected_methods ), // phpcs:ignore WordPress.PHP.StrictInArray
							];
						}
					}
				}
				$list[] = [
					'optgroupend' => true,
				];
			} elseif ( 'legacy_flat_rate' === $method_id ) {
				$list[] = [
					'optgroupstart' => esc_html__( 'Flat Rates (Legacy)', 'woocommerce-tm-extra-product-options' ),
				];
				$list[] = [
					'text'     => $method->get_title() . ' ' . esc_html__( '(Legacy)', 'woocommerce-tm-extra-product-options' ),
					'value'    => $method_id,
					'selected' => in_array( $method_id, $selected_methods ), // phpcs:ignore WordPress.PHP.StrictInArray
				];
				// Additional legacy flat rate options.
				$additional_flat_rate_options = (array) explode( "\n", $method->get_option( 'options' ) );

				foreach ( $additional_flat_rate_options as $option ) {

					$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

					if ( count( $this_option ) !== 3 ) {
						continue;
					}

					$option_id = 'legacy_flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

					$list[] = [
						'text'     => $this_option[0] . ' ' . esc_html__( '(Legacy)', 'woocommerce-tm-extra-product-options' ),
						'value'    => $option_id,
						'selected' => in_array( $option_id, $selected_methods ), // phpcs:ignore WordPress.PHP.StrictInArray
					];
				}
				$list[] = [
					'optgroupend' => true,
				];
			} else {
				$is_legacy = ( 0 === strpos( $method_id, 'legacy_' ) );
				$list[]    = [
					'text'     => $method->get_title() . ( $is_legacy ? ' ' . esc_html__( '(Legacy)', 'woocommerce-tm-extra-product-options' ) : '' ),
					'value'    => $method_id,
					'selected' => in_array( $method_id, $selected_methods ), // phpcs:ignore WordPress.PHP.StrictInArray
				];
			}
		}

		return $list;
	}

	/**
	 * Generate action_settings tab
	 *
	 * @param string       $name The element name.
	 * @param integer      $counter The current counter.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function generate_action_settings( $name = '', $counter = 1, $tabs_override = [] ) {

		$action_settings_data = [];
		if ( 'variations' !== $name ) {
			if ( ! empty( $tabs_override['action_settings'] ) ) {
				$action_settings_data = array_merge(
					$action_settings_data,
					$this->prepend_div( $name . ( $counter + 5 ) ),
					apply_filters(
						'wc_epo_admin_element_action_settings',
						[
							[
								'id'               => $name . '_shipping_methods_enable',
								'wpmldisable'      => 1,
								'default'          => '',
								'type'             => 'select',
								'multiple'         => 'multiple',
								'fill'             => 'shipping',
								'message0x0_class' => 'noborder',
								'tags'             => [
									'data-placeholder' => esc_attr__( 'Select shipping methods ...', 'woocommerce-tm-extra-product-options' ),
									'class'            => 'wc-shipping-methods-search shipping-methods-selector shipping-methods-enable',
									'id'               => 'builder_' . $name . '_shipping_methods_enable',
									'name'             => 'tm_meta[tmfbuilder][' . $name . '_shipping_methods_enable][]',
								],
								'options'          => $this->fetch_shipping_methods_array(),
								'label'            => esc_html__( 'Enable Shipping Methods', 'woocommerce-tm-extra-product-options' ),
								'desc'             => esc_html__( 'Select the shipping methods to enable.', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'id'        => $name . '_shipping_methods_enable_logicrules',
								'default'   => '',
								'type'      => 'hidden',
								'leftclass' => 'align-self-start',
								'tags'      => [
									'class' => 'tm-shipping-enable-logicrules',
									'name'  => 'tm_meta[tmfbuilder][' . $name . '_shipping_methods_enable_logicrules][]',
									'value' => '',
								],
								'extra'     => [ [ $this, 'shipping_showlogic' ], [] ],
								'label'     => esc_html__( 'Enable Shipping Methods Condition', 'woocommerce-tm-extra-product-options' ),
								'desc'      => esc_html__( 'Specify the conditions for enabling shipping methods based on the value of the current field. If the conditions are met, the selected shipping methods will be available for customers to choose during checkout.', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'id'               => $name . '_shipping_methods_disable',
								'wpmldisable'      => 1,
								'default'          => '',
								'type'             => 'select',
								'multiple'         => 'multiple',
								'fill'             => 'shipping',
								'message0x0_class' => 'noborder',
								'tags'             => [
									'data-placeholder' => esc_attr__( 'Select shipping methods ...', 'woocommerce-tm-extra-product-options' ),
									'class'            => 'wc-shipping-methods-search shipping-methods-selector shipping-methods-disable',
									'id'               => 'builder_' . $name . '_shipping_methods_disable',
									'name'             => 'tm_meta[tmfbuilder][' . $name . '_shipping_methods_disable][]',
								],
								'options'          => $this->fetch_shipping_methods_array(),
								'label'            => esc_html__( 'Disable Shipping Methods', 'woocommerce-tm-extra-product-options' ),
								'desc'             => esc_html__( 'Select the shipping methods to disable.', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'id'        => $name . '_shipping_methods_disable_logicrules',
								'default'   => '',
								'type'      => 'hidden',
								'leftclass' => 'align-self-start',
								'tags'      => [
									'class' => 'tm-shipping-disable-logicrules',
									'name'  => 'tm_meta[tmfbuilder][' . $name . '_shipping_methods_disable_logicrules][]',
									'value' => '',
								],
								'extra'     => [ [ $this, 'shipping_showlogic' ], [] ],
								'label'     => esc_html__( 'Disable Shipping Methods Condition', 'woocommerce-tm-extra-product-options' ),
								'desc'      => esc_html__( 'Specify the conditions for disabling shipping methods based on the value of the current field. If the conditions are met, the selected shipping methods will not be available for customers to choose during checkout.', 'woocommerce-tm-extra-product-options' ),
							],
						]
					),
					$this->append_div( $name . ( $counter + 5 ) )
				);
			}
		}
		$action_settings_data = apply_filters( 'wc_epo_admin_add_element_action_settings_data', $action_settings_data, $name, $tabs_override, $counter );

		$header_data = ! empty( $tabs_override['action_settings'] ) ? $this->prepend_tab(
			$name . ( $counter + 5 ),
			[
				'tcfa tcfa-bolt',
				esc_html__( 'Actions', 'woocommerce-tm-extra-product-options' ),
			],
			'closed',
			'tma-tab-woocommerce'
		) : [];

		return [
			'tab_data'    => $action_settings_data,
			'header_data' => $header_data,
		];
	}

	/**
	 * Add element
	 *
	 * @param string       $name The element name.
	 * @param array<mixed> $settings_array Array of settings.
	 * @param boolean      $is_addon If this is an addon element.
	 * @param array<mixed> $tabs_override Tabs array overrides.
	 * @param array<mixed> $extra_tabs Extra tabs data.
	 * @since 6.0
	 * @return array<mixed>
	 */
	public function add_element( $name = '', $settings_array = [], $is_addon = false, $tabs_override = [], $extra_tabs = [] ) {

		$settings_array = apply_filters( 'tc_element_settings_override', $settings_array, $name, $is_addon );
		$tabs_override  = apply_filters( 'tc_element_tabs_override', $tabs_override, $name, $settings_array, $is_addon );
		$extra_tabs     = apply_filters( 'wc_epo_admin_add_element_extra_tabs', $extra_tabs, $name, $settings_array, $is_addon );
		$options        = [];

		$epo_tabs_ids = [
			'label_options',
			'conditional_logic',
			'css_settings',
			'woocommerce_settings',
			'repeater_settings',
			'action_settings',
		];

		$epo_tabs            = $this->get_element_tabs( $epo_tabs_ids, $name, $settings_array, $tabs_override, $extra_tabs );
		$tabs_override       = $epo_tabs['tabs_override'];
		$settings_tabs_array = $epo_tabs['settings_tabs_array'];
		$header_names        = $epo_tabs['header_names'];

		foreach ( $settings_tabs_array as $tab => $settings ) {
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
					$args                  = false;
					$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();

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

						$class_to_use = apply_filters( 'wc_epo_add_element_class', THEMECOMPLETE_EPO_ADMIN_BUILDER(), $key, $value, $name, $settings, $is_addon, $tabs_override );

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

		$counter               = 1;
		$options_array         = [];
		$header_settings_array = [];
		foreach ( $options as $tab => $option ) {
			if ( ! empty( $tabs_override[ $tab ] ) ) {
				++$counter;
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
				} else {
					$header_name = esc_html__( 'General Settings', 'woocommerce-tm-extra-product-options' );
					$header_icon = 'tcfa tcfa-cog';
					$header_slug = 'tma-tab-general tma-tab-extra';
				}
				$header_settings_array = array_merge(
					$header_settings_array,
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

		$header_data = [];

		$tab_data_array = array_reduce(
			$epo_tabs_ids,
			function ( $carry, $tab ) use ( $name, $counter, $tabs_override ) {
				$function_name = 'generate_' . $tab;
				if ( is_callable( [ $this, $function_name ] ) ) {
					$result                       = $this->$function_name( $name, $counter, $tabs_override );
					$carry['tab_data'][ $tab ]    = $result['tab_data'];
					$carry['header_data'][ $tab ] = $result['header_data'];
				}
				return $carry;
			},
			[
				'tab_data'    => [],
				'header_data' => [],
			]
		);

		$tab_data_array['tab_data'] = array_merge(
			array_slice( $tab_data_array['tab_data'], 0, 1, true ),
			[ 'options' => $options_array ],
			array_slice( $tab_data_array['tab_data'], 1, null, true )
		);

		$tab_data = array_reduce(
			$tab_data_array['tab_data'],
			function ( $carry, $sub_array ) {
				return array_merge( $carry, $sub_array );
			},
			[]
		);

		$header_data = $tab_data_array['header_data'];
		$header_data = array_merge(
			array_slice( $header_data, 0, 1, true ),
			[ 'header_settings_array' => $header_settings_array ],
			array_slice( $header_data, 1, null, true )
		);

		$header_data = apply_filters( 'wc_epo_admin_add_element_header_data', $header_data, $name, $tabs_override, $counter );

		$element_data = array_merge(
			$this->prepend_div( '', 'tm-tabs' ),
			// add headers.
			$this->prepend_div( $name, 'tm-tab-headers' ),
			array_reduce(
				$header_data,
				function ( $carry, $sub_array ) {
					return array_merge( $carry, $sub_array );
				},
				[]
			),
			$this->append_div( $name ),
			$tab_data,
			$this->append_div( '' )
		);
		$element_data = apply_filters( 'wc_epo_admin_add_element_element_data', $element_data, $name, $tabs_override, $counter );

		return $element_data;
	}
	/**
	 * Get tax classes
	 *
	 * @since 6.0
	 * @return array<mixed>
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
	 * @return string
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
	 * @param string                $name The element type.
	 * @param mixed                 $value The setting value.
	 * @param array<mixed>          $_value Array of element settings.
	 * @param array<string>|boolean $additional_currencies Additional currencies for the price.
	 * @param boolean               $is_addon If this is an addon element.
	 * @since 6.0
	 * @return array<mixed>
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
	 * @return void
	 */
	public function builder_showlogic() {
		?>
		<div class="builder-logic-div">
			<div class="builder-logic-div-what">
				<div class="tc-row nopadding">
					<select class="epo-rule-toggle">
						<option value="show"><?php esc_html_e( 'Show', 'woocommerce-tm-extra-product-options' ); ?></option>
						<option value="hide"><?php esc_html_e( 'Hide', 'woocommerce-tm-extra-product-options' ); ?></option>
					</select>
					<span>
					<?php
					if ( 'section' === $this->element_name ) {
						esc_html_e( 'this section if the following conditions are met.', 'woocommerce-tm-extra-product-options' );
					} else {
						esc_html_e( 'this field if the following conditions are met.', 'woocommerce-tm-extra-product-options' );
					}
					?>
					</span>
				</div>
			</div>
			<div class="tm-logic-wrapper"></div>
			<button type="button" class="tc tc-button alt tm-logic-add-rule-set"><span class="tmicon tcfa tcfa-plus"></span> <?php esc_html_e( 'Add new condition group', 'woocommerce-tm-extra-product-options' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Show logic select box
	 *
	 * @since 6.0
	 * @return void
	 */
	public function shipping_showlogic() {
		?>
		<div class="shipping-logic-div">
			<div class="tm-logic-wrapper"></div>
			<button type="button" class="tc tc-button alt tm-logic-add-rule-set"><span class="tmicon tcfa tcfa-plus"></span> <?php esc_html_e( 'Add new condition group', 'woocommerce-tm-extra-product-options' ); ?></button>
		</div>
		<?php
	}
}
