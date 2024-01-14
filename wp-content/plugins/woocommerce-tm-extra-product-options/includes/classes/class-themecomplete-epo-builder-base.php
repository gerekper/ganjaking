<?php
/**
 * Extra Product Options Builder class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Builder class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
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
	 * @var array<mixed>
	 */
	public $internal_element_names = [];

	/**
	 * All elements
	 *
	 * @var array<mixed>
	 */
	public $all_elements = [];

	/**
	 * Extra setting for multiple options
	 *
	 * @var array<mixed>
	 */
	public $extra_multiple_options = [];

	/**
	 * Default option attributes
	 *
	 * @var array<mixed>
	 */
	public $default_attributes = [];

	/**
	 * Addon option attributes
	 *
	 * @var array<mixed>
	 */
	public $addons_attributes = [];

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_BUILDER_Base
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

		// extra multiple type options.
		$this->extra_multiple_options = apply_filters( 'wc_epo_extra_multiple_choices', [] );
		add_action( 'tm_epo_register_extra_multiple_choices', [ $this, 'add_extra_choices' ], 50 );

		// Init internal elements.
		$this->init_internal_elements();

		if ( is_admin() ) {
			THEMECOMPLETE_EPO_ADMIN_BUILDER();
		}
	}

	/**
	 * Set internal element names
	 *
	 * @return void
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
			'multiple_file_upload',
		];

		if ( ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) || ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( stripslashes_deep( $_GET['post_type'] ) ) : get_post_type( absint( stripslashes_deep( $_GET['post'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
	 * @return void
	 * @since 1.0
	 */
	public function add_extra_choices() {
		$this->extra_multiple_options = apply_filters( 'wc_epo_extra_multiple_choices', [] );
	}

	/**
	 * Holds all the elements types.
	 *
	 * @return void
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
	 * Get all elements
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_elements() {
		return $this->all_elements;
	}

	/**
	 * Get custom properties
	 *
	 * @param array<mixed> $builder Element builder array.
	 * @param string       $_prefix Element prefix.
	 * @param array<mixed> $_counter Counter array.
	 * @param array<mixed> $_elements The saved element types array.
	 * @param integer      $k0 Current section counter.
	 * @param array<mixed> $current_builder The current element builder array.
	 * @param integer      $current_counter The current element counter.
	 * @param string       $current_element The current element.
	 * @return array<mixed>
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
	 * @param array<mixed> $builder Element builder array.
	 * @param string       $_prefix Element prefix.
	 * @param array<mixed> $_counter Counter array.
	 * @param array<mixed> $_elements The saved element types array.
	 * @param integer      $k0 Current section counter.
	 * @return array<mixed>
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
	 * This function is only used by external addon plugins.
	 *
	 * @param array<mixed> $args Array of arguments.
	 * @return void
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
	 * Set elements
	 * Extends the internal $all_elements variable.
	 *
	 * @param array<mixed> $args Array of arguments.
	 * @return void
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
}
