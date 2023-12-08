<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting;

/**
 * For sorting a list table (e.g. post or user) on a meta_key that holds a User ID (single).
 */
class MetaRelatedUserFactory
{

    public function create(string $meta_type, string $user_property, string $meta_key)
    {
        switch ($meta_type) {
            case MetaType::POST :
                return (new Sorting\Model\Post\MetaRelatedUserFactory())->create($user_property, $meta_key);
            case MetaType::USER :
                return (new Sorting\Model\User\MetaRelatedUserFactory())->create($user_property, $meta_key);
        }

        return null;
    }

}