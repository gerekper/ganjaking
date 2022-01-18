<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class CharNotAllowed implements Reason
{
 public function code() : int
 {
 return 1;
 }
 public function description() : string
 {
 return "Character not allowed";
 }
}
