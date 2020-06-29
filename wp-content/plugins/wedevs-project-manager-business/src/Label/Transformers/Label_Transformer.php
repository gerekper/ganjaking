<?php

namespace WeDevs\PM_Pro\Label\Transformers;

use League\Fractal\TransformerAbstract;

class Label_Transformer extends TransformerAbstract {
    public function transform( $item ) {
        return [
            'id'          => $item->id,
            'title'       => $item->title,
            'description' => $item->description,
            'color'       => $item->color,
            'status'      => $item->status,
            'project_id'  => $item->project_id,
        ];
    }
}
