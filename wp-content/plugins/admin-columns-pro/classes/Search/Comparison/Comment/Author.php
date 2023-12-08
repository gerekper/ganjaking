<?php

namespace ACP\Search\Comparison\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Author extends Field
    implements SearchableValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
            Operators::BEGINS_WITH,
            Operators::ENDS_WITH,
            Operators::CURRENT_USER,
        ]);

        parent::__construct($operators);
    }

    protected function get_field(): string
    {
        return 'user_id';
    }

    private function get_user_ids(): array
    {
        global $wpdb;

        return $wpdb->get_col("SELECT DISTINCT user_id FROM $wpdb->comments;");
    }

    private function get_label_formatter(): UserName
    {
        return new UserName();
    }

    public function format_label($value): string
    {
        $user = get_userdata($value);

        return $user
            ? $this->get_label_formatter()->format_label($user)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new Select\User\PaginatedFactory())->create([
            'search'  => $search,
            'paged'   => $page,
            'include' => $this->get_user_ids(),
        ],
            $this->get_label_formatter()
        );
    }

}