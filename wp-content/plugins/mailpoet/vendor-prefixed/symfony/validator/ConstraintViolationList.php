<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
class ConstraintViolationList implements \IteratorAggregate, ConstraintViolationListInterface
{
 private $violations = [];
 public function __construct(array $violations = [])
 {
 foreach ($violations as $violation) {
 $this->add($violation);
 }
 }
 public function __toString()
 {
 $string = '';
 foreach ($this->violations as $violation) {
 $string .= $violation . "\n";
 }
 return $string;
 }
 public function add(ConstraintViolationInterface $violation)
 {
 $this->violations[] = $violation;
 }
 public function addAll(ConstraintViolationListInterface $otherList)
 {
 foreach ($otherList as $violation) {
 $this->violations[] = $violation;
 }
 }
 public function get($offset)
 {
 if (!isset($this->violations[$offset])) {
 throw new \OutOfBoundsException(\sprintf('The offset "%s" does not exist.', $offset));
 }
 return $this->violations[$offset];
 }
 public function has($offset)
 {
 return isset($this->violations[$offset]);
 }
 public function set($offset, ConstraintViolationInterface $violation)
 {
 $this->violations[$offset] = $violation;
 }
 public function remove($offset)
 {
 unset($this->violations[$offset]);
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 return new \ArrayIterator($this->violations);
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 return \count($this->violations);
 }
 #[\ReturnTypeWillChange]
 public function offsetExists($offset)
 {
 return $this->has($offset);
 }
 #[\ReturnTypeWillChange]
 public function offsetGet($offset)
 {
 return $this->get($offset);
 }
 #[\ReturnTypeWillChange]
 public function offsetSet($offset, $violation)
 {
 if (null === $offset) {
 $this->add($violation);
 } else {
 $this->set($offset, $violation);
 }
 }
 #[\ReturnTypeWillChange]
 public function offsetUnset($offset)
 {
 $this->remove($offset);
 }
 public function findByCodes($codes)
 {
 $codes = (array) $codes;
 $violations = [];
 foreach ($this as $violation) {
 if (\in_array($violation->getCode(), $codes, \true)) {
 $violations[] = $violation;
 }
 }
 return new static($violations);
 }
}
