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

/* homepage.html */
class __TwigTemplate_e93f1843f587ff72b997c53dfebf2ffe3fe3c73139e95193f6816aeb1e21eb2c extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'after_css' => [$this, 'block_after_css'],
            'translations' => [$this, 'block_translations'],
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
        $this->parent = $this->loadTemplate("layout.html", "homepage.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div id=\"mailpoet_homepage_container\">
</div>

<script>
  ";
        // line 9
        echo "    var mailpoet_mta_log = ";
        echo json_encode(($context["mta_log"] ?? null));
        echo ";
    var mailpoet_homepage_data = ";
        // line 10
        echo json_encode(($context["homepage"] ?? null));
        echo ";
  ";
        // line 12
        echo "</script>

";
    }

    // line 16
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 17
        echo $this->extensions['MailPoet\Twig\Assets']->generateStylesheet("mailpoet-homepage.css");
        echo "
";
    }

    // line 20
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 21
        echo "  ";
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["welcomeToMailPoet" => $this->extensions['MailPoet\Twig\I18n']->translate("Welcome to MailPoet 🎉"), "beginByCompletingSetup" => $this->extensions['MailPoet\Twig\I18n']->translate("Begin by completing your setup"), "hideList" => $this->extensions['MailPoet\Twig\I18n']->translate("Hide setup list"), "senderSetTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Add your sender information", "Link in a task list"), "senderSetTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sender information added!", "Link in a task list"), "mssConnectedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Connect MailPoet sending service", "Link in a task list"), "mssConnectedTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("MailPoet sending service activated!", "Link in a task list"), "wooSubscribersImportedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import WooCommerce subscribers", "Link in a task list"), "wooSubscribersImportedTaskDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("WooCommerce subscribers imported!", "Link in a task list"), "subscribersAddedTask" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import existing subscribers", "Link in a task list"), "subscribersAddedTaskDoneByImport" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscribers imported!", "Link in a task list"), "subscribersAddedTaskDoneByForm" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Subscription form created!", "Link in a task list"), "youAreSet" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You’re all set!", "Part of string \"You’re all set! Dismiss setup list\""), "dismissList" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Dismiss setup list", "Part of string \"You’re all set! Dismiss setup list\""), "noSubscribersQuestion" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("No subscribers yet?", "Part of string \"No subscribers yet? Set up a subscription form\""), "setUpForm" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Set up a subscription form", "Part of string \"No subscribers yet? Set up a subscription form\""), "haveSubscribersQuestion" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Have existing subscribers?", "Part of string \"Have existing subscribers? Import\""), "import" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Import", "Part of string \"Have existing subscribers? Import\""), "startEngagingWithYourCustomers" => $this->extensions['MailPoet\Twig\I18n']->translate("Start engaging with your customers"), "setUpWelcomeCampaign" => $this->extensions['MailPoet\Twig\I18n']->translate("Set up a welcome campaign"), "setUpWelcomeCampaignDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Choose from our list of templates or start with a blank editor"), "setUpWelcomeCampaignDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You started a welcome campaign"), "addSubscriptionForm" => $this->extensions['MailPoet\Twig\I18n']->translate("Add a subscription form"), "addSubscriptionFormDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Collect new contacts from signup forms"), "addSubscriptionFormDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You added a subscription form"), "sendFirstNewsletter" => $this->extensions['MailPoet\Twig\I18n']->translate("Send your first newsletter"), "sendFirstNewsletterDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Drive traffic with newsletters that are easy to build and measure"), "sendFirstNewsletterDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You sent or scheduled your first newsletter"), "setUpAbandonedCartEmail" => $this->extensions['MailPoet\Twig\I18n']->translate("Set up an abandoned cart email"), "setUpAbandonedCartEmailDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Create customized email sequences with our new automation editor"), "setUpAbandonedCartEmailDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You set up an abandoned cart campaign"), "brandWooEmails" => $this->extensions['MailPoet\Twig\I18n']->translate("Brand your WooCommerce transactional emails"), "brandWooEmailsDesc" => $this->extensions['MailPoet\Twig\I18n']->translate("Make your store notification emails shine"), "brandWooEmailsDone" => $this->extensions['MailPoet\Twig\I18n']->translate("You branded your transactional emails"), "allDone" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("All done!", "Part of string \"All done! Dismiss tasks\""), "dismissTasks" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Dismiss tasks", "Part of string \"All done! Dismiss tasks\"")]);
        // line 59
        echo "
";
    }

    public function getTemplateName()
    {
        return "homepage.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 59,  87 => 21,  83 => 20,  77 => 17,  73 => 16,  67 => 12,  63 => 10,  58 => 9,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "homepage.html", "/home/circleci/mailpoet/mailpoet/views/homepage.html");
    }
}
