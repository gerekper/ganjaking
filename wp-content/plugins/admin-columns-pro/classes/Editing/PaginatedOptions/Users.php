<?php

namespace ACP\Editing\PaginatedOptions;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptionsFactory;
use ACP\Helper\Select\User\PaginatedFactory;

class Users implements PaginatedOptionsFactory
{

    /**
     * @var array
     */
    private $args;

    public function __construct(array $args = [])
    {
        $this->args = $args;
    }

    public function create(string $search, int $page, int $id = null): Paginated
    {
        $args = array_merge([
            'paged'  => $page,
            'search' => $search,
        ], $this->args);

        return (new PaginatedFactory())->create($args);
    }

}