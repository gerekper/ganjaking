<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class ExpectingCTEXT implements Reason
{
 public function code() : int
 {
 return 139;
 }
 public function description() : string
 {
 return 'Expecting CTEXT';
 }
}
