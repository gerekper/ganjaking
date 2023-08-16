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
        echo "<!-- pre connect to 3d party to speed up page loading -->
<link rel=\"preconnect\" href=\"https://widget.docsbot.ai/\">
<link rel=\"dns-prefetch\" href=\"https://widget.docsbot.ai/\">
<link rel=\"preconnect\" href=\"http://cdn.mxpnl.com\">
<link rel=\"dns-prefetch\" href=\"http://cdn.mxpnl.com\">

<!-- system notices -->
<div id=\"mailpoet_notice_system\" class=\"mailpoet_notice\" style=\"display:none;\"></div>

<!-- handlebars templates -->
";
        // line 11
        $this->displayBlock('templates', $context, $blocks);
        // line 12
        echo "
<!-- main container -->
";
        // line 14
        $this->displayBlock('container', $context, $blocks);
        // line 35
        echo "
";
        // line 36
        echo do_action("mailpoet_styles_admin_after");
        echo "

";
        // line 38
        $this->displayBlock('after_css', $context, $blocks);
        // line 39
        echo "
<script type=\"text/javascript\">
  var mailpoet_datetime_format = \"";
        // line 41
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_date_format = \"";
        // line 42
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_time_format = \"";
        // line 43
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_version = \"";
        // line 44
        echo $this->extensions['MailPoet\Twig\Functions']->getMailPoetVersion();
        echo "\";
  var mailpoet_locale = \"";
        // line 45
        echo $this->extensions['MailPoet\Twig\Functions']->getTwoLettersLocale();
        echo "\";
  var mailpoet_wp_week_starts_on = \"";
        // line 46
        echo $this->extensions['MailPoet\Twig\Functions']->getWPStartOfWeek();
        echo "\";
  var mailpoet_urls = ";
        // line 47
        echo json_encode(($context["urls"] ?? null));
        echo ";
  var mailpoet_premium_version = ";
        // line 48
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getMailPoetPremiumVersion());
        echo ";
  var mailpoet_main_page_slug =   ";
        // line 49
        echo json_encode(($context["main_page"] ?? null));
        echo ";
  var mailpoet_3rd_party_libs_enabled = ";
        // line 50
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled()), "html", null, true);
        echo ";
  var mailpoet_analytics_enabled = ";
        // line 51
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Analytics']->isEnabled()), "html", null, true);
        echo ";
  var mailpoet_analytics_data = ";
        // line 52
        echo json_encode($this->extensions['MailPoet\Twig\Analytics']->generateAnalytics());
        echo ";
  var mailpoet_analytics_public_id = ";
        // line 53
        echo json_encode($this->extensions['MailPoet\Twig\Analytics']->getPublicId());
        echo ";
  var mailpoet_analytics_new_public_id = ";
        // line 54
        echo \MailPoetVendor\twig_escape_filter($this->env, json_encode($this->extensions['MailPoet\Twig\Analytics']->isPublicIdNew()), "html", null, true);
        echo ";
  var mailpoet_free_domains = ";
        // line 55
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getFreeDomains());
        echo ";
  var mailpoet_woocommerce_active = ";
        // line 56
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->isWoocommerceActive());
        echo ";
  var mailpoet_woocommerce_store_config = ";
        // line 57
        echo json_encode(($context["woocommerce_store_config"] ?? null));
        echo ";
  // RFC 5322 standard; http://emailregex.com/ combined with https://google.github.io/closure-library/api/goog.format.EmailAddress.html#isValid
  var mailpoet_email_regex = /(?=^[+a-zA-Z0-9_.!#\$%&'*\\/=?^`{|}~-]+@([a-zA-Z0-9-]+\\.)+[a-zA-Z0-9]{2,63}\$)(?=^(([^<>()\\[\\]\\\\.,;:\\s@\"]+(\\.[^<>()\\[\\]\\\\.,;:\\s@\"]+)*)|(\".+\"))@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}])|(([a-zA-Z\\-0-9]+\\.)+[a-zA-Z]{2,})))/;
  var mailpoet_feature_flags = ";
        // line 60
        echo json_encode(($context["feature_flags"] ?? null));
        echo ";
  var mailpoet_referral_id = ";
        // line 61
        echo json_encode(($context["referral_id"] ?? null));
        echo ";
  var mailpoet_feature_announcement_has_news = ";
        // line 62
        echo json_encode(($context["feature_announcement_has_news"] ?? null));
        echo ";
  var mailpoet_wp_segment_state = ";
        // line 63
        echo json_encode(($context["wp_segment_state"] ?? null));
        echo ";
  var mailpoet_mta_method = '";
        // line 64
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mta_method"] ?? null), "html", null, true);
        echo "';
  var mailpoet_tracking_config = ";
        // line 65
        echo json_encode(($context["tracking_config"] ?? null));
        echo ";
  var mailpoet_is_new_user = ";
        // line 66
        echo json_encode((($context["is_new_user"] ?? null) == true));
        echo ";
  var mailpoet_installed_days_ago = ";
        // line 67
        echo json_encode(($context["installed_days_ago"] ?? null));
        echo ";
  var mailpoet_send_transactional_emails = ";
        // line 68
        echo json_encode(($context["send_transactional_emails"] ?? null));
        echo ";
  var mailpoet_transactional_emails_opt_in_notice_dismissed = ";
        // line 69
        echo json_encode(($context["transactional_emails_opt_in_notice_dismissed"] ?? null));
        echo ";
  var mailpoet_deactivate_subscriber_after_inactive_days = ";
        // line 70
        echo json_encode(($context["deactivate_subscriber_after_inactive_days"] ?? null));
        echo ";
  var mailpoet_woocommerce_version = ";
        // line 71
        echo json_encode($this->extensions['MailPoet\Twig\Functions']->getWooCommerceVersion());
        echo ";
  var mailpoet_track_wizard_loaded_via_woocommerce = ";
        // line 72
        echo json_encode(($context["track_wizard_loaded_via_woocommerce"] ?? null));
        echo ";
  var mailpoet_mail_function_enabled = '";
        // line 73
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mail_function_enabled"] ?? null), "html", null, true);
        echo "';
  var mailpoet_admin_plugins_url = '";
        // line 74
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["admin_plugins_url"] ?? null), "html", null, true);
        echo "';

  var mailpoet_site_name = '";
        // line 76
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo "';
  var mailpoet_site_url = \"";
        // line 77
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["site_url"] ?? null), "html", null, true);
        echo "\";
  var mailpoet_site_address = '";
        // line 78
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["site_address"] ?? null), "html", null, true);
        echo "';

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
  var mailpoet_mss_key_valid = ";
        // line 92
        echo json_encode(($context["mss_key_valid"] ?? null));
        echo ";
  var mailpoet_mss_key_pending_approval = '";
        // line 93
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["mss_key_pending_approval"] ?? null), "html", null, true);
        echo "';
  var mailpoet_mss_active = ";
        // line 94
        echo json_encode(($context["mss_active"] ?? null));
        echo ";
  var mailpoet_plugin_partial_key = '";
        // line 95
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["plugin_partial_key"] ?? null), "html", null, true);
        echo "';
  var mailpoet_hide_automations = ";
        // line 96
        echo json_encode(($context["mailpoet_hide_automations"] ?? null));
        echo ";
  var mailpoet_subscribers_count = ";
        // line 97
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["subscriber_count"] ?? null), "html", null, true);
        echo ";
  var mailpoet_subscribers_counts_cache_created_at = ";
        // line 98
        echo json_encode(($context["subscribers_counts_cache_created_at"] ?? null));
        echo ";
  var mailpoet_subscribers_limit = ";
        // line 99
        ((($context["subscribers_limit"] ?? null)) ? (print (\MailPoetVendor\twig_escape_filter($this->env, ($context["subscribers_limit"] ?? null), "html", null, true))) : (print ("false")));
        echo ";
  var mailpoet_subscribers_limit_reached = ";
        // line 100
        echo json_encode(($context["subscribers_limit_reached"] ?? null));
        echo ";
  var mailpoet_email_volume_limit = ";
        // line 101
        echo json_encode(($context["email_volume_limit"] ?? null));
        echo ";
  var mailpoet_email_volume_limit_reached = ";
        // line 102
        echo json_encode(($context["email_volume_limit_reached"] ?? null));
        echo ";
  var mailpoet_cdn_url = ";
        // line 103
        echo json_encode($this->extensions['MailPoet\Twig\Assets']->generateCdnUrl(""));
        echo ";
  var mailpoet_tags = ";
        // line 104
        echo json_encode(($context["tags"] ?? null));
        echo ";

  ";
        // line 106
        if ( !($context["premium_plugin_active"] ?? null)) {
            // line 107
            echo "    var mailpoet_free_premium_subscribers_limit = ";
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["free_premium_subscribers_limit"] ?? null), "html", null, true);
            echo ";
  ";
        }
        // line 109
        echo "</script>

<!-- javascripts -->
";
        // line 112
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("runtime.js", "vendor.js", "mailpoet.js");
        // line 116
        echo "

";
        // line 118
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["topBarLogoTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to section root"), "topBarUpdates" => $this->extensions['MailPoet\Twig\I18n']->translate("Updates"), "whatsNew" => $this->extensions['MailPoet\Twig\I18n']->translate("What’s new"), "updateMailPoetNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("[link]Update MailPoet[/link] to see the latest changes"), "ajaxFailedErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server has responded with response code %d"), "ajaxTimeoutErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server request has timed out after %d seconds"), "senderEmailAddressWarning1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You might not reach the inbox of your subscribers if you use this email address.", "In the last step, before sending a newsletter. URL: ?page=mailpoet-newsletters#/send/2"), "senderEmailAddressWarning3" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more."), "mailerSendingNotResumedUnauthorized" => $this->extensions['MailPoet\Twig\I18n']->translate("Failed to resume sending because the email address is unauthorized. Please authorize it and try again."), "dismissNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Dismiss this notice."), "confirmEdit" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending is in progress. Do you want to pause sending and edit the newsletter?"), "confirmAutomaticNewsletterEdit" => $this->extensions['MailPoet\Twig\I18n']->translate("To edit this email, it needs to be deactivated. You can activate it again after you make the changes."), "subscribersLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Action required: Upgrade your plan for more than [subscribersLimit] subscribers!"), "subscribersLimitNoticeTitleUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Action required: Upgrade your plan!"), "subscribersLimitReached" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations on reaching over [subscribersLimit] subscribers!"), "subscribersLimitReachedUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more subscribers than your plan’s limit!"), "freeVersionLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers."), "yourPlanLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Your plan is limited to [subscribersLimit] subscribers."), "youNeedToUpgrade" => $this->extensions['MailPoet\Twig\I18n']->translate("To continue using MailPoet without interruption, it’s time to upgrade your plan."), "actToSeamlessService" => $this->extensions['MailPoet\Twig\I18n']->translate("Act now to ensure seamless service to your growing audience."), "checkHowToManageSubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("Alternatively, [link]check how to manage your subscribers[/link] to keep your numbers below your plan’s limit."), "upgradeNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade Now"), "refreshMySubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("Refresh subscriber limit"), "emailVolumeLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you sent more than [emailVolumeLimit] emails this month!"), "emailVolumeLimitNoticeTitleUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you sent a lot of emails this month!"), "youReachedEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("You have sent more emails this month than your MailPoet plan includes ([emailVolumeLimit]), and sending has been temporarily paused."), "youReachedEmailVolumeLimitUnknownLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("You have sent more emails this month than your MailPoet plan includes, and sending has been temporarily paused."), "toContinueUpgradeYourPlanOrWaitUntil" => $this->extensions['MailPoet\Twig\I18n']->translate("To continue sending with MailPoet Sending Service please [link]upgrade your plan[/link], or wait until sending is automatically resumed on <b>[date]</b>."), "refreshMyEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Refresh monthly email limit"), "manageSenderDomainHeaderSubtitle" => $this->extensions['MailPoet\Twig\I18n']->translate("To help your audience and MailPoet authenticate you as the domain owner, please add the following DNS records to your domain’s DNS and click “Verify the DNS records”. Please note that it may take up to 24 hours for DNS changes to propagate after you make the change. [link]Read the guide[/link].", "mailpoet"), "reviewRequestHeading" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Thank you! Time to tell the world?", "After a user gives us positive feedback via the NPS poll, we ask them to review our plugin on WordPress.org."), "reviewRequestDidYouKnow" => $this->extensions['MailPoet\Twig\I18n']->translate("[username], did you know that hundreds of WordPress users read the reviews on the plugin repository? They’re also a source of inspiration for our team."), "reviewRequestUsingForDays" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [days] day now, and we would love to read your own review.", "You’ve been using MailPoet for [days] days now, and we would love to read your own review.",         // line 154
($context["installed_days_ago"] ?? null)), "reviewRequestUsingForMonths" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [months] month now, and we would love to read your own review.", "You’ve been using MailPoet for [months] months now, and we would love to read your own review.", \MailPoetVendor\twig_round((        // line 155
($context["installed_days_ago"] ?? null) / 30))), "reviewRequestRateUsNow" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Rate us now", "Review our plugin on WordPress.org."), "reviewRequestNotNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Not now"), "sent" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent"), "notSentYet" => $this->extensions['MailPoet\Twig\I18n']->translate("Not sent yet!"), "renderingProblem" => $this->extensions['MailPoet\Twig\I18n']->translate("There was a problem with rendering!", "mailpoet"), "allSendingPausedHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("All sending is currently paused!"), "allSendingPausedBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your [link]API key[/link] to send with MailPoet is invalid."), "allSendingPausedLink" => $this->extensions['MailPoet\Twig\I18n']->translate("Purchase a key"), "transactionalEmailNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Good news! MailPoet can now send your website’s emails too"), "transactionalEmailNoticeBody" => $this->extensions['MailPoet\Twig\I18n']->translate("All of your WordPress and WooCommerce emails are sent with your hosting company, unless you have an SMTP plugin. Would you like such emails to be delivered with MailPoet’s active sending method for better deliverability?"), "transactionalEmailNoticeBodyReadMore" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more.", "This is a link that leads to more information about transactional emails"), "transactionalEmailNoticeCTA" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Enable", "Button, after clicking it we will enable transactional emails"), "mailerSendErrorNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending has been paused due to a technical issue with %1\$s"), "mailerSendErrorCheckConfiguration" => $this->extensions['MailPoet\Twig\I18n']->translate("Please check your sending method configuration, you may need to consult with your hosting company."), "mailerSendErrorUseSendingService" => $this->extensions['MailPoet\Twig\I18n']->translate("The easy alternative is to <b>send emails with MailPoet Sending Service</b> instead, like thousands of other users do."), "mailerSendErrorSignUpForSendingService" => $this->extensions['MailPoet\Twig\I18n']->translate("Sign up for free in minutes"), "mailerConnectionErrorNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending is paused because the following connection issue prevents MailPoet from delivering emails"), "mailerErrorCode" => $this->extensions['MailPoet\Twig\I18n']->translate("Error code: %1\$s"), "mailerCheckSettingsNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Check your [link]sending method settings[/link]."), "mailerResumeSendingButton" => $this->extensions['MailPoet\Twig\I18n']->translate("Resume sending"), "mailerResumeSendingAfterUpgradeButton" => $this->extensions['MailPoet\Twig\I18n']->translate("I have upgraded my subscription, resume sending"), "topBarLogoTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Back to section root"), "close" => $this->extensions['MailPoet\Twig\I18n']->translate("Close"), "today" => $this->extensions['MailPoet\Twig\I18n']->translate("Today")]);
        // line 186
        echo "
";
        // line 187
        $this->displayBlock('translations', $context, $blocks);
        // line 188
        echo "
";
        // line 189
        $this->displayBlock('after_translations', $context, $blocks);
        // line 190
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("admin_vendor.js");
        // line 192
        echo "

";
        // line 194
        echo do_action("mailpoet_scripts_admin_before");
        echo "

";
        // line 196
        if (($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled() &&  !$this->extensions['MailPoet\Twig\Functions']->isDotcomEcommercePlan())) {
            // line 197
            echo "  ";
            echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("lib/analytics.js");
            echo "
  <script type=\"text/javascript\">window.DocsBotAI=window.DocsBotAI||{},DocsBotAI.init=function(c){return new Promise(function(e,o){var t=document.createElement(\"script\");t.type=\"text/javascript\",t.async=!0,t.src=\"https://widget.docsbot.ai/chat.js\";var n=document.getElementsByTagName(\"script\")[0];n.parentNode.insertBefore(t,n),t.addEventListener(\"load\",function(){window.DocsBotAI.mount({id:c.id,supportCallback:c.supportCallback,identify:c.identify,options:c.options});var t;t=function(n){return new Promise(function(e){if(document.querySelector(n))return e(document.querySelector(n));var o=new MutationObserver(function(t){document.querySelector(n)&&(e(document.querySelector(n)),o.disconnect())});o.observe(document.body,{childList:!0,subtree:!0})})},t&&t(\"#docsbotai-root\").then(e).catch(o)}),t.addEventListener(\"error\",function(t){o(t.message)})})};</script>
  <script type=\"text/javascript\">
    DocsBotAI.init({
      id: \"TqTdebbGjJeUjrmBIFjh/kzFE5FBebBJiSJ2Tm0nR\",
      // We want to redirect users to the proper page depending on their plan
      supportCallback: function (event, history) {
        ";
            // line 204
            if (($context["has_premium_support"] ?? null)) {
                // line 205
                echo "          const mailpoet_redirect_support_link = 'https://www.mailpoet.com/support/premium/';
        ";
            } else {
                // line 207
                echo "          const mailpoet_redirect_support_link = 'https://wordpress.org/support/plugin/mailpoet/';
        ";
            }
            // line 209
            echo "        event.preventDefault(); // Prevent default behavior opening the url.
        window.open(mailpoet_redirect_support_link, '_blank');
      },
    });
  </script>
";
        }
        // line 215
        echo "
<script>
  Parsley.addMessages('mailpoet', {
    defaultMessage: '";
        // line 218
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
    type: {
      email: '";
        // line 220
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid email.");
        echo "',
      url: '";
        // line 221
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid url.");
        echo "',
      number: '";
        // line 222
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid number.");
        echo "',
      integer: '";
        // line 223
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid integer.");
        echo "',
      digits: '";
        // line 224
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be digits.");
        echo "',
      alphanum: '";
        // line 225
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be alphanumeric.");
        echo "'
    },
    notblank: '";
        // line 227
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should not be blank.");
        echo "',
    required: '";
        // line 228
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is required.");
        echo "',
    pattern: '";
        // line 229
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
    min: '";
        // line 230
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be greater than or equal to %s.");
        echo "',
    max: '";
        // line 231
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be lower than or equal to %s.");
        echo "',
    range: '";
        // line 232
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be between %s and %s.");
        echo "',
    minlength: '";
        // line 233
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too short. It should have %s characters or more.");
        echo "',
    maxlength: '";
        // line 234
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too long. It should have %s characters or fewer.");
        echo "',
    length: '";
        // line 235
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value length is invalid. It should be between %s and %s characters long.");
        echo "',
    mincheck: '";
        // line 236
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select at least %s choices.");
        echo "',
    maxcheck: '";
        // line 237
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select %s choices or fewer.");
        echo "',
    check: '";
        // line 238
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select between %s and %s choices.");
        echo "',
    equalto: '";
        // line 239
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be the same.");
        echo "'
  });

  Parsley.setLocale('mailpoet');
</script>
";
        // line 244
        $this->displayBlock('after_javascript', $context, $blocks);
        // line 245
        echo "<div id=\"mailpoet-modal\"></div>
";
    }

    // line 11
    public function block_templates($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 14
    public function block_container($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 15
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
        // line 30
        $this->displayBlock('title', $context, $blocks);
        // line 31
        echo "  <!-- content block -->
  ";
        // line 32
        $this->displayBlock('content', $context, $blocks);
        // line 33
        echo "</div>
";
    }

    // line 30
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 32
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 38
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 187
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 189
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 244
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
        return array (  563 => 244,  557 => 189,  551 => 187,  545 => 38,  539 => 32,  533 => 30,  528 => 33,  526 => 32,  523 => 31,  521 => 30,  504 => 15,  500 => 14,  494 => 11,  489 => 245,  487 => 244,  479 => 239,  475 => 238,  471 => 237,  467 => 236,  463 => 235,  459 => 234,  455 => 233,  451 => 232,  447 => 231,  443 => 230,  439 => 229,  435 => 228,  431 => 227,  426 => 225,  422 => 224,  418 => 223,  414 => 222,  410 => 221,  406 => 220,  401 => 218,  396 => 215,  388 => 209,  384 => 207,  380 => 205,  378 => 204,  367 => 197,  365 => 196,  360 => 194,  356 => 192,  354 => 190,  352 => 189,  349 => 188,  347 => 187,  344 => 186,  342 => 155,  341 => 154,  340 => 118,  336 => 116,  334 => 112,  329 => 109,  323 => 107,  321 => 106,  316 => 104,  312 => 103,  308 => 102,  304 => 101,  300 => 100,  296 => 99,  292 => 98,  288 => 97,  284 => 96,  280 => 95,  276 => 94,  272 => 93,  268 => 92,  264 => 91,  260 => 90,  256 => 89,  252 => 88,  248 => 87,  244 => 86,  240 => 85,  236 => 84,  232 => 83,  228 => 82,  224 => 81,  218 => 78,  214 => 77,  210 => 76,  205 => 74,  201 => 73,  197 => 72,  193 => 71,  189 => 70,  185 => 69,  181 => 68,  177 => 67,  173 => 66,  169 => 65,  165 => 64,  161 => 63,  157 => 62,  153 => 61,  149 => 60,  143 => 57,  139 => 56,  135 => 55,  131 => 54,  127 => 53,  123 => 52,  119 => 51,  115 => 50,  111 => 49,  107 => 48,  103 => 47,  99 => 46,  95 => 45,  91 => 44,  87 => 43,  83 => 42,  79 => 41,  75 => 39,  73 => 38,  68 => 36,  65 => 35,  63 => 14,  59 => 12,  57 => 11,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "layout.html", "/home/circleci/mailpoet/mailpoet/views/layout.html");
    }
}
