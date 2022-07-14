<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Exception\InvalidArgumentException;
class TableCell
{
 private $value;
 private $options = [
 'rowspan' => 1,
 'colspan' => 1,
 ];
 public function __construct(string $value = '', array $options = [])
 {
 $this->value = $value;
 // check option names
 if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
 throw new InvalidArgumentException(sprintf('The TableCell does not support the following options: \'%s\'.', implode('\', \'', $diff)));
 }
 $this->options = array_merge($this->options, $options);
 }
 public function __toString()
 {
 return $this->value;
 }
 public function getColspan()
 {
 return (int) $this->options['colspan'];
 }
 public function getRowspan()
 {
 return (int) $this->options['rowspan'];
 }
}
