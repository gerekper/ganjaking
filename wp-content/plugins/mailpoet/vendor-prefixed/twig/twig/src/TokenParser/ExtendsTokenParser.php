<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class ExtendsTokenParser extends AbstractTokenParser
{
 public function parse(Token $token)
 {
 $stream = $this->parser->getStream();
 if ($this->parser->peekBlockStack()) {
 throw new SyntaxError('Cannot use "extend" in a block.', $token->getLine(), $stream->getSourceContext());
 } elseif (!$this->parser->isMainScope()) {
 throw new SyntaxError('Cannot use "extend" in a macro.', $token->getLine(), $stream->getSourceContext());
 }
 if (null !== $this->parser->getParent()) {
 throw new SyntaxError('Multiple extends tags are forbidden.', $token->getLine(), $stream->getSourceContext());
 }
 $this->parser->setParent($this->parser->getExpressionParser()->parseExpression());
 $stream->expect(Token::BLOCK_END_TYPE);
 return new Node();
 }
 public function getTag()
 {
 return 'extends';
 }
}
\class_alias('MailPoetVendor\\Twig\\TokenParser\\ExtendsTokenParser', 'MailPoetVendor\\Twig_TokenParser_Extends');
