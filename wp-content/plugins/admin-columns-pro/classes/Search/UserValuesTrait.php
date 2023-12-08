<?php

namespace ACP\Search;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Helper\Select\User\PaginatedFactory;

trait UserValuesTrait
{

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
        $args = [
            'paged'  => $page,
            'search' => $search,
        ];

        if ($this->query instanceof \AC\Meta\Query) {
            $args['include'] = $this->query->get();
        }

        return (new PaginatedFactory())->create($args, $this->get_label_formatter());
    }

}