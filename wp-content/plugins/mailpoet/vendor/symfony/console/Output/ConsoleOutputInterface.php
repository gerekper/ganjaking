<?php
namespace Symfony\Component\Console\Output;
if (!defined('ABSPATH')) exit;
interface ConsoleOutputInterface extends OutputInterface
{
 public function getErrorOutput();
 public function setErrorOutput(OutputInterface $error);
}
