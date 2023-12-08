<?php

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use AC\Meta\QueryMetaFactory;
use ACP\Helper\Select;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Helper\Select\User\PaginatedFactory;
use ACP\Search\Comparison;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class LastModifiedAuthor extends Comparison\Meta
    implements SearchableValues
{

    private $post_type;

    public function __construct(string $post_type)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, '_edit_last');

        $this->post_type = $post_type;
    }

    protected function get_label_formatter(): UserName
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
        return (new PaginatedFactory())->create([
            'paged'   => $page,
            'search'  => $search,
            'include' => $this->get_meta_query_authors()->get(),
        ], $this->get_label_formatter());
    }

    private function get_meta_query_authors(): Query
    {
        return (new QueryMetaFactory())->create_with_post_type(
            $this->meta_key,
            $this->post_type
        );
    }

}