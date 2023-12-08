<?php

namespace ACP\Search\Comparison\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Comment\LabelFormatter;
use ACP\Helper\Select\Comment\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;

class ReplyTo extends Comparison implements SearchableValues
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return (new Bindings\Comment())->parent($value->get_value());
    }

    private function formatter(): LabelFormatter\CommentTitle
    {
        return new LabelFormatter\CommentTitle();
    }

    public function format_label($value): string
    {
        $comment = get_comment($value);

        return $comment
            ? $this->formatter()->format_label($comment)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'      => $search,
            'paged'       => $page,
            'comment__in' => $this->get_parents(),
        ]);
    }

    private function get_parents(): ?array
    {
        global $wpdb;

        $limit = 5000;

        $results = $wpdb->get_col(
            "
			SELECT DISTINCT( comment_parent )
			FROM $wpdb->comments
			WHERE comment_parent != '' 
			LIMIT $limit
		"
        );

        if ( ! $results || $limit <= count($results)) {
            return null;
        }

        return $results;
    }

}