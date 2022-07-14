<?php
namespace Composer\DependencyResolver;
if (!defined('ABSPATH')) exit;
class RuleWatchChain extends \SplDoublyLinkedList
{
 public function seek($offset)
 {
 $this->rewind();
 for ($i = 0; $i < $offset; $i++, $this->next());
 }
 public function remove()
 {
 $offset = $this->key();
 $this->offsetUnset($offset);
 $this->seek($offset);
 }
}
