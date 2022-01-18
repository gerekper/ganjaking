<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\AST\SimpleArithmeticExpression;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class LocateFunction extends FunctionNode
{
 public $firstStringPrimary;
 public $secondStringPrimary;
 public $simpleArithmeticExpression = \false;
 public function getSql(SqlWalker $sqlWalker)
 {
 return $sqlWalker->getConnection()->getDatabasePlatform()->getLocateExpression(
 $sqlWalker->walkStringPrimary($this->secondStringPrimary),
 // its the other way around in platform
 $sqlWalker->walkStringPrimary($this->firstStringPrimary),
 $this->simpleArithmeticExpression ? $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression) : \false
 );
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->firstStringPrimary = $parser->StringPrimary();
 $parser->match(Lexer::T_COMMA);
 $this->secondStringPrimary = $parser->StringPrimary();
 $lexer = $parser->getLexer();
 if ($lexer->isNextToken(Lexer::T_COMMA)) {
 $parser->match(Lexer::T_COMMA);
 $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();
 }
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
