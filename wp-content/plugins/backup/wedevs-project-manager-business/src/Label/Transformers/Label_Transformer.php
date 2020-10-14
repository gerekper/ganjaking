<?php

namespace WeDevs\PM_Pro\Label\Transformers;

use League\Fractal\TransformerAbstract;

class Label_Transformer extends TransformerAbstract {
    public function transform( $item ) {
        return [
            'id'          => (int) $item->id,
            'title'       => $item->title,
            'description' => $item->description,
            'color'       => $item->color,
            'status'      => (int) $item->status,
            'project_id'  => (int) $item->project_id,
        ];
    }
}
