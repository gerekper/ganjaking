<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class AtextAfterCFWS implements Reason
{
 public function code() : int
 {
 return 133;
 }
 public function description() : string
 {
 return 'ATEXT found after CFWS';
 }
}
