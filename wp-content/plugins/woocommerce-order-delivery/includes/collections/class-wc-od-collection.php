<?php
/**
 * Handles a generic items collection.
 *
 * @package WC_OD/Collections
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Collection class.
 */
class WC_OD_Collection implements Iterator, ArrayAccess, Countable {

	/**
	 * Collection items.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $items The collection items.
	 */
	public function __construct( array $items = array() ) {
		$this->items = $this->get_arrayable_items( $items );
	}

	/**
	 * Gets all items for this collection.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * Gets all of the collection's keys.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function keys() {
		return array_keys( $this->items );
	}

	/**
	 * Gets if the collection is empty or not.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->items );
	}

	/**
	 * Gets the total number of items in the collection.
	 *
	 * @since 1.6.0
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->items );
	}

	/**
	 * Gets the first element in the collection.
	 *
	 * @since 1.6.0
	 *
	 * @return mixed The first item. Null on failure.
	 */
	public function first() {
		// This way we don't reset the array pointer.
		foreach ( $this->items as $item ) {
			return $item;
		}

		return null;
	}

	/**
	 * Gets an item by key.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $key The item key.
	 * @return mixed
	 */
	public function get( $key ) {
		return $this->offsetGet( $key );
	}

	/**
	 * Sets an item by key.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $key   The item key.
	 * @param mixed $item  The item.
	 */
	public function set( $key, $item ) {
		$this->offsetSet( $key, $item );
	}

	/**
	 * Adds an item to the collection.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $item The item.
	 */
	public function add( $item ) {
		$this->offsetSet( null, $item );
	}

	/**
	 * Removes an item from the collection.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $key The item key.
	 */
	public function remove( $key ) {
		$this->offsetUnset( $key );
	}

	/**
	 * Runs a map over each of the items.
	 *
	 * @since 1.6.0
	 *
	 * @param callable $callback The callback.
	 * @return WC_OD_Collection
	 */
	public function map( $callback ) {
		return $this->create_collection(
			$this->map_items( $this->items, $callback )
		);
	}

	/**
	 * Runs a filter over each of the items.
	 *
	 * @since 1.6.0
	 *
	 * @see array_filter()
	 *
	 * @param callable $callback The callback.
	 * @return WC_OD_Collection
	 */
	public function filter( $callback ) {
		return $this->create_collection(
			array_filter( $this->items, $callback )
		);
	}

	/**
	 * Filters the items by the given key/value pairs.
	 *
	 * This method has two usages:
	 *
	 * - A single where clause: `where( 'key', 'value', 'operator' )`.
	 * - Multiple where clauses: `where( $clauses, $where_operator )`.
	 *
	 * Where `$clauses` is an array with pairs ['key' => 'value'] or nested arrays with ['key', 'value', 'operator'].
	 * The `$where_operator` can be 'AND', 'OR' or 'NOT'.
	 *
	 * The following example will retrieve the items which match at least one of the two where clauses:
	 *
	 *  where(
	 *      array(
	 *          'key_1' => 'value_1',
	 *          array( 'key_2', 'value_2', '!=' )
	 *      ),
	 *      'OR'
	 * )
	 *
	 * @since 1.6.0
	 *
	 * @param mixed  $key      The item key or an array with multiple where clauses.
	 * @param mixed  $value    Optional. The item value or the where operator when `$key` contains multiple where clauses.
	 * @param string $operator Optional. The where clause operator. Default '==='.
	 * @return WC_OD_Collection
	 */
	public function where( $key, $value = null, $operator = null ) {
		$where_items    = array();
		$where_clauses  = ( is_array( $key ) ? $key : array( array( $key, $value, $operator ) ) );
		$where_operator = ( is_array( $key ) && ! empty( $value ) ? $value : 'AND' );

		foreach ( $this->items as $index => $item ) {
			if ( $this->where_clauses( $item, $where_clauses, $where_operator ) ) {
				$where_items[ $index ] = $item;
			}
		}

		return $this->create_collection( $where_items );
	}

	/**
	 * Searches an item by the given key/value pairs and returns its key if found.
	 *
	 * @since 1.6.0
	 *
	 * @see WC_OD_Collection->where()
	 *
	 * @param mixed  $key      The item key or an array with multiple where clauses.
	 * @param mixed  $value    Optional. The item value or the where operator when `$key` contains multiple where clauses.
	 * @param string $operator Optional. The where clause operator. Default '==='.
	 * @return mixed The key of the first item that matches the specified conditions. Null if not found.
	 */
	public function search( $key, $value = null, $operator = null ) {
		$where_clauses  = ( is_array( $key ) ? $key : array( array( $key, $value, $operator ) ) );
		$where_operator = ( is_array( $key ) && ! empty( $value ) ? $value : 'AND' );

		foreach ( $this->items as $index => $item ) {
			if ( $this->where_clauses( $item, $where_clauses, $where_operator ) ) {
				return $index;
			}
		}

		return null;
	}

	/**
	 * Retrieves all of the values for a given key.
	 *
	 * @since 1.6.0
	 *
	 * @see wp_list_pluck()
	 *
	 * @param int|string $key       Key from the object to place instead of the entire object.
	 * @param int|string $index_key Optional. Key from the object to use as keys for the new array.
	 * @return array
	 */
	public function pluck( $key, $index_key = null ) {
		return wp_list_pluck( $this->items, $key, $index_key );
	}

	/**
	 * Intersects the collection with the given items by key.
	 *
	 * @since 1.6.0
	 *
	 * @see array_intersect_key()
	 *
	 * @param mixed $items The items.
	 * @return WC_OD_Collection
	 */
	public function intersect_keys( $items ) {
		return $this->create_collection(
			array_intersect_key( $this->items, $this->get_arrayable_items( $items ) )
		);
	}

	/**
	 * Converts the collection into a plain PHP array.
	 *
	 * If the collection's values are WC_OD_Data objects, the objects will also be converted to arrays.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function to_array() {
		$items = $this->items;

		foreach ( $items as $key => $item ) {
			if ( $item instanceof WC_OD_Data || $item instanceof self ) {
				$items[ $key ] = $item->to_array();
			}
		}

		return $items;
	}

	/**
	 * Gets the collection of items as JSON.
	 *
	 * @since 1.6.0
	 *
	 * @see wp_json_encode()
	 *
	 * @param int $options Optional. Options to be passed to json_encode(). Default 0.
	 * @return string|false The JSON encoded string, or false if it cannot be encoded.
	 */
	public function to_json( $options = 0 ) {
		return wp_json_encode( $this->to_array(), $options );
	}

	/**
	 * Converts the collection to its string representation.
	 *
	 * @since 1.6.0
	 *
	 * @return string Data in JSON format.
	 */
	public function __toString() {
		return $this->to_json();
	}

	/**
	 * Creates a new collection.
	 *
	 * Some methods of this collection may be chained to fluently manipulate the underlying array.
	 * Furthermore, these methods return a new Collection instance, allowing you to preserve the original copy of the collection when necessary.
	 *
	 * @since 1.6.0
	 *
	 * @param array $items The collection items.
	 * @return WC_OD_Collection
	 */
	protected function create_collection( array $items = array() ) {
		$class = get_class( $this );

		return new $class( $items );
	}

	/**
	 * Converts the given items to an array.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $items The items.
	 * @return array
	 */
	protected function get_arrayable_items( $items ) {
		if ( is_array( $items ) ) {
			return $items;
		} elseif ( $items instanceof self ) {
			return $items->all();
		}

		return (array) $items;
	}

	/**
	 * Runs a map over each of the items.
	 *
	 * Preserves the array indices.
	 *
	 * @since 1.6.0
	 *
	 * @param array    $items    The items to apply the callback.
	 * @param callable $callback The callback.
	 * @return array
	 */
	protected function map_items( array $items, $callback ) {
		$keys   = array_keys( $items );
		$values = array_map( $callback, $items, $keys );

		return array_combine( $keys, $values );
	}

	/**
	 * Checks multiple where clauses for the specified item.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed  $item     The item to apply the where clauses.
	 * @param array  $clauses  The where clauses.
	 * @param string $operator Optional. The logical operation to perform. 'AND' means
	 *                         all clauses must match. 'OR' means only
	 *                         one clause needs to match. 'NOT' means no clauses may
	 *                         match. Default 'AND'.
	 * @return bool
	 */
	protected function where_clauses( $item, array $clauses, $operator = 'AND' ) {
		$operator = ( ! empty( $operator ) ? strtoupper( $operator ) : 'AND' );

		if ( ! in_array( $operator, array( 'AND', 'OR', 'NOT' ), true ) ) {
			return false;
		}

		$valid = ( 'OR' !== $operator );

		foreach ( $clauses as $key => $clause ) {
			// Convert clause from the format ['key' => 'value'] to ['key', 'value'].
			if ( ! is_array( $clause ) ) {
				$clause = array( $key, $clause );
			}

			$match = $this->where_clause( $item, $clause );

			if ( ( ! $match && 'AND' === $operator ) || ( $match && 'NOT' === $operator ) ) {
				$valid = false;
				break;
			} elseif ( $match && 'OR' === $operator ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}

	/**
	 * Checks the where clause for the specified item.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $item   The item to apply the where clause.
	 * @param array $clause The where clause.
	 * @return bool
	 */
	protected function where_clause( $item, $clause ) {
		list( $key, $value, $operator ) = array_pad( $clause, 3, null );

		switch ( $operator ) {
			default:
			case '===':
				return $item[ $key ] === $value;
			case '!==':
				return $item[ $key ] !== $value;
			case '=':
			case '==':
				return $item[ $key ] == $value; // WPCS: loose comparison ok.
			case '!=':
			case '<>':
				return $item[ $key ] != $value; // WPCS: loose comparison ok.
			case '<':
				return $item[ $key ] < $value;
			case '>':
				return $item[ $key ] > $value;
			case '<=':
				return $item[ $key ] <= $value;
			case '>=':
				return $item[ $key ] >= $value;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Iterator Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns the current element.
	 *
	 * @since 1.6.0
	 *
	 * @return array Of items at current priority.
	 */
	public function current() {
		return current( $this->items );
	}

	/**
	 * Moves forward to the next element.
	 *
	 * @since 1.6.0
	 *
	 * @return array Of items at next priority.
	 */
	public function next() {
		return next( $this->items );
	}

	/**
	 * Returns the key of the current element.
	 *
	 * @since 1.6.0
	 *
	 * @return mixed Returns current priority on success, or NULL on failure
	 */
	public function key() {
		return key( $this->items );
	}

	/**
	 * Checks if current position is valid.
	 *
	 * @since 1.6.0
	 *
	 * @return boolean
	 */
	public function valid() {
		return ( key( $this->items ) !== null );
	}

	/**
	 * Rewinds the Iterator to the first element.
	 *
	 * @since 1.6.0
	 */
	public function rewind() {
		reset( $this->items );
	}

	/*
	|--------------------------------------------------------------------------
	| Array Access Methods
	|--------------------------------------------------------------------------
	|
	| For backwards compatibility with legacy arrays.
	|
	*/

	/**
	 * Determines whether an offset value exists.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $offset The offset to check for.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->items[ $offset ] );
	}

	/**
	 * Retrieves a value at a specified offset.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $offset The offset to retrieve.
	 * @return mixed If set, the value at the specified offset, null otherwise.
	 */
	public function offsetGet( $offset ) {
		return ( isset( $this->items[ $offset ] ) ? $this->items[ $offset ] : null );
	}

	/**
	 * Sets a value at a specified offset.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 */
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->items[] = $value;
		} else {
			$this->items[ $offset ] = $value;
		}
	}

	/**
	 * Unsets a specified offset.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $offset The offset to unset.
	 */
	public function offsetUnset( $offset ) {
		unset( $this->items[ $offset ] );
	}
}
