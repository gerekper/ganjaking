<?php

namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Validators;

use WeDevs\PM_Pro\Core\Validator\Abstract_Validator;

class Create_Sub_Task extends Abstract_Validator {
    public function messages() {
        return [
            'title.required'      => __( 'Sub task description is required.', 'pm' ),
            'project_id.required' => __( 'Project id is required.', 'pm' ),
            'project_id.numeric'  => __( 'Project id should be numeric.', 'pm' ),
            'task_id.required'    => __( 'Task id is required.', 'pm' ),
            'task_id.numeric'     => __( 'Task id should be numeric.', 'pm' ),
        ];
    }

    public function rules() {
        return [
            'title'      => 'required',
            'project_id' => 'required|numeric',
            'task_id'    => 'required|numeric',
        ];
    }
}
