<?php
namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Models;

use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM\Common\Models\Assignee;

class Sub_Tasks extends Task {

	public function task_lists() {
        return $this->belongsToMany( 'WeDevs\PM\Task_List\Models\Task_List', pm_tb_prefix() . 'pm_boardables', 'boardable_id', 'board_id' )
            ->where( pm_tb_prefix() . 'pm_boardables.board_type', 'task_list')
            ->where( pm_tb_prefix() . 'pm_boardables.boardable_type', 'sub_task');
    }

    public function boardables() {
        return $this->hasMany( 'WeDevs\PM\Common\Models\Boardable', 'boardable_id' )->where( 'boardable_type', 'sub_task' );
    }

    public function subtask_metas() {
        return $this->hasMany( 'WeDevs\PM\Common\Models\Meta', 'entity_id' )
            ->where( 'entity_type', 'sub_task' );
    }

    public function subtasks( $project_id = false ) {
        $sub_tasks = $this->belongsToMany( 'WeDevs\PM\Task\Models\Task', pm_tb_prefix() . 'pm_boardables', 'board_id', 'boardable_id' )
            ->where( pm_tb_prefix() . 'pm_boardables.boardable_type', 'sub_task' )
            ->where( pm_tb_prefix() . 'pm_boardables.board_type', 'task_list' )
            ->withPivot( 'order' );

        // if ( $project_id ) {
        //     $tasks = apply_filters( 'pm_filter_task_permission', $tasks,  $project_id );
        // }


        return $sub_tasks;
    }

}
