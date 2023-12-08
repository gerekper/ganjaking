<?php

namespace ACP\Filtering\Model\Taxonomy;

use AC\Column;
use ACP;

/**
 * @deprecated NEWVERSION
 */
class TaxonomyParent extends ACP\Column\Taxonomy\ParentTerm
{

    public function __construct(Column $column)
    {
        parent::__construct($column->get_taxonomy());
    }

}