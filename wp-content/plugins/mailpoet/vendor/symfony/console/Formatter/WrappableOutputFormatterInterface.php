<?php
namespace Symfony\Component\Console\Formatter;
if (!defined('ABSPATH')) exit;
interface WrappableOutputFormatterInterface extends OutputFormatterInterface
{
 public function formatAndWrap(string $message, int $width);
}
