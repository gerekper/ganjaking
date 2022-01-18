<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\NodeVisitor\NodeVisitorInterface;
use MailPoetVendor\Twig\TokenParser\TokenParserInterface;
use MailPoetVendor\Twig\TwigFilter;
use MailPoetVendor\Twig\TwigFunction;
use MailPoetVendor\Twig\TwigTest;
interface ExtensionInterface
{
 public function getTokenParsers();
 public function getNodeVisitors();
 public function getFilters();
 public function getTests();
 public function getFunctions();
 public function getOperators();
}
\class_alias('MailPoetVendor\\Twig\\Extension\\ExtensionInterface', 'MailPoetVendor\\Twig_ExtensionInterface');
// Ensure that the aliased name is loaded to keep BC for classes implementing the typehint with the old aliased name.
\class_exists('MailPoetVendor\\Twig\\Environment');
