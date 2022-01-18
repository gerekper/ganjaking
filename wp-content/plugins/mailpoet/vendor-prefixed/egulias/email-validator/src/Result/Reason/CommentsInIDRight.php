<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class CommentsInIDRight implements Reason
{
 public function code() : int
 {
 return 400;
 }
 public function description() : string
 {
 return 'Comments are not allowed in IDRight for message-id';
 }
}
