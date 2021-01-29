<?php

namespace WeDevs\PM_Pro\Modules\Woo_Project\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Models\User;

class Product_Transformer extends TransformerAbstract {

    protected $defaultIncludes =[

    ];

    /**
     * Turn  Products object into a generic array
     *
     * @return array
     */
    public function transform($item){
        return [
            'id'        => $item->ID,
            'title'     => $item->post_title,
        ];
    }

}
