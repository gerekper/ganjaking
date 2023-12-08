<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use AC;
use ACP\Sorting\Type\DataType;

/**
 * For sorting a post list table on a meta_key that holds a User ID (single).
 */
class MetaRelatedUserFactory
{

    public function create(string $user_property, string $meta_key)
    {
        switch ($user_property) {
            case AC\Settings\Column\User::PROPERTY_ID :
                return new Meta($meta_key, new DataType(DataType::NUMERIC));
            case AC\Settings\Column\User::PROPERTY_LOGIN :
            case AC\Settings\Column\User::PROPERTY_NICENAME :
            case AC\Settings\Column\User::PROPERTY_EMAIL :
            case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
                return new RelatedMeta\UserField($user_property, $meta_key);
            case AC\Settings\Column\User::PROPERTY_FULL_NAME :
                return new RelatedMeta\UserMeta('last_name', $meta_key);
            case AC\Settings\Column\User::PROPERTY_LAST_NAME :
            case AC\Settings\Column\User::PROPERTY_FIRST_NAME :
            case AC\Settings\Column\User::PROPERTY_NICKNAME :
                return new RelatedMeta\UserMeta($user_property, $meta_key);
        }

        return null;
    }
}