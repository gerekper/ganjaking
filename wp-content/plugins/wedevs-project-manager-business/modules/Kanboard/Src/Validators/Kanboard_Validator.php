<?php
namespace WeDevs\PM_Pro\Modules\Kanboard\Src\Validators;

use WeDevs\PM_Pro\Core\Validator\Abstract_Validator;

Class Kanboard_Validator extends Abstract_Validator {

    public function messages() {
        return [
            'title.pm_pro_required' => 'Title field is required.',
        ];
    }
    public function rules() {
        return [
            'title' => 'pm_pro_required',
        ];
    }
}
