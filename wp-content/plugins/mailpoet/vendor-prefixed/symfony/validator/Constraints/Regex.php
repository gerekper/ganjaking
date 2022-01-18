<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidArgumentException;
class Regex extends Constraint
{
 public const REGEX_FAILED_ERROR = 'de1e3db3-5ed4-4941-aae4-59f3667cc3a3';
 protected static $errorNames = [self::REGEX_FAILED_ERROR => 'REGEX_FAILED_ERROR'];
 public $message = 'This value is not valid.';
 public $pattern;
 public $htmlPattern;
 public $match = \true;
 public $normalizer;
 public function __construct($options = null)
 {
 parent::__construct($options);
 if (null !== $this->normalizer && !\is_callable($this->normalizer)) {
 throw new InvalidArgumentException(\sprintf('The "normalizer" option must be a valid callable ("%s" given).', \is_object($this->normalizer) ? \get_class($this->normalizer) : \gettype($this->normalizer)));
 }
 }
 public function getDefaultOption()
 {
 return 'pattern';
 }
 public function getRequiredOptions()
 {
 return ['pattern'];
 }
 public function getHtmlPattern()
 {
 // If htmlPattern is specified, use it
 if (null !== $this->htmlPattern) {
 return empty($this->htmlPattern) ? null : $this->htmlPattern;
 }
 // Quit if delimiters not at very beginning/end (e.g. when options are passed)
 if ($this->pattern[0] !== $this->pattern[\strlen($this->pattern) - 1]) {
 return null;
 }
 $delimiter = $this->pattern[0];
 // Unescape the delimiter
 $pattern = \str_replace('\\' . $delimiter, $delimiter, \substr($this->pattern, 1, -1));
 // If the pattern is inverted, we can wrap it in
 // ((?!pattern).)*
 if (!$this->match) {
 return '((?!' . $pattern . ').)*';
 }
 // If the pattern contains an or statement, wrap the pattern in
 // .*(pattern).* and quit. Otherwise we'd need to parse the pattern
 if (\str_contains($pattern, '|')) {
 return '.*(' . $pattern . ').*';
 }
 // Trim leading ^, otherwise prepend .*
 $pattern = '^' === $pattern[0] ? \substr($pattern, 1) : '.*' . $pattern;
 // Trim trailing $, otherwise append .*
 $pattern = '$' === $pattern[\strlen($pattern) - 1] ? \substr($pattern, 0, -1) : $pattern . '.*';
 return $pattern;
 }
}
