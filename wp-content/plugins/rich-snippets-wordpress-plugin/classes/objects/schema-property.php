<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Property object.
 *
 * A property.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Schema_Property {


	/**
	 * The ID of the property.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $id = '';


	/**
	 * The UID of the property.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	public $uid = '';


	/**
	 * A list of classes the property can be used in.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $domain_includes = array();


	/**
	 * The label of the property.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $label = '';


	/**
	 * The comment of a property.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $comment = '';


	/**
	 * Schema classes.
	 *
	 * Related schema classes that can be used on this property.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $range_includes = array();


	/**
	 * The original args given to the constructor.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed
	 */
	public $original_object = null;


	/**
	 * Holds the value, if any.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed|null|Rich_Snippet Null if there is no value.
	 */
	public $value = null;


	/**
	 * If a properties value is overridable.
	 *
	 * @since 2.2.0
	 *
	 * @var bool
	 */
	public $overridable = false;


	/**
	 * If this property can be overwritten multiple times.
	 *
	 * @since 2.2.0
	 *
	 * @var bool
	 */
	public $overridable_multiple = false;


	/**
	 * The suggested value to use.
	 *
	 * @var null|string
	 *
	 * @since 2.14.0
	 */
	public $suggested_value = null;


	/**
	 * The input name to use for overriding.
	 *
	 * @var string
	 *
	 * @since 2.14.3
	 */
	public $overridable_input_name = '';
	

	/**
	 * Transforms an $args array to a Property_Model object.
	 *
	 * @param object $obj
	 *
	 * @return Schema_Property
	 * @since 2.0.0
	 *
	 */
	public function __construct( $obj ) {

		foreach ( get_object_vars( $obj ) as $key => $val ) {
			$this->{$key} = $val;
		}

		if ( empty( $this->uid ) ) {
			$this->uid = uniqid( 'prop-' );
		}

		return $this;
	}


	public function is_overridable() {

		if ( $this->overridable ) {
			return true;
		}

		if ( ! isset( $this->value[1] ) ) {
			return false;
		}

		if ( ! $this->value[1] instanceof Rich_Snippet ) {
			return false;
		}

		return $this->value[1]->has_overridable_props();
	}


	/**
	 * Returns the value.
	 *
	 * @return mixed
	 * @since 2.2.0
	 *
	 */
	public function get_value() {

		if ( isset( $this->value[1] ) ) {
			return $this->value[1];
		}

		return '';
	}


	/**
	 * If the value is another Rich Snippet.
	 *
	 * @return bool
	 * @since 2.10.2
	 *
	 */
	public function inherits_sub_schema() {

		if ( ! isset( $this->value[1] ) ) {
			return false;
		}

		return $this->value[1] instanceof Rich_Snippet;
	}

}
