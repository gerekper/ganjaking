<?php

namespace ACP\Editing\PaginatedOptions;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptionsFactory;
use ACP\Helper\Select\Post\PaginatedFactory;

class Posts implements PaginatedOptionsFactory
{

    /**
     * @var string[]
     */
    private $post_types;

    /**
     * @var array
     */
    private $args;

    public function __construct($post_types = null, array $args = [])
    {
        $this->post_types = empty($post_types) ? ['any'] : (array)$post_types;
        $this->args = $args;
    }

    public function create(string $search, int $page, int $id = null): Paginated
    {
        $args = array_merge([
            'paged'     => $page,
            's'         => $search,
            'post_type' => $this->post_types,
        ], $this->args);

        return (new PaginatedFactory())->create($args);
    }

}