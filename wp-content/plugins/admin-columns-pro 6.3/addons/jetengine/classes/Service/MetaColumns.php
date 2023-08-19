<?php

namespace ACA\JetEngine\Service;

use AC;
use AC\ListScreen;
use ACA\JetEngine\ColumnFactory;
use ACA\JetEngine\FieldRepository;

final class MetaColumns implements AC\Registerable {

	public function register(): void
    {
		add_action( 'ac/column_types', [ $this, 'add_meta_columns' ] );
	}

	public function add_meta_columns( ListScreen $list_screen ) {
		$repo = new FieldRepository($list_screen);
		$column_factory = new ColumnFactory();

		$fields = $repo->find_all();

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