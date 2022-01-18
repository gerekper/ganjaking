<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class ConsecutiveAt implements Reason
{
 public function code() : int
 {
 return 128;
 }
 public function description() : string
 {
 return '@ found after another @';
 }
}
