<?php

declare(strict_types=1);

namespace ACP;

use AC\Asset\Location\Absolute;
use AC\Registerable;
use AC\Vendor\Psr\Container\ContainerInterface;
use DomainException;

final class AddonFactory
{

    private $addons;

    private $location;

    private $container;

    public function __construct(array $addons, Absolute $location, ContainerInterface $container)
    {
        $this->addons = $addons;
        $this->location = $location;
        $this->container = $container;
    }

    public function create(string $key): Registerable
    {
        $addon = $this->addons[$key] ?? null;

        if (null === $addon) {
            throw new DomainException(sprintf('Addon %s does not exist', $key));
        }

        return new $addon(
            $this->location->with_suffix('addons/' . $key),
            $this->container
        );
    }

}