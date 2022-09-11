<?php

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* newsletters.html */
class __TwigTemplate_9641d7769a94575cd44aaeff4df310da82a8fe07490b66905fc1e575dbaf51b2 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'translations' => [$this, 'block_translations'],
            'after_translations' => [$this, 'block_after_translations'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layout.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layout.html", "newsletters.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "  <div id=\"newsletters_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 8
        echo "      var mailpoet_update_available = ";
        echo ((($context["is_mailpoet_update_available"] ?? null)) ? ("true") : ("false"));
        echo "
      var mailpoet_listing_per_page = ";
        // line 9
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["items_per_page"] ?? null), "js", null, true);
        echo ";
      var mailpoet_display_nps_poll = ";
        // line 10
        echo ((((($context["sent_newsletters_count"] ?? null) > 0) && \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["settings"] ?? null), "display_nps_poll", [], "any", false, false, false, 10))) ? ("true") : ("false"));
        echo ";
      var mailpoet_segments = ";
        // line 11
        echo json_encode(($context["segments"] ?? null));
        echo ";
      var mailpoet_show_congratulate_after_first_newsletter = ";
        // line 12
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["show_congratulate_after_first_newsletter"] ?? null), "js", null, true);
        echo ";
      var mailpoet_current_wp_user = ";
        // line 13
        echo json_encode(($context["current_wp_user"] ?? null));
        echo ";
      var mailpoet_current_wp_user_firstname = '";
        // line 14
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["current_wp_user_firstname"] ?? null), "js", null, true);
        echo "';
      var mailpoet_lists = ";
        // line 15
        echo json_encode(($context["lists"] ?? null));
        echo ";
      var mailpoet_roles = ";
        // line 16
        echo json_encode(($context["roles"] ?? null));
        echo ";
      var mailpoet_current_date = ";
        // line 17
        echo json_encode(($context["current_date"] ?? null));
        echo ";
      var mailpoet_tomorrow_date = ";
        // line 18
        echo json_encode(($context["tomorrow_date"] ?? null));
        echo ";
      var mailpoet_current_time = ";
        // line 19
        echo json_encode(($context["current_time"] ?? null));
        echo ";
      var mailpoet_current_date_time = ";
        // line 20
        echo json_encode(($context["current_date_time"] ?? null));
        echo ";
      var mailpoet_schedule_time_of_day = ";
        // line 21
        echo json_encode(($context["schedule_time_of_day"] ?? null));
        echo ";
      var mailpoet_date_display_format = \"";
        // line 22
        echo $this->extensions['MailPoet\Twig\Functions']->getWPDateFormat();
        echo "\";
      var mailpoet_date_storage_format = \"Y-m-d\";
      var mailpoet_transactional_emails_opt_in_notice_dismissed = '";
        // line 24
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["transactional_emails_opt_in_notice_dismissed"] ?? null), "js", null, true);
        echo "';
      var mailpoet_product_categories = ";
        // line 25
        echo json_encode(($context["product_categories"] ?? null));
        echo ";
      var mailpoet_products = ";
        // line 26
        echo json_encode(($context["products"] ?? null));
        echo ";

      var mailpoet_account_url = '";
        // line 28
        echo $this->extensions['MailPoet\Twig\Functions']->addReferralId(((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&email=") . \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 28), "js")));
        echo "';

      var mailpoet_woocommerce_automatic_emails = ";
        // line 30
        echo json_encode(($context["automatic_emails"] ?? null));
        echo ";
      var mailpoet_woocommerce_optin_on_checkout = \"";
        // line 31
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["woocommerce_optin_on_checkout"] ?? null), "js", null, true);
        echo "\";

      var mailpoet_woocommerce_transactional_email_id = ";
        // line 33
        echo json_encode(($context["woocommerce_transactional_email_id"] ?? null));
        echo ";
      var mailpoet_display_detailed_stats = ";
        // line 34
        echo json_encode(($context["display_detailed_stats"] ?? null));
        echo ";
      var mailpoet_last_announcement_seen = ";
        // line 35
        echo json_encode(($context["last_announcement_seen"] ?? null));
        echo ";
      var mailpoet_user_locale = '";
        // line 36
        echo $this->extensions['MailPoet\Twig\I18n']->getLocale();
        echo "';
      var mailpoet_congratulations_success_images = [
        '";
        // line 38
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-1.png");
        echo "',
        '";
        // line 39
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-2.png");
        echo "',
        '";
        // line 40
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-3.png");
        echo "',
        '";
        // line 41
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulate-4.png");
        echo "',
      ];
      var mailpoet_congratulations_error_image = '";
        // line 43
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/error.png");
        echo "';
      var mailpoet_congratulations_loading_image = '";
        // line 44
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("newsletter/congratulation-page-illustration-transparent-LQ.20181121-1440.png");
        echo "';
      var mailpoet_main_page = '";
        // line 45
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mailpoet_main_page"] ?? null), "js", null, true);
        echo "';
      var mailpoet_review_request_illustration_url = '";
        // line 46
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("review-request/review-request-illustration.20190815-1427.svg");
        echo "';
      var mailpoet_installed_at = '";
        // line 47
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["settings"] ?? null), "installed_at", [], "any", false, false, false, 47), "js", null, true);
        echo "';
      var mailpoet_editor_javascript_url = '";
        // line 48
        echo $this->extensions['MailPoet\Twig\Assets']->getJavascriptScriptUrl("newsletter_editor.js");
        echo "';
      var mailpoet_newsletters_count = ";
        // line 49
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["newsletters_count"] ?? null), "js", null, true);
        echo ";
      var mailpoet_send_transactional_emails = ";
        // line 50
        echo json_encode((\MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["settings"] ?? null), "send_transactional_emails", [], "any", false, false, false, 50) == "1"));
        echo ";
      var mailpoet_authorized_emails = ";
        // line 51
        echo json_encode(($context["authorized_emails"] ?? null));
        echo ";
      var mailpoet_verified_sender_domains = ";
        // line 52
        echo json_encode(($context["verified_sender_domains"] ?? null));
        echo ";
      var mailpoet_all_sender_domains = ";
        // line 53
        echo json_encode(($context["all_sender_domains"] ?? null));
        echo ";
    ";
        // line 55
        echo "    var mailpoet_beacon_articles = [
      '57fdc312c697911f2d324fd7',
      '5d541f7c2c7d3a68825ea881',
      '58a719a12c7d3a576d3548da',
      '5a0257ac2c7d3a272c0d7ad6',
      '58f671152c7d3a057f8858e8'
    ];

    var mailpoet_newsletters_templates_recently_sent_count = ";
        // line 63
        echo json_decode(($context["newsletters_templates_recently_sent_count"] ?? null));
        echo ";
  </script>
";
    }

    // line 67
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 68
        echo "  ";
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["pageTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Emails"), "stepNameTypeStandard" => $this->extensions['MailPoet\Twig\I18n']->translate("Newsletter"), "stepNameTypeWelcome" => $this->extensions['MailPoet\Twig\I18n']->translate("Welcome Email"), "stepNameTypeNotification" => $this->extensions['MailPoet\Twig\I18n']->translate("Post Notification"), "stepNameTypeWooCommerce" => $this->extensions['MailPoet\Twig\I18n']->translate("WooCommerce"), "stepNameTypeReEngagement" => $this->extensions['MailPoet\Twig\I18n']->translate("Re-engagement"), "stepNameTemplate" => $this->extensions['MailPoet\Twig\I18n']->translate("Template"), "stepNameDesign" => $this->extensions['MailPoet\Twig\I18n']->translate("Design"), "stepNameSend" => $this->extensions['MailPoet\Twig\I18n']->translate("Send"), "stepNameActivate" => $this->extensions['MailPoet\Twig\I18n']->translate("Activate"), "dismissButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Dismiss this notice."), "tabReEngagementTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Re-engagement Email"), "tabStandardTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Newsletters"), "tabWelcomeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Welcome Emails"), "tabNotificationTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Post Notifications"), "tabWoocommerceTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("WooCommerce Emails"), "tabReEngagementTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Re-engagement Email"), "tabBlankTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Simple text"), "tabReEngagementTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Re-engagement Emails"), "reEngagementTextPre" => $this->extensions['MailPoet\Twig\I18n']->translate("After no activity for"), "reEngagementAterTimeNumberPlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("count"), "reEngagementEmailWarning" => $this->extensions['MailPoet\Twig\I18n']->translate("Disengaged subscribers will [link]become inactive[/link] after {\$months} months and won’t receive this email. Please select a shorter period."), "searchLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Search"), "loadingItems" => $this->extensions['MailPoet\Twig\I18n']->translate("Loading emails..."), "noItemsFound" => $this->extensions['MailPoet\Twig\I18n']->translate("No emails found."), "emptyListing" => $this->extensions['MailPoet\Twig\I18n']->translate("Nothing here yet! But, don't fret - there's no reason to get upset. Pretty soon, you’ll be sending emails faster than a turbo-jet."), "selectAllLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("All emails on this page are selected."), "selectedAllLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("All %d emails are selected."), "selectAllLink" => $this->extensions['MailPoet\Twig\I18n']->translate("Select all emails on all pages"), "clearSelection" => $this->extensions['MailPoet\Twig\I18n']->translate("Clear selection"), "permanentlyDeleted" => $this->extensions['MailPoet\Twig\I18n']->translate("%d emails were permanently deleted."), "selectBulkAction" => $this->extensions['MailPoet\Twig\I18n']->translate("Select bulk action"), "bulkActions" => $this->extensions['MailPoet\Twig\I18n']->translate("Bulk Actions"), "apply" => $this->extensions['MailPoet\Twig\I18n']->translate("Apply"), "filter" => $this->extensions['MailPoet\Twig\I18n']->translate("Filter"), "emptyTrash" => $this->extensions['MailPoet\Twig\I18n']->translate("Empty Trash"), "selectAll" => $this->extensions['MailPoet\Twig\I18n']->translate("Select All"), "restore" => $this->extensions['MailPoet\Twig\I18n']->translate("Restore"), "deletePermanently" => $this->extensions['MailPoet\Twig\I18n']->translate("Delete Permanently"), "showMoreDetails" => $this->extensions['MailPoet\Twig\I18n']->translate("Show more details"), "previousPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Previous page"), "firstPage" => $this->extensions['MailPoet\Twig\I18n']->translate("First page"), "nextPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Next page"), "lastPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Last page"), "currentPage" => $this->extensions['MailPoet\Twig\I18n']->translate("Current page"), "pageOutOf" => $this->extensions['MailPoet\Twig\I18n']->translate("of"), "numberOfItemsSingular" => $this->extensions['MailPoet\Twig\I18n']->translate("1 item"), "numberOfItemsMultiple" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d items"), "selectType" => $this->extensions['MailPoet\Twig\I18n']->translate("Select type"), "events" => $this->extensions['MailPoet\Twig\I18n']->translate("Events"), "conditions" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Conditions", "Configuration options for automatic email events"), "template" => $this->extensions['MailPoet\Twig\I18n']->translate("Template"), "designer" => $this->extensions['MailPoet\Twig\I18n']->translate("Designer"), "send" => $this->extensions['MailPoet\Twig\I18n']->translate("Send"), "subject" => $this->extensions['MailPoet\Twig\I18n']->translate("Subject"), "status" => $this->extensions['MailPoet\Twig\I18n']->translate("Status"), "statsListingActionTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Statistics"), "statistics" => $this->extensions['MailPoet\Twig\I18n']->translate("Clicked, Opened"), "lists" => $this->extensions['MailPoet\Twig\I18n']->translate("Lists"), "settings" => $this->extensions['MailPoet\Twig\I18n']->translate("Settings"), "history" => $this->extensions['MailPoet\Twig\I18n']->translate("History"), "viewHistory" => $this->extensions['MailPoet\Twig\I18n']->translate("View history"), "createdOn" => $this->extensions['MailPoet\Twig\I18n']->translate("Created on"), "lastModifiedOn" => $this->extensions['MailPoet\Twig\I18n']->translate("Last modified on"), "sentOn" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent on"), "oneNewsletterTrashed" => $this->extensions['MailPoet\Twig\I18n']->translate("1 email was moved to the trash."), "multipleNewslettersTrashed" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d emails were moved to the trash."), "oneNewsletterDeleted" => $this->extensions['MailPoet\Twig\I18n']->translate("1 email was permanently deleted."), "multipleNewslettersDeleted" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d emails were permanently deleted."), "oneNewsletterRestored" => $this->extensions['MailPoet\Twig\I18n']->translate("1 email has been restored from the Trash."), "multipleNewslettersRestored" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d emails have been restored from the Trash."), "trash" => $this->extensions['MailPoet\Twig\I18n']->translate("Trash"), "moveToTrash" => $this->extensions['MailPoet\Twig\I18n']->translate("Move to trash"), "edit" => $this->extensions['MailPoet\Twig\I18n']->translate("Edit"), "duplicate" => $this->extensions['MailPoet\Twig\I18n']->translate("Duplicate"), "newsletterDuplicated" => $this->extensions['MailPoet\Twig\I18n']->translate("Email \"%1\$s\" has been duplicated."), "notSentYet" => $this->extensions['MailPoet\Twig\I18n']->translate("Not sent yet"), "scheduledFor" => $this->extensions['MailPoet\Twig\I18n']->translate("Scheduled for"), "scheduleIt" => $this->extensions['MailPoet\Twig\I18n']->translate("Schedule it"), "active" => $this->extensions['MailPoet\Twig\I18n']->translate("Active"), "inactive" => $this->extensions['MailPoet\Twig\I18n']->translate("Not Active"), "sentToXSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("%1\$d sent", "number of welcome emails sent"), "scheduledToXSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("%1\$d scheduled", "number of welcome emails scheduled to be sent"), "resume" => $this->extensions['MailPoet\Twig\I18n']->translate("Resume"), "pause" => $this->extensions['MailPoet\Twig\I18n']->translate("Pause"), "paused" => $this->extensions['MailPoet\Twig\I18n']->translate("Paused"), "new" => $this->extensions['MailPoet\Twig\I18n']->translate("New email"), "excellentBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Excellent"), "excellentBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Congrats!"), "goodBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Good"), "goodBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Good stuff."), "averageBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Average"), "averageBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Something to improve."), "openedStatTooltipExcellent" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("above 30%", "Excellent open rate"), "openedStatTooltipGood" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("between 10 and 30%", "Good open rate"), "openedStatTooltipAverage" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("under 10%", "Average open rate"), "clickedStatTooltipExcellent" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("above 3%", "Excellent click rate"), "clickedStatTooltipGood" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("between 1 and 3%", "Good click rate"), "clickedStatTooltipAverage" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("under 1%", "Average click rate"), "revenueStatsTooltipShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Revenues by customers who clicked on this email in the last 2 weeks."), "badBadgeName" => $this->extensions['MailPoet\Twig\I18n']->translate("Bad"), "badBadgeTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Something to improve."), "openedStatTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Above 30% is excellent.\\nBetween 10 and 30% is good.\\nUnder 10% is bad."), "clickedStatTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Above 3% is excellent.\\nBetween 1 and 3% is good.\\nUnder 1% is bad."), "unsubscribedStatTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Under 1% is excellent.\\nBetween 1 and 3% is good.\\nOver 3% is bad."), "revenueStatsTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("Revenues generated by customers who made a purchase within two weeks after they clicked on this email. This is the sum of the order totals including shipping and taxes."), "checkBackInHours" => $this->extensions['MailPoet\Twig\I18n']->translate("Nice job! Check back in %1\$d hour(s) for more stats."), "improveThisLinkText" => $this->extensions['MailPoet\Twig\I18n']->translate("What can I do to improve this?"), "templateFileMalformedError" => $this->extensions['MailPoet\Twig\I18n']->translate("This template file appears to be damaged. Please try another one."), "importTemplateTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Import a template"), "selectJsonFileToUpload" => $this->extensions['MailPoet\Twig\I18n']->translate("Select a .json file to upload"), "helpTooltipTemplateUpload" => $this->extensions['MailPoet\Twig\I18n']->translate("You can only upload .json templates that were originally created with MailPoet 3."), "upload" => $this->extensions['MailPoet\Twig\I18n']->translate("Upload"), "mailpoetGuideTemplateTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet's Guide"), "confirmTemplateDeletion" => $this->extensions['MailPoet\Twig\I18n']->translate("You are about to delete the template named \"%1\$s\"."), "delete" => $this->extensions['MailPoet\Twig\I18n']->translate("Delete"), "select" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select", "Verb"), "preview" => $this->extensions['MailPoet\Twig\I18n']->translate("Preview"), "selectTemplateTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Select a responsive template"), "draftNewsletterTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Subject"), "draftPostNotificationTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("The last [newsletter:total] posts from our blog"), "pickCampaignType" => $this->extensions['MailPoet\Twig\I18n']->translate("Select type of email"), "createFirstEmailTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Create your first email"), "seeVideoGuide" => $this->extensions['MailPoet\Twig\I18n']->translate("See video guide"), "premiumFeature" => $this->extensions['MailPoet\Twig\I18n']->translate("This is a Premium feature"), "premiumFeatureDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Your current MailPoet plan includes advanced features, but they require the MailPoet Premium plugin to be installed and activated."), "premiumFeatureButtonDownloadPremium" => $this->extensions['MailPoet\Twig\I18n']->translate("Download MailPoet Premium plugin"), "premiumFeatureButtonActivatePremium" => $this->extensions['MailPoet\Twig\I18n']->translate("Activate MailPoet Premium plugin"), "premiumFeatureDescriptionSubscribersLimitReached" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have [subscribersCount] subscribers! Your plan is limited to [subscribersLimit] subscribers. You need to upgrade now to be able to continue using MailPoet."), "premiumFeatureButtonUpgradePlan" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade your plan"), "learnMore" => $this->extensions['MailPoet\Twig\I18n']->translate("Learn more"), "regularNewsletterTypeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Newsletter"), "regularNewsletterTypeDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Send a newsletter with images, buttons, dividers, and social bookmarks. Or, just send a basic text email."), "reEngagementDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Automatically email and win back subscribers who have recently lost interest and stopped engaging with your emails."), "create" => $this->extensions['MailPoet\Twig\I18n']->translate("Create"), "wooCommerceCustomizerTypeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("WooCommerce Emails Customizer"), "wooCommerceCustomizerTypeDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Customize the template used for your WooCommerce emails using MailPoet's editor. Example of WooCommerce email: Order processing notification, Order failed notification, ..."), "customize" => "Customize", "welcomeNewsletterTypeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Welcome Email"), "welcomeNewsletterTypeDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Automatically send an email (or series of emails) to new subscribers or WordPress users. Send a day, a week, or a month after they sign up."), "premiumFeatureLink" => $this->extensions['MailPoet\Twig\I18n']->translate("This is a Premium feature"), "setUp" => $this->extensions['MailPoet\Twig\I18n']->translate("Set up"), "postNotificationNewsletterTypeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Latest Post Notifications"), "postNotificationNewsletterTypeDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Let MailPoet email your subscribers with your latest content. You can send daily, weekly, monthly, or even immediately after publication."), "selectFrequency" => $this->extensions['MailPoet\Twig\I18n']->translate("Select a frequency"), "postNotificationSubjectLineTip" => $this->extensions['MailPoet\Twig\I18n']->translate("Insert [newsletter:total] to show number of posts, [newsletter:post_title] to show the latest post's title & [newsletter:number] to display the issue number."), "activate" => $this->extensions['MailPoet\Twig\I18n']->translate("Activate"), "premiumBannerDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Learn more about your subscribers and optimize your campaigns. See who opened your emails, which links they clicked, and then use the data to make your emails even better. And if you run a WooCommerce store, you’ll also see the revenue earned per email. All starting \$10 per month."), "upgradeRequired" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade required"), "newsletterFreeVersionLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have [subscribersCount] subscribers! Our free version is limited to [subscribersLimit] subscribers. You need to upgrade now to be able to continue using MailPoet."), "newsletterYourPlanLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have [subscribersCount] subscribers! Your plan is limited to [subscribersLimit] subscribers. You need to upgrade now to be able to continue using MailPoet."), "daily" => $this->extensions['MailPoet\Twig\I18n']->translate("Once a day at..."), "weekly" => $this->extensions['MailPoet\Twig\I18n']->translate("Weekly on..."), "monthly" => $this->extensions['MailPoet\Twig\I18n']->translate("Monthly on the..."), "monthlyEvery" => $this->extensions['MailPoet\Twig\I18n']->translate("Monthly every..."), "immediately" => $this->extensions['MailPoet\Twig\I18n']->translate("Immediately"), "sunday" => $this->extensions['MailPoet\Twig\I18n']->translate("Sunday"), "monday" => $this->extensions['MailPoet\Twig\I18n']->translate("Monday"), "tuesday" => $this->extensions['MailPoet\Twig\I18n']->translate("Tuesday"), "wednesday" => $this->extensions['MailPoet\Twig\I18n']->translate("Wednesday"), "thursday" => $this->extensions['MailPoet\Twig\I18n']->translate("Thursday"), "friday" => $this->extensions['MailPoet\Twig\I18n']->translate("Friday"), "saturday" => $this->extensions['MailPoet\Twig\I18n']->translate("Saturday"), "tomorrow" => $this->extensions['MailPoet\Twig\I18n']->translate("Tomorrow"), "first" => $this->extensions['MailPoet\Twig\I18n']->translate("1st"), "second" => $this->extensions['MailPoet\Twig\I18n']->translate("2nd"), "third" => $this->extensions['MailPoet\Twig\I18n']->translate("3rd"), "nth" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$dth"), "last" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("last", "e.g. monthly every last Monday"), "next" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Next", "Button label: Next step"), "reEngagementSettings" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("{\$count} {\$frequency} after inactivity", "example: \"5 months after inactivity\""), "reEngagementFrequencyMonth" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("month", "month in the sentence \"1 month after inactivity\""), "reEngagementFrequencyMonths" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("months", "months in the sentence \"5 months after inactivity\""), "reEngagementFrequencyWeek" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("week", "week in the sentence \"1 week after inactivity\""), "reEngagementFrequencyWeeks" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("weeks", "weeks in the sentence \"5 weeks after inactivity\""), "selectEventToSendWelcomeEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("When to send this welcome email?"), "selectEventToSendReEngagementEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("When to send this re-engagement email?"), "onSubscriptionToList" => $this->extensions['MailPoet\Twig\I18n']->translate("When someone subscribes to the list..."), "onWPUserRegistration" => $this->extensions['MailPoet\Twig\I18n']->translate("When a new WordPress user is added to your site..."), "delayImmediately" => $this->extensions['MailPoet\Twig\I18n']->translate("immediately"), "delayHoursAfter" => $this->extensions['MailPoet\Twig\I18n']->translate("hour(s) later"), "delayDaysAfter" => $this->extensions['MailPoet\Twig\I18n']->translate("day(s) later"), "delayWeeksAfter" => $this->extensions['MailPoet\Twig\I18n']->translate("week(s) later"), "subjectLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Subject"), "subjectLine" => $this->extensions['MailPoet\Twig\I18n']->translate("Type newsletter subject"), "subjectLineTip" => $this->extensions['MailPoet\Twig\I18n']->translate("Be creative! It's the first thing that your subscribers see. Tempt them to open your email."), "preheaderLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Preview text"), "preheaderLine" => $this->extensions['MailPoet\Twig\I18n']->translate("Type preview text (usually displayed underneath the subject line in the inbox)"), "preheaderLineTip1" => $this->extensions['MailPoet\Twig\I18n']->translate("This optional text will appear in your subscribers' inboxes, beside the subject line. Write something enticing!"), "preheaderLineTip2" => $this->extensions['MailPoet\Twig\I18n']->translate("Max length is 250 characters, however, we recommend 80 characters on a single line."), "emptySubjectLineError" => $this->extensions['MailPoet\Twig\I18n']->translate("Please specify a subject"), "segments" => $this->extensions['MailPoet\Twig\I18n']->translate("Lists"), "segmentsTip" => $this->extensions['MailPoet\Twig\I18n']->translate("Subscribers in multiple lists will only receive one email."), "selectSegmentPlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("Select a list"), "noSegmentsSelectedError" => $this->extensions['MailPoet\Twig\I18n']->translate("Please select a list"), "sender" => $this->extensions['MailPoet\Twig\I18n']->translate("Sender"), "senderTip" => $this->extensions['MailPoet\Twig\I18n']->translate("Your name and email"), "senderNamePlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("John Doe"), "senderAddressPlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("john.doe@email.com"), "replyTo" => $this->extensions['MailPoet\Twig\I18n']->translate("Reply-to"), "replyToTip" => $this->extensions['MailPoet\Twig\I18n']->translate("When your subscribers reply to your emails, their emails will go to this address."), "replyToNamePlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("John Doe"), "replyToAddressPlaceholder" => $this->extensions['MailPoet\Twig\I18n']->translate("john.doe@email.com"), "newsletterUpdated" => $this->extensions['MailPoet\Twig\I18n']->translate("Email was updated successfully!"), "newsletterAdded" => $this->extensions['MailPoet\Twig\I18n']->translate("Email was added successfully!"), "newsletterSendingError" => $this->extensions['MailPoet\Twig\I18n']->translate("An error occurred while trying to send. <a href=\"%1\$s\">Please check your settings</a>."), "finalNewsletterStep" => $this->extensions['MailPoet\Twig\I18n']->translate("Final Step: Last Details"), "saveDraftAndClose" => $this->extensions['MailPoet\Twig\I18n']->translate("Save as draft and close"), "pendingKeyApprovalNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("You’ll soon be able to send once our team reviews your account. In the meantime, you can send previews to [link]your authorized emails[/link]."), "orSimply" => $this->extensions['MailPoet\Twig\I18n']->translate("or simply"), "goBackToDesign" => $this->extensions['MailPoet\Twig\I18n']->translate("go back to the Design page"), "websiteTimeIs" => $this->extensions['MailPoet\Twig\I18n']->translate("Your website’s time is"), "noScheduledDateError" => $this->extensions['MailPoet\Twig\I18n']->translate("Please enter the scheduled date."), "schedule" => $this->extensions['MailPoet\Twig\I18n']->translate("Schedule"), "youNeedToAuthorizeTheEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("You need to authorize the email address [email] to be able to send with it."), "authorizeMyEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("Authorize my email address"), "next" => $this->extensions['MailPoet\Twig\I18n']->translate("Next"), "previous" => $this->extensions['MailPoet\Twig\I18n']->translate("Previous"), "newsletterBeingSent" => $this->extensions['MailPoet\Twig\I18n']->translate("The newsletter is being sent..."), "newsletterHasBeenScheduled" => $this->extensions['MailPoet\Twig\I18n']->translate("The newsletter has been scheduled."), "newsletterSendingHasBeenResumed" => $this->extensions['MailPoet\Twig\I18n']->translate("The newsletter sending has been resumed."), "newsletterInvalidFromAddress" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You need to authorize the email address <i>%1\$s</i> to be able to send with it. [link]Authorize my email address[/link]", "Users need to confirm that they own the email address they want to use to send their newsletter"), "welcomeEmailActivated" => $this->extensions['MailPoet\Twig\I18n']->translate("Your Welcome Email is now activated!"), "welcomeEmailActivationFailed" => $this->extensions['MailPoet\Twig\I18n']->translate("Your Welcome Email could not be activated, please check the settings."), "reEngagementEmailActivated" => $this->extensions['MailPoet\Twig\I18n']->translate("Your ReEngagement Email is now activated!"), "reEngagementEmailActivationFailed" => $this->extensions['MailPoet\Twig\I18n']->translate("Your ReEngagement Email could not be activated, please check the settings."), "postNotificationActivated" => $this->extensions['MailPoet\Twig\I18n']->translate("Your post notification is now active!"), "postNotificationActivationFailed" => $this->extensions['MailPoet\Twig\I18n']->translate("Your Post Notification could not be activated, check the settings."), "welcomeEventSegment" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent when someone subscribes to the list: \"%1\$s\"."), "welcomeEventSegment_new" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent when someone subscribes to the list: %1\$s."), "welcomeEventWPUserAnyRole" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent when a new WordPress user is added to your site."), "welcomeEventWPUserWithRole" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent when a new WordPress user with the role \"%1\$s\" is added to your site."), "welcomeEventWPUserWithRole_new" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent when a new WordPress user with the role %1\$s is added to your site."), "sendingDelayMinutes" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d minute(s) later"), "sendingDelayHours" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d hour(s) later"), "sendingDelayDays" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d day(s) later"), "sendingDelayWeeks" => $this->extensions['MailPoet\Twig\I18n']->translate("%1\$d week(s) later"), "sendingDelayInvalid" => $this->extensions['MailPoet\Twig\I18n']->translate("Invalid sending delay."), "sendDaily" => $this->extensions['MailPoet\Twig\I18n']->translate("Daily at %1\$s"), "sendWeekly" => $this->extensions['MailPoet\Twig\I18n']->translate("Weekly on %1\$s at %2\$s"), "sendMonthly" => $this->extensions['MailPoet\Twig\I18n']->translate("Monthly on the %1\$s at %2\$s"), "sendNthWeekDay" => $this->extensions['MailPoet\Twig\I18n']->translate("Every %1\$s %2\$s of the month at %3\$s"), "sendImmediately" => $this->extensions['MailPoet\Twig\I18n']->translate("Immediately"), "sendTo" => $this->extensions['MailPoet\Twig\I18n']->translate("Send to %1\$s"), "sendingToSegmentsNotSpecified" => $this->extensions['MailPoet\Twig\I18n']->translate("You need to select a list to send to."), "backToPostNotifications" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to Post notifications"), "noSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("No subscribers!"), "transactionalEmailNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Good news! MailPoet can now send your website’s emails too"), "transactionalEmailNoticeBody" => $this->extensions['MailPoet\Twig\I18n']->translate("All of your WordPress and WooCommerce emails are sent with your hosting company, unless you have an SMTP plugin. Would you like such emails to be delivered with MailPoet’s active sending method for better deliverability?"), "transactionalEmailNoticeBodyReadMore" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more.", "This is a link that leads to more information about transactional emails"), "transactionalEmailNoticeCTA" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Enable", "Button, after clicking it we will enable transactional emails"), "mailerSendErrorNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending has been paused due to a technical issue with %1\$s"), "mailerSendErrorCheckConfiguration" => $this->extensions['MailPoet\Twig\I18n']->translate("Please check your sending method configuration, you may need to consult with your hosting company."), "mailerSendErrorUseSendingService" => $this->extensions['MailPoet\Twig\I18n']->translate("The easy alternative is to <b>send emails with MailPoet Sending Service</b> instead, like thousands of other users do."), "mailerSendErrorSignUpForSendingService" => $this->extensions['MailPoet\Twig\I18n']->translate("Sign up for free in minutes"), "mailerConnectionErrorNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending is paused because the following connection issue prevents MailPoet from delivering emails"), "mailerErrorCode" => $this->extensions['MailPoet\Twig\I18n']->translate("Error code: %1\$s"), "mailerCheckSettingsNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Check your [link]sending method settings[/link]."), "mailerResumeSendingButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Resume sending"), "mailerResumeSendingAfterUpgradeButton" => $this->extensions['MailPoet\Twig\I18n']->translate("I have upgraded my subscription, resume sending"), "confirmEdit" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending is in progress. Do you want to pause sending and edit the newsletter?"), "confirmTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Confirm to proceed"), "confirmLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Confirm"), "cancelLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Cancel"), "confirmAutomaticNewsletterEdit" => $this->extensions['MailPoet\Twig\I18n']->translate("To edit this email, it needs to be deactivated. You can activate it again after you make the changes."), "recentlySent" => $this->extensions['MailPoet\Twig\I18n']->translate("Recently sent"), "savedTemplates" => $this->extensions['MailPoet\Twig\I18n']->translate("Your saved templates"), "templatePreview" => $this->extensions['MailPoet\Twig\I18n']->translate("Template preview"), "zoom" => $this->extensions['MailPoet\Twig\I18n']->translate("Preview"), "tabImportTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import", "Importing template tab title"), "noTemplates" => $this->extensions['MailPoet\Twig\I18n']->translate("This category does not contain any template yet!"), "soon" => $this->extensions['MailPoet\Twig\I18n']->translate("Soon"), "beta" => $this->extensions['MailPoet\Twig\I18n']->translate("Beta"), "errorWhileTakingScreenshot" => $this->extensions['MailPoet\Twig\I18n']->translate("An error occurred while saving the template in \"Recently sent\""), "cronNotAccessibleNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Oops! There seems to be an issue with the sending on your website. [link]See our guide[/link] to solve this yourself."), "congratulationsSuccessHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations!"), "congratulationsSendSuccessBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your newsletter is being sent!"), "congratulationsScheduleSuccessBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your newsletter is scheduled to be sent."), "congratulationsWooSuccessBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your WooCommerce email has been activated."), "congratulationsPostNotificationSuccessBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your Post Notification is now active."), "congratulationsWelcomeEmailSuccessBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your Welcome Email is now active."), "congratulationsSendFailHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("Oops! We can’t send your newsletter"), "congratulationsSendFailExplain" => $this->extensions['MailPoet\Twig\I18n']->translate("Rest assured, this is fairly common and is usually fixed quickly. [link]See our quick guide[/link] to help you solve this and get your website sending."), "congratulationsLoadingHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("Verification"), "congratulationsLoadingBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Congrats, you’re sending your first newsletter! We’re doing a quick verification to make sure everything works fine."), "congratulationsMSSPitchHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("Your email has been sent!"), "congratulationsMSSPitchHeaderAutomated" => $this->extensions['MailPoet\Twig\I18n']->translate("You are all set up and ready to go!"), "congratulationsMSSPitchSubHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("What’s next? Sign up to the MailPoet Starter plan for fast and reliable email delivery"), "sendingStatusTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sending status", "Page title. This page displays a list of emails along with their sending status: unprocessed, sent or failed."), "subscriber" => $this->extensions['MailPoet\Twig\I18n']->translate("Subscriber"), "sendingStatus" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sending status", "an email sending status: unprocessed, sent or failed."), "failureReason" => $this->extensions['MailPoet\Twig\I18n']->translate("Failure reason (if applicable)"), "resend" => $this->extensions['MailPoet\Twig\I18n']->translate("Resend"), "unprocessed" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Unprocessed", "status when the sending of a newsletter has not been processed"), "sent" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sent", "status when a newsletter has been sent"), "failed" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Failed", "status when the sending of a newsletter has failed"), "noSendingTaskFound" => $this->extensions['MailPoet\Twig\I18n']->translate("No sending task found."), "loadingStats" => $this->extensions['MailPoet\Twig\I18n']->translate("Loading..."), "backToList" => $this->extensions['MailPoet\Twig\I18n']->translate("Back"), "statsPreviewNewsletter" => $this->extensions['MailPoet\Twig\I18n']->translate("Preview"), "statsDateSent" => $this->extensions['MailPoet\Twig\I18n']->translate("Date"), "statsFromAddress" => $this->extensions['MailPoet\Twig\I18n']->translate("From"), "statsToSegments" => $this->extensions['MailPoet\Twig\I18n']->translate("To"), "statsReplyToAddress" => $this->extensions['MailPoet\Twig\I18n']->translate("Reply-to"), "statsTotalSent" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent to"), "percentageOpened" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("opened", "Percentage of subscribers that opened a newsletter link"), "percentageMachineOpened" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("machine-opened", "Percentage of newsletters that were opened by a machine"), "percentageMachineOpenedTooltip" => $this->extensions['MailPoet\Twig\I18n']->translate("A machine-opened email is an email opened by a computer in the background without the user’s explicit request or knowledge. [link]Read more[/link]"), "percentageClicked" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("clicked", "Percentage of subscribers that clicked a newsletter link"), "percentageUnsubscribed" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("unsubscribed", "Percentage of subscribers that unsubscribed from a newsletter"), "percentageBounced" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("bounced", "Percentage of subscribers that bounced from a newsletter"), "readMoreOnStats" => $this->extensions['MailPoet\Twig\I18n']->translate("Read more on stats."), "clickedLinks" => $this->extensions['MailPoet\Twig\I18n']->translate("Clicked Links"), "subscriberEngagement" => $this->extensions['MailPoet\Twig\I18n']->translate("Subscriber Engagement"), "googleAnalytics" => $this->extensions['MailPoet\Twig\I18n']->translate("GA campaign"), "bounces" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Bounces", "A tab title for the list of bounces (w.wiki/45Qc)"), "premiumBannerCtaFree" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade"), "gaCampaignLine" => $this->extensions['MailPoet\Twig\I18n']->translate("Google Analytics Campaign"), "gaCampaignTip" => $this->extensions['MailPoet\Twig\I18n']->translate("For example, “Spring email”. [link]Read the guide.[/link]"), "automaticEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("Automatic Email"), "tip" => $this->extensions['MailPoet\Twig\I18n']->translate("Tip:"), "selectAutomaticEmailsEventsConditionsHeading" => $this->extensions['MailPoet\Twig\I18n']->translate("Select %1s events conditions"), "sendAutomaticEmailWhenHeading" => $this->extensions['MailPoet\Twig\I18n']->translate("Send this %1s Automatic Email when..."), "automaticEmailActivated" => $this->extensions['MailPoet\Twig\I18n']->translate("Your %1s Automatic Email is now activated!"), "automaticEmailActivationFailed" => $this->extensions['MailPoet\Twig\I18n']->translate("Your %1s Automatic Email could not be activated, please check the settings."), "automaticEmailEventOptionsNotConfigured" => $this->extensions['MailPoet\Twig\I18n']->translate("You need to configure email options before this email can be sent."), "sentToXCustomers" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent to %1\$d customers"), "wooCommerceEmailsWarning" => $this->extensions['MailPoet\Twig\I18n']->translate("WooCommerce emails won’t be sent to new customers because the opt-in on checkout is disabled. Enable it so they can immediately get your emails after their first purchase."), "wooCommerceEmailsWarningLink" => $this->extensions['MailPoet\Twig\I18n']->translate("Edit WooCommerce settings"), "reEngagementEmailsDisableIfTrackingIs" => $this->extensions['MailPoet\Twig\I18n']->translate("Re-engagement emails are disabled because [link]open and click tracking[/link] is disabled."), "unsubscribeLinkMissing" => $this->extensions['MailPoet\Twig\I18n']->translate("All emails must include an \"Unsubscribe\" link. Add a footer widget to your email to continue."), "reEngageLinkMissing" => $this->extensions['MailPoet\Twig\I18n']->translate("A re-engagement email must include a link with [link:subscription_re_engage_url] shortcode."), "newsletterIsEmpty" => $this->extensions['MailPoet\Twig\I18n']->translate("Poet, please add prose to your masterpiece before you send it to your followers."), "automatedLatestContentMissing" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Please add an “Automatic Latest Content” widget to the email from the right sidebar.", "(Please reuse the current translation used for the string “Automatic Latest Content”) This Error message is displayed when a user tries to send a “Post Notification” email without any “Automatic Latest Content” widget inside"), "emailAlreadySent" => $this->extensions['MailPoet\Twig\I18n']->translate("This email has already been sent. It can be edited, but not sent again. Duplicate this email if you want to send it again."), "emailCanBeScheduledUpToFiveYears" => $this->extensions['MailPoet\Twig\I18n']->translate("An email can only be scheduled up to 5 years in the future. Please choose a shorter period.")]);
        // line 433
        echo "
  ";
        // line 434
        $this->loadTemplate("mss_pitch_translations.html", "newsletters.html", 434)->display($context);
    }

    // line 437
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 438
        echo "  ";
        echo do_action("mailpoet_newsletters_translations_after");
        echo "
";
    }

    public function getTemplateName()
    {
        return "newsletters.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  259 => 438,  255 => 437,  251 => 434,  248 => 433,  245 => 68,  241 => 67,  234 => 63,  224 => 55,  220 => 53,  216 => 52,  212 => 51,  208 => 50,  204 => 49,  200 => 48,  196 => 47,  192 => 46,  188 => 45,  184 => 44,  180 => 43,  175 => 41,  171 => 40,  167 => 39,  163 => 38,  158 => 36,  154 => 35,  150 => 34,  146 => 33,  141 => 31,  137 => 30,  132 => 28,  127 => 26,  123 => 25,  119 => 24,  114 => 22,  110 => 21,  106 => 20,  102 => 19,  98 => 18,  94 => 17,  90 => 16,  86 => 15,  82 => 14,  78 => 13,  74 => 12,  70 => 11,  66 => 10,  62 => 9,  57 => 8,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletters.html", "/home/circleci/mailpoet/mailpoet/views/newsletters.html");
    }
}
