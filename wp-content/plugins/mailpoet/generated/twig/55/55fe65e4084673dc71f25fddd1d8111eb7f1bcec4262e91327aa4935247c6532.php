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

/* shared_config.html */
class __TwigTemplate_90951eb4ec498ea9cc4cd2823482f33d027bb13ae9c5be69aea09006f8acbf9e extends Template
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
        echo "
<script type=\"text/javascript\">
  ";
        // line 4
        echo "    var mailpoet_premium_plugin_installed = ";
        echo json_encode(($context["premium_plugin_installed"] ?? null));
        echo ";
    var mailpoet_premium_plugin_download_url = ";
        // line 5
        echo json_encode(($context["premium_plugin_download_url"] ?? null));
        echo ";
    var mailpoet_premium_plugin_activation_url = ";
        // line 6
        echo json_encode(($context["premium_plugin_activation_url"] ?? null));
        echo ";
    var mailpoet_plugin_partial_key = ";
        // line 7
        echo json_encode(($context["plugin_partial_key"] ?? null));
        echo ";
    var mailpoet_email_volume_limit = ";
        // line 8
        echo json_encode(($context["email_volume_limit"] ?? null));
        echo ";
    var mailpoet_email_volume_limit_reached = ";
        // line 9
        echo json_encode(($context["email_volume_limit_reached"] ?? null));
        echo ";
  ";
        // line 11
        echo "</script>
";
    }

    public function getTemplateName()
    {
        return "shared_config.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 11,  62 => 9,  58 => 8,  54 => 7,  50 => 6,  46 => 5,  41 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "shared_config.html", "/home/circleci/mailpoet/mailpoet/views/shared_config.html");
    }
}
