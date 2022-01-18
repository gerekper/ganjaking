<?php
namespace MailPoetVendor\Twig\Profiler\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\BlockNode;
use MailPoetVendor\Twig\Node\BodyNode;
use MailPoetVendor\Twig\Node\MacroNode;
use MailPoetVendor\Twig\Node\ModuleNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\NodeVisitor\AbstractNodeVisitor;
use MailPoetVendor\Twig\Profiler\Node\EnterProfileNode;
use MailPoetVendor\Twig\Profiler\Node\LeaveProfileNode;
use MailPoetVendor\Twig\Profiler\Profile;
final class ProfilerNodeVisitor extends AbstractNodeVisitor
{
 private $extensionName;
 private $varName;
 public function __construct(string $extensionName)
 {
 $this->extensionName = $extensionName;
 $this->varName = \sprintf('__internal_%s', \hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $extensionName));
 }
 protected function doEnterNode(Node $node, Environment $env)
 {
 return $node;
 }
 protected function doLeaveNode(Node $node, Environment $env)
 {
 if ($node instanceof ModuleNode) {
 $node->setNode('display_start', new Node([new EnterProfileNode($this->extensionName, Profile::TEMPLATE, $node->getTemplateName(), $this->varName), $node->getNode('display_start')]));
 $node->setNode('display_end', new Node([new LeaveProfileNode($this->varName), $node->getNode('display_end')]));
 } elseif ($node instanceof BlockNode) {
 $node->setNode('body', new BodyNode([new EnterProfileNode($this->extensionName, Profile::BLOCK, $node->getAttribute('name'), $this->varName), $node->getNode('body'), new LeaveProfileNode($this->varName)]));
 } elseif ($node instanceof MacroNode) {
 $node->setNode('body', new BodyNode([new EnterProfileNode($this->extensionName, Profile::MACRO, $node->getAttribute('name'), $this->varName), $node->getNode('body'), new LeaveProfileNode($this->varName)]));
 }
 return $node;
 }
 public function getPriority()
 {
 return 0;
 }
}
\class_alias('MailPoetVendor\\Twig\\Profiler\\NodeVisitor\\ProfilerNodeVisitor', 'MailPoetVendor\\Twig_Profiler_NodeVisitor_Profiler');
