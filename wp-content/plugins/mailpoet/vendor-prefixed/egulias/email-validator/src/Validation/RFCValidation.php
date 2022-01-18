<?php
namespace MailPoetVendor\Egulias\EmailValidator\Validation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\EmailParser;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\ExceptionFound;
class RFCValidation implements EmailValidation
{
 private $parser;
 private $warnings = [];
 private $error;
 public function isValid(string $email, EmailLexer $emailLexer) : bool
 {
 $this->parser = new EmailParser($emailLexer);
 try {
 $result = $this->parser->parse($email);
 $this->warnings = $this->parser->getWarnings();
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
 public function getError() : ?InvalidEmail
 {
 return $this->error;
 }
 public function getWarnings() : array
 {
 return $this->warnings;
 }
}
