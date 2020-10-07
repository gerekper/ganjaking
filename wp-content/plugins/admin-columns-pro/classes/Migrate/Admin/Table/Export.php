<?php

namespace ACP\Migrate\Admin\Table;

use AC;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Storage;

class Export extends AC\Admin\Table {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	/**
	 * @return ListScreenCollection
	 */
	public function get_rows() {
		$rows = $this->storage->find_all( [
			'sort' => new AC\ListScreenRepository\Sort\Label(),
		] );

		if ( $rows->count() < 1 ) {
			$this->message = __( 'No column settings available.', 'codepress-admin-columns' );
		}

		return $rows;
	}

	/**
	 * @param string     $key
	 * @param ListScreen $list_screen
	 *
	 * @return string|null
	 */
	public function get_column( $key, $list_screen ) {
		switch ( $key ) {
			case 'check-column' :
				return sprintf( '<input name="list_screen_ids[]" type="checkbox" id="export-%1$s" value="%1$s">', $list_screen->get_layout_id() );
			case 'name' :
				return sprintf( '<a href="%s">%s</a>', $list_screen->get_edit_link(), $list_screen->get_title() );
			case 'list-table' :
				return sprintf( '<label for="export-%s"><strong>%s</strong></label>', $list_screen->get_layout_id(), $list_screen->get_label() );
			case 'id' :
				return sprintf( '<small>%s</small>', $list_screen->get_layout_id() );
		}

		return null;
	}

	public function get_headings() {
		return [
			'check-column' => sprintf( '<input type="checkbox" data-select-all>' ),
			'list-table'   => __( 'List Table', 'codepress-admin-columns' ),
			'name'         => __( 'Name', 'codepress-admin-columns' ),
			'id'           => __( 'ID', 'codepress-admin-columns' ),
		];
	}

}