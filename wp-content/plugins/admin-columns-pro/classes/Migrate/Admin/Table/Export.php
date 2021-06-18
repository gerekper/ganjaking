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

	/**
	 * @var bool
	 */
	private $network_only;

	public function __construct( Storage $storage, $network_only ) {
		$this->storage = $storage;
		$this->network_only = $network_only;
	}

	/**
	 * @return ListScreenCollection
	 */
	public function get_rows() {
		$args = [
			Storage::ARG_SORT => new AC\ListScreenRepository\Sort\Label(),
		];

		if ( $this->network_only ) {
			$args[ Storage::ARG_FILTER ][] = new AC\ListScreenRepository\Filter\Network();
		}

		$rows = $this->storage->find_all( $args );

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

		return isset( $labels[ $repository_name ] )
			? $labels[ $repository_name ]
			: $repository_name;
	}

	private function get_source( ListScreen $list_screen ) {
		foreach ( array_reverse( $this->storage->get_repositories() ) as $name => $repo ) {
			if ( ! $repo->find( $list_screen->get_id() ) ) {
				continue;
			}

			$label = $this->get_repository_label( $name );

			if ( $repo->has_source( $list_screen->get_id() ) ) {
				return $this->get_mini_tooltip( $label,
					sprintf( '<small>%s</small>',
						sprintf( '%s: %s', __( 'Path', 'codepress-admin-columns' ), $repo->get_source( $list_screen->get_id() ) )
					)
				);
			}

			return $label;
		}

		return null;
	}

	private function get_mini_tooltip( $label, $content ) {
		$view = new AC\View( [
			'label'   => $label,
			'content' => $content,
		] );

		$view->set_template( 'admin/mini-tooltip' );

		return $view->render();
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