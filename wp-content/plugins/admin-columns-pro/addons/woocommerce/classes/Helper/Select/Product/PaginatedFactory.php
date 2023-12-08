<?php

declare(strict_types=1);

namespace ACA\WC\Helper\Select\Product;

use AC\Helper\Select\Options\Paginated;

class PaginatedFactory
{

    public function create(
        array $args,
        LabelFormatter $formatter = null,
        GroupFormatter $group_formatter = null
    ): Paginated {
        if (null === $formatter) {
            $formatter = new LabelFormatter\ProductTitle();
        }

        if ( ! isset($args['posts_per_page'])) {
            $args['posts_per_page'] = 50;
        }

        $posts = new Product($args);
        $options = new Options($posts->get_copy(), $formatter);

        if ($group_formatter) {
            $options = new Groups($options, $group_formatter);
        }

        return new Paginated(
            $posts,
            $options
        );
    }

}