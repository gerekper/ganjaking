<?php

namespace NinjaTablesPro\App\Http\Controllers;

use NinjaTables\App\Models\NinjaTableItem as TableItem;
use NinjaTables\Framework\Request\Request;
use NinjaTablesPro\App\Models\NinjaTableItem;
use NinjaTablesPro\App\Traits\SortableTrait;

class SortableController extends Controller
{
    use SortableTrait;

    public function init(Request $request)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . static::$tableName;

        static::migrateDatabaseIfNeeded($tableName);

        $tableId = intval($_REQUEST['table_id']);

        // The post meta table would have a flag that the data of
        // the table is migrated to use for the manual sorting.
        $dataMigrated = ninjaTablesDataMigratedForManualSort($tableId);

        $tableSettings = ninja_table_get_table_settings($tableId, 'admin');

        if ( ! $dataMigrated) {
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

        if ( ! isset($_REQUEST['noData'])) {
            $perPage        = isset($_REQUEST['per_page']) ? intval($_REQUEST['per_page']) : 10;
            $currentPage    = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
            $skip           = $perPage * ($currentPage - 1);
            $search         = esc_attr($_REQUEST['search']);
            $dataSourceType = ninja_table_get_data_provider($tableId);
            $data           = TableItem::getItems($tableId, $perPage, $currentPage, $skip, $search, $dataSourceType);

            wp_send_json($data, 200);

        }
    }

    public function store(Request $request)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . static::$tableName;

        $id          = intval($_REQUEST['id']);
        $tableId     = intval($_REQUEST['table_id']);
        $newPosition = intval($_REQUEST['newPosition']);
        $oldPosition = NinjaTableItem::find($id)->position;

        // Initially make the target item's position `0`, so that
        // it doesn't get in the way of later sql queries.
        NinjaTableItem::where('table_id', $tableId)->where('id', $id)->update(['position' => 0]);

        // If the new position where the item is to be moved
        // is less than its old position we'll increment
        // each item's position that falls between new
        // position and old position by 1
        if ($newPosition < $oldPosition) {
            $query = "UPDATE {$tableName}
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
            $query = "UPDATE {$tableName}
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
        NinjaTableItem::where('id', $id)->update(['position' => $newPosition]);
        ninjaTablesClearTableDataCache($tableId);
        // Now return the data.

        $perPage        = isset($_REQUEST['per_page']) ? intval($_REQUEST['per_page']) : 10;
        $currentPage    = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $skip           = $perPage * ($currentPage - 1);
        $search         = esc_attr($_REQUEST['search']);
        $dataSourceType = ninja_table_get_data_provider($tableId);
        $data           = TableItem::getItems($tableId, $perPage, $currentPage, $skip, $search, $dataSourceType);

       return $this->json($data, 200);
    }
}
