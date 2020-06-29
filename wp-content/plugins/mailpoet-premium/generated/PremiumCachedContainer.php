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

/**
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @final since Symfony 3.3
 */
class PremiumCachedContainer extends Container
{
    private $parameters = [];
    private $targetDirs = [];

    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();

        $this->services = [];
        $this->normalizedIds = [
            'mailpoet\\config\\accesscontrol' => 'MailPoet\\Config\\AccessControl',
            'mailpoet\\config\\renderer' => 'MailPoet\\Config\\Renderer',
            'mailpoet\\cron\\workers\\statsnotifications\\newsletterlinkrepository' => 'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository',
            'mailpoet\\features\\featurescontroller' => 'MailPoet\\Features\\FeaturesController',
            'mailpoet\\listing\\handler' => 'MailPoet\\Listing\\Handler',
            'mailpoet\\listing\\pagelimit' => 'MailPoet\\Listing\\PageLimit',
            'mailpoet\\newsletter\\newslettersrepository' => 'MailPoet\\Newsletter\\NewslettersRepository',
            'mailpoet\\newsletter\\statistics\\newsletterstatisticsrepository' => 'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository',
            'mailpoet\\premium\\api\\json\\v1\\responsebuilders\\statsresponsebuilder' => 'MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder',
            'mailpoet\\premium\\api\\json\\v1\\stats' => 'MailPoet\\Premium\\API\\JSON\\v1\\Stats',
            'mailpoet\\premium\\config\\hooks' => 'MailPoet\\Premium\\Config\\Hooks',
            'mailpoet\\premium\\config\\initializer' => 'MailPoet\\Premium\\Config\\Initializer',
            'mailpoet\\premium\\config\\renderer' => 'MailPoet\\Premium\\Config\\Renderer',
            'mailpoet\\premium\\newsletter\\statisticsclicksrepository' => 'MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository',
            'mailpoet\\premium\\newsletter\\statisticsopensrepository' => 'MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository',
            'mailpoet\\premium\\newsletter\\statisticsunsubscribesrepository' => 'MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository',
            'mailpoet\\premium\\newsletter\\stats\\purchasedproducts' => 'MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts',
            'mailpoet\\premium\\newsletter\\stats\\subscriberengagement' => 'MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement',
            'mailpoet\\statistics\\statisticswoocommercepurchasesrepository' => 'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository',
            'mailpoet\\woocommerce\\helper' => 'MailPoet\\WooCommerce\\Helper',
            'mailpoet\\wp\\functions' => 'MailPoet\\WP\\Functions',
            'mailpoetvendor\\doctrine\\orm\\entitymanager' => 'MailPoetVendor\\Doctrine\\ORM\\EntityManager',
        ];
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
            'MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder' => 'getStatsResponseBuilderService',
            'MailPoet\\Premium\\API\\JSON\\v1\\Stats' => 'getStatsService',
            'MailPoet\\Premium\\Config\\Hooks' => 'getHooksService',
            'MailPoet\\Premium\\Config\\Initializer' => 'getInitializerService',
            'MailPoet\\Premium\\Config\\Renderer' => 'getRenderer2Service',
            'MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository' => 'getStatisticsClicksRepositoryService',
            'MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository' => 'getStatisticsOpensRepositoryService',
            'MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository' => 'getStatisticsUnsubscribesRepositoryService',
            'MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts' => 'getPurchasedProductsService',
            'MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement' => 'getSubscriberEngagementService',
            'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository' => 'getStatisticsWooCommercePurchasesRepositoryService',
            'MailPoet\\WP\\Functions' => 'getFunctionsService',
            'MailPoet\\WooCommerce\\Helper' => 'getHelperService',
        ];
        $this->privates = [
            'MailPoet\\Premium\\Config\\Hooks' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository' => true,
            'MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts' => true,
            'MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement' => true,
        ];

        $this->aliases = [];
    }

    public function getRemovedIds()
    {
        return [
            'MailPoetVendor\\Psr\\Container\\ContainerInterface' => true,
            'MailPoetVendor\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => true,
            'MailPoet\\Premium\\Config\\Hooks' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository' => true,
            'MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository' => true,
            'MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts' => true,
            'MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement' => true,
        ];
    }

    public function compile()
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }

    public function isCompiled()
    {
        return true;
    }

    public function isFrozen()
    {
        @trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the isCompiled() method instead.', __METHOD__), E_USER_DEPRECATED);

        return true;
    }

    /**
     * Gets the public 'MailPoetVendor\Doctrine\ORM\EntityManager' shared service.
     *
     * @return \MailPoetVendor\Doctrine\ORM\EntityManager
     */
    protected function getEntityManagerService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoetVendor\\Doctrine\\ORM\\EntityManager');
    }

    /**
     * Gets the public 'MailPoet\Config\AccessControl' shared service.
     *
     * @return \MailPoet\Config\AccessControl
     */
    protected function getAccessControlService()
    {
        return $this->services['MailPoet\\Config\\AccessControl'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Config\\AccessControl');
    }

    /**
     * Gets the public 'MailPoet\Config\Renderer' shared service.
     *
     * @return \MailPoet\Config\Renderer
     */
    protected function getRendererService()
    {
        return $this->services['MailPoet\\Config\\Renderer'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Config\\Renderer');
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository' shared service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository
     */
    protected function getNewsletterLinkRepositoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository');
    }

    /**
     * Gets the public 'MailPoet\Features\FeaturesController' shared service.
     *
     * @return \MailPoet\Features\FeaturesController
     */
    protected function getFeaturesControllerService()
    {
        return $this->services['MailPoet\\Features\\FeaturesController'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Features\\FeaturesController');
    }

    /**
     * Gets the public 'MailPoet\Listing\Handler' shared service.
     *
     * @return \MailPoet\Listing\Handler
     */
    protected function getHandlerService()
    {
        return $this->services['MailPoet\\Listing\\Handler'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Listing\\Handler');
    }

    /**
     * Gets the public 'MailPoet\Listing\PageLimit' shared service.
     *
     * @return \MailPoet\Listing\PageLimit
     */
    protected function getPageLimitService()
    {
        return $this->services['MailPoet\\Listing\\PageLimit'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Listing\\PageLimit');
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewslettersRepository' shared service.
     *
     * @return \MailPoet\Newsletter\NewslettersRepository
     */
    protected function getNewslettersRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\NewslettersRepository'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Newsletter\\NewslettersRepository');
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository' shared service.
     *
     * @return \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository
     */
    protected function getNewsletterStatisticsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository');
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
        return $this->services['MailPoet\\Premium\\API\\JSON\\v1\\Stats'] = new \MailPoet\Premium\API\JSON\v1\Stats(${($_ = isset($this->services['MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts']) ? $this->services['MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts'] : $this->getPurchasedProductsService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder']) ? $this->services['MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder'] : ($this->services['MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder'] = new \MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository']) ? $this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository'] : $this->getStatisticsClicksRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement']) ? $this->services['MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement'] : $this->getSubscriberEngagementService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository']) ? $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] : $this->getNewsletterStatisticsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Premium\Config\Initializer' shared autowired service.
     *
     * @return \MailPoet\Premium\Config\Initializer
     */
    protected function getInitializerService()
    {
        return $this->services['MailPoet\\Premium\\Config\\Initializer'] = new \MailPoet\Premium\Config\Initializer(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : $this->getFunctionsService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\Config\\Hooks']) ? $this->services['MailPoet\\Premium\\Config\\Hooks'] : $this->getHooksService()) && false ?: '_'});
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
     * Gets the public 'MailPoet\Statistics\StatisticsWooCommercePurchasesRepository' shared service.
     *
     * @return \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository
     */
    protected function getStatisticsWooCommercePurchasesRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository');
    }

    /**
     * Gets the public 'MailPoet\WP\Functions' shared service.
     *
     * @return \MailPoet\WP\Functions
     */
    protected function getFunctionsService()
    {
        return $this->services['MailPoet\\WP\\Functions'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\WP\\Functions');
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\Helper' shared service.
     *
     * @return \MailPoet\WooCommerce\Helper
     */
    protected function getHelperService()
    {
        return $this->services['MailPoet\\WooCommerce\\Helper'] = ${($_ = isset($this->services['free_container']) ? $this->services['free_container'] : $this->get('free_container', 1)) && false ?: '_'}->get('MailPoet\\WooCommerce\\Helper');
    }

    /**
     * Gets the private 'MailPoet\Premium\Config\Hooks' shared autowired service.
     *
     * @return \MailPoet\Premium\Config\Hooks
     */
    protected function getHooksService()
    {
        return $this->services['MailPoet\\Premium\\Config\\Hooks'] = new \MailPoet\Premium\Config\Hooks(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : $this->getFunctionsService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Premium\Newsletter\StatisticsClicksRepository' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\StatisticsClicksRepository
     */
    protected function getStatisticsClicksRepositoryService()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository'] = new \MailPoet\Premium\Newsletter\StatisticsClicksRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Premium\Newsletter\StatisticsOpensRepository' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\StatisticsOpensRepository
     */
    protected function getStatisticsOpensRepositoryService()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository'] = new \MailPoet\Premium\Newsletter\StatisticsOpensRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository
     */
    protected function getStatisticsUnsubscribesRepositoryService()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository'] = new \MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Premium\Newsletter\Stats\PurchasedProducts' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\Stats\PurchasedProducts
     */
    protected function getPurchasedProductsService()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts'] = new \MailPoet\Premium\Newsletter\Stats\PurchasedProducts(${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : $this->getHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository']) ? $this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] : $this->getStatisticsWooCommercePurchasesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : $this->getFunctionsService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Premium\Newsletter\Stats\SubscriberEngagement' shared autowired service.
     *
     * @return \MailPoet\Premium\Newsletter\Stats\SubscriberEngagement
     */
    protected function getSubscriberEngagementService()
    {
        return $this->services['MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement'] = new \MailPoet\Premium\Newsletter\Stats\SubscriberEngagement(${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : $this->getHandlerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository']) ? $this->services['MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository'] : $this->getStatisticsClicksRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository']) ? $this->services['MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository'] : $this->getStatisticsOpensRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository']) ? $this->services['MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository'] : $this->getStatisticsUnsubscribesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository']) ? $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] : $this->getNewsletterLinkRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'});
    }

    public function getParameter($name)
    {
        $name = (string) $name;
        if (!(isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || array_key_exists($name, $this->parameters))) {
            $name = $this->normalizeParameterName($name);

            if (!(isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || array_key_exists($name, $this->parameters))) {
                throw new InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
            }
        }
        if (isset($this->loadedDynamicParameters[$name])) {
            return $this->loadedDynamicParameters[$name] ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
        }

        return $this->parameters[$name];
    }

    public function hasParameter($name)
    {
        $name = (string) $name;
        $name = $this->normalizeParameterName($name);

        return isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || array_key_exists($name, $this->parameters);
    }

    public function setParameter($name, $value)
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $parameters = $this->parameters;
            foreach ($this->loadedDynamicParameters as $name => $loaded) {
                $parameters[$name] = $loaded ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
            }
            $this->parameterBag = new FrozenParameterBag($parameters);
        }

        return $this->parameterBag;
    }

    private $loadedDynamicParameters = [];
    private $dynamicParameters = [];

    /**
     * Computes a dynamic parameter.
     *
     * @param string $name The name of the dynamic parameter to load
     *
     * @return mixed The value of the dynamic parameter
     *
     * @throws InvalidArgumentException When the dynamic parameter does not exist
     */
    private function getDynamicParameter($name)
    {
        throw new InvalidArgumentException(sprintf('The dynamic parameter "%s" must be defined.', $name));
    }

    private $normalizedParameterNames = [];

    private function normalizeParameterName($name)
    {
        if (isset($this->normalizedParameterNames[$normalizedName = strtolower($name)]) || isset($this->parameters[$normalizedName]) || array_key_exists($normalizedName, $this->parameters)) {
            $normalizedName = isset($this->normalizedParameterNames[$normalizedName]) ? $this->normalizedParameterNames[$normalizedName] : $normalizedName;
            if ((string) $name !== $normalizedName) {
                @trigger_error(sprintf('Parameter names will be made case sensitive in Symfony 4.0. Using "%s" instead of "%s" is deprecated since Symfony 3.4.', $name, $normalizedName), E_USER_DEPRECATED);
            }
        } else {
            $normalizedName = $this->normalizedParameterNames[$normalizedName] = (string) $name;
        }

        return $normalizedName;
    }

    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return [
            'container.autowiring.strict_mode' => true,
        ];
    }
}
