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

/* subscribers/stats.html */
class __TwigTemplate_433ace6ed49496dfe58add646f3f8b4967f2f8b598646d755944101d4e4cce97 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<script type=\"text/javascript\">
  var mailpoet_shortcode_links = ";
        // line 2
        echo json_encode(($context["shortcode_links"] ?? null));
        echo ";
</script>

";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["loadingSubscriberStats" => $this->extensions['MailPoet\Twig\I18n']->translate("Loading stats"), "noStatsForSubscriber" => $this->extensions['MailPoet\Twig\I18n']->translate("No stats"), "orderNumberPrefix" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Order", "Prefix for order number e.g. Order #123")]);
        // line 9
        echo "
";
    }

    public function getTemplateName()
    {
        return "subscribers/stats.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 9,  46 => 5,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscribers/stats.html", "/home/circleci/mailpoet/mailpoet-premium/views/subscribers/stats.html");
    }
}
