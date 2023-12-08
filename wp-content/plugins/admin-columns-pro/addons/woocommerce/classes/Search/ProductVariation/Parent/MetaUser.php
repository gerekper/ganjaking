<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class MetaUser extends Meta implements Comparison\SearchableValues
{

    public function __construct(string $meta_key)
    {
        parent::__construct(
            $meta_key,
            new Operators([
                Operators::EQ,
            ], false)
        );
    }

    public function format_label($value): string
    {
        $user = get_user_by('id', $value);

        return $user
            ? $this->get_label_formatter()->format_label($user)
            : '';
    }

    protected function get_label_formatter()
    {
        return new ACP\Helper\Select\User\LabelFormatter\UserName();
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new ACP\Helper\Select\User\PaginatedFactory())->create([
            'search' => $search,
            'paged'  => $page,
        ], $this->get_label_formatter());
    }
}