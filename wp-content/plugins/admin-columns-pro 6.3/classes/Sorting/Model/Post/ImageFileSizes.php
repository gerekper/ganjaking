<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue\ContentTotalImageSize;
use ACP\Sorting\Model\WarningAware;

class ImageFileSizes extends FieldFormat implements WarningAware
{

    public function __construct()
    {
        parent::__construct('post_content', new ContentTotalImageSize());

        $this->max_value_length = null;
        $this->sort_numeric = true;
    }

}