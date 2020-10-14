<?php

namespace WeDevs\PM_Pro\Reports\Transformers;

use WeDevs\PM\Task_List\Models\Task_List;
use League\Fractal\TransformerAbstract;
use WeDevs\PM\Task\Transformers\Task_Transformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use WeDevs\PM\Task\Models\Task;
use WeDevs\PM_Pro\User\Models\User;
use Carbon\Carbon;

class Task_Lists_Transformer extends TransformerAbstract {

    public $task_ids;
    public function __construct( $task_ids = null )
    {
        $this->task_ids = $task_ids;
    }

    protected $defaultIncludes = [
        'tasks'
    ];


    public function transform( Task_List $item ) {
        return [
            'id'          => (int) $item->id,
            'title'       => $item->title,
            'description' => $item->description,
            'order'       => (int) $item->order,
            'created_at'  => format_date( $item->created_at ),
        ];
    }

    public function includeTasks( Task_List $item ) {
        $tasks = $item->tasks;
        // $Task_Transformer = 
        // $default_inludes = apply_filters('reports_task_transformer_default_includes', ['creator','assignees'] );
        // $Task_Transformer = $Task_Transformer->setDefaultIncludes( $default_inludes );
        
        return $this->collection( $tasks , new Task_Transformer );
    }
}