<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use AC;

class LastModifiedAuthorFactory
{

    public function create(string $type)
    {
        switch ($type) {
            case AC\Settings\Column\User::PROPERTY_FIRST_NAME :
            case AC\Settings\Column\User::PROPERTY_LAST_NAME :
            case AC\Settings\Column\User::PROPERTY_NICKNAME :
                return new RelatedMeta\UserMeta($type, '_edit_last');
            case AC\Settings\Column\User::PROPERTY_LOGIN :
            case AC\Settings\Column\User::PROPERTY_NICENAME :
            case AC\Settings\Column\User::PROPERTY_EMAIL :
            case AC\Settings\Column\User::PROPERTY_ID :
            case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
                return new RelatedMeta\UserField($type, '_edit_last');
            case AC\Settings\Column\User::PROPERTY_FULL_NAME :
                return new RelatedMeta\UserMeta('last_name', '_edit_last');
            case AC\Settings\Column\User::PROPERTY_ROLES :
            default:
                return null;
        }
    }

}