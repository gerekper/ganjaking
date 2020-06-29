<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;

class User extends AC\ListScreen\User
	implements Sorting\ListScreen, Editing\ListScreen, Filtering\ListScreen, Export\ListScreen {

	public function sorting( $model ) {
		return new Sorting\Strategy\User( $model );
	}

	public function editing() {
		return new Editing\Strategy\User();
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\User( $model );
	}

	public function export() {
		return new Export\Strategy\User( $this );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_type( new Column\CustomField );
		$this->register_column_type( new Column\Actions );
		$this->register_column_types_from_dir( 'ACP\Column\User' );
	}

}