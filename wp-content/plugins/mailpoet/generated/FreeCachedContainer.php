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
class FreeCachedContainer extends Container
{
    private $parameters = [];
    private $targetDirs = [];

    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();

        $this->services = [];
        $this->normalizedIds = [
            'mailpoet\\adminpages\\pagerenderer' => 'MailPoet\\AdminPages\\PageRenderer',
            'mailpoet\\adminpages\\pages\\experimentalfeatures' => 'MailPoet\\AdminPages\\Pages\\ExperimentalFeatures',
            'mailpoet\\adminpages\\pages\\formeditor' => 'MailPoet\\AdminPages\\Pages\\FormEditor',
            'mailpoet\\adminpages\\pages\\forms' => 'MailPoet\\AdminPages\\Pages\\Forms',
            'mailpoet\\adminpages\\pages\\help' => 'MailPoet\\AdminPages\\Pages\\Help',
            'mailpoet\\adminpages\\pages\\mp2migration' => 'MailPoet\\AdminPages\\Pages\\MP2Migration',
            'mailpoet\\adminpages\\pages\\newslettereditor' => 'MailPoet\\AdminPages\\Pages\\NewsletterEditor',
            'mailpoet\\adminpages\\pages\\newsletters' => 'MailPoet\\AdminPages\\Pages\\Newsletters',
            'mailpoet\\adminpages\\pages\\premium' => 'MailPoet\\AdminPages\\Pages\\Premium',
            'mailpoet\\adminpages\\pages\\segments' => 'MailPoet\\AdminPages\\Pages\\Segments',
            'mailpoet\\adminpages\\pages\\settings' => 'MailPoet\\AdminPages\\Pages\\Settings',
            'mailpoet\\adminpages\\pages\\subscribers' => 'MailPoet\\AdminPages\\Pages\\Subscribers',
            'mailpoet\\adminpages\\pages\\subscribersexport' => 'MailPoet\\AdminPages\\Pages\\SubscribersExport',
            'mailpoet\\adminpages\\pages\\subscribersimport' => 'MailPoet\\AdminPages\\Pages\\SubscribersImport',
            'mailpoet\\adminpages\\pages\\update' => 'MailPoet\\AdminPages\\Pages\\Update',
            'mailpoet\\adminpages\\pages\\welcomewizard' => 'MailPoet\\AdminPages\\Pages\\WelcomeWizard',
            'mailpoet\\adminpages\\pages\\woocommercesetup' => 'MailPoet\\AdminPages\\Pages\\WooCommerceSetup',
            'mailpoet\\analytics\\analytics' => 'MailPoet\\Analytics\\Analytics',
            'mailpoet\\analytics\\reporter' => 'MailPoet\\Analytics\\Reporter',
            'mailpoet\\api\\json\\api' => 'MailPoet\\API\\JSON\\API',
            'mailpoet\\api\\json\\errorhandler' => 'MailPoet\\API\\JSON\\ErrorHandler',
            'mailpoet\\api\\json\\responsebuilders\\customfieldsresponsebuilder' => 'MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder',
            'mailpoet\\api\\json\\responsebuilders\\formsresponsebuilder' => 'MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder',
            'mailpoet\\api\\json\\responsebuilders\\newslettersresponsebuilder' => 'MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder',
            'mailpoet\\api\\json\\responsebuilders\\newslettertemplatesresponsebuilder' => 'MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder',
            'mailpoet\\api\\json\\responsebuilders\\segmentsresponsebuilder' => 'MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder',
            'mailpoet\\api\\json\\responsebuilders\\subscribersresponsebuilder' => 'MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder',
            'mailpoet\\api\\json\\v1\\analytics' => 'MailPoet\\API\\JSON\\v1\\Analytics',
            'mailpoet\\api\\json\\v1\\automatedlatestcontent' => 'MailPoet\\API\\JSON\\v1\\AutomatedLatestContent',
            'mailpoet\\api\\json\\v1\\automaticemails' => 'MailPoet\\API\\JSON\\v1\\AutomaticEmails',
            'mailpoet\\api\\json\\v1\\customfields' => 'MailPoet\\API\\JSON\\v1\\CustomFields',
            'mailpoet\\api\\json\\v1\\dynamicsegments' => 'MailPoet\\API\\JSON\\v1\\DynamicSegments',
            'mailpoet\\api\\json\\v1\\featureflags' => 'MailPoet\\API\\JSON\\v1\\FeatureFlags',
            'mailpoet\\api\\json\\v1\\forms' => 'MailPoet\\API\\JSON\\v1\\Forms',
            'mailpoet\\api\\json\\v1\\importexport' => 'MailPoet\\API\\JSON\\v1\\ImportExport',
            'mailpoet\\api\\json\\v1\\mailer' => 'MailPoet\\API\\JSON\\v1\\Mailer',
            'mailpoet\\api\\json\\v1\\mp2migrator' => 'MailPoet\\API\\JSON\\v1\\MP2Migrator',
            'mailpoet\\api\\json\\v1\\newsletterlinks' => 'MailPoet\\API\\JSON\\v1\\NewsletterLinks',
            'mailpoet\\api\\json\\v1\\newsletters' => 'MailPoet\\API\\JSON\\v1\\Newsletters',
            'mailpoet\\api\\json\\v1\\newslettertemplates' => 'MailPoet\\API\\JSON\\v1\\NewsletterTemplates',
            'mailpoet\\api\\json\\v1\\premium' => 'MailPoet\\API\\JSON\\v1\\Premium',
            'mailpoet\\api\\json\\v1\\segments' => 'MailPoet\\API\\JSON\\v1\\Segments',
            'mailpoet\\api\\json\\v1\\sendingqueue' => 'MailPoet\\API\\JSON\\v1\\SendingQueue',
            'mailpoet\\api\\json\\v1\\sendingtasksubscribers' => 'MailPoet\\API\\JSON\\v1\\SendingTaskSubscribers',
            'mailpoet\\api\\json\\v1\\services' => 'MailPoet\\API\\JSON\\v1\\Services',
            'mailpoet\\api\\json\\v1\\settings' => 'MailPoet\\API\\JSON\\v1\\Settings',
            'mailpoet\\api\\json\\v1\\setup' => 'MailPoet\\API\\JSON\\v1\\Setup',
            'mailpoet\\api\\json\\v1\\subscribers' => 'MailPoet\\API\\JSON\\v1\\Subscribers',
            'mailpoet\\api\\json\\v1\\subscriberstats' => 'MailPoet\\API\\JSON\\v1\\SubscriberStats',
            'mailpoet\\api\\json\\v1\\userflags' => 'MailPoet\\API\\JSON\\v1\\UserFlags',
            'mailpoet\\api\\json\\v1\\woocommercesettings' => 'MailPoet\\API\\JSON\\v1\\WoocommerceSettings',
            'mailpoet\\api\\mp\\v1\\api' => 'MailPoet\\API\\MP\\v1\\API',
            'mailpoet\\config\\accesscontrol' => 'MailPoet\\Config\\AccessControl',
            'mailpoet\\config\\activator' => 'MailPoet\\Config\\Activator',
            'mailpoet\\config\\changelog' => 'MailPoet\\Config\\Changelog',
            'mailpoet\\config\\databaseinitializer' => 'MailPoet\\Config\\DatabaseInitializer',
            'mailpoet\\config\\hooks' => 'MailPoet\\Config\\Hooks',
            'mailpoet\\config\\initializer' => 'MailPoet\\Config\\Initializer',
            'mailpoet\\config\\localizer' => 'MailPoet\\Config\\Localizer',
            'mailpoet\\config\\menu' => 'MailPoet\\Config\\Menu',
            'mailpoet\\config\\mp2migrator' => 'MailPoet\\Config\\MP2Migrator',
            'mailpoet\\config\\populator' => 'MailPoet\\Config\\Populator',
            'mailpoet\\config\\renderer' => 'MailPoet\\Config\\Renderer',
            'mailpoet\\config\\rendererfactory' => 'MailPoet\\Config\\RendererFactory',
            'mailpoet\\config\\serviceschecker' => 'MailPoet\\Config\\ServicesChecker',
            'mailpoet\\config\\shortcodes' => 'MailPoet\\Config\\Shortcodes',
            'mailpoet\\cron\\cronhelper' => 'MailPoet\\Cron\\CronHelper',
            'mailpoet\\cron\\crontrigger' => 'MailPoet\\Cron\\CronTrigger',
            'mailpoet\\cron\\cronworkerrunner' => 'MailPoet\\Cron\\CronWorkerRunner',
            'mailpoet\\cron\\cronworkerscheduler' => 'MailPoet\\Cron\\CronWorkerScheduler',
            'mailpoet\\cron\\daemon' => 'MailPoet\\Cron\\Daemon',
            'mailpoet\\cron\\daemonhttprunner' => 'MailPoet\\Cron\\DaemonHttpRunner',
            'mailpoet\\cron\\supervisor' => 'MailPoet\\Cron\\Supervisor',
            'mailpoet\\cron\\triggers\\mailpoet' => 'MailPoet\\Cron\\Triggers\\MailPoet',
            'mailpoet\\cron\\triggers\\wordpress' => 'MailPoet\\Cron\\Triggers\\WordPress',
            'mailpoet\\cron\\workers\\authorizedsendingemailscheck' => 'MailPoet\\Cron\\Workers\\AuthorizedSendingEmailsCheck',
            'mailpoet\\cron\\workers\\beamer' => 'MailPoet\\Cron\\Workers\\Beamer',
            'mailpoet\\cron\\workers\\bounce' => 'MailPoet\\Cron\\Workers\\Bounce',
            'mailpoet\\cron\\workers\\exportfilescleanup' => 'MailPoet\\Cron\\Workers\\ExportFilesCleanup',
            'mailpoet\\cron\\workers\\inactivesubscribers' => 'MailPoet\\Cron\\Workers\\InactiveSubscribers',
            'mailpoet\\cron\\workers\\keycheck\\premiumkeycheck' => 'MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck',
            'mailpoet\\cron\\workers\\keycheck\\sendingservicekeycheck' => 'MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck',
            'mailpoet\\cron\\workers\\scheduler' => 'MailPoet\\Cron\\Workers\\Scheduler',
            'mailpoet\\cron\\workers\\sendingqueue\\migration' => 'MailPoet\\Cron\\Workers\\SendingQueue\\Migration',
            'mailpoet\\cron\\workers\\sendingqueue\\sendingerrorhandler' => 'MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler',
            'mailpoet\\cron\\workers\\sendingqueue\\sendingqueue' => 'MailPoet\\Cron\\Workers\\SendingQueue\\SendingQueue',
            'mailpoet\\cron\\workers\\statsnotifications\\automatedemails' => 'MailPoet\\Cron\\Workers\\StatsNotifications\\AutomatedEmails',
            'mailpoet\\cron\\workers\\statsnotifications\\newsletterlinkrepository' => 'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository',
            'mailpoet\\cron\\workers\\statsnotifications\\scheduler' => 'MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler',
            'mailpoet\\cron\\workers\\statsnotifications\\statsnotificationsrepository' => 'MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository',
            'mailpoet\\cron\\workers\\statsnotifications\\worker' => 'MailPoet\\Cron\\Workers\\StatsNotifications\\Worker',
            'mailpoet\\cron\\workers\\subscriberlinktokens' => 'MailPoet\\Cron\\Workers\\SubscriberLinkTokens',
            'mailpoet\\cron\\workers\\unsubscribetokens' => 'MailPoet\\Cron\\Workers\\UnsubscribeTokens',
            'mailpoet\\cron\\workers\\woocommercepastorders' => 'MailPoet\\Cron\\Workers\\WooCommercePastOrders',
            'mailpoet\\cron\\workers\\woocommercesync' => 'MailPoet\\Cron\\Workers\\WooCommerceSync',
            'mailpoet\\cron\\workers\\workersfactory' => 'MailPoet\\Cron\\Workers\\WorkersFactory',
            'mailpoet\\customfields\\apidatasanitizer' => 'MailPoet\\CustomFields\\ApiDataSanitizer',
            'mailpoet\\customfields\\customfieldsrepository' => 'MailPoet\\CustomFields\\CustomFieldsRepository',
            'mailpoet\\di\\containerwrapper' => 'MailPoet\\DI\\ContainerWrapper',
            'mailpoet\\doctrine\\annotations\\annotationreaderprovider' => 'MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider',
            'mailpoet\\doctrine\\configurationfactory' => 'MailPoet\\Doctrine\\ConfigurationFactory',
            'mailpoet\\doctrine\\connectionfactory' => 'MailPoet\\Doctrine\\ConnectionFactory',
            'mailpoet\\doctrine\\entitymanagerfactory' => 'MailPoet\\Doctrine\\EntityManagerFactory',
            'mailpoet\\doctrine\\eventlisteners\\emojiencodinglistener' => 'MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener',
            'mailpoet\\doctrine\\eventlisteners\\timestamplistener' => 'MailPoet\\Doctrine\\EventListeners\\TimestampListener',
            'mailpoet\\doctrine\\eventlisteners\\validationlistener' => 'MailPoet\\Doctrine\\EventListeners\\ValidationListener',
            'mailpoet\\doctrine\\validator\\validatorfactory' => 'MailPoet\\Doctrine\\Validator\\ValidatorFactory',
            'mailpoet\\dynamicsegments\\freepluginconnectors\\addtonewsletterssegments' => 'MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments',
            'mailpoet\\dynamicsegments\\mappers\\dbmapper' => 'MailPoet\\DynamicSegments\\Mappers\\DBMapper',
            'mailpoet\\dynamicsegments\\persistence\\loading\\loader' => 'MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader',
            'mailpoet\\dynamicsegments\\persistence\\loading\\singlesegmentloader' => 'MailPoet\\DynamicSegments\\Persistence\\Loading\\SingleSegmentLoader',
            'mailpoet\\dynamicsegments\\persistence\\loading\\subscriberscount' => 'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount',
            'mailpoet\\dynamicsegments\\persistence\\loading\\subscribersids' => 'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersIds',
            'mailpoet\\features\\featureflagscontroller' => 'MailPoet\\Features\\FeatureFlagsController',
            'mailpoet\\features\\featureflagsrepository' => 'MailPoet\\Features\\FeatureFlagsRepository',
            'mailpoet\\features\\featurescontroller' => 'MailPoet\\Features\\FeaturesController',
            'mailpoet\\form\\assetscontroller' => 'MailPoet\\Form\\AssetsController',
            'mailpoet\\form\\block\\blockrendererhelper' => 'MailPoet\\Form\\Block\\BlockRendererHelper',
            'mailpoet\\form\\block\\checkbox' => 'MailPoet\\Form\\Block\\Checkbox',
            'mailpoet\\form\\block\\column' => 'MailPoet\\Form\\Block\\Column',
            'mailpoet\\form\\block\\columns' => 'MailPoet\\Form\\Block\\Columns',
            'mailpoet\\form\\block\\date' => 'MailPoet\\Form\\Block\\Date',
            'mailpoet\\form\\block\\divider' => 'MailPoet\\Form\\Block\\Divider',
            'mailpoet\\form\\block\\heading' => 'MailPoet\\Form\\Block\\Heading',
            'mailpoet\\form\\block\\html' => 'MailPoet\\Form\\Block\\Html',
            'mailpoet\\form\\block\\image' => 'MailPoet\\Form\\Block\\Image',
            'mailpoet\\form\\block\\paragraph' => 'MailPoet\\Form\\Block\\Paragraph',
            'mailpoet\\form\\block\\radio' => 'MailPoet\\Form\\Block\\Radio',
            'mailpoet\\form\\block\\segment' => 'MailPoet\\Form\\Block\\Segment',
            'mailpoet\\form\\block\\select' => 'MailPoet\\Form\\Block\\Select',
            'mailpoet\\form\\block\\submit' => 'MailPoet\\Form\\Block\\Submit',
            'mailpoet\\form\\block\\text' => 'MailPoet\\Form\\Block\\Text',
            'mailpoet\\form\\block\\textarea' => 'MailPoet\\Form\\Block\\Textarea',
            'mailpoet\\form\\blocksrenderer' => 'MailPoet\\Form\\BlocksRenderer',
            'mailpoet\\form\\blockstylesrenderer' => 'MailPoet\\Form\\BlockStylesRenderer',
            'mailpoet\\form\\blockwrapperrenderer' => 'MailPoet\\Form\\BlockWrapperRenderer',
            'mailpoet\\form\\displayforminwpcontent' => 'MailPoet\\Form\\DisplayFormInWPContent',
            'mailpoet\\form\\formfactory' => 'MailPoet\\Form\\FormFactory',
            'mailpoet\\form\\formsrepository' => 'MailPoet\\Form\\FormsRepository',
            'mailpoet\\form\\previewpage' => 'MailPoet\\Form\\PreviewPage',
            'mailpoet\\form\\renderer' => 'MailPoet\\Form\\Renderer',
            'mailpoet\\form\\templates\\templaterepository' => 'MailPoet\\Form\\Templates\\TemplateRepository',
            'mailpoet\\form\\util\\customfonts' => 'MailPoet\\Form\\Util\\CustomFonts',
            'mailpoet\\form\\util\\fieldnameobfuscator' => 'MailPoet\\Form\\Util\\FieldNameObfuscator',
            'mailpoet\\form\\util\\styles' => 'MailPoet\\Form\\Util\\Styles',
            'mailpoet\\helpscout\\beacon' => 'MailPoet\\Helpscout\\Beacon',
            'mailpoet\\listing\\bulkactioncontroller' => 'MailPoet\\Listing\\BulkActionController',
            'mailpoet\\listing\\bulkactionfactory' => 'MailPoet\\Listing\\BulkActionFactory',
            'mailpoet\\listing\\handler' => 'MailPoet\\Listing\\Handler',
            'mailpoet\\listing\\pagelimit' => 'MailPoet\\Listing\\PageLimit',
            'mailpoet\\logging\\loggerfactory' => 'MailPoet\\Logging\\LoggerFactory',
            'mailpoet\\mailer\\mailer' => 'MailPoet\\Mailer\\Mailer',
            'mailpoet\\mailer\\metainfo' => 'MailPoet\\Mailer\\MetaInfo',
            'mailpoet\\mailer\\methods\\common\\blacklistcheck' => 'MailPoet\\Mailer\\Methods\\Common\\BlacklistCheck',
            'mailpoet\\mailer\\wordpress\\wordpressmailerreplacer' => 'MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer',
            'mailpoet\\newsletter\\automatedlatestcontent' => 'MailPoet\\Newsletter\\AutomatedLatestContent',
            'mailpoet\\newsletter\\listing\\newsletterlistingrepository' => 'MailPoet\\Newsletter\\Listing\\NewsletterListingRepository',
            'mailpoet\\newsletter\\newsletterpostsrepository' => 'MailPoet\\Newsletter\\NewsletterPostsRepository',
            'mailpoet\\newsletter\\newslettersavecontroller' => 'MailPoet\\Newsletter\\NewsletterSaveController',
            'mailpoet\\newsletter\\newslettersrepository' => 'MailPoet\\Newsletter\\NewslettersRepository',
            'mailpoet\\newsletter\\options\\newsletteroptionfieldsrepository' => 'MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository',
            'mailpoet\\newsletter\\options\\newsletteroptionsrepository' => 'MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository',
            'mailpoet\\newsletter\\preview\\sendpreviewcontroller' => 'MailPoet\\Newsletter\\Preview\\SendPreviewController',
            'mailpoet\\newsletter\\renderer\\blocks\\abandonedcartcontent' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent',
            'mailpoet\\newsletter\\renderer\\blocks\\automatedlatestcontentblock' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock',
            'mailpoet\\newsletter\\renderer\\blocks\\button' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Button',
            'mailpoet\\newsletter\\renderer\\blocks\\divider' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Divider',
            'mailpoet\\newsletter\\renderer\\blocks\\footer' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Footer',
            'mailpoet\\newsletter\\renderer\\blocks\\header' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Header',
            'mailpoet\\newsletter\\renderer\\blocks\\image' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Image',
            'mailpoet\\newsletter\\renderer\\blocks\\renderer' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer',
            'mailpoet\\newsletter\\renderer\\blocks\\social' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Social',
            'mailpoet\\newsletter\\renderer\\blocks\\spacer' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer',
            'mailpoet\\newsletter\\renderer\\blocks\\text' => 'MailPoet\\Newsletter\\Renderer\\Blocks\\Text',
            'mailpoet\\newsletter\\renderer\\columns\\renderer' => 'MailPoet\\Newsletter\\Renderer\\Columns\\Renderer',
            'mailpoet\\newsletter\\renderer\\preprocessor' => 'MailPoet\\Newsletter\\Renderer\\Preprocessor',
            'mailpoet\\newsletter\\renderer\\renderer' => 'MailPoet\\Newsletter\\Renderer\\Renderer',
            'mailpoet\\newsletter\\scheduler\\postnotificationscheduler' => 'MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler',
            'mailpoet\\newsletter\\scheduler\\welcomescheduler' => 'MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler',
            'mailpoet\\newsletter\\segment\\newslettersegmentrepository' => 'MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository',
            'mailpoet\\newsletter\\sending\\scheduledtasksrepository' => 'MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository',
            'mailpoet\\newsletter\\sending\\scheduledtasksubscribersrepository' => 'MailPoet\\Newsletter\\Sending\\ScheduledTaskSubscribersRepository',
            'mailpoet\\newsletter\\sending\\sendingqueuesrepository' => 'MailPoet\\Newsletter\\Sending\\SendingQueuesRepository',
            'mailpoet\\newsletter\\statistics\\newsletterstatisticsrepository' => 'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository',
            'mailpoet\\newsletter\\viewinbrowser\\viewinbrowsercontroller' => 'MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController',
            'mailpoet\\newsletter\\viewinbrowser\\viewinbrowserrenderer' => 'MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer',
            'mailpoet\\newslettertemplates\\newslettertemplatesrepository' => 'MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository',
            'mailpoet\\posteditorblocks\\posteditorblock' => 'MailPoet\\PostEditorBlocks\\PostEditorBlock',
            'mailpoet\\posteditorblocks\\subscriptionformblock' => 'MailPoet\\PostEditorBlocks\\SubscriptionFormBlock',
            'mailpoet\\referrals\\referraldetector' => 'MailPoet\\Referrals\\ReferralDetector',
            'mailpoet\\router\\endpoints\\crondaemon' => 'MailPoet\\Router\\Endpoints\\CronDaemon',
            'mailpoet\\router\\endpoints\\formpreview' => 'MailPoet\\Router\\Endpoints\\FormPreview',
            'mailpoet\\router\\endpoints\\subscription' => 'MailPoet\\Router\\Endpoints\\Subscription',
            'mailpoet\\router\\endpoints\\track' => 'MailPoet\\Router\\Endpoints\\Track',
            'mailpoet\\router\\endpoints\\viewinbrowser' => 'MailPoet\\Router\\Endpoints\\ViewInBrowser',
            'mailpoet\\router\\router' => 'MailPoet\\Router\\Router',
            'mailpoet\\segments\\dynamicsegments\\filterhandler' => 'MailPoet\\Segments\\DynamicSegments\\FilterHandler',
            'mailpoet\\segments\\dynamicsegments\\filters\\emailaction' => 'MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction',
            'mailpoet\\segments\\dynamicsegments\\filters\\userrole' => 'MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole',
            'mailpoet\\segments\\dynamicsegments\\filters\\woocommercecategory' => 'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory',
            'mailpoet\\segments\\dynamicsegments\\filters\\woocommerceproduct' => 'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct',
            'mailpoet\\segments\\segmentsrepository' => 'MailPoet\\Segments\\SegmentsRepository',
            'mailpoet\\segments\\segmentsubscribersrepository' => 'MailPoet\\Segments\\SegmentSubscribersRepository',
            'mailpoet\\segments\\subscribersfinder' => 'MailPoet\\Segments\\SubscribersFinder',
            'mailpoet\\segments\\woocommerce' => 'MailPoet\\Segments\\WooCommerce',
            'mailpoet\\services\\authorizedemailscontroller' => 'MailPoet\\Services\\AuthorizedEmailsController',
            'mailpoet\\services\\bridge' => 'MailPoet\\Services\\Bridge',
            'mailpoet\\services\\congratulatorymssemailcontroller' => 'MailPoet\\Services\\CongratulatoryMssEmailController',
            'mailpoet\\settings\\settingscontroller' => 'MailPoet\\Settings\\SettingsController',
            'mailpoet\\settings\\settingsrepository' => 'MailPoet\\Settings\\SettingsRepository',
            'mailpoet\\settings\\userflagscontroller' => 'MailPoet\\Settings\\UserFlagsController',
            'mailpoet\\settings\\userflagsrepository' => 'MailPoet\\Settings\\UserFlagsRepository',
            'mailpoet\\statistics\\statisticsunsubscribesrepository' => 'MailPoet\\Statistics\\StatisticsUnsubscribesRepository',
            'mailpoet\\statistics\\statisticswoocommercepurchasesrepository' => 'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository',
            'mailpoet\\statistics\\track\\clicks' => 'MailPoet\\Statistics\\Track\\Clicks',
            'mailpoet\\statistics\\track\\opens' => 'MailPoet\\Statistics\\Track\\Opens',
            'mailpoet\\statistics\\track\\unsubscribes' => 'MailPoet\\Statistics\\Track\\Unsubscribes',
            'mailpoet\\statistics\\track\\woocommercepurchases' => 'MailPoet\\Statistics\\Track\\WooCommercePurchases',
            'mailpoet\\subscribers\\confirmationemailmailer' => 'MailPoet\\Subscribers\\ConfirmationEmailMailer',
            'mailpoet\\subscribers\\inactivesubscriberscontroller' => 'MailPoet\\Subscribers\\InactiveSubscribersController',
            'mailpoet\\subscribers\\linktokens' => 'MailPoet\\Subscribers\\LinkTokens',
            'mailpoet\\subscribers\\newsubscribernotificationmailer' => 'MailPoet\\Subscribers\\NewSubscriberNotificationMailer',
            'mailpoet\\subscribers\\requiredcustomfieldvalidator' => 'MailPoet\\Subscribers\\RequiredCustomFieldValidator',
            'mailpoet\\subscribers\\statistics\\subscriberstatisticsrepository' => 'MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository',
            'mailpoet\\subscribers\\subscriberactions' => 'MailPoet\\Subscribers\\SubscriberActions',
            'mailpoet\\subscribers\\subscribercustomfieldrepository' => 'MailPoet\\Subscribers\\SubscriberCustomFieldRepository',
            'mailpoet\\subscribers\\subscriberlistingrepository' => 'MailPoet\\Subscribers\\SubscriberListingRepository',
            'mailpoet\\subscribers\\subscribersegmentrepository' => 'MailPoet\\Subscribers\\SubscriberSegmentRepository',
            'mailpoet\\subscribers\\subscribersrepository' => 'MailPoet\\Subscribers\\SubscribersRepository',
            'mailpoet\\subscription\\captcha' => 'MailPoet\\Subscription\\Captcha',
            'mailpoet\\subscription\\captcharenderer' => 'MailPoet\\Subscription\\CaptchaRenderer',
            'mailpoet\\subscription\\captchasession' => 'MailPoet\\Subscription\\CaptchaSession',
            'mailpoet\\subscription\\comment' => 'MailPoet\\Subscription\\Comment',
            'mailpoet\\subscription\\form' => 'MailPoet\\Subscription\\Form',
            'mailpoet\\subscription\\manage' => 'MailPoet\\Subscription\\Manage',
            'mailpoet\\subscription\\managesubscriptionformrenderer' => 'MailPoet\\Subscription\\ManageSubscriptionFormRenderer',
            'mailpoet\\subscription\\pages' => 'MailPoet\\Subscription\\Pages',
            'mailpoet\\subscription\\registration' => 'MailPoet\\Subscription\\Registration',
            'mailpoet\\subscription\\subscriptionurlfactory' => 'MailPoet\\Subscription\\SubscriptionUrlFactory',
            'mailpoet\\tasks\\state' => 'MailPoet\\Tasks\\State',
            'mailpoet\\util\\cdnasseturl' => 'MailPoet\\Util\\CdnAssetUrl',
            'mailpoet\\util\\cookies' => 'MailPoet\\Util\\Cookies',
            'mailpoet\\util\\installation' => 'MailPoet\\Util\\Installation',
            'mailpoet\\util\\license\\features\\subscribers' => 'MailPoet\\Util\\License\\Features\\Subscribers',
            'mailpoet\\util\\license\\license' => 'MailPoet\\Util\\License\\License',
            'mailpoet\\util\\notices\\permanentnotices' => 'MailPoet\\Util\\Notices\\PermanentNotices',
            'mailpoet\\util\\url' => 'MailPoet\\Util\\Url',
            'mailpoet\\woocommerce\\helper' => 'MailPoet\\WooCommerce\\Helper',
            'mailpoet\\woocommerce\\settings' => 'MailPoet\\WooCommerce\\Settings',
            'mailpoet\\woocommerce\\subscription' => 'MailPoet\\WooCommerce\\Subscription',
            'mailpoet\\woocommerce\\transactionalemailhooks' => 'MailPoet\\WooCommerce\\TransactionalEmailHooks',
            'mailpoet\\woocommerce\\transactionalemails' => 'MailPoet\\WooCommerce\\TransactionalEmails',
            'mailpoet\\woocommerce\\transactionalemails\\renderer' => 'MailPoet\\WooCommerce\\TransactionalEmails\\Renderer',
            'mailpoet\\woocommerce\\transactionalemails\\template' => 'MailPoet\\WooCommerce\\TransactionalEmails\\Template',
            'mailpoet\\wp\\emoji' => 'MailPoet\\WP\\Emoji',
            'mailpoet\\wp\\functions' => 'MailPoet\\WP\\Functions',
            'mailpoetvendor\\css' => 'MailPoetVendor\\CSS',
            'mailpoetvendor\\csstidy' => 'MailPoetVendor\\csstidy',
            'mailpoetvendor\\doctrine\\dbal\\connection' => 'MailPoetVendor\\Doctrine\\DBAL\\Connection',
            'mailpoetvendor\\doctrine\\orm\\configuration' => 'MailPoetVendor\\Doctrine\\ORM\\Configuration',
            'mailpoetvendor\\doctrine\\orm\\entitymanager' => 'MailPoetVendor\\Doctrine\\ORM\\EntityManager',
            'mailpoetvendor\\symfony\\component\\validator\\validator\\validatorinterface' => 'MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface',
        ];
        $this->syntheticIds = [
            'premium_container' => true,
        ];
        $this->methodMap = [
            'MailPoetVendor\\CSS' => 'getCSSService',
            'MailPoetVendor\\Doctrine\\DBAL\\Connection' => 'getConnectionService',
            'MailPoetVendor\\Doctrine\\ORM\\Configuration' => 'getConfigurationService',
            'MailPoetVendor\\Doctrine\\ORM\\EntityManager' => 'getEntityManagerService',
            'MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface' => 'getValidatorInterfaceService',
            'MailPoetVendor\\csstidy' => 'getCsstidyService',
            'MailPoet\\API\\JSON\\API' => 'getAPIService',
            'MailPoet\\API\\JSON\\ErrorHandler' => 'getErrorHandlerService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder' => 'getCustomFieldsResponseBuilderService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder' => 'getFormsResponseBuilderService',
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder' => 'getNewsletterTemplatesResponseBuilderService',
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
            'MailPoet\\AdminPages\\PageRenderer' => 'getPageRendererService',
            'MailPoet\\AdminPages\\Pages\\ExperimentalFeatures' => 'getExperimentalFeaturesService',
            'MailPoet\\AdminPages\\Pages\\FormEditor' => 'getFormEditorService',
            'MailPoet\\AdminPages\\Pages\\Forms' => 'getForms2Service',
            'MailPoet\\AdminPages\\Pages\\Help' => 'getHelpService',
            'MailPoet\\AdminPages\\Pages\\MP2Migration' => 'getMP2MigrationService',
            'MailPoet\\AdminPages\\Pages\\NewsletterEditor' => 'getNewsletterEditorService',
            'MailPoet\\AdminPages\\Pages\\Newsletters' => 'getNewsletters2Service',
            'MailPoet\\AdminPages\\Pages\\Premium' => 'getPremium2Service',
            'MailPoet\\AdminPages\\Pages\\Segments' => 'getSegments2Service',
            'MailPoet\\AdminPages\\Pages\\Settings' => 'getSettings2Service',
            'MailPoet\\AdminPages\\Pages\\Subscribers' => 'getSubscribers2Service',
            'MailPoet\\AdminPages\\Pages\\SubscribersExport' => 'getSubscribersExportService',
            'MailPoet\\AdminPages\\Pages\\SubscribersImport' => 'getSubscribersImportService',
            'MailPoet\\AdminPages\\Pages\\Update' => 'getUpdateService',
            'MailPoet\\AdminPages\\Pages\\WelcomeWizard' => 'getWelcomeWizardService',
            'MailPoet\\AdminPages\\Pages\\WooCommerceSetup' => 'getWooCommerceSetupService',
            'MailPoet\\Analytics\\Analytics' => 'getAnalytics2Service',
            'MailPoet\\Analytics\\Reporter' => 'getReporterService',
            'MailPoet\\Config\\AccessControl' => 'getAccessControlService',
            'MailPoet\\Config\\Activator' => 'getActivatorService',
            'MailPoet\\Config\\Changelog' => 'getChangelogService',
            'MailPoet\\Config\\DatabaseInitializer' => 'getDatabaseInitializerService',
            'MailPoet\\Config\\Hooks' => 'getHooksService',
            'MailPoet\\Config\\Initializer' => 'getInitializerService',
            'MailPoet\\Config\\Localizer' => 'getLocalizerService',
            'MailPoet\\Config\\MP2Migrator' => 'getMP2Migrator2Service',
            'MailPoet\\Config\\Menu' => 'getMenuService',
            'MailPoet\\Config\\Populator' => 'getPopulatorService',
            'MailPoet\\Config\\Renderer' => 'getRendererService',
            'MailPoet\\Config\\RendererFactory' => 'getRendererFactoryService',
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
            'MailPoet\\Cron\\Workers\\Scheduler' => 'getSchedulerService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\Migration' => 'getMigrationService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler' => 'getSendingErrorHandlerService',
            'MailPoet\\Cron\\Workers\\SendingQueue\\SendingQueue' => 'getSendingQueue2Service',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\AutomatedEmails' => 'getAutomatedEmailsService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository' => 'getNewsletterLinkRepositoryService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler' => 'getScheduler2Service',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository' => 'getStatsNotificationsRepositoryService',
            'MailPoet\\Cron\\Workers\\StatsNotifications\\Worker' => 'getWorkerService',
            'MailPoet\\Cron\\Workers\\SubscriberLinkTokens' => 'getSubscriberLinkTokensService',
            'MailPoet\\Cron\\Workers\\UnsubscribeTokens' => 'getUnsubscribeTokensService',
            'MailPoet\\Cron\\Workers\\WooCommercePastOrders' => 'getWooCommercePastOrdersService',
            'MailPoet\\Cron\\Workers\\WooCommerceSync' => 'getWooCommerceSyncService',
            'MailPoet\\Cron\\Workers\\WorkersFactory' => 'getWorkersFactoryService',
            'MailPoet\\CustomFields\\ApiDataSanitizer' => 'getApiDataSanitizerService',
            'MailPoet\\CustomFields\\CustomFieldsRepository' => 'getCustomFieldsRepositoryService',
            'MailPoet\\DI\\ContainerWrapper' => 'getContainerWrapperService',
            'MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider' => 'getAnnotationReaderProviderService',
            'MailPoet\\Doctrine\\ConfigurationFactory' => 'getConfigurationFactoryService',
            'MailPoet\\Doctrine\\ConnectionFactory' => 'getConnectionFactoryService',
            'MailPoet\\Doctrine\\EntityManagerFactory' => 'getEntityManagerFactoryService',
            'MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener' => 'getEmojiEncodingListenerService',
            'MailPoet\\Doctrine\\EventListeners\\TimestampListener' => 'getTimestampListenerService',
            'MailPoet\\Doctrine\\EventListeners\\ValidationListener' => 'getValidationListenerService',
            'MailPoet\\Doctrine\\Validator\\ValidatorFactory' => 'getValidatorFactoryService',
            'MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments' => 'getAddToNewslettersSegmentsService',
            'MailPoet\\DynamicSegments\\Mappers\\DBMapper' => 'getDBMapperService',
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader' => 'getLoaderService',
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SingleSegmentLoader' => 'getSingleSegmentLoaderService',
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount' => 'getSubscribersCountService',
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersIds' => 'getSubscribersIdsService',
            'MailPoet\\Features\\FeatureFlagsController' => 'getFeatureFlagsControllerService',
            'MailPoet\\Features\\FeatureFlagsRepository' => 'getFeatureFlagsRepositoryService',
            'MailPoet\\Features\\FeaturesController' => 'getFeaturesControllerService',
            'MailPoet\\Form\\AssetsController' => 'getAssetsControllerService',
            'MailPoet\\Form\\BlockStylesRenderer' => 'getBlockStylesRendererService',
            'MailPoet\\Form\\BlockWrapperRenderer' => 'getBlockWrapperRendererService',
            'MailPoet\\Form\\Block\\BlockRendererHelper' => 'getBlockRendererHelperService',
            'MailPoet\\Form\\Block\\Checkbox' => 'getCheckboxService',
            'MailPoet\\Form\\Block\\Column' => 'getColumnService',
            'MailPoet\\Form\\Block\\Columns' => 'getColumnsService',
            'MailPoet\\Form\\Block\\Date' => 'getDateService',
            'MailPoet\\Form\\Block\\Divider' => 'getDividerService',
            'MailPoet\\Form\\Block\\Heading' => 'getHeadingService',
            'MailPoet\\Form\\Block\\Html' => 'getHtmlService',
            'MailPoet\\Form\\Block\\Image' => 'getImageService',
            'MailPoet\\Form\\Block\\Paragraph' => 'getParagraphService',
            'MailPoet\\Form\\Block\\Radio' => 'getRadioService',
            'MailPoet\\Form\\Block\\Segment' => 'getSegmentService',
            'MailPoet\\Form\\Block\\Select' => 'getSelectService',
            'MailPoet\\Form\\Block\\Submit' => 'getSubmitService',
            'MailPoet\\Form\\Block\\Text' => 'getTextService',
            'MailPoet\\Form\\Block\\Textarea' => 'getTextareaService',
            'MailPoet\\Form\\BlocksRenderer' => 'getBlocksRendererService',
            'MailPoet\\Form\\DisplayFormInWPContent' => 'getDisplayFormInWPContentService',
            'MailPoet\\Form\\FormFactory' => 'getFormFactoryService',
            'MailPoet\\Form\\FormsRepository' => 'getFormsRepositoryService',
            'MailPoet\\Form\\PreviewPage' => 'getPreviewPageService',
            'MailPoet\\Form\\Renderer' => 'getRenderer2Service',
            'MailPoet\\Form\\Templates\\TemplateRepository' => 'getTemplateRepositoryService',
            'MailPoet\\Form\\Util\\CustomFonts' => 'getCustomFontsService',
            'MailPoet\\Form\\Util\\FieldNameObfuscator' => 'getFieldNameObfuscatorService',
            'MailPoet\\Form\\Util\\Styles' => 'getStylesService',
            'MailPoet\\Helpscout\\Beacon' => 'getBeaconService',
            'MailPoet\\Listing\\BulkActionController' => 'getBulkActionControllerService',
            'MailPoet\\Listing\\BulkActionFactory' => 'getBulkActionFactoryService',
            'MailPoet\\Listing\\Handler' => 'getHandlerService',
            'MailPoet\\Listing\\PageLimit' => 'getPageLimitService',
            'MailPoet\\Logging\\LoggerFactory' => 'getLoggerFactoryService',
            'MailPoet\\Mailer\\Mailer' => 'getMailer2Service',
            'MailPoet\\Mailer\\MetaInfo' => 'getMetaInfoService',
            'MailPoet\\Mailer\\Methods\\Common\\BlacklistCheck' => 'getBlacklistCheckService',
            'MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer' => 'getWordpressMailerReplacerService',
            'MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository' => 'getNewsletterTemplatesRepositoryService',
            'MailPoet\\Newsletter\\AutomatedLatestContent' => 'getAutomatedLatestContent2Service',
            'MailPoet\\Newsletter\\Listing\\NewsletterListingRepository' => 'getNewsletterListingRepositoryService',
            'MailPoet\\Newsletter\\NewsletterPostsRepository' => 'getNewsletterPostsRepositoryService',
            'MailPoet\\Newsletter\\NewsletterSaveController' => 'getNewsletterSaveControllerService',
            'MailPoet\\Newsletter\\NewslettersRepository' => 'getNewslettersRepositoryService',
            'MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository' => 'getNewsletterOptionFieldsRepositoryService',
            'MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository' => 'getNewsletterOptionsRepositoryService',
            'MailPoet\\Newsletter\\Preview\\SendPreviewController' => 'getSendPreviewControllerService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent' => 'getAbandonedCartContentService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock' => 'getAutomatedLatestContentBlockService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Button' => 'getButtonService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Divider' => 'getDivider2Service',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Footer' => 'getFooterService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Header' => 'getHeaderService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Image' => 'getImage2Service',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer' => 'getRenderer3Service',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Social' => 'getSocialService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer' => 'getSpacerService',
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Text' => 'getText2Service',
            'MailPoet\\Newsletter\\Renderer\\Columns\\Renderer' => 'getRenderer4Service',
            'MailPoet\\Newsletter\\Renderer\\Preprocessor' => 'getPreprocessorService',
            'MailPoet\\Newsletter\\Renderer\\Renderer' => 'getRenderer5Service',
            'MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler' => 'getPostNotificationSchedulerService',
            'MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler' => 'getWelcomeSchedulerService',
            'MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository' => 'getNewsletterSegmentRepositoryService',
            'MailPoet\\Newsletter\\Sending\\ScheduledTaskSubscribersRepository' => 'getScheduledTaskSubscribersRepositoryService',
            'MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository' => 'getScheduledTasksRepositoryService',
            'MailPoet\\Newsletter\\Sending\\SendingQueuesRepository' => 'getSendingQueuesRepositoryService',
            'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository' => 'getNewsletterStatisticsRepositoryService',
            'MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController' => 'getViewInBrowserControllerService',
            'MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer' => 'getViewInBrowserRendererService',
            'MailPoet\\PostEditorBlocks\\PostEditorBlock' => 'getPostEditorBlockService',
            'MailPoet\\PostEditorBlocks\\SubscriptionFormBlock' => 'getSubscriptionFormBlockService',
            'MailPoet\\Referrals\\ReferralDetector' => 'getReferralDetectorService',
            'MailPoet\\Router\\Endpoints\\CronDaemon' => 'getCronDaemonService',
            'MailPoet\\Router\\Endpoints\\FormPreview' => 'getFormPreviewService',
            'MailPoet\\Router\\Endpoints\\Subscription' => 'getSubscriptionService',
            'MailPoet\\Router\\Endpoints\\Track' => 'getTrackService',
            'MailPoet\\Router\\Endpoints\\ViewInBrowser' => 'getViewInBrowserService',
            'MailPoet\\Router\\Router' => 'getRouterService',
            'MailPoet\\Segments\\DynamicSegments\\FilterHandler' => 'getFilterHandlerService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction' => 'getEmailActionService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole' => 'getUserRoleService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory' => 'getWooCommerceCategoryService',
            'MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct' => 'getWooCommerceProductService',
            'MailPoet\\Segments\\SegmentSubscribersRepository' => 'getSegmentSubscribersRepositoryService',
            'MailPoet\\Segments\\SegmentsRepository' => 'getSegmentsRepositoryService',
            'MailPoet\\Segments\\SubscribersFinder' => 'getSubscribersFinderService',
            'MailPoet\\Segments\\WooCommerce' => 'getWooCommerceService',
            'MailPoet\\Services\\AuthorizedEmailsController' => 'getAuthorizedEmailsControllerService',
            'MailPoet\\Services\\Bridge' => 'getBridgeService',
            'MailPoet\\Services\\CongratulatoryMssEmailController' => 'getCongratulatoryMssEmailControllerService',
            'MailPoet\\Settings\\SettingsController' => 'getSettingsControllerService',
            'MailPoet\\Settings\\SettingsRepository' => 'getSettingsRepositoryService',
            'MailPoet\\Settings\\UserFlagsController' => 'getUserFlagsControllerService',
            'MailPoet\\Settings\\UserFlagsRepository' => 'getUserFlagsRepositoryService',
            'MailPoet\\Statistics\\StatisticsUnsubscribesRepository' => 'getStatisticsUnsubscribesRepositoryService',
            'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository' => 'getStatisticsWooCommercePurchasesRepositoryService',
            'MailPoet\\Statistics\\Track\\Clicks' => 'getClicksService',
            'MailPoet\\Statistics\\Track\\Opens' => 'getOpensService',
            'MailPoet\\Statistics\\Track\\Unsubscribes' => 'getUnsubscribesService',
            'MailPoet\\Statistics\\Track\\WooCommercePurchases' => 'getWooCommercePurchasesService',
            'MailPoet\\Subscribers\\ConfirmationEmailMailer' => 'getConfirmationEmailMailerService',
            'MailPoet\\Subscribers\\InactiveSubscribersController' => 'getInactiveSubscribersControllerService',
            'MailPoet\\Subscribers\\LinkTokens' => 'getLinkTokensService',
            'MailPoet\\Subscribers\\NewSubscriberNotificationMailer' => 'getNewSubscriberNotificationMailerService',
            'MailPoet\\Subscribers\\RequiredCustomFieldValidator' => 'getRequiredCustomFieldValidatorService',
            'MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository' => 'getSubscriberStatisticsRepositoryService',
            'MailPoet\\Subscribers\\SubscriberActions' => 'getSubscriberActionsService',
            'MailPoet\\Subscribers\\SubscriberCustomFieldRepository' => 'getSubscriberCustomFieldRepositoryService',
            'MailPoet\\Subscribers\\SubscriberListingRepository' => 'getSubscriberListingRepositoryService',
            'MailPoet\\Subscribers\\SubscriberSegmentRepository' => 'getSubscriberSegmentRepositoryService',
            'MailPoet\\Subscribers\\SubscribersRepository' => 'getSubscribersRepositoryService',
            'MailPoet\\Subscription\\Captcha' => 'getCaptchaService',
            'MailPoet\\Subscription\\CaptchaRenderer' => 'getCaptchaRendererService',
            'MailPoet\\Subscription\\CaptchaSession' => 'getCaptchaSessionService',
            'MailPoet\\Subscription\\Comment' => 'getCommentService',
            'MailPoet\\Subscription\\Form' => 'getFormService',
            'MailPoet\\Subscription\\Manage' => 'getManageService',
            'MailPoet\\Subscription\\ManageSubscriptionFormRenderer' => 'getManageSubscriptionFormRendererService',
            'MailPoet\\Subscription\\Pages' => 'getPagesService',
            'MailPoet\\Subscription\\Registration' => 'getRegistrationService',
            'MailPoet\\Subscription\\SubscriptionUrlFactory' => 'getSubscriptionUrlFactoryService',
            'MailPoet\\Tasks\\State' => 'getStateService',
            'MailPoet\\Util\\CdnAssetUrl' => 'getCdnAssetUrlService',
            'MailPoet\\Util\\Cookies' => 'getCookiesService',
            'MailPoet\\Util\\Installation' => 'getInstallationService',
            'MailPoet\\Util\\License\\Features\\Subscribers' => 'getSubscribers3Service',
            'MailPoet\\Util\\License\\License' => 'getLicenseService',
            'MailPoet\\Util\\Notices\\PermanentNotices' => 'getPermanentNoticesService',
            'MailPoet\\Util\\Url' => 'getUrlService',
            'MailPoet\\WP\\Emoji' => 'getEmojiService',
            'MailPoet\\WP\\Functions' => 'getFunctionsService',
            'MailPoet\\WooCommerce\\Helper' => 'getHelperService',
            'MailPoet\\WooCommerce\\Settings' => 'getSettings3Service',
            'MailPoet\\WooCommerce\\Subscription' => 'getSubscription2Service',
            'MailPoet\\WooCommerce\\TransactionalEmailHooks' => 'getTransactionalEmailHooksService',
            'MailPoet\\WooCommerce\\TransactionalEmails' => 'getTransactionalEmailsService',
            'MailPoet\\WooCommerce\\TransactionalEmails\\Renderer' => 'getRenderer6Service',
            'MailPoet\\WooCommerce\\TransactionalEmails\\Template' => 'getTemplateService',
        ];
        $this->privates = [
            'MailPoetVendor\\CSS' => true,
            'MailPoetVendor\\Doctrine\\ORM\\Configuration' => true,
            'MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface' => true,
            'MailPoetVendor\\csstidy' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder' => true,
            'MailPoet\\Config\\DatabaseInitializer' => true,
            'MailPoet\\Config\\Localizer' => true,
            'MailPoet\\Config\\MP2Migrator' => true,
            'MailPoet\\Config\\Populator' => true,
            'MailPoet\\Config\\ServicesChecker' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository' => true,
            'MailPoet\\CustomFields\\ApiDataSanitizer' => true,
            'MailPoet\\CustomFields\\CustomFieldsRepository' => true,
            'MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider' => true,
            'MailPoet\\Doctrine\\ConfigurationFactory' => true,
            'MailPoet\\Doctrine\\ConnectionFactory' => true,
            'MailPoet\\Doctrine\\EntityManagerFactory' => true,
            'MailPoet\\Doctrine\\EventListeners\\TimestampListener' => true,
            'MailPoet\\Doctrine\\EventListeners\\ValidationListener' => true,
            'MailPoet\\Doctrine\\Validator\\ValidatorFactory' => true,
            'MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments' => true,
            'MailPoet\\DynamicSegments\\Mappers\\DBMapper' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SingleSegmentLoader' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersIds' => true,
            'MailPoet\\Features\\FeaturesController' => true,
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
            'MailPoet\\Form\\Util\\CustomFonts' => true,
            'MailPoet\\Form\\Util\\Styles' => true,
            'MailPoet\\Helpscout\\Beacon' => true,
            'MailPoet\\Logging\\LoggerFactory' => true,
            'MailPoet\\Mailer\\Mailer' => true,
            'MailPoet\\Mailer\\MetaInfo' => true,
            'MailPoet\\Mailer\\Methods\\Common\\BlacklistCheck' => true,
            'MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer' => true,
            'MailPoet\\Newsletter\\Preview\\SendPreviewController' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Button' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Divider' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Footer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Header' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Image' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Social' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Text' => true,
            'MailPoet\\Newsletter\\Renderer\\Columns\\Renderer' => true,
            'MailPoet\\Newsletter\\Renderer\\Preprocessor' => true,
            'MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler' => true,
            'MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository' => true,
            'MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository' => true,
            'MailPoet\\Newsletter\\Sending\\SendingQueuesRepository' => true,
            'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository' => true,
            'MailPoet\\PostEditorBlocks\\PostEditorBlock' => true,
            'MailPoet\\PostEditorBlocks\\SubscriptionFormBlock' => true,
            'MailPoet\\Referrals\\ReferralDetector' => true,
            'MailPoet\\Router\\Router' => true,
            'MailPoet\\Segments\\SegmentsRepository' => true,
            'MailPoet\\Services\\AuthorizedEmailsController' => true,
            'MailPoet\\Services\\CongratulatoryMssEmailController' => true,
            'MailPoet\\Settings\\UserFlagsController' => true,
            'MailPoet\\Statistics\\StatisticsUnsubscribesRepository' => true,
            'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository' => true,
            'MailPoet\\Statistics\\Track\\Clicks' => true,
            'MailPoet\\Statistics\\Track\\Opens' => true,
            'MailPoet\\Statistics\\Track\\WooCommercePurchases' => true,
            'MailPoet\\Subscribers\\InactiveSubscribersController' => true,
            'MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository' => true,
            'MailPoet\\Subscribers\\SubscriberCustomFieldRepository' => true,
            'MailPoet\\Subscribers\\SubscriberSegmentRepository' => true,
            'MailPoet\\Subscription\\CaptchaSession' => true,
            'MailPoet\\Tasks\\State' => true,
            'MailPoet\\Util\\Cookies' => true,
            'MailPoet\\Util\\Installation' => true,
            'MailPoet\\Util\\License\\Features\\Subscribers' => true,
            'MailPoet\\Util\\License\\License' => true,
            'MailPoet\\Util\\Notices\\PermanentNotices' => true,
        ];

        $this->aliases = [];
    }

    public function getRemovedIds()
    {
        return [
            'MailPoetVendor\\CSS' => true,
            'MailPoetVendor\\Doctrine\\ORM\\Configuration' => true,
            'MailPoetVendor\\Psr\\Container\\ContainerInterface' => true,
            'MailPoetVendor\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => true,
            'MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface' => true,
            'MailPoetVendor\\csstidy' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder' => true,
            'MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder' => true,
            'MailPoet\\Config\\DatabaseInitializer' => true,
            'MailPoet\\Config\\Localizer' => true,
            'MailPoet\\Config\\MP2Migrator' => true,
            'MailPoet\\Config\\Populator' => true,
            'MailPoet\\Config\\ServicesChecker' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler' => true,
            'MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository' => true,
            'MailPoet\\CustomFields\\ApiDataSanitizer' => true,
            'MailPoet\\CustomFields\\CustomFieldsRepository' => true,
            'MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider' => true,
            'MailPoet\\Doctrine\\ConfigurationFactory' => true,
            'MailPoet\\Doctrine\\ConnectionFactory' => true,
            'MailPoet\\Doctrine\\EntityManagerFactory' => true,
            'MailPoet\\Doctrine\\EventListeners\\TimestampListener' => true,
            'MailPoet\\Doctrine\\EventListeners\\ValidationListener' => true,
            'MailPoet\\Doctrine\\Validator\\ValidatorFactory' => true,
            'MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments' => true,
            'MailPoet\\DynamicSegments\\Mappers\\DBMapper' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SingleSegmentLoader' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount' => true,
            'MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersIds' => true,
            'MailPoet\\Features\\FeaturesController' => true,
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
            'MailPoet\\Form\\Util\\CustomFonts' => true,
            'MailPoet\\Form\\Util\\Styles' => true,
            'MailPoet\\Helpscout\\Beacon' => true,
            'MailPoet\\Logging\\LoggerFactory' => true,
            'MailPoet\\Mailer\\Mailer' => true,
            'MailPoet\\Mailer\\MetaInfo' => true,
            'MailPoet\\Mailer\\Methods\\Common\\BlacklistCheck' => true,
            'MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer' => true,
            'MailPoet\\Newsletter\\Preview\\SendPreviewController' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Button' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Divider' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Footer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Header' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Image' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Social' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer' => true,
            'MailPoet\\Newsletter\\Renderer\\Blocks\\Text' => true,
            'MailPoet\\Newsletter\\Renderer\\Columns\\Renderer' => true,
            'MailPoet\\Newsletter\\Renderer\\Preprocessor' => true,
            'MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler' => true,
            'MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository' => true,
            'MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository' => true,
            'MailPoet\\Newsletter\\Sending\\SendingQueuesRepository' => true,
            'MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository' => true,
            'MailPoet\\PostEditorBlocks\\PostEditorBlock' => true,
            'MailPoet\\PostEditorBlocks\\SubscriptionFormBlock' => true,
            'MailPoet\\Referrals\\ReferralDetector' => true,
            'MailPoet\\Router\\Router' => true,
            'MailPoet\\Segments\\SegmentsRepository' => true,
            'MailPoet\\Services\\AuthorizedEmailsController' => true,
            'MailPoet\\Services\\CongratulatoryMssEmailController' => true,
            'MailPoet\\Settings\\UserFlagsController' => true,
            'MailPoet\\Statistics\\StatisticsUnsubscribesRepository' => true,
            'MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository' => true,
            'MailPoet\\Statistics\\Track\\Clicks' => true,
            'MailPoet\\Statistics\\Track\\Opens' => true,
            'MailPoet\\Statistics\\Track\\WooCommercePurchases' => true,
            'MailPoet\\Subscribers\\InactiveSubscribersController' => true,
            'MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository' => true,
            'MailPoet\\Subscribers\\SubscriberCustomFieldRepository' => true,
            'MailPoet\\Subscribers\\SubscriberSegmentRepository' => true,
            'MailPoet\\Subscription\\CaptchaSession' => true,
            'MailPoet\\Tasks\\State' => true,
            'MailPoet\\Util\\Cookies' => true,
            'MailPoet\\Util\\Installation' => true,
            'MailPoet\\Util\\License\\Features\\Subscribers' => true,
            'MailPoet\\Util\\License\\License' => true,
            'MailPoet\\Util\\Notices\\PermanentNotices' => true,
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
     * Gets the public 'MailPoetVendor\Doctrine\DBAL\Connection' shared autowired service.
     *
     * @return \MailPoetVendor\Doctrine\DBAL\Connection
     */
    protected function getConnectionService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection'] = ${($_ = isset($this->services['MailPoet\\Doctrine\\ConnectionFactory']) ? $this->services['MailPoet\\Doctrine\\ConnectionFactory'] : ($this->services['MailPoet\\Doctrine\\ConnectionFactory'] = new \MailPoet\Doctrine\ConnectionFactory())) && false ?: '_'}->createConnection();
    }

    /**
     * Gets the public 'MailPoetVendor\Doctrine\ORM\EntityManager' shared autowired service.
     *
     * @return \MailPoetVendor\Doctrine\ORM\EntityManager
     */
    protected function getEntityManagerService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] = ${($_ = isset($this->services['MailPoet\\Doctrine\\EntityManagerFactory']) ? $this->services['MailPoet\\Doctrine\\EntityManagerFactory'] : $this->getEntityManagerFactoryService()) && false ?: '_'}->createEntityManager();
    }

    /**
     * Gets the public 'MailPoet\API\JSON\API' shared autowired service.
     *
     * @return \MailPoet\API\JSON\API
     */
    protected function getAPIService()
    {
        return $this->services['MailPoet\\API\\JSON\\API'] = new \MailPoet\API\JSON\API(${($_ = isset($this->services['MailPoet\\DI\\ContainerWrapper']) ? $this->services['MailPoet\\DI\\ContainerWrapper'] : $this->getContainerWrapperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\AccessControl']) ? $this->services['MailPoet\\Config\\AccessControl'] : ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ErrorHandler']) ? $this->services['MailPoet\\API\\JSON\\ErrorHandler'] : $this->getErrorHandlerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ErrorHandler' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ErrorHandler
     */
    protected function getErrorHandlerService()
    {
        return $this->services['MailPoet\\API\\JSON\\ErrorHandler'] = new \MailPoet\API\JSON\ErrorHandler(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder
     */
    protected function getSubscribersResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository']) ? $this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] : $this->getSubscriberSegmentRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\CustomFields\\CustomFieldsRepository']) ? $this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] : $this->getCustomFieldsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository']) ? $this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository'] : $this->getSubscriberCustomFieldRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\StatisticsUnsubscribesRepository']) ? $this->services['MailPoet\\Statistics\\StatisticsUnsubscribesRepository'] : $this->getStatisticsUnsubscribesRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Analytics' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Analytics
     */
    protected function getAnalyticsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Analytics'] = new \MailPoet\API\JSON\v1\Analytics(${($_ = isset($this->services['MailPoet\\Analytics\\Reporter']) ? $this->services['MailPoet\\Analytics\\Reporter'] : $this->getReporterService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\AutomatedLatestContent' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\AutomatedLatestContent
     */
    protected function getAutomatedLatestContentService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\AutomatedLatestContent'] = new \MailPoet\API\JSON\v1\AutomatedLatestContent(${($_ = isset($this->services['MailPoet\\Newsletter\\AutomatedLatestContent']) ? $this->services['MailPoet\\Newsletter\\AutomatedLatestContent'] : $this->getAutomatedLatestContent2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\AutomaticEmails' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\AutomaticEmails
     */
    protected function getAutomaticEmailsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\AutomaticEmails'] = new \MailPoet\API\JSON\v1\AutomaticEmails();
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\CustomFields' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\CustomFields
     */
    protected function getCustomFieldsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\CustomFields'] = new \MailPoet\API\JSON\v1\CustomFields(${($_ = isset($this->services['MailPoet\\CustomFields\\CustomFieldsRepository']) ? $this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] : $this->getCustomFieldsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] : ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\DynamicSegments' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\DynamicSegments
     */
    protected function getDynamicSegmentsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\DynamicSegments'] = new \MailPoet\API\JSON\v1\DynamicSegments(${($_ = isset($this->services['MailPoet\\Listing\\BulkActionController']) ? $this->services['MailPoet\\Listing\\BulkActionController'] : $this->getBulkActionControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SegmentSubscribersRepository']) ? $this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] : $this->getSegmentSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\FeatureFlags' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\FeatureFlags
     */
    protected function getFeatureFlagsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\FeatureFlags'] = new \MailPoet\API\JSON\v1\FeatureFlags(${($_ = isset($this->services['MailPoet\\Features\\FeaturesController']) ? $this->services['MailPoet\\Features\\FeaturesController'] : $this->getFeaturesControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Features\\FeatureFlagsController']) ? $this->services['MailPoet\\Features\\FeatureFlagsController'] : $this->getFeatureFlagsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Forms' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Forms
     */
    protected function getFormsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Forms'] = new \MailPoet\API\JSON\v1\Forms(${($_ = isset($this->services['MailPoet\\Listing\\BulkActionController']) ? $this->services['MailPoet\\Listing\\BulkActionController'] : $this->getBulkActionControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\UserFlagsController']) ? $this->services['MailPoet\\Settings\\UserFlagsController'] : $this->getUserFlagsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormFactory']) ? $this->services['MailPoet\\Form\\FormFactory'] : $this->getFormFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormsRepository']) ? $this->services['MailPoet\\Form\\FormsRepository'] : $this->getFormsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder'] : ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Emoji']) ? $this->services['MailPoet\\WP\\Emoji'] : $this->getEmojiService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\ImportExport' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\ImportExport
     */
    protected function getImportExportService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\ImportExport'] = new \MailPoet\API\JSON\v1\ImportExport();
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\MP2Migrator' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\MP2Migrator
     */
    protected function getMP2MigratorService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\MP2Migrator'] = new \MailPoet\API\JSON\v1\MP2Migrator(${($_ = isset($this->services['MailPoet\\Config\\MP2Migrator']) ? $this->services['MailPoet\\Config\\MP2Migrator'] : $this->getMP2Migrator2Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Mailer' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Mailer
     */
    protected function getMailerService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Mailer'] = new \MailPoet\API\JSON\v1\Mailer(${($_ = isset($this->services['MailPoet\\Services\\AuthorizedEmailsController']) ? $this->services['MailPoet\\Services\\AuthorizedEmailsController'] : $this->getAuthorizedEmailsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Services\\Bridge']) ? $this->services['MailPoet\\Services\\Bridge'] : $this->getBridgeService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\MetaInfo']) ? $this->services['MailPoet\\Mailer\\MetaInfo'] : ($this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\NewsletterLinks' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\NewsletterLinks
     */
    protected function getNewsletterLinksService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\NewsletterLinks'] = new \MailPoet\API\JSON\v1\NewsletterLinks();
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\NewsletterTemplates' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\NewsletterTemplates
     */
    protected function getNewsletterTemplatesService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\NewsletterTemplates'] = new \MailPoet\API\JSON\v1\NewsletterTemplates(${($_ = isset($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository']) ? $this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] : $this->getNewsletterTemplatesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder'] : ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\NewsletterTemplatesResponseBuilder())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Newsletters' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Newsletters
     */
    protected function getNewslettersService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Newsletters'] = new \MailPoet\API\JSON\v1\Newsletters(${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Listing\\NewsletterListingRepository']) ? $this->services['MailPoet\\Newsletter\\Listing\\NewsletterListingRepository'] : $this->getNewsletterListingRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder'] : $this->getNewslettersResponseBuilderService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] = new \MailPoet\Newsletter\Scheduler\PostNotificationScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Emoji']) ? $this->services['MailPoet\\WP\\Emoji'] : $this->getEmojiService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\License\\Features\\Subscribers']) ? $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] : $this->getSubscribers3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Preview\\SendPreviewController']) ? $this->services['MailPoet\\Newsletter\\Preview\\SendPreviewController'] : $this->getSendPreviewControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewsletterSaveController']) ? $this->services['MailPoet\\Newsletter\\NewsletterSaveController'] : $this->getNewsletterSaveControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Premium' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Premium
     */
    protected function getPremiumService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Premium'] = new \MailPoet\API\JSON\v1\Premium(${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Segments' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Segments
     */
    protected function getSegmentsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Segments'] = new \MailPoet\API\JSON\v1\Segments(${($_ = isset($this->services['MailPoet\\Listing\\BulkActionController']) ? $this->services['MailPoet\\Listing\\BulkActionController'] : $this->getBulkActionControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SegmentsRepository']) ? $this->services['MailPoet\\Segments\\SegmentsRepository'] : $this->getSegmentsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] : ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\WooCommerce']) ? $this->services['MailPoet\\Segments\\WooCommerce'] : $this->getWooCommerceService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\SendingQueue' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\SendingQueue
     */
    protected function getSendingQueueService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\SendingQueue'] = new \MailPoet\API\JSON\v1\SendingQueue(${($_ = isset($this->services['MailPoet\\Util\\License\\Features\\Subscribers']) ? $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] : $this->getSubscribers3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SubscribersFinder']) ? $this->services['MailPoet\\Segments\\SubscribersFinder'] : $this->getSubscribersFinderService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\SendingTaskSubscribers' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\SendingTaskSubscribers
     */
    protected function getSendingTaskSubscribersService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\SendingTaskSubscribers'] = new \MailPoet\API\JSON\v1\SendingTaskSubscribers(${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Services' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Services
     */
    protected function getServicesService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Services'] = new \MailPoet\API\JSON\v1\Services(${($_ = isset($this->services['MailPoet\\Services\\Bridge']) ? $this->services['MailPoet\\Services\\Bridge'] : $this->getBridgeService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Analytics\\Analytics']) ? $this->services['MailPoet\\Analytics\\Analytics'] : $this->getAnalytics2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck']) ? $this->services['MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck'] : $this->getSendingServiceKeyCheckService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck']) ? $this->services['MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck'] : $this->getPremiumKeyCheckService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Services\\CongratulatoryMssEmailController']) ? $this->services['MailPoet\\Services\\CongratulatoryMssEmailController'] : $this->getCongratulatoryMssEmailControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Settings' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Settings
     */
    protected function getSettingsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Settings'] = new \MailPoet\API\JSON\v1\Settings(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Services\\Bridge']) ? $this->services['MailPoet\\Services\\Bridge'] : $this->getBridgeService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Services\\AuthorizedEmailsController']) ? $this->services['MailPoet\\Services\\AuthorizedEmailsController'] : $this->getAuthorizedEmailsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\TransactionalEmails']) ? $this->services['MailPoet\\WooCommerce\\TransactionalEmails'] : $this->getTransactionalEmailsService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Setup' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Setup
     */
    protected function getSetupService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Setup'] = new \MailPoet\API\JSON\v1\Setup(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Activator']) ? $this->services['MailPoet\\Config\\Activator'] : $this->getActivatorService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\SubscriberStats' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\SubscriberStats
     */
    protected function getSubscriberStatsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\SubscriberStats'] = new \MailPoet\API\JSON\v1\SubscriberStats(${($_ = isset($this->services['MailPoet\\Subscribers\\SubscribersRepository']) ? $this->services['MailPoet\\Subscribers\\SubscribersRepository'] : $this->getSubscribersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository']) ? $this->services['MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository'] : $this->getSubscriberStatisticsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\Subscribers' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\Subscribers
     */
    protected function getSubscribersService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\Subscribers'] = new \MailPoet\API\JSON\v1\Subscribers(${($_ = isset($this->services['MailPoet\\Subscribers\\SubscriberActions']) ? $this->services['MailPoet\\Subscribers\\SubscriberActions'] : $this->getSubscriberActionsService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator']) ? $this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] : ($this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] = new \MailPoet\Subscribers\RequiredCustomFieldValidator())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\Captcha']) ? $this->services['MailPoet\\Subscription\\Captcha'] : $this->getCaptchaService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaSession']) ? $this->services['MailPoet\\Subscription\\CaptchaSession'] : $this->getCaptchaSessionService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer']) ? $this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] : $this->getConfirmationEmailMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Unsubscribes']) ? $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] : $this->getUnsubscribesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscribersRepository']) ? $this->services['MailPoet\\Subscribers\\SubscribersRepository'] : $this->getSubscribersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SubscribersResponseBuilder'] : $this->getSubscribersResponseBuilderService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscriberListingRepository']) ? $this->services['MailPoet\\Subscribers\\SubscriberListingRepository'] : $this->getSubscriberListingRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SegmentsRepository']) ? $this->services['MailPoet\\Segments\\SegmentsRepository'] : $this->getSegmentsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Util\\FieldNameObfuscator']) ? $this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] : $this->getFieldNameObfuscatorService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\UserFlags' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\UserFlags
     */
    protected function getUserFlagsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\UserFlags'] = new \MailPoet\API\JSON\v1\UserFlags(${($_ = isset($this->services['MailPoet\\Settings\\UserFlagsController']) ? $this->services['MailPoet\\Settings\\UserFlagsController'] : $this->getUserFlagsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\JSON\v1\WoocommerceSettings' shared autowired service.
     *
     * @return \MailPoet\API\JSON\v1\WoocommerceSettings
     */
    protected function getWoocommerceSettingsService()
    {
        return $this->services['MailPoet\\API\\JSON\\v1\\WoocommerceSettings'] = new \MailPoet\API\JSON\v1\WoocommerceSettings(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\API\MP\v1\API' shared autowired service.
     *
     * @return \MailPoet\API\MP\v1\API
     */
    protected function getAPI2Service()
    {
        return $this->services['MailPoet\\API\\MP\\v1\\API'] = new \MailPoet\API\MP\v1\API(${($_ = isset($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer']) ? $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] : $this->getNewSubscriberNotificationMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer']) ? $this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] : $this->getConfirmationEmailMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator']) ? $this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] : ($this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] = new \MailPoet\Subscribers\RequiredCustomFieldValidator())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\CustomFields\\ApiDataSanitizer']) ? $this->services['MailPoet\\CustomFields\\ApiDataSanitizer'] : ($this->services['MailPoet\\CustomFields\\ApiDataSanitizer'] = new \MailPoet\CustomFields\ApiDataSanitizer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\PageRenderer' shared autowired service.
     *
     * @return \MailPoet\AdminPages\PageRenderer
     */
    protected function getPageRendererService()
    {
        return $this->services['MailPoet\\AdminPages\\PageRenderer'] = new \MailPoet\AdminPages\PageRenderer(${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Features\\FeaturesController']) ? $this->services['MailPoet\\Features\\FeaturesController'] : $this->getFeaturesControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\ExperimentalFeatures' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\ExperimentalFeatures
     */
    protected function getExperimentalFeaturesService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\ExperimentalFeatures'] = new \MailPoet\AdminPages\Pages\ExperimentalFeatures(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\FormEditor' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\FormEditor
     */
    protected function getFormEditorService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\FormEditor'] = new \MailPoet\AdminPages\Pages\FormEditor(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\CustomFields\\CustomFieldsRepository']) ? $this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] : $this->getCustomFieldsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder']) ? $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] : ($this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Renderer']) ? $this->services['MailPoet\\Form\\Renderer'] : $this->getRenderer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Date']) ? $this->services['MailPoet\\Form\\Block\\Date'] : $this->getDateService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormFactory']) ? $this->services['MailPoet\\Form\\FormFactory'] : $this->getFormFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Localizer']) ? $this->services['MailPoet\\Config\\Localizer'] : ($this->services['MailPoet\\Config\\Localizer'] = new \MailPoet\Config\Localizer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Templates\\TemplateRepository']) ? $this->services['MailPoet\\Form\\Templates\\TemplateRepository'] : $this->getTemplateRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Forms' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Forms
     */
    protected function getForms2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Forms'] = new \MailPoet\AdminPages\Pages\Forms(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\PageLimit']) ? $this->services['MailPoet\\Listing\\PageLimit'] : $this->getPageLimitService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Installation']) ? $this->services['MailPoet\\Util\\Installation'] : $this->getInstallationService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\UserFlagsController']) ? $this->services['MailPoet\\Settings\\UserFlagsController'] : $this->getUserFlagsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Help' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Help
     */
    protected function getHelpService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Help'] = new \MailPoet\AdminPages\Pages\Help(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Tasks\\State']) ? $this->services['MailPoet\\Tasks\\State'] : ($this->services['MailPoet\\Tasks\\State'] = new \MailPoet\Tasks\State())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Helpscout\\Beacon']) ? $this->services['MailPoet\\Helpscout\\Beacon'] : $this->getBeaconService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\MP2Migration' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\MP2Migration
     */
    protected function getMP2MigrationService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\MP2Migration'] = new \MailPoet\AdminPages\Pages\MP2Migration(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\MP2Migrator']) ? $this->services['MailPoet\\Config\\MP2Migrator'] : $this->getMP2Migrator2Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\NewsletterEditor' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\NewsletterEditor
     */
    protected function getNewsletterEditorService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\NewsletterEditor'] = new \MailPoet\AdminPages\Pages\NewsletterEditor(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\UserFlagsController']) ? $this->services['MailPoet\\Settings\\UserFlagsController'] : $this->getUserFlagsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\TransactionalEmails']) ? $this->services['MailPoet\\WooCommerce\\TransactionalEmails'] : $this->getTransactionalEmailsService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Newsletters' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Newsletters
     */
    protected function getNewsletters2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Newsletters'] = new \MailPoet\AdminPages\Pages\Newsletters(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\PageLimit']) ? $this->services['MailPoet\\Listing\\PageLimit'] : $this->getPageLimitService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\UserFlagsController']) ? $this->services['MailPoet\\Settings\\UserFlagsController'] : $this->getUserFlagsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Installation']) ? $this->services['MailPoet\\Util\\Installation'] : $this->getInstallationService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Features\\FeaturesController']) ? $this->services['MailPoet\\Features\\FeaturesController'] : $this->getFeaturesControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\License\\Features\\Subscribers']) ? $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] : $this->getSubscribers3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository']) ? $this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] : $this->getNewsletterTemplatesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments']) ? $this->services['MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments'] : $this->getAddToNewslettersSegmentsService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Premium' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Premium
     */
    protected function getPremium2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Premium'] = new \MailPoet\AdminPages\Pages\Premium(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Segments' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Segments
     */
    protected function getSegments2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Segments'] = new \MailPoet\AdminPages\Pages\Segments(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\PageLimit']) ? $this->services['MailPoet\\Listing\\PageLimit'] : $this->getPageLimitService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\License\\Features\\Subscribers']) ? $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] : $this->getSubscribers3Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Settings' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Settings
     */
    protected function getSettings2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Settings'] = new \MailPoet\AdminPages\Pages\Settings(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Installation']) ? $this->services['MailPoet\\Util\\Installation'] : $this->getInstallationService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\Captcha']) ? $this->services['MailPoet\\Subscription\\Captcha'] : $this->getCaptchaService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Subscribers' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Subscribers
     */
    protected function getSubscribers2Service()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Subscribers'] = new \MailPoet\AdminPages\Pages\Subscribers(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\PageLimit']) ? $this->services['MailPoet\\Listing\\PageLimit'] : $this->getPageLimitService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\License\\Features\\Subscribers']) ? $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] : $this->getSubscribers3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Date']) ? $this->services['MailPoet\\Form\\Block\\Date'] : $this->getDateService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments']) ? $this->services['MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments'] : $this->getAddToNewslettersSegmentsService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\SubscribersExport' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\SubscribersExport
     */
    protected function getSubscribersExportService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\SubscribersExport'] = new \MailPoet\AdminPages\Pages\SubscribersExport(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\SubscribersImport' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\SubscribersImport
     */
    protected function getSubscribersImportService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\SubscribersImport'] = new \MailPoet\AdminPages\Pages\SubscribersImport(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Installation']) ? $this->services['MailPoet\\Util\\Installation'] : $this->getInstallationService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Date']) ? $this->services['MailPoet\\Form\\Block\\Date'] : $this->getDateService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\Update' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\Update
     */
    protected function getUpdateService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\Update'] = new \MailPoet\AdminPages\Pages\Update(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\WelcomeWizard' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\WelcomeWizard
     */
    protected function getWelcomeWizardService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\WelcomeWizard'] = new \MailPoet\AdminPages\Pages\WelcomeWizard(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Features\\FeaturesController']) ? $this->services['MailPoet\\Features\\FeaturesController'] : $this->getFeaturesControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\AdminPages\Pages\WooCommerceSetup' shared autowired service.
     *
     * @return \MailPoet\AdminPages\Pages\WooCommerceSetup
     */
    protected function getWooCommerceSetupService()
    {
        return $this->services['MailPoet\\AdminPages\\Pages\\WooCommerceSetup'] = new \MailPoet\AdminPages\Pages\WooCommerceSetup(${($_ = isset($this->services['MailPoet\\AdminPages\\PageRenderer']) ? $this->services['MailPoet\\AdminPages\\PageRenderer'] : $this->getPageRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Analytics\Analytics' shared autowired service.
     *
     * @return \MailPoet\Analytics\Analytics
     */
    protected function getAnalytics2Service()
    {
        return $this->services['MailPoet\\Analytics\\Analytics'] = new \MailPoet\Analytics\Analytics(${($_ = isset($this->services['MailPoet\\Analytics\\Reporter']) ? $this->services['MailPoet\\Analytics\\Reporter'] : $this->getReporterService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Analytics\Reporter' shared autowired service.
     *
     * @return \MailPoet\Analytics\Reporter
     */
    protected function getReporterService()
    {
        return $this->services['MailPoet\\Analytics\\Reporter'] = new \MailPoet\Analytics\Reporter(${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SegmentsRepository']) ? $this->services['MailPoet\\Segments\\SegmentsRepository'] : $this->getSegmentsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
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
        return $this->services['MailPoet\\Config\\Activator'] = new \MailPoet\Config\Activator(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Populator']) ? $this->services['MailPoet\\Config\\Populator'] : $this->getPopulatorService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Config\Changelog' shared autowired service.
     *
     * @return \MailPoet\Config\Changelog
     */
    protected function getChangelogService()
    {
        return $this->services['MailPoet\\Config\\Changelog'] = new \MailPoet\Config\Changelog(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Url']) ? $this->services['MailPoet\\Util\\Url'] : $this->getUrlService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\MP2Migrator']) ? $this->services['MailPoet\\Config\\MP2Migrator'] : $this->getMP2Migrator2Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Config\Hooks' shared autowired service.
     *
     * @return \MailPoet\Config\Hooks
     */
    protected function getHooksService()
    {
        return $this->services['MailPoet\\Config\\Hooks'] = new \MailPoet\Config\Hooks(${($_ = isset($this->services['MailPoet\\Subscription\\Form']) ? $this->services['MailPoet\\Subscription\\Form'] : $this->getFormService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\Comment']) ? $this->services['MailPoet\\Subscription\\Comment'] : $this->getCommentService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\Manage']) ? $this->services['MailPoet\\Subscription\\Manage'] : $this->getManageService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\Registration']) ? $this->services['MailPoet\\Subscription\\Registration'] : $this->getRegistrationService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Subscription']) ? $this->services['MailPoet\\WooCommerce\\Subscription'] : $this->getSubscription2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\WooCommerce']) ? $this->services['MailPoet\\Segments\\WooCommerce'] : $this->getWooCommerceService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Settings']) ? $this->services['MailPoet\\WooCommerce\\Settings'] : $this->getSettings3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\WooCommercePurchases']) ? $this->services['MailPoet\\Statistics\\Track\\WooCommercePurchases'] : $this->getWooCommercePurchasesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] = new \MailPoet\Newsletter\Scheduler\PostNotificationScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer']) ? $this->services['MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer'] : $this->getWordpressMailerReplacerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\DisplayFormInWPContent']) ? $this->services['MailPoet\\Form\\DisplayFormInWPContent'] : $this->getDisplayFormInWPContentService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Config\Initializer' shared autowired service.
     *
     * @return \MailPoet\Config\Initializer
     */
    protected function getInitializerService()
    {
        $a = ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'};
        $b = ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'};

        return $this->services['MailPoet\\Config\\Initializer'] = new \MailPoet\Config\Initializer(${($_ = isset($this->services['MailPoet\\Config\\RendererFactory']) ? $this->services['MailPoet\\Config\\RendererFactory'] : ($this->services['MailPoet\\Config\\RendererFactory'] = new \MailPoet\Config\RendererFactory())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\AccessControl']) ? $this->services['MailPoet\\Config\\AccessControl'] : ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\API\\JSON\\API']) ? $this->services['MailPoet\\API\\JSON\\API'] : $this->getAPIService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Activator']) ? $this->services['MailPoet\\Config\\Activator'] : $this->getActivatorService()) && false ?: '_'}, $a, ${($_ = isset($this->services['MailPoet\\Router\\Router']) ? $this->services['MailPoet\\Router\\Router'] : $this->getRouterService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Hooks']) ? $this->services['MailPoet\\Config\\Hooks'] : $this->getHooksService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Changelog']) ? $this->services['MailPoet\\Config\\Changelog'] : $this->getChangelogService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Menu']) ? $this->services['MailPoet\\Config\\Menu'] : $this->getMenuService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronTrigger']) ? $this->services['MailPoet\\Cron\\CronTrigger'] : $this->getCronTriggerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Notices\\PermanentNotices']) ? $this->services['MailPoet\\Util\\Notices\\PermanentNotices'] : $this->getPermanentNoticesService()) && false ?: '_'}, new \MailPoet\Config\Shortcodes(new \MailPoet\Subscription\Pages(${($_ = isset($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer']) ? $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] : $this->getNewSubscriberNotificationMailerService()) && false ?: '_'}, $b, $a, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaRenderer']) ? $this->services['MailPoet\\Subscription\\CaptchaRenderer'] : $this->getCaptchaRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\AssetsController']) ? $this->services['MailPoet\\Form\\AssetsController'] : $this->getAssetsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Unsubscribes']) ? $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] : $this->getUnsubscribesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer']) ? $this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] : $this->getManageSubscriptionFormRendererService()) && false ?: '_'}), $b), ${($_ = isset($this->services['MailPoet\\Config\\DatabaseInitializer']) ? $this->services['MailPoet\\Config\\DatabaseInitializer'] : ($this->services['MailPoet\\Config\\DatabaseInitializer'] = new \MailPoet\Config\DatabaseInitializer($this))) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\TransactionalEmailHooks']) ? $this->services['MailPoet\\WooCommerce\\TransactionalEmailHooks'] : $this->getTransactionalEmailHooksService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\PostEditorBlocks\\PostEditorBlock']) ? $this->services['MailPoet\\PostEditorBlocks\\PostEditorBlock'] : $this->getPostEditorBlockService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Localizer']) ? $this->services['MailPoet\\Config\\Localizer'] : ($this->services['MailPoet\\Config\\Localizer'] = new \MailPoet\Config\Localizer())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Config\Menu' shared autowired service.
     *
     * @return \MailPoet\Config\Menu
     */
    protected function getMenuService()
    {
        return $this->services['MailPoet\\Config\\Menu'] = new \MailPoet\Config\Menu(${($_ = isset($this->services['MailPoet\\Config\\AccessControl']) ? $this->services['MailPoet\\Config\\AccessControl'] : ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\DI\\ContainerWrapper']) ? $this->services['MailPoet\\DI\\ContainerWrapper'] : $this->getContainerWrapperService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Config\Renderer' shared service.
     *
     * @return \MailPoet\Config\Renderer
     */
    protected function getRendererService()
    {
        return $this->services['MailPoet\\Config\\Renderer'] = ${($_ = isset($this->services['MailPoet\\Config\\RendererFactory']) ? $this->services['MailPoet\\Config\\RendererFactory'] : ($this->services['MailPoet\\Config\\RendererFactory'] = new \MailPoet\Config\RendererFactory())) && false ?: '_'}->getRenderer();
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
     * Gets the public 'MailPoet\Config\Shortcodes' autowired service.
     *
     * @return \MailPoet\Config\Shortcodes
     */
    protected function getShortcodesService()
    {
        $a = ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'};

        return new \MailPoet\Config\Shortcodes(new \MailPoet\Subscription\Pages(${($_ = isset($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer']) ? $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] : $this->getNewSubscriberNotificationMailerService()) && false ?: '_'}, $a, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaRenderer']) ? $this->services['MailPoet\\Subscription\\CaptchaRenderer'] : $this->getCaptchaRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\AssetsController']) ? $this->services['MailPoet\\Form\\AssetsController'] : $this->getAssetsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Unsubscribes']) ? $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] : $this->getUnsubscribesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer']) ? $this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] : $this->getManageSubscriptionFormRendererService()) && false ?: '_'}), $a);
    }

    /**
     * Gets the public 'MailPoet\Cron\CronHelper' shared autowired service.
     *
     * @return \MailPoet\Cron\CronHelper
     */
    protected function getCronHelperService()
    {
        return $this->services['MailPoet\\Cron\\CronHelper'] = new \MailPoet\Cron\CronHelper(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\CronTrigger' shared autowired service.
     *
     * @return \MailPoet\Cron\CronTrigger
     */
    protected function getCronTriggerService()
    {
        return $this->services['MailPoet\\Cron\\CronTrigger'] = new \MailPoet\Cron\CronTrigger(${($_ = isset($this->services['MailPoet\\Cron\\Triggers\\MailPoet']) ? $this->services['MailPoet\\Cron\\Triggers\\MailPoet'] : $this->getMailPoetService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Triggers\\WordPress']) ? $this->services['MailPoet\\Cron\\Triggers\\WordPress'] : $this->getWordPressService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\CronWorkerRunner' shared autowired service.
     *
     * @return \MailPoet\Cron\CronWorkerRunner
     */
    protected function getCronWorkerRunnerService()
    {
        return $this->services['MailPoet\\Cron\\CronWorkerRunner'] = new \MailPoet\Cron\CronWorkerRunner(${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronWorkerScheduler']) ? $this->services['MailPoet\\Cron\\CronWorkerScheduler'] : $this->getCronWorkerSchedulerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\CronWorkerScheduler' shared autowired service.
     *
     * @return \MailPoet\Cron\CronWorkerScheduler
     */
    protected function getCronWorkerSchedulerService()
    {
        return $this->services['MailPoet\\Cron\\CronWorkerScheduler'] = new \MailPoet\Cron\CronWorkerScheduler(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Daemon' shared autowired service.
     *
     * @return \MailPoet\Cron\Daemon
     */
    protected function getDaemonService()
    {
        return $this->services['MailPoet\\Cron\\Daemon'] = new \MailPoet\Cron\Daemon(${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronWorkerRunner']) ? $this->services['MailPoet\\Cron\\CronWorkerRunner'] : $this->getCronWorkerRunnerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\WorkersFactory']) ? $this->services['MailPoet\\Cron\\Workers\\WorkersFactory'] : $this->getWorkersFactoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\DaemonHttpRunner' shared autowired service.
     *
     * @return \MailPoet\Cron\DaemonHttpRunner
     */
    protected function getDaemonHttpRunnerService()
    {
        return $this->services['MailPoet\\Cron\\DaemonHttpRunner'] = new \MailPoet\Cron\DaemonHttpRunner(${($_ = isset($this->services['MailPoet\\Cron\\Daemon']) ? $this->services['MailPoet\\Cron\\Daemon'] : $this->getDaemonService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Triggers\\WordPress']) ? $this->services['MailPoet\\Cron\\Triggers\\WordPress'] : $this->getWordPressService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Supervisor' shared autowired service.
     *
     * @return \MailPoet\Cron\Supervisor
     */
    protected function getSupervisorService()
    {
        return $this->services['MailPoet\\Cron\\Supervisor'] = new \MailPoet\Cron\Supervisor(${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Triggers\MailPoet' shared autowired service.
     *
     * @return \MailPoet\Cron\Triggers\MailPoet
     */
    protected function getMailPoetService()
    {
        return $this->services['MailPoet\\Cron\\Triggers\\MailPoet'] = new \MailPoet\Cron\Triggers\MailPoet(${($_ = isset($this->services['MailPoet\\Cron\\Supervisor']) ? $this->services['MailPoet\\Cron\\Supervisor'] : $this->getSupervisorService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Triggers\WordPress' shared autowired service.
     *
     * @return \MailPoet\Cron\Triggers\WordPress
     */
    protected function getWordPressService()
    {
        return $this->services['MailPoet\\Cron\\Triggers\\WordPress'] = new \MailPoet\Cron\Triggers\WordPress(${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Triggers\\MailPoet']) ? $this->services['MailPoet\\Cron\\Triggers\\MailPoet'] : $this->getMailPoetService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck
     */
    protected function getAuthorizedSendingEmailsCheckService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\AuthorizedSendingEmailsCheck'] = new \MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck(${($_ = isset($this->services['MailPoet\\Services\\AuthorizedEmailsController']) ? $this->services['MailPoet\\Services\\AuthorizedEmailsController'] : $this->getAuthorizedEmailsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\Beamer' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\Beamer
     */
    protected function getBeamerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\Beamer'] = new \MailPoet\Cron\Workers\Beamer(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\Bounce' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\Bounce
     */
    protected function getBounceService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\Bounce'] = new \MailPoet\Cron\Workers\Bounce(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\ExportFilesCleanup' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\ExportFilesCleanup
     */
    protected function getExportFilesCleanupService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\ExportFilesCleanup'] = new \MailPoet\Cron\Workers\ExportFilesCleanup(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\InactiveSubscribers' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\InactiveSubscribers
     */
    protected function getInactiveSubscribersService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\InactiveSubscribers'] = new \MailPoet\Cron\Workers\InactiveSubscribers(${($_ = isset($this->services['MailPoet\\Subscribers\\InactiveSubscribersController']) ? $this->services['MailPoet\\Subscribers\\InactiveSubscribersController'] : $this->getInactiveSubscribersControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck
     */
    protected function getPremiumKeyCheckService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\KeyCheck\\PremiumKeyCheck'] = new \MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck
     */
    protected function getSendingServiceKeyCheckService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\KeyCheck\\SendingServiceKeyCheck'] = new \MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\ServicesChecker']) ? $this->services['MailPoet\\Config\\ServicesChecker'] : ($this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\Scheduler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\Scheduler
     */
    protected function getSchedulerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\Scheduler'] = new \MailPoet\Cron\Workers\Scheduler(${($_ = isset($this->services['MailPoet\\Segments\\SubscribersFinder']) ? $this->services['MailPoet\\Segments\\SubscribersFinder'] : $this->getSubscribersFinderService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Logging\\LoggerFactory']) ? $this->services['MailPoet\\Logging\\LoggerFactory'] : $this->getLoggerFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\Migration' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\Migration
     */
    protected function getMigrationService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\Migration'] = new \MailPoet\Cron\Workers\SendingQueue\Migration(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler
     */
    protected function getSendingErrorHandlerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler'] = new \MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler();
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SendingQueue\SendingQueue' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SendingQueue\SendingQueue
     */
    protected function getSendingQueue2Service()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingQueue'] = new \MailPoet\Cron\Workers\SendingQueue\SendingQueue(${($_ = isset($this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler']) ? $this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler'] : ($this->services['MailPoet\\Cron\\Workers\\SendingQueue\\SendingErrorHandler'] = new \MailPoet\Cron\Workers\SendingQueue\SendingErrorHandler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler']) ? $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler'] : $this->getScheduler2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Logging\\LoggerFactory']) ? $this->services['MailPoet\\Logging\\LoggerFactory'] : $this->getLoggerFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SubscribersFinder']) ? $this->services['MailPoet\\Segments\\SubscribersFinder'] : $this->getSubscribersFinderService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails
     */
    protected function getAutomatedEmailsService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\AutomatedEmails'] = new \MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository']) ? $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] : $this->getNewsletterStatisticsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\MetaInfo']) ? $this->services['MailPoet\\Mailer\\MetaInfo'] : ($this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\StatsNotifications\Worker' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\Worker
     */
    protected function getWorkerService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\Worker'] = new \MailPoet\Cron\Workers\StatsNotifications\Worker(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\MetaInfo']) ? $this->services['MailPoet\\Mailer\\MetaInfo'] : ($this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository']) ? $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository'] : $this->getStatsNotificationsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository']) ? $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] : $this->getNewsletterLinkRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository']) ? $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] : $this->getNewsletterStatisticsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\License\\Features\\Subscribers']) ? $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] : $this->getSubscribers3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscribersRepository']) ? $this->services['MailPoet\\Subscribers\\SubscribersRepository'] : $this->getSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\SubscriberLinkTokens' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\SubscriberLinkTokens
     */
    protected function getSubscriberLinkTokensService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\SubscriberLinkTokens'] = new \MailPoet\Cron\Workers\SubscriberLinkTokens(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\UnsubscribeTokens' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\UnsubscribeTokens
     */
    protected function getUnsubscribeTokensService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\UnsubscribeTokens'] = new \MailPoet\Cron\Workers\UnsubscribeTokens(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\WooCommercePastOrders' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\WooCommercePastOrders
     */
    protected function getWooCommercePastOrdersService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\WooCommercePastOrders'] = new \MailPoet\Cron\Workers\WooCommercePastOrders(${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\WooCommercePurchases']) ? $this->services['MailPoet\\Statistics\\Track\\WooCommercePurchases'] : $this->getWooCommercePurchasesService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\WooCommerceSync' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\WooCommerceSync
     */
    protected function getWooCommerceSyncService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\WooCommerceSync'] = new \MailPoet\Cron\Workers\WooCommerceSync(${($_ = isset($this->services['MailPoet\\Segments\\WooCommerce']) ? $this->services['MailPoet\\Segments\\WooCommerce'] : $this->getWooCommerceService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Cron\Workers\WorkersFactory' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\WorkersFactory
     */
    protected function getWorkersFactoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\WorkersFactory'] = new \MailPoet\Cron\Workers\WorkersFactory(${($_ = isset($this->services['MailPoet\\DI\\ContainerWrapper']) ? $this->services['MailPoet\\DI\\ContainerWrapper'] : $this->getContainerWrapperService()) && false ?: '_'});
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
     * Gets the public 'MailPoet\Doctrine\EventListeners\EmojiEncodingListener' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EventListeners\EmojiEncodingListener
     */
    protected function getEmojiEncodingListenerService()
    {
        return $this->services['MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener'] = new \MailPoet\Doctrine\EventListeners\EmojiEncodingListener(${($_ = isset($this->services['MailPoet\\WP\\Emoji']) ? $this->services['MailPoet\\WP\\Emoji'] : $this->getEmojiService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Features\FeatureFlagsController' shared autowired service.
     *
     * @return \MailPoet\Features\FeatureFlagsController
     */
    protected function getFeatureFlagsControllerService()
    {
        return $this->services['MailPoet\\Features\\FeatureFlagsController'] = new \MailPoet\Features\FeatureFlagsController(${($_ = isset($this->services['MailPoet\\Features\\FeaturesController']) ? $this->services['MailPoet\\Features\\FeaturesController'] : $this->getFeaturesControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Features\\FeatureFlagsRepository']) ? $this->services['MailPoet\\Features\\FeatureFlagsRepository'] : $this->getFeatureFlagsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Features\FeatureFlagsRepository' shared autowired service.
     *
     * @return \MailPoet\Features\FeatureFlagsRepository
     */
    protected function getFeatureFlagsRepositoryService()
    {
        return $this->services['MailPoet\\Features\\FeatureFlagsRepository'] = new \MailPoet\Features\FeatureFlagsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Form\AssetsController' shared autowired service.
     *
     * @return \MailPoet\Form\AssetsController
     */
    protected function getAssetsControllerService()
    {
        return $this->services['MailPoet\\Form\\AssetsController'] = new \MailPoet\Form\AssetsController(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Form\Block\Date' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Date
     */
    protected function getDateService()
    {
        return $this->services['MailPoet\\Form\\Block\\Date'] = new \MailPoet\Form\Block\Date(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockStylesRenderer']) ? $this->services['MailPoet\\Form\\BlockStylesRenderer'] : ($this->services['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Form\FormFactory' shared autowired service.
     *
     * @return \MailPoet\Form\FormFactory
     */
    protected function getFormFactoryService()
    {
        return $this->services['MailPoet\\Form\\FormFactory'] = new \MailPoet\Form\FormFactory(${($_ = isset($this->services['MailPoet\\Form\\FormsRepository']) ? $this->services['MailPoet\\Form\\FormsRepository'] : $this->getFormsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Templates\\TemplateRepository']) ? $this->services['MailPoet\\Form\\Templates\\TemplateRepository'] : $this->getTemplateRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Form\FormsRepository' shared autowired service.
     *
     * @return \MailPoet\Form\FormsRepository
     */
    protected function getFormsRepositoryService()
    {
        return $this->services['MailPoet\\Form\\FormsRepository'] = new \MailPoet\Form\FormsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Form\Renderer' shared autowired service.
     *
     * @return \MailPoet\Form\Renderer
     */
    protected function getRenderer2Service()
    {
        return $this->services['MailPoet\\Form\\Renderer'] = new \MailPoet\Form\Renderer(${($_ = isset($this->services['MailPoet\\Form\\Util\\Styles']) ? $this->services['MailPoet\\Form\\Util\\Styles'] : ($this->services['MailPoet\\Form\\Util\\Styles'] = new \MailPoet\Form\Util\Styles())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Util\\CustomFonts']) ? $this->services['MailPoet\\Form\\Util\\CustomFonts'] : $this->getCustomFontsService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlocksRenderer']) ? $this->services['MailPoet\\Form\\BlocksRenderer'] : $this->getBlocksRendererService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Form\Util\FieldNameObfuscator' shared autowired service.
     *
     * @return \MailPoet\Form\Util\FieldNameObfuscator
     */
    protected function getFieldNameObfuscatorService()
    {
        return $this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] = new \MailPoet\Form\Util\FieldNameObfuscator(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Listing\BulkActionController' shared autowired service.
     *
     * @return \MailPoet\Listing\BulkActionController
     */
    protected function getBulkActionControllerService()
    {
        return $this->services['MailPoet\\Listing\\BulkActionController'] = new \MailPoet\Listing\BulkActionController(${($_ = isset($this->services['MailPoet\\Listing\\BulkActionFactory']) ? $this->services['MailPoet\\Listing\\BulkActionFactory'] : ($this->services['MailPoet\\Listing\\BulkActionFactory'] = new \MailPoet\Listing\BulkActionFactory())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Listing\\Handler']) ? $this->services['MailPoet\\Listing\\Handler'] : ($this->services['MailPoet\\Listing\\Handler'] = new \MailPoet\Listing\Handler())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Listing\BulkActionFactory' shared autowired service.
     *
     * @return \MailPoet\Listing\BulkActionFactory
     */
    protected function getBulkActionFactoryService()
    {
        return $this->services['MailPoet\\Listing\\BulkActionFactory'] = new \MailPoet\Listing\BulkActionFactory();
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
        return $this->services['MailPoet\\Listing\\PageLimit'] = new \MailPoet\Listing\PageLimit(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\NewsletterTemplates\NewsletterTemplatesRepository' shared autowired service.
     *
     * @return \MailPoet\NewsletterTemplates\NewsletterTemplatesRepository
     */
    protected function getNewsletterTemplatesRepositoryService()
    {
        return $this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] = new \MailPoet\NewsletterTemplates\NewsletterTemplatesRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\AutomatedLatestContent' shared autowired service.
     *
     * @return \MailPoet\Newsletter\AutomatedLatestContent
     */
    protected function getAutomatedLatestContent2Service()
    {
        return $this->services['MailPoet\\Newsletter\\AutomatedLatestContent'] = new \MailPoet\Newsletter\AutomatedLatestContent(${($_ = isset($this->services['MailPoet\\Logging\\LoggerFactory']) ? $this->services['MailPoet\\Logging\\LoggerFactory'] : $this->getLoggerFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Listing\NewsletterListingRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Listing\NewsletterListingRepository
     */
    protected function getNewsletterListingRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Listing\\NewsletterListingRepository'] = new \MailPoet\Newsletter\Listing\NewsletterListingRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewsletterPostsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewsletterPostsRepository
     */
    protected function getNewsletterPostsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\NewsletterPostsRepository'] = new \MailPoet\Newsletter\NewsletterPostsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewsletterSaveController' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewsletterSaveController
     */
    protected function getNewsletterSaveControllerService()
    {
        return $this->services['MailPoet\\Newsletter\\NewsletterSaveController'] = new \MailPoet\Newsletter\NewsletterSaveController(${($_ = isset($this->services['MailPoet\\Services\\AuthorizedEmailsController']) ? $this->services['MailPoet\\Services\\AuthorizedEmailsController'] : $this->getAuthorizedEmailsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Emoji']) ? $this->services['MailPoet\\WP\\Emoji'] : $this->getEmojiService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository']) ? $this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] : $this->getNewsletterOptionsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository']) ? $this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository'] : $this->getNewsletterOptionFieldsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository']) ? $this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] : $this->getNewsletterSegmentRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository']) ? $this->services['MailPoet\\NewsletterTemplates\\NewsletterTemplatesRepository'] : $this->getNewsletterTemplatesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] = new \MailPoet\Newsletter\Scheduler\PostNotificationScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository']) ? $this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] : $this->getScheduledTasksRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\NewslettersRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\NewslettersRepository
     */
    protected function getNewslettersRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\NewslettersRepository'] = new \MailPoet\Newsletter\NewslettersRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository
     */
    protected function getNewsletterOptionFieldsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionFieldsRepository'] = new \MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Options\NewsletterOptionsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Options\NewsletterOptionsRepository
     */
    protected function getNewsletterOptionsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Options\\NewsletterOptionsRepository'] = new \MailPoet\Newsletter\Options\NewsletterOptionsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Blocks\Renderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Renderer
     */
    protected function getRenderer3Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer'] = new \MailPoet\Newsletter\Renderer\Blocks\Renderer(${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] : $this->getAutomatedLatestContentBlockService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Button']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Button'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Button'] = new \MailPoet\Newsletter\Renderer\Blocks\Button())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Divider']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Divider'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Divider'] = new \MailPoet\Newsletter\Renderer\Blocks\Divider())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Footer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Footer'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Footer'] = new \MailPoet\Newsletter\Renderer\Blocks\Footer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Header']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Header'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Header'] = new \MailPoet\Newsletter\Renderer\Blocks\Header())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Image']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Image'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Image'] = new \MailPoet\Newsletter\Renderer\Blocks\Image())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Social']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Social'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Social'] = new \MailPoet\Newsletter\Renderer\Blocks\Social())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer'] = new \MailPoet\Newsletter\Renderer\Blocks\Spacer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Text']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Text'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Text'] = new \MailPoet\Newsletter\Renderer\Blocks\Text())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Renderer\Renderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Renderer
     */
    protected function getRenderer5Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] = new \MailPoet\Newsletter\Renderer\Renderer(${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Renderer'] : $this->getRenderer3Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer'] : ($this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer'] = new \MailPoet\Newsletter\Renderer\Columns\Renderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Preprocessor']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Preprocessor'] : $this->getPreprocessorService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoetVendor\\CSS']) ? $this->services['MailPoetVendor\\CSS'] : ($this->services['MailPoetVendor\\CSS'] = new \MailPoetVendor\CSS())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Services\\Bridge']) ? $this->services['MailPoet\\Services\\Bridge'] : $this->getBridgeService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\License\\License']) ? $this->services['MailPoet\\Util\\License\\License'] : ($this->services['MailPoet\\Util\\License\\License'] = new \MailPoet\Util\License\License())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Scheduler\WelcomeScheduler' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Scheduler\WelcomeScheduler
     */
    protected function getWelcomeSchedulerService()
    {
        return $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler();
    }

    /**
     * Gets the public 'MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository
     */
    protected function getScheduledTaskSubscribersRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Sending\\ScheduledTaskSubscribersRepository'] = new \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController' shared autowired service.
     *
     * @return \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController
     */
    protected function getViewInBrowserControllerService()
    {
        return $this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController'] = new \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController(${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer']) ? $this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer'] : $this->getViewInBrowserRendererService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Newsletter\ViewInBrowser\ViewInBrowserRenderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserRenderer
     */
    protected function getViewInBrowserRendererService()
    {
        return $this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserRenderer'] = new \MailPoet\Newsletter\ViewInBrowser\ViewInBrowserRenderer(${($_ = isset($this->services['MailPoet\\WP\\Emoji']) ? $this->services['MailPoet\\WP\\Emoji'] : $this->getEmojiService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Renderer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] : $this->getRenderer5Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\CronDaemon' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\CronDaemon
     */
    protected function getCronDaemonService()
    {
        return $this->services['MailPoet\\Router\\Endpoints\\CronDaemon'] = new \MailPoet\Router\Endpoints\CronDaemon(${($_ = isset($this->services['MailPoet\\Cron\\DaemonHttpRunner']) ? $this->services['MailPoet\\Cron\\DaemonHttpRunner'] : $this->getDaemonHttpRunnerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\CronHelper']) ? $this->services['MailPoet\\Cron\\CronHelper'] : $this->getCronHelperService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\FormPreview' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\FormPreview
     */
    protected function getFormPreviewService()
    {
        return $this->services['MailPoet\\Router\\Endpoints\\FormPreview'] = new \MailPoet\Router\Endpoints\FormPreview(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\PreviewPage']) ? $this->services['MailPoet\\Form\\PreviewPage'] : $this->getPreviewPageService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\Subscription' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\Subscription
     */
    protected function getSubscriptionService()
    {
        $a = ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'};

        return $this->services['MailPoet\\Router\\Endpoints\\Subscription'] = new \MailPoet\Router\Endpoints\Subscription(new \MailPoet\Subscription\Pages(${($_ = isset($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer']) ? $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] : $this->getNewSubscriberNotificationMailerService()) && false ?: '_'}, $a, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaRenderer']) ? $this->services['MailPoet\\Subscription\\CaptchaRenderer'] : $this->getCaptchaRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\AssetsController']) ? $this->services['MailPoet\\Form\\AssetsController'] : $this->getAssetsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Unsubscribes']) ? $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] : $this->getUnsubscribesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer']) ? $this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] : $this->getManageSubscriptionFormRendererService()) && false ?: '_'}), $a);
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\Track' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\Track
     */
    protected function getTrackService()
    {
        return $this->services['MailPoet\\Router\\Endpoints\\Track'] = new \MailPoet\Router\Endpoints\Track(${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Clicks']) ? $this->services['MailPoet\\Statistics\\Track\\Clicks'] : $this->getClicksService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Opens']) ? $this->services['MailPoet\\Statistics\\Track\\Opens'] : ($this->services['MailPoet\\Statistics\\Track\\Opens'] = new \MailPoet\Statistics\Track\Opens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Router\Endpoints\ViewInBrowser' shared autowired service.
     *
     * @return \MailPoet\Router\Endpoints\ViewInBrowser
     */
    protected function getViewInBrowserService()
    {
        return $this->services['MailPoet\\Router\\Endpoints\\ViewInBrowser'] = new \MailPoet\Router\Endpoints\ViewInBrowser(${($_ = isset($this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController']) ? $this->services['MailPoet\\Newsletter\\ViewInBrowser\\ViewInBrowserController'] : $this->getViewInBrowserControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\FilterHandler' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\FilterHandler
     */
    protected function getFilterHandlerService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] = new \MailPoet\Segments\DynamicSegments\FilterHandler(${($_ = isset($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction']) ? $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction'] : $this->getEmailActionService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole']) ? $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole'] : $this->getUserRoleService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct']) ? $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct'] : $this->getWooCommerceProductService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory']) ? $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory'] : $this->getWooCommerceCategoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\EmailAction' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\EmailAction
     */
    protected function getEmailActionService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\EmailAction'] = new \MailPoet\Segments\DynamicSegments\Filters\EmailAction(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\UserRole' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\UserRole
     */
    protected function getUserRoleService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\UserRole'] = new \MailPoet\Segments\DynamicSegments\Filters\UserRole(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory
     */
    protected function getWooCommerceCategoryService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceCategory'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct' shared autowired service.
     *
     * @return \MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct
     */
    protected function getWooCommerceProductService()
    {
        return $this->services['MailPoet\\Segments\\DynamicSegments\\Filters\\WooCommerceProduct'] = new \MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\SegmentSubscribersRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentSubscribersRepository
     */
    protected function getSegmentSubscribersRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] = new \MailPoet\Segments\SegmentSubscribersRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler']) ? $this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] : $this->getFilterHandlerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\SubscribersFinder' shared autowired service.
     *
     * @return \MailPoet\Segments\SubscribersFinder
     */
    protected function getSubscribersFinderService()
    {
        return $this->services['MailPoet\\Segments\\SubscribersFinder'] = new \MailPoet\Segments\SubscribersFinder(${($_ = isset($this->services['MailPoet\\Segments\\SegmentSubscribersRepository']) ? $this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] : $this->getSegmentSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Segments\WooCommerce' shared autowired service.
     *
     * @return \MailPoet\Segments\WooCommerce
     */
    protected function getWooCommerceService()
    {
        return $this->services['MailPoet\\Segments\\WooCommerce'] = new \MailPoet\Segments\WooCommerce(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Services\Bridge' shared autowired service.
     *
     * @return \MailPoet\Services\Bridge
     */
    protected function getBridgeService()
    {
        return $this->services['MailPoet\\Services\\Bridge'] = new \MailPoet\Services\Bridge(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Settings\SettingsController' shared autowired service.
     *
     * @return \MailPoet\Settings\SettingsController
     */
    protected function getSettingsControllerService()
    {
        return $this->services['MailPoet\\Settings\\SettingsController'] = new \MailPoet\Settings\SettingsController(${($_ = isset($this->services['MailPoet\\Settings\\SettingsRepository']) ? $this->services['MailPoet\\Settings\\SettingsRepository'] : $this->getSettingsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Settings\SettingsRepository' shared autowired service.
     *
     * @return \MailPoet\Settings\SettingsRepository
     */
    protected function getSettingsRepositoryService()
    {
        return $this->services['MailPoet\\Settings\\SettingsRepository'] = new \MailPoet\Settings\SettingsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Settings\UserFlagsRepository' shared autowired service.
     *
     * @return \MailPoet\Settings\UserFlagsRepository
     */
    protected function getUserFlagsRepositoryService()
    {
        return $this->services['MailPoet\\Settings\\UserFlagsRepository'] = new \MailPoet\Settings\UserFlagsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Statistics\Track\Unsubscribes' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\Unsubscribes
     */
    protected function getUnsubscribesService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] = new \MailPoet\Statistics\Track\Unsubscribes(${($_ = isset($this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository']) ? $this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] : $this->getSendingQueuesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\StatisticsUnsubscribesRepository']) ? $this->services['MailPoet\\Statistics\\StatisticsUnsubscribesRepository'] : $this->getStatisticsUnsubscribesRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscribersRepository']) ? $this->services['MailPoet\\Subscribers\\SubscribersRepository'] : $this->getSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscribers\ConfirmationEmailMailer' shared autowired service.
     *
     * @return \MailPoet\Subscribers\ConfirmationEmailMailer
     */
    protected function getConfirmationEmailMailerService()
    {
        return $this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] = new \MailPoet\Subscribers\ConfirmationEmailMailer(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscribers\LinkTokens' shared autowired service.
     *
     * @return \MailPoet\Subscribers\LinkTokens
     */
    protected function getLinkTokensService()
    {
        return $this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens();
    }

    /**
     * Gets the public 'MailPoet\Subscribers\NewSubscriberNotificationMailer' shared autowired service.
     *
     * @return \MailPoet\Subscribers\NewSubscriberNotificationMailer
     */
    protected function getNewSubscriberNotificationMailerService()
    {
        return $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] = new \MailPoet\Subscribers\NewSubscriberNotificationMailer(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscribers\RequiredCustomFieldValidator' shared autowired service.
     *
     * @return \MailPoet\Subscribers\RequiredCustomFieldValidator
     */
    protected function getRequiredCustomFieldValidatorService()
    {
        return $this->services['MailPoet\\Subscribers\\RequiredCustomFieldValidator'] = new \MailPoet\Subscribers\RequiredCustomFieldValidator();
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberActions' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberActions
     */
    protected function getSubscriberActionsService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberActions'] = new \MailPoet\Subscribers\SubscriberActions(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer']) ? $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] : $this->getNewSubscriberNotificationMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer']) ? $this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] : $this->getConfirmationEmailMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscriberListingRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberListingRepository
     */
    protected function getSubscriberListingRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberListingRepository'] = new \MailPoet\Subscribers\SubscriberListingRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler']) ? $this->services['MailPoet\\Segments\\DynamicSegments\\FilterHandler'] : $this->getFilterHandlerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SegmentSubscribersRepository']) ? $this->services['MailPoet\\Segments\\SegmentSubscribersRepository'] : $this->getSegmentSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscribers\SubscribersRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscribersRepository
     */
    protected function getSubscribersRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscribersRepository'] = new \MailPoet\Subscribers\SubscribersRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\Captcha' shared autowired service.
     *
     * @return \MailPoet\Subscription\Captcha
     */
    protected function getCaptchaService()
    {
        return $this->services['MailPoet\\Subscription\\Captcha'] = new \MailPoet\Subscription\Captcha(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaSession']) ? $this->services['MailPoet\\Subscription\\CaptchaSession'] : $this->getCaptchaSessionService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\CaptchaRenderer' shared autowired service.
     *
     * @return \MailPoet\Subscription\CaptchaRenderer
     */
    protected function getCaptchaRendererService()
    {
        return $this->services['MailPoet\\Subscription\\CaptchaRenderer'] = new \MailPoet\Subscription\CaptchaRenderer(${($_ = isset($this->services['MailPoet\\Util\\Url']) ? $this->services['MailPoet\\Util\\Url'] : $this->getUrlService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaSession']) ? $this->services['MailPoet\\Subscription\\CaptchaSession'] : $this->getCaptchaSessionService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Renderer']) ? $this->services['MailPoet\\Form\\Renderer'] : $this->getRenderer2Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\Comment' shared autowired service.
     *
     * @return \MailPoet\Subscription\Comment
     */
    protected function getCommentService()
    {
        return $this->services['MailPoet\\Subscription\\Comment'] = new \MailPoet\Subscription\Comment(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscriberActions']) ? $this->services['MailPoet\\Subscribers\\SubscriberActions'] : $this->getSubscriberActionsService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\Form' shared autowired service.
     *
     * @return \MailPoet\Subscription\Form
     */
    protected function getFormService()
    {
        return $this->services['MailPoet\\Subscription\\Form'] = new \MailPoet\Subscription\Form(${($_ = isset($this->services['MailPoet\\API\\JSON\\API']) ? $this->services['MailPoet\\API\\JSON\\API'] : $this->getAPIService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Url']) ? $this->services['MailPoet\\Util\\Url'] : $this->getUrlService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\Manage' shared autowired service.
     *
     * @return \MailPoet\Subscription\Manage
     */
    protected function getManageService()
    {
        return $this->services['MailPoet\\Subscription\\Manage'] = new \MailPoet\Subscription\Manage(${($_ = isset($this->services['MailPoet\\Util\\Url']) ? $this->services['MailPoet\\Util\\Url'] : $this->getUrlService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Util\\FieldNameObfuscator']) ? $this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] : $this->getFieldNameObfuscatorService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Unsubscribes']) ? $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] : $this->getUnsubscribesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\ManageSubscriptionFormRenderer' shared autowired service.
     *
     * @return \MailPoet\Subscription\ManageSubscriptionFormRenderer
     */
    protected function getManageSubscriptionFormRendererService()
    {
        return $this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] = new \MailPoet\Subscription\ManageSubscriptionFormRenderer(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Url']) ? $this->services['MailPoet\\Util\\Url'] : $this->getUrlService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Renderer']) ? $this->services['MailPoet\\Form\\Renderer'] : $this->getRenderer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Date']) ? $this->services['MailPoet\\Form\\Block\\Date'] : $this->getDateService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\Pages' autowired service.
     *
     * @return \MailPoet\Subscription\Pages
     */
    protected function getPagesService()
    {
        return new \MailPoet\Subscription\Pages(${($_ = isset($this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer']) ? $this->services['MailPoet\\Subscribers\\NewSubscriberNotificationMailer'] : $this->getNewSubscriberNotificationMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\CaptchaRenderer']) ? $this->services['MailPoet\\Subscription\\CaptchaRenderer'] : $this->getCaptchaRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler']) ? $this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] : ($this->services['MailPoet\\Newsletter\\Scheduler\\WelcomeScheduler'] = new \MailPoet\Newsletter\Scheduler\WelcomeScheduler())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\SubscriptionUrlFactory']) ? $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] : $this->getSubscriptionUrlFactoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\AssetsController']) ? $this->services['MailPoet\\Form\\AssetsController'] : $this->getAssetsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Statistics\\Track\\Unsubscribes']) ? $this->services['MailPoet\\Statistics\\Track\\Unsubscribes'] : $this->getUnsubscribesService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer']) ? $this->services['MailPoet\\Subscription\\ManageSubscriptionFormRenderer'] : $this->getManageSubscriptionFormRendererService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\Registration' shared autowired service.
     *
     * @return \MailPoet\Subscription\Registration
     */
    protected function getRegistrationService()
    {
        return $this->services['MailPoet\\Subscription\\Registration'] = new \MailPoet\Subscription\Registration(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscriberActions']) ? $this->services['MailPoet\\Subscribers\\SubscriberActions'] : $this->getSubscriberActionsService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\Subscription\SubscriptionUrlFactory' shared autowired service.
     *
     * @return \MailPoet\Subscription\SubscriptionUrlFactory
     */
    protected function getSubscriptionUrlFactoryService()
    {
        return $this->services['MailPoet\\Subscription\\SubscriptionUrlFactory'] = new \MailPoet\Subscription\SubscriptionUrlFactory(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\LinkTokens']) ? $this->services['MailPoet\\Subscribers\\LinkTokens'] : ($this->services['MailPoet\\Subscribers\\LinkTokens'] = new \MailPoet\Subscribers\LinkTokens())) && false ?: '_'});
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
     * Gets the public 'MailPoet\Util\Url' shared autowired service.
     *
     * @return \MailPoet\Util\Url
     */
    protected function getUrlService()
    {
        return $this->services['MailPoet\\Util\\Url'] = new \MailPoet\Util\Url(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\WP\Emoji' shared autowired service.
     *
     * @return \MailPoet\WP\Emoji
     */
    protected function getEmojiService()
    {
        return $this->services['MailPoet\\WP\\Emoji'] = new \MailPoet\WP\Emoji(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
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
        return $this->services['MailPoet\\WooCommerce\\Settings'] = new \MailPoet\WooCommerce\Settings(${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\Subscription' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\Subscription
     */
    protected function getSubscription2Service()
    {
        return $this->services['MailPoet\\WooCommerce\\Subscription'] = new \MailPoet\WooCommerce\Subscription(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer']) ? $this->services['MailPoet\\Subscribers\\ConfirmationEmailMailer'] : $this->getConfirmationEmailMailerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmailHooks' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmailHooks
     */
    protected function getTransactionalEmailHooksService()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmailHooks'] = new \MailPoet\WooCommerce\TransactionalEmailHooks(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Renderer']) ? $this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Renderer'] : $this->getRenderer6Service()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmails' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmails
     */
    protected function getTransactionalEmailsService()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmails'] = new \MailPoet\WooCommerce\TransactionalEmails(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Template']) ? $this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Template'] : ($this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Template'] = new \MailPoet\WooCommerce\TransactionalEmails\Template())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the public 'MailPoet\WooCommerce\TransactionalEmails\Renderer' shared autowired service.
     *
     * @return \MailPoet\WooCommerce\TransactionalEmails\Renderer
     */
    protected function getRenderer6Service()
    {
        return $this->services['MailPoet\\WooCommerce\\TransactionalEmails\\Renderer'] = new \MailPoet\WooCommerce\TransactionalEmails\Renderer(${($_ = isset($this->services['MailPoetVendor\\csstidy']) ? $this->services['MailPoetVendor\\csstidy'] : ($this->services['MailPoetVendor\\csstidy'] = new \MailPoetVendor\csstidy())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Renderer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] : $this->getRenderer5Service()) && false ?: '_'});
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
     * Gets the private 'MailPoetVendor\CSS' shared autowired service.
     *
     * @return \MailPoetVendor\CSS
     */
    protected function getCSSService()
    {
        return $this->services['MailPoetVendor\\CSS'] = new \MailPoetVendor\CSS();
    }

    /**
     * Gets the private 'MailPoetVendor\Doctrine\ORM\Configuration' shared autowired service.
     *
     * @return \MailPoetVendor\Doctrine\ORM\Configuration
     */
    protected function getConfigurationService()
    {
        return $this->services['MailPoetVendor\\Doctrine\\ORM\\Configuration'] = ${($_ = isset($this->services['MailPoet\\Doctrine\\ConfigurationFactory']) ? $this->services['MailPoet\\Doctrine\\ConfigurationFactory'] : $this->getConfigurationFactoryService()) && false ?: '_'}->createConfiguration();
    }

    /**
     * Gets the private 'MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface' shared autowired service.
     *
     * @return \MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected function getValidatorInterfaceService()
    {
        return $this->services['MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface'] = ${($_ = isset($this->services['MailPoet\\Doctrine\\Validator\\ValidatorFactory']) ? $this->services['MailPoet\\Doctrine\\Validator\\ValidatorFactory'] : $this->getValidatorFactoryService()) && false ?: '_'}->createValidator();
    }

    /**
     * Gets the private 'MailPoetVendor\csstidy' shared autowired service.
     *
     * @return \MailPoetVendor\csstidy
     */
    protected function getCsstidyService()
    {
        return $this->services['MailPoetVendor\\csstidy'] = new \MailPoetVendor\csstidy();
    }

    /**
     * Gets the private 'MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder
     */
    protected function getCustomFieldsResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\CustomFieldsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder();
    }

    /**
     * Gets the private 'MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder
     */
    protected function getFormsResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\FormsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder();
    }

    /**
     * Gets the private 'MailPoet\API\JSON\ResponseBuilders\NewsletterTemplatesResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\NewsletterTemplatesResponseBuilder
     */
    protected function getNewsletterTemplatesResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewsletterTemplatesResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\NewsletterTemplatesResponseBuilder();
    }

    /**
     * Gets the private 'MailPoet\API\JSON\ResponseBuilders\NewslettersResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\NewslettersResponseBuilder
     */
    protected function getNewslettersResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\NewslettersResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\NewslettersResponseBuilder(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository']) ? $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] : $this->getNewsletterStatisticsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder' shared autowired service.
     *
     * @return \MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder
     */
    protected function getSegmentsResponseBuilderService()
    {
        return $this->services['MailPoet\\API\\JSON\\ResponseBuilders\\SegmentsResponseBuilder'] = new \MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder();
    }

    /**
     * Gets the private 'MailPoet\Config\DatabaseInitializer' shared autowired service.
     *
     * @return \MailPoet\Config\DatabaseInitializer
     */
    protected function getDatabaseInitializerService()
    {
        return $this->services['MailPoet\\Config\\DatabaseInitializer'] = new \MailPoet\Config\DatabaseInitializer($this);
    }

    /**
     * Gets the private 'MailPoet\Config\Localizer' shared autowired service.
     *
     * @return \MailPoet\Config\Localizer
     */
    protected function getLocalizerService()
    {
        return $this->services['MailPoet\\Config\\Localizer'] = new \MailPoet\Config\Localizer();
    }

    /**
     * Gets the private 'MailPoet\Config\MP2Migrator' shared autowired service.
     *
     * @return \MailPoet\Config\MP2Migrator
     */
    protected function getMP2Migrator2Service()
    {
        return $this->services['MailPoet\\Config\\MP2Migrator'] = new \MailPoet\Config\MP2Migrator(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Activator']) ? $this->services['MailPoet\\Config\\Activator'] : $this->getActivatorService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Config\Populator' shared autowired service.
     *
     * @return \MailPoet\Config\Populator
     */
    protected function getPopulatorService()
    {
        return $this->services['MailPoet\\Config\\Populator'] = new \MailPoet\Config\Populator(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscription\\Captcha']) ? $this->services['MailPoet\\Subscription\\Captcha'] : $this->getCaptchaService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Referrals\\ReferralDetector']) ? $this->services['MailPoet\\Referrals\\ReferralDetector'] : $this->getReferralDetectorService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormsRepository']) ? $this->services['MailPoet\\Form\\FormsRepository'] : $this->getFormsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormFactory']) ? $this->services['MailPoet\\Form\\FormFactory'] : $this->getFormFactoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Config\ServicesChecker' shared autowired service.
     *
     * @return \MailPoet\Config\ServicesChecker
     */
    protected function getServicesCheckerService()
    {
        return $this->services['MailPoet\\Config\\ServicesChecker'] = new \MailPoet\Config\ServicesChecker();
    }

    /**
     * Gets the private 'MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository
     */
    protected function getNewsletterLinkRepositoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\NewsletterLinkRepository'] = new \MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Cron\Workers\StatsNotifications\Scheduler' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\Scheduler
     */
    protected function getScheduler2Service()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\Scheduler'] = new \MailPoet\Cron\Workers\StatsNotifications\Scheduler(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository']) ? $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository'] : $this->getStatsNotificationsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository' shared autowired service.
     *
     * @return \MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository
     */
    protected function getStatsNotificationsRepositoryService()
    {
        return $this->services['MailPoet\\Cron\\Workers\\StatsNotifications\\StatsNotificationsRepository'] = new \MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\CustomFields\ApiDataSanitizer' shared autowired service.
     *
     * @return \MailPoet\CustomFields\ApiDataSanitizer
     */
    protected function getApiDataSanitizerService()
    {
        return $this->services['MailPoet\\CustomFields\\ApiDataSanitizer'] = new \MailPoet\CustomFields\ApiDataSanitizer();
    }

    /**
     * Gets the private 'MailPoet\CustomFields\CustomFieldsRepository' shared autowired service.
     *
     * @return \MailPoet\CustomFields\CustomFieldsRepository
     */
    protected function getCustomFieldsRepositoryService()
    {
        return $this->services['MailPoet\\CustomFields\\CustomFieldsRepository'] = new \MailPoet\CustomFields\CustomFieldsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Doctrine\Annotations\AnnotationReaderProvider' shared autowired service.
     *
     * @return \MailPoet\Doctrine\Annotations\AnnotationReaderProvider
     */
    protected function getAnnotationReaderProviderService()
    {
        return $this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider'] = new \MailPoet\Doctrine\Annotations\AnnotationReaderProvider();
    }

    /**
     * Gets the private 'MailPoet\Doctrine\ConfigurationFactory' shared autowired service.
     *
     * @return \MailPoet\Doctrine\ConfigurationFactory
     */
    protected function getConfigurationFactoryService()
    {
        return $this->services['MailPoet\\Doctrine\\ConfigurationFactory'] = new \MailPoet\Doctrine\ConfigurationFactory(NULL, ${($_ = isset($this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider']) ? $this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider'] : ($this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider'] = new \MailPoet\Doctrine\Annotations\AnnotationReaderProvider())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Doctrine\ConnectionFactory' shared autowired service.
     *
     * @return \MailPoet\Doctrine\ConnectionFactory
     */
    protected function getConnectionFactoryService()
    {
        return $this->services['MailPoet\\Doctrine\\ConnectionFactory'] = new \MailPoet\Doctrine\ConnectionFactory();
    }

    /**
     * Gets the private 'MailPoet\Doctrine\EntityManagerFactory' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EntityManagerFactory
     */
    protected function getEntityManagerFactoryService()
    {
        return $this->services['MailPoet\\Doctrine\\EntityManagerFactory'] = new \MailPoet\Doctrine\EntityManagerFactory(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection']) ? $this->services['MailPoetVendor\\Doctrine\\DBAL\\Connection'] : $this->getConnectionService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\Configuration']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\Configuration'] : $this->getConfigurationService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Doctrine\\EventListeners\\TimestampListener']) ? $this->services['MailPoet\\Doctrine\\EventListeners\\TimestampListener'] : $this->getTimestampListenerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Doctrine\\EventListeners\\ValidationListener']) ? $this->services['MailPoet\\Doctrine\\EventListeners\\ValidationListener'] : $this->getValidationListenerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener']) ? $this->services['MailPoet\\Doctrine\\EventListeners\\EmojiEncodingListener'] : $this->getEmojiEncodingListenerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Doctrine\EventListeners\TimestampListener' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EventListeners\TimestampListener
     */
    protected function getTimestampListenerService()
    {
        return $this->services['MailPoet\\Doctrine\\EventListeners\\TimestampListener'] = new \MailPoet\Doctrine\EventListeners\TimestampListener(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Doctrine\EventListeners\ValidationListener' shared autowired service.
     *
     * @return \MailPoet\Doctrine\EventListeners\ValidationListener
     */
    protected function getValidationListenerService()
    {
        return $this->services['MailPoet\\Doctrine\\EventListeners\\ValidationListener'] = new \MailPoet\Doctrine\EventListeners\ValidationListener(${($_ = isset($this->services['MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface']) ? $this->services['MailPoetVendor\\Symfony\\Component\\Validator\\Validator\\ValidatorInterface'] : $this->getValidatorInterfaceService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Doctrine\Validator\ValidatorFactory' shared autowired service.
     *
     * @return \MailPoet\Doctrine\Validator\ValidatorFactory
     */
    protected function getValidatorFactoryService()
    {
        return $this->services['MailPoet\\Doctrine\\Validator\\ValidatorFactory'] = new \MailPoet\Doctrine\Validator\ValidatorFactory(${($_ = isset($this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider']) ? $this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider'] : ($this->services['MailPoet\\Doctrine\\Annotations\\AnnotationReaderProvider'] = new \MailPoet\Doctrine\Annotations\AnnotationReaderProvider())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\DynamicSegments\FreePluginConnectors\AddToNewslettersSegments' shared autowired service.
     *
     * @return \MailPoet\DynamicSegments\FreePluginConnectors\AddToNewslettersSegments
     */
    protected function getAddToNewslettersSegmentsService()
    {
        return $this->services['MailPoet\\DynamicSegments\\FreePluginConnectors\\AddToNewslettersSegments'] = new \MailPoet\DynamicSegments\FreePluginConnectors\AddToNewslettersSegments(${($_ = isset($this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader']) ? $this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader'] : $this->getLoaderService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount']) ? $this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount'] : ($this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount'] = new \MailPoet\DynamicSegments\Persistence\Loading\SubscribersCount())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\DynamicSegments\Mappers\DBMapper' shared autowired service.
     *
     * @return \MailPoet\DynamicSegments\Mappers\DBMapper
     */
    protected function getDBMapperService()
    {
        return $this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper'] = new \MailPoet\DynamicSegments\Mappers\DBMapper();
    }

    /**
     * Gets the private 'MailPoet\DynamicSegments\Persistence\Loading\Loader' shared autowired service.
     *
     * @return \MailPoet\DynamicSegments\Persistence\Loading\Loader
     */
    protected function getLoaderService()
    {
        return $this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\Loader'] = new \MailPoet\DynamicSegments\Persistence\Loading\Loader(${($_ = isset($this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper']) ? $this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper'] : ($this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper'] = new \MailPoet\DynamicSegments\Mappers\DBMapper())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\DynamicSegments\Persistence\Loading\SingleSegmentLoader' shared autowired service.
     *
     * @return \MailPoet\DynamicSegments\Persistence\Loading\SingleSegmentLoader
     */
    protected function getSingleSegmentLoaderService()
    {
        return $this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\SingleSegmentLoader'] = new \MailPoet\DynamicSegments\Persistence\Loading\SingleSegmentLoader(${($_ = isset($this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper']) ? $this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper'] : ($this->services['MailPoet\\DynamicSegments\\Mappers\\DBMapper'] = new \MailPoet\DynamicSegments\Mappers\DBMapper())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\DynamicSegments\Persistence\Loading\SubscribersCount' shared autowired service.
     *
     * @return \MailPoet\DynamicSegments\Persistence\Loading\SubscribersCount
     */
    protected function getSubscribersCountService()
    {
        return $this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersCount'] = new \MailPoet\DynamicSegments\Persistence\Loading\SubscribersCount();
    }

    /**
     * Gets the private 'MailPoet\DynamicSegments\Persistence\Loading\SubscribersIds' shared autowired service.
     *
     * @return \MailPoet\DynamicSegments\Persistence\Loading\SubscribersIds
     */
    protected function getSubscribersIdsService()
    {
        return $this->services['MailPoet\\DynamicSegments\\Persistence\\Loading\\SubscribersIds'] = new \MailPoet\DynamicSegments\Persistence\Loading\SubscribersIds();
    }

    /**
     * Gets the private 'MailPoet\Features\FeaturesController' shared autowired service.
     *
     * @return \MailPoet\Features\FeaturesController
     */
    protected function getFeaturesControllerService()
    {
        return $this->services['MailPoet\\Features\\FeaturesController'] = new \MailPoet\Features\FeaturesController(${($_ = isset($this->services['MailPoet\\Features\\FeatureFlagsRepository']) ? $this->services['MailPoet\\Features\\FeatureFlagsRepository'] : $this->getFeatureFlagsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\BlockStylesRenderer' shared autowired service.
     *
     * @return \MailPoet\Form\BlockStylesRenderer
     */
    protected function getBlockStylesRendererService()
    {
        return $this->services['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer();
    }

    /**
     * Gets the private 'MailPoet\Form\BlockWrapperRenderer' shared autowired service.
     *
     * @return \MailPoet\Form\BlockWrapperRenderer
     */
    protected function getBlockWrapperRendererService()
    {
        return $this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer();
    }

    /**
     * Gets the private 'MailPoet\Form\Block\BlockRendererHelper' shared autowired service.
     *
     * @return \MailPoet\Form\Block\BlockRendererHelper
     */
    protected function getBlockRendererHelperService()
    {
        return $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] = new \MailPoet\Form\Block\BlockRendererHelper(${($_ = isset($this->services['MailPoet\\Form\\Util\\FieldNameObfuscator']) ? $this->services['MailPoet\\Form\\Util\\FieldNameObfuscator'] : $this->getFieldNameObfuscatorService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Checkbox' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Checkbox
     */
    protected function getCheckboxService()
    {
        return $this->services['MailPoet\\Form\\Block\\Checkbox'] = new \MailPoet\Form\Block\Checkbox(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Column' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Column
     */
    protected function getColumnService()
    {
        return $this->services['MailPoet\\Form\\Block\\Column'] = new \MailPoet\Form\Block\Column();
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Columns' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Columns
     */
    protected function getColumnsService()
    {
        return $this->services['MailPoet\\Form\\Block\\Columns'] = new \MailPoet\Form\Block\Columns();
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Divider' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Divider
     */
    protected function getDividerService()
    {
        return $this->services['MailPoet\\Form\\Block\\Divider'] = new \MailPoet\Form\Block\Divider();
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Heading' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Heading
     */
    protected function getHeadingService()
    {
        return $this->services['MailPoet\\Form\\Block\\Heading'] = new \MailPoet\Form\Block\Heading();
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Html' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Html
     */
    protected function getHtmlService()
    {
        return $this->services['MailPoet\\Form\\Block\\Html'] = new \MailPoet\Form\Block\Html(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Image' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Image
     */
    protected function getImageService()
    {
        return $this->services['MailPoet\\Form\\Block\\Image'] = new \MailPoet\Form\Block\Image(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Paragraph' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Paragraph
     */
    protected function getParagraphService()
    {
        return $this->services['MailPoet\\Form\\Block\\Paragraph'] = new \MailPoet\Form\Block\Paragraph();
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Radio' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Radio
     */
    protected function getRadioService()
    {
        return $this->services['MailPoet\\Form\\Block\\Radio'] = new \MailPoet\Form\Block\Radio(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Segment' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Segment
     */
    protected function getSegmentService()
    {
        return $this->services['MailPoet\\Form\\Block\\Segment'] = new \MailPoet\Form\Block\Segment(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Segments\\SegmentsRepository']) ? $this->services['MailPoet\\Segments\\SegmentsRepository'] : $this->getSegmentsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Select' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Select
     */
    protected function getSelectService()
    {
        return $this->services['MailPoet\\Form\\Block\\Select'] = new \MailPoet\Form\Block\Select(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockStylesRenderer']) ? $this->services['MailPoet\\Form\\BlockStylesRenderer'] : ($this->services['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Submit' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Submit
     */
    protected function getSubmitService()
    {
        return $this->services['MailPoet\\Form\\Block\\Submit'] = new \MailPoet\Form\Block\Submit(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockStylesRenderer']) ? $this->services['MailPoet\\Form\\BlockStylesRenderer'] : ($this->services['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Text' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Text
     */
    protected function getTextService()
    {
        return $this->services['MailPoet\\Form\\Block\\Text'] = new \MailPoet\Form\Block\Text(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockStylesRenderer']) ? $this->services['MailPoet\\Form\\BlockStylesRenderer'] : ($this->services['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Block\Textarea' shared autowired service.
     *
     * @return \MailPoet\Form\Block\Textarea
     */
    protected function getTextareaService()
    {
        return $this->services['MailPoet\\Form\\Block\\Textarea'] = new \MailPoet\Form\Block\Textarea(${($_ = isset($this->services['MailPoet\\Form\\Block\\BlockRendererHelper']) ? $this->services['MailPoet\\Form\\Block\\BlockRendererHelper'] : $this->getBlockRendererHelperService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockStylesRenderer']) ? $this->services['MailPoet\\Form\\BlockStylesRenderer'] : ($this->services['MailPoet\\Form\\BlockStylesRenderer'] = new \MailPoet\Form\BlockStylesRenderer())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\BlockWrapperRenderer']) ? $this->services['MailPoet\\Form\\BlockWrapperRenderer'] : ($this->services['MailPoet\\Form\\BlockWrapperRenderer'] = new \MailPoet\Form\BlockWrapperRenderer())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\BlocksRenderer' shared autowired service.
     *
     * @return \MailPoet\Form\BlocksRenderer
     */
    protected function getBlocksRendererService()
    {
        return $this->services['MailPoet\\Form\\BlocksRenderer'] = new \MailPoet\Form\BlocksRenderer(${($_ = isset($this->services['MailPoet\\Form\\Block\\Checkbox']) ? $this->services['MailPoet\\Form\\Block\\Checkbox'] : $this->getCheckboxService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Column']) ? $this->services['MailPoet\\Form\\Block\\Column'] : ($this->services['MailPoet\\Form\\Block\\Column'] = new \MailPoet\Form\Block\Column())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Columns']) ? $this->services['MailPoet\\Form\\Block\\Columns'] : ($this->services['MailPoet\\Form\\Block\\Columns'] = new \MailPoet\Form\Block\Columns())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Date']) ? $this->services['MailPoet\\Form\\Block\\Date'] : $this->getDateService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Divider']) ? $this->services['MailPoet\\Form\\Block\\Divider'] : ($this->services['MailPoet\\Form\\Block\\Divider'] = new \MailPoet\Form\Block\Divider())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Html']) ? $this->services['MailPoet\\Form\\Block\\Html'] : $this->getHtmlService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Image']) ? $this->services['MailPoet\\Form\\Block\\Image'] : $this->getImageService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Heading']) ? $this->services['MailPoet\\Form\\Block\\Heading'] : ($this->services['MailPoet\\Form\\Block\\Heading'] = new \MailPoet\Form\Block\Heading())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Paragraph']) ? $this->services['MailPoet\\Form\\Block\\Paragraph'] : ($this->services['MailPoet\\Form\\Block\\Paragraph'] = new \MailPoet\Form\Block\Paragraph())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Radio']) ? $this->services['MailPoet\\Form\\Block\\Radio'] : $this->getRadioService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Segment']) ? $this->services['MailPoet\\Form\\Block\\Segment'] : $this->getSegmentService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Select']) ? $this->services['MailPoet\\Form\\Block\\Select'] : $this->getSelectService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Submit']) ? $this->services['MailPoet\\Form\\Block\\Submit'] : $this->getSubmitService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Text']) ? $this->services['MailPoet\\Form\\Block\\Text'] : $this->getTextService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Block\\Textarea']) ? $this->services['MailPoet\\Form\\Block\\Textarea'] : $this->getTextareaService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\DisplayFormInWPContent' shared autowired service.
     *
     * @return \MailPoet\Form\DisplayFormInWPContent
     */
    protected function getDisplayFormInWPContentService()
    {
        return $this->services['MailPoet\\Form\\DisplayFormInWPContent'] = new \MailPoet\Form\DisplayFormInWPContent(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormsRepository']) ? $this->services['MailPoet\\Form\\FormsRepository'] : $this->getFormsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Renderer']) ? $this->services['MailPoet\\Form\\Renderer'] : $this->getRenderer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\AssetsController']) ? $this->services['MailPoet\\Form\\AssetsController'] : $this->getAssetsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\PreviewPage' shared autowired service.
     *
     * @return \MailPoet\Form\PreviewPage
     */
    protected function getPreviewPageService()
    {
        return $this->services['MailPoet\\Form\\PreviewPage'] = new \MailPoet\Form\PreviewPage(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\Renderer']) ? $this->services['MailPoet\\Form\\Renderer'] : $this->getRenderer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormsRepository']) ? $this->services['MailPoet\\Form\\FormsRepository'] : $this->getFormsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\AssetsController']) ? $this->services['MailPoet\\Form\\AssetsController'] : $this->getAssetsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Templates\TemplateRepository' shared autowired service.
     *
     * @return \MailPoet\Form\Templates\TemplateRepository
     */
    protected function getTemplateRepositoryService()
    {
        return $this->services['MailPoet\\Form\\Templates\\TemplateRepository'] = new \MailPoet\Form\Templates\TemplateRepository(${($_ = isset($this->services['MailPoet\\Util\\CdnAssetUrl']) ? $this->services['MailPoet\\Util\\CdnAssetUrl'] : $this->getCdnAssetUrlService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Util\CustomFonts' shared autowired service.
     *
     * @return \MailPoet\Form\Util\CustomFonts
     */
    protected function getCustomFontsService()
    {
        return $this->services['MailPoet\\Form\\Util\\CustomFonts'] = new \MailPoet\Form\Util\CustomFonts(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Form\Util\Styles' shared autowired service.
     *
     * @return \MailPoet\Form\Util\Styles
     */
    protected function getStylesService()
    {
        return $this->services['MailPoet\\Form\\Util\\Styles'] = new \MailPoet\Form\Util\Styles();
    }

    /**
     * Gets the private 'MailPoet\Helpscout\Beacon' shared autowired service.
     *
     * @return \MailPoet\Helpscout\Beacon
     */
    protected function getBeaconService()
    {
        return $this->services['MailPoet\\Helpscout\\Beacon'] = new \MailPoet\Helpscout\Beacon(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Logging\LoggerFactory' shared autowired service.
     *
     * @return \MailPoet\Logging\LoggerFactory
     */
    protected function getLoggerFactoryService()
    {
        return $this->services['MailPoet\\Logging\\LoggerFactory'] = new \MailPoet\Logging\LoggerFactory(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Mailer\Mailer' shared autowired service.
     *
     * @return \MailPoet\Mailer\Mailer
     */
    protected function getMailer2Service()
    {
        return $this->services['MailPoet\\Mailer\\Mailer'] = new \MailPoet\Mailer\Mailer(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Mailer\MetaInfo' shared autowired service.
     *
     * @return \MailPoet\Mailer\MetaInfo
     */
    protected function getMetaInfoService()
    {
        return $this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo();
    }

    /**
     * Gets the private 'MailPoet\Mailer\Methods\Common\BlacklistCheck' shared autowired service.
     *
     * @return \MailPoet\Mailer\Methods\Common\BlacklistCheck
     */
    protected function getBlacklistCheckService()
    {
        return $this->services['MailPoet\\Mailer\\Methods\\Common\\BlacklistCheck'] = new \MailPoet\Mailer\Methods\Common\BlacklistCheck();
    }

    /**
     * Gets the private 'MailPoet\Mailer\WordPress\WordpressMailerReplacer' shared autowired service.
     *
     * @return \MailPoet\Mailer\WordPress\WordpressMailerReplacer
     */
    protected function getWordpressMailerReplacerService()
    {
        return $this->services['MailPoet\\Mailer\\WordPress\\WordpressMailerReplacer'] = new \MailPoet\Mailer\WordPress\WordpressMailerReplacer(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\MetaInfo']) ? $this->services['MailPoet\\Mailer\\MetaInfo'] : ($this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscribersRepository']) ? $this->services['MailPoet\\Subscribers\\SubscribersRepository'] : $this->getSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Preview\SendPreviewController' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Preview\SendPreviewController
     */
    protected function getSendPreviewControllerService()
    {
        return $this->services['MailPoet\\Newsletter\\Preview\\SendPreviewController'] = new \MailPoet\Newsletter\Preview\SendPreviewController(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\MetaInfo']) ? $this->services['MailPoet\\Mailer\\MetaInfo'] : ($this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Renderer']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Renderer'] : $this->getRenderer5Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent
     */
    protected function getAbandonedCartContentService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent'] = new \MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent(${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] : $this->getAutomatedLatestContentBlockService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock
     */
    protected function getAutomatedLatestContentBlockService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] = new \MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock(${($_ = isset($this->services['MailPoet\\Newsletter\\NewsletterPostsRepository']) ? $this->services['MailPoet\\Newsletter\\NewsletterPostsRepository'] : $this->getNewsletterPostsRepositoryService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\AutomatedLatestContent']) ? $this->services['MailPoet\\Newsletter\\AutomatedLatestContent'] : $this->getAutomatedLatestContent2Service()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Button' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Button
     */
    protected function getButtonService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Button'] = new \MailPoet\Newsletter\Renderer\Blocks\Button();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Divider' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Divider
     */
    protected function getDivider2Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Divider'] = new \MailPoet\Newsletter\Renderer\Blocks\Divider();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Footer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Footer
     */
    protected function getFooterService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Footer'] = new \MailPoet\Newsletter\Renderer\Blocks\Footer();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Header' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Header
     */
    protected function getHeaderService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Header'] = new \MailPoet\Newsletter\Renderer\Blocks\Header();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Image' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Image
     */
    protected function getImage2Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Image'] = new \MailPoet\Newsletter\Renderer\Blocks\Image();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Social' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Social
     */
    protected function getSocialService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Social'] = new \MailPoet\Newsletter\Renderer\Blocks\Social();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Spacer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Spacer
     */
    protected function getSpacerService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Spacer'] = new \MailPoet\Newsletter\Renderer\Blocks\Spacer();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Blocks\Text' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Blocks\Text
     */
    protected function getText2Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\Text'] = new \MailPoet\Newsletter\Renderer\Blocks\Text();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Columns\Renderer' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Columns\Renderer
     */
    protected function getRenderer4Service()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Columns\\Renderer'] = new \MailPoet\Newsletter\Renderer\Columns\Renderer();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Renderer\Preprocessor' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Renderer\Preprocessor
     */
    protected function getPreprocessorService()
    {
        return $this->services['MailPoet\\Newsletter\\Renderer\\Preprocessor'] = new \MailPoet\Newsletter\Renderer\Preprocessor(${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AbandonedCartContent'] : $this->getAbandonedCartContentService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock']) ? $this->services['MailPoet\\Newsletter\\Renderer\\Blocks\\AutomatedLatestContentBlock'] : $this->getAutomatedLatestContentBlockService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\TransactionalEmails']) ? $this->services['MailPoet\\WooCommerce\\TransactionalEmails'] : $this->getTransactionalEmailsService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Scheduler\PostNotificationScheduler' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Scheduler\PostNotificationScheduler
     */
    protected function getPostNotificationSchedulerService()
    {
        return $this->services['MailPoet\\Newsletter\\Scheduler\\PostNotificationScheduler'] = new \MailPoet\Newsletter\Scheduler\PostNotificationScheduler();
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Segment\NewsletterSegmentRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Segment\NewsletterSegmentRepository
     */
    protected function getNewsletterSegmentRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Segment\\NewsletterSegmentRepository'] = new \MailPoet\Newsletter\Segment\NewsletterSegmentRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Sending\ScheduledTasksRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Sending\ScheduledTasksRepository
     */
    protected function getScheduledTasksRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Sending\\ScheduledTasksRepository'] = new \MailPoet\Newsletter\Sending\ScheduledTasksRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Sending\SendingQueuesRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Sending\SendingQueuesRepository
     */
    protected function getSendingQueuesRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Sending\\SendingQueuesRepository'] = new \MailPoet\Newsletter\Sending\SendingQueuesRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository' shared autowired service.
     *
     * @return \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository
     */
    protected function getNewsletterStatisticsRepositoryService()
    {
        return $this->services['MailPoet\\Newsletter\\Statistics\\NewsletterStatisticsRepository'] = new \MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\PostEditorBlocks\PostEditorBlock' shared autowired service.
     *
     * @return \MailPoet\PostEditorBlocks\PostEditorBlock
     */
    protected function getPostEditorBlockService()
    {
        return $this->services['MailPoet\\PostEditorBlocks\\PostEditorBlock'] = new \MailPoet\PostEditorBlocks\PostEditorBlock(${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\PostEditorBlocks\\SubscriptionFormBlock']) ? $this->services['MailPoet\\PostEditorBlocks\\SubscriptionFormBlock'] : $this->getSubscriptionFormBlockService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\PostEditorBlocks\SubscriptionFormBlock' shared autowired service.
     *
     * @return \MailPoet\PostEditorBlocks\SubscriptionFormBlock
     */
    protected function getSubscriptionFormBlockService()
    {
        return $this->services['MailPoet\\PostEditorBlocks\\SubscriptionFormBlock'] = new \MailPoet\PostEditorBlocks\SubscriptionFormBlock(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Form\\FormsRepository']) ? $this->services['MailPoet\\Form\\FormsRepository'] : $this->getFormsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Referrals\ReferralDetector' shared autowired service.
     *
     * @return \MailPoet\Referrals\ReferralDetector
     */
    protected function getReferralDetectorService()
    {
        return $this->services['MailPoet\\Referrals\\ReferralDetector'] = new \MailPoet\Referrals\ReferralDetector(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Router\Router' shared autowired service.
     *
     * @return \MailPoet\Router\Router
     */
    protected function getRouterService()
    {
        return $this->services['MailPoet\\Router\\Router'] = new \MailPoet\Router\Router(${($_ = isset($this->services['MailPoet\\Config\\AccessControl']) ? $this->services['MailPoet\\Config\\AccessControl'] : ($this->services['MailPoet\\Config\\AccessControl'] = new \MailPoet\Config\AccessControl())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\DI\\ContainerWrapper']) ? $this->services['MailPoet\\DI\\ContainerWrapper'] : $this->getContainerWrapperService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Segments\SegmentsRepository' shared autowired service.
     *
     * @return \MailPoet\Segments\SegmentsRepository
     */
    protected function getSegmentsRepositoryService()
    {
        return $this->services['MailPoet\\Segments\\SegmentsRepository'] = new \MailPoet\Segments\SegmentsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Services\AuthorizedEmailsController' shared autowired service.
     *
     * @return \MailPoet\Services\AuthorizedEmailsController
     */
    protected function getAuthorizedEmailsControllerService()
    {
        return $this->services['MailPoet\\Services\\AuthorizedEmailsController'] = new \MailPoet\Services\AuthorizedEmailsController(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Services\\Bridge']) ? $this->services['MailPoet\\Services\\Bridge'] : $this->getBridgeService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Newsletter\\NewslettersRepository']) ? $this->services['MailPoet\\Newsletter\\NewslettersRepository'] : $this->getNewslettersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Services\CongratulatoryMssEmailController' shared autowired service.
     *
     * @return \MailPoet\Services\CongratulatoryMssEmailController
     */
    protected function getCongratulatoryMssEmailControllerService()
    {
        return $this->services['MailPoet\\Services\\CongratulatoryMssEmailController'] = new \MailPoet\Services\CongratulatoryMssEmailController(${($_ = isset($this->services['MailPoet\\Mailer\\Mailer']) ? $this->services['MailPoet\\Mailer\\Mailer'] : $this->getMailer2Service()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Mailer\\MetaInfo']) ? $this->services['MailPoet\\Mailer\\MetaInfo'] : ($this->services['MailPoet\\Mailer\\MetaInfo'] = new \MailPoet\Mailer\MetaInfo())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Config\\Renderer']) ? $this->services['MailPoet\\Config\\Renderer'] : $this->getRendererService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Settings\UserFlagsController' shared autowired service.
     *
     * @return \MailPoet\Settings\UserFlagsController
     */
    protected function getUserFlagsControllerService()
    {
        return $this->services['MailPoet\\Settings\\UserFlagsController'] = new \MailPoet\Settings\UserFlagsController(${($_ = isset($this->services['MailPoet\\Settings\\UserFlagsRepository']) ? $this->services['MailPoet\\Settings\\UserFlagsRepository'] : $this->getUserFlagsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Statistics\StatisticsUnsubscribesRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsUnsubscribesRepository
     */
    protected function getStatisticsUnsubscribesRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsUnsubscribesRepository'] = new \MailPoet\Statistics\StatisticsUnsubscribesRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Statistics\StatisticsWooCommercePurchasesRepository' shared autowired service.
     *
     * @return \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository
     */
    protected function getStatisticsWooCommercePurchasesRepositoryService()
    {
        return $this->services['MailPoet\\Statistics\\StatisticsWooCommercePurchasesRepository'] = new \MailPoet\Statistics\StatisticsWooCommercePurchasesRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Statistics\Track\Clicks' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\Clicks
     */
    protected function getClicksService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\Clicks'] = new \MailPoet\Statistics\Track\Clicks(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Cookies']) ? $this->services['MailPoet\\Util\\Cookies'] : ($this->services['MailPoet\\Util\\Cookies'] = new \MailPoet\Util\Cookies())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Statistics\Track\Opens' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\Opens
     */
    protected function getOpensService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\Opens'] = new \MailPoet\Statistics\Track\Opens();
    }

    /**
     * Gets the private 'MailPoet\Statistics\Track\WooCommercePurchases' shared autowired service.
     *
     * @return \MailPoet\Statistics\Track\WooCommercePurchases
     */
    protected function getWooCommercePurchasesService()
    {
        return $this->services['MailPoet\\Statistics\\Track\\WooCommercePurchases'] = new \MailPoet\Statistics\Track\WooCommercePurchases(${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Util\\Cookies']) ? $this->services['MailPoet\\Util\\Cookies'] : ($this->services['MailPoet\\Util\\Cookies'] = new \MailPoet\Util\Cookies())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Subscribers\InactiveSubscribersController' shared autowired service.
     *
     * @return \MailPoet\Subscribers\InactiveSubscribersController
     */
    protected function getInactiveSubscribersControllerService()
    {
        return $this->services['MailPoet\\Subscribers\\InactiveSubscribersController'] = new \MailPoet\Subscribers\InactiveSubscribersController(${($_ = isset($this->services['MailPoet\\Settings\\SettingsRepository']) ? $this->services['MailPoet\\Settings\\SettingsRepository'] : $this->getSettingsRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository
     */
    protected function getSubscriberStatisticsRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\Statistics\\SubscriberStatisticsRepository'] = new \MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WooCommerce\\Helper']) ? $this->services['MailPoet\\WooCommerce\\Helper'] : ($this->services['MailPoet\\WooCommerce\\Helper'] = new \MailPoet\WooCommerce\Helper())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Subscribers\SubscriberCustomFieldRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberCustomFieldRepository
     */
    protected function getSubscriberCustomFieldRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberCustomFieldRepository'] = new \MailPoet\Subscribers\SubscriberCustomFieldRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Subscribers\SubscriberSegmentRepository' shared autowired service.
     *
     * @return \MailPoet\Subscribers\SubscriberSegmentRepository
     */
    protected function getSubscriberSegmentRepositoryService()
    {
        return $this->services['MailPoet\\Subscribers\\SubscriberSegmentRepository'] = new \MailPoet\Subscribers\SubscriberSegmentRepository(${($_ = isset($this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager']) ? $this->services['MailPoetVendor\\Doctrine\\ORM\\EntityManager'] : $this->getEntityManagerService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Subscription\CaptchaSession' shared autowired service.
     *
     * @return \MailPoet\Subscription\CaptchaSession
     */
    protected function getCaptchaSessionService()
    {
        return $this->services['MailPoet\\Subscription\\CaptchaSession'] = new \MailPoet\Subscription\CaptchaSession(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Tasks\State' shared autowired service.
     *
     * @return \MailPoet\Tasks\State
     */
    protected function getStateService()
    {
        return $this->services['MailPoet\\Tasks\\State'] = new \MailPoet\Tasks\State();
    }

    /**
     * Gets the private 'MailPoet\Util\Cookies' shared autowired service.
     *
     * @return \MailPoet\Util\Cookies
     */
    protected function getCookiesService()
    {
        return $this->services['MailPoet\\Util\\Cookies'] = new \MailPoet\Util\Cookies();
    }

    /**
     * Gets the private 'MailPoet\Util\Installation' shared autowired service.
     *
     * @return \MailPoet\Util\Installation
     */
    protected function getInstallationService()
    {
        return $this->services['MailPoet\\Util\\Installation'] = new \MailPoet\Util\Installation(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Util\License\Features\Subscribers' shared autowired service.
     *
     * @return \MailPoet\Util\License\Features\Subscribers
     */
    protected function getSubscribers3Service()
    {
        return $this->services['MailPoet\\Util\\License\\Features\\Subscribers'] = new \MailPoet\Util\License\Features\Subscribers(${($_ = isset($this->services['MailPoet\\Settings\\SettingsController']) ? $this->services['MailPoet\\Settings\\SettingsController'] : $this->getSettingsControllerService()) && false ?: '_'}, ${($_ = isset($this->services['MailPoet\\Subscribers\\SubscribersRepository']) ? $this->services['MailPoet\\Subscribers\\SubscribersRepository'] : $this->getSubscribersRepositoryService()) && false ?: '_'});
    }

    /**
     * Gets the private 'MailPoet\Util\License\License' shared autowired service.
     *
     * @return \MailPoet\Util\License\License
     */
    protected function getLicenseService()
    {
        return $this->services['MailPoet\\Util\\License\\License'] = new \MailPoet\Util\License\License();
    }

    /**
     * Gets the private 'MailPoet\Util\Notices\PermanentNotices' shared autowired service.
     *
     * @return \MailPoet\Util\Notices\PermanentNotices
     */
    protected function getPermanentNoticesService()
    {
        return $this->services['MailPoet\\Util\\Notices\\PermanentNotices'] = new \MailPoet\Util\Notices\PermanentNotices(${($_ = isset($this->services['MailPoet\\WP\\Functions']) ? $this->services['MailPoet\\WP\\Functions'] : ($this->services['MailPoet\\WP\\Functions'] = new \MailPoet\WP\Functions())) && false ?: '_'});
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
