<?php

namespace WeDevs\PM_Pro\Modules\Invoice\Src\Sanitizers;

use WeDevs\PM_Pro\Core\Sanitizer\Abstract_Sanitizer;

class Invoice_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'title'       => 'trimer|sanitize_text_field',
            'terms'       => 'trimer|pm_kses',
            'client_note' => 'trimer|pm_kses',
        ];
    }
}
