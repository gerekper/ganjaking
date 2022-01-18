<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class DomainTooLong implements Reason
{
 public function code() : int
 {
 return 244;
 }
 public function description() : string
 {
 return 'Domain is longer than 253 characters';
 }
}
