<?php

namespace ACA\Pods\Search;

use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Helper\Select\User\PaginatedFactory;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickUser extends Meta
    implements SearchableValues
{

    private $roles;

    private $query;

    public function __construct(string $meta_key, array $roles, Query $query, string $value_type = null)
    {
        $this->roles = $roles;
        $this->query = $query;

        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::CURRENT_USER,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, $value_type);
    }

    private function formatter(): UserName
    {
        return new UserName();
    }

    public function format_label($value): string
    {
        $user = get_userdata($value);

        return $user ? $this->formatter()->format_label($user) : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'paged'    => $page,
            'search'   => $search,
            'include'  => $this->get_used_user_ids(),
            'role__in' => $this->roles ?: null,
        ]);
    }

    public function get_used_user_ids(): array
    {
        return array_filter($this->query->get(), 'is_numeric');
    }

}