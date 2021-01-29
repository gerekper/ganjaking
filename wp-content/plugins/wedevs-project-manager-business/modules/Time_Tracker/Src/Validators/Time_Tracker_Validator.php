<?php
namespace WeDevs\PM_Pro\Modules\Time_Tracker\Src\Validators;

use WeDevs\PM_Pro\Core\Validator\Abstract_Validator;

Class Time_Tracker_Validator extends Abstract_Validator {

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
