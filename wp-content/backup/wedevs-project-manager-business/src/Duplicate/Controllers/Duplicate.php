<?php
namespace WeDevs\PM_Pro\Duplicate\Controllers;

use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM\Activity\Models\Activity;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Project\Helper\Project_Role_Relation;

class Duplicate {
    public static $instance = null;

    public static function init() {
        if (self::$instance == null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function dplicate_file_child( $file, $parent, $newProject ) {
        if ( empty( $file->children ) ) {
            return;
        }

        foreach ( $file->children as $child ) {
            $new_file = $this->replicate( $child, [
                'project_id'  => $newProject->id,
                'parent'      => $parent->id
            ]);

            foreach ( $child->meta as $key => $meta) {
                $new_meta = $this->replicate( $meta, [
                    'entity_id'  => $new_file->id,
                    'project_id' => $newProject->id
                ]);
            }

            $this->dplicate_file_child( $child, $new_file, $newProject );
        }
    }

    public function project_duplicate( $project_id ) {

        $project = Project::find( $project_id );

        if ( !$project ){
            return ;
        }

        $newProject = $this->replicate( $project, [
            'title' => $this->unique_project_title($project->title . ' copy'),
        ] );

        $transformer = pm_get_projects( [
            'id'   => $newProject->id,
            'with' => 'assignees'
        ] );

        //pmpr($transformer); die();

        ( new Project_Role_Relation )->set_relation_after_create_project( $transformer['data'] );

        if ( !$project->categories->isEmpty() ) {
            $newProject->categories()->attach( $project->categories->first()->id );
        }

        $project->load(
            'milestones',
            'milestones.metas',
            'task_lists',
            'task_lists.metas',
            'task_lists.board',
            'task_lists.tasks',
            'task_lists.tasks.metas',
            'task_lists.tasks.boardables'
        );

        $roles = User_Role::where('project_id', $project_id)->get();

        foreach ( $roles as $role ) {
            $newRole = $this->replicate( $role, [
                'project_id'    => $newProject->id,
            ] );
        }

        // Duplicate milestones of project
        $milestones = [];

        $file_types = [ 'folder', 'pro_file', 'doc', 'doc_file', 'link' ];

        foreach ( $project->files as $file ) {
            if ( ! in_array( $file->type, $file_types ) ) {
                continue;
            }

            if ( $file->parent == '0' ) {
                $parent = $this->replicate( $file, [
                    'project_id'  => $newProject->id
                ]);

                foreach ( $file->meta as $key => $meta) {
                    $new_meta = $this->replicate( $meta, [
                        'entity_id'  => $parent->id,
                        'project_id' => $newProject->id
                    ]);
                }

                $this->dplicate_file_child( $file, $parent, $newProject );
            }


        }

        foreach ( $project->milestones as $milestone ) {
            $newMilesone = $this->replicate($milestone, [
                'project_id'    => $newProject->id,
            ]);

            foreach ( $milestone->metas as $meta ) {
                $newMeta = $this->replicate( $meta, [
                    'entity_id'     => $newMilesone->id,
                    'project_id'    => $newProject->id,
                ] );
            }
            $milestones[$milestone->id] = $newMilesone->id;
        }

        // Duplicate Discuss
        foreach ( $project->discussion_boards as $discussion_board ) {
            $newDisBoard = $this->replicate( $discussion_board, [
                'project_id' => $newProject->id,
            ] );

            $disBoardables = $discussion_board->boardables->where( 'board_type', 'milestone' );

            foreach ( $disBoardables as $disBoardable ) {
                $this->replicate( $disBoardable, [
                    "board_id"      => $milestones[$disBoardable->board_id],
                    "boardable_id"  => $newDisBoard->id,
                ] );
            }

            foreach ( $discussion_board->metas as $meta ) {
                $this->replicate( $meta, [
                    'entity_id'     => $newDisBoard->id,
                    'project_id'    => $newProject->id,
                ] );
            }

            foreach ( $discussion_board->files as $key => $file ) {

                $this->replicate( $file, [
                    'fileable_id' => $newDisBoard->id,
                    'project_id'  => $newProject->id
                ] );
            }
        }
        $listmeta = pm_get_meta($project_id, $project_id, 'task_list', 'list-inbox');
        // Duplicate task list
        foreach ( $project->task_lists as $task_list ) {
            $newlist =  $this->list_duplicate( $task_list, $newProject->id, $milestones  );

            if ($listmeta && intval($listmeta->meta_value) == $task_list->id ) {
                $meta = Meta::create([
                    'entity_id'	=> $newProject->id,
                    'entity_type' => 'task_list',
                    'meta_key' => 'list-inbox',
                    'project_id' => $newProject->id,
                    'meta_value' => $newlist->id
                ]);
            }
        }

        if ( $newProject ) {
            $user = wp_get_current_user();
            Activity::create( [
                'actor_id'      => $user->ID,
                'action'        => 'duplicate_project',
                'action_type'   => 'duplicate',
                'resource_id'   => $newProject->id,
                'resource_type' => 'project',
                'project_id'    => $newProject->id,
                'meta'          => [
                    'old_project_id'    => $project->id,
                    'old_project_title' => $project->title,
                    'project_title_new' => $newProject->title
                ],
            ] );
        }

        do_action( 'cpm_project_duplicate', $project_id, $newProject->id );
        do_action( 'pm_project_duplicate', $project_id, $newProject->id );

        return $newProject;
    }

    public function list_duplicate( Task_List $task_list, $project_id = false, $milestones= [] ) {

        $list_data  = [];
        $board_data = [];
        $meta_data  = [];

        if ( $project_id ) {
            $list_data['project_id'] = $project_id;
            $meta_data['project_id'] = $project_id;
        }

        if ( empty( $project_id ) ) {
            $task_list->title = strtolower( $task_list->title ) == 'inbox' ? $task_list->title : $task_list->title . ' copy';
        }

        $newTaskList = $this->replicate( $task_list, $list_data );

        // include milestone with task list
        $boards = $task_list->board->where( 'board_type', 'milestone' );
        $board_data["boardable_id"] = $newTaskList->id;

        foreach ( $boards as $board ) {
            if ( ! empty( $milestones ) ) {
                $board_data["board_id"] = $milestones[$board->board_id];
            }

            $newBoard = $this->replicate( $board, $board_data );
        }

        $meta_data['entity_id'] = $newTaskList->id;

        foreach ( $task_list->metas as $meta ) {
            $newMeta = $this->replicate( $meta, $meta_data );
        }
        // Duplicate Task under Task list
        foreach ( $task_list->tasks as $task ) {
            $this->task_duplicate( $task, $newTaskList->id, $project_id );
        }

        return $newTaskList;
    }

    public function task_duplicate ( Task $task, $list_id = false, $project_id = false  ) {
        $task_data      = [];
        $boardable_data = [];
        $assignee_data  = [];
        $meta_data      = [];

        if ( $project_id ) {
            $task_data    ['project_id'] = $project_id;
            $assignee_data['project_id'] = $project_id;
            $meta_data    ['project_id'] = $project_id;
        }

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
            $newAssignee = $this->replicate( $assignee, $assignee_data );
        }

        foreach ( $task->metas as $meta ) {
            $newMeta = $this->replicate( $meta, $meta_data );
        }

        do_action( 'cpm_task_duplicate_after', $newTask->id, $list_id, $project_id );
        do_action( 'pm_task_duplicate_after', $newTask->id, $list_id, $project_id, $task );

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

    private function unique_project_title( $title ) {
        if (  ! pm_unique($title, [
            'Project',
            'title'
        ]) ) {
            $title = $title . ' copy';
            return $this->unique_project_title($title);
        }
        return $title;
    }
}
