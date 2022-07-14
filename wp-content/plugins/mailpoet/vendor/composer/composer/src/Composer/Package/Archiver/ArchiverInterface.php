<?php
namespace Composer\Package\Archiver;
if (!defined('ABSPATH')) exit;
interface ArchiverInterface
{
 public function archive($sources, $target, $format, array $excludes = array(), $ignoreFilters = false);
 public function supports($format, $sourceType);
}
