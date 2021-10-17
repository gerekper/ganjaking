<?php
namespace WeDevs\PM_Pro\Modules\Task_Recurring;

use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Common\Models\Meta;
use Carbon\Carbon;
use WeDevs\PM\Task\Controllers\Task_Controller;

class Create_Recurrent_Tasks {

    public $duration = 1;

    public function __construct() {
        if ( pm_pro_is_module_active( 'Task_Recurring/Task_Recurring.php' ) ) {
            if ( ! wp_next_scheduled( 'hook_recurrent_task' ) ) {
                wp_schedule_event( time(), 'wp_pm_emailer_cron_interval', 'hook_recurrent_task' );
            }
            add_action( 'hook_recurrent_task', array( $this, 'do_recurrence' ) );
        } else {
            // Get the timestamp of the next scheduled run
            $timestamp = wp_next_scheduled( 'hook_recurrent_task' );
            // Un-schedule the event
            wp_unschedule_event( $timestamp, 'hook_recurrent_task' );
        }

        add_filter( 'cron_schedules', array( $this,'recurrent_cron_intervals'), 10, 1 );
    }

    public function cron_time( $schedules ) {
        $schedules['recurring_time'] = [
            'interval' => 60,
            'display'  => 'Once Every 1 Minutes'
        ];

        return (array)$schedules;
    }

    /**
     * @return array
     */
    public function do_recurrence() {
        $rec_data = [];
        $item     = [];
        $index    = 0;
        $today    = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );

        $dbtasks = pm_get_tasks( [ 'recurrent' => '1,2,3,4', 'with' => 'project' ] );
        $tasks = [];

        foreach ( $dbtasks['data'] as $task ) {
            if ( ! empty( $task['project']['data']['status'] ) && $task['project']['data']['status'] != 0 ) {
                continue;
            }

            $tasks[$task['id']] = $task;
        }

        $task_ids = wp_list_pluck( $tasks, 'id' );
        $metas    = pm_get_meta( $task_ids, false, 'task', 'recurrence', false );

        foreach ( $metas as $key => $meta ) {
            $mt_tid = $meta['entity_id'];

            if ( empty( $tasks[$mt_tid] ) ) {
                continue;
            }

            $tasks[$mt_tid]['recurring_task'] = $meta;
        }

        foreach ( $tasks as $task ) {

            if ( $task['recurrent'] == 0 ) {
                continue;
            }

            if ( empty( $task['recurring_task'] ) ) {
                continue;
            }

            $last_recurrent_date = empty( $task['meta']['last_recurrent_date'] )
                            ? ''
                            : date( 'Y-m-d', strtotime( $task['meta']['last_recurrent_date'] ) );

            if ( $last_recurrent_date == $today ) {
                continue;
            }

            $recurrence_meta = $task['recurring_task'];
            $recurrence = $task['recurring_task']['meta_value'];

            if ( $recurrence['expire_type'] == 'occurrence' ) {
                $count_recurr = empty( $task['meta']['recurring_count'] ) ? 0 : $task['meta']['recurring_count'];

                if ( $count_recurr >= $recurrence['expire_after_occurrence'] ) {
                    continue;
                }
            }

            $timezone   = wp_timezone_string();

            $start_date = $this->get_start_date( $recurrence, $today );
            $end_date   = $this->get_end_date( $recurrence, $today );
            $start_date  = new \DateTime( $start_date, new \DateTimeZone($timezone) );
            $end_date    = new \DateTime( $end_date, new \DateTimeZone($timezone) );

            $rule = (new \Recurr\Rule)
                    ->setStartDate( $start_date )
                    ->setTimezone( $timezone )
                    ->setInterval( $recurrence['repeat'] );

            if ( $recurrence['expire_type'] == 'occurrence' ) {
                $rule->setCount( $recurrence['expire_after_occurrence'] );
            } else {
                $rule->setUntil( $end_date );
            }

            if ( $task['recurrent'] == 1 ) {
                $rule->setFreq( 'WEEKLY' )
                    ->setByDay( $this->get_days( $recurrence ) );
            }

            if ( $task['recurrent'] == 2 ) {
                $rule->setFreq( 'MONTHLY' );
            }

            if ( $task['recurrent'] == 3 ) {
                $rule->setFreq( 'YEARLY' );
            }

            if ( $task['recurrent'] == 4 ) {
                $rule->setFreq( 'DAILY' );
            }

            $transformer = new \Recurr\Transformer\ArrayTransformer();
            $events = $transformer->transform( $rule );

            foreach ( $events as $key => $repeat ) {
                $repeat_date_items = $repeat->getStart();
                $repeat_date       = $repeat_date_items->format( 'Y-m-d' );

                if ( $repeat_date == $today ) {
                    $this->task_duplicate_self( $task['id'], $task['task_list_id'], $task['project_id'], $today );
                }
            }
        }

        return;
    }

    private function get_tody_day( $today ) {
        $day = date( 'D', strtotime( $today ) );
        $day = substr( $day, 0, -1 );
        $day = strtoupper( $day );

        return [$day];
    }

    private function get_days( $recurrence ) {
        $days = empty( $recurrence['weekdays'] ) ? [] : $recurrence['weekdays'];
        $filter_days = [];

        foreach ( $days as $key => $day ) {
            if ( $day['checked'] == 'true' || $day['checked'] === true ) {
                $name = substr( $day['name'], 0, -1 );

                $filter_days[$name] = $name;
            }
        }

        return $filter_days;
    }

    public function get_start_date( $recurrence, $today ) {
        if ( empty( $recurrence['repeat_year'] ) ) {
            $start_date = date( 'Y-m-d', strtotime( $recurrence_meta['created_at'] ) );
        } else {
            $start_date = date( 'Y-m-d', strtotime( $recurrence['repeat_year'] ) );
        }

        return $start_date;
    }

    public function get_end_date( $recurrence, $today ) {
        if ( $recurrence['expire_type'] == 'date' ) {
            $recurrence['expire_after_date'] = empty( $recurrence['expire_after_date'] ) ? $today : $recurrence['expire_after_date'];
            return date( 'Y-m-d', strtotime( $recurrence['expire_after_date'] ) );
        }

        return date( 'Y-m-d', strtotime( $today ) );
    }

    private function task_duplicate_self( $task_id, $list_id, $project_id, $today ) {
        $today           = date( 'Y-m-d', strtotime( $today ) );
        $task            = Task::find( $task_id );

        $last_count        = pm_get_meta( $task_id, $project_id, 'task', 'recurring_count' );
        $last_count_number = empty( $last_count->meta_value ) ? 0 : $last_count->meta_value;
        $new_count         = $last_count_number+1;

        $task->status            = 0;
        $task->title             = $task->title . ' (copy)';
        $task->recurrent         = 0;

        $duplicated_task = ( new Task_Controller )->task_duplicate( $task, $list_id, $project_id );

        pm_update_meta( $task_id, $project_id, 'task', 'last_recurrent_date', $today );
        pm_update_meta( $task_id, $project_id, 'task', 'recurring_count', $new_count );

        //Task duplicate time this field are also getting duplicate
        pm_delete_meta( $duplicated_task->id, $project_id, 'task', 'recurrence' );
        pm_delete_meta( $duplicated_task->id, $project_id, 'task', 'last_recurrent_date' );
        pm_delete_meta( $duplicated_task->id, $project_id, 'task', 'recurring_count' );

        return $duplicated_task;
    }


    public function recurrent_cron_intervals( $schedules ) {
        // $schedules stores all recurrence schedules within WordPress
        $schedules['one_minutes'] = array(
            'interval'	=> 60,	// Number of seconds, 600 in 10 minutes
            'display'	=> 'Once Every 1 Minutes'
        );

        // Return our newly added schedule to be merged into the others
        return (array)$schedules;
    }

}
