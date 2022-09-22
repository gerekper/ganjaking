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

/* automation/templates.html */
class __TwigTemplate_53d600d8a07aac1f252dba6865bc3c701e94cb6909b20e23d47b9ab03d29823c extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "automation/templates.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div id=\"mailpoet_automation_templates\"></div>
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
  var mailpoet_automation_templates = ";
        // line 10
        echo json_encode(($context["templates"] ?? null));
        echo ";
</script>
";
        // line 12
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("automation_templates.js");
        echo "
";
    }

    // line 15
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 16
        echo $this->extensions['MailPoet\Twig\Assets']->generateStylesheet("mailpoet-automation-templates.css");
        echo "
";
    }

    public function getTemplateName()
    {
        return "automation/templates.html";
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
        return new Source("", "automation/templates.html", "/home/circleci/mailpoet/mailpoet/views/automation/templates.html");
    }
}
