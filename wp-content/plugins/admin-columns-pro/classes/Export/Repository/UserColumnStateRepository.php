<?php
declare( strict_types=1 );

namespace ACP\Export\Repository;

use AC\Type\ListScreenId;
use ACP\Export\ColumnStateCollection;
use ACP\Export\Type\ColumnState;
use ACP\Export\UserPreference\ExportedColumns;

class UserColumnStateRepository {

	/**
	 * @var ExportedColumns
	 */
	private $storage;

	public function __construct() {
		$this->storage = new ExportedColumns();
	}

	public function find_all_by_list_id( ListScreenId $list_id ): ColumnStateCollection {
		$collection = new ColumnStateCollection();

		if ( ! $this->storage->exists( $list_id ) ) {
			return $collection;
		}

		foreach ( $this->storage->get( $list_id ) as $data ) {
			if ( ! isset( $data['column_name'], $data['active'] ) ) {
				continue;
			}

			$collection->add( new ColumnState( $data['column_name'], (bool) $data['active'] ) );
		}

		return $collection;
	}

	public function find_all_active_by_list_id( ListScreenId $list_id ): ColumnStateCollection {
		$collection = new ColumnStateCollection();

		foreach ( $this->find_all_by_list_id( $list_id ) as $state ) {
			if ( $state->is_active() ) {
				$collection->add( $state );
			}
		}

		return $collection;
	}

	public function save( ListScreenId $list_id, ColumnStateCollection $column_states ): void {
		$data = [];

		foreach ( $column_states as $column_state ) {
			$data[] = [
				'column_name' => $column_state->get_column_name(),
				'active'      => $column_state->is_active(),
			];
		}

		$data
			? $this->storage->save( $list_id, $data )
			: $this->storage->delete( $list_id );
	}

}