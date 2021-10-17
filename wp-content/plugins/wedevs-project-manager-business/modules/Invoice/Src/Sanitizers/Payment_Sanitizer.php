<?php

namespace WeDevs\PM_Pro\Modules\Invoice\Src\Sanitizers;

use WeDevs\PM_Pro\Core\Sanitizer\Abstract_Sanitizer;

class Payment_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'paymentNotes'   => 'trimer|pm_kses',
            'paymentGateway' => 'trimer|pm_kses'
        ];
    }
}
