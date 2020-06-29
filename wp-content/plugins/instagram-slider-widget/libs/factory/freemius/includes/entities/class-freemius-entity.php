<?php

namespace WBCR\Factory_Freemius_111\Entities;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, Alex Kovalev <alex.kovalevv@gmail.com>
 * @link          https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd, Freemius, Inc.
 * @version       1.0
 */
class Entity {

	/**
	 * @var number
	 */
	public $id;
	/**
	 * @var string Datetime value in 'YYYY-MM-DD HH:MM:SS' format.
	 */
	public $updated;
	/**
	 * @var string Datetime value in 'YYYY-MM-DD HH:MM:SS' format.
	 */
	public $created;

	/**
	 * @var bool
	 */
	private $is_updated = false;

	/**
	 * @param bool|object|array $entity
	 */
	public function __construct( $entity = false ) {
		if ( ! ( $entity instanceof stdClass ) && ! is_array( $entity ) ) {
			return;
		}

		$props = get_object_vars( $this );

		foreach ( $props as $key => $def_value ) {
			if ( is_object( $entity ) ) {
				$this->{$key} = isset( $entity->{$key} ) ? $entity->{$key} : $def_value;
			} else {
				$this->{$key} = isset( $entity[ $key ] ) ? $entity[ $key ] : $def_value;
			}
		}
	}

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.1
	 *
	 * @param object|array $data
	 *
	 * @return bool
	 */
	public function populate( $data ) {
		if ( ! is_object( $data ) && ! is_array( $data ) ) {
			return false;
		}

		$props = get_object_vars( $this );

		foreach ( $props as $key => $def_value ) {
			if ( is_array( $data ) ) {
				$this->{$key} = isset( $data[ $key ] ) ? $data[ $key ] : $def_value;
			} else {
				$this->{$key} = isset( $data->{$key} ) ? $data->{$key} : $def_value;
			}
		}

		return true;
	}

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 * @return array
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	static function get_type() {
		return 'type';
	}

	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.6
	 *
	 * @param Entity $entity1
	 * @param Entity $entity2
	 *
	 * @return bool
	 */
	static function equals( $entity1, $entity2 ) {
		if ( is_null( $entity1 ) && is_null( $entity2 ) ) {
			return true;
		} else if ( is_object( $entity1 ) && is_object( $entity2 ) ) {
			return ( $entity1->id == $entity2->id );
		} else if ( is_object( $entity1 ) ) {
			return is_null( $entity1->id );
		} else {
			return is_null( $entity2->id );
		}
	}


	/**
	 * Update object property.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.9
	 *
	 * @param string|array[string]mixed $key
	 * @param string|bool $val
	 *
	 * @return bool
	 */
	function update( $key, $val = false ) {
		if ( ! is_array( $key ) ) {
			$key = [ $key => $val ];
		}

		$is_updated = false;

		foreach ( $key as $k => $v ) {
			if ( $this->{$k} === $v ) {
				continue;
			}

			if ( ( is_string( $this->{$k} ) && is_numeric( $v ) || ( is_numeric( $this->{$k} ) && is_string( $v ) ) ) && $this->{$k} == $v ) {
				continue;
			}

			// Update value.
			$this->{$k} = $v;

			$is_updated = true;
		}

		$this->is_updated = $is_updated;

		return $is_updated;
	}

	/**
	 * Checks if entity was updated.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.9
	 *
	 * @return bool
	 */
	function is_updated() {
		return $this->is_updated;
	}

	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.2
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	static function is_valid_id( $id ) {
		return is_numeric( $id );
	}
}
