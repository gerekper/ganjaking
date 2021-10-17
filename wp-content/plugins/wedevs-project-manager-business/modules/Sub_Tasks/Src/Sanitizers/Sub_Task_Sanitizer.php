<?php

namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Sanitizers;

use WeDevs\PM_Pro\Core\Sanitizer\Abstract_Sanitizer;

class Sub_Task_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'title'       => 'trimer|pm_kses',
            'description' => 'trimer|pm_kses',
        ];
    }
}
