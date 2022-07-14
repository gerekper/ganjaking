<?php
namespace Composer\Plugin;
if (!defined('ABSPATH')) exit;
interface Capable
{
 public function getCapabilities();
}
