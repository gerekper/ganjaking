<?php

namespace ACP\Sorting\Model;

use AC\MetaType;

/**
 * Sort a user list table on the number of times the meta_key is used by an object.
 */
class MetaCountFactory
{

    public function create(string $meta_type, string $meta_key)
    {
        switch ($meta_type) {
            case MetaType::POST :
                return new Post\MetaCount($meta_key);
            case MetaType::USER :
                return new User\MetaCount($meta_key);
            case MetaType::COMMENT :
                return new Comment\MetaCount($meta_key);
            case MetaType::TERM :
                return new Taxonomy\MetaCount($meta_key);
            default :
                return null;
        }
    }

}