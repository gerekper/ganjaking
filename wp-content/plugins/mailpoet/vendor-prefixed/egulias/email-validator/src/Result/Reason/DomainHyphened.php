<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class DomainHyphened extends DetailedReason
{
 public function code() : int
 {
 return 144;
 }
 public function description() : string
 {
 return 'S_HYPHEN found in domain';
 }
}
