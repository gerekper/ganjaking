<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class NoLocalPart implements Reason
{
 public function code() : int
 {
 return 130;
 }
 public function description() : string
 {
 return "No local part";
 }
}
