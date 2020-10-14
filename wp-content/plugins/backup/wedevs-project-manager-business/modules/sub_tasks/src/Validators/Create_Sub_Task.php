<?php

namespace WeDevs\PM_Pro\Modules\sub_tasks\src\Validators;

use WeDevs\PM_Pro\Core\Validator\Abstract_Validator;

class Create_Sub_Task extends Abstract_Validator {
    public function messages() {
        return [
            'title.required' => __( 'Sub task discription is required.', 'pm' ),
            'project_id.required'  => __( 'Project id is required.', 'pm' ),
            'task_id.required'     => __( 'Task id is required.', 'pm' ),
        ];
    }

    public function rules() {
        return [
            'title' => 'required',
            'project_id'  => 'required',
            'task_id'     => 'required'
        ];
    }
}