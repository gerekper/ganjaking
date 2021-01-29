<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Core;


trait Reports {

    public function get_estimated_time_by_task_ids( $task_ids ) {
        global $wpdb;

        $task_ids     = empty( $task_ids ) ? [0] : $task_ids;
        $tb_tasks     = pm_tb_prefix() . 'pm_tasks';
        $tb_sub_tasks = pm_tb_prefix() . 'pm_tasks';
        $row_ids      = implode(',', $task_ids);

        $sql = "SELECT tk.id, tk.title, tk.estimation,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'id', '\"', ':' , '\"', IFNULL(sub.id, '') , '\"' ',',
                        '\"', 'title', '\"', ':' , '\"', IFNULL(sub.title, '') , '\"' ',',
                        '\"', 'estimation', '\"', ':' , '\"', IFNULL(sub.estimation, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as sub_tasks
            FROM $tb_tasks as tk
            LEFT JOIN $tb_sub_tasks as sub ON tk.id=sub.parent_id
            WHERE 1=1
            AND tk.id IN ($row_ids)
            GROUP BY (tk.id)";

        $results = $wpdb->get_results( $sql );

        foreach ( $results as $key => $result ) {
            $sub_tasks = explode( '|', $result->sub_tasks );
            $result->sub_tasks = [];

            foreach ( $sub_tasks as $key => $sub_task ) {
                $sub_task = str_replace('`', '"', $sub_task);
                $sub_task = json_decode( $sub_task );

                if ( empty( $sub_task->id ) ) {
                    continue;
                }

                $result->sub_tasks[] = $sub_task;
            }
        }

        $reports = [];

        foreach ( $results as $key => $result ) {
            $task['id'] = $result->id;
            $task['title'] = $result->title;

            if ( count($result->sub_tasks) > 0 ) {
                $all_estimations = wp_list_pluck( $result->sub_tasks, 'estimation' );
                $task['estimation'] = array_sum( $all_estimations );
            } else {
                $task['estimation'] = $result->estimation;
            }

            $reports[$result->id] = $task;
        }

        return $reports;
    }

    public function get_subtask_estimated_time_by_task_ids( $task_ids ) {
        $subtask_ids = $this->get_subtask_ids_from_task_ids( $task_ids );
        $subtask_ids = array_keys( $subtask_ids );

        global $wpdb;

        $tb_meta = pm_tb_prefix() . 'pm_meta';
        $row_ids = implode(',', $subtask_ids);

        $sql = "SELECT *
            FROM $tb_meta
            WHERE
            entity_type='task' AND meta_key IN('estimated_hours', 'estimated_minutes') AND entity_id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );
        $task_ids = [];

        foreach ( $results as $key => $result ) {
            $task_ids[$result->entity_id][] = $result;
        }

        foreach ( $task_ids as $task_id => $id_attrs ) {
            $time = 0;

            foreach ( $id_attrs as $id_key => $id_attr ) {

                if( $id_attr->meta_key == 'estimated_hours' ) {
                    $time = $time + $id_attr->meta_value*3600;
                }

                if( $id_attr->meta_key == 'estimated_minutes' ) {
                    $time = $time + $id_attr->meta_value*60;
                }
            }

            $task_ids[$task_id] = $time;
        }

        return $task_ids;
    }

    public function get_subtask_ids_from_task_ids( $task_ids ) {
        global $wpdb;

        $tb_task = pm_tb_prefix() . 'pm_tasks';
        $row_ids = implode(',', $task_ids);

        $sql = "SELECT id, parent_id
            FROM $tb_task
            WHERE
            parent_id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );

        $results = wp_list_pluck( $results, 'parent_id', 'id' );

        return $results;

    }

    public function get_estimated_time_by_subtask_ids( $subtask_ids ) {
        global $wpdb;

        $tb_meta = pm_tb_prefix() . 'pm_meta';
        $row_ids = implode(',', $subtask_ids);

        $sql = "SELECT *
            FROM $tb_meta
            WHERE
            entity_type='sub_task'
            AND
            meta_key IN('estimated_hours', 'estimated_minutes')
            AND entity_id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );
        $subtask_ids = [];

        foreach ( $results as $key => $result ) {
            $subtask_ids[$result->entity_id][] = $result;
        }

        foreach ( $subtask_ids as $task_id => $id_attrs ) {
            $time = 0;

            foreach ( $id_attrs as $id_key => $id_attr ) {

                if( $id_attr->meta_key == 'estimated_hours' ) {
                    $time = $time + $id_attr->meta_value*3600;
                }

                if( $id_attr->meta_key == 'estimated_minutes' ) {
                    $time = $time + $id_attr->meta_value*60;
                }
            }

            $subtask_ids[$task_id] = $time;
        }

        return $subtask_ids;
    }
}
