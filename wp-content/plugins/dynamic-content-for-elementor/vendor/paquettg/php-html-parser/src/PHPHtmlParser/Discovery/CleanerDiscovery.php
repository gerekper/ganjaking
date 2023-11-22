<?php

declare (strict_types=1);
namespace DynamicOOOS\PHPHtmlParser\Discovery;

use DynamicOOOS\PHPHtmlParser\Contracts\Dom\CleanerInterface;
use DynamicOOOS\PHPHtmlParser\Dom\Cleaner;
class CleanerDiscovery
{
    /**
     * @var Cleaner|null
     */
    private static $parser = null;
    public static function find() : CleanerInterface
    {
        if (self::$parser == null) {
            self::$parser = new Cleaner();
        }
        return self::$parser;
    }
}
