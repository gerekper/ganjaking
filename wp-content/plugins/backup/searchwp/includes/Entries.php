<?php

/**
 * SearchWP Entry Collection.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Entry;
use SearchWP\Source;

/**
 * Class Entries is a collection of Entry objects.
 *
 * @since 4.0
 */
class Entries {

	/**
	 * Collection items.
	 *
	 * @since 4.0
	 * @var   array
	 */
	private $items = [];

	/**
	 * Entries contrsuctor.
	 *
	 * @param Source $source The Source of the Entries.
	 * @param array  $ids    The Source Entry IDs.
	 * @since 4.0
	 */
	function __construct( Source $source, array $ids = [] ) {
		if ( ! empty( $ids ) ) {
			$source_name = $source->get_name();

			foreach ( $ids as $id ) {
				$entry = new Entry( $source_name, $id, false, false );

				$this->add( $entry );
			}
		}
	}

	/**
	 * Adds an Entry to the collection.
	 *
	 * @since 4.0
	 * @param Entry $entry The Entry to add.
	 * @return Entries
	 */
	public function add( Entry $entry ) {
		$this->items[ $this->key( $entry ) ] = $entry;

		return $this;
	}

	/**
	 * Removes an Entry from the collection.
	 *
	 * @since 4.0
	 * @param Entry $entry The Entry to remove.
	 * @return Entries
	 */
	public function remove( Entry $entry ) {
		unset( $this->items[ $this->key( $entry ) ] );

		return $this;
	}

	/**
	 * Generates a unique key for an Entry.
	 *
	 * @since 4.0
	 * @param Entry $entry The collection Entry.
	 * @return string
	 */
	private function key( Entry $entry ) {
		return md5( $entry->get_source()->get_name() . $entry->get_id() );
	}

	/**
	 * Getter for collection item IDs.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_ids() {
		return array_values( array_map( function( $entry ) {
			return $entry->get_id();
		}, $this->items ) );
	}

	/**
	 * Getter for collection items.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get() {
		return $this->items;
	}
}
