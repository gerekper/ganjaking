<?php
namespace Composer\Package\Archiver;
if (!defined('ABSPATH')) exit;
use Composer\Pcre\Preg;
class GitExcludeFilter extends BaseExcludeFilter
{
 public function __construct($sourcePath)
 {
 parent::__construct($sourcePath);
 if (file_exists($sourcePath.'/.gitattributes')) {
 $this->excludePatterns = array_merge(
 $this->excludePatterns,
 $this->parseLines(
 file($sourcePath.'/.gitattributes'),
 array($this, 'parseGitAttributesLine')
 )
 );
 }
 }
 public function parseGitAttributesLine($line)
 {
 $parts = Preg::split('#\s+#', $line);
 if (count($parts) == 2 && $parts[1] === 'export-ignore') {
 return $this->generatePattern($parts[0]);
 }
 if (count($parts) == 2 && $parts[1] === '-export-ignore') {
 return $this->generatePattern('!'.$parts[0]);
 }
 return null;
 }
}
