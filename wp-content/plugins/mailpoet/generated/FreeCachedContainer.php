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
class FreeCachedContainer extends Container
{
    private $parameters = [];

    public function __construct()
    {
        $this->services = $this->privates = [];
        $this->syntheticIds = [
            'premium_container' => true,
        ];
        $this->methodMap = [
            'MailPoetVendor\\CSS' => 'getCSSService',
            'MailPoetVendor\\Doctrine\\DBAL\\Connection' => 'getConnectionService',
            'MailPoetVendor\\Doctrine\\ORM\\EntityManager' => 'getEntityManagerService',
            'MailPoet\\API\\JSON\\API' => 'getAPIService',
            'MailPoet\\API\\JSON\\ErrorHandler' => 'getErrorHandlerService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\DynamicSegmentsResponseBuilder' => 'getDynamicSegmentsResponseBuilderService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder' => 'getFormsResponseBuilderService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder' => 'getNewslettersResponseBuilderService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder' => 'getSegmentsResponseBuilderService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder' => 'getSubscribersResponseBuilderService',
            'MailPoet\\API\\JSON\\v1\\Analytics' => 'getAnalyticsService',
            'MailPoet\\API\\JSON\\v1\\AutomatedLatestContent' => 'getAutomatedLatestContentService',
            'MailPoet\\API\\JSON\\v1\\AutomaticEmails' => 'getAutomaticEmailsService',
            'MailPoet\\API\\JSON\\v1\\CustomFields' => 'getCustomFieldsService',
            'MailPoet\\API\\JSON\\v1\\DynamicSegments' => 'getDynamicSegmentsService',
            'MailPoet\\API\\JSON\\v1\\FeatureFlags' => 'getFeatureFlagsService',
            'MailPoet\\API\\JSON\\v1\\Forms' => 'getFormsService',
            'MailPoet\\API\\JSON\\v1\\ImportExport' => 'getImportExportService',
            'MailPoet\\API\\JSON\\v1\\MP2Migrator' => 'getMP2MigratorService',
            'MailPoet\\API\\JSON\\v1\\Mailer' => 'getMailerService',
            'MailPoet\\API\\JSON\\v1\\NewsletterLinks' => 'getNewsletterLinksService',
            'MailPoet\\API\\JSON\\v1\\NewsletterTemplates' => 'getNewsletterTemplatesService',
            'MailPoet\\API\\JSON\\v1\\Newsletters' => 'getNewslettersService',
            'MailPoet\\API\\JSON\\v1\\Premium' => 'getPremiumService',
            'MailPoet\\API\\JSON\\v1\\Segments' => 'getSegmentsService',
            'MailPoet\\API\\JSON\\v1\\SendingQueue' => 'getSendingQueueService',
            'MailPoet\\API\\JSON\\v1\\SendingTaskSubscribers' => 'getSendingTaskSubscribersService',
            'MailPoet\\API\\JSON\\v1\\Services' => 'getServicesService',
            'MailPoet\\API\\JSON\\v1\\Settings' => 'getSettingsService',
            'MailPoet\\API\\JSON\\v1\\Setup' => 'getSetupService',
            'MailPoet\\API\\JSON\\v1\\SubscriberStats' => 'getSubscriberStatsService',
            'MailPoet\\API\\JSON\\v1\\Subscribers' => 'getSubscribersService',
            'MailPoet\\API\\JSON\\v1\\UserFlags' => 'getUserFlagsService',
            'MailPoet\\API\\JSON\\v1\\WoocommerceSettings' => 'getWoocommerceSettingsService',
            'MailPoet\\API\\MP\\v1\\API' => 'getAPI2Service',
            'MailPoet\\API\\MP\\v1\\CustomFields' => 'getCustomFields2Service',
            'MailPoet\\API\\MP\\v1\\Subscribers' => 'getSubscribers2Service',
            'MailPoet\\AdminPages\\PageRenderer' => 'getPageRendererService',
            'MailPoet\\AdminPages\\Pages\\ExperimentalFeatures' => 'getExperimentalFeaturesService',
            'MailPoet\\AdminPages\\Pages\\FormEditor' => 'getFormEditorService',
            'MailPoet\\AdminPages\\Pages\\Forms' => 'getForms2Service',
            'MailPoet\\AdminPages\\Pages\\Help' => 'getHelpService',
            'MailPoet\\AdminPages\\Pages\\Logs' => 'getLogsService',
            'MailPoet\\AdminPages\\Pages\\MP2Migration' => 'getMP2MigrationService',
            'MailPoet\\AdminPages\\Pages\\NewsletterEditor' => 'getNewsletterEditorService',
            'MailPoet\\AdminPages\\Pages\\Newsletters' => 'getNewsletters2Service',
            'MailPoet\\AdminPages\\Pages\\Premium' => 'getPremium2Service',
            'MailPoet\\AdminPages\\Pages\\Segments' => 'getSegments2Service',
            'MailPoet\\AdminPages\\Pages\\Settings' => 'getSettings2Service',
            'MailPoet\\AdminPages\\Pages\\Subscribers' => 'getSubscribers3Service',
            'MailPoet\\AdminPages\\Pages\\SubscribersExport' => 'getSubscribersExportService',
            'MailPoet\\AdminPages\\Pages\\SubscribersImport' => 'getSubscribersImportService',
            'MailPoet\\AdminPages\\Pages\\WelcomeWizard' => 'getWelcomeWizardService',
            'MailPoet\\AdminPages\\Pages\\WooCommerceSetup' => 'getWooCommerceSetupService',
            'MailPoet\\Analytics\\Analytics' => 'getAnalytics2Service',
            'MailPoet\\Analytics\\Reporter' => 'getReporterService',
            'MailPoet\\AutomaticEmails\\AutomaticEmailFactory' => 'getAutomaticEmailFactoryService',
            'MailPoet\\AutomaticEmails\\AutomaticEmails' => 'getAutomaticEmails2Service',
            'MailPoet\\AutomaticEmails\\WooCommerce\\Events\\AbandonedCart' => 'getAbandonedCartService',
            'MailPoet\\AutomaticEmails\\WooCommerce\\Events\\AbandonedCartPageVisitTracker' => 'getAbandonedCartPageVisitTrackerService',
            'MailPoet\\AutomaticEmails\\WooCommerce\\Events\\FirstPurchase' => 'getFirstPurchaseService',
            'MailPoet\\AutomaticEmails\\WooCommerce\\Events\\PurchasedInCategory' => 'getPurchasedInCategoryService',
            'MailPoet\\AutomaticEmails\\WooCommerce\\Events\\PurchasedProduct' => 'getPurchasedProductService',
            'MailPoet\\AutomaticEmails\\WooCommerce\\WooCommerce' => 'getWooCommerceService',
            'MailPoet\\AutomaticEmails\\WooCommerce\\WooCommerceEventFactory' => 'getWooCommerceEventFactoryService',
            'MailPoet\\Cache\\TransientCache' => 'getTransientCacheService',
            'MailPoet\\Config\\AccessControl' => 'getAccessControlService',
            'MailPoet\\Config\\Activator' => 'getActivatorService',
            'MailPoet\\Config\\AssetsLoader' => 'getAssetsLoaderService',
            'MailPoet\\Config\\Changelog' => 'getChangelogService',
            'MailPoet\\Config\\Hooks' => 'getHooksService',
            'MailPoet\\Config\\HooksWooCommerce' => 'getHooksWooCommerceService',
            'MailPoet\\Config\\Initializer' => 'getInitializerService',
            'MailPoet\\Config\\Menu' => 'getMenuService',
            'MailPoet\\Config\\Populator' => 'getPopulatorService',
            'MailPoet\\Config\\Renderer' => 'getRendererService',
            'MailPoet\\Config\\RendererFactory' => 'getRendererFactoryService',
            'MailPoet\\Config\\Router' => 'getRouterService',
            'MailPoet\\Config\\ServicesChecker' => 'getServicesCheckerService',
            'MailPoet\\Config\\Shortcodes' => 'getShortcodesService',
            'MailPoet\\Cron\\CronHelper' => 'getCronHelperService',
            'MailPoet\\Cron\\CronTrigger' => 'getCronTriggerService',
            'MailPoet\\Cron\\CronWorkerRunner' => 'getCronWorkerRunnerService',
            'MailPoet\\Cron\\CronWorkerScheduler' => 'getCronWorkerSchedulerService',
            'MailPoet\\Cron\\Daemon' => 'getDaemonService',
            'MailPoet\\Cron\\DaemonHttpRunner' => 'getDaemonHttpRunnerService',
            'MailPoet\\Cron\\Supervisor' => 'getSupervisorService',
            'MailPoet\\Cron\\Triggers\\MailPoet' => 'getMailPoetService',
            'MailPoet\\Cron\\Triggers\\WordPress' => 'getWordPressService',
            'MailPoet\\Cron\\Workers\\AuthorizedSendingEmailsCheck' => 'getAuthorizedSendingEmailsCheckService',
            'MailPoet\\Cron\\Workers\\Beamer' => 'getBeamerService',
            'MailPoet\\Cron\\Workers\\Bounce' => 'getBounceService',
            'MailPoet\\Cron\\Workers\\ExportFilesCleanup' => 'getExportFilesCleanupService',
            'MailPoet\\Cron\\Workers\\InactiveSubscribers' => 'getInactiveSubscribersService',
            'MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck' => 'getPremiumKeyCheckService',
            'MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck' => 'getSendingServiceKeyCheckService',
            'MailPoet\\Cron\\Workers\\NewsletterTemplateThumbnails' => 'getNewsletterTemplateThumbnailsService',
            'MailPoet\\Cron\\Workers\\ReEngagementEmailsScheduler' => 'getReEngagementEmailsSchedulerService',
            'MailPoet\\Cron\\Workers\\Scheduler' => 'getSchedulerService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\Migration' => 'getMigrationService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler' => 'getSendingErrorHandlerService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\SendingQueue' => 'getSendingQueue2Service',
            'MailPoet\\Cron\\Workers\\SendingQueue\\SendingThrottlingHandler' => 'getSendingThrottlingHandlerService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\Tasks\\Links' => 'getLinksService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\AutomatedEmails' => 'getAutomatedEmailsService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository' => 'getNewsletterLinkRepositoryService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository' => 'getStatsNotificationsRepositoryService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\Worker' => 'getWorkerService',
            'MailPoet\\Cron\\Workers\\SubscriberLinkTokens' => 'getSubscriberLinkTokensService',
            'MailPoet\\Cron\\Workers\\SubscribersCountCacheRecalculation' => 'getSubscribersCountCacheRecalculationService',
            'MailPoet\\Cron\\Workers\\SubscribersEngagementScore' => 'getSubscribersEngagementScoreService',
            'MailPoet\\Cron\\Workers\\SubscribersLastEngagement' => 'getSubscribersLastEngagementService',
            'MailPoet\\Cron\\Workers\\SubscribersStatsReport' => 'getSubscribersStatsReportService',
            'MailPoet\\Cron\\Workers\\UnsubscribeTokens' => 'getUnsubscribeTokensService',
            'MailPoet\\Cron\\Workers\\WooCommercePastOrders' => 'getWooCommercePastOrdersService',
            'MailPoet\\Cron\\Workers\\WooCommerceSync' => 'getWooCommerceSyncService',
            'MailPoet\\Cron\\Workers\\WorkersFactory' => 'getWorkersFactoryService',
            'MailPoet\\CustomFields\\CustomFieldsRepository' => 'getCustomFieldsRepositoryService',
            'MailPoet\\DI\\ContainerWrapper' => 'getContainerWrapperService',
            'MailPoet\\Doctrine\\ConnectionFactory' => 'getConnectionFactoryService',
            'MailPoet\\Doctrine\\EntityManagerFactory' => 'getEntityManagerFactoryService',
            'MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener' => 'getEmojiEncodingListenerService',
            'MailPoet\\Doctrine\\EventListeners\\LastSubscribedAtListener' => 'getLastSubscribedAtListenerService',
            'MailPoet\\Doctrine\\EventListeners\\TimestampListener' => 'getTimestampListenerService',
            'MailPoet\\Features\\FeatureFlagsController' => 'getFeatureFlagsControllerService',
            'MailPoet\\Features\\FeatureFlagsRepository' => 'getFeatureFlagsRepositoryService',
            'MailPoet\\Features\\FeaturesController' => 'getFeaturesControllerService',
            'MailPoet\\Form\\ApiDataSanitizer' => 'getApiDataSanitizerService',
            'MailPoet\\Form\\AssetsController' => 'getAssetsControllerService',
            'MailPoet\\Form\\Block\\Date' => 'getDateService',
            'MailPoet\\Form\\FormHtmlSanitizer' => 'getFormHtmlSanitizerService',
            'MailPoet\\Form\\FormMessageController' => 'getFormMessageControllerService',
            'MailPoet\\Form\\FormSaveController' => 'getFormSaveControllerService',
            'MailPoet\\Form\\FormsRepository' => 'getFormsRepositoryService',
            'MailPoet\\Form\\Listing\\FormListingRepository' => 'getFormListingRepositoryService',
            'MailPoet\\Form\\Renderer' => 'getRenderer2Service',
            'MailPoet\\Form\\Util\\CustomFonts' => 'getCustomFontsService',
            'MailPoet\\Form\\Util\\FieldNameObfuscator' => 'getFieldNameObfuscatorService',
            'MailPoet\\Helpscout\\Beacon' => 'getBeaconService',
            'MailPoet\\Listing\\Handler' => 'getHandlerService',
            'MailPoet\\Listing\\PageLimit' => 'getPageLimitService',
            'MailPoet\\Logging\\LogRepository' => 'getLogRepositoryService',
            'MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository' => 'getNewsletterTemplatesRepositoryService',
            'MailPoet\\NewsletterTemplates\\ThumbnailSaver' => 'getThumbnailSaverService',
            'MailPoet\\Newsletter\\ApiDataSanitizer' => 'getApiDataSanitizer2Service',
            'MailPoet\\Newsletter\\AutomatedLatestContent' => 'getAutomatedLatestContent2Service',
            'MailPoet\\Newsletter\\AutomaticEmailsRepository' => 'getAutomaticEmailsRepositoryService',
            'MailPoet\\Newsletter\\Links\\Links' => 'getLinks2Service',
            'MailPoet\\Newsletter\\Listing\\NewsletterListingRepository' => 'getNewsletterListingRepositoryService',
            'MailPoet\\Newsletter\\NewsletterHtmlSanitizer' => 'getNewsletterHtmlSanitizerService',
            'MailPoet\\Newsletter\\NewsletterPostsRepository' => 'getNewsletterPostsRepositoryService',
            'MailPoet\\Newsletter\\NewsletterSaveController' => 'getNewsletterSaveControllerService',
            'MailPoet\\Newsletter\\NewslettersRepository' => 'getNewslettersRepositoryService',
            'MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository' => 'getNewsletterOptionFieldsRepositoryService',
            'MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository' => 'getNewsletterOptionsRepositoryService',
            'MailPoet\\Newsletter\\Preview\\SendPreviewController' => 'getSendPreviewControllerService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent' => 'getAbandonedCartContentService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock' => 'getAutomatedLatestContentBlockService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer' => 'getRenderer3Service',
            'MailPoet\\Newsletter\\Renderer\\Columns\\Renderer' => 'getRenderer4Service',
            'MailPoet\\Newsletter\\Renderer\\Preprocessor' => 'getPreprocessorService',
            'MailPoet\\Newsletter\\Renderer\\Renderer' => 'getRenderer5Service',
            'MailPoet\\Newsletter\\Scheduler\\AutomaticEmailScheduler' => 'getAutomaticEmailSchedulerService',
            'MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler' => 'getPostNotificationSchedulerService',
            'MailPoet\\Newsletter\\Scheduler\\ReEngagementScheduler' => 'getReEngagementSchedulerService',
            'MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler' => 'getWelcomeSchedulerService',
            'MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository' => 'getNewsletterSegmentRepositoryService',
            'MailPoet\\Newsletter\\Sending\\ScheduledTaskSubscribersRepository' => 'getScheduledTaskSubscribersRepositoryService',
            'MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository' => 'getScheduledTasksRepositoryService',
            'MailPoet\\Newsletter\\Sending\\SendingQueuesRepository' => 'getSendingQueuesRepositoryService',
            'MailPoet\\Newsletter\\Shortcodes\\Categories\\Date' => 'getDate2Service',
            'MailPoet\\Newsletter\\Shortcodes\\Categories\\Link' => 'getLinkService',
            'MailPoet\\Newsletter\\Shortcodes\\Categories\\Newsletter' => 'getNewsletterService',
            'MailPoet\\Newsletter\\Shortcodes\\Categories\\Subscriber' => 'getSubscriberService',
            'MailPoet\\Newsletter\\Shortcodes\\Shortcodes' => 'getShortcodes2Service',
            'MailPoet\\Newsletter\\Shortcodes\\ShortcodesHelper' => 'getShortcodesHelperService',
            'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository' => 'getNewsletterStatisticsRepositoryService',
            'MailPoet\\Newsletter\\Url' => 'getUrlService',
            'MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController' => 'getViewInBrowserControllerService',
            'MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer' => 'getViewInBrowserRendererService',
            'MailPoet\\Router\\Endpoints\\CronDaemon' => 'getCronDaemonService',
            'MailPoet\\Router\\Endpoints\\FormPreview' => 'getFormPreviewService',
            'MailPoet\\Router\\Endpoints\\Subscription' => 'getSubscriptionService',
            'MailPoet\\Router\\Endpoints\\Track' => 'getTrackService',
            'MailPoet\\Router\\Endpoints\\ViewInBrowser' => 'getViewInBrowserService',
            'MailPoet\\Segments\\DynamicSegments\\DynamicSegmentFilterRepository' => 'getDynamicSegmentFilterRepositoryService',
            'MailPoet\\Segments\\DynamicSegments\\DynamicSegmentsListingRepository' => 'getDynamicSegmentsListingRepositoryService',
            'MailPoet\\Segments\\DynamicSegments\\FilterDataMapper' => 'getFilterDataMapperService',
            'MailPoet\\Segments\\DynamicSegments\\FilterFactory' => 'getFilterFactoryService',
            'MailPoet\\Segments\\DynamicSegments\\FilterHandler' => 'getFilterHandlerService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction' => 'getEmailActionService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\EmailActionClickAny' => 'getEmailActionClickAnyService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\EmailOpensAbsoluteCountAction' => 'getEmailOpensAbsoluteCountActionService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\MailPoetCustomFields' => 'getMailPoetCustomFieldsService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberScore' => 'getSubscriberScoreService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSegment' => 'getSubscriberSegmentService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSubscribedDate' => 'getSubscriberSubscribedDateService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole' => 'getUserRoleService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory' => 'getWooCommerceCategoryService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCountry' => 'getWooCommerceCountryService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceNumberOfOrders' => 'getWooCommerceNumberOfOrdersService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct' => 'getWooCommerceProductService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceSubscription' => 'getWooCommerceSubscriptionService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceTotalSpent' => 'getWooCommerceTotalSpentService',
            'MailPoet\\Segments\\DynamicSegments\\SegmentSaveController' => 'getSegmentSaveControllerService',
            'MailPoet\\Segments\\SegmentDependencyValidator' => 'getSegmentDependencyValidatorService',
            'MailPoet\\Segments\\SegmentListingRepository' => 'getSegmentListingRepositoryService',
            'MailPoet\\Segments\\SegmentSaveController' => 'getSegmentSaveController2Service',
            'MailPoet\\Segments\\SegmentSubscribersRepository' => 'getSegmentSubscribersRepositoryService',
            'MailPoet\\Segments\\SegmentsRepository' => 'getSegmentsRepositoryService',
            'MailPoet\\Segments\\SegmentsSimpleListRepository' => 'getSegmentsSimpleListRepositoryService',
            'MailPoet\\Segments\\SubscribersFinder' => 'getSubscribersFinderService',
            'MailPoet\\Segments\\WP' => 'getWPService',
            'MailPoet\\Segments\\WooCommerce' => 'getWooCommerce2Service',
            'MailPoet\\Services\\AuthorizedEmailsController' => 'getAuthorizedEmailsControllerService',
            'MailPoet\\Services\\Bridge' => 'getBridgeService',
            'MailPoet\\Services\\CongratulatoryMssEmailController' => 'getCongratulatoryMssEmailControllerService',
            'MailPoet\\Settings\\SettingsController' => 'getSettingsControllerService',
            'MailPoet\\Settings\\SettingsRepository' => 'getSettingsRepositoryService',
            'MailPoet\\Settings\\TrackingConfig' => 'getTrackingConfigService',
            'MailPoet\\Settings\\UserFlagsRepository' => 'getUserFlagsRepositoryService',
            'MailPoet\\Statistics\\GATracking' => 'getGATrackingService',
            'MailPoet\\Statistics\\StatisticsBouncesRepository' => 'getStatisticsBouncesRepositoryService',
            'MailPoet\\Statistics\\StatisticsClicksRepository' => 'getStatisticsClicksRepositoryService',
            'MailPoet\\Statistics\\StatisticsFormsRepository' => 'getStatisticsFormsRepositoryService',
            'MailPoet\\Statistics\\StatisticsOpensRepository' => 'getStatisticsOpensRepositoryService',
            'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository' => 'getStatisticsWooCommercePurchasesRepositoryService',
            'MailPoet\\Statistics\\Track\\Opens' => 'getOpensService',
            'MailPoet\\Statistics\\Track\\SubscriberCookie' => 'getSubscriberCookieService',
            'MailPoet\\Statistics\\Track\\SubscriberHandler' => 'getSubscriberHandlerService',
            'MailPoet\\Statistics\\Track\\Unsubscribes' => 'getUnsubscribesService',
            'MailPoet\\Statistics\\UserAgentsRepository' => 'getUserAgentsRepositoryService',
            'MailPoet\\Subscribers\\ConfirmationEmailMailer' => 'getConfirmationEmailMailerService',
            'MailPoet\\Subscribers\\ImportExport\\ImportExportRepository' => 'getImportExportRepositoryService',
            'MailPoet\\Subscribers\\ImportExport\\PersonalDataExporters\\NewsletterClicksExporter' => 'getNewsletterClicksExporterService',
            'MailPoet\\Subscribers\\ImportExport\\PersonalDataExporters\\NewsletterOpensExporter' => 'getNewsletterOpensExporterService',
            'MailPoet\\Subscribers\\ImportExport\\PersonalDataExporters\\NewslettersExporter' => 'getNewslettersExporterService',
            'MailPoet\\Subscribers\\LinkTokens' => 'getLinkTokensService',
            'MailPoet\\Subscribers\\NewSubscriberNotificationMailer' => 'getNewSubscriberNotificationMailerService',
            'MailPoet\\Subscribers\\RequiredCustomFieldValidator' => 'getRequiredCustomFieldValidatorService',
            'MailPoet\\Subscribers\\SubscriberActions' => 'getSubscriberActionsService',
            'MailPoet\\Subscribers\\SubscriberCustomFieldRepository' => 'getSubscriberCustomFieldRepositoryService',
            'MailPoet\\Subscribers\\SubscriberIPsRepository' => 'getSubscriberIPsRepositoryService',
            'MailPoet\\Subscribers\\SubscriberListingRepository' => 'getSubscriberListingRepositoryService',
            'MailPoet\\Subscribers\\SubscriberSaveController' => 'getSubscriberSaveControllerService',
            'MailPoet\\Subscribers\\SubscriberSegmentRepository' => 'getSubscriberSegmentRepositoryService',
            'MailPoet\\Subscribers\\SubscriberSubscribeController' => 'getSubscriberSubscribeControllerService',
            'MailPoet\\Subscribers\\SubscribersCountsController' => 'getSubscribersCountsControllerService',
            'MailPoet\\Subscribers\\SubscribersRepository' => 'getSubscribersRepositoryService',
            'MailPoet\\Subscription\\Captcha' => 'getCaptchaService',
            'MailPoet\\Subscription\\CaptchaRenderer' => 'getCaptchaRendererService',
            'MailPoet\\Subscription\\Comment' => 'getCommentService',
            'MailPoet\\Subscription\\Form' => 'getFormService',
            'MailPoet\\Subscription\\Manage' => 'getManageService',
            'MailPoet\\Subscription\\ManageSubscriptionFormRenderer' => 'getManageSubscriptionFormRendererService',
            'MailPoet\\Subscription\\Pages' => 'getPagesService',
            'MailPoet\\Subscription\\Registration' => 'getRegistrationService',
            'MailPoet\\Subscription\\SubscriptionUrlFactory' => 'getSubscriptionUrlFactoryService',
            'MailPoet\\Subscription\\Throttling' => 'getThrottlingService',
            'MailPoet\\Util\\CdnAssetUrl' => 'getCdnAssetUrlService',
            'MailPoet\\Util\\License\\Features\\Subscribers' => 'getSubscribers4Service',
            'MailPoet\\Util\\License\\License' => 'getLicenseService',
            'MailPoet\\Util\\Url' => 'getUrl2Service',
            'MailPoet\\WP\\AutocompletePostListLoader' => 'getAutocompletePostListLoaderService',
            'MailPoet\\WP\\Emoji' => 'getEmojiService',
            'MailPoet\\WP\\Functions' => 'getFunctionsService',
            'MailPoet\\WooCommerce\\Helper' => 'getHelperService',
            'MailPoet\\WooCommerce\\Settings' => 'getSettings3Service',
            'MailPoet\\WooCommerce\\SubscriberEngagement' => 'getSubscriberEngagementService',
            'MailPoet\\WooCommerce\\Subscription' => 'getSubscription2Service',
            'MailPoet\\WooCommerce\\TransactionalEmailHooks' => 'getTransactionalEmailHooksService',
            'MailPoet\\WooCommerce\\TransactionalEmails' => 'getTransactionalEmailsService',
            'MailPoet\\WooCommerce\\TransactionalEmails\\ContentPreprocessor' => 'getContentPreprocessorService',
            'MailPoet\\WooCommerce\\TransactionalEmails\\Renderer' => 'getRenderer6Service',
            'MailPoet\\WooCommerce\\TransactionalEmails\\Template' => 'getTemplateService',
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
            'MailPoetVendor\\Doctrine\\ORM\\Configuration' => true,
            'MailPoetVendor\\Psr\\Container\\ContainerInterface' => true,
            'MailPoetVendor\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => true,
            'MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface' => true,
            'MailPoetVendor\\csstidy' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder' => true,
            'MailPoet\\Config\\DatabaseInitializer' => true,
            'MailPoet\\Config\\Localizer' => true,
            'MailPoet\\Config\\MP2Migrator' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler' => true,
            'MailPoet\\CustomFields\\ApiDataSanitizer' => true,
            'MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider' => true,
            'MailPoet\\Doctrine\\ConfigurationFactory' => true,
            'MailPoet\\Doctrine\\EventListeners\\ValidationListener' => true,
            'MailPoet\\Doctrine\\Validator\\ValidatorFactory' => true,
            'MailPoet\\Form\\BlockStylesRenderer' => true,
            'MailPoet\\Form\\BlockWrapperRenderer' => true,
            'MailPoet\\Form\\Block\\BlockRendererHelper' => true,
            'MailPoet\\Form\\Block\\Checkbox' => true,
            'MailPoet\\Form\\Block\\Column' => true,
            'MailPoet\\Form\\Block\\Columns' => true,
            'MailPoet\\Form\\Block\\Divider' => true,
            'MailPoet\\Form\\Block\\Heading' => true,
            'MailPoet\\Form\\Block\\Html' => true,
            'MailPoet\\Form\\Block\\Image' => true,
            'MailPoet\\Form\\Block\\Paragraph' => true,
            'MailPoet\\Form\\Block\\Radio' => true,
            'MailPoet\\Form\\Block\\Segment' => true,
            'MailPoet\\Form\\Block\\Select' => true,
            'MailPoet\\Form\\Block\\Submit' => true,
            'MailPoet\\Form\\Block\\Text' => true,
            'MailPoet\\Form\\Block\\Textarea' => true,
            'MailPoet\\Form\\BlocksRenderer' => true,
            'MailPoet\\Form\\DisplayFormInWPContent' => true,
            'MailPoet\\Form\\PreviewPage' => true,
            'MailPoet\\Form\\Templates\\TemplateRepository' => true,
            'MailPoet\\Form\\Util\\Styles' => true,
            'MailPoet\\Logging\\LoggerFactory' => true,
            'MailPoet\\Mailer\\Mailer' => true,
            'MailPoet\\Mailer\\MetaInfo' => true,
            'MailPoet\\Mailer\\Methods\\Common\\BlacklistCheck' => true,
            'MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Button' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Divider' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Footer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Header' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Image' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Placeholder' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Social' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Text' => true,
            'MailPoet\\PostEditorBlocks\\PostEditorBlock' => true,
            'MailPoet\\PostEditorBlocks\\SubscriptionFormBlock' => true,
            'MailPoet\\PostEditorBlocks\\WooCommerceBlocksIntegration' => true,
            'MailPoet\\Referrals\\ReferralDetector' => true,
            'MailPoet\\Router\\Router' => true,
            'MailPoet\\Settings\\UserFlagsController' => true,
            'MailPoet\\Statistics\\StatisticsUnsubscribesRepository' => true,
            'MailPoet\\Statistics\\Track\\Clicks' => true,
            'MailPoet\\Statistics\\Track\\WooCommercePurchases' => true,
            'MailPoet\\Subscribers\\InactiveSubscribersController' => true,
            'MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository' => true,
            'MailPoet\\Subscription\\CaptchaSession' => true,
            'MailPoet\\Tasks\\State' => true,
            'MailPoet\\Util\\Cookies' => true,
            'MailPoet\\Util\\DBCollationChecker' => true,
            'MailPoet\\Util\\Installation' => true,
            'MailPoet\\Util\\Notices\\PermanentNotices' => true,
            'MailPoet\\Util\\Security' => true,
        ];
    }

    /**
     * Gets the public 'MailPoetVendor\CSS' shared autowired service.
     *
     * @return \MailPoetVendor\CSS
     */
    protected function getCSSService()
    {
        return $this->services['MailPoetVendor\\CSS'] = new \MailPoetVendor\CSS();
    }

    /**
     * Gets the public 'MailPoetVendor\Doctrine\DBAL\Connection' shared autowired service.
     *
     * @return \MailPoetVendor\Doctrine\DBAL\Connection
     */
    protected function getConnectionService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection'] = ($this->services['MailPoet\\Doctrine\\ConnectionFactory'] ?? ($this->services['MailPoet\\Doctrine\\ConnectionFactory'] = new \MailPoet\Doctrine\ConnectionFactory()))->createConnection();
    }

    /**
     * Gets the public 'MailPoetVendor\Doctrine\ORM\EntityManager' shared autowired service.
     *
     * @return \MailPoetVendor\Doctrine\ORM\EntityManager
     */
    protected function getEntityManagerService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] = ($this->services['MailPoet\\Doctrine\\EntityManagerFactory'] ?? $this->getEntityManagerFactoryService())->createEntityManager();
    }

    /**
     * Gets the public 'MailPoet\API\JSON\API' shared autowired service.
     *
     * @return \MailPoet\API\JSON\API
     */
    protected function getAPIService()
    {
        return $this->services['MailPoet\\API\\JSON\\API'] = new \MailPoet\API\JSON\API(($this->services['MailPoet\\DI\\ContainerWrapper'] ?? $this->getContainerWrapperService()), ($this->services['MailPoet\\Config\\AccessControl'] ?? ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl())), ($this->services['MailPoet\\API\\JSON\\ErrorHandler'] ?? $this->getErrorHandlerService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ErrorHandler' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ErrorHandler
     */
    protected function getErrorHandlerService()
    {
        return $this->services['MailPoet\\API\\JSON\\ErrorHandler'] = new \MailPoet\API\JSON\ErrorHandler(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ResponseBuilders\DynamicSegmentsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\DynamicSegmentsResponseBuilder
     */
    protected function getDynamicSegmentsResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\DynamicSegmentsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\DynamicSegmentsResponseBuilder(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] ?? $this->getSegmentsResponseBuilderService()), ($this->services['MailPoet\\Segments\\SegmentDependencyValidator'] ?? $this->getSegmentDependencyValidatorService()), ($this->services['MailPoet\\Subscribers\\SubscribersCountsController'] ?? $this->getSubscribersCountsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder
     */
    protected function getFormsResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder(($this->services['MailPoet\\Statistics\\StatisticsFormsRepository'] ?? $this->getStatisticsFormsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ResponseBuilders\NewslettersResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\NewslettersResponseBuilder
     */
    protected function getNewslettersResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\NewslettersResponseBuilder(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] ?? $this->getNewsletterStatisticsRepositoryService()), ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder
     */
    protected function getSegmentsResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Subscribers\\SubscribersCountsController'] ?? $this->getSubscribersCountsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder
     */
    protected function getSubscribersResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] ?? $this->getSubscriberSegmentRepositoryService()), ($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository'] ?? $this->getSubscriberCustomFieldRepositoryService()), ($this->privates['MailPoet\\Statistics\\StatisticsUnsubscribesRepository'] ?? $this->getStatisticsUnsubscribesRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Analytics' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Analytics
     */
    protected function getAnalyticsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Analytics'] = new \MailPoet\API\JSON\v1\Analytics(($this->services['MailPoet\\Analytics\\Reporter'] ?? $this->getReporterService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\AutomatedLatestContent' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\AutomatedLatestContent
     */
    protected function getAutomatedLatestContentService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\AutomatedLatestContent'] = new \MailPoet\API\JSON\v1\AutomatedLatestContent(($this->services['MailPoet\\Newsletter\\AutomatedLatestContent'] ?? $this->getAutomatedLatestContent2Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\AutomaticEmails' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\AutomaticEmails
     */
    protected function getAutomaticEmailsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\AutomaticEmails'] = new \MailPoet\API\JSON\v1\AutomaticEmails(($this->services['MailPoet\\AutomaticEmails\\AutomaticEmails'] ?? $this->getAutomaticEmails2Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\CustomFields' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\CustomFields
     */
    protected function getCustomFieldsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\CustomFields'] = new \MailPoet\API\JSON\v1\CustomFields(($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()), ($this->privates['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] ?? ($this->privates['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\DynamicSegments' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\DynamicSegments
     */
    protected function getDynamicSegmentsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\DynamicSegments'] = new \MailPoet\API\JSON\v1\DynamicSegments(($this->services['MailPoet\\Listing\\Handler'] ?? ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())), ($this->services['MailPoet\\Segments\\DynamicSegments\\DynamicSegmentsListingRepository'] ?? $this->getDynamicSegmentsListingRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\DynamicSegmentsResponseBuilder'] ?? $this->getDynamicSegmentsResponseBuilderService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\FilterDataMapper'] ?? $this->getFilterDataMapperService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\SegmentSaveController'] ?? $this->getSegmentSaveControllerService()), ($this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] ?? $this->getNewsletterSegmentRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\FeatureFlags' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\FeatureFlags
     */
    protected function getFeatureFlagsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\FeatureFlags'] = new \MailPoet\API\JSON\v1\FeatureFlags(($this->services['MailPoet\\Features\\FeaturesController'] ?? $this->getFeaturesControllerService()), ($this->services['MailPoet\\Features\\FeatureFlagsController'] ?? $this->getFeatureFlagsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Forms' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Forms
     */
    protected function getFormsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Forms'] = new \MailPoet\API\JSON\v1\Forms(($this->services['MailPoet\\Listing\\Handler'] ?? ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())), ($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->privates['MailPoet\\Form\\Templates\\TemplateRepository'] ?? $this->getTemplateRepositoryService()), ($this->services['MailPoet\\Form\\Listing\\FormListingRepository'] ?? $this->getFormListingRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder'] ?? $this->getFormsResponseBuilderService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WP\\Emoji'] ?? $this->getEmojiService()), ($this->services['MailPoet\\Form\\ApiDataSanitizer'] ?? $this->getApiDataSanitizerService()), ($this->services['MailPoet\\Form\\FormSaveController'] ?? $this->getFormSaveControllerService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\ImportExport' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\ImportExport
     */
    protected function getImportExportService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\ImportExport'] = new \MailPoet\API\JSON\v1\ImportExport(($this->services['MailPoet\\Segments\\WP'] ?? $this->getWPService()), ($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()), ($this->services['MailPoet\\Subscribers\\ImportExport\\ImportExportRepository'] ?? $this->getImportExportRepositoryService()), ($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] ?? $this->getNewsletterOptionsRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentSaveController'] ?? $this->getSegmentSaveController2Service()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] ?? $this->getSegmentsResponseBuilderService()), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\MP2Migrator' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\MP2Migrator
     */
    protected function getMP2MigratorService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\MP2Migrator'] = new \MailPoet\API\JSON\v1\MP2Migrator(($this->privates['MailPoet\\Config\\MP2Migrator'] ?? $this->getMP2Migrator2Service()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Mailer' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Mailer
     */
    protected function getMailerService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Mailer'] = new \MailPoet\API\JSON\v1\Mailer(($this->services['MailPoet\\Services\\AuthorizedEmailsController'] ?? $this->getAuthorizedEmailsControllerService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()), ($this->privates['MailPoet\\Mailer\\MetaInfo'] ?? ($this->privates['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\NewsletterLinks' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\NewsletterLinks
     */
    protected function getNewsletterLinksService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\NewsletterLinks'] = new \MailPoet\API\JSON\v1\NewsletterLinks(($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] ?? $this->getNewsletterLinkRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\NewsletterTemplates' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\NewsletterTemplates
     */
    protected function getNewsletterTemplatesService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\NewsletterTemplates'] = new \MailPoet\API\JSON\v1\NewsletterTemplates(($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] ?? $this->getNewsletterTemplatesRepositoryService()), new \MailPoet\API\JSON\ResponseBuilders\NewsletterTemplatesResponseBuilder(), ($this->services['MailPoet\\NewsletterTemplates\\ThumbnailSaver'] ?? $this->getThumbnailSaverService()), ($this->services['MailPoet\\Newsletter\\ApiDataSanitizer'] ?? $this->getApiDataSanitizer2Service()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Newsletters' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Newsletters
     */
    protected function getNewslettersService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Newsletters'] = new \MailPoet\API\JSON\v1\Newsletters(($this->services['MailPoet\\Listing\\Handler'] ?? ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Listing\\NewsletterListingRepository'] ?? $this->getNewsletterListingRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder'] ?? $this->getNewslettersResponseBuilderService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] ?? $this->getPostNotificationSchedulerService()), ($this->services['MailPoet\\WP\\Emoji'] ?? $this->getEmojiService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\Newsletter\\Preview\\SendPreviewController'] ?? $this->getSendPreviewControllerService()), ($this->services['MailPoet\\Newsletter\\NewsletterSaveController'] ?? $this->getNewsletterSaveControllerService()), ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Premium' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Premium
     */
    protected function getPremiumService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Premium'] = new \MailPoet\API\JSON\v1\Premium(($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Segments' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Segments
     */
    protected function getSegmentsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Segments'] = new \MailPoet\API\JSON\v1\Segments(($this->services['MailPoet\\Listing\\Handler'] ?? ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentListingRepository'] ?? $this->getSegmentListingRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] ?? $this->getSegmentsResponseBuilderService()), ($this->services['MailPoet\\Segments\\SegmentSaveController'] ?? $this->getSegmentSaveController2Service()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Segments\\WooCommerce'] ?? $this->getWooCommerce2Service()), ($this->services['MailPoet\\Segments\\WP'] ?? $this->getWPService()), ($this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] ?? $this->getNewsletterSegmentRepositoryService()), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\SendingQueue' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\SendingQueue
     */
    protected function getSendingQueueService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\SendingQueue'] = new \MailPoet\API\JSON\v1\SendingQueue(($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] ?? $this->getSendingQueuesRepositoryService()), ($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()), ($this->services['MailPoet\\Segments\\SubscribersFinder'] ?? $this->getSubscribersFinderService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\SendingTaskSubscribers' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\SendingTaskSubscribers
     */
    protected function getSendingTaskSubscribersService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\SendingTaskSubscribers'] = new \MailPoet\API\JSON\v1\SendingTaskSubscribers(($this->services['MailPoet\\Listing\\Handler'] ?? ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Services' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Services
     */
    protected function getServicesService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Services'] = new \MailPoet\API\JSON\v1\Services(($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Analytics\\Analytics'] ?? $this->getAnalytics2Service()), ($this->services['MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck'] ?? $this->getSendingServiceKeyCheckService()), ($this->services['MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck'] ?? $this->getPremiumKeyCheckService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Services\\CongratulatoryMssEmailController'] ?? $this->getCongratulatoryMssEmailControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Settings' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Settings
     */
    protected function getSettingsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Settings'] = new \MailPoet\API\JSON\v1\Settings(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()), ($this->services['MailPoet\\Services\\AuthorizedEmailsController'] ?? $this->getAuthorizedEmailsControllerService()), ($this->services['MailPoet\\WooCommerce\\TransactionalEmails'] ?? $this->getTransactionalEmailsService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Statistics\\StatisticsOpensRepository'] ?? $this->getStatisticsOpensRepositoryService()), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()), ($this->services['MailPoet\\Form\\FormMessageController'] ?? $this->getFormMessageControllerService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersCountsController'] ?? $this->getSubscribersCountsControllerService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Setup' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Setup
     */
    protected function getSetupService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Setup'] = new \MailPoet\API\JSON\v1\Setup(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Config\\Activator'] ?? $this->getActivatorService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\SubscriberStats' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\SubscriberStats
     */
    protected function getSubscriberStatsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\SubscriberStats'] = new \MailPoet\API\JSON\v1\SubscriberStats(($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), new \MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper()))));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Subscribers' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Subscribers
     */
    protected function getSubscribersService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Subscribers'] = new \MailPoet\API\JSON\v1\Subscribers(($this->services['MailPoet\\Listing\\Handler'] ?? ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())), ($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] ?? $this->getConfirmationEmailMailerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder'] ?? $this->getSubscribersResponseBuilderService()), ($this->services['MailPoet\\Subscribers\\SubscriberListingRepository'] ?? $this->getSubscriberListingRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSaveController'] ?? $this->getSubscriberSaveControllerService()), ($this->services['MailPoet\\Subscribers\\SubscriberSubscribeController'] ?? $this->getSubscriberSubscribeControllerService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\UserFlags' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\UserFlags
     */
    protected function getUserFlagsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\UserFlags'] = new \MailPoet\API\JSON\v1\UserFlags(($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\WoocommerceSettings' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\WoocommerceSettings
     */
    protected function getWoocommerceSettingsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\WoocommerceSettings'] = new \MailPoet\API\JSON\v1\WoocommerceSettings(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\API\MP\v1\API' shared autowired service.
     *
     * @return \MailPoet\API\MP\v1\API
     */
    protected function getAPI2Service()
    {
        return $this->services['MailPoet\\API\\MP\\v1\\API'] = new \MailPoet\API\MP\v1\API(($this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] ?? $this->getRequiredCustomFieldValidatorService()), ($this->services['MailPoet\\API\\MP\\v1\\CustomFields'] ?? $this->getCustomFields2Service()), ($this->services['MailPoet\\API\\MP\\v1\\Subscribers'] ?? $this->getSubscribers2Service()), ($this->services['MailPoet\\Config\\Changelog'] ?? $this->getChangelogService()));
    }

    /**
     * Gets the public 'MailPoet\API\MP\v1\CustomFields' shared autowired service.
     *
     * @return \MailPoet\API\MP\v1\CustomFields
     */
    protected function getCustomFields2Service()
    {
        return $this->services['MailPoet\\API\\MP\\v1\\CustomFields'] = new \MailPoet\API\MP\v1\CustomFields(new \MailPoet\CustomFields\ApiDataSanitizer(), ($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\API\MP\v1\Subscribers' shared autowired service.
     *
     * @return \MailPoet\API\MP\v1\Subscribers
     */
    protected function getSubscribers2Service()
    {
        return $this->services['MailPoet\\API\\MP\\v1\\Subscribers'] = new \MailPoet\API\MP\v1\Subscribers(($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] ?? $this->getConfirmationEmailMailerService()), ($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] ?? $this->getSubscriberSegmentRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder'] ?? $this->getSubscribersResponseBuilderService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\PageRenderer' shared autowired service.
     *
     * @return \MailPoet\AdminPages\PageRenderer
     */
    protected function getPageRendererService()
    {
        return $this->services['MailPoet\\AdminPages\\PageRenderer'] = new \MailPoet\AdminPages\PageRenderer(($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Features\\FeaturesController'] ?? $this->getFeaturesControllerService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Cron\\Workers\\SubscribersCountCacheRecalculation'] ?? $this->getSubscribersCountCacheRecalculationService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\ExperimentalFeatures' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\ExperimentalFeatures
     */
    protected function getExperimentalFeaturesService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\ExperimentalFeatures'] = new \MailPoet\AdminPages\Pages\ExperimentalFeatures(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\FormEditor' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\FormEditor
     */
    protected function getFormEditorService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\FormEditor'] = new \MailPoet\AdminPages\Pages\FormEditor(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()), ($this->privates['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] ?? ($this->privates['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder())), ($this->services['MailPoet\\Form\\Renderer'] ?? $this->getRenderer2Service()), ($this->services['MailPoet\\Form\\Block\\Date'] ?? $this->getDateService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->privates['MailPoet\\Config\\Localizer'] ?? ($this->privates['MailPoet\\Config\\Localizer'] = new \MailPoet\Config\Localizer())), ($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()), ($this->services['MailPoet\\WP\\AutocompletePostListLoader'] ?? $this->getAutocompletePostListLoaderService()), ($this->privates['MailPoet\\Form\\Templates\\TemplateRepository'] ?? $this->getTemplateRepositoryService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentsSimpleListRepository'] ?? $this->getSegmentsSimpleListRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Forms' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Forms
     */
    protected function getForms2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Forms'] = new \MailPoet\AdminPages\Pages\Forms(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Listing\\PageLimit'] ?? $this->getPageLimitService()), ($this->privates['MailPoet\\Util\\Installation'] ?? $this->getInstallationService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Help' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Help
     */
    protected function getHelpService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Help'] = new \MailPoet\AdminPages\Pages\Help(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), new \MailPoet\Tasks\State(($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService())), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Helpscout\\Beacon'] ?? $this->getBeaconService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Logs' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Logs
     */
    protected function getLogsService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Logs'] = new \MailPoet\AdminPages\Pages\Logs(($this->services['MailPoet\\Logging\\LogRepository'] ?? $this->getLogRepositoryService()), ($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\MP2Migration' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\MP2Migration
     */
    protected function getMP2MigrationService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\MP2Migration'] = new \MailPoet\AdminPages\Pages\MP2Migration(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->privates['MailPoet\\Config\\MP2Migrator'] ?? $this->getMP2Migrator2Service()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\NewsletterEditor' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\NewsletterEditor
     */
    protected function getNewsletterEditorService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\NewsletterEditor'] = new \MailPoet\AdminPages\Pages\NewsletterEditor(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\TransactionalEmails'] ?? $this->getTransactionalEmailsService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\ShortcodesHelper'] ?? $this->getShortcodesHelperService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\WooCommerce\\TransactionalEmailHooks'] ?? $this->getTransactionalEmailHooksService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Newsletters' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Newsletters
     */
    protected function getNewsletters2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Newsletters'] = new \MailPoet\AdminPages\Pages\Newsletters(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Listing\\PageLimit'] ?? $this->getPageLimitService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->privates['MailPoet\\Settings\\UserFlagsController'] ?? $this->getUserFlagsControllerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->privates['MailPoet\\Util\\Installation'] ?? $this->getInstallationService()), ($this->services['MailPoet\\Features\\FeaturesController'] ?? $this->getFeaturesControllerService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] ?? $this->getNewsletterTemplatesRepositoryService()), ($this->services['MailPoet\\WP\\AutocompletePostListLoader'] ?? $this->getAutocompletePostListLoaderService()), ($this->services['MailPoet\\AutomaticEmails\\AutomaticEmails'] ?? $this->getAutomaticEmails2Service()), ($this->services['MailPoet\\Segments\\SegmentsSimpleListRepository'] ?? $this->getSegmentsSimpleListRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Premium' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Premium
     */
    protected function getPremium2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Premium'] = new \MailPoet\AdminPages\Pages\Premium(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Segments' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Segments
     */
    protected function getSegments2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Segments'] = new \MailPoet\AdminPages\Pages\Segments(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Listing\\PageLimit'] ?? $this->getPageLimitService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\WP\\AutocompletePostListLoader'] ?? $this->getAutocompletePostListLoaderService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()), ($this->privates['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] ?? ($this->privates['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder())), ($this->services['MailPoet\\Segments\\SegmentDependencyValidator'] ?? $this->getSegmentDependencyValidatorService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()), ($this->services['MailPoet\\Cache\\TransientCache'] ?? $this->getTransientCacheService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Settings' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Settings
     */
    protected function getSettings2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Settings'] = new \MailPoet\AdminPages\Pages\Settings(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->privates['MailPoet\\Util\\Installation'] ?? $this->getInstallationService()), ($this->services['MailPoet\\Subscription\\Captcha'] ?? $this->getCaptchaService()), ($this->services['MailPoet\\Segments\\SegmentsSimpleListRepository'] ?? $this->getSegmentsSimpleListRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Subscribers' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Subscribers
     */
    protected function getSubscribers3Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Subscribers'] = new \MailPoet\AdminPages\Pages\Subscribers(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Listing\\PageLimit'] ?? $this->getPageLimitService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Form\\Block\\Date'] ?? $this->getDateService()), ($this->services['MailPoet\\Segments\\SegmentsSimpleListRepository'] ?? $this->getSegmentsSimpleListRepositoryService()), ($this->services['MailPoet\\Cache\\TransientCache'] ?? $this->getTransientCacheService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\SubscribersExport' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\SubscribersExport
     */
    protected function getSubscribersExportService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\SubscribersExport'] = new \MailPoet\AdminPages\Pages\SubscribersExport(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\SubscribersImport' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\SubscribersImport
     */
    protected function getSubscribersImportService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\SubscribersImport'] = new \MailPoet\AdminPages\Pages\SubscribersImport(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->privates['MailPoet\\Util\\Installation'] ?? $this->getInstallationService()), ($this->services['MailPoet\\Form\\Block\\Date'] ?? $this->getDateService()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\WelcomeWizard' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\WelcomeWizard
     */
    protected function getWelcomeWizardService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\WelcomeWizard'] = new \MailPoet\AdminPages\Pages\WelcomeWizard(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Features\\FeaturesController'] ?? $this->getFeaturesControllerService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()));
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\WooCommerceSetup' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\WooCommerceSetup
     */
    protected function getWooCommerceSetupService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\WooCommerceSetup'] = new \MailPoet\AdminPages\Pages\WooCommerceSetup(($this->services['MailPoet\\AdminPages\\PageRenderer'] ?? $this->getPageRendererService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Analytics\Analytics' shared autowired service.
     *
     * @return \MailPoet\Analytics\Analytics
     */
    protected function getAnalytics2Service()
    {
        return $this->services['MailPoet\\Analytics\\Analytics'] = new \MailPoet\Analytics\Analytics(($this->services['MailPoet\\Analytics\\Reporter'] ?? $this->getReporterService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Analytics\Reporter' shared autowired service.
     *
     * @return \MailPoet\Analytics\Reporter
     */
    protected function getReporterService()
    {
        return $this->services['MailPoet\\Analytics\\Reporter'] = new \MailPoet\Analytics\Reporter(($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\DynamicSegmentFilterRepository'] ?? $this->getDynamicSegmentFilterRepositoryService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\AutomaticEmailFactory' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\AutomaticEmailFactory
     */
    protected function getAutomaticEmailFactoryService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\AutomaticEmailFactory'] = new \MailPoet\AutomaticEmails\AutomaticEmailFactory(($this->services['MailPoet\\DI\\ContainerWrapper'] ?? $this->getContainerWrapperService()));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\AutomaticEmails' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\AutomaticEmails
     */
    protected function getAutomaticEmails2Service()
    {
        return $this->services['MailPoet\\AutomaticEmails\\AutomaticEmails'] = new \MailPoet\AutomaticEmails\AutomaticEmails(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\AutomaticEmails\\AutomaticEmailFactory'] ?? $this->getAutomaticEmailFactoryService()));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart
     */
    protected function getAbandonedCartService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\Events\\AbandonedCart'] = new \MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Statistics\\Track\\SubscriberCookie'] ?? $this->getSubscriberCookieService()), ($this->services['MailPoet\\AutomaticEmails\\WooCommerce\\Events\\AbandonedCartPageVisitTracker'] ?? $this->getAbandonedCartPageVisitTrackerService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\AutomaticEmailScheduler'] ?? $this->getAutomaticEmailSchedulerService()));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCartPageVisitTracker' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCartPageVisitTracker
     */
    protected function getAbandonedCartPageVisitTrackerService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\Events\\AbandonedCartPageVisitTracker'] = new \MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCartPageVisitTracker(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Statistics\\Track\\SubscriberCookie'] ?? $this->getSubscriberCookieService()));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\Events\FirstPurchase' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\Events\FirstPurchase
     */
    protected function getFirstPurchaseService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\Events\\FirstPurchase'] = new \MailPoet\AutomaticEmails\WooCommerce\Events\FirstPurchase(($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedInCategory' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedInCategory
     */
    protected function getPurchasedInCategoryService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\Events\\PurchasedInCategory'] = new \MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedInCategory(($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedProduct' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedProduct
     */
    protected function getPurchasedProductService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\Events\\PurchasedProduct'] = new \MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedProduct(($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\WooCommerce' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\WooCommerce
     */
    protected function getWooCommerceService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\WooCommerce'] = new \MailPoet\AutomaticEmails\WooCommerce\WooCommerce(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\AutomaticEmails\\WooCommerce\\WooCommerceEventFactory'] ?? $this->getWooCommerceEventFactoryService()));
    }

    /**
     * Gets the public 'MailPoet\AutomaticEmails\WooCommerce\WooCommerceEventFactory' shared autowired service.
     *
     * @return \MailPoet\AutomaticEmails\WooCommerce\WooCommerceEventFactory
     */
    protected function getWooCommerceEventFactoryService()
    {
        return $this->services['MailPoet\\AutomaticEmails\\WooCommerce\\WooCommerceEventFactory'] = new \MailPoet\AutomaticEmails\WooCommerce\WooCommerceEventFactory(($this->services['MailPoet\\DI\\ContainerWrapper'] ?? $this->getContainerWrapperService()));
    }

    /**
     * Gets the public 'MailPoet\Cache\TransientCache' shared autowired service.
     *
     * @return \MailPoet\Cache\TransientCache
     */
    protected function getTransientCacheService()
    {
        return $this->services['MailPoet\\Cache\\TransientCache'] = new \MailPoet\Cache\TransientCache(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Config\AccessControl' shared autowired service.
     *
     * @return \MailPoet\Config\AccessControl
     */
    protected function getAccessControlService()
    {
        return $this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl();
    }

    /**
     * Gets the public 'MailPoet\Config\Activator' shared autowired service.
     *
     * @return \MailPoet\Config\Activator
     */
    protected function getActivatorService()
    {
        return $this->services['MailPoet\\Config\\Activator'] = new \MailPoet\Config\Activator(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Config\\Populator'] ?? $this->getPopulatorService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Config\AssetsLoader' shared autowired service.
     *
     * @return \MailPoet\Config\AssetsLoader
     */
    protected function getAssetsLoaderService()
    {
        return $this->services['MailPoet\\Config\\AssetsLoader'] = new \MailPoet\Config\AssetsLoader(($this->services['MailPoet\\Config\\RendererFactory'] ?? ($this->services['MailPoet\\Config\\RendererFactory'] = new \MailPoet\Config\RendererFactory())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Config\Changelog' shared autowired service.
     *
     * @return \MailPoet\Config\Changelog
     */
    protected function getChangelogService()
    {
        return $this->services['MailPoet\\Config\\Changelog'] = new \MailPoet\Config\Changelog(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Util\\Url'] ?? $this->getUrl2Service()), ($this->privates['MailPoet\\Config\\MP2Migrator'] ?? $this->getMP2Migrator2Service()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Config\Hooks' shared autowired service.
     *
     * @return \MailPoet\Config\Hooks
     */
    protected function getHooksService()
    {
        $a = ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService());
        $b = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));

        return $this->services['MailPoet\\Config\\Hooks'] = new \MailPoet\Config\Hooks(($this->services['MailPoet\\Subscription\\Form'] ?? $this->getFormService()), ($this->services['MailPoet\\Subscription\\Comment'] ?? $this->getCommentService()), ($this->services['MailPoet\\Subscription\\Manage'] ?? $this->getManageService()), ($this->services['MailPoet\\Subscription\\Registration'] ?? $this->getRegistrationService()), $a, $b, ($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] ?? $this->getPostNotificationSchedulerService()), new \MailPoet\Mailer\WordPress\WordpressMailerReplacer(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->privates['MailPoet\\Mailer\\MetaInfo'] ?? ($this->privates['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())), $a, ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService())), new \MailPoet\Form\DisplayFormInWPContent($b, ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Form\\Renderer'] ?? $this->getRenderer2Service()), ($this->services['MailPoet\\Form\\AssetsController'] ?? $this->getAssetsControllerService()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService())), ($this->services['MailPoet\\Config\\HooksWooCommerce'] ?? $this->getHooksWooCommerceService()), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()), ($this->services['MailPoet\\Segments\\WP'] ?? $this->getWPService()));
    }

    /**
     * Gets the public 'MailPoet\Config\HooksWooCommerce' shared autowired service.
     *
     * @return \MailPoet\Config\HooksWooCommerce
     */
    protected function getHooksWooCommerceService()
    {
        return $this->services['MailPoet\\Config\\HooksWooCommerce'] = new \MailPoet\Config\HooksWooCommerce(($this->services['MailPoet\\WooCommerce\\Subscription'] ?? $this->getSubscription2Service()), ($this->services['MailPoet\\Segments\\WooCommerce'] ?? $this->getWooCommerce2Service()), ($this->services['MailPoet\\WooCommerce\\Settings'] ?? $this->getSettings3Service()), ($this->privates['MailPoet\\Statistics\\Track\\WooCommercePurchases'] ?? $this->getWooCommercePurchasesService()), ($this->services['MailPoet\\Subscription\\Registration'] ?? $this->getRegistrationService()), ($this->privates['MailPoet\\Logging\\LoggerFactory'] ?? $this->getLoggerFactoryService()), ($this->services['MailPoet\\WooCommerce\\SubscriberEngagement'] ?? $this->getSubscriberEngagementService()));
    }

    /**
     * Gets the public 'MailPoet\Config\Initializer' shared autowired service.
     *
     * @return \MailPoet\Config\Initializer
     */
    protected function getInitializerService()
    {
        $a = ($this->services['MailPoet\\Config\\AccessControl'] ?? ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl()));
        $b = ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService());
        $c = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));
        $d = ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService());
        $e = ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService());
        $f = ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService());

        return $this->services['MailPoet\\Config\\Initializer'] = new \MailPoet\Config\Initializer(($this->services['MailPoet\\Config\\RendererFactory'] ?? ($this->services['MailPoet\\Config\\RendererFactory'] = new \MailPoet\Config\RendererFactory())), $a, ($this->services['MailPoet\\API\\JSON\\API'] ?? $this->getAPIService()), ($this->services['MailPoet\\Config\\Activator'] ?? $this->getActivatorService()), $b, new \MailPoet\Router\Router($a, ($this->services['MailPoet\\DI\\ContainerWrapper'] ?? $this->getContainerWrapperService())), ($this->services['MailPoet\\Config\\Hooks'] ?? $this->getHooksService()), ($this->services['MailPoet\\Config\\Changelog'] ?? $this->getChangelogService()), ($this->services['MailPoet\\Config\\Menu'] ?? $this->getMenuService()), ($this->services['MailPoet\\Cron\\CronTrigger'] ?? $this->getCronTriggerService()), new \MailPoet\Util\Notices\PermanentNotices($c, $d, $b), new \MailPoet\Config\Shortcodes(new \MailPoet\Subscription\Pages(($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), $c, $b, ($this->services['MailPoet\\Subscription\\CaptchaRenderer'] ?? $this->getCaptchaRendererService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()), ($this->services['MailPoet\\Form\\AssetsController'] ?? $this->getAssetsControllerService()), $e, ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()), ($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] ?? $this->getManageSubscriptionFormRendererService()), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()), $f, $d), $c, ($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()), $f, ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService())), new \MailPoet\Config\DatabaseInitializer($this), ($this->services['MailPoet\\WooCommerce\\TransactionalEmailHooks'] ?? $this->getTransactionalEmailHooksService()), new \MailPoet\PostEditorBlocks\PostEditorBlock($e, $c, new \MailPoet\PostEditorBlocks\SubscriptionFormBlock($c, ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()))), new \MailPoet\PostEditorBlocks\WooCommerceBlocksIntegration($c, $b, ($this->services['MailPoet\\WooCommerce\\Subscription'] ?? $this->getSubscription2Service()), ($this->services['MailPoet\\Segments\\WooCommerce'] ?? $this->getWooCommerce2Service()), $f), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->privates['MailPoet\\Config\\Localizer'] ?? ($this->privates['MailPoet\\Config\\Localizer'] = new \MailPoet\Config\Localizer())), ($this->services['MailPoet\\AutomaticEmails\\AutomaticEmails'] ?? $this->getAutomaticEmails2Service()), ($this->services['MailPoet\\Config\\AssetsLoader'] ?? $this->getAssetsLoaderService()));
    }

    /**
     * Gets the public 'MailPoet\Config\Menu' shared autowired service.
     *
     * @return \MailPoet\Config\Menu
     */
    protected function getMenuService()
    {
        return $this->services['MailPoet\\Config\\Menu'] = new \MailPoet\Config\Menu(($this->services['MailPoet\\Config\\AccessControl'] ?? ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\DI\\ContainerWrapper'] ?? $this->getContainerWrapperService()), ($this->services['MailPoet\\Config\\Router'] ?? $this->getRouterService()));
    }

    /**
     * Gets the public 'MailPoet\Config\Populator' shared autowired service.
     *
     * @return \MailPoet\Config\Populator
     */
    protected function getPopulatorService()
    {
        $a = ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService());
        $b = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));

        return $this->services['MailPoet\\Config\\Populator'] = new \MailPoet\Config\Populator($a, $b, ($this->services['MailPoet\\Subscription\\Captcha'] ?? $this->getCaptchaService()), new \MailPoet\Referrals\ReferralDetector($b, $a), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\WP'] ?? $this->getWPService()));
    }

    /**
     * Gets the public 'MailPoet\Config\Renderer' shared service.
     *
     * @return \MailPoet\Config\Renderer
     */
    protected function getRendererService()
    {
        return $this->services['MailPoet\\Config\\Renderer'] = ($this->services['MailPoet\\Config\\RendererFactory'] ?? ($this->services['MailPoet\\Config\\RendererFactory'] = new \MailPoet\Config\RendererFactory()))->getRenderer();
    }

    /**
     * Gets the public 'MailPoet\Config\RendererFactory' shared autowired service.
     *
     * @return \MailPoet\Config\RendererFactory
     */
    protected function getRendererFactoryService()
    {
        return $this->services['MailPoet\\Config\\RendererFactory'] = new \MailPoet\Config\RendererFactory();
    }

    /**
     * Gets the public 'MailPoet\Config\Router' shared autowired service.
     *
     * @return \MailPoet\Config\Router
     */
    protected function getRouterService()
    {
        return $this->services['MailPoet\\Config\\Router'] = new \MailPoet\Config\Router(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Config\ServicesChecker' shared autowired service.
     *
     * @return \MailPoet\Config\ServicesChecker
     */
    protected function getServicesCheckerService()
    {
        return $this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker();
    }

    /**
     * Gets the public 'MailPoet\Config\Shortcodes' autowired service.
     *
     * @return \MailPoet\Config\Shortcodes
     */
    protected function getShortcodesService()
    {
        $a = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));
        $b = ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService());

        return new \MailPoet\Config\Shortcodes(new \MailPoet\Subscription\Pages(($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), $a, ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscription\\CaptchaRenderer'] ?? $this->getCaptchaRendererService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()), ($this->services['MailPoet\\Form\\AssetsController'] ?? $this->getAssetsControllerService()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()), ($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] ?? $this->getManageSubscriptionFormRendererService()), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()), $b, ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService())), $a, ($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()), $b, ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\CronHelper' shared autowired service.
     *
     * @return \MailPoet\Cron\CronHelper
     */
    protected function getCronHelperService()
    {
        return $this->services['MailPoet\\Cron\\CronHelper'] = new \MailPoet\Cron\CronHelper(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\CronTrigger' shared autowired service.
     *
     * @return \MailPoet\Cron\CronTrigger
     */
    protected function getCronTriggerService()
    {
        return $this->services['MailPoet\\Cron\\CronTrigger'] = new \MailPoet\Cron\CronTrigger(($this->services['MailPoet\\Cron\\Triggers\\MailPoet'] ?? $this->getMailPoetService()), ($this->services['MailPoet\\Cron\\Triggers\\WordPress'] ?? $this->getWordPressService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\CronWorkerRunner' shared autowired service.
     *
     * @return \MailPoet\Cron\CronWorkerRunner
     */
    protected function getCronWorkerRunnerService()
    {
        return $this->services['MailPoet\\Cron\\CronWorkerRunner'] = new \MailPoet\Cron\CronWorkerRunner(($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\CronWorkerScheduler' shared autowired service.
     *
     * @return \MailPoet\Cron\CronWorkerScheduler
     */
    protected function getCronWorkerSchedulerService()
    {
        return $this->services['MailPoet\\Cron\\CronWorkerScheduler'] = new \MailPoet\Cron\CronWorkerScheduler(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Daemon' shared autowired service.
     *
     * @return \MailPoet\Cron\Daemon
     */
    protected function getDaemonService()
    {
        return $this->services['MailPoet\\Cron\\Daemon'] = new \MailPoet\Cron\Daemon(($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Cron\\CronWorkerRunner'] ?? $this->getCronWorkerRunnerService()), ($this->services['MailPoet\\Cron\\Workers\\WorkersFactory'] ?? $this->getWorkersFactoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\DaemonHttpRunner' shared autowired service.
     *
     * @return \MailPoet\Cron\DaemonHttpRunner
     */
    protected function getDaemonHttpRunnerService()
    {
        return $this->services['MailPoet\\Cron\\DaemonHttpRunner'] = new \MailPoet\Cron\DaemonHttpRunner(($this->services['MailPoet\\Cron\\Daemon'] ?? $this->getDaemonService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Cron\\Triggers\\WordPress'] ?? $this->getWordPressService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Supervisor' shared autowired service.
     *
     * @return \MailPoet\Cron\Supervisor
     */
    protected function getSupervisorService()
    {
        return $this->services['MailPoet\\Cron\\Supervisor'] = new \MailPoet\Cron\Supervisor(($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Triggers\MailPoet' shared autowired service.
     *
     * @return \MailPoet\Cron\Triggers\MailPoet
     */
    protected function getMailPoetService()
    {
        return $this->services['MailPoet\\Cron\\Triggers\\MailPoet'] = new \MailPoet\Cron\Triggers\MailPoet(($this->services['MailPoet\\Cron\\Supervisor'] ?? $this->getSupervisorService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Triggers\WordPress' shared autowired service.
     *
     * @return \MailPoet\Cron\Triggers\WordPress
     */
    protected function getWordPressService()
    {
        return $this->services['MailPoet\\Cron\\Triggers\\WordPress'] = new \MailPoet\Cron\Triggers\WordPress(($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Cron\\Triggers\\MailPoet'] ?? $this->getMailPoetService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck
     */
    protected function getAuthorizedSendingEmailsCheckService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\AuthorizedSendingEmailsCheck'] = new \MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck(($this->services['MailPoet\\Services\\AuthorizedEmailsController'] ?? $this->getAuthorizedEmailsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\Beamer' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\Beamer
     */
    protected function getBeamerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\Beamer'] = new \MailPoet\Cron\Workers\Beamer(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\Bounce' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\Bounce
     */
    protected function getBounceService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\Bounce'] = new \MailPoet\Cron\Workers\Bounce(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] ?? $this->getSendingQueuesRepositoryService()), ($this->services['MailPoet\\Statistics\\StatisticsBouncesRepository'] ?? $this->getStatisticsBouncesRepositoryService()), ($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\ExportFilesCleanup' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\ExportFilesCleanup
     */
    protected function getExportFilesCleanupService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\ExportFilesCleanup'] = new \MailPoet\Cron\Workers\ExportFilesCleanup(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\InactiveSubscribers' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\InactiveSubscribers
     */
    protected function getInactiveSubscribersService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\InactiveSubscribers'] = new \MailPoet\Cron\Workers\InactiveSubscribers(new \MailPoet\Subscribers\InactiveSubscribersController(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Settings\\SettingsRepository'] ?? $this->getSettingsRepositoryService())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck
     */
    protected function getPremiumKeyCheckService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck'] = new \MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck
     */
    protected function getSendingServiceKeyCheckService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck'] = new \MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\NewsletterTemplateThumbnails' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\NewsletterTemplateThumbnails
     */
    protected function getNewsletterTemplateThumbnailsService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\NewsletterTemplateThumbnails'] = new \MailPoet\Cron\Workers\NewsletterTemplateThumbnails(($this->services['MailPoet\\NewsletterTemplates\\ThumbnailSaver'] ?? $this->getThumbnailSaverService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\ReEngagementEmailsScheduler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\ReEngagementEmailsScheduler
     */
    protected function getReEngagementEmailsSchedulerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\ReEngagementEmailsScheduler'] = new \MailPoet\Cron\Workers\ReEngagementEmailsScheduler(($this->services['MailPoet\\Newsletter\\Scheduler\\ReEngagementScheduler'] ?? $this->getReEngagementSchedulerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\Scheduler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\Scheduler
     */
    protected function getSchedulerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\Scheduler'] = new \MailPoet\Cron\Workers\Scheduler(($this->services['MailPoet\\Segments\\SubscribersFinder'] ?? $this->getSubscribersFinderService()), ($this->privates['MailPoet\\Logging\\LoggerFactory'] ?? $this->getLoggerFactoryService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\Migration' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\Migration
     */
    protected function getMigrationService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\Migration'] = new \MailPoet\Cron\Workers\SendingQueue\Migration(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler
     */
    protected function getSendingErrorHandlerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler'] = new \MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler(($this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingThrottlingHandler'] ?? $this->getSendingThrottlingHandlerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\SendingQueue' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\SendingQueue
     */
    protected function getSendingQueue2Service()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingQueue'] = new \MailPoet\Cron\Workers\SendingQueue\SendingQueue(($this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler'] ?? $this->getSendingErrorHandlerService()), ($this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingThrottlingHandler'] ?? $this->getSendingThrottlingHandlerService()), new \MailPoet\Cron\Workers\StatsNotifications\Scheduler(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository'] ?? $this->getStatsNotificationsRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService())), ($this->privates['MailPoet\\Logging\\LoggerFactory'] ?? $this->getLoggerFactoryService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->services['MailPoet\\Segments\\SubscribersFinder'] ?? $this->getSubscribersFinderService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Cron\\Workers\\SendingQueue\\Tasks\\Links'] ?? $this->getLinksService()), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\SendingThrottlingHandler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\SendingThrottlingHandler
     */
    protected function getSendingThrottlingHandlerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingThrottlingHandler'] = new \MailPoet\Cron\Workers\SendingQueue\SendingThrottlingHandler(($this->privates['MailPoet\\Logging\\LoggerFactory'] ?? $this->getLoggerFactoryService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\Tasks\Links' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\Tasks\Links
     */
    protected function getLinksService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\Tasks\\Links'] = new \MailPoet\Cron\Workers\SendingQueue\Tasks\Links(($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Newsletter\\Links\\Links'] ?? $this->getLinks2Service()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] ?? $this->getNewsletterLinkRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails
     */
    protected function getAutomatedEmailsService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\AutomatedEmails'] = new \MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] ?? $this->getNewsletterStatisticsRepositoryService()), ($this->privates['MailPoet\\Mailer\\MetaInfo'] ?? ($this->privates['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository
     */
    protected function getNewsletterLinkRepositoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] = new \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository
     */
    protected function getStatsNotificationsRepositoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository'] = new \MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\Worker' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\Worker
     */
    protected function getWorkerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\Worker'] = new \MailPoet\Cron\Workers\StatsNotifications\Worker(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()), ($this->privates['MailPoet\\Mailer\\MetaInfo'] ?? ($this->privates['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository'] ?? $this->getStatsNotificationsRepositoryService()), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] ?? $this->getNewsletterLinkRepositoryService()), ($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] ?? $this->getNewsletterStatisticsRepositoryService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SubscriberLinkTokens' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SubscriberLinkTokens
     */
    protected function getSubscriberLinkTokensService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SubscriberLinkTokens'] = new \MailPoet\Cron\Workers\SubscriberLinkTokens(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SubscribersCountCacheRecalculation' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SubscribersCountCacheRecalculation
     */
    protected function getSubscribersCountCacheRecalculationService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SubscribersCountCacheRecalculation'] = new \MailPoet\Cron\Workers\SubscribersCountCacheRecalculation(($this->services['MailPoet\\Cache\\TransientCache'] ?? $this->getTransientCacheService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersCountsController'] ?? $this->getSubscribersCountsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SubscribersEngagementScore' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SubscribersEngagementScore
     */
    protected function getSubscribersEngagementScoreService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SubscribersEngagementScore'] = new \MailPoet\Cron\Workers\SubscribersEngagementScore(($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Statistics\\StatisticsOpensRepository'] ?? $this->getStatisticsOpensRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SubscribersLastEngagement' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SubscribersLastEngagement
     */
    protected function getSubscribersLastEngagementService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SubscribersLastEngagement'] = new \MailPoet\Cron\Workers\SubscribersLastEngagement(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->privates['MailPoet\\Util\\DBCollationChecker'] ?? $this->getDBCollationCheckerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SubscribersStatsReport' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SubscribersStatsReport
     */
    protected function getSubscribersStatsReportService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SubscribersStatsReport'] = new \MailPoet\Cron\Workers\SubscribersStatsReport(($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())), ($this->services['MailPoet\\Cron\\CronWorkerScheduler'] ?? $this->getCronWorkerSchedulerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\UnsubscribeTokens' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\UnsubscribeTokens
     */
    protected function getUnsubscribeTokensService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\UnsubscribeTokens'] = new \MailPoet\Cron\Workers\UnsubscribeTokens(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\WooCommercePastOrders' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\WooCommercePastOrders
     */
    protected function getWooCommercePastOrdersService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\WooCommercePastOrders'] = new \MailPoet\Cron\Workers\WooCommercePastOrders(($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Statistics\\StatisticsClicksRepository'] ?? $this->getStatisticsClicksRepositoryService()), ($this->privates['MailPoet\\Statistics\\Track\\WooCommercePurchases'] ?? $this->getWooCommercePurchasesService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\WooCommerceSync' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\WooCommerceSync
     */
    protected function getWooCommerceSyncService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\WooCommerceSync'] = new \MailPoet\Cron\Workers\WooCommerceSync(($this->services['MailPoet\\Segments\\WooCommerce'] ?? $this->getWooCommerce2Service()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection'] ?? $this->getConnectionService()));
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\WorkersFactory' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\WorkersFactory
     */
    protected function getWorkersFactoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\WorkersFactory'] = new \MailPoet\Cron\Workers\WorkersFactory(($this->services['MailPoet\\DI\\ContainerWrapper'] ?? $this->getContainerWrapperService()));
    }

    /**
     * Gets the public 'MailPoet\CustomFields\CustomFieldsRepository' shared autowired service.
     *
     * @return \MailPoet\CustomFields\CustomFieldsRepository
     */
    protected function getCustomFieldsRepositoryService()
    {
        return $this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] = new \MailPoet\CustomFields\CustomFieldsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\DI\ContainerWrapper' shared service.
     *
     * @return \MailPoet\DI\ContainerWrapper
     */
    protected function getContainerWrapperService()
    {
        return $this->services['MailPoet\\DI\\ContainerWrapper'] = \MailPoet\DI\ContainerWrapper::getInstance();
    }

    /**
     * Gets the public 'MailPoet\Doctrine\ConnectionFactory' shared autowired service.
     *
     * @return \MailPoet\Doctrine\ConnectionFactory
     */
    protected function getConnectionFactoryService()
    {
        return $this->services['MailPoet\\Doctrine\\ConnectionFactory'] = new \MailPoet\Doctrine\ConnectionFactory();
    }

    /**
     * Gets the public 'MailPoet\Doctrine\EntityManagerFactory' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EntityManagerFactory
     */
    protected function getEntityManagerFactoryService()
    {
        $a = new \MailPoet\Doctrine\Annotations\AnnotationReaderProvider();

        return $this->services['MailPoet\\Doctrine\\EntityManagerFactory'] = new \MailPoet\Doctrine\EntityManagerFactory(($this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection'] ?? $this->getConnectionService()), (new \MailPoet\Doctrine\ConfigurationFactory($a))->createConfiguration(), ($this->services['MailPoet\\Doctrine\\EventListeners\\TimestampListener'] ?? $this->getTimestampListenerService()), new \MailPoet\Doctrine\EventListeners\ValidationListener((new \MailPoet\Doctrine\Validator\ValidatorFactory($a))->createValidator()), ($this->services['MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener'] ?? $this->getEmojiEncodingListenerService()), ($this->services['MailPoet\\Doctrine\\EventListeners\\LastSubscribedAtListener'] ?? $this->getLastSubscribedAtListenerService()));
    }

    /**
     * Gets the public 'MailPoet\Doctrine\EventListeners\EmojiEncodingListener' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EventListeners\EmojiEncodingListener
     */
    protected function getEmojiEncodingListenerService()
    {
        return $this->services['MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener'] = new \MailPoet\Doctrine\EventListeners\EmojiEncodingListener(($this->services['MailPoet\\WP\\Emoji'] ?? $this->getEmojiService()));
    }

    /**
     * Gets the public 'MailPoet\Doctrine\EventListeners\LastSubscribedAtListener' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EventListeners\LastSubscribedAtListener
     */
    protected function getLastSubscribedAtListenerService()
    {
        return $this->services['MailPoet\\Doctrine\\EventListeners\\LastSubscribedAtListener'] = new \MailPoet\Doctrine\EventListeners\LastSubscribedAtListener(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Doctrine\EventListeners\TimestampListener' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EventListeners\TimestampListener
     */
    protected function getTimestampListenerService()
    {
        return $this->services['MailPoet\\Doctrine\\EventListeners\\TimestampListener'] = new \MailPoet\Doctrine\EventListeners\TimestampListener(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Features\FeatureFlagsController' shared autowired service.
     *
     * @return \MailPoet\Features\FeatureFlagsController
     */
    protected function getFeatureFlagsControllerService()
    {
        return $this->services['MailPoet\\Features\\FeatureFlagsController'] = new \MailPoet\Features\FeatureFlagsController(($this->services['MailPoet\\Features\\FeaturesController'] ?? $this->getFeaturesControllerService()), ($this->services['MailPoet\\Features\\FeatureFlagsRepository'] ?? $this->getFeatureFlagsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Features\FeatureFlagsRepository' shared autowired service.
     *
     * @return \MailPoet\Features\FeatureFlagsRepository
     */
    protected function getFeatureFlagsRepositoryService()
    {
        return $this->services['MailPoet\\Features\\FeatureFlagsRepository'] = new \MailPoet\Features\FeatureFlagsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Features\FeaturesController' shared autowired service.
     *
     * @return \MailPoet\Features\FeaturesController
     */
    protected function getFeaturesControllerService()
    {
        return $this->services['MailPoet\\Features\\FeaturesController'] = new \MailPoet\Features\FeaturesController(($this->services['MailPoet\\Features\\FeatureFlagsRepository'] ?? $this->getFeatureFlagsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Form\ApiDataSanitizer' shared autowired service.
     *
     * @return \MailPoet\Form\ApiDataSanitizer
     */
    protected function getApiDataSanitizerService()
    {
        return $this->services['MailPoet\\Form\\ApiDataSanitizer'] = new \MailPoet\Form\ApiDataSanitizer(($this->services['MailPoet\\Form\\FormHtmlSanitizer'] ?? $this->getFormHtmlSanitizerService()));
    }

    /**
     * Gets the public 'MailPoet\Form\AssetsController' shared autowired service.
     *
     * @return \MailPoet\Form\AssetsController
     */
    protected function getAssetsControllerService()
    {
        return $this->services['MailPoet\\Form\\AssetsController'] = new \MailPoet\Form\AssetsController(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Form\Block\Date' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Date
     */
    protected function getDateService()
    {
        return $this->services['MailPoet\\Form\\Block\\Date'] = new \MailPoet\Form\Block\Date(($this->privates['MailPoet\\Form\\Block\\BlockRendererHelper'] ?? $this->getBlockRendererHelperService()), ($this->privates['MailPoet\\Form\\BlockStylesRenderer'] ?? $this->getBlockStylesRendererService()), ($this->privates['MailPoet\\Form\\BlockWrapperRenderer'] ?? $this->getBlockWrapperRendererService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Form\FormHtmlSanitizer' shared autowired service.
     *
     * @return \MailPoet\Form\FormHtmlSanitizer
     */
    protected function getFormHtmlSanitizerService()
    {
        return $this->services['MailPoet\\Form\\FormHtmlSanitizer'] = new \MailPoet\Form\FormHtmlSanitizer(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Form\FormMessageController' shared autowired service.
     *
     * @return \MailPoet\Form\FormMessageController
     */
    protected function getFormMessageControllerService()
    {
        return $this->services['MailPoet\\Form\\FormMessageController'] = new \MailPoet\Form\FormMessageController(($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Form\FormSaveController' shared autowired service.
     *
     * @return \MailPoet\Form\FormSaveController
     */
    protected function getFormSaveControllerService()
    {
        return $this->services['MailPoet\\Form\\FormSaveController'] = new \MailPoet\Form\FormSaveController(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Form\FormsRepository' shared autowired service.
     *
     * @return \MailPoet\Form\FormsRepository
     */
    protected function getFormsRepositoryService()
    {
        return $this->services['MailPoet\\Form\\FormsRepository'] = new \MailPoet\Form\FormsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Form\Listing\FormListingRepository' shared autowired service.
     *
     * @return \MailPoet\Form\Listing\FormListingRepository
     */
    protected function getFormListingRepositoryService()
    {
        return $this->services['MailPoet\\Form\\Listing\\FormListingRepository'] = new \MailPoet\Form\Listing\FormListingRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Form\Renderer' shared autowired service.
     *
     * @return \MailPoet\Form\Renderer
     */
    protected function getRenderer2Service()
    {
        $a = ($this->privates['MailPoet\\Form\\Block\\BlockRendererHelper'] ?? $this->getBlockRendererHelperService());
        $b = ($this->privates['MailPoet\\Form\\BlockWrapperRenderer'] ?? $this->getBlockWrapperRendererService());
        $c = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));
        $d = ($this->privates['MailPoet\\Form\\BlockStylesRenderer'] ?? $this->getBlockStylesRendererService());

        return $this->services['MailPoet\\Form\\Renderer'] = new \MailPoet\Form\Renderer(new \MailPoet\Form\Util\Styles(), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Form\\Util\\CustomFonts'] ?? $this->getCustomFontsService()), new \MailPoet\Form\BlocksRenderer(new \MailPoet\Form\Block\Checkbox($a, $b, $c), new \MailPoet\Form\Block\Column($c), new \MailPoet\Form\Block\Columns($c), ($this->services['MailPoet\\Form\\Block\\Date'] ?? $this->getDateService()), new \MailPoet\Form\Block\Divider($c), new \MailPoet\Form\Block\Html($a), new \MailPoet\Form\Block\Image($c, ($this->services['MailPoet\\Form\\FormHtmlSanitizer'] ?? $this->getFormHtmlSanitizerService())), new \MailPoet\Form\Block\Heading($c), new \MailPoet\Form\Block\Paragraph($c), new \MailPoet\Form\Block\Radio($a, $b, $c), new \MailPoet\Form\Block\Segment($a, $b, $c, ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService())), new \MailPoet\Form\Block\Select($a, $b, $d, $c), new \MailPoet\Form\Block\Submit($a, $b, $d, $c), new \MailPoet\Form\Block\Text($a, $d, $b, $c), new \MailPoet\Form\Block\Textarea($a, $d, $b, $c)));
    }

    /**
     * Gets the public 'MailPoet\Form\Util\CustomFonts' shared autowired service.
     *
     * @return \MailPoet\Form\Util\CustomFonts
     */
    protected function getCustomFontsService()
    {
        return $this->services['MailPoet\\Form\\Util\\CustomFonts'] = new \MailPoet\Form\Util\CustomFonts(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Form\Util\FieldNameObfuscator' shared autowired service.
     *
     * @return \MailPoet\Form\Util\FieldNameObfuscator
     */
    protected function getFieldNameObfuscatorService()
    {
        return $this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] = new \MailPoet\Form\Util\FieldNameObfuscator(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Helpscout\Beacon' shared autowired service.
     *
     * @return \MailPoet\Helpscout\Beacon
     */
    protected function getBeaconService()
    {
        return $this->services['MailPoet\\Helpscout\\Beacon'] = new \MailPoet\Helpscout\Beacon(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()));
    }

    /**
     * Gets the public 'MailPoet\Listing\Handler' shared autowired service.
     *
     * @return \MailPoet\Listing\Handler
     */
    protected function getHandlerService()
    {
        return $this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler();
    }

    /**
     * Gets the public 'MailPoet\Listing\PageLimit' shared autowired service.
     *
     * @return \MailPoet\Listing\PageLimit
     */
    protected function getPageLimitService()
    {
        return $this->services['MailPoet\\Listing\\PageLimit'] = new \MailPoet\Listing\PageLimit(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Logging\LogRepository' shared autowired service.
     *
     * @return \MailPoet\Logging\LogRepository
     */
    protected function getLogRepositoryService()
    {
        return $this->services['MailPoet\\Logging\\LogRepository'] = new \MailPoet\Logging\LogRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\NewsletterTemplates\NewsletterTemplatesRepository' shared autowired service.
     *
     * @return \MailPoet\NewsletterTemplates\NewsletterTemplatesRepository
     */
    protected function getNewsletterTemplatesRepositoryService()
    {
        return $this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] = new \MailPoet\NewsletterTemplates\NewsletterTemplatesRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\NewsletterTemplates\ThumbnailSaver' shared autowired service.
     *
     * @return \MailPoet\NewsletterTemplates\ThumbnailSaver
     */
    protected function getThumbnailSaverService()
    {
        return $this->services['MailPoet\\NewsletterTemplates\\ThumbnailSaver'] = new \MailPoet\NewsletterTemplates\ThumbnailSaver(($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] ?? $this->getNewsletterTemplatesRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\ApiDataSanitizer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\ApiDataSanitizer
     */
    protected function getApiDataSanitizer2Service()
    {
        return $this->services['MailPoet\\Newsletter\\ApiDataSanitizer'] = new \MailPoet\Newsletter\ApiDataSanitizer(($this->services['MailPoet\\Newsletter\\NewsletterHtmlSanitizer'] ?? $this->getNewsletterHtmlSanitizerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\AutomatedLatestContent' shared autowired service.
     *
     * @return \MailPoet\Newsletter\AutomatedLatestContent
     */
    protected function getAutomatedLatestContent2Service()
    {
        return $this->services['MailPoet\\Newsletter\\AutomatedLatestContent'] = new \MailPoet\Newsletter\AutomatedLatestContent(($this->privates['MailPoet\\Logging\\LoggerFactory'] ?? $this->getLoggerFactoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\AutomaticEmailsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\AutomaticEmailsRepository
     */
    protected function getAutomaticEmailsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\AutomaticEmailsRepository'] = new \MailPoet\Newsletter\AutomaticEmailsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Links\Links' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Links\Links
     */
    protected function getLinks2Service()
    {
        return $this->services['MailPoet\\Newsletter\\Links\\Links'] = new \MailPoet\Newsletter\Links\Links(($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] ?? $this->getNewsletterLinkRepositoryService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] ?? $this->getSendingQueuesRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Listing\NewsletterListingRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Listing\NewsletterListingRepository
     */
    protected function getNewsletterListingRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Listing\\NewsletterListingRepository'] = new \MailPoet\Newsletter\Listing\NewsletterListingRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewsletterHtmlSanitizer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewsletterHtmlSanitizer
     */
    protected function getNewsletterHtmlSanitizerService()
    {
        return $this->services['MailPoet\\Newsletter\\NewsletterHtmlSanitizer'] = new \MailPoet\Newsletter\NewsletterHtmlSanitizer(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewsletterPostsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewsletterPostsRepository
     */
    protected function getNewsletterPostsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\NewsletterPostsRepository'] = new \MailPoet\Newsletter\NewsletterPostsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewsletterSaveController' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewsletterSaveController
     */
    protected function getNewsletterSaveControllerService()
    {
        return $this->services['MailPoet\\Newsletter\\NewsletterSaveController'] = new \MailPoet\Newsletter\NewsletterSaveController(($this->services['MailPoet\\Services\\AuthorizedEmailsController'] ?? $this->getAuthorizedEmailsControllerService()), ($this->services['MailPoet\\WP\\Emoji'] ?? $this->getEmojiService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] ?? $this->getNewsletterOptionsRepositoryService()), ($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository'] ?? $this->getNewsletterOptionFieldsRepositoryService()), ($this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] ?? $this->getNewsletterSegmentRepositoryService()), ($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] ?? $this->getNewsletterTemplatesRepositoryService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] ?? $this->getPostNotificationSchedulerService()), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->privates['MailPoet\\Util\\Security'] ?? $this->getSecurityService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Newsletter\\ApiDataSanitizer'] ?? $this->getApiDataSanitizer2Service()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewslettersRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewslettersRepository
     */
    protected function getNewslettersRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\NewslettersRepository'] = new \MailPoet\Newsletter\NewslettersRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository
     */
    protected function getNewsletterOptionFieldsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository'] = new \MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Options\NewsletterOptionsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Options\NewsletterOptionsRepository
     */
    protected function getNewsletterOptionsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] = new \MailPoet\Newsletter\Options\NewsletterOptionsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Preview\SendPreviewController' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Preview\SendPreviewController
     */
    protected function getSendPreviewControllerService()
    {
        return $this->services['MailPoet\\Newsletter\\Preview\\SendPreviewController'] = new \MailPoet\Newsletter\Preview\SendPreviewController(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->privates['MailPoet\\Mailer\\MetaInfo'] ?? ($this->privates['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())), ($this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] ?? $this->getRenderer5Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Shortcodes'] ?? $this->getShortcodes2Service()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent
     */
    protected function getAbandonedCartContentService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent'] = new \MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent(($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] ?? $this->getAutomatedLatestContentBlockService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock
     */
    protected function getAutomatedLatestContentBlockService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] = new \MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock(($this->services['MailPoet\\Newsletter\\NewsletterPostsRepository'] ?? $this->getNewsletterPostsRepositoryService()), ($this->services['MailPoet\\Newsletter\\AutomatedLatestContent'] ?? $this->getAutomatedLatestContent2Service()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Blocks\Renderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Renderer
     */
    protected function getRenderer3Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer'] = new \MailPoet\Newsletter\Renderer\Blocks\Renderer(($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] ?? $this->getAutomatedLatestContentBlockService()), new \MailPoet\Newsletter\Renderer\Blocks\Button(), new \MailPoet\Newsletter\Renderer\Blocks\Divider(), new \MailPoet\Newsletter\Renderer\Blocks\Footer(), new \MailPoet\Newsletter\Renderer\Blocks\Header(), new \MailPoet\Newsletter\Renderer\Blocks\Image(), new \MailPoet\Newsletter\Renderer\Blocks\Social(), new \MailPoet\Newsletter\Renderer\Blocks\Spacer(), new \MailPoet\Newsletter\Renderer\Blocks\Text(), new \MailPoet\Newsletter\Renderer\Blocks\Placeholder(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()))));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Columns\Renderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Columns\Renderer
     */
    protected function getRenderer4Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer'] = new \MailPoet\Newsletter\Renderer\Columns\Renderer();
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Preprocessor' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Preprocessor
     */
    protected function getPreprocessorService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Preprocessor'] = new \MailPoet\Newsletter\Renderer\Preprocessor(($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent'] ?? $this->getAbandonedCartContentService()), ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] ?? $this->getAutomatedLatestContentBlockService()), ($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\ContentPreprocessor'] ?? $this->getContentPreprocessorService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Renderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Renderer
     */
    protected function getRenderer5Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] = new \MailPoet\Newsletter\Renderer\Renderer(($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer'] ?? $this->getRenderer3Service()), ($this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer'] ?? ($this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer'] = new \MailPoet\Newsletter\Renderer\Columns\Renderer())), ($this->services['MailPoet\\Newsletter\\Renderer\\Preprocessor'] ?? $this->getPreprocessorService()), ($this->services['MailPoetVendor\\CSS'] ?? ($this->services['MailPoetVendor\\CSS'] = new \MailPoetVendor\CSS())), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Config\\ServicesChecker'] ?? ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Scheduler\AutomaticEmailScheduler' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Scheduler\AutomaticEmailScheduler
     */
    protected function getAutomaticEmailSchedulerService()
    {
        return $this->services['MailPoet\\Newsletter\\Scheduler\\AutomaticEmailScheduler'] = new \MailPoet\Newsletter\Scheduler\AutomaticEmailScheduler(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Scheduler\PostNotificationScheduler' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Scheduler\PostNotificationScheduler
     */
    protected function getPostNotificationSchedulerService()
    {
        return $this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] = new \MailPoet\Newsletter\Scheduler\PostNotificationScheduler(($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] ?? $this->getNewsletterOptionsRepositoryService()), ($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository'] ?? $this->getNewsletterOptionFieldsRepositoryService()), ($this->services['MailPoet\\Newsletter\\NewsletterPostsRepository'] ?? $this->getNewsletterPostsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Scheduler\ReEngagementScheduler' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Scheduler\ReEngagementScheduler
     */
    protected function getReEngagementSchedulerService()
    {
        return $this->services['MailPoet\\Newsletter\\Scheduler\\ReEngagementScheduler'] = new \MailPoet\Newsletter\Scheduler\ReEngagementScheduler(($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Scheduler\WelcomeScheduler' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Scheduler\WelcomeScheduler
     */
    protected function getWelcomeSchedulerService()
    {
        return $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler(($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] ?? $this->getScheduledTasksRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Segment\NewsletterSegmentRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Segment\NewsletterSegmentRepository
     */
    protected function getNewsletterSegmentRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] = new \MailPoet\Newsletter\Segment\NewsletterSegmentRepository(($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] ?? $this->getNewsletterOptionsRepositoryService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository
     */
    protected function getScheduledTaskSubscribersRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Sending\\ScheduledTaskSubscribersRepository'] = new \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Sending\ScheduledTasksRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Sending\ScheduledTasksRepository
     */
    protected function getScheduledTasksRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] = new \MailPoet\Newsletter\Sending\ScheduledTasksRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Sending\SendingQueuesRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Sending\SendingQueuesRepository
     */
    protected function getSendingQueuesRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] = new \MailPoet\Newsletter\Sending\SendingQueuesRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTaskSubscribersRepository'] ?? $this->getScheduledTaskSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Shortcodes\Categories\Date' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Shortcodes\Categories\Date
     */
    protected function getDate2Service()
    {
        return $this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Date'] = new \MailPoet\Newsletter\Shortcodes\Categories\Date();
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Shortcodes\Categories\Link' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Shortcodes\Categories\Link
     */
    protected function getLinkService()
    {
        return $this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Link'] = new \MailPoet\Newsletter\Shortcodes\Categories\Link(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Shortcodes\Categories\Newsletter' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Shortcodes\Categories\Newsletter
     */
    protected function getNewsletterService()
    {
        return $this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Newsletter'] = new \MailPoet\Newsletter\Shortcodes\Categories\Newsletter(($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Shortcodes\Categories\Subscriber' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Shortcodes\Categories\Subscriber
     */
    protected function getSubscriberService()
    {
        return $this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Subscriber'] = new \MailPoet\Newsletter\Shortcodes\Categories\Subscriber(($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository'] ?? $this->getSubscriberCustomFieldRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Shortcodes\Shortcodes' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Shortcodes\Shortcodes
     */
    protected function getShortcodes2Service()
    {
        return $this->services['MailPoet\\Newsletter\\Shortcodes\\Shortcodes'] = new \MailPoet\Newsletter\Shortcodes\Shortcodes(($this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Date'] ?? ($this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Date'] = new \MailPoet\Newsletter\Shortcodes\Categories\Date())), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Link'] ?? $this->getLinkService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Newsletter'] ?? $this->getNewsletterService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Subscriber'] ?? $this->getSubscriberService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Shortcodes\ShortcodesHelper' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Shortcodes\ShortcodesHelper
     */
    protected function getShortcodesHelperService()
    {
        return $this->services['MailPoet\\Newsletter\\Shortcodes\\ShortcodesHelper'] = new \MailPoet\Newsletter\Shortcodes\ShortcodesHelper(($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository
     */
    protected function getNewsletterStatisticsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] = new \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Url' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Url
     */
    protected function getUrlService()
    {
        return $this->services['MailPoet\\Newsletter\\Url'] = new \MailPoet\Newsletter\Url(($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController' shared autowired service.
     *
     * @return \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController
     */
    protected function getViewInBrowserControllerService()
    {
        return $this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController'] = new \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController(($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer'] ?? $this->getViewInBrowserRendererService()), ($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] ?? $this->getSendingQueuesRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Newsletter\ViewInBrowser\ViewInBrowserRenderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserRenderer
     */
    protected function getViewInBrowserRendererService()
    {
        return $this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer'] = new \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserRenderer(($this->services['MailPoet\\WP\\Emoji'] ?? $this->getEmojiService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Shortcodes'] ?? $this->getShortcodes2Service()), ($this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] ?? $this->getRenderer5Service()), ($this->services['MailPoet\\Newsletter\\Links\\Links'] ?? $this->getLinks2Service()));
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\CronDaemon' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\CronDaemon
     */
    protected function getCronDaemonService()
    {
        return $this->services['MailPoet\\Router\\Endpoints\\CronDaemon'] = new \MailPoet\Router\Endpoints\CronDaemon(($this->services['MailPoet\\Cron\\DaemonHttpRunner'] ?? $this->getDaemonHttpRunnerService()), ($this->services['MailPoet\\Cron\\CronHelper'] ?? $this->getCronHelperService()));
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\FormPreview' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\FormPreview
     */
    protected function getFormPreviewService()
    {
        $a = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));

        return $this->services['MailPoet\\Router\\Endpoints\\FormPreview'] = new \MailPoet\Router\Endpoints\FormPreview($a, new \MailPoet\Form\PreviewPage($a, ($this->services['MailPoet\\Form\\Renderer'] ?? $this->getRenderer2Service()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Form\\AssetsController'] ?? $this->getAssetsControllerService())));
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\Subscription' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\Subscription
     */
    protected function getSubscriptionService()
    {
        $a = ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions()));

        return $this->services['MailPoet\\Router\\Endpoints\\Subscription'] = new \MailPoet\Router\Endpoints\Subscription(new \MailPoet\Subscription\Pages(($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), $a, ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscription\\CaptchaRenderer'] ?? $this->getCaptchaRendererService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()), ($this->services['MailPoet\\Form\\AssetsController'] ?? $this->getAssetsControllerService()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()), ($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] ?? $this->getManageSubscriptionFormRendererService()), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService())), $a, ($this->services['MailPoet\\Subscription\\Captcha'] ?? $this->getCaptchaService()));
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\Track' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\Track
     */
    protected function getTrackService()
    {
        $a = ($this->services['MailPoet\\Statistics\\Track\\Opens'] ?? $this->getOpensService());
        $b = ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService());

        return $this->services['MailPoet\\Router\\Endpoints\\Track'] = new \MailPoet\Router\Endpoints\Track(new \MailPoet\Statistics\Track\Clicks(($this->privates['MailPoet\\Util\\Cookies'] ?? ($this->privates['MailPoet\\Util\\Cookies'] = new \MailPoet\Util\Cookies())), ($this->services['MailPoet\\Statistics\\Track\\SubscriberCookie'] ?? $this->getSubscriberCookieService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Shortcodes'] ?? $this->getShortcodes2Service()), $a, ($this->services['MailPoet\\Statistics\\StatisticsClicksRepository'] ?? $this->getStatisticsClicksRepositoryService()), ($this->services['MailPoet\\Statistics\\UserAgentsRepository'] ?? $this->getUserAgentsRepositoryService()), ($this->services['MailPoet\\Newsletter\\Shortcodes\\Categories\\Link'] ?? $this->getLinkService()), $b, ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService())), $a, ($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] ?? $this->getSendingQueuesRepositoryService()), $b, ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] ?? $this->getNewsletterLinkRepositoryService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Newsletter\\Links\\Links'] ?? $this->getLinks2Service()));
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\ViewInBrowser' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\ViewInBrowser
     */
    protected function getViewInBrowserService()
    {
        return $this->services['MailPoet\\Router\\Endpoints\\ViewInBrowser'] = new \MailPoet\Router\Endpoints\ViewInBrowser(($this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController'] ?? $this->getViewInBrowserControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\DynamicSegmentFilterRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\DynamicSegmentFilterRepository
     */
    protected function getDynamicSegmentFilterRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\DynamicSegmentFilterRepository'] = new \MailPoet\Segments\DynamicSegments\DynamicSegmentFilterRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\DynamicSegmentsListingRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\DynamicSegmentsListingRepository
     */
    protected function getDynamicSegmentsListingRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\DynamicSegmentsListingRepository'] = new \MailPoet\Segments\DynamicSegments\DynamicSegmentsListingRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\WooCommerce'] ?? $this->getWooCommerce2Service()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\FilterDataMapper' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\FilterDataMapper
     */
    protected function getFilterDataMapperService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\FilterDataMapper'] = new \MailPoet\Segments\DynamicSegments\FilterDataMapper(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\FilterFactory' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\FilterFactory
     */
    protected function getFilterFactoryService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\FilterFactory'] = new \MailPoet\Segments\DynamicSegments\FilterFactory(($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction'] ?? $this->getEmailActionService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailActionClickAny'] ?? $this->getEmailActionClickAnyService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole'] ?? $this->getUserRoleService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\MailPoetCustomFields'] ?? $this->getMailPoetCustomFieldsService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct'] ?? $this->getWooCommerceProductService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory'] ?? $this->getWooCommerceCategoryService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCountry'] ?? $this->getWooCommerceCountryService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailOpensAbsoluteCountAction'] ?? $this->getEmailOpensAbsoluteCountActionService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceNumberOfOrders'] ?? $this->getWooCommerceNumberOfOrdersService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceTotalSpent'] ?? $this->getWooCommerceTotalSpentService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceSubscription'] ?? $this->getWooCommerceSubscriptionService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSubscribedDate'] ?? ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSubscribedDate'] = new \MailPoet\Segments\DynamicSegments\Filters\SubscriberSubscribedDate())), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberScore'] ?? ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberScore'] = new \MailPoet\Segments\DynamicSegments\Filters\SubscriberScore())), ($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSegment'] ?? $this->getSubscriberSegmentService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\FilterHandler' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\FilterHandler
     */
    protected function getFilterHandlerService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] = new \MailPoet\Segments\DynamicSegments\FilterHandler(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\SegmentDependencyValidator'] ?? $this->getSegmentDependencyValidatorService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\FilterFactory'] ?? $this->getFilterFactoryService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\EmailAction' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\EmailAction
     */
    protected function getEmailActionService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction'] = new \MailPoet\Segments\DynamicSegments\Filters\EmailAction(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\EmailActionClickAny' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\EmailActionClickAny
     */
    protected function getEmailActionClickAnyService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailActionClickAny'] = new \MailPoet\Segments\DynamicSegments\Filters\EmailActionClickAny(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\EmailOpensAbsoluteCountAction' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\EmailOpensAbsoluteCountAction
     */
    protected function getEmailOpensAbsoluteCountActionService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailOpensAbsoluteCountAction'] = new \MailPoet\Segments\DynamicSegments\Filters\EmailOpensAbsoluteCountAction(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\MailPoetCustomFields' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\MailPoetCustomFields
     */
    protected function getMailPoetCustomFieldsService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\MailPoetCustomFields'] = new \MailPoet\Segments\DynamicSegments\Filters\MailPoetCustomFields(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\SubscriberScore' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\SubscriberScore
     */
    protected function getSubscriberScoreService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberScore'] = new \MailPoet\Segments\DynamicSegments\Filters\SubscriberScore();
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\SubscriberSegment' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\SubscriberSegment
     */
    protected function getSubscriberSegmentService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSegment'] = new \MailPoet\Segments\DynamicSegments\Filters\SubscriberSegment(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\SubscriberSubscribedDate' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\SubscriberSubscribedDate
     */
    protected function getSubscriberSubscribedDateService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\SubscriberSubscribedDate'] = new \MailPoet\Segments\DynamicSegments\Filters\SubscriberSubscribedDate();
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\UserRole' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\UserRole
     */
    protected function getUserRoleService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole'] = new \MailPoet\Segments\DynamicSegments\Filters\UserRole(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory
     */
    protected function getWooCommerceCategoryService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->privates['MailPoet\\Util\\DBCollationChecker'] ?? $this->getDBCollationCheckerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceCountry' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceCountry
     */
    protected function getWooCommerceCountryService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCountry'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceCountry(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->privates['MailPoet\\Util\\DBCollationChecker'] ?? $this->getDBCollationCheckerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceNumberOfOrders' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceNumberOfOrders
     */
    protected function getWooCommerceNumberOfOrdersService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceNumberOfOrders'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceNumberOfOrders(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->privates['MailPoet\\Util\\DBCollationChecker'] ?? $this->getDBCollationCheckerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct
     */
    protected function getWooCommerceProductService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->privates['MailPoet\\Util\\DBCollationChecker'] ?? $this->getDBCollationCheckerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceSubscription' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceSubscription
     */
    protected function getWooCommerceSubscriptionService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceSubscription'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceSubscription(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceTotalSpent' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceTotalSpent
     */
    protected function getWooCommerceTotalSpentService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceTotalSpent'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceTotalSpent(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->privates['MailPoet\\Util\\DBCollationChecker'] ?? $this->getDBCollationCheckerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\SegmentSaveController' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\SegmentSaveController
     */
    protected function getSegmentSaveControllerService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\SegmentSaveController'] = new \MailPoet\Segments\DynamicSegments\SegmentSaveController(($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\FilterDataMapper'] ?? $this->getFilterDataMapperService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentDependencyValidator' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentDependencyValidator
     */
    protected function getSegmentDependencyValidatorService()
    {
        return $this->services['MailPoet\\Segments\\SegmentDependencyValidator'] = new \MailPoet\Segments\SegmentDependencyValidator(($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentListingRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentListingRepository
     */
    protected function getSegmentListingRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\SegmentListingRepository'] = new \MailPoet\Segments\SegmentListingRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\WooCommerce'] ?? $this->getWooCommerce2Service()));
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentSaveController' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentSaveController
     */
    protected function getSegmentSaveController2Service()
    {
        return $this->services['MailPoet\\Segments\\SegmentSaveController'] = new \MailPoet\Segments\SegmentSaveController(($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentSubscribersRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentSubscribersRepository
     */
    protected function getSegmentSubscribersRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] = new \MailPoet\Segments\SegmentSubscribersRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] ?? $this->getFilterHandlerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentsRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentsRepository
     */
    protected function getSegmentsRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\SegmentsRepository'] = new \MailPoet\Segments\SegmentsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] ?? $this->getNewsletterSegmentRepositoryService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentsSimpleListRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentsSimpleListRepository
     */
    protected function getSegmentsSimpleListRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\SegmentsSimpleListRepository'] = new \MailPoet\Segments\SegmentsSimpleListRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Subscribers\\SubscribersCountsController'] ?? $this->getSubscribersCountsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\SubscribersFinder' shared autowired service.
     *
     * @return \MailPoet\Segments\SubscribersFinder
     */
    protected function getSubscribersFinderService()
    {
        return $this->services['MailPoet\\Segments\\SubscribersFinder'] = new \MailPoet\Segments\SubscribersFinder(($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\WP' shared autowired service.
     *
     * @return \MailPoet\Segments\WP
     */
    protected function getWPService()
    {
        return $this->services['MailPoet\\Segments\\WP'] = new \MailPoet\Segments\WP(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Segments\WooCommerce' shared autowired service.
     *
     * @return \MailPoet\Segments\WooCommerce
     */
    protected function getWooCommerce2Service()
    {
        return $this->services['MailPoet\\Segments\\WooCommerce'] = new \MailPoet\Segments\WooCommerce(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] ?? $this->getSubscriberSegmentRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSaveController'] ?? $this->getSubscriberSaveControllerService()), ($this->services['MailPoet\\Segments\\WP'] ?? $this->getWPService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection'] ?? $this->getConnectionService()));
    }

    /**
     * Gets the public 'MailPoet\Services\AuthorizedEmailsController' shared autowired service.
     *
     * @return \MailPoet\Services\AuthorizedEmailsController
     */
    protected function getAuthorizedEmailsControllerService()
    {
        return $this->services['MailPoet\\Services\\AuthorizedEmailsController'] = new \MailPoet\Services\AuthorizedEmailsController(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Services\\Bridge'] ?? $this->getBridgeService()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Services\Bridge' shared autowired service.
     *
     * @return \MailPoet\Services\Bridge
     */
    protected function getBridgeService()
    {
        return $this->services['MailPoet\\Services\\Bridge'] = new \MailPoet\Services\Bridge(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Util\\License\\Features\\Subscribers'] ?? $this->getSubscribers4Service()));
    }

    /**
     * Gets the public 'MailPoet\Services\CongratulatoryMssEmailController' shared autowired service.
     *
     * @return \MailPoet\Services\CongratulatoryMssEmailController
     */
    protected function getCongratulatoryMssEmailControllerService()
    {
        return $this->services['MailPoet\\Services\\CongratulatoryMssEmailController'] = new \MailPoet\Services\CongratulatoryMssEmailController(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->privates['MailPoet\\Mailer\\MetaInfo'] ?? ($this->privates['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()));
    }

    /**
     * Gets the public 'MailPoet\Settings\SettingsController' shared autowired service.
     *
     * @return \MailPoet\Settings\SettingsController
     */
    protected function getSettingsControllerService()
    {
        return $this->services['MailPoet\\Settings\\SettingsController'] = new \MailPoet\Settings\SettingsController(($this->services['MailPoet\\Settings\\SettingsRepository'] ?? $this->getSettingsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Settings\SettingsRepository' shared autowired service.
     *
     * @return \MailPoet\Settings\SettingsRepository
     */
    protected function getSettingsRepositoryService()
    {
        return $this->services['MailPoet\\Settings\\SettingsRepository'] = new \MailPoet\Settings\SettingsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Settings\TrackingConfig' shared autowired service.
     *
     * @return \MailPoet\Settings\TrackingConfig
     */
    protected function getTrackingConfigService()
    {
        return $this->services['MailPoet\\Settings\\TrackingConfig'] = new \MailPoet\Settings\TrackingConfig(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Settings\UserFlagsRepository' shared autowired service.
     *
     * @return \MailPoet\Settings\UserFlagsRepository
     */
    protected function getUserFlagsRepositoryService()
    {
        return $this->services['MailPoet\\Settings\\UserFlagsRepository'] = new \MailPoet\Settings\UserFlagsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\GATracking' shared autowired service.
     *
     * @return \MailPoet\Statistics\GATracking
     */
    protected function getGATrackingService()
    {
        return $this->services['MailPoet\\Statistics\\GATracking'] = new \MailPoet\Statistics\GATracking(($this->services['MailPoet\\Newsletter\\Links\\Links'] ?? $this->getLinks2Service()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\StatisticsBouncesRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsBouncesRepository
     */
    protected function getStatisticsBouncesRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsBouncesRepository'] = new \MailPoet\Statistics\StatisticsBouncesRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\StatisticsClicksRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsClicksRepository
     */
    protected function getStatisticsClicksRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsClicksRepository'] = new \MailPoet\Statistics\StatisticsClicksRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\StatisticsFormsRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsFormsRepository
     */
    protected function getStatisticsFormsRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsFormsRepository'] = new \MailPoet\Statistics\StatisticsFormsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\StatisticsOpensRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsOpensRepository
     */
    protected function getStatisticsOpensRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsOpensRepository'] = new \MailPoet\Statistics\StatisticsOpensRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\StatisticsWooCommercePurchasesRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository
     */
    protected function getStatisticsWooCommercePurchasesRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] = new \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\Track\Opens' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\Opens
     */
    protected function getOpensService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\Opens'] = new \MailPoet\Statistics\Track\Opens(($this->services['MailPoet\\Statistics\\StatisticsOpensRepository'] ?? $this->getStatisticsOpensRepositoryService()), ($this->services['MailPoet\\Statistics\\UserAgentsRepository'] ?? $this->getUserAgentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\Track\SubscriberCookie' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\SubscriberCookie
     */
    protected function getSubscriberCookieService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\SubscriberCookie'] = new \MailPoet\Statistics\Track\SubscriberCookie(($this->privates['MailPoet\\Util\\Cookies'] ?? ($this->privates['MailPoet\\Util\\Cookies'] = new \MailPoet\Util\Cookies())), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\Track\SubscriberHandler' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\SubscriberHandler
     */
    protected function getSubscriberHandlerService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] = new \MailPoet\Statistics\Track\SubscriberHandler(($this->services['MailPoet\\Statistics\\Track\\SubscriberCookie'] ?? $this->getSubscriberCookieService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Statistics\Track\Unsubscribes' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\Unsubscribes
     */
    protected function getUnsubscribesService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] = new \MailPoet\Statistics\Track\Unsubscribes(($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] ?? $this->getSendingQueuesRepositoryService()), ($this->privates['MailPoet\\Statistics\\StatisticsUnsubscribesRepository'] ?? $this->getStatisticsUnsubscribesRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Statistics\UserAgentsRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\UserAgentsRepository
     */
    protected function getUserAgentsRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\UserAgentsRepository'] = new \MailPoet\Statistics\UserAgentsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\ConfirmationEmailMailer' shared autowired service.
     *
     * @return \MailPoet\Subscribers\ConfirmationEmailMailer
     */
    protected function getConfirmationEmailMailerService()
    {
        return $this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] = new \MailPoet\Subscribers\ConfirmationEmailMailer(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\ImportExport\ImportExportRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\ImportExport\ImportExportRepository
     */
    protected function getImportExportRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\ImportExport\\ImportExportRepository'] = new \MailPoet\Subscribers\ImportExport\ImportExportRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] ?? $this->getFilterHandlerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterClicksExporter' shared autowired service.
     *
     * @return \MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterClicksExporter
     */
    protected function getNewsletterClicksExporterService()
    {
        return $this->services['MailPoet\\Subscribers\\ImportExport\\PersonalDataExporters\\NewsletterClicksExporter'] = new \MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterClicksExporter(($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterOpensExporter' shared autowired service.
     *
     * @return \MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterOpensExporter
     */
    protected function getNewsletterOpensExporterService()
    {
        return $this->services['MailPoet\\Subscribers\\ImportExport\\PersonalDataExporters\\NewsletterOpensExporter'] = new \MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterOpensExporter(($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewslettersExporter' shared autowired service.
     *
     * @return \MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewslettersExporter
     */
    protected function getNewslettersExporterService()
    {
        return $this->services['MailPoet\\Subscribers\\ImportExport\\PersonalDataExporters\\NewslettersExporter'] = new \MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewslettersExporter(($this->services['MailPoet\\Newsletter\\Url'] ?? $this->getUrlService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\LinkTokens' shared autowired service.
     *
     * @return \MailPoet\Subscribers\LinkTokens
     */
    protected function getLinkTokensService()
    {
        return $this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens(($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\NewSubscriberNotificationMailer' shared autowired service.
     *
     * @return \MailPoet\Subscribers\NewSubscriberNotificationMailer
     */
    protected function getNewSubscriberNotificationMailerService()
    {
        return $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] = new \MailPoet\Subscribers\NewSubscriberNotificationMailer(($this->privates['MailPoet\\Mailer\\Mailer'] ?? $this->getMailer2Service()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\RequiredCustomFieldValidator' shared autowired service.
     *
     * @return \MailPoet\Subscribers\RequiredCustomFieldValidator
     */
    protected function getRequiredCustomFieldValidatorService()
    {
        return $this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] = new \MailPoet\Subscribers\RequiredCustomFieldValidator(($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberActions' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberActions
     */
    protected function getSubscriberActionsService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberActions'] = new \MailPoet\Subscribers\SubscriberActions(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), ($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] ?? $this->getConfirmationEmailMailerService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSaveController'] ?? $this->getSubscriberSaveControllerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] ?? $this->getSubscriberSegmentRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberCustomFieldRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberCustomFieldRepository
     */
    protected function getSubscriberCustomFieldRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository'] = new \MailPoet\Subscribers\SubscriberCustomFieldRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberIPsRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberIPsRepository
     */
    protected function getSubscriberIPsRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberIPsRepository'] = new \MailPoet\Subscribers\SubscriberIPsRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberListingRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberListingRepository
     */
    protected function getSubscriberListingRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberListingRepository'] = new \MailPoet\Subscribers\SubscriberListingRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] ?? $this->getFilterHandlerService()), ($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersCountsController'] ?? $this->getSubscribersCountsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberSaveController' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberSaveController
     */
    protected function getSubscriberSaveControllerService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberSaveController'] = new \MailPoet\Subscribers\SubscriberSaveController(($this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] ?? $this->getCustomFieldsRepositoryService()), ($this->privates['MailPoet\\Util\\Security'] ?? $this->getSecurityService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository'] ?? $this->getSubscriberCustomFieldRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] ?? $this->getSubscriberSegmentRepositoryService()), ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberSegmentRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberSegmentRepository
     */
    protected function getSubscriberSegmentRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] = new \MailPoet\Subscribers\SubscriberSegmentRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberSubscribeController' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberSubscribeController
     */
    protected function getSubscriberSubscribeControllerService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberSubscribeController'] = new \MailPoet\Subscribers\SubscriberSubscribeController(($this->services['MailPoet\\Subscription\\Captcha'] ?? $this->getCaptchaService()), ($this->privates['MailPoet\\Subscription\\CaptchaSession'] ?? $this->getCaptchaSessionService()), ($this->services['MailPoet\\Subscribers\\SubscriberActions'] ?? $this->getSubscriberActionsService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()), ($this->services['MailPoet\\Subscription\\Throttling'] ?? $this->getThrottlingService()), ($this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] ?? $this->getFieldNameObfuscatorService()), ($this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] ?? $this->getRequiredCustomFieldValidatorService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Statistics\\StatisticsFormsRepository'] ?? $this->getStatisticsFormsRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscribersCountsController' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscribersCountsController
     */
    protected function getSubscribersCountsControllerService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscribersCountsController'] = new \MailPoet\Subscribers\SubscribersCountsController(($this->services['MailPoet\\Segments\\SegmentsRepository'] ?? $this->getSegmentsRepositoryService()), ($this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] ?? $this->getSegmentSubscribersRepositoryService()), ($this->services['MailPoet\\Cache\\TransientCache'] ?? $this->getTransientCacheService()));
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscribersRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscribersRepository
     */
    protected function getSubscribersRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscribersRepository'] = new \MailPoet\Subscribers\SubscribersRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Captcha' shared autowired service.
     *
     * @return \MailPoet\Subscription\Captcha
     */
    protected function getCaptchaService()
    {
        return $this->services['MailPoet\\Subscription\\Captcha'] = new \MailPoet\Subscription\Captcha(($this->services['MailPoet\\Subscribers\\SubscriberIPsRepository'] ?? $this->getSubscriberIPsRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->privates['MailPoet\\Subscription\\CaptchaSession'] ?? $this->getCaptchaSessionService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\CaptchaRenderer' shared autowired service.
     *
     * @return \MailPoet\Subscription\CaptchaRenderer
     */
    protected function getCaptchaRendererService()
    {
        return $this->services['MailPoet\\Subscription\\CaptchaRenderer'] = new \MailPoet\Subscription\CaptchaRenderer(($this->services['MailPoet\\Util\\Url'] ?? $this->getUrl2Service()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->privates['MailPoet\\Subscription\\CaptchaSession'] ?? $this->getCaptchaSessionService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Form\\Renderer'] ?? $this->getRenderer2Service()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Comment' shared autowired service.
     *
     * @return \MailPoet\Subscription\Comment
     */
    protected function getCommentService()
    {
        return $this->services['MailPoet\\Subscription\\Comment'] = new \MailPoet\Subscription\Comment(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\SubscriberActions'] ?? $this->getSubscriberActionsService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Form' shared autowired service.
     *
     * @return \MailPoet\Subscription\Form
     */
    protected function getFormService()
    {
        return $this->services['MailPoet\\Subscription\\Form'] = new \MailPoet\Subscription\Form(($this->services['MailPoet\\API\\JSON\\API'] ?? $this->getAPIService()), ($this->services['MailPoet\\Util\\Url'] ?? $this->getUrl2Service()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Manage' shared autowired service.
     *
     * @return \MailPoet\Subscription\Manage
     */
    protected function getManageService()
    {
        return $this->services['MailPoet\\Subscription\\Manage'] = new \MailPoet\Subscription\Manage(($this->services['MailPoet\\Util\\Url'] ?? $this->getUrl2Service()), ($this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] ?? $this->getFieldNameObfuscatorService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] ?? $this->getSubscriberSegmentRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\ManageSubscriptionFormRenderer' shared autowired service.
     *
     * @return \MailPoet\Subscription\ManageSubscriptionFormRenderer
     */
    protected function getManageSubscriptionFormRendererService()
    {
        return $this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] = new \MailPoet\Subscription\ManageSubscriptionFormRenderer(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Util\\Url'] ?? $this->getUrl2Service()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Form\\Renderer'] ?? $this->getRenderer2Service()), ($this->services['MailPoet\\Form\\Block\\Date'] ?? $this->getDateService()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Pages' autowired service.
     *
     * @return \MailPoet\Subscription\Pages
     */
    protected function getPagesService()
    {
        return new \MailPoet\Subscription\Pages(($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] ?? $this->getNewSubscriberNotificationMailerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscription\\CaptchaRenderer'] ?? $this->getCaptchaRendererService()), ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] ?? $this->getWelcomeSchedulerService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()), ($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] ?? $this->getSubscriptionUrlFactoryService()), ($this->services['MailPoet\\Form\\AssetsController'] ?? $this->getAssetsControllerService()), ($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()), ($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] ?? $this->getManageSubscriptionFormRendererService()), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Settings\\TrackingConfig'] ?? $this->getTrackingConfigService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Registration' shared autowired service.
     *
     * @return \MailPoet\Subscription\Registration
     */
    protected function getRegistrationService()
    {
        return $this->services['MailPoet\\Subscription\\Registration'] = new \MailPoet\Subscription\Registration(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Subscribers\\SubscriberActions'] ?? $this->getSubscriberActionsService()), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\SubscriptionUrlFactory' shared autowired service.
     *
     * @return \MailPoet\Subscription\SubscriptionUrlFactory
     */
    protected function getSubscriptionUrlFactoryService()
    {
        return $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] = new \MailPoet\Subscription\SubscriptionUrlFactory(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\LinkTokens'] ?? $this->getLinkTokensService()));
    }

    /**
     * Gets the public 'MailPoet\Subscription\Throttling' shared autowired service.
     *
     * @return \MailPoet\Subscription\Throttling
     */
    protected function getThrottlingService()
    {
        return $this->services['MailPoet\\Subscription\\Throttling'] = new \MailPoet\Subscription\Throttling(($this->services['MailPoet\\Subscribers\\SubscriberIPsRepository'] ?? $this->getSubscriberIPsRepositoryService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\Util\CdnAssetUrl' shared service.
     *
     * @return \MailPoet\Util\CdnAssetUrl
     */
    protected function getCdnAssetUrlService()
    {
        return $this->services['MailPoet\\Util\\CdnAssetUrl'] = \MailPoet\DI\ContainerConfigurator::getCdnAssetsUrl();
    }

    /**
     * Gets the public 'MailPoet\Util\License\Features\Subscribers' shared autowired service.
     *
     * @return \MailPoet\Util\License\Features\Subscribers
     */
    protected function getSubscribers4Service()
    {
        return $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] = new \MailPoet\Util\License\Features\Subscribers(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\Util\License\License' shared autowired service.
     *
     * @return \MailPoet\Util\License\License
     */
    protected function getLicenseService()
    {
        return $this->services['MailPoet\\Util\\License\\License'] = new \MailPoet\Util\License\License();
    }

    /**
     * Gets the public 'MailPoet\Util\Url' shared autowired service.
     *
     * @return \MailPoet\Util\Url
     */
    protected function getUrl2Service()
    {
        return $this->services['MailPoet\\Util\\Url'] = new \MailPoet\Util\Url(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\WP\AutocompletePostListLoader' shared autowired service.
     *
     * @return \MailPoet\WP\AutocompletePostListLoader
     */
    protected function getAutocompletePostListLoaderService()
    {
        return $this->services['MailPoet\\WP\\AutocompletePostListLoader'] = new \MailPoet\WP\AutocompletePostListLoader(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\WP\Emoji' shared autowired service.
     *
     * @return \MailPoet\WP\Emoji
     */
    protected function getEmojiService()
    {
        return $this->services['MailPoet\\WP\\Emoji'] = new \MailPoet\WP\Emoji(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the public 'MailPoet\WP\Functions' shared autowired service.
     *
     * @return \MailPoet\WP\Functions
     */
    protected function getFunctionsService()
    {
        return $this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions();
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\Helper' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\Helper
     */
    protected function getHelperService()
    {
        return $this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper();
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\Settings' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\Settings
     */
    protected function getSettings3Service()
    {
        return $this->services['MailPoet\\WooCommerce\\Settings'] = new \MailPoet\WooCommerce\Settings(($this->services['MailPoet\\Config\\Renderer'] ?? $this->getRendererService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\SubscriberEngagement' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\SubscriberEngagement
     */
    protected function getSubscriberEngagementService()
    {
        return $this->services['MailPoet\\WooCommerce\\SubscriberEngagement'] = new \MailPoet\WooCommerce\SubscriberEngagement(($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\Subscription' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\Subscription
     */
    protected function getSubscription2Service()
    {
        return $this->services['MailPoet\\WooCommerce\\Subscription'] = new \MailPoet\WooCommerce\Subscription(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] ?? $this->getConfirmationEmailMailerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] ?? $this->getUnsubscribesService()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmailHooks' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmailHooks
     */
    protected function getTransactionalEmailHooksService()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmailHooks'] = new \MailPoet\WooCommerce\TransactionalEmailHooks(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Renderer'] ?? $this->getRenderer6Service()), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\WooCommerce\\TransactionalEmails'] ?? $this->getTransactionalEmailsService()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmails' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmails
     */
    protected function getTransactionalEmailsService()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmails'] = new \MailPoet\WooCommerce\TransactionalEmails(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Template'] ?? ($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Template'] = new \MailPoet\WooCommerce\TransactionalEmails\Template())), ($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmails\ContentPreprocessor' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmails\ContentPreprocessor
     */
    protected function getContentPreprocessorService()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmails\\ContentPreprocessor'] = new \MailPoet\WooCommerce\TransactionalEmails\ContentPreprocessor(($this->services['MailPoet\\WooCommerce\\TransactionalEmails'] ?? $this->getTransactionalEmailsService()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmails\Renderer' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmails\Renderer
     */
    protected function getRenderer6Service()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Renderer'] = new \MailPoet\WooCommerce\TransactionalEmails\Renderer(new \MailPoetVendor\csstidy(), ($this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] ?? $this->getRenderer5Service()));
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmails\Template' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmails\Template
     */
    protected function getTemplateService()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Template'] = new \MailPoet\WooCommerce\TransactionalEmails\Template();
    }

    /**
     * Gets the private 'MailPoet\Config\MP2Migrator' shared autowired service.
     *
     * @return \MailPoet\Config\MP2Migrator
     */
    protected function getMP2Migrator2Service()
    {
        return $this->privates['MailPoet\\Config\\MP2Migrator'] = new \MailPoet\Config\MP2Migrator(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\Form\\FormsRepository'] ?? $this->getFormsRepositoryService()), ($this->services['MailPoet\\Config\\Activator'] ?? $this->getActivatorService()));
    }

    /**
     * Gets the private 'MailPoet\Form\BlockStylesRenderer' shared autowired service.
     *
     * @return \MailPoet\Form\BlockStylesRenderer
     */
    protected function getBlockStylesRendererService()
    {
        return $this->privates['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Form\BlockWrapperRenderer' shared autowired service.
     *
     * @return \MailPoet\Form\BlockWrapperRenderer
     */
    protected function getBlockWrapperRendererService()
    {
        return $this->privates['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Form\Block\BlockRendererHelper' shared autowired service.
     *
     * @return \MailPoet\Form\Block\BlockRendererHelper
     */
    protected function getBlockRendererHelperService()
    {
        return $this->privates['MailPoet\\Form\\Block\\BlockRendererHelper'] = new \MailPoet\Form\Block\BlockRendererHelper(($this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] ?? $this->getFieldNameObfuscatorService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Form\Templates\TemplateRepository' shared autowired service.
     *
     * @return \MailPoet\Form\Templates\TemplateRepository
     */
    protected function getTemplateRepositoryService()
    {
        return $this->privates['MailPoet\\Form\\Templates\\TemplateRepository'] = new \MailPoet\Form\Templates\TemplateRepository(($this->services['MailPoet\\Util\\CdnAssetUrl'] ?? $this->getCdnAssetUrlService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Logging\LoggerFactory' shared autowired service.
     *
     * @return \MailPoet\Logging\LoggerFactory
     */
    protected function getLoggerFactoryService()
    {
        return $this->privates['MailPoet\\Logging\\LoggerFactory'] = new \MailPoet\Logging\LoggerFactory(($this->services['MailPoet\\Logging\\LogRepository'] ?? $this->getLogRepositoryService()), ($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()), ($this->services['MailPoet\\Doctrine\\EntityManagerFactory'] ?? $this->getEntityManagerFactoryService()), ($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()));
    }

    /**
     * Gets the private 'MailPoet\Mailer\Mailer' shared autowired service.
     *
     * @return \MailPoet\Mailer\Mailer
     */
    protected function getMailer2Service()
    {
        return $this->privates['MailPoet\\Mailer\\Mailer'] = new \MailPoet\Mailer\Mailer(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Settings\UserFlagsController' shared autowired service.
     *
     * @return \MailPoet\Settings\UserFlagsController
     */
    protected function getUserFlagsControllerService()
    {
        return $this->privates['MailPoet\\Settings\\UserFlagsController'] = new \MailPoet\Settings\UserFlagsController(($this->services['MailPoet\\Settings\\UserFlagsRepository'] ?? $this->getUserFlagsRepositoryService()));
    }

    /**
     * Gets the private 'MailPoet\Statistics\StatisticsUnsubscribesRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsUnsubscribesRepository
     */
    protected function getStatisticsUnsubscribesRepositoryService()
    {
        return $this->privates['MailPoet\\Statistics\\StatisticsUnsubscribesRepository'] = new \MailPoet\Statistics\StatisticsUnsubscribesRepository(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the private 'MailPoet\Statistics\Track\WooCommercePurchases' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\WooCommercePurchases
     */
    protected function getWooCommercePurchasesService()
    {
        return $this->privates['MailPoet\\Statistics\\Track\\WooCommercePurchases'] = new \MailPoet\Statistics\Track\WooCommercePurchases(($this->services['MailPoet\\WooCommerce\\Helper'] ?? ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())), ($this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] ?? $this->getStatisticsWooCommercePurchasesRepositoryService()), ($this->services['MailPoet\\Statistics\\StatisticsClicksRepository'] ?? $this->getStatisticsClicksRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()), ($this->privates['MailPoet\\Util\\Cookies'] ?? ($this->privates['MailPoet\\Util\\Cookies'] = new \MailPoet\Util\Cookies())), ($this->services['MailPoet\\Statistics\\Track\\SubscriberHandler'] ?? $this->getSubscriberHandlerService()));
    }

    /**
     * Gets the private 'MailPoet\Subscription\CaptchaSession' shared autowired service.
     *
     * @return \MailPoet\Subscription\CaptchaSession
     */
    protected function getCaptchaSessionService()
    {
        return $this->privates['MailPoet\\Subscription\\CaptchaSession'] = new \MailPoet\Subscription\CaptchaSession(($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Util\DBCollationChecker' shared autowired service.
     *
     * @return \MailPoet\Util\DBCollationChecker
     */
    protected function getDBCollationCheckerService()
    {
        return $this->privates['MailPoet\\Util\\DBCollationChecker'] = new \MailPoet\Util\DBCollationChecker(($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] ?? $this->getEntityManagerService()));
    }

    /**
     * Gets the private 'MailPoet\Util\Installation' shared autowired service.
     *
     * @return \MailPoet\Util\Installation
     */
    protected function getInstallationService()
    {
        return $this->privates['MailPoet\\Util\\Installation'] = new \MailPoet\Util\Installation(($this->services['MailPoet\\Settings\\SettingsController'] ?? $this->getSettingsControllerService()), ($this->services['MailPoet\\WP\\Functions'] ?? ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())));
    }

    /**
     * Gets the private 'MailPoet\Util\Security' shared autowired service.
     *
     * @return \MailPoet\Util\Security
     */
    protected function getSecurityService()
    {
        return $this->privates['MailPoet\\Util\\Security'] = new \MailPoet\Util\Security(($this->services['MailPoet\\Newsletter\\NewslettersRepository'] ?? $this->getNewslettersRepositoryService()), ($this->services['MailPoet\\Subscribers\\SubscribersRepository'] ?? $this->getSubscribersRepositoryService()));
    }
}
