<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Exceptions\InvalidFormatException;
use ReturnTypeWillChange;
use Throwable;
trait Serialization
{
 use ObjectInitialisation;
 protected static $serializer;
 protected $dumpProperties = ['date', 'timezone_type', 'timezone'];
 protected $dumpLocale;
 protected $dumpDateProperties;
 public function serialize()
 {
 return \serialize($this);
 }
 public static function fromSerialized($value)
 {
 $instance = @\unserialize((string) $value);
 if (!$instance instanceof static) {
 throw new InvalidFormatException("Invalid serialized value: {$value}");
 }
 return $instance;
 }
 #[\ReturnTypeWillChange]
 public static function __set_state($dump)
 {
 if (\is_string($dump)) {
 return static::parse($dump);
 }
 $date = \get_parent_class(static::class) && \method_exists(parent::class, '__set_state') ? parent::__set_state((array) $dump) : (object) $dump;
 return static::instance($date);
 }
 public function __sleep()
 {
 $properties = $this->getSleepProperties();
 if ($this->localTranslator ?? null) {
 $properties[] = 'dumpLocale';
 $this->dumpLocale = $this->locale ?? null;
 }
 return $properties;
 }
 #[\ReturnTypeWillChange]
 public function __wakeup()
 {
 if (\get_parent_class() && \method_exists(parent::class, '__wakeup')) {
 // @codeCoverageIgnoreStart
 try {
 parent::__wakeup();
 } catch (Throwable $exception) {
 // FatalError occurs when calling msgpack_unpack() in PHP 7.4 or later.
 ['date' => $date, 'timezone' => $timezone] = $this->dumpDateProperties;
 parent::__construct($date, \unserialize($timezone));
 }
 // @codeCoverageIgnoreEnd
 }
 $this->constructedObjectId = \spl_object_hash($this);
 if (isset($this->dumpLocale)) {
 $this->locale($this->dumpLocale);
 $this->dumpLocale = null;
 }
 $this->cleanupDumpProperties();
 }
 #[\ReturnTypeWillChange]
 public function jsonSerialize()
 {
 $serializer = $this->localSerializer ?? static::$serializer;
 if ($serializer) {
 return \is_string($serializer) ? $this->rawFormat($serializer) : $serializer($this);
 }
 return $this->toJSON();
 }
 public static function serializeUsing($callback)
 {
 static::$serializer = $callback;
 }
 public function cleanupDumpProperties()
 {
 foreach ($this->dumpProperties as $property) {
 if (isset($this->{$property})) {
 unset($this->{$property});
 }
 }
 return $this;
 }
 private function getSleepProperties() : array
 {
 $properties = $this->dumpProperties;
 // @codeCoverageIgnoreStart
 if (!\extension_loaded('msgpack')) {
 return $properties;
 }
 if (isset($this->constructedObjectId)) {
 $this->dumpDateProperties = ['date' => $this->format('Y-m-d H:i:s.u'), 'timezone' => \serialize($this->timezone ?? null)];
 $properties[] = 'dumpDateProperties';
 }
 return $properties;
 // @codeCoverageIgnoreEnd
 }
}
