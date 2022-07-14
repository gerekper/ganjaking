<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
class TableSeparator extends TableCell
{
 public function __construct(array $options = [])
 {
 parent::__construct('', $options);
 }
}
