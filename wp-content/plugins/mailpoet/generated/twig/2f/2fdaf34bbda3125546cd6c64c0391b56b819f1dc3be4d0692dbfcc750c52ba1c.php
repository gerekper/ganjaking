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
        echo "<div id=\"mailpoet_automation_editor\" class=\"edit-site\"></div>
";
    }

    // line 7
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 8
        echo "<script type=\"text/javascript\">
  var mailpoet_automation_api = ";
        // line 9
        echo json_encode(($context["api"] ?? null));
        echo ";
  var mailpoet_automation_workflow = ";
        // line 10
        echo ((($context["workflow"] ?? null)) ? (json_encode(($context["workflow"] ?? null))) : ("undefined"));
        echo ";
</script>
";
        // line 12
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("automation_editor.js");
        echo "
";
    }

    // line 15
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 16
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
        return array (  83 => 16,  79 => 15,  73 => 12,  68 => 10,  64 => 9,  61 => 8,  57 => 7,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation/editor.html", "/home/circleci/mailpoet/mailpoet/views/automation/editor.html");
    }
}
