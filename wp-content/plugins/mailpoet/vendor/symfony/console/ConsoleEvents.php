<?php
namespace Symfony\Component\Console;
if (!defined('ABSPATH')) exit;
final class ConsoleEvents
{
 public const COMMAND = 'console.command';
 public const TERMINATE = 'console.terminate';
 public const ERROR = 'console.error';
}
