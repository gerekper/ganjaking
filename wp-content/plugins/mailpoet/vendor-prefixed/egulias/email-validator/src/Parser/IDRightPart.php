<?php
namespace MailPoetVendor\Egulias\EmailValidator\Parser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\Result;
use MailPoetVendor\Egulias\EmailValidator\Result\ValidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\ExpectingATEXT;
class IDRightPart extends DomainPart
{
 protected function validateTokens(bool $hasComments) : Result
 {
 $invalidDomainTokens = array(EmailLexer::S_DQUOTE => \true, EmailLexer::S_SQUOTE => \true, EmailLexer::S_BACKTICK => \true, EmailLexer::S_SEMICOLON => \true, EmailLexer::S_GREATERTHAN => \true, EmailLexer::S_LOWERTHAN => \true);
 if (isset($invalidDomainTokens[$this->lexer->token['type']])) {
 return new InvalidEmail(new ExpectingATEXT('Invalid token in domain: ' . $this->lexer->token['value']), $this->lexer->token['value']);
 }
 return new ValidEmail();
 }
}
