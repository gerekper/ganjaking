<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpUsersImporter extends MpimpBaseImporter {
  public function form() {
    ?>
      <input type="checkbox" name="args[notice]" <?php checked(false); ?> />
      <?php _e('Send NEW members a password reset link (does not email existing members)'); ?>
    <?php
  }

  public function import($row, $args) {
    $res = $this->import_user($row, $args);
    extract($res);

    if($exists) {
      return sprintf( __('User (username=%1$s, ID=%2$s) already existed and was updated successfully'), $user['user_login'], $user_id );
    }
    else {
      return sprintf( __('User (username=%1$s, ID=%2$s) was created successfully'), $user['user_login'], $user_id );
    }
  }

  public function is_checkboxes_field($slug) {
    $mepr_options = MeprOptions::fetch();

    if(empty($mepr_options->custom_fields)) { return false; }

    foreach($mepr_options->custom_fields as $row) {
      if($row->field_key != $slug) { continue; }

      return ($row->field_type == 'checkboxes');
    }

    return false;
  }

  public function is_multiselect_field($slug) {
    $mepr_options = MeprOptions::fetch();

    if(empty($mepr_options->custom_fields)) { return false; }

    foreach($mepr_options->custom_fields as $row) {
      if($row->field_key != $slug) { continue; }

      return ($row->field_type == 'multiselect');
    }

    return false;
  }

  protected function import_user($row, $args, $ignore_cols = array()) {
    global $wp_roles;

    $required = array('username', 'email');

    $this->check_required('users', array_keys($row), $required);

    // Merge in default values where applicable
    $row = array_merge(array('role' => 'subscriber'), $row);
    $user = array();
    $user_meta = array();
    $gen_password = false; //don't default this to true or we'll overwrite all existing members passwords :)
    $send_notification = (is_array($args) && isset($args['notice']));

    foreach($row as $col => $cell) {
      switch($col) {
        case "username":
          $this->fail_if_empty($col, $cell);
          $user["user_login"] = $cell;
          break;
        case "email":
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_valid_email($cell);
          $user["user_email"] = $cell;
          break;
        case "password":
          $this->fail_if_empty($col, $cell);
          $user["user_pass"] = $cell;
          break;
        case "role":
          $user["role"] = empty($cell)?'subscriber':$cell;
          $this->fail_if_not_in_enum($col,$user["role"],array_keys($wp_roles->roles));
          break;
        case "gen_password": //We're going to silently omit this from the docs now that we send a password reset notification instead
          $gen_password = ((int)$cell == 1);
          break;
        case "first_name":
          $user['first_name'] = $cell;
          break;
        case "last_name":
          $user['last_name'] = $cell;
          break;
        case "website":
          $user['user_url'] = $cell;
          break;
        case "registered":
          $user['user_registered'] = $cell;
          break;
      /**** Supported Meta ****/
        case "address1":
          $user_meta['mepr-address-one'] = $cell;
          break;
        case "address2":
          $user_meta['mepr-address-two'] = $cell;
          break;
        case "city":
          $user_meta['mepr-address-city'] = $cell;
          break;
        case "state":
          $user_meta['mepr-address-state'] = $cell;
          break;
        case "zip":
          $user_meta['mepr-address-zip'] = $cell;
          break;
        case "country":
          $user_meta['mepr-address-country'] = $cell;
          break;
        default:
          // We assume that ignore_cols will be handled elsewhere
          // otherwise we'll set the value as a usermeta
          if(!in_array($col, $ignore_cols)) {
            //make it easier for users to import checkboxes custom fields
            if($this->is_checkboxes_field($col) && !empty($cell)) {
              $selected_boxes = array();
              $vals = explode(',', $cell);

              foreach($vals as $val) {
                $selected_boxes[$val] = 'on';
              }

              //We unserialize it later if needed
              $cell = maybe_serialize($selected_boxes);
            }

            //make it easier for users to import multiselect custom fields
            if($this->is_multiselect_field($col) && !empty($cell)) {
              $vals = explode(',', $cell);

              //We unserialize it later if needed
              $cell = maybe_serialize($vals);
            }

            $user_meta[$col] = maybe_unserialize($cell); //Mabye unserialize here lets us import serialized data for checkboxes etc
          }
      }
    }

    if( ($user_id = $exists = username_exists($user['user_login'])) ) {
      $user['ID'] = $user_id;
    }

    //Don't wipe out the Admin's role yo
    if($exists) { $this->fail_if_admin($user_id, $user['user_login']); }

    // We'll automatically generate a new password for each new user if one isn't set or if gen_password is true
    if((!$exists && (!isset($user['user_pass']) || empty($user['user_pass']))) || $gen_password) {
      $user['user_pass'] = MeprUtils::random_string(10, true, true, true);
    }

    if($exists) {
      unset($user["role"]); //Unset the role so we don't wipe it out
      add_filter('send_email_change_email', '__return_false'); //Disable the email changed notificaiton from WP?
      add_filter('send_password_change_email', '__return_false'); //Disable the password reset notificaiton from WP?
      $user_id = wp_update_user($user);
    } else {
      $user_id = wp_insert_user($user);
    }

    if(is_wp_error($user_id)) {
      throw new Exception($user_id->get_error_message());
    }

    if((!$exists || $gen_password) && $send_notification) {
      $mepr_user = new MeprUser($user_id);
      $mepr_user->send_password_notification('ManuallyAddedUser', true); //This func only listens for "reset" type, so I'm just making up a type here
    }

    if(!empty($user_meta)) {
      foreach($user_meta as $key => $val) {
        update_user_meta($user_id, $key, $val);
      }
    }

    if(!$exists) {
      $mepr_user = new MeprUser($user_id);
      // Needed for autoresponders - call before txn is stored
      MeprHooks::do_action('mepr-signup-user-loaded', $mepr_user);
    }

    return compact('exists', 'user_id', 'user');
  }
}
