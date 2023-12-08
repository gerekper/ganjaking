<?php

namespace ACP\Search\Comparison\Post;

use AC\Meta\QueryMetaFactory;
use ACP\Search\Comparison;

class FeaturedImage extends Comparison\Meta\Attachment
{

    public function __construct($post_type)
    {
        $query = (new QueryMetaFactory())->create_with_post_type('_thumbnail_id', $post_type);

        parent::__construct('_thumbnail_id', $query, 'image');
    }

}