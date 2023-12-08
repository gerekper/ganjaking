<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;

class Author extends AC\Column\Media\Author
    implements Editing\Editable, Export\Exportable, Search\Searchable
{

    public function search()
    {
        return new Search\Comparison\Post\Author($this->get_post_type());
    }

    public function editing()
    {
        return new Editing\Service\Post\Author();
    }

    public function export()
    {
        return new Export\Model\Post\Author();
    }

}