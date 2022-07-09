<?php

namespace ACP\Sorting\UserPreference;

use AC;
use ACP\Sorting\Type;

class SortType {

	const OPTION_ORDER = 'order';
	const OPTION_ORDERBY = 'orderby';

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var AC\Preferences\Site
	 */
	private $storage;

	/**
	 * @param string $key
	 */
	public function __construct( $key ) {
		$this->key = $key;
		$this->storage = new AC\Preferences\Site( 'sorted_by' );
	}

	/**
	 * @return Type\SortType|null
	 */
	public function get() {
		$data = $this->storage->get( $this->key );

		if ( empty( $data[ self::OPTION_ORDERBY ] ) ) {
			return null;
		}

		return new Type\SortType(
			(string) $data[ self::OPTION_ORDERBY ],
			(string) $data[ self::OPTION_ORDER ]
		);
	}

	/**
	 * @return bool
	 */
	public function delete() {
		return $this->storage->delete( $this->key );
	}

	public function save( Type\SortType $sort_type ) {
		$this->storage->set( $this->key, [
			self::OPTION_ORDERBY => $sort_type->get_order_by(),
			self::OPTION_ORDER   => $sort_type->get_order(),
		] );
	}

}