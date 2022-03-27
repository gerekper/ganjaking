<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_NullSchedule extends ActionScheduler_SimpleSchedule {
 public function __construct( DateTime $date = null ) {
 $this->scheduled_date = null;
 }
 public function __sleep() {
 return array();
 }
 public function __wakeup() {
 $this->scheduled_date = null;
 }
}
