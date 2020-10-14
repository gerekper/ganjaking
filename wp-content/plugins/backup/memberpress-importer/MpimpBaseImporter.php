<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

abstract class MpimpBaseImporter {
  public abstract function form();

  public abstract function import($row,$args);

  protected function check_required($action, $headers, $required) {
    if( array_key_exists( 'any', $required ) ) {
      if( !is_array( $required['any'] ) ) { return; }
      $successes = 0;
      foreach( $required['any'] as $any_req ) {
        // wrap non-arrays in an array so we don't throw out the recursion
        $any_req = ( !is_array($any_req) ? array( $any_req ) : $any_req );
        try {
          $this->check_required($action, $headers, $any_req);
          $successes++;
        }
        catch(Exception $e) {
          // Just here to supress exceptions
        }
      }

      // Not more or less than 1 can be true in an any
      if( $successes != 1 )
        throw new MpimpStopImportException( sprintf( __('Your CSV file is misconfigured for %s ... please check the importer documentation and try again', 'memberpress-importer'), ucwords($action) ) );
    }
    else if( array_key_exists( 'any_optional', $required ) ) {
      if( !is_array($required['any_optional']) ) { return; }

      foreach( $required['any_optional'] as $any_req ) {
        // wrap non-arrays in an array so we don't throw out the recursion
        $any_req = ( !is_array($any_req) ? array( $any_req ) : $any_req );

        // Determine if this set of cols exist
        $cols = array_intersect( $any_req, $headers );

        // only check if some of the columns from this block exists
        if(!empty($cols))
          $this->check_required($action, $headers, $any_req);
      }
    }
    else {
      foreach($required as $col) {
        if( is_array($col) ) {
          $this->check_required($action, $headers, $col);
        }
        else if(!in_array($col, $headers)) {
          throw new MpimpStopImportException( sprintf( __( '%1$s not found in the %2$s CSV file ... processing has been halted ... CSV must contain a \'%1$s\' column', 'memberpress-importer'), $col, ucwords($action) ) );
        }
      }
    }
  }

  protected function def($row, $col, $default) {
    return isset($row[$col]) ? $row[$col] : $default;
  }

  protected function fail_if_empty($col, $val) {
    if(is_null($val) or $val=='')
      throw new Exception( sprintf( __( '%1$s cannot be blank', 'memberpress-importer'), $col ) );
  }

  protected function fail_if_not_number($col, $val) {
    if(!is_numeric($val))
      throw new Exception( sprintf( __( '%1$s must be a number', 'memberpress-importer'), $col ) );
  }

  protected function fail_if_not_integer($col, $val) {
    if(!is_integer($val))
      throw new Exception( sprintf( __( '%1$s must be an integer (whole number)', 'memberpress-importer'), $col ) );
  }

  protected function fail_if_not_float($col, $val) {
    if(!is_float($val))
      throw new Exception( sprintf( __( '%1$s must be a float (decimal)' , 'memberpress-importer'), $col ) );
  }

  protected function fail_if_not_bool($col, $val) {
    if(!in_array($val,array(true,false,1,0,'1','0')))
      throw new Exception( sprintf( __( '%1$s must be boolean (a 1 for true or a 0 for false)', 'memberpress-importer'), $col ) );
  }

  protected function fail_if_not_in_enum($col, $val, $enum=array()) {
    if(!in_array($val, $enum))
      throw new Exception( sprintf( __( '%1$s can only be one of the following values: %2$s', 'memberpress-importer'), $col, implode(', ', $enum) ) );
  }

  protected function fail_if_not_date($col, $date) {
    if(!preg_match("#^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$#", $date))
      throw new Exception( sprintf( __( '%1$s "%2$s" must be formatted like this: "####-##-## ##:##:##"', 'memberpress-importer'), $col, $date ) );
  }

  protected function fail_if_not_valid_product_id($product_id) {
    $prd = new MeprProduct($product_id);
    if(empty($prd->ID))
      throw new Exception( sprintf( __( 'Product (ID = %1$s) was not found', 'memberpress-importer'), $product_id ) );
  }

  protected function fail_if_not_valid_rule_id($rule_id) {
    $rule = new MeprRule($rule_id);
    if(empty($rule->ID))
      throw new Exception( sprintf( __( 'Rule (ID = %1$s) was not found', 'memberpress-importer'), $rule_id ) );
  }

  protected function fail_if_not_valid_group_id($group_id) {
    $grp = new MeprGroup($group_id);
    if(empty($grp->ID))
      throw new Exception( sprintf( __( 'Group (ID = %1$s) was not found', 'memberpress-importer'), $group_id ) );
  }

  protected function fail_if_not_valid_username($username) {
    if(!function_exists('username_exists'))
      require_once( ABSPATH . 'wp-includes/user.php' );

    $user_id = username_exists( $username );

    if(empty($user_id))
      throw new Exception( sprintf( __( 'User (username = %1$s) was not found', 'memberpress-importer'), $username ) );
  }

  protected function fail_if_not_valid_user_email($user_email) {
    if(!function_exists('get_user_by'))
      require_once( ABSPATH . 'wp-includes/pluggable.php' );

    $user = get_user_by( 'email', $user_email );

    if(empty($user))
      throw new Exception( sprintf( __( 'User (email = %1$s) was not found', 'memberpress-importer'), $user_email ) );
  }

  protected function fail_if_not_valid_email($email) {
    if(!function_exists('is_email'))
      require_once( ABSPATH . 'wp-includes/formatting.php' );

    if(!is_email( $email ))
      throw new Exception( sprintf( __( 'Email (email = %1$s) isn\'t a real email address', 'memberpress-importer'), $email ) );
  }

  protected function fail_if_not_valid_sub_num($sub_num) {
    if(false===MeprSubscription::get_one_by_subscr_id($sub_num))
      throw new Exception( sprintf( __( 'Subscription (sub_num = %1$s) was not found', 'memberpress-importer'), $sub_num ) );
  }

  protected function fail_if_not_valid_coupon_code($coupon_code) {
    if(false===MeprCoupon::get_one_from_code($coupon_code))
      throw new Exception( sprintf( __( 'Coupon (coupon_code = %1$s) was not found', 'memberpress-importer'), $coupon_code ) );
  }

  protected function fail_if_not_valid_payment_method($pm) {
    $mepr_options = MeprOptions::fetch();
    $pms = $mepr_options->payment_methods();
    if(false===($valid_pm = $mepr_options->payment_method($pm)))
      throw new Exception( sprintf( __( 'Payment Method (payment_method = %1$s) was not found, must be one of the following values: %2$s', 'memberpress-importer'), $pm, implode( ', ', array_keys($pms) ) ) );
  }

  protected function fail_if_admin($user_id, $username) {
    if($user_id && user_can($user_id, 'manage_options')) {
      throw new Exception( sprintf( __( 'User (%1$s) is an Administrator and was not modified', 'memberpress-importer'), $username ) );
    }
  }
}
