<?php

namespace NinjaTablesPro\App\Traits;

use NinjaTablesPro\App\Models\NinjaTableItem;

trait SortableTrait
{
    private static $tableName = 'ninja_table_items';


    /**
     * Determine if the database is migrated to use for manual sorting.
     *
     * @param $tableName
     */
    protected static function migrateDatabaseIfNeeded($tableName)
    {
        // If the database is already migrated for manual
        // sorting the option table would have a flag.
        $option  = '_ninja_tables_sorting_migration';
        $enabled = ! ! get_option($option);

        if ( ! $enabled) {
            global $wpdb;
            // Update the databse to hold the sorting position number.
            $sql = "ALTER TABLE $tableName ADD COLUMN `position` INT(11) AFTER `id`;";
            $wpdb->query($sql);
            // Keep a flag on the options table that the
            // db is migrated to use for manual sorting.
            add_option($option, true);
        }
    }

    /**
     * Check if there has any orphan items, if yes then increment the position from the last item position
     *
     * @param int $tableId
     */
    protected static function recheckItemPositions($tableId)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . static::$tableName;

        $orphans = $wpdb->get_results(
            'SELECT * from ' . $tableName .
            ' WHERE (position IS NULL OR position = 0) AND table_id = ' . $tableId
        );

        if ($orphans) {
            $lastItem = NinjaTableItem::where('table_id', $tableId)->orderBy('position')->first();

            foreach ($orphans as $index => $orphan) {
                NinjaTableItem::where('id', $orphan->id)->update(array(
                    'position' => $lastItem->position + $index + 1
                ));
            }
        }

        // Now Recheck Position Duplications
        $duplicatePositions = $wpdb->get_results('SELECT position, COUNT(*) as position_count FROM ' . $tableName .
                                                 ' WHERE table_id = ' . $tableId
                                                 . ' GROUP BY position HAVING position_count > 1;');

        if ($duplicatePositions) {
            // Initialize the `position` value of the data to allow manual sort.
            $sql = "SET @p=0; ";
            $wpdb->query($sql);
            $sql = "UPDATE {$tableName} SET position= @p:= (@p+1) WHERE table_id = {$tableId} ORDER BY `position` ASC, `created_at` ASC;";
            $wpdb->query($sql);
        }
    }
}
