<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;

class Email extends AC\Column\User\Email
    implements Editing\Editable, Export\Exportable, Search\Searchable
{

    public function editing()
    {
        return new Editing\Service\User\Email($this->get_label());
    }

    public function export()
    {
        return new Export\Model\User\Email();
    }

    public function search()
    {
        return new Search\Comparison\User\Email();
    }

}