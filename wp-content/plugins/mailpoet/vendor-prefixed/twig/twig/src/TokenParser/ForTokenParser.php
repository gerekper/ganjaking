<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\Expression\AssignNameExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\GetAttrExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\ForNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
use MailPoetVendor\Twig\TokenStream;
final class ForTokenParser extends AbstractTokenParser
{
 public function parse(Token $token)
 {
 $lineno = $token->getLine();
 $stream = $this->parser->getStream();
 $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
 $stream->expect(
 8,
 'in'
 );
 $seq = $this->parser->getExpressionParser()->parseExpression();
 $ifexpr = null;
 if ($stream->nextIf(
 5,
 'if'
 )) {
 @\trigger_error(\sprintf('Using an "if" condition on "for" tag in "%s" at line %d is deprecated since Twig 2.10.0, use a "filter" filter or an "if" condition inside the "for" body instead (if your condition depends on a variable updated inside the loop).', $stream->getSourceContext()->getName(), $lineno), \E_USER_DEPRECATED);
 $ifexpr = $this->parser->getExpressionParser()->parseExpression();
 }
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideForFork']);
 if ('else' == $stream->next()->getValue()) {
 $stream->expect(
 3
 );
 $else = $this->parser->subparse([$this, 'decideForEnd'], \true);
 } else {
 $else = null;
 }
 $stream->expect(
 3
 );
 if (\count($targets) > 1) {
 $keyTarget = $targets->getNode(0);
 $keyTarget = new AssignNameExpression($keyTarget->getAttribute('name'), $keyTarget->getTemplateLine());
 $valueTarget = $targets->getNode(1);
 $valueTarget = new AssignNameExpression($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());
 } else {
 $keyTarget = new AssignNameExpression('_key', $lineno);
 $valueTarget = $targets->getNode(0);
 $valueTarget = new AssignNameExpression($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());
 }
 if ($ifexpr) {
 $this->checkLoopUsageCondition($stream, $ifexpr);
 $this->checkLoopUsageBody($stream, $body);
 }
 return new ForNode($keyTarget, $valueTarget, $seq, $ifexpr, $body, $else, $lineno, $this->getTag());
 }
 public function decideForFork(Token $token)
 {
 return $token->test(['else', 'endfor']);
 }
 public function decideForEnd(Token $token)
 {
 return $token->test('endfor');
 }
 // the loop variable cannot be used in the condition
 private function checkLoopUsageCondition(TokenStream $stream, Node $node)
 {
 if ($node instanceof GetAttrExpression && $node->getNode('node') instanceof NameExpression && 'loop' == $node->getNode('node')->getAttribute('name')) {
 throw new SyntaxError('The "loop" variable cannot be used in a looping condition.', $node->getTemplateLine(), $stream->getSourceContext());
 }
 foreach ($node as $n) {
 if (!$n) {
 continue;
 }
 $this->checkLoopUsageCondition($stream, $n);
 }
 }
 // check usage of non-defined loop-items
 // it does not catch all problems (for instance when a for is included into another or when the variable is used in an include)
 private function checkLoopUsageBody(TokenStream $stream, Node $node)
 {
 if ($node instanceof GetAttrExpression && $node->getNode('node') instanceof NameExpression && 'loop' == $node->getNode('node')->getAttribute('name')) {
 $attribute = $node->getNode('attribute');
 if ($attribute instanceof ConstantExpression && \in_array($attribute->getAttribute('value'), ['length', 'revindex0', 'revindex', 'last'])) {
 throw new SyntaxError(\sprintf('The "loop.%s" variable is not defined when looping with a condition.', $attribute->getAttribute('value')), $node->getTemplateLine(), $stream->getSourceContext());
 }
 }
 // should check for parent.loop.XXX usage
 if ($node instanceof ForNode) {
 return;
 }
 foreach ($node as $n) {
 if (!$n) {
 continue;
 }
 $this->checkLoopUsageBody($stream, $n);
 }
 }
 public function getTag()
 {
 return 'for';
 }
}
\class_alias('MailPoetVendor\\Twig\\TokenParser\\ForTokenParser', 'MailPoetVendor\\Twig_TokenParser_For');
