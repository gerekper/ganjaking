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

/* newsletter/templates/components/save.hbs */
class __TwigTemplate_fabd1248ff21b91584686f92b4c5275cf43b599b650065f182e5564d092d10e5 extends Template
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
        echo "<div class=\"mailpoet_save_wrapper {{ wrapperClass }}\">
{{#if isWoocommerceTransactional}}
    <div class=\"mailpoet_save_button_group\">
        <input type=\"button\" name=\"save\" value=\"";
        // line 4
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        echo "\" class=\"button button-primary mailpoet_save_button\" />
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_editor_messages\">
        <div class=\"mailpoet_save_error\"></div>
        <div class=\"mailpoet_editor_last_saved\">
            &nbsp;
            <span class=\"mailpoet_autosaved_message mailpoet_hidden\">";
        // line 11
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Autosaved");
        echo "</span>
            <span class=\"mailpoet_autosaved_at mailpoet_hidden\"></span>
        </div>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_save_woocommerce_customizer_disabled{{#if woocommerceCustomizerEnabled}} mailpoet_hidden{{/if}}\">
      <div class=\"mailpoet_save_woocommerce_error\">";
        // line 17
        echo $this->extensions['MailPoet\Twig\I18n']->translate("The usage of this email template for your WooCommerce emails is not yet activated.");
        echo "</div>
      <input type=\"button\" name=\"activate_wc_customizer\" value=\"";
        // line 18
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Activate now");
        echo "\" class=\"button button-primary mailpoet_save_activate_wc_customizer_button\" style=\"margin-top: 17px\">
    </div>
{{else if isAutomationEmail}}
    <input type=\"button\" name=\"preview\" class=\"button mailpoet_show_preview mailpoet_hidden\" value=\"";
        // line 21
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preview");
        echo "\" />
    <input type=\"button\" name=\"save\" value=\"";
        // line 22
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        echo "\" class=\"button button-primary mailpoet_save_go_to_automation mailpoet_hidden\" />
{{else if isConfirmationEmailTemplate}}
  <div class=\"mailpoet_editor_confirmation_email_section\">
    <div class=\"mailpoet_save_button_group\">
      <input type=\"button\" name=\"save\" value=\"";
        // line 26
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        echo "\" class=\"button button-primary mailpoet_save_button\" />
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_editor_messages_confirmation_email\">
      <div class=\"mailpoet_save_error\"></div>
      <div class=\"mailpoet_editor_last_saved\">
        &nbsp;
        <span class=\"mailpoet_autosaved_message mailpoet_hidden\">";
        // line 33
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Autosaved");
        echo "</span>
        <span class=\"mailpoet_autosaved_at\"></span>
      </div>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_save_confirmation_email_disabled{{#if confirmationEmailCustomizerEnabled}} mailpoet_hidden{{/if}}\">
      <div class=\"mailpoet_save_woocommerce_error\">";
        // line 39
        echo $this->extensions['MailPoet\Twig\I18n']->translate("The usage of this email template for your Confirmation emails is not yet activated.");
        echo "</div>
      <input type=\"button\" name=\"activate_confirmation_email_customizer\" value=\"";
        // line 40
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Activate now");
        echo "\" class=\"button button-primary mailpoet_save_activate_confirmation_email_customizer_button\" style=\"margin-top: 17px\">
    </div>
  </div>
{{else}}
    <input type=\"button\" name=\"preview\" class=\"button mailpoet_show_preview\" value=\"";
        // line 44
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preview");
        echo "\" />
    <input type=\"button\" name=\"next\" value=\"";
        // line 45
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Next");
        echo "\" class=\"button button-primary mailpoet_save_next\" />
    <div class=\"mailpoet_button_group mailpoet_save_button_group\">
        <input type=\"button\" name=\"save\" value=\"";
        // line 47
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save");
        echo "\" class=\"button button-primary mailpoet_save_button\" /><button class=\"button button-primary mailpoet_save_show_options\" data-automation-id=\"newsletter_save_options_toggle\" ><span class=\"dashicons mailpoet_save_show_options_icon\"></span></button>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_editor_messages\">
        <div class=\"mailpoet_save_error\"></div>
        <div class=\"mailpoet_editor_last_saved\">
            &nbsp;
            <span class=\"mailpoet_autosaved_message mailpoet_hidden\">";
        // line 54
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Autosaved");
        echo "</span>
            <span class=\"mailpoet_autosaved_at mailpoet_hidden\"></span>
        </div>
    </div>
    <ul class=\"mailpoet_save_options mailpoet_hidden\">
        <li class=\"mailpoet_save_option\"><a href=\"javascript:;\" class=\"mailpoet_save_template\" data-automation-id=\"newsletter_save_as_template_option\">";
        // line 59
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save as new template");
        echo "</a></li>
        <li class=\"mailpoet_save_option\"><a href=\"javascript:;\" class=\"mailpoet_save_export\">";
        // line 60
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Export as template");
        echo "</a></li>
    </ul>
    <div class=\"clearfix\"></div>
    <div class=\"mailpoet_save_as_template_container mailpoet_hidden\">
        <p><b class=\"mailpoet_save_as_template_title\">";
        // line 64
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save as new template");
        echo "</b></p>
        <p><input type=\"text\" name=\"template_name\" value=\"\" placeholder=\"";
        // line 65
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Insert template name");
        echo "\" class=\"mailpoet_input mailpoet_save_as_template_name\" /></p>
        <p><input type=\"button\" name=\"save_as_template\" value=\"";
        // line 66
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Save as new template");
        echo "\" class=\"button button-primary mailpoet_button_full mailpoet_save_as_template\" data-automation-id=\"newsletter_save_as_template_button\" /></p>
    </div>
    <div class=\"mailpoet_export_template_container mailpoet_hidden\">
        <p><b class=\"mailpoet_export_template_title\">";
        // line 69
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Export template");
        echo "</b></p>
        <p><input type=\"text\" name=\"export_template_name\" value=\"\" placeholder=\"";
        // line 70
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Template name");
        echo "\" class=\"mailpoet_input mailpoet_export_template_name\" /></p>
        <p><input type=\"button\" name=\"export_template\" value=\"";
        // line 71
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Export template");
        echo "\" class=\"button button-primary mailpoet_button_full mailpoet_export_template\" /></p>
    </div>
{{/if}}
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/components/save.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  172 => 71,  168 => 70,  164 => 69,  158 => 66,  154 => 65,  150 => 64,  143 => 60,  139 => 59,  131 => 54,  121 => 47,  116 => 45,  112 => 44,  105 => 40,  101 => 39,  92 => 33,  82 => 26,  75 => 22,  71 => 21,  65 => 18,  61 => 17,  52 => 11,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/save.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/save.hbs");
    }
}
