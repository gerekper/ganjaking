<?php declare( strict_types=1 );

namespace ACP\Export;

use AC;
use AC\Column;
use AC\ColumnRepository;
use AC\ColumnRepository\Sort\ManualOrder;
use AC\ListTable;
use AC\ListTableFactory;
use AC\Request;
use ACP\Export\Asset\Script\Table;
use ACP\Export\Repository\Filter\ExportableColumns;
use ACP\Export\Repository\Filter\IncludeColumnNames;
use ACP\Export\Repository\Sort\ColumnNames;
use ACP\Export\UserPreference;

/**
 * Base class for governing exporting for a list screen that is exportable. This class should be
 * extended, generally, per list screen. Furthermore, each instance of this class should be linked
 * to an Admin Columns list screen object
 * @since 1.0
 */
abstract class Strategy {

	/**
	 * Admin Columns list screen object this object is attached to
	 * @since 1.0
	 * @var ListScreen
	 */
	protected $list_screen;

	/**
	 * @var ListTableFactory
	 */
	protected $list_table_factory;

	/**
	 * @var ColumnRepository
	 */
	private $column_repository;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * Perform all required actions for when an AJAX export is requested. The parent class (this
	 * class) will perform the necessary validation, and the inheriting class should implement
	 * the actual functionality for setting up the items to be exported. The parent class's (this
	 * class) `export` method can then be used to actually export the items
	 * @since 1.0
	 */
	abstract protected function ajax_export(): void;

	/**
	 * Constructor
	 *
	 * @param AC\ListScreen $list_screen Associated Admin Columns list screen objec
	 *
	 * @since 1.0
	 */
	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
		$this->list_table_factory = new ListTableFactory();
		$this->column_repository = new ColumnRepository( $list_screen );
		$this->request = new Request();
	}

	protected function get_list_table(): ?ListTable {
		return $this->list_table_factory->create_from_globals();
	}

	public function is_active(): bool {
		if ( ! $this->list_screen->has_id() ) {
			return false;
		}

		if ( ! $this->get_exportable_columns() ) {
			return false;
		}

		$is_active = ! ( new HideOnScreen\Export() )->is_hidden( $this->list_screen );

		return ( new ApplyFilter\ListScreenActive( $this->list_screen ) )->apply_filters( $is_active );
	}

	/**
	 * Callback for when the list screen is loaded in Admin Columns, i.e., when it is active. Child
	 * classes should implement this method for any setup-related functionality
	 * @since 1.0
	 */
	public function attach(): void {
		$this->maybe_ajax_export();
	}

	/**
	 * Check whether an AJAX export should be made, and validate the input data. Will call child's
	 * `ajax_export` method to do the actual exporting
	 * @since 1.0
	 */
	public function maybe_ajax_export(): void {
		if ( 'acp_export_listscreen_export' !== $this->request->get( 'acp_export_action' ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $this->request->get( '_wpnonce' ), Table::NONCE_ACTION ) ) {
			return;
		}

		if ( $this->get_export_counter() === false ) {
			wp_send_json_error( __( 'Invalid value supplied for export counter.', 'codepress-admin-columns' ) );
		}

		do_action( 'acp/export/before_batch' );

		$this->ajax_export();
	}

	/**
	 * Get the counter value passed for the AJAX export
	 * @return int|false Counter value, or false if there is no valid counter value
	 * @since 1.0
	 */
	protected function get_export_counter() {
		$counter = (int) $this->request->filter( 'acp_export_counter', FILTER_SANITIZE_NUMBER_INT );

		return $counter >= 0
			? $counter
			: false;
	}

	/**
	 * @return int[]
	 */
	protected function get_requested_ids(): array {
		$ids = $this->request->filter( 'acp_export_ids', null, FILTER_SANITIZE_STRING );

		if ( empty( $ids ) ) {
			return [];
		}

		return array_map( 'absint', explode( ',', $ids ) );
	}

	/**
	 * @return Column[]
	 */
	public function get_exportable_columns(): array {
		$user_preference = new UserPreference\ExportedColumns();

		$sort = $user_preference->exists( $this->list_screen->get_id() )
			? new ColumnNames( $user_preference->get( $this->list_screen->get_id() ) )
			: new ManualOrder( $this->list_screen->get_id() );

		return $this->column_repository->find_all( [
			'filter' => [
				new ExportableColumns(),
			],
			'sort'   => $sort,
		] );
	}

	/**
	 * @return Column[]
	 */
	public function get_requested_columns(): array {
		$column_names = $this->request->filter( 'acp_export_columns', null, FILTER_SANITIZE_STRING );

		if ( ! $column_names ) {
			return [];
		}

		$column_names = explode( ',', $column_names );

		if ( ! $column_names ) {
			return [];
		}

		return $this->column_repository->find_all( [
			'filter' => [
				new ExportableColumns(),
				new IncludeColumnNames( $column_names ),
			],
			'sort'   => new ColumnNames( $column_names ),
		] );
	}

	/**
	 * Retrieve the rows to export based on a set of item IDs. The rows contain the column data to
	 * export for each item
	 *
	 * @param int[]    $ids IDs of the items to export
	 * @param Column[] $columns
	 *
	 * @return array[mixed] Rows to export. One row is returned for each item ID
	 * @since 1.0
	 */
	public function get_rows( array $ids, array $columns ): array {
		$table = $this->get_list_table();

		if ( ! $table ) {
			return [];
		}

		$rows = [];
		$headers = $this->get_headers( $columns );

		foreach ( $ids as $id ) {
			$row = [];

			foreach ( $columns as $column ) {
				$header = $column->get_name();

				if ( ! isset( $headers[ $header ] ) ) {
					continue;
				}

				$model = $column instanceof Exportable
					? $column->export()
					: new Model\RawValue( $column );

				if ( ! $model ) {
					continue;
				}

				$value = $model->get_value( $id );

				if ( null === $value && $column->is_original() ) {
					$value = $table->get_column_value( $column->get_name(), $id );
				}

				/**
				 * Filter the column value exported to CSV or another file format in the
				 * exportability add-on. This filter is applied to each value individually, i.e.,
				 * once for every column for every item in the list screen.
				 *
				 * @param string     $value                  Column value to export for item
				 * @param Column     $column                 Column object to export for
				 * @param int        $id                     Item ID to export for
				 * @param ListScreen $exportable_list_screen Exportable list screen instance
				 *
				 * @since 1.0
				 */
				$value = apply_filters( 'ac/export/value', $value, $column, $id, $this );

				$row[ $header ] = $value;
			}

			/**
			 * Filter the complete row. Allows to add extra columns to the exported file
			 *
			 * @param array      $row         Associative array of data for corresponding headers
			 * @param int        $id          Item ID to export for
			 * @param ListScreen $list_screen Exportable list screen instance
			 */
			$row = apply_filters( 'ac/export/row', $row, $id, $this );

			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * Retrieve the headers for the columns
	 *
	 * @param Column[] $columns
	 *
	 * @return string[] Associative array of header labels for the columns.
	 */
	protected function get_headers( array $columns ): array {
		$headers = [];

		foreach ( $columns as $column ) {
			$label = strip_tags( $column->get_setting( 'label' )->get_value() );

			if ( empty( $label ) ) {
				$label = $column->get_type();
			}

			$headers[ $column->get_name() ] = $label;
		}

		/**
		 * Filter to alter the headers. Allows to add extra headers to the exported file
		 *
		 * @param array      $headers     Associative array of data for corresponding headers
		 * @param ListScreen $list_screen Exportable list screen instance
		 */
		return apply_filters( 'ac/export/headers', $headers, $this );
	}

	/**
	 * @param Column[] $columns
	 *
	 * @return void
	 */
	private function save_columns_as_user_preference( array $columns ): void {
		$column_names = array_map(
			static function ( Column $column ) {
				return $column->get_name();
			},
			$columns );

		( new UserPreference\ExportedColumns() )->save( $this->list_screen->get_id(), $column_names );
	}

	/**
	 * Export a list of items, given the item IDs, and sends the output as JSON to the requesting
	 * AJAX process
	 *
	 * @param int[] $ids
	 */
	public function export( array $ids ): void {
		$ids = array_map( 'intval', $ids );

		$csv = '';

		$columns = $this->get_requested_columns();

		if ( ! $columns ) {
			wp_send_json_error( __( "No exportable columns found.", 'codepress-admin-columns' ) );
		}

		$this->save_columns_as_user_preference( $columns );

		$rows = $this->get_rows( $ids, $columns );

		if ( count( $rows ) > 0 ) {

			$exporter = new Exporter\CSV();
			$exporter->load_data( $rows );

			if ( $this->get_export_counter() === 0 ) {
				$exporter->load_column_labels( $this->get_headers( $columns ) );
			}

			$fh = fopen( 'php://memory', 'wb' );
			$exporter->export( $fh );
			$csv = stream_get_contents( $fh, -1, 0 );

			fclose( $fh );
		}

		wp_send_json_success( [
			'rows'               => $csv,
			'num_rows_processed' => count( $rows ),
		] );
	}

	/**
	 * Get the filtered number of items per iteration of the exporting algorithm
	 * @return int Number of items per export iteration
	 */
	public function get_num_items_per_iteration(): int {
		/**
		 * Filters the number of items to export per iteration of the exporting mechanism. It
		 * controls the number of items per batch, i.e., the number of items to process at once:
		 * the final number of items in the export file does not depend on this parameter
		 *
		 * @param int      $num_items Number of items per export iteration
		 * @param Strategy $this      Exportable list screen instance
		 */
		return (int) apply_filters( 'ac/export/exportable_list_screen/num_items_per_iteration', 250, $this );
	}

	/**
	 * @return int|null
	 */
	public function get_total_items(): ?int {
		$table = $this->get_list_table();

		return $table
			? $table->get_total_items()
			: null;
	}

}