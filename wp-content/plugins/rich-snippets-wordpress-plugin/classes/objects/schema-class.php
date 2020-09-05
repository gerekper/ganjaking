<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class object.
 *
 * A schema.org Class instance.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.14.0
 */
class Schema_Class {


	/**
	 * The ID.
	 *
	 * @since 2.14.0
	 *
	 * @var string
	 */
	public $id = '';


	/**
	 * The comment of a property.
	 *
	 * @since 2.14.0
	 *
	 * @var string
	 */
	public $comment = '';


	/**
	 * The original args given to the constructor.
	 *
	 * @since 2.14.0
	 *
	 * @var mixed
	 */
	public $original_object = null;


	/**
	 * Superseded class list.
	 *
	 * @since 2.14.0
	 *
	 * @var array
	 */
	public $superseded_by = array();


	/**
	 * A list where this class is a sabclass of.
	 *
	 * @since 2.14.0
	 *
	 * @var array
	 */
	public $subclass_of = array();


	/**
	 * Schema_Class constructor.
	 *
	 * @param object $obj
	 *
	 * @return Schema_Class
	 * @since 2.14.0
	 */
	public function __construct( $obj ) {

		$class_vars = get_class_vars( __CLASS__ );

		foreach ( get_object_vars( $obj ) as $key => $value ) {
			if ( isset( $class_vars[ $key ] ) ) {
				$this->{$key} = $value;
			}
		}

		return $this;
	}
}
