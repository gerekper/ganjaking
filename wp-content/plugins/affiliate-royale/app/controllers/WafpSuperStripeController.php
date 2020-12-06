<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/** This is a controller that handles all of the SuperStripe Payment
  * specific public static functions for the Affiliate Program.
  */
class WafpSuperStripeController {

  public static function load_hooks() {
    add_action('supstr-transaction-complete', 'WafpSuperStripeController::track');
  }

  /* Tracks when a transaction completes */
  public static function track($post_id) {
    $txn_num  = get_post_meta( $post_id, '_supstr_txn_num', true );
    $amount   = get_post_meta( $post_id, '_supstr_txn_price', true );
    $desc     = get_post_meta( $post_id, '_supstr_txn_desc', true );
    $response = json_encode( get_post_meta( $post_id, '_supstr_txn_response', true ) );

    WafpTransaction::track( $amount, $txn_num, $desc, '', '', $response );
  }
}
