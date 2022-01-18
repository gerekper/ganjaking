<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\Node;
interface NodeVisitorInterface
{
 public function enterNode(Node $node, Environment $env);
 public function leaveNode(Node $node, Environment $env);
 public function getPriority();
}
\class_alias('MailPoetVendor\\Twig\\NodeVisitor\\NodeVisitorInterface', 'MailPoetVendor\\Twig_NodeVisitorInterface');
// Ensure that the aliased name is loaded to keep BC for classes implementing the typehint with the old aliased name.
\class_exists('MailPoetVendor\\Twig\\Environment');
