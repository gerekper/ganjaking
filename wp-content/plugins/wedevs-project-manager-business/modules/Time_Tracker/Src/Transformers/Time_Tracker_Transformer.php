<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker;
use WeDevs\PM\Task\Transformers\Task_Transformer;

class Time_Tracker_Transformer extends TransformerAbstract {

    use Resource_Editors;

    protected $defaultIncludes = [
        'user'
    ];

    protected $availableIncludes = [
        'user'
    ];

    public function transform( Time_Tracker $item ) {
        return [
            'id'         => $item->id,
            'user_id'    => $item->user_id,
            'project_id' => $item->project_id,
            'list_id'    => $item->list_id,
            'task_id'    => $item->task_id,
            'start'      => format_date( make_carbon_date( date( 'Y-m-d H:i:s', $item->start ) ) ),
            'stop'       => format_date( make_carbon_date( date( 'Y-m-d H:i:s', $item->stop ) ) ),
            'total'      => pm_pro_second_to_time( $item->total ),
            'run_status' => $item->run_status,
            'created_by' => $item->created_by,
            'updated_by' => $item->updated_by,
            'updated_at' => $item->updated_at,
            'created_at' => format_date( $item->created_at ),

        ];
    }

    public function includeTask( Time_Tracker $item ) {
        $task = $item->task->first();

        if ( $task ) {
            $resource = $this->item( $task, new Task_Transformer );
        }

        return $resource;
    }

    public function includeUser( Time_Tracker $item ) {
        $user = $item->user;

        if ( $user ) {
            $resource = $this->item( $user, new User_Transformer );
        }

        return $resource;
    }
}
