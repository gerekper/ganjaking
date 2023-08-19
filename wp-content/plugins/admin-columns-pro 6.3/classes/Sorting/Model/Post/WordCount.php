<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\WarningAware;

class WordCount extends FieldFormat implements WarningAware
{

    public function __construct()
    {
        parent::__construct('post_content', new FormatValue\WordCount());

        $this->max_value_length = null;
        $this->sort_numeric = true;
    }

}