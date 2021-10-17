<?php

namespace WeDevs\PM_Pro\Modules\Invoice\Src\Validators;

use WeDevs\PM_Pro\Core\Validator\Abstract_Validator;

class Create_Invoice extends Abstract_Validator {
    public function messages() {
        return [
            'discount.numeric'        => __( 'Discount should be decimal number.', 'pm' ),
            'discount.partial_amount' => __( 'Partial amount should be decimal number.', 'pm' ),
        ];
    }

    public function rules() {
        return [
            'discount'       => 'numeric',
            'partial_amount' => 'numeric',
        ];
    }
}
