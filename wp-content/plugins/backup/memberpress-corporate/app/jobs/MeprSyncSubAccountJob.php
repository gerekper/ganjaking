<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprSyncSubAccountJob extends MeprBaseJob {
  public function perform() {
    if( !isset($this->user_id) || empty($this->user_id) ) {
      throw new Exception(__('"user_id" can\'t be blank', 'memberpress-corporate'));
    }

    if( !isset($this->parent_transaction_id) || empty($this->parent_transaction_id) ) {
      throw new Exception(__('"parent_transaction_id" can\'t be blank', 'memberpress-corporate'));
    }

    $sub_account_transaction_id = MPCA_Sync_Transactions::get_sub_account_transaction_id($this->user_id, $this->parent_transaction_id);

    if( empty($sub_account_transaction_id) ) {
      MPCA_Sync_Transactions::add_transaction($this->user_id, $this->parent_transaction_id);
    } else {
      MPCA_Sync_Transactions::update_transaction($sub_account_transaction_id, $this->parent_transaction_id);
    }
  }
}

