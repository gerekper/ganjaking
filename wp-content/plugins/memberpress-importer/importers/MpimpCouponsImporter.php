<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpCouponsImporter extends MpimpBaseImporter {
  public function form() { }

  public function import($row,$args) {
    $required = array('type','discount');
    $this->check_required('coupons', array_keys($row), $required);
    $random_code = MeprUtils::random_string( 10, false, true );

    // Merge in default values where applicable
    $row = array_merge( array( 'code' => $random_code,
                               'expires_at' => null,
                               'usage_amount' => 0,
                               'discount_mode' => 'standard' ), $row ); // 0 = infinite uses

    $coupon = new MeprCoupon();

    $valid_products = array();
    $valid_types = array('percent','dollar');
    $override = false;

    foreach( $row as $col => $cell ) {
      if( preg_match( '#^product_id_.*$#', $col ) ) {
        if(!empty($cell)) {
          $this->fail_if_not_valid_product_id( $cell );
          $valid_products[] = $cell;
        }
      }
      else {
        switch( $col ) {
          // Should consider adding valid_products and usage_count to this switch
          case "code":
            $this->fail_if_empty($col, $cell);
            $coupon->post_title = empty($cell)?$random_code:$cell;
            break;
          case "discount":
            $this->fail_if_empty($col, $cell);
            $this->fail_if_not_number($col, $cell);
            $coupon->discount_amount = $cell;
            break;
          case "type":
            $this->fail_if_empty($col, $cell);
            $this->fail_if_not_in_enum($col, $cell, $valid_types);
            $coupon->discount_type = $cell;
            break;
          case "usage_amount":
            $this->fail_if_not_number($col, $cell);
            $coupon->usage_amount = empty($cell)?0:$cell;
            break;
          case "discount_mode":
            //Defaults to 'standard' above
            if($cell == 'first-payment' || $cell == 'trial-override') {
              $coupon->discount_mode = strtolower($cell);
            }
            break;
          case "first_payment_discount_type":
            if(!empty(trim($cell))) {
              $this->fail_if_not_in_enum($col, $cell, $valid_types);
              $coupon->first_payment_discount_type = $cell;
            }
            break;
          case "first_payment_discount_amount":
            if(!empty(trim($cell))) {
              $this->fail_if_not_number($col, $cell);
              $coupon->first_payment_discount_amount = $cell;
            }
            break;
          case "trial_days":
            $this->fail_if_not_number($col, $cell);
            $coupon->trial_days = (int)$cell;
            break;
          case "trial_amount":
            $this->fail_if_not_number($col, $cell);
            $coupon->trial_amount = ((float)$cell <= 0.00)?0.00:(float)$cell;
            break;
          case "description":
            $coupon->post_content = trim($cell);
            break;
          case "use_on_upgrades":
            $coupon->use_on_upgrades = ! empty( intval( $cell ) ) ? true : false;
            break;
          case "expires_at":
            if(empty($cell)) {
              $coupon->should_expire = false;
              $coupon->expires_on = 0;
            }
            else {
              // Some spreadsheets force the use of '/' instead of '-' to separate dates
              $cell = preg_replace('#/#','-',$cell);
              $this->fail_if_not_date($col, $cell);
              $coupon->should_expire = true;
              $coupon->expires_on = strtotime($cell); //DATES must be in d/m/y or d-m-y format for strtotime to work properly
            }
            break;
          case "starts_on":
            if(empty($cell)) {
              $coupon->should_start = false;
              $coupon->starts_on = 0;
            }
            else {
              // Some spreadsheets force the use of '/' instead of '-' to separate dates
              $cell = preg_replace('#/#','-',$cell);
              $this->fail_if_not_date($col, $cell);
              $coupon->should_start = true;
              $coupon->starts_on = strtotime($cell); //DATES must be in d/m/y or d-m-y format for strtotime to work properly
            }
            break;
          case "override_existing":
            $override = 1 == $cell;
            break;
        }
      }
    }

    if ( true === $override ) {
      $maybe_coupon = MeprCoupon::get_one_from_code( $coupon->post_title, true );
      if ( ! empty( $maybe_coupon->ID ) ) {
        $coupon->ID = $maybe_coupon->ID;
      }
    }

    $coupon->valid_products = $valid_products;

    if( $coupon_id = $coupon->store() )
      return sprintf(__('Coupon (ID = %s) was %s successfully'), $coupon_id, true === $override ? __('updated') : __('created'));
    else
      throw new Exception(__('Coupon failed to be created'));
  }
}
