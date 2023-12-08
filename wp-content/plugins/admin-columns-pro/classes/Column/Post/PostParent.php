<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class PostParent extends AC\Column\Post\PostParent
    implements Sorting\Sortable, Editing\Editable, Export\Exportable, Search\Searchable,
               ConditionalFormat\Formattable
{

    public function sorting()
    {
        return new Sorting\Model\Post\PostParent();
    }

    public function editing()
    {
        return new Editing\Service\Post\PostParent($this->get_post_type());
    }

    public function export()
    {
        return new Export\Model\Post\PostParent();
    }

    public function search()
    {
        return new Search\Comparison\Post\PostParent($this->get_post_type(), [$this->get_post_type()]);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(
            new ConditionalFormat\Formatter\FilterHtmlFormatter(new ConditionalFormat\Formatter\StringFormatter())
        );
    }

}