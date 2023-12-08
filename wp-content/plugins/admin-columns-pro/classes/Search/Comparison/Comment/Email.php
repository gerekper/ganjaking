<?php

namespace ACP\Search\Comparison\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\CommentField\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Email extends Field implements SearchableValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::CONTAINS,
            Operators::EQ,
            Operators::NOT_CONTAINS,
            Operators::BEGINS_WITH,
            Operators::ENDS_WITH,
        ], false);

        parent::__construct($operators);
    }

    protected function get_field(): string
    {
        return 'comment_author_email';
    }

    public function format_label($value): string
    {
        return $value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create($this->get_field(), $search, $page);
    }

}