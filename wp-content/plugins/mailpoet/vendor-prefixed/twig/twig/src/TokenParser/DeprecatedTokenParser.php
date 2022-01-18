<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\DeprecatedNode;
use MailPoetVendor\Twig\Token;
class DeprecatedTokenParser extends AbstractTokenParser
{
 public function parse(Token $token)
 {
 $expr = $this->parser->getExpressionParser()->parseExpression();
 $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
 return new DeprecatedNode($expr, $token->getLine(), $this->getTag());
 }
 public function getTag()
 {
 return 'deprecated';
 }
}
\class_alias('MailPoetVendor\\Twig\\TokenParser\\DeprecatedTokenParser', 'MailPoetVendor\\Twig_TokenParser_Deprecated');
