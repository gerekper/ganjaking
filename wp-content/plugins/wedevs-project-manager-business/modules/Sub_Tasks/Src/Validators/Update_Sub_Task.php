<?php

namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Validators;

use WeDevs\PM\Core\Validator\Abstract_Validator;

class Update_Sub_Task extends Abstract_Validator {
    public function messages() {
        return [
            'title.required' => __( 'Comment title is required.', 'pm' ),
            'project_id.required' => __( 'Project id is required.', 'pm' ),
            'task_id.required' => __( 'Task id is required.', 'pm' ),
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
