<?php

namespace ACA\YoastSeo\Search\Post;

use AC\Helper\Select\Options;
use AC\Meta\Query;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class PrimaryTaxonomy extends ACP\Search\Comparison\Meta
    implements ACP\Search\Comparison\RemoteValues
{

    /**
     * @var Query
     */
    private $query;

    /**
     * @var string
     */
    private $taxonomy;

    public function __construct(string $meta_key, string $taxonomy, Query $query)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators, $meta_key, Value::INT);

        $this->query = $query;
        $this->taxonomy = $taxonomy;
    }

    public function format_label(string $value): string
    {
        $term = get_term_by('id', $value, $this->taxonomy);

        return $term ? $term->name : $value;
    }

    public function get_values(): Options
    {
        $values = [];
        foreach ($this->query->get() as $value) {
            $values[$value] = $this->format_label($value);
        }

        return Options::create_from_array($values);
    }

}