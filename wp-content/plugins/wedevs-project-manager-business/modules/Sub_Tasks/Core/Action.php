<?php

namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Core;

use WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller;
use WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Models\Sub_Tasks;

class Action {

    public function __construct() {
        add_action( 'pm_after_task_duplicate', [ $this, 'after_task_duplicate' ], 10, 2 );
    }

    public function after_task_duplicate( $new_task, $old_task ) {

        $subtasks = Sub_Tasks::where( 'parent_id', $old_task->id );

        foreach ( $subtasks->get() as $key => $subtask ) {
            $new_sub_task = $this->replicate( $subtask, [
                'parent_id' => $new_task['data']['id']
            ] );

            if ( empty( $new_sub_task ) ) {
                continue;
            }

            foreach ( $subtask->boardables as $key => $boardable ) {
                $this->replicate( $boardable, [
                    'boardable_id' => $new_sub_task->id
                ] );
            }

            foreach ( $subtask->assignees as $assignee ) {
                $newAssignee = $this->replicate( $assignee, [
                    'task_id' => $new_sub_task->id
                ] );
            }

            foreach ( $subtask->metas as $meta ) {
                $newMeta = $this->replicate( $meta, [
                    'entity_id' => $new_sub_task->id
                ] );
            }
        };

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
}
