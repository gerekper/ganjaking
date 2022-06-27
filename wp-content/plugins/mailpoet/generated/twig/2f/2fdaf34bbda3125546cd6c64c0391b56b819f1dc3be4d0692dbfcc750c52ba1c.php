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

/* automation/editor.html */
class __TwigTemplate_fb71bf89a426c5b5b4c882632d479b3cffbaded39146d864b8aea902ce949403 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'container' => [$this, 'block_container'],
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
        $this->parent = $this->loadTemplate("layout.html", "automation/editor.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_container($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div class=\"block-editor\">
  <div id=\"mailpoet_automation_editor\" class=\"block-editor__container\"></div>
</div>
";
    }

    // line 9
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 10
        echo "<script type=\"text/javascript\">
  var mailpoet_automation_api = ";
        // line 11
        echo json_encode(($context["api"] ?? null));
        echo ";
  var mailpoet_automation_workflow = ";
        // line 12
        echo ((($context["workflow"] ?? null)) ? (json_encode(($context["workflow"] ?? null))) : ("undefined"));
        echo ";
</script>
";
        // line 14
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("automation_editor.js");
        echo "
";
    }

    // line 17
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 18
        echo $this->extensions['MailPoet\Twig\Assets']->generateStylesheet("mailpoet-automation-editor.css");
        echo "
";
    }

    public function getTemplateName()
    {
        return "automation/editor.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 18,  81 => 17,  75 => 14,  70 => 12,  66 => 11,  63 => 10,  59 => 9,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation/editor.html", "/home/circleci/mailpoet/mailpoet/views/automation/editor.html");
    }
}
