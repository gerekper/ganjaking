<?php
namespace NinjaTables\Classes;

use NinjaTables\Classes\Libs\Migrations\NinjaTablesSupsysticTableMigration;
use NinjaTables\Classes\Libs\Migrations\NinjaTablesTablePressMigration;
use NinjaTables\Classes\Libs\Migrations\NinjaTablesUltimateTableMigration;
use NinjaTables\Libs\CSVParser\CSVParser;

class NinjaTableImport
{
    private $cpt_name = 'ninja-table';

    public function importTable()
    {
        $format = $_REQUEST['format'];

        if ($format == 'csv') {
            $this->uploadTableCsv();
        } elseif ($format == 'json') {
            $this->uploadTableJson();
        } elseif ($format == 'ninjaJson') {
            $this->uploadTableNinjaJson();
        }

        wp_send_json(array(
            'message' => __('No appropriate driver found for the import format.', 'ninja-tables')
        ), 423);
    }

    public function getTablesFromPlugin()
    {
        $plugin = sanitize_text_field($_REQUEST['plugin']);
        $libraryClass = false;

        if ($plugin == 'UltimateTables') {
            $libraryClass = new NinjaTablesUltimateTableMigration();
        } elseif ($plugin == 'TablePress') {
            $libraryClass = new NinjaTablesTablePressMigration();
        } elseif ($plugin == 'supsystic') {
            $libraryClass = new NinjaTablesSupsysticTableMigration();
        } else {
            return false;
        }
        $tables = $libraryClass->getTables();

        wp_send_json(array(
            'tables' => $tables
        ), 200);
    }

    public function importTableFromPlugin()
    {
        $plugin = esc_attr($_REQUEST['plugin']);
        $tableId = intval($_REQUEST['tableId']);

        if ($plugin == 'UltimateTables') {
            $libraryClass = new NinjaTablesUltimateTableMigration();
        } elseif ($plugin == 'TablePress') {
            $libraryClass = new NinjaTablesTablePressMigration();
        } elseif ($plugin == 'supsystic') {
            $libraryClass = new NinjaTablesSupsysticTableMigration();
        } else {
            return false;
        }

        $tableId = $libraryClass->migrateTable($tableId);
        if (is_wp_error($tableId)) {
            wp_send_json_error(array(
                'message' => $tableId->get_error_message()
            ), 423);
        }

        $message = __(
            'Successfully imported. Please go to all tables and review your newly imported table.',
            'ninja-tables'
        );

        wp_send_json_success(array(
            'message' => $message,
            'tableId' => $tableId
        ), 200);
    }

    private function storeTableConfigWhenImporting($tableId, $header)
    {
        // ninja_table_columns
        $ninjaTableColumns = array();

        foreach ($header as $key => $name) {
            $ninjaTableColumns[] = array(
                'key' => $key,
                'name' => $name,
                'breakpoints' => ''
            );
        }
        update_post_meta($tableId, '_ninja_table_columns', $ninjaTableColumns);
        // ninja_table_settings
        $ninjaTableSettings = ninja_table_get_table_settings($tableId, 'admin');
        update_post_meta($tableId, '_ninja_table_settings', $ninjaTableSettings);
        ninjaTablesClearTableDataCache($tableId);
    }

    private function uploadTableCsv()
    {
        $mimes = array(
            'text/csv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt'
        );

        if (!in_array($_FILES['file']['type'], $mimes)) {
            wp_send_json_error(array(
                'errors' => array(),
                'message' => __('Please upload valid CSV', 'ninja-tables')
            ), 423);
        }

        $tmpName = $_FILES['file']['tmp_name'];
        $fileName = sanitize_text_field($_FILES['file']['name']);

        $data = file_get_contents($tmpName);
        if (isset($_REQUEST['do_unicode']) && $_REQUEST['do_unicode'] == 'yes') {
            $data = utf8_encode($data);
        }

        try {
            $reader = \League\Csv\Reader::createFromString($data)->fetchAll();
        } catch (\Exception $exception) {
            wp_send_json_error(array(
                'errors' => $exception->getMessage(),
                'message' => __('Something is wrong when parsing the csv', 'ninja-tables')
            ), 423);
        }

        $header = array_shift($reader);

        $tableId = $this->createTable(array(
            'post_title' => $fileName,
            'post_content' => '',
            'post_type' => $this->cpt_name,
            'post_status' => 'publish'
        ));

        $header = ninja_table_format_header($header);

        $this->storeTableConfigWhenImporting($tableId, $header);

        ninjaTableInsertDataToTable($tableId, $reader, $header);

        wp_send_json(array(
            'message' => __('Successfully added a table.', 'ninja-tables'),
            'tableId' => $tableId
        ));
    }

    private function uploadTableJson()
    {
        $tableId = $this->createTable();

        $tmpName = $_FILES['file']['tmp_name'];

        $content = json_decode(file_get_contents($tmpName), true);


        $header = array_keys(array_pop(array_reverse($content)));

        $formattedHeader = array();
        foreach ($header as $head) {
            $formattedHeader[$head] = $head;
        }

        $this->storeTableConfigWhenImporting($tableId, $formattedHeader);

        ninjaTableInsertDataToTable($tableId, $content, $formattedHeader);

        wp_send_json(array(
            'message' => __('Successfully added a table.', 'ninja-tables'),
            'tableId' => $tableId
        ));
    }

    private function uploadTableNinjaJson()
    {
        $tmpName = $_FILES['file']['tmp_name'];

        $parsedContent = file_get_contents($tmpName);

        $content = json_decode($parsedContent, true);

        if (json_last_error()) {
            for ($i = 0; $i <= 31; ++$i) {
                $parsedContent = str_replace(chr($i), "", $parsedContent);
            }
            $parsedContent = str_replace(chr(127), "", $parsedContent);
            if (0 === strpos(bin2hex($parsedContent), 'efbbbf')) {
                $parsedContent = substr($parsedContent, 3);
            }
            $content = json_decode($parsedContent, true);
        }

        // validation
        if (!$content['post'] || !$content['columns'] || !$content['settings']) {
            wp_send_json(array(
                'message' => __('You have a faulty JSON file. Please export a new one.',
                    'ninja-tables')
            ), 423);
        }

        $tableAttributes = array(
            'post_title' => sanitize_title($content['post']['post_title']),
            'post_content' => wp_kses_post($content['post']['post_content']),
            'post_type' => $this->cpt_name,
            'post_status' => 'publish'
        );

        $tableId = $this->createTable($tableAttributes);

        update_post_meta($tableId, '_ninja_table_columns', $content['columns']);

        update_post_meta($tableId, '_ninja_table_settings', $content['settings']);

        $metas = $content['metas'];
        foreach ($metas as $meta_key => $meta_value) {
            update_post_meta($tableId, $meta_key, $meta_value);
        }

        if ($rows = $content['rows']) {
            $header = [];
            foreach ($content['columns'] as $column) {
                $header[$column['key']] = $column['name'];
            }
            ninjaTableInsertDataToTable($tableId, $rows, $header);
        }

        if (isset($content['original_rows']) && $originalRows = $content['original_rows']) {
            foreach ($originalRows as $row) {
                $row['table_id'] = $tableId;
                $row['value'] = json_encode($row['value'], JSON_UNESCAPED_UNICODE);
                ninja_tables_DbTable()->insert($row);
            }
        }

        wp_send_json(array(
            'message' => __('Successfully added a table.', 'ninja-tables'),
            'tableId' => $tableId
        ));
    }

    private function createTable($data = null)
    {
        return wp_insert_post($data
            ? $data
            : array(
                'post_title' => __('Temporary table name', 'ninja-tables'),
                'post_content' => __('Temporary table description',
                    'ninja-tables'),
                'post_type' => $this->cpt_name,
                'post_status' => 'publish'
            ));
    }

    public function uploadData()
    {
        $tableId = intval($_REQUEST['table_id']);
        $tmpName = $_FILES['file']['tmp_name'];

        $data = file_get_contents($tmpName);
        if (isset($_REQUEST['do_unicode']) && $_REQUEST['do_unicode'] == 'yes') {
            $data = utf8_encode($data);
        }

        try {
            $reader = \League\Csv\Reader::createFromString($data)->fetchAll();
        } catch (\Exception $exception) {
            wp_send_json_error(array(
                'errors' => $exception->getMessage(),
                'message' => __('CSV File is not valid', 'ninja-tables')
            ), 423);
        }

        $csvHeader = array_shift($reader);
        $csvHeader = array_map('esc_attr', $csvHeader);

        $config = get_post_meta($tableId, '_ninja_table_columns', true);

        if (!$config) {
            wp_send_json(array(
                'message' => __('Please set table configuration first', 'ninja-tables')
            ), 423);
        }

        // Extract header keys to a plain array from the config.
        $header = array_map(function ($item) {
            return $item['key'];
        }, $config);

        // We are gonna allow to upload new data if the CSV
        // has the same number of headers as the config.
        if (count($header) != count($csvHeader)) {
            wp_send_json(array(
                'errors'  => array(),
                'message' => __('Please use the provided CSV header structure font face.', 'ninja-tables')
            ), 423);
        }

        $data = array();

        $userId = get_current_user_id();
        $timeStamp = time() - ( count($reader) * 100 );
        foreach ($reader as $item) {
            $itemTemp = array_combine($header, $item);
            array_push($data, array(
                'table_id' => $tableId,
                'attribute' => 'value',
                'owner_id' => $userId,
                'value' => json_encode($itemTemp, JSON_UNESCAPED_UNICODE),
                'created_at' => date('Y-m-d H:i:s', $timeStamp),
                'updated_at' => date('Y-m-d H:i:s')
            ));
            $timeStamp = $timeStamp + 100;
        }

        $replace = $_REQUEST['replace'] === 'true';

        $data = apply_filters('ninja_tables_import_table_data', $data, $tableId);

        if ($replace) {
            ninja_tables_DbTable()->where('table_id', $tableId)->delete();
        }

        // We are gonna batch insert by small chunk so that we can avoid PHP
        // memory issue or MYSQL max_allowed_packet issue for large data set.
        global $wpdb;
        $tableName = $wpdb->prefix . ninja_tables_db_table_name();
        foreach (array_chunk($data, 3000) as $chunk) {
            ninjtaTableBatchInsert($tableName, $chunk);
        }

        ninjaTablesClearTableDataCache($tableId);

        wp_send_json(array(
            'message' => __('Successfully uploaded data.', 'ninja-tables')
        ));
    }
}