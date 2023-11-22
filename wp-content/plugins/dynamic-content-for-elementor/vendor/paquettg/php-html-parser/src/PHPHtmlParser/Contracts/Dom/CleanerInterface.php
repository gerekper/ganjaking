<?php

namespace DynamicOOOS\PHPHtmlParser\Contracts\Dom;

use DynamicOOOS\PHPHtmlParser\Exceptions\LogicalException;
use DynamicOOOS\PHPHtmlParser\Options;
interface CleanerInterface
{
    /**
     * Cleans the html of any none-html information.
     *
     * @throws LogicalException
     */
    public function clean(string $str, Options $options, string $defaultCharset) : string;
}
