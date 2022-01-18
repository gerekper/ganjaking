<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class SpoofEmail implements Reason
{
 public function code() : int
 {
 return 298;
 }
 public function description() : string
 {
 return 'The email contains mixed UTF8 chars that makes it suspicious';
 }
}
