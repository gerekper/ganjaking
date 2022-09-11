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

/* layout.html */
class __TwigTemplate_8581a8ede6c645c86df82d14a0f9727da4ff92aa0bb85d1970f9d5219782671e extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'templates' => [$this, 'block_templates'],
            'container' => [$this, 'block_container'],
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
            'after_css' => [$this, 'block_after_css'],
            'translations' => [$this, 'block_translations'],
            'after_translations' => [$this, 'block_after_translations'],
            'after_javascript' => [$this, 'block_after_javascript'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        if (($context["sub_menu"] ?? null)) {
            // line 2
            echo "<script type=\"text/javascript\">
jQuery('#adminmenu #toplevel_page_mailpoet-newsletters')
  .addClass('wp-has-current-submenu')
  .removeClass('wp-not-current-submenu')
  .find('a[href\$=\"";
            // line 6
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["sub_menu"] ?? null), "html", null, true);
            echo "\"]')
  .addClass('current')
  .parent()
  .addClass('current');
</script>
";
        }
        // line 12
        echo "
<!-- pre connect to 3d party to speed up page loading -->
<link rel=\"preconnect\" href=\"https://beacon-v2.helpscout.net/\">
<link rel=\"dns-prefetch\" href=\"https://beacon-v2.helpscout.net/\">
<link rel=\"preconnect\" href=\"http://cdn.mxpnl.com\">
<link rel=\"dns-prefetch\" href=\"http://cdn.mxpnl.com\">

<!-- system notices -->
<div id=\"mailpoet_notice_system\" class=\"mailpoet_notice\" style=\"display:none;\"></div>

<!-- handlebars templates -->
";
        // line 23
        $this->displayBlock('templates', $context, $blocks);
        // line 24
        echo "
<!-- main container -->
";
        // line 26
        $this->displayBlock('container', $context, $blocks);
        // line 47
        echo "
";
        // line 48
        echo do_action("mailpoet_styles_admin_after");
        echo "

";
        // line 50
        $this->displayBlock('after_css', $context, $blocks);
        // line 51
        echo "
<script type=\"text/javascript\">
  var mailpoet_datetime_format = \"";
        // line 53
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_date_format = \"";
        // line 54
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_time_format = \"";
        // line 55
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_version = \"";
        // line 56
        echo $this->extensions['MailPoet\Twig\Functions']->getMailPoetVersion();
        echo "\";
  var mailpoet_locale = \"";
        // line 57
        echo $this->extensions['MailPoet\Twig\Functions']->getTwoLettersLocale();
        echo "\";
  var mailpoet_wp_week_starts_on = \"";
        // line 58
        echo $this->extensions['MailPoet\Twig\Functions']->getWPStartOfWeek();
        echo "\";
  var mailpoet_urls = ";
        // line 59
        echo json_encode(($context["urls"] ?? null));
        echo ";
  var mailpoet_premium_version = ";
        // line 60
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getMailPoetPremiumVersion());
        echo ";
  var mailpoet_3rd_party_libs_enabled = ";
        // line 61
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled()), "html", null, true);
        echo ";
  var mailpoet_analytics_enabled = ";
        // line 62
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Analytics']->isEnabled()), "html", null, true);
        echo ";
  var mailpoet_analytics_data = ";
        // line 63
        echo json_encode($this->extensions['MailPoet\Twig\Analytics']->generateAnalytics());
        echo ";
  var mailpoet_analytics_public_id = ";
        // line 64
        echo json_encode($this->extensions['MailPoet\Twig\Analytics']->getPublicId());
        echo ";
  var mailpoet_analytics_new_public_id = ";
        // line 65
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Analytics']->isPublicIdNew()), "html", null, true);
        echo ";
  var mailpoet_free_domains = ";
        // line 66
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getFreeDomains());
        echo ";
  var mailpoet_woocommerce_active = ";
        // line 67
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->isWoocommerceActive());
        echo ";
  // RFC 5322 standard; http://emailregex.com/ combined with https://google.github.io/closure-library/api/goog.format.EmailAddress.html#isValid
  var mailpoet_email_regex = /(?=^[+a-zA-Z0-9_.!#\$%&'*\\/=?^`{|}~-]+@([a-zA-Z0-9-]+\\.)+[a-zA-Z0-9]{2,63}\$)(?=^(([^<>()\\[\\]\\\\.,;:\\s@\"]+(\\.[^<>()\\[\\]\\\\.,;:\\s@\"]+)*)|(\".+\"))@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}])|(([a-zA-Z\\-0-9]+\\.)+[a-zA-Z]{2,})))/;
  var mailpoet_feature_flags = ";
        // line 70
        echo json_encode(($context["feature_flags"] ?? null));
        echo ";
  var mailpoet_referral_id = ";
        // line 71
        echo json_encode(($context["referral_id"] ?? null));
        echo ";
  var mailpoet_feature_announcement_has_news = ";
        // line 72
        echo json_encode(($context["feature_announcement_has_news"] ?? null));
        echo ";
  var mailpoet_wp_segment_state = ";
        // line 73
        echo json_encode(($context["wp_segment_state"] ?? null));
        echo ";
  var mailpoet_mta_method = '";
        // line 74
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mta_method"] ?? null), "html", null, true);
        echo "';
  var mailpoet_tracking_config = ";
        // line 75
        echo json_encode(($context["tracking_config"] ?? null));
        echo ";
  var mailpoet_is_new_user = ";
        // line 76
        echo json_encode((($context["is_new_user"] ?? null) == true));
        echo ";
  var mailpoet_installed_days_ago = ";
        // line 77
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["installed_days_ago"] ?? null), "html", null, true);
        echo ";
  var mailpoet_deactivate_subscriber_after_inactive_days = ";
        // line 78
        echo json_encode(($context["deactivate_subscriber_after_inactive_days"] ?? null));
        echo ";

  // Premium status
  var mailpoet_current_wp_user_email = '";
        // line 81
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, ($context["current_wp_user_email"] ?? null), "js"), "html", null, true);
        echo "';
  var mailpoet_premium_link = ";
        // line 82
        echo json_encode(($context["link_premium"] ?? null));
        echo ";
  var mailpoet_premium_plugin_installed = ";
        // line 83
        echo json_encode(($context["premium_plugin_installed"] ?? null));
        echo ";
  var mailpoet_premium_active = ";
        // line 84
        echo json_encode(($context["premium_plugin_active"] ?? null));
        echo ";
  var mailpoet_premium_plugin_download_url = ";
        // line 85
        echo json_encode(($context["premium_plugin_download_url"] ?? null));
        echo ";
  var mailpoet_premium_plugin_activation_url = ";
        // line 86
        echo json_encode(($context["premium_plugin_activation_url"] ?? null));
        echo ";
  var mailpoet_has_valid_api_key = ";
        // line 87
        echo json_encode(($context["has_valid_api_key"] ?? null));
        echo ";
  var mailpoet_has_valid_premium_key = ";
        // line 88
        echo json_encode(($context["has_valid_premium_key"] ?? null));
        echo ";
  var mailpoet_has_premium_support = ";
        // line 89
        echo json_encode(($context["has_premium_support"] ?? null));
        echo ";
  var has_mss_key_specified = ";
        // line 90
        echo json_encode(($context["has_mss_key_specified"] ?? null));
        echo ";
  var mailpoet_mss_key_invalid = ";
        // line 91
        echo json_encode(($context["mss_key_invalid"] ?? null));
        echo ";
  var mailpoet_mss_key_pending_approval = '";
        // line 92
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mss_key_pending_approval"] ?? null), "html", null, true);
        echo "';
  var mailpoet_mss_active = ";
        // line 93
        echo json_encode(($context["mss_active"] ?? null));
        echo ";
  var mailpoet_plugin_partial_key = '";
        // line 94
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["plugin_partial_key"] ?? null), "html", null, true);
        echo "';
  var mailpoet_subscribers_count = ";
        // line 95
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["subscriber_count"] ?? null), "html", null, true);
        echo ";
  var mailpoet_subscribers_counts_cache_created_at = ";
        // line 96
        echo json_encode(($context["subscribers_counts_cache_created_at"] ?? null));
        echo ";
  var mailpoet_subscribers_limit = ";
        // line 97
        ((($context["subscribers_limit"] ?? null)) ? (print (\MailPoetVendor\twig_escape_filter($this->env, ($context["subscribers_limit"] ?? null), "html", null, true))) : (print ("false")));
        echo ";
  var mailpoet_subscribers_limit_reached = ";
        // line 98
        echo json_encode(($context["subscribers_limit_reached"] ?? null));
        echo ";
  var mailpoet_email_volume_limit = ";
        // line 99
        echo json_encode(($context["email_volume_limit"] ?? null));
        echo ";
  var mailpoet_email_volume_limit_reached = ";
        // line 100
        echo json_encode(($context["email_volume_limit_reached"] ?? null));
        echo ";

  ";
        // line 102
        if ( !($context["premium_plugin_active"] ?? null)) {
            // line 103
            echo "    var mailpoet_free_premium_subscribers_limit = ";
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["free_premium_subscribers_limit"] ?? null), "html", null, true);
            echo ";
  ";
        }
        // line 105
        echo "</script>

<!-- javascripts -->
";
        // line 108
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("runtime.js", "vendor.js", "commons.js", "mailpoet.js");
        // line 113
        echo "

";
        // line 115
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["topBarLogoTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to section root"), "topBarUpdates" => $this->extensions['MailPoet\Twig\I18n']->translate("Updates"), "topBarTutorial" => $this->extensions['MailPoet\Twig\I18n']->translate("Tutorial"), "whatsNew" => $this->extensions['MailPoet\Twig\I18n']->translate("What’s new"), "updateMailPoetNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("[link]Update MailPoet[/link] to see the latest changes"), "ajaxFailedErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server has responded with response code %d"), "ajaxTimeoutErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server request has timed out after %d seconds"), "senderEmailAddressWarning1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You might not reach the inbox of your subscribers if you use this email address.", "In the last step, before sending a newsletter. URL: ?page=mailpoet-newsletters#/send/2"), "senderEmailAddressWarning2" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use an address like %1\$s for the Sender and put %2\$s in the <em>Reply-to</em> field below.", "In the last step, before sending a newsletter. URL: ?page=mailpoet-newsletters#/send/2"), "senderEmailAddressWarning3" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more."), "mailerSendingResumedNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending has been resumed."), "mailerSendingNotResumedUnauthorized" => $this->extensions['MailPoet\Twig\I18n']->translate("Failed to resume sending because the email address is unauthorized. Please authorize it and try again."), "dismissNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Dismiss this notice."), "subscribersLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more than [subscribersLimit] subscribers!"), "freeVersionLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers."), "yourPlanLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Your plan is limited to [subscribersLimit] subscribers."), "youNeedToUpgrade" => $this->extensions['MailPoet\Twig\I18n']->translate("You need to upgrade now to be able to continue using MailPoet."), "youCanDisableWPUsersList" => $this->extensions['MailPoet\Twig\I18n']->translate("If you do not send emails to your WordPress Users list, you can [link]disable it[/link] to lower your number of subscribers."), "upgradeNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade Now"), "refreshMySubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("I’ve upgraded my subscription, refresh subscriber limit"), "viewFilteredSubscribersMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("View subscribers"), "emailVolumeLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you sent more than [emailVolumeLimit] emails this month!"), "youReachedEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("You have sent more emails this month than your MailPoet plan includes ([emailVolumeLimit]), and sending has been temporarily paused."), "toContinueUpgradeYourPlanOrWaitUntil" => $this->extensions['MailPoet\Twig\I18n']->translate("To continue sending with MailPoet Sending Service please [link]upgrade your plan[/link], or wait until sending is automatically resumed on <b>[date]</b>."), "refreshMyEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("I’ve upgraded my subscription, refresh monthly email limit"), "setFromAddressModalTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("It’s time to set your default FROM address!", "mailpoet"), "setFromAddressModalDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Set one of [link]your authorized email addresses[/link] as the default FROM email for your MailPoet emails.", "mailpoet"), "setFromAddressModalSave" => $this->extensions['MailPoet\Twig\I18n']->translate("Save", "mailpoet"), "setFromAddressEmailSuccess" => $this->extensions['MailPoet\Twig\I18n']->translate("Excellent. Your authorized email was saved. You can change it in the [link]Basics tab of the MailPoet settings[/link].", "mailpoet"), "setFromAddressEmailNotAuthorized" => $this->extensions['MailPoet\Twig\I18n']->translate("Can’t use this email yet! [link]Please authorize it first[/link].", "mailpoet"), "setFromAddressEmailUnknownError" => $this->extensions['MailPoet\Twig\I18n']->translate("An error occured when saving FROM email address.", "mailpoet"), "manageSenderDomainHeaderTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Manage Sender Domain ", "mailpoet"), "manageSenderDomainHeaderSubtitle" => $this->extensions['MailPoet\Twig\I18n']->translate("To help your audience and MailPoet authenticate you as the domain owner, please add the following DNS records to your domain’s DNS and click “Verify the DNS records”. Please note that it may take up to 24 hours for DNS changes to propagate after you make the change. [link]Read the guide[/link].", "mailpoet"), "manageSenderDomainTableHeaderType" => $this->extensions['MailPoet\Twig\I18n']->translate("Type", "mailpoet"), "manageSenderDomainTableHeaderHost" => $this->extensions['MailPoet\Twig\I18n']->translate("Host", "mailpoet"), "manageSenderDomainTableHeaderValue" => $this->extensions['MailPoet\Twig\I18n']->translate("Value", "mailpoet"), "manageSenderDomainTableHeaderStatus" => $this->extensions['MailPoet\Twig\I18n']->translate("Status", "mailpoet"), "manageSenderDomainVerifyButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Verify the DNS records", "mailpoet"), "manageSenderDomainTooltipText" => $this->extensions['MailPoet\Twig\I18n']->translate("Click here to copy", "mailpoet"), "manageSenderDomainStatusPending" => $this->extensions['MailPoet\Twig\I18n']->translate("Pending", "mailpoet"), "manageSenderDomainStatusVerified" => $this->extensions['MailPoet\Twig\I18n']->translate("Verified", "mailpoet"), "manageSenderDomainStatusInvalid" => $this->extensions['MailPoet\Twig\I18n']->translate("Invalid", "mailpoet"), "authorizeSenderEmailAndDomainModalSenderEmailTabTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Authorized emails", "mailpoet"), "authorizeSenderEmailAndDomainModalSenderDomainTabTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Sender Domains", "mailpoet"), "authorizeSenderDomain" => $this->extensions['MailPoet\Twig\I18n']->translate("Email violates Sender Domain’s DMARC policy. Please set up [link]sender authentication[/link]."), "reviewRequestHeading" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Thank you! Time to tell the world?", "After a user gives us positive feedback via the NPS poll, we ask them to review our plugin on WordPress.org."), "reviewRequestDidYouKnow" => $this->extensions['MailPoet\Twig\I18n']->translate("[username], did you know that hundreds of WordPress users read the reviews on the plugin repository? They’re also a source of inspiration for our team."), "reviewRequestUsingForDays" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [days] day now, and we would love to read your own review.", "You’ve been using MailPoet for [days] days now, and we would love to read your own review.",         // line 168
($context["installed_days_ago"] ?? null)), "reviewRequestUsingForMonths" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [months] month now, and we would love to read your own review.", "You’ve been using MailPoet for [months] months now, and we would love to read your own review.", \MailPoetVendor\twig_round((        // line 169
($context["installed_days_ago"] ?? null) / 30))), "reviewRequestRateUsNow" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Rate us now", "Review our plugin on WordPress.org."), "reviewRequestNotNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Not now"), "authorizeSenderEmailModalTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Authorizing your email address: [senderEmail]", "mailpoet"), "authorizeSenderEmailModalDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Check your inbox now to confirm your email address. Please find the email with the subject: [bold]\"email address to authorize\"[/bold]", "mailpoet"), "authorizeSenderEmailMessageSuccess" => $this->extensions['MailPoet\Twig\I18n']->translate("The email address has been authorized. You can now send emails using this address with MailPoet.", "mailpoet"), "authorizeSenderEmailMessageError" => $this->extensions['MailPoet\Twig\I18n']->translate("An error occured when performing the request. Please try again later", "mailpoet"), "sent" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent"), "notSentYet" => $this->extensions['MailPoet\Twig\I18n']->translate("Not sent yet!"), "allSendingPausedHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("All sending is currently paused!"), "allSendingPausedBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your [link]API key[/link] to send with MailPoet is invalid."), "allSendingPausedLink" => $this->extensions['MailPoet\Twig\I18n']->translate("Purchase a key"), "topBarLogoTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to section root"), "close" => $this->extensions['MailPoet\Twig\I18n']->translate("Close"), "today" => $this->extensions['MailPoet\Twig\I18n']->translate("Today"), "january" => $this->extensions['MailPoet\Twig\I18n']->translate("January"), "february" => $this->extensions['MailPoet\Twig\I18n']->translate("February"), "march" => $this->extensions['MailPoet\Twig\I18n']->translate("March"), "april" => $this->extensions['MailPoet\Twig\I18n']->translate("April"), "may" => $this->extensions['MailPoet\Twig\I18n']->translate("May"), "june" => $this->extensions['MailPoet\Twig\I18n']->translate("June"), "july" => $this->extensions['MailPoet\Twig\I18n']->translate("July"), "august" => $this->extensions['MailPoet\Twig\I18n']->translate("August"), "september" => $this->extensions['MailPoet\Twig\I18n']->translate("September"), "october" => $this->extensions['MailPoet\Twig\I18n']->translate("October"), "november" => $this->extensions['MailPoet\Twig\I18n']->translate("November"), "december" => $this->extensions['MailPoet\Twig\I18n']->translate("December"), "januaryShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Jan"), "februaryShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Feb"), "marchShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Mar"), "aprilShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Apr"), "mayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("May"), "juneShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Jun"), "julyShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Jul"), "augustShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Aug"), "septemberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Sep"), "octoberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Oct"), "novemberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Nov"), "decemberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Dec"), "sundayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Sun"), "mondayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Mon"), "tuesdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Tue"), "wednesdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Wed"), "thursdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Thu"), "fridayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Fri"), "saturdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Sat"), "sundayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("S", "Sunday - one letter abbreviation"), "mondayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("M", "Monday - one letter abbreviation"), "tuesdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("T", "Tuesday - one letter abbreviation"), "wednesdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("W", "Wednesday - one letter abbreviation"), "thursdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("T", "Thursday - one letter abbreviation"), "fridayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("F", "Friday - one letter abbreviation"), "saturdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("S", "Saturday - one letter abbreviation")]);
        // line 227
        echo "
";
        // line 228
        $this->displayBlock('translations', $context, $blocks);
        // line 229
        echo "
";
        // line 230
        $this->displayBlock('after_translations', $context, $blocks);
        // line 231
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("admin_vendor.js");
        // line 233
        echo "

";
        // line 235
        echo do_action("mailpoet_scripts_admin_before");
        echo "

";
        // line 237
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("admin.js");
        // line 239
        echo "

";
        // line 241
        if ($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled()) {
            // line 242
            echo "  ";
            echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("lib/analytics.js");
            echo "

  ";
            // line 244
            $context["helpscout_form_id"] = "1c666cab-c0f6-4614-bc06-e5d0ad78db2b";
            // line 245
            echo "  ";
            if (((\MailPoetVendor\twig_get_attribute($this->env, $this->source, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["mailpoet_api_key_state"] ?? null), "data", [], "any", false, false, false, 245), "support_tier", [], "any", false, false, false, 245) == "premium") || (\MailPoetVendor\twig_get_attribute($this->env, $this->source, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["premium_key_state"] ?? null), "data", [], "any", false, false, false, 245), "support_tier", [], "any", false, false, false, 245) == "premium"))) {
                // line 246
                echo "    ";
                $context["helpscout_form_id"] = "e93d0423-1fa6-4bbc-9df9-c174f823c35f";
                // line 247
                echo "  ";
            }
            // line 248
            echo "
  <script type=\"text/javascript\">!function(e,t,n){function a(){var e=t.getElementsByTagName(\"script\")[0],n=t.createElement(\"script\");n.type=\"text/javascript\",n.async=!0,n.src=\"https://beacon-v2.helpscout.net\",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],\"complete\"===t.readyState)return a();e.attachEvent?e.attachEvent(\"onload\",a):e.addEventListener(\"load\",a,!1)}(window,document,window.Beacon||function(){});</script>

  <script type=\"text/javascript\"></script>

  <script type=\"text/javascript\">
    if(window['Beacon'] !== undefined && window.hide_mailpoet_beacon !== true) {
      window.Beacon('init', '";
            // line 255
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["helpscout_form_id"] ?? null), "html", null, true);
            echo "');

      // HelpScout Beacon: Configuration
      window.Beacon(\"config\", {
        icon: 'message',
        zIndex: 50000,
        instructions: \"";
            // line 261
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Want to give feedback to the MailPoet team? Contact us here. Please provide as much information as possible!");
            echo "\",
        showContactFields: true
      });

      // HelpScout Beacon: User identity information
      window.Beacon(\"identify\",
        ";
            // line 267
            echo json_encode($this->extensions['MailPoet\Twig\Helpscout']->getHelpscoutUserData());
            echo "
      );

      // HelpScout Beacon: Custom information
      window.Beacon(\"session-data\",
        ";
            // line 272
            echo json_encode($this->extensions['MailPoet\Twig\Helpscout']->getHelpscoutSiteData());
            echo "
    );

      if (window.mailpoet_beacon_articles) {
        window.Beacon('suggest', window.mailpoet_beacon_articles)
      }
    }
  </script>
";
        }
        // line 281
        echo "<script>
  Parsley.addMessages('mailpoet', {
    defaultMessage: '";
        // line 283
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
    type: {
      email: '";
        // line 285
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid email.");
        echo "',
      url: '";
        // line 286
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid url.");
        echo "',
      number: '";
        // line 287
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid number.");
        echo "',
      integer: '";
        // line 288
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid integer.");
        echo "',
      digits: '";
        // line 289
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be digits.");
        echo "',
      alphanum: '";
        // line 290
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be alphanumeric.");
        echo "'
    },
    notblank: '";
        // line 292
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should not be blank.");
        echo "',
    required: '";
        // line 293
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is required.");
        echo "',
    pattern: '";
        // line 294
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
    min: '";
        // line 295
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be greater than or equal to %s.");
        echo "',
    max: '";
        // line 296
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be lower than or equal to %s.");
        echo "',
    range: '";
        // line 297
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be between %s and %s.");
        echo "',
    minlength: '";
        // line 298
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too short. It should have %s characters or more.");
        echo "',
    maxlength: '";
        // line 299
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too long. It should have %s characters or fewer.");
        echo "',
    length: '";
        // line 300
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value length is invalid. It should be between %s and %s characters long.");
        echo "',
    mincheck: '";
        // line 301
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select at least %s choices.");
        echo "',
    maxcheck: '";
        // line 302
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select %s choices or fewer.");
        echo "',
    check: '";
        // line 303
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select between %s and %s choices.");
        echo "',
    equalto: '";
        // line 304
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be the same.");
        echo "'
  });

  Parsley.setLocale('mailpoet');
</script>
";
        // line 309
        $this->displayBlock('after_javascript', $context, $blocks);
        // line 310
        echo "<div id=\"mailpoet-modal\"></div>
";
    }

    // line 23
    public function block_templates($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 26
    public function block_container($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 27
        echo "<div class=\"wrap\">
  <div class=\"wp-header-end\"></div>
  <!-- notices -->
  <div id=\"mailpoet_notice_error\" class=\"mailpoet_notice\" style=\"display:none;\"></div>
  <div id=\"mailpoet_notice_success\" class=\"mailpoet_notice\" style=\"display:none;\"></div>
  <!-- React notices -->
  <div id=\"mailpoet_notices\"></div>

  <!-- Set FROM address modal React root -->
  <div id=\"mailpoet_set_from_address_modal\"></div>

  <!-- Set Authorize sender email React root -->
  <div id=\"mailpoet_authorize_sender_email_modal\"></div>

  <!-- title block -->
  ";
        // line 42
        $this->displayBlock('title', $context, $blocks);
        // line 43
        echo "  <!-- content block -->
  ";
        // line 44
        $this->displayBlock('content', $context, $blocks);
        // line 45
        echo "</div>
";
    }

    // line 42
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 44
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 50
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 228
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 230
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 309
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "layout.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  560 => 309,  554 => 230,  548 => 228,  542 => 50,  536 => 44,  530 => 42,  525 => 45,  523 => 44,  520 => 43,  518 => 42,  501 => 27,  497 => 26,  491 => 23,  486 => 310,  484 => 309,  476 => 304,  472 => 303,  468 => 302,  464 => 301,  460 => 300,  456 => 299,  452 => 298,  448 => 297,  444 => 296,  440 => 295,  436 => 294,  432 => 293,  428 => 292,  423 => 290,  419 => 289,  415 => 288,  411 => 287,  407 => 286,  403 => 285,  398 => 283,  394 => 281,  382 => 272,  374 => 267,  365 => 261,  356 => 255,  347 => 248,  344 => 247,  341 => 246,  338 => 245,  336 => 244,  330 => 242,  328 => 241,  324 => 239,  322 => 237,  317 => 235,  313 => 233,  311 => 231,  309 => 230,  306 => 229,  304 => 228,  301 => 227,  299 => 169,  298 => 168,  297 => 115,  293 => 113,  291 => 108,  286 => 105,  280 => 103,  278 => 102,  273 => 100,  269 => 99,  265 => 98,  261 => 97,  257 => 96,  253 => 95,  249 => 94,  245 => 93,  241 => 92,  237 => 91,  233 => 90,  229 => 89,  225 => 88,  221 => 87,  217 => 86,  213 => 85,  209 => 84,  205 => 83,  201 => 82,  197 => 81,  191 => 78,  187 => 77,  183 => 76,  179 => 75,  175 => 74,  171 => 73,  167 => 72,  163 => 71,  159 => 70,  153 => 67,  149 => 66,  145 => 65,  141 => 64,  137 => 63,  133 => 62,  129 => 61,  125 => 60,  121 => 59,  117 => 58,  113 => 57,  109 => 56,  105 => 55,  101 => 54,  97 => 53,  93 => 51,  91 => 50,  86 => 48,  83 => 47,  81 => 26,  77 => 24,  75 => 23,  62 => 12,  53 => 6,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "layout.html", "/home/circleci/mailpoet/mailpoet/views/layout.html");
    }
}
