<?php

namespace ACP\Migrate\Admin\Table;

use AC;
use AC\ListScreen;
use AC\ListScreenCollection;

class Export extends AC\Admin\Table {

	private $storage;

	private $list_screens;

	public function __construct( AC\ListScreenRepository\Storage $storage, ListScreenCollection $list_screens ) {
		$this->storage = $storage;
		$this->list_screens = $list_screens;
	}

	public function get_rows(): ListScreenCollection {
		if ( $this->list_screens->count() < 1 ) {
			$this->message = __( 'No column settings available.', 'codepress-admin-columns' );
		}

		return $this->list_screens;
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
			case 'source' :
				return $this->get_source( $list_screen );
		}

		return null;
	}

	private function get_repository_label( $repository_name ) {
		$labels = [
			'acp-database' => __( 'Database', 'codepress-admin-columns' ),
			'acp-file'     => __( 'File', 'codepress-admin-columns' ),
		];

		return $labels[ $repository_name ] ?? $repository_name;
	}

	private function get_source( ListScreen $list_screen ) {
		foreach ( array_reverse( $this->storage->get_repositories() ) as $name => $repo ) {
			if ( ! $repo->find( $list_screen->get_id() ) ) {
				continue;
			}

			$label = $this->get_repository_label( $name );

			if ( $repo->has_source( $list_screen->get_id() ) ) {
				return sprintf( '<span data-ac-tip="%s">%s</span>',
					sprintf( '%s: %s', __( 'Path', 'codepress-admin-columns' ), $repo->get_source( $list_screen->get_id() ) ),
					$label
				);
			}

			return $label;
		}

		return null;
	}

	public function get_headings() {
		return [
			'check-column' => '<input type="checkbox" data-select-all>',
			'list-table'   => __( 'List Table', 'codepress-admin-columns' ),
			'name'         => __( 'Name', 'codepress-admin-columns' ),
			'source'       => __( 'Source', 'codepress-admin-columns' ),
			'id'           => __( 'ID', 'codepress-admin-columns' ),
		];
	}

}