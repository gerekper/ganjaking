<?php

namespace WeDevs\PM_Pro\Modules\Custom_Fields\Src\Sanitizers;

use WeDevs\PM_Pro\Core\Sanitizer\Abstract_Sanitizer;

class Custom_Field_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'title'       => 'trimer|pm_kses',
            'type'        => 'trimer|pm_kses',
            'description' => 'trimer|pm_kses',
        ];
    }
}
