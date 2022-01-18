<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class ExpectingDTEXT implements Reason
{
 public function code() : int
 {
 return 129;
 }
 public function description() : string
 {
 return 'Expecting DTEXT';
 }
}
