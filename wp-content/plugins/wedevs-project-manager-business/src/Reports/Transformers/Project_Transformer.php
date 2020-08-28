<?php 

namespace WeDevs\PM_Pro\Reports\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Project\Transformers\Project_Transformer as PTransformer;


/**
*   
*/
class Project_Transformer extends PTransformer
{
    
    protected $defaultIncludes = [
        'task_lists'
    ];

    protected $availableIncludes = [
         
    ];

    public function includeTaskLists ( Project $item ) {
        $task_lists = $item->task_lists;
        return $this->collection( $task_lists, new Task_Lists_Transformer );
    }
}