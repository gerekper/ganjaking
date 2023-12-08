<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use AC\Settings\Column\Post;
use ACP\Sorting\Type\DataType;

/**
 * For sorting a post list table on a meta_key that holds a Post ID (single).
 */
class MetaRelatedPostFactory
{

    public function create(string $post_property, string $meta_key)
    {
        switch ($post_property) {
            case Post::PROPERTY_TITLE :
                return new RelatedMeta\PostField('post_title', $meta_key);
            case Post::PROPERTY_ID :
                return new Meta($meta_key, new DataType(DataType::NUMERIC));
        }

        return null;
    }

}