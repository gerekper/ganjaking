<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_DateTime extends DateTime {
 protected $utcOffset = 0;
 public function getTimestamp() {
 return method_exists( 'DateTime', 'getTimestamp' ) ? parent::getTimestamp() : $this->format( 'U' );
 }
 public function setUtcOffset( $offset ) {
 $this->utcOffset = intval( $offset );
 }
 public function getOffset() {
 return $this->utcOffset ? $this->utcOffset : parent::getOffset();
 }
 public function setTimezone( $timezone ) {
 $this->utcOffset = 0;
 parent::setTimezone( $timezone );
 return $this;
 }
 public function getOffsetTimestamp() {
 return $this->getTimestamp() + $this->getOffset();
 }
}
