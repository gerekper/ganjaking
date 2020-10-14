<?php

namespace WeDevs\PM_Pro\Common\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\Common\Models\Meta;

class Meta_Transformer extends TransformerAbstract {

    public function transform( Meta $item ) {
        return [
            'id'         => (int) $item->id,
            'meta_key'   => $item->meta_key,
            'meta_value' => $item->meta_value,
            'type'       => $item->entity_type
        ];
    }
}