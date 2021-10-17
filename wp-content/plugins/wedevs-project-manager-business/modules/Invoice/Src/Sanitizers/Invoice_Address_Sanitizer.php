<?php

namespace WeDevs\PM_Pro\Modules\Invoice\Src\Sanitizers;

use WeDevs\PM_Pro\Core\Sanitizer\Abstract_Sanitizer;

class Invoice_Address_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'organization'   => 'trimer|pm_kses',
            'address_line_1' => 'trimer|pm_kses',
            'address_line_2' => 'trimer|pm_kses',
            'city'           => 'trimer|pm_kses',
            'sate_province'  => 'trimer|pm_kses',
            'zip_code'       => 'trimer|pm_kses',
            'country_code'   => 'trimer|pm_kses',
        ];
    }
}
