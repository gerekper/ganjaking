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

/* automation.html */
class __TwigTemplate_5c71d57f60a0f5a4fc5d48d80259463fe103c80202fce3a75da88803acda1c77 extends Template
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
            'after_css' => [$this, 'block_after_css'],
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
        $this->parent = $this->loadTemplate("layout.html", "automation.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div class=\"wrap\">
  <div id=\"mailpoet_notices\"></div>
  <div id=\"mailpoet_automation\"></div>
</div>
";
    }

    // line 10
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 11
        echo "  <script type=\"text/javascript\">
    var mailpoet_automation_api = ";
        // line 12
        echo json_encode(($context["api"] ?? null));
        echo ";
  </script>
  ";
        // line 14
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("automation.js");
        echo "
";
    }

    // line 17
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 18
        echo $this->extensions['MailPoet\Twig\Assets']->generateStylesheet("mailpoet-automation.css");
        echo "
";
    }

    public function getTemplateName()
    {
        return "automation.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  82 => 18,  78 => 17,  72 => 14,  67 => 12,  64 => 11,  60 => 10,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation.html", "/home/circleci/mailpoet/mailpoet/views/automation.html");
    }
}
