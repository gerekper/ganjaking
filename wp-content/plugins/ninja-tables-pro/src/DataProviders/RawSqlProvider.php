<?php

namespace NinjaTablesPro\DataProviders;


class RawSqlProvider
{
    public $provider = 'raw_sql';

    public function boot()
    {
        add_filter('ninja_tables_get_table_raw_sql', array($this, 'getTableSettings'), 10, 1);
        add_filter('ninja_tables_get_table_data_raw_sql', array($this, 'getTableData'), 10, 4);
        add_filter('ninja_tables_fetching_table_rows_raw_sql', array($this, 'data'), 10, 5);
        add_action('wp_ajax_ninja_tables_save_raw_sql_table', array($this, 'createTable'));
        add_action('wp_ajax_ninja_table_raw_sql_update_sql', array($this, 'updateSQL'));
        add_filter('ninja_table_activated_features', function ($features) {
            $features['raw_sql_query'] = true;
            return $features;
        });

        add_action('wp_ajax_ninja_table_raw_sql_remote_connection_details', array($this, 'getRemoteSQLDetails'));

        add_filter('ninja_table_sql_permission', array($this, 'hasSQLPermission'), 10, 1);
    }

    public function getDb($tableId = false, $connectionDetails = false)
    {
        if ($connectionDetails) {
            return $this->getRemoteDB($connectionDetails);
        }
        if($tableId) {
            // connetion type
            $connectionType = get_post_meta($tableId, '_ninja_tables_sql_connection_type', true);
            if($connectionType == 'external') {
                $connection = get_post_meta($tableId, '_ninja_tables_sql_connection_details', true);
                return $this->getRemoteDB($connection);
            }
        }
        global $wpdb;
        return $wpdb;
    }

    public function createTable()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }
	    ninjaTablesValidateNonce();

        $hasPermission = apply_filters('ninja_table_sql_permission', '');
        if ($hasPermission != 'yes') {
            wp_send_json_error([
                'message' => 'Sorry, You do not have permission to do this'
            ], 423);
        }

        $sql = wp_unslash($_REQUEST['sql']);

        $connectionType = 'local';
        if(isset($_REQUEST['connection_type'])) {
            $connectionType = $_REQUEST['connection_type'];
        }

        $connection_details = [];

        if ($connectionType == 'external') {
            $connection_details = $_REQUEST['connection_details'];
            $this->validateRemoteConnection($connection_details);
            $this->validateSql($sql, false, $connection_details);
        } else {
            // Validate the SQL and see if we have any data or not
            $this->validateSql($sql);
        }

        // Validate Title
        $messages = array();
        if (empty($_REQUEST['post_title'])) {
            $messages[] = __('The title field is required.', 'ninja-tables');
            wp_send_json_error(array('message' => $messages), 422);
            wp_die();
        }

        // Create The Table now
        $tableId = $this->saveTable();
        update_post_meta($tableId, '_ninja_table_raw_sql_query', $sql);
        update_post_meta($tableId, '_ninja_tables_data_provider', $this->provider);
        update_post_meta($tableId, '_ninja_tables_sql_connection_type', $connectionType);

        if($connectionType == 'external') {
            update_post_meta($tableId, '_ninja_tables_sql_connection_details', $connection_details);
        }


        $this->createColumns($tableId, $sql);


        wp_send_json_success(
            array('table_id' => $tableId)
        );

    }

    public function updateSQL()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }

	    ninjaTablesValidateNonce();

        $hasPermission = apply_filters('ninja_table_sql_permission', '');
        if ($hasPermission != 'yes') {
            wp_send_json_error([
                'message' => 'Sorry, You do not have permission to do this'
            ], 423);
        }

        $tableId = absint($_REQUEST['table_id']);

        if(isset($_REQUEST['connection_details'])) {

            $this->validateRemoteConnection($_REQUEST['connection_details']);

            update_post_meta($tableId, '_ninja_tables_sql_connection_details', $_REQUEST['connection_details']);
        }

        $sql = wp_unslash($_REQUEST['sql']);
        $this->validateSql($sql, $tableId);

        update_post_meta($tableId, '_ninja_table_raw_sql_query', $sql);

        $this->updateTableColumn($tableId, $sql);

        wp_send_json_success(array(
            'message' => 'SQL successfully updated'
        ), 200);
    }

    private function validateRemoteConnection($connection)
    {
        $connect = $this->getRemoteDB($connection);
        if (!$connect) {
            wp_send_json_error(array(
                'message' => array('Provided SQL Connection details is not valid'),
                'error'   => $connect
            ), 423);
            wp_die();
        }
        return $connect;
    }

    private function getRemoteDB($connection)
    {
        $connectionData = false;
        ob_start();
        $dbHost = $connection['db_host'] . ':' . $connection['db_host_port'];
        $connectionData = new \wpdb($connection['db_username'], $connection['db_userpassword'], $connection['db_name'], $dbHost);
        $errors = ob_get_clean();

        if ($errors) {
            return false;
        }

        return $connectionData;
    }

    public function validateSql($sql, $tableId = false, $connectionDetails = false)
    {
        $low_sql = strtolower($sql);
        $hasBadKeyWord = strpos($low_sql, 'delete ') !== false || strpos($low_sql, 'update ') !== false || strpos($low_sql, 'insert ') !== false;
        $hasSelect = strpos($low_sql, 'select ') !== false;
        if ($hasBadKeyWord || !$hasSelect) {
            wp_send_json_error(array(
                'message' => array('SQL is not valid'),
                'error'   => 'We could not validate your provided SQL. Please try another SQL.'
            ), 423);
        }

        $sql = $this->parseTableSQL($sql, $tableId);

        $db = $this->getDb($tableId, $connectionDetails);
        ob_start();
        $results = $db->get_results($sql);
        $errors = ob_get_clean();
        if ($errors) {
            wp_send_json_error(array(
                'message' => array('SQL is not valid'),
                'error'   => $errors
            ), 423);
            wp_die();
        }
        return true;
    }

    public function createColumns($tableId, $sql)
    {
        $sql = $this->parseTableSQL($sql, $tableId);
        // We have to create the columns and additional settings now
        $db = $this->getDb($tableId);
        $row = $db->get_row($sql);
        $fields = array();
        foreach ($row as $row_key => $row_value) {
            $fields[] = $row_key;
        }

        $columns = array();

        foreach ($fields as $key => $column) {
            $columns[] = array(
                'name'                => $column,
                'key'                 => $column,
                'breakpoints'         => null,
                'data_type'           => 'text',
                'dateFormat'          => null,
                'header_html_content' => null,
                'enable_html_content' => false,
                'contentAlign'        => null,
                'textAlign'           => null,
                'original_name'       => $column,
                'original_key'        => $column
            );
        }

        $tableSettings = ninja_table_get_table_settings($tableId, 'admin');
        update_post_meta($tableId, '_ninja_table_settings', $tableSettings);
        update_post_meta($tableId, '_ninja_table_columns', $columns);
    }

    public function updateTableColumn($tableId, $sql)
    {
        // We have to create the columns and additional settings now
        $db = $this->getDb($tableId);
        $sql = $this->parseTableSQL($sql, $tableId);
        $row = $db->get_row($sql);
        if (!$row) {
            if (!class_exists('\PHPSQLParser\PHPSQLParser')) {
                require_once NINJAPROPLUGIN_PATH . '/src/libs/load.php';
            }
            $parser = new \PHPSQLParser\PHPSQLParser($sql, true);
            $parsed = $parser->parsed;
            if (isset($parsed['WHERE'])) {
                unset($parsed['WHERE']);
            }
            $creator = new \PHPSQLParser\PHPSQLCreator();
            $sql = $creator->create($parsed);
            $row = $db->get_row($sql);
        }

        $columns = array();
        foreach ($row as $row_key => $row_value) {
            $columns[] = $row_key;
        }

        $existingColumns = get_post_meta($tableId, '_ninja_table_columns', true);
        $exisitingColumnKeys = array();
        foreach ($existingColumns as $column) {
            $exisitingColumnKeys[$column['original_key']] = $column;
        }
        $formattedColumns = array();
        foreach ($columns as $key => $column) {
            if (isset($exisitingColumnKeys[$column])) {
                $exisitingColumnKeys[$column]['original_key'] = $column;
                $exisitingColumnKeys[$column]['key'] = $column;
                $exisitingColumnKeys[$column]['original_name'] = $column;
                $formattedColumns[] = $exisitingColumnKeys[$column];
            } else {
                $formattedColumns[$key] = array(
                    'name'                => $column,
                    'key'                 => $column,
                    'breakpoints'         => null,
                    'data_type'           => 'text',
                    'dateFormat'          => null,
                    'header_html_content' => null,
                    'enable_html_content' => false,
                    'contentAlign'        => null,
                    'textAlign'           => null,
                    'original_name'       => $column,
                    'original_key'        => $column
                );
            }
        }
        update_post_meta($tableId, '_ninja_table_columns', $formattedColumns);
    }

    public function getTableSettings($table)
    {
        $connectionType = get_post_meta($table->ID, '_ninja_tables_sql_connection_type', true);
        if(!$connectionType) {
            $connectionType = 'local';
        }
        $table->isEditable = false;
        $table->dataSourceType = get_post_meta($table->ID, '_ninja_tables_data_provider', true);
        $table->isEditableMessage = ' to edit your SQL query please click here';
        $table->isExportable = true;
        $table->isImportable = false;
        $table->isSortable = false;
        $table->hasCacheFeature = false;
        $table->isCreatedSortable = false;
        $table->hasExternalCachingInterval = false;
        $table->sql = get_post_meta($table->ID, '_ninja_table_raw_sql_query', true);
        $table->connection_type = $connectionType;
        return $table;
    }

    private function parseTableSQL($sql, $tableID = false)
    {
        $db = $this->getDb($tableID);
        $prefix = (property_exists($db, 'prefix')) ? $db->prefix : '';
        $sqlPlaceHolders = apply_filters('ninja_table_raw_sql_placeholders', array(
            '{current_user_id}'   => get_current_user_id(),
            '{current_date}'      => date('Y-m-d'),
            '{prefix}'            => $prefix,
            '{current_date_time}' => date('Y-m-d H:i:s')
        ), $tableID);
        $parsedSQL = str_replace(array_keys($sqlPlaceHolders), array_values($sqlPlaceHolders), $sql);

        $parsedSQL = apply_filters('ninja_parse_placeholder', $parsedSQL);

        return apply_filters('ninja_table_raw_sql_parsed', $parsedSQL, $sql, $tableID);
    }

    public function getTableData($data, $tableId, $perPage, $offset)
    {
        $newData = array();
        $sql = get_post_meta($tableId, '_ninja_table_raw_sql_query', true);
        $sql = $this->parseTableSQL($sql, $tableId);
        $db = $this->getDb($tableId);
        ob_start();
        $results = $db->get_results($sql);
        $errors = ob_get_clean();

        if ($errors) {
            $results = array();
        }

        $totalData = count($results);

        $responseData = array_slice($results, $offset, $perPage);

        foreach ($responseData as $key => $value) {
            $newData[] = array(
                'id'       => $key + 1,
                'values'   => $value,
                'position' => $key + 1,
            );
        }
        return array(
            $newData,
            $totalData
        );
    }

    public function data($data, $tableId, $defaultSorting, $limitEntries = false, $skip = false)
    {
        $sql = get_post_meta($tableId, '_ninja_table_raw_sql_query', true);
        $sql = $this->parseTableSQL($sql, $tableId);
        $db = $this->getDb($tableId);
        $limitEntries = intval($limitEntries);
        $skip = intval($skip);

        ob_start();
        $results = $db->get_results($sql, ARRAY_A);
        $errors = ob_get_clean();
        if ($errors) {
            $results = array();
        }

        if ($limitEntries || $skip) {
            $results = array_slice($results, $skip, $limitEntries);
        }

        return $results ? $results : $data;
    }

    protected function saveTable($postId = null)
    {
        $attributes = array(
            'post_title'   => sanitize_text_field($this->get($_REQUEST, 'post_title')),
            'post_content' => wp_kses_post($this->get($_REQUEST, 'post_content')),
            'post_type'    => 'ninja-table',
            'post_status'  => 'publish'
        );

        if (!$postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = $postId;
            wp_update_post($attributes);
        }
        return $postId;
    }

    protected function get($array, $key, $default = false)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }

    public function returnFalse()
    {
        return false;
    }

    public function hasSQLPermission($status = false)
    {
        if ($status == 'yes') {
            return $status; // Already admin so further check required
        }

        $permission = get_option('_ninja_tables_sql_permission');

        if ($permission == 'yes') {
            return 'yes';
        }

        if (current_user_can('manage_options')) {
            return 'yes';
        }

        return 'no';
    }


    public function getRemoteSQLDetails()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }

	    ninjaTablesValidateNonce();

        $hasPermission = apply_filters('ninja_table_sql_permission', '');
        if ($hasPermission != 'yes') {
            wp_send_json_error([
                'message' => 'Sorry, You do not have permission to do this'
            ], 423);
        }

        $tableId = intval($_REQUEST['table_id']);

        $connectionDetails = get_post_meta($tableId, '_ninja_tables_sql_connection_details', true);

        wp_send_json_success(array(
            'connection_details' => $connectionDetails
        ));
    }
}
