<?php
namespace MailPoetVendor\Egulias\EmailValidator\Validation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\MessageIDParser;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\ExceptionFound;
class MessageIDValidation implements EmailValidation
{
 private $warnings = [];
 private $error;
 public function isValid(string $email, EmailLexer $emailLexer) : bool
 {
 $parser = new MessageIDParser($emailLexer);
 try {
 $result = $parser->parse($email);
 $this->warnings = $parser->getWarnings();
 if ($result->isInvalid()) {
 $this->error = $result;
 return \false;
 }
 } catch (\Exception $invalid) {
 $this->error = new InvalidEmail(new ExceptionFound($invalid), '');
 return \false;
 }
 return \true;
 }
 public function getWarnings() : array
 {
 return $this->warnings;
 }
 public function getError() : ?InvalidEmail
 {
 return $this->error;
 }
}
