<?php

declare(strict_types=1);

namespace ACP\Helper\Select\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Comment\LabelFormatter\CommentTitle;

class PaginatedFactory
{

    public function create(array $args, LabelFormatter $formatter = null): Paginated
    {
        if (null === $formatter) {
            $formatter = new CommentTitle();
        }

        $comments = new Query($args);
        $options = new Options(
            $comments->get_copy(),
            $formatter
        );

        return new Paginated(
            $comments,
            $options
        );
    }

}