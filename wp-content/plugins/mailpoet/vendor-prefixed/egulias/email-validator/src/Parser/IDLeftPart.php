<?php
namespace MailPoetVendor\Egulias\EmailValidator\Parser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\Result\Result;
use MailPoetVendor\Egulias\EmailValidator\Parser\LocalPart;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\CommentsInIDRight;
class IDLeftPart extends LocalPart
{
 protected function parseComments() : Result
 {
 return new InvalidEmail(new CommentsInIDRight(), $this->lexer->token['value']);
 }
}
