<?php
namespace MailPoetVendor\Egulias\EmailValidator\Warning;
if (!defined('ABSPATH')) exit;
class QuotedString extends Warning
{
 const CODE = 11;
 public function __construct($prevToken, $postToken)
 {
 $this->message = "Quoted String found between {$prevToken} and {$postToken}";
 }
}
