<?php
/**
 * Created by PhpStorm.
 * User: wedevs-macbook-2
 * Date: 15/10/18
 * Time: 9:25 AM
 */

namespace WeDevs\PM_Pro\Modules\task_recurring;

use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Common\Models\Meta;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\task_recurring\FormatRecurrenceData as FRD;

class CreateRecurrentTasks {

    public $duration = 1;

    public function __construct() {

        add_filter( 'cron_schedules', array( $this,'recurrent_cron_intervals'), 10, 1 );

        if ( pm_pro_is_module_active( 'task_recurring/task_recurring.php' ) ) {
            if ( ! wp_next_scheduled( 'hook_recurrent_task' ) ) {
                wp_schedule_event( time(), 'one_minutes', 'hook_recurrent_task' );
            }
            add_action( 'hook_recurrent_task', array( $this, 'get_data' ) );
        } else {
            // Get the timestamp of the next scheduled run
            $timestamp = wp_next_scheduled( 'hook_recurrent_task' );
            // Un-schedule the event
            wp_unschedule_event( $timestamp, 'hook_recurrent_task' );
        }
        //$this->get_data();
    }

    /**
     * @return array
     */
    public function get_data() {
        $rec_data = [];
        $item     = [];
        $index    = 0;
        $tasks    = Task::where( 'recurrent', '!=', '0' )->where( 'recurrent', '!=', '9' )->with( [
            'metas' => function ( $q ) {
                $q->where( 'meta_key', '=', 'recurrence' );
            },
            'task_lists'
        ] )->get();

        foreach ( $tasks as $task ) {
            $item['task_id']   = $task->id;
            $item['title']     = $task->id;
            $item['recurrent'] = $task->recurrent;
            foreach ( $task->metas as $meta ) {
                $item['meta_id']    = $meta->id;
                $item['recurrence'] = $meta->meta_value;
                $item['created_at'] = $meta->created_at;
                $item['formatted']  = $meta->formatted;
            }
            array_push( $rec_data, $item );
            $formatted_rd = new FRD( $rec_data[ $index ] );
            $this->createTasks( $formatted_rd, $task );
            $index ++;
            sleep( 1 );
        }

        die();
    }

    public function createTasks( $formatted_rd, $task ) {

        $run_on = Carbon::parse($formatted_rd->recurrence[ $formatted_rd->unit[ $task['recurrent'] ] ]);
        $last_run = Carbon::parse( $formatted_rd->recurrence['last_run'] );
        $duration = $formatted_rd->recurrence['duration'];
        switch ( $task['recurrent'] ) {
            case "1":
                $weekstart = $run_on;

                if ( $weekstart->startOfWeek()->toDateString() === Carbon::now()->startOfWeek()->toDateString() && ! $formatted_rd->is_expired() && Carbon::now()->toDateString() != $last_run->toDateString())
                {
                    $isToday = $formatted_rd->parseWeekdays();
//                    pmpr(Carbon::now()); die();
                    if ($isToday[Carbon::now()->dayOfWeek ]['checked']) {

                        $this->task_duplicate( $task, $task->task_lists->first()->id, $task->project_id, $duration);
                    }
                    if ( $isToday[Carbon::now()->dayOfWeek] == 6 ) {
                        $repeat = $formatted_rd->recurrence['repeat'];
                        $formatted_rd->recurrence[ $formatted_rd->unit[ $task['recurrent'] ] ] = $weekstart->addWeek( $repeat )->toDateString();
                    }

                    $formatted_rd->updateAfterRun();

                }
                break;
            case "2":
                $month = $run_on;
                $date = Carbon::now()->addMonth(1);
                $y = $date->year;
                $m = $date->month;
                $d = $formatted_rd->recurrence['repeat'] > 29 ? $date->endOfMonth()->day : $formatted_rd->recurrence['repeat'];

                if ( $month->day === Carbon::now()->day && Carbon::now()->toDateString() != $last_run->toDateString() && ! $formatted_rd->is_expired()) {
                    $this->task_duplicate( $task, $task->task_lists->first()->id, $task->project_id, $duration);
                    $formatted_rd->recurrence[ $formatted_rd->unit[ $task['recurrent'] ] ] = Carbon::parse($y.'-'.$m.'-'.$d)->toDateString();

                    $formatted_rd->updateAfterRun();
                }
                break;
            case "3":
                $year = $run_on;
                if ( $year->toDateString() === Carbon::now()->toDateString() && Carbon::now()->toDateString() != $last_run->toDateString() && ! $formatted_rd->is_expired() ) {
                    $this->task_duplicate( $task, $task->task_lists->first()->id, $task->project_id, $duration);
                    $formatted_rd->recurrence[ $formatted_rd->unit[ $task['recurrent'] ] ] = $year->addYear(1);
                    $formatted_rd->updateAfterRun();
                }
                break;
        }


    }

    public function replicateModel( $task ) {
        $model               = Task::find( $task['id'] );
        $newModel            = $model->replicate();
        $newModel->recurrent = '0';
        $newModel->push();
    }


    // Duplicate

    public function task_duplicate ( Task $task, $list_id = false, $project_id = false,  $duration = 1) {
        $task_data      = [];
        $boardable_data = [];
        $assignee_data  = [];
        $meta_data      = [];

        if ( $project_id ) {
            $task_data    ['project_id'] = $project_id;

            $assignee_data['project_id'] = $project_id;
            $meta_data    ['project_id'] = $project_id;
        }

        if($duration > 1){
            $duration = $duration - 1;
            $task_data ['start_at'] = Carbon::now();
            $task_data ['due_date'] = Carbon::now()->addDay($duration);
        } else {
            $task_data ['start_at'] = Carbon::now();
        }

        $task_data ['recurrent'] = 9;
        $newTask = $this->replicate( $task, $task_data );


        // Include task and task list
        $boardable_data['boardable_id'] = $newTask->id;
        $assignee_data ['task_id']      = $newTask->id;
        $meta_data     ['entity_id']    = $newTask->id;

        if ( $list_id ) {
            $boardable_data['board_id'] = $list_id;
        }

        foreach ( $task->boardables as $boardable ) {
            $newBoardables = $this->replicate( $boardable, $boardable_data );
        }

        // Duplicate Assignee in this task

        foreach ( $task->assignees as $assignee ) {
            $assignee['task_id'] = $newTask->id;
            $newAssignee = $this->replicate( $assignee, $assignee_data );
        }

        $metas = $task->metas()->where('meta_key', 'privacy')->get();

        foreach ( $metas as $meta ) {
            $newMeta = $this->replicate( $meta, $meta_data );
        }

        do_action( 'cpm_task_duplicate_after', $newTask->id, $list_id, $project_id );

        do_action( 'pm_after_create_task', $newTask, [] );

        return $newTask;
    }

    private function replicate( $model, $newValues=null, $fireEvents=false) {
        $newModel = $model->replicate()->setRelations([]);


        if ( $newValues !== null && is_array( $newValues ) ) {
            foreach ($newValues as $key => $value) {
                $newModel->{$key} = $value;
            }
        }

        if ( !$fireEvents ) {
            $newModel->unsetEventDispatcher();
        }

        if ( $newModel->save() ) {
            return $newModel;
        }
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
