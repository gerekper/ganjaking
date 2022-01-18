<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class ConsecutiveDot implements Reason
{
 public function code() : int
 {
 return 132;
 }
 public function description() : string
 {
 return 'Concecutive DOT found';
 }
}
