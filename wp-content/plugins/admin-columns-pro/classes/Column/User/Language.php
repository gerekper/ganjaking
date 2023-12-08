<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Language extends AC\Column\Meta
    implements Editing\Editable, Sorting\Sortable, Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-user_default_language');
        $this->set_label(__('Language'));
    }

    public function get_meta_key()
    {
        return 'locale';
    }

    public function get_value($id)
    {
        $translations = (new AC\Helper\User())->get_translations_remote();

        $locale = $this->get_raw_value($id);

        return $translations[$locale]['native_name'] ??
               ac_helper()->html->tooltip(
                   $this->get_empty_char(),
                   _x('Site Default', 'default site language')
               );
    }

    public function editing()
    {
        return new Editing\Service\User\LanguageRemote();
    }

    public function sorting()
    {
        return new Sorting\Model\User\Meta($this->get_meta_key());
    }

    public function search()
    {
        return new Search\Comparison\User\Languages();
    }

}