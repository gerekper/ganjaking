<?php

namespace ACA\EC\Search\Event;

use AC;
use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Relation extends Meta
    implements SearchableValues
{

    private $relation;

    public function __construct(string $meta_key, AC\Relation\Post $relation)
    {
        $this->relation = $relation;
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key);
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? (new PostTitle())->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $this->relation->get_post_type_object()->name,
        ]);
    }

}