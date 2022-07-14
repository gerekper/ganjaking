<?php
namespace Composer\DependencyResolver;
if (!defined('ABSPATH')) exit;
class RuleWatchGraph
{
 protected $watchChains = array();
 public function insert(RuleWatchNode $node)
 {
 if ($node->getRule()->isAssertion()) {
 return;
 }
 if (!$node->getRule() instanceof MultiConflictRule) {
 foreach (array($node->watch1, $node->watch2) as $literal) {
 if (!isset($this->watchChains[$literal])) {
 $this->watchChains[$literal] = new RuleWatchChain;
 }
 $this->watchChains[$literal]->unshift($node);
 }
 } else {
 foreach ($node->getRule()->getLiterals() as $literal) {
 if (!isset($this->watchChains[$literal])) {
 $this->watchChains[$literal] = new RuleWatchChain;
 }
 $this->watchChains[$literal]->unshift($node);
 }
 }
 }
 public function propagateLiteral($decidedLiteral, $level, Decisions $decisions)
 {
 // we invert the decided literal here, example:
 // A was decided => (-A|B) now requires B to be true, so we look for
 // rules which are fulfilled by -A, rather than A.
 $literal = -$decidedLiteral;
 if (!isset($this->watchChains[$literal])) {
 return null;
 }
 $chain = $this->watchChains[$literal];
 $chain->rewind();
 while ($chain->valid()) {
 $node = $chain->current();
 if (!$node->getRule() instanceof MultiConflictRule) {
 $otherWatch = $node->getOtherWatch($literal);
 if (!$node->getRule()->isDisabled() && !$decisions->satisfy($otherWatch)) {
 $ruleLiterals = $node->getRule()->getLiterals();
 $alternativeLiterals = array_filter($ruleLiterals, function ($ruleLiteral) use ($literal, $otherWatch, $decisions) {
 return $literal !== $ruleLiteral &&
 $otherWatch !== $ruleLiteral &&
 !$decisions->conflict($ruleLiteral);
 });
 if ($alternativeLiterals) {
 reset($alternativeLiterals);
 $this->moveWatch($literal, current($alternativeLiterals), $node);
 continue;
 }
 if ($decisions->conflict($otherWatch)) {
 return $node->getRule();
 }
 $decisions->decide($otherWatch, $level, $node->getRule());
 }
 } else {
 foreach ($node->getRule()->getLiterals() as $otherLiteral) {
 if ($literal !== $otherLiteral && !$decisions->satisfy($otherLiteral)) {
 if ($decisions->conflict($otherLiteral)) {
 return $node->getRule();
 }
 $decisions->decide($otherLiteral, $level, $node->getRule());
 }
 }
 }
 $chain->next();
 }
 return null;
 }
 protected function moveWatch($fromLiteral, $toLiteral, RuleWatchNode $node)
 {
 if (!isset($this->watchChains[$toLiteral])) {
 $this->watchChains[$toLiteral] = new RuleWatchChain;
 }
 $node->moveWatch($fromLiteral, $toLiteral);
 $this->watchChains[$fromLiteral]->remove();
 $this->watchChains[$toLiteral]->unshift($node);
 }
}
