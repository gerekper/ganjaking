<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Date picker Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_ADDON extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * Addon element arguments
	 *
	 * @var array
	 */
	private $args;

	/**
	 * Class Constructor
	 *
	 * @param string $options Addon element name.
	 * @param array  $args Addon element attributes.
	 * @since 6.0
	 */
	public function __construct( $options = [], $args = [] ) {
		$this->element_name  = $args['name'];
		$this->args          = $args;
		$options['is_addon'] = true;

		if ( ! isset( $args['namespace'] ) || $args['namespace'] === $this->elements_namespace ) {
			$options['namespace'] = $this->elements_namespace . ' addon ' . $this->element_name;
		} else {
			$options['namespace'] = $args['namespace'];
		}

		if ( ! isset( $options['name'] ) ) {
			$options['name'] = '';
		}
		if ( ! isset( $options['description'] ) ) {
			$options['description'] = '';
		}
		if ( ! isset( $options['type'] ) ) {
			$options['type'] = '';
		}
		if ( ! isset( $options['width'] ) ) {
			$options['width'] = '';
		}
		if ( ! isset( $options['width_display'] ) ) {
			$options['width_display'] = '';
		}
		if ( ! isset( $options['icon'] ) ) {
			$options['icon'] = '';
		}
		if ( ! isset( $options['is_post'] ) ) {
			$options['is_post'] = '';
		}
		if ( ! isset( $options['post_name_prefix'] ) ) {
			$options['post_name_prefix'] = '';
		}
		if ( ! isset( $options['fee_type'] ) ) {
			$options['fee_type'] = '';
		}
		if ( ! isset( $options['show_on_backend'] ) ) {
			$options['show_on_backend'] = true;
		}

		$options['tags'] = $options['name'];

		foreach ( $options as $property => $value ) {
			$this->{$property} = $value;
		}

	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			$this->args['settings'],
			true,
			isset( $this->args['tabs_override'] ) ? $this->args['tabs_override'] : []
		);
	}
}
