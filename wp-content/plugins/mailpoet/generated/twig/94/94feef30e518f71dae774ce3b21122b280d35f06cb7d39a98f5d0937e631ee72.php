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
        echo "
<div class=\"wrap\">
  <h1 class=\"mailpoet-h1\">Automation</h1>

  <div class=\"mailpoet_notice notice notice-error\">
    <p>
      <strong>This is a testing page for MailPoet automation.</strong>
    </p>
  </div>
</div>

<div>
  <a href=\"";
        // line 16
        echo admin_url("admin.php?page=mailpoet-automation-editor");
        echo "\">Automation Editor</a>
</div>

<div id=\"mailpoet_automation\"></div>

";
    }

    // line 23
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 24
        echo "  <script type=\"text/javascript\">
    var mailpoet_automation_api = ";
        // line 25
        echo json_encode(($context["api"] ?? null));
        echo ";
  </script>
  ";
        // line 27
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("automation.js");
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
        return array (  87 => 27,  82 => 25,  79 => 24,  75 => 23,  65 => 16,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation.html", "/home/circleci/mailpoet/mailpoet/views/automation.html");
    }
}
