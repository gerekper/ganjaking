<?php

declare (strict_types=1);
namespace AC\Vendor\DI;

use AC\Vendor\Psr\Container\NotFoundExceptionInterface;
/**
 * Exception thrown when a class or a value is not found in the container.
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
