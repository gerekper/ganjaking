<?php

namespace WeDevs\PM_Pro\Modules\Custom_Fields\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models\Custom_Field;

class Custom_Field_Transformer extends TransformerAbstract {

    use Resource_Editors;

    public function transform( Custom_Field $item ) {
        return [
            'id'          => $item->id,
            'project_id'  => $item->project_id,
            'title'       => $item->title,
            'description' => $item->description,
            'type'        => $item->type,
            'options'     => maybe_unserialize( $item->optional_value ),
            'order'       => $item->order,
            'value'       => empty( $item->value->toArray() ) ? new \stdClass : $item->value->toArray()[0]
        ];
    }
}
