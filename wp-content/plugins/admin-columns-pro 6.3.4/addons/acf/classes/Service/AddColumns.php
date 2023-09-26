<?php

namespace ACA\ACF\Service;

use AC\ListScreen;
use AC\Registerable;
use ACA\ACF\Column\Deprecated;
use ACA\ACF\ColumnFactory;
use ACA\ACF\FieldRepository;
use ACA\ACF\FieldsFactory;

class AddColumns implements Registerable {

	/**
	 * @var FieldRepository
	 */
	private $field_repository;

	/**
	 * @var FieldsFactory
	 */
	private $fields_factory;

	/**
	 * @var ColumnFactory
	 */
	private $column_factory;

	public function __construct( FieldRepository $field_repository, FieldsFactory $fields_factory, ColumnFactory $column_factory ) {
		$this->field_repository = $field_repository;
		$this->fields_factory = $fields_factory;
		$this->column_factory = $column_factory;
	}

	public function register(): void
    {
		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
	}

	public function add_columns( ListScreen $list_screen ) {
		$fields = $this->field_repository->find_by_list_screen( $list_screen );

		// Fields including subfields
		$all_fields = array_map( [ $this->fields_factory, 'create' ], $fields );

		if ( ! empty( $all_fields ) ) {
			$all_fields = array_merge( ...$all_fields );
		}

		$columns = array_filter( array_map( [ $this->column_factory, 'create' ], $all_fields ) );

		// Register deprecated column
		$columns[] = new Deprecated();

		array_map( [ $list_screen, 'register_column_type' ], $columns );
	}

}