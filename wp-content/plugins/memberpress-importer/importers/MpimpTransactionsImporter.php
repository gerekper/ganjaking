<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpTransactionsImporter extends MpimpBaseImporter {
  public function form() {
    return; //Temporarily disable this since it's not even used as of right now
    ?>
    <input type="checkbox" name="args[welcome]" />&nbsp; <?php _e('Send a Welcome Email with each new transaction'); ?>
    <?php
  }

  public function import($row,$args) {
    $required = array(
      array('any' => array('username','email')),
      'product_id', 'amount', 'total'
    );
    $this->check_required('transactions', array_keys($row), $required);

    $this->fail_if_empty('product_id', $row['product_id']);

    // Merge in default values where cols missing
    $row = array_merge(
      array(
        'trans_num' => uniqid(),
        'status' => MeprTransaction::$complete_str,
        'sub_num' => 0,
        'expires_at' => null,
        'coupon_code' => '',
        'payment_method' => MeprTransaction::$manual_gateway_str,
        'send_welcome' => 0,
        'send_receipt' => 0
      ),
      $row
    );

    $txn = new MeprTransaction();
    $txn->txn_type = MeprTransaction::$payment_str;

    // Updating existing txn?
    $updating_txn = false;
    if(isset($row['id']) && !empty($row['id'])) {
      $existing_txn = new MeprTransaction($row['id']);
      if(isset($existing_txn->id) && $existing_txn->id > 0) {
        $txn = $existing_txn;
        $updating_txn = true;
      }
    }

    $valid_statuses = array(
      MeprTransaction::$pending_str,
      MeprTransaction::$failed_str,
      MeprTransaction::$complete_str,
      MeprTransaction::$refunded_str
    );

    foreach($row as $col => $cell) {
      switch($col) {
        case "product_id":
          $this->fail_if_not_valid_product_id($cell);
          $prd = new MeprProduct($cell);
          $txn->product_id = $prd->ID;
          break;
        case "username":
        case "email":
          $this->fail_if_empty($col, $cell);
          $usr = new MeprUser();

          if($col == "username") {
            $usr->load_user_data_by_login($cell);
            if(!$usr->ID)
              throw new Exception(sprintf(__('username=%1$s wasn\'t found so couldn\'t create transaction'), $cell));
          }
          else {
            $usr->load_user_data_by_email($cell);
            if(!$usr->ID)
              throw new Exception(sprintf(__('email=%1$s wasn\'t found so couldn\'t create transaction'), $cell));
          }

          $txn->user_id = $usr->ID;
          break;
        case "amount":
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_number($col, $cell);
          $txn->amount = $cell;
          break;
        case "total":
          $this->fail_if_empty($col, $cell);
          $this->fail_if_not_number($col, $cell);
          $txn->total = $cell;
          break;
        case 'tax_rate':
          $txn->{$col} = empty($cell)?0:$cell;
          break;
        case 'tax_amount':
          $txn->{$col} = empty($cell)?0:$cell;
          break;
        case 'tax_desc':
          $txn->{$col} = empty($cell)?'':$cell;
          break;
        case 'tax_class':
          $txn->{$col} = empty($cell)?'standard':$cell;
          break;
        case 'sub_num':
          if(!empty($cell) && strtolower($cell) != 'none') {
            $this->fail_if_not_valid_sub_num($cell);
            if($sub = MeprSubscription::get_one_by_subscr_id($cell)) {
              $txn->subscription_id = $sub->ID;
            }
          }
          break;
        case 'payment_method':
          if(!empty($cell)) {
            $this->fail_if_not_valid_payment_method($cell);
            $txn->gateway = $cell;
          }
          break;
        case 'coupon_code':
          if(!empty($cell) and $cpn = MeprCoupon::get_one_from_code($cell)) {
            $this->fail_if_not_valid_coupon_code($cell);
            $txn->coupon_id = $cpn->ID;
          }
          else
            $txn->coupon_id = 0;
          break;
        case 'send_welcome':
          $send_welcome = ((int)$cell==1);
          break;
        case 'send_receipt':
          $send_receipt = ((int)$cell==1);
          break;
        case 'trans_num':
          $txn->trans_num = empty($cell)?uniqid():$cell;
          break;
        case 'status':
          $txn->status = empty($cell)?MeprTransaction::$complete_str:$cell;
          $this->fail_if_not_in_enum($col,$cell,$valid_statuses);
          break;
        case 'created_at':
          $txn->created_at = $cell;
          break;
        case 'expires_at':
          if(!empty($cell)) {
            $txn->expires_at = $cell;
            $this->fail_if_not_date($col, $cell);
          }
          break;
        case 'ip_addr':
          $txn->{$col} = empty($cell) ? '' : $cell;
          break;
      }
    }

    // Hook to work with the transaction before its stored
    MeprHooks::apply_filters('mepr_import_transaction_pre_store', $txn);

    $txn_id = $txn->store();

    // Hook to work with the transaction after its stored
    MeprHooks::apply_filters('mepr_import_transaction_post_store', $txn_id);

    $mepr_options = MeprOptions::fetch();

    if($txn_id) {
      //Record the completed signup event (other txn events will be called in $send_receipt below)
      if($txn->status == MeprTransaction::$complete_str) {
        MeprHooks::do_action('mepr-signup', $txn);
      }

      if($send_welcome) {
        MeprUtils::send_signup_notices($txn, true, true);
      }

      if($send_receipt) {
        MeprUtils::send_transaction_receipt_notices($txn);
      }
      elseif(($updating_txn && $existing_txn->status !== MeprTransaction::$complete_str && $txn->status === MeprTransaction::$complete_str) ||
                (!$updating_txn && $txn->status === MeprTransaction::$complete_str)) {
        MeprEvent::record('transaction-completed', $txn); //This is normally called in MeprUtils::send_transaction_receipt_notices()
      }

      if($updating_txn) {
        $txn_message = sprintf(__('Transaction with trans_num = %s was found with an id = %s and was updated successfully'), $txn->trans_num, $txn_id);
      }
      else {
        $txn_message = sprintf(__('Transaction (id = %s) was created successfully'), $txn_id);
      }

      return $txn_message;
    }
    else {
      throw new Exception(__('Transaction failed to be created or updated'));
    }
  }
}
