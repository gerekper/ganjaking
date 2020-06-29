<?php

defined('WYSIJA') or die('Restricted access');

class WYSIJA_module_statistics_model extends WYSIJA_model {

    const STATS_DATE_OF_CREATION = 'STATS_DATE_OF_CREATION';
    /**
     * Time to live of stats data
     */
    const STATS_DATA_LIFE_TIME = 3600; // 1h * 60mins * 60s
    /**
     * Time to live of stats's table (structure)
     */
    const STATS_TABLE_LIFE_TIME = 86400; // 24h * 60mins * 60s

    const STATS_PREFIX = 'sc_'; // prefix of stat tables

    public function __construct() {
        parent::__construct();
        $this->clean_up_out_of_date_tables();
    }

    /**
     *
     * @param array $params array of input params
     */

    public function get_hash(Array $params) {
		$hash = md5(get_class($this) . json_encode($params));
        return substr($hash, 0, 8);// Only get 8 first characters to not make table name too long (less than 64)
    }

    /**
     * set a date of creating temporary tables, useful for caching
     * @return type
     */
    protected function set_date_of_creation() {
        $config = WYSIJA::get('config', 'model');
        return $config->save(array(self::STATS_DATE_OF_CREATION => time()));
    }

    /**
     * Check if cache life time is out of date
     * @return boolean
     */
    protected function is_data_out_of_date() {
        $config = WYSIJA::get('config', 'model');
        $date_of_creation = $config->getValue(self::STATS_DATE_OF_CREATION);
        return (time() - $date_of_creation >= self::STATS_DATA_LIFE_TIME);
    }

    /**
     * Get the list of tables which are out of date, based on create_time
     * @return type
     */
    protected function get_out_of_date_tables() {
        $query = '
            SELECT
                TABLE_NAME as table_name
            FROM
                INFORMATION_SCHEMA.TABLES
            WHERE
                TABLE_SCHEMA IN (SELECT DATABASE())
                AND (TABLE_NAME LIKE "[wysija]'.self::STATS_PREFIX.'%" OR TABLE_NAME LIKE "[wysija]stats_cache_%")
                AND TIMESTAMPDIFF(SECOND,CREATE_TIME, NOW()) >= '.self::STATS_TABLE_LIFE_TIME.';
            ';
        return $this->get_results($query);
    }

    /**
     * Auto cleanup out-of-date tables
     */
    protected function clean_up_out_of_date_tables() {
        $tables = $this->get_out_of_date_tables();
        if (!empty($tables) && is_array($tables)) {
            $_temp = array();
            foreach ($tables as $table)
                if (!empty($table['table_name']))
                    $_temp[] = $table['table_name'];
        }
        if (!empty($_temp)) {
            $query = 'DROP TABLE IF EXISTS `'. implode('`,`', $_temp).'`';
            $this->get_results($query);
        }
    }

    /**
     * Check if a table exists
     * @param string $table_name table name
     * @return boolean
     */
    protected function does_table_exists($table_name = null) {
        if (empty($table_name) OR !is_string($table_name))
            return false;
        $query = "SHOW TABLES LIKE '$table_name'";
        $result = $this->get_results($query);
        return !empty($result) ? true : false;
    }

    /**
     * Generate a table name, based on input params
     * @param type $params
     */
    protected function get_table_name($params) {
        $hash = $this->get_hash($params);
        return '[wysija]' . self::STATS_PREFIX . $hash;
    }

    /**
     * Generate a cached table
     * @param type $table_name
     * @param array $queries_create_table query to create a new cached table
     * @param array $queries_insert_data query to collect and insert data to the newly created/truncated table
     * @return boolean
     */
    protected function generate_table($table_name, Array $queries_create_table, Array $queries_insert_data) {
        $is_out_of_date = $this->is_data_out_of_date();
        $does_table_exists = $this->does_table_exists($table_name);

        if (!$is_out_of_date && $does_table_exists)
            return true;
        if ($does_table_exists) {
            $this->query('TRUNCATE TABLE `' . $table_name . '`');
        } else {
            foreach ($queries_create_table as $query_create_table)
                $this->query($query_create_table);
        }

        foreach ($queries_insert_data as $query_insert_data)
            $this->query($query_insert_data);

        if ($is_out_of_date) {
            $this->set_date_of_creation();
        }

        return true;
    }

}