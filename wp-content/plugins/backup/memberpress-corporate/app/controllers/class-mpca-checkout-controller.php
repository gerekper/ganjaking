<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Checkout_Controller {
  public function __construct() {
    // Associate the CA with this signup early on in the signup process
    add_action( 'mepr-signup', array( $this, 'process_signup' ) );

    // In case the user uses a 100% off coupon on a recurring subscription
    add_action( 'mepr-before-subscription-destroy-create-free-transaction', array( $this, 'process_sub_destroy_free_txn' ) );
  }

  public function process_sub_destroy_free_txn($txn) {
    $sub = $txn->subscription();

    $is_corporate_product = get_post_meta($txn->product_id, 'mpca_is_corporate_product', true);

    //The subscription is destroyed so we need to re-associate this CA with the free txn instead
    if($is_corporate_product) {
      $ca = MPCA_Corporate_Account::find_corporate_account_by_obj($sub);
      $ca->obj_id = $txn->id;
      $ca->obj_type = 'transactions';
      $ca->store();
    }
  }

  public function process_signup($transaction) {
    // DO NOT create a parent account when a child is signing up from a parent's link
    if(isset($_GET['ca'])) { return; }

    $obj = $transaction;
    $type = 'transactions';

    if($transaction->subscription_id > 0) {
      $obj = $transaction->subscription();
      $type = 'subscriptions';
    }

    $is_corporate_product = get_post_meta($obj->product_id, 'mpca_is_corporate_product', true);
    $num_sub_accounts = get_post_meta($obj->product_id, 'mpca_num_sub_accounts', true);

    if($is_corporate_product) {
      // create corporate account using the information from above
      $ca = new MPCA_Corporate_Account();
      $ca->obj_id = $obj->id;
      $ca->obj_type = $type;
      $ca->num_sub_accounts = $num_sub_accounts;
      $ca->user_id = $obj->user_id;
      $ca->store();
    }
  }
}
