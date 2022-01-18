<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class UnclosedComment implements Reason
{
 public function code() : int
 {
 return 146;
 }
 public function description() : string
 {
 return 'No closing comment token found';
 }
}
