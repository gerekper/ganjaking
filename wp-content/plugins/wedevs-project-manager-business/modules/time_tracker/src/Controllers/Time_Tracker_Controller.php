<?php
namespace WeDevs\PM_Pro\Modules\time_tracker\src\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM\Common\Models\Board;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM_Pro\Modules\time_tracker\src\Models\Time_Tracker;
use WeDevs\PM_Pro\Modules\time_tracker\src\Transformers\Time_Tracker_Transformer;
use WeDevs\PM\Task\Transformers\Task_Transformer;
use WeDevs\PM_Pro\Modules\time_tracker\libs\Report_Summary;
use WeDevs\PM_Pro\Modules\time_tracker\libs\Report_Users;


class Time_Tracker_Controller {

    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {

    }

    public function others_time( WP_REST_Request $request ) {
        $users_id   = $request->get_param( 'users_id' );
        $project_id = $request->get_param( 'project_id' );
        $list_id    = $request->get_param( 'list_id' );
        $task_id    = $request->get_param( 'task_id' );
        $data       = [];

        if ( is_array( $users_id ) ) {
            $users_id = array_filter($users_id, function($user_id) {
                return $user_id == get_current_user_id() ? false : true;
            });
        } else {
            $users_id = [];
        }



        foreach ( $users_id as $key => $user_id ) {
            $data[] = $this->get_user_time_log( $user_id, $project_id, $list_id, $task_id );
        }

        return $data; //$this->get_response( $data );
    }

    public function get_user_time_log( $user_id, $project_id, $list_id, $task_id ) {

        $user = get_user_by( 'id', $user_id );

        $time_log = Time_Tracker::where('user_id', $user_id)
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->get();

        // $time_log = Time_Tracker::where('task_id', $task_id)
        //     ->where('project_id', $project_id)
        //     ->orderBy('user_id')
        //     ->get();

        $resource = new Collection( $time_log, new Time_Tracker_Transformer );
        $total_time = $this->get_total_time( $task_id, $user_id );

        $resource->setMetaValue( 'total_time', $total_time );
        $resource->setMetaValue( 'user', $user );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        $task_id    = $request->get_param( 'task_id' );
        $data       = [
            'user_id'    => get_current_user_id(),
            'project_id' => $request->get_param( 'project_id' ),
            'list_id'    => $request->get_param( 'list_id' ),
            'task_id'    => $request->get_param( 'task_id' ),
            'start'      => strtotime( current_time( 'mysql' ) ),
            'stop'       => 0,
            'total'      => 0,
            'run_status' => 1,
        ];

        $time_tracker = Time_Tracker::create( $data );
        $task         = Task::find( $task_id );
        $resource     = new Item( $task, new Task_Transformer );
        $message = [
            'message' => 'Your time start now'
        ];

        return $this->get_response( $resource, $message );
    }

    public function custom_time( WP_REST_Request $request ) {
        $start = strtotime( $request->get_param( 'start' ) );
        $stop  = $request->get_param( 'stop' );
        $pos = strpos( $stop,':');
        if ( $pos === false ) {
            $stop = date( 'H:i', mktime( $stop , 0 ) );
            $stop  = strtotime('1970-01-01 ' . $stop );
        } else {
            $time = explode( ":", $stop );
            $min  = strlen( $time[1] ) == 1 ? $time[1].'0' : $time[1];
            $stop = date( 'H:i', mktime( $time[0],$min ) );
            $stop  = strtotime('1970-01-01 ' . $stop );
        }
            $task_id = $request->get_param( 'task_id' );


        if ( $start > $stop ) {
           // return $this->get_response(null, []);
        }

        $total = $stop - $start;

        $data = [
            'user_id'    => get_current_user_id(),
            'project_id' => $request->get_param( 'project_id' ),
            'list_id'    => $request->get_param( 'list_id' ),
            'task_id'    => $task_id,
            'start'      => $start,
            'stop'       => $start,
            'total'      => $stop,
            'run_status' => 0,
        ];


        $time_tracker = Time_Tracker::create( $data );
        $task         = Task::find( $task_id );
        $resource     = new Item( $task, new Task_Transformer );
        $message = [
            'message' => 'Your time log update successfully'
        ];

        return $this->get_response( $resource, $message );
    }

    public function update( WP_REST_Request $request ) {
        $task_id    = $request->get_param( 'task_id' );
        $list_id    = $request->get_param( 'list_id' );
        $project_id = $request->get_param( 'project_id' );
        $user_id    = get_current_user_id();


        $time = Time_Tracker::where( 'user_id', $user_id )
            ->where( 'task_id', $task_id )
            ->where( 'list_id', $list_id )
            ->where( 'project_id', $project_id )
            ->where( 'run_status', 1 )
            ->first();

        if ( ! $time ) {
            return false;
        }

        $stop  = strtotime( current_time( 'mysql' ) );
        $total = $stop - $time->start;

        $data = [
            'stop'       => $stop,
            'total'      => $total,
            'run_status' => 0,
        ];

        $time->update_model( $data );

        $resource = new Item( $time, new Time_Tracker_Transformer );
        $total_time = $this->get_total_time( $task_id );

        $resource->setMetaValue( 'total_time', $total_time );

        $message = [
            'message' => 'Your traking time was stop successfully'
        ];

        return $this->get_response( $resource, $message );
    }

    public function get_total_time( $task_id, $user_id = false ) {
        $user_id = $user_id ? $user_id : get_current_user_id();

        $times = Time_Tracker::where('user_id', $user_id)
            ->where('task_id', $task_id)
            ->get();

        return pm_pro_get_total_time( $times );
    }

    public function destroy( WP_REST_Request $request ) {
        $id = $request->get_param( 'time_id' );
        $task_id = $request->get_param( 'task_id' );

        // Select the time
        $time = Time_Tracker::where( 'id', $id )
            ->first();

        $time->delete();

        $task = Task::find( $task_id );
        $resource = new Item( $task, new Task_Transformer );

        $message = [
            'message' => 'Time log deleted successfully'
        ];

        return $this->get_response($resource, $message);
    }

    public function report_summary( WP_REST_Request $request ) {
        $type = $request->get_param( 'type' );

        switch ( $type ) {
            case 'summary':
                $reports = Report_Summary::summary( $request->get_params() );
                break;

            case 'user':
                $reports = Report_Users::users( $request->get_params() );
                break;
        }

        wp_send_json_success( $reports );
    }

    public function report_summary_csv( WP_REST_Request $request ) {
        $type = $request->get_param( 'type' );

        switch ( $type ) {
            case 'summary':
                $reports = Report_Summary::summary( $request->get_params() );
                break;

            case 'user':
                $reports = Report_Users::users( $request->get_params() );
                Report_Users::export_csv( $reports );
        }
    }
}

