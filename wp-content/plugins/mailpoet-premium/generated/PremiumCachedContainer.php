<?php

namespace MailPoetGenerated;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use MailPoetVendor\Symfony\Component\DependencyInjection\ContainerInterface;
use MailPoetVendor\Symfony\Component\DependencyInjection\Container;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @final
 */
class PremiumCachedContainer extends Container
{
    private $parameters = [];

    public function __construct()
    {
        $this->services = $this->privates = [];
        $this->syntheticIds = [
            'free_container' => true,
        ];
        $this->methodMap = [
            'MailPoetVendor\\Doctrine\\ORM\\EntityManager' => 'getEntityManagerService',
            'MailPoet\\Config\\AccessControl' => 'getAccessControlService',
            'MailPoet\\Config\\Renderer' => 'getRendererService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository' => 'getNewsletterLinkRepositoryService',
            'MailPoet\\Features\\FeaturesController' => 'getFeaturesControllerService',
            'MailPoet\\Listing\\Handler' => 'getHandlerService',
            'MailPoet\\Listing\\PageLimit' => 'getPageLimitService',
            'MailPoet\\Newsletter\\NewslettersRepository' => 'getNewslettersRepositoryService',
            'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository' => 'getNewsletterStatisticsRepositoryService',
            'MailPoet\\Newsletter\\Url' => 'getUrlService',
            'MailPoet\\Premium\\API\\JSON\\v1\\Bounces' => 'getBouncesService',
            'MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder' => 'getStatsResponseBuilderService',
            'MailPoet\\Premium\\API\\JSON\\v1\\Stats' => 'getStatsService',
            'MailPoet\\Premium\\API\\JSON\\v1\\SubscriberDetailedStats' => 'getSubscriberDetailedStatsService',
            'MailPoet\\Premium\\Config\\Initializer' => 'getInitializerService',
            'MailPoet\\Premium\\Config\\Renderer' => 'getRenderer2Service',
            'MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository' => 'getStatisticsClicksRepositoryService',
            'MailPoet\\Premium\\Newsletter\\Stats\\Bounces' => 'getBounces2Service',
            'MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement' => 'getSubscriberEngagementService',
            'MailPoet\\Premium\\Segments\\DynamicSegments\\SegmentCombinations' => 'getSegmentCombinationsService',
            'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository' => 'getStatisticsWooCommercePurchasesRepositoryService',
            'MailPoet\\Util\\CdnAssetUrl' => 'getCdnAssetUrlService',
            'MailPoet\\WP\\Functions' => 'getFunctionsService',
            'MailPoet\\WooCommerce\\Helper' => 'getHelperService',
        ];

        $this->aliases = [];
    }

    public function compile(): void
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }

    public function isCompiled(): bool
    {
        return true;
    }

    public function getRemovedIds(): array
    {
        return [
            'MailPoetVendor\\Psr\\Container\\ContainerInterface' => true,
            'MailPoetVendor\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => true,
            'MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\SubscriberDetailedStatsResponseBuilder' => true,
            'MailPoet\\Premium\\Config\\Hooks' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository' => true,
            'MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts' => true,
            'MailPoet\\Premium\\Subscriber\\Stats\\SubscriberNewsletterStatsRepository' => true,
        ];
    }

    /**
     * Gets the public 'MailPoetVendor\Doctrine\ORM\EntityManager' shared service.
     *
     * @return \MailPoetVendor\Doctrine\ORM\EntityManager
     */
    protected function getEntityManagerService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoetVendor\\Doctrine\\ORM\\EntityManager');
    }

    /**
     * Gets the public 'MailPoet\Config\AccessControl' shared service.
     *
     * @return \MailPoet\Config\AccessControl
     */
    protected function getAccessControlService()
    {
        return $this->services['MailPoet\\Config\\AccessControl'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Config\\AccessControl');
    }

    /**
     * Gets the public 'MailPoet\Config\Renderer' shared service.
     *
     * @return \MailPoet\Config\Renderer
     */
    protected function getRendererService()
    {
        return $this->services['MailPoet\\Config\\Renderer'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Config\\Renderer');
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository' shared service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository
     */
    protected function getNewsletterLinkRepositoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository');
    }

    /**
     * Gets the public 'MailPoet\Features\FeaturesController' shared service.
     *
     * @return \MailPoet\Features\FeaturesController
     */
    protected function getFeaturesControllerService()
    {
        return $this->services['MailPoet\\Features\\FeaturesController'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Features\\FeaturesController');
    }

    /**
     * Gets the public 'MailPoet\Listing\Handler' shared service.
     *
     * @return \MailPoet\Listing\Handler
     */
    protected function getHandlerService()
    {
        return $this->services['MailPoet\\Listing\\Handler'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Listing\\Handler');
    }

    /**
     * Gets the public 'MailPoet\Listing\PageLimit' shared service.
     *
     * @return \MailPoet\Listing\PageLimit
     */
    protected function getPageLimitService()
    {
        return $this->services['MailPoet\\Listing\\PageLimit'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Listing\\PageLimit');
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewslettersRepository' shared service.
     *
     * @return \MailPoet\Newsletter\NewslettersRepository
     */
    protected function getNewslettersRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\NewslettersRepository'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Newsletter\\NewslettersRepository');
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository' shared service.
     *
     * @return \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository
     */
    protected function getNewsletterStatisticsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository');
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Url' shared service.
     *
     * @return \MailPoet\Newsletter\Url
     */
    protected function getUrlService()
    {
        return $this->services['MailPoet\\Newsletter\\Url'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Newsletter\\Url');
    }

    /**
     * Gets the public 'MailPoet\Premium\API\JSON\v1\Bounces' shared autowired service.
     *
     * @return \MailPoet\Premium\API\JSON\v1\Bounces
     */
    protected function getBouncesService()
    {
        return $this->services['MailPoet\\Premium\\API\\JSON\\v1\\Bounces'] = new \MailPoet\Premium\API\JSON\v1\Bounces(($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Premium\\Newsletter\\Stats\\Bounces'] ?? $this->getBounces2Service()));
    }

    /**
     * Gets the public 'MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder
     */
    protected function getStatsResponseBuilderService()
    {
        return $this->services['MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder'] = new \MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder();
    }

    /**
     * Gets the public 'MailPoet\Premium\API\JSON\v1\Stats' shared autowired service.
     *
     * @return \MailPoet\Premium\API\JSON\v1\Stats
     */
    protected function getStatsService()
    {
        $a = ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService());

        return $this->services['MailPoet\\Premium\\API\\JSON\\v1\\Stats'] = new \MailPoet\Premium\API\JSON\v1\Stats(new \MailPoet\Premium\Newsletter\Stats\PurchasedProducts(($this->services['MailPoet\\WooCommerce\\Helper'] ?? $this->getHelperService()), ($this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] ?? $this->getStatisticsWooCommercePurchasesRepositoryService()), $a, ($this->services['MailPoet\\WP\\Functions'] ?? $this->getFunctionsService())), $a, ($this->services['MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder'] ?? ($this->services['MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder'] = new \MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder())), ($this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository'] ?? $this->getStatisticsClicksRepositoryService()), ($this->services['MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement'] ?? $this->getSubscriberEngagementService()), ($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] ?? $this->getNewsletterStatisticsRepositoryService()), ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()));
    }

    /**
     * Gets the public 'MailPoet\Premium\API\JSON\v1\SubscriberDetailedStats' shared autowired service.
     *
     * @return \MailPoet\Premium\API\JSON\v1\SubscriberDetailedStats
     */
    protected function getSubscriberDetailedStatsService()
    {
        return $this->services['MailPoet\\Premium\\API\\JSON\\v1\\SubscriberDetailedStats'] = new \MailPoet\Premium\API\JSON\v1\SubscriberDetailedStats(new \MailPoet\Premium\Subscriber\Stats\SubscriberNewsletterStatsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService())), new \MailPoet\Premium\API\JSON\v1\ResponseBuilders\SubscriberDetailedStatsResponseBuilder(($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()), ($this->services['MailPoet\\WP\\Functions'] ?? $this->getFunctionsService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? $this->getHelperService())), ($this->services['MailPoet\\Listing\\Handler'] ?? $this->getHandlerService()));
    }

    /**
     * Gets the public 'MailPoet\Premium\Config\Initializer' shared autowired service.
     *
     * @return \MailPoet\Premium\Config\Initializer
     */
    protected function getInitializerService()
    {
        $a = ($this->services['MailPoet\\WP\\Functions'] ?? $this->getFunctionsService());

        return $this->services['MailPoet\\Premium\\Config\\Initializer'] = new \MailPoet\Premium\Config\Initializer($a, new \MailPoet\Premium\Config\Hooks($a), ($this->services['MailPoet\\Premium\\Segments\\DynamicSegments\\SegmentCombinations'] ?? ($this->services['MailPoet\\Premium\\Segments\\DynamicSegments\\SegmentCombinations'] = new \MailPoet\Premium\Segments\DynamicSegments\SegmentCombinations())));
    }

    /**
     * Gets the public 'MailPoet\Premium\Config\Renderer' shared service.
     *
     * @return \MailPoet\Premium\Config\Renderer
     */
    protected function getRenderer2Service()
    {
        return $this->services['MailPoet\\Premium\\Config\\Renderer'] = \MailPoet\Premium\DI\ContainerConfigurator::createRenderer();
    }

    /**
     * Gets the public 'MailPoet\Premium\Newsletter\StatisticsClicksRepository' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\StatisticsClicksRepository
     */
    protected function getStatisticsClicksRepositoryService()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository'] = new \MailPoet\Premium\Newsletter\StatisticsClicksRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Premium\Newsletter\Stats\Bounces' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\Stats\Bounces
     */
    protected function getBounces2Service()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\Stats\\Bounces'] = new \MailPoet\Premium\Newsletter\Stats\Bounces(($this->services['MailPoet\\Listing\\Handler'] ?? $this->getHandlerService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Premium\Newsletter\Stats\SubscriberEngagement' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\Stats\SubscriberEngagement
     */
    protected function getSubscriberEngagementService()
    {
        $a = ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService());

        return $this->services['MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement'] = new \MailPoet\Premium\Newsletter\Stats\SubscriberEngagement(($this->services['MailPoet\\Listing\\Handler'] ?? $this->getHandlerService()), $a, ($this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository'] ?? $this->getStatisticsClicksRepositoryService()), new \MailPoet\Premium\Newsletter\StatisticsOpensRepository($a), new \MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository($a), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] ?? $this->getNewsletterLinkRepositoryService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Premium\Segments\DynamicSegments\SegmentCombinations' shared autowired service.
     *
     * @return \MailPoet\Premium\Segments\DynamicSegments\SegmentCombinations
     */
    protected function getSegmentCombinationsService()
    {
        return $this->services['MailPoet\\Premium\\Segments\\DynamicSegments\\SegmentCombinations'] = new \MailPoet\Premium\Segments\DynamicSegments\SegmentCombinations();
    }

    /**
     * Gets the public 'MailPoet\Statistics\StatisticsWooCommercePurchasesRepository' shared service.
     *
     * @return \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository
     */
    protected function getStatisticsWooCommercePurchasesRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository');
    }

    /**
     * Gets the public 'MailPoet\Util\CdnAssetUrl' shared service.
     *
     * @return \MailPoet\Util\CdnAssetUrl
     */
    protected function getCdnAssetUrlService()
    {
        return $this->services['MailPoet\\Util\\CdnAssetUrl'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\Util\\CdnAssetUrl');
    }

    /**
     * Gets the public 'MailPoet\WP\Functions' shared service.
     *
     * @return \MailPoet\WP\Functions
     */
    protected function getFunctionsService()
    {
        return $this->services['MailPoet\\WP\\Functions'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\WP\\Functions');
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\Helper' shared service.
     *
     * @return \MailPoet\WooCommerce\Helper
     */
    protected function getHelperService()
    {
        return $this->services['MailPoet\\WooCommerce\\Helper'] = ($this->services['free_container'] ?? $this->get('free_container', 1))->get('MailPoet\\WooCommerce\\Helper');
    }
}
