<?php
namespace Symfony\Component\Console\Event;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class ConsoleTerminateEvent extends ConsoleEvent
{
 private $exitCode;
 public function __construct(Command $command, InputInterface $input, OutputInterface $output, int $exitCode)
 {
 parent::__construct($command, $input, $output);
 $this->setExitCode($exitCode);
 }
 public function setExitCode($exitCode)
 {
 $this->exitCode = (int) $exitCode;
 }
 public function getExitCode()
 {
 return $this->exitCode;
 }
}
