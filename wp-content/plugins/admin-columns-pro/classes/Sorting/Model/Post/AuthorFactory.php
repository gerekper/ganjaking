<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use AC;

class AuthorFactory
{

    public function create(string $type)
    {
        switch ($type) {
            case AC\Settings\Column\User::PROPERTY_FIRST_NAME :
            case AC\Settings\Column\User::PROPERTY_LAST_NAME :
            case AC\Settings\Column\User::PROPERTY_NICKNAME :
                return new Author\UserMeta($type);
            case AC\Settings\Column\User::PROPERTY_LOGIN :
            case AC\Settings\Column\User::PROPERTY_NICENAME :
            case AC\Settings\Column\User::PROPERTY_EMAIL :
            case AC\Settings\Column\User::PROPERTY_ID :
            case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
            case AC\Settings\Column\User::PROPERTY_URL :
                return new Author\UserField($type);
            case AC\Settings\Column\User::PROPERTY_FULL_NAME :
                return new Author\FullName();
            case AC\Settings\Column\User::PROPERTY_ROLES :
                return new Author\Roles();
            default:
                return null;
        }
    }

}