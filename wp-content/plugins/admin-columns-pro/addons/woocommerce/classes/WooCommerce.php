<?php

declare(strict_types=1);

namespace ACA\WC;

use AC;
use AC\Asset\Location\Absolute;
use AC\Entity\Plugin;
use AC\Registerable;
use AC\Services;
use AC\Vendor\DI;
use AC\Vendor\DI\ContainerBuilder;
use ACA\WC\Service\TableScreen;
use ACA\WC\Service\TableTemplates;
use ACP;
use ACP\Service\IntegrationStatus;
use ACP\Service\Storage\TemplateFiles;
use ACP\Service\View;
use Automattic;
use Automattic\WooCommerce\Internal\Admin\Orders\PageController;
use Automattic\WooCommerce\Internal\Features\FeaturesController;
use WC_Subscriptions;

use function AC\Vendor\DI\autowire;

final class WooCommerce implements Registerable
{

    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! class_exists('WooCommerce', false)) {
            return;
        }

        $container = $this->create_container();

        define('ACA_WC_USE_HPOS', $container->get('use.hpos'));

        ACP\QuickAdd\Model\Factory::add_factory(new QuickAdd\Factory());

        if ($container->get('use.hpos')) {
            AC\ListScreenFactory\Aggregate::add(
                new ListScreenFactory\OrderFactory(
                    require $this->location->with_suffix('config/columns/orders.php')->get_path(),
                    wc_get_container()->get(PageController::class)
                )
            );
            AC\ListScreenFactory\Aggregate::add(
                new ListScreenFactory\ProductFactory(
                    require $this->location->with_suffix('config/columns/products.php')->get_path()
                )
            );
        } else {
            AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\ShopOrderFactory());
            AC\ListScreenFactory\Aggregate::add(
                new ListScreenFactory\ProductFactory(
                    require $this->location->with_suffix('config/columns/shoporder/products.php')->get_path()
                )
            );
        }

        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\ProductCategoryFactory());
        AC\ListScreenFactory\Aggregate::add(
            new ListScreenFactory\ShopCouponFactory(
                require $this->location->with_suffix('config/columns/coupons.php')->get_path()
            )
        );

        if ($this->use_product_variations()) {
            $product_variation_column_config = $container->get('use.hpos')
                ? require $this->location->with_suffix('config/columns/product_variation.php')->get_path()
                : require $this->location->with_suffix('config/columns/shoporder/product_variation.php')->get_path();

            AC\ListScreenFactory\Aggregate::add(
                new ListScreenFactory\ProductVariationFactory($product_variation_column_config)
            );
        }

        ACP\QueryFactory::register('wc_order', Search\Query\Order::class);

        ACP\Search\TableScreenFactory::register(ListScreen\Order::class, Search\TableScreen\Order::class);
        ACP\Filtering\TableScreenFactory::register(ListScreen\Order::class, Filtering\Table\Order::class);
        ACP\Filtering\TableScreenFactory::register(ListScreen\OrderSubscription::class, Filtering\Table\Order::class);

        $this->create_services($container)
             ->register();
    }

    private function create_container(): DI\Container
    {
        $definitions = [
            'use.hpos'               => static function (Features $features): bool {
                return $features->use_hpos();
            },
            Absolute::class          => autowire()->constructorParameter(0, $this->location->get_url())
                                                  ->constructorParameter(1, $this->location->get_path()),
            TableScreen::class       => autowire()->constructorParameter(1, $this->use_product_variations()),
            IntegrationStatus::class => autowire()->constructorParameter(0, 'ac-addon-woocommerce'),
            Plugin::class            => static function (): Plugin {
                return ACP\Container::get_plugin();
            },
            Features::class          => autowire()->constructorParameter(
                0,
                wc_get_container()->has(FeaturesController::class)
                    ? wc_get_container()->get(FeaturesController::class)
                    : null
            ),
            TemplateFiles::class     => static function (): TemplateFiles {
                return TemplateFiles::from_directory(__DIR__ . '/../config/storage/template');
            },
        ];

        return (new ContainerBuilder())->addDefinitions($definitions)
                                       ->build();
    }

    private function create_services(DI\Container $container): Services
    {
        $user_column_config = $container->get('use.hpos')
            ? require $this->location->with_suffix('config/columns/users.php')->get_path()
            : require $this->location->with_suffix('config/columns/shoporder/users.php')->get_path();

        $services = new Services([
            new Service\Columns('wp-users', $user_column_config),
        ]);

        $services_fqn = [
            Admin::class,
            Rounding::class,
            Service\Compatibility::class,
            Service\Editing::class,
            Service\QuickAdd::class,
            Service\Table::class,
            Service\ColumnGroups::class,
            Service\ListScreenGroups::class,
            Service\TableRows::class,
            Service\TableScreen::class,
            View::class,
            TemplateFiles::class,
        ];

        if ($container->get('use.hpos')) {
            $services_fqn[] = Service\Listscreens::class;
        }

        if ($this->use_subscriptions()) {
            if ($container->get('use.hpos')) {
                $services_fqn[] = Service\Subscriptions::class;
            } else {
                $services_fqn[] = Service\SubscriptionsPostType::class;
            }
        }

        if ($this->use_product_variations()) {
            $services_fqn[] = PostType\ProductVariation::class;
        }

        foreach ($services_fqn as $service) {
            $services->add($container->get($service));
        }

        return $services;
    }

    private function use_subscriptions(): bool
    {
        return class_exists('WC_Subscriptions', false) && version_compare(WC_Subscriptions::$version, '2.6', '>=');
    }

    private function use_product_variations(): bool
    {
        return apply_filters('acp/wc/show_product_variations', true) && version_compare(WC()->version, '3.3', '>=');
    }

}