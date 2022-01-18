<?php
namespace MailPoetVendor\Egulias\EmailValidator\Validation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Validation\Exception\EmptyValidationList;
use MailPoetVendor\Egulias\EmailValidator\Result\MultipleErrors;
class MultipleValidationWithAnd implements EmailValidation
{
 const STOP_ON_ERROR = 0;
 const ALLOW_ALL_ERRORS = 1;
 private $validations = [];
 private $warnings = [];
 private $error;
 private $mode;
 public function __construct(array $validations, $mode = self::ALLOW_ALL_ERRORS)
 {
 if (\count($validations) == 0) {
 throw new EmptyValidationList();
 }
 $this->validations = $validations;
 $this->mode = $mode;
 }
 public function isValid(string $email, EmailLexer $emailLexer) : bool
 {
 $result = \true;
 foreach ($this->validations as $validation) {
 $emailLexer->reset();
 $validationResult = $validation->isValid($email, $emailLexer);
 $result = $result && $validationResult;
 $this->warnings = \array_merge($this->warnings, $validation->getWarnings());
 if (!$validationResult) {
 $this->processError($validation);
 }
 if ($this->shouldStop($result)) {
 break;
 }
 }
 return $result;
 }
 private function initErrorStorage() : void
 {
 if (null === $this->error) {
 $this->error = new MultipleErrors();
 }
 }
 private function processError(EmailValidation $validation) : void
 {
 if (null !== $validation->getError()) {
 $this->initErrorStorage();
 $this->error->addReason($validation->getError()->reason());
 }
 }
 private function shouldStop(bool $result) : bool
 {
 return !$result && $this->mode === self::STOP_ON_ERROR;
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
