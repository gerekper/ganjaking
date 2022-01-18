<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class Alias
{
 private const DEFAULT_DEPRECATION_TEMPLATE = 'The "%alias_id%" service alias is deprecated. You should stop using it, as it will be removed in the future.';
 private $id;
 private $public;
 private $private;
 private $deprecated;
 private $deprecationTemplate;
 public function __construct(string $id, bool $public = \true)
 {
 $this->id = $id;
 $this->public = $public;
 $this->private = 2 > \func_num_args();
 $this->deprecated = \false;
 }
 public function isPublic()
 {
 return $this->public;
 }
 public function setPublic($boolean)
 {
 $this->public = (bool) $boolean;
 $this->private = \false;
 return $this;
 }
 public function setPrivate($boolean)
 {
 $this->private = (bool) $boolean;
 return $this;
 }
 public function isPrivate()
 {
 return $this->private;
 }
 public function setDeprecated($status = \true, $template = null)
 {
 if (null !== $template) {
 if (\preg_match('#[\\r\\n]|\\*/#', $template)) {
 throw new InvalidArgumentException('Invalid characters found in deprecation template.');
 }
 if (!\str_contains($template, '%alias_id%')) {
 throw new InvalidArgumentException('The deprecation template must contain the "%alias_id%" placeholder.');
 }
 $this->deprecationTemplate = $template;
 }
 $this->deprecated = (bool) $status;
 return $this;
 }
 public function isDeprecated() : bool
 {
 return $this->deprecated;
 }
 public function getDeprecationMessage(string $id) : string
 {
 return \str_replace('%alias_id%', $id, $this->deprecationTemplate ?: self::DEFAULT_DEPRECATION_TEMPLATE);
 }
 public function __toString()
 {
 return $this->id;
 }
}
