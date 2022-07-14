<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
class TableRows implements \IteratorAggregate
{
 private $generator;
 public function __construct(callable $generator)
 {
 $this->generator = $generator;
 }
 public function getIterator(): \Traversable
 {
 $g = $this->generator;
 return $g();
 }
}
