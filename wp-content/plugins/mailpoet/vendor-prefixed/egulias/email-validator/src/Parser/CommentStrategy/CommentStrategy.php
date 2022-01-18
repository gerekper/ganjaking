<?php
namespace MailPoetVendor\Egulias\EmailValidator\Parser\CommentStrategy;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\Result;
interface CommentStrategy
{
 public function exitCondition(EmailLexer $lexer, int $openedParenthesis) : bool;
 public function endOfLoopValidations(EmailLexer $lexer) : Result;
 public function getWarnings() : array;
}
