<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting;

class MetaRelatedPostFactory
{

    public function create(string $meta_type, string $post_property, string $meta_key)
    {
        switch ($meta_type) {
            case MetaType::POST :
                return (new Sorting\Model\Post\MetaRelatedPostFactory())->create($post_property, $meta_key);
            case MetaType::USER :
                return (new Sorting\Model\User\MetaRelatedPostFactory())->create($post_property, $meta_key);
        }

        return null;
    }

}