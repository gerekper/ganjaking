<?php

declare (strict_types=1);
namespace AC\Vendor\DI\Definition;

use AC\Vendor\Psr\Container\ContainerInterface;
/**
 * Describes a definition that can resolve itself.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface SelfResolvingDefinition
{
    /**
     * Resolve the definition and return the resulting value.
     *
     * @return mixed
     */
    public function resolve(ContainerInterface $container);
    /**
     * Check if a definition can be resolved.
     */
    public function isResolvable(ContainerInterface $container) : bool;
}
