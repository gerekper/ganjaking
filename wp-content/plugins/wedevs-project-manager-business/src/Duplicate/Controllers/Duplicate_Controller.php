<?php
namespace WeDevs\PM_Pro\Duplicate\Controllers;

use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM\Project\Transformers\Project_Transformer;
use WeDevs\PM\Task_List\Transformers\Task_List_Transformer;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Activity\Models\Activity;

class Duplicate_Controller {

    use Transformer_Manager, Request_Filter;

    public function project_duplicate ( WP_REST_Request $request ) {
        $project_id = $request->get_param('id');
        $newProject = Duplicate::init()->project_duplicate($project_id);

        $resource = new Item( $newProject, new Project_Transformer );

        return $this->get_response( $resource, [ 'message' =>  __( 'A project has been Duplicated successfully.', 'pm-pro' ) ] );
    }

    public function list_duplicate ( WP_REST_Request $request  ) {
        $list = $request->get_param('id');

        // find task list and retrive related data
        $task_list = Task_List::with([
            'tasks',
            'tasks.metas',
            'tasks.boardables',
            'tasks.assignees',
            'board',
            'metas'
        ])->find($list);

        if ( ! $task_list ) {
           return $this->get_response( null, [ 'message' =>  __( 'Task list not found', 'pm-pro' ) ] );
        }

        // duplicate task list wtih task;
        $newTaskList = Duplicate::init()->list_duplicate($task_list);

        // create an activity
        if ( $newTaskList ) {
            $user = wp_get_current_user();
            Activity::create( [
                'actor_id'      => $user->ID,
                'action'        => 'duplicate_list',
                'action_type'   => 'duplicate',
                'resource_id'   => $newTaskList->project_id,
                'resource_type' => 'task_list',
                'project_id'    => $newTaskList->project_id,
                'meta'          => [
                    'old_task_list_id'    => $task_list->id,
                    'old_task_list_title' => $task_list->title,
                    'new_task_list_id' => $newTaskList->id,
                    'new_task_list_title' => $newTaskList->title,
                ],
            ] );
        }

        $task_list_transforment = new Task_List_Transformer;

        $resource = new Item( $newTaskList,  $task_list_transforment->setDefaultIncludes( [
            'creator', 'updater', 'milestone', 'complete_tasks', 'incomplete_tasks', 'comments', 'files'
        ] ) );

        return $this->get_response( $resource, [ 'message' =>  __( 'A Task List has been Duplicated successfully.', 'pm-pro' ) ] );
    }
}
