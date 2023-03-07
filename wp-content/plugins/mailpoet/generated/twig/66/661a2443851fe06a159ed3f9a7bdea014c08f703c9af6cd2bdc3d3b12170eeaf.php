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

/* premium_key_validation_strings.html */
class __TwigTemplate_ef144945da24072956cf965a52a49e213b9cecc6662a1aecc7f22c9163064c8e extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["premiumTabNoKeyNotice" => $this->extensions['MailPoet\Twig\I18n']->translate("Please specify a license key before validating it.", "mailpoet"), "premiumTabKeyValidMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Your key is valid", "mailpoet"), "premiumTabKeyNotValidMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Your key is not valid", "mailpoet"), "premiumTabKeyCannotValidate" => $this->extensions['MailPoet\Twig\I18n']->translate("Yikes, we can’t validate your key because:"), "premiumTabKeyCannotValidateLocalhost" => $this->extensions['MailPoet\Twig\I18n']->translate("You’re on localhost or using an IP address instead of a domain. Not allowed for security reasons!"), "premiumTabKeyCannotValidateBlockingHost" => $this->extensions['MailPoet\Twig\I18n']->translate("Your host is blocking the activation, e.g. Altervista"), "premiumTabKeyCannotValidateIntranet" => $this->extensions['MailPoet\Twig\I18n']->translate("This website is on an Intranet. Activating MailPoet will not be possible."), "learnMore" => $this->extensions['MailPoet\Twig\I18n']->translate("Learn more"), "premiumTabPremiumActiveMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Premium is active", "mailpoet"), "premiumTabPremiumNotInstalledMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Premium is not installed.", "mailpoet"), "premiumTabPremiumNotActivatedMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Premium is installed but not activated.", "mailpoet"), "premiumTabPremiumDownloadMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Download MailPoet Premium plugin", "mailpoet"), "premiumTabPremiumActivateMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Activate MailPoet Premium plugin", "mailpoet"), "premiumTabPremiumKeyNotValidMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Your key is not valid for MailPoet Premium", "mailpoet"), "premiumTabMssActiveMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Sending Service is active", "mailpoet"), "premiumTabMssNotActiveMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet Sending Service is not active.", "mailpoet"), "premiumTabMssActivateMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Activate MailPoet Sending Service", "mailpoet"), "premiumTabMssKeyNotValidMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Your key is not valid for the MailPoet Sending Service", "mailpoet"), "premiumTabPendingApprovalHeading" => $this->extensions['MailPoet\Twig\I18n']->translate("Note: this subscription is currently pending approval by MailPoet.", "mailpoet"), "premiumTabPendingApprovalMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("You should receive an email from us about it within 48h. Sending will be paused in the meantime, but you can still send email previews to yourself and explore the plugin features.", "mailpoet"), "premiumTabCongratulatoryMssEmailSent" => $this->extensions['MailPoet\Twig\I18n']->translate("A test email was sent to [email_address]", "mailpoet"), "premiumTabPendingApprovalMessageRefresh" => $this->extensions['MailPoet\Twig\I18n']->translate("If you have already received approval email, click [link]here[/link] to update the status.", "mailpoet")]);
        // line 24
        echo "
";
    }

    public function getTemplateName()
    {
        return "premium_key_validation_strings.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 24,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "premium_key_validation_strings.html", "/home/circleci/mailpoet/mailpoet/views/premium_key_validation_strings.html");
    }
}
