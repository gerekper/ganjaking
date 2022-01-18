<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class UnusualElements implements Reason
{
 private $element = '';
 public function __construct(string $element)
 {
 $this->element = $element;
 }
 public function code() : int
 {
 return 201;
 }
 public function description() : string
 {
 return 'Unusual element found, wourld render invalid in majority of cases. Element found: ' . $this->element;
 }
}
