<?php
namespace Composer\Pcre;
if (!defined('ABSPATH')) exit;
final class ReplaceResult
{
 public $result;
 public $count;
 public $matched;
 public function __construct($count, $result)
 {
 $this->count = $count;
 $this->matched = (bool) $count;
 $this->result = $result;
 }
}
