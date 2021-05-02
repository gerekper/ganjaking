<?php

namespace ACP\Sorting\NativeSortable;

use AC\Column;
use AC\ColumnRepository;

class NativeSortableRepository {

	/**
	 * @var ColumnRepository
	 */
	private $column_repository;

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( ColumnRepository $column_repository, Storage $storage ) {
		$this->column_repository = $column_repository;
		$this->storage = $storage;
	}

	/**
	 * @param array $column_names
	 *
	 * @return bool
	 */
	public function update( array $column_names ) {
		$this->storage->update( $column_names );

		return true;
	}

	/**
	 * @param string $order_by
	 *
	 * @return Column|null
	 */
	public function find( $order_by ) {

		// The format is: [ $column_name => $orderby ] or [ $column_name => [ $orderby, true ] ]
		// The second format will make the initial sorting order be descending
		$data = $this->storage->get();

		if ( $data ) {

			foreach ( $data as $column_name => $_order_by ) {

				if ( is_string( $_order_by ) && $_order_by === $order_by ) {
					return $this->column_repository->find( $column_name );
				}

				if ( is_array( $_order_by ) && $_order_by[0] === $order_by ) {
					return $this->column_repository->find( $column_name );
				}
			}
		}

		return null;
	}

}