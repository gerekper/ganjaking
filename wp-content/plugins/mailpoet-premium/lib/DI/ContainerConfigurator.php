<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

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
    // Factory for free deps
    $container->register(IContainerConfigurator::FREE_CONTAINER_SERVICE_SLUG)
      ->setSynthetic(true)
      ->setPublic(true);

    // Free plugin dependencies
    $this->registerFreeService($container, \MailPoet\Subscribers\SubscriberTagRepository::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Storage\AutomationRunStorage::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\Sending\ScheduledTasksRepository::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository::class);
    $this->registerFreeService($container, \MailPoet\Tags\TagRepository::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Builder\UpdateStepsController::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Builder\UpdateAutomationController::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Control\FilterHandler::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Mappers\AutomationMapper::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Storage\AutomationStorage::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Storage\AutomationStatisticsStorage::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Hooks::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Templates\AutomationBuilder::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Validation\AutomationValidator::class);
    $this->registerFreeService($container, \MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject::class);
    $this->registerFreeService($container, \MailPoet\Config\AccessControl::class);
    $this->registerFreeService($container, \MailPoet\Config\Renderer::class);
    $this->registerFreeService($container, \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository::class);
    $this->registerFreeService($container, \MailPoet\CustomFields\CustomFieldsRepository::class);
    $this->registerFreeService($container, \MailPoet\Features\FeaturesController::class);
    $this->registerFreeService($container, \MailPoet\Listing\Handler::class);
    $this->registerFreeService($container, \MailPoet\Listing\PageLimit::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\NewslettersRepository::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\Url::class);
    $this->registerFreeService($container, \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository::class);
    $this->registerFreeService($container, \MailPoet\Segments\SegmentsRepository::class);
    $this->registerFreeService($container, \MailPoet\Settings\TrackingConfig::class);
    $this->registerFreeService($container, \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository::class);
    $this->registerFreeService($container, \MailPoet\Statistics\Track\Unsubscribes::class);
    $this->registerFreeService($container, \MailPoet\Subscribers\SubscriberCustomFieldRepository::class);
    $this->registerFreeService($container, \MailPoet\Subscribers\SubscriberSegmentRepository::class);
    $this->registerFreeService($container, \MailPoet\Subscribers\SubscribersRepository::class);
    $this->registerFreeService($container, \MailPoet\WooCommerce\Helper::class);
    $this->registerFreeService($container, \MailPoet\WooCommerce\WooCommerceSubscriptions\Helper::class);
    $this->registerFreeService($container, \MailPoet\WP\Functions::class);
    $this->registerFreeService($container, \MailPoetVendor\Doctrine\ORM\EntityManager::class);
    $this->registerFreeService($container, \MailPoet\Util\CdnAssetUrl::class);
    $this->registerFreeService($container, \MailPoet\Util\License\Features\Subscribers::class);
    $this->registerFreeService($container, \MailPoet\Mailer\MailerFactory::class);
    $this->registerFreeService($container, \MailPoet\Settings\SettingsController::class);
    $this->registerFreeService($container, \MailPoet\Automation\Integrations\WooCommerce\WooCommerce::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\WordPress::class);
    $this->registerFreeService($container, \MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\AutomationTimeSpanController::class);
    $this->registerFreeService($container, \MailPoet\Automation\Engine\Registry::class);

    // API
    $container->autowire(\MailPoet\Premium\API\JSON\v1\Bounces::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\API\JSON\v1\Stats::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\API\JSON\v1\SubscriberDetailedStats::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\API\JSON\v1\ResponseBuilders\SubscriberDetailedStatsResponseBuilder::class);

    // Automation
    $container->autowire(\MailPoet\Premium\Automation\Engine\Builder\CreateAutomationController::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Engine\Builder\UpdateAutomationController::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Engine\Engine::class)->setPublic(true);

    // Automation - API endpoints
    $container->autowire(\MailPoet\Premium\Automation\Engine\Endpoints\Automations\AutomationsPostEndpoint::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Engine\Endpoints\Automations\AutomationsPutEndpoint::class)->setPublic(true);

    // Automation - MailPoet Premium integration
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\MailPoetPremiumIntegration::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\ContextFactory::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\UnsubscribeAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\AddTagAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\RemoveTagAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\AddToListAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\RemoveFromListAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\UpdateSubscriberAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\NotificationEmailAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers\TagAddedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers\TagRemovedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Subjects\TagSubject::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Templates\PremiumTemplatesFactory::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers\CustomTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers\ClicksEmailLinkTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\CustomAction::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Subjects\CustomDataSubject::class)->setPublic(true);

    // Automation - Analytics
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Analytics::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Endpoints\OrderEndpoint::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Storage\OrderStatistics::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Controller\OrderController::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Endpoints\SubscriberEndpoint::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Storage\SubscriberStatistics::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Controller\SubscriberController::class)->setPublic(true);

    // Automation - WordPress integration
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WordPress\WordPressIntegration::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WordPress\Triggers\MadeACommentTrigger::class)->setPublic(true);

    // Automation - WooCommerce integration
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerce\WooCommerceIntegration::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerce\Subjects\ReviewSubject::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerce\Triggers\MadeAReviewTrigger::class)->setPublic(true);

    // Automation - WooCommerce Subscriptions integration
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\WooCommerceSubscriptionsIntegration::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\ContextFactory::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionCreatedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionStatusChangedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionTrialStartedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionRenewedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionExpiredTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionPaymentFailedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionTrialEndedTrigger::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionSubject::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionStatusChangeSubject::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Fields\SubscriptionFields::class)->setPublic(true);

    // Config
    $container->autowire(\MailPoet\Premium\Config\Hooks::class);
    $container->autowire(\MailPoet\Premium\Config\Initializer::class)->setPublic(true);
    $container->register(\MailPoet\Premium\Config\Renderer::class)
      ->setPublic(true)
      ->setFactory([__CLASS__, 'createRenderer']);
    // Segments
    $container->autowire(\MailPoet\Premium\Segments\DynamicSegments\SegmentCombinations::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Segments\DynamicSegments\Filters\SubscriberTag::class)->setPublic(true);
    // Stats
    $container->autowire(\MailPoet\Premium\Newsletter\Stats\Bounces::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Newsletter\Stats\PurchasedProducts::class);
    $container->autowire(\MailPoet\Premium\Newsletter\Stats\SubscriberEngagement::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Newsletter\StatisticsClicksRepository::class)->setPublic(true);
    $container->autowire(\MailPoet\Premium\Newsletter\StatisticsOpensRepository::class);
    $container->autowire(\MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository::class);
    // Subscriber
    $container->autowire(\MailPoet\Premium\Subscriber\Stats\SubscriberNewsletterStatsRepository::class);
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
    $debugging = defined('WP_DEBUG') && WP_DEBUG;
    $caching = !$debugging;
    $autoReload = defined('MAILPOET_DEVELOPMENT') && MAILPOET_DEVELOPMENT;
    return new \MailPoet\Premium\Config\Renderer($caching, $debugging, $autoReload);
  }
}
