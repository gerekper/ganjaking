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

/* form/front_end_form.html */
class __TwigTemplate_53b89d2127c3e3553aef87a0684de61c60938709506258cd8a7cd55ed9684156 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $this->displayBlock('content', $context, $blocks);
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        echo "  ";
        if (($context["before_widget"] ?? null)) {
            // line 3
            echo "    ";
            echo ($context["before_widget"] ?? null);
            echo "
  ";
        }
        // line 5
        echo "
  ";
        // line 6
        if (($context["title"] ?? null)) {
            // line 7
            echo "    ";
            echo ($context["before_title"] ?? null);
            echo ($context["title"] ?? null);
            echo ($context["after_title"] ?? null);
            echo "
  ";
        }
        // line 9
        echo "
  <div class=\"
    mailpoet_form_popup_overlay
    ";
        // line 12
        if ((($context["animation"] ?? null) != "")) {
            // line 13
            echo "      mailpoet_form_overlay_animation_";
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["animation"] ?? null), "html", null, true);
            echo "
      mailpoet_form_overlay_animation
    ";
        }
        // line 16
        echo "  \"></div>
  <div
    id=\"";
        // line 18
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_html_id"] ?? null), "html", null, true);
        echo "\"
    class=\"
      mailpoet_form
      mailpoet_form_";
        // line 21
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_type"] ?? null), "html", null, true);
        echo "
      mailpoet_form_position_";
        // line 22
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["position"] ?? null), "html", null, true);
        echo "
      mailpoet_form_animation_";
        // line 23
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["animation"] ?? null), "html", null, true);
        echo "
    \"
    ";
        // line 25
        if (($context["is_preview"] ?? null)) {
            // line 26
            echo "      data-is-preview=\"1\"
      data-editor-url=\"";
            // line 27
            echo \MailPoetVendor\twig_escape_filter($this->env, ($context["editor_url"] ?? null), "html", null, true);
            echo "\"
    ";
        }
        // line 29
        echo "  >
    ";
        // line 30
        if ((((($context["form_type"] ?? null) == "popup") || (($context["form_type"] ?? null) == "fixed_bar")) || (($context["form_type"] ?? null) == "slide_in"))) {
            // line 31
            echo "      <img
        class=\"mailpoet_form_close_icon\"
        alt=\"close\"
        width=20
        height=20
        src='";
            // line 36
            echo $this->extensions['MailPoet\Twig\Assets']->generateImageUrl((("form_close_icon/" . ($context["close_button_icon"] ?? null)) . ".svg"));
            echo "'
      >
    ";
        }
        // line 39
        echo "
    <style type=\"text/css\">
     ";
        // line 41
        echo ($context["styles"] ?? null);
        echo "
    </style>

    <form
      target=\"_self\"
      method=\"post\"
      action=\"";
        // line 47
        echo admin_url("admin-post.php?action=mailpoet_subscription_form");
        echo "\"
      class=\"mailpoet_form mailpoet_form_form mailpoet_form_";
        // line 48
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_type"] ?? null), "html", null, true);
        echo "\"
      novalidate
      data-delay=\"";
        // line 50
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["delay"] ?? null), "html", null, true);
        echo "\"
      data-exit-intent-enabled=\"";
        // line 51
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["enableExitIntent"] ?? null), "html", null, true);
        echo "\"
      data-font-family=\"";
        // line 52
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["fontFamily"] ?? null), "html", null, true);
        echo "\"
      data-cookie-expiration-time=\"";
        // line 53
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["cookieFormExpirationTime"] ?? null), "html", null, true);
        echo "\"
    >
      <input type=\"hidden\" name=\"data[form_id]\" value=\"";
        // line 55
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_id"] ?? null), "html", null, true);
        echo "\" />
      <input type=\"hidden\" name=\"token\" value=\"";
        // line 56
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["token"] ?? null), "html", null, true);
        echo "\" />
      <input type=\"hidden\" name=\"api_version\" value=\"";
        // line 57
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["api_version"] ?? null), "html", null, true);
        echo "\" />
      <input type=\"hidden\" name=\"endpoint\" value=\"subscribers\" />
      <input type=\"hidden\" name=\"mailpoet_method\" value=\"subscribe\" />

      ";
        // line 61
        echo ($context["html"] ?? null);
        echo "
      <div class=\"mailpoet_message\">
        <p class=\"mailpoet_validate_success\"
        ";
        // line 64
        if ( !($context["success"] ?? null)) {
            // line 65
            echo "        style=\"display:none;\"
        ";
        }
        // line 67
        echo "        >";
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["form_success_message"] ?? null), "html", null, true);
        echo "
        </p>
        <p class=\"mailpoet_validate_error\"
        ";
        // line 70
        if ( !($context["error"] ?? null)) {
            // line 71
            echo "        style=\"display:none;\"
        ";
        }
        // line 73
        echo "        >";
        if (($context["error"] ?? null)) {
            // line 74
            echo "        ";
            echo $this->extensions['MailPoet\Twig\I18n']->translate("An error occurred, make sure you have filled all the required fields.");
            echo "
        ";
        }
        // line 76
        echo "        </p>
      </div>
    </form>
  </div>

  ";
        // line 81
        if (($context["after_widget"] ?? null)) {
            // line 82
            echo "    ";
            echo ($context["after_widget"] ?? null);
            echo "
  ";
        }
    }

    public function getTemplateName()
    {
        return "form/front_end_form.html";
    }

    public function getDebugInfo()
    {
        return array (  229 => 82,  227 => 81,  220 => 76,  214 => 74,  211 => 73,  207 => 71,  205 => 70,  198 => 67,  194 => 65,  192 => 64,  186 => 61,  179 => 57,  175 => 56,  171 => 55,  166 => 53,  162 => 52,  158 => 51,  154 => 50,  149 => 48,  145 => 47,  136 => 41,  132 => 39,  126 => 36,  119 => 31,  117 => 30,  114 => 29,  109 => 27,  106 => 26,  104 => 25,  99 => 23,  95 => 22,  91 => 21,  85 => 18,  81 => 16,  74 => 13,  72 => 12,  67 => 9,  59 => 7,  57 => 6,  54 => 5,  48 => 3,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/front_end_form.html", "/home/circleci/mailpoet/mailpoet/views/form/front_end_form.html");
    }
}
