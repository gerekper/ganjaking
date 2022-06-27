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

/* mp2migration.html */
class __TwigTemplate_f0cd9fd2c192721200f512febda6f709838dd145880b4894c9176565f10028b2 extends Template
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
            'translations' => [$this, 'block_translations'],
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
        $this->parent = $this->loadTemplate("layout.html", "mp2migration.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "
<div class=\"wrap mailpoet-about-wrap\">
  <h1>";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Welcome to MailPoet version 3!");
        echo " ";
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["settings"] ?? null), "version", [], "any", false, false, false, 6), "html", null, true);
        echo "</h1>

  <p class=\"about-text\">";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Before we start, we will first migrate your data automatically from your previous MailPoet to the new one.");
        echo "</p>

  <h3>";
        // line 10
        echo $this->extensions['MailPoet\Twig\I18n']->translate("What will be kept in MailPoet 3");
        echo "</h3>
  <ul class=\"default-list\">
    <li><strong>";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Subscribers and lists");
        echo " <img draggable=\"false\" class=\"emoji\" alt=\"✔\" src=\"https://s.w.org/images/core/emoji/2.2.1/svg/2714.svg\"></strong></li>
    <li><strong>";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Forms");
        echo " <img draggable=\"false\" class=\"emoji\" alt=\"✔\" src=\"https://s.w.org/images/core/emoji/2.2.1/svg/2714.svg\"></strong></li>
    <li><strong>";
        // line 14
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Settings");
        echo " <img draggable=\"false\" class=\"emoji\" alt=\"✔\" src=\"https://s.w.org/images/core/emoji/2.2.1/svg/2714.svg\"></strong></li>
    <li>";
        // line 15
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Archive of sent newsletters (will not be migrated)");
        echo "</li>
    <li>";
        // line 16
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Statistics (will not be migrated)");
        echo "</li>
  </ul>
  <div style=\"position: absolute; top: .2em; right: 0;\"></div>
  <hr />

  <div>
    <br />
    <div id=\"import-actions\">
      <input type=\"submit\" name=\"import\" id=\"import\" class=\"button button-primary\" value=\"";
        // line 24
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Start upgrade");
        echo "\"  />
      <input type=\"submit\" name=\"stop-import\" id=\"stop-import\" class=\"button button-secondary\" value=\"";
        // line 25
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Pause");
        echo "\" style=\"display: none\"/>
      <small> <a id=\"skip-import\" href=\"#\">";
        // line 26
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No thanks, I'll skip and start from scratch.");
        echo "</a></small>
      <br />
      <br />
    </div>

    <div id=\"progressbar\" class=\"mailpoet_progress mailpoet_progress_complete\" style=\"display: none\">
      <span id=\"progresslabel\" class=\"mailpoet_progress_label\">0%</span>
    </div>

    <div id=\"upgrade-completed\" style=\"display: none\">
      <h3>";
        // line 36
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Upgrade completed!");
        echo "</h3>
      <input type=\"button\" name=\"goto-welcome\" id=\"goto-welcome\" class=\"button button-primary\" value=\"";
        // line 37
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Go to MailPoet");
        echo "\"  />
    </div>

    <br />
    <div id=\"logger-container\" style=\"display: none\">
      <small><strong>";
        // line 42
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Log...");
        echo "</strong></small>
      <div id=\"logger\"></div>
    </div>

  </div>

</div>
";
    }

    // line 51
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 52
        echo "  ";
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("mp2migrator.js");
        // line 54
        echo "
  <script type=\"text/javascript\">
    var mailpoet_mp2_migrator = {
      log_file_url: '";
        // line 57
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["log_file_url"] ?? null), "html", null, true);
        echo "',
      progress_url: '";
        // line 58
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["progress_url"] ?? null), "html", null, true);
        echo "'
    };
  </script>
";
    }

    // line 63
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 64
        echo "  ";
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["import_complete" => $this->extensions['MailPoet\Twig\I18n']->translate("IMPORT COMPLETE"), "importing" => $this->extensions['MailPoet\Twig\I18n']->translate("Importing…"), "import_stopped_by_user" => $this->extensions['MailPoet\Twig\I18n']->translate("IMPORT STOPPED BY USER")]);
        // line 68
        echo "
";
    }

    public function getTemplateName()
    {
        return "mp2migration.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  176 => 68,  173 => 64,  169 => 63,  161 => 58,  157 => 57,  152 => 54,  149 => 52,  145 => 51,  133 => 42,  125 => 37,  121 => 36,  108 => 26,  104 => 25,  100 => 24,  89 => 16,  85 => 15,  81 => 14,  77 => 13,  73 => 12,  68 => 10,  63 => 8,  56 => 6,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "mp2migration.html", "/home/circleci/mailpoet/mailpoet/views/mp2migration.html");
    }
}
