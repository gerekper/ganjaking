<?php
defined('WYSIJA') or die('Restricted access');
/*
An user field is a custom field identified by ID,
linked to a particular user, identified by ID.
*/

class WJ_FieldUser {

  // User Table.
  private $user_table;
  // Custom fields table.
  private $field_table;
  // User ID.
  public $user_id;
  // The field object.
  public $field;
  // The field value.
  public $value;

  /*
  $field_user = new WJ_FieldUser();
  Just setup correct tables names.
  */
  function __construct() {
    $this->user_table = WJ_Settings::db_prefix('user');
    $this->field_table = WJ_Settings::db_prefix('custom_field');
  }

  /*
  After object creation, you can set the user id,
  and the field id. The object will now contain the right
  custom field, and the user id.
  $field_user->set(array(
  'user_id' => 1,
  'field_id' => 2
  ));
  */
  public function set($args) {
    $this->user_id = $args['user_id'];
    $this->field = WJ_Field::get($args['field_id']);
  }

  /*
  Updates the field user value.
  $field_user->update('Main Street');
  */
  public function update($value) {
    global $wpdb;
    $column_name = $this->field->user_column_name();
    // Cast value to the correct column type.
    switch ($this->field->type) {
      case 'checkbox':
        $validation = '%d';
        $value = (int)$value;
        break;
      default:
        // We default to a string.
        $validation = '%s';
        $value = (string)$value;
        break;
    }
    $result = $wpdb->update(
      $this->user_table,
      array(
        $column_name => $value
      ), array("user_id" => $this->user_id),
      array($validation),
      array("%d")
    );
    if ($result != false) {
      $this->value = $value;
    }
    return $result;
  }

  /*
  Get the user field value.
  $field_user->value();
  # => 'Main Street'
  */
  public function value() {
    $value = '';
    if (isset($this->value)) {
      $value = $this->value;
    } else {
      $column_name = $this->field->user_column_name();
      global $wpdb;
      $result = $wpdb->get_row($wpdb->prepare(
        "SELECT $column_name FROM $this->user_table
        WHERE user_id = %d",
        array($this->user_id)
      ), ARRAY_A);
      $this->value = $result[$column_name];
      $value = $result[$column_name];
    }
    return $value;
  }

  /*
  Get the user unique column name.
  $field_user->column_name();
  # => 'cf_1'
  */
  public function column_name() {
    return $this->field->user_column_name();
  }

  /*
  Get all UserFields by User ID.
  $WJ_FieldUser::get_all();
  # => Array of WJ_FieldUser
  */
  public static function get_all($user_id) {
    $fields = WJ_Field::get_all();
    if(isset($fields) && !empty($fields)) {
      $collection = array();
      foreach ($fields as $field) {
        $user_field = new self();
        $user_field->user_id = $user_id;
        $user_field->field = $field;
        $collection[] = $user_field;
      }
      return $collection;
    } else {
      return null;
    }
  }

}
