<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\Type\DataType;

/**
 * Sorts a list table by the meta value (raw db value) that is associated with the supplied meta key.
 */
class MetaFactory
{

    public function create(string $meta_type, string $meta_key, DataType $data_type = null)
    {
        switch ($meta_type) {
            case MetaType::POST :
                return new Post\Meta($meta_key, $data_type);
            case MetaType::USER :
                return new User\Meta($meta_key, $data_type);
            case MetaType::COMMENT :
                return new Comment\Meta($meta_key, $data_type);
            case MetaType::TERM :
                return new Taxonomy\Meta($meta_key, $data_type);
            default :
                return null;
        }
    }

}