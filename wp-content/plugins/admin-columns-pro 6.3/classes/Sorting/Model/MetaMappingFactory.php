<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\AbstractModel;

/**
 * Sorts a list table by pre sorted fields that are associated with the supplied meta key.
 */
class MetaMappingFactory
{

    public function create(string $meta_type, string $meta_key, array $fields): AbstractModel
    {
        switch ($meta_type) {
            case MetaType::POST :
                return new Post\MetaMapping($meta_key, $fields);
            case MetaType::USER :
                return new User\MetaMapping($meta_key, $fields);
            case MetaType::COMMENT :
                return new Comment\MetaMapping($meta_key, $fields);
            case MetaType::TERM :
                return new Taxonomy\MetaMapping($meta_key, $fields);
            default :
                return new Disabled();
        }
    }

}