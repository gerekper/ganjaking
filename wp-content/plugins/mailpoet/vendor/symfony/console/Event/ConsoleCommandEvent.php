<?php
namespace Symfony\Component\Console\Event;
if (!defined('ABSPATH')) exit;
class ConsoleCommandEvent extends ConsoleEvent
{
 public const RETURN_CODE_DISABLED = 113;
 private $commandShouldRun = true;
 public function disableCommand()
 {
 return $this->commandShouldRun = false;
 }
 public function enableCommand()
 {
 return $this->commandShouldRun = true;
 }
 public function commandShouldRun()
 {
 return $this->commandShouldRun;
 }
}
