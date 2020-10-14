<?php namespace NinjaTables\Classes\Libs\Migrations;

class NinjaTablesUltimateTableMigration extends NinjaTablesMigration
{
	public function getTables() {
		global $wpdb;
		$tables = array();
		try {
			$tables = $wpdb->get_results( "SELECT id as ID,title as post_title FROM {$wpdb->prefix}ultimatetables", OBJECT );
		} catch (\Exception $exception) {
			
		}
		return $tables;
	}
	
	public function migrateTable($tableId) {
	    try {
			global $wpdb;
			$table = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ultimatetables WHERE id = {$tableId} LIMIT 1");
			if(!$table) {
				return new \WP_Error( 'broke', __('No Ultimate Table Found with the selected table', 'ninja-tables') );
			}
			$tableBody = htmlspecialchars_decode( esc_html( $table->ivalues ) );
			$items = explode( "t6r4ndt6r4ndt6r4ndt6r4ndt6r4ndt6r4ndt6r4ndkh6gfd57hgg", $tableBody );

			$columns = $table->width;
			$rows = $table->height;

			$tableRows = array();
			$counter = 0;
			foreach ( $items as $index => $item ) {
				if($index == ($rows * $columns ) + $columns ) {
					break;
				}
				if( ( $index % $columns ) == 0 ) {
					$counter++;
				}
				$tableRows[$counter][] = $item;
			}

			$headerRow = array_shift($tableRows);

			$headerRow = $this->formatHeader($headerRow);
			$formattedRows = $this->prepareTableRows(array_keys($headerRow), $tableRows);
			$formattedRows = array_reverse($formattedRows);
			$tableTitle = $table->title .' (Imported From Ultimate Table)';

			$ninjaTableId = $this->createTable($tableTitle);
			$this->initTableConfiguration($ninjaTableId, $headerRow);
			$this->addRows($ninjaTableId, $formattedRows);
			return $ninjaTableId;
		} catch (\Exception $exception) {
			return new \WP_Error( 'broke', $exception->getMessage() );
		}
	}
}