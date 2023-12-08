<?php

declare(strict_types=1);

namespace ACP\Helper\Select\CommentField;

use AC\Helper\Select\Options;
use AC\Helper\Select\Options\Paginated;

class PaginatedFactory
{

    public function create(string $field, string $search, int $page): Paginated
    {
        $query = new Query(
            $field,
            [
                'search' => $search,
                'page'   => $page,
            ]
        );

        $values = $query->get();

        $options = array_combine($values, $values);

        return new Paginated(
            $query,
            Options::create_from_array($options)
        );
    }

}