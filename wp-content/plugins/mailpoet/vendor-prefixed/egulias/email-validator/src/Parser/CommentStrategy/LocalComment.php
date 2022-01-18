<?php
namespace MailPoetVendor\Egulias\EmailValidator\Parser\CommentStrategy;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\Result;
use MailPoetVendor\Egulias\EmailValidator\Result\ValidEmail;
use MailPoetVendor\Egulias\EmailValidator\Warning\CFWSNearAt;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\ExpectingATEXT;
class LocalComment implements CommentStrategy
{
 private $warnings = [];
 public function exitCondition(EmailLexer $lexer, int $openedParenthesis) : bool
 {
 return !$lexer->isNextToken(EmailLexer::S_AT);
 }
 public function endOfLoopValidations(EmailLexer $lexer) : Result
 {
 if (!$lexer->isNextToken(EmailLexer::S_AT)) {
 return new InvalidEmail(new ExpectingATEXT('ATEX is not expected after closing comments'), $lexer->token['value']);
 }
 $this->warnings[CFWSNearAt::CODE] = new CFWSNearAt();
 return new ValidEmail();
 }
 public function getWarnings() : array
 {
 return $this->warnings;
 }
}
