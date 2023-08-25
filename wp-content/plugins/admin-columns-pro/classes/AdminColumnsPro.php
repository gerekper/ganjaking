<?php

declare(strict_types=1);

namespace ACP;

use AC;
use AC\Admin\AdminNetwork;
use AC\Admin\AdminScripts;
use AC\Admin\PageNetworkRequestHandler;
use AC\Admin\PageNetworkRequestHandlers;
use AC\Admin\PageRequestHandler;
use AC\Admin\PageRequestHandlers;
use AC\Asset\Location\Absolute;
use AC\Entity\Plugin;
use AC\ListScreenFactory\Aggregate;
use AC\Plugin\InstallCollection;
use AC\Plugin\Version;
use AC\Request;
use AC\Services;
use AC\Storage\KeyValueFactory;
use AC\Storage\NetworkOptionFactory;
use AC\Storage\OptionFactory;
use AC\Table\ScreenTools;
use AC\Vendor\DI;
use AC\Vendor\DI\ContainerBuilder;
use AC\Vendor\Psr\Container\ContainerInterface;
use ACA\ACF\AdvancedCustomFields;
use ACA\BbPress\BbPress;
use ACA\BeaverBuilder\BeaverBuilder;
use ACA\BP\BuddyPress;
use ACA\EC\EventsCalendar;
use ACA\GravityForms\GravityForms;
use ACA\JetEngine\JetEngine;
use ACA\MetaBox\MetaBox;
use ACA\MLA\MediaLibraryAssistant;
use ACA\Pods\Pods;
use ACA\Polylang\Polylang;
use ACA\Types\Types;
use ACA\WC\WooCommerce;
use ACA\YoastSeo\YoastSeo;
use ACP\Access\PermissionChecker;
use ACP\Access\Rule\LocalServer;
use ACP\Admin\NetworkPageFactory;
use ACP\Admin\PageFactory;
use ACP\Plugin\SetupFactory;
use ACP\Search\SegmentRepository;
use ACP\Service\ListScreens;
use ACP\Storage\Decoder\Version510Factory;
use ACP\Storage\Decoder\Version630Factory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer\PhpSerializer;
use ACP\Table\ListKeysFactory;
use ACP\Table\PrimaryColumn;
use ACP\Table\Scripts;
use ACP\Updates\PeriodicUpdateCheck;

use function AC\Vendor\DI\autowire;

final class AdminColumnsPro
{

    public function __construct()
    {
        $container = $this->create_container();

        Container::set_container($container);

        $page_handler = new PageRequestHandler();
        $page_handler
            ->add('columns', $container->get(PageFactory\Columns::class))
            ->add('settings', $container->get(PageFactory\Settings::class))
            ->add('addons', $container->get(PageFactory\Addons::class))
            ->add('import-export', $container->get(PageFactory\Tools::class))
            ->add('license', $container->get(PageFactory\License::class))
            ->add('help', $container->get(PageFactory\Help::class));

        PageRequestHandlers::add_handler($page_handler);

        $page_network_handler = new PageNetworkRequestHandler();
        $page_network_handler
            ->add('columns', $container->get(NetworkPageFactory\Columns::class))
            ->add('import-export', $container->get(NetworkPageFactory\Tools::class))
            ->add('addons', $container->get(NetworkPageFactory\Addons::class))
            ->add('license', $container->get(NetworkPageFactory\License::class));

        PageNetworkRequestHandlers::add_handler($page_network_handler);

        $this->create_services($container)
             ->register();
    }

    private function create_services(ContainerInterface $container): Services
    {
        $request_ajax_handlers = new RequestAjaxHandlers();
        $request_ajax_handlers
            ->add('acp-ajax-activate', $container->get(RequestHandler\Ajax\LicenseActivate::class))
            ->add('acp-daily-subscription-update', $container->get(RequestHandler\Ajax\SubscriptionUpdate::class))
            ->add('acp-update-plugins-check', $container->get(RequestHandler\Ajax\UpdatePlugins::class))
            ->add('acp-layout-get-users', $container->get(RequestHandler\Ajax\ListScreenUsers::class))
            ->add('acp-update-layout-order', $container->get(RequestHandler\Ajax\ListScreenOrder::class))
            ->add('acp-ajax-send-feedback', $container->get(RequestHandler\Ajax\Feedback::class))
            ->add('acp-permalinks', $container->get(RequestHandler\Ajax\Permalinks::class))
            ->add('acp-user-column-reset', $container->get(RequestHandler\Ajax\ColumnReset::class))
            ->add('acp-user-column-order', $container->get(RequestHandler\Ajax\ColumnOrderUser::class))
            ->add('acp-user-column-width', $container->get(RequestHandler\Ajax\ColumnWidthUser::class))
            ->add('acp-user-column-width-reset', $container->get(RequestHandler\Ajax\ColumnWidthUserReset::class))
            ->add('acp-user-list-order', $container->get(RequestHandler\Ajax\ListScreenOrderUser::class))
            ->add('acp-table-save-preference', $container->get(RequestHandler\Ajax\ListScreenTable::class))
            ->add(
                'acp-user-conditional-formatting',
                $container->get(ConditionalFormat\RequestHandler\SaveRules::class)
            );

        $request_handler_factory = new RequestHandlerFactory(new Request());
        $request_handler_factory
            ->add('acp-license-activate', $container->get(RequestHandler\LicenseActivate::class))
            ->add('acp-license-deactivate', $container->get(RequestHandler\LicenseDeactivate::class))
            ->add('acp-license-update', $container->get(RequestHandler\LicenseUpdate::class))
            ->add('acp-force-plugin-updates', $container->get(RequestHandler\ForcePluginUpdates::class))
            ->add('create-layout', $container->get(RequestHandler\ListScreenCreate::class))
            ->add('delete-layout', $container->get(RequestHandler\ListScreenDelete::class));

        $setup_factory = $container->get(SetupFactory::class);
        $is_network_active = $container->get(Plugin::class)->is_network_active();

        $services_fqn = [
            Updates\UpdatePlugin::class,
            Updates\ViewPluginDetails::class,
            Admin\Settings::class,
            Admin\Notice\NotSavedListScreen::class,
            QuickAdd\Addon::class,
            Sorting\Addon::class,
            Editing\Addon::class,
            Export\Addon::class,
            Search\Addon::class,
            ConditionalFormat\Addon::class,
            Filtering\Addon::class,
            Table\HorizontalScrolling::class,
            Table\StickyTableRow::class,
            Table\HideElements::class,
            ListScreens::class,
            Scripts::class,
            Localize::class,
            NativeTaxonomies::class,
            IconPicker::class,
            TermQueryInformation::class,
            Migrate\Export\Request::class,
            Migrate\Import\Request::class,
            PeriodicUpdateCheck::class,
            PluginActionLinks::class,
            Check\Activation::class,
            Check\Expired::class,
            Check\Renewal::class,
            Check\LockedSettings::class,
            Admin\Scripts::class,
            Service\Addon::class,
            Service\ForcePluginUpdate::class,
            Service\Templates::class,
            Service\Banner::class,
            Service\PluginNotice::class,
            ScreenTools::class,
            PrimaryColumn::class,
            Service\Storage::class,
            Service\Permissions::class,
        ];
        if ($is_network_active) {
            $services_fqn[] = AdminNetwork::class;
        }

        if ( ! $is_network_active && $container->get(Plugin::class)->get_version()->is_beta()) {
            $services_fqn[] = Check\Beta::class;
        }

        $services = new Services();

        foreach ($services_fqn as $service_fqn) {
            $services->add($container->get($service_fqn));
        }

        $services->add(new RequestParser($request_handler_factory))
                 ->add(new RequestAjaxParser($request_ajax_handlers))
                 ->add(new AC\Service\Setup($setup_factory->create(AC\Plugin\SetupFactory::SITE)));

        if ($is_network_active) {
            $services->add(new AC\Service\Setup($setup_factory->create(AC\Plugin\SetupFactory::NETWORK)));
        }

        return $services;
    }

    private function create_container(): DI\Container
    {
        $location_core = AC\Container::get_location();

        $addons = [
            'acf'                     => AdvancedCustomFields::class,
            'beaver-builder'          => BeaverBuilder::class,
            'bbpress'                 => BbPress::class,
            'buddypress'              => BuddyPress::class,
            'events-calendar'         => EventsCalendar::class,
            'gravityforms'            => GravityForms::class,
            'jetengine'               => JetEngine::class,
            'metabox'                 => MetaBox::class,
            'media-library-assistant' => MediaLibraryAssistant::class,
            'pods'                    => Pods::class,
            'polylang'                => Polylang::class,
            'types'                   => Types::class,
            'woocommerce'             => WooCommerce::class,
            'yoast-seo'               => YoastSeo::class,
        ];

        $definitions = [
            AC\ListScreenRepository\Storage::class   => static function () {
                return AC\Container::get_storage();
            },
            AddonFactory::class                      => autowire()
                ->constructorParameter(0, $addons),
            SegmentRepository::class                 => autowire(SegmentRepository\Storage::class),
            PhpSerializer\File::class                => static function (PhpSerializer $serializer) {
                return new PhpSerializer\File($serializer);
            },
            Storage\AbstractDecoderFactory::class    => autowire()
                ->constructorParameter(0, [
                    autowire(Version630Factory::class),
                    autowire(Version510Factory::class),
                ]),
            KeyValueFactory::class                   => static function (Plugin $plugin) {
                return $plugin->is_network_active()
                    ? new NetworkOptionFactory()
                    : new OptionFactory();
            },
            Type\SiteUrl::class                      => static function (Plugin $plugin) {
                return new Type\SiteUrl(
                    $plugin->is_network_active()
                        ? network_site_url()
                        : site_url()
                );
            },
            SetupFactory::class                      => static function (
                AC\ListScreenRepository\Storage $storage,
                Plugin $plugin
            ) {
                return new SetupFactory(
                    'acp_version',
                    $plugin,
                    $storage,
                    new InstallCollection([
                        new Search\Install(new Search\Storage\Table\Segment()),
                    ])
                );
            },
            Plugin::class                            => static function () {
                return Plugin::create(ACP_FILE, new Version(ACP_VERSION));
            },
            Storage\EncoderFactory::class            => static function (Plugin $plugin) {
                return new EncoderFactory($plugin->get_version());
            },
            Absolute::class                          => static function (Plugin $plugin) {
                return new Absolute($plugin->get_url(), $plugin->get_dir());
            },
            AC\ListScreenFactory::class              => autowire(Aggregate::class),
            AC\Table\ListKeysFactoryInterface::class => autowire(ListKeysFactory::class),
            AC\Admin\MenuFactoryInterface::class     => autowire(Admin\MenuFactory::class),
            PermissionChecker::class                 => autowire()->methodParameter('add_rule', 0, new LocalServer()),
            PageFactory\Columns::class               => autowire()->constructorParameter(0, $location_core),
            PageFactory\Settings::class              => autowire()->constructorParameter(0, $location_core),
            PageFactory\Addons::class                => autowire()->constructorParameter(0, $location_core),
            NetworkPageFactory\Columns::class        => autowire()->constructorParameter(0, $location_core),
            NetworkPageFactory\Addons::class         => autowire()->constructorParameter(0, $location_core),
            AdminScripts::class                      => autowire()->constructorParameter(0, $location_core),
            AdminNetwork::class                      => autowire()->constructorParameter(1, $location_core),
            Service\Addon::class                     => autowire()->constructorParameter(0, array_keys($addons)),
            Admin\MenuNetworkFactory::class          => autowire()
                ->constructorParameter(0, network_admin_url('settings.php'))
                ->constructorParameter(1, $location_core),
            Admin\MenuFactory::class                 => autowire()
                ->constructorParameter(0, admin_url('options-general.php'))
                ->constructorParameter(1, $location_core),
        ];

        return (new ContainerBuilder())
            ->addDefinitions($definitions)
            ->build();
    }

    public function get_version(): Version
    {
        return Container::get_plugin()->get_version();
    }

    /**
     * For backwards compatibility with the `Dependencies` class
     * @deprecated 6.0
     */
    public function is_version_gte(string $version): bool
    {
        _deprecated_function(__METHOD__, '6.0');

        return (new Version(ACP_VERSION))->is_gte(new Version($version));
    }

    /**
     * @deprecated 5.7
     */
    public function is_network_active(): void
    {
        _deprecated_function(__METHOD__, '5.7');
    }

}