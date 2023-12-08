<?php

namespace ACA\JetEngine\Search\Comparison\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Search\Comparison\Relation;
use ACP\Helper\Select;
use Jet_Engine\Relations\Relation as JetEngineRelation;

class Term extends Relation
{

    private $taxonomy;

    public function __construct(JetEngineRelation $relation, bool $is_parent, string $taxonomy)
    {
        parent::__construct($relation, $is_parent);

        $this->taxonomy = $taxonomy;
    }

    public function format_label($value): string
    {
        return (new Select\Taxonomy\LabelFormatter\TermName())->format_label(get_term((int)$value));
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new Select\Taxonomy\PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ]);
    }

}