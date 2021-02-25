<?php namespace NinjaTablesPro;

class Sortable
{
    /**
     * Initialize the manual sortable for the table.
     */
    public static function init()
    {
        if (!ninja_table_admin_role()) {
            return;
        }

	    ninjaTablesValidateNonce();

        global $wpdb;

        $tableName = $wpdb->prefix.ninja_tables_db_table_name();

        static::migrateDatabaseIfNeeded($tableName);

        $tableId = intval($_REQUEST['table_id']);

        // The post meta table would have a flag that the data of
        // the table is migrated to use for the manual sorting.
        $dataMigrated = ninjaTablesDataMigratedForManualSort($tableId);

        $tableSettings = ninja_table_get_table_settings($tableId, 'admin');

        if (!$dataMigrated) {
            if ($tableSettings['default_sorting'] === 'old_first') {
                $orderBy = 'ASC';
            } else {
                $orderBy = 'DESC';
            }
            // Initialize the `position` value of the data to allow manual sort.
            $sql = "SET @p=0; ";
            $wpdb->query($sql);

            $sql = "UPDATE {$tableName} SET position= @p:= (@p+1) WHERE table_id = {$tableId} ORDER BY created_at {$orderBy};";
            $wpdb->query($sql);

            // Update the post meta table that the data of this
            // table is migrated to use for manual sorting.
	        $postMetaKey = '_ninja_tables_data_migrated_for_manual_sort';
            update_post_meta($tableId, $postMetaKey, true);
        } else {
        	static::recheckItemPositions($tableId);
        }

        // Finally update the table's sorting settings.
        $tableSettings['sorting_type'] = 'manual_sort';
        update_post_meta($tableId, '_ninja_table_settings', $tableSettings);

        if (!isset($_REQUEST['noData'])) {
            // Now return the data.
            (new \NinjaTablesAdmin)->getTableData();
        }
    }

    /**
     * Determine if the database is migrated to use for manual sorting.
     *
     * @param $tableName
     */
    protected static function migrateDatabaseIfNeeded($tableName)
    {
        // If the database is already migrated for manual
        // sorting the option table would have a flag.
        $option = '_ninja_tables_sorting_migration';
        $enabled = !!get_option($option);

        if (!$enabled) {
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

	    $orphans = $wpdb->get_results(
		    'SELECT * from '.$wpdb->prefix.ninja_tables_db_table_name().
		    ' WHERE (position IS NULL OR position = 0) AND table_id = '.$tableId
	    );

	    if ($orphans) {
		    $lastItem = ninja_tables_DbTable()->where('table_id', $tableId)->orderBy('position')->first();

		    foreach ($orphans as $index => $orphan) {
			    ninja_tables_DbTable()->where('id', $orphan->id)->update(array(
				    'position' => $lastItem->position + $index + 1
			    ));
		    }
	    }

	    // Now Recheck Position Duplications
        $duplicatePositions = $wpdb->get_results('SELECT position, COUNT(*) as position_count FROM '.$wpdb->prefix.ninja_tables_db_table_name().
            ' WHERE table_id = '.$tableId
            .' GROUP BY position HAVING position_count > 1;');

        if($duplicatePositions) {
            // Initialize the `position` value of the data to allow manual sort.
            $sql = "SET @p=0; ";
            $wpdb->query($sql);
            $tableName = $wpdb->prefix.ninja_tables_db_table_name();
            $sql = "UPDATE {$tableName} SET position= @p:= (@p+1) WHERE table_id = {$tableId} ORDER BY `position` ASC, `created_at` ASC;";
            $wpdb->query($sql);
        }
    }

    /**
     * Sort table items.
     */
    public static function sort()
    {
        if (!ninja_table_admin_role()) {
            return;
        }

	    ninjaTablesValidateNonce();

        global $wpdb;
        $tableName = ninja_tables_db_table_name();
        $tableNameWithPrefix = $wpdb->prefix.ninja_tables_db_table_name();

        $id = intval($_REQUEST['id']);
        $tableId = intval($_REQUEST['table_id']);
        $newPosition = intval($_REQUEST['newPosition']);
        $oldPosition = ninjaDB($tableName)->find($id)->position;

        // Initially make the target item's position `0`, so that
        // it doesn't get in the way of later sql queries.
        ninjaDB($tableName)->where('table_id', $tableId)->where('id', $id)->update(['position' => 0]);

        // If the new position where the item is to be moved
        // is less than its old position we'll increment
        // each item's position that falls between new
        // position and old position by 1
        if ($newPosition < $oldPosition) {
            $query = "UPDATE {$tableNameWithPrefix}
                      SET position = position + 1
                      WHERE table_id = %d
                      AND position BETWEEN %d AND %d
                      ORDER BY position DESC";

            $bindings = array(
                $tableId,
                $newPosition,
                $oldPosition,
            );
        }

        // If the new position where the item is to be moved
        // is greater than its old position we'll decrement
        // each item's position that falls between old
        // position and new position by 1
        elseif ($newPosition > $oldPosition) {
            $query = "UPDATE {$tableNameWithPrefix}
                      SET position = position - 1
                      WHERE table_id = %d
                      AND position BETWEEN %d AND %d
                      ORDER BY position ASC";

            $bindings = [
                $tableId,
                $oldPosition,
                $newPosition,
            ];
        }

        // Run the prepared query to batch update the
        // items that matches the sorting conditions.
        $wpdb->query($wpdb->prepare($query, $bindings));

        // Finally update the target item's position value with its intended position.
        ninjaDB($tableName)->where('id', $id)->update(['position' => $newPosition]);
	    ninjaTablesClearTableDataCache($tableId);
        // Now return the data.
        (new \NinjaTablesAdmin)->getTableData();
    }
}
