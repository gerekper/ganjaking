<?php
namespace MailPoetVendor\Egulias\EmailValidator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\Parser;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\Result;
use MailPoetVendor\Egulias\EmailValidator\Parser\IDLeftPart;
use MailPoetVendor\Egulias\EmailValidator\Parser\IDRightPart;
use MailPoetVendor\Egulias\EmailValidator\Result\ValidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Warning\EmailTooLong;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\NoLocalPart;
class MessageIDParser extends Parser
{
 const EMAILID_MAX_LENGTH = 254;
 protected $idLeft = '';
 protected $idRight = '';
 public function parse(string $str) : Result
 {
 $result = parent::parse($str);
 $this->addLongEmailWarning($this->idLeft, $this->idRight);
 return $result;
 }
 protected function preLeftParsing() : Result
 {
 if (!$this->hasAtToken()) {
 return new InvalidEmail(new NoLocalPart(), $this->lexer->token["value"]);
 }
 return new ValidEmail();
 }
 protected function parseLeftFromAt() : Result
 {
 return $this->processIDLeft();
 }
 protected function parseRightFromAt() : Result
 {
 return $this->processIDRight();
 }
 private function processIDLeft() : Result
 {
 $localPartParser = new IDLeftPart($this->lexer);
 $localPartResult = $localPartParser->parse();
 $this->idLeft = $localPartParser->localPart();
 $this->warnings = \array_merge($localPartParser->getWarnings(), $this->warnings);
 return $localPartResult;
 }
 private function processIDRight() : Result
 {
 $domainPartParser = new IDRightPart($this->lexer);
 $domainPartResult = $domainPartParser->parse();
 $this->idRight = $domainPartParser->domainPart();
 $this->warnings = \array_merge($domainPartParser->getWarnings(), $this->warnings);
 return $domainPartResult;
 }
 public function getLeftPart() : string
 {
 return $this->idLeft;
 }
 public function getRightPart() : string
 {
 return $this->idRight;
 }
 private function addLongEmailWarning(string $localPart, string $parsedDomainPart) : void
 {
 if (\strlen($localPart . '@' . $parsedDomainPart) > self::EMAILID_MAX_LENGTH) {
 $this->warnings[EmailTooLong::CODE] = new EmailTooLong();
 }
 }
}
