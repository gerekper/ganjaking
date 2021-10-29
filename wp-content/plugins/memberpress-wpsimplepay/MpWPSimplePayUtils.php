<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpWPSimplePayUtils {
  // This function controls adding the user from a stripe charge and (maybe) customer object
  public static function create_user_from_stripe($charge, $customer=null) {
    // If the user is already logged in then just return the current user
    if(MeprUtils::is_user_logged_in()) {
      $current_user = MeprUtils::get_currentuserinfo();
      MeprUtils::debug_log('****** create_user_from_stripe -- Current User Exists: ' . $current_user->user_login);
      return $current_user->ID;
    }

    // Grab the email from the charge object
    if(null===$customer) {
      $email_address = $charge->receipt_email;
    }
    else {
      $email_address = $customer->email;
    }

    // If a user doesn't exist with this email then create a new user and password
    if(false == ($user_id = username_exists($email_address ))) {
      MeprUtils::debug_log('****** create_user_from_stripe -- Creating New User: ' . $email_address);

      // Generate the password and create the user
      $password = wp_generate_password(16, true);
      $user_id  = wp_create_user($email_address, $password, $email_address);

      // Set the required nickname field to the email address
      wp_update_user(
        array(
          'ID'       => $user_id,
          'nickname' => $email_address,
        )
      );

      // Set the role to the lowest role (subscriber). You can change this according to what you need.
      $user = new WP_User($user_id);
      $user->set_role('subscriber');

      MeprUtils::debug_log('****** create_user_from_stripe -- Created New User: ' . $user_id);

      MeprUtils::wp_new_user_notification($user_id);
    }

    return $user_id;
  }

  // This function controls adding the subscription from a stripe charge and customer object
  public static function create_subscription_from_stripe($form_id, $user_id, $membership_id, $charge, $customer) {
    $user             = new MeprUser($user_id);
    $membership       = new MeprProduct($membership_id);
    $created_at       = time();

    if($membership->trial && $membership->trial_amount == 0.00) {
      $expires_at = $created_at + MeprUtils::days($membership->trial_days);
    }
    else {
      $expires_at = $created_at + MeprUtils::days(1);
    }

    $txn              = new MeprTransaction();
    $txn->amount      = 0.00;
    $txn->total       = 0.00;
    $txn->user_id     = $user->ID;
    $txn->product_id  = $membership_id;
    $txn->txn_type    = MeprTransaction::$subscription_confirmation_str;
    $txn->gateway     = MpWPSimplePay::get_selected_gateway();
    $txn->ip_addr     = $_SERVER['REMOTE_ADDR'];
    $txn->created_at  = gmdate('c', $created_at);
    $txn->expires_at  = gmdate('c', $expires_at);
    $txn->status      = MeprTransaction::$confirmed_str;

    if(!empty($customer) && $customer instanceof Stripe\Customer) {
      $sub                = new MeprSubscription();
      $sub->subscr_id     = $customer->id;
      $sub->membership_id = $membership_id;
      $sub->user_id       = $user->ID;
      $sub->gateway       = MpWPSimplePay::get_selected_gateway();
      $sub->status        = 'active';

      $sub->load_product_vars($membership);

      // load terms from plan
      $id          = get_post_meta($form_id, '_single_plan', true);
      $cached_plan = get_post_meta($form_id, '_single_plan_object', true);

      if(!empty($id) && 'empty' !== $id) {

        if($cached_plan) {
          // Use cached plan object if found
          $plan = $cached_plan;
        } else {
          // Default to calling Stripe API for plan if cached not found
          $plan = SimplePay\Pro\Payments\Plan::get_plan_by_id($id);
        }
      }

      $sub->set_subtotal(MeprAppHelper::format_number($plan->amount / 100));
      $sub->period = $plan->interval_count;

      switch($plan->interval) {
        case 'week':
          $sub->period_type = 'weeks'; break;
        case 'year':
          $sub->period_type = 'years'; break;
        default:
          $sub->period_type = 'months';
      }

      if(isset($plan->trial_period_days) && !empty($plan->trial_period_days)) {
        $sub->trial = true;
        $sub->trial_amount = 0.00;
        $sub->trial_days = $plan->trial_period_days;
      }

      $sub->store();

      $txn->subscription_id = $sub->id;
    }

    // Finally, store the confirmation transaction
    $txn->store();

    if(isset($sub)) {
      return $sub;
    }
    else {
      return $txn;
    }
  }


  public static function create_transaction_from_stripe($user_id, $membership_id, $charge) {
    //Let's bail because recurring transactions should come through on the Webhook instead
    $sub = MeprSubscription::get_one_by_subscr_id($charge->customer);
    if($sub instanceof MeprSubscription) {
      return;
    }

    // let's check and make sure this txn doesn't already exist
    $txn = MeprTransaction::get_one_by_trans_num($charge->id);

    // It doesn't exist
    if(!isset($txn->id) || empty($txn) || !($txn instanceof MeprTransaction)) {
      $membership       = new MeprProduct($membership_id);
      $expires_at       = $membership->get_expires_at();

      $txn              = new MeprTransaction();
      $txn->trans_num   = $charge->id;
      $txn->amount      = MeprAppHelper::format_number(((float)($charge->amount / 100)), true);
      $txn->total       = MeprAppHelper::format_number(((float)($charge->amount / 100)), true);
      $txn->user_id     = $user_id;
      $txn->product_id  = $membership_id;
      $txn->txn_type    = MeprTransaction::$payment_str;
      $txn->gateway     = MpWPSimplePay::get_selected_gateway();
      $txn->ip_addr     = $_SERVER['REMOTE_ADDR'];
      $txn->created_at  = gmdate('c');
      $txn->expires_at  = (is_null($expires_at))?MeprUtils::mysql_lifetime():gmdate('c', $expires_at);
    }

    //Set the txn's status - default to pending if nothing else matches up
    if($charge->status == 'failed') {
      $txn->status = MeprTransaction::$failed_str;
    }
    elseif($charge->status == 'refunded') {
      $txn->status = MeprTransaction::$refunded_str;
    }
    elseif($charge->status == 'succeeded') {
      $txn->status = MeprTransaction::$complete_str;
    }
    else {
      $txn->status = MeprTransaction::$pending_str;
    }

    //Don't call store on a non-object (the transaction already existed)
    if($txn instanceof MeprTransaction) {
      $txn->store();
    }

    return $txn;
  }
}
