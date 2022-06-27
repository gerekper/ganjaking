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

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["allConditions" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("<strong>All</strong> of the following conditions", "Dynamic segment creation: The user can select join between condition when he has more than one"), "anyConditions" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("<strong>Any</strong> of the following conditions", "Dynamic segment creation: The user can select join between condition when he has more than one"), "filterConnectAnd" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("and", "Dynamic segment creation: The user can see logical operator between condition when has more than one"), "filterConnectOr" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("or", "Dynamic segment creation: The user can see logical operator between condition when has more than one")]);
        // line 6
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
        return array (  39 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "segments/dynamic.html", "/home/circleci/mailpoet/mailpoet-premium/views/segments/dynamic.html");
    }
}
