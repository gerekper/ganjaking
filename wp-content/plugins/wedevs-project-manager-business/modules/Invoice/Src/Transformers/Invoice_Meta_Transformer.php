<?php

namespace WeDevs\PM_Pro\Modules\Invoice\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM\Common\Models\Meta;

class Invoice_Meta_Transformer extends TransformerAbstract {

    use Resource_Editors;

    public function transform( Meta $item ) {
        $data = maybe_unserialize( $item->meta_value );

        return [
            'id'      => $item->id,
            'amount'  => $data['amount'],
            'date'    => format_date( make_carbon_date( $data['date'] ) ),
            'notes'   => $data['notes'],
            'gateway' => $data['gateway'],
        ];
    }
}
