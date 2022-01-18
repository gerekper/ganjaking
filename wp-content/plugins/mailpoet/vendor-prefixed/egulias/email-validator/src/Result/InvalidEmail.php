<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\Reason;
class InvalidEmail implements Result
{
 private $token;
 protected $reason;
 public function __construct(Reason $reason, string $token)
 {
 $this->token = $token;
 $this->reason = $reason;
 }
 public function isValid() : bool
 {
 return \false;
 }
 public function isInvalid() : bool
 {
 return \true;
 }
 public function description() : string
 {
 return $this->reason->description() . " in char " . $this->token;
 }
 public function code() : int
 {
 return $this->reason->code();
 }
 public function reason() : Reason
 {
 return $this->reason;
 }
}
