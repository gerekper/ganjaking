<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.2
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Cache class used to implement cache mechanisms in MySQL table.
 *
 * @since 1.0.0
 *
 **/
class Cache {

    /**
     * Cache life time in seconds.
     *
     * @var int
     * @since 1.0.0
     **/
    private $cache_time;

    /**
     * Cache table name.
     *
     * @var string
     * @since 1.0.0
     **/
    private $table_name;

    /**
     * Initialize a new cache instance.
     *
     * @param int|null $cache_time - Life time for cache in seconds.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function __construct( $cache_time = null ) {

        /** Initialize cache time. */
        $this->set_cache_time( $cache_time );

        /** Prepare cache table name. */
        $this->table_name = $this->get_cache_table_name();

        /** Create cache table if needed. */
        if ( ! $this->table_exist( $this->table_name ) ) {

            $this->create_cache_table( $this->table_name, ['key' => 'text', 'data' => 'longtext'] );

        }

    }

    /**
     * Initialize cache time.
     *
     * @param int $cache_time - Cache lifetime in seconds.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    private function set_cache_time( $cache_time ) {

        /** Set cache time. */
        if ( null === $cache_time ) {

            /** Default cache lifetime is 120 hours (5 days). */
            $this->cache_time = 120 * HOUR_IN_SECONDS;

        } else {

            $this->cache_time = $cache_time;

        }

    }

    /**
     * Create cache table name from plugin slug.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
    private function get_cache_table_name() {

        global $wpdb;

        /** Convert plugin slug to table name. */
        $table_name = str_replace( '-', '_', Plugin::get_slug() );

        /** And add 'cache' to the end. */
        $table_name .= '_cache';

        return esc_sql( $wpdb->prefix . $table_name );

    }

    /**
     * Check if table already exist.
     *
     * @param string $table_name - Full table name.
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool
     **/
    private function table_exist( $table_name ) {

        global $wpdb;

        return (bool)$wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ) );

    }

    /**
     * Create cache table.
     *
     * @param string $table_name - Full table name.
     * @param array $columns - Custom table columns.
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool - Boolean true on success, false on error.
     **/
    private function create_cache_table( $table_name, $columns ) {

        global $wpdb;

        $columns['updated_at'] = 'int(12)';
        $columns_query = '';

        foreach ( $columns as $key => $type ) {
            $columns_query .= '`' . $key . '` ' . $type . ' NOT NULL,';
        }

        /** @noinspection SqlNoDataSourceInspection */
        return $wpdb->query(
            "CREATE TABLE $table_name ( `id` int( 11 ) unsigned NOT NULL AUTO_INCREMENT, $columns_query PRIMARY KEY ( `id` ) ) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;"
        );

    }

    /**
     * Store value in cache table.
     *
     * @param string $key - Name for data you want to cache.
     * @param mixed $data - Data to cache.
     * @param bool $merge - Flag to merge data with previous value.
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool - Boolean true on success, false on error.
     **/
    public function set( $key, $data, $merge = false ) {

        /** No $data nothing to cache. */
        if ( empty( $data ) ) { return false; }

        $data = $merge ? $this->merge( $data, $this->get( $key, false ) ) : $data;
        $data = is_array( $data ) ? json_encode( $data ) : $data;

        return (bool)$this->row_update(
            $this->table_name,
            [
                'key' => $key,
                'data' => $data
            ],
            ['key' => $key]
        );

    }

    /**
     * Get cached value.
     *
     * @param string $key - Name for data you need from cache.
     * @param bool $check_expire - Flag to check expire time for record.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string|null - Cached value or null on error.
     **/
    public function get( $key, $check_expire = true ) {

        $cache = $this->row_get( $this->table_name, ['key' => $key] );

        if ( ! $cache || ( $check_expire && time() > $cache['updated_at'] + $this->cache_time ) ) { return null; }

        return $cache['data'];

    }

    /**
     * Check cached value not expired.
     *
     * @param string $key - Name for data you need from cache.
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool - True if cache is expired, false otherwise.
     **/
    public function expired( $key ) {

        $cache = $this->row_get($this->table_name, ['key' => $key]);

        return !$cache || ( $cache && time() > $cache['updated_at'] + $this->cache_time );

    }

    private function row_get( $table_name, $where ) {

        global $wpdb;

        list ( $key, $value ) = $this->get_key_value( $where );

        /** @noinspection SqlNoDataSourceInspection */
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE `$key` = %s",
            esc_sql( $value )
        ), ARRAY_A );

    }

    private function get_key_value( $array ) {

        $keys = array_keys( $array );
        $values = array_values( $array );

        return [$keys[0], $values[0]];

    }

    private function row_update( $table_name, $data, $where ) {

        global $wpdb;

        $data['updated_at'] = time();

        if ( $this->row_exist( $table_name, $where ) ) {
            $status = $wpdb->update(
                $table_name,
                $data,
                $where
            );
        } else {
            $status = $wpdb->insert(
                $table_name,
                $data
            );
        }

        return (bool)$status;

    }

    private function row_exist( $table_name, $where ) {

        global $wpdb;

        list ( $key, $value ) = $this->get_key_value( $where );

        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        return (bool)$wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE `$key` = %s",
            esc_sql( $value )
        ) );

    }

    private function unique_sort( $array, $key ) {

        $result = [];

        $array_unique = [];

        $ids = [];
        $timestamps = [];

        foreach ( $array as $item ) {

            if ( ! in_array($item['id'], $ids, true ) ) {

                $ids_unique[] = $item['id'];
                $array_unique[$item['id']] = $item;
                $timestamps[$item['id']] = $item[$key];

            }

        }

        arsort( $timestamps );

        foreach ( $timestamps as $id => $node ) {
            $result[] = $array_unique[$id];
        }

        return $result;

    }

    private function merge( $data, $cache_data_json ) {

        if ( empty( $cache_data_json ) || empty( $data ) ) {
            return $data;
        }

        $cache_data = json_decode( $cache_data_json, true );

        return $this->unique_sort( array_merge_recursive( $data, $cache_data ), 'created_time' );

    }

    /**
     * Remove cache table from DB.
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool - Boolean true on success, false on error.
     **/
    public function drop_cache_table() {

        global $wpdb;

        /** @noinspection SqlNoDataSourceInspection */
        return $wpdb->query( "DROP TABLE IF EXISTS {$this->table_name}" );

    }

}
