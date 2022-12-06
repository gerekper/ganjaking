<?php

namespace ACA\JetEngine\Service;

use AC;
use AC\ListScreen;
use ACA\JetEngine\ColumnFactory;
use ACA\JetEngine\FieldRepository;

final class MetaColumns implements AC\Registerable {

	public function register() {
		add_action( 'ac/column_types', [ $this, 'add_meta_columns' ] );
	}

	public function add_meta_columns( ListScreen $list_screen ) {
		$repo = new FieldRepository();
		$column_factory = new ColumnFactory();

		$fields = $repo->find_by_list_screen( $list_screen );

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$column = $column_factory->create( $field );

			if ( $column ) {
				$column->set_field( $field );

				$list_screen->register_column_type( $column );
			}
		}
	}

}