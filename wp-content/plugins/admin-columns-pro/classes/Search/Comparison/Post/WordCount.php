<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Labels;
use ACP\Search\Operators;

class WordCount extends PostField
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $labels = new Labels([
            Operators::IS_EMPTY     => __('has no content', 'codepress-admin-columns'),
            Operators::NOT_IS_EMPTY => __('has content', 'codepress-admin-columns'),
        ]);

        parent::__construct($operators, null, $labels);
    }

    protected function get_field(): string
    {
        return 'post_content';
    }

}