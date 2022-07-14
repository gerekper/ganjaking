<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
abstract class InputAwareHelper extends Helper implements InputAwareInterface
{
 protected $input;
 public function setInput(InputInterface $input)
 {
 $this->input = $input;
 }
}
