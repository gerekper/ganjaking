<?php
if(!defined('STDIN')) { die("You're unauthorized to view this page."); }

error_reporting(E_ALL);

chdir(dirname(__FILE__));
require_once('../../../wp-load.php');

class MpimpMagicMembersController {
  // No need to migrate users & groups

  // Migrate Payment Methods
  /** MemberPress currently supports only 2 of the same payment methods that
   *  MagicMembers supports ... Authorize.net & PayPal Express Checkout ...
   *  so we'll only worry about migrating those.
   */
  public static function migrate_payment_methods() {
    echo "\nMigrating Payment Methods...\n";
    $pms = array();
    $authnet = (object)get_option('mgm_authorizenet_options');

    if( $authnet->enabled=='Y' ) {
      $pmt = (object)$authnet->setting;

      $mepr_pmt = MeprGatewayFactory::fetch('MeprAuthorizeGateway');

      $mepr_pmt->settings->label = __("Credit Card", 'memberpress-importer');
      $mepr_pmt->settings->login_name = $pmt->loginid;
      $mepr_pmt->settings->transaction_key = $pmt->tran_key;
      $mepr_pmt->settings->force_ssl = 'on';

      $mepr_options = MeprOptions::fetch();
      $mepr_options->integrations[$mepr_pmt->id] = (array)$mepr_pmt->settings;
      $mepr_options->store(false);
      echo ".";
      $pms['mgm_authorizenet'] = $mepr_pmt;
    }

    $paypal = (object)get_option('mgm_paypalexpresscheckout');

    if( $paypal->enabled=='Y' ) {
      $pmt = (object)$paypal->setting;

      $mepr_pmt = MeprGatewayFactory::fetch('MeprPayPalGateway');

      $mepr_pmt->settings->label = __("PayPal", 'memberpress-importer');
      $mepr_pmt->settings->api_username = $pmt->username;
      $mepr_pmt->settings->api_password = $pmt->password;
      $mepr_pmt->settings->signature = $pmt->signature;

      $mepr_options = MeprOptions::fetch();
      $mepr_options->integrations[$mepr_pmt->id] = (array)$mepr_pmt->settings;
      $mepr_options->store(false);
      echo ".";
      $pms['mgm_paypalexpresscheckout'] = $mepr_pmt;
    }

    return $pms;
  }

  // Migrate Products
  public static function migrate_products() {
    echo "\nMigrating Products...\n";
    $packs = get_option('mgm_subscription_packs_options');

    $product_map = array();

    foreach($packs['packs'] as $i => $pack) {
      $pack = (object)$pack;

      $p = new MeprProduct();

      if(!$period_type = self::period_type($pack->duration_type))
        continue;

      $trial_days = self::trial_days( $pack->trial_on,
                                      $pack->trial_duration_type,
                                      $pack->trial_duration,
                                      $pack->trial_num_cycles );

      $p->post_title = ucwords(preg_replace('/_/',' ',$pack->membership_type));
      $p->post_content = $pack->description;
      $p->price = number_format($pack->cost,2);
      $p->period = $pack->duration;
      $p->period_type = $period_type;
      $p->limit_cycles = ($pack->num_cycles > 0);
      $p->limit_cycles_num = $pack->num_cycles;
      $p->trial = (bool)$pack->trial_on;
      $p->trial_days = $trial_days;
      $p->trial_amount = $pack->trial_cost;

      $p->store();

      // Migrate Payment profiles

      $product_map[$pack->membership_type] = $p;
      echo ".";
    }

    return array('packs' => $packs['packs'], 'map' => $product_map);
  }

  public static function migrate_coupons($packs, $map) {
    echo "\nMigrating Coupons...\n";
    global $wpdb;

    $valid_products = array();

    // Apparently in Magic Members, coupons apply to all products
    foreach($map as $p)
      $valid_products[] = $p->ID;

    $query = "SELECT * FROM {$wpdb->prefix}mgm_coupons";

    $mgm_coupons = $wpdb->get_results($query);

    foreach($mgm_coupons as $mgmc) {
      // Don't migrate coupons that are meaningless to MemberPress
      if(!is_numeric(preg_replace('/\%/','',$mgmc->value)))
        continue;

      $product_map[$pack->membership_type] = $p;

      $c = new MeprCoupon();
      $c->post_title    = $mgmc->name;
      $c->should_expire = !empty($mgmc->expire_dt);

      if(empty($mgmc->expire_dt)) {
        $c->expires_on=0;
      }
      else {
        $d = (object)date_parse( $mgmc->expire_dt );
        $c->expires_on = MeprUtils::make_ts_date( $d->month, $d->day, $d->year );
      }

      $c->usage_count   = empty($mgmc->used_count)?0:$mgmc->used_count;
      $c->usage_amount  = empty($mgmc->use_limit)?0:$mgmc->use_limit;
      $c->discount_type = preg_match('/\%/',$mgmc->value)?'percent':'dollar';
      $c->discount_amount = number_format(preg_replace('/\%/','',$mgmc->value),2);
      $c->valid_products  = $valid_products;
      $c->no_recurring  = false; // Allow recurring by default
      $c->store();
      echo ".";
    }
  }

  public static function migrate_rules($packs, $map) {
    // Nothing for now ... we'll do it manually
  }

  public static function migrate_subscriptions($packs, $map, $pms) {
    echo "\nMigrating Subscriptions...\n";
    global $wpdb;
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->users}");

    foreach( $users as $user ) {
      if(!($opts = get_user_meta($user->ID, 'mgm_member_options', true)))
        continue;

      $opts = (object)$opts;

      if(!in_array($opts->duration_type,array('m','y')))
        continue;

      $sub = new MeprSubscription();

      if(!$period_type = self::period_type($opts->duration_type))
        continue;

      $trial_days = self::trial_days( $opts->trial_on,
                                      $opts->trial_duration_type,
                                      $opts->trial_duration,
                                      $opts->trial_num_cycles );

      if( $opts->status=='Active' or $opts->status=='Expired' or
          $opts->status=='Awaiting Cancelled' )
        $sub->status = MeprSubscription::$active_str;
      else if( $opts->status=='Cancelled' )
        $sub->status = MeprSubscription::$cancelled_str;
      else if( $opts->status=='Inactive' )
        $sub->status = MeprSubscription::$pending_str;

      $sub->response = json_encode($opts);
      $sub->user_id = $user->ID;
      //$sub->first_txn_id // Update Later
      //$sub->latest_txn_id // Update Later
      $sub->txn_count = 0;
      //$sub->init_txn_id // Update Later
      //$sub->ip_addr
      //$sub->coupon_id
      $sub->price = number_format($opts->amount,2);
      $sub->period = $opts->duration;
      $sub->period_type = $period_type;
      $sub->limit_cycles = false;
      $sub->limit_cycles_num = 0;
      $sub->trial = (bool)$opts->trial_on;
      $sub->trial_days = $trial_days;
      $sub->trial_amount = number_format($opts->trial_cost,2);
      $sub->created_at = date('Y-m-d H:i:s', (int)$opts->join_date);

      if(isset($opts->payment_info['subscr_id']))
        $sub->subscr_id = $opts->payment_info['subscr_id'];
      else
        $sub->subscr_id = uniqid();

      // Default to manual
      $sub->gateway = MeprTransaction::$manual_gateway_str;

      if(isset($opts->payment_info['module'])) {
        if($opts->payment_info['module']=='mgm_authorizenet') {
          if(isset($pms['mgm_authorizenet']) and $pms['mgm_authorizenet'] instanceof MeprBaseGateway)
            $sub->gateway = $pms['mgm_authorizenet']->id;
        }
        else if($opts->payment_info['module']=='mgm_paypalexpresscheckout') {
          if(isset($pms['mgm_paypalexpresscheckout']) and $pms['mgm_paypalexpresscheckout'] instanceof MeprBaseGateway)
            $sub->gateway = $pms['mgm_paypalexpresscheckout']->id;
        }
      }

      $product = $map[$opts->membership_type];

      if(!$product) { continue; }

      $sub->product_id = $product->ID;

      // Initial Transaction
      $txn = new MeprTransaction();
      $txn->product_id = $product->ID;
      $txn->user_id = $user->ID;

      if($sub->trial and $sub->trial_amount > 0.00) {
        $txn->amount     = $sub->trial_amount;
        $txn->txn_type   = MeprTransaction::$payment_str;
        $txn->status     = MeprTransaction::$complete_str;
        $txn->created_at = date('Y-m-d H:i:s', (int)$opts->join_date);
        $txn->expires_at = date('Y-m-d H:i:s', ((int)$opts->join_date + MeprUtils::days($sub->trial_days)));
        $txn->store();
      }
      else {
        $day_count       = ($sub->trial ? $sub->trial_days : 1);
        $txn->created_at = date('Y-m-d H:i:s', (int)$opts->join_date);
        $txn->expires_at = date('Y-m-d H:i:s', (int)$opts->join_date + MeprUtils::days($day_count));
        $txn->txn_type   = MeprTransaction::$subscription_confirmation_str;
        $txn->status     = MeprTransaction::$confirmed_str;
        $txn->trans_num  = $sub->subscr_id;
        $txn->amount     = 0.00;
        $txn->store();
      }

      // Latest Transaction
      $ltxn = new MeprTransaction();

      $ltxn->user_id    = $user->ID;
      $ltxn->product_id = $product->ID;
      $ltxn->trans_num  = uniqid();

      $ltxn->amount     = $sub->price;
      $ltxn->txn_type   = MeprTransaction::$payment_str;
      $ltxn->status     = MeprTransaction::$complete_str;

      if( !empty($opts->last_payment_check_date) and
          preg_match('/\d\d\d\d-\d\d-\d\d/',$opts->last_payment_check_date) ) {
        $ltxn->created_at = "{$opts->last_payment_check_date} 00:00:00";
      }
      else {
        $ltxn->created_at = date('Y-m-d H:i:s');
      }

      $ltxn->expires_at = "{$opts->expire_date} 00:00:00";
      $ltxn->response   = json_encode($opts);

      $ltxn->gateway = $sub->gateway;
      $ltxn->store();

      $sub->first_txn_id = $txn->id;
      $sub->latest_txn_id = $ltxn->id;

      $sub->store();
      $txn->subscription_id = $sub->ID;
      $txn->store();
      $ltxn->subscription_id = $sub->ID;
      $ltxn->store();
      echo ".";
    }
  }

  public static function migrate_transactions($packs, $map, $pms) {
    echo "\nMigrating Transactions...\n";
    global $wpdb;

    $mgm_txns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mgm_transactions");

    foreach($mgm_txns as $mgm_txn) {
      $mgm_txndata = (object)json_decode($mgm_txn->data);

      if(empty($mgm_txn->user_id) and empty($mgm_txndata->user_id)) { continue; }

      if(strtolower($mgm_txn->status)=='active')
        $status = MeprTransaction::$complete_str;
      else
        $status = MeprTransaction::$pending_str;

      $product_id = 0;

      if(!isset($map[$mgm_txndata->membership_type])) { continue; }

      $product = $map[$mgm_txndata->membership_type];

      if(!$product or !($product instanceof MeprProduct)) { continue; }

      $txn = new MeprTransaction();
      $txn->amount = number_format($mgm_txndata->cost,2);
      $txn->user_id = empty($mgm_txn->user_id)?$mgm_txndata->user_id:$mgm_txn->user_id;
      $txn->product_id = $map[$mgm_txndata->membership_type]->ID;
      //$txn->coupon_id
      $txn->trans_num = uniqid(); // apparently this doesn't get recorded by MGM for some reason
      $txn->status    = $status;
      $txn->txn_type  = MeprTransaction::$payment_str;
      $txn->response  = json_encode($mgm_txn);

      if( $mgm_txn->module=='authorizenet' or $mgm_txn->module=='paypalexpresscheckout' ) {
        $txn->gateway = $pms['mgm_'.$mgm_txn->module]->id;
      }
      else {
        $txn->gateway = MeprTransaction::$manual_gateway_str;
      }

      $query = "SELECT option_value FROM {$wpdb->prefix}mgm_transaction_options WHERE transaction_id=%d AND option_name=%s";
      $query = $wpdb->prepare($query, $mgm_txn->id, "{$mgm_txn->module}_subscription_id");
      if($subscr_id = $wpdb->get_var($query)) {
        if($sub = MeprSubscription::get_one_by_subscr_id($subscr_id))
          $txn->subscription_id=$sub->ID;
        else
          $txn->subscription_id=0;
      }

      $prd = $txn->product();

      $txn->created_at = $mgm_txn->transaction_dt;
      $txn->expires_at = date('Y-m-d H:i:s',$prd->get_expires_at(strtotime($txn->created_at)));

      $txn->store();
      echo ".";
    }
  }

  public static function period_type($mgm_type) {
    // Determine Period Type
    if($mgm_type=='y')
      $period_type = 'years';
    else if($mgm_type=='m')
      $period_type = 'months';
    else
      $period_type = false;

    return $period_type;
  }

  public static function trial_days($on, $period_type, $duration, $num_cycles) {
    if((bool)$on) {
      if($period_type=='d')
        $dcount = 1;
      else if($period_type=='w')
        $dcount = 7;
      else if($period_type=='m')
        $dcount = 30;
      else if($period_type=='y')
        $dcount = 365;

      $trial_days = ( (int)$duration * $dcount ) * (int)$num_cycles;
    }
    else
      $trial_days = 0;

    return $trial_days;
  }
} //End class

// Get the payment methods setup
$pms  = MpimpMagicMembersController::migrate_payment_methods();
// Now products
$info = MpimpMagicMembersController::migrate_products();
extract($info);

MpimpMagicMembersController::migrate_coupons($packs,$map);
//MpimpMagicMembersController::migrate_rules($packs,$map);

MpimpMagicMembersController::migrate_subscriptions($packs,$map,$pms);
//MpimpMagicMembersController::migrate_transactions($packs,$map,$pms);

