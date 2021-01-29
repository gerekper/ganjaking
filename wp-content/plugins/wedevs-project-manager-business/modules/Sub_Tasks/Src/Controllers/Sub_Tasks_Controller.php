<?php
namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers;

use Reflection;
use WP_REST_Request;
use WeDevs\PM\Task\Models\Task;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Task\Transformers\Task_Transformer;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM\Common\Models\Board;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Common\Models\Assignee;
use WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Models\Sub_Tasks;
use Illuminate\Pagination\Paginator;
use WeDevs\PM\Task\Controllers\Task_Controller;
use WeDevs\PM\Settings\Models\Task_Type_Task;
use WeDevs\PM\Settings\Controllers\Task_Types_Controller;

class Sub_Tasks_Controller {

    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {
        $task_id    = $request->get_param( 'task_id' );
        $per_page   = $request->get_param( 'per_page' );
        $page       = $request->get_param( 'page' );

        return $this->get_sub_tasks([
            'task_id'  => $task_id,
            'per_page' => $per_page,
            'page'     => $page
        ]);
    }

    public function get_sub_tasks( $params ) {
        $task_id    = $params['task_id'];
        $per_page   = empty( $params['per_page'] ) ? 1000 : $params['per_page'];
        $page       = empty( $params['page'] ) ? 1 : $params['page'];


        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $tasks = Sub_Tasks::join( pm_tb_prefix() . 'pm_boardables', function ($join) {
                $join->on( pm_tb_prefix() . 'pm_tasks.id', '=', pm_tb_prefix() . 'pm_boardables.boardable_id' )
                    ->where( pm_tb_prefix() . 'pm_boardables.board_type', '=', "task_list" )
                    ->where( pm_tb_prefix() . 'pm_boardables.boardable_type', '=', 'sub_task' );
            } )
            ->selectRaw( pm_tb_prefix() . 'pm_tasks.*' )
            ->where( 'parent_id', $task_id )
            ->groupBy( pm_tb_prefix() . 'pm_tasks.id'  )
            ->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' )
            ->orderBy( pm_tb_prefix() . 'pm_tasks.created_at', 'DESC')
            ->paginate( $per_page, ['*'] );

        $task_collection = $tasks->getCollection();

        $resource = new Collection( $task_collection, new Task_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $tasks ) );

        return $this->get_response( $resource );
    }

    public function get_sub_task( $sub_task_id ) {
        $sub_task = Sub_Tasks::with('task_lists')->where( 'id', $sub_task_id )
            ->first();

        $resource = new Item( $sub_task, new Task_Transformer );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        $task_id     = $request->get_param( 'task_id' );
        $sub_task_id = $request->get_param( 'sub_task_id' );

        $sub_task = Sub_Tasks::with('task_lists')->where( 'id', $sub_task_id )
            ->where( 'parent_id', $task_id )
            ->first();

        $resource = new Item( $sub_task, new Task_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {

        $data          = $request->get_params();
        // $project_id    = $request->get_param( 'project_id' );
        // $board_id      = $request->get_param( 'board_id' );
        //$assignees     = $request->get_param( 'assignees' );

        return $this->create($data);

    }

    function create( $params ) {

        $project_id    = $params['project_id'];
        $board_id      = $params['board_id'];
        $task_id      = $params['task_id'];
        $assignees     = empty( $params['assignees'] ) ? false : $params['assignees'];
        $type_id       = empty( $params['type_id'] ) ? false : intval( $params['type_id'] );

        $project       = Project::find( $project_id );
        $board         = Board::find( $board_id );

        if ( $project ) {
            $data = apply_filters( 'pm_pro_before_create_subtask', $params );
            $task = Sub_Tasks::create( $data );
        }

        if ( $task && $board ) {
            $latest_order = Boardable::latest_order( $board->id, $board->type, 'sub_task' );
            $boardable    = Boardable::create([
                'board_id'       => $board->id,
                'board_type'     => $board->type,
                'boardable_id'   => $task->id,
                'boardable_type' => 'sub_task',
                'order'          => $latest_order + 1,
            ]);
        }

        if ( is_array( $assignees ) && ! empty( $task ) ) {
            $this->attach_assignees( $task, $assignees );
            $this->set_assignees_to_parent( $data['parent_id'], $project_id,$assignees );
        }

        $this->insert_type( $task->id, $type_id, $project_id, $board_id );

        do_action('pm_after_create_subtask', $task, $data );

        $Task_Transformer = new Item( $task, new Task_Transformer );

        $message = [
            'message' => __( "A sub task has been created successfully.", 'pm-pro' )
        ];

        $resource = $this->get_response( $Task_Transformer, $message );
        $resource['task'] = pm_get_tasks( [ 'id' => $task_id ] );

        do_action( 'pm_create_subtask_after_transformer', $resource, $data );

        return $resource;
    }

    public function insert_type ( $task_id, $type_id, $project_id, $list_id ) {
        if ( empty( intval( $task_id ) || empty( intval( $type_id ) ) ) ) {
            return;
        }

        $has_task_type_task = Task_Type_Task::find( $task_id );

        if ( empty( $has_task_type_task ) ) {
            Task_Type_Task::create([
                'type_id'    => $type_id,
                'task_id'    => $task_id,
                'list_id'    => $list_id,
                'project_id' => $project_id
            ]);
        }
    }


    private function set_assignees_to_parent($parent_id,$project_id,$assignees){
        $parentTask = new Sub_Tasks();
        $parentTask->id = $parent_id ;
        $parentTask->project_id = $project_id ;
        $this->attach_assignees( $parentTask, $assignees );
    }

    private function attach_assignees( Sub_Tasks $task, $assignees = [] ) {
        foreach ( $assignees as $user_id ) {
            $data = [
                'task_id'     => $task->id,
                'assigned_to' => $user_id,
                'project_id'  => $task->project_id,
            ];

            $assignee = Assignee::firstOrCreate( $data );

            if ( !$assignee->assigned_at ) {
                $assignee->assigned_at = Carbon::now();
                $assignee->save();
            }
        }
    }

    public function update( WP_REST_Request $request ) {
        $data        = $request->get_params();
        $task_id     = $request->get_param( 'task_id' );
        $sub_task_id = $request->get_param( 'sub_task_id' );
        $assignees   = $request->get_param( 'assignees' );
        $type_id     = empty( $data['type_id'] ) ? false : intval( $data['type_id'] );

        $sub_task = Sub_Tasks::where( 'parent_id', $task_id )
            ->where( 'id', $sub_task_id )
            ->first();

        if ( isset( $data['assignees'] ) ) {
            $deleted_users = $sub_task->assignees()
                ->whereNotIn( 'assigned_to', empty( $assignees ) ? [] : $assignees  )
                ->get()
                ->toArray(); //->delete();
            $deleted_users = wp_list_pluck( $deleted_users, 'id' );
        }

        if ( $sub_task ) {
            $ordStatus = $sub_task->status;
            $data = apply_filters( 'pm_pro_before_update_subtask', $data, $sub_task );
            $sub_task->update_model( $data );
        }

        if ( isset( $data['assignees'] ) && $deleted_users ) {
            Assignee::destroy( $deleted_users );
        }

        if ( is_array( $assignees ) && $sub_task ) {
            //$sub_task->assignees()->whereNotIn( 'assigned_to', $assignees )->delete();
            $this->attach_assignees( $sub_task, $assignees );
            $this->set_assignees_to_parent( $data['parent_id'], $sub_task->project_id, $assignees );
        }

        if ( isset( $data['type_id'] ) ) {
            $this->update_type( $sub_task->id, $type_id, $sub_task->project_id, $sub_task->task_list );
        }

        $Task_Transformer = new Item( $sub_task, new Task_Transformer );
        $task = pm_get_tasks( [ 'id' => $task_id ] );

        $message = [
            'message' => __( "A sub task has been updated successfully.", 'pm-pro' )
        ];

        $resource = $this->get_response( $Task_Transformer, $message );
        $resource['task'] = $task;

        do_action( 'pm_udpate_subtask_after_transformer', $resource, $data );

        return $resource;

    }

    public function update_type( $task_id, $type_id, $project_id, $list_id ) {

        if ( empty( intval( $task_id ) ) ) {
            return;
        }

        $has_task_type_task = Task_Type_Task::find( $task_id );

        if ( empty( $has_task_type_task ) && empty( intval( $type_id ) ) ) {
            return;
        }

        if ( ! empty( $has_task_type_task ) && empty( intval( $type_id ) ) ) {
            Task_Types_Controller::destroy_task_type_task_relation_task( $task_id );

            return;
        }

        if ( empty( $has_task_type_task ) && ! empty( intval( $type_id ) ) ) {
            $this->insert_type( $task_id, $type_id, $project_id, $list_id );

            return;
        }

        $has_task_type_task->update_model( [
            'type_id'    => $type_id,
            'list_id'    => $list_id,
            'project_id' => $project_id
        ] );
    }

    public function destroy( WP_REST_Request $request ) {
        // Grab user inputs
        $task_id     = $request->get_param( 'task_id' );
        $sub_task_id = $request->get_param( 'sub_task_id' );

        // Select the task
        $sub_task = Sub_Tasks::where( 'id', $sub_task_id )
            ->where( 'parent_id', $task_id )
            ->first();

        // Delete relations assoicated with the task
        $sub_task->boardables()->delete();
        $sub_task->files()->delete();
        $comments = $sub_task->comments;

        foreach ($comments as $comment) {
            $comment->replies()->delete();
            $comment->files()->delete();
        }

        $sub_task->comments()->delete();
        $sub_task->assignees()->delete();

        // Delete the sub_task
        $sub_task->delete();

        $message = [
            'message' => __( "A sub task has been deleted successfully.", 'pm-pro' )
        ];

        $response = $this->get_response( null, $message);
        $response['task'] = pm_get_tasks( [ 'id' => $task_id ] );

        return $response;
    }

    public function subtask_to_task( WP_REST_Request $request ) {
        //$data        = $this->extract_non_empty_values( $request );

        $list_id     = $request->get_param( 'list_id' );
        $task_id     = $request->get_param( 'task_id' );
        $sub_task_id = $request->get_param( 'sub_task_id' );
        $subtask = pm_pro_get_sub_tasks( ['id' => $sub_task_id] );

        $sub_task = Sub_Tasks::with('task_lists')->where( 'id', $sub_task_id )
            ->where( 'parent_id', $task_id )
            ->first();

        if ( $sub_task ) {
            $sub_task->parent_id = 0;
            $sub_task->update_model( $sub_task );
        }

        $boardable = Boardable::where('boardable_id', $sub_task_id)
            ->where( 'boardable_type', 'sub_task' )
            ->first();

        if ( $boardable ) {
            $latest_order = Boardable::latest_order( $list_id, 'task_list', 'task' );
            $boardable->boardable_type = 'task';
            $boardable->board_id = $list_id;
            $boardable->order = $latest_order + 1;
            $boardable->update_model( $boardable );
        }


        wp_send_json_success( [
            'list' => pm_get_task_lists( ['id' => $list_id] ),
            'task' => pm_get_tasks( ['id' => $task_id] ),
            'subtask' => $subtask,
        ] );
    }

    public function sorting (  WP_REST_Request $request  ) {
        $list        = $request->get_param('list_id');
        $orders      = $request->get_param('orders');
        $received_id = $request->get_param('receive');
        $item        = $request->get_param('item');

        if ( !empty( $received_id ) ) {
            $subtask = Task::find( $item );
            $subtask->parent_id = $received_id;
            $subtask->save();
        }

        foreach ( $orders as $order ) {
            Boardable::where('board_id', $list)
                ->where( 'board_type', 'task_list' )
                ->where( 'boardable_type', 'sub_task' )
                ->where( 'boardable_id', $order['id'] )
                ->update(['order' => $order['index'] ]);
        }

        $message = [
            'message' => __( "A sub task has been sorted successfully", 'pm-pro' )
        ];

        return $resource = $this->get_response( null, $message );
    }
}
