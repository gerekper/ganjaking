<?php

declare (strict_types=1);
namespace DynamicOOOS\PHPHtmlParser\Discovery;

use DynamicOOOS\PHPHtmlParser\Contracts\Dom\ParserInterface;
use DynamicOOOS\PHPHtmlParser\Dom\Parser;
class DomParserDiscovery
{
    /**
     * @var ParserInterface|null
     */
    private static $parser = null;
    public static function find() : ParserInterface
    {
        if (self::$parser == null) {
            self::$parser = new Parser();
        }
        return self::$parser;
    }
}
