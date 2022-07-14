<?php
namespace Composer\Package\Archiver;
if (!defined('ABSPATH')) exit;
class ComposerExcludeFilter extends BaseExcludeFilter
{
 public function __construct($sourcePath, array $excludeRules)
 {
 parent::__construct($sourcePath);
 $this->excludePatterns = $this->generatePatterns($excludeRules);
 }
}
