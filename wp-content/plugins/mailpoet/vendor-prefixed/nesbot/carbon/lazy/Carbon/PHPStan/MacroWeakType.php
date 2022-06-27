<?php
declare (strict_types=1);
namespace MailPoetVendor\Carbon\PHPStan;
if (!defined('ABSPATH')) exit;
if (!\class_exists(LazyMacro::class, \false)) {
 abstract class LazyMacro extends AbstractMacro
 {
 public function getFileName()
 {
 return $this->reflectionFunction->getFileName();
 }
 public function getStartLine()
 {
 return $this->reflectionFunction->getStartLine();
 }
 public function getEndLine()
 {
 return $this->reflectionFunction->getEndLine();
 }
 }
}
