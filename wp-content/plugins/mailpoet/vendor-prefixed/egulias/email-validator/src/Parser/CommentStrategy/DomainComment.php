<?php
namespace MailPoetVendor\Egulias\EmailValidator\Parser\CommentStrategy;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\Result;
use MailPoetVendor\Egulias\EmailValidator\Result\ValidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\ExpectingATEXT;
class DomainComment implements CommentStrategy
{
 public function exitCondition(EmailLexer $lexer, int $openedParenthesis) : bool
 {
 if ($openedParenthesis === 0 && $lexer->isNextToken(EmailLexer::S_DOT)) {
 // || !$internalLexer->moveNext()) {
 return \false;
 }
 return \true;
 }
 public function endOfLoopValidations(EmailLexer $lexer) : Result
 {
 //test for end of string
 if (!$lexer->isNextToken(EmailLexer::S_DOT)) {
 return new InvalidEmail(new ExpectingATEXT('DOT not found near CLOSEPARENTHESIS'), $lexer->token['value']);
 }
 //add warning
 //Address is valid within the message but cannot be used unmodified for the envelope
 return new ValidEmail();
 }
 public function getWarnings() : array
 {
 return [];
 }
}
