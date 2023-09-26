<?php

namespace ACP\Export\UserPreference;

use AC\Preferences\Site;
use AC\Type\ListScreenId;
use LogicException;

class ExportedColumns {

	/**
	 * @var Site
	 */
	private $user_preference;

	public function __construct() {
		$this->user_preference = new Site( 'export_columns' );
	}

	private function validate_item( array $column_state ): void {
		if ( ! isset( $column_state['column_name'], $column_state['active'] ) ) {
			throw new LogicException( 'Invalid  item.' );
		}
	}

	public function save( ListScreenId $id, array $column_states ): void {
		array_map( [ $this, 'validate_item' ], $column_states );

		$this->user_preference->set(
			$id->get_id(),
			$column_states
		);
	}

	public function exists( ListScreenId $id ): bool {
		return $this->user_preference->exists(
			$id->get_id()
		);
	}

	public function get( ListScreenId $id ): array {
		return $this->user_preference->get(
			$id->get_id()
		);
	}

	public function delete( ListScreenId $id ): void {
		$this->user_preference->delete(
			$id->get_id()
		);
	}

}