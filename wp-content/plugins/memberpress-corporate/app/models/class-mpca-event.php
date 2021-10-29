<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Event extends MeprEvent {
  /**
  * Create a new Event record in the DB
  * Similar to MeprEvent::record but doesn't use objects
  */
  public static function record_event($event, $event_id, $event_type, $args='') {
    if((int) $event_id > 0) {
      $e = new MPCA_Event;
      $e->event = $event;
      $e->evt_id = $event_id;
      $e->evt_id_type = $event_type;
      if(!empty($args)) {
        $e->args = json_encode($args);
      }

      $e->store();
    }
  }
}
