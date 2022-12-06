<?php

namespace ACP\Export\UserPreference;

use AC\Preferences\Site;
use AC\Type\ListScreenId;

class ExportedColumns {

	/**
	 * @var Site
	 */
	private $user_preference;

	public function __construct() {
		$this->user_preference = new Site( 'export_columns' );
	}

	public function save( ListScreenId $id, array $column_names ): void {
		$this->user_preference->set(
			$id->get_id(),
			$column_names
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