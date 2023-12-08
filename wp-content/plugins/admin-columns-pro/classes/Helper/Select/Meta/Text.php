<?php

declare(strict_types=1);

namespace ACP\Helper\Select\Meta;

use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use AC\Meta\Query;

class Text extends ArrayIterator
    implements Paginated
{

    public function __construct(Query $query)
    {
        $data = $query->get();

        parent::__construct( array_combine( $data, $data ) );
    }

    public function get_total_pages(): int
    {
        return 1;
    }

    public function get_page(): int
    {
        return 1;
    }

    public function is_last_page(): bool
    {
        return true;
    }

}