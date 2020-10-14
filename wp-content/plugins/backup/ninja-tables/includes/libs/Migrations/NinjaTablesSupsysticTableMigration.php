<?php namespace NinjaTables\Classes\Libs\Migrations;

use NinjaTables\Classes\ArrayHelper;

class NinjaTablesSupsysticTableMigration extends NinjaTablesMigration
{
	public function getTables() {
		global $wpdb;
		$tables = array();
		try {
			$tables = $wpdb->get_results( "SELECT id as ID,title as post_title FROM {$wpdb->prefix}supsystic_tbl_tables", OBJECT );
		} catch (\Exception $exception) {
			
		}
		
		return $tables;
	}
	
	public function migrateTable($tableId) {
	    try {
			global $wpdb;
			$table = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}supsystic_tbl_tables WHERE id = {$tableId} LIMIT 1");
			if(!$table) {
				return new \WP_Error( 'broke', __('No Table Found with the selected table', 'ninja-tables') );
			}
			
			$tableRows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}supsystic_tbl_rows WHERE table_id = {$tableId}");
			
			$rawHeaders = array_shift($tableRows);
		    $raw_header = @unserialize($rawHeaders->data);
		    $headerCells = ArrayHelper::get($raw_header, 'cells', array());

		    $headers = array();
		    foreach ($headerCells as $header_cell) {
		    	if($headerTitle = ArrayHelper::get($header_cell, 'fv', '')) {
				    $headers[] = $headerTitle;
			    } else {
				    $headers[] = ArrayHelper::get($header_cell, 'd', '');
			    }
		    }
		    
		    $rows = array();
		    foreach ($tableRows as $table_row) {
			    $cell =  @unserialize($table_row->data);
			    $rowValues = ArrayHelper::get($cell, 'cells', array());
			    if($rowValues) {
			    	$rowItem = array();
			    	foreach ($rowValues as $row_value) {
					    if($value = ArrayHelper::get($row_value, 'fv', '')) {
						    $rowItem[] = $value;
					    } else {
						    $rowItem[] = ArrayHelper::get($row_value, 'd', '');
					    }
				    }
				    $rows[] = $rowItem;
			    }
			    
		    }


		    $headerRow = $this->formatHeader($headers);
		    $formattedRows = $this->prepareTableRows(array_keys($headerRow), $rows);
		    
		    $tableTitle = $table->title .' (Imported From Supsystic Table)';

		    $ninjaTableId = $this->createTable($tableTitle);
		    $this->initTableConfiguration($ninjaTableId, $headerRow);
		    $this->addRows($ninjaTableId, $formattedRows);
		    return $ninjaTableId;
		} catch (\Exception $exception) {
			return new \WP_Error( 'broke', $exception->getMessage() );
		}
	}
}
