<?php
namespace Composer\Pcre;
if (!defined('ABSPATH')) exit;
final class MatchAllResult
{
 public $matches;
 public $count;
 public $matched;
 public function __construct($count, array $matches)
 {
 $this->matches = $matches;
 $this->matched = (bool) $count;
 $this->count = $count;
 }
}
