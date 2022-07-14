<?php
namespace Composer\Plugin\Capability;
if (!defined('ABSPATH')) exit;
interface CommandProvider extends Capability
{
 public function getCommands();
}
