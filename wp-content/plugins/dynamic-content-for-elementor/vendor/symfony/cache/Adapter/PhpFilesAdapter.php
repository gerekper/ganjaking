<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DynamicOOOS\Symfony\Component\Cache\Adapter;

use DynamicOOOS\Symfony\Component\Cache\Exception\CacheException;
use DynamicOOOS\Symfony\Component\Cache\PruneableInterface;
use DynamicOOOS\Symfony\Component\Cache\Traits\PhpFilesTrait;
class PhpFilesAdapter extends AbstractAdapter implements PruneableInterface
{
    use PhpFilesTrait;
    /**
     * @param $appendOnly Set to `true` to gain extra performance when the items stored in this pool never expire.
     *                    Doing so is encouraged because it fits perfectly OPcache's memory model.
     *
     * @throws CacheException if OPcache is not enabled
     */
    public function __construct(string $namespace = '', int $defaultLifetime = 0, string $directory = null, bool $appendOnly = \false)
    {
        $this->appendOnly = $appendOnly;
        self::$startTime = self::$startTime ?? $_SERVER['REQUEST_TIME'] ?? \time();
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);
        $this->includeHandler = static function ($type, $msg, $file, $line) {
            throw new \ErrorException($msg, 0, $type, $file, $line);
        };
    }
}
