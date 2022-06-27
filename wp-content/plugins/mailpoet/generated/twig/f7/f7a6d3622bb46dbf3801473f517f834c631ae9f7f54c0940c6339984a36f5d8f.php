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

/* mss_pitch_translations.html */
class __TwigTemplate_817e35bc9901e106cb80d1f854e91e7d5bc9a9be36014be5fa1cd05f1e39249e extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["welcomeWizardMSSFreeTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Reliable email delivery is entirely free for you. Sign up!", "Promotion for our email sending service: Title"), "welcomeWizardMSSFreeSubtitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Did you know? Users with 1,000 subscribers or less get the Starter plan for free.", "Promotion for our email sending service: Paragraph"), "welcomeWizardMSSFreeListTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("You’ll get", "Promotion for our email sending service: Paragraph"), "welcomeWizardMSSList1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Reliable marketing and transactional email delivery. Reach inboxes, not spam boxes", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSList2" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Send your emails super fast (up to 50,000 emails per hour)", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSList4" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Maintain your sender reputation and improve engagement levels with automated bounce and complaint handling. Stop sending to non-deliverable and complaining addresses, automatically", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSList5" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Authenticate your emails (with SPF and DKIM) to improve deliverability and avoid spam boxes", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSFreeButton" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Sign up for free", "Promotion for our email sending service: Button"), "welcomeWizardMSSNotFreeTitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("It’s now time to take your MailPoet to the next level", "Promotion for our email sending service: Title"), "welcomeWizardMSSNotFreeSubtitle" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Starting at only \$10 per month, MailPoet Business offers the following features", "Promotion for our email sending service: Paragraph"), "welcomeWizardMSSNotFreeList1" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Reliable marketing and transactional email delivery. Reach inboxes, not spam boxes", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSNotFreeList2" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Detailed engagement statistics", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSNotFreeList3" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Multi-condition subscriber segmentation", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSNotFreeList4" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Google Analytics integration", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSNotFreeList5" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Priority customer support", "Promotion for our email sending service: Feature item"), "welcomeWizardMSSNotFreeButton" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Upgrade", "Promotion for our email sending service: Button"), "welcomeWizardMSSNoThanks" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("No thanks!", "Promotion for our email sending service: Skip link")]);
        // line 19
        echo "
";
    }

    public function getTemplateName()
    {
        return "mss_pitch_translations.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 19,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "mss_pitch_translations.html", "/home/circleci/mailpoet/mailpoet/views/mss_pitch_translations.html");
    }
}
