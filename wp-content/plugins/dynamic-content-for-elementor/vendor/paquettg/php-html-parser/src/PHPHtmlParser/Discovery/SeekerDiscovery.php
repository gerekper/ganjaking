<?php

declare (strict_types=1);
namespace DynamicOOOS\PHPHtmlParser\Discovery;

use DynamicOOOS\PHPHtmlParser\Contracts\Selector\SeekerInterface;
use DynamicOOOS\PHPHtmlParser\Selector\Seeker;
class SeekerDiscovery
{
    /**
     * @var SeekerInterface|null
     */
    private static $seeker = null;
    public static function find() : SeekerInterface
    {
        if (self::$seeker == null) {
            self::$seeker = new Seeker();
        }
        return self::$seeker;
    }
}
