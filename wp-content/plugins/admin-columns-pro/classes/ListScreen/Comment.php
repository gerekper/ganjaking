<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;
use ReflectionException;

class Comment extends AC\ListScreen\Comment
	implements Sorting\ListScreen, Editing\ListScreen, Filtering\ListScreen, Export\ListScreen {

	public function sorting( $model ) {
		return new Sorting\Strategy\Comment( $model );
	}

	public function editing() {
		return new Editing\Strategy\Comment();
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\Comment( $model );
	}

	public function export() {
		return new Export\Strategy\Comment( $this );
	}

	/**
	 * @throws ReflectionException
	 */
	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_type( new Column\CustomField );
		$this->register_column_type( new Column\Actions );

		$this->register_column_types_from_dir( 'ACP\Column\Comment' );
	}

}