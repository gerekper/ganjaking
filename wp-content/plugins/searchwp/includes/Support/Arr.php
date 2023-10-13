<?php

namespace SearchWP\Support;

use ArrayAccess;

/**
 * Array manipulations class.
 *
 * Only general purpose actions are allowed in this class.
 * All actions specific to SearchWP operations should be placed elsewhere.
 *
 * @since 4.2.9
 */
class Arr {

	public const KEY_SEPARATOR = '.';

	/**
	 * Determine whether the given value is array accessible.
	 *
	 * @since 4.2.9
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function accessible( $value ) {

		return is_array( $value ) || $value instanceof ArrayAccess;
	}

	/**
	 * Add an element to an array using "dot" notation if it doesn't exist.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function add( $array, $key, $value ) {

		if ( is_null( static::get( $array, $key ) ) ) {
			static::set( $array, $key, $value );
		}

		return $array;
	}

	/**
	 * Divide an array into two arrays. One with keys and the other with values.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function divide( $array ) {

		return [ array_keys( $array ), array_values( $array ) ];
	}

	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @since 4.2.9
	 *
	 * @param iterable $array
	 * @param string $prepend
	 * @param string $key_separator
	 *
	 * @return array
	 */
	public static function dot( $array, $prepend = '', $key_separator = null ) {

		$results = [];

		if ( $key_separator === null ) {
			$key_separator = static::KEY_SEPARATOR;
		}

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				$results = array_merge( $results, static::dot( $value, $prepend . $key . $key_separator ) );
			} else {
				$results[ $prepend . $key ] = $value;
			}
		}

		return $results;
	}

	/**
	 * Convert a flattened "dot" notation array into an expanded array.
	 *
	 * @since 4.2.9
	 *
	 * @param iterable $array
	 *
	 * @return array
	 */
	public static function undot( $array ) {

		$results = [];

		foreach ( $array as $key => $value ) {
			static::set( $results, $key, $value );
		}

		return $results;
	}

	/**
	 * Get all of the given array except for a specified array of keys.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param array|string $keys
	 *
	 * @return array
	 */
	public static function except( $array, $keys ) {

		static::forget( $array, $keys );

		return $array;
	}

	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @since 4.2.9
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|int $key
	 *
	 * @return bool
	 */
	public static function exists( $array, $key ) {

		if ( $array instanceof ArrayAccess ) {
			return $array->offsetExists( $key );
		}

		return array_key_exists( $key, $array );
	}

	/**
	 * Return the first element in an array.
	 *
	 * @since 4.2.9
	 *
	 * @param iterable $array
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function first( $array, $default = null ) {

		foreach ( $array as $item ) {
			return $item;
		}

		return $default;
	}

	/**
	 * Return the first element in an array passing a given truth test.
	 *
	 * @since 4.2.9
	 *
	 * @param iterable $array
	 * @param callable $callback
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function first_if( $array, callable $callback, $default = null ) {

		if ( ! is_callable( $callback ) ) {
			return $default;
		}

		foreach ( $array as $key => $value ) {
			if ( $callback( $value, $key ) ) {
				return $value;
			}
		}

		return $default;
	}

	/**
	 * Return the last element in an array.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function last( $array, $default = null ) {

		return static::first( array_reverse( $array, true ), $default );
	}

	/**
	 * Return the last element in an array passing a given truth test.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param callable $callback
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function last_if( $array, callable $callback, $default = null ) {

		return static::first_if( array_reverse( $array, true ), $callback, $default );
	}

	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @since 4.2.9
	 *
	 * @param iterable $array
	 * @param int $depth
	 *
	 * @return array
	 */
	public static function flatten( $array, $depth = INF ) {

		$result = [];

		foreach ( $array as $item ) {

			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = $depth === 1
					? array_values( $item )
					: static::flatten( $item, $depth - 1 );

				foreach ( $values as $value ) {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Remove one or many array items from a given array using "dot" notation.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param array|string $keys
	 * @param string $key_separator
	 *
	 * @return void
	 */
	public static function forget( &$array, $keys, $key_separator = null ) {

		$original = &$array;

		$keys = (array) $keys;

		if ( count( $keys ) === 0 ) {
			return;
		}

		if ( $key_separator === null ) {
			$key_separator = static::KEY_SEPARATOR;
		}

		foreach ( $keys as $key ) {
			// If the exact key exists in the top-level, remove it.
			if ( static::exists( $array, $key ) ) {
				unset( $array[ $key ] );

				continue;
			}

			$parts       = explode( $key_separator, $key );
			$parts_count = count( $parts );

			// Clean up before each pass.
			$array = &$original;

			while ( $parts_count > 1 ) {
				$part        = array_shift( $parts );
				$parts_count = count( $parts );

				if ( isset( $array[ $part ] ) && is_array( $array[ $part ] ) ) {
					$array = &$array[ $part ];
				} else {
					continue 2;
				}
			}

			unset( $array[ array_shift( $parts ) ] );
		}
	}

	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @since 4.2.9
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|int|null $key
	 * @param mixed $default
	 * @param string $key_separator
	 *
	 * @return mixed
	 */
	public static function get( $array, $key, $default = null, $key_separator = null ) {

		if ( ! static::accessible( $array ) ) {
			return $default;
		}

		if ( is_null( $key ) ) {
			return $array;
		}

		if ( static::exists( $array, $key ) ) {
			return $array[ $key ];
		}

		if ( $key_separator === null ) {
			$key_separator = static::KEY_SEPARATOR;
		}

		if ( strpos( $key, $key_separator ) === false ) {
			return $array[ $key ] ?? $default;
		}

		foreach ( explode( $key_separator, $key ) as $segment ) {
			if ( static::accessible( $array ) && static::exists( $array, $segment ) ) {
				$array = $array[ $segment ];
			} else {
				return $default;
			}
		}

		return $array;
	}

	/**
	 * Check if an item or items exist in an array using "dot" notation.
	 *
	 * @since 4.2.9
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|array $keys
	 * @param string $key_separator
	 *
	 * @return bool
	 */
	public static function has( $array, $keys, $key_separator = null ) {

		$keys = (array) $keys;

		if ( ! $array || $keys === [] ) {
			return false;
		}

		if ( $key_separator === null ) {
			$key_separator = static::KEY_SEPARATOR;
		}

		foreach ( $keys as $key ) {
			$subKeyArray = $array;

			if ( static::exists( $array, $key ) ) {
				continue;
			}

			foreach ( explode( $key_separator, $key ) as $segment ) {
				if ( static::accessible( $subKeyArray ) && static::exists( $subKeyArray, $segment ) ) {
					$subKeyArray = $subKeyArray[ $segment ];
				} else {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Determine if any of the keys exist in an array using "dot" notation.
	 *
	 * @since 4.2.9
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|array $keys
	 *
	 * @return bool
	 */
	public static function has_any( $array, $keys ) {

		if ( is_null( $keys ) ) {
			return false;
		}

		$keys = (array) $keys;

		if ( ! $array ) {
			return false;
		}

		if ( $keys === [] ) {
			return false;
		}

		foreach ( $keys as $key ) {
			if ( static::has( $array, $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if an array is associative.
	 *
	 * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function is_assoc( array $array ) {

		$keys = array_keys( $array );

		return array_keys( $keys ) !== $keys;
	}

	/**
	 * Determines if an array is a list.
	 *
	 * An array is a "list" if all array keys are sequential integers starting from 0 with no gaps in between.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function is_list( $array ) {

		return ! static::is_assoc( $array );
	}

	/**
	 * Get a subset of the items from the given array.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param array|string $keys
	 *
	 * @return array
	 */
	public static function only( $array, $keys ) {

		return array_intersect_key( $array, array_flip( (array) $keys ) );
	}

	/**
	 * Push an item onto the beginning of an array.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param mixed $value
	 * @param mixed $key
	 *
	 * @return array
	 */
	public static function prepend( $array, $value, $key = null ) {

		if ( func_num_args() === 2 ) {
			array_unshift( $array, $value );
		} else {
			$array = [ $key => $value ] + $array;
		}

		return $array;
	}

	/**
	 * Get a value from the array, and remove it.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param string|int $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function pull( &$array, $key, $default = null ) {

		$value = static::get( $array, $key, $default );

		static::forget( $array, $key );

		return $value;
	}

	/**
	 * Convert the array into a query string.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public static function query( $array ) {

		return http_build_query( $array, '', '&', PHP_QUERY_RFC3986 );
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param string|null $key
	 * @param mixed $value
	 * @param string $key_separator
	 *
	 * @return array
	 */
	public static function set( &$array, $key, $value, $key_separator = null ) {

		if ( is_null( $key ) ) {
			return $array = $value;
		}

		if ( $key_separator === null ) {
			$key_separator = static::KEY_SEPARATOR;
		}

		$keys = explode( $key_separator, $key );

		foreach ( $keys as $i => $_key ) {
			if ( count( $keys ) === 1 ) {
				break;
			}

			unset( $keys[ $i ] );

			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			if ( ! isset( $array[ $_key ] ) || ! is_array( $array[ $_key ] ) ) {
				$array[ $_key ] = [];
			}

			$array = &$array[ $_key ];
		}

		$array[ array_shift( $keys ) ] = $value;

		return $array;
	}

	/**
	 * Recursively sort an array by keys and values.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param int $options
	 * @param bool $descending
	 *
	 * @return array
	 */
	public static function sort_recursive( $array, $options = SORT_REGULAR, $descending = false ) {

		foreach ( $array as &$value ) {
			if ( is_array( $value ) ) {
				$value = static::sort_recursive( $value, $options, $descending );
			}
		}

		if ( static::is_assoc( $array ) ) {
			$descending
				? krsort( $array, $options )
				: ksort( $array, $options );
		} else {
			$descending
				? rsort( $array, $options )
				: sort( $array, $options );
		}

		return $array;
	}

	/**
	 * Conditionally compile classes from an array into a CSS class list.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public static function to_css_classes( $array ) {

		$class_list = static::wrap( $array );

		$classes = [];

		foreach ( $class_list as $class => $constraint ) {
			if ( is_numeric( $class ) ) {
				$classes[] = $constraint;
			} elseif ( $constraint ) {
				$classes[] = $class;
			}
		}

		return implode( ' ', $classes );
	}

	/**
	 * Filter the array using the given callback.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 * @param callable $callback
	 *
	 * @return array
	 */
	public static function where( $array, callable $callback ) {

		return array_filter( $array, $callback, ARRAY_FILTER_USE_BOTH );
	}

	/**
	 * Filter items where the value is not null.
	 *
	 * @since 4.2.9
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function where_not_null( $array ) {

		return static::where( $array, function ( $value ) {
			return ! is_null( $value );
		} );
	}

	/**
	 * If the given value is not an array and not null, wrap it in one.
	 *
	 * @since 4.2.9
	 *
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function wrap( $value ) {

		if ( is_null( $value ) ) {
			return [];
		}

		return is_array( $value ) ? $value : [ $value ];
	}
}
