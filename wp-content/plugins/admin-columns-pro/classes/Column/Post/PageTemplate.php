<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class PageTemplate extends AC\Column\Post\PageTemplate
    implements Sorting\Sortable, Editing\Editable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Post\PageTemplate($this->get_post_type(), $this->get_meta_key());
    }

    public function editing()
    {
        if ( ! $this->get_page_templates()) {
            return false;
        }

        return new Editing\Service\Post\PageTemplate($this->get_post_type());
    }

    public function search()
    {
        $templates = $this->get_page_templates();

        return ! empty($templates)
            ? new Search\Comparison\Post\PageTemplate($templates)
            : false;
    }

}