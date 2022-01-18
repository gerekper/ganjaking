<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class UnclosedQuotedString implements Reason
{
 public function code() : int
 {
 return 145;
 }
 public function description() : string
 {
 return "Unclosed quoted string";
 }
}
