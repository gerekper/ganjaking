<?php

namespace ACP\Filtering;

use AC\Registerable;
use AC\Services;
use AC\Vendor\Psr\Container\ContainerInterface;

class Addon implements Registerable
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function register(): void
    {
        $services = new Services();

        $services_fqn = [
            Service\Table\FilterRequestHandler::class,
            Service\Table\FilterContainers::class,
            Service\Table\Scripts::class,
            Service\Admin\Scripts::class,
            Service\Admin\ColumnSettings::class,
        ];

        foreach ($services_fqn as $service) {
            $services->add($this->container->get($service));
        }

        $services->register();
    }

}