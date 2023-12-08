<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Settings;
use ACP\Sorting;

class AuthorName extends AC\Column\Post\AuthorName
    implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return (new Sorting\Model\Post\AuthorFactory())->create((string)$this->get_user_setting()->get_value());
    }

    public function editing()
    {
        if (Settings\Column\User::PROPERTY_META === $this->get_user_setting()->get_value()) {
            return false;
        }

        return new Editing\Service\Post\Author();
    }

    public function export()
    {
        if (Settings\Column\User::PROPERTY_GRAVATAR === $this->get_user_setting()->get_value()) {
            return new Export\Model\Post\AuthorGravatar();
        }

        return new Export\Model\StrippedValue($this);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Column\User($this));
    }

    /**
     * @return AC\Settings\Column\User
     */
    private function get_user_setting()
    {
        return $this->get_setting(AC\Settings\Column\User::NAME);
    }

    public function search()
    {
        switch ($this->get_user_setting()->get_value()) {
            case AC\Settings\Column\User::PROPERTY_FIRST_NAME :
                return new Search\Comparison\Post\AuthorMeta('first_name');
            case AC\Settings\Column\User::PROPERTY_LAST_NAME :
                return new Search\Comparison\Post\AuthorMeta('last_name');
            case AC\Settings\Column\User::PROPERTY_NICKNAME :
                return new Search\Comparison\Post\AuthorMeta('nickname');
            case AC\Settings\Column\User::PROPERTY_ROLES :
                return new Search\Comparison\Post\AuthorRole();
            case AC\Settings\Column\User::PROPERTY_NICENAME :
                return new Search\Comparison\Post\AuthorField('user_nicename');
            case AC\Settings\Column\User::PROPERTY_LOGIN :
                return new Search\Comparison\Post\AuthorField('user_login');
            case AC\Settings\Column\User::PROPERTY_EMAIL :
                return new Search\Comparison\Post\AuthorField('user_email');
            case AC\Settings\Column\User::PROPERTY_URL :
                return new Search\Comparison\Post\AuthorField('user_url');
            case AC\Settings\Column\User::PROPERTY_FULL_NAME :
            case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
            case AC\Settings\Column\User::PROPERTY_ID :
                return new Search\Comparison\Post\Author($this->get_post_type());
            default:
                return false;
        }
    }

}