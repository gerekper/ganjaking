<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class CRNoLF implements Reason
{
 public function code() : int
 {
 return 150;
 }
 public function description() : string
 {
 return 'Missing LF after CR';
 }
}
