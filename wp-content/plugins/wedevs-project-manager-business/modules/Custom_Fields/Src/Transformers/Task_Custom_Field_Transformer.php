<?php

namespace WeDevs\PM_Pro\Modules\Custom_Fields\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models\Task_Custom_Field;

class Task_Custom_Field_Transformer extends TransformerAbstract {

    use Resource_Editors;

    public function transform( Task_Custom_Field $item ) {

        return [
            'id'         => $item->id,
            'field_id'   => $item->field_id,
            'project_id' => $item->project_id,
            'list_id'    => $item->list_id,
            'task_id'    => $item->task_id,
            'value'      => $item->value,
            'color'      => $item->color
        ];
    }
}
