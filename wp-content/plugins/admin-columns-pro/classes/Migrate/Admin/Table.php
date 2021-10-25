<?php

namespace ACP\Migrate\Admin;

use AC;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Aggregate;

class Table extends AC\Admin\Table {

	/** @var Aggregate */
	private $repository;

	public function __construct( $repository ) {
		$this->repository = $repository;
	}

	/**
	 * @return ListScreenCollection
	 */
	public function get_items() {
		return $this->repository->find_all( [
			'sort' => new AC\ListScreenRepository\SortStrategy\ListScreenLabel(),
		] );
	}

	/**
	 * @param string     $name
	 * @param ListScreen $listScreen
	 *
	 * @return string|null
	 */
	public function render_column( $name, $listScreen ) {
		switch ( $name ) {
			case 'check-column' :
				return sprintf( '<input name="list_screen_id[]" type="checkbox" id="export-%1$s" value="%1$s">', $listScreen->get_layout_id() );
			case 'name' :
				return sprintf( '<a href="%s">%s</a>', $listScreen->get_edit_link(), $listScreen->get_title() );
			case 'list-table' :
				return sprintf( '<label for="export-%s"><strong>%s</strong></label>', $listScreen->get_layout_id(), $listScreen->get_label() );
			case 'id' :
				return sprintf( '<small>%s</small>', $listScreen->get_layout_id() );
		}

		return null;
	}

	public function get_columns() {
		return [
			'check-column' => sprintf( '<input type="checkbox" data-select-all>' ),
			'list-table'   => __( 'List Table', 'codepress-admin-columns' ),
			'name'         => __( 'Name', 'codepress-admin-columns' ),
			'id'           => __( 'ID', 'codepress-admin-columns' ),
		];
	}

}