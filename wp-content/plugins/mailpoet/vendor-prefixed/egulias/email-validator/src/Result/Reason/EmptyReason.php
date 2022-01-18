<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class EmptyReason implements Reason
{
 public function code() : int
 {
 return 0;
 }
 public function description() : string
 {
 return 'Empty reason';
 }
}
