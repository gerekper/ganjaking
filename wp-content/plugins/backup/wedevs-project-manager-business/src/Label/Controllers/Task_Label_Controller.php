<?php
namespace WeDevs\PM_Pro\Label\Controllers;

use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM_Pro\Label\Models\Task_Label_Task;
use League\Fractal\Resource\Item as Item;
use WeDevs\PM_Pro\Label\Transformers\Label_Transformer;


class Task_Label_Controller {

    use Transformer_Manager;

    private static $_instance;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function after_create_task( $task, $params ) {
        if ( ! isset( $params['task_labels'] ) ) {
            return;
        }

        $label_ids = empty( $params['task_labels'] ) ? [] : $params['task_labels'];
        $task_id = $task->id;

        self::getInstance()->store( $label_ids, $task_id );
    }

    public function store( $label_ids, $task_id ) {

        foreach ( $label_ids as $key => $label_id ) {
            Task_Label_Task::create(
                [
                    'task_id' => $task_id,
                    'label_id' => $label_id
                ]
            );
        }
    }

    public function update( $task_id, $label_ids ) {
        if ( ! is_array( $label_ids ) ) {
            $label_ids = [$label_ids];
        }

        $task_label_tasks = Task_Label_Task::where( 'task_id', $task_id )
            ->get()
            ->toArray();

        if( count( $label_ids ) == 1 && count( $task_label_tasks ) == 1 ) {
            if ( $label_ids[0] == $task_label_tasks[0] ) {
                $this->delete( $task_id, $label_ids );
            }
        }

        $db_label_ids      = wp_list_pluck( $task_label_tasks, 'label_id' );
        $deleted_label_ids = array_diff( $db_label_ids, $label_ids );

        if ( ! empty( $deleted_label_ids ) ) {
            $this->delete( $task_id, $deleted_label_ids );
        }

        $insert_label_ids = array_diff( $label_ids, $db_label_ids );

        if ( ! empty( $insert_label_ids ) ) {
            $this->store( $insert_label_ids, $task_id );
        }

        return true;
    }

    public function delete( $task_ids = [], $label_ids = [] ) {
        $task_ids = ! is_array( $task_ids ) ? [$task_ids] : $task_ids;
        $label_ids = ! is_array( $label_ids ) ? [$label_ids] : $label_ids;

        if ( is_array( $label_ids ) && is_array( $task_ids ) ) {

            Task_Label_Task::whereIn( 'label_id', $label_ids)
                ->whereIn( 'task_id', $task_ids )
                ->delete();
        }

        if ( is_array( $label_ids ) && empty( $task_ids ) ) {
            Task_Label_Task::whereIn( 'label_id', $label_ids)
                ->delete();
        }

        if ( is_array( $task_ids ) && empty( $label_ids ) ) {
            Task_Label_Task::whereIn( 'task_id', $task_ids)
                ->delete();
        }


        // if ( intval( $label_ids ) && intval( $task_ids ) ) {

        //     Task_Label_Task::where( 'label_id', $label_ids)
        //         ->where( 'task_id', $task_ids )
        //         ->delete();

        // }

        // if ( intval( $label_ids ) && empty( $task_ids ) ) {
        //     Task_Label_Task::where( 'label_id', $label_ids)
        //         ->delete();

        // }

        // if ( intval( $task_ids ) && empty( $label_ids ) ) {
        //     Task_Label_Task::where( 'task_id', $task_ids)
        //         ->delete();

        //}

        return true;
    }

    public static function task_model_labels( $self ) {
        return $self;
        return $self->belongsToMany( 'WeDevs\PM_Pro\Label\Models\Label', pm_tb_prefix() . 'pm_task_label_task', 'task_id', 'label_id' );
    }

    public static function label_transform( $data, $item ) {
        return $data;
        $labels = new Collection( $item->labels, new Label_Transformer );
        $data['labels'] = pm_get_response( $labels );

        return $data;
    }

    public static function set_labales_in_task( $tasks, $task_ids, $params ) {
        return $tasks;

        global $wpdb;

        $with = empty( $params['with'] ) ? [] : $params['with'];

        if ( ! is_array( $with ) ) {
            $with = explode( ',', str_replace(' ', '', $with ) );
        }

        if ( ! in_array( 'labels', $with ) || empty( $task_ids ) ) {
            return $tasks;
        }

        $tb_label = pm_tb_prefix() . 'pm_task_label';
        $tb_task_label = pm_tb_prefix() . 'pm_task_label_task';

        $ids = implode( ',', $task_ids );

        $query = "SELECT tl.*, tlt.task_id
            FROM $tb_label AS tl
            LEFT JOIN $tb_task_label AS tlt ON tl.id=tlt.label_id
            WHERE tlt.task_id IN ( $ids )";

        $results = $wpdb->get_results( $query );

        $task_labels = [];

        foreach ( $results as $key => $label ) {
            $task_labels[$label->task_id][] = $label;
        }

        foreach ( $tasks as $key => $task ) {
            $label = empty( $task_labels[$task->id] ) ? [] : $task_labels[$task->id];

            if ( ! empty( $label ) ) {
                $resource = new Collection( $label, new Label_Transformer );
                $label = pm_get_response( $resource );
                $task->labels = $label;
            } else {
                $task->labels = $label;
            }
        }

        return $tasks;
    }

    public static function set_labales( $tasks, $task_ids ) {

        global $wpdb;

        $tb_label = pm_tb_prefix() . 'pm_task_label';
        $tb_task_label = pm_tb_prefix() . 'pm_task_label_task';

        $ids = implode( ',', $task_ids );

        $query = "SELECT tl.*, tlt.task_id
            FROM $tb_label AS tl
            LEFT JOIN $tb_task_label AS tlt ON tl.id=tlt.label_id
            WHERE tlt.task_id IN ( $ids )";

        $results = $wpdb->get_results( $query );

        $task_labels = [];

        foreach ( $results as $key => $label ) {
            $task_labels[$label->task_id][] = $label;
        }

        foreach ( $tasks['data'] as $key => $task ) {
            $label = empty( $task_labels[$task['id']] ) ? [] : $task_labels[$task['id']];

            if ( ! empty( $label ) ) {
                $resource = new Collection( $label, new Label_Transformer );
                $label = pm_get_response( $resource );
                $tasks['data'][$key]['labels'] = $label;
            } else {
                $tasks['data'][$key]['labels']['data'] = $label;
            }
        }

        return $tasks;
    }

    public function delete_task_labels( $task_ids ) {
        if ( !is_array( $task_ids ) ) {
            $task_ids = [ $task_ids ];
        }

        Task_Label_Task::whereIn( 'task_id', $task_ids)->delete();

        return true;
    }

    public static function after_update_task( $task, $params ) {
        if ( ! isset( $params['task_labels'] ) ) {
            return;
        }

        $task_id = empty( $task->id ) ? 0 : intval( $task->id );
        $label_ids = empty( $params['task_labels'] ) ? [] : $params['task_labels'];

        if ( isset( $params['task_labels'] ) ) {
            self::getInstance()->update( $task_id, $label_ids );
        } else {
            self::getInstance()->delete_task_labels( $task_id );
            // $task_label_tasks = Task_Label_Task::where( 'task_id', $task_id )
            //     ->get()
            //     ->toArray();

            // $db_label_ids = wp_list_pluck( $task_label_tasks, 'label_id' );

            // if ( ! empty( $db_label_ids ) ) {
            //     self::getInstance()->delete( [], $db_label_ids );
            // }
        }
    }

    public static function after_delete_task( $task_id, $project_id ) {
        self::getInstance()->delete( $task_id );
    }

    public static function duplicate_task_label( $new_task_id, $new_list_id, $project_id, $prev_task ) {

        $label_collection = self::task_model_labels( $prev_task );
        $labels           = $label_collection->get()->toArray();
        $label_ids        = wp_list_pluck( $labels, 'id' );

        self::getInstance()->store( $label_ids, $new_task_id );
    }
}






















