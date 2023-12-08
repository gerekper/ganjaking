<?php

namespace ACA\Pods\Search;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Comment\LabelFormatter\CommentTitle;
use ACP\Helper\Select\Comment\PaginatedFactory;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickComment extends Meta
    implements SearchableValues
{

    public function __construct(string $meta_key, string $value_type = null)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, $value_type);
    }

    public function format_label($value): string
    {
        $comment = get_comment($value);

        return $comment
            ? (new CommentTitle())->format_label($comment)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search' => $search,
            'paged'  => $page,
        ]);
    }

}