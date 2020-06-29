<?php

namespace ACP\Export;

use AC;
use AC\Column;
use AC\ListTable;
use AC\ListTableFactory;

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
	 * @var ExportDirectory
	 */
	protected $export_dir;

	/**
	 * @var ListTableFactory
	 */
	protected $list_table_factory;

	/**
	 * @var ExportableColumnFactory
	 */
	private $exportable_columns_factory;

	/**
	 * Perform all required actions for when an AJAX export is requested. The parent class (this
	 * class) will perform the necessary validation, and the inheriting class should implement
	 * the actual functionality for setting up the items to be exported. The parent class's (this
	 * class) `export` method can then be used to actually export the items
	 * @since 1.0
	 */
	abstract protected function ajax_export();

	/**
	 * @return ListTable
	 */
	abstract protected function get_list_table();

	/**
	 * Constructor
	 *
	 * @param AC\ListScreen $list_screen Associated Admin Columns list screen object
	 *
	 * @since 1.0
	 */
	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
		$this->export_dir = new ExportDirectory();
		$this->list_table_factory = new ListTableFactory();
		$this->exportable_columns_factory = new ExportableColumnFactory( $list_screen );
	}

	/**
	 * Callback for when the list screen is loaded in Admin Columns, i.e., when it is active. Child
	 * classes should implement this method for any setup-related functionality
	 * @since 1.0
	 */
	public function attach() {
		$this->maybe_ajax_export();
	}

	/**
	 * Check whether an AJAX export should be made, and validate the input data. Will call child's
	 * `ajax_export` method to do the actual exporting
	 * @since 1.0
	 */
	public function maybe_ajax_export() {
		// Check whether the user requested an export
		if ( 'acp_export_listscreen_export' !== filter_input( INPUT_GET, 'acp_export_action' ) ) {
			return;
		}

		if ( ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce' ), 'acp_export_listscreen_export' ) ) {
			return;
		}

		if ( $this->get_export_counter() === false ) {
			wp_send_json_error( __( 'Invalid value supplied for export counter.', 'codepress-admin-columns' ) );
		}

		if ( $this->get_export_hash() === false ) {
			wp_send_json_error( __( 'Invalid value supplied for export hash.', 'codepress-admin-columns' ) );
		}

		$this->ajax_export();
	}

	/**
	 * Get the counter value passed for the AJAX export
	 * @return int Counter value, or false if there is no valid counter value
	 * @since 1.0
	 */
	protected function get_export_counter() {
		$counter = (int) filter_input( INPUT_GET, 'acp_export_counter', FILTER_SANITIZE_NUMBER_INT );

		return $counter >= 0 ? $counter : false;
	}

	/**
	 * Get the hash value passed for the AJAX export
	 * @return string|bool Hash value, or false if there is no valid hash value
	 * @since 1.0
	 */
	protected function get_export_hash() {
		$hash = filter_input( INPUT_GET, 'acp_export_hash' );

		return $hash ?: false;
	}

	/**
	 * Get the Admin Columns list screen object associated with this object
	 * @return AC\ListScreen Associated Admin Columns list screen object
	 * @since 1.0
	 */
	public function get_list_screen() {
		return $this->list_screen;
	}

	/**
	 * @return Column[]
	 */
	private function get_exportable_columns() {
		return $this->exportable_columns_factory->create( $this->get_hidden_columns() );
	}

	/**
	 * Retrieve the rows to export based on a set of item IDs. The rows contain the column data to
	 * export for each item
	 *
	 * @param array [int] $items IDs of the items to export
	 *
	 * @return array[mixed] Rows to export. One row is returned for each item ID
	 * @since 1.0
	 */
	public function get_rows( $ids ) {
		$table = $this->get_list_table();

		$exportable_columns = $this->get_exportable_columns();

		// Construct CSV rows
		$rows = [];
		$headers = $this->get_headers( $exportable_columns );

		foreach ( $ids as $id ) {
			$row = [];

			foreach ( $exportable_columns as $column ) {
				$header = $column->get_name();

				if ( ! isset( $headers[ $header ] ) ) {
					continue;
				}

				$model = $column instanceof Exportable
					? $column->export()
					: new Model\RawValue( $column );

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

				// Add column to row data
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

			// Add current row to list of rows
			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * @return array
	 */
	private function get_hidden_columns() {
		return get_hidden_columns( $this->get_list_screen()->get_screen_id() );
	}

	/**
	 * Retrieve the headers for the columns
	 *
	 * @param Column[] $columns
	 *
	 * @return string[] Associative array of header labels for the columns.
	 */
	protected function get_headers( array $columns ) {
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
		$headers = apply_filters( 'ac/export/headers', $headers, $this );

		return $headers;
	}

	/**
	 * Export a list of items, given the item IDs, and sends the output as JSON to the requesting
	 * AJAX process
	 *
	 * @param array [int] $items Array of item IDs
	 *
	 * @since 1.0
	 */
	public function export( $ids ) {
		$ids = array_map( 'intval', $ids );

		// Retrieve list screen items and columns
		$rows = $this->get_rows( $ids );

		$exportable_columns = $this->get_exportable_columns();

		if ( count( $rows ) > 0 ) {
			// Create CSV exporter
			$exporter = new Exporter\CSV();
			$exporter->load_data( $rows );

			if ( $this->get_export_counter() === 0 ) {
				$exporter->load_column_labels( $this->get_headers( $exportable_columns ) );
			}

			// Base of file name path
			$fname = md5( get_current_user_id() . $this->get_export_hash() ) . '.csv';
			$fpath = $this->export_dir->get_path() . $fname;
			$fpath .= '-' . $this->get_export_counter() . '.csv';

			// Write CSV output to file
			$fh = fopen( $fpath, 'wb' );
			$exporter->export( $fh, true );
			fclose( $fh );
		}

		$download_url = add_query_arg( [
			'acp-export-download'        => $this->get_export_hash(),
			'acp-export-filename-prefix' => $this->get_list_screen()->get_label(),
		], admin_url( '/' ) );

		wp_send_json_success( [
			'num_rows_processed' => count( $rows ),
			'download_url'       => $download_url,
		] );
	}

	/**
	 * Get the filtered number of items per iteration of the exporting algorithm
	 * @return int Number of items per export iteration
	 * @since 1.0
	 */
	protected function get_num_items_per_iteration() {
		/**
		 * Filters the number of items to export per iteration of the exporting mechanism. It
		 * controls the number of items per batch, i.e., the number of items to process at once:
		 * the final number of items in the export file does not depend on this parameter
		 *
		 * @param int        $num_items              Number of items per export iteration
		 * @param ListScreen $exportable_list_screen Exportable list screen instance
		 *
		 * @since 1.0
		 */
		return apply_filters( 'ac/export/exportable_list_screen/num_items_per_iteration', 250, $this );
	}

}