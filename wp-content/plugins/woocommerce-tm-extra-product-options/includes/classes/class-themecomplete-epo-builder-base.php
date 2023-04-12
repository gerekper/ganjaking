<?php
/**
 * Extra Product Options Builder class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Builder class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_BUILDER_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_BUILDER_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Internal element names
	 *
	 * @var array
	 */
	public $internal_element_names = [];

	/**
	 * All elements
	 *
	 * @var array
	 */
	public $all_elements = [];

	/**
	 * Section element
	 *
	 * @var THEMECOMPLETE_EPO_BUILDER_ELEMENT_SECTION
	 */
	public $section;

	/**
	 * Extra setting for multiple options
	 *
	 * @var array
	 */
	public $extra_multiple_options = [];

	/**
	 * The properties of the choices
	 * This doesn't include the default value
	 *
	 * @var array
	 */
	public $multiple_properties = [];

	/**
	 * Default option attributes
	 *
	 * @var array
	 */
	public $default_attributes = [];

	/**
	 * Addon option attributes
	 *
	 * @var array
	 */
	public $addons_attributes = [];

	/**
	 * Sizes display array
	 *
	 * @var array
	 */
	public $sizer = [];

	/**
	 * Array of JS data to create the builder setting
	 *
	 * @var array
	 */
	public $jsbuilder = [];

	/**
	 * Flag to determine is output should happen
	 *
	 * @var boolean
	 */
	public $noecho = false;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Set internal element names.
		$this->set_internal_element_names();

		// Init the properties of choices.
		$this->multiple_properties = [
			'title',
			'value',
			'price',
			'sale_price',
			'image',
			'imagec',
			'imagep',
			'imagel',
			'price_type',
			'url',
			'description',
			'enabled',
			'color',
			'fee',
		];

		// extra multiple type options.
		$this->extra_multiple_options = apply_filters( 'wc_epo_extra_multiple_choices', [] );
		add_action( 'tm_epo_register_extra_multiple_choices', [ $this, 'add_extra_choices' ], 50 );

		// element available sizes.
		$this->element_available_sizes();

		// Init internal elements.
		$this->init_internal_elements();

		add_action( 'admin_footer', [ $this, 'admin_footer' ], 9 );
		add_action( 'admin_init', [ $this, 'admin_init' ], 9 );

	}

	/**
	 * Set internal element names
	 *
	 * @since 6.0
	 */
	public function set_internal_element_names() {
		global $pagenow;

		$this->internal_element_names = [
			'header',
			'divider',
			'date',
			'time',
			'range',
			'color',
			'textarea',
			'textfield',
			'upload',
			'selectbox',
			'selectboxmultiple',
			'radiobuttons',
			'checkboxes',
			'variations',
			'product',
			'template',
		];

		if ( ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) || ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : get_post_type( absint( wp_unslash( $_GET['post'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post_type ) {
				$key = array_search( 'template', $this->internal_element_names, true );
				if ( false !== $key ) {
					unset( $this->internal_element_names[ $key ] );
				}
			}
		}

	}

	/**
	 * Get extra setting for multiple choice options
	 *
	 * @since 1.0
	 */
	public function add_extra_choices() {
		$this->extra_multiple_options = apply_filters( 'wc_epo_extra_multiple_choices', [] );
	}

	/**
	 * Available element sizes
	 *
	 * @since 1.0
	 */
	private function element_available_sizes() {
		$this->sizer = [];
		for ( $x = 1; $x <= 100; $x++ ) {
			$this->sizer[ 'w' . $x ] = $x . '%';
		}
		$this->sizer['w12-5'] = '12.5%';
		$this->sizer['w37-5'] = '37.5%';
		$this->sizer['w62-5'] = '62.5%';
		$this->sizer['w87-5'] = '87.5%';
	}

	/**
	 * Holds all the elements types.
	 *
	 * @since 6.0
	 * @access private
	 */
	private function init_internal_elements() {

		foreach ( $this->internal_element_names as $class_name ) {
			$class                             = 'THEMECOMPLETE_EPO_BUILDER_ELEMENT_' . strtoupper( $class_name );
			$this->all_elements[ $class_name ] = new $class( $class_name );
		}

		$this->all_elements = apply_filters( 'wc_epo_builder_element_settings', $this->all_elements );

		do_action( 'wc_epo_builder_after_element_settings', $this->all_elements );

	}

	/**
	 * Add footer script
	 *
	 * @since 4.9.12
	 */
	public function admin_footer() {
		wp_register_script( 'themecomplete-footer-admin-js', false, [], THEMECOMPLETE_EPO_VERSION, true );
		wp_localize_script(
			'themecomplete-footer-admin-js',
			'TMEPOOPTIONSJS',
			[
				'data' => $this->jsbuilder,
			]
		);
		wp_enqueue_script( 'themecomplete-footer-admin-js' );
	}

	/**
	 * Init elements
	 *
	 * @since 5.0
	 */
	public function admin_init() {

		// Init section elements.
		$this->init_section_elements();

		// Init elements.
		$this->init_elements();

	}

	/**
	 * Get all elements
	 *
	 * @since 1.0
	 */
	public function get_elements() {
		return $this->all_elements;
	}

	/**
	 * Set elements
	 *
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	private function set_elements( $args = [] ) {

		$element = $args['name'];
		$options = apply_filters( 'wc_epo_set_elements_options', $args['options'], $args );

		if ( ! empty( $element ) && is_array( $options ) ) {
			$addon              = new THEMECOMPLETE_EPO_BUILDER_ELEMENT_ADDON( $options, $args );
			$this->all_elements = array_merge( [ $element => $addon ], $this->all_elements );
		}

	}

	/**
	 * Get custom properties
	 *
	 * @param array   $builder Element builder array.
	 * @param string  $_prefix Element prefix.
	 * @param array   $_counter Counter array.
	 * @param array   $_elements The saved element types array.
	 * @param integer $k0 Current section counter.
	 * @param array   $current_builder The current element builder array.
	 * @param integer $current_counter The current element counter.
	 * @param string  $current_element The current element.
	 * @since 1.0
	 */
	public function get_custom_properties( $builder, $_prefix, $_counter, $_elements, $k0, $current_builder, $current_counter, $current_element ) {

		$p = [];
		foreach ( $this->addons_attributes as $key => $value ) {
			$p[ $value ] = THEMECOMPLETE_EPO()->get_builder_element( $_prefix . $value, $builder, $current_builder, $current_counter, '', $current_element );
		}

		return $p;

	}

	/**
	 * Get default properties
	 *
	 * @param array   $builder Element builder array.
	 * @param string  $_prefix Element prefix.
	 * @param array   $_counter Counter array.
	 * @param array   $_elements The saved element types array.
	 * @param integer $k0 Current section counter.
	 * @since 1.0
	 */
	public function get_default_properties( $builder, $_prefix, $_counter, $_elements, $k0 ) {
		$p = [];
		foreach ( $this->default_attributes as $key => $value ) {
			$p[ $value ] = isset( $builder[ $_prefix . $value ][ $_counter[ $_elements[ $k0 ] ] ] )
				? $builder[ $_prefix . $value ][ $_counter[ $_elements[ $k0 ] ] ]
				: '';
		}

		return $p;
	}

	/**
	 * Register addons
	 *
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function register_addon( $args = [] ) {
		if ( isset( $args['namespace'] )
			&& isset( $args['name'] )
			&& isset( $args['options'] )
			&& isset( $args['settings'] ) ) {

			$this->set_elements( $args );

		}
	}

	/**
	 * Init section elements
	 *
	 * @since 1.0
	 */
	private function init_section_elements() {

		$this->section = new THEMECOMPLETE_EPO_BUILDER_ELEMENT_SECTION( 'section' );
		$this->section->set_properties();

	}

	/**
	 * Init elements
	 *
	 * @since 1.0
	 */
	private function init_elements() {

		$this->all_elements = apply_filters( 'wc_epo_builder_before_element_set_properties', $this->all_elements );

		foreach ( $this->all_elements as $element => $class ) {
			$class->set_properties();
		}

		$this->all_elements = apply_filters( 'wc_epo_builder_after_element_set_properties', $this->all_elements );

	}

	/**
	 * Variation disabled setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_variations_disabled( $name = '' ) {
		// this field must be unique, no multiples allowed or have sense.
		return [
			'id'          => $name . '_disabled',
			'wpmldisable' => 1,
			'nodiv'       => 1,
			'default'     => '5',
			'type'        => 'hidden',
			'tags'        => [
				'class' => 'tm-variations-disabled',
				'id'    => 'builder_' . $name . '_disabled',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_disabled]',
				'value' => '',
			],
			'label'       => '',
			'desc'        => '',
		];
	}

	/**
	 * Pips setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_pips( $name = '' ) {
		return [
			'id'          => $name . '_pips',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'id'   => 'builder_' . $name . '_pips',
				'name' => 'tm_meta[tmfbuilder][' . $name . '_pips][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
					'value' => 'yes',
				],
			],
			'label'       => esc_html__( 'Enable points display?', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'This allows you to generate points along the range picker.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Number of points setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_noofpips( $name = '' ) {
		return [
			'id'          => $name . '_noofpips',
			'wpmldisable' => 1,
			'default'     => '10',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_noofpips',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_noofpips][]',
				'value' => '',
			],
			'label'       => esc_html__( 'Number of points', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter the number of values for the points display.', 'woocommerce-tm-extra-product-options' ),
			'required'    => [
				'#builder_range_pips' => [
					'operator' => 'is',
					'value'    => 'yes',
				],
			],
		];
	}

	/**
	 * Show value on setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_show_picker_value( $name = '' ) {
		return [
			'id'          => $name . '_show_picker_value',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'id'   => 'builder_' . $name . '_show_picker_value',
				'name' => 'tm_meta[tmfbuilder][' . $name . '_show_picker_value][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'Tooltip', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Left side', 'woocommerce-tm-extra-product-options' ),
					'value' => 'left',
				],
				[
					'text'  => esc_html__( 'Right side', 'woocommerce-tm-extra-product-options' ),
					'value' => 'right',
				],
				[
					'text'  => esc_html__( 'Tooltip and Left side', 'woocommerce-tm-extra-product-options' ),
					'value' => 'tleft',
				],
				[
					'text'  => esc_html__( 'Tooltip and Right side', 'woocommerce-tm-extra-product-options' ),
					'value' => 'tright',
				],
			],
			'label'       => esc_html__( 'Show value on', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Select how to show the value of the range picker.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Range picker Min value setting
	 *
	 * @param string $name Element name.
	 * @since 5.0
	 */
	public function add_setting_rangemin( $name = '' ) {

		return $this->add_setting_min( $name, [], false );

	}

	/**
	 * Range picker Max value setting
	 *
	 * @param string $name Element name.
	 * @since 5.0
	 */
	public function add_setting_rangemax( $name = '' ) {

		return $this->add_setting_max( $name, [], false );

	}

	/**
	 * Step value setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_rangestep( $name = '' ) {
		return [
			'id'          => $name . '_step',
			'wpmldisable' => 1,
			'default'     => '1',
			'type'        => 'text',
			'tags'        => [
				'id'    => 'builder_' . $name . '_step',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_step][]',
				'value' => '',
			],
			'label'       => esc_html__( 'Step value', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter the step for the handle.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Validate as setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_validation1( $name = '' ) {
		return [
			'id'          => $name . '_validation1',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'tc-validateas',
				'id'    => 'builder_' . $name . '_validation1',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_validation1][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'No validation', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Email', 'woocommerce-tm-extra-product-options' ),
					'value' => 'email',
				],
				[
					'text'  => esc_html__( 'Url', 'woocommerce-tm-extra-product-options' ),
					'value' => 'url',
				],
				[
					'text'  => esc_html__( 'Number', 'woocommerce-tm-extra-product-options' ),
					'value' => 'number',
				],
				[
					'text'  => esc_html__( 'Digits', 'woocommerce-tm-extra-product-options' ),
					'value' => 'digits',
				],
				[
					'text'  => esc_html__( 'Letters only', 'woocommerce-tm-extra-product-options' ),
					'value' => 'lettersonly',
				],
				[
					'text'  => esc_html__( 'Letters or Space only', 'woocommerce-tm-extra-product-options' ),
					'value' => 'lettersspaceonly',
				],
				[
					'text'  => esc_html__( 'Alphanumeric', 'woocommerce-tm-extra-product-options' ),
					'value' => 'alphanumeric',
				],
				[
					'text'  => esc_html__( 'Alphanumeric Unicode', 'woocommerce-tm-extra-product-options' ),
					'value' => 'alphanumericunicode',
				],
				[
					'text'  => esc_html__( 'Alphanumeric Unicode or Space', 'woocommerce-tm-extra-product-options' ),
					'value' => 'alphanumericunicodespace',
				],
			],
			'label'       => esc_html__( 'Validate as', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Choose whether the field will be validated against the choosen method.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Required setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_required( $name = '' ) {
		return [
			'id'          => $name . '_required',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'checkbox',
			'tags'        => [
				'value' => '1',
				'id'    => 'builder_' . $name . '_required',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_required][]',
			],
			'label'       => esc_html__( 'Required', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Choose whether the user must fill out this field or not.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Enabled setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_enabled( $name = '' ) {
		return [
			'id'          => $name . '_enabled',
			'wpmldisable' => 1,
			'default'     => '1',
			'type'        => 'checkbox',
			'tags'        => [
				'value' => '1',
				'class' => 'is_enabled',
				'id'    => 'builder_' . $name . '_required',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_enabled][]',
			],
			'label'       => esc_html__( 'Enabled', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Choose whether the option is enabled or not.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Price setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_price( $name = '' ) {
		return [
			'id'       => $name . '_price',
			'default'  => '',
			'type'     => 'text',
			'tags'     => [
				'class' => 't tc-element-setting-price',
				'id'    => 'builder_' . $name . '_price',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_price][]',
				'value' => '',
				'step'  => 'any',
			],
			'label'    => esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ),
			'desc'     => esc_html__( 'Enter the price for this field or leave it blank for no price.', 'woocommerce-tm-extra-product-options' ),
			'required' => [
				'.tm-pricetype-selector' => [
					'operator' => 'isnot',
					'value'    => [ 'currentstep', 'lookuptable' ],
				],
			],
		];
	}

	/**
	 * Sale price setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_sale_price( $name = '' ) {
		return [
			'id'       => $name . '_sale_price',
			'default'  => '',
			'type'     => 'text',
			'tags'     => [
				'class' => 't tc-element-setting-sale-price',
				'id'    => 'builder_' . $name . '_sale_price',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_sale_price][]',
				'value' => '',
				'step'  => 'any',
			],
			'label'    => esc_html__( 'Sale Price', 'woocommerce-tm-extra-product-options' ),
			'desc'     => esc_html__( 'Enter the sale price for this field or leave it blankto use the default price.', 'woocommerce-tm-extra-product-options' ),
			'required' => [
				'.tm-pricetype-selector' => [
					'operator' => 'isnot',
					'value'    => [ 'currentstep', 'lookuptable' ],
				],
			],
		];
	}

	/**
	 * Text after price setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_text_after_price( $name = '' ) {
		return [
			'id'      => $name . '_text_after_price',
			'default' => '',
			'type'    => 'text',
			'tags'    => [
				'class' => 't',
				'id'    => 'builder_' . $name . '_text_after_price',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_text_after_price][]',
				'value' => '',
			],
			'label'   => esc_html__( 'Text after Price', 'woocommerce-tm-extra-product-options' ),
			'desc'    => esc_html__( 'Enter a text to display after the price for this field or leave it blank for no text.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Text before price setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_text_before_price( $name = '' ) {
		return [
			'id'      => $name . '_text_before_price',
			'default' => '',
			'type'    => 'text',
			'tags'    => [
				'class' => 't',
				'id'    => 'builder_' . $name . '_text_before_price',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_text_before_price][]',
				'value' => '',
			],
			'label'   => esc_html__( 'Text before Price', 'woocommerce-tm-extra-product-options' ),
			'desc'    => esc_html__( 'Enter a text to display before the price for this field or leave it blank for no text.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Textarea price type setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_price_type( $name = '' ) {

		$options = [
			[
				'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
			[
				'text'  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percent',
			],
			[
				'text'  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percentcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Price per word', 'woocommerce-tm-extra-product-options' ),
				'value' => 'word',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per word', 'woocommerce-tm-extra-product-options' ),
				'value' => 'wordpercent',
			],
			[
				'text'  => esc_html__( 'Price per word (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'wordnon',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per word (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'wordpercentnon',
			],
			[
				'text'  => esc_html__( 'Price per character', 'woocommerce-tm-extra-product-options' ),
				'value' => 'char',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercent',
			],
			[
				'text'  => esc_html__( 'Price per character (no first char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnofirst',
			],
			[
				'text'  => esc_html__( 'Price per character (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnon',
			],
			[
				'text'  => esc_html__( 'Price per character (no n-th char and no spaces)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnonnospaces',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character (no first char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercentnofirst',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercentnon',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character (no n-th char and no spaces)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercentnonnospaces',
			],
			[
				'text'  => esc_html__( 'Price per character (no spaces)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnospaces',
			],
			[
				'text'  => esc_html__( 'Price per row', 'woocommerce-tm-extra-product-options' ),
				'value' => 'row',
			],
			[
				'text'  => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ),
				'value' => 'math',
			],
			[
				'text'  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'fixedcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
				'value' => 'lookuptable',
			],
		];

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return [
			'id'          => $name . '_price_type',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'tm-pricetype-selector',
				'id'    => 'builder_' . $name . '_price_type',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_price_type][]',
			],
			'options'     => $options,
			'label'       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Textfield price type setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_price_type2( $name = '' ) {

		$options = [
			[
				'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
			[
				'text'  => esc_html__( 'Quantity', 'woocommerce-tm-extra-product-options' ),
				'value' => 'step',
			],
			[
				'text'  => esc_html__( 'Current value', 'woocommerce-tm-extra-product-options' ),
				'value' => 'currentstep',
			],
			[
				'text'  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percent',
			],
			[
				'text'  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percentcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Price per word', 'woocommerce-tm-extra-product-options' ),
				'value' => 'word',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per word', 'woocommerce-tm-extra-product-options' ),
				'value' => 'wordpercent',
			],
			[
				'text'  => esc_html__( 'Price per word (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'wordnon',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per word (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'wordpercentnon',
			],
			[
				'text'  => esc_html__( 'Price per character', 'woocommerce-tm-extra-product-options' ),
				'value' => 'char',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercent',
			],
			[
				'text'  => esc_html__( 'Price per character (no first char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnofirst',
			],
			[
				'text'  => esc_html__( 'Price per character (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnon',
			],
			[
				'text'  => esc_html__( 'Price per character (no n-th char and no spaces)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnonnospaces',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character (no first char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercentnofirst',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercentnon',
			],
			[
				'text'  => esc_html__( 'Percent of the original price per character (no n-th char and no spaces)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charpercentnonnospaces',
			],
			[
				'text'  => esc_html__( 'Price per character (no spaces)', 'woocommerce-tm-extra-product-options' ),
				'value' => 'charnospaces',
			],
			[
				'text'  => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ),
				'value' => 'math',
			],
			[
				'text'  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'fixedcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
				'value' => 'lookuptable',
			],
		];

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return [
			'id'          => $name . '_price_type',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'tm-pricetype-selector',
				'id'    => 'builder_' . $name . '_price_type',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_price_type][]',
			],
			'options'     => $options,
			'label'       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Upload price type setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_price_type5( $name = '' ) {

		$options = [
			[
				'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
			[
				'text'  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percent',
			],
			[
				'text'  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percentcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ),
				'value' => 'math',
			],
			[
				'text'  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'fixedcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
				'value' => 'lookuptable',
			],
		];

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return [
			'id'          => $name . '_price_type',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'tm-pricetype-selector',
				'id'    => 'builder_' . $name . '_price_type',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_price_type][]',
			],
			'options'     => $options,
			'label'       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Date and time price type setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_price_type6( $name = '' ) {

		$options = [
			[
				'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
			[
				'text'  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percent',
			],
			[
				'text'  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percentcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ),
				'value' => 'math',
			],
			[
				'text'  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'fixedcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
				'value' => 'lookuptable',
			],
		];

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return [
			'id'          => $name . '_price_type',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'tm-pricetype-selector',
				'id'    => 'builder_' . $name . '_price_type',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_price_type][]',
			],
			'options'     => $options,
			'label'       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Range picker price type setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_price_type7( $name = '' ) {

		$options = [
			[
				'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
			[
				'text'  => esc_html__( 'Step * price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'step',
			],
			[
				'text'  => esc_html__( 'Current value', 'woocommerce-tm-extra-product-options' ),
				'value' => 'currentstep',
			],
			[
				'text'  => esc_html__( 'Price per Interval', 'woocommerce-tm-extra-product-options' ),
				'value' => 'intervalstep',
			],
			[
				'text'  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percent',
			],
			[
				'text'  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percentcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ),
				'value' => 'math',
			],
			[
				'text'  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'fixedcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
				'value' => 'lookuptable',
			],
		];

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return [
			'id'          => $name . '_price_type',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'tm-pricetype-selector',
				'id'    => 'builder_' . $name . '_price_type',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_price_type][]',
			],
			'options'     => $options,
			'label'       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Lookuptable setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_lookuptable( $name = '' ) {

		$options = [
			[
				'text'  => esc_html__( 'None', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
		];

		$lookup_tables = THEMECOMPLETE_EPO()->lookup_tables;
		if ( $lookup_tables ) {
			foreach ( $lookup_tables as $table => $tables ) {
				if ( is_array( $tables ) ) {
					$show_num = count( $tables ) > 1;
					foreach ( $tables as $table_num => $data ) {
						$options[] = [
							'text'  => $table . ( $show_num ? ' (' . $table_num . ')' : '' ),
							'value' => $table . '|' . $table_num,
						];
					}
				}
			}
		}

		$options = apply_filters( 'wc_epo_add_setting_lookuptable', $options, $name );

		return [
			'_multiple_values' => [
				[
					'id'          => $name . '_lookuptable',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'select',
					'tags'        => [
						'class' => 'tm-lookuptable-selector',
						'id'    => 'builder_' . $name . '_lookuptable',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_lookuptable][]',
					],
					'options'     => $options,
					'label'       => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
					'required'    => [
						'.tm-pricetype-selector' => [
							'operator' => 'is',
							'value'    => 'lookuptable',
						],
					],
				],
				[
					'id'       => $name . '_lookuptable_x',
					'default'  => '',
					'type'     => 'text',
					'tags'     => [
						'class' => 't tm-lookuptable-x-selector',
						'id'    => 'builder_' . $name . '_lookuptable_x',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_lookuptable_x][]',
						'value' => '',
						'step'  => 'any',
					],
					'label'    => esc_html__( 'Element ID for x', 'woocommerce-tm-extra-product-options' ),
					'desc'     => esc_html__( 'Enter the element id to use for the x column.', 'woocommerce-tm-extra-product-options' ),
					'required' => [
						'.tm-pricetype-selector' => [
							'operator' => 'is',
							'value'    => 'lookuptable',
						],
					],
				],
				[
					'id'       => $name . '_lookuptable_y',
					'default'  => '',
					'type'     => 'text',
					'tags'     => [
						'class' => 't tm-lookuptable-y-selector',
						'id'    => 'builder_' . $name . '_lookuptable_y',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_lookuptable_y][]',
						'value' => '',
						'step'  => 'any',
					],
					'label'    => esc_html__( 'Element ID for y', 'woocommerce-tm-extra-product-options' ),
					'desc'     => esc_html__( 'Enter the element id to use for the y column.', 'woocommerce-tm-extra-product-options' ),
					'required' => [
						'.tm-pricetype-selector' => [
							'operator' => 'is',
							'value'    => 'lookuptable',
						],
					],
				],
			],
		];
	}

	/**
	 * Fee setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 5.0
	 */
	public function add_setting_fee( $name = '', $args = [] ) {
		$setting = array_merge(
			[
				'id'          => $name . '_fee',
				'wpmldisable' => 1,
				'default'     => '',
				'type'        => 'checkbox',
				'tags'        => [
					'class' => 'c tc-element-setting-fee',
					'id'    => 'builder_' . $name . '_fee',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_fee][]',
					'value' => '1',
				],
				'label'       => esc_html__( 'Set to Fee', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Attach the price to the order making it independent of the product quantity.', 'woocommerce-tm-extra-product-options' ),
			],
			$args
		);

		$setting = apply_filters( 'wc_epo_add_setting_fee', $setting, $name, $args );

		return $setting;
	}

	/**
	 * Free characters setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function add_setting_freechars( $name = '', $args = [] ) {
		return array_merge(
			[
				'id'          => $name . '_freechars',
				'wpmldisable' => 1,
				'default'     => '',
				'type'        => 'number',
				'tags'        => [
					'class' => 'n',
					'id'    => 'builder_' . $name . '_freechars',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_freechars][]',
					'value' => '',
					'step'  => '1',
				],
				'label'       => esc_html__( 'Free characters', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the number of Free characters.', 'woocommerce-tm-extra-product-options' ),
				'required'    => [
					'.tm-pricetype-selector' => [
						'operator' => 'is',
						'value'    => [
							'wordnon',
							'wordpercentnon',
							'charnon',
							'charnonnospaces',
							'charpercentnon',
							'charpercentnonnospaces',
						],
					],
				],
			],
			$args
		);
	}

	/**
	 * Min value setting
	 *
	 * @param array   $name Element name.
	 * @param array   $args Array of arguments.
	 * @param boolean $required If the setting is required.
	 * @since 1.0
	 */
	public function add_setting_min( $name = '', $args = [], $required = true ) {
		$min = array_merge(
			[
				'id'          => $name . '_min',
				'wpmldisable' => 1,
				'default'     => '',
				'type'        => 'number',
				'extra_tags'  => [],
				'tags'        => [
					'class' => 'n',
					'id'    => 'builder_' . $name . '_min',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_min][]',
					'value' => '',
					'step'  => 'any',
				],
				'label'       => esc_html__( 'Min value', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the minimum value.', 'woocommerce-tm-extra-product-options' ),
			],
			$args
		);

		$min['tags'] = array_merge( $min['tags'], $min['extra_tags'] );

		if ( $required ) {
			$min['required'] = [
				'relation'               => 'OR',
				'.tm-pricetype-selector' => [
					'operator' => 'is',
					'value'    => [ 'step', 'currentstep' ],
				],
				'.tc-validateas'         => [
					'operator' => 'is',
					'value'    => [ 'number', 'digits' ],
				],
			];
		}

		return $min;

	}

	/**
	 * Max value setting
	 *
	 * @param array   $name Element name.
	 * @param array   $args Array of arguments.
	 * @param boolean $required If the setting is required.
	 * @since 1.0
	 */
	public function add_setting_max( $name = '', $args = [], $required = true ) {
		$max = array_merge(
			[
				'id'          => $name . '_max',
				'wpmldisable' => 1,
				'default'     => '',
				'type'        => 'number',
				'extra_tags'  => [],
				'tags'        => [
					'class' => 'n',
					'id'    => 'builder_' . $name . '_max',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_max][]',
					'value' => '',
					'step'  => 'any',
				],
				'label'       => esc_html__( 'Max value', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the maximum value.', 'woocommerce-tm-extra-product-options' ),
			],
			$args
		);

		$max['tags'] = array_merge( $max['tags'], $max['extra_tags'] );

		if ( $required ) {
			$max['required'] = [
				'relation'               => 'OR',
				'.tm-pricetype-selector' => [
					'operator' => 'is',
					'value'    => [ 'step', 'currentstep' ],
				],
				'.tc-validateas'         => [
					'operator' => 'is',
					'value'    => [ 'number', 'digits' ],
				],
			];
		}

		return $max;

	}

	/**
	 * Date format setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_date_format( $name = '' ) {
		return [
			'id'      => $name . '_format',
			'default' => '0',
			'type'    => 'select',
			'tags'    => [
				'id'   => 'builder_' . $name . '_format',
				'name' => 'tm_meta[tmfbuilder][' . $name . '_format][]',
			],
			'options' => [
				[
					'text'  => esc_html__( 'Day / Month / Year', 'woocommerce-tm-extra-product-options' ),
					'value' => '0',
				],
				[
					'text'  => esc_html__( 'Month / Day / Year', 'woocommerce-tm-extra-product-options' ),
					'value' => '1',
				],
				[
					'text'  => esc_html__( 'Day . Month . Year', 'woocommerce-tm-extra-product-options' ),
					'value' => '2',
				],
				[
					'text'  => esc_html__( 'Month . Day . Year', 'woocommerce-tm-extra-product-options' ),
					'value' => '3',
				],
				[
					'text'  => esc_html__( 'Day - Month - Year', 'woocommerce-tm-extra-product-options' ),
					'value' => '4',
				],
				[
					'text'  => esc_html__( 'Month - Day - Year', 'woocommerce-tm-extra-product-options' ),
					'value' => '5',
				],

				[
					'text'  => esc_html__( 'Year / Month / Day', 'woocommerce-tm-extra-product-options' ),
					'value' => '6',
				],
				[
					'text'  => esc_html__( 'Year / Day / Month', 'woocommerce-tm-extra-product-options' ),
					'value' => '7',
				],
				[
					'text'  => esc_html__( 'Year . Month . Day', 'woocommerce-tm-extra-product-options' ),
					'value' => '8',
				],
				[
					'text'  => esc_html__( 'Year . Day . Month', 'woocommerce-tm-extra-product-options' ),
					'value' => '9',
				],
				[
					'text'  => esc_html__( 'Year - Month - Day', 'woocommerce-tm-extra-product-options' ),
					'value' => '10',
				],
				[
					'text'  => esc_html__( 'Year - Day - Month', 'woocommerce-tm-extra-product-options' ),
					'value' => '11',
				],

			],
			'label'   => esc_html__( 'Date format', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Time format setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function add_setting_time_format( $name = '', $args = [] ) {
		$time_format = [
			'id'      => $name . '_time_format',
			'default' => '0',
			'type'    => 'select',
			'tags'    => [
				'id'   => 'builder_' . $name . '_format',
				'name' => 'tm_meta[tmfbuilder][' . $name . '_time_format][]',
			],
			'options' => [
				[
					'text'  => esc_html__( 'HH:mm', 'woocommerce-tm-extra-product-options' ),
					'value' => 'HH:mm',
				],
				[
					'text'  => esc_html__( 'HH:m', 'woocommerce-tm-extra-product-options' ),
					'value' => 'HH:m',
				],
				[
					'text'  => esc_html__( 'H:mm', 'woocommerce-tm-extra-product-options' ),
					'value' => 'H:mm',
				],
				[
					'text'  => esc_html__( 'H:m', 'woocommerce-tm-extra-product-options' ),
					'value' => 'H:m',
				],
				[
					'text'  => esc_html__( 'HH:mm:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'HH:mm:ss',
				],
				[
					'text'  => esc_html__( 'HH:m:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'HH:m:ss',
				],
				[
					'text'  => esc_html__( 'H:mm:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'H:mm:ss',
				],
				[
					'text'  => esc_html__( 'H:m:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'H:m:ss',
				],
				[
					'text'  => esc_html__( 'HH:mm:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'HH:mm:s',
				],
				[
					'text'  => esc_html__( 'HH:m:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'HH:m:s',
				],
				[
					'text'  => esc_html__( 'H:mm:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'H:mm:s',
				],
				[
					'text'  => esc_html__( 'H:m:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'H:m:s',
				],

				[
					'text'  => esc_html__( 'hh:mm', 'woocommerce-tm-extra-product-options' ),
					'value' => 'hh:mm',
				],
				[
					'text'  => esc_html__( 'hh:m', 'woocommerce-tm-extra-product-options' ),
					'value' => 'hh:m',
				],
				[
					'text'  => esc_html__( 'h:mm', 'woocommerce-tm-extra-product-options' ),
					'value' => 'h:mm',
				],
				[
					'text'  => esc_html__( 'h:m', 'woocommerce-tm-extra-product-options' ),
					'value' => 'h:m',
				],
				[
					'text'  => esc_html__( 'hh:mm:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'hh:mm:ss',
				],
				[
					'text'  => esc_html__( 'hh:m:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'hh:m:ss',
				],
				[
					'text'  => esc_html__( 'h:mm:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'h:mm:ss',
				],
				[
					'text'  => esc_html__( 'h:m:ss', 'woocommerce-tm-extra-product-options' ),
					'value' => 'h:m:ss',
				],
				[
					'text'  => esc_html__( 'hh:mm:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'hh:mm:s',
				],
				[
					'text'  => esc_html__( 'hh:m:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'hh:m:s',
				],
				[
					'text'  => esc_html__( 'h:mm:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'h:mm:s',
				],
				[
					'text'  => esc_html__( 'h:m:s', 'woocommerce-tm-extra-product-options' ),
					'value' => 'h:m:s',
				],
			],
			'label'   => esc_html__( 'Time format', 'woocommerce-tm-extra-product-options' ),
		];

		$time_format = array_merge( $time_format, $args );

		return $time_format;
	}

	/**
	 * Custom Time format setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function add_setting_custom_time_format( $name = '', $args = [] ) {
		$custom_time_format = [
			'id'          => $name . '_custom_time_format',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'text',
			'tags'        => [
				'class' => 't',
				'id'    => 'builder_' . $name . '_custom_time_format',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_custom_time_format][]',
				'value' => '',
			],
			'label'       => esc_html__( 'Custom Time format', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'This will override the time format above.', 'woocommerce-tm-extra-product-options' ),
		];
		$custom_time_format = array_merge( $custom_time_format, $args );

		return $custom_time_format;
	}

	/**
	 * Start year setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_start_year( $name = '' ) {
		return [
			'id'          => $name . '_start_year',
			'wpmldisable' => 1,
			'default'     => '1900',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_start_year',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_start_year][]',
				'value' => '',
			],
			'label'       => esc_html__( 'Start year', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter starting year.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * End year setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_end_year( $name = '' ) {
		return [
			'id'          => $name . '_end_year',
			'wpmldisable' => 1,
			'default'     => ( gmdate( 'Y' ) + 10 ),
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_end_year',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_end_year][]',
				'value' => '',
			],
			'label'       => esc_html__( 'End year', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter ending year.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Use URL replacements setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_use_url( $name = '' ) {
		return [
			'id'          => $name . '_use_url',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'use_url',
				'id'    => 'builder_' . $name . '_use_url',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_use_url][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
					'value' => 'url',
				],
			],
			'label'       => esc_html__( 'Use URL replacements', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Choose whether to redirect to a URL if the option is click.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Populate options setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_options( $name = '' ) {
		return [
			'id'         => $name . '_options',
			'tmid'       => 'populate',
			'default'    => '',
			'type'       => 'custom_multiple',
			'leftclass'  => 'onerow',
			'rightclass' => 'onerow',
			'html'       => [
				[ $this, 'builder_sub_options' ],
				[ [ 'name' => 'multiple_' . $name . '_options' ] ],
			],
			'label'      => esc_html__( 'Populate options', 'woocommerce-tm-extra-product-options' ),
			'desc'       => ( 'checkboxes' === $name ) ? '' : esc_html__( 'Double click the radio button to remove its selected attribute.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Variation options setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_variations_options( $name = '' ) {
		return [
			'id'         => $name . '_options',
			'default'    => '',
			'type'       => 'custom_multiple',
			'leftclass'  => 'onerow',
			'rightclass' => 'onerow2 tm-all-attributes',
			'html'       => [ [ $this, 'builder_sub_variations_options' ], [ [] ] ],
			'label'      => esc_html__( 'Variation options', 'woocommerce-tm-extra-product-options' ),
			'desc'       => '',
		];
	}

	/**
	 * Replacement mode setting
	 *
	 * @param string $name Element name.
	 * @since 6.0
	 */
	public function add_setting_replacement_mode( $name = '' ) {
		return [
			'id'               => $name . '_replacement_mode',
			'message0x0_class' => 'tm-replacement-mode tm-epo-switch-wrapper',
			'wpmldisable'      => 1,
			'default'          => 'none',
			'type'             => 'radio',
			'tags'             => [
				'class' => 'replacement-mode',
				'id'    => 'builder_' . $name . '_replacement_mode',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_replacement_mode][]',
			],
			'options'          => [
				[
					'text'  => esc_html__( 'None', 'woocommerce-tm-extra-product-options' ),
					'value' => 'none',
				],
				[
					'text'  => esc_html__( 'Image swatches', 'woocommerce-tm-extra-product-options' ),
					'value' => 'image',
				],
				[
					'text'  => esc_html__( 'Color swatches', 'woocommerce-tm-extra-product-options' ),
					'value' => 'color',
				],
				[
					'text'  => esc_html__( 'Text swatches', 'woocommerce-tm-extra-product-options' ),
					'value' => 'text',
				],
			],
			'label'            => esc_html__( 'Replacement mode', 'woocommerce-tm-extra-product-options' ),
			'desc'             => esc_html__( 'Select how to display the element.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Use image replacements setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_swatch_position( $name = '' ) {
		return [
			'id'               => $name . '_swatch_position',
			'message0x0_class' => 'tm-swatch-position tm-epo-switch-wrapper',
			'wpmldisable'      => 1,
			'default'          => 'center',
			'type'             => 'radio',
			'tags'             => [
				'class' => 'swatch-position',
				'id'    => 'builder_' . $name . '_swatch_position',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_swatch_position][]',
			],
			'options'          => [
				[
					'text'  => esc_html__( 'Start of the label', 'woocommerce-tm-extra-product-options' ),
					'value' => 'start',
				],
				[
					'text'  => esc_html__( 'Center', 'woocommerce-tm-extra-product-options' ),
					'value' => 'center',
				],
				[
					'text'  => esc_html__( 'End of the label', 'woocommerce-tm-extra-product-options' ),
					'value' => 'end',
				],
			],
			'label'            => esc_html__( 'Swatch position', 'woocommerce-tm-extra-product-options' ),
			'desc'             => esc_html__( 'Choose how the swatch will be displayed.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Use image lightbox setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_use_lightbox( $name = '' ) {
		return [
			'id'               => $name . '_use_lightbox',
			'message0x0_class' => 'tm-show-when-use-images',
			'wpmldisable'      => 1,
			'default'          => '',
			'type'             => 'checkbox',
			'tags'             => [
				'value' => 'lightbox',
				'class' => 'use_lightbox tm-use-lightbox',
				'id'    => 'builder_' . $name . '_use_lightbox',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_use_lightbox][]',
			],
			'label'            => esc_html__( 'Use image lightbox', 'woocommerce-tm-extra-product-options' ),
			'desc'             => esc_html__( 'Choose whether to enable the lightbox on the thumbnail.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Changes product image setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_changes_product_image( $name = '' ) {
		return [
			'id'          => $name . '_changes_product_image',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'class' => 'changes-product-image tm-changes-product-image',
				'id'    => 'builder_' . $name . '_changes_product_image',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_changes_product_image][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Use the image replacements', 'woocommerce-tm-extra-product-options' ),
					'value' => 'images',
				],
				[
					'text'  => esc_html__( 'Use custom image', 'woocommerce-tm-extra-product-options' ),
					'value' => 'custom',
				],
			],
			'label'       => esc_html__( 'Changes product image', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Choose whether to change the product image.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Enable Show tooltip setting
	 *
	 * @param string $name Element name.
	 * @since 6.0
	 */
	public function add_setting_show_tooltip( $name = '' ) {
		return [
			'id'               => $name . '_show_tooltip',
			'message0x0_class' => 'tm-show-when-use-images tm-show-when-use-color',
			'wpmldisable'      => 1,
			'default'          => '',
			'type'             => 'select',
			'tags'             => [
				'class' => 'show-tooltip',
				'id'    => 'builder_' . $name . '_show_tooltip',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_show_tooltip][]',
			],
			'options'          => apply_filters(
				'wc_epo_add_setting_show_tooltip',
				[
					[
						'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
						'value' => '',
					],
					[
						'text'  => esc_html__( 'Show label', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch',
					],
					[
						'text'  => esc_html__( 'Show description', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch_desc',
					],
					[
						'text'  => esc_html__( 'Show label and description', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch_lbl_desc',
					],
					[
						'text'  => esc_html__( 'Show image', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch_img',
					],
					[
						'text'  => esc_html__( 'Show image and label', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch_img_lbl',
					],
					[
						'text'  => esc_html__( 'Show image and description', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch_img_desc',
					],
					[
						'text'  => esc_html__( 'Show image, label and description', 'woocommerce-tm-extra-product-options' ),
						'value' => 'swatch_img_lbl_desc',
					],
				]
			),
			'label'            => esc_html__( 'Show tooltip', 'woocommerce-tm-extra-product-options' ),
			'desc'             => esc_html__( 'Enabling this will show a tooltip over the choice.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Enable clear options button setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_clear_options( $name = '' ) {
		return [
			'id'          => $name . '_clear_options',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'checkbox',
			'tags'        => [
				'class' => 'clear_options',
				'value' => '1',
				'id'    => 'builder_' . $name . '_clear_options',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_clear_options][]',
			],
			'label'       => esc_html__( 'Enable clear options button', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'This will add a button to clear the selected option.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Items per row setting helper
	 *
	 * @since 4.8.5
	 */
	public function add_setting_items_per_row_helper() {

		echo "<span class='tc-enable-responsive'>" . esc_html__( 'Show responsive values', 'woocommerce-tm-extra-product-options' ) . " <span class='off tcfa tcfa-desktop'></span><span class='on tcfa tcfa-tablet-alt tm-hidden'></span></span>";

	}

	/**
	 * Items per row setting
	 *
	 * @param array   $name Element name.
	 * @param array   $args Array of arguments.
	 * @param boolean $required If the setting is required.
	 * @since 1.0
	 */
	public function add_setting_items_per_row( $name = '', $args = [], $required = false ) {

		$per_row = [
			'_multiple_values' => [
				[
					'id'          => $name . '_items_per_row',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'number',
					'extra'       => [ [ $this, 'add_setting_items_per_row_helper' ], [] ],
					'tags'        => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row][]',
					],
					'label'       => esc_html__( 'Items per row (Desktops and laptops)', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {
				[
					'id'               => $name . '_items_per_row_tablets',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_tablets',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_tablets][]',
					],
					'label'            => esc_html__( 'Items per row (Tablets landscape)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">768px - 1024px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width : 481px) and (max-device-width : 767px) {
				[
					'id'               => $name . '_items_per_row_tablets_small',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_tablets_small',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_tablets_small][]',
					],
					'label'            => esc_html__( 'Items per row (Tablets portrait)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">481px - 767px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width : 320px) and (max-device-width : 480px) {
				[
					'id'               => $name . '_items_per_row_smartphones',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_smartphones',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_smartphones][]',
					],
					'label'            => esc_html__( 'Items per row (Smartphones)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">320px - 480px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2) {
				[
					'id'               => $name . '_items_per_row_iphone5',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_iphone5',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_iphone5][]',
					],
					'label'            => esc_html__( 'Items per row (iPhone 5)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">320px - 568px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2) {
				[
					'id'               => $name . '_items_per_row_iphone6',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_iphone6',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_iphone6][]',
					],
					'label'            => esc_html__( 'Items per row (iPhone 6)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">375px - 667px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2) {
				[
					'id'               => $name . '_items_per_row_iphone6_plus',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_iphone6_plus',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_iphone6_plus][]',
					],
					'label'            => esc_html__( 'Items per row (iPhone 6 +)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">414px - 736px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2) {
				[
					'id'               => $name . '_items_per_row_samsung_galaxy',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_samsung_galaxy',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_samsung_galaxy][]',
					],
					'label'            => esc_html__( 'Items per row (Samnsung Galaxy)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">320px - 640px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],
				// @media only screen and (min-device-width : 800px) and (max-device-width : 1280px) {
				[
					'id'               => $name . '_items_per_row_tablets_galaxy',
					'message0x0_class' => 'builder_responsive_div tc-hidden',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_items_per_row_tablets_galaxy',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_items_per_row_tablets_galaxy][]',
					],
					'label'            => esc_html__( 'Items per row (Galaxy Tablets landscape)', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-pixels">800px - 1280px<span>',
					'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				],

			],
		];

		$per_row = array_merge( $per_row, $args );

		if ( $required ) {
			$per_row['_multiple_values'][0]['required'] = $required;
			$per_row['_multiple_values'][1]['required'] = $required;
			$per_row['_multiple_values'][2]['required'] = $required;
			$per_row['_multiple_values'][3]['required'] = $required;
			$per_row['_multiple_values'][4]['required'] = $required;
			$per_row['_multiple_values'][5]['required'] = $required;
			$per_row['_multiple_values'][6]['required'] = $required;
			$per_row['_multiple_values'][7]['required'] = $required;
			$per_row['_multiple_values'][8]['required'] = $required;
		}

		return $per_row;
	}

	/**
	 * Limit selection setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_limit_choices( $name = '' ) {
		return [
			'id'          => $name . '_limit_choices',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_limit_choices',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_limit_choices][]',
				'min'   => 0,
			],
			'label'       => esc_html__( 'Limit selection', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter a number above 0 to limit the checkbox selection or leave blank for default behaviour.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Exact selection setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_exactlimit_choices( $name = '' ) {
		return [
			'id'          => $name . '_exactlimit_choices',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_exactlimit_choices',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_exactlimit_choices][]',
				'min'   => 0,
			],
			'label'       => esc_html__( 'Exact selection', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter a number above 0 to have the user select the exact number of checkboxes or leave blank for default behaviour.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Minimum selection setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_minimumlimit_choices( $name = '' ) {
		return [
			'id'          => $name . '_minimumlimit_choices',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_minimumlimit_choices',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_minimumlimit_choices][]',
				'min'   => 0,
			],
			'label'       => esc_html__( 'Minimum selection', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter a number above 0 to have the user select at least that number of checkboxes or leave blank for default behaviour.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Upload button style setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function add_setting_button_type( $name = '', $args = [] ) {
		$button_type = [
			'id'          => $name . '_button_type',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'select',
			'tags'        => [
				'id'   => 'builder_' . $name . '_button_type',
				'name' => 'tm_meta[tmfbuilder][' . $name . '_button_type][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'Normal browser button', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Styled button', 'woocommerce-tm-extra-product-options' ),
					'value' => 'button',
				],
			],
			'label'       => esc_html__( 'Upload button style', 'woocommerce-tm-extra-product-options' ),
		];

		$button_type = array_merge( $button_type, $args );

		return $button_type;
	}

	/**
	 * Date picker style setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_button_type2( $name = '' ) {
		return [
			'id'          => $name . '_button_type',
			'wpmldisable' => 1,
			'default'     => 'picker',
			'type'        => 'select',
			'tags'        => [
				'id'   => 'builder_' . $name . '_button_type',
				'name' => 'tm_meta[tmfbuilder][' . $name . '_button_type][]',
			],
			'options'     => [
				[
					'text'  => esc_html__( 'Date field', 'woocommerce-tm-extra-product-options' ),
					'value' => '',
				],
				[
					'text'  => esc_html__( 'Date picker', 'woocommerce-tm-extra-product-options' ),
					'value' => 'picker',
				],
				[
					'text'  => esc_html__( 'Date field and picker', 'woocommerce-tm-extra-product-options' ),
					'value' => 'fieldpicker',
				],
			],
			'label'       => esc_html__( 'Date picker style', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Hide price setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_hide_amount( $name = '' ) {
		return [
			'id'               => $name . '_hide_amount',
			'message0x0_class' => 'builder_' . $name . '_hide_amount_div',
			'wpmldisable'      => 1,
			'default'          => '',
			'type'             => 'checkbox',
			'tags'             => [
				'value' => 'hidden',
				'id'    => 'builder_' . $name . '_hide_amount',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_hide_amount][]',
			],
			'label'            => esc_html__( 'Hide price', 'woocommerce-tm-extra-product-options' ),
			'desc'             => esc_html__( 'Choose whether to hide the price or not.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Quantity selector setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_quantity( $name = '' ) {
		return [
			'_multiple_values' => [
				[
					'id'               => $name . '_quantity',
					'message0x0_class' => 'builder_' . $name . '_quantity_div',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'select',
					'tags'             => [
						'id'    => 'builder_' . $name . '_quantity',
						'class' => 'tm-qty-selector',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_quantity][]',
					],
					'options'          => [
						[
							'text'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
							'value' => '',
						],
						[
							'text'  => esc_html__( 'Right', 'woocommerce-tm-extra-product-options' ),
							'value' => 'right',
						],
						[
							'text'  => esc_html__( 'Left', 'woocommerce-tm-extra-product-options' ),
							'value' => 'left',
						],
						[
							'text'  => esc_html__( 'Top', 'woocommerce-tm-extra-product-options' ),
							'value' => 'top',
						],
						[
							'text'  => esc_html__( 'Bottom', 'woocommerce-tm-extra-product-options' ),
							'value' => 'bottom',
						],
					],
					'label'            => esc_html__( 'Quantity selector', 'woocommerce-tm-extra-product-options' ),
					'desc'             => esc_html__( 'This will show a quantity selector for this option.', 'woocommerce-tm-extra-product-options' ),
				],
				$this->add_setting_min(
					$name . '_quantity',
					[
						'label'            => esc_html__( 'Quantity min value', 'woocommerce-tm-extra-product-options' ),
						'message0x0_class' => 'tm-qty-min',
						'required'         => [
							'.tm-qty-selector' => [
								'operator' => 'isnot',
								'value'    => '',
							],
						],
					],
					false
				),
				$this->add_setting_max(
					$name . '_quantity',
					[
						'label'            => esc_html__( 'Quantity max value', 'woocommerce-tm-extra-product-options' ),
						'message0x0_class' => 'tm-qty-max',
						'required'         => [
							'.tm-qty-selector' => [
								'operator' => 'isnot',
								'value'    => '',
							],
						],
					],
					false
				),
				[
					'id'               => $name . '_quantity_step',
					'message0x0_class' => 'tm-qty-step',
					'wpmldisable'      => 1,
					'default'          => '',
					'type'             => 'number',
					'tags'             => [
						'class' => 'n',
						'id'    => 'builder_' . $name . '_min',
						'name'  => 'tm_meta[tmfbuilder][' . $name . '_quantity_step][]',
						'value' => '',
						'step'  => 'any',
						'min'   => 0,
					],
					'label'            => esc_html__( 'Quantity step', 'woocommerce-tm-extra-product-options' ),
					'desc'             => esc_html__( 'Enter the quantity step.', 'woocommerce-tm-extra-product-options' ),
					'required'         => [
						'.tm-qty-selector' => [
							'operator' => 'isnot',
							'value'    => '',
						],
					],
				],

				$this->add_setting_default_value(
					$name . '_quantity',
					[
						'type'             => 'number',
						'tags'             => [
							'class' => 'n',
							'id'    => 'builder_' . $name . '_quantity_default_value',
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_quantity_default_value][]',
							'value' => '',
						],
						'label'            => esc_html__( 'Quantity Default value', 'woocommerce-tm-extra-product-options' ),
						'message0x0_class' => 'tm-qty-default',
						'desc'             => esc_html__( 'Enter a value to be applied to the Quantity field automatically.', 'woocommerce-tm-extra-product-options' ),
						'required'         => [
							'.tm-qty-selector' => [
								'operator' => 'isnot',
								'value'    => '',
							],
						],
					]
				),
			],
		];
	}

	/**
	 * Placeholder setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function add_setting_placeholder( $name = '', $args = [] ) {
		return array_merge(
			[
				'id'      => $name . '_placeholder',
				'default' => '',
				'type'    => 'text',
				'tags'    => [
					'class' => 't',
					'id'    => 'builder_' . $name . '_placeholder',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_placeholder][]',
					'value' => '',
				],
				'label'   => esc_html__( 'Placeholder', 'woocommerce-tm-extra-product-options' ),
				'desc'    => '',
			],
			$args
		);
	}

	/**
	 * Minimum characters setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_min_chars( $name = '' ) {
		return [
			'id'          => $name . '_min_chars',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_min_chars',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_min_chars][]',
				'value' => '',
				'min'   => 0,
			],
			'label'       => esc_html__( 'Minimum characters', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter a value for the minimum characters the user must enter.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Maximum characters setting
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_max_chars( $name = '' ) {
		return [
			'id'          => $name . '_max_chars',
			'wpmldisable' => 1,
			'default'     => '',
			'type'        => 'number',
			'tags'        => [
				'class' => 'n',
				'id'    => 'builder_' . $name . '_max_chars',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_max_chars][]',
				'value' => '',
				'min'   => 0,
			],
			'label'       => esc_html__( 'Maximum characters', 'woocommerce-tm-extra-product-options' ),
			'desc'        => esc_html__( 'Enter a value to limit the maximum characters the user can enter.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Default value setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function add_setting_default_value( $name = '', $args = [] ) {
		return array_merge(
			[
				'id'      => $name . '_default_value',
				'default' => '',
				'type'    => 'text',
				'tags'    => [
					'class' => 't',
					'id'    => 'builder_' . $name . '_default_value',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_default_value][]',
					'value' => '',
				],
				'label'   => esc_html__( 'Default value', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value to be applied to the field automatically.', 'woocommerce-tm-extra-product-options' ),
			],
			$args
		);
	}

	/**
	 * Default value setting (for textarea)
	 *
	 * @param string $name Element name.
	 * @since 1.0
	 */
	public function add_setting_default_value_multiple( $name = '' ) {
		return [
			'id'      => $name . '_default_value',
			'default' => '',
			'type'    => 'textarea',
			'tags'    => [
				'class' => 't tm-no-editor',
				'id'    => 'builder_' . $name . '_default_value',
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_default_value][]',
				'value' => '',
			],
			'label'   => esc_html__( 'Default value', 'woocommerce-tm-extra-product-options' ),
			'desc'    => esc_html__( 'Enter a value to be applied to the field automatically.', 'woocommerce-tm-extra-product-options' ),
		];
	}

	/**
	 * Element template
	 *
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function template_bitem( $args = [] ) {

		global $post;

		$show_buttons = true;

		if ( ! $post && isset( $_POST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$post = get_post( absint( $_POST['post_id'] ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride, WordPress.Security.NonceVerification
		}

		if ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post->post_type ) {
			$show_buttons = false;
		}
		$wpml_is_original_product = true;
		if ( $post && THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type );
		}

		$is_enabled = isset( $args['is_enabled'] ) ? $args['is_enabled'] : '';
		$is_enabled = '' === $is_enabled ? ' element-is-disabled' : '';
		if ( empty( $this->noecho ) ) {
			echo '<div class="bitem element-' . esc_attr( $args['element'] ) . esc_attr( $is_enabled ) . ' ' . esc_attr( $args['width'] ) . '">';
			if ( ! empty( $args['is_template'] ) ) {
				echo '<input class="builder_element_type" name="tm_meta[tmfbuilder][element_type][]" type="hidden" value="' . esc_attr( $args['element'] ) . '">';
				echo '<input class="div_size" name="tm_meta[tmfbuilder][div_size][]" type="hidden" value="' . esc_attr( $args['width'] ) . '">';
			}
			echo '<div class="hstc2">';
			echo '<div class="bitem-inner">';
			echo '<ul class="bitem-settings">';
			if ( $show_buttons ) {
				echo '<li class="bitem-setting size" title="' . esc_attr__( 'Size', 'woocommerce-tm-extra-product-options' ) . '">';
				echo '<span>' . esc_html( $args['width_display'] ) . '</span>';
				echo '</li>';
				if ( $wpml_is_original_product ) {
					echo '<li class="bitem-setting minus" title="' . esc_attr__( 'Reduce Width', 'woocommerce-tm-extra-product-options' ) . '">';
					echo '<i class="tmicon tcfa tcfa-minus"></i>';
					echo '</li>';
					echo '<li class="bitem-setting plus" title="' . esc_attr__( 'Increase Width', 'woocommerce-tm-extra-product-options' ) . '">';
					echo '<i class="tmicon tcfa tcfa-plus"></i>';
					echo '</li>';
				}
			}
			echo '<li class="bitem-setting edit" title="' . esc_attr__( 'Edit Element', 'woocommerce-tm-extra-product-options' ) . '">';
			echo '<i class="tmicon tcfa tcfa-edit"></i>';
			echo '</li>';
			if ( $show_buttons ) {
				if ( $wpml_is_original_product ) {
					echo '<li class="bitem-setting clone" title="' . esc_attr__( 'Duplicate Element', 'woocommerce-tm-extra-product-options' ) . '">';
					echo '<i class="tmicon tcfa tcfa-copy"></i>';
					echo '</li>';
				}
			}
			echo '</ul>';
			echo '</div>';
			echo '<div class="bitem-inner-info">';
			if ( $show_buttons ) {
				echo '<div class="tm-label-move">';
				echo '<button type="button" class="tmicon tcfa tcfa-grip-vertical move"></button>';
				echo '</div>';
			}
			echo '<div class="tm-label-icon">';
			echo '<div class="tm-icon-wrap">';
			echo '<i class="tmfa tcfa ' . esc_attr( $args['icon'] ) . '"></i>';
			echo '</div>';
			echo '</div>';
			echo '<div class="tm-label-info">';
			echo '<div class="tm-label-desc' . ( '' !== $args['internal_name'] ? ' tc-has-value' : ' tc-empty-value' ) . '">';
			echo '<div class="tm-element-label">' . esc_html( $args['label'] ) . '</div>';
			echo '<div class="tm-internal-label">' . esc_html( $args['internal_name'] ) . '</div>';
			echo '</div>';
			echo '<div class="tm-for-bitem tm-label-desc-edit tm-hidden" data-element="' . esc_attr( $args['element'] ) . '">';
			if ( ! empty( $args['is_template'] ) ) {
				echo '<input type="text" value="' . esc_attr( $args['internal_name'] ) . '" name="tm_meta[tmfbuilder][' . esc_attr( $args['element'] ) . '_internal_name][]" class="t tm-internal-name">';
			}
			echo '</div>';
			echo '<div class="tm-label">';
			if ( '&nbsp;' === $args['desc'] || '' === $args['desc'] ) {
				esc_html_e( '(No title)', 'woocommerce-tm-extra-product-options' );
			} else {
				echo esc_html( $args['desc'] );
			}
			echo '</div>';
			echo '<div class="tm-label-line">';
			echo '<div class="tm-label-line-inner"></div>';
			echo '</div>';
			echo '</div>';
			echo '<div class="tm-label-delete">';
			echo '<button type="button" class="tmicon tcfa tcfa-times delete"></button>';
			echo '</div>';
			echo '</div>';
			echo '<div class="inside">';
			echo '<div class="manager">';
			echo '<div class="builder-element-wrap">';
		}
		if ( isset( $args['fields'] ) && is_array( $args['fields'] ) ) {
			if ( empty( $args['is_template'] ) ) {
				$pointer = count( $this->jsbuilder ) - 1;

				if ( $pointer >= 0 ) {
					$fields_pointer = count( $this->jsbuilder[ $pointer ]['fields'] );
					$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ]   = [];
					$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ][] = [
						'id'      => 'element_type',
						'default' => $args['element'],
						'type'    => 'hidden',
						'tags'    => [
							'class' => 'builder_element_type',
							'name'  => 'tm_meta[tmfbuilder][element_type][]',
						],
					];
					$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ][] = [
						'id'      => 'div_size',
						'default' => $args['width'],
						'type'    => 'hidden',
						'tags'    => [
							'class' => 'div_size',
							'name'  => 'tm_meta[tmfbuilder][div_size][]',
						],
					];
					$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ][] = [
						'id'      => $args['element'] . '_internal_name',
						'default' => $args['internal_name'],
						'type'    => 'text',
						'tags'    => [
							'class' => 't tm-internal-name',
							'name'  => 'tm_meta[tmfbuilder][' . $args['element'] . '_internal_name][]',
						],
					];
					foreach ( $args['fields'] as $value ) {
						if ( 'custom' !== $value['type'] ) {
							if ( 'custom_multiple' === $value['type'] ) {
								if ( isset( $value['html'] ) ) {
									if ( is_array( $value['html'] ) ) {
										$method                     = $value['html'][0];
										$methodargs                 = $value['html'][1];
										$methodargs[0]['return_js'] = true;

										$returned_js = call_user_func_array( $method, $methodargs );

										if ( is_array( $returned_js ) ) {
											$temp_array = [
												'id'       => 'multiple',
												'multiple' => [],
											];
											foreach ( $returned_js as $js_value ) {
												$js_value                 = $this->remove_for_js( $js_value );
												$temp_array['multiple'][] = $js_value;
												if ( 'variations_options' === $value['id'] ) {
													foreach ( $js_value as $js_value_array ) {
														$js_value_array       = $this->remove_for_js( $js_value_array );
														$js_value_array['id'] = $value['id'];
														$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ][] = $js_value_array;
													}
												}
											}
											$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ][] = $temp_array;
										}
									}
								}
							} else {
								if ( isset( $value['fill'] ) ) {
									if ( 'product' === $value['fill'] ) {
										$product_ids = isset( $value['default'] ) ? $value['default'] : [];
										if ( ! is_array( $product_ids ) ) {
											if ( $product_ids ) {
												$product_ids = [ $product_ids ];
											} else {
												$product_ids = [];
											}
										}
										if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
											$value['options'] = [];
										}
										foreach ( $product_ids as $product_id ) {
											$product = wc_get_product( $product_id );
											if ( is_object( $product ) ) {
												$value['options'][] = [
													'text' => wp_kses_post( $product->get_formatted_name() ),
													'value' => $product_id,
												];
											}
										}
									} elseif ( 'category' === $value['fill'] ) {
										$category_ids = isset( $value['default'] ) ? $value['default'] : [];
										if ( ! is_array( $category_ids ) ) {
											$category_ids = [];
										}
										if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
											$value['options'] = [];
										}
										foreach ( $category_ids as $category_id ) {
											$current_category_id = wc_clean( wp_unslash( $category_id ) );
											$current_category    = $current_category_id ? get_term_by( 'id', $current_category_id, 'product_cat' ) : false;
											if ( is_object( $current_category ) ) {
												$value['options'][] = [
													'text' => wp_kses_post( $current_category->name ),
													'value' => $current_category_id,
												];
											}
										}
									}
								}
								if ( 'product_default_value' === $value['id'] ) {
									$temp_product = wc_get_product( $value['default'] );
									if ( $temp_product ) {
										$value['current_selected_text'] = $temp_product->get_title();
									}
								}
								$this->jsbuilder[ $pointer ]['fields'][ $fields_pointer ][] = $this->remove_for_js( $value );
							}
						}
					}
				}
			} else {
				if ( empty( $this->noecho ) ) {
					foreach ( $args['fields'] as $value ) {
						if ( isset( $value['fill'] ) ) {
							if ( 'product' === $value['type'] ) {
								$product_ids = isset( $value['default'] ) ? $value['default'] : [];
								if ( ! is_array( $product_ids ) ) {
									$product_ids = [];
								}
								if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
									$value['options'] = [];
								}
								foreach ( $product_ids as $product_id ) {
									$product = wc_get_product( $product_id );
									if ( is_object( $product ) ) {
										$value['options'][] = [
											'text'  => wp_kses_post( $product->get_formatted_name() ),
											'value' => $product_id,
										];
									}
								}
							} elseif ( 'category' === $value['fill'] ) {
								$category_ids = isset( $value['default'] ) ? $value['default'] : [];
								if ( ! is_array( $category_ids ) ) {
									$category_ids = [];

									if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
										$value['options'] = [];
									}
									foreach ( $category_ids as $category_id ) {
										$current_category_id = wc_clean( wp_unslash( $category_id ) );
										$current_category    = $current_category_id ? get_term_by( 'id', $current_category_id, 'product_cat' ) : false;
										if ( is_object( $current_category ) ) {
											$value['options'][] = [
												'text'  => wp_kses_post( $current_category->name ),
												'value' => $current_category_id,
											];
										}
									}
								}
							}
						}
						THEMECOMPLETE_EPO_HTML()->create_field( $value, 1 );
					}
				}
			}
		}
		if ( empty( $this->noecho ) ) {
			echo '</div></div></div></div></div>';
		}
	}

	/**
	 * Section elements template
	 *
	 * @param array $args Array of arguments.
	 * @since  1.0
	 * @access private
	 */
	private function section_elements_template( $args = [] ) {

		$args = shortcode_atts(
			[
				'section_fields'           => '',
				'size'                     => '',
				'wpml_is_original_product' => true,
				'sections_internal_name'   => false,
				'is_template'              => false,
			],
			$args
		);

		$section_fields           = $args['section_fields'];
		$size                     = $args['size'];
		$wpml_is_original_product = $args['wpml_is_original_product'];
		$sections_internal_name   = $args['sections_internal_name'];

		if ( empty( $args['is_template'] ) ) {
			$pointer                                = count( $this->jsbuilder );
			$this->jsbuilder[ $pointer ]            = [];
			$this->jsbuilder[ $pointer ]['fields']  = [];
			$this->jsbuilder[ $pointer ]['section'] = [];
			$this->jsbuilder[ $pointer ]['size']    = $size;
			$this->jsbuilder[ $pointer ]['sections_internal_name']            = $sections_internal_name;
			$this->jsbuilder[ $pointer ]['section']['sections_internal_name'] = [
				'id'      => 'sections_internal_name',
				'default' => $sections_internal_name,
				'type'    => 'text',
				'tags'    => [
					'class' => 't tm-internal-name',
					'name'  => 'tm_meta[tmfbuilder][sections_internal_name][]',
				],
			];
		}
		if ( empty( $this->noecho ) ) {
			echo '<div class="section_elements closed">';
		}
		foreach ( $section_fields as $section_field ) {
			if ( empty( $args['is_template'] ) ) {
				if ( 'custom' !== $section_field['type'] ) {
					$this->jsbuilder[ $pointer ]['section'][ $section_field['id'] ] = $this->remove_for_js( $section_field );
				}
			} else {
				if ( empty( $this->noecho ) ) {
					THEMECOMPLETE_EPO_HTML()->create_field( $section_field, 1 );
				}
			}
		}
		if ( empty( $this->noecho ) ) {
			global $post;
			$show_buttons = true;

			if ( ! $post && isset( $_POST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$post = get_post( absint( $_POST['post_id'] ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride, WordPress.Security.NonceVerification
			}

			if ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post->post_type ) {
				$show_buttons = false;
			}
			echo '</div>';
			echo '<div class="section-inner">';
			if ( $show_buttons ) {
				echo '<ul class="section-settings">';
				echo '<li class="section-setting size" title="' . esc_attr__( 'Size', 'woocommerce-tm-extra-product-options' ) . '">';
				echo '<span>' . esc_html( $size ) . '</span>';
				echo '</li>';
				if ( $wpml_is_original_product ) {
					echo '<li class="section-setting minus" title="' . esc_attr__( 'Reduce Width', 'woocommerce-tm-extra-product-options' ) . '">';
					echo '<i class="tmicon tcfa tcfa-minus"></i>';
					echo '</li>';
					echo '<li class="section-setting plus" title="' . esc_attr__( 'Increase Width', 'woocommerce-tm-extra-product-options' ) . '">';
					echo '<i class="tmicon tcfa tcfa-plus"></i>';
					echo '</li>';
				}
				echo '<li class="section-setting edit" title="' . esc_attr__( 'Edit Section', 'woocommerce-tm-extra-product-options' ) . '">';
				echo '<i class="tmicon tcfa tcfa-edit"></i>';
				echo '</li>';
				if ( $wpml_is_original_product ) {
					echo '<li class="bitem-setting clone" title="' . esc_attr__( 'Duplicate Section', 'woocommerce-tm-extra-product-options' ) . '">';
					echo '<i class="tmicon tcfa tcfa-copy"></i>';
					echo '</li>';
				}
				echo '</ul>';
			}
			echo '</div>';
			echo '<div class="btitle">';
			if ( $show_buttons ) {
				if ( $wpml_is_original_product ) {
					echo '<button type="button" class="tmicon tcfa tcfa-grip-vertical move"></button>';
					echo '<button type="button" class="tmicon tcfa tcfa-times delete"></button>';
				}
				echo '<button type="button" class="tmicon tcfa tcfa-caret-down fold"></button>';
				echo '<div class="tm-label-desc' . ( '' !== $sections_internal_name ? ' tc-has-value' : ' tc-empty-value' ) . ' display-flex">';
				echo '<div class="tm-element-label">' . esc_html__( 'Section', 'woocommerce-tm-extra-product-options' ) . '</div>';
				echo '<div class="tm-internal-label">' . esc_html( $sections_internal_name ) . '</div>';
				echo '</div>';
			}
			echo '<div class="tm-for-section tm-label-desc-edit tm-hidden">';
			if ( ! empty( $args['is_template'] ) ) {
				echo '<input type="text" value="' . esc_attr( $sections_internal_name ) . '" name="tm_meta[tmfbuilder][sections_internal_name][]" class="t tm-internal-name">';
			}
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Section template
	 *
	 * @param array $args Array of arguments.
	 * @since  1.0
	 * @access private
	 */
	private function section_template( $args = [] ) {

		global $post;

		$args = shortcode_atts(
			[
				'section_fields'           => '',
				'size'                     => '',
				'section_size'             => '',
				'sections_slides'          => '',
				'sections_tabs_labels'     => '',
				'sections_type'            => '',
				'elements'                 => '',
				'wpml_is_original_product' => true,
				'sections_internal_name'   => false,
				'is_template'              => false,
			],
			$args
		);

		$section_fields           = $args['section_fields'];
		$size                     = $args['size'];
		$section_size             = $args['section_size'];
		$sections_slides          = $args['sections_slides'];
		$sections_tabs_labels     = json_decode( $args['sections_tabs_labels'] );
		$sections_type            = $args['sections_type'];
		$elements                 = $args['elements'];
		$wpml_is_original_product = $args['wpml_is_original_product'];
		$sections_internal_name   = $args['sections_internal_name'];

		if ( false === $sections_internal_name ) {
			$sections_internal_name = esc_html__( 'Section', 'woocommerce-tm-extra-product-options' );
		}

		if ( is_array( $elements ) ) {
			$elements = array_values( $elements );
		}

		if ( '' !== $sections_slides && is_array( $elements ) ) {
			if ( empty( $this->noecho ) ) {
				echo '<div class="builder-wrapper tm-slider-wizard ' . esc_attr( $section_size ) . ' is-' . esc_attr( $sections_type ) . '"><div class="builder-section-wrap">';
			}
			$this->section_elements_template(
				[
					'section_fields'           => $section_fields,
					'size'                     => $size,
					'wpml_is_original_product' => $wpml_is_original_product,
					'sections_internal_name'   => $sections_internal_name,
					'is_template'              => $args['is_template'],
				]
			);

			$sections_slides = explode( ',', $sections_slides );

			if ( empty( $this->noecho ) ) {
				echo '<div class="transition tm-slider-wizard-headers">';
				$s = 0;

				foreach ( $sections_slides as $key => $value ) {
					if ( ! isset( $sections_tabs_labels[ $s ] ) ) {
						$sections_tabs_labels[ $s ] = $s + 1;
					}
					echo '<div class="tm-box"><h4 class="tm-slider-wizard-header" data-id="tc-tab-slide' . esc_attr( $s ) . '"><span class="tab-text">' . esc_html( $sections_tabs_labels[ $s ] ) . '</span></h4></div>';

					$s ++;

				}
				echo '</div>';
				if ( $wpml_is_original_product && ( ! $post || ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $post->post_type ) ) ) {
					echo '<div class="bitem-add tc-prepend tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-prepend tmfa tcfa tcfa-plus"></button></div></div>';
				}
			}

			$c = 0;
			$s = 0;

			foreach ( $sections_slides as $key => $value ) {

				$value = (int) $value;

				if ( empty( $this->noecho ) ) {
					echo "<div class='bitem-wrapper tc-tab-slide tc-tab-slide" . esc_attr( $s ) . "'>";
				}
				for ( $_s = $c; $_s < ( $c + $value ); $_s ++ ) {
					if ( isset( $elements[ $_s ] ) ) {
						$this->template_bitem( $elements[ $_s ] );
					}
				}
				if ( empty( $this->noecho ) ) {
					echo '</div>';
				}

				$c = $c + $value;
				$s ++;

			}
			if ( empty( $this->noecho ) ) {
				if ( $wpml_is_original_product && ( ! $post || ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $post->post_type ) ) ) {
					echo '<div class="bitem-add tc-append tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-append tmfa tcfa tcfa-plus"></button></div></div>';
				}
				echo '</div></div>';
			}
		} else {
			if ( empty( $this->noecho ) ) {
				echo "<div class='builder-wrapper " . esc_attr( $section_size ) . "'><div class='builder-section-wrap'>";
			}
			$this->section_elements_template(
				[
					'section_fields'           => $section_fields,
					'size'                     => $size,
					'wpml_is_original_product' => $wpml_is_original_product,
					'sections_internal_name'   => $sections_internal_name,
					'is_template'              => $args['is_template'],
				]
			);
			if ( empty( $this->noecho ) ) {
				if ( $wpml_is_original_product && ( ! $post || ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $post->post_type ) ) ) {
					echo '<div class="bitem-add tc-prepend tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-prepend tmfa tcfa tcfa-plus"></button></div></div>';
				}

				echo "<div class='bitem-wrapper'>";
			}

			if ( is_array( $elements ) ) {
				foreach ( $elements as $value ) {
					$this->template_bitem( $value );
				}
			}

			if ( empty( $this->noecho ) ) {
				echo '</div>';
				if ( $wpml_is_original_product && ( ! $post || ( $post && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $post->post_type ) ) ) {
					echo '<div class="bitem-add tc-append tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-append tmfa tcfa tcfa-plus"></button></div></div>';
				}
				echo '</div></div>';
			}
		}

	}

	/**
	 * Generates all hidden sections for use in jQuery.
	 *
	 * @param boolean $wpml_is_original_product If the product is the original product.
	 * @since  1.0.0
	 * @access public
	 */
	public function template_section_elements( $wpml_is_original_product = true ) {

		$this->section_template(
			[
				'section_fields'           => $this->section->properties,
				'size'                     => $this->sizer['w100'],
				'section_size'             => '',
				'sections_slides'          => '',
				'sections_tabs_labels'     => '',
				'sections_type'            => '',
				'elements'                 => '',
				'wpml_is_original_product' => $wpml_is_original_product,
				'sections_internal_name'   => false,
				'is_template'              => true,
			]
		);

	}

	/**
	 * Generates all hidden elements for use in jQuery.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function template_elements() {

		foreach ( $this->all_elements as $element => $settings ) {

			// double quotes are problematic to json_encode.
			$settings->name = str_replace( '"', "'", $settings->name );
			$fields         = [];

			foreach ( $settings->properties as $key => $value ) {
				// no need to auto fill the product element here
				// since it will always be empty.
				$fields[] = $value;
			}

			$this->template_bitem(
				[
					'element'       => $element,
					'width'         => $settings->width,
					'width_display' => $settings->width_display,
					'internal_name' => $settings->name,
					'label'         => $settings->name,
					'fields'        => $fields,
					'desc'          => '&nbsp;',
					'icon'          => $settings->icon,
					'is_enabled'    => '1',
					'is_template'   => true,
				]
			);

		}

	}

	/**
	 * Clear array values
	 *
	 * @param mixed $val The value to clear.
	 * @since  1.0
	 * @access private
	 */
	private function clear_array_values( $val ) {
		if ( is_array( $val ) ) {
			return array_map( [ $this, 'clear_array_values' ], $val );
		} else {
			return '';
		}
	}

	/**
	 * Set array values to false
	 *
	 * @param mixed $val The value to false.
	 * @since  5.0
	 * @access private
	 */
	private function return_false_array_values( $val ) {
		if ( is_array( $val ) ) {
			return array_map( [ $this, 'return_false_array_values' ], $val );
		} else {
			return false;
		}
	}

	/**
	 * Get current jsbuilder.
	 *
	 * @param integer $post_id The original post id.
	 * @param integer $current_post_id current post id.
	 * @param boolean $wpml_is_original_product If the product is the original product.
	 * @since  5.0
	 * @access public
	 */
	public function get_current_jsbuilder( $post_id = 0, $current_post_id = 0, $wpml_is_original_product = true ) {
		$this->noecho    = true;
		$this->jsbuilder = [];
		$this->print_saved_elements( $post_id, $current_post_id, $wpml_is_original_product );
		$this->noecho = false;

		return $this->jsbuilder;
	}

	/**
	 * Generates all saved elements.
	 * Used when importing CSV files
	 *
	 * @param integer $post_id The original post id.
	 * @param integer $current_post_id current post id.
	 * @param boolean $wpml_is_original_product If the product is the original product.
	 * @since  5.0
	 * @access public
	 */
	public function ajax_print_saved_elements( $post_id = 0, $current_post_id = 0, $wpml_is_original_product = true ) {
		$this->admin_init();
		$this->print_saved_elements( $post_id, $current_post_id, $wpml_is_original_product );
	}

	/**
	 * Generates all saved elements.
	 *
	 * @param integer $post_id The original post id.
	 * @param integer $current_post_id current post id.
	 * @param boolean $wpml_is_original_product If the product is the original product.
	 * @since  1.0.0
	 * @access public
	 */
	public function print_saved_elements( $post_id = 0, $current_post_id = 0, $wpml_is_original_product = true ) {

		$builder         = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
		$current_builder = themecomplete_get_post_meta( $current_post_id, 'tm_meta_wpml', true );

		if ( ! $current_builder ) {
			$current_builder = [];
		} else {
			if ( ! isset( $current_builder['tmfbuilder'] ) ) {
				$current_builder['tmfbuilder'] = [];
			}
			$current_builder = $current_builder['tmfbuilder'];
		}

		if ( ! isset( $builder['tmfbuilder'] ) ) {
			if ( ! is_array( $builder ) ) {
				$builder = [];
			}
			$builder['tmfbuilder'] = [];
		}
		$builder = $builder['tmfbuilder'];

		// only check for element_type meta as if it exists div_size will exist too unless database has been compromised.

		if ( ! empty( $post_id ) && is_array( $builder ) && count( $builder ) > 0 && isset( $builder['sections'] ) && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] ) > 0 ) {
			// All the elements.
			$_elements = $builder['element_type'];
			// All element sizes.
			$_div_size = $builder['div_size'];

			// All sections (holds element count for each section).
			$_sections = $builder['sections'];
			// All section sizes.
			$_sections_size = $builder['sections_size'];

			$_sections_slides      = isset( $builder['sections_slides'] ) ? $builder['sections_slides'] : '';
			$_sections_tabs_labels = isset( $current_builder['sections_tabs_labels'] ) ? isset( $current_builder['sections_tabs_labels'] ) : ( isset( $builder['sections_tabs_labels'] ) ? $builder['sections_tabs_labels'] : '' );

			$sections_type = $builder['sections_type'];

			if ( ! is_array( $_sections ) ) {
				$_sections = [ count( $_elements ) ];
			}
			if ( ! is_array( $_sections_size ) ) {
				$_sections_size = array_fill( 0, count( $_sections ), 'w100' );
			}

			if ( ! is_array( $_sections_slides ) ) {
				$_sections_slides = array_fill( 0, count( $_sections ), '' );
			}

			if ( ! is_array( $_sections_tabs_labels ) ) {
				$_sections_tabs_labels = array_fill( 0, count( $_sections ), '' );
			}

			$_helper_counter       = 0;
			$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();
			$t                     = [];
			$_counter              = [];
			$id_counter            = [];
			$count_sections        = count( $_sections );
			for ( $_s = 0; $_s < $count_sections; $_s ++ ) {

				$section_fields          = [];
				$_sections_internal_name = '';
				foreach ( $this->section->properties as $_sk => $_sv ) {
					$transition_counter = $_s;
					$section_use_wpml   = false;
					if ( isset( $current_builder['sections_uniqid'] )
						&& isset( $builder['sections_uniqid'] )
						&& isset( $builder['sections_uniqid'][ $_s ] )
					) {
						// get index of element id in internal array.
						$get_current_builder_uniqid_index = array_search( $builder['sections_uniqid'][ $_s ], $current_builder['sections_uniqid'], true );
						if ( null !== $get_current_builder_uniqid_index && false !== $get_current_builder_uniqid_index ) {
							$transition_counter = $get_current_builder_uniqid_index;
							$section_use_wpml   = true;
						}
					}

					if ( isset( $builder['sections_internal_name'] ) && isset( $builder['sections_internal_name'][ $_s ] ) ) {
						$_sections_internal_name = $builder['sections_internal_name'][ $_s ];
						if ( $section_use_wpml
							&& isset( $current_builder['sections_internal_name'] )
							&& isset( $current_builder['sections_internal_name'][ $transition_counter ] )
						) {
							$_sections_internal_name = $current_builder['sections_internal_name'][ $transition_counter ];
						}
					}

					if ( isset( $builder[ $_sv['id'] ] ) && isset( $builder[ $_sv['id'] ][ $_s ] ) ) {
						$_sv['default'] = $builder[ $_sv['id'] ][ $_s ];
						if ( $section_use_wpml
							&& isset( $current_builder[ $_sv['id'] ] )
							&& isset( $current_builder[ $_sv['id'] ][ $transition_counter ] )
						) {
							$_sv['default'] = $current_builder[ $_sv['id'] ][ $transition_counter ];
						}
					}
					if ( isset( $_sv['tags']['id'] ) ) {
						// we assume that $_sv['tags']['name'] exists if tag id is set.
						$_name             = str_replace( [ '[', ']' ], '', $_sv['tags']['name'] );
						$_sv['tags']['id'] = $_name . $_s;
					}
					if ( 'sectionuniqid' === $_sk && ! isset( $builder[ $_sv['id'] ] ) ) {
						$_sv['default'] = THEMECOMPLETE_EPO_HELPER()->tm_uniqid();
					}
					if ( absint( $post_id ) !== absint( $current_post_id ) && ! empty( $_sv['wpmldisable'] ) ) {
						$_sv['disabled'] = 1;
					}
					if ( 'sections_clogic' === $_sv['id'] ) {
						if ( is_object( $_sv['default'] ) ) {
							$_sv['default'] = wp_json_encode( $_sv['default'] );
						}
						$_sv['default'] = stripslashes_deep( $_sv['default'] );
					}

					$section_fields[] = $_sv;
				}

				$elements_html       = '';
				$elements_html_array = [];

				for ( $k0 = $_helper_counter; $k0 < (int) ( $_helper_counter + (int) $_sections[ $_s ] ); $k0 ++ ) {
					if ( isset( $_elements[ $k0 ] ) ) {
						if ( isset( $this->all_elements[ $_elements[ $k0 ] ] ) ) {
							$elements_html_array[ $k0 ] = '';
							$regetdefault               = 0;
							if ( ! isset( $_counter[ $_elements[ $k0 ] ] ) ) {
								$_counter[ $_elements[ $k0 ] ] = 0;
							} else {
								$_counter[ $_elements[ $k0 ] ] ++;
							}

							$transition_counter = $_counter[ $_elements[ $k0 ] ];
							$use_wpml           = false;
							if ( isset( $current_builder[ $_elements[ $k0 ] . '_uniqid' ] )
								&& isset( $builder[ $_elements[ $k0 ] . '_uniqid' ] )
								&& isset( $builder[ $_elements[ $k0 ] . '_uniqid' ][ $_counter[ $_elements[ $k0 ] ] ] )
							) {
								// get index of element id in internal array.
								$get_current_builder_uniqid_index = array_search(
									$builder[ $_elements[ $k0 ] . '_uniqid' ][ $_counter[ $_elements[ $k0 ] ] ],
									$current_builder[ $_elements[ $k0 ] . '_uniqid' ],
									true
								);
								if ( null !== $get_current_builder_uniqid_index && false !== $get_current_builder_uniqid_index ) {
									$transition_counter = $get_current_builder_uniqid_index;
									$use_wpml           = true;
								}
							}

							$internal_name = $this->all_elements[ $_elements[ $k0 ] ]->name;
							if ( isset( $builder[ $_elements[ $k0 ] . '_internal_name' ] )
								&& isset( $builder[ $_elements[ $k0 ] . '_internal_name' ][ $_counter[ $_elements[ $k0 ] ] ] )
							) {
								$internal_name = $builder[ $_elements[ $k0 ] . '_internal_name' ][ $_counter[ $_elements[ $k0 ] ] ];
								if ( $use_wpml
									&& isset( $current_builder[ $_elements[ $k0 ] . '_internal_name' ] )
									&& isset( $current_builder[ $_elements[ $k0 ] . '_internal_name' ][ $transition_counter ] )
								) {
									$internal_name = $current_builder[ $_elements[ $k0 ] . '_internal_name' ][ $transition_counter ];
								}
							}

							// backwards compatibility.
							if ( isset( $builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
								switch ( $builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
									case 'fee':
										$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = '';
										$builder[ $_elements[ $k0 ] . '_fee' ][ $_counter[ $_elements[ $k0 ] ] ]        = '1';
										break;
									case 'stepfee':
										$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = 'step';
										$builder[ $_elements[ $k0 ] . '_fee' ][ $_counter[ $_elements[ $k0 ] ] ]        = '1';
										break;
									case 'currentstepfee':
										$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = 'currentstep';
										$builder[ $_elements[ $k0 ] . '_fee' ][ $_counter[ $_elements[ $k0 ] ] ]        = '1';
										break;
								}
							}

							// backwards compatibility.
							if ( ! isset( $builder[ $_elements[ $k0 ] . '_show_tooltip' ] ) ) {
								if ( isset( $builder[ $_elements[ $k0 ] . '_swatchmode' ] ) ) {
									$builder[ $_elements[ $k0 ] . '_show_tooltip' ] = $builder[ $_elements[ $k0 ] . '_swatchmode' ];
								} else {
									$builder[ $_elements[ $k0 ] . '_show_tooltip' ] = [];
								}
							}

							// backwards compatibility.
							if ( ! isset( $builder[ $_elements[ $k0 ] . '_replacement_mode' ] ) ) {
								$builder[ $_elements[ $k0 ] . '_replacement_mode' ] = [];
							}
							if ( ! isset( $builder[ $_elements[ $k0 ] . '_replacement_mode' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
								if ( isset( $builder[ $_elements[ $k0 ] . '_use_images' ] ) ) {
									if ( '' !== $builder[ $_elements[ $k0 ] . '_use_images' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
										$builder[ $_elements[ $k0 ] . '_replacement_mode' ][ $_counter[ $_elements[ $k0 ] ] ] = 'image';
									} elseif ( '' !== $builder[ $_elements[ $k0 ] . '_use_colors' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
										$builder[ $_elements[ $k0 ] . '_replacement_mode' ][ $_counter[ $_elements[ $k0 ] ] ] = 'color';
									} else {
										$builder[ $_elements[ $k0 ] . '_replacement_mode' ][ $_counter[ $_elements[ $k0 ] ] ] = 'none';
									}
								} else {
									$builder[ $_elements[ $k0 ] . '_replacement_mode' ][ $_counter[ $_elements[ $k0 ] ] ] = 'none';
								}
								$regetdefault = 1;
							}

							// backwards compatibility.
							if ( ! isset( $builder[ $_elements[ $k0 ] . '_swatch_position' ] ) ) {
								$builder[ $_elements[ $k0 ] . '_swatch_position' ] = [];
							}
							if ( ! isset( $builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
								switch ( $builder[ $_elements[ $k0 ] . '_replacement_mode' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
									case 'none':
										$builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] = 'center';
										break;
									case 'image':
										if ( isset( $builder[ $_elements[ $k0 ] . '_use_images' ] ) && ! empty( $builder[ $_elements[ $k0 ] . '_use_images' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
											switch ( $builder[ $_elements[ $k0 ] . '_use_images' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
												case 'images':
													$builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] = 'center';
													break;
												default:
													$builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] = $builder[ $_elements[ $k0 ] . '_use_images' ][ $_counter[ $_elements[ $k0 ] ] ];
													break;
											}
										}
										break;
									case 'color':
										if ( isset( $builder[ $_elements[ $k0 ] . '_use_colors' ] ) && ! empty( $builder[ $_elements[ $k0 ] . '_use_colors' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
											switch ( $builder[ $_elements[ $k0 ] . '_use_colors' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
												case 'color':
													$builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] = 'center';
													break;
												default:
													$builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] = $builder[ $_elements[ $k0 ] . '_use_colors' ][ $_counter[ $_elements[ $k0 ] ] ];
													break;
											}
										}
										break;
								}
								if ( ! isset( $builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
									$builder[ $_elements[ $k0 ] . '_swatch_position' ][ $_counter[ $_elements[ $k0 ] ] ] = 'center';
								}
								$regetdefault = 1;
							}

							$fields       = [];
							$element_desc = '&nbsp;';

							foreach ( $this->all_elements[ $_elements[ $k0 ] ]->properties as $key => $value ) {

								if ( isset( $value['id'] ) ) {
									$_vid = $value['id'];

									if ( ! isset( $t[ $_vid ] ) || $regetdefault ) {
										$t[ $_vid ] = isset( $builder[ $_vid ] )
											? $builder[ $_vid ]
											: null;
										if ( null !== $t[ $_vid ] ) {
											if ( absint( $post_id ) !== absint( $current_post_id ) && ! empty( $value['wpmldisable'] ) ) {
												$value['disabled'] = 1;
											}
										}
									} elseif ( null !== $t[ $_vid ] ) {
										if ( absint( $post_id ) !== absint( $current_post_id ) && ! empty( $value['wpmldisable'] ) ) {
											$value['disabled'] = 1;
										}
									}

									if ( null !== $t[ $_vid ]
										&& is_array( $t[ $_vid ] )
										&& count( $t[ $_vid ] ) > 0
										&& isset( $value['default'] )
										&& isset( $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
										$value['default'] = $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ];

										if ( empty( $value['wpmldisable'] ) && $use_wpml
											&& isset( $current_builder[ $_vid ] )
											&& isset( $current_builder[ $_vid ][ $transition_counter ] )
										) {
											$value['default'] = $current_builder[ $_vid ][ $transition_counter ];
										}
										if ( 'number' === $value['type'] ) {
											$value['default'] = themecomplete_convert_local_numbers( $value['default'] );
										}
									}

									if ( null !== $t[ $_vid ]
										&& is_string( $t[ $_vid ] )
										&& isset( $value['default'] )
									) {
										$value['default'] = $t[ $_vid ];

										if ( 'number' === $value['type'] ) {
											$value['default'] = themecomplete_convert_local_numbers( $value['default'] );
										}
									}

									if ( ( 'header_title' === $_vid || $_elements[ $k0 ] . '_header_title' === $_vid ) && '' !== $value['default'] ) {
										$element_desc = $value['default'];
									}

									if ( $_elements[ $k0 ] . '_clogic' === $_vid ) {
										if ( is_object( $value['default'] ) ) {
											$value['default'] = wp_json_encode( $value['default'] );
										}
										$value['default'] = stripslashes_deep( $value['default'] );
									}

									// backwards compatibility.
									if ( $_vid === $_elements[ $k0 ] . '_clear_options' ) {
										if ( 'clear' === $value['default'] ) {
											$value['default'] = '1';
											if ( isset( $builder[ $_elements[ $k0 ] . '_clear_options' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
												$builder[ $_elements[ $k0 ] . '_clear_options' ][ $_counter[ $_elements[ $k0 ] ] ] = '1';
											}
										}
									}

									// backwards compatibility.
									if ( 'radiobuttons' === $_elements[ $k0 ] && ( 'radiobuttons_fee' === $_vid || 'radiobuttons_subscriptionfee' === $_vid ) ) {
										$_prices_type_base = isset( $builder['multiple_radiobuttons_options_price_type'] )
											? $builder['multiple_radiobuttons_options_price_type']
											: [];

										foreach ( $_prices_type_base as $_extra_key => $_extra_value ) {
											foreach ( $_extra_value as $__key => $__value ) {
												if ( 'radiobuttons_fee' === $_vid && 'fee' === $__value ) {
													$value['default'] = '1';
													break 2;
												}
												if ( 'radiobuttons_subscriptionfee' === $_vid && 'subscriptionfee' === $__value ) {
													$value['default']           = '1';
													$_multiple_prices_type_base = $builder['multiple_radiobuttons_options_price_type'][ $_counter[ $_elements[ $k0 ] ] ];
													if ( is_array( $_multiple_prices_type_base ) ) {
														foreach ( $_multiple_prices_type_base as $_xtra_key => $_xtra_value ) {
															$builder['multiple_radiobuttons_options_price_type'][ $_counter[ $_elements[ $k0 ] ] ][ $_xtra_key ] = '';
														}
													}
													break 2;
												}
											}
										}
									}

									if ( 'selectbox' === $_elements[ $k0 ] && 'selectbox_subscriptionfee' === $_vid ) {
										$_prices_type_base = isset( $builder['selectbox_price_type'] )
											? $builder['selectbox_price_type']
											: [];

										foreach ( $_prices_type_base as $__key => $__value ) {
											if ( 'subscriptionfee' === $__value ) {
												$value['default']           = '1';
												$_multiple_prices_type_base = $builder['multiple_selectbox_options_price_type'][ $_counter[ $_elements[ $k0 ] ] ];
												if ( is_array( $_multiple_prices_type_base ) ) {
													foreach ( $_multiple_prices_type_base as $_extra_key => $_extra_value ) {
														$builder['multiple_selectbox_options_price_type'][ $_counter[ $_elements[ $k0 ] ] ][ $_extra_key ] = '';
													}
												}
												break;
											}
										}
									}

									if ( $_vid === $_elements[ $k0 ] . '_enabled' ) {
										if ( '0' === (string) $value['default'] ) {
											$value['default'] = '';
											if ( isset( $builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
												$builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] = '';
											}
										}
									}

									if ( $_vid === $_elements[ $k0 ] . '_price_type' ) {
										if ( 'subscriptionfee' === $value['default'] ) {
											$value['default'] = '';
											$builder[ $_elements[ $k0 ] . '_subscriptionfee' ][ $_counter[ $_elements[ $k0 ] ] ] = '1';
											if ( isset( $builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
												$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = '';
											}
										}
									}

									if ( 'variations_options' === $_vid ) {
										if ( $use_wpml && isset( $current_builder[ $_vid ] ) ) {
											$value['html'] = [
												[ $this, 'builder_sub_variations_options' ],
												[
													[
														'meta'       => isset( $current_builder[ $_vid ] ) ? $current_builder[ $_vid ] : null,
														'product_id' => $current_post_id,
													],
												],
											];
										} else {
											$value['html'] = [
												[ $this, 'builder_sub_variations_options' ],
												[
													[
														'meta'       => isset( $builder[ $_vid ] ) ? $builder[ $_vid ] : null,
														'product_id' => $current_post_id,
													],
												],
											];
										}
									} elseif ( ( isset( $value['tmid'] ) && 'populate' === $value['tmid'] ) &&
											( 'multiple' === $this->all_elements[ $_elements[ $k0 ] ]->type
												|| 'multipleall' === $this->all_elements[ $_elements[ $k0 ] ]->type
												|| 'multiplesingle' === $this->all_elements[ $_elements[ $k0 ] ]->type
												|| 'singlemultiple' === $this->all_elements[ $_elements[ $k0 ] ]->type )
									) {

										// holds the default checked values (cannot be cached in $t[$_vid]).
										$_default_value = isset( $builder[ 'multiple_' . $_vid . '_default_value' ] ) ? $builder[ 'multiple_' . $_vid . '_default_value' ] : null;

										if ( is_null( $t[ $_vid ] ) ) {
											$null_array = [
												'base'    => null,
												'current' => null,
											];
											$properties = [];

											foreach ( $this->multiple_properties as $property ) {
												$properties[ $property ] = $null_array;
											}

											foreach ( $properties as $property => $property_value ) {
												$properties[ $property ]['base'] = isset( $builder[ 'multiple_' . $_vid . '_' . $property ] )
												? $builder[ 'multiple_' . $_vid . '_' . $property ]
												: null;

												$properties[ $property ]['current'] = isset( $builder[ 'multiple_' . $_vid . '_' . $property ] )
												? isset( $current_builder[ 'multiple_' . $_vid . '_' . $property ] )
													? $current_builder[ 'multiple_' . $_vid . '_' . $property ]
													: $builder[ 'multiple_' . $_vid . '_' . $property ]
												: null;
												if ( is_array( $properties[ $property ]['base'] ) && is_array( $properties[ $property ]['current'] ) && count( $properties[ $property ]['current'] ) !== count( $properties[ $property ]['base'] ) ) {
													$properties[ $property ]['current'] = $properties[ $property ]['current'] + $properties[ $property ]['base'];
												}
											}

											$_titles_base = $properties['title']['base'];
											$_titles      = $properties['title']['current'];
											$_values_base = $properties['value']['base'];
											$_prices_base = $properties['price']['base'];

											$c_prices_base      = [];
											$c_prices           = [];
											$c_sale_prices_base = [];
											$c_sale_prices      = [];
											if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
												foreach ( $additional_currencies as $ckey => $currency ) {
													$mt_prefix                       = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );
													$c_prices_base[ $currency ]      = isset( $builder[ 'multiple_' . $_vid . '_price' . $mt_prefix ] )
														? $builder[ 'multiple_' . $_vid . '_price' . $mt_prefix ]
														: null;
													$c_prices[ $currency ]           = isset( $builder[ 'multiple_' . $_vid . '_price' . $mt_prefix ] )
														? isset( $current_builder[ 'multiple_' . $_vid . '_price' . $mt_prefix ] )
															? $current_builder[ 'multiple_' . $_vid . '_price' . $mt_prefix ]
															: $builder[ 'multiple_' . $_vid . '_price' . $mt_prefix ]
														: null;
													$c_sale_prices_base[ $currency ] = isset( $builder[ 'multiple_' . $_vid . '_sale_price' . $mt_prefix ] )
														? $builder[ 'multiple_' . $_vid . '_sale_price' . $mt_prefix ]
														: null;
													$c_sale_prices[ $currency ]      = isset( $builder[ 'multiple_' . $_vid . '_sale_price' . $mt_prefix ] )
														? isset( $current_builder[ 'multiple_' . $_vid . '_sale_price' . $mt_prefix ] )
															? $current_builder[ 'multiple_' . $_vid . '_sale_price' . $mt_prefix ]
															: $builder[ 'multiple_' . $_vid . '_sale_price' . $mt_prefix ]
														: null;
												}
											}

											$_extra_options = $this->extra_multiple_options;

											$_extra_base = [];
											$_extra      = [];
											$_extra_keys = [];
											foreach ( $_extra_options as $__key => $__name ) {
												if ( $_vid === $__name['type'] . '_options' ) {
													$_extra_name   = $__name['name'];
													$_extra_base[] = isset( $builder[ 'multiple_' . $_vid . '_' . $_extra_name ] )
														? $builder[ 'multiple_' . $_vid . '_' . $_extra_name ]
														: null;
													$_extra[]      = isset( $builder[ 'multiple_' . $_vid . '_' . $_extra_name ] )
														? isset( $current_builder[ 'multiple_' . $_vid . '_' . $_extra_name ] )
															? $current_builder[ 'multiple_' . $_vid . '_' . $_extra_name ]
															: $builder[ 'multiple_' . $_vid . '_' . $_extra_name ]
														: null;
													$_extra_keys[] = $__key;
												}
											}

											if ( ! is_null( $_titles_base ) && ! is_null( $_values_base ) && ! is_null( $_prices_base ) ) {
												$t[ $_vid ] = [];
												// backwards combatility.

												foreach ( $properties as $property => $property_value ) {
													if ( is_null( $property_value['base'] ) ) {
														$func = 'clear_array_values';
														if ( 'enabled' === $property ) {
															$func = 'return_false_array_values';
														}
														$properties[ $property ]['base'] = array_map(
															[
																$this,
																$func,
															],
															$_titles_base
														);
													}
													if ( is_null( $property_value['current'] ) ) {
														$properties[ $property ]['current'] = $properties[ $property ]['base'];
													}
												}

												foreach ( $c_prices as $ckey => $cvalue ) {
													if ( is_null( $cvalue ) ) {
														$c_prices[ $ckey ] = $c_prices_base[ $ckey ];
													}
												}

												foreach ( $c_sale_prices as $ckey => $cvalue ) {
													if ( is_null( $cvalue ) ) {
														$c_sale_prices[ $ckey ] = $c_sale_prices_base[ $ckey ];
													}
												}

												foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
													if ( is_null( $_extra_base[ $_extra_base_key ] ) ) {
														$_extra_base[ $_extra_base_key ] = array_map(
															[
																$this,
																'clear_array_values',
															],
															$_titles_base
														);
													}
												}
												foreach ( $_extra as $_extra_key => $_extra_value ) {
													if ( is_null( $_extra_base[ $_extra_key ] ) ) {
														$_extra_base[ $_extra_key ] = array_map(
															[
																$this,
																'clear_array_values',
															],
															$_titles_base
														);
													}
												}

												foreach ( $_titles_base as $option_key => $option_value ) {

													$use_original_builder = false;
													$_option_key          = $option_key;
													if ( isset( $current_builder[ $_elements[ $k0 ] . '_uniqid' ] )
														&& isset( $builder[ $_elements[ $k0 ] . '_uniqid' ] )
														&& isset( $builder[ $_elements[ $k0 ] . '_uniqid' ][ $option_key ] )
													) {
														// get index of element id in internal array.
														$get_current_builder_uniqid_index = array_search( $builder[ $_elements[ $k0 ] . '_uniqid' ][ $option_key ], $current_builder[ $_elements[ $k0 ] . '_uniqid' ], true );
														if ( null !== $get_current_builder_uniqid_index && false !== $get_current_builder_uniqid_index ) {
															$_option_key = $get_current_builder_uniqid_index;
														} else {
															$use_original_builder = true;
														}
													}

													if ( ! isset( $_titles_base[ $_option_key ] ) ) {
														continue;
													}

													foreach ( $properties as $property => $property_value ) {
														$func = 'clear_array_values';
														if ( 'enabled' === $property ) {
															$func = 'return_false_array_values';
														}
														if ( ! isset( $property_value['base'][ $_option_key ] ) ) {
															$properties[ $property ]['base'] = array_map(
																[
																	$this,
																	$func,
																],
																$_titles_base[ $_option_key ]
															);
														}
														if ( ! isset( $property_value['current'][ $_option_key ] ) ) {
															$properties[ $property ]['current'] = array_map(
																[
																	$this,
																	$func,
																],
																$_titles_base[ $_option_key ]
															);
														}
													}

													foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
														if ( ! isset( $_extra_base[ $_extra_base_key ][ $_option_key ] ) ) {
															$_extra_base[ $_extra_base_key ][ $_option_key ] = array_map(
																[
																	$this,
																	'clear_array_values',
																],
																$_titles_base[ $_option_key ]
															);
														}
													}
													foreach ( $_extra as $_extra_key => $_extra_value ) {
														if ( ! isset( $_extra[ $_extra_key ][ $_option_key ] ) ) {
															$_extra[ $_extra_key ][ $_option_key ] = array_map(
																[
																	$this,
																	'clear_array_values',
																],
																$_titles_base[ $_option_key ]
															);
														}
													}

													// backwards compatibility.
													foreach ( $properties['enabled']['base'] as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( '0' === $__value ) {
																$properties['enabled']['base'][ $_extra_key ][ $__key ] = '';
															}
														}
													}
													foreach ( $properties['enabled']['current'] as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( '0' === $__value ) {
																$properties['enabled']['current'][ $_extra_key ][ $__key ] = '';
															}
														}
													}
													foreach ( $properties['price_type']['base'] as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( 'fee' === $__value ) {
																if ( 'checkboxes' === $_elements[ $k0 ] ) {
																	$_fee_base[ $_extra_key ][ $__key ] = '1';
																}

																$properties['price_type']['base'][ $_extra_key ][ $__key ] = '';
															}
														}
													}
													foreach ( $properties['price_type']['current'] as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( 'fee' === $__value ) {
																if ( 'checkboxes' === $_elements[ $k0 ] ) {
																	$_fee[ $_extra_key ][ $__key ] = '1';
																}

																$properties['price_type']['current'][ $_extra_key ][ $__key ] = '';
															}
														}
													}

													if ( $use_original_builder ) {
														$obvalues = [];
														foreach ( $properties as $property => $property_value ) {
															$obvalues[ $property ] = $property_value['base'][ $_option_key ];
														}
														$obvalues = apply_filters( 'wc_epo_obvalues', $obvalues, $builder, $value, $current_builder, $_titles_base, $_option_key );
														foreach ( $c_prices_base as $ckey => $cvalue ) {
															$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															if ( isset( $cvalue[ $_option_key ] ) ) {
																$obvalues[ 'price' . $mt_prefix ] = $cvalue[ $_option_key ];
															}
														}
														foreach ( $c_sale_prices_base as $ckey => $cvalue ) {
															$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															if ( isset( $cvalue[ $_option_key ] ) ) {
																$obvalues[ 'sale_price' . $mt_prefix ] = $cvalue[ $_option_key ];
															}
														}
														foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
															$obvalues[ $_extra_options[ $_extra_keys[ $_extra_base_key ] ]['name'] ] = $_extra_base_value[ $_option_key ];
														}
														$t[ $_vid ][] = $obvalues;
													} else {
														$cbvalues = [];
														foreach ( $properties as $property => $property_value ) {
															$cbvalues[ $property ] = THEMECOMPLETE_EPO_HELPER()->build_array( $property_value['current'][ $_option_key ], $property_value['base'][ $_option_key ] );
														}
														$cbvalues = apply_filters( 'wc_epo_cbvalues', $cbvalues, $builder, $value, $current_builder, $_titles_base, $_option_key, $option_key );
														foreach ( $c_prices as $ckey => $cvalue ) {
															$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															if ( isset( $cvalue[ $_option_key ] ) ) {
																$cbvalues[ 'price' . $mt_prefix ] = THEMECOMPLETE_EPO_HELPER()->build_array(
																	$cvalue[ $_option_key ],
																	$c_prices_base[ $ckey ][ $option_key ]
																);
															}
														}
														foreach ( $c_sale_prices as $ckey => $cvalue ) {
															$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															if ( isset( $cvalue[ $_option_key ] ) ) {
																$cbvalues[ 'sale_price' . $mt_prefix ] = THEMECOMPLETE_EPO_HELPER()->build_array(
																	$cvalue[ $_option_key ],
																	$c_sale_prices_base[ $ckey ][ $option_key ]
																);
															}
														}
														foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
															$cbvalues[ $_extra_options[ $_extra_keys[ $_extra_base_key ] ]['name'] ] = $_extra_base_value[ $_option_key ];
														}
														$t[ $_vid ][] = $cbvalues;
													}
												}
											}
										}
										if ( ! is_null( $t[ $_vid ] ) && isset( $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {

											$value['html'] = [
												[ $this, 'builder_sub_options' ],
												[
													[
														'options'       => $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ],
														'name'          => 'multiple_' . $_vid,
														'counter'       => $_counter[ $_elements[ $k0 ] ],
														'default_value' => $_default_value,
													],
												],
											];

										}
									}
								}
								// we assume that $value['tags']['name'] exists if tag id is set.
								if ( isset( $value['tags']['id'] ) ) {
									$_name = str_replace( [ '[', ']' ], '', $value['tags']['name'] );
									if ( ! isset( $id_counter[ $_name ] ) ) {
										$id_counter[ $_name ] = 0;
									} else {
										$id_counter[ $_name ] = $id_counter[ $_name ] + 1;
									}
									$value['tags']['id'] = $_name . $id_counter[ $_name ];
								}

								$fields[] = $value;

							}

							$elements_html_array[ $k0 ] = [
								'element'       => $_elements[ $k0 ],
								'width'         => $_div_size[ $k0 ],
								'width_display' => $this->sizer[ $_div_size[ $k0 ] ],
								'internal_name' => $internal_name,
								'fields'        => $fields,
								'label'         => $this->all_elements[ $_elements[ $k0 ] ]->name,
								'desc'          => $element_desc,
								'icon'          => $this->all_elements[ $_elements[ $k0 ] ]->icon,
								'is_enabled'    => isset( $builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] ) ? $builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] : '1',
							];
						}
					}
				}

				$this->section_template(
					[
						'section_fields'           => $section_fields,
						'size'                     => $this->sizer[ $_sections_size[ $_s ] ],
						'section_size'             => $_sections_size[ $_s ],
						'sections_slides'          => isset( $_sections_slides[ $_s ] ) ? $_sections_slides[ $_s ] : '',
						'sections_tabs_labels'     => isset( $_sections_tabs_labels[ $_s ] ) ? $_sections_tabs_labels[ $_s ] : '',
						'sections_type'            => $sections_type[ $_s ],
						'elements'                 => $elements_html_array,
						'wpml_is_original_product' => $wpml_is_original_product,
						'sections_internal_name'   => $_sections_internal_name,
					]
				);

				$_helper_counter = (int) ( $_helper_counter + (int) $_sections[ $_s ] );
			}
		}

	}

	/**
	 * Helper to generate html for Image replacement
	 *
	 * @see    builder_sub_variations_options
	 * @since  4.8.5
	 * @param  string $value The image source.
	 * @access public
	 */
	public function settings_term_variations_image_helper( $value = '' ) {

		echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the radio button.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span><span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Remove the image.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tm-upload-button-remove cp-button tm-tooltip"><i class="tcfa tcfa-times"></i></span>';
		echo '<span class="tm_upload_image tm_upload_imagep"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $value ) . '" /></span>';

	}

	/**
	 * Helper to generate html for Product Image replacement
	 *
	 * @see    builder_sub_variations_options
	 * @since  4.8.5
	 * @param  string $value The image source.
	 * @access public
	 */
	public function settings_term_variations_imagep_helper( $value = '' ) {

		echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span><span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Remove the image.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tm-upload-button-remove cp-button tm-tooltip"><i class="tcfa tcfa-times"></i></span>';
		echo '<span class="tm_upload_image"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $value ) . '" /></span>';

	}

	/**
	 * Generates element sub-options for variations.
	 *
	 * @since  3.0.0
	 * @param  array $args Array of arguments.
	 * @access public
	 */
	public function builder_sub_variations_options( $args = [] ) {

		$args = shortcode_atts(
			[
				'meta'       => [],
				'product_id' => 0,
				'return_js'  => false,
			],
			$args
		);

		$meta       = $args['meta'];
		$product_id = $args['product_id'];
		$return_js  = $args['return_js'];

		$js_object = [];

		$o     = [];
		$name  = 'tm_builder_variation_options';
		$class = ' withupload';

		$settings_attribute = [
			[
				'id'      => 'variations_display_as',
				'default' => 'select',
				'type'    => 'select',
				'tags'    => [
					'class' => 'variations-display-as',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'options' => [
					[
						'text'  => esc_html__( 'Select boxes', 'woocommerce-tm-extra-product-options' ),
						'value' => 'select',
					],
					[
						'text'  => esc_html__( 'Radio buttons', 'woocommerce-tm-extra-product-options' ),
						'value' => 'radio',
					],
					[
						'text'  => esc_html__( 'Radio buttons and image at start of the label', 'woocommerce-tm-extra-product-options' ),
						'value' => 'radiostart',
					],
					[
						'text'  => esc_html__( 'Radio buttons and image at end of the label', 'woocommerce-tm-extra-product-options' ),
						'value' => 'radioend',
					],
					[
						'text'  => esc_html__( 'Image swatches', 'woocommerce-tm-extra-product-options' ),
						'value' => 'image',
					],
					[
						'text'  => esc_html__( 'Color swatches', 'woocommerce-tm-extra-product-options' ),
						'value' => 'color',
					],
					[
						'text'  => esc_html__( 'Text swatches', 'woocommerce-tm-extra-product-options' ),
						'value' => 'text',
					],
				],
				'label'   => esc_html__( 'Display as', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the display type of this attribute.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'      => 'variations_label',
				'default' => '',
				'type'    => 'text',
				'tags'    => [
					'class' => 't',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
					'value' => '',
				],
				'label'   => esc_html__( 'Attribute Label', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Leave blank to use the original attribute label.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'               => 'variations_show_reset_button',
				'message0x0_class' => 'tma-hide-for-select-box',
				'default'          => '',
				'type'             => 'select',
				'tags'             => [
					'id'   => 'builder_%id%',
					'name' => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'options'          => [
					[
						'text'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
						'value' => '',
					],
					[
						'text'  => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
						'value' => 'yes',
					],
				],
				'label'            => esc_html__( 'Show reset button', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Enables the display of a reset button for this attribute.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'      => 'variations_class',
				'default' => '',
				'type'    => 'text',
				'tags'    => [
					'class' => 't',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
					'value' => '',
				],
				'label'   => esc_html__( 'Attribute element class name', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter an extra class name to add to this attribute element', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'               => 'variations_items_per_row',
				'message0x0_class' => 'tma-hide-for-select-box',
				'default'          => '',
				'type'             => 'number',
				'extra'            => [ [ $this, 'add_setting_items_per_row_helper' ], [] ],
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
					'value' => '',
				],
				'label'            => esc_html__( 'Items per row (Desktops and laptops)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],

			// @media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {
			[
				'id'               => 'variations_items_per_row_tablets',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (Tablets landscape)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (min-device-width : 481px) and (max-device-width : 767px) {
			[
				'id'               => 'variations_items_per_row_tablets_small',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (Tablets portrait)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (min-device-width : 320px) and (max-device-width : 480px) {
			[
				'id'               => 'variations_items_per_row_smartphones',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (Smartphones)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2) {
			[
				'id'               => 'variations_items_per_row_iphone5',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (iPhone 5)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2) {
			[
				'id'               => 'variations_items_per_row_iphone6',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (iPhone 6)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2) {
			[
				'id'               => 'variations_items_per_row_iphone6_plus',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (iPhone 6 +)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2) {
			[
				'id'               => 'variations_items_per_row_samsung_galaxy',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (Samnsung Galaxy)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			// @media only screen and (min-device-width : 800px) and (max-device-width : 1280px) {
			[
				'id'               => 'variations_items_per_row_tablets_galaxy',
				'message0x0_class' => 'builder_responsive_div tc-hidden',
				'wpmldisable'      => 1,
				'default'          => '',
				'type'             => 'number',
				'tags'             => [
					'class' => 'n',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'label'            => esc_html__( 'Items per row (Galaxy Tablets landscape)', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'               => 'variations_item_width',
				'message0x0_class' => 'tma-show-for-swatches tma-hide-for-select-box',
				'default'          => '',
				'type'             => 'text',
				'tags'             => [
					'class' => 't',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
					'value' => '',
				],
				'label'            => esc_html__( 'Width', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Enter the width of the displayed item or leave blank for auto width.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'               => 'variations_item_height',
				'message0x0_class' => 'tma-show-for-swatches tma-hide-for-select-box',
				'default'          => '',
				'type'             => 'text',
				'tags'             => [
					'class' => 't',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
					'value' => '',
				],
				'label'            => esc_html__( 'Height', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Enter the height of the displayed item or leave blank for auto height.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'      => 'variations_changes_product_image',
				'default' => '',
				'type'    => 'select',
				'tags'    => [
					'class' => 'tm-changes-product-image',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'options' => [
					[
						'text'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
						'value' => '',
					],
					[
						'text'  => esc_html__( 'Use the image replacements', 'woocommerce-tm-extra-product-options' ),
						'value' => 'images',
					],
					[
						'text'  => esc_html__( 'Use custom image', 'woocommerce-tm-extra-product-options' ),
						'value' => 'custom',
					],
				],
				'label'   => esc_html__( 'Changes product image', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose whether to change the product image.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'               => 'variations_show_name',
				'message0x0_class' => 'tma-show-for-swatches',
				'default'          => 'hide',
				'type'             => 'select',
				'tags'             => [
					'class' => 'variations-show-name',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]',
				],
				'options'          => [
					[
						'text'  => esc_html__( 'Hide', 'woocommerce-tm-extra-product-options' ),
						'value' => 'hide',
					],
					[
						'text'  => esc_html__( 'Show bottom', 'woocommerce-tm-extra-product-options' ),
						'value' => 'bottom',
					],
					[
						'text'  => esc_html__( 'Show inside', 'woocommerce-tm-extra-product-options' ),
						'value' => 'inside',
					],
					[
						'text'  => esc_html__( 'Tooltip', 'woocommerce-tm-extra-product-options' ),
						'value' => 'tooltip',
					],
				],
				'label'            => esc_html__( 'Show attribute name', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Choose whether to show or hide the attribute name.', 'woocommerce-tm-extra-product-options' ),
			],
		];

		$settings_term = [
			[
				'id'               => 'variations_color',
				'message0x0_class' => 'tma-term-color',
				'default'          => '',
				'type'             => 'text',
				'tags'             => [
					'class' => 'tm-color-picker',
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][[%id%]][%term_id%]',
					'value' => '',
				],
				'label'            => esc_html__( 'Color', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Select the color to use.', 'woocommerce-tm-extra-product-options' ),
			],
			[
				'id'               => 'variations_image',
				'message0x0_class' => 'tma-term-image',
				'default'          => '',
				'type'             => 'hidden',
				'tags'             => [
					'class' => 'n tm_option_image' . $class,
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][[%id%]][%term_id%]',
				],
				'label'            => esc_html__( 'Image replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Select an image for this term.', 'woocommerce-tm-extra-product-options' ),
				'extra'            => [ [ $this, 'settings_term_variations_image_helper' ], [] ],
				'method'           => 'settings_term_variations_image_helper',
			],
			[
				'id'               => 'variations_imagep',
				'message0x0_class' => 'tma-term-custom-image',
				'default'          => '',
				'type'             => 'hidden',
				'tags'             => [
					'class' => 'n tm_option_image tm_option_imagep' . $class,
					'id'    => 'builder_%id%',
					'name'  => 'tm_meta[tmfbuilder][variations_options][%attribute_id%][[%id%]][%term_id%]',
				],
				'label'            => esc_html__( 'Product Image replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'             => esc_html__( 'Select the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ),
				'extra'            => [ [ $this, 'settings_term_variations_imagep_helper' ], [] ],
				'method'           => 'settings_term_variations_imagep_helper',
			],

		];

		$attributes = [];

		if ( ! empty( $product_id ) ) {
			$product = wc_get_product( $product_id );

			if ( $product && is_object( $product ) && is_callable( [ $product, 'get_variation_attributes' ] ) ) {
				$attributes     = $product->get_variation_attributes();
				$all_attributes = $product->get_attributes();
				if ( $attributes ) {
					foreach ( $attributes as $key => $value ) {
						if ( ! $value ) {
							$attributes[ $key ] = array_map( 'trim', explode( '|', $all_attributes[ $key ]['value'] ) );
						}
					}
				}
			}
		}

		if ( empty( $return_js ) ) {
			if ( empty( $attributes ) ) {
				echo '<div class="errortitle"><p><i class="tcfa tcfa-exclamation-triangle"></i> ' . esc_html__( 'No saved variations found.', 'woocommerce-tm-extra-product-options' ) . '</p></div>';
			}
		}
		$d_counter = 0;
		foreach ( $attributes as $name => $options ) {
			$js_object[ $d_counter ] = [];
			if ( empty( $return_js ) ) {
				echo '<div class="tma-handle-wrap tm-attribute">'
					. '<div class="tma-handle"><div class="tma-attribute_label">'
					. esc_html( wc_attribute_label( $name ) )
					. '</div><div class="tmicon tcfa fold tcfa-caret-up"></div></div>'
					. '<div class="tma-handle-wrapper tm-hidden">'
					. '<div class="tma-attribute w100">';
			}

			$attribute_id = sanitize_title( $name );
			foreach ( $settings_attribute as $setting ) {
				$setting['tags']['id']   = str_replace( '%id%', $setting['id'], $setting['tags']['id'] );
				$setting['tags']['name'] = str_replace( '%id%', $setting['id'], $setting['tags']['name'] );
				$setting['tags']['name'] = str_replace( '%attribute_id%', $attribute_id, $setting['tags']['name'] );
				if ( ! empty( $meta ) && isset( $meta[ $attribute_id ] ) && isset( $meta[ $attribute_id ][ $setting['id'] ] ) ) {
					$setting['default'] = $meta[ $attribute_id ][ $setting['id'] ];
				}
				if ( empty( $return_js ) ) {
					THEMECOMPLETE_EPO_HTML()->create_field( $setting, 1 );
				} else {
					$js_object[ $d_counter ][] = $this->remove_for_js( $setting );
				}
			}

			if ( is_array( $options ) ) {
				$taxonomy_name = rawurldecode( sanitize_title( $name ) );
				if ( taxonomy_exists( $taxonomy_name ) ) {

					if ( function_exists( 'wc_get_product_terms' ) ) {
						$terms = wc_get_product_terms( $product_id, $name, [ 'fields' => 'all' ] );
					} else {

						$orderby = wc_attribute_orderby( $taxonomy_name );
						$args    = [];
						switch ( $orderby ) {
							case 'name':
								$args = [
									'orderby'    => 'name',
									'hide_empty' => false,
									'menu_order' => false,
								];
								break;
							case 'id':
								$args = [
									'orderby'    => 'id',
									'order'      => 'ASC',
									'menu_order' => false,
									'hide_empty' => false,
								];
								break;
							case 'menu_order':
								$args = [
									'menu_order' => 'ASC',
									'hide_empty' => false,
								];
								break;
						}
						$terms = get_terms( $taxonomy_name, $args );
					}
					if ( ! empty( $terms ) ) {

						foreach ( $terms as $term ) {
							// Get only selected terms.
							$has_term = has_term( (int) $term->term_id, $taxonomy_name, $product_id );
							if ( ! $has_term ) {
								continue;
							}
							$term_name = THEMECOMPLETE_EPO_HELPER()->entity_decode( $term->name );
							$term_id   = THEMECOMPLETE_EPO_HELPER()->sanitize_key( $term->slug );

							if ( empty( $return_js ) ) {
								echo '<div class="tma-handle-wrap tm-term">'
									. '<div class="tma-handle"><div class="tma-attribute_label">'
									. esc_html( apply_filters( 'woocommerce_variation_option_name', $term_name ) )
									. '</div><div class="tmicon tcfa fold tcfa-caret-up"></div></div>'
									. '<div class="tma-handle-wrapper tm-hidden">'
									. '<div class="tma-attribute w100">';
							}

							foreach ( $settings_term as $setting ) {
								$setting['tags']['id']   = str_replace( '%id%', $setting['id'], $setting['tags']['id'] );
								$setting['tags']['name'] = str_replace( '%id%', $setting['id'], $setting['tags']['name'] );
								$setting['tags']['name'] = str_replace( '%attribute_id%', sanitize_title( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $name ) ), $setting['tags']['name'] );
								$setting['tags']['name'] = str_replace( '%term_id%', esc_attr( $term_id ), $setting['tags']['name'] );

								if ( ! empty( $meta )
									&& isset( $meta[ $attribute_id ] )
									&& isset( $meta[ $attribute_id ][ $setting['id'] ] )
									&& isset( $meta[ $attribute_id ][ $setting['id'] ][ $term_id ] )
								) {
									$setting['default'] = $meta[ $attribute_id ][ $setting['id'] ][ $term_id ];
									if ( isset( $setting['extra'] ) && isset( $setting['method'] ) ) {
										$setting['extra'] = [
											[ $this, $setting['method'] ],
											[ $meta[ $attribute_id ][ $setting['id'] ][ $term_id ] ],
										];
									}
								}
								if ( empty( $return_js ) ) {
									THEMECOMPLETE_EPO_HTML()->create_field( $setting, 1 );
								} else {
									$js_object[ $d_counter ][] = $this->remove_for_js( $setting );
								}
							}
							if ( empty( $return_js ) ) {
								echo '</div></div></div>';
							}
						}
					}
				} else {

					foreach ( $options as $option ) {
						$optiont = rawurldecode( THEMECOMPLETE_EPO_HELPER()->entity_decode( $option ) );
						$option  = THEMECOMPLETE_EPO_HELPER()->entity_decode( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $option ) );
						if ( empty( $return_js ) ) {
							echo '<div class="tma-handle-wrap tm-term">'
								. '<div class="tma-handle"><div class="tma-attribute_label">'
								. esc_html( apply_filters( 'woocommerce_variation_option_name', $optiont ) )
								. '</div><div class="tmicon tcfa fold tcfa-caret-up"></div></div>'
								. '<div class="tma-handle-wrapper tm-hidden">'
								. '<div class="tma-attribute w100">';
						}

						foreach ( $settings_term as $setting ) {
							$setting['tags']['id']   = str_replace( '%id%', $setting['id'], $setting['tags']['id'] );
							$setting['tags']['name'] = str_replace( '%id%', $setting['id'], $setting['tags']['name'] );
							$setting['tags']['name'] = str_replace( '%attribute_id%', sanitize_title( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $name ) ), $setting['tags']['name'] );
							$setting['tags']['name'] = str_replace( '%term_id%', esc_attr( $option ), $setting['tags']['name'] );

							if ( ! empty( $meta )
								&& isset( $meta[ $attribute_id ] )
								&& isset( $meta[ $attribute_id ][ $setting['id'] ] )
								&& isset( $meta[ $attribute_id ][ $setting['id'] ][ $option ] )
							) {
								$setting['default'] = $meta[ $attribute_id ][ $setting['id'] ][ $option ];
								if ( isset( $setting['extra'] ) && isset( $setting['method'] ) ) {
									$setting['extra'] = [
										[ $this, $setting['method'] ],
										[ $meta[ $attribute_id ][ $setting['id'] ][ $option ] ],
									];

								}
							}
							if ( empty( $return_js ) ) {
								THEMECOMPLETE_EPO_HTML()->create_field( $setting, 1 );
							} else {
								$js_object[ $d_counter ][] = $this->remove_for_js( $setting );
							}
						}
						if ( empty( $return_js ) ) {
							echo '</div></div></div>';
						}
					}
				}
			}
			if ( empty( $return_js ) ) {
				echo '</div></div></div>';
			}
			$d_counter ++;
		}

		if ( ! empty( $return_js ) ) {
			return $js_object;
		}

	}

	/**
	 * Helper to print the upload button for Image replacement
	 *
	 * @param string $name Element name.
	 * @see    builder_sub_options_image_helper
	 * @since  4.8.5
	 * @access public
	 */
	public function get_builder_sub_options_upload_helper( $name = '' ) {

		if ( 'multiple_radiobuttons_options' === $name || 'multiple_checkboxes_options' === $name ) {
			if ( 'multiple_radiobuttons_options' === $name ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the radio button.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			} elseif ( 'multiple_checkboxes_options' === $name ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the checkbox.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			}
		}

	}

	/**
	 * Helper to generate html for Image replacement
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @param string       $name Element name.
	 * @param string|false $image Image to output.
	 * @access public
	 */
	public function builder_sub_options_image_helper( $name = '', $image = false ) {

		$this->get_builder_sub_options_upload_helper( $name );

		if ( false !== $image ) {
			if ( '' === $image ) {
				$image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
			}
			echo '<span class="s tm_upload_image"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" /><button rel="tc-option-image" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button></span>';
		}

	}

	/**
	 * Helper to print the upload button for checked Image replacement
	 *
	 * @see    builder_sub_options_imagec_helper
	 * @since  4.8.5
	 * @param string $name Element name.
	 * @access public
	 */
	public function get_builder_sub_options_uploadc_helper( $name = '' ) {

		if ( 'multiple_radiobuttons_options' === $name || 'multiple_checkboxes_options' === $name ) {
			if ( 'multiple_radiobuttons_options' === $name ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the radio button when it is checked.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			} elseif ( 'multiple_checkboxes_options' === $name ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the checkbox when it is checked.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			}
		}

	}

	/**
	 * Helper to generate html for checked Image replacement
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @param string       $name Element name.
	 * @param string|false $image Image to output.
	 * @access public
	 */
	public function builder_sub_options_imagec_helper( $name = '', $image = false ) {

		$this->get_builder_sub_options_uploadc_helper( $name );

		if ( false !== $image ) {
			if ( '' === $image ) {
				$image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
			}
			echo '<span class="tm_upload_image tm_upload_imagec"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" /><button rel="tc-option-imagec" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button></span>';
		}

	}

	/**
	 * Helper to print the upload button for Product Image replacement
	 *
	 * @see    builder_sub_options_imagep_helper
	 * @since  4.8.5
	 * @param string $name Element name.
	 * @access public
	 */
	public function get_builder_sub_options_uploadp_helper( $name = '' ) {

		if ( 'multiple_radiobuttons_options' === $name || 'multiple_checkboxes_options' === $name ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		} elseif ( 'multiple_selectbox_options' === $name ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		}

	}

	/**
	 * Helper to generate html for Product Image replacement
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @param string       $name Element name.
	 * @param string|false $image Image to output.
	 * @access public
	 */
	public function builder_sub_options_imagep_helper( $name = '', $image = false ) {

		$this->get_builder_sub_options_uploadp_helper( $name );

		if ( false !== $image ) {
			if ( '' === $image ) {
				$image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
			}
			echo '<span class="tm_upload_image tm_upload_imagep"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" /><button rel="tc-option-imagep" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button></span>';
		}

	}

	/**
	 * Helper to print the upload button for Lightbox Image
	 *
	 * @see    builder_sub_options_imagel_helper
	 * @since  4.8.5
	 * @param string $name Element name.
	 * @access public
	 */
	public function get_builder_sub_options_uploadl_helper( $name = '' ) {

		if ( 'multiple_radiobuttons_options' === $name || 'multiple_checkboxes_options' === $name ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image for the lightbox.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		} elseif ( 'multiple_selectbox_options' === $name ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image for the lightbox.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		}

	}

	/**
	 * Helper to generate html for Lightbox Image
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @param string       $name Element name.
	 * @param string|false $image Image to output.
	 * @access public
	 */
	public function builder_sub_options_imagel_helper( $name = '', $image = false ) {

		$this->get_builder_sub_options_uploadl_helper( $name );

		if ( false !== $image ) {
			if ( '' === $image ) {
				$image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
			}
			echo '<span class="tm_upload_image tm_upload_imagel"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" /><button rel="tc-option-imagel" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button></span>';
		}

	}

	/**
	 * Remove problematic array keys for js output
	 *
	 * @since  4.9.12
	 * @param array $array Array of arguments.
	 * @access public
	 */
	public function remove_for_js( $array = [] ) {

		unset( $array['extra'] );

		if ( isset( $array['type'] ) && 'checkbox' === $array['type'] ) {
			$array['checked'] = '' !== checked( ( $array['default'] === $array['tags']['value'] ), true, false );
		}

		return $array;

	}

	/**
	 * Generates element sub-options for selectbox, checkbox and radio buttons.
	 *
	 * @since  1.0.0
	 * @param array $args Array of arguments.
	 * @access public
	 */
	public function builder_sub_options( $args = [] ) {

		$args = shortcode_atts(
			[
				'options'       => [],
				'name'          => 'multiple_selectbox_options',
				'counter'       => null,
				'default_value' => null,
				'return_js'     => false,
			],
			$args
		);

		$options       = $args['options'];
		$name          = $args['name'];
		$counter       = $args['counter'];
		$default_value = $args['default_value'];
		$return_js     = $args['return_js'];

		$js_object = [];

		$o                     = [];
		$upload                = '';
		$uploadc               = '';
		$uploadp               = '';
		$uploadl               = '';
		$class                 = '';
		$_extra_options        = $this->extra_multiple_options;
		$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();

		$price_type_options = [
			[
				'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'value' => '',
			],
			[
				'text'  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percent',
			],
			[
				'text'  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'percentcurrenttotal',
			],
			[
				'text'  => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ),
				'value' => 'math',
			],
			[
				'text'  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				'value' => 'fixedcurrenttotal',
			],
		];

		if ( 'multiple_selectboxmultiple_options' === $name ) {
			unset( $price_type_options[2] );
			unset( $price_type_options[4] );
			sort( $price_type_options );
		}

		if ( ! $options ) {
			$options = [];
			foreach ( $this->multiple_properties as $property ) {
				$property_value = [];
				if ( 'title' === $property ) {
					$property_value = [ false ];
				}
				$options[ $property ] = $property_value;
			}
			foreach ( $_extra_options as $__key => $__name ) {
				if ( 'multiple_' . $__name['type'] . '_options' === $name ) {
					$options[ $__name['name'] ] = [ '' ];
				}
			}
		}

		if ( 'multiple_radiobuttons_options' === $name || 'multiple_checkboxes_options' === $name ) {
			if ( 'multiple_radiobuttons_options' === $name ) {
				$upload  = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the radio button.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
				$uploadc = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the radio button when it is checked.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			} elseif ( 'multiple_checkboxes_options' === $name ) {
				$upload  = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the checkbox.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
				$uploadc = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to use in place of the checkbox when it is checked.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			}
			$uploadp = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$uploadl = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image for the lightbox.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$class   = ' withupload';
		}
		if ( 'multiple_selectbox_options' === $name ) {
			$uploadp = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$uploadl = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( 'Choose the image for the lightbox.', 'woocommerce-tm-extra-product-options' ) ) . '" class="tc-upload-button tc-upload-buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$class   = ' withupload';
		}

		foreach ( $options as $property => $property_value ) {
			$o[ $property ] = [
				'id'      => $name . '_' . $property,
				'default' => '',
				'nodiv'   => 1,
				'tags'    => [
					'id'   => $name . '_' . $property,
					'name' => $name . '_' . $property,
				],
			];
		}

		$o['title']['type'] = 'text';
		$o['title']['tags'] = [
			'class' => 't tm_option_title',
			'value' => '',
		];

		$o['value']['type'] = 'text';
		$o['value']['tags'] = [
			'class' => 't tm_option_value',
			'value' => '',
		];

		$o['price']['type'] = 'text';
		$o['price']['tags'] = [
			'class' => 't tm_option_price',
			'value' => '',
		];

		$o['sale_price']['type'] = 'text';
		$o['sale_price']['tags'] = [
			'class' => 't tm_option_sale_price',
			'value' => '',
		];

		$o['image']['type']   = 'hidden';
		$o['image']['tags']   = [
			'class' => 't tm_option_image tc-option-image' . $class,
			'value' => '',
		];
		$o['image']['extra']  = [ [ $this, 'builder_sub_options_image_helper' ], [ $name ] ];
		$o['image']['method'] = 'builder_sub_options_image_helper';

		$o['imagec']['type'] = 'hidden';
		$o['imagec']['tags'] = [
			'class' => 't tm_option_image tm_option_imagec tc-option-imagec' . $class,
			'value' => '',
		];

		$o['imagep']['type'] = 'hidden';
		$o['imagep']['tags'] = [
			'class' => 't tm_option_image tm_option_imagep tc-option-imagep' . $class,
			'value' => '',
		];

		$o['imagel']['type'] = 'hidden';
		$o['imagel']['tags'] = [
			'class' => 't tm_option_image tm_option_imagel tc-option-imagel' . $class,
			'value' => '',
		];

		$o['price_type']['type']    = 'select';
		$o['price_type']['options'] = $price_type_options;
		$o['price_type']['tags']    = [
			'class' => 't tm_option_price_type ' . $name,
		];

		$o['url']['type'] = 'text';
		$o['url']['tags'] = [
			'class' => 't tm_option_url',
			'value' => '',
		];

		$o['description']['type'] = 'text';
		$o['description']['tags'] = [
			'class' => 't tm_option_description',
			'value' => '',
		];

		$o['enabled']['type']    = 'checkbox';
		$o['enabled']['default'] = '1';
		$o['enabled']['tags']    = [
			'class' => 'c tm_option_enabled ' . $name,
			'value' => '1',
		];

		$o['color']['type'] = 'text';
		$o['color']['tags'] = [
			'class' => 'tm-color-picker',
			'value' => '',
		];

		$o['fee']['type'] = 'checkbox';
		$o['fee']['tags'] = [
			'class' => 'c',
			'value' => '1',
		];

		foreach ( $_extra_options as $__key => $__name ) {
			$_extra_name = $__name['name'];
			if ( 'multiple_' . $__name['type'] . '_options' === $name ) {
				$o[ $_extra_name ]          = $__name['field'];
				$o[ $_extra_name ]['id']    = $name . '_' . $_extra_name;
				$o[ $_extra_name ]['nodiv'] = 1;
				$o[ $_extra_name ]['tags']  = array_merge(
					$__name['field']['tags'],
					[
						'id'   => $name . '_' . $_extra_name,
						'name' => $name . '_' . $_extra_name,
					]
				);
			}
		}

		$o          = apply_filters( 'wc_epo_builder_after_multiple_element_array', $o, $name );
		$original_o = $o;

		if ( empty( $return_js ) ) {
			echo "<div class='tc-row nopadding multiple_options tc-clearfix'>"
				. "<div class='tc-cell tc-col-auto tm_cell_move'>";

			THEMECOMPLETE_EPO_HTML()->create_button(
				[
					'text' => '',
					'tags' => [
						'href'  => '#move',
						'class' => 'tmicon tcfa tcfa-grip-vertical tm-hidden-inline',
					],
				],
				1
			);
			THEMECOMPLETE_EPO_HTML()->create_button(
				[
					'text' => '',
					'icon' => 'angle-up',
					'tags' => [
						'href'  => '#move',
						'class' => 'tc tc-button small tm-hidden-inline',
					],
				],
				1
			);
			THEMECOMPLETE_EPO_HTML()->create_button(
				[
					'text' => '',
					'icon' => 'angle-down',
					'tags' => [
						'href'  => '#move',
						'class' => 'tc tc-button small tm-hidden-inline',
					],
				],
				1
			);

			echo '</div>'
				. "<div class='tc-cell tc-col-auto tm_cell_default'>" . ( ( 'multiple_checkboxes_options' === $name || 'multiple_selectboxmultiple_options' === $name ) ? esc_html__( 'Checked', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Default', 'woocommerce-tm-extra-product-options' ) ) . '</div>'
				. "<div class='tc-cell tc-col-3 tm_cell_title'>" . esc_html__( 'Label', 'woocommerce-tm-extra-product-options' ) . '</div>'
				. "<div class='tc-cell tc-col-3 tm_cell_images'>" . esc_html__( 'Images', 'woocommerce-tm-extra-product-options' ) . '</div>'
				. "<div class='tc-cell tc-col-0 tc-cell-value'>" . esc_html__( 'Value', 'woocommerce-tm-extra-product-options' ) . '</div>'
				. "<div class='tc-cell tc-col-auto tm_cell_price'>" . esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ) . '</div>'
				. "<div class='tc-cell tc-col-auto tm_cell_delete'><button type='button' class='tc tc-button builder_panel_delete_all'>" . esc_html__( 'Delete all options', 'woocommerce-tm-extra-product-options' ) . '</button></div>'
				. '</div>';
		}
		$total_entries = count( $options['title'] );
		$per_page      = apply_filters( 'tm_choices_shown', 20 );
		if ( $per_page <= 0 ) {
			$per_page = 20;
		}
		if ( $total_entries > $per_page ) {
			$pages = ceil( $total_entries / $per_page );
			if ( empty( $return_js ) ) {
				echo '<div data-perpage="' . esc_attr( $per_page ) . '" data-totalpages="' . esc_attr( $pages ) . '" class="tcpagination tc-clearfix"></div>';
			}
		} else {
			if ( empty( $return_js ) ) {
				echo '<div data-perpage="' . esc_attr( $per_page ) . '" data-totalpages="0" class="tcpagination tc-clearfix"></div>';
			}
		}
		if ( empty( $return_js ) ) {
			echo "<div class='panels_wrap nof_wrapper'>";
		}

		$d_counter    = 0;
		$show_counter = 0;
		foreach ( $options['title'] as $ar => $el ) {
			$js_object[ $d_counter ] = [];
			$hidden_class            = '';
			if ( $show_counter >= $per_page ) {
				$hidden_class = ' tm-hidden ';
			}
			$show_counter ++;

			if ( false === $options['title'][ $ar ] ) {
				$options['title'][ $ar ] = $original_o['title']['default'];
			}
			if ( ! isset( $options['value'][ $ar ] ) ) {
				$options['value'][ $ar ] = $original_o['value']['default'];
			}
			if ( ! isset( $options['price'][ $ar ] ) ) {
				$options['price'][ $ar ] = $original_o['price']['default'];
			}
			if ( ! isset( $options['sale_price'][ $ar ] ) ) {
				$options['sale_price'][ $ar ] = $original_o['sale_price']['default'];
			}
			if ( ! isset( $options['image'][ $ar ] ) ) {
				$options['image'][ $ar ] = $original_o['image']['default'];
			}
			if ( ! isset( $options['imagec'][ $ar ] ) ) {
				$options['imagec'][ $ar ] = $original_o['imagec']['default'];
			}
			if ( ! isset( $options['imagep'][ $ar ] ) ) {
				$options['imagep'][ $ar ] = $original_o['imagep']['default'];
			}
			if ( ! isset( $options['imagel'][ $ar ] ) ) {
				$options['imagel'][ $ar ] = $original_o['imagel']['default'];
			}
			if ( ! isset( $options['price_type'][ $ar ] ) ) {
				$options['price_type'][ $ar ] = $original_o['price_type']['default'];
			}
			if ( ! isset( $options['url'][ $ar ] ) ) {
				$options['url'][ $ar ] = $original_o['url']['default'];
			}
			if ( ! isset( $options['description'][ $ar ] ) ) {
				$options['description'][ $ar ] = $original_o['description']['default'];
			}

			// backwards compatibility.
			if ( ! isset( $options['enabled'][ $ar ] ) || false === $options['enabled'][ $ar ] ) {
				$options['enabled'][ $ar ] = $original_o['enabled']['default'];
			}
			if ( '0' === $options['enabled'][ $ar ] || '' === $options['enabled'][ $ar ] ) {
				$options['enabled'][ $ar ] = '';
			}

			if ( ! isset( $options['color'][ $ar ] ) ) {
				$options['color'][ $ar ] = $original_o['color']['default'];
			}
			if ( ! isset( $options['fee'][ $ar ] ) ) {
				$options['fee'][ $ar ] = $original_o['fee']['default'];
			}
			foreach ( $_extra_options as $__key => $__name ) {
				if ( 'multiple_' . $__name['type'] . '_options' === $name ) {
					$_extra_name = $__name['name'];
					if ( ! isset( $options[ $_extra_name ][ $ar ] ) ) {
						if ( isset( $original_o[ $_extra_name ]['default'] ) ) {
							$options[ $_extra_name ][ $ar ] = $original_o[ $_extra_name ]['default'];
						} else {
							$options[ $_extra_name ][ $ar ] = '';
						}
					}
				}
			}

			$options = apply_filters( 'wc_epo_builder_element_array_in_loop_before', $options, $o, $ar, $name, $counter );

			foreach ( $o as $o_property => $o_value ) {
				if ( isset( $options[ $o_property ] ) ) {
					$o[ $o_property ]['default']      = $options[ $o_property ][ $ar ];
					$o[ $o_property ]['tags']['name'] = 'tm_meta[tmfbuilder][' . $name . '_' . $o_property . '][' . ( is_null( $counter ) ? 0 : $counter ) . '][]';
					$o[ $o_property ]['tags']['id']   = str_replace( [ '[', ']' ], '', $o[ $o_property ]['tags']['name'] ) . '_' . $ar;
				}
			}
			$o['image']['extra']  = [
				[ $this, 'builder_sub_options_image_helper' ],
				[ $name, $options['image'][ $ar ] ],
			];
			$o['imagec']['extra'] = [
				[ $this, 'builder_sub_options_imagec_helper' ],
				[ $name, $options['imagec'][ $ar ] ],
			];
			$o['imagep']['extra'] = [
				[ $this, 'builder_sub_options_imagep_helper' ],
				[ $name, $options['imagep'][ $ar ] ],
			];
			$o['imagel']['extra'] = [
				[ $this, 'builder_sub_options_imagel_helper' ],
				[ $name, $options['imagel'][ $ar ] ],
			];

			foreach ( $_extra_options as $__key => $__name ) {
				if ( 'multiple_' . $__name['type'] . '_options' === $name ) {
					$_extra_name                       = $__name['name'];
					$o[ $_extra_name ]['default']      = $options[ $_extra_name ][ $ar ];
					$o[ $_extra_name ]['tags']['name'] = 'tm_meta[tmfbuilder][' . $name . '_' . $_extra_name . '][' . ( is_null( $counter ) ? 0 : $counter ) . '][]';
					$o[ $_extra_name ]['tags']['id']   = str_replace(
						[
							'[',
							']',
						],
						'',
						$o[ $_extra_name ]['tags']['name']
					) . '_' . $ar;
					if ( isset( $o[ $_extra_name ]['admin_class'] ) ) {
						$o[ $_extra_name ]['admin_class'] = 'tc-extra-option ' . $o[ $_extra_name ]['admin_class'];
					} else {
						$o[ $_extra_name ]['admin_class'] = 'tc-extra-option';
					}
				}
			}

			$o = apply_filters( 'wc_epo_builder_element_array_in_loop_after', $o, $options, $ar, $name, $counter );

			$is_enabled = '' === $o['enabled']['default'] ? ' choice-is-disabled' : '';
			if ( empty( $return_js ) ) {
				echo '<div class="options-wrap' . esc_attr( $hidden_class ) . esc_attr( $is_enabled ) . '"><div class="tc-row nopadding tc-clearfix">';

				echo '<div class="tc-cell tc-col-auto tm_cell_move">';

				// Drag button.
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => '',
						'tags' => [
							'href'  => '#move',
							'class' => 'tmicon tcfa tcfa-grip-vertical move',
						],
					],
					1
				);
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => '',
						'icon' => 'angle-up',
						'tags' => [
							'href'  => '#move',
							'class' => 'tc tc-button small builder_panel_up',
						],
					],
					1
				);
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => '',
						'icon' => 'angle-down',
						'tags' => [
							'href'  => '#move',
							'class' => 'tc tc-button small builder_panel_down',
						],
					],
					1
				);

				echo '</div>';
				echo "<div class='tc-cell tc-col-auto tm_cell_default'>";

				// Default_select.
				echo '<span class="tm-hidden-inline">' . ( ( 'multiple_checkboxes_options' === $name || 'multiple_selectboxmultiple_options' === $name ) ? esc_html__( 'Checked', 'woocommerce-tm-extra-product-options' ) : esc_html__( 'Default', 'woocommerce-tm-extra-product-options' ) ) . '</span>';
			}
			if ( 'multiple_checkboxes_options' === $name || 'multiple_selectboxmultiple_options' === $name ) {
				if ( empty( $return_js ) ) {
					echo '<input type="checkbox" value="'
						. esc_attr( $d_counter )
						. '" name="tm_meta[tmfbuilder]['
						. esc_attr( $name ) . '_default_value][' . ( is_null( $counter ) ? 0 : esc_attr( $counter ) ) . '][]" class="tm-default-checkbox" ';
					checked(
						( is_null( $counter )
						? ''
						: ( isset( $default_value[ $counter ] )
							? is_array( $default_value[ $counter ] ) && in_array( (string) $d_counter, $default_value[ $counter ] ) // phpcs:ignore WordPress.PHP.StrictInArray
						: '' ) ),
						true,
						1
					);
					echo '>';
				} else {
					$js_object[ $d_counter ][] = [
						'id'      => $name . '_default_value',
						'default' => (string) $d_counter,
						'checked' => ( is_null( $counter )
							? ''
							: ( isset( $default_value[ $counter ] )
								? is_array( $default_value[ $counter ] ) && in_array( (string) $d_counter, $default_value[ $counter ] ) // phpcs:ignore WordPress.PHP.StrictInArray
								: '' ) ),
						'type'    => 'checkbox',
						'tags'    => [
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_default_value][' . ( is_null( $counter ) ? 0 : $counter ) . '][]',
							'class' => 'tm-default-checkbox',
						],
					];
				}
			} else {
				if ( empty( $return_js ) ) {
					echo '<input type="radio" value="'
						. esc_attr( $d_counter )
						. '" name="tm_meta[tmfbuilder]['
						. esc_attr( $name ) . '_default_value][' . ( is_null( $counter ) ? 0 : esc_attr( $counter ) ) . ']" class="tm-default-radio" ';
					checked(
						( is_null( $counter )
						? ''
						: ( ( isset( $default_value[ $counter ] ) && ! is_array( $default_value[ $counter ] ) )
							? (string) $default_value[ $counter ]
						: '' ) ),
						$d_counter,
						1
					);
					echo '>';
				} else {
					$js_object[ $d_counter ][] = [
						'id'      => $name . '_default_value',
						'default' => (string) $d_counter,
						'checked' => ( is_null( $counter )
							? ''
							: ( ( isset( $default_value[ $counter ] ) && ! is_array( $default_value[ $counter ] ) )
								? (string) $default_value[ $counter ] === (string) $d_counter
								: '' ) ),
						'type'    => 'radio',
						'tags'    => [
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_default_value][' . ( is_null( $counter ) ? 0 : $counter ) . ']',
							'class' => 'tm-default-checkbox',
						],
					];
				}
			}
			if ( empty( $return_js ) ) {
				echo '</div>';
				echo "<div class='tc-cell tc-col-3 tm_cell_title'>";
				THEMECOMPLETE_EPO_HTML()->create_field( $o['title'], 1 );
				echo '</div>';
				echo "<div class='tc-cell tc-col-3 tm_cell_images'>";
				THEMECOMPLETE_EPO_HTML()->create_field( $o['image'], 1 );
				THEMECOMPLETE_EPO_HTML()->create_field( $o['imagec'], 1 );
				THEMECOMPLETE_EPO_HTML()->create_field( $o['imagep'], 1 );
				THEMECOMPLETE_EPO_HTML()->create_field( $o['imagel'], 1 );
				if ( 'multiple_selectbox_options' !== $name ) {
					THEMECOMPLETE_EPO_HTML()->create_field( $o['color'], 1 );
				}
				echo '</div>';

				echo "<div class='tc-cell tc-col-0 tc-cell-value'>";
				THEMECOMPLETE_EPO_HTML()->create_field( $o['value'], 1 );
				echo '</div>';
				echo "<div class='tc-cell tc-col-auto tm_cell_price'>";
			} else {
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['title'] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['image'] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['imagec'] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['imagep'] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['imagel'] );
				if ( 'multiple_selectbox_options' !== $name ) {
					$js_object[ $d_counter ][] = $this->remove_for_js( $o['color'] );
				}
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['value'] );
			}

			if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
				$_copy_value                          = $o['price'];
				$_sale_copy_value                     = $o['sale_price'];
				$o['price']['html_before_field']      = '<span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span><span class="tm-choice-regular">' . esc_html__( 'Regular', 'woocommerce-tm-extra-product-options' ) . '</span>';
				$o['sale_price']['html_before_field'] = '<span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span><span class="tm-choice-sale">' . esc_html__( 'Sale', 'woocommerce-tm-extra-product-options' ) . '</span>';
				if ( empty( $return_js ) ) {
					THEMECOMPLETE_EPO_HTML()->create_field( $o['price'], 1 );
					THEMECOMPLETE_EPO_HTML()->create_field( $o['sale_price'], 1 );
				} else {
					$js_object[ $d_counter ][] = $this->remove_for_js( $o['price'] );
					$js_object[ $d_counter ][] = $this->remove_for_js( $o['sale_price'] );
				}
				foreach ( $additional_currencies as $ckey => $currency ) {
					$mt_prefix             = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );
					$copy_value            = $_copy_value;
					$copy_value['default'] = isset( $options[ 'price_' . $currency ][ $ar ] ) ? $options[ 'price' . $mt_prefix ][ $ar ] : '';
					$copy_value['id']     .= $mt_prefix;

					$copy_value['html_before_field'] = '<span class="tm-choice-currency">' . $currency . '</span><span class="tm-choice-regular">' . esc_html__( 'Regular', 'woocommerce-tm-extra-product-options' ) . '</span>';
					$copy_value['tags']['name']      = 'tm_meta[tmfbuilder][' . $name . '_price' . $mt_prefix . '][' . ( is_null( $counter ) ? 0 : $counter ) . '][]';
					$copy_value['tags']['id']        = str_replace(
						[
							'[',
							']',
						],
						'',
						$copy_value['tags']['name']
					) . '_' . $ar;
					if ( empty( $return_js ) ) {
						THEMECOMPLETE_EPO_HTML()->create_field( $copy_value, 1 );
					} else {
						$js_object[ $d_counter ][] = $copy_value;
					}

					$copy_value            = $_sale_copy_value;
					$copy_value['default'] = isset( $options[ 'sale_price_' . $currency ][ $ar ] ) ? $options[ 'sale_price' . $mt_prefix ][ $ar ] : '';
					$copy_value['id']     .= $mt_prefix;

					$copy_value['html_before_field'] = '<span class="tm-choice-currency">' . $currency . '</span><span class="tm-choice-sale">' . esc_html__( 'Sale', 'woocommerce-tm-extra-product-options' ) . '</span>';
					$copy_value['tags']['name']      = 'tm_meta[tmfbuilder][' . $name . '_sale_price' . $mt_prefix . '][' . ( is_null( $counter ) ? 0 : $counter ) . '][]';
					$copy_value['tags']['id']        = str_replace(
						[
							'[',
							']',
						],
						'',
						$copy_value['tags']['name']
					) . '_' . $ar;
					if ( empty( $return_js ) ) {
						THEMECOMPLETE_EPO_HTML()->create_field( $copy_value, 1 );
					} else {
						$js_object[ $d_counter ][] = $copy_value;
					}
				}
			} else {
				$o['price']['html_before_field']      = '<span class="tm-choice-regular">' . esc_html__( 'Regular', 'woocommerce-tm-extra-product-options' ) . '</span>';
				$o['sale_price']['html_before_field'] = '<span class="tm-choice-sale">' . esc_html__( 'Sale', 'woocommerce-tm-extra-product-options' ) . '</span>';
				if ( empty( $return_js ) ) {
					THEMECOMPLETE_EPO_HTML()->create_field( $o['price'], 1 );
					THEMECOMPLETE_EPO_HTML()->create_field( $o['sale_price'], 1 );
				} else {
					$js_object[ $d_counter ][] = $this->remove_for_js( $o['price'] );
					$js_object[ $d_counter ][] = $this->remove_for_js( $o['sale_price'] );
				}
			}

			if ( empty( $return_js ) ) {
				THEMECOMPLETE_EPO_HTML()->create_field( $o['price_type'], 1 );
				echo '</div>';
				echo "<div class='tc-cell tc-col-auto tm_cell_delete'>";

				// Delete button.
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => '',
						'tags' => [ 'class' => 'tmicon tcfa tcfa-times delete builder_panel_delete' ],
					],
					1
				);

				echo '</div>';

				if ( 'multiple_checkboxes_options' === $name ) {
					echo "<div class='" . esc_attr( apply_filters( 'wc_epo_builder_element_multiple_checkboxes_options_class', 'tc-cell tc-col-12 tm_cell_fee', $o ) ) . "'><span class='tm-inline-label bsbb'>" . esc_html__( 'Set to Fee', 'woocommerce-tm-extra-product-options' ) . '</span>';
					THEMECOMPLETE_EPO_HTML()->create_field( $o['fee'], 1 );
					echo '</div>';

					do_action( 'wc_epo_builder_element_multiple_checkboxes_options', $o );
				}
				if ( 'multiple_selectboxmultiple_options' !== $name ) {
					echo "<div class='tc-cell tc-col-12 tm_cell_description'><span class='tm-inline-label bsbb'>" . esc_html__( 'Description', 'woocommerce-tm-extra-product-options' ) . '</span>';
					THEMECOMPLETE_EPO_HTML()->create_field( $o['description'], 1 );
					echo '</div>';
				}

				echo "<div class='tc-cell tc-col-12 tm_cell_enabled'><span class='tm-inline-label bsbb'>" . esc_html__( 'Enabled', 'woocommerce-tm-extra-product-options' ) . '</span>';
				THEMECOMPLETE_EPO_HTML()->create_field( $o['enabled'], 1 );
				echo '</div>';
			} else {
				if ( 'multiple_checkboxes_options' === $name ) {

					$js_object[ $d_counter ][] = [
						'id'      => $name . '_fee',
						'default' => (string) $o['fee']['default'],
						'checked' => (string) $o['fee']['default'] === (string) $o['fee']['tags']['value'],
						'type'    => 'checkbox',
						'tags'    => [
							'name'  => 'tm_meta[tmfbuilder][' . $name . '_fee][' . ( is_null( $counter ) ? 0 : $counter ) . '][]',
							'value' => $o['fee']['tags']['value'],
						],
					];

					$js_object = apply_filters( 'wc_epo_builder_element_multiple_checkboxes_options_js_object', $js_object, $d_counter, $o, $name, $counter );

				}
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['price_type'] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['description'] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['enabled'] );
			}

			foreach ( $_extra_options as $__key => $__name ) {
				if ( 'multiple_' . $__name['type'] . '_options' === $name ) {
					$_extra_name = $__name['name'];
					if ( empty( $return_js ) ) {
						echo "<div class='tc-cell tc-col-12 " . esc_attr( $__name['admin_class'] ) . "'>";
						echo "<span class='tm-inline-label bsbb'>" . esc_attr( $__name['label'] ) . '</span>';
						THEMECOMPLETE_EPO_HTML()->create_field( $o[ $_extra_name ], 1 );
						echo '</div>';
					} else {
						$js_object[ $d_counter ][] = $this->remove_for_js( $o[ $_extra_name ] );
					}
				}
			}
			if ( empty( $return_js ) ) {
				echo "<div class='tc-cell tc-col-12 tc-cell-url'><span class='tm-inline-label bsbb'>" . esc_html__( 'URL', 'woocommerce-tm-extra-product-options' ) . '</span>';
				THEMECOMPLETE_EPO_HTML()->create_field( $o['url'], 1 );
				echo '</div>';

				echo '</div></div>';
			} else {
				$js_object[ $d_counter ][] = $this->remove_for_js( $o['url'] );
			}
			$d_counter ++;
		}
		if ( empty( $return_js ) ) {
			echo '</div>';
			echo ' <button type="button" class="tc tc-button builder-panel-add">' . esc_html__( 'Add item', 'woocommerce-tm-extra-product-options' ) . '</button>';
			if ( 'multiple_radiobuttons_options' === $name || 'multiple_checkboxes_options' === $name ) {
				echo ' <button type="button" class="tc tc-button builder-panel-add-separator">' . esc_html__( 'Add separator', 'woocommerce-tm-extra-product-options' ) . '</button>';
			}
			echo ' <button type="button" class="tc tc-button builder-panel-mass-add">' . esc_html__( 'Mass add', 'woocommerce-tm-extra-product-options' ) . '</button>';
		} else {
			return $js_object;
		}
	}

}
