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

/* upgrade.html */
class __TwigTemplate_dada0e5bfec8608eb9f123444f9396788d2220a0663c5d2c4a893ecbc6a06709 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'after_javascript' => [$this, 'block_after_javascript'],
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
        $this->parent = $this->loadTemplate("layout.html", "upgrade.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div class=\"mailpoet-premium-page\">
  <div class=\"mailpoet-premium-page-intro mailpoet-premium-page-section mailpoet-grid-two-columns\">
    <div>
      <div class=\"mailpoet-gap-large\"></div>
      <h1 class=\"mailpoet-h1\">
        ";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Take your email marketing strategy<br> to the next level", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h1>
      <p>
        ";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Ready to level up your email marketing efforts?", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        ";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Upgrade your plan to unlock MailPoet’s <b>advanced features</b>, access to <b>detailed analytics</b>, and <b>priority support</b>.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </p>
      <div class=\"mailpoet-premium-page-intro-link-wrap\">
        <a
          target=\"_blank\"
          href=\"";
        // line 18
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->addReferralId((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&g=business&billing=monthly&email=") . \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 18)) . "&utm_source=plugin&utm_medium=premium&utm_campaign=purchase")), "html_attr");
        echo "\"
          class=\"mailpoet-button button-primary\"
        >
          ";
        // line 21
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Upgrade", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </a>
      </div>
      <div class=\"mailpoet-gap-large\"></div>
    </div>
    <img
      src=\"";
        // line 27
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("premium/1-we-take-care.svg");
        echo "\"
      alt=\"";
        // line 28
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image bird feeder");
        echo "\"
    >
  </div>

  <hr>

  ";
        // line 34
        if ((($context["subscriber_count"] ?? null) < 1000)) {
            // line 35
            echo "  <div class=\"mailpoet-premium-page-section mailpoet-premium-page-section-narrow mailpoet-premium-page-text-center\">
    <h1 class=\"mailpoet-h0\">
      ";
            // line 37
            echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("MailPoet Starter plan is actually yours for free", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
            echo "
    </h1>
    <p class=\"mailpoet-premium-page-section-large\">
      ";
            // line 40
            echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Website owners with less than 1,000 subscribers, like you, can get their emails delivered reliably for free with MailPoet Sending Service", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
            echo "
    </p>
    <a
      target=\"_blank\"
      href=\"";
            // line 44
            echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->addReferralId((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&g=starter&billing=monthly&email=") . \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 44)) . "&utm_source=plugin&utm_medium=premium&utm_campaign=purchase")), "html_attr");
            echo "\"
      class=\"mailpoet-button button-primary\"
    >
      ";
            // line 47
            echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sign up for free", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
            echo "
    </a>
  </div>

  <hr>
  ";
        }
        // line 53
        echo "
  <div class=\"mailpoet-premium-page-upgrade-to-premium mailpoet-premium-page-section\">
    <div class=\" mailpoet-premium-page-section-narrow mailpoet-premium-page-text-center\">
      <h1 class=\"mailpoet-h0\">
        ";
        // line 57
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Fuel growth with<br>a MailPoet Business plan", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h1>
      <p>
        ";
        // line 60
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Everything you need to take your email marketing (and business!) to the next level.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </p>
    </div>
    <div class=\"mailpoet-grid-three-columns mailpoet-premium-three-columns-two-orphans\">
      <div>
        <h2 class=\"mailpoet-h2\">
          ";
        // line 66
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Optimize with detailed engagement statistics", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h2>
        <p>
          ";
        // line 69
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Learn more about your subscribers and optimize your campaigns.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 70
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("<b>Understand how your subscribers engage with your emails</b> via open, click, and unsubscribe statistics.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 71
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("And if you manage a WooCommerce store, you’ll be able to see <b>how your email campaigns influence purchase behavior, including total revenue, number of orders, and the products purchased.</b>", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <p>
          ";
        // line 74
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use these valuable insights to make incremental improvements to your email campaigns, and better tailor your marketing to your target audience to <b>increase engagement and conversions!</b>", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
      </div>
      <div>
        <h2 class=\"mailpoet-h2\">
          ";
        // line 79
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Get personal with multi-segment criteria", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h2>
        <p>
          ";
        // line 82
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("The more you can tailor your email campaigns to your subscribers’ interests or behavior, the higher the engagement and conversion levels you can expect to see.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 83
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("When you upgrade to a paid plan, you’ll <b>unlock the ability to combine subscriber segments</b> in order to further personalize your email campaigns.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <p>
          ";
        // line 86
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("For example, if you’d like to invite your most loyal, long-term subscribers to sign up to receive exclusive content, you could combine the “subscribe date” option with “number of email opens” to send an email to this subset of subscribers inviting them to sign up.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 87
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Or if you run a WooCommerce store and you’d like to encourage one-time customers to return and make an additional purchase, you could combine the “number of orders” segment with “purchased in this category” to send an exclusive discount tailored to their purchase history.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
      </div>
      <div>
        <h2 class=\"mailpoet-h2\">
          ";
        // line 92
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Track engagement with Google Analytics", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h2>
        <p>
          ";
        // line 95
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("<b>Discover the part your email marketing campaigns play in your overall marketing strategy</b> by connecting MailPoet with your Google Analytics account.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 96
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Add a custom campaign name for each of your email campaigns, then <b>track how your subscribers are engaging with your website content</b> (through behavior metrics such as time on site, pages per visit, etc.), where they’re based (filter by city, country, or continent), and how they’re viewing your content (screen resolution, device, operating system, etc.).", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <p>
          ";
        // line 99
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Unlike in MailPoet, you won’t be able to track the identity of each visitor. This is against Google’s policy, which we respect.", "This text resides in the Uprade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
      </div>
      <div>
        <h2 class=\"mailpoet-h2\">
          ";
        // line 104
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Expert help on hand, whenever you need it", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h2>
        <p>
          ";
        // line 107
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("If you’re ever not sure on how to use any of our features, what the best way to achieve your email marketing goals would be, or you run into a technical issue, you’ll have <b>direct access to our expert customer support team</b>.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <p>
          ";
        // line 110
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Reach out to them via our priority support form, or by using the chat icon on your dashboard, and one of our friendly experts will respond within 48 hours.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>

      </div>
      <div>
        <h2 class=\"mailpoet-h2\">
          ";
        // line 116
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Email that scales with you", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h2>
        <p>
          ";
        // line 119
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Whether you send 10 or 100,000 emails a day, if you use the MailPoet Sending Service, <b>your emails will always be delivered on time, every time</b>.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 120
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Our advanced email infrastructure routinely handles small business newsletters through to huge mailing lists with tens of thousands of subscribers.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <p>
          ";
        // line 123
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Our plans scale as your list grows, with an automated upgrade option available for up to 200,000 subscribers.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          ";
        // line 124
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Going beyond 200,000? No problem. Contact our friendly support team to discuss plan options and pricing.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
      </div>
    </div>
  </div>

  <hr>

  <div class=\"mailpoet-premium-page-get-started mailpoet-premium-page-section mailpoet-premium-page-text-center\">
    <h1 class=\"mailpoet-h0\">
      ";
        // line 134
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Upgrade and unlock MailPoet’s advanced features", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
    </h1>
    <p>
      ";
        // line 137
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Choose the MailPoet plan that’s right for you.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
    </p>
    <div class=\"mailpoet-premium-page-options mailpoet-grid-two-columns\">
      <div>
        <div class=\"mailpoet-premium-page-options-label-wrap\">
          <div class=\"mailpoet-premium-page-options-label\">
            ";
        // line 143
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Recommended", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
          </div>
        </div>
        <h1 class=\"mailpoet-h0\">
          ";
        // line 147
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Business", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h1>
        <p class=\"mailpoet-premium-page-text-large\">
          ";
        // line 150
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("From \$10/month,<br>based on 500 subscribers", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <h3>
          ";
        // line 153
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Send with: MailPoet Sending Service", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h3>
        <div class=\"mailpoet-premium-page-options-divider\"></div>
        <h3 class=\"mailpoet-premium-feature-list-heading\">
          ";
        // line 157
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("This plan includes:", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h3>
        <ul>
          <li>";
        // line 160
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use MailPoet on 1 website", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 161
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Send unlimited emails", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 162
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Without MailPoet branding", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 163
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Priority customer support", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        </ul>
        <h3 class=\"mailpoet-premium-feature-list-heading\">
          ";
        // line 166
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("All Starter features, plus:", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h3>
        <ul>
          <li>";
        // line 169
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Detailed engagement statistics", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 170
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscriber segmentation", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 171
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Google Analytics integration", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        </ul>
        <a
          target=\"_blank\"
          href=\"";
        // line 175
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->addReferralId((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&g=business&billing=monthly&email=") . \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 175)) . "&utm_source=plugin&utm_medium=premium&utm_campaign=purchase")), "html_attr");
        echo "\"
          class=\"mailpoet-button button-primary\"
        >
          ";
        // line 178
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Get started", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </a>
      </div>
      <div>
        <div class=\"mailpoet-premium-page-options-label-wrap\"></div>
        <h1 class=\"mailpoet-h0\">
          ";
        // line 184
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Creator", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h1>
        <p class=\"mailpoet-premium-page-text-large\">
          ";
        // line 187
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("From \$8/month,<br>based on 500 subscribers", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </p>
        <h3>
          ";
        // line 190
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Send with: Your own sending method", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h3>
        <h3 class=\"mailpoet-premium-feature-list-heading\">
          ";
        // line 193
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("This plan includes:", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h3>
        <ul>
          <li>";
        // line 196
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use MailPoet on 1 website", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 197
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Without MailPoet branding", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 198
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Priority customer support", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        </ul>
        <h3 class=\"mailpoet-premium-feature-list-heading\">
          ";
        // line 201
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("All Starter features, plus:", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </h3>
        <ul>
          <li>";
        // line 204
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Detailed engagement statistics", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 205
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscriber segmentation", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
          <li>";
        // line 206
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Google Analytics integration", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        </ul>
        <div class=\"mailpoet-premium-page-options-divider\"></div>
        <a
          target=\"_blank\"
          href=\"";
        // line 211
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->addReferralId((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&g=creator&billing=monthly&email=") . \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 211)) . "&utm_source=plugin&utm_medium=premium&utm_campaign=purchase")), "html_attr");
        echo "\"
          class=\"mailpoet-button button-primary\"
        >
          ";
        // line 214
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Get started", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        </a>
      </div>
    </div>
  </div>

  <hr>

  <div class=\"mailpoet-premium-page-our-agency-license mailpoet-premium-page-section mailpoet-grid-two-columns\">
    <div>
      <h1 class=\"mailpoet-h0\">
        ";
        // line 225
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Want to use MailPoet on multiple websites? Choose the Agency plan", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h1>
      <p>
        ";
        // line 228
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Our Agency plan has been designed for those who maintain or build WordPress and WooCommerce solutions for multiple clients.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
        ";
        // line 229
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Quickly deploy an easy to learn email solution on up to 50 websites, and rest assured that your clients’ emails make it to the inbox with the MailPoet Sending Service.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </p>
      <h3>
        ";
        // line 232
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("From \$30/month, based on 500 subscribers per website", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h3>
      <h3>
        ";
        // line 235
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Send with: MailPoet Sending Service", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h3>
      <h3>
        ";
        // line 238
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("This plan includes:", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h3>
      <ul class=\"mailpoet-premium-page-bullet-list\">
        <li>";
        // line 241
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use MailPoet on 50 websites", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        <li>";
        // line 242
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Send unlimited emails", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        <li>";
        // line 243
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Without MailPoet branding", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        <li>";
        // line 244
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Priority customer support", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
      </ul>
      <h3>
        ";
        // line 247
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("All Starter features, plus:", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </h3>
      <ul class=\"mailpoet-premium-page-bullet-list\">
        <li>";
        // line 250
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Detailed engagement statistics", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        <li>";
        // line 251
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscriber segmentation", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
        <li>";
        // line 252
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Google Analytics integration", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "</li>
      </ul>
      <a
        target=\"_blank\"
        href=\"";
        // line 256
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->addReferralId((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&g=agency&billing=monthly&email=") . \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 256)) . "&utm_source=plugin&utm_medium=premium&utm_campaign=purchase")), "html_attr");
        echo "\"
        class=\"mailpoet-button button-primary\"
      >
        ";
        // line 259
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Buy Now", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      </a>
    </div>
    <img
      src=\"";
        // line 263
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("premium/3-our-agency-license.svg");
        echo "\"
      alt=\"";
        // line 264
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image woodpecker");
        echo "\"
    >
  </div>

  <hr>

  <div class=\"mailpoet-premium-page-section mailpoet-premium-page-section-narrow mailpoet-premium-page-text-center\">
    <h1 class=\"mailpoet-h0\">
      ";
        // line 272
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Get started today!", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
    </h1>
    <p class=\"mailpoet-premium-page-text-large\">
      ";
        // line 275
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Over 500,000 people trust MailPoet to power their email marketing campaigns – why not join them?!", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
      ";
        // line 276
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Upgrade now to grow your business and achieve your business goals.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
    </p>
    <a
      target=\"_blank\"
      href=\"";
        // line 280
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\Functions']->addReferralId((((("https://account.mailpoet.com/?s=" . ($context["subscriber_count"] ?? null)) . "&g=business&billing=monthly&email=") . \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_wp_user"] ?? null), "user_email", [], "any", false, false, false, 280)) . "&utm_source=plugin&utm_medium=premium&utm_campaign=purchase")), "html_attr");
        echo "\"
      class=\"mailpoet-button button-primary\"
    >
      ";
        // line 283
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Upgrade now", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade");
        echo "
    </a>
    <p>
      ";
        // line 286
        echo MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translateWithContext("And if you’re not sure which plan is the right one for you, our [link]Customer Support team[/link] are on hand to help you decide.", "This text resides in the Upgrade page: /wp-admin/admin.php?page=mailpoet-upgrade"), "https://www.mailpoet.com/support/sales-pre-sales-questions/", ["target" => "_blank"]);
        // line 290
        echo "
    </p>
  </div>
</div>

<div class=\"mailpoet-premium-page-footer-image\">
  <img
    src=\"";
        // line 297
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("premium/5-footer.png");
        echo "\"
    alt=\"";
        // line 298
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image rooster crowing");
        echo "\"
  >
</div>
";
    }

    // line 303
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 304
        echo "<script type=\"text/javascript\">
  MailPoet.trackEvent('Premium page viewed');
</script>
";
    }

    public function getTemplateName()
    {
        return "upgrade.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  631 => 304,  627 => 303,  619 => 298,  615 => 297,  606 => 290,  604 => 286,  598 => 283,  592 => 280,  585 => 276,  581 => 275,  575 => 272,  564 => 264,  560 => 263,  553 => 259,  547 => 256,  540 => 252,  536 => 251,  532 => 250,  526 => 247,  520 => 244,  516 => 243,  512 => 242,  508 => 241,  502 => 238,  496 => 235,  490 => 232,  484 => 229,  480 => 228,  474 => 225,  460 => 214,  454 => 211,  446 => 206,  442 => 205,  438 => 204,  432 => 201,  426 => 198,  422 => 197,  418 => 196,  412 => 193,  406 => 190,  400 => 187,  394 => 184,  385 => 178,  379 => 175,  372 => 171,  368 => 170,  364 => 169,  358 => 166,  352 => 163,  348 => 162,  344 => 161,  340 => 160,  334 => 157,  327 => 153,  321 => 150,  315 => 147,  308 => 143,  299 => 137,  293 => 134,  280 => 124,  276 => 123,  270 => 120,  266 => 119,  260 => 116,  251 => 110,  245 => 107,  239 => 104,  231 => 99,  225 => 96,  221 => 95,  215 => 92,  207 => 87,  203 => 86,  197 => 83,  193 => 82,  187 => 79,  179 => 74,  173 => 71,  169 => 70,  165 => 69,  159 => 66,  150 => 60,  144 => 57,  138 => 53,  129 => 47,  123 => 44,  116 => 40,  110 => 37,  106 => 35,  104 => 34,  95 => 28,  91 => 27,  82 => 21,  76 => 18,  68 => 13,  64 => 12,  58 => 9,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "upgrade.html", "/home/circleci/mailpoet/mailpoet/views/upgrade.html");
    }
}
