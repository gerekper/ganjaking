<?php namespace NinjaTables\Classes\Libs\Migrations;
abstract class NinjaTablesMigration {
	
	protected $cpt_name = 'ninja-table';
	
	abstract function getTables();
	
	abstract function migrateTable($tableId);

	/**
	 * Create  a table post type
	 * @param string $title
	 * @param string $description
	 *
	 * @return int|WP_Error
	 */
	public function createTable($title = '', $description = '') {
		if(!$title) {
			$title =  __( 'Temporary table name', 'ninja-tables' );
		}
		if(!$description) {
			$description = __( 'Temporary table description',
				'ninja-tables' );
		}
		
		return wp_insert_post( array(
				'post_title'   =>$title,
				'post_content' => $description,
				'post_type'    => $this->cpt_name,
				'post_status'  => 'publish'
			 ));
	}
	
	public function addRows($tableId, $rows) {
		$time = current_time( 'mysql' );
		foreach ($rows as $row) {
			$data = array(
				'table_id'   => $tableId,
				'attribute'  => 'value',
				'value'      => json_encode($row),
				'created_at' => $time,
				'updated_at' => $time
			);
			ninja_tables_DbTable()->insert($data);
		}
	}
	
	public function initTableConfiguration($tableId, $headers) {
		// ninja_table_columns
		$ninjaTableColumns = array();
		foreach ( $headers as $key => $name ) {
			$ninjaTableColumns[] = array(
				'key'         => $key,
				'name'        => $name,
				'breakpoints' => '',
				'data_type' => 'text'
			);
		}
		update_post_meta( $tableId, '_ninja_table_columns',
			$ninjaTableColumns );

		// ninja_table_settings
		$ninjaTableSettings = ninja_table_get_table_settings( $tableId,
			'admin' );
		update_post_meta( $tableId, '_ninja_table_settings',
			$ninjaTableSettings );
		ninjaTablesClearTableDataCache( $tableId );
	}
	

	/**
	 * Create Table Header as an array where array keys will be sanitized and formatted column keys
	 * @param $header array
	 *
	 * @return array
	 */
	public function formatHeader( $header ) {
		$data = array();
		$column_counter = 1;
		foreach ( $header as $item ) {
			$item = trim( strip_tags( $item ) );

			// We'll slugify only if item is printable characters.
			// Otherwise we'll generate custom key for the item.
			// Printable chars as in ASCII printable chars.
			// Ref: http://www.catonmat.net/blog/my-favorite-regex/

			$key = preg_replace('/[^A-Za-z0-9]+/', '', $item);
			
			$key = sanitize_title( $key, 'ninja_column_' . $column_counter, 'display' );
			
			$counter = 1;
			while ( isset( $data[ $key ] ) ) {
				$key .= '_' . $counter;
				$counter ++;
			}
			$data[ $key ] = $item;

			$column_counter ++;
		}
		return $data;
	}
	
	/**
	 * Prepare the table Rows combining with $headerKeys
	 * @param $headerKeys array
	 * @param $rows array
	 *
	 * @return array
	 */
	public function prepareTableRows($headerKeys, $rows) {
		$formattedRows = array();
		foreach ($rows as $row) {
			if(count($headerKeys) == count($row)) {
				$formattedRows[] = array_combine($headerKeys, $row);
			}
		}
		return $formattedRows;
	}
}

