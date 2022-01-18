<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class ExpectingATEXT extends DetailedReason
{
 public function code() : int
 {
 return 137;
 }
 public function description() : string
 {
 return "Expecting ATEXT (Printable US-ASCII). Extended: " . $this->detailedDescription;
 }
}
