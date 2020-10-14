<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpMembershipsImporter extends MpimpBaseImporter {
  public function form() { }

  public function import($row,$args) {
    $required = array('name','price','period','period_type');
    $this->check_required('products', array_keys($row), $required);

    // Merge in default values where applicable
    $row = array_merge( array( 'trial' => 0,
                               'trial_days' => 0,
                               'trial_amount' => 0.00,
                               'group_id' => 0 ), $row );

    $product = new MeprProduct();
    $product->post_status='publish';

    foreach( $row as $col => $cell ) {
      switch( $col ) {
        case "name":
          $this->fail_if_empty($col, $cell);
          $product->post_title = $cell;
          break;
        case "price":
          $this->fail_if_empty($col, $cell);
          $product->price = $cell;
          break;
        case "period":
          $this->fail_if_empty($col, $cell);
          $product->period = $cell;
          break;
        case "period_type":
          $this->fail_if_empty($col, $cell);
          $product->period_type = $cell;
          break;
        case "trial":
          $product->trial = empty($cell)?false:((int)$cell==1);
          break;
        case "trial_days":
          $product->trial_days = empty($cell)?0:$cell;
          $this->fail_if_not_number($col,$cell);
          break;
        case "trial_amount":
          $product->trial_amount = empty($cell)?0.00:$cell;
          $this->fail_if_not_number($col,$cell);
          break;
        case "group_id":
          $product->group_id = empty($cell)?0:$cell;
          if( $product->group_id != 0 )
            $this->fail_if_not_valid_group_id($cell);
          break;
      }
    }

    if( $product_id = $product->store() )
      return sprintf(__('Membership (ID = %d) was created successfully'), $product_id);
    else
      throw new Exception(__('Membership failed to be created'));
  }
}
