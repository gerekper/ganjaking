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

/* segments/dynamic.html */
class __TwigTemplate_756448057f537f5715d1a262e5a4bc2ae04cea7f694f005ae4bcfe6c68608edd extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'after_translations' => [$this, 'block_after_translations'],
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
        $this->parent = $this->loadTemplate("layout.html", "segments/dynamic.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "  <div id=\"dynamic_segments_container\"></div>

  <script type=\"text/javascript\">
    var mailpoet_listing_per_page = ";
        // line 7
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["items_per_page"] ?? null), "html", null, true);
        echo ";
    var mailpoet_custom_fields = ";
        // line 8
        echo json_encode(($context["custom_fields"] ?? null));
        echo ";
    var mailpoet_static_segments_list = ";
        // line 9
        echo json_encode(($context["static_segments_list"] ?? null));
        echo ";
    var wordpress_editable_roles_list = ";
        // line 10
        echo json_encode(($context["wordpress_editable_roles_list"] ?? null));
        echo ";
    var mailpoet_newsletters_list = ";
        // line 11
        echo json_encode(($context["newsletters_list"] ?? null));
        echo ";
    var mailpoet_product_categories = ";
        // line 12
        echo json_encode(($context["product_categories"] ?? null));
        echo ";
    var mailpoet_products = ";
        // line 13
        echo json_encode(($context["products"] ?? null));
        echo ";
    var mailpoet_membership_plans = ";
        // line 14
        echo json_encode(($context["membership_plans"] ?? null));
        echo ";
    var mailpoet_subscription_products = ";
        // line 15
        echo json_encode(($context["subscription_products"] ?? null));
        echo ";
    var mailpoet_can_use_woocommerce_memberships = ";
        // line 16
        echo json_encode(($context["can_use_woocommerce_memberships"] ?? null));
        echo ";
    var mailpoet_can_use_woocommerce_subscriptions = ";
        // line 17
        echo json_encode(($context["can_use_woocommerce_subscriptions"] ?? null));
        echo ";
    var mailpoet_woocommerce_currency_symbol = ";
        // line 18
        echo json_encode(($context["woocommerce_currency_symbol"] ?? null));
        echo ";
    var mailpoet_woocommerce_countries = ";
        // line 19
        echo json_encode(($context["woocommerce_countries"] ?? null));
        echo ";
    var mailpoet_woocommerce_payment_methods = ";
        // line 20
        echo json_encode(($context["woocommerce_payment_methods"] ?? null));
        echo ";
    var mailpoet_woocommerce_shipping_methods = ";
        // line 21
        echo json_encode(($context["woocommerce_shipping_methods"] ?? null));
        echo ";
    var mailpoet_signup_forms = ";
        // line 22
        echo json_encode(($context["signup_forms"] ?? null));
        echo ";
    var mailpoet_automations = ";
        // line 23
        echo json_encode(($context["automations"] ?? null));
        echo ";
  </script>

  ";
        // line 26
        $this->loadTemplate("segments/translations.html", "segments/dynamic.html", 26)->display($context);
    }

    // line 30
    public function block_after_translations($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 31
        echo "  ";
        echo do_action("mailpoet_segments_translations_after");
        echo "
";
    }

    public function getTemplateName()
    {
        return "segments/dynamic.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  134 => 31,  130 => 30,  126 => 26,  120 => 23,  116 => 22,  112 => 21,  108 => 20,  104 => 19,  100 => 18,  96 => 17,  92 => 16,  88 => 15,  84 => 14,  80 => 13,  76 => 12,  72 => 11,  68 => 10,  64 => 9,  60 => 8,  56 => 7,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "segments/dynamic.html", "/home/circleci/mailpoet/mailpoet/views/segments/dynamic.html");
    }
}
