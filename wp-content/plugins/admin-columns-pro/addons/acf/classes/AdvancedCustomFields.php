<?php

declare(strict_types=1);

namespace ACA\ACF;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Request;
use AC\Services;
use AC\Vendor\Psr\Container\ContainerInterface;
use ACA\ACF\ConditionalFormatting\FieldFormattableFactory;
use ACA\ACF\ListScreenFactory\FieldGroupFactory;
use ACA\ACF\RequestHandler\MapLegacyListScreen;
use ACP\RequestHandlerFactory;
use ACP\RequestParser;
use ACP\Service\IntegrationStatus;

final class AdvancedCustomFields implements Registerable
{

    private $location;

    private $container;

    public function __construct(AC\Asset\Location\Absolute $location, ContainerInterface $container)
    {
        $this->location = $location;
        $this->container = $container;
    }

    public function register(): void
    {
        if ( ! class_exists('acf', false)) {
            return;
        }

        AC\ListScreenFactory\Aggregate::add(new FieldGroupFactory());

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        $column_initiator = new ColumnInstantiator(
            new ConfigFactory(new FieldFactory()),
            new Search\ComparisonFactory(),
            new Sorting\ModelFactory(),
            new Editing\ModelFactory(),
            new FieldFormattableFactory()
        );

        $request_handler_factory = new RequestHandlerFactory(new Request());
        $request_handler_factory->add(
            'aca-acf-map-legacy-list-screen',
            new MapLegacyListScreen($this->container->get(Storage::class))
        );

        return new Services([
            new IntegrationStatus('ac-addon-acf'),
            new ColumnGroup(),
            new Service\ColumnSettings(),
            new Service\EditingFix(),
            new Service\LegacyColumnMapper(),
            new Service\ListScreens(),
            new Service\RemoveDeprecatedColumnFromTypeSelector(),
            new Service\AddColumns(
                new FieldRepository(new FieldGroup\QueryFactory()),
                new FieldsFactory(),
                new ColumnFactory($column_initiator)
            ),
            new Service\Scripts($this->location),
            new Service\InitColumn($column_initiator),
            new RequestParser($request_handler_factory),
        ]);
    }

}