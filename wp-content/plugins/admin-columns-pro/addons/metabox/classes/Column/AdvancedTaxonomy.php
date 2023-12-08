<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Editing;
use ACA\MetaBox\Editing\StorageFactory;
use ACA\MetaBox\Sorting;
use ACP;

class AdvancedTaxonomy extends Taxonomy
    implements ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function sorting()
    {
        return (new Sorting\Factory\AdvancedTaxonomy())->create($this);
    }

    public function editing()
    {
        return $this->is_clonable()
            ? false
            : new Editing\Service\Taxonomy(
                (new StorageFactory())->create($this),
                $this->get_taxonomy()
            );
    }

}