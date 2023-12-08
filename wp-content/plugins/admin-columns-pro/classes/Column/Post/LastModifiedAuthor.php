<?php

namespace ACP\Column\Post;

use AC;
use ACP;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class LastModifiedAuthor extends AC\Column\Post\LastModifiedAuthor
    implements Sorting\Sortable, Export\Exportable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return (new Sorting\Model\Post\LastModifiedAuthorFactory())->create($this->get_user_setting_display());
    }

    public function export()
    {
        return new Export\Model\Post\LastModifiedAuthor($this);
    }

    public function search()
    {
        return new Search\Comparison\Post\LastModifiedAuthor($this->get_post_type());
    }

    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new ACP\Settings\Column\User($this));
    }

}