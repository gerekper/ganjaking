<?php

namespace WeDevs\PM_Pro\Label\Sanitizers;

use WeDevs\PM_Pro\Core\Sanitizer\Abstract_Sanitizer;

class Label_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'title'       => 'trimer|pm_kses',
            'description' => 'trimer|pm_kses',
            'color'       => 'trimer|pm_kses',
        ];
    }
}
