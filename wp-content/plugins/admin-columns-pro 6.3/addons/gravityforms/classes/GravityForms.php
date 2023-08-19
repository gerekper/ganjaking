<?php

declare(strict_types=1);

namespace ACA\GravityForms;

use AC;
use AC\DefaultColumnsRepository;
use AC\Registerable;
use AC\Services;
use AC\Vendor\Psr\Container\ContainerInterface;
use ACA\GravityForms\Search\Query;
use ACA\GravityForms\Service\ColumnGroup;
use ACA\GravityForms\Service\Scripts;
use ACP\Search\QueryFactory;
use ACP\Search\TableScreenFactory;
use ACP\Service\IntegrationStatus;
use GFCommon;

final class GravityForms implements Registerable
{

    public const GROUP = 'gravity_forms';

    private $location;

    private $container;

    public function __construct(AC\Asset\Location\Absolute $location, ContainerInterface $container)
    {
        $this->location = $location;
        $this->container = $container;
    }

    public function register(): void
    {
        if ( ! class_exists('GFCommon', false)) {
            return;
        }

        $minimum_gf_version = '2.5';

        if (class_exists('GFCommon', false) && version_compare((string)GFCommon::$version, $minimum_gf_version, '<')) {
            return;
        }

        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\EntryFactory());

        $this->create_services()->register();

        // Enable Search
        QueryFactory::register(MetaTypes::GRAVITY_FORMS_ENTRY, Query::class);
        TableScreenFactory::register(ListScreen\Entry::class, Search\TableScreen\Entry::class);
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\ListScreens(),
            new TableScreen\Entry(
                new AC\ListScreenFactory\Aggregate(),
                $this->container->get(AC\ListScreenRepository\Storage::class),
                new DefaultColumnsRepository()
            ),
            new Admin(),
            new IntegrationStatus('ac-addon-gravityforms'),
            new Scripts($this->location),
            new ColumnGroup(),
        ]);
    }

}