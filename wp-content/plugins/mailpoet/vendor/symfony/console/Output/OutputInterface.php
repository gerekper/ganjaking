<?php
namespace Symfony\Component\Console\Output;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
interface OutputInterface
{
 public const VERBOSITY_QUIET = 16;
 public const VERBOSITY_NORMAL = 32;
 public const VERBOSITY_VERBOSE = 64;
 public const VERBOSITY_VERY_VERBOSE = 128;
 public const VERBOSITY_DEBUG = 256;
 public const OUTPUT_NORMAL = 1;
 public const OUTPUT_RAW = 2;
 public const OUTPUT_PLAIN = 4;
 public function write($messages, $newline = false, $options = 0);
 public function writeln($messages, $options = 0);
 public function setVerbosity($level);
 public function getVerbosity();
 public function isQuiet();
 public function isVerbose();
 public function isVeryVerbose();
 public function isDebug();
 public function setDecorated($decorated);
 public function isDecorated();
 public function setFormatter(OutputFormatterInterface $formatter);
 public function getFormatter();
}
