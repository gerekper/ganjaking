<?php
namespace Symfony\Component\Console\Input;
if (!defined('ABSPATH')) exit;
interface StreamableInputInterface extends InputInterface
{
 public function setStream($stream);
 public function getStream();
}
