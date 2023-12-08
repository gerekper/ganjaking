<?php

namespace ACP\Sorting\Model\Comment;

use AC;
use AC\Column;
use LogicException;

class AuthorFactory
{

    public function create(string $type, Column $column = null)
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
                return new Author\UserField($type);
            case AC\Settings\Column\User::PROPERTY_FULL_NAME :
                return new Author\FullName();
            case AC\Settings\Column\User::PROPERTY_ROLES :
                if ( ! $column) {
                    throw new LogicException('Missing column');
                }

                return new Author($column->get_setting('user'));
            default:
                return null;
        }
    }

}