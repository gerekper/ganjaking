<?php
declare( strict_types=1 );

namespace ACP\Export\Asset;

use AC;
use AC\Column;
use AC\ColumnRepository;
use AC\ColumnRepository\Sort\ManualOrder;
use ACP\Export\ColumnRepository\Filter\ExportableColumns;
use ACP\Export\ColumnRepository\Sort\ColumnNames;
use ACP\Export\Repository\UserColumnStateRepository;

class ExportVarFactory {

	private $list_screen;

	private $column_repository;

	private $column_state_repository;

	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
		$this->column_repository = new ColumnRepository( $list_screen );
		$this->column_state_repository = new UserColumnStateRepository();
	}

	public function create(): array {
		$vars = [];

		$active_column_names = $this->get_active_column_names();

		foreach ( $this->get_exportable_columns() as $column ) {
			$vars[] = [
				'name'          => $column->get_name(),
				'label'         => $this->get_sanitized_label( $column ),
				'default_state' => in_array( $column->get_name(), $active_column_names, true ) ? 'on' : 'off',
			];
		}

		return $vars;
	}

	private function get_active_column_names(): array {
		$user_exported_column_names = $this->get_user_exported_column_names_active();
		$exportable_column_names = $this->get_exportable_column_names();

		if ( ! $user_exported_column_names ) {
			$hidden_column_names = get_hidden_columns( $this->list_screen->get_screen_id() );

			return array_diff(
				$exportable_column_names,
				$hidden_column_names
			);
		}

		// add columns that have been added at a later time through the column settings page
		$missing_column_names = array_diff(
			$exportable_column_names,
			$this->get_user_exported_column_names()
		);

		return array_merge( $user_exported_column_names, $missing_column_names );
	}

	private function get_exportable_columns(): array {
		static $columns;

		if ( null === $columns ) {
			$column_names = $this->get_user_exported_column_names();

			$sort = $column_names
				? new ColumnNames( $column_names )
				: new ManualOrder( $this->list_screen->get_id() );

			$columns = $this->column_repository->find_all( [
				'filter' => [
					new ExportableColumns(),
				],
				'sort'   => $sort,
			] );
		}

		return $columns;
	}

	private function get_exportable_column_names(): array {
		return array_keys( $this->get_exportable_columns() );
	}

	private function get_user_exported_column_names_active(): array {
		$column_names = [];

		foreach ( $this->column_state_repository->find_all_active_by_list_id( $this->list_screen->get_id() ) as $state ) {
			$column_names[] = $state->get_column_name();
		}

		return $column_names;
	}

	private function get_user_exported_column_names(): array {
		$column_names = [];

		foreach ( $this->column_state_repository->find_all_by_list_id( $this->list_screen->get_id() ) as $state ) {
			$column_names[] = $state->get_column_name();
		}

		return $column_names;
	}

	private function get_sanitized_label( Column $column ): string {
		return $this->sanitize_column_label( $column->get_custom_label() ) ?: sprintf( '%s (%s)', $column->get_name(), $column->get_label() );
	}

	private function sanitize_column_label( string $label ): string {
		if ( false === strpos( $label, 'dashicons' ) ) {
			$label = strip_tags( $label );
		}

		return trim( $label );
	}

}