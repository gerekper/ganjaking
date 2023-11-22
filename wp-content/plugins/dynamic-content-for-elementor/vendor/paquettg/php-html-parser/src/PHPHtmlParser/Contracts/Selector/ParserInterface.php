<?php

declare (strict_types=1);
namespace DynamicOOOS\PHPHtmlParser\Contracts\Selector;

use DynamicOOOS\PHPHtmlParser\DTO\Selector\ParsedSelectorCollectionDTO;
interface ParserInterface
{
    public function parseSelectorString(string $selector) : ParsedSelectorCollectionDTO;
}
