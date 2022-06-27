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
        // line 43
        echo "
<!-- stylesheets -->
";
        // line 45
        echo $this->extensions['MailPoet\Twig\Assets']->generateStylesheet("mailpoet-plugin.css");
        // line 47
        echo "

";
        // line 49
        echo do_action("mailpoet_styles_admin_after");
        echo "

";
        // line 51
        $this->displayBlock('after_css', $context, $blocks);
        // line 52
        echo "
<script type=\"text/javascript\">
  var mailpoet_datetime_format = \"";
        // line 54
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_date_format = \"";
        // line 55
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPDateFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_time_format = \"";
        // line 56
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->getWPTimeFormat(), "js"), "html", null, true);
        echo "\";
  var mailpoet_version = \"";
        // line 57
        echo $this->extensions['MailPoet\Twig\Functions']->getMailPoetVersion();
        echo "\";
  var mailpoet_locale = \"";
        // line 58
        echo $this->extensions['MailPoet\Twig\Functions']->getTwoLettersLocale();
        echo "\";
  var mailpoet_wp_week_starts_on = \"";
        // line 59
        echo $this->extensions['MailPoet\Twig\Functions']->getWPStartOfWeek();
        echo "\";
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
</script>

<!-- javascripts -->
";
        // line 78
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("runtime.js", "vendor.js", "commons.js", "mailpoet.js");
        // line 83
        echo "

";
        // line 85
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["ajaxFailedErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server has responded with response code %d"), "ajaxTimeoutErrorMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("An error has happened while performing a request, the server request has timed out after %d seconds"), "senderEmailAddressWarning1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You might not reach the inbox of your subscribers if you use this email address.", "In the last step, before sending a newsletter. URL: ?page=mailpoet-newsletters#/send/2"), "senderEmailAddressWarning2" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use an address like %1\$s for the Sender and put %2\$s in the <em>Reply-to</em> field below.", "In the last step, before sending a newsletter. URL: ?page=mailpoet-newsletters#/send/2"), "senderEmailAddressWarning3" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Read more."), "mailerSendingResumedNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Sending has been resumed."), "mailerSendingNotResumedUnauthorized" => $this->extensions['MailPoet\Twig\I18n']->translate("Failed to resume sending because the email address is unauthorized. Please authorize it and try again."), "dismissNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Dismiss this notice."), "subscribersLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you now have more than [subscribersLimit] subscribers!"), "freeVersionLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Our free version is limited to [subscribersLimit] subscribers."), "yourPlanLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("Your plan is limited to [subscribersLimit] subscribers."), "youNeedToUpgrade" => $this->extensions['MailPoet\Twig\I18n']->translate("You need to upgrade now to be able to continue using MailPoet."), "youCanDisableWPUsersList" => $this->extensions['MailPoet\Twig\I18n']->translate("If you do not send emails to your WordPress Users list, you can [link]disable it[/link] to lower your number of subscribers."), "upgradeNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade Now"), "refreshMySubscribers" => $this->extensions['MailPoet\Twig\I18n']->translate("I’ve upgraded my subscription, refresh subscriber limit"), "viewFilteredSubscribersMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("View everyone subscribed to \"%1\$s\""), "emailVolumeLimitNoticeTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Congratulations, you sent more than [emailVolumeLimit] emails this month!"), "youReachedEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("You have sent more emails this month than your MailPoet plan includes ([emailVolumeLimit]), and sending has been temporarily paused."), "toContinueUpgradeYourPlanOrWaitUntil" => $this->extensions['MailPoet\Twig\I18n']->translate("To continue sending with MailPoet Sending Service please [link]upgrade your plan[/link], or wait until sending is automatically resumed on <b>[date]</b>."), "refreshMyEmailVolumeLimit" => $this->extensions['MailPoet\Twig\I18n']->translate("I’ve upgraded my subscription, refresh monthly email limit"), "setFromAddressModalTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("It’s time to set your default FROM address!", "mailpoet"), "setFromAddressModalDescription" => $this->extensions['MailPoet\Twig\I18n']->translate("Set one of [link]your authorized email addresses[/link] as the default FROM email for your MailPoet emails.", "mailpoet"), "setFromAddressModalSave" => $this->extensions['MailPoet\Twig\I18n']->translate("Save", "mailpoet"), "setFromAddressEmailSuccess" => $this->extensions['MailPoet\Twig\I18n']->translate("Excellent. Your authorized email was saved. You can change it in the [link]Basics tab of the MailPoet settings[/link].", "mailpoet"), "setFromAddressEmailNotAuthorized" => $this->extensions['MailPoet\Twig\I18n']->translate("Can’t use this email yet! [link]Please authorize it first[/link].", "mailpoet"), "setFromAddressEmailUnknownError" => $this->extensions['MailPoet\Twig\I18n']->translate("An error occured when saving FROM email address.", "mailpoet"), "reviewRequestHeading" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Thank you! Time to tell the world?", "After a user gives us positive feedback via the NPS poll, we ask them to review our plugin on WordPress.org."), "reviewRequestDidYouKnow" => $this->extensions['MailPoet\Twig\I18n']->translate("[username], did you know that hundreds of WordPress users read the reviews on the plugin repository? They’re also a source of inspiration for our team."), "reviewRequestUsingForDays" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [days] day now, and we would love to read your own review.", "You’ve been using MailPoet for [days] days now, and we would love to read your own review.",         // line 118
($context["installed_days_ago"] ?? null)), "reviewRequestUsingForMonths" => $this->extensions['MailPoet\Twig\I18n']->pluralize("You’ve been using MailPoet for [months] month now, and we would love to read your own review.", "You’ve been using MailPoet for [months] months now, and we would love to read your own review.", \MailPoetVendor\twig_round((        // line 119
($context["installed_days_ago"] ?? null) / 30))), "reviewRequestRateUsNow" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Rate us now", "Review our plugin on WordPress.org."), "reviewRequestNotNow" => $this->extensions['MailPoet\Twig\I18n']->translate("Not now"), "sent" => $this->extensions['MailPoet\Twig\I18n']->translate("Sent"), "notSentYet" => $this->extensions['MailPoet\Twig\I18n']->translate("Not sent yet!"), "allSendingPausedHeader" => $this->extensions['MailPoet\Twig\I18n']->translate("All sending is currently paused!"), "allSendingPausedBody" => $this->extensions['MailPoet\Twig\I18n']->translate("Your [link]API key[/link] to send with MailPoet is invalid."), "allSendingPausedLink" => $this->extensions['MailPoet\Twig\I18n']->translate("Purchase a key"), "close" => $this->extensions['MailPoet\Twig\I18n']->translate("Close"), "today" => $this->extensions['MailPoet\Twig\I18n']->translate("Today"), "january" => $this->extensions['MailPoet\Twig\I18n']->translate("January"), "february" => $this->extensions['MailPoet\Twig\I18n']->translate("February"), "march" => $this->extensions['MailPoet\Twig\I18n']->translate("March"), "april" => $this->extensions['MailPoet\Twig\I18n']->translate("April"), "may" => $this->extensions['MailPoet\Twig\I18n']->translate("May"), "june" => $this->extensions['MailPoet\Twig\I18n']->translate("June"), "july" => $this->extensions['MailPoet\Twig\I18n']->translate("July"), "august" => $this->extensions['MailPoet\Twig\I18n']->translate("August"), "september" => $this->extensions['MailPoet\Twig\I18n']->translate("September"), "october" => $this->extensions['MailPoet\Twig\I18n']->translate("October"), "november" => $this->extensions['MailPoet\Twig\I18n']->translate("November"), "december" => $this->extensions['MailPoet\Twig\I18n']->translate("December"), "januaryShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Jan"), "februaryShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Feb"), "marchShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Mar"), "aprilShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Apr"), "mayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("May"), "juneShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Jun"), "julyShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Jul"), "augustShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Aug"), "septemberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Sep"), "octoberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Oct"), "novemberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Nov"), "decemberShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Dec"), "sundayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Sun"), "mondayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Mon"), "tuesdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Tue"), "wednesdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Wed"), "thursdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Thu"), "fridayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Fri"), "saturdayShort" => $this->extensions['MailPoet\Twig\I18n']->translate("Sat"), "sundayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("S", "Sunday - one letter abbreviation"), "mondayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("M", "Monday - one letter abbreviation"), "tuesdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("T", "Tuesday - one letter abbreviation"), "wednesdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("W", "Wednesday - one letter abbreviation"), "thursdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("T", "Thursday - one letter abbreviation"), "fridayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("F", "Friday - one letter abbreviation"), "saturdayMin" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("S", "Saturday - one letter abbreviation")]);
        // line 170
        echo "
";
        // line 171
        $this->displayBlock('translations', $context, $blocks);
        // line 172
        echo "
";
        // line 173
        $this->displayBlock('after_translations', $context, $blocks);
        // line 174
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("admin_vendor.js");
        // line 176
        echo "

";
        // line 178
        echo do_action("mailpoet_scripts_admin_before");
        echo "

";
        // line 180
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("admin.js");
        // line 182
        echo "

";
        // line 184
        if ($this->extensions['MailPoet\Twig\Functions']->libs3rdPartyEnabled()) {
            // line 185
            echo "  ";
            echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("lib/analytics.js");
            echo "

  ";
            // line 187
            $context["helpscout_form_id"] = "1c666cab-c0f6-4614-bc06-e5d0ad78db2b";
            // line 188
            echo "  ";
            if (((\MailPoetVendor\twig_get_attribute($this->env, $this->source, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["mailpoet_api_key_state"] ?? null), "data", [], "any", false, false, false, 188), "support_tier", [], "any", false, false, false, 188) == "premium") || (\MailPoetVendor\twig_get_attribute($this->env, $this->source, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["premium_key_state"] ?? null), "data", [], "any", false, false, false, 188), "support_tier", [], "any", false, false, false, 188) == "premium"))) {
                // line 189
                echo "    ";
                $context["helpscout_form_id"] = "e93d0423-1fa6-4bbc-9df9-c174f823c35f";
                // line 190
                echo "  ";
            }
            // line 191
            echo "
  <script type=\"text/javascript\">!function(e,t,n){function a(){var e=t.getElementsByTagName(\"script\")[0],n=t.createElement(\"script\");n.type=\"text/javascript\",n.async=!0,n.src=\"https://beacon-v2.helpscout.net\",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],\"complete\"===t.readyState)return a();e.attachEvent?e.attachEvent(\"onload\",a):e.addEventListener(\"load\",a,!1)}(window,document,window.Beacon||function(){});</script>

  <script type=\"text/javascript\"></script>

  <script type=\"text/javascript\">
    if(window['Beacon'] !== undefined && window.hide_mailpoet_beacon !== true) {
      window.Beacon('init', '";
            // line 198
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["helpscout_form_id"] ?? null), "html", null, true);
            echo "');

      // HelpScout Beacon: Configuration
      window.Beacon(\"config\", {
        icon: 'message',
        zIndex: 50000,
        instructions: \"";
            // line 204
            echo $this->extensions['MailPoet\Twig\I18n']->translate("Want to give feedback to the MailPoet team? Contact us here. Please provide as much information as possible!");
            echo "\",
        showContactFields: true
      });

      // HelpScout Beacon: Custom information
      window.Beacon(\"identify\",
        ";
            // line 210
            echo json_encode($this->extensions['MailPoet\Twig\Helpscout']->getHelpscoutData());
            echo "
      );

      if (window.mailpoet_beacon_articles) {
        window.Beacon('suggest', window.mailpoet_beacon_articles)
      }
    }
  </script>
";
        }
        // line 219
        echo "<script>
  Parsley.addMessages('mailpoet', {
    defaultMessage: '";
        // line 221
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
    type: {
      email: '";
        // line 223
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid email.");
        echo "',
      url: '";
        // line 224
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid url.");
        echo "',
      number: '";
        // line 225
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid number.");
        echo "',
      integer: '";
        // line 226
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be a valid integer.");
        echo "',
      digits: '";
        // line 227
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be digits.");
        echo "',
      alphanum: '";
        // line 228
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be alphanumeric.");
        echo "'
    },
    notblank: '";
        // line 230
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should not be blank.");
        echo "',
    required: '";
        // line 231
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is required.");
        echo "',
    pattern: '";
        // line 232
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value seems to be invalid.");
        echo "',
    min: '";
        // line 233
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be greater than or equal to %s.");
        echo "',
    max: '";
        // line 234
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be lower than or equal to %s.");
        echo "',
    range: '";
        // line 235
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be between %s and %s.");
        echo "',
    minlength: '";
        // line 236
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too short. It should have %s characters or more.");
        echo "',
    maxlength: '";
        // line 237
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value is too long. It should have %s characters or fewer.");
        echo "',
    length: '";
        // line 238
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value length is invalid. It should be between %s and %s characters long.");
        echo "',
    mincheck: '";
        // line 239
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select at least %s choices.");
        echo "',
    maxcheck: '";
        // line 240
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select %s choices or fewer.");
        echo "',
    check: '";
        // line 241
        echo $this->extensions['MailPoet\Twig\I18n']->translate("You must select between %s and %s choices.");
        echo "',
    equalto: '";
        // line 242
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This value should be the same.");
        echo "'
  });

  Parsley.setLocale('mailpoet');
</script>
";
        // line 247
        $this->displayBlock('after_javascript', $context, $blocks);
        // line 248
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
  <!-- notices -->
  <div id=\"mailpoet_notice_error\" class=\"mailpoet_notice\" style=\"display:none;\"></div>
  <div id=\"mailpoet_notice_success\" class=\"mailpoet_notice\" style=\"display:none;\"></div>
  <!-- React notices -->
  <div id=\"mailpoet_notices\"></div>

  <!-- Set FROM address modal React root -->
  <div id=\"mailpoet_set_from_address_modal\"></div>

  <!-- title block -->
  ";
        // line 38
        $this->displayBlock('title', $context, $blocks);
        // line 39
        echo "  <!-- content block -->
  ";
        // line 40
        $this->displayBlock('content', $context, $blocks);
        // line 41
        echo "</div>
";
    }

    // line 38
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 40
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 51
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 171
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 173
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 247
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
        return array (  442 => 247,  436 => 173,  430 => 171,  424 => 51,  418 => 40,  412 => 38,  407 => 41,  405 => 40,  402 => 39,  400 => 38,  387 => 27,  383 => 26,  377 => 23,  372 => 248,  370 => 247,  362 => 242,  358 => 241,  354 => 240,  350 => 239,  346 => 238,  342 => 237,  338 => 236,  334 => 235,  330 => 234,  326 => 233,  322 => 232,  318 => 231,  314 => 230,  309 => 228,  305 => 227,  301 => 226,  297 => 225,  293 => 224,  289 => 223,  284 => 221,  280 => 219,  268 => 210,  259 => 204,  250 => 198,  241 => 191,  238 => 190,  235 => 189,  232 => 188,  230 => 187,  224 => 185,  222 => 184,  218 => 182,  216 => 180,  211 => 178,  207 => 176,  205 => 174,  203 => 173,  200 => 172,  198 => 171,  195 => 170,  193 => 119,  192 => 118,  191 => 85,  187 => 83,  185 => 78,  178 => 74,  174 => 73,  170 => 72,  166 => 71,  162 => 70,  156 => 67,  152 => 66,  148 => 65,  144 => 64,  140 => 63,  136 => 62,  132 => 61,  128 => 60,  124 => 59,  120 => 58,  116 => 57,  112 => 56,  108 => 55,  104 => 54,  100 => 52,  98 => 51,  93 => 49,  89 => 47,  87 => 45,  83 => 43,  81 => 26,  77 => 24,  75 => 23,  62 => 12,  53 => 6,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "layout.html", "/home/circleci/mailpoet/mailpoet/views/layout.html");
    }
}
