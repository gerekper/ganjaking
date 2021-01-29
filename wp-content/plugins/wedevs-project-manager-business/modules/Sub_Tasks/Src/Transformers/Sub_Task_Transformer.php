<?php

namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Transformers;

use WeDevs\PM\Task\Transformers\Task_Transformer;
use WeDevs\PM\Task\Models\Task;


class Sub_Task_Transformer extends Task_Transformer {

    public function meta( Task $item ) {

        $meta = $item->subtask_metas()->get()->toArray();
        $meta = wp_list_pluck( $meta, 'meta_value', 'meta_key' );

        $metas = array_merge( $meta, [
            'total_comment'  => $item->comments->count(),
            'total_files'    => $item->files->count(),
            'total_board'    => $item->boards->count(),
            'total_assignee' => $item->assignees->count(),
            'can_complete_task' => pm_user_can_complete_task( $item ),
        ] );

	    return $metas;
    }
}
