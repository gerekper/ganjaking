<?php

namespace WeDevs\PM_Pro\Modules\Gantt\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\Gantt\Src\Models\Gantt;

class Link_Transformer extends TransformerAbstract {

    use Resource_Editors;

    protected $defaultIncludes = [
    ];

    protected $availableIncludes = [
    ];

    public function transform( Gantt $item ) {

        return [
            'id'     => $item->id,
            'source' => $item->source,
            'target' => $item->target,
            'type'   => $item->type,
        ];
    }
}
