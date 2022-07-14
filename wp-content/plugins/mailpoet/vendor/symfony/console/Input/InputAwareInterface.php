<?php
namespace Symfony\Component\Console\Input;
if (!defined('ABSPATH')) exit;
interface InputAwareInterface
{
 public function setInput(InputInterface $input);
}
