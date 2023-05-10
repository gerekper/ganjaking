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

/* settings.html */
class __TwigTemplate_d7e5c6eabd771def3ba904afb1433c0226390ff983f51ef08486a758acfd35d3 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'translations' => [$this, 'block_translations'],
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
        $this->parent = $this->loadTemplate("layout.html", "settings.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "  <div id=\"settings_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 8
        echo "      var mailpoet_authorized_emails = ";
        echo json_encode(($context["authorized_emails"] ?? null));
        echo ";
      var mailpoet_verified_sender_domains = ";
        // line 9
        echo json_encode(($context["verified_sender_domains"] ?? null));
        echo ";
      var mailpoet_all_sender_domains = ";
        // line 10
        echo json_encode(($context["all_sender_domains"] ?? null));
        echo ";
      var mailpoet_members_plugin_active = ";
        // line 11
        echo json_encode((($context["is_members_plugin_active"] ?? null) == true));
        echo ";
      var mailpoet_settings = ";
        // line 12
        echo json_encode(($context["settings"] ?? null));
        echo ";
      var mailpoet_segments = ";
        // line 13
        echo json_encode(($context["segments"] ?? null));
        echo ";
      var mailpoet_pages = ";
        // line 14
        echo json_encode(($context["pages"] ?? null));
        echo ";
      var mailpoet_mss_key_valid = ";
        // line 15
        echo json_encode(($context["mss_key_valid"] ?? null));
        echo ";
      var mailpoet_premium_key_valid = ";
        // line 16
        echo json_encode(($context["premium_key_valid"] ?? null));
        echo ";
      var mailpoet_paths = ";
        // line 17
        echo json_encode(($context["paths"] ?? null));
        echo ";
      var mailpoet_built_in_captcha_supported = ";
        // line 18
        echo json_encode((($context["built_in_captcha_supported"] ?? null) == true));
        echo ";
      var mailpoet_free_plan_url = \"";
        // line 19
        echo $this->extensions['MailPoet\Twig\Functions']->addReferralId("https://www.mailpoet.com/free-plan");
        echo "\";
      var mailpoet_current_user_email = \"";
        // line 20
        echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_get_attribute($this->env, $this->source, ($context["current_user"] ?? null), "user_email", [], "any", false, false, false, 20), "js", null, true);
        echo "\";
      var mailpoet_hosts = ";
        // line 21
        echo json_encode(($context["hosts"] ?? null));
        echo ";
      var mailpoet_current_site_title = ";
        // line 22
        echo json_encode(($context["current_site_title"] ?? null));
        echo ";
    ";
        // line 24
        echo "    var mailpoet_beacon_articles = [
      '57f71d49c697911f2d323486',
      '57fb0e1d9033600277a681ca',
      '57f49a929033602e61d4b9f4',
      '57fb134cc697911f2d323e3b',
    ];
  </script>
";
    }

    // line 32
    public function block_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 33
        echo "  ";
        $this->loadTemplate("settings_translations.html", "settings.html", 33)->display($context);
        // line 34
        echo "  ";
        $this->loadTemplate("premium_key_validation_strings.html", "settings.html", 34)->display($context);
    }

    // line 37
    public function block_after_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 38
        echo $this->extensions['MailPoet\Twig\Assets']->generateJavascript("settings.js");
        echo "
";
    }

    public function getTemplateName()
    {
        return "settings.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  145 => 38,  141 => 37,  136 => 34,  133 => 33,  129 => 32,  118 => 24,  114 => 22,  110 => 21,  106 => 20,  102 => 19,  98 => 18,  94 => 17,  90 => 16,  86 => 15,  82 => 14,  78 => 13,  74 => 12,  70 => 11,  66 => 10,  62 => 9,  57 => 8,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "settings.html", "/home/circleci/mailpoet/mailpoet/views/settings.html");
    }
}
