<?php
namespace Symfony\Component\Console\CommandLoader;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
interface CommandLoaderInterface
{
 public function get($name);
 public function has($name);
 public function getNames();
}
