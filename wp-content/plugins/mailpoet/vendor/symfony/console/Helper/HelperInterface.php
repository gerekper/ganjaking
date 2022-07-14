<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
interface HelperInterface
{
 public function setHelperSet(HelperSet $helperSet = null);
 public function getHelperSet();
 public function getName();
}
