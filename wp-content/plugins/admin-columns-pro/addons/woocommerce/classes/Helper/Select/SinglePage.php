<?php

namespace ACA\WC\Helper\Select;

use AC\Helper\Select\Paginated;

class SinglePage implements Paginated
{

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