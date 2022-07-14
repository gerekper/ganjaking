<?php
namespace Symfony\Component\Console\Output;
if (!defined('ABSPATH')) exit;
class BufferedOutput extends Output
{
 private $buffer = '';
 public function fetch()
 {
 $content = $this->buffer;
 $this->buffer = '';
 return $content;
 }
 protected function doWrite($message, $newline)
 {
 $this->buffer .= $message;
 if ($newline) {
 $this->buffer .= \PHP_EOL;
 }
 }
}
