<?php

declare (strict_types=1);
namespace AC\Vendor\DI;

use AC\Vendor\Psr\Container\ContainerExceptionInterface;
/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
