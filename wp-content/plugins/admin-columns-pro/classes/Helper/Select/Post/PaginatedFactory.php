<?php

declare(strict_types=1);

namespace ACP\Helper\Select\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;

class PaginatedFactory
{

    public function create(
        array $args,
        LabelFormatter $label_formatter = null,
        GroupFormatter $group_formatter = null
    ): Paginated {
        if (null === $label_formatter) {
            $label_formatter = new LabelFormatter\PostTitle();
        }

        if (null === $group_formatter) {
            $group_formatter = new Select\Post\GroupFormatter\PostType();
        }

        $posts = new Query($args);

        $options = new Groups(
            new Options($posts->get_copy(), $label_formatter),
            $group_formatter
        );

        return new Paginated(
            $posts,
            $options
        );
    }

    public function create_media(array $args, LabelFormatter $label_formatter = null): Paginated
    {
        $args = array_merge($args, [
            'post_type'     => 'attachment',
            'orderby'       => 'date',
            'order'         => 'DESC',
            'search_fields' => ['post_title', 'ID', 'post_name'],
        ]);

        return $this->create(
            $args,
            $label_formatter,
            new GroupFormatter\MimeType()
        );
    }

}