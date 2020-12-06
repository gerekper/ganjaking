<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpSubscriptionsImporter extends MpimpBaseImporter {
  public function form() { }

  public function import($row,$args) {
    $required = array(array('any' => array('username','email')), 'product_id', 'payment_method', 'sub_num', 'amount', 'total', 'period', 'period_type');

    $cols = array_keys($row);
    $this->check_required('subscriptions', $cols, $required);
    $this->fail_if_empty('product_id', $row['product_id']);
    $this->fail_if_empty('payment_method', $row['payment_method']);
    $this->fail_if_empty('sub_num', $row['sub_num']);
    $this->fail_if_empty('amount', $row['amount']);
    $this->fail_if_empty('total', $row['total']);
    $this->fail_if_empty('period', $row['period']);
    $this->fail_if_empty('period_type', $row['period_type']);

    // Merge in default values where cols missing
    $row = array_merge( array('coupon_code' => '',
                              'created_at' => date('Y-m-d H:i:s'),
                              'status' => MeprSubscription::$active_str), $row );

    $sub = new MeprSubscription();

    // Load up the defaults like a champion
    // Must do this before we start churning through rows so we don't overwrite any goodies
    $this->fail_if_not_valid_product_id($row['product_id']);
    $prd = new MeprProduct($row['product_id']);
    $sub->product_id = $prd->ID;
    $sub->load_product_vars($prd, (isset($row['coupon_code']) ? $row['coupon_code'] : null));

    // Updating existing sub?
    $updating_sub = false;
    if(isset($row['id']) && !empty($row['id'])) {
      $existing_sub = new MeprSubscription($row['id']);
      if(isset($existing_sub->id) && $existing_sub->id > 0) {
        $sub = $existing_sub;
        $updating_sub = true;
      }
    }

    $valid_period_types = array('weeks', 'months', 'years', 'lifetime');
    $valid_statuses = array(MeprSubscription::$active_str,
                            MeprSubscription::$cancelled_str,
                            MeprSubscription::$suspended_str,
                            MeprSubscription::$pending_str);

    // In case we're importing cc info
    $cc = array();

    foreach($row as $col => $cell) {
      switch($col) {
        case "email":
        case "username":
          $this->fail_if_empty($col, $cell);
          $usr = new MeprUser();

          if($col == "username") {
            $this->fail_if_not_valid_username($row['username']);
            $usr->load_user_data_by_login($cell);
          }
          else {
            $this->fail_if_not_valid_user_email($row['email']);
            $usr->load_user_data_by_email($cell);
          }

          $sub->user_id = $usr->ID;
          break;
        case 'payment_method':
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_valid_payment_method($cell);
          $sub->gateway = $cell;
          break;
        // Import Existing Columns
        case 'sub_num':
        case 'subscr_id':
          $this->fail_if_empty($col, $cell);
          $sub->subscr_id = $cell;
          break;
        case "amount":
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_number($col, $cell);
          $sub->price = $cell;
          break;
        case 'total':
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_number($col, $cell);
          $sub->{$col} = empty($cell)?0:$cell;
          break;
        case 'tax_rate':
          $sub->{$col} = empty($cell)?0:$cell;
          break;
        case 'tax_amount':
          $sub->{$col} = empty($cell)?0:$cell;
          break;
        case 'tax_desc':
          $sub->{$col} = empty($cell)?'':$cell;
          break;
        case 'tax_class':
          $sub->{$col} = empty($cell)?'standard':$cell;
          break;
        case "period":
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_number($col, $cell);
          $sub->{$col} = $cell;
          break;
        case "period_type":
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_in_enum($col, $cell, $valid_period_types);
          $sub->{$col} = $cell;
          break;
        case 'limit_cycles':
          $sub->{$col} = empty($cell)?0:1;
          break;
        case 'limit_cycles_num':
          $sub->{$col} = empty($cell)?2:$cell;
          break;
        case 'limit_cycles_action':
          $sub->{$col} = empty($cell)?'':$cell;
          break;
        case 'prorated_trial':
          $sub->{$col} = empty($cell)?0:1;
          break;
        case 'trial':
          $sub->{$col} = empty($cell)?0:$cell;
          $this->fail_if_not_bool($col,$sub->{$col});
          break;
        case 'trial_days':
          $sub->{$col} = empty($cell)?0:$cell;
          $this->fail_if_not_number($col,$sub->{$col});
          break;
        case 'trial_amount':
          $sub->{$col} = empty($cell)?0.00:$cell;
          $this->fail_if_not_number($col,$sub->{$col});
          break;
        case 'status':
          $sub->status = empty($cell) ? MeprSubscription::$active_str : $cell;
          $this->fail_if_not_in_enum($col,$sub->status,$valid_statuses);
          break;
        case 'created_at':
        case 'started_at':
          $sub->created_at = empty($cell) ? date('Y-m-d H:i:s') : $cell;
          $this->fail_if_not_date($col,$sub->created_at);
          break;
        case 'ip_addr':
          $sub->{$col} = empty($cell) ? '' : $cell;
          break;
        case 'cc_last4':
          $sub->{$col} = empty($cell)?'':$cell;
          break;
        case 'cc_exp_month':
          $sub->{$col} = empty($cell)?'':$cell;
          break;
        case 'cc_exp_year':
          $sub->{$col} = empty($cell)?'':$cell;
          break;
        default:
          break;
      }
    }

    // Hook to work with the subscription before its stored
    MeprHooks::apply_filters('mepr_import_subscription_pre_store', $sub);

    $sub_id = $sub->store();

    // Hook to work with the subscription after its stored
    MeprHooks::apply_filters('mepr_import_subscription_post_store', $sub_id);

    if($sub_id) {
      if($updating_sub) {
        $sub_message = sprintf(__('Subscription with sub_num = %s was found with an id = %s and was updated successfully'), $sub->subscr_id, $sub_id);
      }
      else {
        $sub_message = sprintf(__('Subscription (id = %s) was created successfully'), $sub_id);
      }

      return $sub_message;
    }
    else {
      throw new Exception(__('Subscription failed to be created'));
    }
  }
}
