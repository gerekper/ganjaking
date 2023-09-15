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

/* form/template_selection.html */
class __TwigTemplate_72cb81adca1fcada3d740fa851dd9cdc07463639615e596a1f2d37bd9b4fa4aa extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'after_css' => [$this, 'block_after_css'],
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
        $this->parent = $this->loadTemplate("layout.html", "form/template_selection.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_after_css($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo $this->extensions['MailPoet\Twig\Assets']->generateStylesheet("mailpoet-form-editor.css");
        echo "
";
    }

    // line 7
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 8
        echo "
<div class=\"block-editor\">
  <div id=\"mailpoet_form_edit_templates\">
  </div>
</div>

<script>
  ";
        // line 16
        echo "  var mailpoet_templates = ";
        echo json_encode(($context["templates"] ?? null));
        echo ";
  var mailpoet_form_edit_url =
    \"";
        // line 18
        echo admin_url("admin.php?page=mailpoet-form-editor&template_id=");
        echo "\";
  ";
        // line 20
        echo "</script>

<style id=\"mailpoet-form-editor-form-styles\"></style>
";
    }

    // line 25
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 26
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("form_editor.js");
        echo "
";
    }

    public function getTemplateName()
    {
        return "form/template_selection.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 26,  88 => 25,  81 => 20,  77 => 18,  71 => 16,  62 => 8,  58 => 7,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/template_selection.html", "/home/circleci/mailpoet/mailpoet/views/form/template_selection.html");
    }
}
