<?php
namespace MailPoetVendor\Egulias\EmailValidator\Validation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\RFCWarnings;
class NoRFCWarningsValidation extends RFCValidation
{
 private $error;
 public function isValid(string $email, EmailLexer $emailLexer) : bool
 {
 if (!parent::isValid($email, $emailLexer)) {
 return \false;
 }
 if (empty($this->getWarnings())) {
 return \true;
 }
 $this->error = new InvalidEmail(new RFCWarnings(), '');
 return \false;
 }
 public function getError() : ?InvalidEmail
 {
 return $this->error ?: parent::getError();
 }
}
