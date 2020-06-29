<?php

namespace MailPoet\Premium\DI;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\IContainerConfigurator;
use MailPoetVendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use MailPoetVendor\Symfony\Component\DependencyInjection\Reference;

class ContainerConfigurator implements IContainerConfigurator {
  public function getDumpNamespace() {
    return 'MailPoetGenerated';
  }

  public function getDumpClassname() {
    return 'PremiumCachedContainer';
  }

  public function configure(ContainerBuilder $container) {
    // Every service must be registered
    // Strict mode disables magical loading services by looking for an instance within other services
    // see https://symfonycasts.com/screencast/symfony4-upgrade/service-deprecations#strict-autowiring-mode
    $container->setParameter('container.autowiring.strict_mode', true);

    // Factory for free deps
    $container->register(IContainerConfigurator::FREE_CONTAINER_SERVICE_SLUG)
      ->setSynthetic(true)
      ->setPublic(true);

    // Free plugin dependencies
    $this->registerFreeService($container, \MailPoet\Config\AccessControl::class);
    $this->registerFreeService($container, \MailPoet\Config\Renderer::class);
    $this->registerFreeService($container, \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository::class);
    $this->registerFreeService($container, \MailPoet\Features\FeaturesController::class);
    $this->registerFreeService($container, \MailPoet\Listing\Handler::class);
    $this->registerFreeService($container, \MailPoet\Listing\PageLimit::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\NewslettersRepository::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository::class);
    $this->registerFreeService($container, \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository::class);
    $this->registerFreeService($container, \MailPoet\WooCommerce\Helper::class);
    $this->registerFreeService($container, \MailPoet\WP\Functions::class);
    $this->registerFreeService($container, \MailPoetVendor\Doctrine\ORM\EntityManager::class);

    // API
    $container->autowire(\MailPoet\Premium\API\JSON\v1\Stats::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder::class)->setPublic(true);

    // Config
    $container->autowire(\MailPoet\Premium\Config\Hooks::class);
    $container->autowire(\MailPoet\Premium\Config\Initializer::class)->setPublic(true);
    $container->register(\MailPoet\Premium\Config\Renderer::class)
      ->setPublic(true)
      ->setFactory([__CLASS__, 'createRenderer']);
    // Stats
    $container->autowire(\MailPoet\Premium\Newsletter\Stats\PurchasedProducts::class);
    $container->autowire(\MailPoet\Premium\Newsletter\Stats\SubscriberEngagement::class);
    $container->autowire(\MailPoet\Premium\Newsletter\StatisticsClicksRepository::class);
    $container->autowire(\MailPoet\Premium\Newsletter\StatisticsOpensRepository::class);
    $container->autowire(\MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository::class);
    return $container;
  }

  private function registerFreeService(ContainerBuilder $container, $id) {
    $container->register($id)
      ->setPublic(true)
      ->addArgument($id)
      ->setFactory([
        new Reference(IContainerConfigurator::FREE_CONTAINER_SERVICE_SLUG),
        'get',
      ]);
  }

  public static function createRenderer() {
    $caching = !WP_DEBUG;
    $debugging = WP_DEBUG;
    return new \MailPoet\Premium\Config\Renderer($caching, $debugging);
  }
}
