<?php

namespace NinjaTable\FrontEnd\DataProviders;

class DefaultProvider
{
    public function boot()
    {
        add_filter('ninja_tables_get_table_default', array($this, 'getTableSettings'));
        add_filter('ninja_tables_fetching_table_rows_default', array($this, 'data'), 10, 6);
    }

    public function getTableSettings($table)
    {
        $table->isEditable = true;
        $table->dataSourceType = 'default';
        $table->isExportable = true;
        $table->isImportable = true;
        $table->isSortable = true;
        $table->isCreatedSortable = true;
        $table->hasCacheFeature = true;
        return $table;
    }

    public function data($data, $tableId, $defaultSorting, $limit = false, $skip = false, $ownOnly = false)
    {
        $advancedQuery = false;
        $disabledCache = false;

        if($skip || $limit || $ownOnly) {
            $advancedQuery = true;
        }

        // if cached not disabled then return cached data
        if( ! $advancedQuery && ! $disabledCache = ninja_tables_shouldNotCache($tableId) ) {
            $cachedData = get_post_meta($tableId, '_ninja_table_cache_object', true);
            if ($cachedData) {
                return $cachedData;
            }
        }

        $query = ninja_tables_DbTable()->where('table_id', $tableId);
        if ($defaultSorting == 'new_first') {
            $query->orderBy('created_at', 'desc');
        } else if ($defaultSorting == 'manual_sort') {
            $query->orderBy('position', 'asc');
        } else {
            $query->orderBy('created_at', 'asc');
        }

        $skip = intval($skip);
        if ($skip && $skip > 0) {
            $query->skip($skip);
        }

        $limit = intval($limit);
        if ($limit && $limit > 0) {
            $query->limit($limit);
        } else if($skip && $skip > 0) {
            $query->limit(99999);
        }

        if($ownOnly) {
            $query = apply_filters('ninja_table_own_data_filter_query', $query, $tableId);
        }

        global $wpdb;
        $items = $query->get();
        //dd($wpdb->last_query);
        foreach ($items as $item) {
            $values = json_decode($item->value, true);
            $values['___id___'] = $item->id;
            $data[] = $values;
        }
        
        // Please do not hook this filter unless you don't know what you are doing.
        // Hook ninja_tables_get_public_data instead.
        // You should hook this if you need to cache your filter modifications
        $data = apply_filters('ninja_tables_get_raw_table_data', $data, $tableId);

        if (!$advancedQuery && !$disabledCache) {
            update_post_meta($tableId, '_ninja_table_cache_object', $data);
        }

        return $data;
    }
}
